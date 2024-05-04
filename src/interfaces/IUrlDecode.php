<?php

namespace Nimp\LinkLoom\interfaces;

use Nimp\LinkLoom\exceptions\UrlShortenerException;

interface IUrlDecode
{
    /**
     * @param string $code
     * @return string
     * @throws UrlShortenerException
     */
    public function decode(string $code): string;
}