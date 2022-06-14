<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Unit\Sorter;

use Codeception\Test\Unit;
use DevZer0x00\DataProvider\Exception\InvalidArgumentException;
use DevZer0x00\DataProvider\Sorter;
use DevZer0x00\DataProvider\Sorter\Column;
use SplObserver;

/**
 * @internal
 * @coversNothing
 */
final class ColumnTest extends Unit
{
    public function testName(): void
    {
        $column = new Column('test');

        $this->assertEquals('test', $column->getName());
    }

    public function testConstructorEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Column('');
    }

    public function testDirection(): void
    {
        $column = new Column('test');
        $this->assertNull($column->getDirection());

        $column->setDirection(Sorter::SORT_DESC);

        $this->assertTrue($column->isSorted());
        $this->assertEquals(Sorter::SORT_DESC, $column->getDirection());

        $column->setDirection(Sorter::SORT_ASC);
        $this->assertEquals(Sorter::SORT_ASC, $column->getDirection());
    }

    public function testInvalidSetDirection(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $column = new Column('test');
        $column->setDirection('test');
    }

    public function testOrderSettingsWithoutCustomSettings(): void
    {
        $column = new Column('test');

        $column->setDirection(Sorter::SORT_ASC);
        $this->assertEquals(['test' => Sorter::SORT_ASC], $column->getOrderByFields());

        $column->setDirection(Sorter::SORT_DESC);
        $this->assertEquals(['test' => Sorter::SORT_DESC], $column->getOrderByFields());
    }

    public function testOrderMultipleFieldsSettings(): void
    {
        $column = new Column('test', [
            Sorter::SORT_ASC => [
                'field1' => Sorter::SORT_DESC,
                'field2' => Sorter::SORT_ASC,
                'field3',
            ],
            Sorter::SORT_DESC => [
                'field1',
                'field2' => Sorter::SORT_ASC,
                'field4',
            ],
        ]);

        $column->setDirection(Sorter::SORT_ASC);
        $asc = [
            'field1' => Sorter::SORT_DESC,
            'field2' => Sorter::SORT_ASC,
            'field3' => Sorter::SORT_ASC,
        ];

        $this->assertEquals($asc, $column->getOrderByFields());

        $column->setDirection(Sorter::SORT_DESC);
        $desc = [
            'field1' => Sorter::SORT_DESC,
            'field2' => Sorter::SORT_ASC,
            'field4' => Sorter::SORT_DESC,
        ];
        $this->assertEquals($desc, $column->getOrderByFields());
    }

    /**
     * @dataProvider getInvalidFieldsSettings
     */
    public function testInvalidFieldsSettings(array $settings): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Column('test', $settings);
    }

    public function getInvalidFieldsSettings()
    {
        return [
            [
                [Sorter::SORT_ASC => ['f1']],
            ],
            [
                ['ASC' => ['f1'], 'DESC' => []],
            ],
            [
                [
                    Sorter::SORT_ASC => ['f1', 'f2'],
                    ['f1', 'f2'],
                ],
            ],
            [
                [
                    Sorter::SORT_ASC => ['f1' => 'test'],
                    Sorter::SORT_DESC => [],
                ],
            ],
        ];
    }

    public function testEvents(): void
    {
        $observer = $this->createMock(SplObserver::class);
        $observer->expects($this->any())
            ->method('update')
            ->withConsecutive(
                [
                    $this->callback(function (Column $column) {
                        $this->assertEquals(Sorter::SORT_DESC, $column->getDirection());

                        return true;
                    }),
                ],
                [
                    $this->callback(function (Column $column) {
                        $this->assertEquals(Sorter::SORT_ASC, $column->getDirection());

                        return true;
                    }),
                ],
            );

        $column = new Column('test');
        $column->attach($observer);

        $column->setDirection(Sorter::SORT_DESC);
        $column->setDirection(Sorter::SORT_DESC);
        $column->setDirection(Sorter::SORT_ASC);
    }
}
