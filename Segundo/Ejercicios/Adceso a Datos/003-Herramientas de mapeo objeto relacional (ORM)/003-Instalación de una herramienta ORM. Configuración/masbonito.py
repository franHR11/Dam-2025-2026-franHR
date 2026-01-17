from pydub import AudioSegment
import numpy as np
from PIL import Image, ImageDraw

# Cargar archivo
audio = AudioSegment.from_mp3("0802.mp3")
samples = np.array(audio.get_array_of_samples())

# Parámetros de la imagen (La milla extra: Paso 2)
WIDTH = 800
HEIGHT = 200
BACKGROUND_COLOR = (255, 255, 255)
WAVE_COLOR = (0, 0, 255)

# Crear imagen
image = Image.new("RGB", (WIDTH, HEIGHT), BACKGROUND_COLOR)
draw = ImageDraw.Draw(image)

# Normalizar muestras para que quepan en la altura
# Tomamos un subconjunto de muestras para ajustar al ancho
step = len(samples) // WIDTH
if step == 0: step = 1
normalized_samples = samples[::step]
normalized_samples = normalized_samples[:WIDTH] # Asegurar longitud

# Dibujar la onda
# El centro vertical es HEIGHT // 2
mid_y = HEIGHT // 2
# Factor de escala vertical. Max valor de sample suele ser 32768 para 16bit
max_val = np.max(np.abs(samples)) if len(samples) > 0 else 1
scale_y = (HEIGHT / 2) / max_val

points = []
for x, sample in enumerate(normalized_samples):
    y = mid_y - int(sample * scale_y)
    points.append((x, y))

if len(points) > 1:
    draw.line(points, fill=WAVE_COLOR)

# Guardar imagen
image.save("waveform.png")
print("Imagen waveform.png generada correctamente.")
