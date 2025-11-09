<?php
require_once __DIR__ . '/../config.php';

function jsonInput() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(["ok" => false, "error" => "Método no permitido"]);
        exit;
    }

    $db = getConnection();
    if (!$db) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "No se pudo conectar a la base de datos."]);
        exit;
    }

    $p = jsonInput();
    $id = isset($p['id']) ? intval($p['id']) : null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "ID de producto inválido"]);
        exit;
    }

    $stmt = $db->prepare("DELETE FROM productos WHERE id = :id");
    $stmt->execute(['id' => $id]);

    echo json_encode(["ok" => true, "data" => ["id" => $id]]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}