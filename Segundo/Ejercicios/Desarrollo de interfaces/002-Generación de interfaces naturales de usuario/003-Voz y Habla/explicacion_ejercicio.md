# Introducción y contextualización (25%)

Hola, en este proyecto he combinado mis hobbies de programar y pesca para crear una aplicación sencilla que usa reconocimiento de voz para gestionar las pesqueras y los clientes de un pesquero local. La idea es que puedas hablar con la app para buscar información de clientes, como si estuvieras charlando con un asistente en la barca. Es práctico porque en un entorno de pesca, a veces las manos están ocupadas, y la voz facilita las cosas. He usado tecnologías web básicas como HTML, JavaScript y la Web Speech API, que es nativa del navegador, sin librerías externas, para mantenerlo simple y como lo hemos visto en clase.

# Desarrollo técnico correcto y preciso (25%)

El desarrollo se basa en conceptos básicos: HTML para la estructura de la página con dos botones ("Escuchar" y "Hablar"), JavaScript para manejar el reconocimiento de voz usando SpeechRecognition (que captura el audio y lo convierte en texto), y SpeechSynthesis para generar voz en español con un tono amable. He definido un array de objetos JSON con clientes, cada uno con nombre, apellidos y email. La función de búsqueda recorre el array y busca coincidencias por nombre o apellidos, mostrando la info en la pantalla. Todo está comentado paso a paso en español, en primera persona, para que sea fácil de entender. He seguido buenas prácticas como separar el código en funciones claras y usar variables descriptivas.

# Aplicación práctica con ejemplo claro (25%)

Aquí va todo el código de la aplicación, minimalista y funcional. Primero, el archivo index.html:

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pesqueras con Voz</title>
</head>
<body>
    <h1>App de Voz para Pesqueras</h1>
    <button id="escucharBtn">Escuchar</button>
    <button id="hablarBtn">Hablar</button>
    <p id="textoEscuchado"></p>
    <div id="resultadoCliente"></div>
    <script src="script.js"></script>
</body>
</html>
```

Ahora, el archivo script.js:

```javascript
// Aquí defino el array de clientes, como me pidieron, con algunos ejemplos de pescadores o clientes del pesquero.
const clientes = [
    {"nombre": "Jose Vicente", "apellidos": "Carratala Sanchis", "email": "info@jocarsa.com"},
    {"nombre": "Juan", "apellidos": "Garcia", "email": "info@jocarsa.com"},
    {"nombre": "Jorge", "apellidos": "Lopez", "email": "info@jocarsa.com"}
];

// Esta función busca un cliente por nombre o apellidos. La uso cuando escucho algo.
function buscarCliente(texto) {
    for (let cliente of clientes) {
        if (cliente.nombre.toLowerCase().includes(texto.toLowerCase()) || cliente.apellidos.toLowerCase().includes(texto.toLowerCase())) {
            return cliente;
        }
    }
    return null;
}

// Aquí configuro el reconocimiento de voz. Uso la Web Speech API que viene con el navegador.
const reconocimiento = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
reconocimiento.lang = 'es-ES'; // Lo pongo en español porque la app es para un pesquero local.
reconocimiento.continuous = false;
reconocimiento.interimResults = false;

// Cuando termino de escuchar, tomo el texto y lo muestro, además busco si es un cliente.
reconocimiento.onresult = function(event) {
    const texto = event.results[0][0].transcript;
    document.getElementById('textoEscuchado').textContent = 'Escuché: ' + texto;
    const cliente = buscarCliente(texto);
    if (cliente) {
        document.getElementById('resultadoCliente').innerHTML = 'Cliente encontrado: ' + cliente.nombre + ' ' + cliente.apellidos + ', Email: ' + cliente.email;
    } else {
        document.getElementById('resultadoCliente').textContent = 'No encontré ese cliente.';
    }
};

// Si hay error, lo muestro.
reconocimiento.onerror = function(event) {
    document.getElementById('textoEscuchado').textContent = 'Error al escuchar: ' + event.error;
};

// El botón para escuchar inicia el reconocimiento.
document.getElementById('escucharBtn').addEventListener('click', function() {
    reconocimiento.start();
});

// El botón para hablar usa síntesis de voz. Respondo amablemente en español.
document.getElementById('hablarBtn').addEventListener('click', function() {
    const texto = document.getElementById('textoEscuchado').textContent.replace('Escuché: ', '');
    if (texto) {
        const synth = window.speechSynthesis;
        const utterance = new SpeechSynthesisUtterance('Hola, escuché: ' + texto + '. Si buscas un cliente, aquí está la info.');
        utterance.lang = 'es-ES'; // Español amable.
        utterance.rate = 0.8; // Un poco lento para que suene amigable.
        synth.speak(utterance);
    } else {
        const synth = window.speechSynthesis;
        const utterance = new SpeechSynthesisUtterance('No he escuchado nada aún. Prueba el botón de escuchar.');
        utterance.lang = 'es-ES';
        synth.speak(utterance);
    }
});
```

Para usar la app, abre index.html en un navegador moderno (como Chrome), haz clic en "Escuchar", di algo como "Juan" o "Garcia", y verás la info del cliente. Luego, "Hablar" te responde en voz. Es simple, pero funciona sin nada extra.

# Cierre/Conclusión enlazando con la unidad (25%)

He aprendido mucho sobre interfaces naturales de usuario, especialmente con voz, que es clave en esta unidad. Combinar hobbies hace que programar sea más divertido, y veo cómo aplicar esto a apps móviles o web para entornos reales como pesca. En futuros proyectos, usaré síntesis y reconocimiento para hacer interfaces más accesibles, sin complicar el código.