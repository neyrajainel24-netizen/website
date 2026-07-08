<?php
require_once __DIR__ . '/../config/config.php';

try {
    $pdo = obtener_conexion();

    echo "<p style='color: green;'>¡Conexión exitosa a la base de datos!</p>";

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos");
    $fila = $stmt->fetch();

    echo "Total de productos: " . $fila['total'];

    $stmt2 = $pdo->query("SELECT nombre, precio FROM productos ORDER BY nombre");
    $prods = $stmt2->fetchAll();

    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>Producto</th><th>Precio</th></tr>";

    foreach ($prods as $p) {
        echo "<tr><td>" . htmlspecialchars($p['nombre']) . "</td><td>" . htmlspecialchars($p['precio']) . "</td></tr>";
    }

    echo "</table>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error de conexión: " . $e->getMessage() . "</p>";
}
?>