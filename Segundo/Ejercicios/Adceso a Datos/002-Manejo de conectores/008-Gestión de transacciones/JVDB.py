import mysql.connector
import json

class JVDB:
    def __init__(self, host, user, password, database):
        # Guardar los parámetros de conexión
        self.host = host
        self.user = user
        self.password = password
        self.database = database
        self.connection = None

    def conectar(self):
        # Crear la conexión a la base de datos MySQL
        self.connection = mysql.connector.connect(
            host=self.host,
            user=self.user,
            password=self.password,
            database=self.database
        )

    def seleccionar(self, tabla):
        # Si no hay conexión, conectarse primero
        if not self.connection:
            self.conectar()
        # Crear cursor que devuelve diccionarios
        cursor = self.connection.cursor(dictionary=True)
        # Ejecutar consulta SELECT en la tabla
        cursor.execute(f"SELECT * FROM {tabla}")
        # Obtener todos los resultados
        resultados = cursor.fetchall()
        # Cerrar cursor
        cursor.close()
        # Devolver los resultados en formato JSON
        return json.dumps(resultados)
