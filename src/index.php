<?php

require_once 'vendor/autoload.php';

use Monolog\Level;
use Nimp\LinkLoom\CLI\Color;
use Nimp\LinkLoom\CLI\CommandHandler;
use Nimp\LinkLoom\CLI\commands\TestCommand;
use Nimp\LinkLoom\CLI\commands\UrlDecodeCommand;
use Nimp\LinkLoom\CLI\commands\UrlEncodeCommand;
use Nimp\LinkLoom\CLI\Writer;
use Nimp\LinkLoom\exceptions\RepositoryDataException;
use Nimp\LinkLoom\helpers\LoomLogger;
use Nimp\LinkLoom\helpers\ConfigContainer;
use Nimp\LinkLoom\UrlShortener;
use Nimp\LinkLoom\FileRepository;

$configMain = require_once __DIR__ . '/config/main.php';

$config = ConfigContainer::instance()->setConfig($configMain);

$commandHandler = new CommandHandler();

LoomLogger::instance();
LoomLogger::instance()->setLogPath($config->get('logger.pathError'), Level::Error);
LoomLogger::instance()->setLogPath($config->get('logger.pathInfo'), Level::Info);

try {
    $repository = new FileRepository($config->get('db.path'), $config->get('db.maxSize'));
} catch (RepositoryDataException $e) {
    exit($e->getMessage());
}

$shortener = new UrlShortener($repository, new ($config->get('validator.class')));

$commandHandler->addCommand(new UrlEncodeCommand($shortener));
$commandHandler->addCommand(new UrlDecodeCommand($shortener));
$commandHandler->handle($argv, function ($params, Exception $e)
{
    LoomLogger::error($e->getMessage());
    Writer::instance()->setColor(Color::RED)->writeLn($e->getMessage());
    Writer::instance()->writeBorder();
});

echo PHP_EOL;
