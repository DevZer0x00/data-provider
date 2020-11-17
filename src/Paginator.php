<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider;

use DevZer0x00\DataProvider\Exception\InvalidArgumentException;
use DevZer0x00\DataProvider\Traits\ConfigurableTrait;
use DevZer0x00\DataProvider\Traits\ObserverableTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SplSubject;

class Paginator implements SplSubject
{
    use ConfigurableTrait, ObserverableTrait;

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

        $resolver->setAllowedValues('pageSize', function ($value) {
                return $value > 0;
            })->setAllowedValues('currentPage', function ($value) {
                return $value >= 1;
            })->setAllowedValues('totalCount', function ($value) {
                return $value >= 0;
            });

        return $resolver;
    }

    /**
     * @param int $pageSize
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setPageSize(int $pageSize): Paginator
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
     * @param int $currentPage
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setCurrentPage(int $currentPage): Paginator
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
     * @param int $totalCount
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setTotalCount(int $totalCount): Paginator
    {
        if ($totalCount < 0) {
            throw new InvalidArgumentException('Total count must be greater or equals 0');
        }

        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @return int
     */
    public function getPageCount(): int
    {
        return (int)ceil($this->totalCount / $this->pageSize);
    }
}