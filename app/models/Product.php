<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/config.php';

class Product
{
    public function all(): array
    {
        $pdo = obtener_conexion();

        $sql = "SELECT
                    p.slug AS id,
                    p.nombre AS name,
                    p.descripcion AS description,
                    p.precio AS price,
                    c.slug AS category,
                    p.imagen AS image,
                    p.destacado AS featured
                FROM productos p
                INNER JOIN categorias c ON p.categoria_id = c.id
                WHERE p.estado = 'activo'
                  AND c.estado = 'activo'
                ORDER BY c.orden ASC, p.nombre ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $this->mapProducts($stmt->fetchAll());
    }

    public function byCategory(string $categoryId): array
    {
        $pdo = obtener_conexion();

        $sql = "SELECT
                    p.slug AS id,
                    p.nombre AS name,
                    p.descripcion AS description,
                    p.precio AS price,
                    c.slug AS category,
                    p.imagen AS image,
                    p.destacado AS featured
                FROM productos p
                INNER JOIN categorias c ON p.categoria_id = c.id
                WHERE p.estado = 'activo'
                  AND c.estado = 'activo'
                  AND c.slug = :category
                ORDER BY p.nombre ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'category' => $categoryId,
        ]);

        return $this->mapProducts($stmt->fetchAll());
    }

    public function featured(): array
    {
        $pdo = obtener_conexion();

        $sql = "SELECT
                    p.slug AS id,
                    p.nombre AS name,
                    p.descripcion AS description,
                    p.precio AS price,
                    c.slug AS category,
                    p.imagen AS image,
                    p.destacado AS featured
                FROM productos p
                INNER JOIN categorias c ON p.categoria_id = c.id
                WHERE p.estado = 'activo'
                  AND c.estado = 'activo'
                  AND p.destacado = 1
                ORDER BY c.orden ASC, p.nombre ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $this->mapProducts($stmt->fetchAll());
    }

    public function find(string $productId): ?array
    {
        $pdo = obtener_conexion();

        $sql = "SELECT
                    p.slug AS id,
                    p.nombre AS name,
                    p.descripcion AS description,
                    p.precio AS price,
                    c.slug AS category,
                    p.imagen AS image,
                    p.destacado AS featured
                FROM productos p
                INNER JOIN categorias c ON p.categoria_id = c.id
                WHERE p.estado = 'activo'
                  AND c.estado = 'activo'
                  AND p.slug = :product
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'product' => $productId,
        ]);

        $product = $stmt->fetch();

        if ($product === false) {
            return null;
        }

        return $this->mapProduct($product);
    }

    private function mapProducts(array $products): array
    {
        return array_map([$this, 'mapProduct'], $products);
    }

    private function mapProduct(array $product): array
    {
        return [
            'id' => (string) $product['id'],
            'name' => (string) $product['name'],
            'description' => (string) ($product['description'] ?? ''),
            'price' => (float) $product['price'],
            'category' => (string) $product['category'],
            'image' => (string) ($product['image'] ?? ''),
            'featured' => (bool) $product['featured'],
        ];
    }
}
