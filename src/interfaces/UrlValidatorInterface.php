<?php

namespace Nimp\LinkLoom\interfaces;

interface UrlValidatorInterface
{
    /**
     * @return string
     */
    public function getMessageError(): string;

    /**
     * @param string $url
     * @return bool
     */
    public function validate(string $url): bool;
}