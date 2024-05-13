<?php

namespace Nimp\LinkLoom\helpers;

use Exception;
use InvalidArgumentException;
use Monolog\Handler\AbstractProcessingHandler;
use Psr\Log\LoggerInterface;

class BaseSingletonLogger
{
    protected LoggerInterface $logger;

    protected static ?self $instance = NULL;

    /**
     * @param LoggerInterface|NUll $logger
     * @return $this
     */
    public static function instance(LoggerInterface $logger = NUll): self
    {
        if (empty(self::$instance)) {
            if ($logger === NULL) {
                throw new InvalidArgumentException('Cant create logger');
            }
            self::$instance = new static($logger);
        }

        return self::$instance;
    }

    public function pushHandler(AbstractProcessingHandler $handler, ): self
    {
        $this->logger->pushHandler($handler);
        return $this;
    }


    /**
     * @param LoggerInterface $logger
     */
    protected function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }


    /**
     */
    protected function __clone() { }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize a singleton.");
    }

}