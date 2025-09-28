fetch(window.API_BASE_URL + 'componentes/listado-de-modulos/listadoModulos.php?ruta=categorias', {
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
            let listadoCard = document.getElementById('menu');
            listadoCard.innerHTML = ''; // Limpiar contenido existente
            data.data.forEach(function (listadoMenu) {
                let nav = document.createElement('nav');
                nav.innerHTML = `
                    <ul>
            <li>
                <a href="${listadoMenu.enlace}">
                        <i class="${listadoMenu.icono}"></i>
                        <span>${listadoMenu.nombre}</span>
                    </a>
            </li>
            </ul>
                `;
                listadoCard.appendChild(nav);
            });
        } else {
            console.error('Error al obtener las categorias:', data.message);
        }
    });
