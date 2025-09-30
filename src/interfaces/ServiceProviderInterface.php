<?php

namespace Nimp\LinkLoom\interfaces;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ServiceProviderInterface
{
    public function register(ContainerBuilder $container): void;
}