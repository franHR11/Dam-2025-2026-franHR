<?php
// API para obtener módulos activos para el menú - Sistema independiente
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

// Manejar peticiones OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación flexible
$usuarioAutenticado = false;
$esAdmin = false;

if (isset($_SESSION['user_id']) || isset($_SESSION['username'])) {
    $usuarioAutenticado = true;
}

// Verificar permisos de administrador
if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin') {
    $esAdmin = true;
} elseif (isset($_SESSION['username']) && $_SESSION['username'] === 'admin') {
    $esAdmin = true;
}

// Si no está aut
