<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider;

use DevZer0x00\DataProvider\Traits\ConfigurableTrait;
use SplObserver;
use SplSubject;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class DataProviderAbstract implements DataProviderInterface, SplObserver
{
    use ConfigurableTrait;

    protected ?Filter $filter = null;

    protected ?Sorter $sorter = null;

    protected ?Paginator $paginator = null;

    protected bool $initialized = false;

    protected iterable $data;

    protected function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setDefined(['filter', 'sorter', 'paginator']);

        $resolver->setAllowedTypes('filter', ['null', Filter::class])
            ->setAllowedTypes('sorter', ['null', Sorter::class])
            ->setAllowedTypes('paginator', ['null', Paginator::class]);

        return $resolver;
    }

    public function update(SplSubject $subject): void
    {
        $this->refresh();
    }

    public function getSorter(): ?Sorter
    {
        return $this->sorter;
    }

    public function setSorter(?Sorter $sorter): self
    {
        $this->refresh();

        if ($this->sorter !== $sorter) {
            if ($this->sorter !== null) {
                $this->sorter->detach($this);
            }

            if ($sorter !== null) {
                $sorter->attach($this);
            }
        }

        $this->sorter = $sorter;

        return $this;
    }

    public function getPaginator(): ?Paginator
    {
        return $this->paginator;
    }

    public function setPaginator(?Paginator $paginator): self
    {
        $this->refresh();

        if ($this->paginator !== $paginator) {
            if ($this->paginator !== null) {
                $this->paginator->detach($this);
            }

            if ($paginator !== null) {
                $paginator->attach($this);
            }
        }

        $this->paginator = $paginator;

        return $this;
    }

    public function getFilter(): ?Filter
    {
        return $this->filter;
    }

    public function setFilter(?Filter $filter): self
    {
        $this->refresh();

        if ($this->filter !== $filter) {
            if ($this->filter !== null) {
                $this->filter->detach($this);
            }

            if ($filter !== null) {
                $filter->attach($this);
            }
        }

        $this->filter = $filter;

        return $this;
    }

    public function getData(): iterable
    {
        $this->initializeData();

        return $this->data;
    }

    public function refresh(): self
    {
        $this->initialized = false;

        return $this;
    }

    abstract protected function initializeData(): self;
}
