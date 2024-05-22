<?php

require_once 'vendor/autoload.php';

use Monolog\Level;
use Nimp\LinkLoom\exceptions\RepositoryDataException;
use Nimp\LinkLoom\exceptions\UrlShortenerException;
use Nimp\LinkLoom\helpers\LoomLogger;
use Nimp\LinkLoom\helpers\ConfigContainer;
use Nimp\LinkLoom\UrlShortener;
use Nimp\LinkLoom\FileRepository;

$configMain = require_once __DIR__ . '/config/main.php';

$url = 'https://test21.com';

ConfigContainer::instance()->setConfig($configMain);
$config = ConfigContainer::instance();


LoomLogger::instance();
LoomLogger::instance()->setLogPath($config->get('logger.pathError'), Level::Error);
LoomLogger::instance()->setLogPath($config->get('logger.pathInfo'), Level::Info);

try {
    $repository = new FileRepository($config->get('db.path'), $config->get('db.maxSize'));
} catch (RepositoryDataException $e) {
    exit($e->getMessage());
}

$shortener = new UrlShortener($repository, new  ($config->get('validator.class')));

try {
    $code = $shortener->encode($url);
} catch (UrlShortenerException|Exception $e) {
    LoomLogger::error($e->getMessage());
    exit($e->getMessage());
}

echo $code;
LoomLogger::info("add code: $code for url: $url");
echo PHP_EOL;

try {
    $decodedUrl = $shortener->decode($code);
} catch (UrlShortenerException $e) {
    LoomLogger::error($e->getMessage());
    exit($e->getMessage() . PHP_EOL);
}

echo $decodedUrl;

echo PHP_EOL;
