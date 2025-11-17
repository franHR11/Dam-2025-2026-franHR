<?php
// API para crear usuarios
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
    if (!$input) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "Datos no válidos"]);
        exit;
    }

    $required = ['usuario', 'email', 'contrasena', 'rol'];
    foreach ($required as $field) {
        if (empty(trim($input[$field] ?? ''))) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "El campo '$field' es obligatorio."]);
            exit;
        }
    }

    // Verificar existencia previa
    $stmt = $db->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario OR email = :email");
    $stmt->execute([
        ':usuario' => $input['usuario'],
        ':email' => $input['email']
    ]);
    if ($stmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(["ok" => false, "error" => "El usuario o email ya existe."]);
        exit;
    }

    $passwordHash = password_hash($input['contrasena'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO usuarios (usuario, email, contrasena, nombre, apellidos, telefono, rol, activo, fecha_creacion)
            VALUES (:usuario, :email, :contrasena, :nombre, :apellidos, :telefono, :rol, :activo, NOW())";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':usuario', trim($input['usuario']));
    $stmt->bindValue(':email', trim($input['email']));
    $stmt->bindValue(':contrasena', $passwordHash);
    $stmt->bindValue(':nombre', trim($input['nombre'] ?? ''));
    $stmt->bindValue(':apellidos', trim($input['apellidos'] ?? ''));
    $stmt->bindValue(':telefono', trim($input['telefono'] ?? ''));
    $stmt->bindValue(':rol', $input['rol']);
    $stmt->bindValue(':activo', !empty($input['activo']) ? 1 : 0, PDO::PARAM_INT);

    $stmt->execute();

    echo json_encode([
        "ok" => true,
        "mensaje" => "Usuario creado correctamente",
        "usuario_id" => $db->lastInsertId()
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
?>
