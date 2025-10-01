# Bueno, vamos a empezar con este ejercicio de pesca. Imagina que tengo un diario de peces que pesco.

# Primero, abro un archivo llamado peces.txt para escribir, como si empezara mi diario.
archivo = open('peces.txt', 'w')
# Escribo el primer pez que pesqué, un Pececín Rojo.
archivo.write('Pececín Rojo\n')
# Cierro el archivo, porque no quiero dejarlo abierto.
archivo.close()

# Ahora, quiero ver qué tengo escrito, así que abro el archivo para leer.
archivo = open('peces.txt', 'r')
# Leo todas las líneas y las guardo en una lista llamada lineas.
lineas = archivo.readlines()
# Cierro el archivo otra vez.
archivo.close()
# Imprimo cada línea, para ver mis peces.
for linea in lineas:
    print(linea.strip())  # Quito el salto de línea al imprimir.

# Después de un rato, pesco otro pez, un Pececín Azul, y quiero añadirlo al diario sin borrar lo anterior.
archivo = open('peces.txt', 'a')
# Añado la nueva línea al final.
archivo.write('Pececín Azul\n')
# Cierro el archivo.
archivo.close()

# Finalmente, leo todo de nuevo para ver la lista completa.
archivo = open('peces.txt', 'r')
lineas = archivo.readlines()
archivo.close()
# Imprimo todas las líneas otra vez.
for linea in lineas:
    print(linea.strip())