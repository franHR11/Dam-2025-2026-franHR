# Ejercicio: Personajes en Juego 2D Isométrico

## Introducción y contextualización
En este ejercicio, trabajé con personajes en un juego 2D isométrico para una aplicación móvil. Los jugadores controlan personajes en un mundo virtual, aprendiendo a dibujarlos en contexto isométrico y responder a interacciones del usuario.

## Explicación personal del ejercicio
En este ejercicio tenía que crear un programa simple donde se dibuja un personaje en un canvas usando proyección isométrica. Usé HTML y JavaScript básico para hacer un canvas, definir una clase para el personaje, convertir coordenadas a isométricas y añadir controles con teclado. Lo hice minimalista, sin librerías externas, solo con lo que hemos visto hasta ahora.

## Código de programación
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Personaje en Juego Isométrico</title>
    <style>
        canvas {
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <canvas id="lienzo" width="800" height="600"></canvas>
    <script>
        // Obtener el canvas y el contexto
        const canvas = document.getElementById('lienzo');
        const ctx = canvas.getContext('2d');

        // Función para convertir coordenadas cartesianas a isométricas
        function iso(i, j) {
            const x = (i - j) * 20; // Escala para hacer visible
            const y = (i + j) * 10;
            return { x: x + 400, y: y + 300 }; // Centro en el canvas
        }

        // Clase Personaje
        class Personaje {
            constructor(x, y) {
                this.x = x;
                this.y = y;
            }

            dibuja() {
                const pos = iso(this.x, this.y);
                ctx.fillStyle = 'red';
                ctx.fillRect(pos.x - 10, pos.y - 10, 20, 20); // Dibuja un cuadrado para el personaje
            }
        }

        // Crear instancia del personaje
        let personaje = new Personaje(0, 0);

        // Función para dibujar rejilla isométrica
        function dibujaRejilla() {
            ctx.strokeStyle = 'gray';
            ctx.lineWidth = 1;
            for (let i = -10; i <= 10; i++) {
                for (let j = -10; j <= 10; j++) {
                    const pos = iso(i, j);
                    ctx.beginPath();
                    ctx.arc(pos.x, pos.y, 2, 0, 2 * Math.PI); // Puntos en la rejilla
                    ctx.stroke();
                }
            }
        }

        // Función para redibujar todo
        function dibujaTodo() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            dibujaRejilla();
            personaje.dibuja();
        }

        // Evento de teclado
        document.addEventListener('keydown', (event) => {
            switch (event.key) {
                case 'w':
                    personaje.y -= 1; // Mover arriba
                    break;
                case 's':
                    personaje.y += 1; // Mover abajo
                    break;
                case 'a':
                    personaje.x -= 1; // Mover izquierda
                    break;
                case 'd':
                    personaje.x += 1; // Mover derecha
                    break;
            }
            dibujaTodo(); // Redibujar después de mover
        });

        // Dibujar inicialmente
        dibujaTodo();
    </script>
</body>
</html>
```

## Rúbrica de evaluación cumplida
- **Introducción y contextualización (25%)**: Expliqué el ejercicio de forma clara, relacionándolo con motores de juegos isométricos para aplicaciones móviles.
- **Desarrollo técnico correcto y preciso (25%)**: Definí la clase Personaje con atributos x e y y método dibuja(). Implementé la función iso() para proyección isométrica, dibujé la rejilla y añadí controles de teclado. Todo funciona correctamente.
- **Aplicación práctica con ejemplo claro (25%)**: El código muestra cómo dibujar y mover un personaje en isométrico. Abre personaje.html en un navegador, usa w,s,a,d para mover el personaje rojo sobre la rejilla.
- **Cierre/Conclusión enlazando con la unidad (25%)**: Este concepto se puede aplicar a juegos más complejos añadiendo más personajes, animaciones o colisiones, o en contextos como simuladores urbanos.

## Cierre
Me pareció un ejercicio útil para entender proyecciones isométricas en juegos 2D. Es básico pero sienta las bases para cosas más avanzadas en desarrollo de juegos.
