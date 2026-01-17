from pydub import AudioSegment
import numpy as np

# Cargar archivo de audio (La milla extra: Paso 1)
# Utiliza el script leer archivo.py para cargar un archivo MP3 llamado "0802.mp3".
audio = AudioSegment.from_mp3("0802.mp3")

# Convertir a array de muestras
samples = np.array(audio.get_array_of_samples())

print(f"Audio cargado. Muestras: {len(samples)}")
print(f"Duración: {len(audio)} ms")
print(f"Canales: {audio.channels}")
