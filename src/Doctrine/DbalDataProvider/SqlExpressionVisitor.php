<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Doctrine\DbalDataProvider;

use DevZer0x00\DataProvider\Exception\InvalidCriteriaException;
use DevZer0x00\DataProvider\Exception\RuntimeException;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use function is_array;

class SqlExpressionVisitor extends ExpressionVisitor
{
    public function __construct(private QueryBuilder $builder)
    {
    }

    public function walkComparison(Comparison $comparison)
    {
        $operator = $comparison->getOperator();
        $field = $comparison->getField();
        $value = $this->getValueFromComparison($comparison);

        $expressionBuilder = $this->builder->expr();

        if (($operator === Comparison::EQ || $operator === Comparison::IS) && $value === null) {
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

    private function getValueFromComparison(Comparison $comparison)
    {
        $value = $comparison->getValue()->getValue();

        $value = match ($comparison->getOperator()) {
            Comparison::CONTAINS => '%' . $value . '%',
            Comparison::STARTS_WITH => $value . '%',
            Comparison::ENDS_WITH => '%' . $value,
            default => $value
        };

        if (is_array($value)) {
            return $this->builder->createNamedParameter($value, Connection::PARAM_STR_ARRAY);
        }

        return $this->builder->createNamedParameter($value);
    }

    public function walkCompositeExpression(CompositeExpression $expr): string
    {
        $expressionList = [];

        foreach ($expr->getExpressionList() as $child) {
            $expressionList[] = $this->dispatch($child);
        }

        switch ($expr->getType()) {
            case CompositeExpression::TYPE_AND:
                return '(' . implode(' AND ', $expressionList) . ')';
            case CompositeExpression::TYPE_OR:
                return '(' . implode(' OR ', $expressionList) . ')';
            default:
                throw new RuntimeException('Unknown composite ' . $expr->getType());
        }
    }

    public function walkValue(Value $value): void
    {
    }
}
