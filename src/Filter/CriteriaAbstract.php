<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Filter;

use DevZer0x00\DataProvider\Traits\ObserverableTrait;
use Doctrine\Common\Collections\Criteria;
use SplSubject;

abstract class CriteriaAbstract implements SplSubject
{
    // @TODO дописать обсервер
    use ObserverableTrait;

    private string $name;

    private string $prefix;

    public function __construct(string $name, string $prefix = '')
    {
        $this->name = $name;
        $this->prefix = $prefix;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCriteriaHash(): string
    {
        return $this->prefix . '.' . $this->name;
    }

    abstract public function getCriteria(): Criteria;
}
