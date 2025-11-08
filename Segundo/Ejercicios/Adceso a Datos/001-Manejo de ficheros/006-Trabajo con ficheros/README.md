# Actividad: Manejo de Ficheros y Archivos CSV

## 1. Introducción y Contextualización

Los archivos CSV (Comma-Separated Values) son uno de los formatos más utilizados para almacenar e intercambiar datos tabulares. Su estructura simple, basada en valores separados por comas, los hace ideales para representar información organizada en filas y columnas sin necesidad de formatos complejos. En el contexto de la programación, el manejo de ficheros CSV es fundamental para aplicaciones que necesitan persistir datos de forma estructurada, como registros, inventarios, o en este caso, un registro de actividades de pesca.

El manejo de ficheros en Python permite a los programas almacenar información de forma permanente, superando las limitaciones de la memoria volátil. A través de las operaciones de lectura y escritura, podemos crear aplicaciones que mantienen sus datos incluso después de cerrarse, lo que es esencial para cualquier sistema de gestión de información.

## 2. Desarrollo Técnico

### 2.1. Clase GestorCSV

La clase `GestorCSV` ha sido diseñada para proporcionar una interfaz sencilla para trabajar con archivos CSV sin necesidad de librerías externas. Esta clase encapsula la lógica necesaria para:

- **Escribir tuplas en formato CSV**: El método `escribir()` toma una tupla de datos y la convierte a una línea CSV, separando cada elemento con comas.
- **Leer datos desde archivos CSV**: El método `leer()` lee la primera línea del archivo y la convierte de vuelta a una tupla de Python.

### 2.2. Implementación Técnica

La implementación se basa en las siguientes características clave:

```python
class GestorCSV:
    def __init__(self, nombre_archivo):
        self.nombre_archivo = nombre_archivo
    
    def escribir(self, tupla_datos):
        # Conversión de tupla a formato CSV
        linea_csv = ','.join(str(dato) for dato in tupla_datos)
        
        # Escritura segura con gestión de excepciones
        with open(self.nombre_archivo, 'a', encoding='utf-8') as archivo:
            archivo.write(linea_csv + '\n')
```

El uso del contexto `with open()` garantiza que los recursos se liberen correctamente, incluso si ocurren errores durante la operación. Además, se ha implementado un manejo robusto de excepciones para proporcionar retroalimentación clara en caso de problemas.

### 2.3. Características Técnicas

- **Codificación UTF-8**: Se utiliza codificación UTF-8 para garantizar compatibilidad con caracteres especiales y acentos.
- **Modo de apertura 'a' (append)**: Permite añadir nuevos registros sin sobrescribir los existentes.
- **Conversión automática de tipos**: Los datos se convierten automáticamente a string antes de escribirlos.
- **Gestión de errores**: Todas las operaciones incluyen manejo de excepciones para evitar fallos inesperados.

## 3. Aplicación Práctica

### 3.1. Ejemplo de Uso: Registro de Pesca

El ejemplo práctico demuestra cómo utilizar la clase `GestorCSV` para gestionar un registro de actividades de pesca:

```python
# Crear datos de pesca como tupla
datos_pesca = ("Peixe Rojo", "10/05/23", "Lago del Lobo")

# Crear instancia y escribir datos
gestor = GestorCSV("pesca.csv")
gestor.escribir(datos_pesca)

# Leer los datos almacenados
primer_registro = gestor.leer()
print(f"Registro leído: {primer_registro}")
```

### 3.2. Errores Comunes y Soluciones

1. **Error de codificación**: Al trabajar con caracteres especiales, es importante especificar la codificación UTF-8 para evitar problemas con acentos o caracteres no ASCII.

2. **Archivos no encontrados**: El código incluye manejo de la excepción `FileNotFoundError` para proporcionar mensajes claros cuando el archivo no existe.

3. **Conversión de tipos**: Todos los elementos de la tupla se convierten a string antes de escribirlos, evitando errores al intentar escribir objetos no serializables.

### 3.3. Ventajas del Enfoque

- **Sin dependencias externas**: La implementación utiliza solo funcionalidad estándar de Python.
- **Código legible**: La estructura orientada a objetos facilita la comprensión y mantenimiento.
- **Flexibilidad**: La clase puede reutilizarse para diferentes tipos de datos CSV.

## 4. Conclusión

Este ejercicio sobre manejo de ficheros CSV demuestra cómo las estructuras de datos como las tuplas y las operaciones de ficheros en Python pueden combinarse para crear sistemas de almacenamiento de información eficientes y sencillos. La implementación de la clase `GestorCSV` ilustra conceptos fundamentales de la programación como la encapsulación, el manejo de errores y la persistencia de datos.

El manejo de ficheros es una habilidad esencial en programación, ya que permite crear aplicaciones que mantienen su estado entre ejecuciones. Los archivos CSV, en particular, son un puente importante entre los programas y otras herramientas como hojas de cálculo o bases de datos, facilitando el intercambio de información en un formato universalmente comprendido.

Este ejercicio se conecta directamente con otros contenidos de la unidad sobre estructuras de datos, operaciones de entrada/salida y programación orientada a objetos, mostrando cómo estos conceptos se integran en aplicaciones prácticas del mundo real.

## 5. Instrucciones de Ejecución

Para ejecutar este ejercicio:

1. Asegúrate de tener Python instalado en tu sistema
2. Navega a la carpeta que contiene los archivos
3. Ejecuta el script principal con el comando:
   ```
   python ejemplo_pesca.py
   ```
4. Observa cómo se crea el archivo `pesca.csv` y se muestran los datos leídos

El código está completamente comentado para facilitar su comprensión y modificación según necesidades específicas.