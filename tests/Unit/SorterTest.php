<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Unit;

use DevZer0x00\DataProvider\Sorter;
use DevZer0x00\DataProvider\Sorter\ColumnCollection;
use PHPUnit\Framework\TestCase;

class SorterTest extends TestCase
{
    public function testMultiSortable()
    {
        $sorter = new Sorter(['multiSortable' => true]);

        $sorter->setMultiSortable(false);
        $this->assertFalse($sorter->isMultiSortable());
        $sorter->setMultiSortable(true);
        $this->assertTrue($sorter->isMultiSortable());
    }

    public function testGetSortableColumns()
    {
        $sorter = new Sorter();

        $columnCollection = $this->createMock(ColumnCollection::class);

        $columnCollectionS1 = $this->createMock(ColumnCollection::class);

        $columnCollection->expects($this->exactly(2))
            ->method('findSortable')
            ->willReturn($columnCollectionS1);

        $columnCollectionS1->expects($this->once())
            ->method('reduceToFirstColumn');

        $sorter->setColumnCollection($columnCollection);

        $sorter->setMultiSortable(true);
        $sorter->getSortableColumns();

        $sorter->setMultiSortable(false);
        $sorter->getSortableColumns();
    }

    public function testEvents()
    {
        $sorter = new Sorter([
            'multiSortable' => false
        ]);

        $observer = $this->createMock(\SplObserver::class);
        $observer->expects($this->exactly(4))->method('update');

        $sorter->attach($observer);

        $collection = $this->createMock(ColumnCollection::class);
        $collection->expects($this->once())
            ->method('attach')
            ->with($sorter);
        $collection->expects($this->once())
            ->method('detach');

        $sorter->setColumnCollection($collection);
        $sorter->setColumnCollection($collection);
        $sorter->setColumnCollection($this->createMock(ColumnCollection::class));

        $sorter->setMultiSortable(false);
        $sorter->setMultiSortable(true);
        $sorter->setMultiSortable(false);
    }
}