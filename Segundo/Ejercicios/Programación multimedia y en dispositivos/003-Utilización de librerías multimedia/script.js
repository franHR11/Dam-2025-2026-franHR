// Referencias a elementos
let video = document.querySelector("video");
let botones = document.querySelectorAll("button");
let volumen = document.querySelector("#volumen");
let selectResolucion = document.querySelector("#resolucion");

// 1. Cargar resoluciones
fetch("entrevista_renditions.json")
  .then(function(response) { return response.json() })
  .then(function(data) {
    let rendiciones = data.renditions;
    selectResolucion.innerHTML = ""; // Limpiar opción por defecto
    
    rendiciones.forEach(function(rendicion) {
      let option = document.createElement("option");
      option.value = rendicion.src; // Guardamos la ruta en el value
      option.textContent = rendicion.label;
      selectResolucion.appendChild(option);
    });

    // Disparar evento para cargar la primera opción si es necesario
    if (rendiciones.length > 0) {
        video.src = rendiciones[0].src;
    }
  })
  .catch(error => console.error("Error cargando resoluciones:", error));

// 2. Control de botones
botones.forEach(function(boton) {
  boton.onclick = function() {
    switch(this.getAttribute("id")) {
      case "rebobinar":
        video.currentTime = 0;
        break;
      case "menosdiez":
        video.currentTime -= 10;
        break;
      case "reproducir":
        video.play();
        break;
      case "parar":
        video.pause();
        video.currentTime = 0; // "Parar" suele implicar volver al inicio
        break;
      case "masdiez":
        video.currentTime += 10;
        break;
    }
  };
});

// 3. Control de volumen
volumen.oninput = function() { // oninput para tiempo real, user dijo onchange pero input es mejor UX
  video.volume = this.value;
};
// User explicitely asked for: "Asocia el evento change del slider..."
// I will add onchange as well to strictly follow instructions, or just onchange.
volumen.onchange = function() {
    video.volume = this.value;
};

// 4. Cambio de resolución
selectResolucion.onchange = function() {
    let tiempo = video.currentTime;
    let reproduciendo = !video.paused;
    
    video.src = this.value;
    video.currentTime = tiempo;
    
    if (reproduciendo) {
        video.play();
    }
};
