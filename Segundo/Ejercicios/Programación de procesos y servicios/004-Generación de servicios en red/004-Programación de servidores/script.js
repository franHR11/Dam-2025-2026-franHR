// Función para obtener la fecha actual y mantenerla actualizada
function obtenerFecha() {
    const ahora = new Date();
    // Formateamos para que se vea bien: DD/MM/AAAA HH:MM:SS
    const opciones = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
    const fechaString = ahora.toLocaleDateString('es-ES', opciones);

    const elementoFecha = document.getElementById('fecha');
    if (elementoFecha) {
        elementoFecha.textContent = fechaString;
    }
}

// Inicialización de datos de prueba si no existen (para que el ejercicio funcione a la primera)
function inicializarDatosPrueba() {
    if (!localStorage.getItem('título')) {
        localStorage.setItem('título', 'Desarrollo de Aplicaciones Web');
        localStorage.setItem('descripcion', 'Práctica de manipulación del DOM y Storage.');
    }
}

// Función para mostrar la información de la sesión
function obtenerSesion() {
    // Aseguramos que haya datos
    inicializarDatosPrueba();

    // Obtenemos los datos de la sesión desde localStorage
    const título = localStorage.getItem('título');
    const descripcion = localStorage.getItem('descripcion');

    const elTitulo = document.getElementById('título');
    const elDesc = document.getElementById('descripcion');

    if (elTitulo) elTitulo.textContent = título;
    if (elDesc) elDesc.textContent = descripcion;
}

// Función extra para hacer los hobbies dinámicos (Punto extra del ejercicio)
function cargarHobbiesDinamicos() {
    const lista = document.getElementById('lista-hobbies');
    if (!lista) return;

    // Array de hobbies (simulando una base de datos o JSON)
    const misHobbies = [
        "Pesca (Spinning)",
        "Caza Fotográfica",
        "Senderismo de Montaña",
        "Bicicleta de Montaña (MTB)",
        "Motocross"
    ];

    // Limpiamos la lista estática original para demostrar control total del DOM
    lista.innerHTML = '';

    misHobbies.forEach(hobby => {
        const li = document.createElement('li');
        li.textContent = hobby;
        li.classList.add('dinamico'); // Clase CSS añadida dinámicamente
        lista.appendChild(li);
    });
}

// Llamada a las funciones
obtenerFecha();
obtenerSesion();
cargarHobbiesDinamicos();

// Actualizamos el reloj cada segundo para darle "vida" a la página
setInterval(obtenerFecha, 1000);
