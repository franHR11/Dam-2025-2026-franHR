<?php
// API para obtener todos los clientes
require_once __DIR__ . '/../config.php';

try {
    $db = getConnection();
    if (!$db) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "No se pudo conectar a la base de datos."]);
        exit;
    }

    $sql = "SELECT
                id,
                codigo,
                nombre_comercial,
                razon_social,
                nif_cif,
                direccion,
                codigo_postal,
                ciudad,
                provincia,
                pais,
                telefono,
                telefono2,
                email,
                web,
                tipo_cliente,
                forma_pago,
                dias_credito,
                limite_credito,
                importe_acumulado,
                saldo_pendiente,
                activo,
                bloqueado,
                observaciones,
                contacto_principal,
                cargo_contacto,
                created_at,
                updated_at
            FROM clientes
            ORDER BY nombre_comercial ASC";

    $stmt = $db->query($sql);
    $clientes = $stmt->fetchAll();

    echo json_encode(["ok" => true, "clientes" => $clientes, "total" => count($clientes)]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
?>
