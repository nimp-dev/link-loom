<?php

namespace Nimp\LinkLoom\DI\provider;

use Nimp\LinkLoom\implementation\RedisRepository;
use Nimp\LinkLoom\interfaces\ServiceProviderInterface;
use Nimp\LinkLoomCore\interfaces\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class RedisRepositoryProvider implements ServiceProviderInterface
{
    public function __construct(
        private string $dsn,    // redis://host:port/db
        private int    $ttl,
        private string $prefix = 'linkloom:'
    ) {}

    public function register(ContainerBuilder $container): void
    {
        $container
            ->register(RepositoryInterface::class, RedisRepository::class)
            ->addArgument($this->dsn)
            ->addArgument($this->ttl)
            ->addArgument($this->prefix);
    }

}