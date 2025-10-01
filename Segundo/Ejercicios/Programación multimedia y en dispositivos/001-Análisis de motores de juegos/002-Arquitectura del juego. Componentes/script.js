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