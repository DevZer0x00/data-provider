<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Functional;

use DevZer0x00\DataProvider\CsvDataProvider;
use DevZer0x00\DataProvider\Exception\ColumnCountException;
use DevZer0x00\DataProvider\Filter;
use DevZer0x00\DataProvider\Paginator;
use DevZer0x00\DataProvider\Sorter;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class CsvDataProviderTest extends TestCase
{
    private const TEST_CSV_DATA = "id; name; value \r
    2; ahmed; ahmed_value \r
    4; mamed; mamed_value \r
    3; ibragim; ibragim_value \r
    6; boris_britva; boris_britva_value \r
    5; cigan; cigan_value \r
    7; test; test_value \r";

    private const ORIGINAL_CSV_ARRAY = [
        ['id' => '2', 'name' => 'ahmed', 'value' => 'ahmed_value'],
        ['id' => '4', 'name' => 'mamed', 'value' => 'mamed_value'],
        ['id' => '3', 'name' => 'ibragim', 'value' => 'ibragim_value'],
        ['id' => '6', 'name' => 'boris_britva', 'value' => 'boris_britva_value'],
        ['id' => '5', 'name' => 'cigan', 'value' => 'cigan_value'],
        ['id' => '7', 'name' => 'test', 'value' => 'test_value'],
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
        $this->assertEquals(self::ORIGINAL_CSV_ARRAY, $provider->getData());

        $stream = $this->createStream(self::TEST_CSV_DATA);
        $provider->setSourceStream($stream);
        $provider->getPaginator()->setPageSize(100)->setCurrentPage(2);

        $this->assertCount(0, $provider->getData());

        /*$provider->getPaginator()->setCurrentPage(3)->setPageSize(2);
        $this->assertEquals([end($originalData)], $provider->getData());*/
    }
}
