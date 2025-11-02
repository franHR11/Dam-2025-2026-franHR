# Este es mi programa para guardar clientes de pesca con pickle
# Importo pickle para poder guardar objetos en archivos binarios
import pickle

# Aquí creo la clase Cliente como me pedían, con nombre, apellidos y emails
class Cliente:
    def __init__(self, nombre, apellidos, emails):
        self.nombre = nombre
        self.apellidos = apellidos
        self.emails = emails

# Creo una lista vacía donde pondré mis clientes
clientes = []

# Uso un bucle para hacer 10 clientes ficticios, porque no tengo reales ahora
for i in range(1, 11):
    nombre = f"Cliente{i}"
    apellidos = f"Apellido{i}"
    emails = f"cliente{i}@email.com"
    cliente = Cliente(nombre, apellidos, emails)
    clientes.append(cliente)

# Ahora guardo la lista en un archivo binario llamado clientes.bin
with open('clientes.bin', 'wb') as f:
    pickle.dump(clientes, f)

# Luego cargo la lista desde el archivo para ver si se guardó bien
with open('clientes.bin', 'rb') as f:
    clientes_cargados = pickle.load(f)

# Imprimo todos los clientes para comprobar que están ahí
for cliente in clientes_cargados:
    print(f"Nombre: {cliente.nombre}, Apellidos: {cliente.apellidos}, Email: {cliente.emails}")
