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
use Nimp\LinkLoom\helpers\BaseCodeGenerator;
use Nimp\LinkLoom\helpers\LoomLogger;
use Nimp\LinkLoom\helpers\ConfigContainer;
use Nimp\LinkLoom\helpers\UrlValidator;
use Nimp\LinkLoom\interfaces\RepositoryInterface;
use Nimp\LinkLoom\interfaces\UrlValidatorInterface;
use Nimp\LinkLoom\LinkPluginContainer;
use Nimp\LinkLoom\observer\subscribers\LoggerSubscriber;
use Nimp\LinkLoom\UrlShortener;
use Nimp\LinkLoom\FileRepository;
use Psr\Container\ContainerInterface;

$configMain = require_once __DIR__ . '/src/config/main.php';

$config = ConfigContainer::instance()->setConfig($configMain);

$dependencies = [
    UrlShortener::class => function (ContainerInterface $container) {
        $urlShortener = new UrlShortener(
            $container->get(RepositoryInterface::class),
            $container->get(UrlValidatorInterface::class),
            $container->get(ContainerInterface::class)
        );
        // Подключение подписчиков
        $urlShortener->attach($container->get(LoggerSubscriber::class));
        return $urlShortener;
    },
    RepositoryInterface::class => function (ContainerInterface $container) {
        return new FileRepository(__DIR__ . '/storage/file-storage.json', 10);
    },
    UrlValidatorInterface::class => function (ContainerInterface $container) {
        return new UrlValidator();
    },
    ContainerInterface::class => function (ContainerInterface $container) {
        return new BaseCodeGenerator(8);
    },
    LoggerSubscriber::class => function (ContainerInterface $container) {
        return new LoggerSubscriber(
            $container->get(Logger::class),
        );
    },
    Logger::class => function (ContainerInterface $container) {
        return new Logger(
            'general',
            [
                new StreamHandler(__DIR__ . '/logs/'.date('Y-m-d'), Level::Error),
                new StreamHandler(__DIR__ . '/logs/'.date('Y-m-d'), Level::Info),
            ]
        );
    }
];

LinkPluginContainer::instance()->addDependencies($dependencies);

/** @var UrlShortener $shortener */
$shortener = LinkPluginContainer::instance()->get(UrlShortener::class);

$code = $shortener->encode('https://test2.com');

print_r($code);
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
