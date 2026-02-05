# Explicación: Clase Robot POO

## Explicación del ejercicio
En esta actividad, he implementado una clase `Robot` utilizando la programación orientada a objetos en JavaScript. El objetivo es simular un robot básico que tiene propiedades de posición (`x`, `y`) y orientación (`angle`).

Para el comportamiento, he creado un método `update` que mueve al robot basándose en su ángulo actual usando funciones trigonométricas (seno y coseno), y un método `checkSensors` que comprueba si un punto proyectado delante del robot choca con algún obstáculo definido. Me ha parecido un buen ejercicio para repasar cómo conectar la lógica matemática con la estructura de clases.

## Código desarrollado

### index.html
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Robot POO JS</title>
    <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f0f0f0; }
        canvas { background: white; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <canvas id="simulation" width="400" height="400"></canvas>
    <script src="script.js"></script>
    <script>
        const canvas = document.getElementById('simulation');
        const ctx = canvas.getContext('2d');
        const robot = new Robot(200, 200, 0);
        
        const obstacles = [
            {x: 100, y: 100, w: 50, h: 50},
            {x: 250, y: 250, w: 100, h: 20}
        ];

        function gameLoop() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            ctx.fillStyle = '#666';
            obstacles.forEach(obs => ctx.fillRect(obs.x, obs.y, obs.w, obs.h));

            robot.update(1, 0.02); 
            robot.draw(ctx);
            
            if (robot.checkSensors(obstacles)) {
                ctx.fillStyle = 'red';
                ctx.fillText('¡Obstáculo!', 10, 20);
            }

            requestAnimationFrame(gameLoop);
        }
        
        gameLoop();
    </script>
</body>
</html>
```

### script.js
```javascript
class Robot {
    constructor(x, y, angle) {
        this.x = x;
        this.y = y;
        this.angle = angle;
    }

    update(speed, rotationSpeed) {
        this.angle += rotationSpeed;
        this.x += Math.cos(this.angle) * speed;
        this.y += Math.sin(this.angle) * speed;
    }

    checkSensors(obstacles) {
        let sensorX = this.x + Math.cos(this.angle) * 30;
        let sensorY = this.y + Math.sin(this.angle) * 30;

        for (let obs of obstacles) {
            if (sensorX > obs.x && sensorX < obs.x + obs.w &&
                sensorY > obs.y && sensorY < obs.y + obs.h) {
                return { x: sensorX, y: sensorY };
            }
        }
        return null;
    }

    draw(ctx) {
        ctx.save();
        ctx.translate(this.x, this.y);
        ctx.rotate(this.angle);
        ctx.fillStyle = 'blue';
        ctx.fillRect(-10, -10, 20, 20);
        ctx.fillStyle = 'red';
        ctx.beginPath();
        ctx.moveTo(0, 0);
        ctx.lineTo(30, 0);
        ctx.stroke();
        ctx.restore();
    }
}
```

## Rúbrica de evaluación detallada (100 puntos)

### 1. Estructura y POO (30 puntos)
- **Definición de Clase (10 pts)**: La clase `Robot` está correctamente definida con sintaxis moderna ES6, encapsulando la lógica del agente.
- **Constructor y Propiedades (10 pts)**: Se inicializan correctamente las propiedades de estado (`x`, `y`, `angle`) en el constructor, permitiendo instancias independientes.
- **Encapsulamiento de Métodos (10 pts)**: Las funciones `update`, `checkSensors` y `draw` están implementadas como métodos de instancia que operan sobre el estado interno (`this`).

### 2. Lógica Matemática y Física (25 puntos)
- **Movimiento Vectorial (10 pts)**: Uso correcto de trigonometría (`Math.cos`, `Math.sin`) para descomponer el vector de movimiento basado en el ángulo actual.
- **Manejo de Rotación (5 pts)**: Implementación funcional de la velocidad angular para actualizar la orientación en radianes.
- **Proyección de Sensores (10 pts)**: Cálculo preciso de las coordenadas del sensor proyectado a una distancia fija delante del robot.

### 3. Renderizado y Canvas API (20 puntos)
- **Gestión del Contexto (5 pts)**: Uso de `ctx.save()` y `ctx.restore()` para aislar las transformaciones del robot y no afectar al resto del renderizado.
- **Transformaciones Geométricas (10 pts)**: Aplicación correcta de `ctx.translate()` para mover el origen y `ctx.rotate()` para rotar el sistema de coordenadas local.
- **Dibujo de Primitivas (5 pts)**: Uso adecuado de primitivas de dibujo (`fillRect`, `moveTo`, `lineTo`) para representar visualmente el cuerpo y la dirección del robot.

### 4. Implementación y Funcionalidad (15 puntos)
- **Bucle de Animación (5 pts)**: Implementación fluida utilizando `requestAnimationFrame` que limpia y redibuja el escena en cada frame.
- **Lógica de Colisiones (10 pts)**: Algoritmo correcto para verificar si las coordenadas del sensor interceptan con las áreas rectangulares de los obstáculos definidos.

### 5. Calidad y Estilo (10 puntos)
- **Legibilidad (5 pts)**: Código limpio con indentación consistente y nombres de variables auto-explicativos (`speed`, `rotationSpeed`, `obstacles`).
- **Eficiencia (5 pts)**: Estructura lógica simple y directa, evitando cálculos redundantes dentro del bucle principal.

## Conclusión
Este ejercicio me ha servido para asentar los conceptos de objetos en JS. Aunque la lógica de movimiento es sencilla, ver cómo el objeto mantiene su propio estado (`x`, `y`, `angle`) demuestra la utilidad de la POO para organizar el código en juegos o simulaciones.
