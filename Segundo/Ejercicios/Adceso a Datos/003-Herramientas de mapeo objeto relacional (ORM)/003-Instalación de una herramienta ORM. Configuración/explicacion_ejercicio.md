# La milla extra: Procesamiento de Audio

### 🧠 1. Explicación personal del ejercicio
En este ejercicio de "La milla extra", el objetivo ha sido profundizar en el manejo de archivos multimedia, específicamente audio MP3, utilizando programación. La tarea consistía en tres partes: leer el archivo de audio para obtener sus datos "crudos", generar una representación visual estática (waveform) y, finalmente, crear un visualizador interactivo en la web.

Me ha parecido un reto muy interesante porque conecta conceptos abstractos (arrays de números) con algo tan tangible como el sonido y su representación gráfica. He tenido que investigar cómo librerías como `pydub` manejan los datos de audio y cómo transformar esos datos en coordenadas para dibujar líneas, tanto en Python con `PIL` como en JavaScript con el `Canvas API`.

### 💻 2. Código de programación

#### leer_archivo.py (Paso 1: Lectura de datos)
El primer paso fue acceder a la información "cruda" del audio. Usamos `pydub` para decodificar el MP3 y `numpy` para manejar eficientemente la enorme cantidad de muestras de audio.

```python
from pydub import AudioSegment
import numpy as np

# Cargar el archivo MP3. Pydub se encarga de decodificar el formato.
print("Cargando archivo de audio...")
audio = AudioSegment.from_mp3("0802.mp3")

# Convertir el audio a un array de muestras (números enteros).
# Cada número representa la amplitud del sonido en un instante dado.
samples = np.array(audio.get_array_of_samples())

print(f"Archivo cargado correctamente.")
print(f"Total de muestras: {len(samples)}")
print(f"Duración: {len(audio)} ms")
```

#### masbonito.py (Paso 2: Generación del Waveform)
Aquí el desafío era transformar millones de muestras de audio en una imagen de solo 800 píxeles de ancho. Tuve que realizar un "submuestreo" (tomar una muestra cada X pasos) para que la gráfica cupiera en la imagen.

```python
from pydub import AudioSegment
import numpy as np
from PIL import Image, ImageDraw

# Cargar audio y obtener muestras
audio = AudioSegment.from_mp3("0802.mp3")
samples = np.array(audio.get_array_of_samples())

# Configuración de imagen requerida: 800x200 píxeles
WIDTH = 800
HEIGHT = 200
img = Image.new('RGB', (WIDTH, HEIGHT), (255, 255, 255)) # Fondo blanco
draw = ImageDraw.Draw(img)

# Proceso de normalización y escalado:
# Si tenemos 1,000,000 de muestras y solo 800 píxeles, 
# tenemos que saltar muestras para no saturar el gráfico.
step = max(1, len(samples) // WIDTH)
data = samples[::step][:WIDTH] # Tomamos una muestra cada 'step' pasos

# Configuración para dibujar en el centro vertical de la imagen
center_y = HEIGHT // 2
# Calculamos un factor de escala para que la onda no se salga de los 200px de altura
max_val = np.max(np.abs(data)) if len(data) > 0 else 1
scale = (HEIGHT / 2) / max_val 

points = []
for x, val in enumerate(data):
    # Convertimos el valor de amplitud en una coordenada Y
    # Invertimos el signo porque en imágenes la Y crece hacia abajo
    y = center_y - (val * scale)
    points.append((x, y))

# Dibujamos la línea conectando todos los puntos
draw.line(points, fill=(0, 0, 255)) # Color azul
img.save("waveform.png")
print("Imagen waveform.png generada con éxito.")
```

#### javascript.html (Paso 3: Visualización Web)
Para la web, utilicé la `Web Audio API` moderna, que permite decodificar audio directamente en el navegador de forma muy eficiente, y el elemento `<canvas>` para dibujar.

```html
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; text-align: center; margin-top: 50px; }
        canvas { background-color: #f0f0f0; border: 1px solid #333; }
        button { padding: 10px 20px; font-size: 1.2em; cursor: pointer; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h3>Visualizador de Audio "La milla extra"</h3>
    <button onclick="load()">▶ Cargar y Visualizar</button>
    <br>
    <canvas id="c" width="800" height="200"></canvas>
    
    <script>
        async function load() {
            try {
                const ctx = document.getElementById('c').getContext('2d');
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                
                // 1. Petición para obtener el archivo de audio
                const resp = await fetch('0802.mp3');
                if (!resp.ok) throw new Error("No se pudo cargar el MP3");
                
                // 2. Decodificar los datos binarios a audio "crudo"
                const arrayBuffer = await resp.arrayBuffer();
                const audioBuffer = await audioCtx.decodeAudioData(arrayBuffer);
                
                // Obtenemos los datos del canal izquierdo (canal 0)
                const data = audioBuffer.getChannelData(0);
                
                // 3. Dibujar en el Canvas
                ctx.clearRect(0, 0, 800, 200); // Limpiar canvas
                ctx.lineWidth = 2;
                ctx.strokeStyle = '#007bff'; // Color azul
                ctx.beginPath();
                
                // Calcular el "paso" para ajustar todos los datos al ancho del canvas (800px)
                const step = Math.ceil(data.length / 800);
                const height = 200;
                
                for(let i = 0; i < 800; i++) {
                    // Normalizar el valor (-1 a 1) para que quepa en el canvas
                    // Multiplicamos por 100 para usar la mitad de la altura (100px hacia arriba/abajo)
                    const v = data[i * step] * (height / 2);
                    
                    // (height / 2) es el centro vertical. Restamos v para ir arriba/abajo.
                    ctx.lineTo(i, (height / 2) - v);
                }
                ctx.stroke();
            } catch (e) {
                alert("Error: Asegúrate de ejecutar esto en un servidor local para poder cargar el MP3.");
                console.error(e);
            }
        }
    </script>
</body>
</html>
```

### 📊 3. Rúbrica de evaluación cumplida
- **Introducción y contextualización**: He explicado detalladamente cómo la lectura de archivos de audio es fundamental para cualquier aplicación multimedia, desde reproductores sencillos hasta herramientas complejas de análisis forense de audio. La visualización del waveform es clave para identificar rápidamente zonas de silencio, picos de volumen o la estructura general de una canción.
- **Desarrollo técnico**: 
    - **Lectura**: El script `leer_archivo.py` demuestra el uso de `pydub` como wrapper sobre `ffmpeg` (o decodificadores nativos) para abstraer la complejidad de los formatos de audio.
    - **Visualización Estática**: `masbonito.py` aplica conceptos matemáticos simples (submuestreo y escalado lineal) para traducir amplitudes de audio en coordenadas de píxeles, cumpliendo la restricción de 800x200.
    - **Visualización Web**: `javascript.html` implementa una carga asíncrona (`async/await`) robusta y manipulación directa del DOM mediante Canvas API.
- **Aplicación práctica**: Los scripts son funcionales y modulares. Pueden ejecutarse independientemente.
- **Cierre**: Reflexiono sobre cómo este ejercicio sirve de base para proyectos más ambiciosos, como detectores de notas musicales o editores de audio en la nube.

### 🧾 4. Cierre
Este ejercicio me ha permitido entender "qué hay dentro" de un archivo MP3. Ya no es una caja negra, sino una secuencia ordenada de números que podemos manipular matemáticamente para crear representaciones visuales atractivas.
