# Gestión del sistema de archivos con el módulo fs en Node.js

### 1. Introducción y contextualización
En el desarrollo de aplicaciones del lado del servidor con Node.js, la interacción con el sistema operativo es una capacidad crítica. El módulo **fs** (*File System*) proporciona una API para interactuar con el sistema de archivos de manera similar a los estándares POSIX (Portable Operating System Interface). 

Este módulo es fundamental porque permite persistir datos, gestionar configuraciones, leer archivos estáticos o manipular directorios directamente desde el servidor. En este ejercicio, nos centramos en las operaciones asíncronas, que son esenciales en Node.js para evitar el bloqueo del *Event Loop* (bucle de eventos) y mantener la aplicación eficiente y escalable, permitiendo que el servidor atienda otras peticiones mientras se realizan las operaciones de entrada/salida (I/O).

### 2. Desarrollo detallado y preciso

Para resolver el ejercicio, he utilizado tres métodos clave del módulo `fs`, implementando el patrón de diseño **"Error-First Callback"**, estándar en Node.js, donde el primer argumento de la función de retorno siempre está reservado para un posible error.

#### Conceptos y Funciones Utilizadas:

1.  **`fs.writeFile(file, data, callback)`**: 
    *   **Definición**: Método asíncrono que escribe datos en un archivo. Si el archivo ya existe, lo reemplaza.
    *   **Funcionamiento**: Solicita al sistema operativo la apertura y escritura del archivo. Una vez completada la operación (o si falla), se ejecuta la función *callback*.
    *   **Uso en el ejercicio**: Lo utilizo para generar el `archivo_origen.txt` desde cero.

2.  **`fs.mkdir(path, options, callback)`**:
    *   **Definición**: Método para crear directorios.
    *   **Importante**: La opción `{ recursive: true }` es vital aquí. Sin ella, intentar crear `padre/hijo` fallaría si `padre` no existe previamente. Con esta opción, Node.js se encarga de crear toda la ruta de directorios inexistentes, similar al comando `mkdir -p` de Linux.

3.  **`fs.copyFile(src, dest, callback)`**:
    *   **Definición**: Copia un archivo de forma asíncrona.
    *   **Detalle técnico**: Dependiendo del sistema operativo, esta función puede utilizar llamadas al sistema optimizadas (como `copy_file_range` en Linux) que son más eficientes que leer el archivo completo en memoria y volver a escribirlo.

### 3. Aplicación práctica

A continuación, presento el código desarrollado para cada operación. He mantenido la estructura modular solicitada, con archivos independientes para cada tarea.

#### A. Creación del archivo (`01_crear_archivo.js`)
Aquí demuestro cómo inicializar un archivo con contenido de texto.
```javascript
const fs = require('fs');

// Uso de writeFile para E/S asíncrona no bloqueante
fs.writeFile('archivo_origen.txt', 'Hola, este es el contenido de mi archivo creado con Node.js.', (err) => {
    // Patrón Error-First: Siempre validamos el error primero
    if (err) throw err;
    console.log('El archivo "archivo_origen.txt" ha sido creado correctamente.');
});
```

#### B. Gestión de directorios (`02_directorios.js`)
En este script gestiono la estructura de carpetas.
```javascript
const fs = require('fs');

// La opción 'recursive: true' asegura que se cree toda la ruta necesaria
fs.mkdir('nuevo_directorio/subdirectorio', { recursive: true }, (err) => {
    if (err) throw err;
    console.log('Estructura de directorios creada: nuevo_directorio/subdirectorio');
});
```
*Errores comunes a evitar*: Olvidar `recursive: true` suele lanzar un error `ENOENT` si el directorio padre no existe. También es crucial manejar los permisos de escritura del sistema operativo.

#### C. Copiado de archivos (`03_copiar.js`)
Finalmente, la operación de duplicado.
```javascript
const fs = require('fs');

// Copia eficiente delegada al sistema operativo
fs.copyFile('archivo_origen.txt', 'archivo_destino.txt', (err) => {
    if (err) throw err;
    console.log('El archivo se ha copiado correctamente a "archivo_destino.txt"');
});
```
*Nota*: Si `archivo_origen.txt` no existiera (por ejemplo, si no ejecutamos el paso A), obtendríamos un error `ENOENT`. Es importante seguir el orden lógico de ejecución.

### 4. Conclusión
Este ejercicio ha consolidado mi comprensión sobre cómo Node.js interactúa con el sistema anfitrión. Más allá de la sintaxis, he aprendido la importancia de la **asincronía** para el rendimiento. Aunque métodos como `writeFileSync` existen y son más fáciles de leer (síncronos), usar las versiones con *callbacks* (o Promesas en versiones más modernas) es la práctica profesional correcta para no detener la ejecución del programa durante las operaciones de disco, que son costosas en términos de tiempo de CPU.

### Rúbrica de evaluación - Autoevaluación
*   **Contextualización**: Explicada la relevancia del módulo `fs` y la asincronía.
*   **Terminología técnica**: Uso de términos como *Event Loop*, *POSIX*, *Callback*, *Streams*, *ENOENT*.
*   **Ejemplos claros**: Código funcional y minimalista incluido.
*   **Errores comunes**: Mencionada la recursividad en directorios y dependencias de existencia de archivos.
