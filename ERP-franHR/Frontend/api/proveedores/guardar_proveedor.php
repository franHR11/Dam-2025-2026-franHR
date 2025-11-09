<?php
require_once __DIR__ . '/../config.php';

try {
    $db = getConnection();
    if (!$db) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "No se pudo conectar a la base de datos."]);
        exit;
    }

    // Obtener datos del POST
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "Datos inválidos"]);
        exit;
    }

    // Validar campos obligatorios
    $campos_obligatorios = ['nombre_comercial', 'tipo_proveedor'];
    foreach ($campos_obligatorios as $campo) {
        if (empty($data[$campo])) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "El campo $campo es obligatorio"]);
            exit;
        }
    }

    // Determinar si es creación o edición
    $es_edicion = !empty($data['id']);

    try {
        $db->beginTransaction();

        if ($es_edicion) {
            // Actualizar proveedor existente
            $sql = "UPDATE proveedores SET
                    nombre_comercial = ?,
                    razon_social = ?,
                    nif_cif = ?,
                    direccion = ?,
                    codigo_postal = ?,
                    ciudad = ?,
                    provincia = ?,
                    pais = ?,
                    telefono = ?,
                    telefono2 = ?,
                    email = ?,
                    web = ?,
                    tipo_proveedor = ?,
                    forma_pago = ?,
                    cuenta_bancaria = ?,
                    swift_bic = ?,
                    dias_pago = ?,
                    descuento_comercial = ?,
                    activo = ?,
                    bloqueado = ?,
                    certificaciones = ?,
                    es_proveedor_urgente = ?,
                    observaciones = ?,
                    contacto_principal = ?,
                    cargo_contacto = ?,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?";

            $params = [
                $data['nombre_comercial'],
                $data['razon_social'] ?? null,
                $data['nif_cif'] ?? null,
                $data['direccion'] ?? null,
                $data['codigo_postal'] ?? null,
                $data['ciudad'] ?? null,
                $data['provincia'] ?? null,
                $data['pais'] ?? 'España',
                $data['telefono'] ?? null,
                $data['telefono2'] ?? null,
                $data['email'] ?? null,
                $data['web'] ?? null,
                $data['tipo_proveedor'],
                $data['forma_pago'] ?? 'transferencia',
                $data['cuenta_bancaria'] ?? null,
                $data['swift_bic'] ?? null,
                $data['dias_pago'] ?? 30,
                $data['descuento_comercial'] ?? 0.00,
                $data['activo'] ?? 1,
                $data['bloqueado'] ?? 0,
                isset($data['certificaciones']) ? json_encode($data['certificaciones']) : null,
                $data['es_proveedor_urgente'] ?? 0,
                $data['observaciones'] ?? null,
                $data['contacto_principal'] ?? null,
                $data['cargo_contacto'] ?? null,
                $data['id']
            ];

            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            $proveedor_id = $data['id'];
            $mensaje = "Proveedor actualizado correctamente";

        } else {
            // Generar código único si no se proporciona
            if (empty($data['codigo'])) {
                $sql_codigo = "SELECT MAX(CAST(SUBSTRING(codigo, 5) AS UNSIGNED)) as max_num
                              FROM proveedores WHERE codigo LIKE 'PROV%'";
                $stmt_codigo = $db->query($sql_codigo);
                $result_codigo = $stmt_codigo->fetch();
                $next_num = ($result_codigo['max_num'] ?? 0) + 1;
                $codigo = 'PROV' . str_pad($next_num, 4, '0', STR_PAD_LEFT);
            } else {
                $codigo = $data['codigo'];
            }

            // Verificar que el código no exista
            $sql_check = "SELECT COUNT(*) as count FROM proveedores WHERE codigo = ?";
            $stmt_check = $db->prepare($sql_check);
            $stmt_check->execute([$codigo]);
            if ($stmt_check->fetchColumn() > 0) {
                http_response_code(400);
                echo json_encode(["ok" => false, "error" => "El código de proveedor ya existe"]);
                exit;
            }

            // Insertar nuevo proveedor
            $sql = "INSERT INTO proveedores (
                    codigo, nombre_comercial, razon_social, nif_cif, direccion,
                    codigo_postal, ciudad, provincia, pais, telefono, telefono2,
                    email, web, tipo_proveedor, forma_pago, cuenta_bancaria,
                    swift_bic, dias_pago, descuento_comercial, activo, bloqueado,
                    certificaciones, es_proveedor_urgente, observaciones,
                    contacto_principal, cargo_contacto, created_by, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";

            $params = [
                $codigo,
                $data['nombre_comercial'],
                $data['razon_social'] ?? null,
                $data['nif_cif'] ?? null,
                $data['direccion'] ?? null,
                $data['codigo_postal'] ?? null,
                $data['ciudad'] ?? null,
                $data['provincia'] ?? null,
                $data['pais'] ?? 'España',
                $data['telefono'] ?? null,
                $data['telefono2'] ?? null,
                $data['email'] ?? null,
                $data['web'] ?? null,
                $data['tipo_proveedor'],
                $data['forma_pago'] ?? 'transferencia',
                $data['cuenta_bancaria'] ?? null,
                $data['swift_bic'] ?? null,
                $data['dias_pago'] ?? 30,
                $data['descuento_comercial'] ?? 0.00,
                $data['activo'] ?? 1,
                $data['bloqueado'] ?? 0,
                isset($data['certificaciones']) ? json_encode($data['certificaciones']) : null,
                $data['es_proveedor_urgente'] ?? 0,
                $data['observaciones'] ?? null,
                $data['contacto_principal'] ?? null,
                $data['cargo_contacto'] ?? null,
                $_SESSION['user_id'] ?? 1
            ];

            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            $proveedor_id = $db->lastInsertId();
            $mensaje = "Proveedor creado correctamente";
        }

        $db->commit();

        echo json_encode([
            "ok" => true,
            "mensaje" => $mensaje,
            "proveedor_id" => $proveedor_id
        ]);

    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
