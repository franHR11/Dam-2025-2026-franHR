# Explicación del Ejercicio: Estilizar Gráficas con HTML y CSS

## 1.- Introducción breve y contextualización - 25% de la nota del ejercicio

Las gráficas son herramientas fundamentales en el desarrollo de interfaces de usuario modernas, ya que permiten visualizar datos de manera intuitiva y atractiva. En el contexto de la pesca deportiva, las estadísticas visuales ayudan a los pescadores a analizar sus patrones de captura, identificar las mejores horas del día y las especies más abundantes en sus zonas de pesca. Este ejercicio demuestra cómo podemos crear representaciones visuales efectivas utilizando únicamente HTML y CSS, sin necesidad de librerías externas o JavaScript, aplicando principios fundamentales de diseño y maquetación web.

## 2.- Desarrollo detallado y preciso - 25% de la nota del ejercicio

### Estructura HTML
He creado una estructura semántica con cuatro contenedores principales, cada uno representando un tipo diferente de gráfica:

1. **Gráfica 1 (grafica1)**: Barras horizontales para mostrar capturas por día
2. **Gráfica 2 (grafica2)**: Barras verticales para visualizar peces por especie  
3. **Gráfica 3 (grafica3)**: Gráfica circular para mostrar proporciones
4. **Gráfica 4 (grafica4)**: Gráfica de puntos para indicar capturas por hora

Cada gráfica contiene elementos específicos y una leyenda descriptiva posicionada en la parte inferior con un margen de 36px para evitar interferencias visuales.

### Propiedades CSS fundamentales
- **position**: Utilizada para controlar el posicionamiento de elementos dentro de las gráficas
- **width y height**: Definen las dimensiones de las barras y elementos gráficos
- **aspect-ratio**: Mantiene proporciones uniformes en todas las gráficas (16:9)
- **margin-bottom: 36px**: Espacio reservado específicamente para las leyendas
- **background**: Gradientes de color para dar profundidad y atractivo visual
- **border-radius**: Redondea los bordes para un aspecto moderno
- **box-shadow**: Añade profundidad y separación visual

### Técnicas de estilización específicas
Para cada tipo de gráfica apliqué técnicas diferentes:
- **Barras horizontales**: Gradientes lineales y animaciones de crecimiento
- **Barras verticales**: Alineación flexible y animaciones desde la base
- **Gráfica circular**: Transformaciones rotativas y posicionamiento absoluto
- **Gráfica de puntos**: Posicionamiento absoluto y animaciones de aparición

## 3.- Aplicación práctica - 25% de la nota del ejercicio

### Código completo de la aplicación

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Estadísticas de Pesca</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <!-- Contenedor principal de mi aplicación -->
    <div class="contenedor">
        <!-- Título principal de mi aplicación -->
        <header>
            <h1>Mis Estadísticas de Pesca Diaria</h1>
            <p>Registro de mis capturas durante la semana</p>
        </header>

        <!-- Sección de gráficas -->
        <main>
            <!-- Gráfica 1: Barras horizontales - Capturas por día -->
            <div class="grafica grafica1">
                <div class="barra" style="width: 80%;"></div>
                <div class="barra" style="width: 60%;"></div>
                <div class="barra" style="width: 90%;"></div>
                <div class="barra" style="width: 45%;"></div>
                <div class="barra" style="width: 70%;"></div>
                <div class="leyenda">Capturas por día (lunes a viernes)</div>
            </div>

            <!-- Gráfica 2: Barras verticales - Peces por especie -->
            <div class="grafica grafica2">
                <div class="barra-vertical" style="height: 70%;"></div>
                <div class="barra-vertical" style="height: 85%;"></div>
                <div class="barra-vertical" style="height: 50%;"></div>
                <div class="barra-vertical" style="height: 95%;"></div>
                <div class="leyenda">Peces por especie (trucha, lucio, carpa, perca)</div>
            </div>

            <!-- Gráfica 3: Gráfica circular - Proporción de capturas -->
            <div class="grafica grafica3">
                <div class="circulo">
                    <div class="sector sector1"></div>
                    <div class="sector sector2"></div>
                    <div class="sector sector3"></div>
                    <div class="sector sector4"></div>
                </div>
                <div class="leyenda">Distribución por tipo de pesca</div>
            </div>

            <!-- Gráfica 4: Gráfica de puntos - Capturas por hora -->
            <div class="grafica grafica4">
                <div class="punto" style="left: 20%; top: 30%;"></div>
                <div class="punto" style="left: 35%; top: 60%;"></div>
                <div class="punto" style="left: 50%; top: 45%;"></div>
                <div class="punto" style="left: 65%; top: 75%;"></div>
                <div class="punto" style="left: 80%; top: 40%;"></div>
                <div class="leyenda">Mejores horas para pescar</div>
            </div>
        </main>

        <!-- Pie de página -->
        <footer>
            <p>© 2025 - Mi Diario de Pesca</p>
        </footer>
    </div>
</body>
</html>
```

```css
/* Estilos generales de mi página */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    background-color: #f0f8ff; /* Color de cielo para tema de pesca */
    color: #333;
    line-height: 1.6;
}

.contenedor {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Estilos del encabezado */
header {
    text-align: center;
    margin-bottom: 40px;
    padding: 20px;
    background: linear-gradient(135deg, #1e90ff, #4682b4);
    color: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

header h1 {
    font-size: 2.5em;
    margin-bottom: 10px;
}

header p {
    font-size: 1.2em;
    opacity: 0.9;
}

/* Estilos principales para las gráficas */
main {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

/* Estilos base para todas las gráficas */
.grafica {
    position: relative;
    width: 100%;
    aspect-ratio: 16/9; /* Mantengo proporción uniforme */
    background-color: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    margin-bottom: 36px; /* Espacio reservado para la leyenda */
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.grafica:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

/* Estilos para la leyenda de todas las gráficas */
.leyenda {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: #2c3e50;
    color: white;
    padding: 10px;
    text-align: center;
    font-size: 0.9em;
    border-bottom-left-radius: 15px;
    border-bottom-right-radius: 15px;
}

/* Gráfica 1: Barras horizontales - Capturas por día */
.grafica1 {
    background: linear-gradient(to right, #e8f5e8, #f0fff0);
}

.grafica1 .barra {
    height: 30px;
    background: linear-gradient(to right, #4caf50, #8bc34a);
    margin: 15px 0;
    border-radius: 15px;
    position: relative;
    animation: crecerBarra 1.5s ease-out;
    box-shadow: 0 2px 4px rgba(76, 175, 80, 0.3);
}

/* Gráfica 2: Barras verticales - Peces por especie */
.grafica2 {
    background: linear-gradient(to bottom, #e3f2fd, #bbdefb);
    display: flex;
    align-items: flex-end;
    justify-content: space-around;
    padding-bottom: 50px;
}

.grafica2 .barra-vertical {
    width: 60px;
    background: linear-gradient(to top, #2196f3, #03a9f4);
    border-radius: 10px 10px 0 0;
    position: relative;
    animation: crecerBarraVertical 1.5s ease-out;
    box-shadow: 0 -2px 4px rgba(33, 150, 243, 0.3);
}

/* Gráfica 3: Gráfica circular - Proporción de capturas */
.grafica3 {
    background: radial-gradient(circle, #fff3e0, #ffe0b2);
    display: flex;
    justify-content: center;
    align-items: center;
}

.grafica3 .circulo {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(255, 152, 0, 0.3);
}

.grafica3 .sector {
    position: absolute;
    width: 50%;
    height: 50%;
    transform-origin: right bottom;
}

.grafica3 .sector1 {
    background-color: #ff9800;
    transform: rotate(0deg);
}

.grafica3 .sector2 {
    background-color: #ffc107;
    transform: rotate(90deg);
}

.grafica3 .sector3 {
    background-color: #ff5722;
    transform: rotate(180deg);
}

.grafica3 .sector4 {
    background-color: #ffeb3b;
    transform: rotate(270deg);
}

/* Gráfica 4: Gráfica de puntos - Capturas por hora */
.grafica4 {
    background: linear-gradient(135deg, #f3e5f5, #e1bee7);
    position: relative;
}

.grafica4 .punto {
    position: absolute;
    width: 20px;
    height: 20px;
    background: radial-gradient(circle, #9c27b0, #7b1fa2);
    border-radius: 50%;
    box-shadow: 0 2px 6px rgba(156, 39, 176, 0.4);
    animation: aparecerPunto 1s ease-out;
}

/* Pie de página */
footer {
    text-align: center;
    padding: 20px;
    background-color: #34495e;
    color: white;
    border-radius: 10px;
    margin-top: 40px;
}

/* Animaciones para hacer las gráficas más dinámicas */
@keyframes crecerBarra {
    from {
        width: 0;
    }
}

@keyframes crecerBarraVertical {
    from {
        height: 0;
    }
}

@keyframes aparecerPunto {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

/* Diseño responsivo para móviles */
@media (max-width: 768px) {
    main {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .grafica {
        aspect-ratio: 4/3;
    }
    
    header h1 {
        font-size: 2em;
    }
    
    .grafica3 .circulo {
        width: 150px;
        height: 150px;
    }
}
```

### Errores comunes y cómo evitarlos
1. **No reservar espacio para leyendas**: Es fundamental usar margin-bottom: 36px para evitar que el texto se superponga
2. **Proporciones inconsistentes**: El uso de aspect-ratio garantiza que todas las gráficas mantengan la misma proporción
3. **Colores poco contrastantes**: He utilizado gradientes y sombras para mejorar la legibilidad
4. **Diseño no responsivo**: Las media queries aseguran que las gráficas se adapten a diferentes tamaños de pantalla

## 4.- Conclusión breve - 25% de la nota del ejercicio

Este ejercicio demuestra cómo es posible crear visualizaciones de datos atractivas y funcion utilizando únicamente HTML y CSS, aplicando principios fundamentales de diseño de interfaces. Las técnicas aprendidas aquí se conectan directamente con los conceptos de generación de interfaces de usuario, mostrando que podemos crear componentes visuales complejos sin depender de librerías externas. Estas habilidades son transferibles a cualquier proyecto web que requiera presentación de datos, desde dashboards administrativos hasta aplicaciones de seguimiento personal. La capacidad de crear gráficas con CSS puro nos da un control total sobre el aspecto visual y optimiza el rendimiento de nuestras aplicaciones.