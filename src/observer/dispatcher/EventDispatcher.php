<?php

namespace Nimp\LinkLoom\observer\dispatcher;

use Nimp\LinkLoom\observer\events\Event;
use Nimp\LinkLoom\observer\subscribers\EventSubscriberInterface;

class EventDispatcher
{
    private array $listeners = [];

    /**
     * @param string $eventName
     * @param callable $listener
     * @return void
     */
    protected function addListener(string $eventName, callable $listener): void
    {
        $this->listeners[$eventName][] = $listener;
    }

    /**
     * @param EventSubscriberInterface $subscriber
     * @return void
     */
    public function addSubscriber(EventSubscriberInterface $subscriber): void
    {
        foreach ($subscriber->events() as $eventName => $handler) {
            if (is_string($handler)) {
                // ['eventName' => 'methodName']
                $this->addListener($eventName, [$subscriber, $handler]);
            } elseif (is_callable($handler)) {
                // ['eventName' => $this->method(...)]
                $this->addListener($eventName, $handler);
            }
        }
    }

    /**
     * @param Event $event
     * @return void
     */
    public function dispatch(Event $event): void
    {
        $eventName = $event->getName();

        if (empty($this->listeners[$eventName])) {
            return;
        }

        foreach ($this->listeners[$eventName] as $listener) {
            $listener($event);
        }
    }
}
