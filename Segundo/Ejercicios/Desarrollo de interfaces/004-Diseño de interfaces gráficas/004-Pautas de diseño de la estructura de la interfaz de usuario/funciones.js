// Variable global para almacenar los estudiantes
var estudiantes = [];

// Función para agregar un nuevo estudiante a la lista y actualizar la tabla
function agregarEstudiante() {
    // 1. Obtener referencias a los elementos del DOM
    var nombreInput = document.getElementById('nombre');
    var apellidosInput = document.getElementById('apellidos');
    var edadInput = document.getElementById('edad');
    var mensajeDiv = document.getElementById('mensaje');
    var tablaCuerpo = document.querySelector('#tabla-estudiantes tbody');

    // 2. Obtener los valores de los inputs
    var nombre = nombreInput.value.trim();
    var apellidos = apellidosInput.value.trim();
    var edad = edadInput.value.trim();

    // 3. Validar que los campos no estén vacíos
    if (nombre === "" || apellidos === "" || edad === "") {
        mostrarMensaje("Por favor, rellena todos los campos correctamente.", "error");
        return; // Detener la ejecución si hay error
    }

    // 4. Crear el objeto estudiante (Estructura de datos)
    var nuevoEstudiante = {
        nombre: nombre,
        apellidos: apellidos,
        edad: parseInt(edad)
    };

    // 5. Añadir a la variable global
    estudiantes.push(nuevoEstudiante);

    // 6. Actualizar la tabla
    var fila = document.createElement('tr');

    // Celda Nombre
    var celdaNombre = document.createElement('td');
    celdaNombre.textContent = nuevoEstudiante.nombre;
    fila.appendChild(celdaNombre);

    // Celda Apellidos
    var celdaApellidos = document.createElement('td');
    celdaApellidos.textContent = nuevoEstudiante.apellidos;
    fila.appendChild(celdaApellidos);

    // Celda Edad
    var celdaEdad = document.createElement('td');
    celdaEdad.textContent = nuevoEstudiante.edad;
    fila.appendChild(celdaEdad);

    // Añadir la fila al cuerpo de la tabla
    tablaCuerpo.appendChild(fila);

    // 7. Limpiar el formulario
    nombreInput.value = "";
    apellidosInput.value = "";
    edadInput.value = "";

    // 8. Mostrar mensaje de éxito
    mostrarMensaje("Estudiante " + nuevoEstudiante.nombre + " agregado exitosamente.", "exito");
}

// Función auxiliar para mostrar mensajes de estado
function mostrarMensaje(texto, tipo) {
    var mensajeDiv = document.getElementById('mensaje');
    mensajeDiv.textContent = texto;
    mensajeDiv.style.display = 'block';

    // Resetear clases
    mensajeDiv.className = '';

    // Añadir clase según el tipo (exito o error) para el CSS
    mensajeDiv.classList.add(tipo);

    // Ocultar el mensaje después de 3 segundos
    setTimeout(function () {
        mensajeDiv.style.display = 'none';
    }, 3000);
}
