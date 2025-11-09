<?php
require_once __DIR__ . '/../config.php';

try {
    $db = getConnection();
    if (!$db) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "No se pudo conectar a la base de datos."]);
        exit;
    }

    $sql = "SELECT id, nombre_comercial AS nombre FROM proveedores WHERE activo = 1 ORDER BY nombre_comercial";
    $stmt = $db->query($sql);
    $proveedores = $stmt->fetchAll();

    echo json_encode(["ok" => true, "proveedores" => $proveedores]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}