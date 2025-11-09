<?php
// Autoguardar cambios parciales en producto (borrador)
require_once '../config.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit();
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Falta id de producto']);
        exit();
    }

    $pdo = getConnection();
    if (!$pdo) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
        exit();
    }

    // Actualizar solo updated_at como señal de autoguardado
    $stmt = $pdo->prepare("UPDATE productos SET updated_at = NOW() WHERE id = :id");
    $stmt->bindValue(':id', (int)$input['id'], PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    error_log('autoguardar error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}
?>