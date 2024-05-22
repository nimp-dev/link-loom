<?php

namespace Nimp\LinkLoom\helpers;

use Exception;
use Nimp\LinkLoom\abstracts\Singleton;

/** todo implements ContainerInterface */

class ConfigContainer extends Singleton
{

    protected array $hashmap = [];

    protected function __construct()
    {
    }

    public function setConfig(array $config)
    {
        $this->hashmap = $config;
    }

    public function addConfig(array $addConfig)
    {
        $this->hashmap[] = $addConfig;
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