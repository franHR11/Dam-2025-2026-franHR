<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Favoritos</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="contenedor">
        <h1>🎣 Inicio / Favoritos</h1>
        <p>Aquí están tus canciones guardadas para el viaje.</p>

        <div id="contenedor-favoritos">
            <p>Cargando favoritos...</p>
        </div>

        <a href="pantalla_lista.php">Volver a la lista de canciones</a>
        <br><br>
        <button onclick="borrarTodo()" style="background-color: #666;">Borrar todo (Reset)</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            mostrarFavoritos();
        });

        function mostrarFavoritos() {
            const contenedor = document.getElementById('contenedor-favoritos');
            // Recuperar el estado guardado
            const favoritos = JSON.parse(localStorage.getItem('misFavoritos')) || [];

            if (favoritos.length === 0) {
                contenedor.innerHTML = "<p>No tienes canciones favoritas guardadas aún.</p>";
                return;
            }

            let html = "<ul style='list-style: none; padding: 0;'>";
            favoritos.forEach(cancion => {
                html += `<li class="cancion" style="justify-content: center;">🎵 ${cancion.titulo}</li>`;
            });
            html += "</ul>";
            
            contenedor.innerHTML = html;
        }

        function borrarTodo() {
            localStorage.removeItem('misFavoritos');
            mostrarFavoritos();
        }
    </script>
</body>
</html>
