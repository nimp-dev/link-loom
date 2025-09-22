<?php

namespace Nimp\LinkLoom\observer\events;

use Nimp\LinkLoom\observer\events\BaseShortenerEvent;
use Nimp\LinkLoom\UrlShortenerInterfaceInterface;

class EncodeStartEvent extends BaseShortenerEvent
{
    public readonly string $url;
    public function __construct(UrlShortenerInterfaceInterface $context, string $url)
    {
        $this->url = $url;
        parent::__construct($context);
    }
}