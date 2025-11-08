// Aquí tengo una lista de ciudades que uso para mis expediciones de pesca y caza
const ciudades = ['Madrid', 'Barcelona', 'Sevilla', 'Valencia', 'Bilbao', 'Zaragoza'];

// Cojo el elemento de la lista para mostrar las ciudades
const lista = document.getElementById('lista-ciudades');

// Recorro cada ciudad y la agrego a la lista en la página
ciudades.forEach(ciudad => {
    const li = document.createElement('li');
    li.textContent = ciudad;
    lista.appendChild(li);
});

// Cuando escribo en el buscador, filtro las ciudades que coincidan
document.getElementById('buscador').addEventListener('input', (e) => {
    const filtro = e.target.value.toLowerCase();
    const items = lista.querySelectorAll('li');
    // Para cada item de la lista, veo si incluye el texto del filtro
    items.forEach(item => {
        item.style.display = item.textContent.toLowerCase().includes(filtro) ? '' : 'none';
    });
});
