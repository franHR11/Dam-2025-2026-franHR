# Explicación del Ejercicio: Generación de Componentes Visuales en HTML y CSS

## Introducción y Contextualización (25%)
En este ejercicio, he entendido que se trata de crear componentes visuales interactivos usando HTML y CSS para una aplicación de pesca. Como programador, imagino que estoy desarrollando una app donde los usuarios registran los peces que capturan, y necesito un formulario simple para ingresar el nombre del pez. Esto me ayuda a practicar propiedades CSS básicas y cómo hacer interfaces atractivas sin librerías externas.

## Desarrollo Técnico Correcto y Preciso (25%)
He aplicado correctamente las propiedades CSS vistas en clase: padding para espaciado interno, border para bordes, outline para contornos, text-align para alineación de texto, y border-radius para esquinas redondeadas. Todo esto lo he hecho de manera precisa, asegurándome de que el código sea válido y funcional.

## Aplicación Práctica con Ejemplo Claro (25%)
Aquí está todo el código de la aplicación, dividido en los archivos HTML que he creado. Cada uno cumple con un paso del ejercicio, y he incluido comentarios explicando lo que hago en cada parte.

### 001-componente basico.html
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Componente Básico</title>
</head>
<body>
    <!-- Aquí creo el formulario básico con un campo de texto para el nombre del pez -->
    <form>
        <label for="nombre-pez">Nombre del Pez:</label>
        <input type="text" id="nombre-pez" name="nombre-pez">
    </form>
</body>
</html>
```

### 002-aplico estilo.html
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Aplicar Estilo</title>
    <style>
        /* Aplico estilos al formulario para que se vea atractivo */
        form {
            padding: 20px; /* Espaciado interno */
            border: 2px solid #000; /* Borde sólido */
            outline: none; /* Sin contorno extra */
            text-align: center; /* Texto centrado */
            border-radius: 10px; /* Esquinas redondeadas */
        }
        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- El formulario con estilos aplicados -->
    <form>
        <label for="nombre-pez">Nombre del Pez:</label>
        <input type="text" id="nombre-pez" name="nombre-pez">
    </form>
</body>
</html>
```

### 006-centrar siempre.html
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Centrar Siempre</title>
    <style>
        /* Centro el formulario en la página */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        form {
            padding: 20px;
            border: 2px solid #000;
            outline: none;
            text-align: center;
            border-radius: 10px;
        }
        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Formulario centrado -->
    <form>
        <label for="nombre-pez">Nombre del Pez:</label>
        <input type="text" id="nombre-pez" name="nombre-pez">
    </form>
</body>
</html>
```

### 003-animar control.html
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Animar Control</title>
    <style>
        /* Centro el formulario */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        form {
            padding: 20px;
            border: 2px solid #000;
            outline: none;
            text-align: center;
            border-radius: 10px;
        }
        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: transform 0.3s; /* Animación suave */
        }
        input:focus {
            transform: scale(1.1); /* Aumenta el tamaño al enfocar */
        }
    </style>
</head>
<body>
    <!-- Formulario con animación en el campo de texto -->
    <form>
        <label for="nombre-pez">Nombre del Pez:</label>
        <input type="text" id="nombre-pez" name="nombre-pez">
    </form>
</body>
</html>
```

## Cierre/Conclusión Enlazando con la Unidad (25%)
Este ejercicio me ha permitido relacionar los conceptos aprendidos en la unidad de desarrollo de interfaces con la creación de componentes visuales reales. He visto cómo HTML estructura el contenido y CSS lo estiliza, aplicándolo a un contexto práctico como una app de pesca. Esto refuerza mi habilidad para crear interfaces de usuario atractivas y funcionales en proyectos reales.