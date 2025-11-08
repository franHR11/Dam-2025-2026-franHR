<?php

declare(strict_types=1);

namespace GMIP\Service;

use GMIP\DocDB\Mongo;
use GMIP\Config\Config;

class MongoService
{
    public static function insert(string $collection, array $doc): array
    {
        $client = Mongo::getClient();
        $db = Config::get('mongo.db');
        $res = $client->selectCollection((string)$db, (string)$collection)->insertOne($doc);
        return ['insertedId' => (string)$res->getInsertedId()];
    }

    public static function find(string $collection, array $filter = [], array $options = []): array
    {
        $client = Mongo::getClient();
        $db = Config::get('mongo.db');
        $cursor = $client->selectCollection((string)$db, (string)$collection)->find($filter, $options);
        $rows = [];
        foreach ($cursor as $doc) {
            $rows[] = json_decode(json_encode($doc), true);
        }
        return $rows;
    }

    public static function update(string $collection, array $filter, array $update): int
    {
        $client = Mongo::getClient();
        $db = Config::get('mongo.db');
        $res = $client->selectCollection((string)$db, (string)$collection)->updateMany($filter, $update);
        return $res->getModifiedCount();
    }

    public static function delete(string $collection, array $filter): int
    {
        $client = Mongo::getClient();
        $db = Config::get('mongo.db');
        $res = $client->selectCollection((string)$db, (string)$collection)->deleteMany($filter);
        return $res->getDeletedCount();
    }
}