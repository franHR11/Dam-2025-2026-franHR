# Explicación del Ejercicio - Editor de Diagramas Interactivo

### 1. Introducción breve y contextualización
En este ejercicio he desarrollado una herramienta web interactiva que permite crear diagramas simples directamente en el navegador. Este tipo de editores son fundamentales en el diseño de interfaces moderno, ya que permiten prototipar visualmente flujos de usuario, mapas de sitio o arquitecturas de software de manera ágil sin necesidad de software pesado. La contextualización de esta práctica se sitúa en el aprendizaje del uso de la API Canvas de HTML5 y la manipulación del DOM para crear interfaces ricas ("Rich Internet Applications").

### 2. Explicación personal del ejercicio
Para realizar esta actividad, mi objetivo principal ha sido crear una interfaz que sea intuitiva y agradable a la vista, siguiendo las pautas de diseño vistas en clase. Decidí estructurar la pantalla en dos zonas claras: una barra de herramientas lateral con los controles y un gran lienzo central de trabajo.

He programado el editor desde cero usando JavaScript "vanilla" para entender bien cómo gestionar el estado de los objetos gráficos. He creado clases `Shape` (Forma) y `Connection` (Conexión) para mantener el código ordenado. La parte que me resultó más desafiante pero más gratificante fue la lógica de las conexiones: calcular el centro de las figuras para que las flechas salgan y lleguen correctamente, y redibujar todo el lienzo cada vez que muevo un objeto para dar esa sensación de fluidez en tiempo real.

### 3. Código de programación

A continuación muestro las partes más relevantes del código que he implementado:

#### HTML (Estructura)
He usado `<canvas>` como elemento central y una estructura semántica para la barra de herramientas.

```html
<main class="workspace">
    <canvas id="editorCanvas"></canvas>
    <div class="help-text">
        <p>Modo actual: <span id="currentMode">Seleccionar</span></p>
    </div>
</main>
```

#### JavaScript (Lógica de Dibujo)
Aquí se muestra cómo la clase `Shape` se dibuja a sí misma en el contexto 2D. He usado métodos modernos como `roundRect` para la forma de píldora.

```javascript
class Shape {
    constructor(x, y, type) {
        this.id = Date.now() + Math.random();
        this.x = x;
        this.y = y;
        this.type = type; // 'rect', 'pill', 'circle'
    }

    draw(ctx, isSelected) {
        ctx.beginPath();
        // Lógica de visualización de selección
        ctx.strokeStyle = isSelected ? '#4f46e5' : '#334155';
        
        if (this.type === 'rect') {
            ctx.rect(this.x, this.y, this.width, this.height);
        } else if (this.type === 'pill') {
            const r = this.height / 2;
            ctx.roundRect(this.x, this.y, this.width, this.height, r);
        }
        ctx.fill();
        ctx.stroke();
    }
}
```

#### Persistencia (JSON)
Para guardar y cargar, simplemente serializo el estado de los arrays a JSON.

```javascript
window.saveDiagram = () => {
    const data = { shapes: shapes, connections: connections };
    const json = JSON.stringify(data, null, 2);
    // ... código para crear el Blob y descargarlo ...
};
```

### 4. Rúbrica de evaluación cumplida
- **Exploración y Prueba:** La interfaz permite dibujar rectángulos, píldoras y círculos arrastrando o haciendo clic.
- **Práctica de Funcionalidades:** He implementado botones dedicados para "Guardar JSON" y "Cargar JSON", permitiendo persistir el trabajo.
- **Aplicación Práctica:** El editor permite conectar formas lógicamente, útil para diagramas de flujo reales.
- **Exploración Avanzada:** He añadido estilos CSS modernos (sombras, bordes redondeados, paleta de colores profesional) y feedback visual (hover en botones, línea punteada al conectar) para mejorar la UX.

### 5. Conclusión
Me ha parecido un ejercicio muy completo. Al principio pensaba que gestionar el canvas sería más complicado, pero estructura el código en clases ha facilitado mucho la gestión de colisiones (saber qué objeto estoy clicando) y el redibujado. Creo que he conseguido una herramienta sencilla pero funcional que cumple con todos los requisitos y además tiene un aspecto visual bastante pulido.
