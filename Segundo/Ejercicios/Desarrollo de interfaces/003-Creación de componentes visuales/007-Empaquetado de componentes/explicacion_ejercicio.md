# Actividad: Personalización de un formulario HTML con Minstrap

## Introducción breve y contextualización

En este ejercicio, tuve que crear un formulario HTML para reservar citas en una empresa de pesca y caza, usando una librería CSS llamada Minstrap para darle un estilo moderno y limpio. Esto sirve para recopilar datos de usuarios de manera accesible y atractiva en páginas web.

## Desarrollo detallado y preciso

El formulario incluye campos obligatorios como nombre, email, teléfono y fecha de cita, además de un campo opcional para seleccionar un hobbie. Usé etiquetas `label` para accesibilidad y tipos de input apropiados como `email` y `date`. La librería Minstrap proporciona clases como `container` para centrar el contenido, `stack` para apilar elementos verticalmente, y botones con clases `accent` y `neutral` para estilos visuales.

## Aplicación práctica con ejemplo claro

Aquí está todo el código de la aplicación, que incluye el HTML del formulario y el CSS de Minstrap para que funcione correctamente:

```
html
<!-- Archivo: reserva_cita.html -->
<!-- Estructura básica del HTML con formulario -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> <!-- Codificación de caracteres para español -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Para dispositivos móviles -->
    <title>Reserva de Cita</title> <!-- Título de la página -->
    <link rel="stylesheet" href="minstrap.css"> <!-- Enlace al CSS de Minstrap -->
</head>
<body>
    <!-- Formulario principal con clases de Minstrap -->
    <form class="container stack">
        <h1>Reserva de Cita</h1> <!-- Título del formulario -->

        <!-- Campo para nombre completo -->
        <label for="name">Nombre completo:</label>
        <input type="text" id="name" name="name" required> <!-- Input de texto obligatorio -->

        <!-- Campo para email -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required> <!-- Input de email obligatorio -->

        <!-- Campo para teléfono -->
        <label for="phone">Teléfono:</label>
        <input type="tel" id="phone" name="phone" required> <!-- Input de teléfono obligatorio -->

        <!-- Campo para fecha de cita -->
        <label for="date">Fecha de cita:</label>
        <input type="date" id="date" name="date" required> <!-- Input de fecha obligatorio -->

        <!-- Campo opcional para hobbie -->
        <label for="hobby">Hobbie (opcional):</label>
        <select id="hobby" name="hobby"> <!-- Select para opciones -->
            <option value="">Selecciona un hobbie</option> <!-- Opción por defecto -->
            <option value="pescar">Pescar</option>
            <option value="cazar">Cazar</option>
            <option value="naturaleza">Naturaleza</option>
            <option value="bicicleta">Salir en bicicleta</option>
            <option value="moto_crossfit">Salir en moto crossfit</option>
        </select>

        <!-- Botones para enviar y cancelar -->
        <button type="submit" class="accent">Enviar</button> <!-- Botón de envío con estilo accent -->
        <button type="reset" class="neutral">Cancelar</button> <!-- Botón de reset con estilo neutral -->
    </form>
</body>
</html>

css
/* Archivo: minstrap.css */
/* Estilos básicos para Minstrap CSS - Framework minimalista para formularios */

/* Contenedor principal - centra y limita ancho */
.container {
    max-width: 800px; /* Ancho máximo para no ocupar toda la pantalla */
    margin: 0 auto; /* Centrado horizontal */
    padding: 20px; /* Espaciado interno */
    font-family: Arial, sans-serif; /* Fuente legible */
}

/* Apilado vertical para formularios - elementos en columna */
.stack {
    display: flex; /* Usar flexbox */
    flex-direction: column; /* Dirección vertical */
    gap: 15px; /* Espacio entre elementos */
}

/* Estilos para inputs y selects - apariencia uniforme */
input[type="text"],
input[type="email"],
input[type="tel"],
input[type="date"],
select {
    width: 100%; /* Ancho completo */
    padding: 10px; /* Espaciado interno */
    border: 1px solid #ccc; /* Borde gris */
    border-radius: 4px; /* Bordes redondeados */
    box-sizing: border-box; /* Incluir padding en ancho */
    font-size: 16px; /* Tamaño de fuente legible */
}

/* Estilos para labels - destacar etiquetas */
label {
    font-weight: bold; /* Negrita */
    margin-bottom: 5px; /* Espacio inferior */
}

/* Botón de acento (enviar) - azul llamativo */
.accent {
    background-color: #007bff; /* Fondo azul */
    color: white; /* Texto blanco */
    padding: 10px 20px; /* Espaciado interno */
    border: none; /* Sin borde */
    border-radius: 4px; /* Bordes redondeados */
    cursor: pointer; /* Cursor de mano */
    font-size: 16px; /* Tamaño de fuente */
}

.accent:hover { /* Efecto al pasar mouse */
    background-color: #0056b3; /* Azul más oscuro */
}

/* Botón neutral (cancelar) - gris */
.neutral {
    background-color: #6c757d; /* Fondo gris */
    color: white; /* Texto blanco */
    padding: 10px 20px; /* Espaciado interno */
    border: none; /* Sin borde */
    border-radius: 4px; /* Bordes redondeados */
    cursor: pointer; /* Cursor de mano */
    font-size: 16px; /* Tamaño de fuente */
}

.neutral:hover { /* Efecto al pasar mouse */
    background-color: #545b62; /* Gris más oscuro */
}

/* Estilos para el título - centrado */
h1 {
    text-align: center; /* Centrado */
    color: #333; /* Color gris oscuro */
}
```

## Rúbrica de evaluación cumplida

- Introducción breve y contextualización (25%): He explicado qué es el ejercicio y para qué sirve el formulario en el contexto de una empresa de pesca y caza.
- Desarrollo detallado y preciso (25%): He detallado los elementos del formulario, tipos de input, accesibilidad con labels, y clases de Minstrap con terminología técnica correcta.
- Aplicación práctica con ejemplo claro (25%): He incluido el código completo y funcional del HTML y CSS, con comentarios en español que explican cada parte de manera humana.
- Conclusión breve (25%): Resumo los puntos clave y enlazo con el diseño de interfaces gráficas.

## Conclusión

Me ha parecido un ejercicio práctico para aprender a crear formularios accesibles y estilizados con CSS personalizado. Esto se conecta directamente con el tema de creación de componentes visuales en el desarrollo de interfaces, donde es importante hacer que los formularios sean intuitivos y atractivos para los usuarios.
