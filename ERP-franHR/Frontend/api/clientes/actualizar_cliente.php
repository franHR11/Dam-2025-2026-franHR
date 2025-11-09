<?php
// API para actualizar clientes existentes
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
        echo json_encode(["ok" => false, "error" => "Datos no válidos o ID no proporcionado."]);
        exit;
    }

    // Validar campos obligatorios
    $camposObligatorios = ['codigo', 'nombre_comercial', 'tipo_cliente'];
    foreach ($camposObligatorios as $campo) {
        if (empty(trim($input[$campo]))) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "El campo '$campo' es obligatorio."]);
            exit;
        }
    }

    // Verificar si el cliente existe
    $stmtCheck = $db->prepare("SELECT id FROM clientes WHERE id = ?");
    $stmtCheck->execute([$input['id']]);
    if (!$stmtCheck->fetch()) {
        http_response_code(404);
        echo json_encode(["ok" => false, "error" => "El cliente no existe."]);
        exit;
    }

    // Verificar si el código ya existe en otro cliente
    $stmtCode = $db->prepare("SELECT id FROM clientes WHERE codigo = ? AND id != ?");
    $stmtCode->execute([$input['codigo'], $input['id']]);
    if ($stmtCode->fetch()) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "El código de cliente ya está en uso por otro cliente."]);
        exit;
    }

    // Verificar si el NIF/CIF ya existe en otro cliente (si se proporciona)
    if (!empty(trim($input['nif_cif']))) {
        $stmtNif = $db->prepare("SELECT id FROM clientes WHERE nif_cif = ? AND id != ?");
        $stmtNif->execute([trim($input['nif_cif']), $input['id']]);
        if ($stmtNif->fetch()) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "El NIF/CIF ya está registrado por otro cliente."]);
            exit;
        }
    }

    // Preparar datos para actualización
    $sql = "UPDATE clientes SET
                codigo = :codigo,
                nombre_comercial = :nombre_comercial,
                razon_social = :razon_social,
                nif_cif = :nif_cif,
                direccion = :direccion,
                codigo_postal = :codigo_postal,
                ciudad = :ciudad,
                provincia = :provincia,
                pais = :pais,
                telefono = :telefono,
                telefono2 = :telefono2,
                email = :email,
                web = :web,
                tipo_cliente = :tipo_cliente,
                forma_pago = :forma_pago,
                dias_credito = :dias_credito,
                limite_credito = :limite_credito,
                activo = :activo,
                bloqueado = :bloqueado,
                observaciones = :observaciones,
                contacto_principal = :contacto_principal,
                cargo_contacto = :cargo_contacto,
                updated_at = NOW()
            WHERE id = :id";

    $stmt = $db->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':id', $input['id'], PDO::PARAM_INT);
    $stmt->bindParam(':codigo', $input['codigo']);
    $stmt->bindParam(':nombre_comercial', $input['nombre_comercial']);
    $stmt->bindParam(':razon_social', $input['razon_social']);
    $stmt->bindParam(':nif_cif', $input['nif_cif']);
    $stmt->bindParam(':direccion', $input['direccion']);
    $stmt->bindParam(':codigo_postal', $input['codigo_postal']);
    $stmt->bindParam(':ciudad', $input['ciudad']);
    $stmt->bindParam(':provincia', $input['provincia']);
    $stmt->bindParam(':pais', $input['pais']);
    $stmt->bindParam(':telefono', $input['telefono']);
    $stmt->bindParam(':telefono2', $input['telefono2']);
    $stmt->bindParam(':email', $input['email']);
    $stmt->bindParam(':web', $input['web']);
    $stmt->bindParam(':tipo_cliente', $input['tipo_cliente']);
    $stmt->bindParam(':forma_pago', $input['forma_pago']);
    $stmt->bindParam(':dias_credito', $input['dias_credito'], PDO::PARAM_INT);
    $stmt->bindParam(':limite_credito', $input['limite_credito']);
    $stmt->bindParam(':activo', $input['activo'], PDO::PARAM_INT);
    $stmt->bindParam(':bloqueado', $input['bloqueado'], PDO::PARAM_INT);
    $stmt->bindParam(':observaciones', $input['observaciones']);
    $stmt->bindParam(':contacto_principal', $input['contacto_principal']);
    $stmt->bindParam(':cargo_contacto', $input['cargo_contacto']);

    // Ejecutar la actualización
    if ($stmt->execute()) {
        echo json_encode([
            "ok" => true,
            "mensaje" => "Cliente actualizado correctamente"
        ]);
    } else {
        throw new Exception("Error al actualizar el cliente.");
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
?>
