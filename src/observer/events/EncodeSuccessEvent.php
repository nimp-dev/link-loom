<?php

namespace Nimp\LinkLoom\observer\events;

use Nimp\LinkLoom\observer\events\BaseShortenerEvent;
use Nimp\LinkLoom\UrlShortener;

class EncodeSuccessEvent extends BaseShortenerEvent
{
    public readonly string $code;
    public function __construct(UrlShortener $context, string $code)
    {
        $this->code = $code;
        parent::__construct($context);
    }
}