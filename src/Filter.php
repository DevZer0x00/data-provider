<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider;

use DevZer0x00\DataProvider\Filter\CriteriaCollection;
use DevZer0x00\DataProvider\Traits\ConfigurableTrait;
use DevZer0x00\DataProvider\Traits\ObserverableTrait;
use SplObserver;
use SplSubject;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Filter implements SplObserver, SplSubject
{
    use ConfigurableTrait;
    use ObserverableTrait;

    private CriteriaCollection $criteriaCollection;

    protected function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setDefined(['criteriaCollection']);

        $resolver->setAllowedTypes('criteriaCollection', CriteriaCollection::class);

        return $resolver;
    }

    public function getCriteriaCollection(): CriteriaCollection
    {
        return $this->criteriaCollection;
    }

    public function setCriteriaCollection(CriteriaCollection $criteriaCollection): self
    {
        $oldCollection = $this->criteriaCollection ?? null;

        if (empty($this->criteriaCollection)) {
            $criteriaCollection->attach($this);
        } elseif ($this->criteriaCollection !== $criteriaCollection) {
            $criteriaCollection->attach($this);
            $this->criteriaCollection->detach($this);
        }

        $this->criteriaCollection = $criteriaCollection;

        if ($oldCollection !== $criteriaCollection) {
            $this->notify();
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function update(SplSubject $subject): void
    {
        $this->notify();
    }
}
