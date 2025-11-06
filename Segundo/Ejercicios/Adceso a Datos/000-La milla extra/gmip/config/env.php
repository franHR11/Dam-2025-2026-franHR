<?php

declare(strict_types=1);

namespace GMIP\Config;

/**
 * Configuración centralizada del proyecto GMIP.
 * Carga variables desde .env y valida requisitos.
 */
class Config
{
    private static bool $initialized = false;
    private static array $store = [];

    public static function init(string $baseDir): void
    {
        if (self::$initialized) return;

        $envPath = $baseDir . DIRECTORY_SEPARATOR . '.env';
        $env = [];
        if (file_exists($envPath)) {
            // Parser robusto para .env: ignora líneas vacías y comentarios (#, ;),
            // soporta valores con comillas y formato KEY=VALUE.
            $lines = @file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || str_starts_with($line, ';')) {
                    continue;
                }
                if (str_starts_with($line, 'export ')) {
                    $line = substr($line, 7);
                }
                $pos = strpos($line, '=');
                if ($pos === false) {
                    continue;
                }
                $name = trim(substr($line, 0, $pos));
                $value = trim(substr($line, $pos + 1));
                if ($value !== '' && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
                    $value = substr($value, 1, -1);
                }
                if ($name !== '') {
                    $env[$name] = $value;
                }
            }
        }

        // Helper para obtener variables con prioridad: .env -> getenv -> $_ENV
        $get = function (string $key, $default = null) use ($env) {
            if (array_key_exists($key, $env)) return $env[$key];
            $val = getenv($key);
            if ($val !== false && $val !== null) return $val;
            return $_ENV[$key] ?? $default;
        };

        // Variables de entorno (prefijo GMIP_ para evitar colisiones)
        self::$store = [
            'db.host'    => (string)$get('GMIP_DB_HOST', ''),
            'db.name'    => (string)$get('GMIP_DB_NAME', ''),
            'db.user'    => (string)$get('GMIP_DB_USER', ''),
            'db.pass'    => (string)$get('GMIP_DB_PASS', ''),
            'db.charset' => (string)$get('GMIP_DB_CHARSET', 'utf8mb4'),

            'mongo.uri'  => (string)$get('GMIP_MONGO_URI', ''),
            'mongo.db'   => (string)$get('GMIP_MONGO_DB', ''),

            'app.baseUrl'        => (string)$get('GMIP_APP_BASE_URL', ''),
            'app.corsAllowOrigin' => (string)$get('GMIP_CORS_ALLOW_ORIGIN', ''),
        ];

        self::$initialized = true;
    }

    public static function get(string $key, $default = null)
    {
        return self::$store[$key] ?? $default;
    }

    public static function validate(): array
    {
        $issues = [];

        // DB requerida
        foreach (['db.host', 'db.name', 'db.user'] as $k) {
            if (!self::$store[$k]) {
                $issues[] = ['key' => $k, 'ok' => false, 'message' => 'Falta variable requerida'];
            } else {
                $issues[] = ['key' => $k, 'ok' => true];
            }
        }

        // Opcional: mongo
        if (self::$store['mongo.uri']) {
            $ok = (bool)preg_match('/^mongodb(\+srv)?:\/\//i', (string)self::$store['mongo.uri']);
            $issues[] = ['key' => 'mongo.uri', 'ok' => $ok, 'message' => $ok ? null : 'Formato de URI inválido'];
        } else {
            $issues[] = ['key' => 'mongo.uri', 'ok' => false, 'message' => 'No configurado (opcional, recomendado)'];
        }

        // CORS
        $issues[] = ['key' => 'app.corsAllowOrigin', 'ok' => (bool)self::$store['app.corsAllowOrigin']];

        return $issues;
    }

    public static function requireKeys(array $keys): void
    {
        foreach ($keys as $k) {
            if (!self::$store[$k]) {
                throw new \RuntimeException('Configuración incompleta: falta ' . $k);
            }
        }
    }
}