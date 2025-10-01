# Explicación del Ejercicio: Animación 2D con Canvas y Programación

## Introducción y contextualización (25%)
Este ejercicio me ayuda a entender cómo animar un personaje en una pantalla usando HTML5 Canvas y JavaScript. Es importante porque en el desarrollo de juegos, controlar el movimiento y las posiciones de los elementos gráficos es fundamental. Aquí aplico conceptos básicos como Canvas, JavaScript, loops y eventos de teclado, que hemos visto en clase. Esto me prepara para crear juegos simples o animaciones interactivas.

## Desarrollo técnico correcto y preciso (25%)
Implemento la lógica para mover el personaje y actualizar la pantalla de manera correcta. Uso variables como posx y posy para la posición, y creo funciones separadas para dibujar el escenario y el personaje. El movimiento se hace en un bucle automático, asegurando que el personaje no salga del canvas. Combino las funciones en una sola para optimizar el código.

## Aplicación práctica con ejemplo claro (25%)
Aquí está todo el código de la aplicación, explicado paso a paso. Es un ejemplo simple de cómo animar un personaje que se mueve automáticamente por los tramos del escenario.

```html
<!doctype html>
<html>
  <body>
    <canvas width=512 height=512></canvas>
    <script>
      // Aquí defino las funciones que voy a usar
      function pintaEscenario(){
        // Pinto el escenario con bloques negros donde hay paredes
        for(let x = 0; x < 16; x++){
          for(let y = 0; y < 16; y++){
            if(escenario[y][x] == 1){
              contexto.fillStyle = "black";
              contexto.fillRect(x * anchurabloque, y * anchurabloque, anchurabloque, anchurabloque);
            }
          }
        }
      }
      
      function dibujaPersonaje(){
        // Dibujo el personaje como un círculo rojo en las coordenadas posx, posy
        contexto.fillStyle = "red";
        contexto.beginPath();
        contexto.arc(posx * anchurabloque + anchurabloque/2, posy * anchurabloque + anchurabloque/2, anchurabloque/2, 0, Math.PI*2);
        contexto.fill();
      }
      
      function actualizaPantalla(){
        // Combino pintar escenario y dibujar personaje en una función
        contexto.clearRect(0, 0, 512, 512);
        pintaEscenario();
        dibujaPersonaje();
      }
      
      function bucle(){
        // En el bucle, muevo el personaje automáticamente
        // Por ejemplo, lo muevo hacia la derecha si puede
        if(escenario[posy][posx + 1] != 1 && posx < 15){
          posx += 1;
        } else {
          // Si no puede, lo muevo hacia abajo o algo simple
          if(escenario[posy + 1][posx] != 1 && posy < 15){
            posy += 1;
          }
        }
        // Aseguro que no salga del canvas
        if(posx < 0) posx = 0;
        if(posx > 15) posx = 15;
        if(posy < 0) posy = 0;
        if(posy > 15) posy = 15;
        actualizaPantalla();
        requestAnimationFrame(bucle);
      }
      
      // Variables globales
      var anchurabloque = 32;
      let posx = 1; // Posición inicial x
      let posy = 1; // Posición inicial y
      const escenario = [
        [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],
        [1,3,2,2,2,2,2,2,2,2,2,2,2,2,3,1],
        [1,2,1,1,2,1,1,2,1,1,2,1,1,2,2,1],
        [1,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1],
        [1,2,1,1,2,1,2,1,1,1,2,1,2,1,2,1],
        [1,2,2,2,2,1,2,2,2,2,2,1,2,2,2,1],
        [1,2,1,1,2,1,1,4,4,1,2,1,1,2,2,1],
        [1,2,2,2,2,1,6,6,6,1,2,2,2,2,2,1],
        [1,2,1,1,2,1,6,6,6,1,2,1,1,2,2,1],
        [1,2,2,2,2,1,1,1,1,1,2,2,2,2,2,1],
        [1,2,1,1,2,2,2,2,2,2,2,1,1,2,2,1],
        [1,2,2,2,2,1,2,2,2,2,2,1,2,2,2,1],
        [1,2,1,1,2,1,2,5,2,1,2,1,2,1,2,1],
        [1,2,2,2,2,2,2,2,2,2,2,2,2,2,2,1],
        [1,3,2,2,2,2,2,2,2,2,2,2,2,2,3,1],
        [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1],
      ];
      const lienzo = document.querySelector("canvas");
      const contexto = lienzo.getContext("2d");
      
      // Inicio el bucle
      bucle();
    </script>
  </body>
</html>
```

## Cierre/Conclusión enlazando con la unidad (25%)
He completado el ejercicio entendiendo cómo animar personajes en 2D. Esto se aplica en proyectos reales como juegos simples, donde el movimiento automático o controlado es clave. Me siento más cómodo con Canvas y JavaScript para crear animaciones básicas.