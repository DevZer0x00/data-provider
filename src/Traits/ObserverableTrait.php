<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider\Traits;

use SplObserver;
use SplObjectStorage;

trait ObserverableTrait
{
    private ?SplObjectStorage $observers = null;

    private function initObStorage()
    {
        if ($this->observers === null) {
            $this->observers = new SplObjectStorage();
        }
    }

    public function attach(SplObserver $observer)
    {
        $this->initObStorage();

        $this->observers->attach($observer);
    }

    public function detach(SplObserver $observer)
    {
        $this->initObStorage();

        $this->observers->detach($observer);
    }

    public function notify()
    {
        $this->initObStorage();

        /** @var SplObserver $observer */
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}