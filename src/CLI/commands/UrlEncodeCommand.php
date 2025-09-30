<?php

namespace Nimp\LinkLoom\CLI\commands;

use Nimp\LinkLoom\CLI\Color;
use Nimp\LinkLoom\CLI\exceptions\CliCommandException;
use Nimp\LinkLoom\CLI\interfaces\CliCommandInterface;
use Nimp\LinkLoom\CLI\Writer;
use Nimp\LinkLoom\UrlShortener;

class UrlEncodeCommand implements CliCommandInterface
{

    protected UrlShortener $shortener;

    /**
     * @param UrlShortener $shortener
     */
    public function __construct(UrlShortener $shortener)
    {
        $this->shortener = $shortener;
    }


    public static function getCommandName(): string
    {
        return 'encode';
    }

    public static function getCommandDesc(): string
    {
        return 'This command is used to convert the URL into a shortcode';
    }

    public function run(array $params = []): void
    {
        $write = Writer::instance();
        $write->setColor(Color::YELLOW)->writeBorder();
        $write->writeLn(static::getCommandName().' -> '. $params[0]);
        $write->writeLn('Your code -> '. $this->shortener->encode($params[0]));
        $write->writeBorder();
    }
}