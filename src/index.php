<?php

require_once 'vendor/autoload.php';

use Nimp\LinkLoom\exceptions\RepositoryDataException;
use Nimp\LinkLoom\UrlShortener;
use Nimp\LinkLoom\FileRepository;
use Nimp\LinkLoom\UrlValidator;

$config = [
    'db' => [
        'path' => __DIR__ . '/../storage/file-storage.json',
        'maxSize' => 10,
    ],
    'validator' => UrlValidator::class
];

$url = 'https://test7.com';


try {
    $repository = new FileRepository($config['db']['path'], $config['db']['maxSize']);
} catch (RepositoryDataException $e) {
    exit($e->getMessage());
}


$shortener = new UrlShortener($repository, new  $config['validator']);

try {
    $code = $shortener->encode($url);
} catch (RepositoryDataException|Exception $e) {
    exit($e->getMessage());
}

echo $code;
echo PHP_EOL;

try {
    $decodedUrl = $shortener->decode($code.'123');
} catch (RepositoryDataException $e) {
    exit($e->getMessage() . PHP_EOL);
}

echo $decodedUrl;


echo PHP_EOL;
