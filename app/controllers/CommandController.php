<?php

declare(strict_types=1);

class CommandController extends BaseController
{
    public function index(): void
    {
        $user = $this->requireAnyRole(['administrador', 'cocina']);
        $selectedCategory = (string) ($_GET['category'] ?? 'calientes');
        $query = trim((string) ($_GET['search'] ?? ''));
        $productModel = new Product();
        $products = $productModel->byCategory($selectedCategory);

        if ($query !== '') {
            $products = array_values(array_filter($products, function (array $product) use ($query): bool {
                return stripos($product['name'], $query) !== false
                    || stripos($product['description'], $query) !== false;
            }));
        }

        $this->render('commands/index', [
            'pageTitle' => 'Toma de Comandas',
            'currentUser' => $user,
            'categories' => (new Category())->all(),
            'selectedCategory' => $selectedCategory,
            'products' => $products,
            'items' => $this->items(),
            'total' => $this->total(),
            'query' => $query,
            'error' => $_SESSION['command_error'] ?? null,
            'success' => $_SESSION['command_success'] ?? null,
        ]);

        unset($_SESSION['command_error'], $_SESSION['command_success']);
    }

    public function add(): void
    {
        $this->requireAnyRole(['administrador', 'cocina']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('commands');
        }

        $productId = (string) ($_POST['product_id'] ?? '');

        if ($productId !== '' && (new Product())->find($productId) !== null) {
            $_SESSION['command'][$productId] = ($_SESSION['command'][$productId] ?? 0) + 1;
        }

        $this->redirect('commands', [
            'category' => (string) ($_POST['category'] ?? 'calientes'),
            'search' => (string) ($_POST['search'] ?? ''),
        ]);
    }

    public function remove(): void
    {
        $this->requireAnyRole(['administrador', 'cocina']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = (string) ($_POST['product_id'] ?? '');

            if (isset($_SESSION['command'][$productId])) {
                $_SESSION['command'][$productId]--;

                if ($_SESSION['command'][$productId] <= 0) {
                    unset($_SESSION['command'][$productId]);
                }
            }
        }

        $this->redirect('commands');
    }

    public function clear(): void
    {
        $this->requireAnyRole(['administrador', 'cocina']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['command'] = [];
        }

        $this->redirect('commands');
    }

    public function submit(): void
    {
        $user = $this->requireAnyRole(['administrador', 'cocina']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('commands');
        }

        $items = $this->items();

        if (count($items) === 0) {
            $_SESSION['command_error'] = 'Agrega productos antes de enviar la comanda.';
            $this->redirect('commands');
        }

        $table = trim((string) ($_POST['table'] ?? ''));
        $customerName = trim((string) ($_POST['customer_name'] ?? ''));
        $type = (string) ($_POST['type'] ?? 'mesa');
        $notes = trim((string) ($_POST['notes'] ?? ''));

        if (!in_array($type, ['mesa', 'llevar', 'delivery'], true)) {
            $_SESSION['command_error'] = 'Selecciona un tipo de pedido valido.';
            $this->redirect('commands');
        }

        $name = $customerName !== '' ? $customerName : ($table !== '' ? 'Mesa ' . $table : 'Comanda mostrador');
        $orderNotes = trim(($table !== '' ? 'Mesa: ' . $table . "\n" : '') . $notes);

        (new Order())->createFromCart($items, [
            'name' => $name,
            'phone' => '',
            'type' => $type,
            'notes' => $orderNotes === '' ? null : $orderNotes,
        ], (int) $user['id']);

        $_SESSION['command'] = [];
        $_SESSION['command_success'] = 'Comanda enviada a cocina correctamente.';

        $this->redirect('commands');
    }

    private function items(): array
    {
        $command = $_SESSION['command'] ?? [];
        $items = [];
        $productModel = new Product();

        foreach ($command as $productId => $quantity) {
            $product = $productModel->find((string) $productId);

            if ($product === null) {
                continue;
            }

            $quantity = (int) $quantity;
            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $product['price'] * $quantity,
            ];
        }

        return $items;
    }

    private function total(): float
    {
        return array_sum(array_column($this->items(), 'subtotal'));
    }
}
