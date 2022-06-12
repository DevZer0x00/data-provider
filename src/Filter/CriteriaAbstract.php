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

    private ?string $criteriaHash;

    public function __construct(string $name, string $prefix = '', ?string $criteriaHash = null)
    {
        $this->name = $name;
        $this->prefix = $prefix;
        $this->criteriaHash = $criteriaHash;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCriteriaHash(): string
    {
        if (empty($this->criteriaHash)) {
            $this->criteriaHash = $this->prefix . '.' . $this->name;
        }

        return $this->criteriaHash;
    }

    abstract public function getCriteria(): Criteria;
}
