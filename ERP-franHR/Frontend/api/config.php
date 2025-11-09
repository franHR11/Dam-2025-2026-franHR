<?php
// Cargar variables de entorno desde .env con parser seguro (ignora comentarios y evita avisos)
function safeLoadEnv($path)
{
    if (!file_exists($path)) {
        return false;
    }
    $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return false;
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || $line[0] === ';') {
            continue;
        }
        $eqPos = strpos($line, '=');
        if ($eqPos === false) {
            continue;
        }
        $key = trim(substr($line, 0, $eqPos));
        $value = trim(substr($line, $eqPos + 1));
        $len = strlen($value);
        if ($len >= 2 && ((($value[0] === '"') && ($value[$len - 1] === '"')) || (($value[0] === "'") && ($value[$len - 1] === "'")))) {
            $value = substr($value, 1, -1);
        }
        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
    }
    return true;
}

// Cargar .env en /api y fallback al root /Frontend
$envPaths = [
    __DIR__ . '/.env',
    dirname(__DIR__) . '/.env',
];
foreach ($envPaths as $envPath) {
    if (safeLoadEnv($envPath)) {
        break;
    }
}

// Configuración de la base de datos
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'erp-dam');
define('DB_USER', getenv('DB_USER') ?: 'erp-dam2');
define('DB_PASS', getenv('DB_PASS') ?: 'erp-dam2');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');

// Función para obtener la conexión a la base de datos
function getConnection()
{
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    // Intento principal con credenciales del .env
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        error_log("[DB] Error con credenciales configuradas: " . $e->getMessage());
        // Fallbacks comunes para entornos de desarrollo (Laragon/XAMPP)
        $alternativas = [
            ['user' => 'root', 'pass' => ''],
            ['user' => 'root', 'pass' => 'root'],
        ];
        foreach ($alternativas as $alt) {
            try {
                $pdo = new PDO($dsn, $alt['user'], $alt['pass']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                error_log("[DB] Conectado usando credenciales fallback: {$alt['user']}");
                return $pdo;
            } catch (PDOException $e2) {
                error_log("[DB] Fallo fallback ({$alt['user']}): " . $e2->getMessage());
            }
        }
        return null;
    }
}

// Configuración de headers para JSON
header('Content-Type: application/json; charset=utf-8');
?>
