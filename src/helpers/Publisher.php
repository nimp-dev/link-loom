<?php

namespace Nimp\LinkLoom\helpers;

use SplObserver;
use SplSubject;

class Publisher implements SplSubject
{
    /**
     * Наблюдатели (Слушатели событий)
     * *
     * @var array $subjects
     */
    protected array $observers;

    /**
     * @param string $event группа, которой нужно слушать все события
     * @return void
     */
    private function initEventGroup(string $event = "*"): void
    {
        if (!isset($this->observers[$event])) {
            $this->observers[$event] = [];
        }
    }

    /**
     * @param SplObserver $observer
     * @param string $event
     * @return void
     */
    public function attach(SplObserver $observer, string $event = "*"): void
    {
        $this->initEventGroup($event);

        $this->observers[$event][] = $observer;
    }

    public function detach(SplObserver $observer, string $event = "*"): void
    {
        foreach ($this->getEventObservers($event) as $key => $s) {
            if ($s === $observer) {
                unset($this->observers[$event][$key]);
            }
        }
    }

    private function getEventObservers(string $event = "*"): array
    {
        $this->initEventGroup($event);
        $group = $this->observers[$event];
        $all = $this->observers["*"];

        return array_merge($group, $all);
    }

    public function notify(string $event = "*", $data = null): void
    {
        foreach ($this->getEventObservers($event) as $observer) {
            /** @var SplObserver $observer */
            $observer->update($this, $event, $data);
        }
    }
}