<?php

require_once 'vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nimp\LinkLoom\DI\provider\CodeGeneratorProvider;
use Nimp\LinkLoom\DI\provider\EventsProvider;
use Nimp\LinkLoom\DI\provider\LoggingProvider;
use Nimp\LinkLoom\DI\provider\RepositoryProvider;
use Nimp\LinkLoom\DI\provider\ValidatorProvider;
use Nimp\LinkLoom\factory\UrlShortenerFactory;




// ========== by di configuration ==========
try {

    $providers = [
        new LoggingProvider(__DIR__.'/logs/'.date('Y-m-d').'.log', \Monolog\Level::Debug, 'general'),
        new EventsProvider(),
        new RepositoryProvider(__DIR__.'/storage/file-storage.json', 10),
        new CodeGeneratorProvider(8),
        new ValidatorProvider(),
    ];
    $shortener = UrlShortenerFactory::create($providers);

    $shortener->encode('https://github.com/nimp-linkloom');

} catch (Exception $e) {
    echo $e->getMessage();
}
// ==========


