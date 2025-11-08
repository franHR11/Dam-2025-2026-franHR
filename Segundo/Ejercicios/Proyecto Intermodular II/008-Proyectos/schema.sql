CREATE DATABASE erp_db;
USE erp_db;

CREATE TABLE productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255),
    precio DECIMAL(10,2),
    imagen VARCHAR(255)
);

CREATE TABLE ventas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    producto_id INT,
    cantidad INT,
    total DECIMAL(10,2),
    fecha DATE
);

CREATE TABLE empleados (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255),
    puesto VARCHAR(255)
);

INSERT INTO productos (nombre, precio, imagen) VALUES ('Producto A', 10.00, 'img/productoA.jpg');
INSERT INTO ventas (producto_id, cantidad, total, fecha) VALUES (1, 2, 20.00, '2025-11-05');
INSERT INTO empleados (nombre, puesto) VALUES ('Fran', 'Desarrollador');
