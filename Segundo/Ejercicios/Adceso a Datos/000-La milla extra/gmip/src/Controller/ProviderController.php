<?php

declare(strict_types=1);

namespace GMIP\Controller;

use GMIP\Model\DataAccessComponent;
use PDO;

class ProviderController
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
            $rows = $dac->query('SELECT id, name, email, phone, created_at AS createdAt FROM providers WHERE id = ?', [$id]);
            if (!$rows) {
                http_response_code(404);
                echo json_encode(['error' => 'Proveedor no encontrado']);
                return;
            }
            echo json_encode($rows[0]);
            return;
        }
        $rows = $dac->query('SELECT id, name, email, phone, created_at AS createdAt FROM providers ORDER BY id DESC');
        echo json_encode($rows);
    }

    private function create(PDO $pdo): void
    {
        $data = $this->readJsonBody();
        $errors = $this->validate($data);
        if ($errors) {
            http_response_code(400);
            echo json_encode(['error' => 'Validación', 'details' => $errors]);
            return;
        }
        $dac = (new DataAccessComponent())->withPdo($pdo);
        $dac->exec(
            'INSERT INTO providers (name, email, phone, created_at) VALUES (?, ?, ?, NOW())',
            [
                $data['name'],
                $data['email'],
                $data['phone'],
            ]
        );
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
        $errors = $this->validate($data);
        if ($errors) {
            http_response_code(400);
            echo json_encode(['error' => 'Validación', 'details' => $errors]);
            return;
        }
        $dac = (new DataAccessComponent())->withPdo($pdo);
        $exists = $dac->query('SELECT id FROM providers WHERE id = ?', [$id]);
        if (!$exists) {
            http_response_code(404);
            echo json_encode(['error' => 'Proveedor no encontrado']);
            return;
        }
        $dac->exec(
            'UPDATE providers SET name = ?, email = ?, phone = ? WHERE id = ?',
            [
                $data['name'],
                $data['email'],
                $data['phone'],
                $id,
            ]
        );
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
        $affected = $dac->exec('DELETE FROM providers WHERE id = ?', [$id]);
        if ($affected === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Proveedor no encontrado']);
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

    private function validate(array $d): array
    {
        $err = [];
        if (!isset($d['name']) || !is_string($d['name']) || trim($d['name']) === '') {
            $err['name'] = 'Nombre requerido';
        }
        if (!isset($d['email']) || !is_string($d['email']) || !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            $err['email'] = 'Email inválido';
        }
        if (!isset($d['phone']) || !is_string($d['phone']) || trim($d['phone']) === '') {
            $err['phone'] = 'Teléfono requerido';
        }
        return $err;
    }
}