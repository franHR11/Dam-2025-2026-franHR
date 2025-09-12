import json

personas = [
    {
        "nombre": "Ana",
        "apellido": "González",
        "edad": 25,
        "ciudad": "Zaragoza",
        "profesion": "Diseñadora"
    },
    {
        "nombre": "Pedro",
        "apellido": "Ramírez",
        "edad": 35,
        "ciudad": "Málaga",
        "profesion": "Abogado"
    },
    {
        "nombre": "Sofía",
        "apellido": "Hernández",
        "edad": 29,
        "ciudad": "Granada",
        "profesion": "Enfermera"
    },
    {
        "nombre": "Miguel",
        "apellido": "Torres",
        "edad": 42,
        "ciudad": "Córdoba",
        "profesion": "Chef"
    },
    {
        "nombre": "Elena",
        "apellido": "Díaz",
        "edad": 31,
        "ciudad": "Valladolid",
        "profesion": "Periodista"
    }
]

print(personas)
print(type(personas))
cadena = json.dumps(personas)
print(cadena)
print(type(cadena))

archivo = open("basededatos.dat",'w')
archivo.write(cadena)
archivo.close()




    
