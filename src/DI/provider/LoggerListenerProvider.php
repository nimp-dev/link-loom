<?php

namespace Nimp\LinkLoom\DI\provider;

use Nimp\LinkLoom\interfaces\ServiceProviderInterface;
use Nimp\LinkLoom\observer\subscribers\LoggerListener;
use Nimp\Observer\ListenerProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class LoggerListenerProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $container): void
    {
        $container
            ->register(LoggerListener::class, LoggerListener::class)
            ->setAutowired(true)
            ->setPublic(true);
    }
}
