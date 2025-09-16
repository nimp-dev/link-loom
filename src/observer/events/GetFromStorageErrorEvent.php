<?php

namespace Nimp\LinkLoom\observer\events;

use Nimp\LinkLoom\observer\events\BaseShortenerEvent;
use Nimp\LinkLoom\UrlShortener;

class GetFromStorageErrorEvent extends BaseShortenerEvent
{
    public readonly string $code;
    public readonly string $message;

    public function __construct(UrlShortener $context, string $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
        parent::__construct($context);
    }
}