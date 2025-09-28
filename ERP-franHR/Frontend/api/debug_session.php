<?php
// Configuración de sesión idéntica al login
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', 'test');
ini_set('session.cookie_samesite', 'Lax');

session_start();

echo "=== DEBUG DE SESIÓN ===\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Name: " . session_name() . "\n";
echo "\n=== VARIABLES DE SESIÓN ===\n";
foreach ($_SESSION as $key => $value) {
    echo "$key: $value\n";
}

echo "\n=== CONFIGURACIÓN DE COOKIES ===\n";
echo "Domain: " . ini_get('session.cookie_domain') . "\n";
echo "Path: " . ini_get('session.cookie_path') . "\n";
echo "SameSite: " . ini_get('session.cookie_samesite') . "\n";
?>
