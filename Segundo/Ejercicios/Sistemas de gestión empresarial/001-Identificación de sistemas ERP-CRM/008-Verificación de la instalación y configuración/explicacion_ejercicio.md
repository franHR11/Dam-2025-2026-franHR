# Explicación del Ejercicio: Sistema de Gestión de Clientes con Interfaces XML y Flask

## Introducción y contextualización

En este ejercicio tenía que crear un sistema simple para gestionar datos de clientes en una empresa. Usé interfaces gráficas basadas en XML para definir los campos de entrada y Flask para hacer la web. Esto facilita la entrada de datos porque el XML permite cambiar la interfaz sin tocar mucho código, y es útil en sistemas ERP donde se manejan muchos datos de clientes.

## Desarrollo técnico correcto y preciso

La función `miInterfaz` lee el archivo `interfaz.xml` usando `xml.etree.ElementTree` y genera HTML con campos de formulario. La tabla en SQLite se crea con los campos nombre, email y telefono usando `sqlite3`. El servidor Flask muestra la interfaz en GET y guarda los datos en POST.

```python
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
```

## Aplicación práctica con ejemplo claro

Para usar el sistema, ejecuta `app.py` con Python. Abre el navegador en localhost:5000, llena el formulario con datos como nombre "Juan Pérez", email "juan@example.com", telefono "123456789", y envía. Los datos se guardan en `clientes.db`. Evita errores comunes como no cerrar la conexión a la DB o no validar inputs, aunque aquí es simple.

## Cierre/Conclusión enlazando con la unidad

Este proyecto se puede usar en una empresa para gestionar clientes fácilmente, mejorando la eficiencia al automatizar la entrada de datos con XML. Relaciona con sistemas ERP donde se integran interfaces para procesos empresariales.
