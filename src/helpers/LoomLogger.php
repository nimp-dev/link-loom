<?php

namespace Nimp\LinkLoom\helpers;

use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Nimp\LinkLoom\abstracts\Singleton;
use Psr\Log\LoggerInterface;

class LoomLogger extends Singleton
{
    protected LoggerInterface $logger;

    protected function __construct()
    {
        $this->logger = new Logger('general');
    }

    public function setLogPath(string $path, Level $level): self
    {
        $this->logger->pushHandler(
            new StreamHandler($path, $level)
        );
        return $this;
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }

    public static function error(string $message)
    {
        self::instance()->getLogger()->error($message);
    }

    public static function info(string $message)
    {
        self::instance()->getLogger()->info($message);
    }

}