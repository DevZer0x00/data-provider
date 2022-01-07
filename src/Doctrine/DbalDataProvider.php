<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Doctrine;

use DevZer0x00\DataProvider\DataProviderAbstract;
use DevZer0x00\DataProvider\Doctrine\DbalDataProvider\QueryBuilderCriteriaProcessor;
use DevZer0x00\DataProvider\Sorter\Column;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DbalDataProvider extends DataProviderAbstract
{
    private QueryBuilder $queryBuilder;

    protected function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        return parent::configureOptions($resolver)
            ->setDefined('queryBuilder')
            ->setAllowedTypes('queryBuilder', QueryBuilder::class);
    }

    public function setQueryBuilder(QueryBuilder $queryBuilder): self
    {
        $this->queryBuilder = $queryBuilder;

        return $this;
    }

    protected function initializeData(): self
    {
        if ($this->initialized) {
            return $this;
        }

        $qb = clone $this->queryBuilder;

        if ($sorter = $this->getSorter()) {
            $columns = $sorter->getSortableColumns();

            /** @var Column $column */
            foreach ($columns as $column) {
                $qb->addOrderBy($column->getName(), $column->getDirection());
            }
        }

        if ($paginator = $this->getPaginator()) {
            $qb->setMaxResults($paginator->getPageSize())
                ->setFirstResult($this->getResultOffset());
        }

        if ($filter = $this->getFilter()) {
            $criteriaProcessor = new QueryBuilderCriteriaProcessor($qb);
            $criteriaProcessor->processCollection($filter->getCriteriaCollection());
        }

        $this->data = $qb->executeQuery()
            ->fetchAllAssociative();

        return $this;
    }

    private function getResultOffset(): int
    {
        return ($this->paginator->getCurrentPage() - 1) * $this->paginator->getPageSize();
    }
}
