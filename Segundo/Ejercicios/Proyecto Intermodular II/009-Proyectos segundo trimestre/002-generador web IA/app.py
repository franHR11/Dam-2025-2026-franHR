from flask import Flask, render_template, request

# Inicializamos la aplicación Flask
app = Flask(__name__)

# Simulación de función que llama a un modelo de IA
# En un entorno real, aquí conectaríamos con la API de OpenAI o similar
def generar_html_ia(descripcion):
    descripcion = descripcion.lower()
    html_generado = ""
    
    # Lógica heurística para simular la respuesta de la IA
    # basada en palabras clave del prompt del usuario
    if "pesca" in descripcion or "pez" in descripcion or "rio" in descripcion:
        html_generado = """
            <div style="background-color: #e0f7fa; padding: 25px; border-radius: 12px; text-align: center; border: 1px solid #b2ebf2;">
                <h2 style="color: #006064; margin-bottom: 15px;">🎣 Galería de Grandes Capturas</h2>
                <p style="color: #00838f; font-size: 1.1em;">Compartiendo la pasión por el río y el mar.</p>
                <img src="https://images.unsplash.com/photo-1544551763-8dd40575152a?w=500" alt="Pez capturado" style="border-radius: 8px; margin-top: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <p style="margin-top: 10px; font-style: italic;">"La paciencia es la mejor caña."</p>
            </div>
        """
    elif "caza" in descripcion or "ciervo" in descripcion or "monte" in descripcion:
        html_generado = """
            <div style="background-color: #f1f8e9; padding: 25px; border-radius: 12px; text-align: center; border: 1px solid #c5e1a5;">
                <h2 style="color: #33691e; margin-bottom: 15px;">🦌 Diario del Cazador</h2>
                <p style="color: #558b2f; font-size: 1.1em;">El respeto por la naturaleza es nuestra prioridad.</p>
                 <img src="https://images.unsplash.com/photo-1476900966465-950c4c47842e?w=500" alt="Bosque y naturaleza" style="border-radius: 8px; margin-top: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                 <ul style="text-align: left; display: inline-block; margin-top: 15px; color: #33691e;">
                    <li>Conoce tu entorno.</li>
                    <li>Mantén el silencio.</li>
                    <li>Respeta las vedas.</li>
                 </ul>
            </div>
        """
    else:
        html_generado = """
            <div style="padding: 25px; text-align: center; border: 2px dashed #ccc; border-radius: 12px;">
                <h2 style="color: #555;">Esperando instrucciones...</h2>
                <p>Por favor, describe si quieres ver contenido sobre <strong>pesca</strong> o <strong>caza</strong>.</p>
                <div style="font-size: 3em;">🤔</div>
            </div>
        """
    return html_generado

@app.route("/", methods=["GET", "POST"])
def index():
    vista_previa = ""
    # Si recibimos un formulario (POST), procesamos la entrada
    if request.method == "POST":
        # Captura de la descripción del usuario
        prompt = request.form.get("prompt")
        
        # Llamada a la función "IA" para generar el HTML
        if prompt:
            vista_previa = generar_html_ia(prompt)
            
    # Renderizamos la plantilla con la vista previa (si existe)
    return render_template("index.html", vista_previa=vista_previa)

if __name__ == "__main__":
    app.run(debug=True)
