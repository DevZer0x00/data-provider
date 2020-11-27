<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider;

use DevZer0x00\DataProvider\Sorter\Column;
use DevZer0x00\DataProvider\Sorter\ColumnCollection;
use DevZer0x00\DataProvider\Traits\ConfigurableTrait;
use SplSubject;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SplObserver;

class ArrayDataProvider implements SplObserver
{
    use ConfigurableTrait;

    private ?Sorter $sorter = null;

    private ?Paginator $paginator = null;

    private array $originalData = [];

    private array $data;

    private bool $initialized = false;

    /**
     * @var callable|null
     */
    private $sortCallback;

    /**
     * @inheritDoc
     */
    public function update(SplSubject $subject)
    {
        $this->refresh();
    }

    public function getSorter(): ?Sorter
    {
        return $this->sorter;
    }

    public function setSorter(?Sorter $sorter): ArrayDataProvider
    {
        $this->refresh();

        $this->sorter = $sorter;

        return $this;
    }

    public function getSortCallback(): ?callable
    {
        return $this->sortCallback;
    }

    public function setSortCallback(?callable $sortCallback): ArrayDataProvider
    {
        $this->sortCallback = $sortCallback;

        return $this;
    }

    public function getPaginator(): ?Paginator
    {
        return $this->paginator;
    }

    public function setPaginator(?Paginator $paginator): ArrayDataProvider
    {
        $this->refresh();

        if ($this->paginator !== $paginator) {
            if (!empty($this->paginator)) {
                $this->paginator->detach($this);
            }

            $paginator->attach($this);
        }

        $this->paginator = $paginator;

        return $this;
    }

    public function getData(): array
    {
        $this->initializeData();

        return $this->data;
    }

    public function setOriginalData(array $originalData): self
    {
        $this->refresh();

        $this->originalData = $originalData;

        return $this;
    }

    protected function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setDefined(['originalData', 'sorter', 'paginator', 'sortCallback']);

        $resolver->setAllowedTypes('originalData', 'array')
            ->setAllowedTypes('sorter', ['null', Sorter::class])
            ->setAllowedTypes('paginator', ['null', Paginator::class])
            ->setAllowedTypes('sortCallback', ['null', 'callable']);

        return $resolver;
    }

    private function refresh(): self
    {
        $this->initialized = false;

        return $this;
    }

    private function initializeData(): self
    {
        if ($this->initialized) {
            return $this;
        }

        $this->initialized = true;

        $data = $this->originalData;

        if ($sorter = $this->getSorter()) {
            $callback = $this->getSortCallback() ?? [$this, 'defaultSortCallback'];

            $data = $callback($data, $sorter->getSortableColumns());
        }

        if ($paginator = $this->getPaginator()) {
            $data = array_slice(
                $data,
                $paginator->getPageSize() * ($paginator->getCurrentPage() - 1),
                $paginator->getPageSize()
            );
        }

        $this->data = $data;

        return $this;
    }

    private function defaultSortCallback(array $data, ColumnCollection $sortedColumns): array
    {
        $sortParams = [];

        /** @var Column $sortedColumn */
        foreach ($sortedColumns as $sortedColumn) {
            $fields = $sortedColumn->getOrderByFields();

            foreach ($fields as $field => $direction) {
                $sortParams[] = array_column($data, $field);
                $sortParams[] = $direction === Sorter::SORT_ASC ? SORT_ASC : SORT_DESC;
                $sortParams[] = SORT_NUMERIC;
            }
        }

        $sortParams[] = &$data;

        array_multisort(...$sortParams);

        return $data;
    }
}