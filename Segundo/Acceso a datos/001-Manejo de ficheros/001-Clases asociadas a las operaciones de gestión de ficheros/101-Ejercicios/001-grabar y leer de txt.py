# Primero escribimos un archivo

archivo = open("clientes.txt",'w')
archivo.write("Esto es un texto de prueba")
archivo.close()

# Ahora vamos a leer archivos

archivo = open("clientes.txt",'r')
lineas = archivo.readlines()
for linea in lineas:
    print(linea)

# Ahora apendizamos en archivos

archivo = open("clientes.txt",'a')
archivo.write("  Esto es otro texto de prueba")
archivo.close()

# Ahora vamos a leer archivos

archivo = open("clientes.txt",'r')
lineas = archivo.readlines()
for linea in lineas:
    print(linea)
