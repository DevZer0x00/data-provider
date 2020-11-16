<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider;

use DevZer0x00\DataProvider\Traits\ConfigurableTrait;
use SplSubject;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SplObserver;

class ArrayDataProvider implements SplObserver
{
    use ConfigurableTrait;

    private ?Sorter $sorter = null;

    private ?Paginator $paginator = null;

    private array $originalData;

    private array $data;

    private bool $initialized = false;

    /**
     * @inheritDoc
     */
    public function update(SplSubject $subject)
    {
        $this->refresh();
    }

    public function getSorter(): ?Sorter
    {
        return $this->sorter;
    }

    public function setSorter(?Sorter $sorter): ArrayDataProvider
    {
        $this->refresh();

        $this->sorter = $sorter;

        return $this;
    }

    public function getPaginator(): ?Paginator
    {
        return $this->paginator;
    }

    public function setPaginator(?Paginator $paginator): ArrayDataProvider
    {
        $this->refresh();

        if ($this->paginator !== $paginator) {
            if (!empty($this->paginator)) {
                $this->paginator->detach($this);
            }

            $paginator->attach($this);
        }

        $this->paginator = $paginator;

        return $this;
    }

    public function getData(): array
    {
        $this->initializeData();

        return $this->data;
    }

    public function setOriginalData(array $originalData): self
    {
        $this->refresh();

        $this->originalData = $originalData;

        return $this;
    }

    protected function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired('originalData');
        $resolver->setDefined(['sorter', 'paginator']);

        $resolver->setAllowedTypes('originalData', 'array')
            ->setAllowedTypes('sorter', ['null', Sorter::class])
            ->setAllowedTypes('paginator', ['null', Paginator::class]);

        return $resolver;
    }

    private function refresh(): self
    {
        $this->initialized = false;

        return $this;
    }

    private function initializeData(): self
    {
        if ($this->initialized) {
            return $this;
        }

        $this->initialized = true;

        $data = $this->originalData;

        if ($paginator = $this->getPaginator()) {
            $data = array_slice(
                $data,
                $paginator->getPageSize() * ($paginator->getCurrentPage() - 1),
                $paginator->getPageSize()
            );
        }

        $this->data = $data;

        return $this;
    }
}