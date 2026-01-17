# Explicación del Ejercicio: Automatización de Matrices Rectangulares

## 1.- Introducción breve y contextualización
Para este ejercicio, he decidido profundizar en la creación de **matrices rectangulares**, una técnica fundamental en el diseño asistido por ordenador (CAD) con herramientas como Rhino. Una matriz rectangular no es más que la copia sistémica de un objeto original organizada en filas, columnas y niveles.

El objetivo de este ejercicio es entender qué ocurre realmente "bajo el capó" cuando usamos el comando `MatrizRectangular` en Rhino. A menudo hacemos clic y ya está, pero entender la lógica matemática detrás de esta automatización nos permite crear scripts mucho más potentes y personalizados en el futuro. He querido desglosar este proceso automático para comprender cómo las coordenadas se incrementan de forma aritmética.

## 2.- Desarrollo detallado y preciso
Para automatizar la creación de una matriz 3D, he tenido que aplicar conceptos de **geometría cartesiana** y **lógica de programación estructurada**.

### Conceptos Clave
1.  **Origen de Coordenadas (x, y, z)**: Es el punto de partida. En Rhino, cada objeto tiene una ubicación en el espacio.
2.  **Intervalo (Offset)**: Es la distancia constante entre un objeto y su copia. No basta con saber "cuántos" objetos queremos, sino "a qué distancia" están.
3.  **Iteración (Bucles)**: La automatización se basa en repetir una acción. En una matriz 3D, la repetición no es lineal, es tridimensional.

### Lógica del Algoritmo
He diseñado el algoritmo basándome en **tres bucles anidados**, que funcionan como un reloj mecánico donde una rueda mueve a la siguiente:

*   **Bucle Externo (Eje Z - Altura)**: Controla cuántos "pisos" o niveles tiene nuestra matriz. Por cada iteración de este bucle, subimos en altura.
*   **Bucle Intermedio (Eje Y - Profundidad)**: Dentro de cada piso, controla cuántas filas de objetos hay hacia el fondo.
*   **Bucle Interno (Eje X - Anchura)**: Es el más rápido. Dentro de cada fila, coloca los objetos uno al lado del otro horizontalmente.

### Fórmula Matemática
Para calcular la posición exacta de cada "bloque" o ficha sin hacerlo a mano, he usado la siguiente fórmula en mi código:
`Coordenada_Final = Coordenada_Origen + (Índice_del_Bucle * Intervalo)`

Por ejemplo, si el intervalo en X es 10 metros:
- Objeto 1 (índice 0): `0 + (0 * 10) = 0`
- Objeto 2 (índice 1): `0 + (1 * 10) = 10`
- Objeto 3 (índice 2): `0 + (2 * 10) = 20`

Esta fórmula simple es el corazón de la automatización en Rhino.

## 3.- Aplicación práctica
He implementado un script en Python llamado `generar_matriz.py` que simula este motor de generación. En lugar de crear geometría visual (que requeriría librerías pesadas), mi script genera la **data de construcción**: las coordenadas precisas donde Rhino colocaría cada objeto.

### Código Desarrollado
El código define una función `crear_matriz_rectangular` que acepta el origen, el número de repeticiones por eje y las distancias.

```python
def crear_matriz_rectangular(origen, repeticiones_x, repeticiones_y, repeticiones_z, intervalo_x, intervalo_y, intervalo_z):
    # ... (código completo en el archivo adjunto)
    # Bucle anidado para recorrer las 3 dimensiones
    for z in range(repeticiones_z):
        pos_z = origen[2] + (z * intervalo_z)
        for y in range(repeticiones_y):
            pos_y = origen[1] + (y * intervalo_y)
            for x in range(repeticiones_x):
                pos_x = origen[0] + (x * intervalo_x)
                # ...
```

### Ejecución y Resultados
Al ejecutar el script, obtengo una lista ordenada de instrucciones de creación. Por ejemplo, para una matriz de 3x2x2, el programa calcula instantáneamente las 12 posiciones (3*2*2=12) necesarias. Esto demuestra cómo un script puede ahorrar minutos de trabajo manual repetitivo, eliminando además el error humano al colocar objetos a distancias exactas.

## 4.- Conclusión breve
Realizar este ejercicio me ha enseñado que la **automatización** no es "magia", sino la aplicación estricta de reglas matemáticas simples en bucles. He aprendido a descomponer un problema visual (una matriz de cajas) en un problema numérico (listas de coordenadas).

Esta práctica es muy útil no solo para usar Rhino más eficientemente, sino para cualquier tarea de Acceso a Datos donde necesitemos generar o leer estructuras de información repetitivas en múltiples dimensiones. La lógica que he aplicado aquí es la misma que usaría para recorrer una base de datos cúbica o un array multidimensional.
