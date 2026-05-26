<?php

declare(strict_types=1);

namespace App\Core;

final class App
{
    public function __construct(
        private Router $router,
        private Session $session
    ) {
    }

    public function run(): void
    {
        $this->session->start();
        
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $baseDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        
        if ($baseDir !== '/' && $baseDir !== '' && strpos(explode('?', $uri)[0], $baseDir) === 0) {
            $uri = substr($uri, strlen($baseDir));
        }
        
        if ($uri === '') {
            $uri = '/';
        }

        $this->router->dispatch(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $uri
        );
    }
}
