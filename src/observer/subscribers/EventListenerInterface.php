<?php

namespace Nimp\LinkLoom\observer\subscribers;

interface EventListenerInterface
{
    /**
     * Возвращает список подписок:
     *  - ключ = имя события (string)
     *  - значение = callable|string (метод обработчика)
     *
     * @return iterable<string, callable|string>
     */
    public function events(): iterable;
}