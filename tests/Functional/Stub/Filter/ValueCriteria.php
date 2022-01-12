<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Functional\Stub\Filter;

use DevZer0x00\DataProvider\Filter\CriteriaAbstract;
use Doctrine\Common\Collections\Criteria;

class ValueCriteria extends CriteriaAbstract
{
    private ?float $minValue;

    private ?float $maxValue;

    public function __construct()
    {
        parent::__construct('value');
    }

    public function getMinValue(): ?float
    {
        return $this->minValue;
    }

    public function setMinValue(?float $minValue): self
    {
        $this->minValue = $minValue;

        $this->notify();

        return $this;
    }

    public function getMaxValue(): ?float
    {
        return $this->maxValue;
    }

    public function setMaxValue(?float $maxValue): self
    {
        $this->maxValue = $maxValue;

        $this->notify();

        return $this;
    }

    public function getCriteria(): Criteria
    {
        $criteria = new Criteria();

        if (!empty($this->minValue)) {
            $criteria->andWhere($criteria::expr()->gte('value', $this->minValue));
        }

        if (!empty($this->maxValue)) {
            $criteria->andWhere($criteria::expr()->lte('value', $this->maxValue));
        }

        return $criteria;
    }
}
