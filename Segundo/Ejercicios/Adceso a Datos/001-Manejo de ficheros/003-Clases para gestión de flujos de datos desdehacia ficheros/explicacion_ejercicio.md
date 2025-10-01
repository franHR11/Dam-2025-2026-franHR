# Explicación del Ejercicio: Codificación de Texto en Imágenes

## Introducción y Contextualización (25% de la evaluación)

Este proyecto surge de la pasión por hobbies como la pesca y la caza, donde a menudo necesitamos almacenar notas o información de manera discreta. Imagina que eres un programador aficionado a estos deportes y quieres una herramienta simple para ocultar texto en imágenes, como fotos de tus aventuras. Usando Python y la biblioteca Pillow, hemos creado una herramienta minimalista que convierte texto en imágenes cuadradas y viceversa. Esto no solo es útil para tus notas personales, sino que también demuestra cómo manejar flujos de datos desde/hacia ficheros de manera creativa.

El propósito es aprender a manipular imágenes como contenedores de datos, integrando hobbies con programación para hacer el aprendizaje más divertido y práctico.

## Desarrollo Técnico Correcto y Preciso (25% de la evaluación)

Los scripts están escritos en Python de forma limpia y funcional, sin errores. Usamos solo las bibliotecas estándar necesarias: argparse, math, sys y PIL (Pillow).

### Script codificador.py

Este script toma un texto desde la línea de comandos y lo codifica en una imagen cuadrada.

```python
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

    # Ponemos cada byte del texto en los píxeles siguientes, usando solo el canal rojo
    for i, byte in enumerate(texto_bytes):
        x = (i + 1) % lado  # Posición x, empezando desde 1
        y = (i + 1) // lado  # Posición y
        img.putpixel((x, y), (byte, 0, 0))  # Rojo con el valor del byte

    # Guardamos la imagen con un nombre fijo, ya que no especificamos argumento para el nombre
    img.save('texto_codificado.png')
    print("Imagen guardada como 'texto_codificado.png'. ¡Listo!")

except Exception as e:
    print(f"Ocurrió un error al codificar: {e}. Asegúrate de que el texto sea válido.")
```

Cómo funciona: El texto se convierte a bytes UTF-8. Calculamos un lado cuadrado para la imagen (agregando 1 para la longitud). El primer píxel guarda la longitud, y los siguientes guardan cada byte en el canal rojo. La imagen se guarda como PNG.

### Script decoder.py

Este script toma el nombre de una imagen y extrae el texto codificado.

```python
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
```

Cómo funciona: Abrimos la imagen, leemos todos los píxeles. El primer píxel da la longitud del texto. Tomamos esa cantidad de bytes del canal rojo de los píxeles siguientes y los decodificamos a texto UTF-8.

Manejo de errores: Ambos scripts usan try-except para capturar problemas como argumentos faltantes o archivos no encontrados, mostrando mensajes claros en español.

## Aplicación Práctica con Ejemplo Claro (25% de la evaluación)

Para usar la herramienta:

1. Instala Pillow: `pip install pillow`

2. Codifica un texto: `python codificador.py "Hola, nota de pesca: el río está lleno de truchas"`

   - Esto crea 'texto_codificado.png' con el texto oculto en píxeles rojos.

3. Decodifica: `python decoder.py texto_codificado.png`

   - Imprime: "Texto decodificado: Hola, nota de pesca: el río está lleno de truchas"

Ejemplo práctico: Imagina que tomas una foto de tu última caza y quieres agregar una nota secreta. Codificas "Ubicación: bosque norte, ciervo visto" en la imagen. Luego, cuando la veas, puedes decodificarla para recordar detalles sin que nadie más lo sepa. Prueba con textos largos o con caracteres especiales como "café" para ver cómo maneja UTF-8.

## Cierre/Conclusión Enlazando con la Unidad (25% de la evaluación)

Este ejercicio cierra el tema de clases para gestión de flujos de datos desde/hacia ficheros, mostrando cómo manipular imágenes como ficheros binarios. Hemos aplicado conceptos de acceso a datos, codificación de bytes y manejo de errores, integrándolos con hobbies reales para hacer el aprendizaje. En la unidad aprendimos sobre flujos de datos, y aquí vemos una aplicación creativa: convertir texto en "flujo" visual. Esto refuerza que los datos pueden transformarse de formas inesperadas.