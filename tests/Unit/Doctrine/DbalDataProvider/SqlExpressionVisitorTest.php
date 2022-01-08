<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Unit\Doctrine\DbalDataProvider;

use Codeception\Test\Unit;
use DevZer0x00\DataProvider\Doctrine\DbalDataProvider\SqlExpressionVisitor;
use DevZer0x00\DataProvider\Exception\InvalidCriteriaException;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @internal
 * @coversNothing
 */
final class SqlExpressionVisitorTest extends Unit
{
    /**
     * @dataProvider comparisonCriteriaProvider
     *
     * @param mixed $value
     */
    public function testSimpleComparison(string $operator, string $method, string $field, $value): void
    {
        $expressionBuilder = $this->createMock(ExpressionBuilder::class);
        $expressionBuilder->expects($this->once())
            ->method($method)
            ->with(
                $this->callback(function ($f) use ($field) {
                    $this->assertEquals($field, $f);

                    return true;
                })
            )
            ->willReturn($method);

        $this->processComparison($expressionBuilder, new Comparison($field, $operator, $value));
    }

    public function comparisonCriteriaProvider(): iterable
    {
        return [
            [Comparison::EQ, 'eq', 'test', '1'],
            [Comparison::NEQ, 'neq', 'test', '2'],
            [Comparison::LT, 'lt', 'test', '3'],
            [Comparison::LTE, 'lte', 'test', '4'],
            [Comparison::GT, 'gt', 'test', '4'],
            [Comparison::GTE, 'gte', 'test', '4'],
            [Comparison::IN, 'in', 'test', ['1', '2', '3']],
            [Comparison::NIN, 'notIn', 'test', ['1', '2', '3']],
            [Comparison::CONTAINS, 'like', 'test', 'test', 'like'],
            [Comparison::STARTS_WITH, 'like', 'test', 'test', 'like'],
            [Comparison::ENDS_WITH, 'like', 'test', 'test', 'like'],
        ];
    }

    public function testIsNull(): void
    {
        $expressionBuilder = $this->createMock(ExpressionBuilder::class);
        $expressionBuilder->expects($this->once())
            ->method('isNull')
            ->with('test')
            ->willReturn('');

        $this->processComparison($expressionBuilder, Criteria::expr()->isNull('test'));
    }

    public function testIsNotNull(): void
    {
        $expressionBuilder = $this->createMock(ExpressionBuilder::class);
        $expressionBuilder->expects($this->once())
            ->method('isNotNull')
            ->with('test')
            ->willReturn('');

        $this->processComparison($expressionBuilder, Criteria::expr()->neq('test', null));
    }

    public function testInvalidCriteria(): void
    {
        $this->expectException(InvalidCriteriaException::class);

        $this->processComparison($this->createMock(ExpressionBuilder::class), Criteria::expr()->memberOf('test', 1));
    }

    private function processComparison(MockObject|ExpressionBuilder $expressionBuilder, Comparison $comparison): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())->method('expr')->willReturn($expressionBuilder);
        $queryBuilder->method('createNamedParameter')->willReturnArgument(0);

        (new SqlExpressionVisitor($queryBuilder))->walkComparison($comparison);
    }
}
