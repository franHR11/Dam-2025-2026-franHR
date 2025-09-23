<?php
/**
 * AuthConfig.php
 * Configuración centralizada de autenticación
 * Carga variables de entorno y define constantes
 */

// Cargar variables de entorno del Frontend
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Saltar comentarios
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Definir constantes de autenticación
define('LOGIN_URL', getenv('LOGIN_URL') ?: 'login.php');
define('DASHBOARD_URL', getenv('DASHBOARD_URL') ?: 'escritorio.php');
define('LOGOUT_URL', getenv('LOGOUT_URL') ?: 'logout.php');
define('SESSION_TIMEOUT', (int)getenv('SESSION_TIMEOUT') ?: 1800);

?>