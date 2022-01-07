<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Unit\Doctrine\DbalDataProvider;

use Codeception\Test\Unit;
use DevZer0x00\DataProvider\Doctrine\DbalDataProvider\QueryBuilderCriteriaProcessor;
use DevZer0x00\DataProvider\Exception\InvalidCriteriaException;
use DevZer0x00\DataProvider\Filter\CriteriaAbstract;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @internal
 * @coversNothing
 */
final class QueryBuilderCriteriaProcessorTest extends Unit
{
    /**
     * @dataProvider comparisonCriteriaProvider
     */
    public function testSimpleComparison(string $method, string $field, mixed $value, string $methodEB = null): void
    {
        $expressionBuilder = $this->createMock(ExpressionBuilder::class);
        $expressionBuilder->expects($this->once())
            ->method($methodEB ?? $method)
            ->with(
                $this->callback(function ($f) use ($field) {
                    $this->assertEquals($field, $f);

                    return true;
                }),
                $this->callback(function ($v) use ($value) {
                    $this->assertEquals($value, $v);

                    return true;
                })
            )
            ->willReturn('');

        $this->processCriteria(
            $expressionBuilder,
            $this->buildFilterCriteriaMock(
                (new Criteria())->where(Criteria::expr()->{$method}($field, $value))
            )
        );
    }

    public function comparisonCriteriaProvider(): iterable
    {
        return [
            ['eq', 'test', '1'],
            ['neq', 'test', '2'],
            ['lt', 'test', '3'],
            ['lte', 'test', '4'],
            ['gt', 'test', '4'],
            ['gte', 'test', '4'],
            ['in', 'test', ['1', '2', '3']],
            ['notIn', 'test', ['1', '2', '3']],
            ['contains', 'test', 'test', 'like'],
            ['startsWith', 'test', 'test', 'like'],
            ['endsWith', 'test', 'test', 'like'],
        ];
    }

    public function testIsNull(): void
    {
        $expressionBuilder = $this->createMock(ExpressionBuilder::class);
        $expressionBuilder->expects($this->once())
            ->method('isNull')
            ->with('test')
            ->willReturn('');

        $this->processCriteria(
            $expressionBuilder,
            $this->buildFilterCriteriaMock(
                (new Criteria())->where(Criteria::expr()->isNull('test'))
            )
        );
    }

    public function testIsNotNull(): void
    {
        $expressionBuilder = $this->createMock(ExpressionBuilder::class);
        $expressionBuilder->expects($this->once())
            ->method('isNotNull')
            ->with('test')
            ->willReturn('');

        $this->processCriteria(
            $expressionBuilder,
            $this->buildFilterCriteriaMock(
                (new Criteria())->where(Criteria::expr()->neq('test', null))
            )
        );
    }

    public function testInvalidCriteria(): void
    {
        $this->expectException(InvalidCriteriaException::class);

        $this->processCriteria(
            $this->createMock(ExpressionBuilder::class),
            $this->buildFilterCriteriaMock(
                (new Criteria())->where(Criteria::expr()->memberOf('test', 1))
            )
        );
    }

    private function buildFilterCriteriaMock(Criteria $criteria): MockObject
    {
        $filterCriteria = $this->createMock(CriteriaAbstract::class);
        $filterCriteria->method('getCriteria')->willReturn(
            $criteria
        );

        return $filterCriteria;
    }

    private function processCriteria(MockObject|ExpressionBuilder $expressionBuilder, MockObject $filterCriteria): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())->method('expr')->willReturn($expressionBuilder);

        $processor = new QueryBuilderCriteriaProcessor($queryBuilder);
        $processor->process($filterCriteria);
    }
}
