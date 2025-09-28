-- Estructura de tablas para el sistema Kanban

-- Tabla de tableros
CREATE TABLE IF NOT EXISTS `kanban_tableros` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(255) NOT NULL,
    `descripcion` TEXT,
    `usuario_id` INT NOT NULL,
    `fecha_creacion` DATETIME NOT NULL,
    `fecha_actualizacion` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`Identificador`) ON DELETE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Tabla de columnas
CREATE TABLE IF NOT EXISTS `kanban_columnas` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `tablero_id` INT NOT NULL,
    `nombre` VARCHAR(255) NOT NULL,
    `color` VARCHAR(7) DEFAULT '#3498db',
    `posicion` INT NOT NULL DEFAULT 1,
    `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`tablero_id`) REFERENCES `kanban_tableros`(`id`) ON DELETE CASCADE
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Tabla de tarjetas
CREATE TABLE IF NOT EXISTS `kanban_tarjetas` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `columna_id` INT NOT NULL,
    `titulo` VARCHAR(255) NOT NULL,
    `descripcion` TEXT,
    `color` VARCHAR(7) DEFAULT '#ffffff',
    `posicion` INT NOT NULL DEFAULT 1,
    `asignado_a` INT NULL,
    `fecha_creacion` DATETIME NOT NULL,
    `fecha_vencimiento` DATETIME NULL,
    `fecha_actualizacion` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`columna_id`) REFERENCES `kanban_columnas`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`asignado_a`) REFERENCES `usuarios`(`Identificador`) ON DELETE SET NULL
) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Índices para mejorar el rendimiento
CREATE INDEX idx_tableros_usuario ON kanban_tableros(usuario_id);
CREATE INDEX idx_columnas_tablero ON kanban_columnas(tablero_id, posicion);
CREATE INDEX idx_tarjetas_columna ON kanban_tarjetas(columna_id, posicion);
CREATE INDEX idx_tarjetas_asignado ON kanban_tarjetas(asignado_a);