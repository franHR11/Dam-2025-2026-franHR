# Utilización de librerías multimedia: Arquitectura del API

### 1. Encabezado informativo
- **Título:** Trabajo con el API de Audio HTML5 y Visualización
- **Fecha:** 05/02/2026  
- **Estudiante:** Francisco José  
- **Módulo:** Programación multimedia y en dispositivos móviles

---

### 2. Explicación personal del ejercicio

En esta práctica me he dedicado a investigar cómo funciona el control de audio en HTML5 yendo un paso más allá de la simple etiqueta `<audio>`. He cogido los ejemplos básicos de clase y los he modificado para entender mejor cada propiedad. 

Primero, personalicé la barra de progreso para darle un toque más visual y entender cómo sincronizar el ancho de un `div` con el `currentTime` del audio. Después, me metí con la API de Web Audio para pintar la onda de sonido en un canvas, cambiando los colores para que reaccionen a las frecuencias. 

Para terminar, creé un reproductor más completo que gestiona una lista de reproducción (simulada con el mismo archivo para el ejemplo) y captura eventos como `ended` para pasar automáticamente a la siguiente canción, lo cual me parece súper útil para hacer apps de música reales.

Ha sido interesante ver que con poco código JavaScript se pueden controlar tantos eventos del navegador.

---

### 3. Código de programación

#### Modificación 1: Barra de progreso personalizada (`progresbar.html`)
Aquí cambié el color a verde esmeralda y mejoré la fluidez de actualización.

```html
<!-- Fragmento clave del script -->
<script>
    audio.addEventListener('timeupdate', () => {
        // Cálculo del porcentaje basado en tiempo actual vs duración
        const porcentaje = (audio.currentTime / audio.duration) * 100;
        barra.style.width = porcentaje + '%';
    });
    
    // Función para saltar a una parte de la canción al hacer clic
    function seek(e) {
        /* ... cálculo de posición ... */
        audio.currentTime = nuevoTiempo;
    }
</script>
```

#### Modificación 2: Visualizador de onda (`desvelar onda.html`)
En este usé `requestAnimationFrame` para dibujar en el canvas, modificando los canales RGB según la intensidad del sonido.

```javascript
// Dentro del bucle de animación
for(let i = 0; i < bufferLength; i++) {
    const barHeight = dataArray[i];
    
    // Modificación: Gradiente dinámico
    const r = 50 + (barHeight * 0.2); // Rojo base
    const g = 255 - barHeight;        // Verde inverso a la intensidad
    
    ctx.fillStyle = `rgb(${r},${g},255)`;
    ctx.fillRect(x, canvas.height - barHeight, anchoBarra, barHeight);
}
```

#### Funcionalidad Extra: Reproductor con Playlist (`reproductor_avanzado.html`)
He implementado un array de objetos para manejar múltiples pistas y escuchar el evento `ended`.

```javascript
const canciones = [
    { nombre: "Pista 1", url: "../audio.mp3" },
    { nombre: "Pista 2", url: "../audio.mp3" }
];

// Detectar cuando termina una canción para poner la siguiente
audio.addEventListener('ended', () => {
    log("Canción terminada. Siguiente...");
    let siguiente = indiceActual + 1;
    if (siguiente >= canciones.length) siguiente = 0; // Vuelta al principio
    cargarCancion(siguiente);
});
```

---

### 4. Rúbrica de evaluación cumplida

He verificado que esta práctica cumple con todos los puntos solicitados en la actividad:

1.  **Modificación de ejemplos existentes**:
    *   **Requisito:** Ajustar color y velocidad en `progresbar.html`.
    *   **Cumplimiento:** Cambiado el color de la barra a un tono verde `#2ecc71` y mejorada la fluidez usando el evento `timeupdate` en lugar de un `interval` básico.
    *   **Requisito:** Modificar colores de onda en `desvelar onda.html`.
    *   **Cumplimiento:** Implementado un gradiente dinámico que varía de cian a morado calculando los canales RGB en función de la frecuencia (`barHeight`).
    *   **Requisito:** Ajustar temporizador en `bucle.html`.
    *   **Cumplimiento:** Añadido un temporizador de alta precisión que muestra hasta milisegundos, actualizándose cada 50ms.

2.  **Exploración de nuevas funcionalidades**:
    *   **Requisito:** Añadir eventos para manejar estados (finalización).
    *   **Cumplimiento:** En `reproductor_avanzado.html`, se escucha el evento `ended` para disparar automáticamente la siguiente canción.
    *   **Requisito:** Cargar múltiples archivos de audio.
    *   **Cumplimiento:** Creado un array de objetos JSON con la "playlist" que permite cambiar dinámicamente el `src` del audio.
    *   **Requisito:** Sistema de reproducción en bucle.
    *   **Cumplimiento:** Implementado tanto con el atributo `loop` nativo en `bucle.html` como mediante lógica de código (vuelta al índice 0) en la playlist.

3.  **Documentación y Pruebas**:
    *   **Requisito:** Documentar cambios y depurar.
    *   **Cumplimiento:** Todos los archivos scripts tienen comentarios explicativos y he añadido logs en la consola (y en pantalla en el reproductor avanzado) para verificar que los eventos se disparan correctamente.

---

### 5. Cierre
Me ha gustado mucho trastear con el `AudioContext`, aunque al principio asusta un poco configurar el analizador y las fuentes. Una vez que ves las barras moviéndose al ritmo de la música, se entiende mucho mejor cómo procesa el navegador la señal de audio. Creo que esto tiene muchas aplicaciones para videojuegos o reproductores personalizados.
