<?php

namespace Nimp\LinkLoom\helpers;

use Nimp\LinkLoom\interfaces\UrlValidatorInterface;

class UrlValidator implements UrlValidatorInterface
{

    protected string $message = 'msg error';

    public function getMessageError(): string
    {
        return $this->message;
    }

    public function validate(string $url): bool
    {
        $isValid = true;
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $isValid = false;
            $this->message = 'invalid url';
        }
        return $isValid;
    }
}