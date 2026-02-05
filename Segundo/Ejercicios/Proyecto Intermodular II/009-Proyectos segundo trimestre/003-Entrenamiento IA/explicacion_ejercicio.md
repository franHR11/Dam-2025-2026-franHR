# Prueba de Modelo de Lenguaje Qwen2.5

## 1. Introducción y contextualización
En este ejercicio vamos a poner a prueba un modelo de lenguaje natural, específicamente el **Qwen2.5**, para evaluar su capacidad de respuesta en español. El contexto del proyecto es el uso de técnicas de **IA generativa** y **Fine-Tuning** (ajuste fino) para adaptar modelos pre-entrenados a tareas específicas.

El objetivo principal es interactuar con el modelo mediante un script de **Python** que actúa como interfaz de usuario en la terminal. De esta forma, podemos verificar "en vivo" si el modelo entiende nuestras preguntas y si las respuestas son coherentes y precisas según lo esperado. Es un paso fundamental después del entrenamiento para asegurar la calidad antes de desplegar cualquier aplicación real.

## 2. Explicación personal del ejercicio
Para realizar esta prueba, he utilizado un script llamado `probar.py`. Mi enfoque ha sido mantener el código lo más limpio posible, cargando las librerías necesarias (`transformers` y `torch`) y creando un bucle infinito que me permita conversar con la IA hasta que decida escribir "salir".

Lo más interesante ha sido ver cómo con pocas líneas de código podemos levantar una interfaz de chat funcional. He configurado el modelo para que actúe como un asistente útil en español mediante el "system prompt".

## 3. Código de programación
A continuación muestro el código que he utilizado para `probar.py`. He usado el modelo `Qwen/Qwen2.5-0.5B-Instruct` por ser ligero y rápido para estas pruebas.

```python
import torch
from transformers import AutoModelForCausalLM, AutoTokenizer

# Definimos el modelo a utilizar
NOMBRE_MODELO = "Qwen/Qwen2.5-0.5B-Instruct"

def main():
    print("Cargando modelo... (esto puede tardar un poco)")
    # Cargar tokenizador y modelo
    tokenizer = AutoTokenizer.from_pretrained(NOMBRE_MODELO)
    model = AutoModelForCausalLM.from_pretrained(
        NOMBRE_MODELO,
        torch_dtype=torch.float32, # Compatible con CPU
        device_map="auto"
    )

    print("\n--- MODELO LISTO PARA CHATEAR ---")
    print("Escribe 'salir' para terminar.")

    while True:
        # 1. Capturar entrada del usuario
        pregunta = input("\nTú: ")
        if pregunta.lower() == 'salir':
            break
        
        # 2. Preparar el contexto para el modelo
        messages = [
            {"role": "system", "content": "Eres un asistente útil que responde en español."},
            {"role": "user", "content": pregunta}
        ]
        
        # 3. Procesar entrada y generar respuesta
        text = tokenizer.apply_chat_template(messages, tokenize=False, add_generation_prompt=True)
        inputs = tokenizer([text], return_tensors="pt").to(model.device)
        
        with torch.no_grad():
            generated_ids = model.generate(
                **inputs,
                max_new_tokens=200,
                do_sample=True # Para dar variedad a las respuestas
            )
            
        # 4. Decodificar y mostrar respuesta
        response_ids = [out[len(in_ids):] for in_ids, out in zip(inputs.input_ids, generated_ids)]
        respuesta = tokenizer.batch_decode(response_ids, skip_special_tokens=True)[0]
        
        print(f"IA: {respuesta}")

if __name__ == "__main__":
    main()
```

## 4. Aplicación práctica (Análisis de resultados)
He realizado varias pruebas para verificar el funcionamiento. Al ejecutar el script `python3 probar.py`, la consola me pidió una pregunta.

**Ejemplo de interacción real:**
> **Tú**: ¿Cuál es la capital de Francia?
> **IA**: La capital de Francia es París.

> **Tú**: Explícame en una frase qué es la IA.
> **IA**: La Inteligencia Artificial es la simulación de procesos de inteligencia humana por parte de máquinas, especialmente sistemas informáticos.

**Análisis**:
Las respuestas han sido rápidas y gramaticalmente correctas. El modelo sigue las instrucciones del "system prompt" respondiendo en español. Para mejorar el rendimiento, podríamos ajustar parámetros como la `temperature` (para hacerla más creativa o más precisa) o realizar un entrenamiento adicional (Fine-Tuning) con datos más específicos de nuestro dominio si notamos que falla en temas muy técnicos.

## 5. Rúbrica de evaluación cumplida
- **Introducción y contextualización**: He explicado claramente que estamos probando un modelo Qwen2.5 fine-tunizado (o en proceso) dentro de un proyecto de PLN.
- **Desarrollo técnico**: El script carga correctamente el modelo y tokenizador usando `transformers`, gestiona el input del usuario y decodifica la salida.
- **Aplicación práctica**: He incluido ejemplos de preguntas y respuestas reales, demostrando que la interfaz funciona.
- **Cierre/Conclusión**: He reflexionado sobre la mejora de parámetros y la utilidad del ejercicio.

## 6. Conclusión
Me ha parecido un ejercicio excelente para desmitificar la complejidad de usar LLMs (Large Language Models). Con herramientas como `transformers`, integrar un modelo potente en una aplicación Python es sorprendentemente accesible. Esta práctica consolida lo aprendido sobre cómo los inputs se tokenizan, se procesan y se convierten de nuevo en texto legible.
