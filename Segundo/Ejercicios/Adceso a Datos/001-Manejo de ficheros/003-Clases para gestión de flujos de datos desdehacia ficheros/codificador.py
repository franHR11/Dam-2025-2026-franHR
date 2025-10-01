# Este script toma un texto desde la línea de comandos y lo convierte en una imagen cuadrada.
# Usamos Pillow para crear la imagen, donde cada píxel representa un byte del texto.
# Es simple: convertimos el texto a bytes, calculamos un tamaño cuadrado y ponemos los bytes en los píxeles rojos.

import argparse
import math
from PIL import Image

# Configuramos los argumentos de la línea de comandos
parser = argparse.ArgumentParser(description='Codifica un texto en una imagen cuadrada.')
parser.add_argument('texto', type=str, help='El texto que quieres codificar en la imagen.')
args = parser.parse_args()

try:
    # Convertimos el texto a bytes usando UTF-8 para manejar caracteres especiales
    texto_bytes = args.texto.encode('utf-8')

    # Calculamos el tamaño del lado de la imagen cuadrada
    # Usamos la raíz cuadrada del número de bytes, redondeada hacia arriba
    lado = math.ceil(math.sqrt(len(texto_bytes) + 1))

    # Creamos una nueva imagen RGB negra (fondo negro)
    img = Image.new('RGB', (lado, lado), color=(0, 0, 0))
    # Ponemos la longitud del texto en el primer píxel
    img.putpixel((0, 0), (len(texto_bytes), 0, 0))

    # Ponemos cada byte en un píxel, usando solo el canal rojo para simplicidad
    for i, byte in enumerate(texto_bytes):
        x = i % lado  # Posición x en la fila
        y = i // lado  # Posición y en la columna
        img.putpixel((x, y), (byte, 0, 0))  # Rojo con el valor del byte

    # Guardamos la imagen con un nombre fijo, ya que no especificamos argumento para el nombre
    img.save('texto_codificado.png')
    print("Imagen guardada como 'texto_codificado.png'. ¡Listo!")

except Exception as e:
    print(f"Ocurrió un error al codificar: {e}. Asegúrate de que el texto sea válido.")