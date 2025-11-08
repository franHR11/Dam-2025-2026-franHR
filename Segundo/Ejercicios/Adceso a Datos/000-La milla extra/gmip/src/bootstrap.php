<?php

declare(strict_types=1);

// Autoloader simple para el namespace GMIP
spl_autoload_register(function ($class) {
    $prefix = 'GMIP\\';
    $baseDir = __DIR__ . DIRECTORY_SEPARATOR;
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Cargar configuración centralizada
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mongo.php';

// Inicializar configuración
GMIP\Config\Config::init(dirname(__DIR__));

// Ajustes básicos de ejecución
date_default_timezone_set('Europe/Madrid');
error_reporting(E_ALL);
ini_set('display_errors', '0');