<?php
require_once __DIR__ . '/facturacion_utils.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
        exit;
    }

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'ID de factura inválido']);
        exit;
    }

    $db = facturacion_db();
    $factura = factura_obtener($db, $id);

    if (!$factura) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'Factura no encontrada']);
        exit;
    }

    echo json_encode([
        'ok' => true,
        'factura' => $factura
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}