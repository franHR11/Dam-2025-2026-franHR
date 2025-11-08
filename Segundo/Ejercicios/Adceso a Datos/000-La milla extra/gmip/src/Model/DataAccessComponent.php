<?php

declare(strict_types=1);

namespace GMIP\Model;

use PDO;

/**
 * Componente reutilizable de acceso a datos con eventos y persistencia.
 */
class DataAccessComponent
{
    private ?PDO $pdo = null;
    private array $events = [];
    private array $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function withPdo(PDO $pdo): self
    {
        $this->pdo = $pdo;
        return $this;
    }

    public function on(string $event, callable $handler): void
    {
        $this->events[$event][] = $handler;
    }

    private function emit(string $event, array $payload = []): void
    {
        if (!empty($this->events[$event])) {
            foreach ($this->events[$event] as $h) {
                try {
                    $h($payload);
                } catch (\Throwable $e) { /* no-op */
                }
            }
        }
    }

    public function query(string $sql, array $params = []): array
    {
        if (!$this->pdo) throw new \RuntimeException('PDO no inicializado');
        $this->emit('beforeQuery', ['sql' => $sql, 'params' => $params]);
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll();
            $this->emit('afterQuery', ['rows' => $rows]);
            return $rows;
        } catch (\Throwable $e) {
            $this->emit('error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function exec(string $sql, array $params = []): int
    {
        if (!$this->pdo) throw new \RuntimeException('PDO no inicializado');
        $this->emit('beforeExec', ['sql' => $sql, 'params' => $params]);
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $count = $stmt->rowCount();
            $this->emit('afterExec', ['count' => $count]);
            return $count;
        } catch (\Throwable $e) {
            $this->emit('error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function serializeConfig(): string
    {
        return serialize($this->config);
    }

    public function unserializeConfig(string $serialized): void
    {
        $cfg = @unserialize($serialized);
        if (!is_array($cfg)) {
            throw new \InvalidArgumentException('Config serializada inválida');
        }
        $this->config = $cfg;
    }
}