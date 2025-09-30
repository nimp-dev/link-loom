<?php

namespace Nimp\LinkLoom\observer\events;

use Nimp\LinkLoom\interfaces\NamedEventInterface;
use Nimp\LinkLoom\UrlShortener;

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