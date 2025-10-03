<?php

namespace Nimp\LinkLoom\DI\provider;

use Nimp\LinkLoom\DI\ServiceProviderInterface;
use Nimp\LinkLoom\implementation\UrlValidator;
use Nimp\LinkLoomCore\interfaces\UrlValidatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BaseValidatorProvider implements ServiceProviderInterface
{

    public function register(ContainerBuilder $container): void
    {
        $container
            ->register(UrlValidatorInterface::class, UrlValidator::class);
    }
}