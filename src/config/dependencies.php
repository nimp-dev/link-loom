<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nimp\LinkLoom\implementation\BaseCodeGenerator;
use Nimp\LinkLoom\implementation\FileRepository;
use Nimp\LinkLoom\implementation\UrlValidator;
use Nimp\LinkLoom\observer\subscribers\LoggerListener;
use Nimp\LinkLoomCore\interfaces\CodeGeneratorInterface;
use Nimp\LinkLoomCore\interfaces\RepositoryInterface;
use Nimp\LinkLoomCore\interfaces\UrlValidatorInterface;
use Nimp\LinkLoomCore\UrlShortener;
use Nimp\Observer\EventDispatcher;
use Nimp\Observer\ListenerProvider;
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