# Explicación del ejercicio: Reproductor de Música con carga dinámica JSON

## 1. Introducción y contextualización
En la asignatura de Programación Multimedia hemos estado viendo cómo las aplicaciones modernas no tienen la información "pegada" en el código, sino que la cargan desde fuera. 

Para este ejercicio, el objetivo ha sido crear una pequeña "app" web de música que carga su contenido dinámicamente. Esto es fundamental porque en el desarrollo de aplicaciones para dispositivos móviles real, los datos (canciones, usuarios, fotos) siempre vienen de una API o base de datos externa. Usar un archivo JSON local es la mejor forma de simular este comportamiento y entender cómo funciona la comunicación asíncrona.

## 2. Desarrollo detallado y preciso
A la hora de plantear la solución, dividí el trabajo en tres capas claramente diferenciadas para mantener el código ordenado, tal y como nos explicaste en clase:

### A. La Estructura de Datos (El Backend simulado)
En lugar de escribir los artistas directamente en el HTML, he creado un archivo `api/favoritos.json`.
*   **¿Por qué?** Esto permite que si mañana quiero añadir 50 artistas más, solo toco el archivo de texto JSON y no tengo que reprogramar la web.
*   He usado el formato estándar JSON (`key: value`) definiendo un array `favorites` que contiene objetos con las propiedades: `artist` (nombre), `image` (ruta relativa) y `song` (título).

### B. El Diseño de la Interfaz (HTML + CSS)
Para la interfaz, quería que se sintiera como una aplicación móvil nativa ("Single Page Application").
*   **Navegación fluida:** En lugar de crear varios archivos HTML (`lista.html`, `reproductor.html`), he creado un único `index.html` con secciones (`<section>`).
*   **Clase 'pantalla-oculta':** He usado un truco de CSS muy útil. Creo una clase `.pantalla-oculta { display: none; }`. Con Javascript, simplemente quito o pongo esta clase para mostrar u ocultar pantallas al instante, sin recargar la página. Es lo que hace que la app se sienta rápida.

### C. La Lógica (JavaScript y Tuberías)
Esta ha sido la parte más interesante. He usado la función `fetch()`, que es la forma moderna de pedir datos.
1.  **Petición:** `fetch('api/favoritos.json')` lanza la "pregunta" al servidor.
2.  **Promesa:** Como la respuesta no es inmediata, uso `.then()` para esperar a que lleguen los datos.
3.  **Renderizado DOM:** Una vez tengo los datos, no los escribo a mano. He creado un bucle `forEach` que recorre cada artista y "fabrica" el HTML (la etiqueta `<article>`, la `<img>`, etc.) al vuelo usando `document.createElement`.

## 3. Código y Aplicación práctica
A continuación muestro el código completo de mi solución `index.html`, donde se puede ver cómo integro estas tres partes. He añadido comentarios explicativos en las líneas clave.

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Artistas - Práctica JSON</title>
    <style>
        /* ESTILOS GENERALES (Dark Mode) */
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: #1a1a1a; 
            color: #f0f0f0; 
            margin: 0; 
            padding: 20px; 
        }

        /* NAVEGACIÓN */
        header { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 10px; }
        button { 
            padding: 10px 20px; 
            background: #ff00cc; /* Color neon para destacar */
            border: none; 
            color: white; 
            cursor: pointer; 
            border-radius: 5px; 
            font-weight: bold;
        }
        button:hover { background: #d900ad; }

        /* GRID DE ARTISTAS */
        #favoritos { 
            display: grid; 
            gap: 20px; 
            /* Grid responsivo automático */
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); 
        }

        /* TARJETA DE ARTISTA */
        article { 
            background: #2a2a2a; 
            padding: 15px; 
            border-radius: 12px; 
            text-align: center; 
            cursor: pointer; 
            transition: transform 0.2s, background 0.2s; /* Animación suave */
        }
        article:hover { 
            transform: scale(1.05); 
            background: #333; 
        }
        img { 
            width: 100%; 
            height: 150px; 
            object-fit: cover; /* Evita que la imagen se deforme */
            border-radius: 8px; 
            margin-bottom: 10px; 
        }
        h3 { margin: 10px 0; color: #00ccff; }

        /* UTILIDAD PARA OCULTAR PANTALLAS */
        .pantalla-oculta { display: none; }
    </style>
</head>
<body>

    <header>
        <!-- Botones para moverse entre "vistas" -->
        <button onclick="cambiarPantalla('favoritos')">🎵 Artistas</button>
        <button onclick="cambiarPantalla('reproductor')">🎧 Reproductor</button>
    </header>

    <!-- PANTALLA 1: LISTA (Se llena con JS) -->
    <section id="favoritos">
        <!-- JS inyectará aquí los <article> -->
    </section>

    <!-- PANTALLA 2: REPRODUCTOR (Oculta por defecto) -->
    <section id="reproductor" class="pantalla-oculta">
        <h2>Ahora sonando</h2>
        <div id="info-cancion" style="font-size: 1.2em; margin-bottom: 20px; color: #aaa;">
            Selecciona una canción de la lista...
        </div>
        <audio id="audioPlayer" controls style="width: 100%;"></audio>
        
        <br><br>
        <button onclick="cambiarPantalla('favoritos')" style="background: #444;">⬅ Volver a la lista</button>
    </section>

    <script>
        // --- LÓGICA DE NAVEGACIÓN ---
        function cambiarPantalla(idPantallaQueQuieroVer) {
            // 1. Oculto todas las secciones
            document.getElementById('favoritos').classList.add('pantalla-oculta');
            document.getElementById('reproductor').classList.add('pantalla-oculta');
            
            // 2. Muestro solo la que me han pedido
            document.getElementById(idPantallaQueQuieroVer).classList.remove('pantalla-oculta');
        }

        // --- LÓGICA DE CARGA DE DATOS ---
        // Uso fetch para leer el archivo JSON local
        fetch('api/favoritos.json')
            .then(resultado => resultado.json()) // Convierto la respuesta a objeto JS usable
            .then(datos => {
                const contenedor = document.getElementById('favoritos');

                // Recorro el array de favoritos
                datos.favorites.forEach(item => {
                    // Creo el elemento <article>
                    const nuevoArticulo = document.createElement('article');
                    
                    // Relleno su contenido HTML usando "Template Strings" (las comillas invertidas)
                    // Esto permite mezclar HTML con variables ${} fácilmente
                    nuevoArticulo.innerHTML = `
                        <img src="${item.image}" alt="Foto de ${item.artist}">
                        <h3>${item.artist}</h3>
                        <p>${item.song}</p>
                    `;

                    // Añado el evento CLICK dinámicamente
                    nuevoArticulo.onclick = function() {
                        // Navego al reproductor
                        cambiarPantalla('reproductor');
                        // Actualizo la información de qué está sonando
                        document.getElementById('info-cancion').innerHTML = 
                            `Reproduciendo: <strong>${item.song}</strong> de <em>${item.artist}</em>`;
                    };

                    // Finalmente, "pego" el artículo en la página
                    contenedor.appendChild(nuevoArticulo);
                });
            })
            .catch(error => {
                console.error("Ups, hubo un error cargando el JSON:", error);
                alert("No se pudieron cargar los artistas. Revisa que el servidor esté funcionando.");
            });
    </script>
</body>
</html>
```

## 4. Conclusión
Este ejercicio me ha servido para consolidar tres conceptos clave que tenía algo dispersos:
1.  **Separación de responsabilidades:** Ver claramente cómo el JSON guarda datos, el HTML estructura y el JS une todo.
2.  **Manipulación del DOM:** He aprendido que crear elementos con `document.createElement` es más potente que simplemente escribir texto.
3.  **Experiencia de usuario (UX):** Me he dado cuenta de que pequeños detalles como ocultar divs en lugar de recargar la página hacen que la web parezca una aplicación nativa real.

Personalmente, lo que más me costó entender al principio fue que `fetch` no devuelve los datos inmediatamente, sino que hay que "esperar" con el `.then()`, pero ahora veo que es lógico porque internet puede ser lento.
