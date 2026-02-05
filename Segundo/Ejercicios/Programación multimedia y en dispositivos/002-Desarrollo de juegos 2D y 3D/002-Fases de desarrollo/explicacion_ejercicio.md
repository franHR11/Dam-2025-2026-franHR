# Sistema de Partículas Interactivo

**Fecha:** 02/02/2026  
**Módulo:** Programación multimedia y en dispositivos - Desarrollo de juegos 2D y 3D  
**Objetivo:** Crear un sistema interactivo de partículas con comportamientos de atracción, repulsión y rebotes

## Introducción y Contextualización

En este ejercicio tenía que crear un sistema interactivo de partículas que simule diferentes comportamientos físicos como atracción, repulsión y rebotes. Los sistemas de partículas se usan mucho en videojuegos y simulaciones para crear efectos visuales como fuego, agua, humo o incluso para simular comportamiento de multitudes. La idea principal es que cada partícula tenga posición, velocidad y pueda interactuar con otras partículas mediante fuerzas calculadas matemáticamente.

Para familiarizarme con el concepto, he repasado los ejemplos de clase donde se veía cómo calcular la distancia entre partículas para determinar la fuerza de atracción o repulsión, y cómo aplicar esas fuerzas para modificar la velocidad y posición de cada partícula. También he visto cómo hacer que las partículas reboten contra las paredes del canvas invirtiendo su velocidad cuando llegan a los bordes.

## Desarrollo Detallado y Preciso

El sistema funciona con tres conceptos principales:

1. **Clase Partícula**: Cada partícula tiene coordenadas (x, y), velocidad (vx, vy), radio y color. En cada frame del bucle de animación, la partícula actualiza su posición sumando la velocidad a las coordenadas actuales.

2. **Cálculo de fuerzas**: Cuando dos partículas están cerca, se calcula la distancia entre ellas usando el teorema de Pitágoras (distancia = √((x2-x1)² + (y2-y1)²)). Si la distancia es menor que un umbral, se aplica una fuerza:
   - **Atracción**: las partículas se acercan sumando la diferencia de coordenadas a la velocidad
   - **Repulsión**: las partículas se alejan restando la diferencia

3. **Rebotes**: Cuando una partícula toca el borde del canvas, su velocidad en ese eje se invierte (multiplicando por -1), lo que hace que rebote.

El bucle principal usa `requestAnimationFrame` para actualizar 60 veces por segundo, llamando al método `actualizar()` y `dibujar()` de cada partícula, y luego comprobando las colisiones entre todas las partículas.

## Aplicación Práctica

He creado un sistema de partículas completo con HTML y JavaScript mínimo pero funcional. Las partículas se agrupan automáticamente cuando están cerca (atracción) y se repelen si están demasiado juntas. Además rebotan contra las paredes del canvas.

```html
<!DOCTYPE html>
<html>
<head>
  <style>
    canvas { display: block; margin: auto; background: #1a1a2e; }
    body { background: #16213e; margin: 0; }
  </style>
</head>
<body>
  <canvas id="canvas"></canvas>
  <script>
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const particulas = [];
    const numParticulas = 50;

    class Particula {
      constructor() {
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.vx = (Math.random() - 0.5) * 4;
        this.vy = (Math.random() - 0.5) * 4;
        this.radio = 5 + Math.random() * 5;
        this.color = `hsl(${Math.random() * 60 + 180}, 70%, 60%)`;
      }

      actualizar() {
        this.x += this.vx;
        this.y += this.vy;

        if (this.x < this.radio || this.x > canvas.width - this.radio) this.vx *= -1;
        if (this.y < this.radio || this.y > canvas.height - this.radio) this.vy *= -1;
      }

      dibujar() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.radio, 0, Math.PI * 2);
        ctx.fillStyle = this.color;
        ctx.fill();
      }
    }

    for (let i = 0; i < numParticulas; i++) particulas.push(new Particula());

    function aplicarFuerzas() {
      for (let i = 0; i < particulas.length; i++) {
        for (let j = i + 1; j < particulas.length; j++) {
          const dx = particulas[j].x - particulas[i].x;
          const dy = particulas[j].y - particulas[i].y;
          const distancia = Math.sqrt(dx * dx + dy * dy);

          if (distancia < 100 && distancia > 20) {
            const fuerza = 0.01 / distancia;
            particulas[i].vx += dx * fuerza;
            particulas[i].vy += dy * fuerza;
            particulas[j].vx -= dx * fuerza;
            particulas[j].vy -= dy * fuerza;
          }

          if (distancia < 20) {
            particulas[i].vx -= dx * 0.05;
            particulas[i].vy -= dy * 0.05;
            particulas[j].vx += dx * 0.05;
            particulas[j].vy += dy * 0.05;
          }
        }
      }
    }

    function animar() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      aplicarFuerzas();
      particulas.forEach(p => { p.actualizar(); p.dibujar(); });
      requestAnimationFrame(animar);
    }
    animar();
  </script>
</body>
</html>
```

Errores comunes a evitar:
- No limitar el número de partículas puede ralentizar mucho el navegador
- Olvidar el rebote contra las paredes hace que las partículas desaparezcan
- Fuerzas muy fuertes pueden hacer que las partículas se aceleren sin control
- Usar distancias muy pequeñas en el cálculo de fuerzas puede causar divisiones por números muy pequeños

## Conclusión

Este ejercicio me ha servido para entender cómo funcionan los sistemas de partículas que se usan en muchos videojuegos y animaciones. Al principio parecía complicado calcular las fuerzas entre todas las partículas, pero con el bucle doble que compara cada partícula con todas las demás se ve claro que no es tan difícil. Me ha resultado especialmente interesante ver cómo combinando atracción y repulsión se puede crear un comportamiento que parece inteligente aunque sea puramente matemático.

Creo que estos conceptos los puedo aplicar en futuros proyectos de juegos, por ejemplo para crear sistemas de partículas de explosiones, efectos de agua, o incluso para simular comportamiento de enemigos en un juego de estrategia. También relacionado con la unidad de programación de servicios, veo que estos cálculos matemáticos son similares a los que se usan en algoritmos de IA para mover entidades de forma inteligente.
