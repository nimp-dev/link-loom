<?php

require_once 'vendor/autoload.php';

use Monolog\Logger;
use Nimp\LinkLoom\exceptions\RepositoryDataException;
use Nimp\LinkLoom\exceptions\UrlShortenerException;
use Nimp\LinkLoom\helpers\BaseSingletonLogger;
use Nimp\LinkLoom\helpers\UrlValidator;
use Nimp\LinkLoom\UrlShortener;
use Nimp\LinkLoom\FileRepository;

$config = [
    'db' => [
        'path' => __DIR__ . '/../storage/file-storage.json',
        'maxSize' => 10,
    ],
    'validator' => [
        'class' => UrlValidator::class,
        'httpClient' => false,
    ],
    'logger' => [
        'class' => Logger::class,
        'pathError' => __DIR__ . '/../logs/error.log',
        'pathInfo' => __DIR__ . '/../logs/info.log',
    ],
];

$url = 'https://test13.com';

$logger = BaseSingletonLogger::instance(new $config['logger']['class']('general'));
$logger->pushHandler(new \Monolog\Handler\StreamHandler($config['logger']['pathError'], \Monolog\Level::Error));
$logger->pushHandler(new \Monolog\Handler\StreamHandler($config['logger']['pathInfo'], \Monolog\Level::Info));


try {
    $repository = new FileRepository($config['db']['path'], $config['db']['maxSize']);
} catch (RepositoryDataException $e) {
    exit($e->getMessage());
}


$shortener = new UrlShortener($repository, new  $config['validator']['class']);

try {
    $code = $shortener->encode($url);
} catch (UrlShortenerException|Exception $e) {
    $logger->getLogger()->error($e->getMessage());
    exit($e->getMessage());
}

echo $code;
$logger->getLogger()->info("add code: $code for url: $url");
echo PHP_EOL;

try {
    $decodedUrl = $shortener->decode($code);
} catch (UrlShortenerException $e) {
    $logger->getLogger()->error($e->getMessage());
    exit($e->getMessage() . PHP_EOL);
}

echo $decodedUrl;

echo PHP_EOL;
