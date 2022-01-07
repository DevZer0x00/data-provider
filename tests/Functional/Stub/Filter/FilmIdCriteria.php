<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Functional\Stub\Filter;

use DevZer0x00\DataProvider\Filter\CriteriaAbstract;
use Doctrine\Common\Collections\Criteria;

class FilmIdCriteria extends CriteriaAbstract
{
    private ?array $value;

    public function __construct()
    {
        parent::__construct('film_id');
    }

    public function getValue(): ?array
    {
        return $this->value;
    }

    public function setValue(?array $value): self
    {
        $this->value = $value;

        $this->notify();

        return $this;
    }

    public function getCriteria(): Criteria
    {
        $criteria = new Criteria();

        if (!empty($this->value)) {
            $criteria->where($criteria::expr()->in('film_id', $this->getValue()));
        }

        return $criteria;
    }
}
