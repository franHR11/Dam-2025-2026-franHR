# Ejercicio: Generación de consultas SQL con Ollama

## Explicación personal del ejercicio
En este ejercicio tuve que desarrollar una función que me ayude a generar consultas SQL para gestionar mis expediciones de pesca y caza. Como me apasiona la pesca, decidí crear algo simple que use un servicio local como Ollama para generar las consultas automáticamente, sin complicarme mucho.

## Código de programación
```python
import subprocess
import json

def generar_consulta(tabla, tipo_consulta):
    # Creo el prompt con la tabla y el tipo de consulta
    prompt = f"Genera una consulta SQL {tipo_consulta} para la tabla '{tabla}'."
    
    # Uso subprocess para ejecutar curl a Ollama en localhost
    comando = [
        "curl",
        "-X", "POST",
        "http://localhost:11434/api/generate",
        "-H", "Content-Type: application/json",
        "-d", json.dumps({"model": "llama2", "prompt": prompt, "stream": False})
    ]
    
    # Ejecuto el comando y capturo la salida
    resultado = subprocess.run(comando, capture_output=True, text=True)
    
    if resultado.returncode == 0:
        # Proceso la respuesta JSON
        respuesta = json.loads(resultado.stdout)
        return respuesta.get("response", "")
    else:
        return "Error al generar consulta"

# Ejemplo de uso
consulta = generar_consulta("capturas", "SELECT")
print(consulta)
```

## Rúbrica de evaluación cumplida
- Introducción y contextualización (25%): Expliqué el problema de gestionar expediciones con una función para generar consultas SQL, en el contexto de pesca y caza.
- Desarrollo técnico correcto y preciso (25%): Implementé la función generar_consulta usando subprocess para ejecutar HTTP a Ollama, con prompt dinámico, procesando la respuesta JSON.
- Aplicación práctica con ejemplo claro (25%): Proporcioné un ejemplo de uso de la función con tabla "capturas" y tipo "SELECT", mostrando cómo se llama y se imprime el resultado.
- Cierre/Conclusión enlazando con la unidad (25%): Esta actividad relaciona con programación multiproceso y servicios, aplicable en gestión de datos para apps reales como la mía de pesca.

## Cierre
Me pareció útil practicar con subprocess y APIs locales, simplificando el código para no perder tiempo.
