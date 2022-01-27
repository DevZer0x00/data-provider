<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider;

use DevZer0x00\DataProvider\Exception\InvalidArgumentException;
use DevZer0x00\DataProvider\Traits\ConfigurableTrait;
use DevZer0x00\DataProvider\Traits\ObserverableTrait;
use SplSubject;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Paginator implements SplSubject
{
    use ConfigurableTrait;
    use ObserverableTrait;

    private int $pageSize;

    private int $currentPage;

    private int $totalCount;

    protected function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setDefined([
            'pageSize',
            'currentPage',
            'totalCount',
        ]);

        $resolver->setAllowedTypes('pageSize', 'int')
            ->setAllowedTypes('currentPage', 'int')
            ->setAllowedTypes('totalCount', 'int');

        $resolver->setAllowedValues('pageSize', fn ($value) => $value > 0)
            ->setAllowedValues('currentPage', fn ($value) => $value >= 1)
            ->setAllowedValues('totalCount', fn ($value) => $value >= 0);

        $resolver->setDefaults([
            'currentPage' => 1,
        ]);

        return $resolver;
    }

    /**
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setPageSize(int $pageSize): self
    {
        if ($pageSize <= 0) {
            throw new InvalidArgumentException('Page size must be greater that 0');
        }

        $oldSize = $this->pageSize ?? null;

        $this->pageSize = $pageSize;

        if ($oldSize !== $pageSize) {
            $this->notify();
        }

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setCurrentPage(int $currentPage): self
    {
        if ($currentPage < 1) {
            throw new InvalidArgumentException('Current page must be greater or equals 1');
        }

        $oldPage = $this->currentPage ?? null;

        $this->currentPage = $currentPage;

        if ($oldPage !== $this->currentPage) {
            $this->notify();
        }

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setTotalCount(int $totalCount): self
    {
        if ($totalCount < 0) {
            throw new InvalidArgumentException('Total count must be greater or equals 0');
        }

        $this->totalCount = $totalCount;

        return $this;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getPageCount(): int
    {
        return (int)ceil($this->totalCount / $this->pageSize);
    }
}
