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
