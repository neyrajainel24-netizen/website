<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/config.php';

class User
{
    public function findByEmail(string $email): ?array
    {
        $pdo = obtener_conexion();

        $sql = "SELECT
                    u.id,
                    u.nombre AS name,
                    u.email,
                    u.telefono AS phone,
                    u.password,
                    u.foto AS photo,
                    u.estado AS status,
                    r.nombre AS role
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                WHERE u.email = :email
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'email' => $email,
        ]);

        $user = $stmt->fetch();

        return $user === false ? null : $this->mapUser($user);
    }

    public function findById(int $id): ?array
    {
        $pdo = obtener_conexion();

        $sql = "SELECT
                    u.id,
                    u.nombre AS name,
                    u.email,
                    u.telefono AS phone,
                    u.password,
                    u.foto AS photo,
                    u.estado AS status,
                    r.nombre AS role
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                WHERE u.id = :id
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
        ]);

        $user = $stmt->fetch();

        return $user === false ? null : $this->mapUser($user);
    }

    public function createCustomer(string $name, string $email, string $password, ?string $phone = null): int
    {
        $pdo = obtener_conexion();

        $sql = "INSERT INTO usuarios (rol_id, nombre, email, telefono, password)
                SELECT r.id, :name, :email, :phone, :password
                FROM roles r
                WHERE r.nombre = 'cliente'
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return (int) $pdo->lastInsertId();
    }

    public function all(): array
    {
        $pdo = obtener_conexion();

        $sql = "SELECT
                    u.id,
                    u.nombre AS name,
                    u.email,
                    u.telefono AS phone,
                    u.password,
                    u.foto AS photo,
                    u.estado AS status,
                    r.nombre AS role
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                ORDER BY r.nombre ASC, u.nombre ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return array_map([$this, 'mapUser'], $stmt->fetchAll());
    }

    public function roles(): array
    {
        $pdo = obtener_conexion();

        $sql = "SELECT nombre
                FROM roles
                ORDER BY nombre ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return array_column($stmt->fetchAll(), 'nombre');
    }

    public function createWithRole(string $name, string $email, string $password, string $role, ?string $phone = null): int
    {
        $pdo = obtener_conexion();

        $sql = "INSERT INTO usuarios (rol_id, nombre, email, telefono, password)
                SELECT r.id, :name, :email, :phone, :password
                FROM roles r
                WHERE r.nombre = :role
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public function updateRole(int $id, string $role): void
    {
        $pdo = obtener_conexion();

        $sql = "UPDATE usuarios u
                INNER JOIN roles r ON r.nombre = :role
                SET u.rol_id = r.id
                WHERE u.id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'role' => $role,
        ]);
    }

    public function updateStatus(int $id, string $status): void
    {
        $pdo = obtener_conexion();

        $sql = "UPDATE usuarios
                SET estado = :status
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);
    }

    public function allWorkers(): array
    {
        $pdo = obtener_conexion();

        $sql = "SELECT
                    u.id,
                    u.nombre AS name,
                    u.email,
                    u.telefono AS phone,
                    u.password,
                    u.foto AS photo,
                    u.estado AS status,
                    r.nombre AS role
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                WHERE r.nombre IN ('administrador', 'cocina')
                ORDER BY r.nombre ASC, u.nombre ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return array_map([$this, 'mapUser'], $stmt->fetchAll());
    }

    public function workerRoles(): array
    {
        $pdo = obtener_conexion();

        $sql = "SELECT nombre
                FROM roles
                WHERE nombre IN ('administrador', 'cocina')
                ORDER BY nombre ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return array_column($stmt->fetchAll(), 'nombre');
    }

    public function createWorker(string $name, string $email, string $password, string $role, ?string $phone = null): int
    {
        $pdo = obtener_conexion();

        $sql = "INSERT INTO usuarios (rol_id, nombre, email, telefono, password)
                SELECT r.id, :name, :email, :phone, :password
                FROM roles r
                WHERE r.nombre = :role
                  AND r.nombre IN ('administrador', 'cocina')
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public function updateWorkerRole(int $id, string $role): void
    {
        $pdo = obtener_conexion();

        $sql = "UPDATE usuarios u
                INNER JOIN roles current_role ON u.rol_id = current_role.id
                INNER JOIN roles new_role ON new_role.nombre = :role
                SET u.rol_id = new_role.id
                WHERE u.id = :id
                  AND current_role.nombre IN ('administrador', 'cocina')
                  AND new_role.nombre IN ('administrador', 'cocina')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'role' => $role,
        ]);
    }

    public function updateWorkerStatus(int $id, string $status): void
    {
        $pdo = obtener_conexion();

        $sql = "UPDATE usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                SET u.estado = :status
                WHERE u.id = :id
                  AND r.nombre IN ('administrador', 'cocina')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);
    }

    private function mapUser(array $user): array
    {
        return [
            'id' => (int) $user['id'],
            'name' => (string) $user['name'],
            'email' => (string) $user['email'],
            'phone' => $user['phone'] === null ? '' : (string) $user['phone'],
            'password' => (string) $user['password'],
            'photo' => $user['photo'] === null ? '' : (string) $user['photo'],
            'status' => (string) $user['status'],
            'role' => (string) $user['role'],
        ];
    }
}
