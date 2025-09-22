<?php

namespace Nimp\LinkLoom\observer\events;

use Nimp\LinkLoom\observer\events\BaseShortenerEvent;
use Nimp\LinkLoom\UrlShortenerInterfaceInterface;

class ValidateErrorEvent extends BaseShortenerEvent
{

    public readonly string $url;
    public readonly string $message;
    public function __construct(UrlShortenerInterfaceInterface $context, string $url , string $message)
    {
        $this->url = $url;
        $this->message = $message;
        parent::__construct($context);
    }

}