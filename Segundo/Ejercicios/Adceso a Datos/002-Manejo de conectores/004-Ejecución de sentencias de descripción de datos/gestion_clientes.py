import mysql.connector

# Me conecto a la base de datos usando las credenciales dadas
conexion = mysql.connector.connect(
    host="localhost",
    user="accesoadatos2526",
    password="Accesoadatos2526$",
    database="accesoadatos2526"
)

# Creo un cursor que devuelve los resultados como diccionarios para facilitar el acceso
cursor = conexion.cursor(dictionary=True)

# Selecciono los campos nombre, apellidos y correo con alias descriptivos
consulta = "SELECT Nombre AS nombre_cliente, Apellidos AS apellidos_cliente, Correo_electronico AS email FROM clientes"

# Ejecuto la consulta
cursor.execute(consulta)

# Obtengo todos los resultados
resultados = cursor.fetchall()

# Recorro cada fila y la imprimo de forma legible
for fila in resultados:
    print(f"Nombre: {fila['nombre_cliente']}, Apellidos: {fila['apellidos_cliente']}, Correo: {fila['email']}")

# Cierro el cursor y la conexión
cursor.close()
conexion.close()
