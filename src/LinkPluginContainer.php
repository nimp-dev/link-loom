<?php

namespace Nimp\LinkLoom;

use Nimp\LinkLoom\abstracts\Singleton;
use Nimp\LinkLoom\exceptions\NotFountConfigContainerException;
use Psr\Container\ContainerInterface;

class LinkPluginContainer extends Singleton implements ContainerInterface
{
    /**
     * @var array  ключи это пути к классам => значение - функция колбек, которая принимает единственный аргумент
     * котейнер, в этой функции нудно реализовать создание завимистей
     */
    private array $dependencies;

    public function addDependencies(array $dependencies): void
    {
        $this->dependencies = $dependencies;
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id): mixed
    {
        if ($this->has($id)) {
            return $this->resolve($id);
        } else {
            throw new NotFountConfigContainerException("Dependency [{$id}] is not defined");
        }
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return !empty($this->dependencies[$id]);
    }

    protected function resolve(string $id)
    {
        return call_user_func($this->dependencies[$id], $this);
    }
}