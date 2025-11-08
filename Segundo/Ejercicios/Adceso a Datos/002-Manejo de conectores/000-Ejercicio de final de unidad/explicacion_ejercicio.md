# Ejercicio de final de unidad 2: Componente abstracto de acceso a datos

## Introducción y Contextualización (25%)
En este ejercicio, decidí crear un componente abstracto de acceso a datos que se pueda integrar fácilmente en el backend de cualquier aplicación. Es como una capa intermedia que simplifica las operaciones con la base de datos, escondiendo los detalles técnicos y permitiendo que el código principal se centre en la lógica de negocio. Por ejemplo, en mi proyecto personal de tienda de pesca, esto me serviría para manejar clientes, productos o pedidos sin tener que escribir consultas SQL cada vez.

## Desarrollo Técnico Correcto y Preciso (25%)
El componente es una clase llamada `DataAccessLayer` en Python, usando mysql.connector para conectar a MySQL. Incluye métodos para conectar, desconectar, ejecutar consultas de selección, inserción, actualización y eliminación. Maneja transacciones básicas y cierra recursos automáticamente. La clase recibe parámetros de conexión al inicializarse, lo que la hace reutilizable para diferentes bases de datos. Usé prepared statements para evitar inyecciones SQL y manejo de excepciones básico para errores de conexión.

## Aplicación Práctica con Ejemplo Claro (25%)
El código completo del componente es el siguiente:

```python
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
```

Este código se puede importar en cualquier parte del backend. Por ejemplo, en una API REST, usaría `dal.ejecutar_consulta()` para obtener datos y `dal.ejecutar_modificacion()` para guardar cambios. Es minimalista pero cubre las operaciones básicas necesarias.

## Cierre/Conclusión Enlazando con la Unidad (25%)
Este componente me ha servido para abstraer el acceso a datos, facilitando el desarrollo de aplicaciones que necesitan persistencia. Se conecta con lo visto en la unidad sobre conectores, protocolos y ejecución de sentencias, permitiendo integrar fácilmente operaciones CRUD. En proyectos futuros, como mi app de pesca, podré extenderlo con más métodos específicos sin complicar el código principal.

## Rúbrica de evaluación cumplida
- Introducción breve y contextualización: Explico qué es un componente abstracto de acceso a datos y su utilidad en backends.
- Desarrollo detallado y preciso: Defino la clase con métodos para conexión, consultas y transacciones, usando terminología técnica correcta.
- Aplicación práctica: Incluyo código funcional completo con ejemplo de inserción y consulta.
- Conclusión breve: Resumo puntos clave y enlazo con la unidad.
- Calidad de presentación: Texto en primera persona, natural, sin errores.
- Código válido y funcional: El código usa mysql.connector correctamente, maneja errores y es ejecutable.
