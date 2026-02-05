import requests
import csv
import io
import datetime
import os

if __name__ == "__main__":
    # Limpiamos la pantalla para una ejecución limpia
    os.system('cls' if os.name == 'nt' else 'clear')

    try:
        # URL del archivo CSV (Ejemplo)
        # Nota: Sustituir esta URL por la proporcionada en el ejercicio real
        url = "https://raw.githubusercontent.com/gist/tu_usuario/tu_gist/alumnos.csv"
        
        # Realizamos la petición GET
        response = requests.get(url)
        response.raise_for_status() # Verificamos si hubo errores en la descarga

        # Procesamos el contenido de texto como si fuera un archivo
        with io.StringIO(response.text) as archivo_csv:
            # Creamos el lector de diccionario
            reader = csv.DictReader(archivo_csv)
            
            print(f"{'NOMBRE':<30} {'CURSO':<20} {'FECHA INICIO'}")
            print("-" * 65)

            for fila in reader:
                nombre = fila.get('Nombre', 'Desconocido')
                curso = fila.get('Curso', 'No especificado')
                fecha_str = fila.get('Fecha', '01/01/2000')

                # Procesamos la fecha con datetime (asumiendo formato DD/MM/YYYY)
                try:
                    fecha_obj = datetime.datetime.strptime(fecha_str, '%d/%m/%Y')
                    fecha_formateada = fecha_obj.strftime('%Y-%m-%d')
                except ValueError:
                    fecha_formateada = fecha_str

                print(f"{nombre:<30} {curso:<20} {fecha_formateada}")

    except requests.exceptions.RequestException as e:
        print(f"Error al descargar el archivo: {e}")
    except Exception as e:
        print(f"Ocurrió un error inesperado: {e}")
