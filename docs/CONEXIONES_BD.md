# Conexiones a Base de Datos

## Tablas Utilizadas
- interfaz: Almacena datos de clientes (id, nombre, email, telefono)
- notas_nota: Tabla para el módulo personalizado de notas en Odoo (titulo, contenido)

## Consultas SQL
- CREATE TABLE IF NOT EXISTS interfaz (id INTEGER PRIMARY KEY, nombre TEXT, email TEXT, telefono TEXT)
- INSERT INTO interfaz (nombre, email, telefono) VALUES (?, ?, ?)
- Para Odoo: Las consultas se manejan vía ORM, ej. self.env['notas.nota'].create({'titulo': 'Ejemplo', 'contenido': 'Nota'})

## Estructura General de la Base de Datos
- SQLite para apps simples como la de gestión de clientes.
- PostgreSQL para Odoo, con usuario 'odoo' y contraseña configurada en odoo.conf.
