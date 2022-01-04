<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Filter;

use DevZer0x00\DataProvider\Traits\ObserverableTrait;
use Doctrine\Common\Collections\Criteria;
use SplSubject;

abstract class CriteriaAbstract implements SplSubject
{
    use ObserverableTrait;

    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract public function getCriteria(): Criteria;
}
