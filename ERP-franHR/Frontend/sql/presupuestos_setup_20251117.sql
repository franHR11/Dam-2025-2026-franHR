USE `erp-dam`;

START TRANSACTION;

-- ---------------------------------------------------------------------------
-- Tablas base para el módulo de Presupuestos
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `presupuestos` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `numero_presupuesto` VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `ejercicio` INT NOT NULL,
    `fecha` DATE NOT NULL,
    `fecha_valido_hasta` DATE NOT NULL,
    `estado` ENUM(
        'borrador',
        'enviado',
        'aceptado',
        'rechazado',
        'cancelado'
    ) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'borrador',
    `cliente_id` INT NOT NULL,
    `direccion_envio_id` INT DEFAULT NULL,
    `contacto_id` INT DEFAULT NULL,
    `base_imponible` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `importe_descuento` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `porcentaje_descuento` DECIMAL(5, 2) NOT NULL DEFAULT '0.00',
    `base_imponible_descuento` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `importe_iva` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `total` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `moneda` VARCHAR(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EUR',
    `forma_pago` ENUM(
        'contado',
        'transferencia',
        'tarjeta',
        'cheque',
        'paypal'
    ) COLLATE utf8mb4_unicode_ci DEFAULT 'transferencia',
    `plazo_entrega` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `garantia` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `notas_internas` TEXT COLLATE utf8mb4_unicode_ci,
    `terminos_condiciones` TEXT COLLATE utf8mb4_unicode_ci,
    `aceptado_por` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `fecha_aceptacion` DATETIME DEFAULT NULL,
    `motivo_rechazo` TEXT COLLATE utf8mb4_unicode_ci,
    `enviado_email` TINYINT(1) NOT NULL DEFAULT '0',
    `fecha_envio_email` DATETIME DEFAULT NULL,
    `created_by` INT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_presupuesto_numero` (`numero_presupuesto`),
    KEY `idx_presupuestos_cliente_estado` (`cliente_id`, `estado`),
    KEY `idx_presupuestos_ejercicio` (`ejercicio`),
    CONSTRAINT `fk_presupuesto_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_presupuesto_usuario` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`Identificador`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `presupuestos_lineas` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `presupuesto_id` INT NOT NULL,
    `linea` INT NOT NULL,
    `producto_id` INT DEFAULT NULL,
    `descripcion` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `cantidad` DECIMAL(12, 3) NOT NULL DEFAULT '1.000',
    `unidad_medida` VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unidades',
    `precio_unitario` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `descuento_linea` DECIMAL(5, 2) NOT NULL DEFAULT '0.00',
    `importe_descuento` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `subtotal` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `iva_tipo` ENUM(
        '21',
        '10',
        '4',
        '0',
        'exento'
    ) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '21',
    `importe_iva` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `total_linea` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_presupuesto_linea` (`presupuesto_id`, `linea`),
    KEY `idx_presupuesto_producto` (`producto_id`),
    CONSTRAINT `fk_presupuesto_linea_cabecera` FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuestos` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_presupuesto_linea_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

COMMIT;