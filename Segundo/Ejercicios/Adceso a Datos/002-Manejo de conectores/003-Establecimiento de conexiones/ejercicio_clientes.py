import mysql.connector

# Me conecto a la base de datos usando los datos proporcionados
conexion = mysql.connector.connect(
    host="localhost",
    user="accesoadatos2526",
    password="Accesoadatos2526$",
    database="accesoadatos2526"
)

# Creo un cursor para ejecutar las consultas
cursor = conexion.cursor()

# Creo la tabla clientes con las columnas especificadas si no existe
consulta_crear = '''
CREATE TABLE IF NOT EXISTS clientes (
    Identificador INT,
    Nombre VARCHAR(50),
    Apellidos VARCHAR(100),
    Correo_electronico VARCHAR(50)
)
'''
cursor.execute(consulta_crear)

# Añado la clave primaria a la columna Identificador
consulta_pk = "ALTER TABLE clientes ADD PRIMARY KEY (Identificador)"
cursor.execute(consulta_pk)

# Modifico la columna Identificador para que sea autoincremental y no nulo
consulta_modificar = "ALTER TABLE clientes MODIFY Identificador INT NOT NULL AUTO_INCREMENT"
cursor.execute(consulta_modificar)

# Inserto el cliente de prueba
consulta_insertar = '''
INSERT INTO clientes (Identificador, Nombre, Apellidos, Correo_electronico) VALUES (1, 'Jose Vicente', 'Carratala Sanchis', 'info@jocarsa.com')
'''
cursor.execute(consulta_insertar)

# Confirmo los cambios
conexion.commit()

# Verifico el resultado seleccionando todos los registros
cursor.execute("SELECT * FROM clientes")
resultados = cursor.fetchall()

# Muestro los resultados
for fila in resultados:
    print(fila)

# Cierro el cursor y la conexión
cursor.close()
conexion.close()
