<?php

namespace Nimp\LinkLoom\CLI\commands;

use Nimp\LinkLoom\CLI\Color;
use Nimp\LinkLoom\CLI\interfaces\CliCommandInterface;
use Nimp\LinkLoom\CLI\Writer;

class TestCommand implements CliCommandInterface
{

    /**
     * @inheritDoc
     */
    public static function getCommandName(): string
    {
        return 'test';
    }

    /**
     * @inheritDoc
     */
    public static function getCommandDesc(): string
    {
        return 'This command demonstrates a simple use of the CLI';
    }

    /**
     * @inheritDoc
     */
    public function run(array $params = []): void
    {
        $write = Writer::instance();
        $write->setColor(Color::YELLOW)->writeBorder();
        $write->writeLn(static::getCommandName());
        $write->writeLn(static::getCommandDesc());
        $write->writeBorder();
        $write->setColor(Color::CYAN)->writeLn('end test program');
    }
}