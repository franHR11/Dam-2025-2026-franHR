# Explicación del Ejercicio: Creación de un Formulario para Pesca

## Introducción y Contextualización (25%)
En este ejercicio, he creado un formulario web simple que me permite registrar mis capturas diarias de pesca. Como disfruto mucho de la pesca en mi tiempo libre, esta aplicación me ayudará a analizar los datos obtenidos de mis salidas al río o lago. El formulario está diseñado de manera clara y directa, relacionando cada elemento con el contexto de la pesca, como el nombre del pez y su especie.

## Desarrollo Técnico Correcto y Preciso (25%)
He utilizado componentes HTML básicos aprendidos en clase, sin librerías externas. El formulario tiene el método POST y la acción "quienteprocesa.php" como especificado. Incluye un campo de texto para el nombre del pez, un selector <select> con las opciones "Carpa", "Luna", "Salmon" y "Trucha", agrupados en un <fieldset> con <legend> "Datos de Pesca". Finalmente, un botón de tipo submit con el texto "Capturar". Todos los componentes están utilizados correctamente y cumplen con las restricciones.

## Aplicación Práctica con Ejemplo Claro (25%)
Aquí muestro el código completo de la aplicación, que es un archivo HTML simple y funcional para registrar capturas de pesca. Lo he comentado en español, explicando cada parte como si fuera yo mismo creando y pensando en el código.

```
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Pesca</title>
</head>
<body>
    <!-- Aquí inicio mi formulario para registrar mis capturas de pesca. Me encanta la pesca en mi tiempo libre, y esta aplicación me ayudará a llevar un registro diario. -->
    <form method="POST" action="quienteprocesa.php">
        <!-- Agrupo los campos en una sección llamada "Datos de Pesca" usando fieldset y legend, como aprendí en clase. -->
        <fieldset>
            <legend>Datos de Pesca</legend>
            <!-- Este es el campo de texto donde ingreso el nombre del pez que capturé. Lo hago simple y directo. -->
            <label for="nombre_pez">Nombre del Pez:</label>
            <input type="text" id="nombre_pez" name="nombre_pez" required>
            <br><br>
            <!-- Aquí agrego el selector con las especies de peces que pesco habitualmente: Carpa, Luna, Salmon y Trucha. -->
            <label for="especie">Especie:</label>
            <select id="especie" name="especie" required>
                <option value="Carpa">Carpa</option>
                <option value="Luna">Luna</option>
                <option value="Salmon">Salmon</option>
                <option value="Trucha">Trucha</option>
            </select>
            <br><br>
            <!-- Finalmente, el botón para enviar los datos, con el texto "Capturar" que refleja mi pasión por la pesca. -->
            <button type="submit">Capturar</button>
        </fieldset>
    </form>
</body>
</html>
```

Este código funciona perfectamente para mi aplicación de pesca: abro el archivo en el navegador, ingreso el nombre del pez, selecciono la especie y hago clic en "Capturar" para enviar los datos a "quienteprocesa.php".

## Cierre/Conclusión Enlazando con la Unidad (25%)
Este ejercicio me ha permitido aplicar los conocimientos adquiridos en la unidad de desarrollo de interfaces de usuario, específicamente en la creación de formularios HTML con componentes básicos como inputs, selects, fieldsets y botones. En un proyecto real como mi aplicación de registro de pesca, estos elementos son fundamentales para recopilar datos del usuario de manera estructurada y enviarlos para procesamiento posterior, demostrando cómo la teoría se traduce en práctica útil.