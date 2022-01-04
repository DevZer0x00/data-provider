<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider;

interface DataProviderInterface
{
    public function getSorter(): ?Sorter;

    public function setSorter(?Sorter $sorter): self;

    public function getPaginator(): ?Paginator;

    public function setPaginator(?Paginator $paginator): self;

    public function getFilter(): ?Filter;

    public function setFilter(?Filter $filter): self;

    public function getData(): iterable;
}
