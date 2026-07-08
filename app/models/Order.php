<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/config.php';

class Order
{
    public function createFromCart(array $items, array $customer, ?int $userId = null): int
    {
        $pdo = obtener_conexion();
        $total = array_sum(array_column($items, 'subtotal'));

        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO pedidos (usuario_id, cliente_nombre, cliente_telefono, tipo, estado, subtotal, total, observaciones)
                 VALUES (:user_id, :customer_name, :customer_phone, :type, 'pendiente', :subtotal, :total, :notes)"
            );
            $stmt->execute([
                'user_id' => $userId,
                'customer_name' => $customer['name'],
                'customer_phone' => $customer['phone'],
                'type' => $customer['type'],
                'subtotal' => $total,
                'total' => $total,
                'notes' => $customer['notes'],
            ]);

            $orderId = (int) $pdo->lastInsertId();
            $detailStmt = $pdo->prepare(
                "INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio_unitario, subtotal)
                 SELECT :order_id, p.id, :quantity, :unit_price, :subtotal
                 FROM productos p
                 WHERE p.slug = :product_slug
                 LIMIT 1"
            );

            foreach ($items as $item) {
                $product = $item['product'];
                $detailStmt->execute([
                    'order_id' => $orderId,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product['price'],
                    'subtotal' => $item['subtotal'],
                    'product_slug' => $product['id'],
                ]);
            }

            $ticketStmt = $pdo->prepare(
                "INSERT INTO tickets (pedido_id, codigo, tipo)
                 VALUES (:order_id, :code, 'cocina')"
            );
            $ticketStmt->execute([
                'order_id' => $orderId,
                'code' => 'COC-' . str_pad((string) $orderId, 6, '0', STR_PAD_LEFT),
            ]);

            $pdo->commit();

            return $orderId;
        } catch (Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }
    }

    public function forKitchen(): array
    {
        $pdo = obtener_conexion();

        $stmt = $pdo->prepare(
            "SELECT
                p.id,
                p.cliente_nombre,
                p.cliente_telefono,
                p.tipo,
                p.estado,
                p.total,
                p.observaciones,
                p.creado_en,
                p.actualizado_en
             FROM pedidos p
             WHERE p.estado IN ('pendiente', 'preparando', 'listo')
             ORDER BY FIELD(p.estado, 'pendiente', 'preparando', 'listo'), p.creado_en ASC"
        );
        $stmt->execute();

        $orders = [];

        foreach ($stmt->fetchAll() as $order) {
            $orders[] = $this->mapOrder($order, $this->itemsFor((int) $order['id']));
        }

        return $orders;
    }

    public function updateKitchenStatus(int $orderId, string $status): void
    {
        if (!in_array($status, ['pendiente', 'preparando', 'listo'], true)) {
            return;
        }

        $pdo = obtener_conexion();
        $stmt = $pdo->prepare(
            "UPDATE pedidos
             SET estado = :status
             WHERE id = :id
               AND estado IN ('pendiente', 'preparando', 'listo')"
        );
        $stmt->execute([
            'id' => $orderId,
            'status' => $status,
        ]);
    }

    private function itemsFor(int $orderId): array
    {
        $pdo = obtener_conexion();
        $stmt = $pdo->prepare(
            "SELECT
                pd.cantidad,
                pd.precio_unitario,
                pd.subtotal,
                pr.nombre AS product_name
             FROM pedido_detalles pd
             INNER JOIN productos pr ON pd.producto_id = pr.id
             WHERE pd.pedido_id = :order_id
             ORDER BY pd.id ASC"
        );
        $stmt->execute([
            'order_id' => $orderId,
        ]);

        return array_map(function (array $item): array {
            return [
                'quantity' => (int) $item['cantidad'],
                'unit_price' => (float) $item['precio_unitario'],
                'subtotal' => (float) $item['subtotal'],
                'product_name' => (string) $item['product_name'],
            ];
        }, $stmt->fetchAll());
    }

    private function mapOrder(array $order, array $items): array
    {
        return [
            'id' => (int) $order['id'],
            'customer_name' => (string) ($order['cliente_nombre'] ?? 'Cliente'),
            'customer_phone' => (string) ($order['cliente_telefono'] ?? ''),
            'type' => (string) $order['tipo'],
            'status' => (string) $order['estado'],
            'total' => (float) $order['total'],
            'notes' => (string) ($order['observaciones'] ?? ''),
            'created_at' => (string) $order['creado_en'],
            'updated_at' => (string) $order['actualizado_en'],
            'items' => $items,
        ];
    }
}
