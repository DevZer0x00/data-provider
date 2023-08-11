<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Doctrine;

use DevZer0x00\DataProvider\DataProviderAbstract;
use DevZer0x00\DataProvider\Doctrine\DbalDataProvider\QueryBuilderCriteriaProcessor;
use DevZer0x00\DataProvider\Doctrine\DbalDataProvider\SqlExpressionVisitor;
use DevZer0x00\DataProvider\Sorter\Column;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DbalDataProvider extends DataProviderAbstract
{
    protected QueryBuilder $queryBuilder;

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

        $this->initialized = true;

        $qb = clone $this->queryBuilder;

        if ($sorter = $this->getSorter()) {
            $columns = $sorter->getSortableColumns();

            /** @var Column $column */
            foreach ($columns as $column) {
                if (!$column->isSorted()) {
                    continue;
                }

                $orderedFields = $column->getOrderByFields();

                foreach ($orderedFields as $field => $direction) {
                    $qb->addOrderBy($field, $direction);
                }
            }
        }

        if ($filter = $this->getFilter()) {
            $criteriaProcessor = new QueryBuilderCriteriaProcessor($qb, new SqlExpressionVisitor($qb));
            $criteriaProcessor->processCollection($filter->getCriteriaCollection());
        }

        $this->calculateTotalCount($qb);

        $this->data = $qb->executeQuery()
            ->fetchAllAssociative();

        return $this;
    }

    protected function calculateTotalCount($qb): void
    {
        if ($paginator = $this->getPaginator()) {
            $totalCountQb = clone $qb;
            $totalCountQb->resetQueryPart('select');
            $totalCountQb->select('count(*)');
            $totalCount = (int)$totalCountQb->fetchOne();

            $this->getPaginator()->setTotalCount($totalCount);

            $qb->setMaxResults($paginator->getPageSize())
                ->setFirstResult($this->getResultOffset());
        }
    }

    protected function getResultOffset(): int
    {
        return ($this->paginator->getCurrentPage() - 1) * $this->paginator->getPageSize();
    }
}
