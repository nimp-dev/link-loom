<?php

require_once 'vendor/autoload.php';

use Nimp\LinkLoom\UrlShortenerBuilder;

// ========== by di configuration ==========
try {
    $redis = new Redis([
        'host' => 'redis'
    ]);



    $shortener = (new UrlShortenerBuilder())
        ->withRedisRepository($redis, 1000)
        ->withLogger(__DIR__.'/logs/'.date('Y-m-d').'.log')
        ->build();


    $code = $shortener->encode('https://github.com/nimp-dev/link-loome/test2');
    echo $code;
} catch (Exception $e) {
    echo $e->getMessage();
}
// ==========


