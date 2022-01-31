<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Functional\Doctrine;

use Codeception\Module\Doctrine2;
use Codeception\Test\Unit;
use DevZer0x00\DataProvider\Doctrine\DqlDataProvider;
use DevZer0x00\DataProvider\Filter;
use DevZer0x00\DataProvider\Paginator;
use DevZer0x00\DataProvider\Sorter;
use DevZer0x00\DataProvider\Sorter\Column;
use DevZer0x00\DataProvider\Sorter\ColumnCollection;
use DevZer0x00\DataProvider\Tests\Functional\Stub\Entity\Actor;
use DevZer0x00\DataProvider\Tests\Functional\Stub\Filter\NameCriteria;
use Doctrine\ORM\QueryBuilder;

/**
 * @internal
 * @coversNothing
 */
final class DqlDataProviderTest extends Unit
{
    public function testGetData(): void
    {
        $qb = $this->createEmptyQueryBuilder();
        $qb->select('a')->from(Actor::class, 'a')->setMaxResults(10);

        $provider = new DqlDataProvider();
        $provider->setQueryBuilder($qb);

        $data = $provider->getData();

        $this->assertCount(10, $data);
        $this->assertInstanceOf(Actor::class, $data[0]);
    }

    public function testFilter(): void
    {
        $qb = $this->createEmptyQueryBuilder();
        $qb->select('a')->from(Actor::class, 'a');

        $firstNameCriteria = new NameCriteria('firstName');
        $firstNameCriteria->setValue('PENELOPE');

        $lastNameCriteria = new NameCriteria('lastName');
        $lastNameCriteria->setValue('MONROE');

        $filter = new Filter([
            'criteriaCollection' => new Filter\CriteriaCollection([
                $firstNameCriteria,
                $lastNameCriteria,
            ]),
        ]);

        $provider = new DqlDataProvider();
        $provider->setQueryBuilder($qb);
        $provider->setFilter($filter);

        $data = $provider->getData();

        $this->assertCount(1, $data);

        /** @var Actor $actor */
        $actor = current($data);

        $this->assertEquals($actor->actorId, 120);
    }

    public function testPagination(): void
    {
        $qb = $this->createEmptyQueryBuilder();
        $qb->select('a')->from(Actor::class, 'a')->orderBy('a.actorId', 'asc');

        $provider = new DqlDataProvider();
        $provider->setQueryBuilder($qb);

        $paginator = new Paginator(['pageSize' => 10]);
        $provider->setPaginator($paginator);

        $data = $provider->getData();
        $this->assertCount(10, $data);

        $paginator->setPageSize(5);
        $data = $provider->getData();
        $this->assertCount(5, $data);
        $this->assertEquals(5, $data[4]->actorId);
    }

    public function testSorter(): void
    {
        $qb = $this->createEmptyQueryBuilder();
        $qb->select('a')->from(Actor::class, 'a')->setMaxResults(5);

        $provider = new DqlDataProvider();
        $provider->setQueryBuilder($qb);

        $idColumn = new Column('a.actorId');
        $firstNameColumn = new Column(
            'a.firstName',
            [
                Sorter::SORT_ASC => ['a.firstName' => Sorter::SORT_DESC],
                Sorter::SORT_DESC => ['a.firstName' => Sorter::SORT_ASC],
            ]
        );

        $columnCollection = new ColumnCollection([$idColumn, $firstNameColumn]);

        $sorter = new Sorter(['columnCollection' => $columnCollection]);

        $provider = new DqlDataProvider();
        $provider->setQueryBuilder($qb);
        $provider->setSorter($sorter);

        $data = $provider->getData();
        $firstActor = $data[0];
        $lastActor = $data[4];
        $this->assertEquals(1, $firstActor->actorId);
        $this->assertEquals(5, $lastActor->actorId);

        $idColumn->setDirection(Sorter::SORT_DESC);
        $data = $provider->getData();
        $firstActor = $data[0];
        $lastActor = $data[4];
        $this->assertEquals(200, $firstActor->actorId);
        $this->assertEquals(196, $lastActor->actorId);

        $idColumn->setDirection(null);
        $firstNameColumn->setDirection(Sorter::SORT_ASC);
        $data = $provider->getData();
        $firstActor = $data[0];
        $lastActor = $data[4];
        $this->assertEquals(11, $firstActor->actorId);
        $this->assertEquals(168, $lastActor->actorId);
    }

    private function createEmptyQueryBuilder(): QueryBuilder
    {
        /** @var Doctrine2 $doctrine */
        $doctrine = $this->getModule('Doctrine2');

        return $doctrine->em->createQueryBuilder();
    }
}
