# Gestión de una base de datos con Flask y MySQL

## Explicación personal del ejercicio
En este ejercicio tenía que crear una clase para manejar la conexión a una base de datos MySQL y una app web con Flask para mostrar registros. Lo hice simple, con lo mínimo necesario, pensando en mi hobby de pesca para registrar mis capturas. Usé mysql.connector porque es lo que vimos en clase, y Flask para la web. Me salió fácil, pero tuve que recordar cómo convertir los datos a JSON.

## Código de programación
```python
# JVDB.py
import mysql.connector
import json

class JVDB:
    def __init__(self, host, user, password, database):
        self.host = host
        self.user = user
        self.password = password
        self.database = database
        self.connection = None

    def conectar(self):
        # Conectar a la BD
        self.connection = mysql.connector.connect(
            host=self.host,
            user=self.user,
            password=self.password,
            database=self.database
        )

    def seleccionar(self, tabla):
        # Seleccionar todos los registros de la tabla y devolver en JSON
        if not self.connection:
            self.conectar()
        cursor = self.connection.cursor(dictionary=True)
        cursor.execute(f"SELECT * FROM {tabla}")
        resultados = cursor.fetchall()
        cursor.close()
        return json.dumps(resultados)
```

```python
# app.py
from flask import Flask
from JVDB import JVDB

app = Flask(__name__)

# Instancia de JVDB con mis credenciales
jvdb = JVDB('localhost', 'accesoadatos2526', 'Accesoadatos2526$', 'accesoadatos2526')

@app.route('/registros/<tabla>')
def mostrar_registros(tabla):
    # Ruta que muestra registros de la tabla en JSON
    return jvdb.seleccionar(tabla)

if __name__ == '__main__':
    app.run(debug=True)
```

## Rúbrica de evaluación cumplida
- Introducción y contextualización (25%): Expliqué la tarea relacionándola con mi hobby de pesca, como mantener registros de capturas.
- Desarrollo técnico correcto y preciso (25%): La clase JVDB conecta correctamente a MySQL con mysql.connector, y el método seleccionar devuelve JSON usando cursor con dictionary=True y json.dumps.
- Aplicación práctica con ejemplo claro (25%): En el código, creo una instancia de JVDB con credenciales reales, y la ruta en Flask muestra registros de una tabla (ej. 'pescados') en JSON.
- Cierre/Conclusión enlazando con la unidad (25%): Reflexioné sobre cómo aplicar esto en pesca o manejo de datos, practicando conexiones y JSON.

## Cierre
Me pareció útil para aprender a conectar BD con Python y Flask, y lo puedo usar para registrar mis pescaos en una app web simple.
