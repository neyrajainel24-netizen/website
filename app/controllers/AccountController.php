<?php

declare(strict_types=1);

class AccountController extends BaseController
{
    public function index(): void
    {
        $this->render('account/index', [
            'pageTitle' => 'Mi cuenta',
            'currentUser' => $this->currentUser(),
            'mode' => $_GET['mode'] ?? 'login',
            'error' => $_SESSION['account_error'] ?? null,
            'success' => $_SESSION['account_success'] ?? null,
        ]);

        unset($_SESSION['account_error'], $_SESSION['account_success']);
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('account');
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $_SESSION['account_error'] = 'Ingresa tu email y contrasena.';
            $this->redirect('account');
        }

        $user = (new User())->findByEmail($email);

        if ($user === null || $user['status'] !== 'activo' || !password_verify($password, $user['password'])) {
            $_SESSION['account_error'] = 'Las credenciales no son correctas.';
            $this->redirect('account');
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];

        $this->redirect('account');
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('account', ['mode' => 'register']);
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            $_SESSION['account_error'] = 'Completa nombre, email y contrasena.';
            $this->redirect('account', ['mode' => 'register']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['account_error'] = 'Ingresa un email valido.';
            $this->redirect('account', ['mode' => 'register']);
        }

        if (strlen($password) < 6) {
            $_SESSION['account_error'] = 'La contrasena debe tener al menos 6 caracteres.';
            $this->redirect('account', ['mode' => 'register']);
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['account_error'] = 'Las contrasenas no coinciden.';
            $this->redirect('account', ['mode' => 'register']);
        }

        $userModel = new User();

        if ($userModel->findByEmail($email) !== null) {
            $_SESSION['account_error'] = 'Ya existe una cuenta con ese email.';
            $this->redirect('account', ['mode' => 'register']);
        }

        $userId = $userModel->createCustomer($name, $email, $password, $phone === '' ? null : $phone);
        $user = $userModel->findById($userId);

        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_role'] = $user['role'] ?? 'cliente';
        $_SESSION['account_success'] = 'Cuenta creada correctamente.';

        $this->redirect('account');
    }

    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            unset($_SESSION['user_id'], $_SESSION['user_role']);
            session_regenerate_id(true);
        }

        $this->redirect('account');
    }
}
