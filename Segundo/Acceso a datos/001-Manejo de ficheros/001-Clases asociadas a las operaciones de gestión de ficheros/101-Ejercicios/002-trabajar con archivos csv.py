import csv

# Primero escribo datos

datos = [
    ['nombre','apellidos','telefono'],
    ['Pedro','Gomez','612345678'],
    ['Maria','Rodriguez','623456789'],
    ['Ana','Fernandez','634567890'],
    ['Carlos','Lopez','645678901'],
]

archivo = open("datos.csv",'w')
escritor = csv.writer(archivo)
escritor.writerows(datos)
archivo.close()

# Ahora leo los datos

archivo = open("datos.csv",'r')
lector = csv.reader(archivo)
for linea in lector:
    print(linea)
