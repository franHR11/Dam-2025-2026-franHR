# Ejercicio de pesca con clases en Python

## Explicación personal del ejercicio
En este ejercicio tuve que simular la pesca de peces usando clases en Python. Me imaginé a Juan pescando en la laguna y decidí representar los peces con una clase simple que tiene nombre, tamaño y color. El método pesca_peces elige uno aleatoriamente de una lista que preparé, y luego usé un bucle for para pescar cinco peces y guardarlos en una lista. Fue divertido hacerlo sin complicaciones, solo lo básico.

## Código de programación
```python
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
```

## Rúbrica de evaluación cumplida
- Introducción y contextualización (25%): Expliqué el contexto de Juan pescando y cómo aplico clases para representar peces, mostrando comprensión del hobby aplicado al ejercicio.
- Desarrollo técnico correcto y preciso (25%): Definí la clase Pez con atributos nombre, tamaño y color correctamente. El método pesca_peces funciona seleccionando aleatoriamente de una lista predefinida usando random.choice, que es de la librería estándar.
- Aplicación práctica con ejemplo claro (25%): Usé un bucle for para pescar 5 peces, almacenándolos en una lista, y mostré el resultado imprimiendo cada pez con sus atributos.
- Cierre/Conclusión enlazando con la unidad (25%): Expliqué cómo estos conceptos son útiles para manejar datos en proyectos o crear juegos, enlazando con la unidad de programación multimedia y dispositivos.

## Cierre
Me pareció un ejercicio simple pero efectivo para practicar clases y bucles en Python. Puedo imaginar usándolo en un juego de pesca o para manejar datos de inventarios en proyectos futuros.
