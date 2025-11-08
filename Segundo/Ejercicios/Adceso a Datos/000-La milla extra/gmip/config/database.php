<?php

declare(strict_types=1);

namespace GMIP\DB;

use GMIP\Config\Config;
use PDO;
use PDOException;

class Database
{
    public static function getPdo(): PDO
    {
        Config::requireKeys(['db.host', 'db.name', 'db.user']);

        $host = Config::get('db.host');
        $db   = Config::get('db.name');
        $user = Config::get('db.user');
        $pass = Config::get('db.pass');
        $charset = Config::get('db.charset', 'utf8mb4');

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $host, $db, $charset);
        try {
            $pdo = new PDO($dsn, (string)$user, (string)$pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $pdo->exec("SET NAMES 'utf8mb4'");
            return $pdo;
        } catch (PDOException $e) {
            throw new \RuntimeException('Error de conexión a la base de datos: ' . $e->getMessage());
        }
    }
}