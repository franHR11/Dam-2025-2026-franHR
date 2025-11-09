<?php
// Listar proveedores (nombre comercial)
require_once '../config.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit();
    }

    $pdo = getConnection();
    if (!$pdo) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
        exit();
    }

    // Alias a nombre para que encaje con el JS
    $stmt = $pdo->query("SELECT id, nombre_comercial AS nombre FROM proveedores WHERE activo = 1 ORDER BY nombre_comercial ASC");
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'proveedores' => $proveedores
    ]);
} catch (Throwable $e) {
    error_log('proveedores error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}
?>