import subprocess
import json
import time

def descargar_motor_ollama(nombre_motor):
    """Descarga un motor de Ollama si no está disponible"""
    try:
        resultado = subprocess.run(["ollama", "list"], capture_output=True, text=True)
        if nombre_motor in resultado.stdout:
            print(f"✅ El motor {nombre_motor} ya está disponible")
            return True
        
        print(f"📥 Descargando motor {nombre_motor}...")
        subprocess.run(["ollama", "pull", nombre_motor], check=True)
        print(f"✅ Motor {nombre_motor} descargado correctamente")
        return True
    except subprocess.CalledProcessError:
        print(f"❌ Error al descargar el motor {nombre_motor}")
        return False
    except FileNotFoundError:
        print("❌ Ollama no está instalado o no está en el PATH")
        return False

def probar_motor(motor, pregunta):
    """Envía una pregunta a un motor específico y mide el tiempo de respuesta"""
    try:
        inicio = time.time()
        resultado = subprocess.run(
            ["ollama", "run", motor, pregunta],
            capture_output=True,
            text=True,
            timeout=30
        )
        fin = time.time()
        
        return {
            "motor": motor,
            "pregunta": pregunta,
            "respuesta": resultado.stdout.strip(),
            "tiempo_respuesta": round(fin - inicio, 2),
            "exitoso": resultado.returncode == 0
        }
    except subprocess.TimeoutExpired:
        return {
            "motor": motor,
            "pregunta": pregunta,
            "respuesta": "Tiempo de espera agotado",
            "tiempo_respuesta": 30.0,
            "exitoso": False
        }
    except Exception as e:
        return {
            "motor": motor,
            "pregunta": pregunta,
            "respuesta": f"Error: {str(e)}",
            "tiempo_respuesta": 0.0,
            "exitoso": False
        }

def main():
    # Motores de IA a descargar y probar
    motores = ["llama3", "mistral", "phi3"]
    
    # Batería de preguntas tipo para evaluar habilidades de pesca y caza
    preguntas = [
        "¿Cuál es la mejor época del año para pescar lubina en el Mediterráneo?",
        "Describe tres técnicas efectivas para capturar truchas en ríos de montaña",
        "¿Qué equipo básico necesito para iniciarme en la pesca con caña?",
        "Explica cómo identificar las mejores zonas para la caza menor",
        "¿Qué factores climáticos influyen más en el comportamiento de los peces?"
    ]
    
    print("🎣 Análisis de Motores de IA para Mejorar Habilidades de Pesca y Caza")
    print("=" * 60)
    
    # Descargar motores
    for motor in motores:
        descargar_motor_ollama(motor)
    
    # Probar cada motor con cada pregunta
    resultados = []
    for motor in motores:
        print(f"\n🔍 Probando motor: {motor}")
        print("-" * 40)
        
        for pregunta in preguntas:
            print(f"Pregunta: {pregunta[:50]}...")
            resultado = probar_motor(motor, pregunta)
            resultados.append(resultado)
            
            if resultado["exitoso"]:
                print(f"✅ Respuesta obtenida en {resultado['tiempo_respuesta']}s")
            else:
                print(f"❌ Error: {resultado['respuesta']}")
    
    # Generar informe de resultados
    print("\n📊 INFORME DE RESULTADOS")
    print("=" * 60)
    
    for motor in motores:
        resultados_motor = [r for r in resultados if r["motor"] == motor]
        exitosos = [r for r in resultados_motor if r["exitoso"]]
        tiempo_promedio = sum(r["tiempo_respuesta"] for r in exitosos) / len(exitosos) if exitosos else 0
        
        print(f"\n🤖 Motor: {motor}")
        print(f"   Preguntas respondidas: {len(exitosos)}/{len(resultados_motor)}")
        print(f"   Tiempo promedio de respuesta: {tiempo_promedio:.2f}s")
        
        # Mostrar la mejor respuesta
        if exitosos:
            mejor_respuesta = max(exitosos, key=lambda x: len(x["respuesta"]))
            print(f"   Mejor respuesta (más completa):")
            print(f"   Pregunta: {mejor_respuesta['pregunta']}")
            print(f"   Respuesta: {mejor_respuesta['respuesta'][:100]}...")
    
    # Guardar resultados en archivo JSON
    with open("resultados_motores_ia.json", "w", encoding="utf-8") as f:
        json.dump(resultados, f, ensure_ascii=False, indent=2)
    
    print(f"\n💾 Resultados guardados en 'resultados_motores_ia.json'")
    print("\n🎯 Recomendación: Elige el motor con mejores respuestas y menor tiempo de respuesta")

if __name__ == "__main__":
    main()