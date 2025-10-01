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