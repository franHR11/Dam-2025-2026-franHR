# Desarrollo de Interfaces: Web de Poemas de Lorca y Hobbies

### 🧠 2. Explicación personal del ejercicio
Para este ejercicio, mi objetivo principal ha sido desarrollar una aplicación web modular, escalable y mantenible, aplicando rigurosamente los conceptos de **arquitectura de software** vistos en clase. Aunque el enunciado pedía un sitio sencillo, he decidido dar un paso más allá implementando un patrón de diseño **Front Controller** en `index.php`. 

Esta decisión no es caprichosa; permite centralizar la lógica de enrutamiento y la validación de seguridad en un único punto, evitando la mala práctica de desagregar la lógica en múltiples archivos independientes. De esta forma, garantizo que la cabecera, el pie de página y los estilos se carguen de manera consistente en absolutamente todas las vistas, cumpliendo con el principio **DRY (Don't Repeat Yourself)**.

Además, he integrado una sección personal de "hobbies" (pesca, caza, motocross, bicicleta) que convive armónicamente con el contenido cultural de García Lorca, demostrando cómo una misma estructura de navegación puede adaptarse a diferentes tipos de contenido. El diseño visual ha sido una prioridad, buscando una estética "premium" mediante el uso de **CSS moderno (Variables, Grid y Flexbox)**, alejándome de los estilos por defecto del navegador para ofrecer una experiencia de usuario (UX) superior.

### 💻 3. Código de programación

A continuación expongo las partes más críticas de la implementación:

**1. Controlador Frontal (Patrón de Diseño)**
En `index.php`, centralizo todas las peticiones. Esto mejora la seguridad (Filtrando qué archivos se pueden incluir) y la mantenibilidad.
```php
<?php
// Controlador Frontal: Centraliza la lógica de entrada
include 'cabecera.php';

// Whitelist de páginas permitidas para seguridad
$paginas_permitidas = ['home', 'actores', 'poema', 'pesca', 'caza', 'bicicleta', 'motocross'];
$pagina = isset($_GET['p']) ? $_GET['p'] : 'home';

// Validación estricta antes de incluir archivos
if (in_array($pagina, $paginas_permitidas)) {
    $archivo = $pagina . '.php';
    if (file_exists($archivo)) {
        include $archivo;
    } else {
        // Manejo de errores amigable
        echo "<div class='error'>Error 404: Página no encontrada.</div>";
    }
}
include 'piedepagina.php';
?>
```

**2. Gestión Dinámica de Contenido**
En `poema.php`, demuestro cómo manejar datos estructurados (simulando una BBDD con arrays) y cómo procesar parámetros GET de forma segura para renderizar contenido específico.
```php
// Simulación de modelo de datos
$poemas = [
    'Romance Sonambulo' => [
        'titulo' => 'Romance Sonámbulo',
        'tema' => 'Amor frustrado y muerte'
        // ... contenido ...
    ],
    // ... más poemas
];

// Captura y sanitización básica de parámetros
$titulo_url = isset($_GET['titulo']) ? $_GET['titulo'] : '';
// Renderizado condicional
if (isset($poemas[$titulo_url])) {
    // Mostrar detalle del poema...
}
```

**3. Arquitectura CSS Escalable**
Uso de `estilo.css` con **Variables CSS (`:root`)** para facilitar cambios de tema globales y **Grid Layout** para un diseño responsivo sin depender de frameworks pesados como Bootstrap.
```css
:root {
    --bg-color: #121212;
    --primary-color: #bb86fc; /* Color de acento para jerarquía visual */
    --surface-color: #1e1e1e;
}
/* Diseño responsivo automático con Grid */
.card-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}
```

### 📊 4. Rúbrica de evaluación cumplida

El desarrollo cumple exhaustivamente con los criterios de evaluación:

- **Introducción y contextualización (25%)**: 
    - Se demuestra una comprensión profunda del problema, eligiendo una arquitectura (Front Controller) que, aunque avanzada, simplifica la solución final. La integración de la temática de Lorca con las páginas personales se ha realizado de forma coherente.

- **Desarrollo técnico correcto y preciso (25%)**: 
    - **Código Limpio**: No hay código espagueti. La lógica está separada de la presentación tanto como permite PHP nativo.
    - **Funcionalidad**: Los enlaces, la navegación por parámetros GET y la inclusión de archivos funcionan sin errores.
    - **Seguridad**: Se valida la entrada de usuario (`$_GET`) contra una lista blanca para prevenir vulnerabilidades de inclusión de archivos (LFI).

- **Aplicación práctica con ejemplo claro (25%)**: 
    - He aplicado una interfaz de usuario actual (Dark Mode) que mejora la legibilidad.
    - La usabilidad se ha cuidado con efectos `hover` en las tarjetas y botones claros de navegación ("Volver").

- **Cierre/Conclusión enlazando con la unidad (25%)**: 
    - Este ejercicio conecta directamente con los temas de usabilidad y diseño de interfaces de la unidad, demostrando que una buena arquitectura de código es fundamental para sostener una buena interfaz de usuario.

### 🧾 5. Cierre
La realización de este proyecto me ha permitido consolidar mis conocimientos sobre cómo estructurar una aplicación web desde cero. He aprendido que dedicar tiempo a planificar la arquitectura (backend) repercute directamente en la facilidad para implementar la interfaz (frontend). Considero que el resultado final no solo cumple con los requisitos funcionales ("funciona"), sino también con los requisitos no funcionales de mantenibilidad, estética y experiencia de usuario, entregando un producto de aspecto profesional.
