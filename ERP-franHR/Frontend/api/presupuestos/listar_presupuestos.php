<?php
require_once __DIR__ . '/presupuestos_utils.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
        exit;
    }

    $db = presupuestos_db();

    $estado = isset($_GET['estado']) && $_GET['estado'] !== '' ? $_GET['estado'] : null;
    $clienteId = isset($_GET['cliente_id']) ? (int) $_GET['cliente_id'] : null;
    $busqueda = isset($_GET['q']) ? trim($_GET['q']) : null;

    $sql = 'SELECT
                p.id,
                p.numero_presupuesto,
                p.ejercicio,
                p.fecha,
                p.fecha_valido_hasta,
                p.estado,
                p.total,
                p.base_imponible,
                p.importe_iva,
                p.created_at,
                c.id AS cliente_id,
                c.nombre_comercial AS cliente_nombre
            FROM presupuestos p
            INNER JOIN clientes c ON c.id = p.cliente_id
            WHERE 1=1';

    $params = [];

    if ($estado) {
        $sql .= ' AND p.estado = ?';
        $params[] = $estado;
    }

    if ($clienteId) {
        $sql .= ' AND p.cliente_id = ?';
        $params[] = $clienteId;
    }

    if ($busqueda) {
        $sql .= ' AND (p.numero_presupuesto LIKE ? OR c.nombre_comercial LIKE ?)';
        $buscador = '%' . $busqueda . '%';
        $params[] = $buscador;
        $params[] = $buscador;
    }

    $sql .= ' ORDER BY p.created_at DESC LIMIT 200';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $presupuestos = $stmt->fetchAll();

    echo json_encode([
        'ok' => true,
        'presupuestos' => $presupuestos,
        'total' => count($presupuestos),
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
