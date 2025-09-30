<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nimp\LinkLoom\entities\LinkLoomConfig;
use Nimp\LinkLoom\FileRepository;
use Nimp\LinkLoom\helpers\BaseCodeGenerator;
use Nimp\LinkLoom\helpers\UrlValidator;
use Nimp\LinkLoom\interfaces\CodeGeneratorInterface;
use Nimp\LinkLoom\interfaces\RepositoryInterface;
use Nimp\LinkLoom\interfaces\UrlValidatorInterface;
use Nimp\LinkLoom\observer\dispatcher\EventDispatcher;
use Nimp\LinkLoom\observer\dispatcher\ListenerProvider;
use Nimp\LinkLoom\observer\subscribers\LoggerListener;
use Nimp\LinkLoom\UrlShortener;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [

    UrlShortener::class => function (ContainerInterface $container) {
        return new UrlShortener(
            $container->get(RepositoryInterface::class),
            $container->get(UrlValidatorInterface::class),
            $container->get(CodeGeneratorInterface::class),
            $container->get(EventDispatcher::class),
        );
    },
    RepositoryInterface::class => function (ContainerInterface $container) {
        /** @var LinkLoomConfig $cfg */
        $cfg = $container->get(LinkLoomConfig::class);
        return new FileRepository(
            $cfg->storageFile,
            $cfg->storageTtl
        );
    },
    UrlValidatorInterface::class => function (ContainerInterface $container) {
        return new UrlValidator();
    },
    CodeGeneratorInterface::class => function (ContainerInterface $container) {
        /** @var LinkLoomConfig $cfg */
        $cfg = $container->get(LinkLoomConfig::class);
        $length = $cfg->codeLength;
        return new BaseCodeGenerator($length);
    },
    EventDispatcher::class => function (ContainerInterface $container) {
        return new EventDispatcher($container->get(ListenerProvider::class));
    },
    ListenerProvider::class => function (ContainerInterface $container) {
        $provider = new ListenerProvider();
        $provider->addListeners($container->get(LoggerListener::class));
        return $provider;
    },
    LoggerListener::class => function (ContainerInterface $container) {
        return new LoggerListener(
            $container->get(LoggerInterface::class),
        );
    },
    LoggerInterface::class => function (ContainerInterface $container) {
        /** @var LinkLoomConfig $cfg */
        $cfg = $container->get(LinkLoomConfig::class);
        return new Logger(
            $cfg->loggingChannel,
            [
                new StreamHandler(
                    $cfg->loggingPath,
                    $cfg->loggingLevel
                ),
            ]
        );
    },
];