CREATE TABLE `erp-dam`.`usuarios` (`Identificador` INT NOT NULL AUTO_INCREMENT , `usuario` VARCHAR(100) NOT NULL , `contrasena` VARCHAR(100) NOT NULL , `nombrecompleto` VARCHAR(255) NOT NULL , `email` VARCHAR(100) NOT NULL , `telefono` INT NOT NULL , PRIMARY KEY (`Identificador`)) ENGINE = InnoDB;

CREATE TABLE `erp-dam`.`categorias_aplicaciones` (`identificador` INT NOT NULL AUTO_INCREMENT , `nombre` VARCHAR(255) NOT NULL , PRIMARY KEY (`identificador`)) ENGINE = InnoDB;

CREATE TABLE `erp-dam`.`aplicaciones` (`identificador` INT NOT NULL AUTO_INCREMENT , `nombre` VARCHAR(255) NOT NULL , `descripcion` VARCHAR(255) NOT NULL , `icono` VARCHAR(255) NOT NULL , `categoria` INT NOT NULL , PRIMARY KEY (`identificador`)) ENGINE = InnoDB;

ALTER TABLE `aplicaciones` ADD CONSTRAINT `categorias_aplicaciones` FOREIGN KEY (`categoria`) REFERENCES `categorias_aplicaciones`(`identificador`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `categorias_aplicaciones` 
ADD COLUMN `icono` VARCHAR(255) NOT NULL AFTER `nombre`;

UPDATE `categorias_aplicaciones` SET icono = 'fa-solid fa-cart-shopping' WHERE identificador = 1; -- Ventas
UPDATE `categorias_aplicaciones` SET icono = 'fa-solid fa-truck' WHERE identificador = 2; -- Compras
UPDATE `categorias_aplicaciones` SET icono = 'fa-solid fa-boxes-stacked' WHERE identificador = 3; -- Inventario
UPDATE `categorias_aplicaciones` SET icono = 'fa-solid fa-calculator' WHERE identificador = 4; -- Contabilidad
UPDATE `categorias_aplicaciones` SET icono = 'fa-solid fa-users' WHERE identificador = 5; -- Recursos Humanos
UPDATE `categorias_aplicaciones` SET icono = 'fa-solid fa-diagram-project' WHERE identificador = 6; -- Proyectos
UPDATE `categorias_aplicaciones` SET icono = 'fa-solid fa-handshake' WHERE identificador = 7; -- CRM
UPDATE `categorias_aplicaciones` SET icono = 'fa-solid fa-bullhorn' WHERE identificador = 8; -- Marketing
UPDATE `categorias_aplicaciones` SET icono = 'fa-solid fa-headset' WHERE identificador = 9; -- Soporte
UPDATE `categorias_aplicaciones` SET icono = 'fa-solid fa-gears' WHERE identificador = 10; -- Administración
