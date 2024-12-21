<?php

declare(strict_types=1);

namespace Imefisto\PsrSwooleTests\Infrastructure\Routing;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Container\ContainerInterface;
use Psr\Http\{
    Message\ServerRequestInterface,
    Message\ResponseFactoryInterface,
    Message\ResponseInterface,
    Server\MiddlewareInterface,
    Server\RequestHandlerInterface
};
use RuntimeException;

use function FastRoute\simpleDispatcher;

class Router
{
    private array $middlewares = [];
    private Dispatcher $dispatcher;
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->initializeDispatcher();
    }

    private function initializeDispatcher(): void
    {
        $this->dispatcher = simpleDispatcher(function (RouteCollector $r) {
            $routes = $this->container->get('routes');
            foreach ($routes as $route) {
                $r->addRoute($route[0], $route[1], $route[2]);
            }
        });
    }

    public function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->runMiddlewareStack($request, function ($request) {
            $routeInfo = $this->dispatcher->dispatch(
                $request->getMethod(),
                $request->getUri()->getPath()
            );

            switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return $this->handleNotFound($request);
            case Dispatcher::METHOD_NOT_ALLOWED:
                return $this->handleMethodNotAllowed($request, $routeInfo[1]);
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                return $this->handleFound($request, $handler, $vars);
            }

            throw new RuntimeException('An unexpected routing error occurred.');
        });

        return $response;
    }

    private function runMiddlewareStack(ServerRequestInterface $request, callable $handler): ResponseInterface
    {
        $next = $handler;
        foreach (array_reverse($this->middlewares) as $middleware) {
            $next = function ($request) use ($middleware, $next) {
                return $middleware->process($request, new class($next) implements RequestHandlerInterface {
                    private $next;
                    public function __construct(callable $next) { $this->next = $next; }
                    public function handle(ServerRequestInterface $request): ResponseInterface {
                        return ($this->next)($request);
                    }
                });
            };
        }
        return $next($request);
    }

    private function handleNotFound(ServerRequestInterface $request): ResponseInterface
    {
        return $this->container->get(ResponseFactoryInterface::class)
                               ->createResponse(404);
    }

    private function handleMethodNotAllowed(ServerRequestInterface $request, array $allowedMethods): ResponseInterface
    {
        return $this->container->get(ResponseFactoryInterface::class)
                               ->createResponse(405);
    }

    private function handleFound(ServerRequestInterface $request, $handler, array $vars): ResponseInterface
    {
        if (is_string($handler)) {
            if (strpos($handler, '::') !== false) {
                [$class, $method] = explode('::', $handler, 2);
                $handler = [$this->container->get($class), $method];
            } else {
                $handler = $this->container->get($handler);
            }
        }

        if (is_array($handler) && is_string($handler[0])) {
            $handler[0] = $this->container->get($handler[0]);
        }

        if (is_callable($handler)) {
            return $handler($request, $vars);
        }

        throw new RuntimeException('Invalid route handler');
    }
}
