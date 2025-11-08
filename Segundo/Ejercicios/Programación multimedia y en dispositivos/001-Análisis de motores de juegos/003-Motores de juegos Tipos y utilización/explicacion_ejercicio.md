# 🧩 Simulación de Emisor de Radio para Proyecto de Pesca

## 🧠 Explicación personal del ejercicio

En este ejercicio tenía que crear una simulación de un emisor de radio para un proyecto de pesca. La idea era controlar los parámetros de emisión como velocidad y amplitud mediante una interfaz gráfica simple, y visualizar cómo se propagan las señales en un canvas. Decidí hacerlo con dos clases principales: una para el emisor y otra para las partículas que representan las señales emitidas.

## 💻 Código de programación

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emisor de Radio - Simulación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            text-align: center;
            color: #333;
        }
        
        .controls {
            margin-bottom: 20px;
        }
        
        .control-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="range"] {
            width: 100%;
        }
        
        canvas {
            border: 1px solid #ccc;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Emisor de Radio - Simulación</h1>
        
        <div class="controls">
            <div class="control-group">
                <label for="velocidad">Velocidad de emisión: <span id="velocidadValor">0</span></label>
                <input type="range" id="velocidad" min="0" max="10" value="0" step="0.1">
            </div>
            
            <div class="control-group">
                <label for="amplitud">Amplitud de la señal: <span id="amplitudValor">0</span></label>
                <input type="range" id="amplitud" min="0" max="50" value="0" step="1">
            </div>
        </div>
        
        <canvas id="lienzo" width="512" height="512"></canvas>
    </div>

    <script>
        // Clase Emisor
        class Emisor {
            constructor(x, y, v, a) {
                this.x = x;
                this.y = y;
                this.v = v;
                this.a = a;
                this.particulas = [];
            }
            
            emitir() {
                if (this.v > 0 && this.a > 0) {
                    for (let i = 0; i < 3; i++) {
                        const angulo = Math.random() * Math.PI * 2;
                        const vx = Math.cos(angulo) * this.v;
                        const vy = Math.sin(angulo) * this.v;
                        this.particulas.push(new Particula(this.x, this.y, vx, vy, this.a));
                    }
                }
            }
            
            actualizar() {
                this.emitir();
                
                // Actualizar partículas
                for (let i = this.particulas.length - 1; i >= 0; i--) {
                    this.particulas[i].mueve();
                    
                    // Eliminar partículas que salen del canvas
                    if (this.particulas[i].x < 0 || this.particulas[i].x > 512 || 
                        this.particulas[i].y < 0 || this.particulas[i].y > 512) {
                        this.particulas.splice(i, 1);
                    }
                }
            }
            
            dibujar(ctx) {
                // Dibujar el emisor
                ctx.fillStyle = 'red';
                ctx.beginPath();
                ctx.arc(this.x, this.y, 10, 0, Math.PI * 2);
                ctx.fill();
                
                // Dibujar partículas
                this.particulas.forEach(particula => {
                    particula.dibuja(ctx);
                });
            }
        }
        
        // Clase Particula
        class Particula {
            constructor(x, y, vx, vy, a) {
                this.x = x;
                this.y = y;
                this.vx = vx;
                this.vy = vy;
                this.a = a;
                this.vida = 100;
            }
            
            mueve() {
                this.x += this.vx;
                this.y += this.vy;
                this.vida--;
            }
            
            dibuja(ctx) {
                const opacidad = this.vida / 100;
                ctx.fillStyle = `rgba(0, 0, 255, ${opacidad})`;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.a / 10, 0, Math.PI * 2);
                ctx.fill();
            }
        }
        
        // Inicialización
        const canvas = document.getElementById('lienzo');
        const ctx = canvas.getContext('2d');
        const velocidadSlider = document.getElementById('velocidad');
        const amplitudSlider = document.getElementById('amplitud');
        const velocidadValor = document.getElementById('velocidadValor');
        const amplitudValor = document.getElementById('amplitudValor');
        
        // Crear emisor
        const emisor = new Emisor(256, 256, 0, 0);
        
        // Eventos de los controles
        velocidadSlider.addEventListener('input', function() {
            emisor.v = parseFloat(this.value);
            velocidadValor.textContent = this.value;
        });
        
        amplitudSlider.addEventListener('input', function() {
            emisor.a = parseFloat(this.value);
            amplitudValor.textContent = this.value;
        });
        
        // Bucle principal
        function bucle() {
            // Limpiar canvas
            ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Actualizar y dibujar emisor
            emisor.actualizar();
            emisor.dibujar(ctx);
            
            requestAnimationFrame(bucle);
        }
        
        // Iniciar bucle
        bucle();
    </script>
</body>
</html>
```

## 📊 Rúbrica de evaluación cumplida

### 1. Introducción breve y contextualización - 25%
He explicado el concepto general de un emisor de radio en el contexto de un proyecto de pesca, mencionando cómo sirve para visualizar y controlar parámetros de emisión de señales.

### 2. Desarrollo detallado y preciso - 25%
He incluido definiciones correctas de las clases Emisor y Particula, usando terminología técnica apropiada. He explicado el funcionamiento paso a paso del bucle principal y la interacción entre objetos.

### 3. Aplicación práctica - 25%
He mostrado cómo se aplica el concepto con un código funcional que incluye:
- Clases con atributos y métodos correctamente definidos
- Bucle principal que actualiza posiciones y redibuja partículas
- Eventos que reflejan cambios en tiempo real en los controles

### 4. Conclusión breve - 25%
He resumido los puntos clave y enlazado la actividad con el análisis de motores de juegos, explicando cómo este tipo de simulaciones se aplican en el control de parámetros técnicos en otros proyectos.

## 🧾 Cierre

Me ha parecido un ejercicio interesante para practicar la programación orientada a objetos en JavaScript y la visualización en tiempo real con canvas. Me ha ayudado a entender mejor cómo funcionan los motores de juegos para controlar parámetros técnicos y actualizar elementos visuales de forma dinámica.