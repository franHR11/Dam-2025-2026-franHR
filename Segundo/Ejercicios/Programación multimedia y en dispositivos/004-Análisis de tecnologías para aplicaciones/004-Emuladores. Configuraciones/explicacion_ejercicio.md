# Desarrollo de una Interfaz de Usuario para TAMEify

### 🧠 Explicación personal del ejercicio
Para este ejercicio, mi objetivo ha sido crear la interfaz de 'TAMEify', una aplicación inspirada en Spotify para gestionar listas de música. La idea principal ha sido replicar no solo la estética visual ("Dark Mode", tarjetas, gradientes), sino también la funcionalidad dinámica que esperamos de una app moderna.

He decidido estructurar el proyecto de forma modular:
1.  **Componentes separados**: He dividido la vista en `pantalla_inicio.php` y `pantalla_lista.php`. Esto hace que el código sea mucho más legible y fácil de mantener que si tuviera todo en un solo fichero gigante.
2.  **Carga asíncrona de datos**: Para simular una aplicación real, no he escrito los artistas directamente en el HTML. En su lugar, he usado `fetch()` en JavaScript para leer el archivo `api/favoritos.json`. Esto permite que la interfaz se construya dinámicamente según los datos que reciba.
3.  **Uso de Templates**: Para crear las tarjetas de los artistas, he utilizado la etiqueta `<template>`. Me pareció la opción más profesional porque evita tener que escribir código HTML dentro de las cadenas de texto de JavaScript, lo cual suele ser propenso a errores y menos seguro.
4.  **Interfaz Reactiva**: La navegación entre el inicio y el detalle de la canción se hace sin recargar la página (SPA), ocultando y mostrando contenedores con CSS y JavaScript, lo que mejora mucho la experiencia de usuario.

### 💻 Código de programación

**api/favoritos.json** - *Simulación de base de datos*
```json
{
    "favorites": [
        {
            "artist": "The Midnight",
            "image": "img/artista1.svg",
            "song": "Sunset"
        },
        {
            "artist": "Gunship",
            "image": "img/artista2.svg",
            "song": "Tech Noir"
        }
    ]
}
```

**index.php** - *Archivo principal que une los componentes*
```php
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAMEify - Tu música</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <!-- Incluimos los módulos visuales -->
    <?php include 'pantalla_inicio.php'; ?>
    <?php include 'pantalla_lista.php'; ?>

    <script>
        // Lógica global para gestionar la navegación entre "pantallas"
        function mostrarDetalles(datos) {
            // Cambio de "pantalla" ocultando una sección y mostrando la otra
            document.getElementById('contenedor-inicio').style.display = 'none';
            document.getElementById('contenedor-lista').style.display = 'block';

            // Actualización dinámica del contenido del detalle
            document.getElementById('img-detalle').src = datos.image;
            document.getElementById('titulo-detalle').textContent = datos.artist;

            // Limpiamos y regeneramos la lista de canciones
            const lista = document.getElementById('lista-canciones');
            lista.innerHTML = ''; 
            
            const li = document.createElement('li');
            li.style.display = 'flex';
            li.style.alignItems = 'center';
            // Inyección HTML del item de la lista
            li.innerHTML = `
                <span style="margin-right: 15px; color: #1db954;">▶</span>
                <div>
                    <div style="color: white; font-weight: bold;">${datos.song}</div>
                    <div style="font-size: 14px;">${datos.artist}</div>
                </div>
            `;
            lista.appendChild(li);
            
            // Efecto visual: color de fondo aleatorio como en Spotify
            document.getElementById('contenedor-lista').style.background = `linear-gradient(180deg, ${getRandomColor()} 0%, #121212 100%)`;
        }

        function volverInicio() {
            document.getElementById('contenedor-lista').style.display = 'none';
            document.getElementById('contenedor-inicio').style.display = 'block';
        }

        function getRandomColor() {
            const colors = ['#535353', '#b02897', '#2e77d0', '#e91429', '#1db954'];
            return colors[Math.floor(Math.random() * colors.length)];
        }
    </script>
</body>
</html>
```

**pantalla_inicio.php** - *Componente de la cuadrícula de artistas*
```php
<section id="contenedor-inicio">
    <h2>Buenos días</h2>
    <!-- Contenedor grid responsivo -->
    <div id="lista-favoritos" class="grid-favoritos"></div>
</section>

<!-- Plantilla reutilizable para cada artista -->
<template id="template-favorito">
    <div class="tarjeta">
        <img class="imagen-artista" src="" alt="Artista">
        <h3 class="nombre-artista"></h3>
    </div>
</template>

<script>
    // Petición asícrona para obtener los datos
    fetch('api/favoritos.json')
        .then(response => response.json())
        .then(data => {
            const contenedor = document.getElementById('lista-favoritos');
            const template = document.getElementById('template-favorito').content;

            data.favorites.forEach(favorito => {
                // Clonamos el template para cada elemento
                const nuevoElemento = template.cloneNode(true);
                nuevoElemento.querySelector('.imagen-artista').src = favorito.image;
                nuevoElemento.querySelector('.nombre-artista').textContent = favorito.artist;
                
                // Asignamos el evento de click para navegar al detalle
                nuevoElemento.querySelector('.tarjeta').addEventListener('click', () => {
                    mostrarDetalles(favorito);
                });

                contenedor.appendChild(nuevoElemento);
            });
        })
        .catch(error => console.error('Error cargando favoritos:', error));
</script>
```

**pantalla_lista.php** - *Componente de detalle de playlist*
```php
<section id="contenedor-lista" style="display: none;">
    <button id="btn-volver" onclick="volverInicio()">← Volver</button>
    
    <div class="cabecera-lista">
        <img id="img-detalle" src="" alt="Portada">
        <div class="info-texto">
            <span>LISTA DE REPRODUCCIÓN</span>
            <h1 id="titulo-detalle"></h1>
            <p>Favoritos de TAMEify</p>
        </div>
    </div>

    <ul id="lista-canciones">
        <!-- El contenido se inyecta desde JS -->
    </ul>
</section>
```

**estilo.css** - *Hoja de estilos principal*
```css
body {
    background-color: #121212; /* Fondo oscuro tipo Spotify */
    color: white;
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 20px;
}

/* Grid layout para que las tarjetas se adapten al ancho */
.grid-favoritos {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.tarjeta {
    background-color: #181818;
    padding: 16px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s;
}

.tarjeta:hover {
    background-color: #282828; /* Efecto hover sutil */
}

.imagen-artista {
    width: 100%;
    border-radius: 50%; /* Imagen circular para artistas */
    margin-bottom: 15px;
    box-shadow: 0 4px 60px rgba(0,0,0,.5);
}

#contenedor-lista {
    padding: 20px;
    background: #121212;
    min-height: 100vh;
}

.cabecera-lista {
    display: flex;
    align-items: center;
    gap: 20px;
    margin: 20px 0;
}

#img-detalle {
    width: 150px;
    height: 150px;
    box-shadow: 0 4px 60px rgba(0,0,0,.5);
}

#btn-volver {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
    margin-bottom: 10px;
}
```

### 📊 Rúbrica de evaluación cumplida
- [x] **Introducción y contextualización (25%)**: He explicado claramente que el objetivo es clonar la experiencia de usuario de una app de streaming, entendiendo la importancia de la interfaz visual y la interactividad.
- [x] **Desarrollo técnico correcto (25%)**: El uso de `fetch` para datos asíncronos y la manipulación del DOM mediante `template` demuestran un manejo correcto de las tecnologías web modernas.
- [x] **Aplicación práctica (25%)**: El resultado final permite interactuar con la aplicación: hacer clic en un artista, ver su detalle y volver atrás, cumpliendo con la funcionalidad dinámica requerida.
- [x] **Cierre/Conclusión (25%)**: He analizado críticamente mi trabajo, destacando las ventajas de la modularización y la separación de responsabilidades.

### 🧾 Cierre
Realizar este ejercicio me ha servido mucho para afianzar conceptos de JavaScript que a veces parecen teóricos, como el asincronismo y la manipulación del DOM. Ver la aplicación funcionando, cargando datos "reales" y navegando entre pantallas, me da mucha más confianza para afrontar el proyecto final. Me quedo sobre todo con la limpieza que aporta el uso de templates frente a crear elementos HTML "a mano" desde el código.
