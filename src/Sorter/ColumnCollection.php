<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Sorter;

use DevZer0x00\DataProvider\Exception\NonUniqueColumnException;
use ArrayIterator;

class ColumnCollection implements \IteratorAggregate, \Countable
{
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

        $this->columns[$column->getName()] = $column;
    }

    public function first(): ?Column
    {
        return current($this->columns);
    }

    public function findSorted(): self
    {
        $columns = [];

        foreach ($this->columns as $column) {
            if ($column->isSorted()) {
                $columns[] = $column;
            }
        }

        return new self($columns);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->columns);
    }


    public function count()
    {
        return count($this->columns);
    }
}