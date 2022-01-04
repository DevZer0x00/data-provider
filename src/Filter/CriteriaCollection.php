<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Filter;

use DevZer0x00\DataProvider\Exception\NonUniqueCriteriaException;
use DevZer0x00\DataProvider\Traits\ObserverableTrait;
use IteratorAggregate;
use ArrayIterator;
use Countable;
use SplSubject;
use SplObserver;

class CriteriaCollection implements IteratorAggregate, Countable, SplSubject, SplObserver
{
    use ObserverableTrait;

    private array $criteria;

    public function __construct(array $criterias = [])
    {
        $this->criteria = [];

        foreach ($criterias as $criteria) {
            $this->addCriteria($criteria);
        }
    }
    
    public function addCriteria(CriteriaAbstract $criteriaAbstract): self
    {
        if (isset($this->criteria[$criteriaAbstract->getName()])) {
            throw new NonUniqueCriteriaException(
                sprintf('Criteria with name - %s already exists', $criteriaAbstract->getName())
            );
        }

        $criteriaAbstract->attach($this);
        $this->criteria[$criteriaAbstract->getName()] = $criteriaAbstract;

        $this->notify();

        return $this;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->criteria);
    }

    public function count()
    {
        return count($this->criteria);
    }

    /**
     * @inheritDoc
     */
    public function update(SplSubject $subject)
    {
        $this->notify();
    }
}