import subprocess
import json

def generar_consulta(tabla, tipo_consulta):
    # Creo el prompt dinámico con el nombre de la tabla y el tipo de consulta
    prompt = f"Genera una consulta SQL {tipo_consulta} para la tabla '{tabla}'."
    
    # Preparo el comando curl para enviar la solicitud HTTP a Ollama
    comando = [
        "curl",
        "-X", "POST",
        "http://localhost:11434/api/generate",
        "-H", "Content-Type: application/json",
        "-d", json.dumps({"model": "llama2", "prompt": prompt, "stream": False})
    ]
    
    # Ejecuto el comando usando subprocess y capturo la salida
    resultado = subprocess.run(comando, capture_output=True, text=True)
    
    if resultado.returncode == 0:
        # Si todo sale bien, parseo la respuesta JSON y devuelvo la consulta
        respuesta = json.loads(resultado.stdout)
        return respuesta.get("response", "")
    else:
        # Si hay error, devuelvo un mensaje simple
        return "Error al generar consulta"

# Ejemplo práctico: genero una consulta SELECT para la tabla 'capturas'
consulta = generar_consulta("capturas", "SELECT")
print(consulta)
