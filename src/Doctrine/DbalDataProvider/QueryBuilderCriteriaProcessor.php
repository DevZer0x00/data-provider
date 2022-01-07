<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Doctrine\DbalDataProvider;

use DevZer0x00\DataProvider\Exception\InvalidCriteriaException;
use DevZer0x00\DataProvider\Filter\CriteriaAbstract;
use DevZer0x00\DataProvider\Filter\CriteriaCollection;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;

class QueryBuilderCriteriaProcessor
{
    public function __construct(private QueryBuilder $builder)
    {
    }

    public function process(CriteriaAbstract $filterCriteria): void
    {
        $criteriaExpr = $filterCriteria->getCriteria()->getWhereExpression();

        if ($criteriaExpr === null) {
            return;
        }

        $qb = $this->builder;
        $expressionBuilder = $qb->expr();

        if ($criteriaExpr instanceof Comparison) {
            $qb->andWhere($this->getComparisonExpression($criteriaExpr, $expressionBuilder));
        }
    }

    private function getComparisonExpression(Comparison $comparison, ExpressionBuilder $expressionBuilder): string
    {
        $operator = $comparison->getOperator();
        $field = $comparison->getField();
        $value = $comparison->getValue()->getValue();

        if ($operator === Comparison::EQ && $value === null) {
            return $expressionBuilder->isNull($field);
        }

        if ($operator === Comparison::NEQ && $value === null) {
            return $expressionBuilder->isNotNull($field);
        }

        return match ($operator) {
            Comparison::EQ,
            Comparison::IS => $expressionBuilder->eq($field, $value),
            Comparison::NEQ => $expressionBuilder->neq($field, $value),
            Comparison::LT => $expressionBuilder->lt($field, $value),
            Comparison::LTE => $expressionBuilder->lte($field, $value),
            Comparison::GT => $expressionBuilder->gt($field, $value),
            Comparison::GTE => $expressionBuilder->gte($field, $value),
            Comparison::IN => $expressionBuilder->in($field, $value),
            Comparison::NIN => $expressionBuilder->notIn($field, $value),
            Comparison::CONTAINS,
            Comparison::STARTS_WITH,
            Comparison::ENDS_WITH => $expressionBuilder->like($field, $value),

            default => throw new InvalidCriteriaException()
        };
    }

    public function processCollection(CriteriaCollection $collection): void
    {
        /** @var CriteriaAbstract $criteria */
        foreach ($collection as $criteria) {
            $this->process($criteria);
        }
    }
}
