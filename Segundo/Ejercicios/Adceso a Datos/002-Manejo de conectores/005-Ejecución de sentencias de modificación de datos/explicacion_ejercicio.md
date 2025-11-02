# Ejercicio: Actualización de registros en tabla de clientes

## Introducción breve y contextualización - 25%

En este ejercicio, aprendí a actualizar registros en una tabla de MySQL usando Python. Es útil para cambiar datos existentes en la base de datos sin tener que hacerlo manualmente.

## Desarrollo detallado y preciso - 25%

La actualización se hace con la sentencia SQL UPDATE, especificando la tabla, el campo a cambiar y la condición WHERE para identificar el registro. Usé mysql.connector para conectar y ejecutar la consulta, luego commit() para guardar los cambios.

## Aplicación práctica con Ejemplo Claro - 25%

Aquí está el código que hice para actualizar el nombre del cliente con id 3:

```
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
```

Un error común es olvidar el commit(), así que siempre lo incluyo.

## Conclusión breve - 25%

Resumiendo, actualicé un registro usando UPDATE y Python. Esto se relaciona con otras operaciones como INSERT o SELECT que vimos antes.

Me pareció útil para manejar datos dinámicos en aplicaciones.
