<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Acceso denegado. Se requieren permisos de administrador.'
    ]);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$accion = $input['accion'] ?? null;

if (!$accion) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Acción requerida'
    ]);
    exit();
}

try {
    $pdo = getConnection();
    if (!$pdo) {
        throw new Exception('No se pudo establecer la conexión con la base de datos');
    }

    switch ($accion) {
        case 'actualizar_configuracion':
            $configuracion = $input['configuracion'] ?? null;
            if (!$configuracion || !isset($configuracion['id'], $configuracion['valor'])) {
                throw new Exception('Datos incompletos para actualizar la configuración');
            }

            $stmt = $pdo->prepare("UPDATE modulo_configuracion SET valor = :valor, updated_at = NOW() WHERE id = :id");
            $stmt->execute([
                ':valor' => $configuracion['valor'],
                ':id' => $configuracion['id']
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Configuración actualizada correctamente'
            ]);
            break;

        case 'actualizar_permiso':
            $permiso = $input['permiso'] ?? null;
            if (!$permiso || !isset($permiso['modulo_id'], $permiso['rol'], $permiso['key'], $permiso['valor'])) {
                throw new Exception('Datos incompletos para actualizar el permiso');
            }

            $stmt = $pdo->prepare("INSERT INTO modulo_permisos (modulo_id, rol, permiso, concedido, updated_at)
                 VALUES (:modulo_id, :rol, :permiso, :valor, NOW())
                 ON DUPLICATE KEY UPDATE concedido = VALUES(concedido), updated_at = NOW()");
            $stmt->execute([
                ':modulo_id' => $permiso['modulo_id'],
                ':rol' => $permiso['rol'],
                ':permiso' => $permiso['key'],
                ':valor' => (int)$permiso['valor']
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Permiso actualizado correctamente'
            ]);
            break;

        case 'actualizar_estado_modulo':
            $moduloId = $input['modulo_id'] ?? null;
            $campo = $input['campo'] ?? null;
            $valor = $input['valor'] ?? null;

            if (!$moduloId || !in_array($campo, ['instalado', 'activo', 'menu_order'], true)) {
                throw new Exception('Datos incompletos para actualizar el módulo');
            }

            if ($campo === 'menu_order') {
                $stmt = $pdo->prepare("INSERT INTO modulo_configuracion (modulo_id, clave, valor, tipo, descripcion)
                    VALUES (:modulo_id, 'menu_order', :valor, 'numero', 'Orden del módulo en el menú')
                    ON DUPLICATE KEY UPDATE valor = VALUES(valor), updated_at = NOW()");
                $stmt->execute([
                    ':modulo_id' => $moduloId,
                    ':valor' => $valor
                ]);
            } else {
                $stmt = $pdo->prepare("UPDATE modulos SET {$campo} = :valor, updated_at = NOW() WHERE id = :id");
                $stmt->execute([
                    ':valor' => (int)$valor,
                    ':id' => $moduloId
                ]);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Módulo actualizado correctamente'
            ]);
            break;

        default:
            throw new Exception('Acción no soportada');
    }
} catch (Throwable $e) {
    error_log('[CONFIGURACION][UPDATE] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
    ]);
}
