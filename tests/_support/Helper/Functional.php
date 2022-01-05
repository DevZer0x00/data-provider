<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Helper;

use Doctrine\DBAL\Query\QueryBuilder as DbalQueryBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class Functional extends \Codeception\Module
{
    public static function _createEntityManager(): EntityManager
    {
        return EntityManager::create(
            [
                'driver' => 'pdo_mysql',
                'host' => 'dataprovider-mysql',
                'user' => 'user',
                'password' => 'pass',
                'dbname' => 'test',
            ],
            Setup::createAnnotationMetadataConfiguration([], true)
        );
    }

    public function assertDbalQBResult(DbalQueryBuilder $qb1, DbalQueryBuilder $qb2): void
    {
        $this->assertSame($qb1->execute()->fetchAllAssociative(), $qb2->execute()->fetchAllAssociative());
    }
}
