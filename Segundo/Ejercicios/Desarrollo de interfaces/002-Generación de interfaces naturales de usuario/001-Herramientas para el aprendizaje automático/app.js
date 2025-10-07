// app.js - Frontend para manejar el chat
// Obtengo los elementos del DOM
const formEl = document.getElementById('chat-form');
const promptEl = document.getElementById('prompt');
const modelSelect = document.getElementById('model-select');
const chatEl = document.getElementById('chat');

// Agrego un event listener al formulario para cuando se envíe
formEl.addEventListener('submit', async (event) => {
    // Prevengo el comportamiento por defecto del formulario
    event.preventDefault();

    // Obtengo el prompt y el modelo seleccionado
    const prompt = promptEl.value;
    const model = modelSelect.value;

    // Hago una petición fetch al backend
    const response = await fetch('/api/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ model, prompt })
    });

    // Obtengo los datos de la respuesta
    const data = await response.json();

    // Agrego la respuesta del asistente al chat
    chatEl.innerHTML += `<div class="assistant">${data.response}</div>`;
});