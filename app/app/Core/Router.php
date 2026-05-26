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

        try {
            $controller = new $class($this->session);
            $controller->{$action}();
        } catch (Throwable $e) {
            http_response_code(500);
            
            // In a production environment, we show a clean error message. 
            // We check app.debug to decide whether to show details.
            if ((bool) config('app.debug', false)) {
                echo '<h1>Application Error</h1>';
                echo '<p><strong>' . e($e->getMessage()) . '</strong></p>';
                echo '<p>Location: ' . e($e->getFile()) . ' on line ' . $e->getLine() . '</p>';
                echo '<pre>' . e($e->getTraceAsString()) . '</pre>';
                return;
            }

            echo '<h1>Application Error</h1>';
            echo '<p>We are sorry, but something went wrong. Please try again later.</p>';
        }
    }
}
