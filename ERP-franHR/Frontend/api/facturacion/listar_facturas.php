<?php
require_once __DIR__ . '/facturacion_utils.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
        exit;
    }

    $db = facturacion_db();

    $estado = isset($_GET['estado']) && $_GET['estado'] !== '' ? $_GET['estado'] : null;
    $clienteId = isset($_GET['cliente_id']) ? (int) $_GET['cliente_id'] : null;
    $tipo = isset($_GET['tipo']) && $_GET['tipo'] !== '' ? $_GET['tipo'] : null;
    $q = isset($_GET['q']) ? trim($_GET['q']) : null;

    $sql = 'SELECT f.id, f.numero_factura, f.numero_serie, f.ejercicio, f.fecha,
                   f.fecha_vencimiento, f.estado, f.tipo_factura, f.total,
                   f.base_imponible, f.importe_iva, f.importe_irpf, f.importe_pagado,
                   c.nombre_comercial AS cliente_nombre
            FROM facturas f
            LEFT JOIN clientes c ON c.id = f.cliente_id
            WHERE 1=1';
    $params = [];

    if ($estado) {
        $sql .= ' AND f.estado = ?';
        $params[] = $estado;
    }
    if ($clienteId) {
        $sql .= ' AND f.cliente_id = ?';
        $params[] = $clienteId;
    }
    if ($tipo) {
        $sql .= ' AND f.tipo_factura = ?';
        $params[] = $tipo;
    }
    if ($q) {
        $sql .= ' AND (f.numero_factura LIKE ? OR c.nombre_comercial LIKE ?)';
        $like = '%' . $q . '%';
        $params[] = $like;
        $params[] = $like;
    }

    $sql .= ' ORDER BY f.created_at DESC LIMIT 200';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $facturas = $stmt->fetchAll();

    echo json_encode([
        'ok' => true,
        'facturas' => $facturas,
        'total' => count($facturas),
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
