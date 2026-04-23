<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class OrderRepository
{
    public function byUser(string $userId): array
    {
        return Database::query(
            'SELECT o.*, COUNT(oi.id) AS items_count
             FROM orders o
             LEFT JOIN order_items oi ON oi."orderId" = o.id
             WHERE o."userId" = :user_id
             GROUP BY o.id
             ORDER BY o."createdAt" DESC',
            ['user_id' => $userId]
        )->fetchAll();
    }

    public function pendingCountByUser(string $userId): int
    {
        return (int) Database::query(
            'SELECT COUNT(*) FROM orders WHERE "userId" = :user_id AND status = :status',
            [
                'user_id' => $userId,
                'status' => 'PENDING',
            ]
        )->fetchColumn();
    }

    public function findForUser(string $orderId, string $userId): ?array
    {
        $order = Database::query(
            'SELECT o.*, COUNT(oi.id) AS items_count
             FROM orders o
             LEFT JOIN order_items oi ON oi."orderId" = o.id
             WHERE o.id = :order_id AND o."userId" = :user_id
             GROUP BY o.id
             LIMIT 1',
            [
                'order_id' => $orderId,
                'user_id' => $userId,
            ]
        )->fetch();

        if (!$order) {
            return null;
        }

        $order['items'] = Database::query(
            'SELECT oi.*, p.name, p.sku, p.imageurl AS "imageUrl", p.type
             FROM order_items oi
             INNER JOIN products p ON p.id = oi."productId"
             WHERE oi."orderId" = :order_id
             ORDER BY p.name ASC',
            ['order_id' => $orderId]
        )->fetchAll();

        return $order;
    }

    public function createOrder(string $userId, array $items, string $currency = 'USD'): string
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $orderId = bin2hex(random_bytes(16));
            $total = 0.0;

            foreach ($items as $item) {
                $total += ((float) $item['price']) * ((int) $item['quantity']);
            }

            Database::query(
                'INSERT INTO orders (id, "userId", total, currency, status, "createdAt", "updatedAt")
                 VALUES (:id, :user_id, :total, :currency, :status, NOW(), NOW())',
                [
                    'id' => $orderId,
                    'user_id' => $userId,
                    'total' => $total,
                    'currency' => $currency,
                    'status' => 'PENDING',
                ]
            );

            foreach ($items as $item) {
                Database::query(
                    'INSERT INTO order_items (id, "orderId", "productId", quantity, price)
                     VALUES (:id, :order_id, :product_id, :quantity, :price)',
                    [
                        'id' => bin2hex(random_bytes(16)),
                        'order_id' => $orderId,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]
                );

                Database::query(
                    'UPDATE products
                     SET stock = GREATEST(stock - :quantity, 0), "updatedAt" = NOW()
                     WHERE id = :product_id',
                    [
                        'quantity' => $item['quantity'],
                        'product_id' => $item['product_id'],
                    ]
                );
            }

            $pdo->commit();
            return $orderId;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
