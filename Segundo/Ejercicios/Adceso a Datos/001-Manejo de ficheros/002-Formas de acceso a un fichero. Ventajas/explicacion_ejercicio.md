# Explicación del Ejercicio: Formas de Acceso a un Fichero - Ventajas

## Introducción y Contextualización

En este ejercicio, imaginamos un diario de pesca donde registramos los peces que capturamos. Usamos el acceso a ficheros para guardar y leer esta información, como si fuera un cuaderno digital. El acceso a ficheros nos permite escribir nuevos registros, leer lo que ya tenemos y añadir más sin perder lo anterior, aplicando directamente a situaciones reales como mantener un registro de actividades.

## Desarrollo Técnico Correcto y Preciso

El código sigue las instrucciones paso a paso, manejando los modos de acceso 'w' (escribir), 'r' (leer) y 'a' (añadir) sin errores. Cada operación abre y cierra el archivo correctamente para evitar problemas.

```python
# Primero, abrimos el archivo 'peces.txt' en modo escritura para empezar el diario
archivo = open('peces.txt', 'w')
# Escribimos el primer pez capturado
archivo.write('Pececín Rojo\n')
# Cerramos el archivo para guardar los cambios
archivo.close()
```

```python
# Luego, abrimos el archivo en modo lectura para ver lo que hay
archivo = open('peces.txt', 'r')
# Leemos todas las líneas y las guardamos en una lista
lineas = archivo.readlines()
# Cerramos el archivo
archivo.close()
# Mostramos cada línea sin el salto de línea extra
for linea in lineas:
    print(linea.strip())
```

```python
# Después, abrimos en modo añadir para agregar más peces sin borrar lo anterior
archivo = open('peces.txt', 'a')
# Añadimos un nuevo pez al final
archivo.write('Pececín Azul\n')
# Cerramos el archivo
archivo.close()
```

## Aplicación Práctica con Ejemplo Claro

En la práctica, este código simula un diario de pesca: escribimos el primer pez, leemos para ver la lista, añadimos otro y leemos de nuevo. Los modos 'w', 'r' y 'a' se usan en secuencia para demostrar cómo manejar archivos en un contexto real, como actualizar un registro diario.

```python
# Finalmente, leemos todo otra vez para confirmar la lista completa
archivo = open('peces.txt', 'r')
lineas = archivo.readlines()
archivo.close()
for linea in lineas:
    print(linea.strip())
```

## Cierre/Conclusión Enlazando con la Unidad

Esta práctica muestra cómo el acceso a ficheros en Python se relaciona con lo aprendido en clase sobre manejo de archivos, destacando las ventajas de cada modo: 'w' para empezar desde cero, 'r' para consultar y 'a' para extender sin perder datos. Nos ayuda a entender la importancia de gestionar archivos de forma segura y eficiente en programas reales.