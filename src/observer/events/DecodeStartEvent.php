<?php

namespace Nimp\LinkLoom\observer\events;

use Nimp\LinkLoom\observer\events\BaseShortenerEvent;
use Nimp\LinkLoom\UrlShortenerInterfaceInterface;

class DecodeStartEvent extends BaseShortenerEvent
{

    public readonly string $code;
    public function __construct(UrlShortenerInterfaceInterface $context, string $code)
    {
        $this->code = $code;
        parent::__construct($context);
    }
}