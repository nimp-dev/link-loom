<?php

namespace Nimp\LinkLoom\observer\events;

use Nimp\LinkLoom\interfaces\NamedEventInterface;
use Nimp\LinkLoom\UrlShortenerInterfaceInterface;

abstract class BaseShortenerEvent implements NamedEventInterface
{

    public function __construct(
        public readonly UrlShortenerInterfaceInterface $context,
    ) {}

    public function eventName(): string|null
    {
        return static::class;
    }

}