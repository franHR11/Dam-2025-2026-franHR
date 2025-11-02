# Conectar con Base de Datos en Python

## 🧩 1. Encabezado informativo
Este ejercicio trata sobre cómo conectar con una base de datos MySQL usando Python y crear una tabla de clientes.

## 🧠 2. Explicación personal del ejercicio
En este ejercicio tuve que aprender a conectar con una base de datos MySQL desde Python. Usé el módulo mysql.connector para establecer la conexión, crear un cursor y ejecutar una consulta SQL para crear una tabla llamada 'clientes' con campos como identificador, nombre, apellidos y email. Me pareció interesante porque me permite gestionar datos de manera programática, como si estuviera organizando mis capturas de pesca en un sistema.

## 💻 3. Código de programación
```python
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
```


## 🧾 4. Cierre
Me ha parecido un ejercicio básico pero fundamental para entender cómo trabajar con bases de datos en Python. Ahora puedo empezar a insertar y gestionar datos, lo que es útil para aplicaciones reales.
