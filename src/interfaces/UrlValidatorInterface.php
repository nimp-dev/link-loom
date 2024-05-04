<?php

namespace Nimp\LinkLoom\interfaces;

interface UrlValidatorInterface
{
    public function getMessageError(): string;
    public function validate(string $url): bool;
}