<?php

declare(strict_types=1);

class Cart
{
    private Product $products;

    public function __construct()
    {
        $this->products = new Product();

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function add(string $productId): void
    {
        $product = $this->products->find($productId);

        if ($product === null) {
            return;
        }

        $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + 1;
    }

    public function remove(string $productId): void
    {
        unset($_SESSION['cart'][$productId]);
    }

    public function clear(): void
    {
        $_SESSION['cart'] = [];
    }

    public function count(): int
    {
        return array_sum($_SESSION['cart']);
    }

    public function items(): array
    {
        $items = [];

        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $product = $this->products->find((string) $productId);

            if ($product === null) {
                continue;
            }

            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $product['price'] * $quantity,
            ];
        }

        return $items;
    }

    public function total(): float
    {
        return array_sum(array_column($this->items(), 'subtotal'));
    }
}
