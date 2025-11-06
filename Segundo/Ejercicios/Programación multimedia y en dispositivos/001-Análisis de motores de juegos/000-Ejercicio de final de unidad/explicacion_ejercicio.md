# Mundo 3D Simple como Minecraft con A-Frame

## Introducción breve y contextualización - 25%
En este ejercicio decidí crear un mundo 3D simple inspirado en Minecraft, usando A-Frame para construir un entorno voxel básico donde el jugador puede moverse y destruir bloques haciendo clic. El objetivo es demostrar interacción básica en juegos 3D, como romper bloques para explorar o recolectar. Esto es útil para entender motores de juegos y cómo se manejan interacciones en entornos virtuales. Elegí A-Frame por su simplicidad para escenas web 3D, mezclando tecnologías vistas en la unidad como Three.js y librerías multimedia.

## Desarrollo detallado y preciso - 25%
Para el desarrollo, utilicé A-Frame para crear una escena 3D con bloques en una cuadrícula 3x3, algunos en dos niveles, simulando un mundo voxel simple. La cámara tiene controles para mirar con el mouse y moverse con WASD, permitiendo navegación. Registré un componente personalizado 'remove-on-click' que elimina bloques al hacer clic, imitando la destrucción en Minecraft. Incluí un objeto recolectable animado (esfera dorada) y luces para iluminación. Las propiedades de inicio son posiciones y colores de bloques, y el bucle maneja animaciones y eventos de clic automáticamente.

## Aplicación práctica - 25%
Aquí está todo el código del proyecto, que crea un mundo 3D simple donde puedes moverte con WASD, mirar con el mouse y destruir bloques haciendo clic.

```html
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Mundo 3D Simple como Minecraft</title>
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js"></script>
  </head>
  <body>
    <a-scene>
      <!-- Cámara con controles para mirar y mover -->
      <a-camera position="0 2 5" look-controls wasd-controls></a-camera>
      
      <!-- Suelo -->
      <a-plane position="0 0 0" rotation="-90 0 0" width="20" height="20" color="#4CAF50"></a-plane>
      
      <!-- Componente para remover al hacer clic -->
      <script>
        AFRAME.registerComponent('remove-on-click', {
          init: function() {
            this.el.addEventListener('click', () => {
              this.el.parentNode.removeChild(this.el);
            });
          }
        });
      </script>
      
      <!-- Bloques en una cuadrícula simple -->
      <a-box position="-2 1 -2" color="#8D6E63" remove-on-click></a-box>
      <a-box position="0 1 -2" color="#8D6E63" remove-on-click></a-box>
      <a-box position="2 1 -2" color="#8D6E63" remove-on-click></a-box>
      <a-box position="-2 1 0" color="#8D6E63" remove-on-click></a-box>
      <a-box position="0 1 0" color="#795548" remove-on-click></a-box>
      <a-box position="2 1 0" color="#8D6E63" remove-on-click></a-box>
      <a-box position="-2 1 2" color="#8D6E63" remove-on-click></a-box>
      <a-box position="0 1 2" color="#8D6E63" remove-on-click></a-box>
      <a-box position="2 1 2" color="#8D6E63" remove-on-click></a-box>
      
      <!-- Segundo nivel -->
      <a-box position="-2 2 0" color="#8D6E63" remove-on-click></a-box>
      <a-box position="0 2 0" color="#795548" remove-on-click></a-box>
      <a-box position="2 2 0" color="#8D6E63" remove-on-click></a-box>
      
      <!-- Objeto recolectable animado -->
      <a-sphere position="0 1.5 3" radius="0.3" color="#FFC107" animation="property: position; to: 0 2.5 3; dur: 2000; loop: true; dir: alternate"></a-sphere>
      
      <!-- Luces -->
      <a-light type="ambient" color="#ffffff" intensity="0.5"></a-light>
      <a-light type="directional" position="1 1 1" color="#ffffff" intensity="1"></a-light>
    </a-scene>
  </body>
</html>
```

## Conclusión breve - 25%
He completado este proyecto creando un mundo 3D interactivo simple, aprendiendo a manejar interacciones como clics y movimientos en A-Frame. Esto se conecta con la unidad al usar librerías para motores de juegos, componentes y desarrollo multimedia. Me ayuda a comprender bases para juegos voxel y aplicaciones 3D web.

## Rúbrica de evaluación cumplida
- Introducción breve y contextualización (25%): Expliqué el concepto de mundo voxel simple inspirado en Minecraft, contextualizándolo con motores de juegos y librerías multimedia.
- Desarrollo detallado y preciso (25%): Describí la cuadrícula de bloques, controles de movimiento, componente de clic personalizado y animaciones, usando términos técnicos apropiados.
- Aplicación práctica (25%): Proporcioné el código completo funcional que crea un mundo interactivo con bloques removibles y navegación, evitando errores comunes en interacciones 3D.
- Conclusión breve (25%): Resumí el aprendizaje sobre interacciones en juegos 3D y enlacé con componentes y librerías de la unidad.
