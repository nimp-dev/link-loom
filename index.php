<?php

require_once 'vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nimp\LinkLoom\DI\provider\CodeGeneratorProvider;
use Nimp\LinkLoom\DI\provider\EventsProvider;
use Nimp\LinkLoom\DI\provider\LoggingProvider;
use Nimp\LinkLoom\DI\provider\RepositoryProvider;
use Nimp\LinkLoom\DI\provider\ValidatorProvider;
use Nimp\LinkLoom\factory\BuilderInterface;
use Nimp\LinkLoom\factory\UrlShortenerFactory;


// ========== by constructor ==========


try {
    $shortener = new \Nimp\LinkLoom\UrlShortenerInterfaceInterface(
      new \Nimp\LinkLoom\FileRepository(__DIR__.'/storage/file-storage.json', 10),
      new \Nimp\LinkLoom\helpers\UrlValidator(),
      new \Nimp\LinkLoom\helpers\BaseCodeGenerator(8),
      new \Nimp\LinkLoom\observer\dispatcher\EventDispatcher(
          new \Nimp\LinkLoom\observer\dispatcher\ListenerProvider()
      ),
    );
} catch (Exception $e) {
    echo $e->getMessage();
}


// ==========




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


