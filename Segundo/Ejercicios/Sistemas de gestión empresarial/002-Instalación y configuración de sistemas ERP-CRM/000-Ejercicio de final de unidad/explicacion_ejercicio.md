# Ejercicio de Final de Unidad 2: Sistema de Facturación Empresarial

**Asignatura:** Sistemas de Gestión Empresarial  
**Unidad:** 002 - Instalación y configuración de sistemas ERP-CRM  
**Alumno:** Fran  
**Fecha:** 5 de noviembre de 2025

---

## 1. Introducción breve y contextualización (25%)

En este ejercicio he desarrollado un sistema completo de facturación empresarial que va más allá del CRM de la unidad anterior. Mientras que en la unidad 1 me centré en la gestión de clientes, ahora he creado un sistema que integra tres módulos fundamentales: gestión de clientes, gestión de productos y generación de facturas.

Un sistema de facturación es esencial para cualquier empresa porque automatiza el proceso de venta, mantiene un registro contable de todas las transacciones y facilita el seguimiento de los ingresos. He decidido crear una aplicación web porque permite acceder al sistema desde cualquier lugar y facilita la colaboración entre diferentes departamentos de la empresa.

El sistema que he desarrollado permite registrar clientes con sus datos fiscales (NIF/CIF), mantener un catálogo de productos con precios y stock, y generar facturas automáticas con numeración secuencial. Además, incluye un dashboard con estadísticas en tiempo real que muestra el estado general del negocio: número de clientes, productos, facturas emitidas e ingresos totales.

Este tipo de sistema se usa en el contexto empresarial para llevar la contabilidad, generar documentos fiscales válidos, controlar el inventario y tener una visión clara de la situación financiera de la empresa. Es especialmente útil para pequeñas y medianas empresas que necesitan una solución de facturación sin la complejidad de un ERP completo.

---

## 2. Desarrollo detallado y preciso (25%)

### Arquitectura del sistema

He construido el sistema de facturación usando Flask como framework web y SQLite como base de datos. La arquitectura sigue el patrón MVC simplificado con 4 tablas relacionadas.

### Estructura de la base de datos

**Tabla clientes:** id, nombre, nif (único), direccion, telefono, email  
**Tabla productos:** id, nombre, descripcion, precio, stock  
**Tabla facturas:** id, numero_factura (único), cliente_id (FK), fecha, total, estado  
**Tabla lineas_factura:** id, factura_id (FK), producto_id (FK), cantidad, precio_unitario, subtotal

### Funcionalidades implementadas

- **Dashboard:** Estadísticas en tiempo real (clientes, productos, facturas, ingresos) y últimas 5 facturas
- **Gestión de clientes:** Listar y agregar clientes con validación de NIF único
- **Gestión de productos:** Listar y agregar productos con precio y stock
- **Gestión de facturas:** Crear facturas con múltiples productos, generación automática de número (FAC-YYYY-NNNN), cálculo automático de totales, ver detalle completo

### Tecnologías utilizadas

Flask 3.0.0, SQLite, Jinja2, HTML5, CSS3, JavaScript

### Diferencias con el sistema de la unidad 1

- **Unidad 1:** Solo gestión de clientes con búsqueda
- **Unidad 2:** Sistema completo con clientes, productos, facturas y dashboard
- Base de datos relacional con 4 tablas y claves foráneas
- Generación de documentos fiscales con numeración automática

---

## 3. Aplicación práctica con ejemplo claro (25%)

Ver código completo en los archivos adjuntos del proyecto. El sistema incluye:

- **app.py:** Aplicación Flask con todas las rutas y lógica de negocio
- **requirements.txt:** Flask==3.0.0
- **templates/:** 9 plantillas HTML (base, index, clientes, agregar_cliente, productos, agregar_producto, facturas, crear_factura, ver_factura)
- **static/css/style.css:** Estilos modernos con gradientes azules

### Instrucciones de instalación

```bash
pip install -r requirements.txt
python app.py
```

Abrir navegador en: `http://localhost:5001`

### Errores comunes evitados

- **NIF duplicado:** Try-except con IntegrityError
- **Campos vacíos:** Atributo required en HTML
- **Base de datos no creada:** CREATE TABLE IF NOT EXISTS
- **Cálculo incorrecto:** Validación de productos y cantidades antes de insertar

---

## 4. Conclusión breve (25%)

Este ejercicio me ha permitido crear un sistema de facturación completo que integra múltiples módulos de un ERP. He aplicado conceptos de bases de datos relacionales con claves foráneas, generación automática de documentos con numeración secuencial y visualización de datos agregados en un dashboard.

Lo más interesante ha sido implementar la lógica de creación de facturas con múltiples líneas de productos, donde el sistema calcula automáticamente los subtotales y el total general. La estructura con 4 tablas relacionadas permite mantener la integridad referencial y facilita las consultas con JOIN.

Este sistema se conecta con los contenidos de la unidad sobre instalación y configuración de sistemas ERP-CRM, ya que he implementado módulos típicos de un ERP (clientes, productos, facturación) con una configuración minimalista pero funcional. En una empresa real, este sistema podría ampliarse con más módulos como inventario, contabilidad o gestión de pagos para crear un ERP completo.

El código es escalable y podría mejorarse añadiendo impresión de facturas en PDF, envío por email, control de stock automático al facturar, gestión de proveedores o integración con pasarelas de pago.
