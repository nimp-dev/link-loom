<?php

namespace Nimp\LinkLoom\observer\subscribers;

use Nimp\LinkLoom\observer\events\Event;
use Nimp\LinkLoom\UrlShortener;
use Psr\Log\LoggerInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function events(): array
    {
        return array_fill_keys([
            UrlShortener::ENCODE_START_EVENT,
            UrlShortener::ENCODE_SUCCESS_EVENT,
            UrlShortener::DECODE_START_EVENT,
            UrlShortener::DECODE_SUCCESS_EVENT,
            UrlShortener::VALIDATE_ERROR_EVENT,
            UrlShortener::GET_FROM_STORAGE_ERROR_EVENT,
            UrlShortener::SAVE_ERROR_EVENT,
        ], $this->startEncode(...));
    }

    public function startEncode(Event $event): void
    {
        $entry = date("Y-m-d H:i:s") . ": {$event->getName()}" . "with data '" . json_encode([0]) . "'\n";
        $this->logger->info($entry);
    }

}