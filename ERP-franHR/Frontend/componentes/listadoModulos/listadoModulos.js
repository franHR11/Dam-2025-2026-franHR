fetch((window.CONFIG?.API_BASE_URL || '/api/') + 'componentes/listado-de-modulos/listadoModulos.php?ruta=aplicaciones', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json'
    }
})
    .then(function (respuesta) {
        return respuesta.json();
    })
    .then(function (data) {
        if (data.success) {
            let listadoCard = document.getElementById('listadoCard');
            listadoCard.innerHTML = ''; // Limpiar contenido existente
            data.data.forEach(function (modulo) {
                let article = document.createElement('article');
                article.innerHTML = `
                    <div class="logo">
                        <i class="logo fas fa-home"></i>
                    </div>
                    <div class="texto">
                        <h3>${modulo.nombre}</h3>
                        <p>${modulo.descripcion || 'Sin descripción'}</p>
                        <button>Instalar</button>
                    </div>
                `;
                listadoCard.appendChild(article);
            });
        } else {
            console.error('Error al obtener los módulos:', data.message);
        }
    });
