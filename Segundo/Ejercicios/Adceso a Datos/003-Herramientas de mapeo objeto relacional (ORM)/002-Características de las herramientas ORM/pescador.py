import mysql.connector

class Pescador:
    def __init__(self):
        # Defino las credenciales de conexión
        self.host = "localhost"
        self.user = "pesca_user"
        self.password = "pescador123"
        self.database = "peces_capturados"
        self.conexion = None

    def conectar(self):
        # Intento conectarme a la base de datos controlando errores
        try:
            self.conexion = mysql.connector.connect(
                host=self.host,
                user=self.user,
                password=self.password,
                database=self.database
            )
            print("Conexión exitosa a la base de datos.")
        except mysql.connector.Error as err:
            print(f"Error al conectar: {err}")

    def listar_peces(self):
        # Compruebo si hay conexión antes de consultar
        if self.conexion and self.conexion.is_connected():
            cursor = self.conexion.cursor(dictionary=True)
            # Ordeno por nombre alfabéticamente
            sql = "SELECT * FROM peces ORDER BY nombre ASC"
            cursor.execute(sql)
            resultados = cursor.fetchall()
            
            print("\n--- Lista de Peces Capturados ---")
            for pez in resultados:
                print(pez)
            
            cursor.close()
            return resultados
        else:
            print("No hay conexión activa.")
            return []

    def buscar_pez(self, nombre_parcial):
        if self.conexion and self.conexion.is_connected():
            cursor = self.conexion.cursor(dictionary=True)
            # Uso parámetros para evitar inyección SQL (%s)
            sql = "SELECT * FROM peces WHERE nombre LIKE %s"
            patron = f"%{nombre_parcial}%"
            cursor.execute(sql, (patron,))
            resultados = cursor.fetchall()
            
            print(f"\n--- Resultados de búsqueda para '{nombre_parcial}' ---")
            for pez in resultados:
                print(pez)
                
            cursor.close()
            return resultados
        else:
            print("No hay conexión activa.")
            return []

    def cerrar(self):
        if self.conexion and self.conexion.is_connected():
            self.conexion.close()
            print("\nConexión cerrada.")

# --- Bloque de ejecución principal ---
if __name__ == "__main__":
    mi_pescador = Pescador()
    mi_pescador.conectar()
    
    # Listar todos los peces
    mi_pescador.listar_peces()
    
    # Buscar peces que contengan "trucha"
    mi_pescador.buscar_pez("trucha")
    
    mi_pescador.cerrar()
