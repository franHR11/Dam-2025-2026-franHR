// Aquí defino el array de clientes, como me pidieron, con algunos ejemplos de pescadores o clientes del pesquero.
const clientes = [
    { "nombre": "Jose Vicente", "apellidos": "Carratala Sanchis", "email": "info@jocarsa.com" },
    { "nombre": "Juan", "apellidos": "Garcia", "email": "info@jocarsa.com" },
    { "nombre": "Jorge", "apellidos": "Lopez", "email": "info@jocarsa.com" }
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
reconocimiento.onresult = function (event) {
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
reconocimiento.onerror = function (event) {
    document.getElementById('textoEscuchado').textContent = 'Error al escuchar: ' + event.error;
};

// El botón para escuchar inicia el reconocimiento.
document.getElementById('escucharBtn').addEventListener('click', function () {
    reconocimiento.start();
});

// El botón para hablar usa síntesis de voz. Respondo amablemente en español.
document.getElementById('hablarBtn').addEventListener('click', function () {
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