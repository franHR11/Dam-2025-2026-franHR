import multiprocessing as mp
import time

# Función que cuenta palabras en un texto
def contar_palabras(texto):
    palabras = texto.split()
    return len(palabras)

# Función principal
if __name__ == "__main__":
    # Lista de textos simulados (como si fueran archivos)
    textos = [
        "Este es un texto largo de ejemplo para demostrar el conteo de palabras en paralelo. " * 1000,
        "Otro texto extenso que contiene muchas palabras repetidas para probar la eficiencia. " * 1000,
        "Tercer texto con contenido variado y diferente para asegurar el procesamiento correcto. " * 1000,
        "Cuarto y último texto que completa el conjunto de datos a procesar simultáneamente. " * 1000
    ]
    
    # Procesamiento secuencial para comparación
    inicio_secuencial = time.time()
    total_secuencial = sum(contar_palabras(texto) for texto in textos)
    fin_secuencial = time.time()
    print(f"Secuencial: {total_secuencial} palabras en {fin_secuencial - inicio_secuencial:.2f} segundos")
    
    # Procesamiento paralelo
    inicio_paralelo = time.time()
    with mp.Pool(processes=mp.cpu_count()) as pool:
        resultados = pool.map(contar_palabras, textos)
    total_paralelo = sum(resultados)
    fin_paralelo = time.time()
    print(f"Paralelo: {total_paralelo} palabras en {fin_paralelo - inicio_paralelo:.2f} segundos usando {mp.cpu_count()} núcleos")
