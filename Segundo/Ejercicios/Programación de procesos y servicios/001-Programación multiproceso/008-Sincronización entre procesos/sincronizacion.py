import multiprocessing as mp  # Importar módulo para procesos
from multiprocessing import Value, Lock  # Value para variable compartida, Lock para sincronización

def incrementar(contador, lock):
    # Usar lock para sincronizar el acceso
    with lock:
        contador.value += 1  # Incrementar la variable compartida
        print(f"Proceso {mp.current_process().name}: {contador.value}")  # Imprimir el valor actual

if __name__ == "__main__":
    contador = Value('i', 0)  # Variable compartida de tipo entero inicializada en 0
    lock = Lock()  # Crear un lock para sincronización
    
    procesos = []  # Lista para almacenar procesos
    for i in range(2):  # Crear 2 procesos
        p = mp.Process(target=incrementar, args=(contador, lock))  # Crear proceso con función y argumentos
        procesos.append(p)  # Agregar a la lista
        p.start()  # Iniciar el proceso
    
    for p in procesos:
        p.join()  # Esperar a que terminen
    
    print(f"Resultado final: {contador.value}")  # Mostrar resultado final
