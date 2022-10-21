<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Doctrine\DbalDataProvider;

use DevZer0x00\DataProvider\Filter\CriteriaAbstract;
use DevZer0x00\DataProvider\Filter\CriteriaCollection;
use Doctrine\DBAL\Query\QueryBuilder;

class QueryBuilderCriteriaProcessor
{
    public function __construct(private QueryBuilder $builder, private SqlExpressionVisitor $expressionVisitor)
    {
    }

    public function process(CriteriaAbstract $filterCriteria): void
    {
        $whereCriteria = $filterCriteria->getCriteria();

        if ($whereCriteria !== null) {
            $criteriaExpr = $whereCriteria->getWhereExpression();

            if ($criteriaExpr === null) {
                return;
            }

            $this->builder->andWhere($this->expressionVisitor->dispatch($criteriaExpr));
        }

        $havingCriteria = $filterCriteria->getHavingCriteria();

        if ($havingCriteria !== null) {
            $criteriaExpr = $havingCriteria->getWhereExpression();

            if ($criteriaExpr === null) {
                return;
            }

            $this->builder->andHaving($this->expressionVisitor->dispatch($criteriaExpr));
        }
    }

    public function processCollection(CriteriaCollection $collection): void
    {
        /** @var CriteriaAbstract $criteria */
        foreach ($collection as $criteria) {
            $this->process($criteria);
        }
    }
}
