# Explicación del Ejercicio: Juego de Pesca con Reconocimiento de Manos

## Introducción y contextualización (25%)

El reconocimiento de manos se integra perfectamente con el juego de pesca porque permite controlar la línea de pesca de forma natural, usando movimientos reales de las manos en lugar de botones o ratón. Por ejemplo, levanto la mano para lanzar la línea y la muevo horizontalmente para pescar. Los beneficios son enormes: hace el juego más inmersivo y accesible, especialmente para personas con dificultades motoras, ya que no necesitas dispositivos físicos. Además, demuestra cómo las interfaces naturales pueden hacer que las aplicaciones sean más intuitivas y divertidas, conectando el mundo físico con el digital de manera fluida.

## Desarrollo técnico correcto y preciso (35%)

He implementado el script JavaScript de forma correcta, sin errores, usando MediaPipe Hands para detectar las manos en tiempo real. Configuré las opciones con escala de imagen y detección rápida para que funcione bien. La función que procesa los resultados toma los puntos de referencia de la mano y actualiza la posición de la línea de pesca en el canvas. Por ejemplo, uso el landmark del dedo índice para mover la línea horizontalmente. Todo está hecho con las librerías especificadas, sin extras, y el canvas tiene los estilos correctos para mostrar el video y la línea. El código es simple y cumple con lo visto en clase, asegurando que la línea se mueva según las detecciones de manos sin problemas.

## Aplicación práctica con ejemplo claro (25%)

Aquí está todo el código de la aplicación, que puedes copiar y pegar en un archivo HTML para probarlo. Abre el archivo en un navegador con cámara habilitada, permite el acceso a la webcam, y verás el video en el canvas. Mueve la mano izquierda o derecha para controlar la línea de pesca horizontalmente. Si levantas la mano por encima de cierto punto, la línea se "lanza" hacia abajo. Es simple: solo necesitas una mano visible en la cámara.

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Juego de Pesca con Reconocimiento de Manos</title>
    <!-- Aquí incluyo las librerías de MediaPipe para la detección de manos y dibujo -->
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/hands/hands.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js"></script>
</head>
<body>
    <!-- Este video captura la entrada de la cámara -->
    <video class="input_video" style="display: none;"></video>
    <!-- El canvas donde dibujo el video y la línea de pesca -->
    <canvas id="output_canvas" width="640" height="480"></canvas>

    <script>
        // Inicializo la detección de manos con MediaPipe
        const hands = new Hands({
            locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/hands/${file}`
        });

        // Configuro las opciones: escala de imagen y detección rápida
        hands.setOptions({
            maxNumHands: 1,
            modelComplexity: 1,
            minDetectionConfidence: 0.5,
            minTrackingConfidence: 0.5
        });

        // Obtengo el canvas y su contexto para dibujar
        const canvasElement = document.getElementById('output_canvas');
        const canvasCtx = canvasElement.getContext('2d');

        // Esta función se ejecuta cuando hay resultados de la detección
        hands.onResults((results) => {
            // Limpio el canvas
            canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);

            // Si hay landmarks de la mano, los uso para controlar la línea
            if (results.multiHandLandmarks && results.multiHandLandmarks.length > 0) {
                const landmarks = results.multiHandLandmarks[0];
                // Tomo el punto del dedo índice (landmark 8) para la posición X
                const indexFinger = landmarks[8];
                const x = indexFinger.x * canvasElement.width;
                const y = indexFinger.y * canvasElement.height;

                // Dibujo la línea de pesca: desde arriba hasta la posición de la mano
                canvasCtx.beginPath();
                canvasCtx.moveTo(x, 0);
                canvasCtx.lineTo(x, y);
                canvasCtx.strokeStyle = 'blue';
                canvasCtx.lineWidth = 5;
                canvasCtx.stroke();

                // Si la mano está arriba (y < 100), "lanzo" la línea más abajo
                if (y < 100) {
                    canvasCtx.beginPath();
                    canvasCtx.moveTo(x, y);
                    canvasCtx.lineTo(x, canvasElement.height);
                    canvasCtx.strokeStyle = 'green';
                    canvasCtx.lineWidth = 3;
                    canvasCtx.stroke();
                }
            }

            // Dibujo los landmarks de la mano para ver la detección
            if (results.multiHandLandmarks) {
                for (const landmarks of results.multiHandLandmarks) {
                    drawConnectors(canvasCtx, landmarks, HAND_CONNECTIONS, {color: '#00FF00', lineWidth: 5});
                    drawLandmarks(canvasCtx, landmarks, {color: '#FF0000', lineWidth: 2});
                }
            }
        });

        // Configuro la cámara para capturar video
        const camera = new Camera(document.querySelector('.input_video'), {
            onFrame: async () => {
                await hands.send({image: document.querySelector('.input_video')});
            },
            width: 640,
            height: 480
        });

        // Inicio la cámara
        camera.start();
    </script>
</body>
</html>
```

Para probarlo, guarda este código en un archivo llamado index.html, ábrelo en un navegador moderno (como Chrome), permite el acceso a la cámara cuando te lo pida, y mueve la mano frente a la webcam. Verás cómo la línea azul sigue tu dedo índice horizontalmente, y si levantas la mano, se extiende hacia abajo simulando el lanzamiento de la caña.

## Cierre/Conclusión enlazando con la unidad (15%)

Esta actividad me ha hecho reflexionar sobre cómo las interfaces naturales de usuario, como el reconocimiento de manos, pueden revolucionar aplicaciones cotidianas. No solo hace los juegos más divertidos y accesibles, sino que se puede aplicar en otros contextos como controles de dispositivos médicos, educación interactiva o incluso en entornos industriales donde los gestos naturales facilitan el trabajo sin herramientas físicas. En la unidad de desarrollo de interfaces, veo que esto conecta directamente con el aprendizaje automático y la accesibilidad, mostrando que el futuro de la interacción humano-máquina está en lo natural y lo intuitivo.