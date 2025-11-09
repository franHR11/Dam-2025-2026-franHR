-- =============================================
-- SISTEMA ERP MODULAR TIPO ODOO/WORDPRESS
-- Base de datos: erp-dam
-- Autor: FranHR
-- Versión: 1.0
-- =============================================

-- LIMPIAR TABLAS EXISTENTES (CUIDADO - BORRARÁ DATOS)
-- DROP DATABASE IF EXISTS `erp-dam`;
-- CREATE DATABASE `erp-dam` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `erp-dam`;

-- =============================================
-- TABLA DE USUARIOS (EXISTENTE - MEJORADA)
-- =============================================
CREATE TABLE IF NOT EXISTS `usuarios` (
  `Identificador` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contrasena` varchar(255) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellidos` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `rol` enum('admin','usuario','gerente') NOT NULL DEFAULT 'usuario',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_ultimo_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`Identificador`),
  UNIQUE KEY `usuario` (`usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- SISTEMA DE MÓDULOS (CORE)
-- =============================================

-- Tabla principal de módulos
CREATE TABLE `modulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `nombre_tecnico` varchar(50) NOT NULL,
  `descripcion` text,
  `version` varchar(20) NOT NULL DEFAULT '1.0.0',
  `icono` varchar(50) DEFAULT 'fas fa-cube',
  `categoria` enum('ventas','compras','inventario','contabilidad','rrhh','crm','produccion','sistema') NOT NULL,
  `instalado` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 0,
  `dependencias` text COMMENT 'JSON con módulos requeridos',
  `rutas` text COMMENT 'JSON con rutas del módulo',
  `archivos` text COMMENT 'JSON con archivos del módulo',
  `permisos_por_defecto` text COMMENT 'JSON con permisos por defecto',
  `autor` varchar(100) DEFAULT NULL,
  `fecha_instalacion` datetime DEFAULT NULL,
  `fecha_activacion` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre_tecnico` (`nombre_tecnico`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configuración de módulos
CREATE TABLE `modulo_configuracion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modulo_id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text,
  `tipo` enum('texto','numero','booleano','json') NOT NULL DEFAULT 'texto',
  `descripcion` text,
  `editable` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `modulo_id` (`modulo_id`),
  FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permisos de módulos por rol
CREATE TABLE `modulo_permisos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modulo_id` int(11) NOT NULL,
  `rol` varchar(50) NOT NULL,
  `permiso` varchar(100) NOT NULL COMMENT 'crear,editar,eliminar,ver,listar,configurar',
  `concedido` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modulo_permiso_rol` (`modulo_id`,`rol`,`permiso`),
  KEY `modulo_id` (`modulo_id`),
  FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- MÓDULO: CLIENTES
-- =============================================

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `nombre_comercial` varchar(200) NOT NULL,
  `razon_social` varchar(200) DEFAULT NULL,
  `nif_cif` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `pais` varchar(100) DEFAULT 'España',
  `telefono` varchar(20) DEFAULT NULL,
  `telefono2` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `web` varchar(255) DEFAULT NULL,
  `tipo_cliente` enum('particular','empresa','autonomo','ong','publico') NOT NULL DEFAULT 'empresa',
  `forma_pago` enum('contado','transferencia','tarjeta','cheque','paypal') DEFAULT 'transferencia',
  `dias_credito` int(11) DEFAULT 0 COMMENT 'Días de crédito concedido',
  `limite_credito` decimal(12,2) DEFAULT 0.00,
  `importe_acumulado` decimal(12,2) DEFAULT 0.00,
  `saldo_pendiente` decimal(12,2) DEFAULT 0.00,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `bloqueado` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Cliente bloqueado por impago',
  `observaciones` text,
  `contacto_principal` varchar(100) DEFAULT NULL,
  `cargo_contacto` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `nif_cif` (`nif_cif`),
  KEY `tipo_cliente` (`tipo_cliente`),
  KEY `activo` (`activo`),
  KEY `created_by` (`created_by`),
  FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`Identificador`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contactos de clientes
CREATE TABLE `clientes_contactos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido1` varchar(100) DEFAULT NULL,
  `apellido2` varchar(100) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `movil` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `es_contacto_principal` tinyint(1) NOT NULL DEFAULT 0,
  `enviar_emails` tinyint(1) NOT NULL DEFAULT 1,
  `observaciones` text,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- MÓDULO: PROVEEDORES
-- =============================================

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `nombre_comercial` varchar(200) NOT NULL,
  `razon_social` varchar(200) DEFAULT NULL,
  `nif_cif` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `pais` varchar(100) DEFAULT 'España',
  `telefono` varchar(20) DEFAULT NULL,
  `telefono2` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `web` varchar(255) DEFAULT NULL,
  `tipo_proveedor` enum('material','servicio','transporte','seguro','suministro','tecnologia') NOT NULL DEFAULT 'material',
  `forma_pago` enum('contado','transferencia','tarjeta','cheque','paypal','efectivo') DEFAULT 'transferencia',
  `cuenta_bancaria` varchar(50) DEFAULT NULL,
  `swift_bic` varchar(20) DEFAULT NULL,
  `dias_pago` int(11) DEFAULT 30 COMMENT 'Días de pago habitual',
  `descuento_comercial` decimal(5,2) DEFAULT 0.00,
  `importe_acumulado` decimal(12,2) DEFAULT 0.00,
  `saldo_pendiente` decimal(12,2) DEFAULT 0.00,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `bloqueado` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Proveedor bloqueado',
  `certificaciones` text COMMENT 'JSON con certificaciones del proveedor',
  `es_proveedor_urgente` tinyint(1) NOT NULL DEFAULT 0,
  `observaciones` text,
  `contacto_principal` varchar(100) DEFAULT NULL,
  `cargo_contacto` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `nif_cif` (`nif_cif`),
  KEY `tipo_proveedor` (`tipo_proveedor`),
  KEY `activo` (`activo`),
  KEY `created_by` (`created_by`),
  FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`Identificador`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contactos de proveedores
CREATE TABLE `proveedores_contactos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido1` varchar(100) DEFAULT NULL,
  `apellido2` varchar(100) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `movil` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `es_contacto_principal` tinyint(1) NOT NULL DEFAULT 0,
  `enviar_emails` tinyint(1) NOT NULL DEFAULT 1,
  `observaciones` text,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `proveedor_id` (`proveedor_id`),
  FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- MÓDULO: PRODUCTOS
-- =============================================

CREATE TABLE `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `codigo_barras` varchar(50) DEFAULT NULL,
  `codigo_proveedor` varchar(50) DEFAULT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text,
  `categoria_id` int(11) DEFAULT NULL,
  `tipo_producto` enum('producto','servicio','consumible','material','kit','digital') NOT NULL DEFAULT 'producto',
  `unidad_medida` enum('unidades','kg','litros','metros','metros2','metros3','cajas','palets') NOT NULL DEFAULT 'unidades',
  `precio_coste` decimal(12,2) DEFAULT 0.00,
  `precio_venta` decimal(12,2) DEFAULT 0.00,
  `precio_minorista` decimal(12,2) DEFAULT 0.00,
  `precio_mayorista` decimal(12,2) DEFAULT 0.00,
  `margen` decimal(5,2) DEFAULT 0.00,
  `iva_tipo` enum('21','10','4','0','exento') NOT NULL DEFAULT '21',
  `stock_actual` decimal(12,3) NOT NULL DEFAULT 0.000,
  `stock_minimo` decimal(12,3) DEFAULT 0.000,
  `stock_maximo` decimal(12,3) DEFAULT 0.000,
  `stock_reservado` decimal(12,3) DEFAULT 0.000,
  `control_stock` tinyint(1) NOT NULL DEFAULT 1,
  `peso` decimal(10,3) DEFAULT NULL,
  `dimensiones` varchar(50) DEFAULT NULL COMMENT 'Largo x Ancho x Alto',
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `es_venta_online` tinyint(1) NOT NULL DEFAULT 1,
  `requiere_receta` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_caducidad_control` tinyint(1) NOT NULL DEFAULT 0,
  `tags` text COMMENT 'Etiquetas separadas por comas',
  `observaciones` text,
  `proveedor_principal_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `codigo_barras` (`codigo_barras`),
  KEY `nombre` (`nombre`),
  KEY `categoria_id` (`categoria_id`),
  KEY `tipo_producto` (`tipo_producto`),
  KEY `activo` (`activo`),
  KEY `proveedor_principal_id` (`proveedor_principal_id`),
  KEY `created_by` (`created_by`),
  FOREIGN KEY (`proveedor_principal_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`Identificador`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categorías de productos
CREATE TABLE `productos_categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `categoria_padre_id` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `categoria_padre_id` (`categoria_padre_id`),
  FOREIGN KEY (`categoria_padre_id`) REFERENCES `productos_categorias` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Múltiples proveedores por producto
CREATE TABLE `productos_proveedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `proveedor_id` int(11) NOT NULL,
  `codigo_proveedor` varchar(50) DEFAULT NULL,
  `precio_coste` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tiempo_entrega` int(11) DEFAULT 7 COMMENT 'Días de entrega',
  `pedido_minimo` decimal(12,3) DEFAULT 1.000,
  `es_proveedor_principal` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `producto_proveedor` (`producto_id`,`proveedor_id`),
  KEY `producto_id` (`producto_id`),
  KEY `proveedor_id` (`proveedor_id`),
  FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- MÓDULO: PRESUPUESTOS
-- =============================================

CREATE TABLE `presupuestos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_presupuesto` varchar(20) NOT NULL,
  `ejercicio` int(4) NOT NULL,
  `fecha` date NOT NULL,
  `fecha_valido_hasta` date NOT NULL,
  `estado` enum('borrador','enviado','aceptado','rechazado','cancelado') NOT NULL DEFAULT 'borrador',
  `cliente_id` int(11) NOT NULL,
  `direccion_envio_id` int(11) DEFAULT NULL,
  `contacto_id` int(11) DEFAULT NULL,
  `base_imponible` decimal(12,2) NOT NULL DEFAULT 0.00,
  `importe_descuento` decimal(12,2) NOT NULL DEFAULT 0.00,
  `porcentaje_descuento` decimal(5,2) NOT NULL DEFAULT 0.00,
  `base_imponible_descuento` decimal(12,2) NOT NULL DEFAULT 0.00,
  `importe_iva` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `moneda` varchar(3) NOT NULL DEFAULT 'EUR',
  `forma_pago` enum('contado','transferencia','tarjeta','cheque','paypal') DEFAULT 'transferencia',
  `plazo_entrega` varchar(100) DEFAULT NULL,
  `garantia` varchar(100) DEFAULT NULL,
  `notas_internas` text,
  `terminos_condiciones` text,
  `aceptado_por` varchar(100) DEFAULT NULL,
  `fecha_aceptacion` datetime DEFAULT NULL,
  `motivo_rechazo` text,
  `enviado_email` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_envio_email` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_presupuesto` (`numero_presupuesto`),
  KEY `ejercicio` (`ejercicio`),
  KEY `fecha` (`fecha`),
  KEY `estado` (`estado`),
  KEY `cliente_id` (`cliente_id`),
  KEY `created_by` (`created_by`),
  FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`Identificador`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Líneas de presupuestos
CREATE TABLE `presupuestos_lineas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `presupuesto_id` int(11) NOT NULL,
  `linea` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `descripcion` varchar(255) NOT NULL,
  `cantidad` decimal(12,3) NOT NULL DEFAULT 1.000,
  `unidad_medida` varchar(20) NOT NULL DEFAULT 'unidades',
  `precio_unitario` decimal(12,2) NOT NULL DEFAULT 0.00,
  `descuento_linea` decimal(5,2) NOT NULL DEFAULT 0.00,
  `importe_descuento` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `iva_tipo` enum('21','10','4','0','exento') NOT NULL DEFAULT '21',
  `importe_iva` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_linea` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `presupuesto_linea` (`presupuesto_id`,`linea`),
  KEY `presupuesto_id` (`presupuesto_id`),
  KEY `producto_id` (`producto_id`),
  FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuestos` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- MÓDULO: FACTURAS
-- =============================================

CREATE TABLE `facturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_factura` varchar(20) NOT NULL,
  `numero_serie` varchar(10) NOT NULL DEFAULT 'FAC',
  `ejercicio` int(4) NOT NULL,
  `fecha` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `tipo_factura` enum('venta','compra','rectificativa','proforma') NOT NULL DEFAULT 'venta',
  `estado` enum('borrador','pendiente','pagada','vencida','cancelada','cobrada') NOT NULL DEFAULT 'borrador',
  `cliente_id` int(11) DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `presupuesto_id` int(11) DEFAULT NULL,
  `direccion_envio_id` int(11) DEFAULT NULL,
  `contacto_id` int(11) DEFAULT NULL,
  `base_imponible` decimal(12,2) NOT NULL DEFAULT 0.00,
  `importe_descuento` decimal(12,2) NOT NULL DEFAULT 0.00,
  `porcentaje_descuento` decimal(5,2) NOT NULL DEFAULT 0.00,
  `base_imponible_descuento` decimal(12,2) NOT NULL DEFAULT 0.00,
  `importe_iva` decimal(12,2) NOT NULL DEFAULT 0.00,
  `importe_irpf` decimal(12,2) NOT NULL DEFAULT 0.00,
  `retencion_irpf` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `moneda` varchar(3) NOT NULL DEFAULT 'EUR',
  `forma_pago` enum('contado','transferencia','tarjeta','cheque','paypal','efectivo') DEFAULT 'transferencia',
  `numero_cuenta` varchar(50) DEFAULT NULL,
  `notas_internas` text,
  `terminos_condiciones` text,
  `fecha_pago` datetime DEFAULT NULL,
  `importe_pagado` decimal(12,2) DEFAULT 0.00,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `referencia_pago` varchar(100) DEFAULT NULL,
  `factura_rectificada_id` int(11) DEFAULT NULL,
  `motivo_rectificacion` text,
  `enviada_email` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_envio_email` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_factura` (`numero_factura`),
  KEY `ejercicio` (`ejercicio`),
  KEY `fecha` (`fecha`),
  KEY `tipo_factura` (`tipo_factura`),
  KEY `estado` (`estado`),
  KEY `cliente_id` (`cliente_id`),
  KEY `proveedor_id` (`proveedor_id`),
  KEY `presupuesto_id` (`presupuesto_id`),
  KEY `created_by` (`created_by`),
  FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuestos` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`Identificador`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Líneas de facturas
CREATE TABLE `facturas_lineas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `factura_id` int(11) NOT NULL,
  `linea` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `descripcion` varchar(255) NOT NULL,
  `cantidad` decimal(12,3) NOT NULL DEFAULT 1.000,
  `unidad_medida` varchar(20) NOT NULL DEFAULT 'unidades',
  `precio_unitario` decimal(12,2) NOT NULL DEFAULT 0.00,
  `descuento_linea` decimal(5,2) NOT NULL DEFAULT 0.00,
  `importe_descuento` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `iva_tipo` enum('21','10','4','0','exento') NOT NULL DEFAULT '21',
  `importe_iva` decimal(12,2) NOT NULL DEFAULT 0.00,
  `irpf_tipo` enum('19','15','7','0') DEFAULT NULL,
  `importe_irpf` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_linea` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `factura_linea` (`factura_id`,`linea`),
  KEY `factura_id` (`factura_id`),
  KEY `producto_id` (`producto_id`),
  FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DATOS INICIALES DEL SISTEMA
-- =============================================

-- Insertar usuario administrador por defecto (si no existe)
INSERT IGNORE INTO `usuarios` (`usuario`, `contrasena`, `nombre`, `apellidos`, `email`, `rol`, `activo`)
VALUES ('admin', 'admin123', 'Administrador', 'Sistema', 'admin@erp.com', 'admin', 1);

-- Insertar módulos base del sistema
INSERT INTO `modulos` (`nombre`, `nombre_tecnico`, `descripcion`, `icono`, `categoria`, `instalado`, `activo`, `version`) VALUES
('Clientes', 'clientes', 'Gestión completa de clientes y contactos', 'fas fa-users', 'crm', 1, 1, '1.0.0'),
('Proveedores', 'proveedores', 'Gestión de proveedores y contactos', 'fas fa-truck', 'compras', 1, 1, '1.0.0'),
('Productos', 'productos', 'Catálogo de productos y control de stock', 'fas fa-box', 'inventario', 1, 1, '1.0.0'),
('Presupuestos', 'presupuestos', 'Gestión de presupuestos para clientes', 'fas fa-file-invoice', 'ventas', 1, 1, '1.0.0'),
('Facturación', 'facturacion', 'Facturas de venta y compra', 'fas fa-file-invoice-dollar', 'contabilidad', 1, 1, '1.0.0'),
('Dashboard', 'dashboard', 'Panel principal del sistema', 'fas fa-tachometer-alt', 'sistema', 1, 1, '1.0.0'),
('Usuarios', 'usuarios', 'Gestión de usuarios y permisos', 'fas fa-user-shield', 'sistema', 1, 1, '1.0.0'),
('Configuración', 'configuracion', 'Configuración general del sistema', 'fas fa-cogs', 'sistema', 1, 1, '1.0.0');

-- Insertar configuración inicial para módulos
INSERT INTO `modulo_configuracion` (`modulo_id`, `clave`, `valor`, `tipo`, `descripcion`)
SELECT
    m.id,
    'menu_order',
    CASE m.nombre_tecnico
        WHEN 'dashboard' THEN '1'
        WHEN 'clientes' THEN '2'
        WHEN 'proveedores' THEN '3'
        WHEN 'productos' THEN '4'
        WHEN 'presupuestos' THEN '5'
        WHEN 'facturacion' THEN '6'
        WHEN 'usuarios' THEN '7'
        WHEN 'configuracion' THEN '8'
        ELSE '999'
    END,
    'numero',
    'Orden del módulo en el menú'
FROM modulos m WHERE m.instalado = 1;

-- Insertar permisos por defecto para cada módulo
INSERT INTO `modulo_permisos` (`modulo_id`, `rol`, `permiso`, `concedido`)
SELECT
    m.id,
    'admin',
    p.permiso,
    1
FROM modulos m
CROSS JOIN (
    SELECT 'ver' as permiso UNION ALL
    SELECT 'crear' UNION ALL
    SELECT 'editar' UNION ALL
    SELECT 'eliminar' UNION ALL
    SELECT 'listar' UNION ALL
    SELECT 'configurar'
) p
WHERE m.instalado = 1;

-- Insertar categorías de productos por defecto
INSERT INTO `productos_categorias` (`nombre`, `descripcion`) VALUES
('Hardware', 'Componentes y equipos informáticos'),
('Software', 'Programas y licencias'),
('Servicios', 'Servicios técnicos y consultoría'),
('Suministros', 'Material de oficina y consumibles'),
('Componentes', 'Piezas y recambios'),
('Otros', 'Otros productos no clasificados');

-- Insertar clientes de ejemplo
INSERT INTO `clientes` (`codigo`, `nombre_comercial`, `razon_social`, `nif_cif`, `email`, `telefono`, `tipo_cliente`, `created_by`) VALUES
('CLI0001', 'Empresa Técnica S.L.', 'Empresa Técnica Soluciones S.L.', 'B12345678', 'info@empresa-tecnica.com', '912345678', 'empresa', 1),
('CLI0002', 'José García', 'José García Martín', '12345678Z', 'jgarcia@email.com', '600123456', 'autonomo', 1),
('CLI0003', 'Municipalidad Madrid', 'Ayuntamiento de Madrid', 'P87654321', 'contratacion@madrid.es', '915555555', 'publico', 1);

-- Insertar proveedores de ejemplo
INSERT INTO `proveedores` (`codigo`, `nombre_comercial`, `razon_social`, `nif_cif`, `email`, `telefono`, `tipo_proveedor`, `created_by`) VALUES
('PROV0001', 'Distribuidora IT', 'Distribuidora Informática Tecnológica S.L.', 'B87654321', 'compras@distri-it.com', '918765432', 'material', 1),
('PROV0002', 'Servicios Cloud', 'Cloud Services S.L.', 'B11223344', 'soporte@cloud-services.es', '900123123', 'servicio', 1),
('PROV0003', 'Transporte Rápido', 'Transportes Rápidos S.A.', 'A55667788', 'logistica@transporte-rapido.com', '933334444', 'transporte', 1);

-- Insertar productos de ejemplo
INSERT INTO `productos` (`codigo`, `nombre`, `descripcion`, `categoria_id`, `tipo_producto`, `precio_coste`, `precio_venta`, `iva_tipo`, `stock_actual`, `proveedor_principal_id`, `created_by`)
SELECT
    p.codigo,
    p.nombre,
    p.descripcion,
    c.id,
    p.tipo_producto,
    p.precio_coste,
    p.precio_venta,
    p.iva_tipo,
    p.stock_actual,
    prov.id,
    1
FROM (
    SELECT 'HARD001' as codigo, 'Portátil HP 15"' as nombre, 'Portátil HP 15 pulgadas, 8GB RAM, 256GB SSD' as descripcion, 1 as categoria_id, 'producto' as tipo_producto, 450.00 as precio_coste, 699.00 as precio_venta, '21' as iva_tipo, 50 as stock_actual, 1 as proveedor_principal_id UNION ALL
    SELECT 'HARD002', 'Monitor Dell 24"', 'Monitor Dell 24 pulgadas Full HD IPS', 1, 'producto', 120.00, 189.00, '21', 30, 1 UNION ALL
    SELECT 'SOFT001', 'Windows 11 Pro', 'Licencia Windows 11 Professional OEM', 2, 'producto', 45.00, 89.00, '21', 100, 2 UNION ALL
    SELECT 'SERV001', 'Instalación Windows', 'Servicio de instalación de Windows', 3, 'servicio', 0.00, 60.00, '21', 0, NULL UNION ALL
    SELECT 'CONS001', 'Soporte Técnico', 'Soporte técnico remoto por hora', 3, 'servicio', 0.00, 40.00, '21', 0, NULL UNION ALL
    SELECT 'SUMI001', 'Pack Ratón USB', 'Pack 10 ratones USB básicos', 4, 'material', 80.00, 150.00, '21', 20, 1 UNION ALL
    SELECT 'COMP001', 'RAM DDR4 8GB', 'Módulo de memoria RAM DDR4 8GB 3200MHz', 5, 'material', 25.00, 45.00, '21', 75, 1
) p
CROSS JOIN productos_categorias c
WHERE c.id = p.categoria_id
CROSS JOIN proveedores prov
WHERE prov.codigo = 'PROV0001' AND p.proveedor_principal_id = 1
LIMIT 7;

-- =============================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =============================================

-- Índices para búsquedas rápidas
CREATE INDEX idx_clientes_nombre ON clientes(nombre_comercial);
CREATE INDEX idx_proveedores_nombre ON proveedores(nombre_comercial);
CREATE INDEX idx_productos_nombre ON productos(nombre);
CREATE INDEX idx_presupuestos_numero ON presupuestos(numero_presupuesto);
CREATE INDEX idx_facturas_numero ON facturas(numero_factura);

-- Índices compuestos
CREATE INDEX idx_facturas_cliente_estado ON facturas(cliente_id, estado);
CREATE INDEX idx_presupuestos_cliente_estado ON presupuestos(cliente_id, estado);
CREATE INDEX idx_productos_categoria_activo ON productos(categoria_id, activo);

-- =============================================
-- VISTAS ÚTILES
-- =============================================

-- Vista para productos con stock bajo
CREATE OR REPLACE VIEW `vista_productos_stock_bajo` AS
SELECT
    p.*,
    c.nombre as nombre_categoria,
    (p.stock_actual <= p.stock_minimo AND p.control_stock = 1) as stock_critico
FROM productos p
LEFT JOIN productos_categorias c ON p.categoria_id = c.id
WHERE p.activo = 1 AND p.control_stock = 1 AND p.stock_actual <= p.stock_minimo;

-- Vista para clientes con saldo pendiente
CREATE OR REPLACE VIEW `vista_clientes_saldo_pendiente` AS
SELECT
    c.*,
    COALESCE(f.total_pendiente, 0) as saldo_pendiente
FROM clientes c
LEFT JOIN (
    SELECT
        cliente_id,
        SUM(total - COALESCE(importe_pagado, 0)) as total_pendiente
    FROM facturas
    WHERE estado IN ('pendiente', 'vencida') AND tipo_factura = 'venta'
    GROUP BY cliente_id
) f ON c.id = f.cliente_id
WHERE c.activo = 1 AND COALESCE(f.total_pendiente, 0) > 0;

-- =============================================
-- TRIGGERS PARA MANTENER INTEGRIDAD
-- =============================================

DELIMITER $$

-- Trigger para actualizar saldo de cliente
CREATE TRIGGER tr_actualizar_saldo_cliente_factura
AFTER INSERT ON facturas_lineas
FOR EACH ROW
BEGIN
    UPDATE facturas f
    SET f.base_imponible = (
        SELECT COALESCE(SUM(subtotal), 0)
        FROM facturas_lineas fl
        WHERE fl.factura_id = f.id
    ),
    f.total = (
        SELECT COALESCE(SUM(total_linea), 0)
        FROM facturas_lineas fl
        WHERE fl.factura_id = f.id
    )
    WHERE f.id = NEW.factura_id;
END$$

DELIMITER ;

-- =============================================
-- COMENTARIOS FINALES
-- =============================================

-- Este SQL crea un sistema ERP completo y modular con:
-- 1. Sistema de módulos configurable (similar a Odoo/WordPress)
-- 2. Gestión completa de clientes, proveedores y productos
-- 3. Sistema de presupuestos y facturación
-- 4. Sistema de permisos por rol y módulo
-- 5. Control de stock y gestión de categorías
-- 6. Vistas y triggers para optimización
-- 7. Datos de ejemplo para pruebas inmediatas

-- Para implementar el sistema de menú dinámico:
-- 1. Leer los módulos activos desde la tabla `modulos`
-- 2. Verificar permisos del usuario en `modulo_permisos`
-- 3. Construir el menú dinámicamente según `modulo_configuracion.menu_order`

-- Próximos pasos recomendados:
-- 1. Crear API endpoints para gestión de módulos
-- 2. Implementar frontend dinámico del menú
-- 3. Crear sistema de instalación/desinstalación de módulos
-- 4. Desarrollar interfaces CRUD para cada módulo
