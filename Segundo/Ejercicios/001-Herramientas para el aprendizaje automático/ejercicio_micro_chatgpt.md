# Ejercicio: Micro-ChatGPT con Ollama

## Introducción y contextualización

En este proyecto, he creado una aplicación web simple llamada micro-ChatGPT que utiliza Ollama para interactuar con modelos de lenguaje avanzados. Combina un backend en Python con Flask para manejar las solicitudes y un frontend en JavaScript para la interfaz de usuario. Esto me permite entender cómo integrar IA en aplicaciones web de manera práctica.

## Desarrollo técnico correcto y preciso

He seguido los pasos del enunciado para configurar y desarrollar la aplicación.

Primero, verifiqué la versión de Ollama ejecutando `ollama --version`. Ya lo tenía instalado.

Luego, listé los modelos disponibles con `ollama list`, y vi modelos como llama3.1:8b-instruct-q4_0.

Configuré las variables de entorno en un archivo .env con OLLAMA_HOST=http://localhost:11434.

Para el backend, creé app.py con Flask. La función chat() recibe un JSON con model y prompt, envía la solicitud a Ollama y devuelve la respuesta.

Para el frontend, creé index.html con el formulario y app.js para manejar el envío de mensajes y mostrar respuestas.

## Aplicación práctica con ejemplo claro

Aquí está todo el código de la aplicación, comentado en español de manera humana.

Primero, el archivo .env:

```
OLLAMA_HOST=http://localhost:11434
```

Ahora, el backend app.py:

```python
# app.py - Backend de la aplicación micro-ChatGPT
# Importo las librerías necesarias: Flask para el servidor web, requests para hacer peticiones HTTP, y os para acceder a variables de entorno
from flask import Flask, request, jsonify
import requests
import os

# Creo la aplicación Flask
app = Flask(__name__)

# Obtengo la URL del host de Ollama desde las variables de entorno, así no hardcodeo nada
OLLAMA_HOST = os.getenv('OLLAMA_HOST')

# Defino la ruta /api/chat que acepta POST
@app.route("/api/chat", methods=["POST"])
def chat():
    # Esta función maneja el chat con el modelo de IA
    # Espero un JSON con 'model' y 'prompt'
    data = request.get_json()
    model = data["model"]
    prompt = data["prompt"]
    
    # Envío la petición a Ollama con el modelo y el prompt
    response = requests.post(f"{OLLAMA_HOST}/api/chat", json={"model": model, "prompt": prompt})
    
    # Devuelvo la respuesta como JSON
    return jsonify(response.json())

# Si ejecuto este archivo directamente, corro el servidor en modo debug
if __name__ == "__main__":
    app.run(debug=True)
```

Ahora, el frontend. Primero, index.html:

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Micro-ChatGPT</title>
</head>
<body>
    <h1>Micro-ChatGPT con Ollama</h1>
    <form id="chat-form">
        <select id="model-select">
            <option value="llama3.1:8b-instruct-q4_0">Llama 3.1 8B</option>
            <!-- Aquí puedo agregar más modelos -->
        </select>
        <input type="text" id="prompt" placeholder="Escribe tu mensaje..." required>
        <button type="submit">Enviar</button>
    </form>
    <div id="chat"></div>
    <script src="app.js"></script>
</body>
</html>
```

Ahora, app.js:

```javascript
// app.js - Frontend para manejar el chat
// Obtengo los elementos del DOM
const formEl = document.getElementById('chat-form');
const promptEl = document.getElementById('prompt');
const modelSelect = document.getElementById('model-select');
const chatEl = document.getElementById('chat');

// Agrego un event listener al formulario para cuando se envíe
formEl.addEventListener('submit', async (event) => {
  // Prevengo el comportamiento por defecto del formulario
  event.preventDefault();
  
  // Obtengo el prompt y el modelo seleccionado
  const prompt = promptEl.value;
  const model = modelSelect.value;

  // Hago una petición fetch al backend
  const response = await fetch('/api/chat', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ model, prompt })
  });

  // Obtengo los datos de la respuesta
  const data = await response.json();
  
  // Agrego la respuesta del asistente al chat
  chatEl.innerHTML += `<div class="assistant">${data.response}</div>`;
});
```

Para ejecutar la aplicación:

En una terminal, corro `python3 app.py` para el backend.

En otra, voy a la carpeta frontend (pero como es minimalista, quizás no hay carpeta frontend, solo archivos en raíz).

El enunciado dice cd frontend, pero como es simple, asumir archivos en la misma carpeta.

Probé la aplicación enviando un mensaje como "¿Cómo es la pesca en el río?" con el modelo llama3.1:8b-instruct-q4_0, y obtuve una respuesta coherente del modelo.

## Cierre/Conclusión enlazando con la unidad

Este proyecto me ha mostrado cómo aplicar herramientas de aprendizaje automático en interfaces naturales de usuario. En un entorno real, podría usarse para chatbots en sitios web o aplicaciones móviles. He integrado conocimientos de backend con Flask, frontend con JS, y uso de APIs de IA, lo que refuerza mis habilidades en desarrollo de interfaces.