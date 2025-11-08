// Asistente de voz con IA - Integración de reconocimiento de voz y OpenAI GPT

class VoiceAssistant {
    constructor() {
        this.recognition = null;
        this.synthesis = window.speechSynthesis;
        this.apiKey = localStorage.getItem('openai_api_key') || '';
        this.isListening = false;
        this.conversationHistory = [];

        this.initializeElements();
        this.initializeSpeechRecognition();
        this.bindEvents();
        this.updateApiStatus();
    }

    // Inicializo los elementos del DOM
    initializeElements() {
        this.startBtn = document.getElementById('startBtn');
        this.stopBtn = document.getElementById('stopBtn');
        this.textInput = document.getElementById('textInput');
        this.sendBtn = document.getElementById('sendBtn');
        this.apiKeyInput = document.getElementById('apiKey');
        this.saveApiBtn = document.getElementById('saveApiBtn');
        this.messagesContainer = document.getElementById('messages');
        this.statusText = document.getElementById('statusText');
        this.statusDot = document.querySelector('.status-dot');
    }

    // Inicializo el reconocimiento de voz del navegador
    initializeSpeechRecognition() {
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.recognition = new SpeechRecognition();

            // Configuro el reconocimiento para español
            this.recognition.lang = 'es-ES';
            this.recognition.continuous = true;
            this.recognition.interimResults = false;
            this.recognition.maxAlternatives = 1;

            // Eventos del reconocimiento de voz
            this.recognition.onstart = () => {
                console.log('Reconocimiento de voz iniciado');
                this.updateStatus('Escuchando...', 'listening');
                this.isListening = true;
            };

            this.recognition.onresult = (event) => {
                const last = event.results.length - 1;
                const transcript = event.results[last][0].transcript;
                console.log('Texto reconocido:', transcript);

                if (event.results[last].isFinal) {
                    this.processUserMessage(transcript);
                }
            };

            this.recognition.onerror = (event) => {
                console.error('Error en reconocimiento:', event.error);
                this.updateStatus('Error: ' + event.error, 'error');
                this.stopListening();
            };

            this.recognition.onend = () => {
                console.log('Reconocimiento de voz detenido');
                if (this.isListening) {
                    // Si estaba escuchando, reinicio automáticamente
                    this.recognition.start();
                }
            };
        } else {
            console.error('El navegador no soporta reconocimiento de voz');
            this.updateStatus('Tu navegador no soporta reconocimiento de voz', 'error');
        }
    }

    // Vinculo eventos a los botones
    bindEvents() {
        this.startBtn.addEventListener('click', () => this.startListening());
        this.stopBtn.addEventListener('click', () => this.stopListening());
        this.sendBtn.addEventListener('click', () => this.sendTextMessage());
        this.textInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendTextMessage();
            }
        });
        this.saveApiBtn.addEventListener('click', () => this.saveApiKey());
    }

    // Inicio el reconocimiento de voz
    startListening() {
        if (this.recognition && !this.isListening) {
            try {
                this.recognition.start();
                this.startBtn.disabled = true;
                this.stopBtn.disabled = false;
            } catch (error) {
                console.error('Error al iniciar reconocimiento:', error);
                this.updateStatus('Error al iniciar', 'error');
            }
        }
    }

    // Detengo el reconocimiento de voz
    stopListening() {
        if (this.recognition && this.isListening) {
            this.recognition.stop();
            this.isListening = false;
            this.startBtn.disabled = false;
            this.stopBtn.disabled = true;
            this.updateStatus('Listo para escuchar', 'ready');
        }
    }

    // Envío mensaje de texto
    sendTextMessage() {
        const text = this.textInput.value.trim();
        if (text) {
            this.processUserMessage(text);
            this.textInput.value = '';
        }
    }

    // Proceso el mensaje del usuario y obtengo respuesta de IA
    async processUserMessage(message) {
        // Añado mensaje del usuario al chat
        this.addMessage(message, 'user');

        // Actualizo estado
        this.updateStatus('Procesando...', 'processing');

        try {
            const response = await this.getAIResponse(message);
            this.addMessage(response, 'assistant');
            this.speakResponse(response);
            this.updateStatus('Listo para escuchar', 'ready');
        } catch (error) {
            console.error('Error al obtener respuesta:', error);
            this.addMessage('Lo siento, ha ocurrido un error. ¿Tienes configurada tu API key?', 'assistant');
            this.updateStatus('Error de conexión', 'error');
        }
    }

    // Obtengo respuesta de OpenAI API
    async getAIResponse(message) {
        if (!this.apiKey) {
            throw new Error('API key no configurada');
        }

        // Preparo el historial de conversación para el contexto
        const messages = [
            {
                role: 'system',
                content: 'Eres un asistente amigable y útil. Responde de forma breve y natural en español.'
            },
            ...this.conversationHistory.slice(-4), // Últimos 4 mensajes para contexto
            {
                role: 'user',
                content: message
            }
        ];

        const response = await fetch('https://api.openai.com/v1/chat/completions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.apiKey}`
            },
            body: JSON.stringify({
                model: 'gpt-3.5-turbo',
                messages: messages,
                max_tokens: 150,
                temperature: 0.7
            })
        });

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const data = await response.json();
        const assistantMessage = data.choices[0].message.content;

        // Guardo en el historial
        this.conversationHistory.push(
            { role: 'user', content: message },
            { role: 'assistant', content: assistantMessage }
        );

        return assistantMessage;
    }

    // Convierto texto a voz
    speakResponse(text) {
        if ('speechSynthesis' in window) {
            // Detengo cualquier reproducción anterior
            this.synthesis.cancel();

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'es-ES';
            utterance.rate = 0.9;
            utterance.pitch = 1;

            this.synthesis.speak(utterance);
            console.log('Reproduciendo voz:', text);
        }
    }

    // Añado mensaje al chat visual
    addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        messageDiv.textContent = text;

        this.messagesContainer.appendChild(messageDiv);

        // Hago scroll automático al último mensaje
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }

    // Guardo API key
    saveApiKey() {
        const key = this.apiKeyInput.value.trim();
        if (key) {
            this.apiKey = key;
            localStorage.setItem('openai_api_key', key);
            this.apiKeyInput.value = '';
            this.updateApiStatus();
            this.addMessage('API Key guardada correctamente. ¡Ahora podemos hablar!', 'assistant');
        }
    }

    // Actualizo estado visual de API
    updateApiStatus() {
        if (this.apiKey) {
            this.apiKeyInput.placeholder = 'API Key configurada ✓';
            this.apiKeyInput.style.borderColor = '#4CAF50';
        } else {
            this.apiKeyInput.placeholder = 'Introduce tu API Key de OpenAI';
            this.apiKeyInput.style.borderColor = '#ddd';
        }
    }

    // Actualizo indicador de estado
    updateStatus(text, type = 'ready') {
        this.statusText.textContent = text;
        this.statusDot.className = 'status-dot ' + type;
    }
}

// Inicializo el asistente cuando la página carga
document.addEventListener('DOMContentLoaded', () => {
    console.log('Iniciando Asistente de Voz con IA...');

    // Muestro mensaje de bienvenida
    const welcomeMessage = document.createElement('div');
    welcomeMessage.className = 'message assistant-message';
    welcomeMessage.innerHTML = `
        <strong>¡Hola! Soy tu asistente de voz con IA. 🤖</strong><br>
        1. Configura tu API Key de OpenAI abajo<br>
        2. Haz clic en "Iniciar Reconocimiento de Voz"<br>
        3. Háblame o escribe un mensaje<br>
        ¡Estoy listo para ayudarte!
    `;
    document.getElementById('messages').appendChild(welcomeMessage);

    // Creo instancia del asistente
    window.voiceAssistant = new VoiceAssistant();
});
