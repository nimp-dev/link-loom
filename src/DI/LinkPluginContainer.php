<?php

namespace Nimp\LinkLoom\DI;

use Nimp\LinkLoom\exceptions\NotFountConfigContainerException;
use Psr\Container\ContainerInterface;

final class LinkPluginContainer implements ContainerInterface
{
    /**
     * @var array<string, callable(ContainerInterface): mixed>
     */
    private array $factories = [];

    /**
     * @var array<string, mixed>
     */
    private array $instances = [];

    /**
     * @param array<string, callable(ContainerInterface): mixed> $dependencies
     */
    public function __construct(array $dependencies = [])
    {
        $this->addDependencies($dependencies);
    }

    /**
     * Регистрация/переопределение зависимостей. Новые переопределяют старые.
     *
     * @param array<string, callable(ContainerInterface): mixed> $dependencies
     */
    public function addDependencies(array $dependencies): void
    {
        $this->factories = array_replace($this->factories, $dependencies);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id): mixed
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }
        if ($this->has($id)) {
            $this->instances[$id] = ($this->factories[$id])($this);
            return $this->instances[$id];
        }
        throw new NotFountConfigContainerException("Dependency [{$id}] is not defined");
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->instances) || array_key_exists($id, $this->factories);
    }
}