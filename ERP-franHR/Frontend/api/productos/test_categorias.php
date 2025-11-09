<?php
// Archivo de prueba para el API de categorías
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

try {
    $db = getConnection();
    if (!$db) {
        echo json_encode(["ok" => false, "error" => "No se pudo conectar a la base de datos"]);
        exit;
    }

    // Probar consulta simple
    $sql = "SELECT id, nombre, descripcion, categoria_padre_id, imagen, activo FROM productos_categorias ORDER BY nombre";
    $stmt = $db->query($sql);
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "ok" => true,
        "message" => "API funcionando correctamente",
        "total_categorias" => count($categorias),
        "categorias" => $categorias
    ]);

} catch (Exception $e) {
    echo json_encode([
        "ok" => false,
        "error" => $e->getMessage()
    ]);
}
?>
