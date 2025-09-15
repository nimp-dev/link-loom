<?php

namespace Nimp\LinkLoom\observer\events;

interface Event
{
    public function getName(): string;
}