<?php
require_once __DIR__ . '/facturacion_utils.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
        exit;
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data || !isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'ID de factura requerido']);
        exit;
    }

    $id = (int) $data['id'];
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'ID de factura inválido']);
        exit;
    }

    $db = facturacion_db();

    // Verificar que la factura existe
    $stmt = $db->prepare("SELECT id, estado, tipo_factura FROM facturas WHERE id = ?");
    $stmt->execute([$id]);
    $factura = $stmt->fetch();

    if (!$factura) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'Factura no encontrada']);
        exit;
    }

    // Validar que se puede eliminar
    if (in_array($factura['estado'], ['pagada', 'cobrada'])) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'No se pueden eliminar facturas pagadas o cobradas']);
        exit;
    }

    // Verificar si tiene facturas rectificativas asociadas
    $stmt = $db->prepare("SELECT COUNT(*) FROM facturas WHERE factura_rectificada_id = ?");
    $stmt->execute([$id]);
    $rectificativasCount = $stmt->fetchColumn();

    if ($rectificativasCount > 0) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'No se puede eliminar una factura que tiene rectificativas asociadas']);
        exit;
    }

    $db->beginTransaction();

    try {
        // Eliminar líneas de factura (CASCADE debería hacerlo automáticamente, pero por seguridad)
        $stmt = $db->prepare("DELETE FROM facturas_lineas WHERE factura_id = ?");
        $stmt->execute([$id]);

        // Eliminar factura
        $stmt = $db->prepare("DELETE FROM facturas WHERE id = ?");
        $stmt->execute([$id]);

        $db->commit();

        echo json_encode([
            'ok' => true,
            'message' => 'Factura eliminada correctamente'
        ]);

    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}