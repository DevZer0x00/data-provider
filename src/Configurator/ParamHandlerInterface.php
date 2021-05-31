<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Configurator;

interface ParamHandlerInterface
{
    public function getPaginatorParams(): array;

    public function getSorterParams(): array;

    public function getFilterParams(): array;
}