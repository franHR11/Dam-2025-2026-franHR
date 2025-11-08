import mysql.connector

# Me conecto a la base de datos con los datos que me dieron en clase
conexion = mysql.connector.connect(
    host="localhost",
    user="accesoadatos2526",
    password="Accesoadatos2526$",
    database="accesoadatos2526"
)

# Creo el cursor para ejecutar las consultas
cursor = conexion.cursor()

# Hago la actualización del nombre del cliente con id 3 a Juan Diego
update_query = "UPDATE clientes SET Nombre = 'Juan Diego' WHERE Identificador = 3"
cursor.execute(update_query)

# Confirmo los cambios en la base de datos
conexion.commit()

# Imprimo un mensaje para saber que se hizo
print("He actualizado el nombre del cliente correctamente.")

# Cierro el cursor y la conexión
cursor.close()
conexion.close()
