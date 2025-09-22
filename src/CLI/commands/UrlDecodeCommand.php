<?php

namespace Nimp\LinkLoom\CLI\commands;

use Nimp\LinkLoom\CLI\Color;
use Nimp\LinkLoom\CLI\exceptions\CliCommandException;
use Nimp\LinkLoom\CLI\interfaces\CliCommandInterface;
use Nimp\LinkLoom\CLI\Writer;
use Nimp\LinkLoom\UrlShortenerInterfaceInterface;

class UrlDecodeCommand implements CliCommandInterface
{

    protected UrlShortenerInterfaceInterface $shortener;

    /**
     * @param UrlShortenerInterfaceInterface $shortener
     */
    public function __construct(UrlShortenerInterfaceInterface $shortener)
    {
        $this->shortener = $shortener;
    }


    public static function getCommandName(): string
    {
        return 'decode';
    }

    public static function getCommandDesc(): string
    {
        return 'this command is used to return the URL by code';
    }

    public function run(array $params = []): void
    {
        $write = Writer::instance();
        $write->setColor(Color::YELLOW)->writeBorder();
        $write->writeLn(static::getCommandName().' -> '. $params[0]);
        $write->writeLn('Your url -> '. $this->shortener->decode($params[0]));
        $write->writeBorder();
    }
}