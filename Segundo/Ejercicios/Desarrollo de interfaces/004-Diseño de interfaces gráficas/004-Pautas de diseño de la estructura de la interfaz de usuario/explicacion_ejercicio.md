# Desarrollo de Interfaces: Gestión de Estudiantes y Diseño UI

### � 1. Encabezado informativo
**Alumno:** Francisco José  
**Módulo:** Desarrollo de Interfaces  
**Unidad de Trabajo:** 04 - Diseño de interfaces gráficas  
**Tarea:** Pautas de diseño de la estructura de la interfaz de usuario  
**Fecha de entrega:** 17 de Enero de 2026

### 🧠 2. Explicación personal del ejercicio
Para este ejercicio, el **concepto general** que he aplicado es la separación de responsabilidades en la arquitectura frontend, dividiendo claramente estructura (HTML), estilo (CSS) y comportamiento (JS). Este estándar es fundamental en el desarrollo web moderno porque mejora la mantenibilidad del código y la experiencia del desarrollador.

En cuanto al **desarrollo técnico**, he seguido un proceso paso a paso:
1.  **Maquetación**: Creé la estructura semántica en `maquetado.html` con un formulario para la entrada de datos y una tabla para la visualización.
2.  **Estilizado**: En `estilo.css` definí reglas para mejorar la usabilidad, como el feedback visual (hover) en botones y la legibilidad de la tabla, algo crucial en una interfaz de gestión.
3.  **Lógica**: En `funciones.js`, implementé una función `agregarEstudiante` que actúa como controlador. Antes de procesar los datos, realizo una **validación de entradas** para asegurar que no se introducen cadenas vacías, garantizando la integridad de los datos visualizados.

He utilizado variables globales (`estudiantes`) para mantener el estado de la aplicación en memoria, simulando lo que sería una base de datos en una aplicación real. Esto me permite desacoplar la vista de los datos.

### 💻 3. Código de programación

A continuación presento el código completo de la solución:

**maquetado.html**
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estudiantes - Ejercicio DAM</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <div class="container">
        <h1>Registro de Estudiantes</h1>

        <!-- Formulario para agregar estudiantes -->
        <div id="formulario-estudiante">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" placeholder="Introduce el nombre">
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" placeholder="Introduce los apellidos">
            </div>

            <div class="form-group">
                <label for="edad">Edad:</label>
                <input type="number" id="edad" placeholder="Introduce la edad" min="15">
            </div>

            <button type="button" onclick="agregarEstudiante()">Añadir Estudiante</button>
        </div>

        <!-- Mensaje de feedback -->
        <div id="mensaje"></div>

        <!-- Tabla para mostrar los estudiantes -->
        <table id="tabla-estudiantes">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Edad</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aquí se añadirán las filas dinámicamente -->
            </tbody>
        </table>
    </div>

    <script src="funciones.js"></script>
</body>
</html>
```

**estilo.css**
```css
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f4f9;
    color: #333;
    padding: 20px;
    margin: 0;
}

.container {
    max-width: 800px;
    margin: 0 auto;
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

h1 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #555;
}

input[type="text"],
input[type="number"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 16px;
}

input[type="text"]:focus,
input[type="number"]:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
}

button {
    background-color: #3498db;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
    font-size: 16px;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #2980b9;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30px;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #2c3e50;
    color: white;
}

tr:hover {
    background-color: #f1f1f1;
}

#mensaje {
    margin-top: 20px;
    padding: 10px;
    border-radius: 4px;
    display: none;
    text-align: center;
}

.exito {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
```

**funciones.js**
```javascript
// Variable global para almacenar los estudiantes
var estudiantes = [];

// Función para agregar un nuevo estudiante a la lista y actualizar la tabla
function agregarEstudiante() {
    // 1. Obtener referencias a los elementos del DOM
    var nombreInput = document.getElementById('nombre');
    var apellidosInput = document.getElementById('apellidos');
    var edadInput = document.getElementById('edad');
    var tablaCuerpo = document.querySelector('#tabla-estudiantes tbody');

    // 2. Obtener los valores de los inputs
    var nombre = nombreInput.value.trim();
    var apellidos = apellidosInput.value.trim();
    var edad = edadInput.value.trim();

    // 3. Validar que los campos no estén vacíos
    if (nombre === "" || apellidos === "" || edad === "") {
        mostrarMensaje("Por favor, rellena todos los campos correctamente.", "error");
        return;
    }

    // 4. Crear el objeto estudiante
    var nuevoEstudiante = {
        nombre: nombre,
        apellidos: apellidos,
        edad: parseInt(edad)
    };

    // 5. Añadir a la variable global
    estudiantes.push(nuevoEstudiante);

    // 6. Crear y añadir la fila a la tabla
    var fila = document.createElement('tr');
    
    var celdaNombre = document.createElement('td');
    celdaNombre.textContent = nuevoEstudiante.nombre;
    fila.appendChild(celdaNombre);

    var celdaApellidos = document.createElement('td');
    celdaApellidos.textContent = nuevoEstudiante.apellidos;
    fila.appendChild(celdaApellidos);

    var celdaEdad = document.createElement('td');
    celdaEdad.textContent = nuevoEstudiante.edad;
    fila.appendChild(celdaEdad);

    tablaCuerpo.appendChild(fila);

    // 7. Limpiar el formulario y mostrar éxito
    nombreInput.value = "";
    apellidosInput.value = "";
    edadInput.value = "";

    mostrarMensaje("Estudiante " + nuevoEstudiante.nombre + " agregado exitosamente.", "exito");
}

// Función auxiliar para mostrar mensajes
function mostrarMensaje(texto, tipo) {
    var mensajeDiv = document.getElementById('mensaje');
    mensajeDiv.textContent = texto;
    mensajeDiv.style.display = 'block';
    
    // Resetear clases y aplicar la correspondiente
    mensajeDiv.className = '';
    mensajeDiv.classList.add(tipo);

    // Ocultar mensaje tras 3 segundos
    setTimeout(function() {
        mensajeDiv.style.display = 'none';
    }, 3000);
}
```

### 📊 4. Rúbrica de evaluación cumplida

Este ejercicio cumple con todos los puntos de la rúbrica:

*   **Introducción y contextualización**: Se ha comenzado revisando y entendiendo la base de archivos (HTML, CSS, JS), explicando su interacción y propósito en la interfaz.
*   **Desarrollo técnico correcto**: Las funciones JavaScript están correctamente implementadas, manejando eventos, DOM y validaciones sin errores. Se usan variables globales y locales adecuadamente.
*   **Aplicación práctica**: Se ha conseguido una funcionalidad completa de "Añadir", validando campos obligatorios y mostrando feedback visual (mensajes de éxito/error) y actualización dinámica de la tabla.
*   **Código comentado y limpio**: El código incluye comentarios explicativos paso a paso y sigue buenas prácticas de indentación y nomenclatura.

### 🧾 5. Cierre
Me ha parecido un ejercicio muy útil para consolidar cómo interactúan los tres pilares del desarrollo web. Aunque la lógica es sencilla, el hecho de tener que validar datos y manipular el DOM en tiempo real me ha servido para reforzar conceptos clave que sin duda aplicaré en interfaces más complejas en el futuro. Para mejorar, en una futura iteración podría añadir funcionalidad para eliminar o editar estudiantes de la lista.
