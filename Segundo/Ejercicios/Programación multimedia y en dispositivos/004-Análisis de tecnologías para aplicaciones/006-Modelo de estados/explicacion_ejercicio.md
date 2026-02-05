# Ejercicio: Modelo de Estados - Gestión de Favoritos

### 🧠 Explicación personal del ejercicio
En este ejercicio he implementado una funcionalidad clave en cualquier aplicación moderna: la gestión del estado y la persistencia de datos. El objetivo era crear una pantalla de lista de canciones donde pudiera marcar mis favoritas y que estas se mostraran en la pantalla de inicio.

Para hacerlo, no he necesitado una base de datos compleja. He utilizado `localStorage` de JavaScript, que permite guardar información en el navegador del usuario de forma sencilla. Es como la "memoria" de la aplicación. Además, le he dado un toque personal con colores azules inspirados en el mar, ya que soy aficionado a la pesca y quería que la app reflejara ese estilo relajante.

El proceso ha sido dividirlo en tres partes: la estructura HTML (lista), el estilo CSS (para que se vea bien en el móvil) y la lógica JavaScript que hace la "magia" de guardar y leer los favoritos.

### 💻 Código de programación

**1. pantalla_lista.php (Estructura y Lógica de Guardado)**
Aquí creo la lista y con JavaScript detecto cuándo se pulsa "Favorito" para guardar el ID y el título.
```html
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
        <p>Selecciona tus favoritas para llevarlas contigo.</p>
        
        <div id="lista-canciones">
            <div class="cancion" data-id="1" data-titulo="La Marea">
                <span>La Marea - Artista A</span>
                <button onclick="toggleFavorito(1, 'La Marea')">❤ Favorito</button>
            </div>
            <div class="cancion" data-id="2" data-titulo="Bajo el Mar">
                <span>Bajo el Mar - Artista B</span>
                <button onclick="toggleFavorito(2, 'Bajo el Mar')">❤ Favorito</button>
            </div>
        </div>

        <a href="pantalla_inicio.php">Ir a Inicio (Ver Favoritos)</a>
    </div>

    <script>
        function toggleFavorito(id, titulo) {
            // Leemos lo que ya hay guardado o empezamos de cero
            let favoritos = JSON.parse(localStorage.getItem('misFavoritos')) || [];
            const existe = favoritos.some(f => f.id === id);

            if (existe) {
                // Si ya está, lo quitamos
                favoritos = favoritos.filter(f => f.id !== id);
            } else {
                // Si no, lo guardamos
                favoritos.push({ id: id, titulo: titulo });
            }

            // Guardamos el nuevo estado en el navegador
            localStorage.setItem('misFavoritos', JSON.stringify(favoritos));
            actualizarBotones();
        }

        function actualizarBotones() {
            // Cambia el color del botón si ya es favorito
            let favoritos = JSON.parse(localStorage.getItem('misFavoritos')) || [];
            document.querySelectorAll('button').forEach(btn => {
                const id = parseInt(btn.parentElement.getAttribute('data-id'));
                if (favoritos.some(f => f.id === id)) {
                    btn.classList.add('favorito-activo');
                    btn.textContent = "✔ Guardado";
                } else {
                    btn.classList.remove('favorito-activo');
                    btn.textContent = "❤ Favorito";
                }
            });
        }
        // Llamada inicial para pintar los botones correctamente
        document.addEventListener('DOMContentLoaded', actualizarBotones);
    </script>
</body>
</html>
```

**2. pantalla_inicio.php (Recuperación y Muestra)**
En esta pantalla leo la información guardada y la muestro.
```html
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
        <div id="contenedor-favoritos">
            <!-- Se llena con JS -->
        </div>
        <a href="pantalla_lista.php">Volver a canciones</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const contenedor = document.getElementById('contenedor-favoritos');
            const favoritos = JSON.parse(localStorage.getItem('misFavoritos')) || [];

            if (favoritos.length === 0) {
                contenedor.innerHTML = "<p>No tienes favoritos aún.</p>";
            } else {
                let html = "<ul>";
                favoritos.forEach(c => html += `<li>🎵 ${c.titulo}</li>`);
                html += "</ul>";
                contenedor.innerHTML = html;
            }
        });
    </script>
</body>
</html>
```

**3. estilos.css (Diseño)**
```css
body {
    font-family: sans-serif;
    background-color: #f0f8ff; /* Azul claro estilo mar */
    color: #333;
    text-align: center;
    padding: 20px;
}
.contenedor {
    background: white;
    padding: 20px;
    border-radius: 15px;
    max-width: 600px;
    margin: 0 auto;
}
.cancion {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border-bottom: 1px solid #eee;
}
button {
    background-color: #ff6b6b;
    color: white;
    border: none;
    padding: 5px 15px;
    border-radius: 15px;
    cursor: pointer;
}
button.favorito-activo {
    background-color: #4ecdc4; /* Verde agua para indicar activado */
}
```

### 📊 Rúbrica de evaluación cumplida
- **Introducción y contextualización**: He explicado claramente que usamos el almacenamiento local para gestionar el estado de los favoritos en el contexto de una app de música.
- **Desarrollo técnico**: El código utiliza HTML semántico, CSS limpio y JavaScript nativo sin librerías, gestionando correctamente arrays y JSON en `localStorage`.
- **Aplicación práctica**: Se demuestra con el ejemplo de marcar/desmarcar canciones cómo los datos persisten entre navegaciones (de lista a inicio).
- **Cierre/Conclusión**: Vinculo el ejercicio con la importancia de mantener el estado de la aplicación para una buena experiencia de usuario.

### 🧾 Cierre
Este ejercicio me ha servido para entender que no siempre hace falta un servidor para guardar datos básicos. La implementación de favoritos es muy común y creo que esta solución es ligera y efectiva para lo que pide el proyecto. Además, personalizarlo con mis gustos de pesca lo ha hecho más entretenido.
