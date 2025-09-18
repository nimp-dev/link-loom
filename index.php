<?php

require_once 'vendor/autoload.php';

use Nimp\LinkLoom\exceptions\UrlShortenerException;
use Nimp\LinkLoom\factory\UrlShortenerFactory;
use Psr\Log\LoggerInterface;


try {

//    $config = [
//        'logging' => [
//            'path' => __DIR__ . '/logs/'.date('Y-m-d').'.log',
//            'level' => \Monolog\Level::Debug,
//            'channel' => 'general'
//        ],
//        'code' => [
//            'length' => 8,
//        ],
//        'storage' => [
//            'file' => __DIR__ . '/storage/file-storage.json',
//            'ttl' => 10,
//        ],
//    ];
//    $lumConfig = UrlShortenerFactory::createConfigFromArray($config);
//    $container = UrlShortenerFactory::createDefaultContainer($lumConfig);
//    $logger = $container->get(LoggerInterface::class);
//    $logger->error('test');
//    var_dump($lumConfig);;

    $shortener = \Nimp\LinkLoom\factory\UrlShortenerFactory::create(
        [
            'logging' => [
                'path' => __DIR__ . '/logs/'.date('Y-m-d').'.log',
                'level' => \Monolog\Level::Debug,
                'channel' => 'general'
            ],
            'code' => [
                'length' => 8,
            ],
            'storage' => [
                'file' => __DIR__ . '/storage/file-storage.json',
                'ttl' => 10,
            ],
        ],
    );

    $code = $shortener->encode('https://balumba3.ua');
    echo $code;

} catch (Exception $e) {
    echo $e->getMessage();
}
