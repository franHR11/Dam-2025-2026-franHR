import json

# Vamos a abrir el archivo de base de datos que contiene información en formato JSON
archivo = open('basededatos.dat', 'r')

# Leemos la primera línea del archivo, que debería ser una cadena JSON
linea = archivo.readline().strip()

# Mostramos la línea que leímos y qué tipo de dato es (debería ser una cadena de texto)
print("La línea leída del archivo es:", linea)
print("Y su tipo de dato es:", type(linea))

# Cerramos el archivo porque ya no lo necesitamos
archivo.close()

# Ahora, convertimos esa cadena JSON en un objeto Python usando json.loads()
devuelta = json.loads(linea)

# Mostramos el objeto resultante y su tipo (debería ser un diccionario en Python)
print("Después de deserializar, el objeto es:", devuelta)
print("Y su tipo de dato es:", type(devuelta))