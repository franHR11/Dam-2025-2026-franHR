import random  # Usamos random para elegir aleatoriamente, ya que es parte de la librería estándar

class Pez:
    def __init__(self, nombre, tamaño, color):
        self.nombre = nombre  # Nombre del pez
        self.tamaño = tamaño  # Tamaño en cm
        self.color = color    # Color del pez

    @staticmethod
    def pesca_peces():
        # Lista predefinida de peces posibles
        peces_posibles = [
            ("Salmón", 50, "Plateado"),
            ("Trucha", 30, "Marrón"),
            ("Carpín", 40, "Dorado"),
            ("Lucio", 60, "Verde"),
            ("Perca", 25, "Azul")
        ]
        # Elegimos uno aleatoriamente
        elegido = random.choice(peces_posibles)
        return Pez(*elegido)

# Lista para almacenar los peces pescados
peces_pescados = []

# Bucle para pescar 5 peces
for _ in range(5):
    pez = Pez.pesca_peces()
    peces_pescados.append(pez)

# Mostramos los peces pescados
for pez in peces_pescados:
    print(f"Pescado: {pez.nombre}, Tamaño: {pez.tamaño} cm, Color: {pez.color}")
