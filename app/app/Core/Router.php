<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

final class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function __construct(private Session $session)
    {
    }

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = app_request_path($uri);
        $method = strtoupper($method);
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo 'Page not found';
            return;
        }

        [$class, $action] = $handler;
        $controller = new $class($this->session);

        try {
            $controller->{$action}();
        } catch (Throwable $e) {
            http_response_code(500);
            if ((bool) config('app.debug', false)) {
                echo '<pre>' . e($e->getMessage() . PHP_EOL . $e->getTraceAsString()) . '</pre>';
                return;
            }

            echo 'Application error';
        }
    }
}
