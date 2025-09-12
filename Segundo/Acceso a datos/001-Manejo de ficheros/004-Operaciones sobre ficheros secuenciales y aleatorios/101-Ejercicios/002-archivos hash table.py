import os
import hashlib
import json

try:
    os.mkdir("hash")
except:
    pass

# Lista de personas ficticias
personas = [
    {
        "nombre": "Francisco",
        "apellido": "Hernández",
        "edad": 35,
        "ciudad": "Granada",
        "profesion": "Programador"
    },
    {
        "nombre": "Isabel",
        "apellido": "González",
        "edad": 29,
        "ciudad": "Málaga",
        "profesion": "Diseñadora"
    },
    {
        "nombre": "Miguel",
        "apellido": "Ramírez",
        "edad": 42,
        "ciudad": "Zaragoza",
        "profesion": "Médico"
    },
    {
        "nombre": "Carmen",
        "apellido": "Torres",
        "edad": 26,
        "ciudad": "Córdoba",
        "profesion": "Enfermera"
    },
    {
        "nombre": "Antonio",
        "apellido": "Navarro",
        "edad": 31,
        "ciudad": "Valladolid",
        "profesion": "Ingeniero"
    }
]

for persona in personas:
    cadena = persona['nombre']+persona['apellido']+str(persona['edad'])
    picadillo = hashlib.md5(cadena.encode()).hexdigest()
    print(picadillo)
    archivo = open("hash/"+picadillo+".json",'w')
    json.dump(persona,archivo,indent=4)
    archivo.close()

# Ahora busco entre esos hashes


cadena = "Antonio"+"Navarro"+"31"
picadillo = hashlib.md5(cadena.encode()).hexdigest()
archivo = open("hash/"+picadillo+".json",'r')
contenido = json.load(archivo)
print(contenido)

    
