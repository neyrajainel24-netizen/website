CREATE DATABASE IF NOT EXISTS cafego
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE cafego;

CREATE TABLE IF NOT EXISTS roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL UNIQUE,
  descripcion VARCHAR(180),
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rol_id INT NOT NULL,
  nombre VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  telefono VARCHAR(30),
  password VARCHAR(255) NOT NULL,
  foto VARCHAR(255),
  estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_usuarios_roles
    FOREIGN KEY (rol_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE,
  slug VARCHAR(100) NOT NULL UNIQUE,
  icono VARCHAR(60),
  orden INT NOT NULL DEFAULT 0,
  estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT NOT NULL,
  nombre VARCHAR(120) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  descripcion TEXT,
  precio DECIMAL(10,2) NOT NULL,
  imagen VARCHAR(255),
  destacado TINYINT(1) NOT NULL DEFAULT 0,
  estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_productos_categorias
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NULL,
  cliente_nombre VARCHAR(120),
  cliente_telefono VARCHAR(30),
  tipo ENUM('mesa', 'llevar', 'delivery') NOT NULL DEFAULT 'llevar',
  estado ENUM('pendiente', 'preparando', 'listo', 'entregado', 'cancelado') NOT NULL DEFAULT 'pendiente',
  subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  observaciones TEXT,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_pedidos_usuarios
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pedido_detalles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  precio_unitario DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_detalles_pedidos
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_detalles_productos
    FOREIGN KEY (producto_id) REFERENCES productos(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL,
  codigo VARCHAR(40) NOT NULL UNIQUE,
  tipo ENUM('preventa', 'cocina', 'venta') NOT NULL DEFAULT 'preventa',
  impreso TINYINT(1) NOT NULL DEFAULT 0,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tickets_pedidos
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO roles (nombre, descripcion)
VALUES
  ('administrador', 'Gestiona productos, pedidos y usuarios.'),
  ('cliente', 'Realiza pedidos desde la web.'),
  ('cocina', 'Consulta pedidos para preparacion.')
ON DUPLICATE KEY UPDATE
  descripcion = VALUES(descripcion);

INSERT INTO usuarios (rol_id, nombre, email, telefono, password)
SELECT r.id, 'Administrador CafeGo', 'admin@cafego.local', NULL, '$2y$10$DU3yMjmEIgXksbJ.PSR82eONXebsMHJMJEuOCrHxYNrrIGJr.W6hW'
FROM roles r
WHERE r.nombre = 'administrador'
LIMIT 1
ON DUPLICATE KEY UPDATE
  email = VALUES(email);

INSERT INTO categorias (nombre, slug, icono, orden)
VALUES
  ('Cafe caliente', 'calientes', 'cup-soda', 1),
  ('Cafe frio', 'frios', 'snowflake', 2),
  ('Te e infusiones', 'infusiones', 'leaf', 3),
  ('Postres', 'postres', 'cake-slice', 4)
ON DUPLICATE KEY UPDATE
  nombre = VALUES(nombre),
  icono = VALUES(icono),
  orden = VALUES(orden);

INSERT INTO productos (categoria_id, nombre, slug, descripcion, precio, imagen, destacado)
VALUES
  ((SELECT id FROM categorias WHERE slug = 'calientes'), 'Cafe espresso', 'espresso', 'Shot de cafe puro y concentrado.', 10000.00, 'https://images.unsplash.com/photo-1510707577719-ae7c14805e3a?auto=format&fit=crop&w=500&q=80', 1),
  ((SELECT id FROM categorias WHERE slug = 'calientes'), 'Cafe capuccino', 'capuccino', 'Espresso con espuma cremosa de leche.', 12500.00, 'https://images.unsplash.com/photo-1534778101976-62847782c213?auto=format&fit=crop&w=500&q=80', 1),
  ((SELECT id FROM categorias WHERE slug = 'calientes'), 'Cafe americano', 'americano', 'Espresso suavizado con agua caliente.', 7000.00, 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=500&q=80', 1),
  ((SELECT id FROM categorias WHERE slug = 'calientes'), 'Cafe lungo', 'lungo', 'Espresso largo con sabor intenso.', 5000.00, 'https://images.unsplash.com/photo-1497636577773-f1231844b336?auto=format&fit=crop&w=500&q=80', 0),
  ((SELECT id FROM categorias WHERE slug = 'frios'), 'Cafe cortado', 'cortado', 'Espresso con un toque de leche.', 13000.00, 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?auto=format&fit=crop&w=500&q=80', 1),
  ((SELECT id FROM categorias WHERE slug = 'frios'), 'Cafe mocha', 'mocha', 'Cafe frio con chocolate y leche.', 13500.00, 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=500&q=80', 1),
  ((SELECT id FROM categorias WHERE slug = 'frios'), 'Cafe frappe', 'frappe', 'Cafe frio licuado con hielo y crema.', 15000.00, 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?auto=format&fit=crop&w=500&q=80', 0),
  ((SELECT id FROM categorias WHERE slug = 'infusiones'), 'Infusion de canela', 'canela', 'Bebida aromatica con notas dulces.', 5000.00, 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?auto=format&fit=crop&w=500&q=80', 0),
  ((SELECT id FROM categorias WHERE slug = 'infusiones'), 'Infusion de manzanilla', 'manzanilla', 'Infusion suave y relajante.', 4000.00, 'https://images.unsplash.com/photo-1564890369478-c89ca6d9cde9?auto=format&fit=crop&w=500&q=80', 1),
  ((SELECT id FROM categorias WHERE slug = 'infusiones'), 'Infusion de menta', 'menta', 'Infusion fresca con aroma natural.', 4000.00, 'https://images.unsplash.com/photo-1567922045116-2a00fae2ed03?auto=format&fit=crop&w=500&q=80', 0),
  ((SELECT id FROM categorias WHERE slug = 'postres'), 'Tarta', 'tarta', 'Postre con crema y frutos rojos.', 20000.00, 'https://images.unsplash.com/photo-1464305795204-6f5bbfc7fb81?auto=format&fit=crop&w=500&q=80', 0),
  ((SELECT id FROM categorias WHERE slug = 'postres'), 'Tiramisu', 'tiramisu', 'Postre de cafe, cacao y crema.', 13500.00, 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?auto=format&fit=crop&w=500&q=80', 1),
  ((SELECT id FROM categorias WHERE slug = 'postres'), 'Flan', 'flan', 'Postre suave con caramelo.', 10000.00, 'https://images.unsplash.com/photo-1587314168485-3236d6710814?auto=format&fit=crop&w=500&q=80', 0)
ON DUPLICATE KEY UPDATE
  categoria_id = VALUES(categoria_id),
  nombre = VALUES(nombre),
  descripcion = VALUES(descripcion),
  precio = VALUES(precio),
  imagen = VALUES(imagen),
  destacado = VALUES(destacado);
