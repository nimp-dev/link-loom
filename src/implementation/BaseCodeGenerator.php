<?php

namespace Nimp\LinkLoom\implementation;

use Nimp\LinkLoomCore\interfaces\CodeGeneratorInterface;

class BaseCodeGenerator implements CodeGeneratorInterface
{
    protected int $length = 8;

    /**
     * @param int $length
     */
    public function __construct(int $length)
    {
        $this->length = $length;
    }

    /**
     * @inheritDoc
     */
    public function generate(string $url): string
    {
        $hash = hash('md5', $url . time());
        return mb_substr($hash,0, $this->length);
    }
}