<?php

namespace Nimp\LinkLoom\DI\provider;

use Nimp\LinkLoom\implementation\FileRepository;
use Nimp\LinkLoom\interfaces\ServiceProviderInterface;
use Nimp\LinkLoomCore\interfaces\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Дефолтный провайдер хранилища на файле.
 * Клиент может заменить его своим провайдером.
 */
final readonly class FileRepositoryProvider implements ServiceProviderInterface
{
    public function __construct(
        private string $file,
        private int    $ttl
    ) {}

    public function register(ContainerBuilder $container): void
    {
        $container
            ->register(RepositoryInterface::class, FileRepository::class)
            ->addArgument($this->file)
            ->addArgument($this->ttl);
    }
}
