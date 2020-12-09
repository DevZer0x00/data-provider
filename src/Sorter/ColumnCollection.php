<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Sorter;

use DevZer0x00\DataProvider\Exception\NonUniqueColumnException;
use ArrayIterator;
use DevZer0x00\DataProvider\Traits\ObserverableTrait;
use IteratorAggregate;
use Countable;
use SplObserver;
use SplSubject;

class ColumnCollection implements IteratorAggregate, Countable, SplObserver, SplSubject
{
    use ObserverableTrait;

    /**
     * @var array|Column[]
     */
    private array $columns = [];

    public function __construct(array $columns = [])
    {
        foreach ($columns as $column) {
            $this->add($column);
        }
    }

    public function add(Column $column)
    {
        if (isset($this->columns[$column->getName()])) {
            throw new NonUniqueColumnException(
                sprintf('Column with name - %s already exists', $column->getName())
            );
        }

        $column->attach($this);

        $this->columns[$column->getName()] = $column;

        $this->notify();
    }

    public function first(): ?Column
    {
        $column = reset($this->columns);

        return $column ? $column : null;
    }

    public function findSortable(): self
    {
        $columns = [];

        foreach ($this->columns as $column) {
            if ($column->isSorted()) {
                $columns[] = $column;
            }
        }

        return new self($columns);
    }

    public function reduceToFirstColumn(): self
    {
        return new self($this->first() ? [$this->first()] : []);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->columns);
    }


    public function count()
    {
        return count($this->columns);
    }

    public function update(SplSubject $subject)
    {
        $this->notify();
    }
}