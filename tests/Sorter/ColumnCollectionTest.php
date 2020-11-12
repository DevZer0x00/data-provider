<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Sorter;

use DevZer0x00\DataProvider\Exception\NonUniqueColumnException;
use DevZer0x00\DataProvider\Sorter;
use DevZer0x00\DataProvider\Sorter\Column;
use DevZer0x00\DataProvider\Sorter\ColumnCollection;
use PHPUnit\Framework\TestCase;

class ColumnCollectionTest extends TestCase
{
    public function testAdd()
    {
        $collection = new ColumnCollection();

        $this->assertCount(0, $collection);

        $collection->add(new Column('test'));

        $this->assertCount(1, $collection);
    }

    public function testNonUniqueException()
    {
        $this->expectException(NonUniqueColumnException::class);

        $collection = new ColumnCollection();
        $collection->add(new Column('test'));
        $collection->add(new Column('test'));
    }

    public function testGetSorted()
    {
        $collection = new ColumnCollection();

        $column1 = new Column('test');
        $column2 = new Column('test1');

        $collection->add($column1);
        $collection->add($column2);

        $this->assertCount(0, $collection->findSorted());

        $column2->setDirection(Sorter::SORT_ASC);
        $this->assertCount(1, $collection->findSorted());
        $this->assertSame($column2, $collection->findSorted()->first());

        $column1->setDirection(Sorter::SORT_DESC);
        $this->assertCount(2, $collection->findSorted());
    }
}