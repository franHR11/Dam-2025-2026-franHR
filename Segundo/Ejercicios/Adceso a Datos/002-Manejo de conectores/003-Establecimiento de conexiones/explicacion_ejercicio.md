# Creación de tabla clientes en base de datos

## Explicación personal del ejercicio
En este ejercicio decidí conectarme a la base de datos "accesoadatos2526" y crear una tabla llamada "clientes" para mi tienda de pesca. Primero creé la tabla con las columnas necesarias, luego añadí la clave primaria al identificador y lo modifiqué para que sea autoincremental. Después inserté un cliente de prueba y verifiqué que todo estuviera bien seleccionando los registros. Fue sencillo pero me ayudó a practicar los comandos SQL básicos.

## Código de programación
```python
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
```

## Rúbrica de evaluación cumplida
- La conexión a la base de datos es correcta: Uso mysql.connector para conectarme con host localhost, usuario accesoadatos2526, contraseña Accesoadatos2526$ y base de datos accesoadatos2526.
- La tabla "clientes" se ha creado correctamente con las columnas especificadas: Columnas Identificador (INT), Nombre (VARCHAR), Apellidos (VARCHAR), Correo_electronico (VARCHAR).
- La clave primaria y la modificación de la columna "Identificador" son correctas: Añado PRIMARY KEY con ALTER TABLE, luego modifico a INT NOT NULL AUTO_INCREMENT.
- El cliente se inserta correctamente en la tabla: Inserto los datos especificados con INSERT INTO.
- El resultado final es verificado correctamente: Ejecuto SELECT * FROM clientes y muestro los resultados con print.

## Cierre
Me ha parecido un ejercicio práctico para combinar programación y bases de datos, especialmente útil para mi proyecto de tienda de pesca. Refuerza conceptos básicos de SQL y conexiones en Python.
