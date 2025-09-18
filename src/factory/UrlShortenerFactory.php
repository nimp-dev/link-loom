<?php

namespace Nimp\LinkLoom\factory;

use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nimp\LinkLoom\entities\LinkLoomConfig;
use Nimp\LinkLoom\helpers\LinkLoomConfigBuilder;
use Nimp\LinkLoom\helpers\UrlValidator;
use Nimp\LinkLoom\interfaces\CodeGeneratorInterface;
use Nimp\LinkLoom\interfaces\RepositoryInterface;
use Nimp\LinkLoom\UrlShortener;
use Nimp\LinkLoom\FileRepository;
use Nimp\LinkLoom\observer\dispatcher\EventDispatcher;
use Nimp\LinkLoom\observer\dispatcher\ListenerProvider;
use Nimp\LinkLoom\observer\subscribers\LoggerListener;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class UrlShortenerFactory
{

    /**
     * @throws Exception
     */
    public static function create(
        LinkLoomConfig|array $config = [],
        array $overrides = [],
        ?ContainerBuilder $container = null
    ): UrlShortener
    {
        $cfg = $config instanceof LinkLoomConfig
            ? $config
            : self::createConfigFromArray($config);

        $container ??= self::createDefaultContainer($cfg);

        // Применяем переопределения (точечные)
        if (!empty($overrides)) {
            self::applyOverrides($container, $overrides);
            // После изменений пересобираем контейнер
            $container->compile(true);
        }

        return $container->get(UrlShortener::class);
    }

    public static function createDefaultContainer(LinkLoomConfig $cfg): ContainerBuilder
    {
        $container = new ContainerBuilder();

        // === Core config (как сервис) ===
        $container->set(LinkLoomConfig::class, $cfg);

        // === Repository ===
        $container
            ->register(RepositoryInterface::class, FileRepository::class)
            ->addArgument($cfg->storageFile)
            ->addArgument($cfg->storageTtl);

        // === Url Validator ===
        $container
            ->register(\Nimp\LinkLoom\interfaces\UrlValidatorInterface::class, UrlValidator::class);

        // === Code Generator ===
        $container
            ->register(CodeGeneratorInterface::class, \Nimp\LinkLoom\helpers\BaseCodeGenerator::class)
            ->addArgument($cfg->codeLength);

        // === Logger ===
        $container
            ->register(LoggerInterface::class, Logger::class)
            ->addArgument($cfg->loggingChannel)
            ->addArgument([new StreamHandler($cfg->loggingPath, $cfg->loggingLevel)])->setPublic(true);

        // === Logger Listener ===
        $container
            ->register(LoggerListener::class, LoggerListener::class)
            ->addArgument(new Reference(LoggerInterface::class));

        // === Listener Provider ===
        $container
            ->register(ListenerProvider::class, ListenerProvider::class)
            ->addMethodCall('addListeners', [new Reference(LoggerListener::class)]);

        // === Event Dispatcher ===
        $container
            ->register(EventDispatcher::class, EventDispatcher::class)
            ->addArgument(new Reference(ListenerProvider::class));

        // === UrlShortener ===
        $container
            ->register(UrlShortener::class, UrlShortener::class)
            ->addArgument(new Reference(RepositoryInterface::class))
            ->addArgument(new Reference(\Nimp\LinkLoom\interfaces\UrlValidatorInterface::class))
            ->addArgument(new Reference(CodeGeneratorInterface::class))
            ->addArgument(new Reference(EventDispatcher::class))
            ->setPublic(true); // ключевая строка


        $container->compile();

        return $container;
    }

    /**
     * Универсальные переопределения сервисов.
     * Поддерживает формы:
     *  - [Iface::class => MyClass::class]
     *  - [Iface::class => ['class' => MyClass::class, 'arguments' => [..]]]
     *  - [Iface::class => function(ContainerBuilder $c) { ... }]
     */
    public static function applyOverrides(ContainerBuilder $c, array $overrides): void
    {
        foreach ($overrides as $id => $override) {
            // callable: полная свобода пользователю
            if (is_callable($override)) {
                $override($c);
                continue;
            }

            // массив с class/arguments
            if (is_array($override) && isset($override['class'])) {
                $def = $c->register($id, $override['class']);
                if (!empty($override['arguments']) && is_array($override['arguments'])) {
                    foreach ($override['arguments'] as $arg) {
                        $def->addArgument($arg instanceof \Psr\Container\ContainerInterface ? null : $arg);
                    }
                }
                continue;
            }

            // строка-класс: простая замена реализации без аргументов
            if (is_string($override) && class_exists($override)) {
                $c->register($id, $override);
                continue;
            }

            throw new \InvalidArgumentException("Invalid override for service {$id}");
        }
    }

    public static function createConfigFromArray(array $settings): LinkLoomConfig
    {
        $builder = new LinkLoomConfigBuilder();

        if (isset($settings['storage'])) {
            $builder->storage(
                $settings['storage']['file'] ?? throw new \RuntimeException('storage.file required'),
                $settings['storage']['ttl'] ?? 0
            );
        }

        if (isset($settings['code']['length'])) {
            $builder->code($settings['code']['length']);
        }

        if (isset($settings['logging'])) {
            $builder->logging(
                $settings['logging']['path'] ?? throw new \RuntimeException('logging.path required'),
                $settings['logging']['level'] ?? Logger::DEBUG,
                $settings['logging']['channel'] ?? 'general'
            );
        }

        return $builder->build();
    }
}