<?php

namespace Nimp\LinkLoom\DI\provider;

use Nimp\LinkLoom\DI\ServiceProviderInterface;
use Nimp\LinkLoom\observer\listeners\LoggerListener;
use Nimp\Observer\EventListenerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class LoggerListenerProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $container): void
    {
        $container
            ->register(EventListenerInterface::class, LoggerListener::class)
            ->setAutowired(true)
            ->setPublic(true);
    }
}
