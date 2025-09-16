<?php

namespace Nimp\LinkLoom\observer\events;

interface NamedEventInterface
{
    public function eventName(): string|null;
}