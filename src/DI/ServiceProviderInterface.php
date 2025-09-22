<?php

namespace Nimp\LinkLoom\DI;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ServiceProviderInterface
{
    public function register(ContainerBuilder $container): void;
}