<?php

namespace Nimp\LinkLoom\observer\subscribers;

use Nimp\LinkLoom\interfaces\EventListenerInterface;
use Nimp\LinkLoom\interfaces\NamedEventInterface;
use Nimp\LinkLoom\observer\events\DecodeStartEvent;
use Nimp\LinkLoom\observer\events\DecodeSuccessEvent;
use Nimp\LinkLoom\observer\events\EncodeStartEvent;
use Nimp\LinkLoom\observer\events\EncodeSuccessEvent;
use Nimp\LinkLoom\observer\events\GetFromStorageErrorEvent;
use Nimp\LinkLoom\observer\events\SaveErrorEvent;
use Nimp\LinkLoom\observer\events\ValidateErrorEvent;
use Psr\Log\LoggerInterface;

class LoggerListener implements EventListenerInterface
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return iterable
     */
    public function events(): iterable
    {
        yield EncodeStartEvent::class => $this->startEncode(...);
        yield EncodeSuccessEvent::class => $this->encodeSuccessEvent(...);
        yield DecodeStartEvent::class => $this->decodeStartEvent(...);
        yield DecodeSuccessEvent::class => $this->decodeSuccessEvent(...);
        yield ValidateErrorEvent::class => $this->validateErrorEvent(...);
        yield GetFromStorageErrorEvent::class => $this->getFromStorageErrorEvent(...);
        yield SaveErrorEvent::class => $this->saveErrorEvent(...);
    }

    /**
     * @param EncodeStartEvent $event
     * @return void
     */
    public function startEncode(EncodeStartEvent $event): void
    {
        $this->logger->info(
            $this->createPrefix($event),
            ['URL' => $event->url],
        );
    }

    /**
     * @param EncodeSuccessEvent $event
     * @return void
     */
    public function encodeSuccessEvent(EncodeSuccessEvent $event): void
    {
        $this->logger->info(
            $this->createPrefix($event),
            ['CODE' => $event->code],
        );
    }

    /**
     * @param DecodeStartEvent $event
     * @return void
     */
    public function decodeStartEvent(DecodeStartEvent $event): void
    {
        $this->logger->info(
            $this->createPrefix($event),
            ['CODE' => $event->code],
        );
    }

    /**
     * @param DecodeSuccessEvent $event
     * @return void
     */
    public function decodeSuccessEvent(DecodeSuccessEvent $event): void
    {

        $this->logger->info(
            $this->createPrefix($event),
            ['URL' => $event->url],
        );
    }

    /**
     * @param ValidateErrorEvent $event
     * @return void
     */
    public function validateErrorEvent(ValidateErrorEvent $event): void
    {
        $this->logger->error(
            $this->createPrefix($event),
            ['URL' => $event->url, 'MESSAGE' => $event->message],
        );
    }

    /**
     * @param GetFromStorageErrorEvent $event
     * @return void
     */
    public function getFromStorageErrorEvent(GetFromStorageErrorEvent $event): void
    {
        $this->logger->error(
            $this->createPrefix($event),
            ['CODE' => $event->code, 'MESSAGE' => $event->message],
        );
    }

    /**
     * @param SaveErrorEvent $event
     * @return void
     */
    public function saveErrorEvent(SaveErrorEvent $event): void
    {
        $this->logger->error(
            $this->createPrefix($event),
            ['message' => $event->message],
        );
    }

    /**
     * @param NamedEventInterface $event
     * @return string
     */
    public function createPrefix(NamedEventInterface $event): string
    {
        return basename(str_replace('\\', '/', $event->eventName())) . ' ';
    }
}
