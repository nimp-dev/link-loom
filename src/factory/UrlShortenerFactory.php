<?php

namespace Nimp\LinkLoom\factory;

use Exception;
use Nimp\LinkLoom\interfaces\ServiceProviderInterface;
use Nimp\LinkLoomCore\UrlShortener;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class UrlShortenerFactory
{
    /**
     * Клиент обязан передать провайдеры (или может взять дефолтные через self::defaults()).
     *
     * @param ServiceProviderInterface[] $providers
     * @throws Exception
     */
    public static function create(array $providers): UrlShortener
    {
        $container = new ContainerBuilder();

        foreach ($providers as $provider) {
            if (!$provider instanceof ServiceProviderInterface) {
                throw new \InvalidArgumentException('Each provider must implement ServiceProviderInterface');
            }
            $provider->register($container);
        }

        if (!$container->has(UrlShortener::class)) {
            $container
                ->register(UrlShortener::class, UrlShortener::class)
                ->setAutowired(true)
                ->setPublic(true);
        }

        $container->compile(true);
        return $container->get(UrlShortener::class);
    }

}