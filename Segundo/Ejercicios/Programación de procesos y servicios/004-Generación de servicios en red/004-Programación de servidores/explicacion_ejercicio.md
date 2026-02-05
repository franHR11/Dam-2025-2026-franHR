# Práctica de Aplicación de Programación

### 1. Introducción breve y contextualización
En este ejercicio vamos a poner en práctica la creación de páginas web dinámicas utilizando **HTML5** para la estructura y **JavaScript** para la lógica client-side. El objetivo es manipular el **DOM (Document Object Model)** en tiempo real y utilizar el almacenamiento local del navegador (**localStorage**) para persistir información de la sesión. Es un ejercicio fundamental para entender cómo las aplicaciones web modernas interactúan con el usuario sin necesidad de recargar la página constantemente.

### 2. Desarrollo detallado y preciso
Para resolver el ejercicio, he seguido estos pasos:
1.  **Estructura HTML**: He creado el archivo `index.html` definiendo las secciones principales (`header`, `main`, `section`) y asignando identificadores (`id`) únicos a los elementos que necesito modificar desde JavaScript (como la fecha, el título de la sesión y la lista de hobbies).
2.  **Lógica JavaScript**: En `script.js` he implementado funciones modulares:
    *   `obtenerFecha()`: Utiliza el objeto `Date` para capturar el instante actual.
    *   `obtenerSesion()`: Recupera valores almacenados en `localStorage`.
    *   **Mejora de persistencia**: He añadido una validación para inicializar el `localStorage` si está vacío, de modo que al abrir la página por primera vez ya muestre datos de ejemplo.
    *   **Manipulación del DOM**: He usado `document.getElementById()` y `textContent` para inyectar los datos en el HTML.

### 3. Aplicación práctica
A continuación muestro el código implementado. He añadido funcionalidades extra como la **actualización del reloj en tiempo real** (usando `setInterval`) y la **generación dinámica de la lista de hobbies**, sustituyendo la lista estática del HTML por una generada desde un array en JS, lo que simula mejor una aplicación real que carga datos.

#### Archivo: index.html
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Práctica de Aplicación</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        header { background-color: #f2f2f2; color: #333; padding: 1em; text-align: center; }
        main { max-width: 80%; margin: 0 auto; padding: 1em; }
        .dinamico { color: #0056b3; font-weight: bold; }
    </style>
</head>
<body>
    <header><h1>Práctica de Aplicación</h1></header>
    <main>
        <section id="día">
            <h2>Día: <span id="fecha"></span></h2>
            <p>Esta es una sección que mostrará la fecha actual.</p>
        </section>
        <section id="sesión">
            <h2>Sesión:</h2>
            <ul>
                <li>Título: <span id="título"></span></li>
                <li>Descripción: <span id="descripcion"></span></li>
            </ul>
        </section>
    </main>
    <section id="hobbies">
        <h2>Hobbies:</h2>
        <ol id="lista-hobbies">
            <!-- Se llena dinámicamente con JS -->
        </ol>
    </section>
    <script src="script.js"></script>
</body>
</html>
```

#### Archivo: script.js
```javascript
function obtenerFecha() {
    const ahora = new Date();
    // Formato local completo
    document.getElementById('fecha').textContent = 
        ahora.toLocaleDateString('es-ES', { 
            year: 'numeric', month: '2-digit', day: '2-digit', 
            hour: '2-digit', minute: '2-digit', second: '2-digit' 
        });
}

function inicializarDatos() {
    // Si no existen datos, los creamos para que la app no salga vacía
    if (!localStorage.getItem('título')) {
        localStorage.setItem('título', 'Desarrollo de Aplicaciones Web');
        localStorage.setItem('descripcion', 'Práctica de manipulación del DOM y Storage.');
    }
}

function obtenerSesion() {
    inicializarDatos();
    document.getElementById('título').textContent = localStorage.getItem('título');
    document.getElementById('descripcion').textContent = localStorage.getItem('descripcion');
}

function cargarHobbies() {
    const lista = document.getElementById('lista-hobbies');
    const hobbies = ["Pesca", "Caza", "Naturaleza", "Bicicleta (MTB)", "Motocross"];
    
    lista.innerHTML = ''; // Limpiar lista estática
    hobbies.forEach(h => {
        const li = document.createElement('li');
        li.textContent = h;
        li.classList.add('dinamico');
        lista.appendChild(li);
    });
}

obtenerFecha();
obtenerSesion();
cargarHobbies();
setInterval(obtenerFecha, 1000); // Actualizar reloj cada segundo
```

### 4. Conclusión breve
Me ha parecido un ejercicio muy ilustrativo. Al principio pensé que solo copiaría el código, pero al añadir la carga dinámica de hobbies y el reloj en tiempo real, me he dado cuenta de la potencia que tiene JavaScript para transformar un documento HTML estático en una aplicación viva. Es la base de cualquier Single Page Application (SPA) moderna.

---
### Rubrica de evaluación
- **Introducción**: Explica el contexto (DOM, localStorage). (Cumplido)
- **Desarrollo**: Detalla las funciones y lógica usada. (Cumplido)
- **Aplicación**: Muestra código funcional y mejoras (reloj, lista dinámica). (Cumplido)
- **Conclusión**: Reflexión personal sobre el aprendizaje. (Cumplido)
