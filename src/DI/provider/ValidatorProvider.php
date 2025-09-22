<?php

namespace Nimp\LinkLoom\DI\provider;

use Nimp\LinkLoom\DI\ServiceProviderInterface;
use Nimp\LinkLoom\FileRepository;
use Nimp\LinkLoom\helpers\UrlValidator;
use Nimp\LinkLoom\interfaces\RepositoryInterface;
use Nimp\LinkLoom\interfaces\UrlValidatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ValidatorProvider implements ServiceProviderInterface
{

    public function register(ContainerBuilder $container): void
    {
        $container
            ->register(UrlValidatorInterface::class, UrlValidator::class);
    }
}