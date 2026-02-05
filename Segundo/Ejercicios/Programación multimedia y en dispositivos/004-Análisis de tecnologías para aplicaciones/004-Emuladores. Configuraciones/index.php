<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAMEify - Tu música</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <!-- Incluimos los componentes -->
    <?php include 'pantalla_inicio.php'; ?>
    <?php include 'pantalla_lista.php'; ?>

    <!-- Lógica principal de navegación -->
    <script>
        // Función para cambiar a la pantalla de lista
        function mostrarDetalles(datos) {
            // Ocultar inicio y mostrar lista
            document.getElementById('contenedor-inicio').style.display = 'none';
            document.getElementById('contenedor-lista').style.display = 'block';

            // Actualizar datos de la cabecera
            document.getElementById('img-detalle').src = datos.image;
            document.getElementById('titulo-detalle').textContent = datos.artist;

            // Rellenar lista de canciones (simulado con la única canción del JSON)
            const lista = document.getElementById('lista-canciones');
            lista.innerHTML = ''; // Limpiar lista anterior
            
            const li = document.createElement('li');
            li.style.display = 'flex';
            li.style.alignItems = 'center';
            li.innerHTML = `
                <span style="margin-right: 15px; color: #1db954;">▶</span>
                <div>
                    <div style="color: white; font-weight: bold;">${datos.song}</div>
                    <div style="font-size: 14px;">${datos.artist}</div>
                </div>
            `;
            lista.appendChild(li);
            
            // Cambiar fondo dinámicamente (opcional, detalle visual extra)
            document.getElementById('contenedor-lista').style.background = `linear-gradient(180deg, ${getRandomColor()} 0%, #121212 100%)`;
        }

        // Función para volver al inicio
        function volverInicio() {
            document.getElementById('contenedor-lista').style.display = 'none';
            document.getElementById('contenedor-inicio').style.display = 'block';
        }

        // Helper para color aleatorio en el fondo (estética)
        function getRandomColor() {
            const colors = ['#535353', '#b02897', '#2e77d0', '#e91429', '#1db954'];
            return colors[Math.floor(Math.random() * colors.length)];
        }
    </script>
</body>
</html>
