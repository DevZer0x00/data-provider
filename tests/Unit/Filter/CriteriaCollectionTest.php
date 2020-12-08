<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Unit\Sorter;

use DevZer0x00\DataProvider\Exception\NonUniqueCriteriaException;
use DevZer0x00\DataProvider\Filter\CriteriaAbstract;
use DevZer0x00\DataProvider\Filter\CriteriaCollection;
use PHPUnit\Framework\TestCase;
use SplObserver;

class CriteriaCollectionTest extends TestCase
{
    public function testConstruct()
    {
        $criteria = $this->getMockForAbstractClass(CriteriaAbstract::class, ['test']);
        $criteria1 = $this->getMockForAbstractClass(CriteriaAbstract::class, ['test1']);

        $collection = new CriteriaCollection([
            $criteria,
            $criteria1
        ]);

        $this->assertCount(2, $collection);
    }

    public function testConstructNonUniqueName()
    {
        $this->expectException(NonUniqueCriteriaException::class);

        $criteria = $this->getMockForAbstractClass(CriteriaAbstract::class, ['test']);
        $criteria1 = $this->getMockForAbstractClass(CriteriaAbstract::class, ['test']);

        new CriteriaCollection([
            $criteria,
            $criteria1
        ]);
    }

    public function testAddCriteria()
    {
        $collection = new CriteriaCollection();

        $this->assertCount(0, $collection);

        $collection->addCriteria($this->getMockForAbstractClass(CriteriaAbstract::class, ['t']));
        $this->assertCount(1, $collection);

        $collection->addCriteria($this->getMockForAbstractClass(CriteriaAbstract::class, ['t1']));
        $this->assertCount(2, $collection);
    }

    public function testAddCriteriaNonUniqueName()
    {
        $this->expectException(NonUniqueCriteriaException::class);

        $collection = new CriteriaCollection();
        $collection->addCriteria($this->getMockForAbstractClass(CriteriaAbstract::class, ['t']));
        $collection->addCriteria($this->getMockForAbstractClass(CriteriaAbstract::class, ['t']));
    }

    /**
     * @dataProvider filteredCriteriaProvider
     *
     * @param bool[] ...$canUse
     */
    public function testFindFiltered(...$canUse)
    {
        $criterias = $usedCriterias = [];
        $c = 0;

        foreach ($canUse as $use) {
            $c++;

            $criteria = $this->getMockForAbstractClass(CriteriaAbstract::class, [$c]);
            $criteria->expects($this->once())
                ->method('canUse')
                ->willReturn($use);

            $criterias[] = $criteria;

            if ($use) {
                $usedCriterias[$criteria->getName()] = $criteria;
            }
        }

        $collection = new CriteriaCollection($criterias);

        $filtered = $collection->findFiltered();

        $this->assertCount(count($usedCriterias), $filtered);
        $this->assertSame($usedCriterias, iterator_to_array($filtered));
    }

    public function filteredCriteriaProvider()
    {
        return [
            [true, false, true],
            [false, false, false],
            [false, false, true],
            [false, true, true, true],
        ];
    }

    public function testEvents()
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

        $criteria = $this->getMockForAbstractClass(CriteriaAbstract::class, ['t'],'',true,true,true,['attach']);
        $criteria->expects($this->once())
            ->method('attach')
            ->with($collection);

        $collection->addCriteria($criteria);
    }
}