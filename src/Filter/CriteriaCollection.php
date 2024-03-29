<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Filter;

use ArrayIterator;
use Countable;
use DevZer0x00\DataProvider\Exception\NonUniqueCriteriaException;
use DevZer0x00\DataProvider\Traits\ObserverableTrait;
use IteratorAggregate;
use SplObserver;
use SplSubject;
use function count;

class CriteriaCollection implements Countable, IteratorAggregate, SplObserver, SplSubject
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

    public function findByHash(string $hash): ?CriteriaAbstract
    {
        return $this->criteria[$hash] ?? null;
    }

    public function addCriteria(CriteriaAbstract $criteriaAbstract): self
    {
        if (isset($this->criteria[$criteriaAbstract->getCriteriaHash()])) {
            throw new NonUniqueCriteriaException(sprintf('Criteria with hash - %s already exists', $criteriaAbstract->getCriteriaHash()));
        }

        $criteriaAbstract->attach($this);
        $this->criteria[$criteriaAbstract->getCriteriaHash()] = $criteriaAbstract;

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
     * {@inheritDoc}
     */
    public function update(SplSubject $subject): void
    {
        $this->notify();
    }
}
