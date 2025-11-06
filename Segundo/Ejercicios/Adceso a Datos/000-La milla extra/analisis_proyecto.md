# Análisis Completo del Proyecto GMIP

## Resumen Ejecutivo
GMIP es una aplicación web educativa desarrollada en PHP puro que implementa un sistema de gestión de inventario y pedidos. El proyecto está diseñado para demostrar conocimientos avanzados en acceso a datos, cubriendo todas las unidades del temario de DAM segundo trimestre.

## Arquitectura General
- **Tipo**: Aplicación web monolítica con API REST + SPA frontend
- **Lenguaje principal**: PHP 8.1+ con tipos estrictos
- **Base de datos**: MySQL/MariaDB (relacional) + MongoDB (documental)
- **Frontend**: HTML5 + JavaScript vanilla + TailwindCSS
- **Gestión de dependencias**: Composer (opcional)

## Tecnologías y Librerías Utilizadas

### Backend - PHP Core
- **PHP 8.1+**: Uso de tipos estrictos, namespaces, autoloading
- **PDO**: Conector principal para MySQL, con prepared statements
- **Composer**: Gestión de dependencias (doctrine/orm, mongodb/mongodb)
- **Funciones nativas PHP**:
  - Manejo de ficheros: `fopen`, `fread`, `fwrite`, `fseek`, `rename`, `copy`
  - Serialización: `serialize`, `unserialize`
  - Parsing: `json_encode/decode`, `SimpleXMLElement`, `str_getcsv`
  - Fechas: `date()`, zona horaria Europe/Madrid

### Bases de Datos
- **MySQL/MariaDB**:
  - Conexión PDO con charset utf8mb4
  - Transacciones implícitas en procedimientos almacenados
  - Prepared statements para prevenir SQL injection
- **MongoDB**:
  - Driver oficial mongodb/mongodb via Composer
  - Operaciones CRUD básicas (insertOne, find, updateMany, deleteMany)

### ORM y Mapeo
- **Doctrine ORM 2.16**:
  - Preparado para uso con anotaciones (entidades en src/Model/Entity/)
  - No implementado completamente (comentado como "base para mapeo ORM")
  - PSR-4 autoloading via Composer

### Gestión de Configuración
- **Archivo .env**: Variables de entorno con prefijo GMIP_
- **Clase Config**: Carga, validación y centralización de configuración
- **Validación robusta**: Parser propio de .env, soporte para comillas y comentarios

### Componentes y Servicios

#### DataAccessComponent
- **Ubicación**: src/Model/DataAccessComponent.php
- **Funcionalidades**:
  - Wrapper para PDO con métodos query() y exec()
  - Sistema de eventos/hooks (beforeQuery, afterQuery, error)
  - Persistencia del componente via serialización
  - Inyección de PDO via método withPdo()

#### Servicios Especializados
- **ImportExportService**: Conversión entre formatos CSV, JSON, XML
- **MongoService**: Operaciones CRUD en MongoDB
- **FileService**: Manejo de ficheros binarios con acceso aleatorio

### Frontend
- **HTML5**: Estructura semántica básica
- **CSS**: TailwindCSS via CDN para estilos modernos
- **JavaScript vanilla**: 
  - Fetch API para llamadas REST
  - Manipulación DOM para SPA ligera
  - Web Components implícitos (formularios reutilizables)
  - Gestión de estado con arrays locales (productsCache)

## Estructura de Archivos y Directorios

```
/gmip/
├── .env                    # Variables de entorno (no versionado)
├── .env.example           # Plantilla de configuración
├── composer.json          # Dependencias PHP
├── README.md              # Documentación técnica
├── explicacion_ejercicio.md # Guía de explicación (creada)
├── config/
│   ├── database.php       # Conexión PDO a MySQL
│   ├── env.php           # Gestión de configuración
│   └── mongo.php         # Conexión a MongoDB
├── public/
│   ├── index.php         # Punto de entrada API REST
│   ├── index.html        # Frontend SPA
│   ├── assets/
│   │   ├── app.css       # Estilos Tailwind
│   │   └── app.js        # Lógica frontend
│   └── uploads/          # Almacenamiento de archivos subidos
├── scripts/
│   ├── create_sp.sql     # Procedimiento almacenado
│   └── seed.sql          # Datos iniciales BD
└── src/
    ├── bootstrap.php     # Inicialización del sistema
    ├── Controller/       # Controladores REST
    │   ├── ProductController.php
    │   ├── ProviderController.php
    │   └── OrderController.php
    ├── Model/
    │   ├── DataAccessComponent.php  # Componente principal
    │   └── Entity/      # Entidades de dominio
    │       ├── Product.php
    │       └── Provider.php
    └── Service/          # Servicios de negocio
        ├── FileService.php
        ├── ImportExportService.php
        └── MongoService.php
```

## Funciones y Métodos Importantes

### Clases Principales

#### GMIP\DB\Database
- `getPdo()`: Retorna instancia PDO configurada con charset y modo exception

#### GMIP\Config\Config
- `init()`: Carga configuración desde .env
- `get()`: Obtiene valor de configuración
- `validate()`: Valida configuración requerida
- `requireKeys()`: Lanza excepción si claves faltan

#### GMIP\Model\DataAccessComponent
- `withPdo(PDO)`: Inyección de conexión
- `query(string, array)`: Ejecuta SELECT con parámetros
- `exec(string, array)`: Ejecuta INSERT/UPDATE/DELETE
- `on(string, callable)`: Registra eventos
- `serializeConfig()`: Serializa configuración para persistencia

#### Controladores
- `handle(PDO, string)`: Dispatcher basado en método HTTP
- Métodos privados: get(), create(), update(), delete()
- Validación: validateCreate(), validateUpdate()

### API REST Endpoints
- `GET /?ruta=health`: Estado del sistema
- `GET /?ruta=config-check`: Validación de configuración
- `GET/POST/PUT/DELETE /?ruta=productos`: CRUD productos
- `GET/POST/PUT/DELETE /?ruta=proveedores`: CRUD proveedores
- `GET/POST /?ruta=pedidos`: Gestión de pedidos
- `POST /?ruta=procesar-pedido`: Procesamiento con SP

## Tablas de Base de Datos

### MySQL
```sql
-- Proveedores
CREATE TABLE providers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  created_at DATETIME NOT NULL
);

-- Productos
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  sku VARCHAR(100) NOT NULL UNIQUE,
  price DECIMAL(12,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  provider_id INT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_products_provider FOREIGN KEY (provider_id) REFERENCES providers(id)
);

-- Pedidos
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(100) NOT NULL UNIQUE,
  created_at DATETIME NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'pending'
);

-- Líneas de pedido
CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id),
  CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Logs del sistema
CREATE TABLE logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(50) NOT NULL,
  message TEXT NOT NULL,
  created_at DATETIME NOT NULL
);
```

### MongoDB
- Colecciones dinámicas (logs, attachments, notes)
- Documentos JSON sin esquema fijo
- Búsqueda por campos arbitrarios

## Procedimientos Almacenados
```sql
DELIMITER $$
CREATE PROCEDURE sp_process_order(IN p_order_id INT)
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE v_product_id INT;
    DECLARE v_qty INT;

    DECLARE cur CURSOR FOR
        SELECT product_id, quantity FROM order_items WHERE order_id = p_order_id;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO v_product_id, v_qty;
        IF done = 1 THEN
            LEAVE read_loop;
        END IF;
        -- Actualizar stock
        UPDATE products SET stock = stock - v_qty WHERE id = v_product_id;
    END LOOP;
    CLOSE cur;

    -- Marcar como procesado
    UPDATE orders SET status = 'processed' WHERE id = p_order_id;

    -- Log
    INSERT INTO logs (type, message, created_at)
    VALUES ('order', CONCAT('Pedido procesado: ', p_order_id), NOW());
END$$
DELIMITER ;
```

## Manejo de Errores y Excepciones
- **PDOException**: Capturadas en conexiones y operaciones BD
- **RuntimeException**: Para configuraciones faltantes o conexiones fallidas
- **Throwable**: Captura genérica en punto de entrada
- **Códigos HTTP**: 400 (validación), 404 (no encontrado), 409 (duplicado), 500 (servidor)

## Seguridad Implementada
- **Prepared statements**: Prevención SQL injection
- **Validación de entrada**: Tipos y rangos en PHP
- **CORS**: Configurado via variable de entorno
- **Charset**: UTF-8 en conexiones BD
- **No hardcoding**: Todo via .env

## Rendimiento y Optimización
- **Conexión persistente**: PDO reutilizado en requests
- **Lazy loading**: No implementado (Doctrine no activo)
- **Índices**: Claves primarias y foreign keys
- **Paginación**: No implementada en este MVP
- **Caché**: Arrays locales en frontend (productsCache)

## Testing y Debugging
- **Scripts de seed**: Datos de prueba consistentes
- **Logs del sistema**: Tabla logs + MongoDB para auditoría
- **Validación de configuración**: Endpoint config-check
- **Excepciones descriptivas**: Mensajes claros para debugging

## Limitaciones y Mejoras Posibles
- **ORM**: Solo preparado, no implementado completamente
- **Autenticación**: No implementada
- **Paginación**: Falta en listados largos
- **Transacciones**: Solo en SP, no en código PHP
- **Testing**: Sin tests automatizados
- **Frontend**: SPA básica, no framework moderno

Este análisis proporciona una visión completa de las tecnologías, arquitectura y implementación del proyecto GMIP, facilitando su comprensión y mantenimiento.
