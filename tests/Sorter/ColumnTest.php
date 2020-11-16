<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Sorter;

use DevZer0x00\DataProvider\Exception\InvalidArgumentException;
use DevZer0x00\DataProvider\Sorter\Column;
use DevZer0x00\DataProvider\Sorter;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    public function testName()
    {
        $column = new Column('test');

        $this->assertEquals('test', $column->getName());
    }

    public function testDirection()
    {
        $column = new Column('test');
        $this->assertNull($column->getDirection());

        $column->setDirection(Sorter::SORT_DESC);

        $this->assertTrue($column->isSorted());
        $this->assertEquals(Sorter::SORT_DESC, $column->getDirection());

        $column->setDirection(Sorter::SORT_ASC);
        $this->assertEquals(Sorter::SORT_ASC, $column->getDirection());
    }

    public function testInvalidSetDirection()
    {
        $this->expectException(InvalidArgumentException::class);

        $column = new Column('test');
        $column->setDirection('test');
    }

    public function testOrderSettingsWithoutCustomSettings()
    {
        $column = new Column('test');

        $column->setDirection(Sorter::SORT_ASC);
        $this->assertEquals(['test' => Sorter::SORT_ASC], $column->getOrderByFields());

        $column->setDirection(Sorter::SORT_DESC);
        $this->assertEquals(['test' => Sorter::SORT_DESC], $column->getOrderByFields());
    }

    public function testOrderMultipleFieldsSettings()
    {
        $column = new Column('test', [
            Sorter::SORT_ASC => [
                'field1' => Sorter::SORT_DESC,
                'field2' => Sorter::SORT_ASC,
                'field3'
            ],
            Sorter::SORT_DESC => [
                'field1',
                'field2' => Sorter::SORT_ASC,
                'field4'
            ]
        ]);

        $column->setDirection(Sorter::SORT_ASC);
        $asc = [
            'field1' => Sorter::SORT_DESC,
            'field2' => Sorter::SORT_ASC,
            'field3' => Sorter::SORT_ASC
        ];

        $this->assertEquals($asc, $column->getOrderByFields());

        $column->setDirection(Sorter::SORT_DESC);
        $desc = [
            'field1' => Sorter::SORT_DESC,
            'field2' => Sorter::SORT_ASC,
            'field4' => Sorter::SORT_DESC
        ];
        $this->assertEquals($desc, $column->getOrderByFields());
    }

    /**
     * @dataProvider getInvalidFieldsSettings
     * @param array $settings
     */
    public function testInvalidFieldsSettings(array $settings)
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
                    ['f1', 'f2']
                ]
            ],
            [
                [
                    Sorter::SORT_ASC => ['f1' => 'test'],
                    Sorter::SORT_DESC => []
                ]
            ]
        ];
    }

    public function testNotify()
    {
        $observer = $this->createMock(\SplObserver::class);
        $observer->expects($this->exactly(2))->method('update');

        $column = new Column('test');
        $column->attach($observer);

        $column->setDirection(Sorter::SORT_DESC);
        $column->setDirection(Sorter::SORT_DESC);
        $column->setDirection(Sorter::SORT_ASC);
    }
}

