# Explicación del Ejercicio

## Introducción y contextualización
En mi tiempo libre, me encanta programar y también disfruto mucho de la pesca. Estoy desarrollando un programa para gestionar una base de datos de clientes mientras estoy pescando en el río. Necesito almacenar información sobre los clientes en archivos secuenciales y hash table para facilitar la búsqueda y acceso a esta información de manera eficiente.

## Desarrollo técnico correcto y preciso
La implementación sigue las estructuras y métodos vistos en clase, sin usar librerías externas. Utiliza únicamente módulos estándar de Python como os y json. Los archivos secuenciales se crean uno por uno, y el hash table se construye leyendo todos los archivos y organizándolos por ID.

## Aplicación práctica con ejemplo claro
Aquí se muestra cómo se crean los archivos secuenciales y se accede a ellos, así como la creación y acceso al hash table.

```python
import os
import json

# Primero, creamos el directorio para los archivos secuenciales si no existe
os.makedirs("secuenciales", exist_ok=True)

# Ahora, vamos a crear 100 archivos JSON, cada uno con datos de un cliente ficticio
for i in range(100):
    # Datos simples para cada cliente
    datos_cliente = {
        "id": i,
        "nombre": f"Cliente {i}",
        "informacion": "texto del cliente"
    }
    # Escribimos el archivo
    with open(f"secuenciales/cliente{i}.json", "w") as archivo:
        json.dump(datos_cliente, archivo)

# Función para leer un cliente específico de manera dinámica
def leer_cliente(id_cliente):
    # Construimos el nombre del archivo dinámicamente
    nombre_archivo = f"secuenciales/cliente{id_cliente}.json"
    # Leemos y devolvemos los datos
    with open(nombre_archivo, "r") as archivo:
        return json.load(archivo)

# Ejemplo: leemos el cliente 56
cliente_56 = leer_cliente(56)
print("Contenido del cliente 56:", cliente_56)

# Ahora, creamos el directorio para el hash table
os.makedirs("hash", exist_ok=True)

# Leemos todos los archivos secuenciales y los ponemos en un diccionario hash
hash_table = {}
for i in range(100):
    with open(f"secuenciales/cliente{i}.json", "r") as archivo:
        datos = json.load(archivo)
        # Usamos el id como clave
        hash_table[str(datos["id"])] = datos

# Guardamos el hash table en un archivo JSON
with open("hash/hash_table.json", "w") as archivo:
    json.dump(hash_table, archivo)

# Función para acceder al hash table y obtener un cliente específico
def acceder_cliente_hash(id_cliente):
    # Leemos el archivo hash
    with open("hash/hash_table.json", "r") as archivo:
        datos_hash = json.load(archivo)
    # Devolvemos el cliente si existe
    return datos_hash.get(str(id_cliente), "Cliente no encontrado")

# Ejemplo: accedemos al cliente 56
cliente_56_hash = acceder_cliente_hash(56)
print("Cliente 56 desde hash table:", cliente_56_hash)
```

## Cierre/Conclusión enlazando con la unidad
Estos conceptos de archivos secuenciales y hash table pueden ser aplicados en un proyecto real, como el programa para gestionar clientes que mencioné al inicio. Los archivos secuenciales permiten un acceso ordenado y secuencial a los datos, mientras que el hash table proporciona búsquedas rápidas por clave, lo que es ideal para bases de datos simples sin necesidad de bases de datos relacionales.