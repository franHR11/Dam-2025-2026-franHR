from flask import Flask
from JVDB import JVDB

app = Flask(__name__)

# Crear instancia de JVDB con las credenciales de MySQL
jvdb = JVDB('localhost', 'accesoadatos2526', 'Accesoadatos2526$', 'accesoadatos2526')

@app.route('/registros/<tabla>')
def mostrar_registros(tabla):
    # Llamar al método seleccionar y devolver el JSON
    return jvdb.seleccionar(tabla)

if __name__ == '__main__':
    # Ejecutar la app en modo debug
    app.run(debug=True)
