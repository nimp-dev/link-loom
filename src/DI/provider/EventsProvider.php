<?php

namespace Nimp\LinkLoom\DI\provider;

use Nimp\LinkLoom\interfaces\ServiceProviderInterface;
use Nimp\Observer\EventDispatcher;
use Nimp\Observer\EventListenerInterface;
use Nimp\Observer\ListenerProvider;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface as PsrListenerProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class EventsProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $container): void
    {
        // Базовый ListenerProvider
        if (!$container->has(ListenerProvider::class)) {
            $container
                ->register(ListenerProvider::class, ListenerProvider::class)
                ->setPublic(true);
        }

        // Алиас PSR ListenerProvider
        if (!$container->has(PsrListenerProviderInterface::class)) {
            $container->setAlias(PsrListenerProviderInterface::class, ListenerProvider::class);
        }

        // Диспетчер
        if (!$container->has(EventDispatcher::class)) {
            $container
                ->register(EventDispatcher::class, EventDispatcher::class)
                ->addArgument(new Reference(PsrListenerProviderInterface::class))
                ->setPublic(true);
        }

        // Алиас PSR Dispatcher
        if (!$container->has(EventDispatcherInterface::class)) {
            $container->setAlias(EventDispatcherInterface::class, EventDispatcher::class);
        }

        // Автоматическая регистрация всех слушателей
        foreach ($container->getDefinitions() as $id => $definition) {
            $class = $definition->getClass();
            if ($class && is_subclass_of($class, EventListenerInterface::class)) {
                $container
                    ->getDefinition(ListenerProvider::class)
                    ->addMethodCall('addListeners', [new Reference($id)]);
            }
        }
    }
}
