<?php

namespace Nimp\LinkLoom\DI\provider;

use Nimp\LinkLoom\DI\ServiceProviderInterface;
use Nimp\LinkLoom\implementation\RedisRepository;
use Nimp\LinkLoomCore\interfaces\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class RedisRepositoryProvider implements ServiceProviderInterface
{
    public function __construct(
        private \Redis $redis,
        private int    $ttl,
        private string $prefix = 'linkloom:'
    ) {}

    public function register(ContainerBuilder $container): void
    {
        $container
            ->register(RepositoryInterface::class, RedisRepository::class)
            ->addArgument($this->redis)
            ->addArgument($this->ttl)
            ->addArgument($this->prefix)
            ->setPublic(true);
    }

}