<?php

declare(strict_types=1);

namespace MaisBatik\Models;

use PDO;

final class ProductRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, category, variant, price_min, price_max, stock, image_path, status, created_at
             FROM products
             ORDER BY updated_at DESC, id DESC'
        );
        $statement->execute();

        return $statement->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, category, variant, price_min, price_max, stock, image_path, status
             FROM products WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $product = $statement->fetch();

        return $product === false ? null : $product;
    }

    public function create(array $product): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO products (name, category, variant, price_min, price_max, stock, image_path, status)
             VALUES (:name, :category, :variant, :price_min, :price_max, :stock, :image_path, :status)'
        );
        $statement->execute($product);
    }

    public function update(int $id, array $product): void
    {
        $product['id'] = $id;
        $statement = $this->pdo->prepare(
            'UPDATE products
             SET name = :name, category = :category, variant = :variant,
                 price_min = :price_min, price_max = :price_max, stock = :stock,
                 image_path = :image_path, status = :status
             WHERE id = :id'
        );
        $statement->execute($product);
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM products WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
