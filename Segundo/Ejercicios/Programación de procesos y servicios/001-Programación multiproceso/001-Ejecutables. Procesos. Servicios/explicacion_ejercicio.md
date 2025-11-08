# 🧠 Ejercicio: Simulación de un bucle con pesca

## 🧩 1. Encabezado informativo
- **Asignatura:** Programación de procesos y servicios
- **Tema:** Programación multiproceso
- **Ejercicio:** Simulación de un bucle con pesca
- **Fecha:** 10/10/2025
- **Alumno:** Fran

## 🧠 2. Explicación personal del ejercicio

Un bucle es una estructura de programación que repite instrucciones de forma automática hasta cumplir una condición específica. Sirve para automatizar tareas repetitivas sin tener que escribir el mismo código múltiples veces.

En este ejercicio tenía que crear un programa que simule el proceso de pesca utilizando un bucle for. La idea era contar cuántos peces voy capturando hasta alcanzar un objetivo determinado. Decidí hacerlo con el mínimo código posible usando las variables exactas que me pedían: peces_capturados y objetivo. El bucle se encarga de incrementar automáticamente el contador hasta llegar a la meta.

## 💻 3. Código de programación

```python
# Definir variables
peces_capturados = 0
objetivo = 10

# Crear bucle para simular pesca
for _ in range(objetivo):
    peces_capturados += 1

# Imprimir resultado
print(f"¡He capturado {peces_capturados} peces!")
```


## 🧾 5. Cierre

Me ha parecido un ejercicio sencillo pero útil para practicar bucles for y entender cómo funcionan los procesos repetitivos en programación. La relación con la pesca hace que sea más fácil visualizar cómo un bucle puede simular tareas cotidianas hasta alcanzar una meta específica.