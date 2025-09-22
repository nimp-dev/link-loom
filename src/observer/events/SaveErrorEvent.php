<?php

namespace Nimp\LinkLoom\observer\events;

use Nimp\LinkLoom\observer\events\BaseShortenerEvent;
use Nimp\LinkLoom\UrlShortenerInterfaceInterface;

class SaveErrorEvent extends BaseShortenerEvent
{
    public readonly string $message;
    public function __construct(UrlShortenerInterfaceInterface $context, string $message)
    {
        $this->message = $message;
        parent::__construct($context);
    }
}