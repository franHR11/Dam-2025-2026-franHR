# Sistema de Facturación Empresarial
# Aplicación web con Flask y SQLite para gestionar facturas, clientes y productos

from flask import Flask, render_template, request, redirect, url_for, flash
import sqlite3
from datetime import datetime

app = Flask(__name__)
app.secret_key = 'facturacion_empresarial_2025'

# Inicializo la base de datos con las tres tablas necesarias
def inicializar_bd():
    conexion = sqlite3.connect('facturacion.db')
    cursor = conexion.cursor()
    
    # Tabla de clientes
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS clientes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            nif TEXT NOT NULL UNIQUE,
            direccion TEXT,
            telefono TEXT,
            email TEXT
        )
    ''')
    
    # Tabla de productos
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS productos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            descripcion TEXT,
            precio REAL NOT NULL,
            stock INTEGER DEFAULT 0
        )
    ''')
    
    # Tabla de facturas
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS facturas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            numero_factura TEXT NOT NULL UNIQUE,
            cliente_id INTEGER NOT NULL,
            fecha TEXT NOT NULL,
            total REAL NOT NULL,
            estado TEXT DEFAULT 'Pendiente',
            FOREIGN KEY (cliente_id) REFERENCES clientes(id)
        )
    ''')
    
    # Tabla de líneas de factura (productos en cada factura)
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS lineas_factura (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            factura_id INTEGER NOT NULL,
            producto_id INTEGER NOT NULL,
            cantidad INTEGER NOT NULL,
            precio_unitario REAL NOT NULL,
            subtotal REAL NOT NULL,
            FOREIGN KEY (factura_id) REFERENCES facturas(id),
            FOREIGN KEY (producto_id) REFERENCES productos(id)
        )
    ''')
    
    conexion.commit()
    conexion.close()

# Ruta principal - Dashboard con resumen
@app.route('/')
def index():
    conexion = sqlite3.connect('facturacion.db')
    cursor = conexion.cursor()
    
    # Obtengo estadísticas para el dashboard
    cursor.execute('SELECT COUNT(*) FROM clientes')
    total_clientes = cursor.fetchone()[0]
    
    cursor.execute('SELECT COUNT(*) FROM productos')
    total_productos = cursor.fetchone()[0]
    
    cursor.execute('SELECT COUNT(*) FROM facturas')
    total_facturas = cursor.fetchone()[0]
    
    cursor.execute('SELECT SUM(total) FROM facturas')
    ingresos_totales = cursor.fetchone()[0] or 0
    
    # Últimas facturas
    cursor.execute('''
        SELECT f.id, f.numero_factura, c.nombre, f.fecha, f.total, f.estado
        FROM facturas f
        JOIN clientes c ON f.cliente_id = c.id
        ORDER BY f.id DESC LIMIT 5
    ''')
    ultimas_facturas = cursor.fetchall()
    
    conexion.close()
    return render_template('index.html', 
                         total_clientes=total_clientes,
                         total_productos=total_productos,
                         total_facturas=total_facturas,
                         ingresos_totales=ingresos_totales,
                         ultimas_facturas=ultimas_facturas)

# GESTIÓN DE CLIENTES
@app.route('/clientes')
def listar_clientes():
    conexion = sqlite3.connect('facturacion.db')
    cursor = conexion.cursor()
    cursor.execute('SELECT * FROM clientes ORDER BY id DESC')
    clientes = cursor.fetchall()
    conexion.close()
    return render_template('clientes.html', clientes=clientes)

@app.route('/clientes/agregar', methods=['GET', 'POST'])
def agregar_cliente():
    if request.method == 'POST':
        nombre = request.form['nombre']
        nif = request.form['nif']
        direccion = request.form['direccion']
        telefono = request.form['telefono']
        email = request.form['email']
        
        try:
            conexion = sqlite3.connect('facturacion.db')
            cursor = conexion.cursor()
            cursor.execute('INSERT INTO clientes (nombre, nif, direccion, telefono, email) VALUES (?, ?, ?, ?, ?)',
                         (nombre, nif, direccion, telefono, email))
            conexion.commit()
            conexion.close()
            flash('Cliente agregado correctamente', 'success')
            return redirect(url_for('listar_clientes'))
        except sqlite3.IntegrityError:
            flash('Error: El NIF ya existe', 'error')
    
    return render_template('agregar_cliente.html')

# GESTIÓN DE PRODUCTOS
@app.route('/productos')
def listar_productos():
    conexion = sqlite3.connect('facturacion.db')
    cursor = conexion.cursor()
    cursor.execute('SELECT * FROM productos ORDER BY id DESC')
    productos = cursor.fetchall()
    conexion.close()
    return render_template('productos.html', productos=productos)

@app.route('/productos/agregar', methods=['GET', 'POST'])
def agregar_producto():
    if request.method == 'POST':
        nombre = request.form['nombre']
        descripcion = request.form['descripcion']
        precio = float(request.form['precio'])
        stock = int(request.form['stock'])
        
        conexion = sqlite3.connect('facturacion.db')
        cursor = conexion.cursor()
        cursor.execute('INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)',
                     (nombre, descripcion, precio, stock))
        conexion.commit()
        conexion.close()
        flash('Producto agregado correctamente', 'success')
        return redirect(url_for('listar_productos'))
    
    return render_template('agregar_producto.html')

# GESTIÓN DE FACTURAS
@app.route('/facturas')
def listar_facturas():
    conexion = sqlite3.connect('facturacion.db')
    cursor = conexion.cursor()
    cursor.execute('''
        SELECT f.id, f.numero_factura, c.nombre, f.fecha, f.total, f.estado
        FROM facturas f
        JOIN clientes c ON f.cliente_id = c.id
        ORDER BY f.id DESC
    ''')
    facturas = cursor.fetchall()
    conexion.close()
    return render_template('facturas.html', facturas=facturas)

@app.route('/facturas/crear', methods=['GET', 'POST'])
def crear_factura():
    conexion = sqlite3.connect('facturacion.db')
    cursor = conexion.cursor()
    
    if request.method == 'POST':
        cliente_id = request.form['cliente_id']
        productos_ids = request.form.getlist('producto_id[]')
        cantidades = request.form.getlist('cantidad[]')
        
        # Genero número de factura automático
        cursor.execute('SELECT COUNT(*) FROM facturas')
        num_facturas = cursor.fetchone()[0]
        numero_factura = f'FAC-{datetime.now().year}-{num_facturas + 1:04d}'
        fecha = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        
        # Calculo el total
        total = 0
        lineas = []
        for prod_id, cantidad in zip(productos_ids, cantidades):
            if prod_id and cantidad:
                cursor.execute('SELECT precio FROM productos WHERE id=?', (prod_id,))
                precio = cursor.fetchone()[0]
                cantidad = int(cantidad)
                subtotal = precio * cantidad
                total += subtotal
                lineas.append((prod_id, cantidad, precio, subtotal))
        
        # Inserto la factura
        cursor.execute('INSERT INTO facturas (numero_factura, cliente_id, fecha, total) VALUES (?, ?, ?, ?)',
                     (numero_factura, cliente_id, fecha, total))
        factura_id = cursor.lastrowid
        
        # Inserto las líneas de factura
        for prod_id, cantidad, precio, subtotal in lineas:
            cursor.execute('INSERT INTO lineas_factura (factura_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)',
                         (factura_id, prod_id, cantidad, precio, subtotal))
        
        conexion.commit()
        conexion.close()
        flash(f'Factura {numero_factura} creada correctamente', 'success')
        return redirect(url_for('listar_facturas'))
    
    # Obtengo clientes y productos para el formulario
    cursor.execute('SELECT id, nombre FROM clientes ORDER BY nombre')
    clientes = cursor.fetchall()
    cursor.execute('SELECT id, nombre, precio FROM productos ORDER BY nombre')
    productos = cursor.fetchall()
    conexion.close()
    
    return render_template('crear_factura.html', clientes=clientes, productos=productos)

@app.route('/facturas/ver/<int:id>')
def ver_factura(id):
    conexion = sqlite3.connect('facturacion.db')
    cursor = conexion.cursor()
    
    # Datos de la factura
    cursor.execute('''
        SELECT f.*, c.nombre, c.nif, c.direccion, c.telefono, c.email
        FROM facturas f
        JOIN clientes c ON f.cliente_id = c.id
        WHERE f.id = ?
    ''', (id,))
    factura = cursor.fetchone()
    
    # Líneas de la factura
    cursor.execute('''
        SELECT p.nombre, l.cantidad, l.precio_unitario, l.subtotal
        FROM lineas_factura l
        JOIN productos p ON l.producto_id = p.id
        WHERE l.factura_id = ?
    ''', (id,))
    lineas = cursor.fetchall()
    
    conexion.close()
    return render_template('ver_factura.html', factura=factura, lineas=lineas)

if __name__ == '__main__':
    inicializar_bd()
    app.run(debug=True, port=5001)
