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
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id']) || empty($data['id'])) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "ID de proveedor requerido"]);
        exit;
    }

    $proveedor_id = (int)$data['id'];

    // Verificar si el proveedor tiene productos asociados
    $sql_check_productos = "SELECT COUNT(*) as count FROM productos WHERE proveedor_principal_id = ?";
    $stmt_check = $db->prepare($sql_check_productos);
    $stmt_check->execute([$proveedor_id]);
    $productos_count = $stmt_check->fetchColumn();

    // Verificar si el proveedor tiene facturas asociadas
    $sql_check_facturas = "SELECT COUNT(*) as count FROM facturas WHERE proveedor_id = ?";
    $stmt_check_facturas = $db->prepare($sql_check_facturas);
    $stmt_check_facturas->execute([$proveedor_id]);
    $facturas_count = $stmt_check_facturas->fetchColumn();

    if ($productos_count > 0 || $facturas_count > 0) {
        // No eliminar físicamente, solo desactivar
        $sql = "UPDATE proveedores SET activo = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$proveedor_id]);

        echo json_encode([
            "ok" => true,
            "mensaje" => "Proveedor desactivado correctamente. No se puede eliminar porque tiene registros asociados."
        ]);
    } else {
        try {
            $db->beginTransaction();

            // Eliminar contactos asociados
            $sql_contactos = "DELETE FROM proveedores_contactos WHERE proveedor_id = ?";
            $stmt_contactos = $db->prepare($sql_contactos);
            $stmt_contactos->execute([$proveedor_id]);

            // Eliminar proveedor
            $sql = "DELETE FROM proveedores WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$proveedor_id]);

            $db->commit();

            echo json_encode([
                "ok" => true,
                "mensaje" => "Proveedor eliminado correctamente"
            ]);

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
