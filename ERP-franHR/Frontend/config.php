<?php
// Leer variables de entorno desde .env
$env = [];
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }
    }
}

// Generar JavaScript con las variables
header('Content-Type: application/javascript');
echo "// Configuración desde .env\n";
foreach ($env as $key => $value) {
    echo "window.$key = '$value';\n";
}
?>