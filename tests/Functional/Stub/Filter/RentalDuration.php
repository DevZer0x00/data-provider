<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Functional\Stub\Filter;

use DevZer0x00\DataProvider\Filter\CriteriaAbstract;
use Doctrine\Common\Collections\Criteria;

class RentalDuration extends CriteriaAbstract
{
    private ?int $minDuration;

    private ?int $maxDuration;

    public function __construct()
    {
        parent::__construct('rental_duration');
    }

    public function getMinDuration(): ?int
    {
        return $this->minDuration;
    }

    public function setMinDuration(?int $minDuration): self
    {
        $this->minDuration = $minDuration;

        return $this;
    }

    public function getMaxDuration(): ?int
    {
        return $this->maxDuration;
    }

    public function setMaxDuration(?int $maxDuration): self
    {
        $this->maxDuration = $maxDuration;

        return $this;
    }

    public function getCriteria(): Criteria
    {
        $criteria = new Criteria();

        if ($this->minDuration !== null) {
            $criteria->andWhere(Criteria::expr()->gte($this->getName(), $this->minDuration));
        }

        if ($this->maxDuration !== null) {
            $criteria->andWhere(Criteria::expr()->lte($this->getName(), $this->maxDuration));
        }

        return $criteria;
    }
}
