<?php

require_once 'vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Nimp\LinkLoom\CLI\Color;
use Nimp\LinkLoom\CLI\CommandHandler;
use Nimp\LinkLoom\CLI\commands\TestCommand;
use Nimp\LinkLoom\CLI\commands\UrlDecodeCommand;
use Nimp\LinkLoom\CLI\commands\UrlEncodeCommand;
use Nimp\LinkLoom\CLI\Writer;
use Nimp\LinkLoom\exceptions\RepositoryDataException;
use Nimp\LinkLoom\exceptions\UrlShortenerException;
use Nimp\LinkLoom\helpers\BaseCodeGenerator;
use Nimp\LinkLoom\helpers\LoomLogger;
use Nimp\LinkLoom\helpers\ConfigContainer;
use Nimp\LinkLoom\helpers\UrlValidator;
use Nimp\LinkLoom\interfaces\CodeGeneratorInterface;
use Nimp\LinkLoom\interfaces\RepositoryInterface;
use Nimp\LinkLoom\interfaces\UrlValidatorInterface;
use Nimp\LinkLoom\LinkPluginContainer;
use Nimp\LinkLoom\observer\dispatcher\EventDispatcher;
use Nimp\LinkLoom\observer\dispatcher\ListenerProvider;
use Nimp\LinkLoom\observer\subscribers\LoggerListener;
use Nimp\LinkLoom\UrlShortener;
use Nimp\LinkLoom\FileRepository;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

$configMain = require_once __DIR__ . '/src/config/main.php';

$config = ConfigContainer::instance()->setConfig($configMain);

$dependencies = [
    UrlShortener::class => function (ContainerInterface $container) {
        return new UrlShortener(
            $container->get(RepositoryInterface::class),
            $container->get(UrlValidatorInterface::class),
            $container->get(CodeGeneratorInterface::class),
            $container->get(EventDispatcher::class),
        );
    },
    RepositoryInterface::class => function (ContainerInterface $container) {
        return new FileRepository(__DIR__ . '/storage/file-storage.json', 10);
    },
    UrlValidatorInterface::class => function (ContainerInterface $container) {
        return new UrlValidator();
    },
    CodeGeneratorInterface::class => function (ContainerInterface $container) {
        return new BaseCodeGenerator(8);
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
        return new Logger(
            'general',
            [
                new StreamHandler(__DIR__ . '/logs/'.date('Y-m-d').'.log', Level::Debug),
            ]
        );
    }
];

LinkPluginContainer::instance()->addDependencies($dependencies);

/** @var UrlShortener $shortener */
$shortener = LinkPluginContainer::instance()->get(UrlShortener::class);

try {
    $code = $shortener->decode('e9b2e308');
} catch (UrlShortenerException $e) {
    $code = '';
}

//print_r($code);
exit();
try {
    $repository = new FileRepository($config->get('db.path'), $config->get('db.maxSize'));
} catch (RepositoryDataException $e) {
    exit($e->getMessage());
}

$shortener = new UrlShortener($repository, new ($config->get('validator.class')));

$commandHandler = new CommandHandler();
$commandHandler->addCommand(new UrlEncodeCommand($shortener));
$commandHandler->addCommand(new UrlDecodeCommand($shortener));
$commandHandler->handle($argv, function ($params, Exception $e)
{
    LoomLogger::instance()->setLogPath(__DIR__ . '/logs', Level::Error);
    LoomLogger::instance()->setLogPath(__DIR__ . '/logs', Level::Info);
    LoomLogger::error($e->getMessage());
    Writer::instance()->setColor(Color::RED)->writeLn($e->getMessage());
    Writer::instance()->writeBorder();
});

echo PHP_EOL;
