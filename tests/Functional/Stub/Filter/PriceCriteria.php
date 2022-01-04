<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Functional\Stub\Filter;

use DevZer0x00\DataProvider\Filter\CriteriaAbstract;
use Doctrine\Common\Collections\Criteria;

class PriceCriteria extends CriteriaAbstract
{
    private ?float $minPrice;

    private ?float $maxPrice;

    public function __construct()
    {
        parent::__construct('price');
    }

    public function getMinPrice(): ?float
    {
        return $this->minPrice;
    }

    public function setMinPrice(?float $minPrice): PriceCriteria
    {
        $this->minPrice = $minPrice;

        $this->notify();

        return $this;
    }

    public function getMaxPrice(): ?float
    {
        return $this->maxPrice;
    }

    public function setMaxPrice(?float $maxPrice): PriceCriteria
    {
        $this->maxPrice = $maxPrice;

        $this->notify();

        return $this;
    }

    public function getCriteria(): Criteria
    {
        $criteria = new Criteria();

        if (!empty($this->minPrice)) {
            $criteria->andWhere($criteria::expr()->gte('price', $this->minPrice));
        }

        if (!empty($this->maxPrice)) {
            $criteria->andWhere($criteria::expr()->lte('price', $this->maxPrice));
        }

        return $criteria;
    }
}