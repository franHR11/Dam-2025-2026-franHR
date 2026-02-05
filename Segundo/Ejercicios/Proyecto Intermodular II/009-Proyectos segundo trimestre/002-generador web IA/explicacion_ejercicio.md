# Explicación del ejercicio: Generador Web IA para Pesca y Caza

### 🧠 2. Explicación personal del ejercicio
Hola, aquí Francisco José. Para este ejercicio, me he puesto en la piel de un desarrollador que gestiona una comunidad de amantes de la naturaleza. El objetivo era crear una herramienta donde los usuarios (pescadores y cazadores) pudieran describir qué tipo de contenido quieren ver, y el sistema les generase automáticamente una vista previa de la página web.

Para cumplir con las restricciones de "mínimo código" y "sin librerías externas complejas no vistas", he simulado el comportamiento de la IA creando una lógica en el servidor Flask. Esta lógica analiza las palabras clave de la descripción (como "pesca", "pez", "caza", "ciervo") y devuelve fragmentos de HTML predefinidos que encajan con lo que pide el usuario. Es una forma sencilla de ver cómo funcionarían estos flujos de "Prompt -> Generación -> Visualización" sin complicarnos con APIs externas reales por ahora.

### 💻 3. Código de programación

**Archivo: `app.py` (Lógica del servidor)**
```python
from flask import Flask, render_template, request

# Inicializamos la aplicación Flask
app = Flask(__name__)

# Simulación de función que llama a un modelo de IA
# Esta función decide qué HTML devolver basándose en lo que escribe el usuario
def generar_html_ia(descripcion):
    descripcion = descripcion.lower()
    html_generado = ""
    
    # Si habla de pesca, mostramos peces
    if "pesca" in descripcion or "pez" in descripcion or "rio" in descripcion:
        html_generado = """
            <div style="background-color: #e0f7fa; padding: 25px; border-radius: 12px; text-align: center; border: 1px solid #b2ebf2;">
                <h2 style="color: #006064;">🎣 Galería de Grandes Capturas</h2>
                <p>Compartiendo la pasión por el río y el mar.</p>
                <img src="https://images.unsplash.com/photo-1544551763-8dd40575152a?w=500" alt="Pez capturado" style="border-radius: 8px; margin-top: 15px;">
            </div>
        """
    # Si habla de caza, mostramos caza
    elif "caza" in descripcion or "ciervo" in descripcion or "monte" in descripcion:
        html_generado = """
            <div style="background-color: #f1f8e9; padding: 25px; border-radius: 12px; text-align: center; border: 1px solid #c5e1a5;">
                <h2 style="color: #33691e;">🦌 Diario del Cazador</h2>
                <p>El respeto por la naturaleza es nuestra prioridad.</p>
                 <img src="https://images.unsplash.com/photo-1476900966465-950c4c47842e?w=500" alt="Bosque" style="border-radius: 8px; margin-top: 15px;">
            </div>
        """
    # Si no entiende, pide más detalles
    else:
        html_generado = """
            <div style="padding: 25px; text-align: center; border: 2px dashed #ccc; border-radius: 12px;">
                <h2 style="color: #555;">Esperando instrucciones...</h2>
                <p>Por favor, escribe sobre <strong>pesca</strong> o <strong>caza</strong> para generar tu web.</p>
            </div>
        """
    return html_generado

@app.route("/", methods=["GET", "POST"])
def index():
    vista_previa = ""
    # Al enviar el formulario
    if request.method == "POST":
        prompt = request.form.get("prompt")
        
        # Llamamos a nuestra función generadora
        if prompt:
            vista_previa = generar_html_ia(prompt)
            
    # Mostramos la plantilla con el resultado (si lo hay)
    return render_template("index.html", vista_previa=vista_previa)

if __name__ == "__main__":
    app.run(debug=True)
```

**Archivo: `templates/index.html` (Interfaz)**
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generador Web IA</title>
    <!-- Estilos CSS básicos para que se vea limpio -->
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background-color: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        textarea { width: 100%; height: 100px; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background-color: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; width: 100%; }
        button:hover { background-color: #219150; }
        .preview { margin-top: 30px; border-top: 2px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌲 Generador Web de Pesca y Caza 🦆</h1>
        
        <form method="POST">
            <label for="prompt">Describe tu página ideal (ej: "fotos de pesca" o "consejos de caza"):</label>
            <textarea name="prompt" id="prompt"></textarea>
            <button type="submit">✨ Generar HTML</button>
        </form>

        {% if vista_previa %}
        <div class="preview">
            <h3>Resultado Generado:</h3>
            <!-- El filtro safe es necesario para que Flask renderice el HTML generado -->
            {{ vista_previa | safe }}
        </div>
        {% endif %}
    </div>
</body>
</html>
```

### 📊 4. Rúbrica de evaluación cumplida

1.  **Introducción y contextualización:**
    *   He situado el ejercicio en el contexto de una comunidad de naturaleza, tal como se pedía ("programador apasionado por la pesca y la caza").
    *   He explicado que sirve para prototipar páginas web rápidamente basándose en descripciones de texto.

2.  **Desarrollo técnico correcto y preciso:**
    *   He usado **Flask** para el servidor, gestionando rutas y peticiones POST.
    *   He implementado la captura de datos del formulario (`request.form.get`).
    *   He creado una función `generar_html_ia` que simula la respuesta de la IA generando código HTML válido.
    *   La inyección del HTML en la vista se, realiza correctamente usando `{{ vista_previa | safe }}` para que el navegador lo interprete y no lo muestre como texto plano.

3.  **Aplicación práctica con ejemplo claro:**
    *   El código es funcional y permite probar el flujo completo: Escribir "Quiero ver pesca" -> Botón -> Aparece una tarjeta con imágenes de pesca.
    *   He incluido dos casos de uso claros (Pesca y Caza) y un caso por defecto para errores o entradas vacías.

4.  **Cierre/Conclusión:**
    *   Como conclusión, veo que este patrón de "Prompt -> Proceso -> Resultado" es la base de las aplicaciones de IA Generativa modernas. Aunque aquí lo he simulado con condicionales simples `if/else`, la estructura del programa sería idéntica si conectase con una API real, solo cambiaría la función `generar_html_ia`.

### 🧾 5. Cierre
Personalmente, me ha gustado el ejercicio porque une mi afición por el campo con la programación. Es interesante ver cómo podemos generar contenido dinámico en base a lo que escribe el usuario, y aunque aquí está simplificado, se ve claramente el potencial para crear herramientas más complejas en el futuro.
