<?php

namespace Nimp\LinkLoom\observer\events;

use Nimp\LinkLoom\observer\events\BaseShortenerEvent;
use Nimp\LinkLoom\UrlShortener;

class SaveErrorEvent extends BaseShortenerEvent
{
    public readonly string $message;
    public function __construct(UrlShortener $context, string $message)
    {
        $this->message = $message;
        parent::__construct($context);
    }
}