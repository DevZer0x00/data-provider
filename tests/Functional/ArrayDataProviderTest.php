<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Functional;

use DevZer0x00\DataProvider\ArrayDataProvider;
use DevZer0x00\DataProvider\Filter;
use DevZer0x00\DataProvider\Paginator;
use DevZer0x00\DataProvider\Sorter;
use DevZer0x00\DataProvider\Tests\Functional\Stub\Filter\NameCriteria;
use DevZer0x00\DataProvider\Tests\Functional\Stub\Filter\PriceCriteria;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class ArrayDataProviderTest extends TestCase
{
    public function testPaginatorGetData(): void
    {
        $sorter = new Sorter();
        $sorter->setColumnCollection(new Sorter\ColumnCollection());

        $paginator = new Paginator();
        $paginator->setCurrentPage(1)
            ->setPageSize(10);

        $filter = new Filter();
        $filter->setCriteriaCollection(new Filter\CriteriaCollection());

        $originalData = $this->getOriginalData();

        $provider = new ArrayDataProvider([
            'filter' => $filter,
            'sorter' => $sorter,
            'paginator' => $paginator,
            'originalData' => $originalData,
        ]);

        $this->assertEquals($originalData, $provider->getData());

        $provider->getPaginator()->setPageSize(1);
        $this->assertEquals([reset($originalData)], $provider->getData());

        $provider->getPaginator()->setPageSize(100)->setCurrentPage(2);
        $this->assertCount(0, $provider->getData());

        $provider->getPaginator()->setCurrentPage(3)->setPageSize(2);
        $this->assertEquals([end($originalData)], $provider->getData());
    }

    public function testFilterGetData(): void
    {
        $nameCriteria = new NameCriteria();

        $filter = new Filter();
        $filter->setCriteriaCollection(new Filter\CriteriaCollection([$nameCriteria]));

        $originalData = $this->getOriginalData();

        $provider = new ArrayDataProvider([
            'filter' => $filter,
            'originalData' => $originalData,
        ]);

        $this->assertEquals($originalData, $provider->getData());

        $nameCriteria->setValue('test');

        $this->assertCount(2, $provider->getData());
        $this->assertEquals(
            [
                [
                    'name' => 'test',
                    'qty' => 10,
                    'price' => 12.3,
                    'status' => 'enabled',
                ],
                [
                    'name' => 'test',
                    'qty' => 0,
                    'price' => 0.54,
                    'status' => 'disabled',
                ],
            ],
            $provider->getData()
        );

        $priceCriteria = new PriceCriteria();
        $priceCriteria->setMinPrice(10);

        $provider->getFilter()->getCriteriaCollection()->addCriteria($priceCriteria);

        $this->assertEquals(
            [
                [
                    'name' => 'test',
                    'qty' => 10,
                    'price' => 12.3,
                    'status' => 'enabled',
                ],
            ],
            $provider->getData()
        );

        $priceCriteria->setMaxPrice(11);

        $this->assertCount(0, $provider->getData());
    }

    protected function getOriginalData(): array
    {
        return [
            [
                'name' => 'test',
                'qty' => 10,
                'price' => 12.3,
                'status' => 'enabled',
            ],
            [
                'name' => 'qwerty1',
                'qty' => 4,
                'price' => 100,
                'status' => 'enabled',
            ],
            [
                'name' => 'test',
                'qty' => 0,
                'price' => 0.54,
                'status' => 'disabled',
            ],
            [
                'name' => 'qwerty',
                'qty' => 4,
                'price' => 100,
                'status' => 'enabled',
            ],
            [
                'name' => 'asdfasdf',
                'qty' => 54,
                'price' => 1,
                'status' => 'status',
            ],
        ];
    }
}
