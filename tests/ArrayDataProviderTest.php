<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests;

use DevZer0x00\DataProvider\ArrayDataProvider;
use DevZer0x00\DataProvider\Paginator;
use PHPUnit\Framework\TestCase;

class ArrayDataProviderTest extends TestCase
{
    public function testPaginator()
    {
        $originalData = [
            [1],
            [2],
            [3],
            [4],
            [5]
        ];

        $paginator = $this->createMock(Paginator::class);
        $provider = new ArrayDataProvider(
            [
                'originalData' => $originalData,
            ]
        );

        $this->assertNull($provider->getPaginator());
        $this->assertEquals($originalData, $provider->getData());

        $provider->setPaginator($paginator);
        $this->assertSame($paginator, $provider->getPaginator());

        $paginator->method('getPageSize')->willReturn(2);
        $paginator->method('getCurrentPage')->willReturn(2);

        $this->assertEquals([[3], [4]], $provider->getData());

        $paginator = $this->createMock(Paginator::class);
        $paginator->method('getPageSize')->willReturn(10);
        $paginator->method('getCurrentPage')->willReturn(2);

        $provider->setPaginator($paginator);
        $this->assertEmpty($provider->getData());

        $paginator = $this->createMock(Paginator::class);
        $paginator->expects($this->once())
            ->method('attach')
            ->with($provider);
        $paginator->expects($this->once())
            ->method('detach')
            ->with($provider);

        $provider->setPaginator($paginator);
        $provider->setPaginator($this->createMock(Paginator::class));
    }
}