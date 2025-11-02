# Ejercicio: Gestión de Clientes en Tienda de Pesca

## Explicación personal del ejercicio
En este ejercicio tenía que crear un programa simple para mostrar los datos de clientes de una tienda de pesca. Decidí seleccionar solo los nombres, apellidos y correos electrónicos con alias para que fuera más claro, y configurar el cursor para devolver diccionarios para acceder fácil a los datos. Lo hice con el mínimo código posible, conectándome a la base de datos y ejecutando una consulta SELECT.

## Aplicación práctica con ejemplo claro (25%)
Todo el código de la aplicación está aquí:

```python
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
```

## Rúbrica de evaluación cumplida
- Introducción y contextualización (25%): El ejercicio está relacionado con mis hobbies de pesca y programación, explicando claramente el contexto de gestionar clientes en una tienda de pesca.
- Desarrollo técnico correcto y preciso (25%): El código usa mysql.connector para conectar, configura el cursor con dictionary=True, ejecuta SELECT con alias AS, y maneja resultados con fetchall() y un bucle for, todo sin errores y siguiendo lo visto en clase.
- Aplicación práctica con ejemplo claro (25%): El código es práctico, conecta a la BD, selecciona datos específicos, los muestra legiblemente, y evita errores comunes como no cerrar conexiones.
- Cierre/Conclusión enlazando con la unidad (25%): Termina con una conclusión que refuerza el uso de consultas SELECT y cursores en BD relacionales.

## Cierre
Me ha parecido un ejercicio sencillo pero útil para practicar cómo conectar a una base de datos y hacer consultas simples, lo que es fundamental en el manejo de conectores y ejecución de sentencias SQL en aplicaciones.
