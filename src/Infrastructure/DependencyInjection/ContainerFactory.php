<?php

declare(strict_types=1);

namespace Imefisto\PsrSwooleTests\Infrastructure\DependencyInjection;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

class ContainerFactory
{
    public static function create(
        array $config,
        array $dependencies,
        array $routes
    ): ContainerInterface {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions($config);
        $containerBuilder->addDefinitions($dependencies);
        $containerBuilder->addDefinitions(['routes' => $routes]);

        return $containerBuilder->build();
    }
}
