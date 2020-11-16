<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests;

use DevZer0x00\DataProvider\Exception\ConfigException;
use DevZer0x00\DataProvider\Exception\InvalidArgumentException;
use DevZer0x00\DataProvider\Paginator;
use PHPUnit\Framework\TestCase;
use Throwable;

class PaginatorTest extends TestCase
{
    public function testPageSize()
    {
        $paginator = new Paginator();

        $paginator->setPageSize(1);
        $this->assertEquals(1, $paginator->getPageSize());
    }

    /**
     * @dataProvider getInvalidPageSize
     */
    public function testInvalidPageSize($size)
    {
        $this->expectException(InvalidArgumentException::class);

        $paginator = new Paginator();
        $paginator->setPageSize($size);
    }

    public function getInvalidPageSize()
    {
        return [
            [0],
            [-1],
        ];
    }

    public function testCurrentPage()
    {
        $paginator = new Paginator();
        $paginator->setCurrentPage(4);

        $this->assertEquals(4, $paginator->getCurrentPage());
    }

    /**
     * @dataProvider getInvalidCurrentPage
     */
    public function testInvalidCurrentPage(int $page)
    {
        $this->expectException(InvalidArgumentException::class);

        $paginator = new Paginator();
        $paginator->setPageSize($page);
    }

    public function getInvalidCurrentPage()
    {
        return [
            [0],
            [-1],
        ];
    }

    public function testTotalCount()
    {
        $paginator = new Paginator();
        $paginator->setTotalCount(5);

        $this->assertEquals(5, $paginator->getTotalCount());

        try {
            $paginator->setTotalCount(-1);
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
        }
    }

    public function testGetPageCount()
    {
        $paginator = new Paginator();

        $paginator->setPageSize(1)->setTotalCount(10);
        $this->assertEquals(10, $paginator->getPageCount());

        $paginator->setPageSize(2);
        $this->assertEquals(5, $paginator->getPageCount());

        $paginator->setPageSize(3);
        $this->assertEquals(4, $paginator->getPageCount());
    }

    public function testConstructorConfig()
    {
        $paginator = new Paginator([
            'pageSize' => 2,
            'currentPage' => 3,
            'totalCount' => 15,
        ]);

        $this->assertEquals(2, $paginator->getPageSize());
        $this->assertEquals(3, $paginator->getCurrentPage());
        $this->assertEquals(15, $paginator->getTotalCount());

        try {
            $paginator = new Paginator([
                'pageSize' => -1
            ]);
        } catch (Throwable $e) {
            $this->assertInstanceOf(ConfigException::class, $e);
        }

        try {
            $paginator = new Paginator([
                'pageSize' => -1
            ]);
        } catch (Throwable $e) {
            $this->assertInstanceOf(ConfigException::class, $e);
        }

        try {
            $paginator = new Paginator([
                'totalCount' => -1
            ]);
        } catch (Throwable $e) {
            $this->assertInstanceOf(ConfigException::class, $e);
        }
    }

    public function testNotify()
    {
        $observer = $this->createMock(\SplObserver::class);
        $observer->expects($this->exactly(4))->method('update');

        $paginator = new Paginator();

        $paginator->attach($observer);

        $paginator->setPageSize(2);
        $paginator->setPageSize(2);
        $paginator->setPageSize(3);

        $paginator->setCurrentPage(3);
        $paginator->setCurrentPage(4);
        $paginator->setCurrentPage(4);
    }
}