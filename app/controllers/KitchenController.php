<?php

declare(strict_types=1);

class KitchenController extends BaseController
{
    public function index(): void
    {
        $user = $this->requireAnyRole(['administrador', 'cocina']);

        $this->render('kitchen/index', [
            'pageTitle' => 'Cocina',
            'currentUser' => $user,
            'orders' => (new Order())->forKitchen(),
            'success' => $_SESSION['kitchen_success'] ?? null,
        ]);

        unset($_SESSION['kitchen_success']);
    }

    public function update(): void
    {
        $this->requireAnyRole(['administrador', 'cocina']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('kitchen');
        }

        $orderId = (int) ($_POST['order_id'] ?? 0);
        $status = (string) ($_POST['status'] ?? '');

        if ($orderId > 0) {
            (new Order())->updateKitchenStatus($orderId, $status);
            $_SESSION['kitchen_success'] = 'Pedido actualizado correctamente.';
        }

        $this->redirect('kitchen');
    }
}
