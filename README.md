# CafeGo

Proyecto web MVC en PHP para una cafeteria, adaptado desde el maquetado movil inicial.

## Como ejecutarlo en XAMPP

1. Copia o mueve esta carpeta dentro de `htdocs`.
2. En XAMPP, inicia Apache.
3. Abre `http://localhost/nombre-de-la-carpeta/public/`.

Tambien puedes entrar a la raiz del proyecto y esta redirige automaticamente a `public/`.

## Estructura

```txt
app/
  controllers/
  models/
  views/
public/
  css/
  js/
  index.php
```

Por ahora los productos estan en `app/models/Product.php`. En la siguiente etapa ese modelo se cambia para consultar MySQL con XAMPP.

## Base de datos

La configuracion inicial esta en `config/database.php`.

Valores actuales para XAMPP:

```txt
host: 127.0.0.1
base de datos: cafego
usuario: root
password: vacio
```

Antes de conectar los modelos, crea la base de datos `cafego` en phpMyAdmin o MySQL.

El script de creacion esta en `database/schema.sql`.

Puedes ejecutarlo desde la terminal con XAMPP asi:

```txt
C:\xampp\mysql\bin\mysql.exe -u root < database\schema.sql
```

El script crea un administrador inicial para entrar al panel:

```txt
email: admin@cafego.local
password: admin123
```
