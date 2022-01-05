<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Unit;

use ArrayIterator;
use Codeception\Test\Unit;
use DevZer0x00\DataProvider\ArrayDataProvider;
use DevZer0x00\DataProvider\Filter;
use DevZer0x00\DataProvider\Filter\CriteriaAbstract;
use DevZer0x00\DataProvider\Filter\CriteriaCollection;
use DevZer0x00\DataProvider\Paginator;
use DevZer0x00\DataProvider\Sorter;
use Doctrine\Common\Collections\Criteria;

/**
 * @internal
 * @coversNothing
 */
final class ArrayDataProviderTest extends Unit
{
    public function testPaginator(): void
    {
        $originalData = [
            [1],
            [2],
            [3],
            [4],
            [5],
        ];

        $paginator = $this->createMock(Paginator::class);
        $provider = new ArrayDataProvider(
            [
                'originalData' => $originalData,
            ]
        );

        $this->assertNull($provider->getPaginator());
        $this->assertEquals($originalData, $provider->getData());

        $provider->setPaginator($paginator);
        $this->assertSame($paginator, $provider->getPaginator());

        $paginator->method('getPageSize')->willReturn(2);
        $paginator->method('getCurrentPage')->willReturn(2);

        $this->assertEquals([[3], [4]], $provider->getData());

        $paginator = $this->createMock(Paginator::class);
        $paginator->method('getPageSize')->willReturn(10);
        $paginator->method('getCurrentPage')->willReturn(2);

        $provider->setPaginator($paginator);
        $this->assertEmpty($provider->getData());

        $paginator = $this->createMock(Paginator::class);
        $paginator->expects($this->once())
            ->method('setTotalCount')
            ->with(count($originalData));
        $paginator->expects($this->once())
            ->method('attach')
            ->with($provider);
        $paginator->expects($this->once())
            ->method('detach')
            ->with($provider);

        $provider->setPaginator($paginator);

        $provider->getData();

        $provider->setPaginator($this->createMock(Paginator::class));
        $provider->setPaginator(null);
    }

    public function testSort(): void
    {
        $arr = [
            ['c1' => 1, 'c2' => 3, 'c3' => 7],
            ['c1' => 6, 'c2' => 2, 'c3' => 6],
            ['c1' => 5, 'c2' => 4, 'c3' => 5],
            ['c1' => 6, 'c2' => 100, 'c3' => 4],
        ];

        $provider = new ArrayDataProvider();
        $provider->setOriginalData($arr);

        $sorter = $this->createMock(Sorter::class);
        $sorter->expects($this->any())
            ->method('isMultiSortable')
            ->willReturn(false);

        $columnCollection = $this->createMock(Sorter\ColumnCollection::class);
        $columnCollection->expects($this->once())
            ->method('count')
            ->willReturn(0);

        $sorter->expects($this->any())
            ->method('getSortableColumns')
            ->willReturn($columnCollection);

        $provider->setSorter($sorter);

        $this->assertEquals($arr, $provider->getData());

        $sorter = $this->createMock(Sorter::class);
        $sorter->expects($this->any())
            ->method('isMultiSortable')
            ->willReturn(true);

        $column1 = $this->createMock(Sorter\Column::class);
        $column1->expects($this->any())
            ->method('getOrderByFields')
            ->willReturn(['c1' => Sorter::SORT_ASC]);

        $column2 = $this->createMock(Sorter\Column::class);
        $column2->expects($this->any())
            ->method('getOrderByFields')
            ->willReturn(['c2' => Sorter::SORT_DESC]);

        $columnCollection = $this->createMock(Sorter\ColumnCollection::class);
        $columnCollection->expects($this->any())
            ->method('getIterator')
            ->willReturn(new ArrayIterator([$column1, $column2]));

        $sorter->expects($this->any())
            ->method('getSortableColumns')
            ->willReturn($columnCollection);

        $sorter->expects($this->once())
            ->method('attach')
            ->with($provider);
        $sorter->expects($this->once())
            ->method('detach')
            ->with($provider);

        $provider->setSorter($sorter);
        $provider->setSorter($sorter);

        $sorted1 = [
            ['c1' => 1, 'c2' => 3, 'c3' => 7],
            ['c1' => 5, 'c2' => 4, 'c3' => 5],
            ['c1' => 6, 'c2' => 100, 'c3' => 4],
            ['c1' => 6, 'c2' => 2, 'c3' => 6],
        ];

        $this->assertEquals($sorted1, $provider->getData());

        $provider->setSorter(null);

        $this->assertEquals($arr, $provider->getData());
    }

    public function testFilter(): void
    {
        $provider = new ArrayDataProvider();

        $criteria1 = $this->getMockForAbstractClass(CriteriaAbstract::class, [1]);
        $criteria1->expects($this->once())
            ->method('getCriteria')
            ->willReturn(new Criteria());
        $criteria2 = $this->getMockForAbstractClass(CriteriaAbstract::class, [2]);
        $criteria2->expects($this->once())
            ->method('getCriteria')
            ->willReturn(new Criteria());

        $criteriaCollection = $this->createMock(CriteriaCollection::class);
        $criteriaCollection->method('getIterator')
            ->willReturn(new ArrayIterator([$criteria1, $criteria2]));

        $filter = $this->createMock(Filter::class);
        $filter->expects($this->once())
            ->method('getCriteriaCollection')
            ->willReturn($criteriaCollection);
        $filter->expects($this->once())
            ->method('attach')
            ->with($provider);
        $filter->expects($this->once())
            ->method('detach')
            ->with($provider);

        $provider->setFilter($filter);
        $provider->setFilter($filter);

        $provider->getData();

        $provider->setFilter(null);
    }
}
