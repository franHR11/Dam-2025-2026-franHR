<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["ok" => false, "error" => "Método no permitido"]);
    exit;
}

try {
    $db = getConnection();
    if (!$db) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "No se pudo conectar a la base de datos."]);
        exit;
    }

    $payload = file_get_contents('php://input');
    $input = $payload ? json_decode($payload, true) : [];

    if (!$input || empty($input['id'])) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "ID de usuario requerido"]);
        exit;
    }

    $usuarioId = (int) $input['id'];

    $stmt = $db->prepare("SELECT Identificador FROM usuarios WHERE Identificador = :id");
    $stmt->execute([':id' => $usuarioId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(["ok" => false, "error" => "Usuario no encontrado"]);
        exit;
    }

    $stmtDelete = $db->prepare("DELETE FROM usuarios WHERE Identificador = :id");
    $stmtDelete->execute([':id' => $usuarioId]);

    echo json_encode([
        "ok" => true,
        "mensaje" => "Usuario eliminado correctamente"
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
?>
