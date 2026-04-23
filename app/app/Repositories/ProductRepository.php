<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class ProductRepository
{
    public function getActiveProducts(): array
    {
        return Database::query(
            'SELECT
                p.id,
                p.name,
                p.description,
                p.price,
                p.currency,
                p.sku,
                p.type,
                p.imageurl AS "imageUrl",
                p.stock,
                p.previousprice,
                p.specialprice,
                p.specialactive,
                c.name AS category_name
             FROM products
             p
             LEFT JOIN categories c ON c.id = p."categoryId"
             WHERE p.active = TRUE
             ORDER BY p."createdAt" DESC'
        )->fetchAll();
    }

    public function findActiveProduct(string $id): ?array
    {
        $row = Database::query(
            'SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON c.id = p."categoryId"
             WHERE p.id = :id AND p.active = TRUE
             LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $row ?: null;
    }

    public function findActiveProductsByIds(array $ids): array
    {
        $ids = array_values(array_filter(array_map('strval', $ids), static fn(string $id): bool => $id !== ''));
        if ($ids === []) {
            return [];
        }

        $placeholders = [];
        $params = [];
        foreach ($ids as $index => $id) {
            $key = 'id_' . $index;
            $placeholders[] = ':' . $key;
            $params[$key] = $id;
        }

        return Database::query(
            sprintf(
                'SELECT p.*, c.name AS category_name
                 FROM products p
                 LEFT JOIN categories c ON c.id = p."categoryId"
                 WHERE p.active = TRUE AND p.id IN (%s)',
                implode(', ', $placeholders)
            ),
            $params
        )->fetchAll();
    }

    public function productImages(string $productId): array
    {
        return Database::query(
            'SELECT image_url, sort_order
             FROM product_images
             WHERE product_id = :product_id
             ORDER BY sort_order ASC, created_at ASC',
            ['product_id' => $productId]
        )->fetchAll();
    }

    public function recommendations(string $productId): array
    {
        return Database::query(
            'SELECT p.id, p.name, p.description, p.price, p.currency, p.imageurl AS "imageUrl", p.type
             FROM product_recommendations pr
             INNER JOIN products p ON p.id = pr.recommended_product_id
             WHERE pr.product_id = :product_id AND p.active = TRUE
             ORDER BY pr.sort_order ASC, pr.created_at DESC
             LIMIT 4',
            ['product_id' => $productId]
        )->fetchAll();
    }

    public function categories(): array
    {
        return Database::query(
            'SELECT id, name, slug
             FROM categories
             WHERE active = TRUE
             ORDER BY "order" ASC, "createdAt" DESC'
        )->fetchAll();
    }

    public function reduceStock(string $productId, int $quantity): void
    {
        Database::query(
            'UPDATE products
             SET stock = GREATEST(stock - :quantity, 0), "updatedAt" = NOW()
             WHERE id = :product_id',
            [
                'quantity' => $quantity,
                'product_id' => $productId,
            ]
        );
    }
}
