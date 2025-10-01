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