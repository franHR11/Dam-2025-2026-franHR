# Ejercicio de Final de Unidad 1: Sistema CRM de Gestión de Clientes

**Asignatura:** Sistemas de Gestión Empresarial  
**Unidad:** 001 - Identificación de sistemas ERP-CRM  
**Alumno:** Fran  
**Fecha:** 5 de noviembre de 2025

---

## 1. Introducción breve y contextualización (25%)

En este ejercicio he desarrollado un sistema CRM (Customer Relationship Management) para gestionar clientes de una empresa. Un CRM es una herramienta fundamental en cualquier sistema de gestión empresarial porque permite centralizar toda la información de los clientes en un solo lugar, facilitando el seguimiento de las relaciones comerciales y mejorando la atención al cliente.

He elegido crear una aplicación web porque es la forma más práctica de acceder al sistema desde cualquier dispositivo con navegador. El CRM que he desarrollado permite realizar las operaciones básicas que cualquier empresa necesita: agregar nuevos clientes, ver la lista completa, buscar clientes específicos, editar su información y eliminarlos cuando sea necesario.

Este tipo de sistema se usa en el contexto empresarial para llevar un registro organizado de todos los contactos comerciales, sus datos de contacto, la empresa para la que trabajan y cuándo fueron registrados en el sistema. Es especialmente útil para departamentos de ventas, atención al cliente y marketing.

---

## 2. Desarrollo detallado y preciso (25%)

### Arquitectura del sistema

He construido el CRM usando el patrón MVC (Modelo-Vista-Controlador) de forma simplificada:

- **Modelo:** Base de datos SQLite con una tabla `clientes` que almacena toda la información
- **Vista:** Plantillas HTML con Jinja2 para mostrar la interfaz de usuario
- **Controlador:** Flask con rutas que gestionan las peticiones HTTP

### Estructura de la base de datos

La tabla `clientes` tiene los siguientes campos:
- `id`: Identificador único autoincremental (clave primaria)
- `nombre`: Nombre del cliente (obligatorio)
- `apellidos`: Apellidos del cliente (obligatorio)
- `email`: Correo electrónico único (obligatorio y sin duplicados)
- `telefono`: Número de teléfono (opcional)
- `empresa`: Nombre de la empresa del cliente (opcional)
- `fecha_registro`: Fecha y hora de registro automática

### Funcionalidades implementadas

1. **Listar clientes:** Muestra todos los clientes en una tabla ordenada por ID descendente
2. **Agregar cliente:** Formulario para registrar nuevos clientes con validación de email único
3. **Editar cliente:** Permite modificar los datos de un cliente existente
4. **Eliminar cliente:** Borra un cliente con confirmación previa
5. **Buscar cliente:** Sistema de búsqueda que filtra por nombre, apellidos, email o empresa

### Tecnologías utilizadas

- **Flask 3.0.0:** Framework web ligero de Python
- **SQLite:** Base de datos embebida, sin necesidad de servidor
- **Jinja2:** Motor de plantillas incluido en Flask
- **HTML5 y CSS3:** Para la interfaz de usuario
- **Sistema de mensajes flash:** Para notificar al usuario sobre las acciones realizadas

### Flujo de funcionamiento

1. Al iniciar la aplicación, se ejecuta `inicializar_bd()` que crea la base de datos y la tabla si no existen
2. La ruta principal `/` consulta todos los clientes y los muestra en `index.html`
3. Cada formulario envía datos mediante POST a su ruta correspondiente
4. Las rutas procesan los datos, ejecutan las consultas SQL y redirigen con mensajes flash
5. El sistema maneja errores como emails duplicados con bloques try-except

---

## 3. Aplicación práctica con ejemplo claro (25%)

### Código completo de la aplicación

**Archivo: app.py**

```python
# CRM de Clientes - Sistema de Gestión Empresarial
# Aplicación web con Flask y SQLite para gestionar clientes

from flask import Flask, render_template, request, redirect, url_for, flash
import sqlite3
from datetime import datetime

app = Flask(__name__)
app.secret_key = 'clave_secreta_crm_2025'

# Creo la base de datos y la tabla de clientes si no existe
def inicializar_bd():
    conexion = sqlite3.connect('crm_clientes.db')
    cursor = conexion.cursor()
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS clientes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            apellidos TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            telefono TEXT,
            empresa TEXT,
            fecha_registro TEXT NOT NULL
        )
    ''')
    conexion.commit()
    conexion.close()

# Ruta principal - Lista todos los clientes
@app.route('/')
def index():
    conexion = sqlite3.connect('crm_clientes.db')
    cursor = conexion.cursor()
    cursor.execute('SELECT * FROM clientes ORDER BY id DESC')
    clientes = cursor.fetchall()
    conexion.close()
    return render_template('index.html', clientes=clientes)

# Ruta para añadir un nuevo cliente
@app.route('/agregar', methods=['GET', 'POST'])
def agregar_cliente():
    if request.method == 'POST':
        nombre = request.form['nombre']
        apellidos = request.form['apellidos']
        email = request.form['email']
        telefono = request.form['telefono']
        empresa = request.form['empresa']
        fecha_registro = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        
        try:
            conexion = sqlite3.connect('crm_clientes.db')
            cursor = conexion.cursor()
            cursor.execute('''
                INSERT INTO clientes (nombre, apellidos, email, telefono, empresa, fecha_registro)
                VALUES (?, ?, ?, ?, ?, ?)
            ''', (nombre, apellidos, email, telefono, empresa, fecha_registro))
            conexion.commit()
            conexion.close()
            flash('Cliente agregado correctamente', 'success')
            return redirect(url_for('index'))
        except sqlite3.IntegrityError:
            flash('Error: El email ya existe en el sistema', 'error')
            return redirect(url_for('agregar_cliente'))
    
    return render_template('agregar.html')

# Ruta para editar un cliente existente
@app.route('/editar/<int:id>', methods=['GET', 'POST'])
def editar_cliente(id):
    conexion = sqlite3.connect('crm_clientes.db')
    cursor = conexion.cursor()
    
    if request.method == 'POST':
        nombre = request.form['nombre']
        apellidos = request.form['apellidos']
        email = request.form['email']
        telefono = request.form['telefono']
        empresa = request.form['empresa']
        
        cursor.execute('''
            UPDATE clientes 
            SET nombre=?, apellidos=?, email=?, telefono=?, empresa=?
            WHERE id=?
        ''', (nombre, apellidos, email, telefono, empresa, id))
        conexion.commit()
        conexion.close()
        flash('Cliente actualizado correctamente', 'success')
        return redirect(url_for('index'))
    
    cursor.execute('SELECT * FROM clientes WHERE id=?', (id,))
    cliente = cursor.fetchone()
    conexion.close()
    return render_template('editar.html', cliente=cliente)

# Ruta para eliminar un cliente
@app.route('/eliminar/<int:id>')
def eliminar_cliente(id):
    conexion = sqlite3.connect('crm_clientes.db')
    cursor = conexion.cursor()
    cursor.execute('DELETE FROM clientes WHERE id=?', (id,))
    conexion.commit()
    conexion.close()
    flash('Cliente eliminado correctamente', 'success')
    return redirect(url_for('index'))

# Ruta para buscar clientes
@app.route('/buscar', methods=['GET'])
def buscar_cliente():
    termino = request.args.get('q', '')
    conexion = sqlite3.connect('crm_clientes.db')
    cursor = conexion.cursor()
    cursor.execute('''
        SELECT * FROM clientes 
        WHERE nombre LIKE ? OR apellidos LIKE ? OR email LIKE ? OR empresa LIKE ?
        ORDER BY id DESC
    ''', (f'%{termino}%', f'%{termino}%', f'%{termino}%', f'%{termino}%'))
    clientes = cursor.fetchall()
    conexion.close()
    return render_template('index.html', clientes=clientes, termino=termino)

if __name__ == '__main__':
    inicializar_bd()
    app.run(debug=True, port=5000)
```

**Archivo: requirements.txt**

```
Flask==3.0.0
```

**Archivo: templates/base.html**

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}CRM Clientes{% endblock %}</title>
    <link rel="stylesheet" href="{{ url_for('static', filename='css/style.css') }}">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1>🏢 CRM de Clientes</h1>
            <div class="nav-links">
                <a href="{{ url_for('index') }}">Inicio</a>
                <a href="{{ url_for('agregar_cliente') }}" class="btn-primary">+ Nuevo Cliente</a>
            </div>
        </div>
    </nav>

    <main class="container">
        {% with messages = get_flashed_messages(with_categories=true) %}
            {% if messages %}
                {% for category, message in messages %}
                    <div class="alert alert-{{ category }}">{{ message }}</div>
                {% endfor %}
            {% endif %}
        {% endwith %}

        {% block content %}{% endblock %}
    </main>

    <footer>
        <p>Sistema de Gestión Empresarial - CRM 2025</p>
    </footer>
</body>
</html>
```

**Archivo: templates/index.html**

```html
{% extends "base.html" %}

{% block title %}Lista de Clientes - CRM{% endblock %}

{% block content %}
<div class="search-box">
    <form action="{{ url_for('buscar_cliente') }}" method="GET">
        <input type="text" name="q" placeholder="Buscar cliente..." value="{{ termino if termino else '' }}">
        <button type="submit">🔍 Buscar</button>
    </form>
</div>

<h2>📋 Lista de Clientes</h2>

{% if clientes %}
<table class="tabla-clientes">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellidos</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Empresa</th>
            <th>Fecha Registro</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        {% for cliente in clientes %}
        <tr>
            <td>{{ cliente[0] }}</td>
            <td>{{ cliente[1] }}</td>
            <td>{{ cliente[2] }}</td>
            <td>{{ cliente[3] }}</td>
            <td>{{ cliente[4] if cliente[4] else '-' }}</td>
            <td>{{ cliente[5] if cliente[5] else '-' }}</td>
            <td>{{ cliente[6] }}</td>
            <td class="acciones">
                <a href="{{ url_for('editar_cliente', id=cliente[0]) }}" class="btn-editar">✏️ Editar</a>
                <a href="{{ url_for('eliminar_cliente', id=cliente[0]) }}" 
                   class="btn-eliminar" 
                   onclick="return confirm('¿Estás seguro de eliminar este cliente?')">🗑️ Eliminar</a>
            </td>
        </tr>
        {% endfor %}
    </tbody>
</table>
{% else %}
<div class="mensaje-vacio">
    <p>No hay clientes registrados{% if termino %} que coincidan con "{{ termino }}"{% endif %}.</p>
    <a href="{{ url_for('agregar_cliente') }}" class="btn-primary">Agregar primer cliente</a>
</div>
{% endif %}
{% endblock %}
```

**Archivo: templates/agregar.html**

```html
{% extends "base.html" %}

{% block title %}Agregar Cliente - CRM{% endblock %}

{% block content %}
<h2>➕ Agregar Nuevo Cliente</h2>

<form method="POST" class="formulario">
    <div class="form-group">
        <label for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" required>
    </div>

    <div class="form-group">
        <label for="apellidos">Apellidos *</label>
        <input type="text" id="apellidos" name="apellidos" required>
    </div>

    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" required>
    </div>

    <div class="form-group">
        <label for="telefono">Teléfono</label>
        <input type="tel" id="telefono" name="telefono">
    </div>

    <div class="form-group">
        <label for="empresa">Empresa</label>
        <input type="text" id="empresa" name="empresa">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary">💾 Guardar Cliente</button>
        <a href="{{ url_for('index') }}" class="btn-secondary">❌ Cancelar</a>
    </div>
</form>
{% endblock %}
```

**Archivo: templates/editar.html**

```html
{% extends "base.html" %}

{% block title %}Editar Cliente - CRM{% endblock %}

{% block content %}
<h2>✏️ Editar Cliente</h2>

<form method="POST" class="formulario">
    <div class="form-group">
        <label for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" value="{{ cliente[1] }}" required>
    </div>

    <div class="form-group">
        <label for="apellidos">Apellidos *</label>
        <input type="text" id="apellidos" name="apellidos" value="{{ cliente[2] }}" required>
    </div>

    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" value="{{ cliente[3] }}" required>
    </div>

    <div class="form-group">
        <label for="telefono">Teléfono</label>
        <input type="tel" id="telefono" name="telefono" value="{{ cliente[4] if cliente[4] else '' }}">
    </div>

    <div class="form-group">
        <label for="empresa">Empresa</label>
        <input type="text" id="empresa" name="empresa" value="{{ cliente[5] if cliente[5] else '' }}">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary">💾 Actualizar Cliente</button>
        <a href="{{ url_for('index') }}" class="btn-secondary">❌ Cancelar</a>
    </div>
</form>
{% endblock %}
```

**Archivo: static/css/style.css**

```css
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding-bottom: 60px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.navbar {
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 15px 0;
    margin-bottom: 30px;
}

.navbar .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar h1 {
    color: #667eea;
    font-size: 24px;
}

.nav-links {
    display: flex;
    gap: 15px;
}

.nav-links a {
    text-decoration: none;
    color: #333;
    padding: 8px 15px;
    border-radius: 5px;
    transition: all 0.3s;
}

.nav-links a:hover {
    background: #f0f0f0;
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.search-box {
    background: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.search-box form {
    display: flex;
    gap: 10px;
}

.search-box input {
    flex: 1;
    padding: 12px;
    border: 2px solid #e0e0e0;
    border-radius: 5px;
    font-size: 16px;
}

.search-box button {
    padding: 12px 25px;
    background: #667eea;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.3s;
}

.search-box button:hover {
    background: #5568d3;
}

h2 {
    color: white;
    margin-bottom: 20px;
    font-size: 28px;
}

.tabla-clientes {
    width: 100%;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.tabla-clientes thead {
    background: #667eea;
    color: white;
}

.tabla-clientes th,
.tabla-clientes td {
    padding: 15px;
    text-align: left;
}

.tabla-clientes tbody tr:nth-child(even) {
    background: #f8f9fa;
}

.tabla-clientes tbody tr:hover {
    background: #e9ecef;
}

.acciones {
    display: flex;
    gap: 10px;
}

.btn-editar,
.btn-eliminar {
    padding: 6px 12px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s;
}

.btn-editar {
    background: #ffc107;
    color: #000;
}

.btn-editar:hover {
    background: #e0a800;
}

.btn-eliminar {
    background: #dc3545;
    color: white;
}

.btn-eliminar:hover {
    background: #c82333;
}

.mensaje-vacio {
    background: white;
    padding: 40px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.mensaje-vacio p {
    font-size: 18px;
    color: #666;
    margin-bottom: 20px;
}

.formulario {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    max-width: 600px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #333;
    font-weight: 500;
}

.form-group input {
    width: 100%;
    padding: 12px;
    border: 2px solid #e0e0e0;
    border-radius: 5px;
    font-size: 16px;
    transition: border 0.3s;
}

.form-group input:focus {
    outline: none;
    border-color: #667eea;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn-primary {
    padding: 12px 30px;
    background: #667eea;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s;
}

.btn-primary:hover {
    background: #5568d3;
}

.btn-secondary {
    padding: 12px 30px;
    background: #6c757d;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s;
}

.btn-secondary:hover {
    background: #5a6268;
}

footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    background: rgba(255, 255, 255, 0.95);
    text-align: center;
    padding: 15px;
    color: #666;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
}
```

### Instrucciones de instalación y ejecución

1. Instalar las dependencias:
```bash
pip install -r requirements.txt
```

2. Ejecutar la aplicación:
```bash
python app.py
```

3. Abrir el navegador en: `http://localhost:5000`

### Errores comunes y cómo evitarlos

**Error 1: Email duplicado**
- **Problema:** Intentar agregar un cliente con un email que ya existe
- **Solución:** He implementado un try-except que captura `sqlite3.IntegrityError` y muestra un mensaje de error al usuario

**Error 2: Campos vacíos**
- **Problema:** Enviar el formulario sin completar los campos obligatorios
- **Solución:** He usado el atributo `required` en HTML para validación del lado del cliente

**Error 3: Base de datos no creada**
- **Problema:** Intentar acceder a la base de datos antes de crearla
- **Solución:** La función `inicializar_bd()` se ejecuta automáticamente al iniciar la aplicación con `CREATE TABLE IF NOT EXISTS`

**Error 4: Eliminar sin confirmación**
- **Problema:** Borrar clientes accidentalmente
- **Solución:** He añadido un `onclick="return confirm()"` en JavaScript para pedir confirmación antes de eliminar

---

## 4. Conclusión breve (25%)

Este ejercicio me ha permitido crear un sistema CRM funcional y completo que cumple con los requisitos básicos de un sistema de gestión empresarial. He aplicado conceptos fundamentales de la unidad como la identificación de las necesidades de un CRM, la estructura de datos necesaria para gestionar clientes y la implementación de operaciones CRUD (Crear, Leer, Actualizar, Eliminar).

Lo más interesante ha sido ver cómo un sistema aparentemente complejo como un CRM puede desarrollarse de forma minimalista pero efectiva usando tecnologías modernas como Flask y SQLite. La base de datos embebida SQLite es perfecta para este tipo de aplicaciones porque no requiere configuración de servidor y el archivo `.db` contiene todos los datos de forma portátil.

Este CRM se conecta directamente con otros contenidos de la unidad como los módulos de un ERP (este sería el módulo de gestión de clientes), la importancia de centralizar la información empresarial y cómo los sistemas CRM mejoran la relación con los clientes al tener todos sus datos organizados y accesibles. En una empresa real, este sistema podría integrarse con otros módulos como ventas, facturación o marketing para crear un ERP completo.

El código es escalable y podría ampliarse fácilmente añadiendo más funcionalidades como historial de interacciones con clientes, gestión de oportunidades de venta, seguimiento de tareas o integración con email para enviar comunicaciones masivas.
