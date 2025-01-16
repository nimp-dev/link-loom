<?php

namespace Nimp\LinkLoom\interfaces;

interface CodeGeneratorInterface
{
    /**
     * Convert url string to unique code
     * @param string $url
     * @return string
     */
    public function generate(string $url): string;
}