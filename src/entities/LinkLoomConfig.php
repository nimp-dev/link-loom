<?php

namespace Nimp\LinkLoom\entities;

use Monolog\Level;

final readonly class LinkLoomConfig
{
    public function __construct(
        public string $storageFile,
        public int    $storageTtl,
        public int    $codeLength,
        public string $loggingPath,
        public Level $loggingLevel,
        public string $loggingChannel = 'general'
    ) {}
}