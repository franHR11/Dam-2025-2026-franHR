<?php
// index.php - Router simple

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

// Rutas de páginas principales
$routes = [
    '' => 'Login/login.php',   // raíz → login
    'login' => 'Login/login.php',
    'ventas' => 'Ventas/ventas.php',
    'compras' => 'Compras/compras.php',
    // etc...
];

if (array_key_exists($uri, $routes)) {
    require $routes[$uri];
} else {
    http_response_code(404);
    require 'pages/404.php';
}
