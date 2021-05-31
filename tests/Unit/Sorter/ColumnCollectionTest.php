<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Unit\Sorter;

use DevZer0x00\DataProvider\Exception\NonUniqueColumnException;
use DevZer0x00\DataProvider\Sorter\Column;
use DevZer0x00\DataProvider\Sorter\ColumnCollection;
use Codeception\Test\Unit;

class ColumnCollectionTest extends Unit
{
    public function testAdd()
    {
        $collection = new ColumnCollection();

        $this->assertCount(0, $collection);

        $collection->add($this->createMock(Column::class));

        $this->assertCount(1, $collection);
    }

    public function testNonUniqueException()
    {
        $this->expectException(NonUniqueColumnException::class);

        $column1 = $this->createMock(Column::class);
        $column1->method('getName')->willReturn('test');

        $column2 = $this->createMock(Column::class);
        $column2->method('getName')->willReturn('test');

        $collection = new ColumnCollection();
        $collection->add($column1);
        $collection->add($column2);
    }

    public function testFindSortable()
    {
        $collection = new ColumnCollection();

        $column1 = $this->createMock(Column::class);
        $column1->method('getName')->willReturn('test');

        $column2 = $this->createMock(Column::class);
        $column2->method('getName')->willReturn('test1');

        $collection->add($column1);
        $collection->add($column2);

        $this->assertCount(0, $collection->findSortable());

        $column2->method('isSorted')->willReturn(true);
        $this->assertCount(1, $collection->findSortable());

        $column1->method('isSorted')->willReturn(true);
        $this->assertCount(2, $collection->findSortable());

        $this->assertCount(1, $collection->reduceToFirstColumn());
    }

    public function testFirst()
    {
        $collection = new ColumnCollection();

        $this->assertNull($collection->first());

        $column = $this->createMock(Column::class);
        $column->method('getName')->willReturn('test');

        $collection->add($column);

        $this->assertSame($column, $collection->first());
    }

    public function testReduceToFirst()
    {
        $collection = new ColumnCollection();

        $this->assertCount(0, $collection->reduceToFirstColumn());

        $column1 = $this->createMock(Column::class);
        $column1->method('getName')->willReturn('test');

        $column2 = $this->createMock(Column::class);
        $column2->method('getName')->willReturn('test1');

        $collection->add($column1);
        $collection->add($column2);

        $this->assertSame($column1, $collection->reduceToFirstColumn()->first());
    }

    public function testEvents()
    {
        $collection = new ColumnCollection();

        $observer = $this->createMock(\SplObserver::class);
        $observer->expects($this->once())->method('update')->with(
            $this->callback(function(ColumnCollection $collection) {
                $this->assertCount(1, $collection);

                return true;
            })
        );

        $collection->attach($observer);

        $column = $this->createMock(Column::class);
        $column->expects($this->once())
            ->method('attach')
            ->with($collection);

        $collection->add($column);
    }
}