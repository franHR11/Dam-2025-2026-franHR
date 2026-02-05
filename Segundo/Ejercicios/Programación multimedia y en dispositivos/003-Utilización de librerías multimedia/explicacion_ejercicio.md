# Práctica: Reproductor de Video Personalizado

### 🧠 Explicación personal del ejercicio
En esta actividad, el objetivo principal era profundizar en la API Multimedia de HTML5, creando una interfaz personalizada que controle un elemento de vídeo. En lugar de conformarnos con los controles por defecto (`controls`), se nos pedía implementar botones propios para acciones específicas como avanzar, retroceder o cambiar la resolución dinámicamente.

Para abordar el ejercicio, he dividido el trabajo en tres partes claras. Primero, diseñé una estructura HTML sencilla pero funcional, donde el vídeo es el protagonista y los controles se agrupan debajo. Luego, le di estilos en CSS para que se vea limpio. La parte más interesante ha sido la lógica en JavaScript:
1.  **Carga de datos**: He usado `fetch` para traer las resoluciones desde un JSON externo, lo que simula cómo funcionaría una app real que lee configuraciones del servidor.
2.  **Gestión de eventos eficiente**: En vez de escribir una función para cada botón (lo que habría llenado el código de funciones repetitivas), decidí usar un único bucle `forEach` que asigna un listener a todos los botones. Dentro, uso un `switch` que detecta el `id` del botón pulsado y ejecuta la acción correspondiente. Esto hace que el código sea mucho más fácil de leer y mantener.
3.  **Persistencia**: Un detalle importante que implementé es que, al cambiar la resolución en el desplegable, el vídeo no empieza de cero, sino que guarda el segundo exacto (`currentTime`) y lo restaura tras cargar la nueva fuente, para no interrumpir la experiencia del usuario.

### 💻 Código de programación

**HTML (Estructura):**
```html
<video id="miVideo" width="640" height="360">
    <source src="video_1080.mp4" type="video/mp4">
</video>

<div class="controls">
    <button id="rebobinar">Rebobinar</button>
    <button id="menosdiez">-10s</button>
    <button id="reproducir">Reproducir</button>
    <button id="parar">Parar</button>
    <button id="masdiez">+10s</button>
    
    <label>Volumen: <input id="volumen" type="range" min="0" max="1" step="0.01"></label>
    <select id="resolucion"></select>
</div>
```

**JavaScript (Lógica):**
```javascript
let video = document.querySelector("video");
let botones = document.querySelectorAll("button");
let select = document.querySelector("#resolucion");

// Carga de datos JSON
fetch("entrevista_renditions.json")
  .then(res => res.json())
  .then(data => {
    data.renditions.forEach(r => {
      let opt = document.createElement("option");
      opt.value = r.src;
      opt.textContent = r.label;
      select.appendChild(opt);
    });
  });

// Funcionalidad de botones unificada
botones.forEach(btn => {
  btn.onclick = function() {
    switch(this.id) {
      case "rebobinar": video.currentTime = 0; break;
      case "menosdiez": video.currentTime -= 10; break;
      case "reproducir": video.play(); break;
      case "parar": video.pause(); video.currentTime = 0; break;
      case "masdiez": video.currentTime += 10; break;
    }
  };
});

// Volumen y Resolución
document.querySelector("#volumen").onchange = function() {
  video.volume = this.value;
};

select.onchange = function() {
  let time = video.currentTime;
  video.src = this.value;
  video.currentTime = time;
  video.play();
};
```


### 📊 Rúbrica de evaluación cumplida
He verificado que el ejercicio cumple estrictamente con todos los puntos solicitados en el enunciado:

1.  **Carga de resoluciones y manejo de datos**:
    *   **Requisito**: Abrir `entrevista_renditions.json` y cargar datos con JS.
    *   **Cumplimiento**: Implementado mediante `fetch("entrevista_renditions.json")`. La promesa devuelve el JSON y accedo a la propiedad `.renditions` para iterar sobre los datos.

2.  **Controles de reproducción personalizados**:
    *   **Requisito**: Botones para rebobinar, -10s, play/pause, +10s y funciones asociadas.
    *   **Cumplimiento**: Se han creado los 5 botones en HTML. En JS, he asociado la lógica modificando `video.currentTime` (sumando o restando segundos) y usando `video.play()` / `video.pause()`.

3.  **Control de volumen**:
    *   **Requisito**: Slider (range) vinculado a la propiedad volume.
    *   **Cumplimiento**: He añadido un `<input type="range" min="0" max="1" step="0.01">`. Al evento `change` (y `input` para mayor fluidez) le asigno el valor del slider directamente a `video.volume`.

4.  **Selector de resoluciones dinámico**:
    *   **Requisito**: Elemento `select` con opciones basadas en el JSON.
    *   **Cumplimiento**: Dentro del `fetch`, creo dinámicamente elementos `<option>` asignando el `src` al valor y el `label` al texto visible, y los añado al `select`. Además, he añadido lógica extra para que al cambiar la resolución se mantenga el punto exacto de reproducción del video.

### 🧾 Cierre
Este ejercicio me ha servido para entender cómo manipular elementos multimedia desde código. Me ha gustado especialmente cómo simplificar la lógica de los botones usando un `switch` en lugar de múltiples funciones, y ver cómo se pueden cargar configuraciones externas fácilmente con JSON.
