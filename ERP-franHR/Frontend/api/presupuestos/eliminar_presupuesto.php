<?php
require_once __DIR__ . '/presupuestos_utils.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
        exit;
    }

    $payload = json_decode(file_get_contents('php://input'), true);
    $id = isset($payload['id']) ? (int) $payload['id'] : 0;
    if ($id <= 0) {
        throw new InvalidArgumentException('ID de presupuesto inválido');
    }

    $db = presupuestos_db();

    $stmt = $db->prepare('SELECT id FROM presupuestos WHERE id = ?');
    $stmt->execute([$id]);
    if (!$stmt->fetchColumn()) {
        throw new InvalidArgumentException('El presupuesto no existe');
    }

    $stmtFacturas = $db->prepare('SELECT COUNT(*) FROM facturas WHERE presupuesto_id = ?');
    $stmtFacturas->execute([$id]);
    if ((int) $stmtFacturas->fetchColumn() > 0) {
        throw new InvalidArgumentException('No se puede eliminar porque está vinculado a facturas');
    }

    $stmtDelete = $db->prepare('DELETE FROM presupuestos WHERE id = ?');
    $stmtDelete->execute([$id]);

    echo json_encode(['ok' => true, 'mensaje' => 'Presupuesto eliminado correctamente']);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
