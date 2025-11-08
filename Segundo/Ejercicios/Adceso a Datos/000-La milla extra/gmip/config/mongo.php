<?php

declare(strict_types=1);

namespace GMIP\DocDB;

use GMIP\Config\Config;

class Mongo
{
    /**
     * @return object MongoDB\Client
     */
    public static function getClient(): object
    {
        Config::requireKeys(['mongo.uri', 'mongo.db']);

        if (!class_exists('MongoDB\\Client')) {
            throw new \RuntimeException('MongoDB Client no disponible. Instala la librería composer mongodb/mongodb.');
        }

        $uri = Config::get('mongo.uri');
        return new \MongoDB\Client((string)$uri);
    }
}