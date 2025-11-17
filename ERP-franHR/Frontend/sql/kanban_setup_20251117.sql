USE `erp-dam`;

START TRANSACTION;

SET
    @col_existe := (
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'usuarios'
            AND COLUMN_NAME = 'nombrecompleto'
    );

SET
    @ddl := IF(
        @col_existe = 0,
        'ALTER TABLE `usuarios` ADD COLUMN `nombrecompleto` VARCHAR(200) NOT NULL DEFAULT '''' AFTER `apellidos`',
        'SELECT 1'
    );

PREPARE stmt FROM @ddl;

EXECUTE stmt;

DEALLOCATE PREPARE stmt;

UPDATE `usuarios`
SET
    `nombrecompleto` = TRIM(
        CONCAT(
            COALESCE(`nombre`, ''),
            ' ',
            COALESCE(`apellidos`, '')
        )
    )
WHERE
    `nombrecompleto` = ''
    OR `nombrecompleto` IS NULL;

-- 2) Tablas necesarias para el módulo Kanban
CREATE TABLE IF NOT EXISTS `kanban_tableros` (
    `Identificador` INT NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(150) NOT NULL,
    `descripcion` TEXT,
    `usuario_propietario` INT NOT NULL,
    `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`Identificador`),
    KEY `idx_kanban_tablero_usuario` (`usuario_propietario`),
    CONSTRAINT `fk_kanban_tablero_usuario` FOREIGN KEY (`usuario_propietario`) REFERENCES `usuarios` (`Identificador`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kanban_columnas` (
    `Identificador` INT NOT NULL AUTO_INCREMENT,
    `tablero_id` INT NOT NULL,
    `nombre` VARCHAR(120) NOT NULL,
    `color` VARCHAR(7) NOT NULL DEFAULT '#875A7B',
    `orden` INT NOT NULL DEFAULT 1,
    `limite_tarjetas` INT DEFAULT NULL,
    `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`Identificador`),
    KEY `idx_kanban_columnas_tablero` (`tablero_id`, `orden`),
    CONSTRAINT `fk_kanban_columnas_tablero` FOREIGN KEY (`tablero_id`) REFERENCES `kanban_tableros` (`Identificador`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kanban_tarjetas` (
    `Identificador` INT NOT NULL AUTO_INCREMENT,
    `columna_id` INT NOT NULL,
    `titulo` VARCHAR(255) NOT NULL,
    `descripcion` TEXT,
    `orden` INT NOT NULL DEFAULT 1,
    `asignado_a` INT DEFAULT NULL,
    `prioridad` TINYINT DEFAULT 2,
    `fecha_vencimiento` DATE DEFAULT NULL,
    `etiquetas` VARCHAR(255) DEFAULT NULL,
    `creado_por` INT DEFAULT NULL,
    `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`Identificador`),
    KEY `idx_kanban_tarjetas_columna` (`columna_id`, `orden`),
    KEY `idx_kanban_tarjetas_asignado` (`asignado_a`),
    CONSTRAINT `fk_kanban_tarjetas_columna` FOREIGN KEY (`columna_id`) REFERENCES `kanban_columnas` (`Identificador`) ON DELETE CASCADE,
    CONSTRAINT `fk_kanban_tarjetas_asignado` FOREIGN KEY (`asignado_a`) REFERENCES `usuarios` (`Identificador`) ON DELETE SET NULL,
    CONSTRAINT `fk_kanban_tarjetas_creador` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`Identificador`) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- 3) Semilla mínima para que el tablero cargue sin errores
INSERT INTO
    `kanban_tableros` (
        `Identificador`,
        `nombre`,
        `descripcion`,
        `usuario_propietario`,
        `fecha_creacion`
    )
SELECT 1, 'Tablero General', 'Tablero inicial del sistema Kanban', 1, NOW()
WHERE
    NOT EXISTS (
        SELECT 1
        FROM `kanban_tableros`
        WHERE
            `Identificador` = 1
    );

INSERT INTO
    `kanban_columnas` (
        `tablero_id`,
        `nombre`,
        `color`,
        `orden`,
        `fecha_creacion`
    )
SELECT 1, 'Por hacer', '#e74c3c', 1, NOW()
WHERE
    NOT EXISTS (
        SELECT 1
        FROM `kanban_columnas`
        WHERE
            `tablero_id` = 1
            AND `orden` = 1
    );

INSERT INTO
    `kanban_columnas` (
        `tablero_id`,
        `nombre`,
        `color`,
        `orden`,
        `fecha_creacion`
    )
SELECT 1, 'En progreso', '#f39c12', 2, NOW()
WHERE
    NOT EXISTS (
        SELECT 1
        FROM `kanban_columnas`
        WHERE
            `tablero_id` = 1
            AND `orden` = 2
    );

INSERT INTO
    `kanban_columnas` (
        `tablero_id`,
        `nombre`,
        `color`,
        `orden`,
        `fecha_creacion`
    )
SELECT 1, 'Completado', '#27ae60', 3, NOW()
WHERE
    NOT EXISTS (
        SELECT 1
        FROM `kanban_columnas`
        WHERE
            `tablero_id` = 1
            AND `orden` = 3
    );

COMMIT;