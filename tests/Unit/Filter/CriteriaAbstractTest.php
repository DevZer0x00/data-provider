<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Unit\Sorter;

use Codeception\Test\Unit;
use DevZer0x00\DataProvider\Filter\CriteriaAbstract;

/**
 * @internal
 * @coversNothing
 */
final class CriteriaAbstractTest extends Unit
{
    public function testGetName(): void
    {
        $criteria = $this->getMockForAbstractClass(CriteriaAbstract::class, ['t']);

        $this->assertEquals('t', $criteria->getName());
    }
}
