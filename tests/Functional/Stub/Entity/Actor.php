<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Functional\Stub\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'actor')]
class Actor
{
    #[ORM\Id]
    #[ORM\Column(name: 'actor_id', type: 'integer', nullable: false)]
    public int $actorId;

    #[ORM\Column(name: 'first_name', type: 'string', nullable: false)]
    public string $firstName;

    #[ORM\Column(name: 'last_name', type: 'string', nullable: false)]
    public string $lastName;

    #[ORM\Column(name: 'last_update', type: 'datetime', nullable: false)]
    public DateTime $lastUpdate;
}
