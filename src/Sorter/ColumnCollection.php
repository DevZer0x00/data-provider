<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Sorter;

use ArrayIterator;
use Countable;
use DevZer0x00\DataProvider\Exception\NonUniqueColumnException;
use DevZer0x00\DataProvider\Traits\ObserverableTrait;
use IteratorAggregate;
use SplObserver;
use SplSubject;
use function count;

/**
 * @TODO доделать column priority при множественных полях сортировки
 */
class ColumnCollection implements Countable, IteratorAggregate, SplObserver, SplSubject
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

    public function add(Column $column): void
    {
        if (isset($this->columns[$column->getName()])) {
            throw new NonUniqueColumnException(sprintf('Column with name - %s already exists', $column->getName()));
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

    public function update(SplSubject $subject): void
    {
        $this->notify();
    }
}
