import pickle

class Contacto:
    def __init__(self,minombre,mitelefono):
        self.nombre = minombre
        self.telefono = mitelefono
    
    def __str__(self):
        return f"Nombre: {self.nombre}, Teléfono: {self.telefono}"

agenda = []

for i in range(0,10):
    agenda.append(Contacto("Francisco Jose",7564564564))

# Primero voy a guardar

archivo = open("datos.bin",'wb')
pickle.dump(agenda,archivo)
archivo.close()

# Ahora voy a leer

archivo = open("datos.bin",'rb')
contenido = pickle.load(archivo)
for elemento in contenido:
    print(elemento)