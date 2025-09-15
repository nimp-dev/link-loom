<?php

namespace Nimp\LinkLoom\observer\events;

use Nimp\LinkLoom\UrlShortener;

class BaseShortenerEvent implements Event
{
    public UrlShortener $context;
    public string $eventName;

    public function __construct(string $name, UrlShortener $context)
    {
        $this->context = $context;
        $this->eventName = $name;
    }
    public function getName(): string
    {
        return $this->eventName;
    }
}