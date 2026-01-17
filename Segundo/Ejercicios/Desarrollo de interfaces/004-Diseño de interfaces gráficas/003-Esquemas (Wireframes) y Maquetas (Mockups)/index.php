<?php
// Controlador Frontal Simple

include 'cabecera.php';

// Lista blanca de páginas permitidas para seguridad básica
$paginas_permitidas = [
    'home', 
    'actores', 
    'poema', 
    'pesca', 
    'caza', 
    'bicicleta', 
    'motocross'
];

$pagina = isset($_GET['p']) ? $_GET['p'] : 'home';

if (in_array($pagina, $paginas_permitidas)) {
    // Verificar si el archivo existe antes de incluirlo
    $archivo = $pagina . '.php';
    if (file_exists($archivo)) {
        include $archivo;
    } else {
        echo "<h2>Error 404</h2><p>La página solicitada está en construcción.</p>";
    }
} else {
    echo "<h2>Error</h2><p>Página no encontrada.</p>";
}

include 'piedepagina.php';
?>
