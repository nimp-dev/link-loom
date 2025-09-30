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
use Nimp\LinkLoom\factory\UrlShortenerFactory;




// ========== by di configuration ==========
try {

    $providers = [
//        new LoggingProvider(__DIR__.'/logs/'.date('Y-m-d').'.log', \Monolog\Level::Debug, 'general'),
//        new FileRepositoryProvider(__DIR__.'/storage/file-storage.json', 10),
//        new BaseCodeGeneratorProvider(8),
//        new BaseValidatorProvider(),
//        new EventsProvider(), // сам подхватит LoggerListener


        new LoggingProvider(__DIR__.'/logs/'.date('Y-m-d').'.log', \Monolog\Level::Debug, \Monolog\Level::Debug->value),
        new LoggerListenerProvider(),   // регистрирует LoggerListener
        new EventsProvider(),          // подхватит все слушатели
        new FileRepositoryProvider(__DIR__.'/storage/file-storage.json', 10),
        new BaseCodeGeneratorProvider(8),
        new BaseValidatorProvider(),
    ];
    $shortener = UrlShortenerFactory::create($providers);

    $code = $shortener->encode('https://github.com/nimp-linkloom');
    echo $code;
} catch (Exception $e) {
    echo $e->getMessage();
}
// ==========


