<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;

final class AuthService
{
    public function __construct(private UserRepository $users = new UserRepository())
    {
    }

    public function attempt(string $email, string $password): ?array
    {
        $user = $this->users->findByEmail($email);
        if (!$user || empty($user['password'])) {
            return null;
        }

        if (!password_verify($password, $user['password'])) {
            return null;
        }

        unset($user['password']);
        return $user;
    }

    public function register(string $name, string $email, string $password): ?array
    {
        if ($this->users->findByEmail($email)) {
            return null;
        }

        $id = bin2hex(random_bytes(16));
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $this->users->create($id, $email, $passwordHash, $name);

        return $this->users->findById($id);
    }
}
