import yt_dlp

# URL del vídeo específico a transcribir
URL_VIDEO = "https://www.youtube.com/watch?v=VIDEO_EXAMPLE_ID"

def transcribir_video(url):
    # Configuración para descargar solo subtítulos (prioridad español)
    opciones = {
        'skip_download': True,       # Importante: No descargar el vídeo (.mp4/etc)
        'writesubtitles': True,      # Activar descarga de subs manuales
        'writeautomaticsub': True,   # Activar subs autogenerados si no hay manuales
        'subtitleslangs': ['es', 'en'], # Idiomas: Español, y si no, Inglés
        'outtmpl': 'transcripciones/%(title)s.%(ext)s', # Guardar en carpeta organizada
        'quiet': False
    }

    print(f"Iniciando extracción de subtítulos para: {url}")

    try:
        with yt_dlp.YoutubeDL(opciones) as ydl:
            ydl.download([url])
            print("\n¡Proceso finalizado! Revisa la carpeta de salida.")
    except Exception as e:
        print(f"Error durante la transcripción: {e}")

if __name__ == "__main__":
    transcribir_video(URL_VIDEO)
