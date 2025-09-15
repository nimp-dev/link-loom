<?php

namespace Nimp\LinkLoom\interfaces;

use Nimp\LinkLoom\entities\UrlCodePair;
use Nimp\LinkLoom\exceptions\RepositoryDataException;

interface RepositoryInterface
{
    /**
     * @param UrlCodePair $urlCodePair
     * @return bool
     */
    public function saveUrlEntity(UrlCodePair $urlCodePair): bool;

    /**
     * @param string $code
     * @throws RepositoryDataException
     * @return string
     */
    public function getUrlByCode(string $code): string;

    /**
     * @param string $url
     * @throws RepositoryDataException
     * @return string
     */
    public function getCodeByUrl(string $url): string;


}