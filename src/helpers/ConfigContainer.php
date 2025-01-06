<?php

namespace Nimp\LinkLoom\helpers;

use Exception;
use Nimp\LinkLoom\abstracts\Singleton;
use Psr\Container\ContainerInterface;

/** todo implements ContainerInterface */

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
        $has = false;
        if ($this->definePath($id))
        {
            $has = true;
        }
        return $has;
    }

    public function get(string $id): mixed
    {
        return $this->definePath($id);
    }

    protected function definePath(string $find): mixed
    {
        $tokens = explode('.', $find);
        $context = $this->hashmap;

        while (null !== ($token = array_shift($tokens))) {
            if (!isset($context[$token])) {
                throw new \InvalidArgumentException(sprintf('Key %s not found', $token));
            }

            $context = $context[$token];
        }
        return $context;
    }

}