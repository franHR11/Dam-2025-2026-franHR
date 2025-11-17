<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["ok" => false, "error" => "Método no permitido"]);
    exit;
}

try {
    $db = getConnection();
    if (!$db) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "No se pudo conectar a la base de datos."]);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || empty($input['id'])) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "ID de usuario requerido"]);
        exit;
    }

    $usuarioId = (int) $input['id'];

    $stmt = $db->prepare("SELECT * FROM usuarios WHERE Identificador = :id");
    $stmt->execute([':id' => $usuarioId]);
    $usuarioActual = $stmt->fetch();

    if (!$usuarioActual) {
        http_response_code(404);
        echo json_encode(["ok" => false, "error" => "Usuario no encontrado"]);
        exit;
    }

    $campos = ['usuario', 'email', 'rol'];
    foreach ($campos as $campo) {
        if (isset($input[$campo]) && empty(trim($input[$campo]))) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "El campo '$campo' no puede estar vacío."]);
            exit;
        }
    }

    if (!empty($input['usuario']) || !empty($input['email'])) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM usuarios WHERE (usuario = :usuario OR email = :email) AND Identificador != :id");
        $stmt->execute([
            ':usuario' => $input['usuario'] ?? $usuarioActual['usuario'],
            ':email' => $input['email'] ?? $usuarioActual['email'],
            ':id' => $usuarioId
        ]);
        if ($stmt->fetchColumn() > 0) {
            http_response_code(409);
            echo json_encode(["ok" => false, "error" => "Otro usuario ya utiliza ese usuario o email."]);
            exit;
        }
    }

    $camposActualizar = [
        'usuario' => $input['usuario'] ?? $usuarioActual['usuario'],
        'email' => $input['email'] ?? $usuarioActual['email'],
        'nombre' => $input['nombre'] ?? $usuarioActual['nombre'],
        'apellidos' => $input['apellidos'] ?? $usuarioActual['apellidos'],
        'telefono' => $input['telefono'] ?? $usuarioActual['telefono'],
        'rol' => $input['rol'] ?? $usuarioActual['rol'],
        'activo' => isset($input['activo']) ? (int) !!$input['activo'] : (int) $usuarioActual['activo'],
    ];

    $passwordSet = false;
    if (!empty($input['contrasena'])) {
        $camposActualizar['contrasena'] = password_hash($input['contrasena'], PASSWORD_BCRYPT);
        $passwordSet = true;
    }

    $setParts = [];
    $params = [':id' => $usuarioId];
    foreach ($camposActualizar as $campo => $valor) {
        $setParts[] = "$campo = :$campo";
        $params[":$campo"] = $valor;
    }

    $sql = 'UPDATE usuarios SET ' . implode(', ', $setParts) . ', updated_at = NOW() WHERE Identificador = :id';
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    echo json_encode([
        "ok" => true,
        "mensaje" => "Usuario actualizado correctamente",
        "password_actualizado" => $passwordSet
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
?>
