import yt_dlp
import json

# URL de la playlist (Ejemplo: Playlist del profesor)
# Sustituir con la URL real de la playlist a procesar
URL_PLAYLIST = "https://www.youtube.com/playlist?list=PL_EXAMPLE_PLAYLIST_ID"

def expandir_playlist(url):
    # Configuración mínima para obtener metadatos sin descargar nada
    opciones = {
        'extract_flat': True,      # Modo rápido: no descarga videos, solo lista
        'dump_single_json': True,  # Devuelve un JSON consolidado
        'quiet': True              # Limpia la salida de consola
    }

    print(f"Procesando playlist: {url}...")
    
    with yt_dlp.YoutubeDL(opciones) as ydl:
        # Extraemos la información sin descargar
        info = ydl.extract_info(url, download=False)
        
        print(f"\nPlaylist encontrada: {info.get('title', 'Desconocido')}")
        print("-" * 50)
        
        videos = info.get('entries', [])
        if videos:
            for i, video in enumerate(videos, 1):
                print(f"{i}. [{video['id']}] {video['title']}")
        else:
            print("No se encontraron vídeos en esta playlist.")

if __name__ == "__main__":
    try:
        expandir_playlist(URL_PLAYLIST)
    except Exception as e:
        print(f"Ocurrió un error: {e}")
