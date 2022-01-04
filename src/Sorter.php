<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider;

use DevZer0x00\DataProvider\Sorter\ColumnCollection;
use DevZer0x00\DataProvider\Traits\ConfigurableTrait;
use DevZer0x00\DataProvider\Traits\ObserverableTrait;
use SplObserver;
use SplSubject;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Sorter implements SplObserver, SplSubject
{
    use ConfigurableTrait;
    use ObserverableTrait;

    public const SORT_ASC = 'asc';

    public const SORT_DESC = 'desc';

    private ColumnCollection $columnCollection;

    private bool $multiSortable;

    protected function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setDefined(['columnCollection', 'multiSortable']);

        $resolver->setDefault('multiSortable', false);

        $resolver->setAllowedTypes('columnCollection', ColumnCollection::class);
        $resolver->setAllowedTypes('multiSortable', 'bool');

        return $resolver;
    }

    public function setColumnCollection(ColumnCollection $columnCollection): self
    {
        $oldCollection = $this->columnCollection ?? null;

        if (empty($this->columnCollection)) {
            $columnCollection->attach($this);
        } elseif ($this->columnCollection !== $columnCollection) {
            $columnCollection->attach($this);
            $this->columnCollection->detach($this);
        }

        $this->columnCollection = $columnCollection;

        if ($oldCollection !== $this->columnCollection) {
            $this->notify();
        }

        return $this;
    }

    public function isMultiSortable(): bool
    {
        return $this->multiSortable;
    }

    public function setMultiSortable(bool $flag): self
    {
        $oldFlag = $this->multiSortable ?? null;

        $this->multiSortable = $flag;

        if ($oldFlag !== $this->multiSortable) {
            $this->notify();
        }

        return $this;
    }

    public function getSortableColumns(): ColumnCollection
    {
        $sortable = $this->columnCollection->findSortable();

        return $this->isMultiSortable() ? $sortable : $sortable->reduceToFirstColumn();
    }

    public function update(SplSubject $subject): void
    {
        $this->notify();
    }
}
