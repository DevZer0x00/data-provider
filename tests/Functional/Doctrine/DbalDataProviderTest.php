<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Functional\Doctrine;

use Codeception\Module\Doctrine2;
use Codeception\Test\Unit;
use DevZer0x00\DataProvider\Doctrine\DbalDataProvider;
use DevZer0x00\DataProvider\Filter;
use DevZer0x00\DataProvider\Paginator;
use DevZer0x00\DataProvider\Sorter;
use DevZer0x00\DataProvider\Tests\Functional\Stub\Filter\FilmIdCriteria;
use DevZer0x00\DataProvider\Tests\FunctionalTester;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @internal
 * @coversNothing
 */
final class DbalDataProviderTest extends Unit
{
    protected FunctionalTester $tester;

    public function testGetDataWithoutPS(): void
    {
        $provider = new DbalDataProvider(['queryBuilder' => $this->createQueryBuilderWithJoin()]);

        $this->assertSame(
            $this->createQueryBuilderWithJoin()
                ->executeQuery()
                ->fetchAllAssociative(),
            $provider->getData()
        );
    }

    public function testDataSorting(): void
    {
        $qb = $this->createQueryBuilderWithJoin();
        $qb->orderBy('f.film_id', 'DESC');

        $filmSort = new Sorter\Column('f.film_id');
        $filmSort->setDirection(Sorter::SORT_DESC);

        $provider = new DbalDataProvider(['queryBuilder' => $this->createQueryBuilderWithJoin()]);
        $provider->setSorter(
            new Sorter([
                'columnCollection' => new Sorter\ColumnCollection(
                    [$filmSort]
                ),
            ])
        );

        $this->assertSame($qb->executeQuery()->fetchAllAssociative(), $provider->getData());
    }

    public function testDataSortingRepeatExecute(): void
    {
        $qb = $this->createQueryBuilderWithJoin();
        $qb->orderBy('f.length', 'DESC');

        $filmSort = new Sorter\Column('f.film_id');
        $filmSort->setDirection(Sorter::SORT_DESC);

        $lengthSort = new Sorter\Column('f.length');

        $provider = new DbalDataProvider(['queryBuilder' => $this->createQueryBuilderWithJoin()]);
        $provider->setSorter(
            new Sorter([
                'columnCollection' => new Sorter\ColumnCollection(
                    [$filmSort, $lengthSort]
                ),
            ])
        );

        $provider->getData();

        $filmSort->setDirection(null);
        $lengthSort->setDirection(Sorter::SORT_DESC);

        $this->assertSame($qb->executeQuery()->fetchAllAssociative(), $provider->getData());
    }

    public function testPagination(): void
    {
        $qb = $this->createQueryBuilderWithJoin();
        $qb->setMaxResults(10)->setFirstResult(20);

        $paginator = new Paginator();

        $provider = new DbalDataProvider(['queryBuilder' => $this->createQueryBuilderWithJoin()]);
        $provider->setPaginator($paginator);

        $paginator->setPageSize(10)->setCurrentPage(3);

        $this->assertSame($qb->executeQuery()->fetchAllAssociative(), $provider->getData());
    }

    public function testPaginationRepeatExecute(): void
    {
        $qb = $this->createQueryBuilderWithJoin();
        $qb->setMaxResults(10)->setFirstResult(20);

        $paginator = new Paginator();

        $provider = new DbalDataProvider(['queryBuilder' => $this->createQueryBuilderWithJoin()]);
        $provider->setPaginator($paginator);

        $paginator->setPageSize(1)->setCurrentPage(3);
        $provider->getData();

        $paginator->setPageSize(10)->setCurrentPage(3);

        $this->assertSame($qb->executeQuery()->fetchAllAssociative(), $provider->getData());
    }

    public function testFilter(): void
    {
        $qb = $this->createQueryBuilderWithJoin();
        $qb->andWhere('f.film_id IN (1,2,3,4,5)');

        $filter = new Filter();
        $filmIdCriteria = new FilmIdCriteria();
        $filmIdCriteria->setValue([
            1, 2, 3, 4, 5,
        ]);

        $filter->setCriteriaCollection(new Filter\CriteriaCollection([$filmIdCriteria]));

        $provider = new DbalDataProvider(['queryBuilder' => $this->createQueryBuilderWithJoin()]);
        $provider->setFilter($filter);

        $this->assertSame($qb->executeQuery()->fetchAllAssociative(), $provider->getData());
    }

    private function createEmptyQueryBuilder(): QueryBuilder
    {
        /** @var Doctrine2 $doctrine */
        $doctrine = $this->getModule('Doctrine2');

        return $doctrine->em->getConnection()->createQueryBuilder();
    }

    private function createQueryBuilderWithJoin(): QueryBuilder
    {
        $qb = $this->createEmptyQueryBuilder();
        $qb->select('*')->from('film', 'f')->join('f', 'language', 'l', 'f.language_id = l.language_id');

        return $qb;
    }
}
