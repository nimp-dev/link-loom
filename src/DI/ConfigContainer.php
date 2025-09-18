<?php

namespace Nimp\LinkLoom\DI;

use Nimp\LinkLoom\exceptions\NotFountConfigContainerException;
use Psr\Container\ContainerInterface;

final class ConfigContainer implements ContainerInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $hashmap;

    public function __construct(array $config = [])
    {
        $this->hashmap = $config;
    }

    /**
     * Глубокое слияние конфигурации с переопределением значений по ключам.
     *
     * @param array<string, mixed> $addConfig
     * @return $this
     */
    public function addConfig(array $addConfig): self
    {
        $this->hashmap = $this->mergeRecursiveReplace($this->hashmap, $addConfig);
        return $this;
    }

    public function has(string $id): bool
    {
        try {
            $this->definePath($id);
            return true;
        } catch (NotFountConfigContainerException) {
            return false;
        }
    }

    public function get(string $id): mixed
    {
        return $this->definePath($id);
    }

    public function getOrDefault(string $id, mixed $default = null): mixed
    {
        try {
            return $this->definePath($id);
        } catch (NotFountConfigContainerException) {
            return $default;
        }
    }

    public function set(string $path, mixed $value): self
    {
        $tokens = $this->tokenize($path);
        $ref =& $this->hashmap;
        while (count($tokens) > 1) {
            $key = array_shift($tokens);
            if (!isset($ref[$key]) || !is_array($ref[$key])) {
                $ref[$key] = [];
            }
            $ref =& $ref[$key];
        }
        $ref[array_shift($tokens)] = $value;
        return $this;
    }

    /**
     * @param string $find
     * @return mixed
     * @throws NotFountConfigContainerException
     */
    private function definePath(string $find): mixed
    {
        $tokens = $this->tokenize($find);
        $context = $this->hashmap;

        while (null !== ($token = array_shift($tokens))) {
            if (!is_array($context) || !array_key_exists($token, $context)) {
                throw new NotFountConfigContainerException(sprintf('Key %s not found', $token));
            }
            $context = $context[$token];
        }
        return $context;
    }

    /**
     * @param array<string, mixed> $base
     * @param array<string, mixed> $override
     * @return array<string, mixed>
     */
    private function mergeRecursiveReplace(array $base, array $override): array
    {
        foreach ($override as $key => $value) {
            if (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
                $base[$key] = $this->mergeRecursiveReplace($base[$key], $value);
            } else {
                $base[$key] = $value;
            }
        }
        return $base;
    }

    /**
     * @return string[]
     */
    private function tokenize(string $path): array
    {
        $path = trim($path);
        if ($path === '') {
            return [];
        }
        return explode('.', $path);
    }
}