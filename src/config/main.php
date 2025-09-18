<?php


return [
    'logging' => [
        'path' => __DIR__ . '/../../logs/'.date('Y-m-d').'.log',
        'level' => \Monolog\Level::Debug,
        'channel' => 'general'
    ],
    'code' => [
        'length' => 8,
    ],
    'storage' => [
        'file' => __DIR__ . '/../../storage/file-storage.json',
        'ttl' => 10,
    ],
];