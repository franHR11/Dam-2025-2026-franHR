<?php
// Configuración de headers CORS se maneja en .htaccess

// Configuración de sesión para compatibilidad cross-domain
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 si usas HTTPS
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', '.test');
ini_set('session.cookie_samesite', 'None');

// Iniciar la sesión para poder destruirla
session_start();

// Limpiar todas las variables de sesión
$_SESSION = [];

// Destruir la cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión
session_destroy();

// Enviar una respuesta JSON de éxito
http_response_code(200);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['success' => true, 'message' => 'Sesión cerrada exitosamente']);
