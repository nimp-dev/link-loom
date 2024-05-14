<?php

use Monolog\Logger;
use Nimp\LinkLoom\helpers\UrlValidator;

return [
    'db' => [
        'path' => __DIR__ . '/../../storage/file-storage.json',
        'maxSize' => 10,
    ],
    'validator' => [
        'class' => UrlValidator::class,
        'httpClient' => false,
    ],
    'logger' => [
        'class' => Logger::class,
        'pathError' => __DIR__ . '/../../logs/error.log',
        'pathInfo' => __DIR__ . '/../../logs/info.log',
    ],
];