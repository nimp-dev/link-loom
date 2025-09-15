<?php

namespace Nimp\LinkLoom\observer\subscribers;

interface EventSubscriberInterface
{
    /**
     * Возвращает массив подписок
     *  - ключ = имя события
     *  - значение = метод
     */
    public function events(): array;
}