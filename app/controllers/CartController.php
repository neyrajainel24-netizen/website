<?php

declare(strict_types=1);

class CartController extends BaseController
{
    public function index(): void
    {
        $this->requireRole('cliente');

        $cart = new Cart();

        $this->render('cart/index', [
            'pageTitle' => 'Pedido',
            'items' => $cart->items(),
            'total' => $cart->total(),
            'error' => $_SESSION['cart_error'] ?? null,
            'success' => $_SESSION['cart_success'] ?? null,
        ]);

        unset($_SESSION['cart_error'], $_SESSION['cart_success']);
    }

    public function add(): void
    {
        $this->requireRole('cliente');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('menu');
        }

        $productId = $_POST['product_id'] ?? '';
        $returnTo = $_POST['return_to'] ?? 'menu';

        if ($productId !== '') {
            (new Cart())->add($productId);
        }

        if ($returnTo === 'home') {
            $this->redirect('home');
        }

        $this->redirect('menu', ['category' => $_POST['category'] ?? 'calientes']);
    }

    public function remove(): void
    {
        $this->requireRole('cliente');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new Cart())->remove($_POST['product_id'] ?? '');
        }

        $this->redirect('cart');
    }

    public function clear(): void
    {
        $this->requireRole('cliente');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new Cart())->clear();
        }

        $this->redirect('cart');
    }

    public function checkout(): void
    {
        $this->requireRole('cliente');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('cart');
        }

        $cart = new Cart();
        $items = $cart->items();

        if (count($items) === 0) {
            $_SESSION['cart_error'] = 'Agrega productos antes de confirmar el pedido.';
            $this->redirect('cart');
        }

        $currentUser = $this->currentUser();
        $name = trim((string) ($_POST['customer_name'] ?? ($currentUser['name'] ?? '')));
        $phone = trim((string) ($_POST['customer_phone'] ?? ($currentUser['phone'] ?? '')));
        $type = (string) ($_POST['type'] ?? 'llevar');
        $notes = trim((string) ($_POST['notes'] ?? ''));

        if ($name === '' || !in_array($type, ['mesa', 'llevar', 'delivery'], true)) {
            $_SESSION['cart_error'] = 'Completa tu nombre y selecciona un tipo de pedido valido.';
            $this->redirect('cart');
        }

        (new Order())->createFromCart($items, [
            'name' => $name,
            'phone' => $phone,
            'type' => $type,
            'notes' => $notes === '' ? null : $notes,
        ], $currentUser !== null ? (int) $currentUser['id'] : null);

        $cart->clear();
        $_SESSION['cart_success'] = 'Pedido enviado a cocina correctamente.';

        $this->redirect('cart');
    }
}
