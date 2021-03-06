<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Unit;

use DevZer0x00\DataProvider\Filter;
use DevZer0x00\DataProvider\Filter\CriteriaCollection;
use Codeception\Test\Unit;

class FilterTest extends Unit
{
    public function testSetCollection()
    {
        $collection = $this->createMock(CriteriaCollection::class);

        $filter = new Filter(['criteriaCollection' => $collection]);

        $this->assertSame($collection, $filter->getCriteriaCollection());

        $filter = new Filter();
        $filter->setCriteriaCollection($collection);

        $this->assertSame($collection, $filter->getCriteriaCollection());
    }

    public function testFilteredCollection()
    {
        $filter = new Filter();
        $collection = $this->createMock(CriteriaCollection::class);
        $collection->expects($this->once())->method('findFiltered');
        $filter->setCriteriaCollection($collection);

        $filter->getFilterCriteriaCollection();
    }

    public function testEvents()
    {
        $filter = new Filter();

        $observer = $this->createMock(\SplObserver::class);
        $observer->expects($this->exactly(2))->method('update');

        $filter->attach($observer);

        $collection = $this->createMock(CriteriaCollection::class);
        $collection->expects($this->once())
            ->method('attach')
            ->with($filter);
        $collection->expects($this->once())
            ->method('detach')
            ->with($filter);

        $filter->setCriteriaCollection($collection);
        $filter->setCriteriaCollection($collection);
        $filter->setCriteriaCollection($this->createMock(CriteriaCollection::class));
    }
}