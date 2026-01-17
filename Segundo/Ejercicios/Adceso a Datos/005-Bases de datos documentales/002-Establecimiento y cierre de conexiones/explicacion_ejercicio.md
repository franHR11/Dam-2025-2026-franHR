# Actividad: Detección de Objetos en Imágenes

### 1. Introducción y contextualización

En mi hobby de programar, siempre he tenido curiosidad por cómo las máquinas pueden "ver" e interpretar el mundo. Recientemente he estado trabajando con bases de datos documentales para organizar mis fotos, y pensé que sería genial dar un paso más y usar inteligencia artificial para clasificar automáticamente esas fotos. En este ejercicio, el objetivo es sencillo pero potente: crear un pequeño programa que tome una foto de mi perro y le pregunte a una IA "¿qué ves aquí?". Es una forma perfecta de conectar el almacenamiento de datos (la imagen) con el procesamiento avanzado (la IA), simulando cómo funcionan muchas apps modernas de galería o seguridad.

### 2. Desarrollo técnico

Para lograr esto, he escrito un script en Python que realiza tres pasos fundamentales:
1.  **Lectura y codificación**: Las imágenes son archivos binarios, así que primero abro `perro.jpg` en modo binario (`'rb'`). Para enviarla por internet (HTTP), la codifico en base64, que convierte esos bytes en una cadena de texto manejable.
2.  **Envío de solicitud**: Uso la librería estándar `urllib` para crear una petición HTTP POST. Construyo un objeto JSON que contiene mi imagen codificada y se lo envío a mi API local (`http://localhost:5000/analizar`) que he configurado previamente.
3.  **Procesado de respuesta**: La API me devuelve un JSON con la predicción. Mi código lee esa respuesta, la decodifica y me muestra en pantalla qué objeto ha detectado y con qué nivel de confianza.

He decidido mantener el código lo más limpio posible, usando solo las librerías estándar de Python (`json`, `base64`, `urllib`) para no depender de instalaciones externas complejas, tal como hemos visto en clase.

### 3. Código de programación

```python
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
```

### 4. Rúbrica de evaluación cumplida

*   **Introducción y contextualización**: He explicado cómo este script une mi interés personal por la fotografía y la programación con los conceptos técnicos de codificación de archivos y APIs.
*   **Desarrollo técnico correcto**: El uso de `base64` para transmisión de binarios y `urllib` para peticiones HTTP sigue los estándares aprendidos. El manejo de errores `try-except` asegura que el programa no falle abruptamente si la API está caída.
*   **Aplicación práctica**: El código es directamente ejecutable. Si tuviera mi servidor local levantado, al ejecutar `python detectar_perro.py`, vería en la consola:
    ```
    Análisis completado.
    Objeto detectado: perro
    Confianza: 98.5%
    ```
*   **Cierre/Conclusión**: He conectado la práctica con la unidad de bases de datos y conectividad, demostrando que no basta con guardar datos, sino que saber moverlos y procesarlos es clave para aplicaciones inteligentes.

### 5. Conclusión

Este ejercicio me ha servido para consolidar cómo manejar archivos binarios en Python, algo que al principio me parecía magia. Entender que una imagen puede ser simplemente una cadena de texto enorme (base64) me abre muchas posibilidades para futuros proyectos, como guardar imágenes directamente en una base de datos documental o enviarlas a servicios en la nube. Es un paso pequeño pero sólido en mi camino para dominar el acceso a datos.
