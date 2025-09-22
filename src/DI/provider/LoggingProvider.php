<?php

namespace Nimp\LinkLoom\DI\provider;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Nimp\LinkLoom\DI\ServiceProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class LoggingProvider implements ServiceProviderInterface
{
    public function __construct(
        private string $path,
        private Level  $level,
        private string $channel = 'general'
    ) {}

    public function register(ContainerBuilder $container): void
    {
        $dir = \dirname($this->path);
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new \RuntimeException("Cannot create log directory: {$dir}");
            }
        }

        $container
            ->register(LoggerInterface::class, Logger::class)
            ->addArgument($this->channel)
            ->addArgument([new StreamHandler($this->path, $this->level)]);
    }
}
