# 🎙️ Asistente de Voz con IA

Un proyecto web que integra tecnologías de interacción máquina-persona usando reconocimiento de voz, generación de habla e inteligencia artificial.

## 🚀 Características

- **Reconocimiento de voz**: Habla con el sistema usando el micrófono de tu navegador
- **Generación de habla**: El sistema te responde verbalmente
- **IA integrada**: Usa OpenAI GPT-3.5-turbo para respuestas inteligentes
- **Chat visual**: Interfaz de conversación moderna
- **Control dual**: Puedes usar voz o texto

## 📋 Requisitos

- Navegador moderno que soporte Web Speech API (Chrome, Edge, Firefox)
- API Key de OpenAI (obtén una en [platform.openai.com](https://platform.openai.com))
- Conexión a internet

## 🛠️ Instalación y Uso

1. **Abre el proyecto**: Abre el archivo `index.html` en tu navegador
2. **Configura tu API Key**: 
   - Copia tu API Key de OpenAI
   - Pégala en el campo de texto amarillo
   - Haz clic en "Guardar API Key"
3. **Inicia el reconocimiento**:
   - Haz clic en "🎤 Iniciar Reconocimiento de Voz"
   - El navegador te pedirá permiso para usar el micrófono
   - ¡Comienza a hablar!

## 🎮 Modos de Interacción

### Voz
- Haz clic en "Iniciar Reconocimiento de Voz"
- Habla naturalmente en español
- El sistema procesará tu voz y responderá verbalmente

### Texto
- Escribe tu mensaje en el campo de texto
- Presiona Enter o haz clic en "Enviar"
- Recibirás respuesta escrita y hablada

## 🔧 Características Técnicas

- **Lenguajes**: HTML5, CSS3, JavaScript Vanilla
- **APIs utilizadas**:
  - Web Speech Recognition API (reconocimiento de voz)
  - Speech Synthesis API (generación de habla)
  - OpenAI API (procesamiento de lenguaje natural)
- **Almacenamiento**: localStorage para la API Key
- **Diseño**: Responsive y moderno

## 📁 Estructura del Proyecto

```
voz-ia-asistente/
├── index.html          # Página principal
├── css/
│   └── style.css      # Estilos visuales
├── js/
│   └── app.js         # Lógica principal
└── README.md          # Este archivo
```

## 🎨 Capturas de Pantalla

La interfaz incluye:
- Indicador visual de estado (escuchando/procesando/listo)
- Área de chat con mensajes del usuario y del asistente
- Controles para iniciar/detener el reconocimiento
- Configuración segura de API Key

## 🚨 Solución de Problemas

### "El navegador no soporta reconocimiento de voz"
- Usa Chrome, Edge o Firefox actualizados
- Verifica que el micrófono esté conectado

### "Error de conexión con OpenAI"
- Revisa tu API Key esté correcta
- Verifica tu conexión a internet
- Confirma que tienes créditos en tu cuenta OpenAI

### "No oigo las respuestas"
- Revisa el volumen de tu dispositivo
- Asegúrate que el navegador tenga permisos de audio

## 📝 Notas del Desarrollador

Este proyecto fue creado como ejercicio final de interfaces naturales de usuario, demostrando la integración de múltiples tecnologías de interacción humana-máquina en una aplicación web funcional y minimalista.

## 🤝 Contribuciones

¡Es un proyecto educativo! Siéntete libre de aprender y modificar el código.

---

**Creado por:** Fran DAM  
**Asignatura:** Desarrollo de Interfaces  
**Unidad:** Generación de Interfaces Naturales de Usuario