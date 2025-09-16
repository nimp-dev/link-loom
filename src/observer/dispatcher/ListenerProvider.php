<?php

namespace Nimp\LinkLoom\observer\dispatcher;

use Nimp\LinkLoom\observer\events\BaseShortenerEvent;
use Nimp\LinkLoom\observer\events\NamedEventInterface;
use Nimp\LinkLoom\observer\subscribers\EventListenerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    /**
     * @var array
     */
    private array $listeners = [];

    /**
     * @param EventListenerInterface $listener
     * @return void
     */
    public function addListeners(EventListenerInterface $listener): void
    {
        foreach ($listener->events() as $eventName => $handler) {
            if (is_string($handler)) {
                // ['eventName' => 'methodName']
                $this->listeners[$eventName][] = [$listener, $handler];
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
        /** @var BaseShortenerEvent $event */

        if ($event instanceof NamedEventInterface) {
            $eventName = $event->eventName();
        }
        $eventName ??= $event::class;

        return $this->listeners[$eventName] ?? [];
    }
}