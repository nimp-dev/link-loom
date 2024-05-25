<?php

namespace Nimp\LinkLoom\CLI\interfaces;

use Nimp\LinkLoom\CLI\exceptions\CliCommandException;

interface CliCommandInterface
{
    /**
     * @return string
     */
    public static function getCommandName(): string;

    /**
     * @return string
     */
    public static function getCommandDesc(): string;

    /**
     * @param array $params
     * @return void
     * @throws CliCommandException
     */
    public function run(array $params = []): void;

}