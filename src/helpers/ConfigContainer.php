<?php

namespace Nimp\LinkLoom\helpers;

use Nimp\LinkLoom\abstracts\Singleton;
use Nimp\LinkLoom\exceptions\NotFountConfigContainerException;
use Psr\Container\ContainerInterface;

class ConfigContainer extends Singleton implements ContainerInterface
{

    protected array $hashmap = [];

    protected function __construct()
    {
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config): static
    {
        $this->hashmap = $config;
        return static::instance();
    }

    /**
     * @param array $addConfig
     * @return $this
     */
    public function addConfig(array $addConfig): static
    {
        $this->hashmap[] = $addConfig;
        return self::instance();
    }


    public function has(string $id): bool
    {
        try {
            $this->definePath($id);
            return true;
        } catch (NotFountConfigContainerException $e) {
            return false;
        }
    }

    public function get(string $id): mixed
    {
        return $this->definePath($id);
    }

    /**
     * @param string $find
     * @return mixed
     * @throws NotFountConfigContainerException
     */
    protected function definePath(string $find): mixed
    {
        $tokens = explode('.', $find);
        $context = $this->hashmap;

        while (null !== ($token = array_shift($tokens))) {
            if (!isset($context[$token])) {
                throw new NotFountConfigContainerException(sprintf('Key %s not found', $token));
            }

            $context = $context[$token];
        }
        return $context;
    }

}