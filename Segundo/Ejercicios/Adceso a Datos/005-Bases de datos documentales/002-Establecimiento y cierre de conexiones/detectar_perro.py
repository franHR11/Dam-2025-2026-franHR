import urllib.request
import json
import base64

# 1. Cargar la imagen del perro
# Abrimos el archivo en modo lectura binaria ('rb')
with open('perro.jpg', 'rb') as archivo_imagen:
    # Leemos el contenido y lo codificamos a base64
    imagen_codificada = base64.b64encode(archivo_imagen.read()).decode('utf-8')

# 2. Envíar la solicitud de análisis
# Definimos la URL de nuestra API local (asumiendo que está corriendo en el puerto 5000)
url = 'http://localhost:5000/analizar'

# Preparamos los datos en formato JSON
datos = {
    'imagen': imagen_codificada
}

# Convertimos el diccionario a un string JSON y luego a bytes
datos_json = json.dumps(datos).encode('utf-8')

# Creamos la solicitud, especificando que enviamos JSON
solicitud = urllib.request.Request(url, data=datos_json, headers={'Content-Type': 'application/json'})

try:
    # Enviamos la solicitud y esperamos la respuesta
    with urllib.request.urlopen(solicitud) as respuesta:
        # 3. Procesar la respuesta
        datos_respuesta = json.loads(respuesta.read().decode('utf-8'))
        
        # Mostramos el resultado
        print("Análisis completado.")
        print(f"Objeto detectado: {datos_respuesta.get('objeto', 'Desconocido')}")
        print(f"Confianza: {datos_respuesta.get('confianza', 0) * 100}%")

except urllib.error.URLError as e:
    print(f"Error al conectar con la API: {e}")
    print("Asegúrate de que el servidor de análisis esté ejecutándose.")
except FileNotFoundError:
    print("Error: No se encuentra el archivo 'perro.jpg'.")
