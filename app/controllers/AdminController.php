<?php

declare(strict_types=1);

class AdminController extends BaseController
{
    public function users(): void
    {
        $admin = $this->requireRole('administrador');
        $userModel = new User();

        $this->render('admin/users', [
            'pageTitle' => 'Usuarios',
            'currentUser' => $admin,
            'users' => $userModel->all(),
            'roles' => $userModel->roles(),
            'error' => $_SESSION['admin_error'] ?? null,
            'success' => $_SESSION['admin_success'] ?? null,
        ]);

        unset($_SESSION['admin_error'], $_SESSION['admin_success']);
    }

    public function createUser(): void
    {
        $this->requireRole('administrador');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin.users');
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $role = (string) ($_POST['role'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $userModel = new User();
        $roles = $userModel->roles();

        if ($name === '' || $email === '' || $password === '' || !in_array($role, $roles, true)) {
            $_SESSION['admin_error'] = 'Completa nombre, email, contrasena y rol.';
            $this->redirect('admin.users');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['admin_error'] = 'Ingresa un email valido.';
            $this->redirect('admin.users');
        }

        if (strlen($password) < 6) {
            $_SESSION['admin_error'] = 'La contrasena debe tener al menos 6 caracteres.';
            $this->redirect('admin.users');
        }

        if ($userModel->findByEmail($email) !== null) {
            $_SESSION['admin_error'] = 'Ya existe una cuenta con ese email.';
            $this->redirect('admin.users');
        }

        $userModel->createWithRole($name, $email, $password, $role, $phone === '' ? null : $phone);
        $_SESSION['admin_success'] = 'Usuario creado correctamente.';

        $this->redirect('admin.users');
    }

    public function updateUser(): void
    {
        $admin = $this->requireRole('administrador');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin.users');
        }

        $userId = (int) ($_POST['user_id'] ?? 0);
        $role = (string) ($_POST['role'] ?? '');
        $status = (string) ($_POST['status'] ?? '');
        $userModel = new User();
        $roles = $userModel->roles();

        if ($userId <= 0 || !in_array($role, $roles, true) || !in_array($status, ['activo', 'inactivo'], true)) {
            $_SESSION['admin_error'] = 'No se pudo actualizar el usuario.';
            $this->redirect('admin.users');
        }

        if ($userId === $admin['id'] && ($status === 'inactivo' || $role !== 'administrador')) {
            $_SESSION['admin_error'] = 'No puedes quitarte tus propios permisos de administrador.';
            $this->redirect('admin.users');
        }

        $userModel->updateRole($userId, $role);
        $userModel->updateStatus($userId, $status);
        $_SESSION['admin_success'] = 'Usuario actualizado correctamente.';

        $this->redirect('admin.users');
    }

    public function employees(): void
    {
        $admin = $this->requireRole('administrador');
        $userModel = new User();

        $this->render('admin/employees', [
            'pageTitle' => 'Trabajadores',
            'currentUser' => $admin,
            'workers' => $userModel->allWorkers(),
            'roles' => $userModel->workerRoles(),
            'error' => $_SESSION['admin_error'] ?? null,
            'success' => $_SESSION['admin_success'] ?? null,
        ]);

        unset($_SESSION['admin_error'], $_SESSION['admin_success']);
    }

    public function createEmployee(): void
    {
        $this->requireRole('administrador');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin.employees');
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $role = (string) ($_POST['role'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if ($name === '' || $email === '' || $password === '' || !in_array($role, ['administrador', 'cocina'], true)) {
            $_SESSION['admin_error'] = 'Completa nombre, email, contrasena y rol.';
            $this->redirect('admin.employees');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['admin_error'] = 'Ingresa un email valido.';
            $this->redirect('admin.employees');
        }

        if (strlen($password) < 6) {
            $_SESSION['admin_error'] = 'La contrasena debe tener al menos 6 caracteres.';
            $this->redirect('admin.employees');
        }

        $userModel = new User();

        if ($userModel->findByEmail($email) !== null) {
            $_SESSION['admin_error'] = 'Ya existe una cuenta con ese email.';
            $this->redirect('admin.employees');
        }

        $userModel->createWorker($name, $email, $password, $role, $phone === '' ? null : $phone);
        $_SESSION['admin_success'] = 'Trabajador creado correctamente.';

        $this->redirect('admin.employees');
    }

    public function updateEmployee(): void
    {
        $admin = $this->requireRole('administrador');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin.employees');
        }

        $workerId = (int) ($_POST['worker_id'] ?? 0);
        $role = (string) ($_POST['role'] ?? '');
        $status = (string) ($_POST['status'] ?? '');

        if ($workerId <= 0 || !in_array($role, ['administrador', 'cocina'], true) || !in_array($status, ['activo', 'inactivo'], true)) {
            $_SESSION['admin_error'] = 'No se pudo actualizar el trabajador.';
            $this->redirect('admin.employees');
        }

        if ($workerId === $admin['id'] && ($status === 'inactivo' || $role !== 'administrador')) {
            $_SESSION['admin_error'] = 'No puedes quitarte tus propios permisos de administrador.';
            $this->redirect('admin.employees');
        }

        $userModel = new User();
        $userModel->updateWorkerRole($workerId, $role);
        $userModel->updateWorkerStatus($workerId, $status);
        $_SESSION['admin_success'] = 'Trabajador actualizado correctamente.';

        $this->redirect('admin.employees');
    }
}
