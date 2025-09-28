clientes = [{
    "nombre": "Antonio",
    "apellidos": "García Martínez",
    "emails": [
        {
            "tipo": "personal",
            "direcciones": [
                "antonio.garcia@email.com",
                "agarcia85@gmail.com"
            ]
        },
        {
            "tipo": "empresa",
            "direcciones": ["a.garcia@empresa.es"]
        }
    ]
}, {
    "nombre": "María",
    "apellidos": "López Sánchez",
    "emails": [
        {
            "tipo": "personal",
            "direcciones": [
                "maria.lopez@email.com",
                "mlopez92@gmail.com"
            ]
        },
        {
            "tipo": "empresa",
            "direcciones": ["m.lopez@empresa.es"]
        }
    ]
}]

muestra = clientes[0]
print(muestra)

for clave in muestra.keys():
    print(clave)
    print(type(muestra[clave]))
    if type(muestra[clave]) == str:
        print("Lo voy a convertir en una columna de SQL que sera de tipo varchar")
    elif type(muestra[clave]) == list:
        print("Lo voy a convertir en una tabla externa de SQL")
    elif type(muestra[clave]) == int:
        print("Lo voy a convertir en una columna de SQL que sera de tipo int ")
    else:
        print("No me han programado para ese tipo de campo")










