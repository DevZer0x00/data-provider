<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Unit\Sorter;

use Codeception\Test\Unit;
use DevZer0x00\DataProvider\Exception\NonUniqueCriteriaException;
use DevZer0x00\DataProvider\Filter\CriteriaAbstract;
use DevZer0x00\DataProvider\Filter\CriteriaCollection;
use SplObserver;

/**
 * @internal
 * @coversNothing
 */
final class CriteriaCollectionTest extends Unit
{
    public function testConstruct(): void
    {
        $criteria = $this->getMockForAbstractClass(CriteriaAbstract::class, ['test']);
        $criteria1 = $this->getMockForAbstractClass(CriteriaAbstract::class, ['test1']);

        $collection = new CriteriaCollection([
            $criteria,
            $criteria1,
        ]);

        $this->assertCount(2, $collection);
    }

    public function testConstructNonUniqueName(): void
    {
        $this->expectException(NonUniqueCriteriaException::class);

        $criteria = $this->getMockForAbstractClass(CriteriaAbstract::class, ['test']);
        $criteria1 = $this->getMockForAbstractClass(CriteriaAbstract::class, ['test']);

        new CriteriaCollection([
            $criteria,
            $criteria1,
        ]);
    }

    public function testAddCriteria(): void
    {
        $collection = new CriteriaCollection();

        $this->assertCount(0, $collection);

        $collection->addCriteria($this->getMockForAbstractClass(CriteriaAbstract::class, ['t']));
        $this->assertCount(1, $collection);

        $collection->addCriteria($this->getMockForAbstractClass(CriteriaAbstract::class, ['t1']));
        $this->assertCount(2, $collection);
    }

    public function testAddCriteriaNonUniqueName(): void
    {
        $this->expectException(NonUniqueCriteriaException::class);

        $collection = new CriteriaCollection();
        $collection->addCriteria($this->getMockForAbstractClass(CriteriaAbstract::class, ['t']));
        $collection->addCriteria($this->getMockForAbstractClass(CriteriaAbstract::class, ['t']));
    }

    public function testEvents(): void
    {
        $collection = new CriteriaCollection();

        $observer = $this->createMock(SplObserver::class);
        $observer->expects($this->once())
            ->method('update')
            ->with($this->callback(
                function (CriteriaCollection $collection) {
                    $this->assertCount(1, $collection);

                    return true;
                }
            ));

        $collection->attach($observer);

        $criteria = $this->getMockForAbstractClass(CriteriaAbstract::class, ['t'], '', true, true, true, ['attach']);
        $criteria->expects($this->once())
            ->method('attach')
            ->with($collection);

        $collection->addCriteria($criteria);
    }
}
