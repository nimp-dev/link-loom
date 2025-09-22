<?php

namespace Nimp\LinkLoom\DI\provider;

use Nimp\LinkLoom\DI\ServiceProviderInterface;
use Nimp\LinkLoom\observer\dispatcher\EventDispatcher;
use Nimp\LinkLoom\observer\dispatcher\ListenerProvider;
use Nimp\LinkLoom\observer\subscribers\LoggerListener;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface as PsrListenerProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class EventsProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $container): void
    {
        // LoggerListener можно переопределить извне: регистрируем только если нет
        if (!$container->has(LoggerListener::class)) {
            $container
                ->register(LoggerListener::class, LoggerListener::class)
                ->setAutowired(true);
        }

        // Конкретный провайдер слушателей
        if (!$container->has(ListenerProvider::class)) {
            $container
                ->register(ListenerProvider::class, ListenerProvider::class)
                ->addMethodCall('addListeners', [new Reference(LoggerListener::class)]);
        }

        // ВАЖНО: алиас PSR-интерфейса на конкретный провайдер
        if (!$container->has(PsrListenerProviderInterface::class)) {
            $container->setAlias(PsrListenerProviderInterface::class, ListenerProvider::class);
        }

        // Конкретный диспетчер событий
        if (!$container->has(EventDispatcher::class)) {
            $container
                ->register(EventDispatcher::class, EventDispatcher::class)
                // Явно указываем зависимость через PSR-интерфейс (можно и autowire, но так явно)
                ->addArgument(new Reference(PsrListenerProviderInterface::class));
        }

        // Алиас PSR-диспетчера на конкретный
        if (!$container->has(EventDispatcherInterface::class)) {
            $container->setAlias(EventDispatcherInterface::class, EventDispatcher::class);
        }
    }
}