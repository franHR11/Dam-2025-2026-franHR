-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS erp;

-- Usar la base de datos
USE erp;

-- Crear la tabla usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    Identificador INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(100) NOT NULL,
    nombrecompleto VARCHAR(100) NOT NULL
);

-- Insertar algunos usuarios de ejemplo
INSERT INTO usuarios (usuario, contrasena, nombrecompleto) VALUES
('admin', 'admin123', 'Administrador del Sistema'),
('juan', 'juan456', 'Juan Pérez'),
('maria', 'maria789', 'María García');