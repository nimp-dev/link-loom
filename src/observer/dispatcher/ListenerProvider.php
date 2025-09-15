<?php

namespace Nimp\LinkLoom\observer\dispatcher;

use Nimp\LinkLoom\observer\events\BaseShortenerEvent;
use Nimp\LinkLoom\observer\subscribers\EventSubscriberInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    private array $listeners = [];

    public function addSubscriber(EventSubscriberInterface $subscriber): void
    {
        foreach ($subscriber->events() as $eventName => $handler) {
            if (is_string($handler)) {
                // ['eventName' => 'methodName']
                $this->listeners[$eventName][] = [$subscriber, $handler];
            } elseif (is_callable($handler)) {
                // ['eventName' => $this->method(...)]
                $this->listeners[$eventName][] = $handler;
            }
        }
    }

    /**
     * @param object $event
     * @return iterable
     */
    public function getListenersForEvent(object $event): iterable
    {
        return $this->listeners[get_class($event)] ?? [];
    }
}