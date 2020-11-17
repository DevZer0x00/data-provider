<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Sorter;

use DevZer0x00\DataProvider\Exception\InvalidArgumentException;
use DevZer0x00\DataProvider\Sorter;
use DevZer0x00\DataProvider\Traits\ObserverableTrait;
use SplSubject;

class Column implements SplSubject
{
    use ObserverableTrait;

    private string $name;

    private array $orderSettings;

    private ?string $direction = null;

    public function __construct(
        string $name,
        array $orderSettings = []
    ) {
        $this->name = $name;
        $this->orderSettings = $this->prepareOrderSettings($orderSettings);
    }

    /**
     * Возвращает имя колонки
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function isSorted() : bool
    {
        return !is_null($this->getDirection());
    }

    /**
     * Возвращает текущее направление сортировки, или null - если на данный момент
     * по колонке нету сортировки
     *
     * @return string|null
     */
    public function getDirection(): ?string
    {
        return $this->direction;
    }

    /**
     * Возвращает поля для сортировки, если таковые имеются на данный момент.
     *
     * @return array|null Return ["field1" => Sorter::SORT_DESC, "field2" => Sorter::SORT_ASC ...]
     */
    public function getOrderByFields(): ?array
    {
        $direction = $this->getDirection();

        if ($direction === null) {
            return null;
        }

        return $this->orderSettings[$direction];
    }

    /**
     * Устанавливает направление сортировки
     *
     * @param string|null $direction
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setDirection(?string $direction): Column
    {
        if ($direction !== null) {
            $this->validateDirection($direction);
        }

        $oldDirection = $this->direction;
        $this->direction = $direction;

        if ($oldDirection !== $direction) {
            $this->notify();
        }

        return $this;
    }

    private function prepareOrderSettings(array $orderSettings): array
    {
        if (empty($orderSettings)) {
            return [
                Sorter::SORT_ASC => [
                    $this->name => Sorter::SORT_ASC
                ],
                Sorter::SORT_DESC => [
                    $this->name => Sorter::SORT_DESC
                ],
            ];
        }

        $result = [];

        if (count($orderSettings) !== 2) {
            throw new InvalidArgumentException();
        }

        foreach ($orderSettings as $direction => $fields) {
            $this->validateDirection($direction);

            foreach ($fields as $field => $fieldDirection) {
                if (is_int($field)) {
                    $result[$direction][$fieldDirection] = $direction;
                } else {
                    $this->validateDirection($fieldDirection);
                    $result[$direction][$field] = $fieldDirection;
                }
            }
        }

        return $result;
    }

    private function validateDirection($direction): void
    {
        if (!in_array($direction, [Sorter::SORT_ASC, Sorter::SORT_DESC], true)) {
            throw new InvalidArgumentException(sprintf('Invalid direction - %s', $direction));
        }
    }
}