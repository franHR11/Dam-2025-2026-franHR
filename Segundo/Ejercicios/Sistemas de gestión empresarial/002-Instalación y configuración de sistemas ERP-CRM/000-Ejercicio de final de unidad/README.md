# 💼 Sistema de Facturación Empresarial

Sistema completo de facturación desarrollado con Flask y SQLite que integra gestión de clientes, productos y generación de facturas.

## 📋 Características

- ✅ Dashboard con estadísticas en tiempo real
- ✅ Gestión completa de clientes con datos fiscales (NIF/CIF)
- ✅ Catálogo de productos con precios y stock
- ✅ Generación automática de facturas con numeración secuencial
- ✅ Facturas con múltiples líneas de productos
- ✅ Cálculo automático de subtotales y totales
- ✅ Vista detallada de facturas
- ✅ Sistema de estados (Pendiente, Pagada, Cancelada)
- ✅ Base de datos relacional con 4 tablas

## 🚀 Instalación y Ejecución

### Requisitos previos
- Python 3.7 o superior
- pip

### Pasos para ejecutar

1. **Instalar dependencias:**
```bash
pip install -r requirements.txt
```

2. **Ejecutar la aplicación:**
```bash
python app.py
```

3. **Abrir en el navegador:**
```
http://localhost:5001
```

## 📁 Estructura del proyecto

```
000-Ejercicio de final de unidad/
│
├── app.py                      # Aplicación principal Flask
├── requirements.txt            # Dependencias
├── explicacion_ejercicio.md    # Documentación completa
├── README.md                   # Este archivo
│
├── templates/                  # Plantillas HTML
│   ├── base.html              # Plantilla base
│   ├── index.html             # Dashboard
│   ├── clientes.html          # Lista de clientes
│   ├── agregar_cliente.html   # Formulario cliente
│   ├── productos.html         # Lista de productos
│   ├── agregar_producto.html  # Formulario producto
│   ├── facturas.html          # Lista de facturas
│   ├── crear_factura.html     # Formulario factura
│   └── ver_factura.html       # Detalle factura
│
├── static/css/
│   └── style.css              # Estilos CSS
│
└── facturacion.db             # Base de datos (se crea automáticamente)
```

## 🗄️ Estructura de la base de datos

**Tabla: clientes**
- id, nombre, nif (único), direccion, telefono, email

**Tabla: productos**
- id, nombre, descripcion, precio, stock

**Tabla: facturas**
- id, numero_factura (único), cliente_id (FK), fecha, total, estado

**Tabla: lineas_factura**
- id, factura_id (FK), producto_id (FK), cantidad, precio_unitario, subtotal

## 🎯 Funcionalidades principales

### 1. Dashboard
- Estadísticas: total clientes, productos, facturas e ingresos
- Últimas 5 facturas emitidas
- Tarjetas visuales con iconos

### 2. Gestión de Clientes
- Listar todos los clientes
- Agregar clientes con NIF único
- Datos fiscales completos

### 3. Gestión de Productos
- Listar productos con precio y stock
- Agregar nuevos productos
- Control de inventario

### 4. Gestión de Facturas
- Crear facturas con múltiples productos
- Numeración automática (FAC-2025-0001)
- Cálculo automático de totales
- Ver detalle completo
- Sistema de estados

## 🛠️ Tecnologías utilizadas

- **Flask 3.0.0** - Framework web
- **SQLite** - Base de datos
- **Jinja2** - Motor de plantillas
- **HTML5 & CSS3** - Interfaz
- **JavaScript** - Funcionalidad dinámica

## 📝 Diferencias con el CRM de la Unidad 1

- Sistema completo vs solo gestión de clientes
- 4 tablas relacionadas vs 1 tabla simple
- Generación de documentos fiscales
- Dashboard con estadísticas agregadas
- Puerto 5001 vs 5000

## 👨‍💻 Autor

Fran - Estudiante de DAM  
Asignatura: Sistemas de Gestión Empresarial  
Unidad 2: Instalación y configuración de sistemas ERP-CRM
