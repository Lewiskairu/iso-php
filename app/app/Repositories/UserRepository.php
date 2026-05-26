<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class UserRepository
{
    public function findByEmail(string $email): ?array
    {
        $user = Database::query(
            'SELECT id, email, password, name, role, is_verified FROM users WHERE email = :email LIMIT 1',
            ['email' => $email]
        )->fetch();

        return $user ?: null;
    }

    public function findById(string $id): ?array
    {
        $user = Database::query(
            'SELECT id, email, name, image, role, is_verified, "createdAt", "updatedAt" FROM users WHERE id = :id LIMIT 1',
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

    public function verifyEmail(string $id): void
    {
        Database::query('UPDATE users SET is_verified = 1 WHERE id = :id', ['id' => $id]);
    }

    public function updatePassword(string $id, string $passwordHash): void
    {
        Database::query('UPDATE users SET password = :password, "updatedAt" = NOW() WHERE id = :id', [
            'id' => $id,
            'password' => $passwordHash,
        ]);
    }
}
