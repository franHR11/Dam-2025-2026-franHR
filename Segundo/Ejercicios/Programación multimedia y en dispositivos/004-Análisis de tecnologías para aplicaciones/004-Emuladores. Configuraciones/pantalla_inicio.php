<section id="contenedor-inicio">
    <h2>Buenos días</h2>
    <div id="lista-favoritos" class="grid-favoritos">
        <!-- Aquí se inyectarán los elementos -->
    </div>
</section>

<!-- Plantilla para los elementos de la lista -->
<template id="template-favorito">
    <div class="tarjeta">
        <img class="imagen-artista" src="" alt="Artista">
        <h3 class="nombre-artista"></h3>
    </div>
</template>

<script>
    // Cargar datos desde el JSON
    fetch('api/favoritos.json')
        .then(response => response.json())
        .then(data => {
            const contenedor = document.getElementById('lista-favoritos');
            const template = document.getElementById('template-favorito').content;

            data.favorites.forEach(favorito => {
                const nuevoElemento = template.cloneNode(true);
                nuevoElemento.querySelector('.imagen-artista').src = favorito.image;
                nuevoElemento.querySelector('.nombre-artista').textContent = favorito.artist;
                
                // Añadir evento click al div de la tarjeta
                nuevoElemento.querySelector('.tarjeta').addEventListener('click', () => {
                    // Llamamos a la función global definida en index.php
                    mostrarDetalles(favorito);
                });

                contenedor.appendChild(nuevoElemento);
            });
        })
        .catch(error => console.error('Error cargando favoritos:', error));
</script>
