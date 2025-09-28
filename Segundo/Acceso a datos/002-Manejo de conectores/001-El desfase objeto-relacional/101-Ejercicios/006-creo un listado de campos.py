import mysql.connector

clientes = [{
    "nombre": "Antonio",
    "apellidos": "García Martínez",
    "dni": "12345678A",
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
    "dni": "87654321B",
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

campos = []

for clave in muestra.keys():
    print(clave)
    print(type(muestra[clave]))
    if type(muestra[clave]) == str:
        print("Lo voy a convertir en una columna de SQL que sera de tipo varchar")
        campos.append(clave)
    elif type(muestra[clave]) == list:
        print("Lo voy a convertir en una tabla externa de SQL")
    elif type(muestra[clave]) == int:
        print("Lo voy a convertir en una columna de SQL que sera de tipo int ")
    else:
        print("No me han programado para ese tipo de campo")

conn = mysql.connector.connect(
    host="localhost",
    user="desfase",
    password="desfase",
    database="desfase"
)

cadena = '''
CREATE TABLE `desfase`.`clientes` (
    `Identificador` INT NOT NULL AUTO_INCREMENT,'''

for campo in campos:
    cadena += '''
    `'''+campo+'''` VARCHAR(255) NOT NULL,'''

cadena += '''
    PRIMARY KEY (`Identificador`)
) ENGINE = InnoDB;
'''

print(cadena)
#cursor = conn.cursor()
#cursor.execute("DROP TABLE clientes;")







