<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    public function __construct(protected Session $session)
    {
    }

    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    protected function requireAuth(): array
    {
        $user = $this->session->get(config('auth.session_key'));
        if (!$user) {
            redirect('/login');
        }

        return $user;
    }

    protected function requireRole(array $roles): array
    {
        $user = $this->requireAuth();
        if (!in_array($user['role'] ?? 'USER', $roles, true)) {
            http_response_code(403);
            exit('Forbidden');
        }

        return $user;
    }

    protected function flashFormState(array $errors = [], array $old = []): void
    {
        $this->session->flash('errors', $errors);
        $this->session->flash('old', $old);
    }
}
