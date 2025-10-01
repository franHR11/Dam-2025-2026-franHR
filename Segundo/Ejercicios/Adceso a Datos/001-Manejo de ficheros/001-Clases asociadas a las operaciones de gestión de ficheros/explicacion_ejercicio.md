# Explicación del Ejercicio: Gestión de Contactos de Pescador

## Introducción y contextualización

Este ejercicio nos muestra cómo trabajar con archivos en Python, específicamente usando los formatos JSON y CSV para manejar datos relacionados con la pesca. Imagina que eres un pescador que quiere llevar un registro de las capturas que hace cada día - el tipo de pez, su tamaño y la fecha en que lo pescaste. Este tipo de gestión de datos es fundamental en aplicaciones reales donde necesitamos almacenar información de manera estructurada y poder recuperarla después.

El propósito principal es aprender a usar las funciones básicas de Python para leer y escribir archivos, tanto en formato JSON (que es ideal para datos estructurados como objetos) como CSV (perfecto para datos tabulares que se pueden abrir en Excel). Esto es especialmente útil en el contexto de acceso a datos, donde necesitamos persistir información y poder consultarla más tarde.

## Desarrollo técnico correcto y preciso

Vamos a revisar el código paso a paso para entender exactamente cómo funciona cada parte. El archivo completo se ve así:

```python
import json
import csv

# 1. Crear lista de diccionarios para JSON
contactos_json = [
    {"nombre": "Salmón", "tamanio": 75, "fecha": "2023-06-15"},
    {"nombre": "Trucha", "tamanio": 32, "fecha": "2023-07-22"},
    {"nombre": "Lucio", "tamanio": 95, "fecha": "2023-08-05"}
]

# 2. Escribir archivo JSON
with open('contactos_pescados.json', 'w') as f:
    json.dump(contactos_json, f)

# 3. Leer archivo JSON
with open('contactos_pescados.json', 'r') as f:
    datos_leidos_json = json.load(f)

# 4. Crear lista de listas para CSV
contactos_csv = [
    ['Salmón', 75, '2023-06-15'],
    ['Trucha', 32, '2023-07-22'],
    ['Lucio', 95, '2023-08-05']
]

# 5. Escribir archivo CSV
with open('contactos_pescados.csv', 'w', newline='') as f:
    writer = csv.writer(f)
    writer.writerows(contactos_csv)

# 6. Leer archivo CSV
with open('contactos_pescados.csv', 'r') as f:
    reader = csv.reader(f)
    datos_leidos_csv = list(reader)
```

Primero, importamos las librerías necesarias: `json` para trabajar con archivos JSON y `csv` para los archivos CSV. Estas vienen incluidas en Python, así que no necesitamos instalar nada adicional.

Luego creamos una lista de diccionarios llamada `contactos_json`. Cada diccionario representa una captura de pescado con tres propiedades: nombre (el tipo de pez), tamanio (en centímetros) y fecha (cuando se pescó).

Para escribir el archivo JSON, usamos la función `json.dump()`. Esta función toma dos parámetros principales: los datos que queremos guardar (nuestra lista de contactos) y el archivo donde escribir (que abrimos en modo escritura 'w'). El `with` es una buena práctica porque cierra automáticamente el archivo cuando terminamos.

Para leer el archivo JSON, usamos `json.load()`. Esta función lee el contenido del archivo y lo convierte de vuelta a objetos Python que podemos usar en nuestro programa.

Para el CSV, creamos una lista de listas donde cada lista interna representa una fila. Luego usamos `csv.writer()` para crear un objeto escritor y `writer.writerows()` para escribir todas las filas de una vez.

Para leer el CSV, usamos `csv.reader()` que nos devuelve un iterador que podemos convertir a lista con `list()`.

## Aplicación práctica con ejemplo claro

Ahora vamos a ver ejemplos específicos de cómo se utilizan estas funciones en situaciones reales. Imagina que eres un pescador profesional que necesita llevar un registro detallado de sus capturas.

**Ejemplo con json.dump():**
```python
# Guardar una nueva captura en el archivo JSON
nueva_captura = {"nombre": "Carpa", "tamanio": 45, "fecha": "2023-09-10"}

with open('contactos_pescados.json', 'r') as f:
    capturas_actuales = json.load(f)

capturas_actuales.append(nueva_captura)

with open('contactos_pescados.json', 'w') as f:
    json.dump(capturas_actuales, f)
```

Aquí vemos cómo `json.dump()` nos permite guardar datos estructurados. Es perfecto para aplicaciones donde necesitamos mantener la estructura de objetos.

**Ejemplo con json.load():**
```python
# Leer y mostrar todas las capturas
with open('contactos_pescados.json', 'r') as f:
    mis_capturas = json.load(f)

for captura in mis_capturas:
    print(f"Pescado: {captura['nombre']}, Tamaño: {captura['tamanio']}cm, Fecha: {captura['fecha']}")
```

`json.load()` recupera los datos y los convierte en objetos Python que podemos manipular fácilmente.

**Ejemplo con csv.writer():**
```python
# Crear un nuevo archivo CSV con encabezados
with open('registro_pesca.csv', 'w', newline='') as f:
    writer = csv.writer(f)
    writer.writerow(['Tipo de Pez', 'Tamaño (cm)', 'Fecha de Captura'])  # Encabezados
    writer.writerow(['Salmón', 75, '2023-06-15'])
    writer.writerow(['Trucha', 32, '2023-07-22'])
```

`csv.writer()` es ideal para crear archivos que luego se pueden abrir en Excel o procesar con otras herramientas de análisis de datos.

**Ejemplo con csv.reader():**
```python
# Leer y procesar datos del CSV
with open('contactos_pescados.csv', 'r') as f:
    reader = csv.reader(f)
    total_peces = 0
    tamaño_total = 0
    
    for fila in reader:
        if fila:  # Evitar filas vacías
            total_peces += 1
            tamaño_total += int(fila[1])  # El tamaño está en la posición 1
    
    promedio = tamaño_total / total_peces if total_peces > 0 else 0
    print(f"Promedio de tamaño: {promedio}cm")
```

`csv.reader()` nos permite procesar los datos fila por fila, perfecto para análisis estadísticos.

## Cierre/Conclusión enlazando con la unidad

Este ejercicio nos demuestra cómo las operaciones básicas de gestión de ficheros son fundamentales en cualquier aplicación que maneje datos persistentes. En el contexto de una aplicación real de gestión de datos para pesca, podríamos estar hablando de una app móvil donde los pescadores registran sus capturas diarias, o un sistema web para una empresa pesquera que necesita llevar inventario.

En proyectos más amplios, estas técnicas se aplican constantemente. Por ejemplo:
- **Sistemas de inventario**: Guardar productos en JSON para estructuras complejas o CSV para reportes simples
- **Aplicaciones de registro**: Desde apps de fitness hasta diarios personales
- **Sistemas empresariales**: Exportar reportes en CSV para análisis en Excel, o usar JSON para APIs

Este ejercicio sienta las bases para entender cómo funcionan las operaciones de entrada/salida en Python, que es un conocimiento esencial para cualquier desarrollador que trabaje con datos reales. Nos ayuda a comprender no solo la sintaxis, sino también las mejores prácticas como usar `with` para manejo seguro de archivos y elegir el formato adecuado según nuestras necesidades.

En resumen, dominar estas funciones básicas de JSON y CSV nos abre las puertas a un mundo de posibilidades en el manejo de datos, desde aplicaciones simples hasta sistemas empresariales complejos donde la persistencia de información es clave para el éxito del proyecto.
