<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class UserRepository
{
    public function findByEmail(string $email): ?array
    {
        $user = Database::query(
            'SELECT id, email, password, name, role FROM users WHERE email = :email LIMIT 1',
            ['email' => $email]
        )->fetch();

        return $user ?: null;
    }

    public function findById(string $id): ?array
    {
        $user = Database::query(
            'SELECT id, email, name, image, role, "createdAt", "updatedAt" FROM users WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $user ?: null;
    }

    public function create(string $id, string $email, string $passwordHash, string $name): void
    {
        Database::query(
            'INSERT INTO users (id, email, password, name, role, "createdAt", "updatedAt")
             VALUES (:id, :email, :password, :name, :role, NOW(), NOW())',
            [
                'id' => $id,
                'email' => $email,
                'password' => $passwordHash,
                'name' => $name,
                'role' => 'USER',
            ]
        );
    }

    public function updateProfile(string $id, string $name, ?string $image = null): void
    {
        $params = [
            'id' => $id,
            'name' => $name,
        ];

        $sql = 'UPDATE users SET name = :name, "updatedAt" = NOW()';
        if ($image !== null) {
            $sql .= ', image = :image';
            $params['image'] = $image;
        }

        $sql .= ' WHERE id = :id';
        Database::query($sql, $params);
    }
}
