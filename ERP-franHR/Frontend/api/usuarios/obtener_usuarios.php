<?php
// API para obtener la lista de usuarios del sistema
require_once __DIR__ . '/../config.php';

try {
    $db = getConnection();
    if (!$db) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "No se pudo conectar a la base de datos."]);
        exit;
    }

    $busqueda = trim($_GET['busqueda'] ?? '');
    $rol = trim($_GET['rol'] ?? '');
    $estado = trim($_GET['estado'] ?? '');

    $sql = "SELECT
                Identificador AS id,
                usuario,
                email,
                nombre,
                apellidos,
                CONCAT_WS(' ', COALESCE(nombre, ''), COALESCE(apellidos, '')) AS nombre_completo,
                telefono,
                rol,
                activo,
                fecha_creacion,
                fecha_ultimo_login
            FROM usuarios
            WHERE 1=1";

    $params = [];

    if ($busqueda !== '') {
        $sql .= " AND (usuario LIKE :busqueda OR email LIKE :busqueda OR nombre LIKE :busqueda OR apellidos LIKE :busqueda)";
        $params[':busqueda'] = "%$busqueda%";
    }

    if ($rol !== '') {
        $sql .= " AND rol = :rol";
        $params[':rol'] = $rol;
    }

    if ($estado !== '') {
        if ($estado === 'activos') {
            $sql .= " AND activo = 1";
        } elseif ($estado === 'inactivos') {
            $sql .= " AND activo = 0";
        }
    }

    $sql .= " ORDER BY nombre IS NULL, nombre ASC, apellidos ASC, usuario ASC";

    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();

    $usuarios = $stmt->fetchAll();

    echo json_encode([
        "ok" => true,
        "usuarios" => $usuarios,
        "total" => count($usuarios)
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
?>
