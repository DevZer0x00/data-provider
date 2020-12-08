<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Unit\Sorter;

use DevZer0x00\DataProvider\Filter\CriteriaAbstract;
use PHPUnit\Framework\TestCase;

class CriteriaAbstractTest extends TestCase
{
    public function testGetName()
    {
        $criteria = $this->getMockForAbstractClass(CriteriaAbstract::class, ['t']);

        $this->assertEquals('t', $criteria->getName());
    }
}