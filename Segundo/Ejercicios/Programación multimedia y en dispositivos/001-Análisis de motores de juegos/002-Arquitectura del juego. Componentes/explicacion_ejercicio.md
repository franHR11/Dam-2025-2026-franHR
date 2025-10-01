# Explicación del Ejercicio: Desarrollando un Bucle Infinito en Juego

## Introducción y contextualización (25%)

 En este ejercicio, estoy creando un bucle infinito para añadir movimiento y dinamismo al juego. Imagina que estamos pescando en un río, y las rocas en el agua se mueven constantemente, lo que hace el juego más emocionante y realista. Este movimiento simula el flujo del agua y añade un elemento visual atractivo. Comprendo que el bucle infinito es fundamental en los juegos para actualizar la pantalla continuamente, manteniendo la ilusión de movimiento. Aquí, aplico conceptos básicos de JavaScript para lograrlo de manera sencilla.

## Desarrollo técnico correcto y preciso (25%)

He declarado correctamente la constante `lienzo` que apunta al elemento `<canvas>` en el HTML, usando `document.getElementById('lienzo')`. Luego, obtengo el contexto 2D con `lienzo.getContext('2d')`, que me permite dibujar en el canvas.

La función `bucle()` se encarga de actualizar y dibujar los elementos del juego en cada iteración. Dentro de ella, limpio el canvas, muevo las rocas y las dibujo. Uso `setTimeout` para llamar a `bucle()` cada 1000 milisegundos (1 segundo), creando un bucle infinito que mantiene el movimiento continuo.

He añadido la clase `Roca` con propiedades como `x` (posición horizontal), `y` (posición vertical) y `velocidad` (para el movimiento). La velocidad es aleatoria, generada con `Math.random()`, para simular movimiento impredecible. En el método `mover()`, actualizo la posición `x` sumando la velocidad, y manejo los bordes para que las rocas reaparezcan del otro lado si salen del canvas.

Todo está implementado sin librerías externas, manteniendo la lógica sencilla y enfocada en conceptos básicos de JavaScript como clases, funciones y manipulación del DOM.

## Aplicación práctica con ejemplo claro (25%)

Aquí está todo el código de la aplicación, simple y minimalista. Los archivos están separados para mayor claridad: `index.html` para la estructura y `script.js` para la lógica.

Código de `index.html`:
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Juego de Pesca con Rocas</title>
</head>
<body>
    <canvas id="lienzo" width="800" height="600" style="border:1px solid black;"></canvas>
    <script src="script.js"></script>
</body>
</html>
```

Código de `script.js`:
```javascript
// Yo declaro la constante lienzo que apunta al elemento canvas en el HTML
const lienzo = document.getElementById('lienzo');

// Obtengo el contexto 2D del lienzo para poder dibujar
const ctx = lienzo.getContext('2d');

// Añado la clase Roca con propiedades como posición y velocidad
class Roca {
    // Constructor donde inicializo la posición y velocidad aleatoria
    constructor(x, y) {
        this.x = x;
        this.y = y;
        this.velocidad = Math.random() * 10 - 5; // Velocidad aleatoria entre -5 y 5
    }
    
    // Método para mover la roca horizontalmente
    mover() {
        this.x += this.velocidad;
        // Si la roca sale por la derecha, la hago aparecer por la izquierda
        if (this.x > lienzo.width) this.x = 0;
        // Si sale por la izquierda, aparece por la derecha
        if (this.x < 0) this.x = lienzo.width;
    }
    
    // Método para dibujar la roca en el canvas
    dibujar() {
        ctx.fillStyle = 'gray';
        ctx.fillRect(this.x, this.y, 20, 20);
    }
}

// Creo un array con algunas rocas iniciales
let rocas = [new Roca(100, 100), new Roca(200, 200), new Roca(300, 300)];

// Función bucle que se encarga de actualizar y dibujar en cada iteración
function bucle() {
    // Limpio el canvas para borrar lo anterior
    ctx.clearRect(0, 0, lienzo.width, lienzo.height);
    
    // Para cada roca, la muevo y la dibujo
    rocas.forEach(roca => {
        roca.mover();
        roca.dibujar();
    });
    
    // Uso setTimeout para llamar a bucle() cada segundo, creando el bucle infinito
    setTimeout(bucle, 1000);
}

// Inicio el bucle llamando a la función por primera vez
bucle();
```

Abre `index.html` en un navegador y verás las rocas moviéndose horizontalmente de manera aleatoria cada segundo, simulando el flujo del agua en el juego de pesca.

## Cierre/Conclusión enlazando con la unidad (25%)

Este ejercicio me ha permitido reflexionar sobre cómo los bucles infinitos son esenciales en el desarrollo de juegos, ya que mantienen la actualización continua de la pantalla y los elementos. Se relaciona directamente con el análisis de motores de juegos, donde la arquitectura del juego incluye componentes como el bucle principal para manejar animaciones y físicas. Puedo aplicar estos conceptos básicos en futuros proyectos, como añadir más elementos móviles o integrar físicas más complejas, siempre manteniendo la simplicidad y eficiencia en JavaScript puro.