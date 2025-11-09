<?php
// API para guardar nuevos clientes
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

    if (!$input) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "Datos no válidos."]);
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

    // Verificar si el código ya existe
    $stmtCheck = $db->prepare("SELECT id FROM clientes WHERE codigo = ?");
    $stmtCheck->execute([$input['codigo']]);
    if ($stmtCheck->fetch()) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "El código de cliente ya existe."]);
        exit;
    }

    // Verificar si el NIF/CIF ya existe (si se proporciona)
    if (!empty(trim($input['nif_cif']))) {
        $stmtNif = $db->prepare("SELECT id FROM clientes WHERE nif_cif = ?");
        $stmtNif->execute([trim($input['nif_cif'])]);
        if ($stmtNif->fetch()) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "El NIF/CIF ya está registrado."]);
            exit;
        }
    }

    // Preparar datos para inserción
    $sql = "INSERT INTO clientes (
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
                activo,
                bloqueado,
                observaciones,
                contacto_principal,
                cargo_contacto,
                created_by,
                created_at,
                updated_at
            ) VALUES (
                :codigo,
                :nombre_comercial,
                :razon_social,
                :nif_cif,
                :direccion,
                :codigo_postal,
                :ciudad,
                :provincia,
                :pais,
                :telefono,
                :telefono2,
                :email,
                :web,
                :tipo_cliente,
                :forma_pago,
                :dias_credito,
                :limite_credito,
                :activo,
                :bloqueado,
                :observaciones,
                :contacto_principal,
                :cargo_contacto,
                :created_by,
                NOW(),
                NOW()
            )";

    $stmt = $db->prepare($sql);

    // Bind parameters
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

    // Obtener ID del usuario de sesión (asumimos que existe)
    $created_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
    $stmt->bindParam(':created_by', $created_by, PDO::PARAM_INT);

    // Ejecutar la inserción
    if ($stmt->execute()) {
        $cliente_id = $db->lastInsertId();
        echo json_encode([
            "ok" => true,
            "mensaje" => "Cliente creado correctamente",
            "cliente_id" => $cliente_id
        ]);
    } else {
        throw new Exception("Error al insertar el cliente.");
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
?>
