import mysql.connector

# Establece la conexión con la base de datos
conexion = mysql.connector.connect(
    host="localhost",
    user="accesoadatos2526",
    password="Accesoadatos2526$",
    database="accesoadatos2526"
)

# Crea un cursor para ejecutar consultas SQL
cursor = conexion.cursor()

# Escribe la consulta SQL para crear la tabla 'clientes'
consulta = '''
CREATE TABLE `clientes` (
  `Identificador` INT NOT NULL , 
  `nombre` VARCHAR(50) NOT NULL , 
  `apellidos` VARCHAR(100) NOT NULL , 
  `email` VARCHAR(50) NOT NULL  
) ENGINE = InnoDB;
'''

# Ejecuta la consulta
cursor.execute(consulta)

# Confirma los cambios en la base de datos
conexion.commit()

# Cierra el cursor y la conexión
cursor.close()
conexion.close()
