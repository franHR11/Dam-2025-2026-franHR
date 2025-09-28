import mysql.connector

clientes = [{
    "nombre": "Antonio",
    "apellidos": "García Martínez",
    "dni": "12345678A",
    "edad": 35,
    "emails": [
        {
            "tipo": "personal",
            "direccion": "antonio.garcia@email.com"
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
    "edad": 28,
    "emails": [
        {
            "tipo": "personal",
            "direccion": "maria.lopez@email.com"
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
        campos.append(clave)
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
    if isinstance(campo, str):
        cadena += '''
    `'''+campo+'''` VARCHAR(255) NOT NULL,'''
    elif isinstance(campo, int):
        cadena += '''
    `'''+campo+'''` INT NOT NULL,'''

cadena += '''
    PRIMARY KEY (`Identificador`)
) ENGINE = InnoDB;
'''

print(cadena)
cursor = conn.cursor()
cursor.execute("DROP TABLE clientes;")
cursor.execute(cadena)







