# Este script toma el nombre de una imagen desde la línea de comandos y extrae el texto codificado.
# Leemos los píxeles de la imagen, tomamos la longitud del primer píxel y luego los bytes siguientes para reconstruir el texto.
# Es simple: abrimos la imagen, leemos los píxeles rojos y convertimos de vuelta a texto.

import argparse
from PIL import Image

# Configuramos los argumentos de la línea de comandos
parser = argparse.ArgumentParser(description='Decodifica una imagen para extraer el texto codificado.')
parser.add_argument('imagen', type=str, help='El nombre del archivo de imagen a decodificar.')
args = parser.parse_args()

try:
    # Abrimos la imagen
    img = Image.open(args.imagen)

    # Obtenemos todos los píxeles como una lista
    pixels = list(img.getdata())

    # El primer píxel contiene la longitud del texto
    length = pixels[0][0]  # Solo el canal rojo

    # Tomamos los siguientes 'length' píxeles para los bytes del texto
    bytes_list = [pixels[i + 1][0] for i in range(length)]

    # Convertimos los bytes de vuelta a texto usando UTF-8
    texto = bytes(bytes_list).decode('utf-8')

    # Imprimimos el texto decodificado
    print(f"Texto decodificado: {texto}")

except FileNotFoundError:
    print(f"Error: No se encontró el archivo '{args.imagen}'. Asegúrate de que existe.")
except Exception as e:
    print(f"Ocurrió un error al decodificar: {e}. Verifica que la imagen sea válida y esté codificada correctamente.")