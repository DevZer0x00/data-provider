<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider;

use DevZer0x00\DataProvider\Filter\CriteriaAbstract;
use DevZer0x00\DataProvider\Sorter\Column;
use DevZer0x00\DataProvider\Sorter\ColumnCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArrayDataProvider extends DataProviderAbstract
{
    private array $originalData = [];

    /**
     * @var callable|null
     */
    private $sortCallback;

    public function getSortCallback(): ?callable
    {
        return $this->sortCallback;
    }

    public function setSortCallback(?callable $sortCallback): self
    {
        $this->sortCallback = $sortCallback;

        return $this;
    }

    public function setOriginalData(array $originalData): self
    {
        $this->refresh();

        $this->originalData = $originalData;

        return $this;
    }

    protected function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        return parent::configureOptions($resolver)
            ->setDefined(['originalData', 'sortCallback'])
            ->setAllowedTypes('originalData', 'array')
            ->setAllowedTypes('sortCallback', ['null', 'callable']);
    }

    protected function initializeData(): self
    {
        if ($this->initialized) {
            return $this;
        }

        $this->initialized = true;

        $data = $this->originalData;

        if ($filter = $this->getFilter()) {
            $collection = new ArrayCollection($data);

            /** @var CriteriaAbstract $criteria */
            foreach ($filter->getCriteriaCollection() as $criteria) {
                $collection = $collection->matching($criteria->getCriteria());
            }

            $data = $collection->getValues();
        }

        if ($sorter = $this->getSorter()) {
            $callback = $this->getSortCallback() ?? [$this, 'defaultSortCallback'];

            $data = $callback($data, $sorter->getSortableColumns());
        }

        if ($paginator = $this->getPaginator()) {
            $paginator->setTotalCount(count($data));

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
        if ($sortedColumns->count() === 0) {
            return $data;
        }

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