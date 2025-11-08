import mysql.connector
from mysql.connector import Error

class DataAccessLayer:
    def __init__(self, host, user, password, database):
        self.host = host
        self.user = user
        self.password = password
        self.database = database
        self.connection = None

    def conectar(self):
        # Establezco la conexión a la base de datos
        try:
            self.connection = mysql.connector.connect(
                host=self.host,
                user=self.user,
                password=self.password,
                database=self.database
            )
            print("Conexión exitosa a la base de datos.")
        except Error as e:
            print(f"Error al conectar: {e}")
            self.connection = None

    def desconectar(self):
        # Cierro la conexión si existe
        if self.connection and self.connection.is_connected():
            self.connection.close()
            print("Conexión cerrada.")

    def ejecutar_consulta(self, query, params=None):
        # Ejecuto consultas SELECT y devuelvo resultados
        if not self.connection:
            print("No hay conexión activa.")
            return None
        try:
            cursor = self.connection.cursor()
            cursor.execute(query, params or ())
            resultados = cursor.fetchall()
            cursor.close()
            return resultados
        except Error as e:
            print(f"Error en consulta: {e}")
            return None

    def ejecutar_modificacion(self, query, params=None):
        # Ejecuto INSERT, UPDATE, DELETE
        if not self.connection:
            print("No hay conexión activa.")
            return False
        try:
            cursor = self.connection.cursor()
            cursor.execute(query, params or ())
            self.connection.commit()
            cursor.close()
            print("Modificación ejecutada correctamente.")
            return True
        except Error as e:
            print(f"Error en modificación: {e}")
            self.connection.rollback()
            return False

    def iniciar_transaccion(self):
        # Inicio una transacción
        if self.connection:
            self.connection.start_transaction()

    def confirmar_transaccion(self):
        # Confirmo la transacción
        if self.connection:
            self.connection.commit()

    def deshacer_transaccion(self):
        # Deshago la transacción
        if self.connection:
            self.connection.rollback()

# Ejemplo de uso: conecto, inserto un producto, consulto y desconecto
if __name__ == "__main__":
    # Creo la instancia del componente con mis credenciales
    dal = DataAccessLayer("localhost", "accesoadatos2526", "Accesoadatos2526$", "accesoadatos2526")
    dal.conectar()
    
    # Inserto un producto de ejemplo (suponiendo tabla productos con id, nombre, precio)
    dal.ejecutar_modificacion("INSERT INTO productos (nombre, precio) VALUES (%s, %s)", ("Caña de pescar", 50.0))
    
    # Consulto todos los productos
    productos = dal.ejecutar_consulta("SELECT * FROM productos")
    if productos:
        for prod in productos:
            print(prod)
    
    dal.desconectar()
