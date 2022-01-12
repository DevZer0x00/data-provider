<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Functional;

use DevZer0x00\DataProvider\CsvDataProvider;
use DevZer0x00\DataProvider\Exception\ColumnCountException;
use DevZer0x00\DataProvider\Filter;
use DevZer0x00\DataProvider\Paginator;
use DevZer0x00\DataProvider\Sorter;
use DevZer0x00\DataProvider\Tests\Functional\Stub\Filter\NameCriteria;
use DevZer0x00\DataProvider\Tests\Functional\Stub\Filter\ValueCriteria;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class CsvDataProviderTest extends TestCase
{
    private const TEST_CSV_DATA = "id; name; value \r
    2; ahmed; 100 \r
    4; mamed; 150 \r
    3; ibragim; 200 \r
    6; boris_britva; 250 \r
    5; cigan; 300 \r";

    private const ORIGINAL_CSV_ARRAY = [
        ['id' => '2', 'name' => 'ahmed', 'value' => '100'],
        ['id' => '4', 'name' => 'mamed', 'value' => '150'],
        ['id' => '3', 'name' => 'ibragim', 'value' => '200'],
        ['id' => '6', 'name' => 'boris_britva', 'value' => '250'],
        ['id' => '5', 'name' => 'cigan', 'value' => '300'],
    ];

    private function createStream(string $data, bool $first = true)
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $data);

        if ($first === true) {
            fseek($stream, 0);
        }

        return $stream;
    }

    public function testGetData(): void
    {
        $stream = $this->createStream(self::TEST_CSV_DATA);

        $provider = new CsvDataProvider([
            'sourceStream' => $stream,
        ]);

        $this->assertEquals(self::ORIGINAL_CSV_ARRAY, $provider->getData());
    }

    public function testColumnCountException(): void
    {
        $this->expectException(ColumnCountException::class);
        $data = "id; name; value \r test_name_1; test_value_1 \r 2; test_name_2; test_value_2 \r";

        $stream = $this->createStream($data);

        $provider = new CsvDataProvider([
            'sourceStream' => $stream,
        ]);

        $provider->getData();
    }

    public function testPaginatorGetData(): void
    {
        $sorter = new Sorter();
        $sorter->setColumnCollection(new Sorter\ColumnCollection());

        $paginator = new Paginator();
        $paginator->setCurrentPage(1)
            ->setPageSize(10);

        $filter = new Filter();
        $filter->setCriteriaCollection(new Filter\CriteriaCollection());

        $stream = $this->createStream(self::TEST_CSV_DATA);

        $provider = new CsvDataProvider([
            'filter' => $filter,
            'sorter' => $sorter,
            'paginator' => $paginator,
            'sourceStream' => $stream,
        ]);

        $this->assertEquals(self::ORIGINAL_CSV_ARRAY, $provider->getData());

        $stream = $this->createStream(self::TEST_CSV_DATA);
        $provider->setSourceStream($stream);
        $provider->getPaginator()->setPageSize(1);

        $this->assertEquals([self::ORIGINAL_CSV_ARRAY[0]], $provider->getData());

        $stream = $this->createStream(self::TEST_CSV_DATA);
        $provider->setSourceStream($stream);
        $provider->getPaginator()->setPageSize(100)->setCurrentPage(2);

        $this->assertCount(0, $provider->getData());

        $stream = $this->createStream(self::TEST_CSV_DATA);
        $provider->setSourceStream($stream);
        $provider->getPaginator()->setCurrentPage(3)->setPageSize(2);

        $this->assertEquals([self::ORIGINAL_CSV_ARRAY[4]], $provider->getData());
    }

    public function testFilterGetData(): void
    {
        $nameCriteria = new NameCriteria();

        $filter = new Filter();
        $filter->setCriteriaCollection(new Filter\CriteriaCollection([$nameCriteria]));

        $stream = $this->createStream(self::TEST_CSV_DATA);

        $provider = new CsvDataProvider([
            'filter' => $filter,
            'sourceStream' => $stream,
        ]);

        $this->assertEquals(self::ORIGINAL_CSV_ARRAY, $provider->getData());

        $stream = $this->createStream(self::TEST_CSV_DATA);
        $provider->setSourceStream($stream);
        $nameCriteria->setValue('ibragim');

        $this->assertCount(1, $provider->getData());
        $this->assertEquals(
            [
                self::ORIGINAL_CSV_ARRAY[2],
            ],
            $provider->getData()
        );

        $stream = $this->createStream(self::TEST_CSV_DATA);
        $provider->setSourceStream($stream);

        $valueCriteria = new ValueCriteria();
        $valueCriteria->setMinValue(190);

        $provider->getFilter()->getCriteriaCollection()->addCriteria($valueCriteria);

        $this->assertEquals(
            [
                self::ORIGINAL_CSV_ARRAY[2],
            ],
            $provider->getData()
        );

        $stream = $this->createStream(self::TEST_CSV_DATA);
        $provider->setSourceStream($stream);

        $valueCriteria->setMaxValue(160);

        $this->assertCount(0, $provider->getData());
    }

    public function testSorterGetData(): void
    {
        $sorter = new Sorter();
        $sorter->setColumnCollection(new Sorter\ColumnCollection());

        $stream = $this->createStream(self::TEST_CSV_DATA);

        $provider = new CsvDataProvider([
            'sorter' => $sorter,
            'sourceStream' => $stream,
        ]);

        $this->assertEquals(self::ORIGINAL_CSV_ARRAY, $provider->getData());

        $column = new Sorter\Column('id');
        $column->setDirection(Sorter::SORT_DESC);
        $sorter->setColumnCollection(new Sorter\ColumnCollection([$column]));
        $stream = $this->createStream(self::TEST_CSV_DATA);

        $provider = new CsvDataProvider([
            'sorter' => $sorter,
            'sourceStream' => $stream,
        ]);

        $arr = [
            ['id' => '6', 'name' => 'boris_britva', 'value' => '250'],
            ['id' => '5', 'name' => 'cigan', 'value' => '300'],
            ['id' => '4', 'name' => 'mamed', 'value' => '150'],
            ['id' => '3', 'name' => 'ibragim', 'value' => '200'],
            ['id' => '2', 'name' => 'ahmed', 'value' => '100'],
        ];

        $this->assertEquals($arr, $provider->getData());
    }
}
