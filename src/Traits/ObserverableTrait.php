<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Traits;

use SplObjectStorage;
use SplObserver;

trait ObserverableTrait
{
    private ?SplObjectStorage $observers = null;

    private function initObStorage(): void
    {
        if ($this->observers === null) {
            $this->observers = new SplObjectStorage();
        }
    }

    public function attach(SplObserver $observer): void
    {
        $this->initObStorage();

        $this->observers->attach($observer);
    }

    public function detach(SplObserver $observer): void
    {
        $this->initObStorage();

        $this->observers->detach($observer);
    }

    public function notify(): void
    {
        $this->initObStorage();

        /** @var SplObserver $observer */
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}
