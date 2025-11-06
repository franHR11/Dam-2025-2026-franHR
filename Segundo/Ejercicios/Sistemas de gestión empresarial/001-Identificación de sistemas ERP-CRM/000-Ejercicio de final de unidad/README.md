# 🏢 CRM de Clientes - Sistema de Gestión Empresarial

Sistema CRM (Customer Relationship Management) desarrollado con Flask y SQLite para gestionar clientes de forma eficiente.

## 📋 Características

- ✅ Agregar nuevos clientes
- ✅ Listar todos los clientes
- ✅ Buscar clientes por nombre, apellidos, email o empresa
- ✅ Editar información de clientes existentes
- ✅ Eliminar clientes con confirmación
- ✅ Validación de emails únicos
- ✅ Interfaz web moderna y responsive
- ✅ Base de datos SQLite embebida

## 🚀 Instalación y Ejecución

### Requisitos previos
- Python 3.7 o superior
- pip (gestor de paquetes de Python)

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
http://localhost:5000
```

## 📁 Estructura del proyecto

```
000-Ejercicio de final de unidad/
│
├── app.py                      # Aplicación principal Flask
├── requirements.txt            # Dependencias del proyecto
├── explicacion_ejercicio.md    # Documentación completa del ejercicio
├── README.md                   # Este archivo
│
├── templates/                  # Plantillas HTML
│   ├── base.html              # Plantilla base
│   ├── index.html             # Lista de clientes
│   ├── agregar.html           # Formulario agregar cliente
│   └── editar.html            # Formulario editar cliente
│
├── static/                     # Archivos estáticos
│   └── css/
│       └── style.css          # Estilos CSS
│
└── crm_clientes.db            # Base de datos (se crea automáticamente)
```

## 🗄️ Estructura de la base de datos

**Tabla: clientes**

| Campo           | Tipo    | Descripción                    |
|----------------|---------|--------------------------------|
| id             | INTEGER | Identificador único (PK)       |
| nombre         | TEXT    | Nombre del cliente             |
| apellidos      | TEXT    | Apellidos del cliente          |
| email          | TEXT    | Email único del cliente        |
| telefono       | TEXT    | Teléfono (opcional)            |
| empresa        | TEXT    | Empresa del cliente (opcional) |
| fecha_registro | TEXT    | Fecha y hora de registro       |

## 🎯 Funcionalidades principales

### 1. Listar clientes
- Muestra todos los clientes en una tabla ordenada
- Visualización clara de todos los datos
- Botones de acción para editar y eliminar

### 2. Agregar cliente
- Formulario con validación de campos obligatorios
- Validación de email único
- Registro automático de fecha y hora

### 3. Buscar cliente
- Búsqueda en tiempo real
- Filtra por nombre, apellidos, email o empresa
- Resultados instantáneos

### 4. Editar cliente
- Modificación de datos existentes
- Formulario precargado con información actual
- Actualización inmediata

### 5. Eliminar cliente
- Confirmación antes de eliminar
- Eliminación permanente de la base de datos
- Mensaje de confirmación

## 🛠️ Tecnologías utilizadas

- **Flask 3.0.0** - Framework web de Python
- **SQLite** - Base de datos embebida
- **Jinja2** - Motor de plantillas
- **HTML5 & CSS3** - Interfaz de usuario
- **JavaScript** - Confirmaciones y validaciones

## 📝 Notas importantes

- La base de datos se crea automáticamente al ejecutar la aplicación por primera vez
- Los emails deben ser únicos en el sistema
- Los campos nombre, apellidos y email son obligatorios
- La aplicación se ejecuta en modo debug en el puerto 5000

## 👨‍💻 Autor

Fran - Estudiante de DAM  
Asignatura: Sistemas de Gestión Empresarial  
Unidad 1: Identificación de sistemas ERP-CRM
