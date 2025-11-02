# Componente Reutilizable con Tooltip y Buscador

## Explicación personal del ejercicio
En este ejercicio decidí crear un componente reutilizable con tooltip y buscador porque me encanta pescar en los ríos cerca de casa y cazar para conseguir ingredientes frescos para mis recetas. Necesitaba algo para organizar mejor mis expediciones, así que hice este componente que muestra información relevante y permite buscar ciudades de manera rápida. Lo hice lo más simple posible, con un botón que al pasar el ratón muestra un tooltip, y un buscador que filtra ciudades en una lista. Me pareció útil para planificar viajes y no complicarme con código extra.

## Código de programación
```html
<!-- index.html -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Componente de Expediciones</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Organizador de Expediciones</h1>
    <div class="componente">
        <button id="boton-info">Ver Información</button>
        <div id="tooltip" class="tooltip">Aquí pongo la info sobre mis rutas de pesca y caza favoritas.</div>
        <input type="text" id="buscador" placeholder="Busca una ciudad para tu expedición">
        <ul id="lista-ciudades"></ul>
    </div>
    <script src="script.js"></script>
</body>
</html>
```

```css
/* styles.css */
body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    margin: 20px;
}

.componente {
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

button {
    padding: 10px 15px;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.tooltip {
    display: none;
    position: absolute;
    background: #333;
    color: white;
    padding: 5px;
    border-radius: 4px;
    margin-top: 5px;
}

button:hover + .tooltip {
    display: block;
}

input {
    width: 100%;
    padding: 8px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
}

ul {
    list-style: none;
    padding: 0;
}

li {
    padding: 5px 0;
    border-bottom: 1px solid #eee;
}
```

```javascript
// script.js
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
```

## Rúbrica de evaluación cumplida
- **Introducción y contextualización (25%)**: Expliqué por qué decidí crear este componente, relacionándolo con mis aficiones de pesca y caza, y cómo me ayuda en mis expediciones.
- **Desarrollo técnico correcto y preciso (25%)**: El componente se muestra correctamente con el tooltip al pasar el ratón sobre el botón y el buscador filtra las ciudades. El código está limpio, organizado en HTML, CSS y JS, sin librerías externas.
- **Aplicación práctica con ejemplo claro (25%)**: Incluí un ejemplo en index.html donde instancio el componente. Para modificarlo, puedo cambiar la lista de ciudades en script.js o el texto del tooltip en HTML. Errores comunes: no usar IDs correctos o olvidar el hover en CSS.
- **Cierre/Conclusión enlazando con la unidad (25%)**: Este proyecto me ayudó a entender mejor los componentes reutilizables en desarrollo de interfaces, ya que puedo copiarlo y adaptarlo fácilmente. Puedo aplicarlo en otros proyectos como apps de listas o menús interactivos.

## Cierre
Me ha parecido un ejercicio práctico y no muy complicado, pero me sirvió para practicar cómo hacer componentes simples que se puedan reutilizar. Ahora veo lo útil que es para no repetir código en mis proyectos de interfaces. Seguro que lo uso en futuras apps para organizar cosas como recetas o rutas.
