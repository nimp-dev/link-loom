<?php

namespace Nimp\LinkLoom\abstracts;

use Exception;

abstract class Singleton
{

    private static array $instances = [];

    /**
     * Клонирование и десериализация не разрешены для одиночек.
     */
    protected function __clone() { }

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }


    public static function instance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {

            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }
}