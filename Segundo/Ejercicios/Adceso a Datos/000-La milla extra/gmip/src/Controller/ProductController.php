<?php

declare(strict_types=1);

namespace GMIP\Controller;

use GMIP\Model\DataAccessComponent;
use PDO;
use PDOException;

class ProductController
{
    public function handle(PDO $pdo, string $method): void
    {
        try {
            switch ($method) {
                case 'GET':
                    $this->get($pdo);
                    break;
                case 'POST':
                    $this->create($pdo);
                    break;
                case 'PUT':
                    $this->update($pdo);
                    break;
                case 'DELETE':
                    $this->delete($pdo);
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Método no permitido']);
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error del servidor', 'message' => $e->getMessage()]);
        }
    }

    private function get(PDO $pdo): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $dac = (new DataAccessComponent())->withPdo($pdo);

        if ($id) {
            $rows = $dac->query('SELECT id, name, sku, price, stock, provider_id AS providerId, created_at AS createdAt FROM products WHERE id = ?', [$id]);
            if (!$rows) {
                http_response_code(404);
                echo json_encode(['error' => 'Producto no encontrado']);
                return;
            }
            echo json_encode($rows[0]);
            return;
        }

        $rows = $dac->query('SELECT id, name, sku, price, stock, provider_id AS providerId, created_at AS createdAt FROM products ORDER BY id DESC');
        echo json_encode($rows);
    }

    private function create(PDO $pdo): void
    {
        $data = $this->readJsonBody();
        $errors = $this->validateCreate($data);
        if ($errors) {
            http_response_code(400);
            echo json_encode(['error' => 'Validación', 'details' => $errors]);
            return;
        }

        $dac = (new DataAccessComponent())->withPdo($pdo);
        try {
            $dac->exec(
                'INSERT INTO products (name, sku, price, stock, provider_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())',
                [
                    $data['name'],
                    $data['sku'],
                    (float)$data['price'],
                    (int)$data['stock'],
                    $data['providerId'] !== null ? (int)$data['providerId'] : null,
                ]
            );
        } catch (PDOException $e) {
            // 1062: duplicate entry (SKU único)
            $dup = ($e->errorInfo[1] ?? null) === 1062;
            http_response_code($dup ? 409 : 500);
            echo json_encode(['error' => $dup ? 'SKU duplicado' : 'Error al crear', 'message' => $e->getMessage()]);
            return;
        }

        $id = (int)$pdo->lastInsertId();
        http_response_code(201);
        echo json_encode(['id' => $id]);
    }

    private function update(PDO $pdo): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Falta parámetro id']);
            return;
        }
        $data = $this->readJsonBody();
        $errors = $this->validateUpdate($data);
        if ($errors) {
            http_response_code(400);
            echo json_encode(['error' => 'Validación', 'details' => $errors]);
            return;
        }

        $dac = (new DataAccessComponent())->withPdo($pdo);
        // Comprobar existencia
        $exists = $dac->query('SELECT id FROM products WHERE id = ?', [$id]);
        if (!$exists) {
            http_response_code(404);
            echo json_encode(['error' => 'Producto no encontrado']);
            return;
        }

        try {
            $dac->exec(
                'UPDATE products SET name = ?, sku = ?, price = ?, stock = ?, provider_id = ? WHERE id = ?',
                [
                    $data['name'],
                    $data['sku'],
                    (float)$data['price'],
                    (int)$data['stock'],
                    $data['providerId'] !== null ? (int)$data['providerId'] : null,
                    $id,
                ]
            );
        } catch (PDOException $e) {
            $dup = ($e->errorInfo[1] ?? null) === 1062;
            http_response_code($dup ? 409 : 500);
            echo json_encode(['error' => $dup ? 'SKU duplicado' : 'Error al actualizar', 'message' => $e->getMessage()]);
            return;
        }

        echo json_encode(['updated' => true]);
    }

    private function delete(PDO $pdo): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Falta parámetro id']);
            return;
        }
        $dac = (new DataAccessComponent())->withPdo($pdo);
        $affected = $dac->exec('DELETE FROM products WHERE id = ?', [$id]);
        if ($affected === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Producto no encontrado']);
            return;
        }
        echo json_encode(['deleted' => true]);
    }

    private function readJsonBody(): array
    {
        $raw = file_get_contents('php://input') ?: '';
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    private function validateCreate(array $d): array
    {
        $err = [];
        if (!isset($d['name']) || !is_string($d['name']) || trim($d['name']) === '') {
            $err['name'] = 'Nombre requerido';
        }
        if (!isset($d['sku']) || !is_string($d['sku']) || trim($d['sku']) === '') {
            $err['sku'] = 'SKU requerido';
        }
        if (!isset($d['price']) || !is_numeric($d['price']) || (float)$d['price'] < 0) {
            $err['price'] = 'Precio inválido';
        }
        if (!isset($d['stock']) || !is_numeric($d['stock']) || (int)$d['stock'] < 0) {
            $err['stock'] = 'Stock inválido';
        }
        if (array_key_exists('providerId', $d) && !is_null($d['providerId']) && !is_numeric($d['providerId'])) {
            $err['providerId'] = 'providerId debe ser numérico o null';
        }
        return $err;
    }

    private function validateUpdate(array $d): array
    {
        // Para simplificar, exigimos los mismos campos que en create
        return $this->validateCreate($d);
    }
}