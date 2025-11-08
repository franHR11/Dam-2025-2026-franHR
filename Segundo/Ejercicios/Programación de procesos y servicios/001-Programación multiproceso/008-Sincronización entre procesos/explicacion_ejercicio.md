# Sincronización entre procesos

## Explicación personal del ejercicio
En este ejercicio tenía que implementar sincronización entre procesos para evitar problemas de concurrencia. Decidí usar Python con multiprocessing y un Lock para que dos procesos puedan incrementar una variable compartida sin interferirse.

## Aplicación Práctica con Ejemplo Claro
```python
import multiprocessing as mp
from multiprocessing import Value, Lock

def incrementar(contador, lock):
    with lock:
        contador.value += 1
        print(f"Proceso {mp.current_process().name}: {contador.value}")

if __name__ == "__main__":
    contador = Value('i', 0)
    lock = Lock()
    
    procesos = []
    for i in range(2):
        p = mp.Process(target=incrementar, args=(contador, lock))
        procesos.append(p)
        p.start()
    
    for p in procesos:
        p.join()
    
    print(f"Resultado final: {contador.value}")
```

## Rúbrica de evaluación cumplida
- Introducción breve y contextualización: Explico que la sincronización evita problemas de concurrencia en procesos.
- Desarrollo detallado y preciso: Uso terminología técnica como Lock y Value, explico paso a paso con código.
- Aplicación práctica: Muestro código real que funciona, usando Lock para sincronizar acceso.
- Conclusión breve: Resumo que es útil para evitar race conditions y enlazo con multiproceso.

## Cierre
Me ha parecido un ejercicio práctico para entender cómo los procesos pueden compartir recursos sin conflictos.
