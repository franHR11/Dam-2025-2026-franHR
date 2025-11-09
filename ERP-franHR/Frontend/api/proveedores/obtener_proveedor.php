<?php
require_once __DIR__ . '/../config.php';

try {
    $db = getConnection();
    if (!$db) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "No se pudo conectar a la base de datos."]);
        exit;
    }

    // Verificar que se proporcionó un ID
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "ID de proveedor requerido"]);
        exit;
    }

    $proveedor_id = (int)$_GET['id'];

    // Obtener datos del proveedor
    $sql = "SELECT p.*,
                   CASE
                       WHEN p.bloqueado = 1 THEN 'bloqueado'
                       WHEN p.activo = 0 THEN 'inactivo'
                       ELSE 'activo'
                   END as estado
            FROM proveedores p
            WHERE p.id = ?";

    $stmt = $db->prepare($sql);
    $stmt->execute([$proveedor_id]);
    $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$proveedor) {
        http_response_code(404);
        echo json_encode(["ok" => false, "error" => "Proveedor no encontrado"]);
        exit;
    }

    // Decodificar certificaciones si existen
    if (!empty($proveedor['certificaciones'])) {
        $proveedor['certificaciones'] = json_decode($proveedor['certificaciones'], true);
    }

    // Obtener contactos del proveedor
    $sql_contactos = "SELECT * FROM proveedores_contactos
                      WHERE proveedor_id = ? AND activo = 1
                      ORDER BY es_contacto_principal DESC, nombre ASC";

    $stmt_contactos = $db->prepare($sql_contactos);
    $stmt_contactos->execute([$proveedor_id]);
    $contactos = $stmt_contactos->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "ok" => true,
        "proveedor" => $proveedor,
        "contactos" => $contactos
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
