<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider;

class Sorter
{
    const SORT_ASC = 'asc';

    const SORT_DESC = 'desc';

    /**
     * $columnSettings = [
     *    "column_name_1" => [
     *        SorterInterface::SORT_ASC => [
     *          "field_name1" => SorterInterface::SORT_ASC, "field_name2" => SorterInterface::SORT_DESC
     *        ],
     *        SorterInterface::SORT_DESC => [
     *          "field_name1" => SorterInterface::SORT_DESC, "field_name2" => SorterInterface::SORT_ASC
     *        ],
     *        "default" => SORT_ASC
     *     ],
     *     "column_name_2",
     *     .....
     *     "column_name_N"
     * ]
     *
     * @param array $columnSettings
     * @return $this
     */
    public function setAvailableOrderBy(array $columnSettings): self
    {

    }

    /**
     * [
     * "column_name_1" => [
     *     SorterInterface::SORT_ASC => [
     *        "field_name1" => SorterInterface::SORT_ASC, "field_name2" => SorterInterface::SORT_DESC
     *     ],
     *     SorterInterface::SORT_DESC => [
     *        "field_name1" => SorterInterface::SORT_DESC, "field_name2" => SorterInterface::SORT_ASC
     *     ],
     *        "default" => SORT_ASC
     *     ],
     * ]
     *
     * @return array
     */
    public function getAvailableOrderBy(): array
    {

    }

    public function setDefaultOrderBy(array $columns)
    {

    }

    public function getDefaultOrderBy(): array
    {

    }
    
    public function setOrderBy(array $columns): self
    {

    }

    public function getOrderBy(): array
    {

    }
}