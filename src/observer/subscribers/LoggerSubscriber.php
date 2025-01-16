<?php

namespace Nimp\LinkLoom\observer\subscribers;

use Psr\Log\LoggerInterface;
use SplObserver;
use SplSubject;

class LoggerSubscriber implements SplObserver
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function update(SplSubject $subject, string $event = null, $data = null): void
    {
        $entry = date("Y-m-d H:i:s") . ": '$event' with data '" . json_encode($data) . "'\n";
        $this->logger->info($entry);
    }
}