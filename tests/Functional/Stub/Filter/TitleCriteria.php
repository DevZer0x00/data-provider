<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Tests\Functional\Stub\Filter;

use DevZer0x00\DataProvider\Filter\CriteriaAbstract;
use Doctrine\Common\Collections\Criteria;

class TitleCriteria extends CriteriaAbstract
{
    private ?string $value;

    public function __construct()
    {
        parent::__construct('title');
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        $this->notify();

        return $this;
    }

    public function getCriteria(): Criteria
    {
        $criteria = new Criteria();

        if (!empty($this->value)) {
            $criteria->where($criteria::expr()->contains('title', $this->getValue()));
        }

        return $criteria;
    }
}
