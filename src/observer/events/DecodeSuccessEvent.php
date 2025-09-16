<?php

namespace Nimp\LinkLoom\observer\events;

use Nimp\LinkLoom\observer\events\BaseShortenerEvent;
use Nimp\LinkLoom\UrlShortener;

class DecodeSuccessEvent extends BaseShortenerEvent
{
    public readonly string $url;
    public function __construct(UrlShortener $context, string $url)
    {
        $this->url = $url;
        parent::__construct($context);
    }
}