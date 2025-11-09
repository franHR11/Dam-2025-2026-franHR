<?php
// API para eliminar clientes
require_once __DIR__ . '/../config.php';

try {
    $db = getConnection();
    if (!$db) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "No se pudo conectar a la base de datos."]);
        exit;
    }

    // Obtener datos JSON del POST
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "ID de cliente no proporcionado."]);
        exit;
    }

    $cliente_id = (int)$input['id'];

    // Verificar si el cliente existe
    $stmtCheck = $db->prepare("SELECT id, nombre_comercial FROM clientes WHERE id = ?");
    $stmtCheck->execute([$cliente_id]);
    $cliente = $stmtCheck->fetch();

    if (!$cliente) {
        http_response_code(404);
        echo json_encode(["ok" => false, "error" => "El cliente no existe."]);
        exit;
    }

    // Verificar si el cliente tiene facturas o pedidos asociados
    // Esto evita eliminar clientes con transacciones importantes
    $stmtFacturas = $db->prepare("SELECT COUNT(*) as total FROM facturas WHERE cliente_id = ?");
    $stmtFacturas->execute([$cliente_id]);
    $facturasCount = $stmtFacturas->fetch()['total'];

    if ($facturasCount > 0) {
        http_response_code(400);
        echo json_encode([
            "ok" => false,
            "error" => "No se puede eliminar el cliente porque tiene {$facturasCount} factura(s) asociada(s). Considera desactivarlo en lugar de eliminarlo."
        ]);
        exit;
    }

    // Opcional: Verificar si el cliente tiene contactos asociados
    $stmtContactos = $db->prepare("SELECT COUNT(*) as total FROM clientes_contactos WHERE cliente_id = ?");
    $stmtContactos->execute([$cliente_id]);
    $contactosCount = $stmtContactos->fetch()['total'];

    // Iniciar transacción para asegurar consistencia
    $db->beginTransaction();

    try {
        // Si tiene contactos, eliminarlos primero
        if ($contactosCount > 0) {
            $stmtDeleteContactos = $db->prepare("DELETE FROM clientes_contactos WHERE cliente_id = ?");
            $stmtDeleteContactos->execute([$cliente_id]);
        }

        // Eliminar el cliente
        $stmtDelete = $db->prepare("DELETE FROM clientes WHERE id = ?");
        $stmtDelete->execute([$cliente_id]);

        // Confirmar transacción
        $db->commit();

        echo json_encode([
            "ok" => true,
            "mensaje" => "Cliente '{$cliente['nombre_comercial']}' eliminado correctamente",
            "contactos_eliminados" => $contactosCount
        ]);

    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $db->rollback();
        throw $e;
    }

} catch (Throwable $e) {
    // Si hay una transacción activa, revertirla
    if (isset($db) && $db->inTransaction()) {
        $db->rollback();
    }

    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
?>
