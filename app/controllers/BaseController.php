<?php

declare(strict_types=1);

abstract class BaseController
{
    protected function currentUser(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $user = (new User())->findById((int) $_SESSION['user_id']);

        if ($user === null || $user['status'] !== 'activo') {
            unset($_SESSION['user_id'], $_SESSION['user_role']);
            return null;
        }

        $_SESSION['user_role'] = $user['role'];

        return $user;
    }

    protected function isLoggedIn(): bool
    {
        return $this->currentUser() !== null;
    }

    protected function hasRole(string $role): bool
    {
        return ($_SESSION['user_role'] ?? '') === $role;
    }

    protected function requireRole(string $role): array
    {
        $user = $this->currentUser();

        if ($user === null || $user['role'] !== $role) {
            http_response_code(403);
            $this->render('errors/403', [
                'pageTitle' => 'Acceso denegado',
                'currentUser' => $user,
            ]);
            exit;
        }

        return $user;
    }

    protected function requireAnyRole(array $roles): array
    {
        $user = $this->currentUser();

        if ($user === null || !in_array($user['role'], $roles, true)) {
            http_response_code(403);
            $this->render('errors/403', [
                'pageTitle' => 'Acceso denegado',
                'currentUser' => $user,
            ]);
            exit;
        }

        return $user;
    }

    protected function render(string $viewName, array $data = []): void
    {
        $currentUser = $data['currentUser'] ?? $this->currentUser();

        extract($data, EXTR_SKIP);

        $view = BASE_PATH . '/app/views/' . $viewName . '.php';
        require BASE_PATH . '/app/views/layouts/main.php';
    }

    protected function redirect(string $route, array $params = []): void
    {
        header('Location: ' . route_url($route, $params));
        exit;
    }
}
