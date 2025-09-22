<?php

namespace Nimp\LinkLoom\interfaces;

use Nimp\LinkLoom\exceptions\UrlShortenerException;

interface UrlEncodeInterface
{
    /**
     * @param string $url
     * @return string
     * @throws UrlShortenerException
     */
    public function encode(string $url): string;
}