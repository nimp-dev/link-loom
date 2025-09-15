<?php

namespace Nimp\LinkLoom\observer\events;

use Nimp\LinkLoom\observer\enums\EventEnum;
use Nimp\LinkLoom\UrlShortener;

abstract class BaseShortenerEvent
{
    public function __construct(
        public readonly UrlShortener $context,
        public readonly array $payload,

    ) {}

}