<?php

require_once 'vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nimp\LinkLoom\DI\provider\BaseCodeGeneratorProvider;
use Nimp\LinkLoom\DI\provider\EventsProvider;
use Nimp\LinkLoom\DI\provider\LoggerListenerProvider;
use Nimp\LinkLoom\DI\provider\FileRepositoryProvider;
use Nimp\LinkLoom\DI\provider\BaseValidatorProvider;
use Nimp\LinkLoom\DI\provider\LoggingProvider;
use Nimp\LinkLoom\DI\provider\RedisRepositoryProvider;
use Nimp\LinkLoom\factory\UrlShortenerFactory;
use Nimp\LinkLoom\implementation\RedisRepository;

// ========== by di configuration ==========
try {
    $redis = new Redis([
        'host' => 'redis'
    ]);

    $providers = [

//        new LoggingProvider(__DIR__.'/logs/'.date('Y-m-d').'.log', \Monolog\Level::Debug, \Monolog\Level::Debug->value),
//        new LoggerListenerProvider(),   // регистрирует LoggerListener
        new EventsProvider(),          // подхватит все слушатели
        new RedisRepositoryProvider($redis, 100, 'linkloom'),
        new BaseCodeGeneratorProvider(8),
        new BaseValidatorProvider(),
    ];
    $shortener = UrlShortenerFactory::create($providers);

    $code = $shortener->decode('1ec8e11c');
    echo $code;
} catch (Exception $e) {
    echo $e->getMessage();
}
// ==========


