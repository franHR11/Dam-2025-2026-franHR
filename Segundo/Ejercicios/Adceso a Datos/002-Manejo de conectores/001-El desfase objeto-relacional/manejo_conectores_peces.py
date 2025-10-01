# Importo la biblioteca mysql.connector para poder conectar con la base de datos MySQL
import mysql.connector

# Defino una clase Pez para representar la información de cada pez capturado
# Esto me ayuda a organizar los datos de manera estructurada
class Pez:
    def __init__(self, nombre_cientifico, peso, medida, fecha_captura):
        self.nombre_cientifico = nombre_cientifico  # El nombre científico del pez
        self.peso = peso  # El peso en kilogramos
        self.medida = medida  # La medida en centímetros
        self.fecha_captura = fecha_captura  # La fecha cuando lo capturé

# Función para conectar a la base de datos
# Aquí configuro la conexión usando mis credenciales
def conectar_bd():
    return mysql.connector.connect(
        host="localhost",  # El servidor donde está mi base de datos
        user="root",  # Mi usuario de MySQL
        password="",  # Mi contraseña (vacía en este caso)
        database="pesca"  # La base de datos que uso para almacenar los peces
    )

# Función para insertar un pez en la base de datos
# Esta función toma un objeto Pez y lo guarda en la tabla 'peces'
def insertar_pez(pez):
    conexion = conectar_bd()  # Me conecto a la base de datos
    cursor = conexion.cursor()  # Creo un cursor para ejecutar consultas

    # Preparo la consulta SQL para insertar los datos
    # Uso placeholders (?) para evitar problemas de seguridad
    consulta = "INSERT INTO peces (nombre_cientifico, peso, medida, fecha_captura) VALUES (%s, %s, %s, %s)"
    valores = (pez.nombre_cientifico, pez.peso, pez.medida, pez.fecha_captura)

    cursor.execute(consulta, valores)  # Ejecuto la consulta con los valores
    conexion.commit()  # Confirmo los cambios en la base de datos

    cursor.close()  # Cierro el cursor
    conexion.close()  # Cierro la conexión

    print("He insertado el pez en la base de datos exitosamente.")  # Mensaje de confirmación

# Ejemplo de uso: creo un pez y lo inserto
# Esto es para probar que todo funciona
if __name__ == "__main__":
    # Creo un objeto Pez con datos de ejemplo
    mi_pez = Pez("Salmo trutta", 2.5, 45.0, "2023-08-15")
    insertar_pez(mi_pez)  # Lo inserto en la base de datos