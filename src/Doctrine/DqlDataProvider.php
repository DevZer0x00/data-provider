<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Doctrine;

use DevZer0x00\DataProvider\DataProviderAbstract;
use DevZer0x00\DataProvider\Filter\CriteriaAbstract;
use DevZer0x00\DataProvider\Sorter\Column;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DqlDataProvider extends DataProviderAbstract
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
                if (!$column->isSorted()) {
                    continue;
                }

                $orderedFields = $column->getOrderByFields();

                foreach ($orderedFields as $field => $direction) {
                    $qb->addOrderBy($field, $direction);
                }
            }
        }

        if ($paginator = $this->getPaginator()) {
            $qb->setMaxResults($paginator->getPageSize())
                ->setFirstResult($this->getResultOffset());
        }

        if ($filter = $this->getFilter()) {
            $criteria = new Criteria();

            /** @var CriteriaAbstract $filterCriteria */
            foreach ($filter->getCriteriaCollection() as $filterCriteria) {
                $criteriaExpr = $filterCriteria->getCriteria()->getWhereExpression();

                if ($criteriaExpr === null) {
                    continue;
                }

                $criteria->andWhere($criteriaExpr);
            }

            $qb->addCriteria($criteria);
        }

        $this->data = $qb->getQuery()->getResult();

        return $this;
    }

    private function getResultOffset(): int
    {
        return ($this->paginator->getCurrentPage() - 1) * $this->paginator->getPageSize();
    }
}
