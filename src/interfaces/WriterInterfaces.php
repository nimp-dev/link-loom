<?php

namespace Nimp\LinkLoom\interfaces;

interface WriterInterfaces
{
    const BORDER_LENGTH = 50;

    /**
     * @param string $message
     * @param bool $endLine
     * @return void
     */
    public function write(string $message, bool $endLine = false): void;

    /**
     * @param string $message
     * @return void
     */
    public function writeLn(string $message): void;

    /**
     * @param int $length
     * @return void
     */
    public function writeBorder(int $length = self::BORDER_LENGTH): void;

}