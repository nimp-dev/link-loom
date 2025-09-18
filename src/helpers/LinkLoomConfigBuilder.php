<?php

namespace Nimp\LinkLoom\helpers;

use Monolog\Level;
use Nimp\LinkLoom\entities\LinkLoomConfig;

final class LinkLoomConfigBuilder
{
    private ?string $storageFile = null;
    private int $storageTtl = 0;

    private int $codeLength = 6;

    private ?string $loggingPath = null;
    private Level $loggingLevel;
    private string $loggingChannel = 'general';

    public function storage(string $file, int $ttl = 0): self
    {
        $this->storageFile = $file;
        $this->storageTtl  = $ttl;
        return $this;
    }

    public function code(int $length): self
    {
        $this->codeLength = $length;
        return $this;
    }

    public function logging(string $path, Level $level, string $channel = 'general'): self
    {
        $this->loggingPath    = $path;
        $this->loggingLevel   = $level;
        $this->loggingChannel = $channel;
        return $this;
    }

    public function build(): LinkLoomConfig
    {
        return new LinkLoomConfig(
            $this->storageFile,
            $this->storageTtl,
            $this->codeLength,
            $this->loggingPath,
            $this->loggingLevel,
            $this->loggingChannel
        );
    }
}