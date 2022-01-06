<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Functional;

use DevZer0x00\DataProvider\CsvDataProvider;
use DevZer0x00\DataProvider\Exception\ColumnCountException;
use DevZer0x00\DataProvider\Exception\FailReadStreamException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class CsvDataProviderTest extends TestCase
{
    public function testGetData(): void
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, "id; name; value \r 1; test_name_1; test_value_1 \r 2; test_name_2; test_value_2 \r");
        fseek($stream, 0);

        $provider = new CsvDataProvider([
            'sourceStream' => $stream,
        ]);

        $testArr = [
            ['id' => '1', 'name' => 'test_name_1', 'value' => 'test_value_1'],
            ['id' => '2', 'name' => 'test_name_2', 'value' => 'test_value_2'],
        ];

        $this->assertEquals($testArr, $provider->getData());
    }

    public function testColumnCountException(): void
    {
        $this->expectException(ColumnCountException::class);

        $stream = fopen('php://memory', 'r+');
        fwrite($stream, "id; name; value \r test_name_1; test_value_1 \r 2; test_name_2; test_value_2 \r");
        fseek($stream, 0);

        $provider = new CsvDataProvider([
            'sourceStream' => $stream,
        ]);

        $provider->getData();
    }

    public function testFailReadStreamException(): void
    {
        $this->expectException(FailReadStreamException::class);

        $stream = fopen('php://memory', 'r+');
        fwrite($stream, "id; name; value \r test_name_1; test_value_1 \r 2; test_name_2; test_value_2 \r");

        $provider = new CsvDataProvider([
            'sourceStream' => $stream,
        ]);

        $provider->getData();
    }
}
