<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Unit\Sorter;

use DevZer0x00\DataProvider\Filter\CriteriaAbstract;
use Codeception\Test\Unit;

class CriteriaAbstractTest extends Unit
{
    public function testGetName()
    {
        $criteria = $this->getMockForAbstractClass(CriteriaAbstract::class, ['t']);

        $this->assertEquals('t', $criteria->getName());
    }
}