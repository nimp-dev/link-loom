<?php

namespace Nimp\LinkLoom\interfaces;

interface NamedEventInterface
{
    public function eventName(): string|null;
}