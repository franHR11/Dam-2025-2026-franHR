-- Script para añadir la columna 'icono' a la tabla productos_categorias
-- Este script puede ser ejecutado si se desea agregar soporte para iconos de FontAwesome

-- Agregar la columna icono a la tabla productos_categorias
ALTER TABLE `productos_categorias`
ADD COLUMN `icono` VARCHAR(100) NULL DEFAULT NULL
AFTER `imagen`;

-- Actualizar categorías existentes con iconos predeterminados
UPDATE `productos_categorias` SET `icono` = 'fas fa-microchip' WHERE `nombre` LIKE '%hardware%' OR `nombre` LIKE '%componente%';
UPDATE `productos_categorias` SET `icono` = 'fas fa-laptop-code' WHERE `nombre` LIKE '%software%' OR `nombre` LIKE '%programa%';
UPDATE `productos_categorias` SET `icono` = 'fas fa-print' WHERE `nombre` LIKE '%impresora%';
UPDATE `productos_categorias` SET `icono` = 'fas fa-mobile-alt' WHERE `nombre` LIKE '%móvil%' OR `nombre` LIKE '%telefono%';
UPDATE `productos_categorias` SET `icono` = 'fas fa-desktop' WHERE `nombre` LIKE '%computadora%' OR `nombre` LIKE '%pc%';
UPDATE `productos_categorias` SET `icono` = 'fas fa-tv' WHERE `nombre` LIKE '%monitor%' OR `nombre` LIKE '%pantalla%';
UPDATE `productos_categorias` SET `icono` = 'fas fa-keyboard' WHERE `nombre` LIKE '%teclado%';
UPDATE `productos_categorias` SET `icono` = 'fas fa-mouse' WHERE `nombre` LIKE '%mouse%' OR `nombre` LIKE '%ratón%';
UPDATE `productos_categorias` SET `icono` = 'fas fa-hdd' WHERE `nombre` LIKE '%disco%' OR `nombre` LIKE '%almacenamiento%';
UPDATE `productos_categorias` SET `icono` = 'fas fa-memory' WHERE `nombre` LIKE '%memoria%' OR `nombre` LIKE '%ram%';
UPDATE `productos_categorias` SET `icono` = 'fas fa-microchip' WHERE `icono` IS NULL;

-- Nota: Después de ejecutar este script, se puede actualizar la API y el frontend
-- para utilizar los iconos de FontAwesome en las categorías
