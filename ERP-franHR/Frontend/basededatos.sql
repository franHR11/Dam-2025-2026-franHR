-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 08, 2025 at 05:49 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `erp-dam`
--

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `id` int NOT NULL,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_comercial` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razon_social` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nif_cif` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_postal` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provincia` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pais` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'España',
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono2` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `web` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_cliente` enum('particular','empresa','autonomo','ong','publico') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'empresa',
  `forma_pago` enum('contado','transferencia','tarjeta','cheque','paypal') COLLATE utf8mb4_unicode_ci DEFAULT 'transferencia',
  `dias_credito` int DEFAULT '0' COMMENT 'Días de crédito concedido',
  `limite_credito` decimal(12,2) DEFAULT '0.00',
  `importe_acumulado` decimal(12,2) DEFAULT '0.00',
  `saldo_pendiente` decimal(12,2) DEFAULT '0.00',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `bloqueado` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Cliente bloqueado por impago',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `contacto_principal` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo_contacto` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clientes`
--

INSERT INTO `clientes` (`id`, `codigo`, `nombre_comercial`, `razon_social`, `nif_cif`, `direccion`, `codigo_postal`, `ciudad`, `provincia`, `pais`, `telefono`, `telefono2`, `email`, `web`, `tipo_cliente`, `forma_pago`, `dias_credito`, `limite_credito`, `importe_acumulado`, `saldo_pendiente`, `activo`, `bloqueado`, `observaciones`, `contacto_principal`, `cargo_contacto`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'CLI0001', 'Empresa Técnica S.L.', 'Empresa Técnica Soluciones S.L.', 'B12345678', NULL, NULL, NULL, NULL, 'España', '912345678', NULL, 'info@empresa-tecnica.com', NULL, 'empresa', 'transferencia', 0, 0.00, 0.00, 0.00, 1, 0, NULL, NULL, NULL, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(2, 'CLI0002', 'José García', 'José García Martín', '12345678Z', NULL, NULL, NULL, NULL, 'España', '600123456', NULL, 'jgarcia@email.com', NULL, 'autonomo', 'transferencia', 0, 0.00, 0.00, 0.00, 1, 0, NULL, NULL, NULL, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(3, 'CLI0003', 'Municipalidad Madrid', 'Ayuntamiento de Madrid', 'P87654321', NULL, NULL, NULL, NULL, 'España', '915555555', NULL, 'contratacion@madrid.es', NULL, 'publico', 'transferencia', 0, 0.00, 0.00, 0.00, 1, 0, NULL, NULL, NULL, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43');

-- --------------------------------------------------------

--
-- Table structure for table `clientes_contactos`
--

CREATE TABLE `clientes_contactos` (
  `id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido1` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apellido2` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departamento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `movil` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `es_contacto_principal` tinyint(1) NOT NULL DEFAULT '0',
  `enviar_emails` tinyint(1) NOT NULL DEFAULT '1',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facturas`
--

CREATE TABLE `facturas` (
  `id` int NOT NULL,
  `numero_factura` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_serie` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FAC',
  `ejercicio` int NOT NULL,
  `fecha` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `tipo_factura` enum('venta','compra','rectificativa','proforma') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'venta',
  `estado` enum('borrador','pendiente','pagada','vencida','cancelada','cobrada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'borrador',
  `cliente_id` int DEFAULT NULL,
  `proveedor_id` int DEFAULT NULL,
  `presupuesto_id` int DEFAULT NULL,
  `direccion_envio_id` int DEFAULT NULL,
  `contacto_id` int DEFAULT NULL,
  `base_imponible` decimal(12,2) NOT NULL DEFAULT '0.00',
  `importe_descuento` decimal(12,2) NOT NULL DEFAULT '0.00',
  `porcentaje_descuento` decimal(5,2) NOT NULL DEFAULT '0.00',
  `base_imponible_descuento` decimal(12,2) NOT NULL DEFAULT '0.00',
  `importe_iva` decimal(12,2) NOT NULL DEFAULT '0.00',
  `importe_irpf` decimal(12,2) NOT NULL DEFAULT '0.00',
  `retencion_irpf` decimal(5,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `moneda` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EUR',
  `forma_pago` enum('contado','transferencia','tarjeta','cheque','paypal','efectivo') COLLATE utf8mb4_unicode_ci DEFAULT 'transferencia',
  `numero_cuenta` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas_internas` text COLLATE utf8mb4_unicode_ci,
  `terminos_condiciones` text COLLATE utf8mb4_unicode_ci,
  `fecha_pago` datetime DEFAULT NULL,
  `importe_pagado` decimal(12,2) DEFAULT '0.00',
  `metodo_pago` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia_pago` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `factura_rectificada_id` int DEFAULT NULL,
  `motivo_rectificacion` text COLLATE utf8mb4_unicode_ci,
  `enviada_email` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_envio_email` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facturas_lineas`
--

CREATE TABLE `facturas_lineas` (
  `id` int NOT NULL,
  `factura_id` int NOT NULL,
  `linea` int NOT NULL,
  `producto_id` int DEFAULT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` decimal(12,3) NOT NULL DEFAULT '1.000',
  `unidad_medida` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unidades',
  `precio_unitario` decimal(12,2) NOT NULL DEFAULT '0.00',
  `descuento_linea` decimal(5,2) NOT NULL DEFAULT '0.00',
  `importe_descuento` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `iva_tipo` enum('21','10','4','0','exento') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '21',
  `importe_iva` decimal(12,2) NOT NULL DEFAULT '0.00',
  `irpf_tipo` enum('19','15','7','0') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `importe_irpf` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_linea` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `facturas_lineas`
--
DELIMITER $$
CREATE TRIGGER `tr_actualizar_totales_factura` AFTER INSERT ON `facturas_lineas` FOR EACH ROW BEGIN
    UPDATE facturas f
    SET
        f.base_imponible = (
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
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_actualizar_totales_factura_delete` AFTER DELETE ON `facturas_lineas` FOR EACH ROW BEGIN
    UPDATE facturas f
    SET
        f.base_imponible = (
            SELECT COALESCE(SUM(subtotal), 0)
            FROM facturas_lineas fl
            WHERE fl.factura_id = f.id
        ),
        f.total = (
            SELECT COALESCE(SUM(total_linea), 0)
            FROM facturas_lineas fl
            WHERE fl.factura_id = f.id
        )
    WHERE f.id = OLD.factura_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_actualizar_totales_factura_update` AFTER UPDATE ON `facturas_lineas` FOR EACH ROW BEGIN
    UPDATE facturas f
    SET
        f.base_imponible = (
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `modulos`
--

CREATE TABLE `modulos` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_tecnico` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `version` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0.0',
  `icono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'fas fa-cube',
  `categoria` enum('ventas','compras','inventario','contabilidad','rrhh','crm','produccion','sistema') COLLATE utf8mb4_unicode_ci NOT NULL,
  `instalado` tinyint(1) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '0',
  `dependencias` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON con módulos requeridos',
  `rutas` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON con rutas del módulo',
  `archivos` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON con archivos del módulo',
  `permisos_por_defecto` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON con permisos por defecto',
  `autor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_instalacion` datetime DEFAULT NULL,
  `fecha_activacion` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modulos`
--

INSERT INTO `modulos` (`id`, `nombre`, `nombre_tecnico`, `descripcion`, `version`, `icono`, `categoria`, `instalado`, `activo`, `dependencias`, `rutas`, `archivos`, `permisos_por_defecto`, `autor`, `fecha_instalacion`, `fecha_activacion`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard', 'dashboard', 'Panel principal del sistema', '1.0.0', 'fas fa-tachometer-alt', 'sistema', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(2, 'Clientes', 'clientes', 'Gestión completa de clientes y contactos', '1.0.0', 'fas fa-users', 'crm', 1, 0, NULL, NULL, NULL, NULL, NULL, '2025-11-08 18:36:32', NULL, '2025-11-08 11:14:43', '2025-11-08 17:45:50'),
(3, 'Proveedores', 'proveedores', 'Gestión de proveedores y contactos', '1.0.0', 'fas fa-truck', 'compras', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-08 11:14:43', '2025-11-08 17:48:34'),
(4, 'Productos', 'productos', 'Catálogo de productos y control de stock', '1.0.0', 'fas fa-box', 'inventario', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-08 18:29:11', '2025-11-08 11:14:43', '2025-11-08 17:29:11'),
(5, 'Presupuestos', 'presupuestos', 'Gestión de presupuestos para clientes', '1.0.0', 'fas fa-file-invoice', 'ventas', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(6, 'Facturación', 'facturacion', 'Facturas de venta y compra', '1.0.0', 'fas fa-file-invoice-dollar', 'contabilidad', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-08 18:42:11', '2025-11-08 11:14:43', '2025-11-08 17:42:11'),
(7, 'Usuarios', 'usuarios', 'Gestión de usuarios y permisos', '1.0.0', 'fas fa-user-shield', 'sistema', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(8, 'Configuración', 'configuracion', 'Configuración general del sistema', '1.0.0', 'fas fa-cogs', 'sistema', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-08 11:14:43', '2025-11-08 11:14:43');

-- --------------------------------------------------------

--
-- Table structure for table `modulo_configuracion`
--

CREATE TABLE `modulo_configuracion` (
  `id` int NOT NULL,
  `modulo_id` int NOT NULL,
  `clave` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text COLLATE utf8mb4_unicode_ci,
  `tipo` enum('texto','numero','booleano','json') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'texto',
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `editable` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modulo_configuracion`
--

INSERT INTO `modulo_configuracion` (`id`, `modulo_id`, `clave`, `valor`, `tipo`, `descripcion`, `editable`, `created_at`, `updated_at`) VALUES
(1, 1, 'menu_order', '1', 'numero', 'Orden del módulo en el menú', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(4, 4, 'menu_order', '4', 'numero', 'Orden del módulo en el menú', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(5, 5, 'menu_order', '5', 'numero', 'Orden del módulo en el menú', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(6, 6, 'menu_order', '6', 'numero', 'Orden del módulo en el menú', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(7, 7, 'menu_order', '7', 'numero', 'Orden del módulo en el menú', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(8, 8, 'menu_order', '8', 'numero', 'Orden del módulo en el menú', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43');

-- --------------------------------------------------------

--
-- Table structure for table `modulo_permisos`
--

CREATE TABLE `modulo_permisos` (
  `id` int NOT NULL,
  `modulo_id` int NOT NULL,
  `rol` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permiso` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'crear,editar,eliminar,ver,listar,configurar',
  `concedido` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modulo_permisos`
--

INSERT INTO `modulo_permisos` (`id`, `modulo_id`, `rol`, `permiso`, `concedido`, `created_at`, `updated_at`) VALUES
(1, 8, 'admin', 'ver', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(2, 7, 'admin', 'ver', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(3, 6, 'admin', 'ver', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(4, 5, 'admin', 'ver', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(5, 4, 'admin', 'ver', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(8, 1, 'admin', 'ver', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(9, 8, 'admin', 'crear', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(10, 7, 'admin', 'crear', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(11, 6, 'admin', 'crear', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(12, 5, 'admin', 'crear', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(13, 4, 'admin', 'crear', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(16, 1, 'admin', 'crear', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(17, 8, 'admin', 'editar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(18, 7, 'admin', 'editar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(19, 6, 'admin', 'editar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(20, 5, 'admin', 'editar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(21, 4, 'admin', 'editar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(24, 1, 'admin', 'editar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(25, 8, 'admin', 'eliminar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(26, 7, 'admin', 'eliminar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(27, 6, 'admin', 'eliminar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(28, 5, 'admin', 'eliminar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(29, 4, 'admin', 'eliminar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(32, 1, 'admin', 'eliminar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(33, 8, 'admin', 'listar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(34, 7, 'admin', 'listar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(35, 6, 'admin', 'listar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(36, 5, 'admin', 'listar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(37, 4, 'admin', 'listar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(40, 1, 'admin', 'listar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(41, 8, 'admin', 'configurar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(42, 7, 'admin', 'configurar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(43, 6, 'admin', 'configurar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(44, 5, 'admin', 'configurar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(45, 4, 'admin', 'configurar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(48, 1, 'admin', 'configurar', 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43');

-- --------------------------------------------------------

--
-- Table structure for table `presupuestos`
--

CREATE TABLE `presupuestos` (
  `id` int NOT NULL,
  `numero_presupuesto` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ejercicio` int NOT NULL,
  `fecha` date NOT NULL,
  `fecha_valido_hasta` date NOT NULL,
  `estado` enum('borrador','enviado','aceptado','rechazado','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'borrador',
  `cliente_id` int NOT NULL,
  `direccion_envio_id` int DEFAULT NULL,
  `contacto_id` int DEFAULT NULL,
  `base_imponible` decimal(12,2) NOT NULL DEFAULT '0.00',
  `importe_descuento` decimal(12,2) NOT NULL DEFAULT '0.00',
  `porcentaje_descuento` decimal(5,2) NOT NULL DEFAULT '0.00',
  `base_imponible_descuento` decimal(12,2) NOT NULL DEFAULT '0.00',
  `importe_iva` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `moneda` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EUR',
  `forma_pago` enum('contado','transferencia','tarjeta','cheque','paypal') COLLATE utf8mb4_unicode_ci DEFAULT 'transferencia',
  `plazo_entrega` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `garantia` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas_internas` text COLLATE utf8mb4_unicode_ci,
  `terminos_condiciones` text COLLATE utf8mb4_unicode_ci,
  `aceptado_por` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_aceptacion` datetime DEFAULT NULL,
  `motivo_rechazo` text COLLATE utf8mb4_unicode_ci,
  `enviado_email` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_envio_email` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `presupuestos_lineas`
--

CREATE TABLE `presupuestos_lineas` (
  `id` int NOT NULL,
  `presupuesto_id` int NOT NULL,
  `linea` int NOT NULL,
  `producto_id` int DEFAULT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` decimal(12,3) NOT NULL DEFAULT '1.000',
  `unidad_medida` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unidades',
  `precio_unitario` decimal(12,2) NOT NULL DEFAULT '0.00',
  `descuento_linea` decimal(5,2) NOT NULL DEFAULT '0.00',
  `importe_descuento` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `iva_tipo` enum('21','10','4','0','exento') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '21',
  `importe_iva` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_linea` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `productos`
--

CREATE TABLE `productos` (
  `id` int NOT NULL,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_barras` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_proveedor` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `categoria_id` int DEFAULT NULL,
  `tipo_producto` enum('producto','servicio','consumible','material','kit','digital') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'producto',
  `unidad_medida` enum('unidades','kg','litros','metros','metros2','metros3','cajas','palets') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unidades',
  `precio_coste` decimal(12,2) DEFAULT '0.00',
  `precio_venta` decimal(12,2) DEFAULT '0.00',
  `precio_minorista` decimal(12,2) DEFAULT '0.00',
  `precio_mayorista` decimal(12,2) DEFAULT '0.00',
  `margen` decimal(5,2) DEFAULT '0.00',
  `iva_tipo` enum('21','10','4','0','exento') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '21',
  `stock_actual` decimal(12,3) NOT NULL DEFAULT '0.000',
  `stock_minimo` decimal(12,3) DEFAULT '0.000',
  `stock_maximo` decimal(12,3) DEFAULT '0.000',
  `stock_reservado` decimal(12,3) DEFAULT '0.000',
  `control_stock` tinyint(1) NOT NULL DEFAULT '1',
  `peso` decimal(10,3) DEFAULT NULL,
  `dimensiones` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Largo x Ancho x Alto',
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `es_venta_online` tinyint(1) NOT NULL DEFAULT '1',
  `requiere_receta` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_caducidad_control` tinyint(1) NOT NULL DEFAULT '0',
  `tags` text COLLATE utf8mb4_unicode_ci COMMENT 'Etiquetas separadas por comas',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `proveedor_principal_id` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `productos`
--

INSERT INTO `productos` (`id`, `codigo`, `codigo_barras`, `codigo_proveedor`, `nombre`, `descripcion`, `categoria_id`, `tipo_producto`, `unidad_medida`, `precio_coste`, `precio_venta`, `precio_minorista`, `precio_mayorista`, `margen`, `iva_tipo`, `stock_actual`, `stock_minimo`, `stock_maximo`, `stock_reservado`, `control_stock`, `peso`, `dimensiones`, `imagen`, `activo`, `es_venta_online`, `requiere_receta`, `fecha_caducidad_control`, `tags`, `observaciones`, `proveedor_principal_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'COMP001', NULL, NULL, 'RAM DDR4 8GB', 'Módulo de memoria RAM DDR4 8GB 3200MHz', 5, 'material', 'unidades', 25.00, 45.00, 0.00, 0.00, 0.00, '21', 75.000, 0.000, 0.000, 0.000, 1, NULL, NULL, NULL, 1, 1, 0, 0, NULL, NULL, 1, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(2, 'HARD001', NULL, NULL, 'Portátil HP 15\"', 'Portátil HP 15 pulgadas, 8GB RAM, 256GB SSD', 1, 'producto', 'unidades', 450.00, 699.00, 0.00, 0.00, 0.00, '21', 50.000, 0.000, 0.000, 0.000, 1, NULL, NULL, NULL, 1, 1, 0, 0, NULL, NULL, 1, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(3, 'HARD002', NULL, NULL, 'Monitor Dell 24\"', 'Monitor Dell 24 pulgadas Full HD IPS', 1, 'producto', 'unidades', 120.00, 189.00, 0.00, 0.00, 0.00, '21', 30.000, 0.000, 0.000, 0.000, 1, NULL, NULL, NULL, 1, 1, 0, 0, NULL, NULL, 1, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(4, 'SERV001', NULL, NULL, 'Instalación Windows', 'Servicio de instalación de Windows', 3, 'servicio', 'unidades', 0.00, 60.00, 0.00, 0.00, 0.00, '21', 0.000, 0.000, 0.000, 0.000, 1, NULL, NULL, NULL, 1, 1, 0, 0, NULL, NULL, 1, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(5, 'SERV002', NULL, NULL, 'Soporte Técnico', 'Soporte técnico remoto por hora', 3, 'servicio', 'unidades', 0.00, 40.00, 0.00, 0.00, 0.00, '21', 0.000, 0.000, 0.000, 0.000, 1, NULL, NULL, NULL, 1, 1, 0, 0, NULL, NULL, 1, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(6, 'SOFT001', NULL, NULL, 'Windows 11 Pro', 'Licencia Windows 11 Professional OEM', 2, 'producto', 'unidades', 45.00, 89.00, 0.00, 0.00, 0.00, '21', 100.000, 0.000, 0.000, 0.000, 1, NULL, NULL, NULL, 1, 1, 0, 0, NULL, NULL, 1, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(7, 'SUMI001', NULL, NULL, 'Pack Ratón USB', 'Pack 10 ratones USB básicos', 4, 'material', 'unidades', 80.00, 150.00, 0.00, 0.00, 0.00, '21', 20.000, 0.000, 0.000, 0.000, 1, NULL, NULL, NULL, 1, 1, 0, 0, NULL, NULL, 1, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43');

-- --------------------------------------------------------

--
-- Table structure for table `productos_categorias`
--

CREATE TABLE `productos_categorias` (
  `id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `categoria_padre_id` int DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `productos_categorias`
--

INSERT INTO `productos_categorias` (`id`, `nombre`, `descripcion`, `categoria_padre_id`, `imagen`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Hardware', 'Componentes y equipos informáticos', NULL, NULL, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(2, 'Software', 'Programas y licencias', NULL, NULL, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(3, 'Servicios', 'Servicios técnicos y consultoría', NULL, NULL, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(4, 'Suministros', 'Material de oficina y consumibles', NULL, NULL, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(5, 'Componentes', 'Piezas y recambios', NULL, NULL, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(6, 'Otros', 'Otros productos no clasificados', NULL, NULL, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43');

-- --------------------------------------------------------

--
-- Table structure for table `productos_proveedores`
--

CREATE TABLE `productos_proveedores` (
  `id` int NOT NULL,
  `producto_id` int NOT NULL,
  `proveedor_id` int NOT NULL,
  `codigo_proveedor` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio_coste` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tiempo_entrega` int DEFAULT '7' COMMENT 'Días de entrega',
  `pedido_minimo` decimal(12,3) DEFAULT '1.000',
  `es_proveedor_principal` tinyint(1) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int NOT NULL,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_comercial` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razon_social` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nif_cif` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_postal` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provincia` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pais` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'España',
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono2` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `web` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_proveedor` enum('material','servicio','transporte','seguro','suministro','tecnologia') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'material',
  `forma_pago` enum('contado','transferencia','tarjeta','cheque','paypal','efectivo') COLLATE utf8mb4_unicode_ci DEFAULT 'transferencia',
  `cuenta_bancaria` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `swift_bic` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dias_pago` int DEFAULT '30' COMMENT 'Días de pago habitual',
  `descuento_comercial` decimal(5,2) DEFAULT '0.00',
  `importe_acumulado` decimal(12,2) DEFAULT '0.00',
  `saldo_pendiente` decimal(12,2) DEFAULT '0.00',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `bloqueado` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Proveedor bloqueado',
  `certificaciones` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON con certificaciones del proveedor',
  `es_proveedor_urgente` tinyint(1) NOT NULL DEFAULT '0',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `contacto_principal` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo_contacto` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `proveedores`
--

INSERT INTO `proveedores` (`id`, `codigo`, `nombre_comercial`, `razon_social`, `nif_cif`, `direccion`, `codigo_postal`, `ciudad`, `provincia`, `pais`, `telefono`, `telefono2`, `email`, `web`, `tipo_proveedor`, `forma_pago`, `cuenta_bancaria`, `swift_bic`, `dias_pago`, `descuento_comercial`, `importe_acumulado`, `saldo_pendiente`, `activo`, `bloqueado`, `certificaciones`, `es_proveedor_urgente`, `observaciones`, `contacto_principal`, `cargo_contacto`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'PROV0001', 'Distribuidora IT', 'Distribuidora Informática Tecnológica S.L.', 'B87654321', NULL, NULL, NULL, NULL, 'España', '918765432', NULL, 'compras@distri-it.com', NULL, 'material', 'transferencia', NULL, NULL, 30, 0.00, 0.00, 0.00, 1, 0, NULL, 0, NULL, NULL, NULL, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(2, 'PROV0002', 'Servicios Cloud', 'Cloud Services S.L.', 'B11223344', NULL, NULL, NULL, NULL, 'España', '900123123', NULL, 'soporte@cloud-services.es', NULL, 'servicio', 'transferencia', NULL, NULL, 30, 0.00, 0.00, 0.00, 1, 0, NULL, 0, NULL, NULL, NULL, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43'),
(3, 'PROV0003', 'Transporte Rápido', 'Transportes Rápidos S.A.', 'A55667788', NULL, NULL, NULL, NULL, 'España', '933334444', NULL, 'logistica@transporte-rapido.com', NULL, 'transporte', 'transferencia', NULL, NULL, 30, 0.00, 0.00, 0.00, 1, 0, NULL, 0, NULL, NULL, NULL, 1, '2025-11-08 11:14:43', '2025-11-08 11:14:43');

-- --------------------------------------------------------

--
-- Table structure for table `proveedores_contactos`
--

CREATE TABLE `proveedores_contactos` (
  `id` int NOT NULL,
  `proveedor_id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido1` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apellido2` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departamento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `movil` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `es_contacto_principal` tinyint(1) NOT NULL DEFAULT '0',
  `enviar_emails` tinyint(1) NOT NULL DEFAULT '1',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `Identificador` int NOT NULL,
  `usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contrasena` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apellidos` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rol` enum('admin','usuario','gerente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'usuario',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_ultimo_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`Identificador`, `usuario`, `email`, `contrasena`, `nombre`, `apellidos`, `telefono`, `rol`, `activo`, `fecha_creacion`, `fecha_ultimo_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@erp.com', 'admin123', 'Administrador', 'Sistema', NULL, 'admin', 1, '2025-11-08 12:14:43', NULL, '2025-11-08 11:14:43', '2025-11-08 11:14:43');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vista_clientes_saldo_pendiente`
-- (See below for the actual view)
--
CREATE TABLE `vista_clientes_saldo_pendiente` (
`activo` tinyint(1)
,`bloqueado` tinyint(1)
,`cargo_contacto` varchar(100)
,`ciudad` varchar(100)
,`codigo` varchar(20)
,`codigo_postal` varchar(10)
,`contacto_principal` varchar(100)
,`created_at` timestamp
,`created_by` int
,`dias_credito` int
,`direccion` varchar(255)
,`email` varchar(150)
,`forma_pago` enum('contado','transferencia','tarjeta','cheque','paypal')
,`id` int
,`importe_acumulado` decimal(12,2)
,`limite_credito` decimal(12,2)
,`nif_cif` varchar(20)
,`nombre_comercial` varchar(200)
,`observaciones` text
,`pais` varchar(100)
,`provincia` varchar(100)
,`razon_social` varchar(200)
,`saldo_pendiente` decimal(12,2)
,`saldo_pendiente_facturas` decimal(35,2)
,`telefono` varchar(20)
,`telefono2` varchar(20)
,`tipo_cliente` enum('particular','empresa','autonomo','ong','publico')
,`updated_at` timestamp
,`web` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vista_productos_stock_bajo`
-- (See below for the actual view)
--
CREATE TABLE `vista_productos_stock_bajo` (
`activo` tinyint(1)
,`categoria_id` int
,`codigo` varchar(50)
,`codigo_barras` varchar(50)
,`codigo_proveedor` varchar(50)
,`control_stock` tinyint(1)
,`created_at` timestamp
,`created_by` int
,`descripcion` text
,`dimensiones` varchar(50)
,`es_venta_online` tinyint(1)
,`fecha_caducidad_control` tinyint(1)
,`id` int
,`imagen` varchar(255)
,`iva_tipo` enum('21','10','4','0','exento')
,`margen` decimal(5,2)
,`nombre` varchar(200)
,`nombre_categoria` varchar(100)
,`observaciones` text
,`peso` decimal(10,3)
,`precio_coste` decimal(12,2)
,`precio_mayorista` decimal(12,2)
,`precio_minorista` decimal(12,2)
,`precio_venta` decimal(12,2)
,`proveedor_principal_id` int
,`requiere_receta` tinyint(1)
,`stock_actual` decimal(12,3)
,`stock_critico` int
,`stock_maximo` decimal(12,3)
,`stock_minimo` decimal(12,3)
,`stock_reservado` decimal(12,3)
,`tags` text
,`tipo_producto` enum('producto','servicio','consumible','material','kit','digital')
,`unidad_medida` enum('unidades','kg','litros','metros','metros2','metros3','cajas','palets')
,`updated_at` timestamp
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `nif_cif` (`nif_cif`),
  ADD KEY `tipo_cliente` (`tipo_cliente`),
  ADD KEY `activo` (`activo`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_clientes_nombre` (`nombre_comercial`);

--
-- Indexes for table `clientes_contactos`
--
ALTER TABLE `clientes_contactos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Indexes for table `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_factura` (`numero_factura`),
  ADD KEY `ejercicio` (`ejercicio`),
  ADD KEY `fecha` (`fecha`),
  ADD KEY `tipo_factura` (`tipo_factura`),
  ADD KEY `estado` (`estado`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `proveedor_id` (`proveedor_id`),
  ADD KEY `presupuesto_id` (`presupuesto_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `factura_rectificada_id` (`factura_rectificada_id`),
  ADD KEY `idx_facturas_numero` (`numero_factura`),
  ADD KEY `idx_facturas_cliente_estado` (`cliente_id`,`estado`);

--
-- Indexes for table `facturas_lineas`
--
ALTER TABLE `facturas_lineas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `factura_linea` (`factura_id`,`linea`),
  ADD KEY `factura_id` (`factura_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indexes for table `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_tecnico` (`nombre_tecnico`);

--
-- Indexes for table `modulo_configuracion`
--
ALTER TABLE `modulo_configuracion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `modulo_id` (`modulo_id`);

--
-- Indexes for table `modulo_permisos`
--
ALTER TABLE `modulo_permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `modulo_permiso_rol` (`modulo_id`,`rol`,`permiso`),
  ADD KEY `modulo_id` (`modulo_id`);

--
-- Indexes for table `presupuestos`
--
ALTER TABLE `presupuestos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_presupuesto` (`numero_presupuesto`),
  ADD KEY `ejercicio` (`ejercicio`),
  ADD KEY `fecha` (`fecha`),
  ADD KEY `estado` (`estado`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_presupuestos_numero` (`numero_presupuesto`),
  ADD KEY `idx_presupuestos_cliente_estado` (`cliente_id`,`estado`);

--
-- Indexes for table `presupuestos_lineas`
--
ALTER TABLE `presupuestos_lineas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `presupuesto_linea` (`presupuesto_id`,`linea`),
  ADD KEY `presupuesto_id` (`presupuesto_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indexes for table `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `codigo_barras` (`codigo_barras`),
  ADD KEY `nombre` (`nombre`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `tipo_producto` (`tipo_producto`),
  ADD KEY `activo` (`activo`),
  ADD KEY `proveedor_principal_id` (`proveedor_principal_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_productos_nombre` (`nombre`),
  ADD KEY `idx_productos_categoria_activo` (`categoria_id`,`activo`);

--
-- Indexes for table `productos_categorias`
--
ALTER TABLE `productos_categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `categoria_padre_id` (`categoria_padre_id`);

--
-- Indexes for table `productos_proveedores`
--
ALTER TABLE `productos_proveedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `producto_proveedor` (`producto_id`,`proveedor_id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `proveedor_id` (`proveedor_id`);

--
-- Indexes for table `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `nif_cif` (`nif_cif`),
  ADD KEY `tipo_proveedor` (`tipo_proveedor`),
  ADD KEY `activo` (`activo`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_proveedores_nombre` (`nombre_comercial`);

--
-- Indexes for table `proveedores_contactos`
--
ALTER TABLE `proveedores_contactos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proveedor_id` (`proveedor_id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`Identificador`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `clientes_contactos`
--
ALTER TABLE `clientes_contactos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `facturas_lineas`
--
ALTER TABLE `facturas_lineas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `modulo_configuracion`
--
ALTER TABLE `modulo_configuracion`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `modulo_permisos`
--
ALTER TABLE `modulo_permisos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `presupuestos`
--
ALTER TABLE `presupuestos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `presupuestos_lineas`
--
ALTER TABLE `presupuestos_lineas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `productos_categorias`
--
ALTER TABLE `productos_categorias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `productos_proveedores`
--
ALTER TABLE `productos_proveedores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `proveedores_contactos`
--
ALTER TABLE `proveedores_contactos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `Identificador` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

-- --------------------------------------------------------

--
-- Structure for view `vista_clientes_saldo_pendiente`
--
DROP TABLE IF EXISTS `vista_clientes_saldo_pendiente`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_clientes_saldo_pendiente`  AS SELECT `c`.`id` AS `id`, `c`.`codigo` AS `codigo`, `c`.`nombre_comercial` AS `nombre_comercial`, `c`.`razon_social` AS `razon_social`, `c`.`nif_cif` AS `nif_cif`, `c`.`direccion` AS `direccion`, `c`.`codigo_postal` AS `codigo_postal`, `c`.`ciudad` AS `ciudad`, `c`.`provincia` AS `provincia`, `c`.`pais` AS `pais`, `c`.`telefono` AS `telefono`, `c`.`telefono2` AS `telefono2`, `c`.`email` AS `email`, `c`.`web` AS `web`, `c`.`tipo_cliente` AS `tipo_cliente`, `c`.`forma_pago` AS `forma_pago`, `c`.`dias_credito` AS `dias_credito`, `c`.`limite_credito` AS `limite_credito`, `c`.`importe_acumulado` AS `importe_acumulado`, `c`.`saldo_pendiente` AS `saldo_pendiente`, `c`.`activo` AS `activo`, `c`.`bloqueado` AS `bloqueado`, `c`.`observaciones` AS `observaciones`, `c`.`contacto_principal` AS `contacto_principal`, `c`.`cargo_contacto` AS `cargo_contacto`, `c`.`created_by` AS `created_by`, `c`.`created_at` AS `created_at`, `c`.`updated_at` AS `updated_at`, coalesce(`f`.`total_pendiente`,0) AS `saldo_pendiente_facturas` FROM (`clientes` `c` left join (select `facturas`.`cliente_id` AS `cliente_id`,sum((`facturas`.`total` - coalesce(`facturas`.`importe_pagado`,0))) AS `total_pendiente` from `facturas` where ((`facturas`.`estado` in ('pendiente','vencida')) and (`facturas`.`tipo_factura` = 'venta')) group by `facturas`.`cliente_id`) `f` on((`c`.`id` = `f`.`cliente_id`))) WHERE ((`c`.`activo` = 1) AND (coalesce(`f`.`total_pendiente`,0) > 0)) ;

-- --------------------------------------------------------

--
-- Structure for view `vista_productos_stock_bajo`
--
DROP TABLE IF EXISTS `vista_productos_stock_bajo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos_stock_bajo`  AS SELECT `p`.`id` AS `id`, `p`.`codigo` AS `codigo`, `p`.`codigo_barras` AS `codigo_barras`, `p`.`codigo_proveedor` AS `codigo_proveedor`, `p`.`nombre` AS `nombre`, `p`.`descripcion` AS `descripcion`, `p`.`categoria_id` AS `categoria_id`, `p`.`tipo_producto` AS `tipo_producto`, `p`.`unidad_medida` AS `unidad_medida`, `p`.`precio_coste` AS `precio_coste`, `p`.`precio_venta` AS `precio_venta`, `p`.`precio_minorista` AS `precio_minorista`, `p`.`precio_mayorista` AS `precio_mayorista`, `p`.`margen` AS `margen`, `p`.`iva_tipo` AS `iva_tipo`, `p`.`stock_actual` AS `stock_actual`, `p`.`stock_minimo` AS `stock_minimo`, `p`.`stock_maximo` AS `stock_maximo`, `p`.`stock_reservado` AS `stock_reservado`, `p`.`control_stock` AS `control_stock`, `p`.`peso` AS `peso`, `p`.`dimensiones` AS `dimensiones`, `p`.`imagen` AS `imagen`, `p`.`activo` AS `activo`, `p`.`es_venta_online` AS `es_venta_online`, `p`.`requiere_receta` AS `requiere_receta`, `p`.`fecha_caducidad_control` AS `fecha_caducidad_control`, `p`.`tags` AS `tags`, `p`.`observaciones` AS `observaciones`, `p`.`proveedor_principal_id` AS `proveedor_principal_id`, `p`.`created_by` AS `created_by`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at`, `c`.`nombre` AS `nombre_categoria`, ((`p`.`stock_actual` <= `p`.`stock_minimo`) and (`p`.`control_stock` = 1)) AS `stock_critico` FROM (`productos` `p` left join `productos_categorias` `c` on((`p`.`categoria_id` = `c`.`id`))) WHERE ((`p`.`activo` = 1) AND (`p`.`control_stock` = 1) AND (`p`.`stock_actual` <= `p`.`stock_minimo`)) ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`Identificador`) ON DELETE SET NULL;

--
-- Constraints for table `clientes_contactos`
--
ALTER TABLE `clientes_contactos`
  ADD CONSTRAINT `clientes_contactos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `facturas_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `facturas_ibfk_3` FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuestos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `facturas_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`Identificador`) ON DELETE SET NULL,
  ADD CONSTRAINT `facturas_ibfk_5` FOREIGN KEY (`factura_rectificada_id`) REFERENCES `facturas` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `facturas_lineas`
--
ALTER TABLE `facturas_lineas`
  ADD CONSTRAINT `facturas_lineas_ibfk_1` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `facturas_lineas_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `modulo_configuracion`
--
ALTER TABLE `modulo_configuracion`
  ADD CONSTRAINT `modulo_configuracion_ibfk_1` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `modulo_permisos`
--
ALTER TABLE `modulo_permisos`
  ADD CONSTRAINT `modulo_permisos_ibfk_1` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `presupuestos`
--
ALTER TABLE `presupuestos`
  ADD CONSTRAINT `presupuestos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `presupuestos_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`Identificador`) ON DELETE SET NULL;

--
-- Constraints for table `presupuestos_lineas`
--
ALTER TABLE `presupuestos_lineas`
  ADD CONSTRAINT `presupuestos_lineas_ibfk_1` FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuestos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `presupuestos_lineas_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `productos_categorias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`proveedor_principal_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `productos_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`Identificador`) ON DELETE SET NULL;

--
-- Constraints for table `productos_categorias`
--
ALTER TABLE `productos_categorias`
  ADD CONSTRAINT `fk_categoria_padre` FOREIGN KEY (`categoria_padre_id`) REFERENCES `productos_categorias` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `productos_proveedores`
--
ALTER TABLE `productos_proveedores`
  ADD CONSTRAINT `productos_proveedores_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `productos_proveedores_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `proveedores`
--
ALTER TABLE `proveedores`
  ADD CONSTRAINT `proveedores_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`Identificador`) ON DELETE SET NULL;

--
-- Constraints for table `proveedores_contactos`
--
ALTER TABLE `proveedores_contactos`
  ADD CONSTRAINT `proveedores_contactos_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
