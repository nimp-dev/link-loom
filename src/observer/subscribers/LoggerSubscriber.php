<?php

namespace Nimp\LinkLoom\observer\subscribers;

use Nimp\LinkLoom\observer\events\BaseShortenerEvent;
use Nimp\LinkLoom\observer\events\DecodeStartEvent;
use Nimp\LinkLoom\observer\events\DecodeSuccessEvent;
use Nimp\LinkLoom\observer\events\EncodeStartEvent;
use Nimp\LinkLoom\observer\events\EncodeSuccessEvent;
use Nimp\LinkLoom\observer\events\GetFromStorageErrorEvent;
use Nimp\LinkLoom\observer\events\SaveErrorEvent;
use Nimp\LinkLoom\observer\events\ValidateErrorEvent;
use Nimp\LinkLoom\UrlShortener;
use Psr\Log\LoggerInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function events(): iterable
    {
        yield EncodeStartEvent::class => $this->startEncode(...);
        yield EncodeSuccessEvent::class => $this->startEncode(...);
        yield DecodeStartEvent::class => $this->startEncode(...);
        yield DecodeSuccessEvent::class => $this->startEncode(...);
        yield ValidateErrorEvent::class => $this->startEncode(...);
        yield GetFromStorageErrorEvent::class => $this->startEncode(...);
        yield SaveErrorEvent::class => $this->startEncode(...);
    }

    public function startEncode(BaseShortenerEvent $event): void
    {
        $entry = date("Y-m-d H:i:s") . $event::class . " with data '" . json_encode($event->payload ?? []) . "'\n";
        $this->logger->info($entry);
    }
}
