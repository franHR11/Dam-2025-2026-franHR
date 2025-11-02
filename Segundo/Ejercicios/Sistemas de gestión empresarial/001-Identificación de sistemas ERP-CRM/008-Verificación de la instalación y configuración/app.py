import sqlite3
import xml.etree.ElementTree as ET
from flask import Flask, request, render_template_string

app = Flask(__name__)

def miInterfaz(destino):
    tree = ET.parse(destino)
    root = tree.getroot()
    html = '<form method="POST">'
    for field in root:
        name = field.attrib['name']
        tipo = field.attrib['type']
        label = field.attrib['label']
        html += f'<label>{label}: <input type="{tipo}" name="{name}" required></label><br>'
    html += '<input type="submit" value="Guardar"></form>'
    return html

def crear_tabla():
    conn = sqlite3.connect('clientes.db')
    c = conn.cursor()
    c.execute('''CREATE TABLE IF NOT EXISTS interfaz (
                    id INTEGER PRIMARY KEY,
                    nombre TEXT,
                    email TEXT,
                    telefono TEXT
                )''')
    conn.commit()
    conn.close()

@app.route('/', methods=['GET', 'POST'])
def index():
    if request.method == 'POST':
        nombre = request.form['nombre']
        email = request.form['email']
        telefono = request.form['telefono']
        conn = sqlite3.connect('clientes.db')
        c = conn.cursor()
        c.execute("INSERT INTO interfaz (nombre, email, telefono) VALUES (?, ?, ?)", (nombre, email, telefono))
        conn.commit()
        conn.close()
        return "Datos guardados correctamente."
    html = miInterfaz('interfaz.xml')
    return render_template_string(html)

if __name__ == '__main__':
    crear_tabla()
    app.run(debug=True)
