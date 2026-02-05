<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Canciones</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="contenedor">
        <h1>🎵 Mis Canciones</h1>
        <p>Selecciona tus favoritas para llevarlas contigo (incluso de pesca).</p>
        
        <div id="lista-canciones">
            <!-- Las canciones se generan aquí o están estáticas -->
            <div class="cancion" data-id="1" data-titulo="La Marea">
                <span>La Marea - Artista A</span>
                <button onclick="toggleFavorito(1, 'La Marea')">❤ Favorito</button>
            </div>
            <div class="cancion" data-id="2" data-titulo="Bajo el Mar">
                <span>Bajo el Mar - Artista B</span>
                <button onclick="toggleFavorito(2, 'Bajo el Mar')">❤ Favorito</button>
            </div>
            <div class="cancion" data-id="3" data-titulo="Navegando">
                <span>Navegando - Artista C</span>
                <button onclick="toggleFavorito(3, 'Navegando')">❤ Favorito</button>
            </div>
        </div>

        <a href="pantalla_inicio.php">Ir a Inicio (Ver Favoritos)</a>
    </div>

    <script>
        // Función para gestionar los favoritos usando LocalStorage (persistencia de estado)
        function toggleFavorito(id, titulo) {
            // Obtener favoritos actuales o iniciar array vacío
            let favoritos = JSON.parse(localStorage.getItem('misFavoritos')) || [];
            
            // Comprobar si ya existe
            const existe = favoritos.some(f => f.id === id);

            if (existe) {
                // Si existe, lo quitamos (filter devuelve todos MENOS el que coincide)
                favoritos = favoritos.filter(f => f.id !== id);
                alert("Eliminado de favoritos: " + titulo);
            } else {
                // Si no existe, lo añadimos
                favoritos.push({ id: id, titulo: titulo });
                alert("Añadido a favoritos: " + titulo); // Feedback simple
            }

            // Guardar el nuevo estado
            localStorage.setItem('misFavoritos', JSON.stringify(favoritos));
            
            // Actualizar visualmente (opcional, pero buena práctica)
            actualizarBotones();
        }

        function actualizarBotones() {
            let favoritos = JSON.parse(localStorage.getItem('misFavoritos')) || [];
            const botones = document.querySelectorAll('button');
            
            botones.forEach(btn => {
                // Lógica visual simple: comprobar si el padre tiene un ID que está en favoritos
                // Nota: en un entorno real usaríamos selectores más robustos
                const idCancion = parseInt(btn.parentElement.getAttribute('data-id'));
                if (favoritos.some(f => f.id === idCancion)) {
                    btn.classList.add('favorito-activo');
                    btn.textContent = "✔ Guardado";
                } else {
                    btn.classList.remove('favorito-activo');
                    btn.textContent = "❤ Favorito";
                }
            });
        }

        // Ejecutar al cargar para mostrar el estado correcto
        document.addEventListener('DOMContentLoaded', actualizarBotones);
    </script>
</body>
</html>
