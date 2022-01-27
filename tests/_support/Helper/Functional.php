<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Helper;

use Doctrine\DBAL\Query\QueryBuilder as DbalQueryBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Tools\Setup;

class Functional extends \Codeception\Module
{
    public static function _createEntityManager(): EntityManager
    {
        $driver = new AttributeDriver([__DIR__ . '/../../Functional/Stub/Entity']);
        $config = Setup::createConfiguration(true);
        $config->setMetadataDriverImpl($driver);

        return EntityManager::create(
            [
                'driver' => 'pdo_mysql',
                'host' => 'dataprovider-mysql',
                'user' => 'user',
                'password' => 'pass',
                'dbname' => 'test',
            ],
            $config
        );
    }

    public function assertDbalQBResult(DbalQueryBuilder $qb1, DbalQueryBuilder $qb2): void
    {
        $this->assertSame($qb1->execute()->fetchAllAssociative(), $qb2->execute()->fetchAllAssociative());
    }
}
