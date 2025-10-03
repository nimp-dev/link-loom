<?php

namespace Nimp\LinkLoom\DI\provider;

use Nimp\LinkLoom\DI\ServiceProviderInterface;
use Nimp\LinkLoom\implementation\BaseCodeGenerator;
use Nimp\LinkLoomCore\interfaces\CodeGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class BaseCodeGeneratorProvider implements ServiceProviderInterface
{
    public function __construct(
        private int $length
    ) {}

    public function register(ContainerBuilder $container): void
    {
        $container
            ->register(CodeGeneratorInterface::class, BaseCodeGenerator::class)
            ->addArgument($this->length);
    }
}