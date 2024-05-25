<?php

namespace Nimp\LinkLoom\CLI;

use Nimp\LinkLoom\abstracts\Singleton;
use Nimp\LinkLoom\interfaces\WriterInterfaces;

class Writer extends Singleton implements WriterInterfaces
{

    public Color $color;

    public function setColor(Color $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function write(string $message, bool $endLine = false): void
    {
        echo $this->color->value . $message . ($endLine ? PHP_EOL : '') . Color::RESET->value;
    }

    public function writeLn(string $message): void
    {
        $this->write($message, true);
    }

    public function writeBorder(int $length = self::BORDER_LENGTH): void
    {
        $this->writeLn(str_repeat('*', $length));
    }
}