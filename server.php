<?php

use Imefisto\PsrSwooleTests\Infrastructure\DependencyInjection\ContainerFactory;
use Imefisto\PsrSwooleTests\Infrastructure\Routing\Router;
use Imefisto\PsrSwooleTests\Infrastructure\Swoole\Server;

require __DIR__ . '/vendor/autoload.php';

$config = include __DIR__ . '/src/config/config.php';
$dependencies = include __DIR__ . '/src/config/dependencies.php';
$routes = include __DIR__ . '/src/config/routes.php';

$container = ContainerFactory::create($config, $dependencies, $routes);

$server = $container->get(Server::class);
$server->run();
