<?php

namespace Nimp\LinkLoom\observer\events;

use Nimp\LinkLoom\UrlShortener;
use Nimp\LinkLoom\observer\events\NamedEventInterface;

abstract class BaseShortenerEvent implements NamedEventInterface
{

    public function __construct(
        public readonly UrlShortener $context,
    ) {}

    public function eventName(): string|null
    {
        return static::class;
    }

}