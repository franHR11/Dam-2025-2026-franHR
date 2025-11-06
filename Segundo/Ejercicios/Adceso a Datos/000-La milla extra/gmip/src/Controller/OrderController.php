<?php

declare(strict_types=1);

namespace GMIP\Controller;

use GMIP\Model\DataAccessComponent;
use PDO;
use PDOException;
use JsonException;

class OrderController
{
    public function handle(PDO $pdo, string $method): void
    {
        try {
            $action = $_GET['action'] ?? null;
            if ($action === 'procesar' && $method === 'POST') {
                $this->process($pdo);
                return;
            }

            switch ($method) {
                case 'GET':
                    $this->get($pdo);
                    break;
                case 'POST':
                    $this->create($pdo);
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'MÃ©todo no permitido']);
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
            $order = $dac->query('SELECT id, code, created_at AS createdAt, status FROM orders WHERE id = ?', [$id]);
            if (!$order) {
                http_response_code(404);
                echo json_encode(['error' => 'Pedido no encontrado']);
                return;
            }
            $items = $dac->query(
                'SELECT oi.id, oi.product_id AS productId, p.name AS productName, oi.quantity, oi.price
                 FROM order_items oi INNER JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?',
                [$id]
            );
            $order[0]['items'] = $items;
            try {
                echo json_encode($order[0], JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Error de serialización JSON', 'message' => $e->getMessage()], JSON_THROW_ON_ERROR);
            }
            return;
        }
        $rows = $dac->query('SELECT id, code, created_at AS createdAt, status FROM orders ORDER BY id DESC');
        try {
            echo json_encode($rows, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error de serialización JSON', 'message' => $e->getMessage()], JSON_THROW_ON_ERROR);
        }
    }

    private function create(PDO $pdo): void
    {
        $data = $this->readJsonBody();
        $errors = $this->validateCreate($data);
        if ($errors) {
            http_response_code(400);
            echo json_encode(['error' => 'ValidaciÃ³n', 'details' => $errors]);
            return;
        }

        $code = $data['code'] ?? $this->generateCode();
        $items = $data['items'];

        try {
            $pdo->beginTransaction();
            $dac = (new DataAccessComponent())->withPdo($pdo);

            // Insertar pedido
            $dac->exec('INSERT INTO orders (code, created_at, status) VALUES (?, NOW(), "pending")', [$code]);
            $orderId = (int)$pdo->lastInsertId();

            // Insertar items
            foreach ($items as $it) {
                $dac->exec(
                    'INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)',
                    [$orderId, (int)$it['productId'], (int)$it['quantity'], (float)$it['price']]
                );
            }

            $pdo->commit();
            http_response_code(201);
            echo json_encode(['id' => $orderId, 'code' => $code]);
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $sqlState = $e->errorInfo[1] ?? null;
            $dup = $sqlState === 1062; // cÃ³digo Ãºnico duplicado
            $fkFail = $sqlState === 1452; // FK invÃ¡lida (producto no existe)
            if ($dup) {
                http_response_code(409);
                echo json_encode(['error' => 'CÃ³digo de pedido duplicado', 'message' => $e->getMessage()]);
            } elseif ($fkFail) {
                http_response_code(400);
                echo json_encode(['error' => 'AlgÃºn productId no existe', 'message' => $e->getMessage()]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al crear pedido', 'message' => $e->getMessage()]);
            }
        }
    }

    private function process(PDO $pdo): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Falta parÃ¡metro id']);
            return;
        }

        try {
            // Verificar existencia y estado
            $dac = (new DataAccessComponent())->withPdo($pdo);
            $order = $dac->query('SELECT id, status FROM orders WHERE id = ?', [$id]);
            if (!$order) {
                http_response_code(404);
                echo json_encode(['error' => 'Pedido no encontrado']);
                return;
            }
            if ($order[0]['status'] === 'processed') {
                http_response_code(409);
                echo json_encode(['error' => 'Pedido ya procesado']);
                return;
            }

            // Llamar al SP
            $stmt = $pdo->prepare('CALL sp_process_order(?)');
            $stmt->execute([$id]);
            // Algunas versiones devuelven un result set vacÃ­o: cerrar cursor
            $stmt->closeCursor();

            // Consultar estado actualizado
            $updated = $dac->query('SELECT id, status FROM orders WHERE id = ?', [$id]);
            echo json_encode(['processed' => true, 'status' => $updated[0]['status']]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al procesar pedido', 'message' => $e->getMessage()]);
        }
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
        if (isset($d['code']) && (!is_string($d['code']) || trim($d['code']) === '')) {
            $err['code'] = 'CÃ³digo invÃ¡lido';
        }
        if (!isset($d['items']) || !is_array($d['items']) || count($d['items']) === 0) {
            $err['items'] = 'Items requeridos';
        } else {
            foreach ($d['items'] as $idx => $it) {
                if (!isset($it['productId']) || !is_numeric($it['productId'])) {
                    $err["items[$idx].productId"] = 'productId invÃ¡lido';
                }
                if (!isset($it['quantity']) || !is_numeric($it['quantity']) || (int)$it['quantity'] <= 0) {
                    $err["items[$idx].quantity"] = 'quantity invÃ¡lida (>0)';
                }
                if (!isset($it['price']) || !is_numeric($it['price']) || (float)$it['price'] < 0) {
                    $err["items[$idx].price"] = 'price invÃ¡lido (>=0)';
                }
            }
        }
        return $err;
    }

    private function generateCode(): string
    {
        return 'ORD-' . date('Ymd-His') . '-' . mt_rand(100, 999);
    }
}
