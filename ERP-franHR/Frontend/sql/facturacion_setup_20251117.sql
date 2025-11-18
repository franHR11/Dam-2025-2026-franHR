USE `erp-dam`;

START TRANSACTION;

-- ---------------------------------------------------------------------------
-- Tablas base para el módulo de Facturación (cabecera y líneas)
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `facturas` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `numero_factura` VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `numero_serie` VARCHAR(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FAC',
    `ejercicio` INT NOT NULL,
    `fecha` DATE NOT NULL,
    `fecha_vencimiento` DATE NOT NULL,
    `tipo_factura` ENUM(
        'venta',
        'compra',
        'rectificativa',
        'proforma'
    ) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'venta',
    `estado` ENUM(
        'borrador',
        'pendiente',
        'pagada',
        'vencida',
        'cancelada',
        'cobrada'
    ) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'borrador',
    `cliente_id` INT DEFAULT NULL,
    `proveedor_id` INT DEFAULT NULL,
    `presupuesto_id` INT DEFAULT NULL,
    `direccion_envio_id` INT DEFAULT NULL,
    `contacto_id` INT DEFAULT NULL,
    `base_imponible` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `importe_descuento` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `porcentaje_descuento` DECIMAL(5, 2) NOT NULL DEFAULT '0.00',
    `base_imponible_descuento` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `importe_iva` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `importe_irpf` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `retencion_irpf` DECIMAL(5, 2) NOT NULL DEFAULT '0.00',
    `total` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `moneda` VARCHAR(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EUR',
    `forma_pago` ENUM(
        'contado',
        'transferencia',
        'tarjeta',
        'cheque',
        'paypal',
        'efectivo'
    ) COLLATE utf8mb4_unicode_ci DEFAULT 'transferencia',
    `numero_cuenta` VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `notas_internas` TEXT COLLATE utf8mb4_unicode_ci,
    `terminos_condiciones` TEXT COLLATE utf8mb4_unicode_ci,
    `fecha_pago` DATETIME DEFAULT NULL,
    `importe_pagado` DECIMAL(12, 2) DEFAULT '0.00',
    `metodo_pago` VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `referencia_pago` VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `factura_rectificada_id` INT DEFAULT NULL,
    `motivo_rectificacion` TEXT COLLATE utf8mb4_unicode_ci,
    `enviada_email` TINYINT(1) NOT NULL DEFAULT '0',
    `fecha_envio_email` DATETIME DEFAULT NULL,
    `created_by` INT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_factura_numero` (`numero_factura`),
    KEY `idx_facturas_cliente_estado` (`cliente_id`, `estado`),
    KEY `idx_facturas_ejercicio` (`ejercicio`),
    CONSTRAINT `fk_factura_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_factura_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_factura_presupuesto` FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuestos` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_factura_usuario` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`Identificador`) ON DELETE SET NULL,
    CONSTRAINT `fk_factura_rectificada` FOREIGN KEY (`factura_rectificada_id`) REFERENCES `facturas` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `facturas_lineas` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `factura_id` INT NOT NULL,
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
    `irpf_tipo` ENUM('19', '15', '7', '0') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `importe_irpf` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `total_linea` DECIMAL(12, 2) NOT NULL DEFAULT '0.00',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_factura_linea` (`factura_id`, `linea`),
    KEY `idx_factura_producto` (`producto_id`),
    CONSTRAINT `fk_factura_linea_cabecera` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_factura_linea_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Triggers para mantener totales de factura en sincronía con sus líneas
-- ---------------------------------------------------------------------------

DELIMITER $$

CREATE TRIGGER `tr_facturas_totales_insert`
AFTER INSERT ON `facturas_lineas`
FOR EACH ROW
BEGIN
    UPDATE facturas f
    SET
        f.base_imponible = (
            SELECT COALESCE(SUM(subtotal), 0)
            FROM facturas_lineas
            WHERE factura_id = f.id
        ),
        f.importe_iva = (
            SELECT COALESCE(SUM(importe_iva), 0)
            FROM facturas_lineas
            WHERE factura_id = f.id
        ),
        f.importe_irpf = (
            SELECT COALESCE(SUM(importe_irpf), 0)
            FROM facturas_lineas
            WHERE factura_id = f.id
        ),
        f.total = (
            SELECT COALESCE(SUM(total_linea), 0)
            FROM facturas_lineas
            WHERE factura_id = f.id
        )
    WHERE f.id = NEW.factura_id;
END$$

CREATE TRIGGER `tr_facturas_totales_update`
AFTER UPDATE ON `facturas_lineas`
FOR EACH ROW
BEGIN
    UPDATE facturas f
    SET
        f.base_imponible = (
            SELECT COALESCE(SUM(subtotal), 0)
            FROM facturas_lineas
            WHERE factura_id = f.id
        ),
        f.importe_iva = (
            SELECT COALESCE(SUM(importe_iva), 0)
            FROM facturas_lineas
            WHERE factura_id = f.id
        ),
        f.importe_irpf = (
            SELECT COALESCE(SUM(importe_irpf), 0)
            FROM facturas_lineas
            WHERE factura_id = f.id
        ),
        f.total = (
            SELECT COALESCE(SUM(total_linea), 0)
            FROM facturas_lineas
            WHERE factura_id = f.id
        )
    WHERE f.id = NEW.factura_id;
END$$

CREATE TRIGGER `tr_facturas_totales_delete`
AFTER DELETE ON `facturas_lineas`
FOR EACH ROW
BEGIN
    UPDATE facturas f
    SET
        f.base_imponible = (
            SELECT COALESCE(SUM(subtotal), 0)
            FROM facturas_lineas
            WHERE factura_id = f.id
        ),
        f.importe_iva = (
            SELECT COALESCE(SUM(importe_iva), 0)
            FROM facturas_lineas
            WHERE factura_id = f.id
        ),
        f.importe_irpf = (
            SELECT COALESCE(SUM(importe_irpf), 0)
            FROM facturas_lineas
            WHERE factura_id = f.id
        ),
        f.total = (
            SELECT COALESCE(SUM(total_linea), 0)
            FROM facturas_lineas
            WHERE factura_id = f.id
        )
    WHERE f.id = OLD.factura_id;
END$$

DELIMITER;

COMMIT;