<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Functional\Stub\Filter;

use DevZer0x00\DataProvider\Filter\CriteriaAbstract;
use Doctrine\Common\Collections\Criteria;

class PriceCriteria extends CriteriaAbstract
{
    private ?float $minPrice;

    private ?float $maxPrice;

    public function __construct(string $field = 'price')
    {
        parent::__construct($field);
    }

    public function getMinPrice(): ?float
    {
        return $this->minPrice;
    }

    public function setMinPrice(?float $minPrice): self
    {
        $this->minPrice = $minPrice;

        $this->notify();

        return $this;
    }

    public function getMaxPrice(): ?float
    {
        return $this->maxPrice;
    }

    public function setMaxPrice(?float $maxPrice): self
    {
        $this->maxPrice = $maxPrice;

        $this->notify();

        return $this;
    }

    public function getCriteria(): Criteria
    {
        $criteria = new Criteria();

        if (!empty($this->minPrice)) {
            $criteria->andWhere($criteria::expr()->gte($this->getName(), $this->minPrice));
        }

        if (!empty($this->maxPrice)) {
            $criteria->andWhere($criteria::expr()->lte($this->getName(), $this->maxPrice));
        }

        return $criteria;
    }
}
