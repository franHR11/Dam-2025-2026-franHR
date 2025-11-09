<?php
// Configuración de headers CORS
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

// Manejar peticiones OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir la configuración de la base de datos
require_once '../config.php';

// Verificar autenticación y permisos de administrador
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay sesión activa (flexible para diferentes sistemas de autenticación)
$usuarioAutenticado = false;
if (isset($_SESSION['user_id']) || isset($_SESSION['username']) || isset($_SESSION['user_rol'])) {
    $usuarioAutenticado = true;
}

// Verificar permisos de administrador
$esAdmin = false;
if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin') {
    $esAdmin = true;
} elseif (isset($_SESSION['username']) && $_SESSION['username'] === 'admin') {
    $esAdmin = true;
}

if (!$usuarioAutenticado || !$esAdmin) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Acceso denegado. Se requieren permisos de administrador.',
        'debug' => [
            'user_id' => $_SESSION['user_id'] ?? 'no_set',
            'username' => $_SESSION['username'] ?? 'no_set',
            'user_rol' => $_SESSION['user_rol'] ?? 'no_set',
            'autenticado' => $usuarioAutenticado,
            'es_admin' => $esAdmin
        ]
    ]);
    exit();
}

// Obtener el método HTTP
$method = $_SERVER['REQUEST_METHOD'];

try {
    // Obtener conexión a la base de datos
    $pdo = getConnection();

    if (!$pdo) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error de conexión a la base de datos'
        ]);
        exit();
    }

    switch ($method) {
        case 'GET':
            // Obtener lista de todos los módulos (instalados y no instalados)
            $sql = "
                SELECT
                    m.id,
                    m.nombre,
                    m.nombre_tecnico,
                    m.descripcion,
                    m.version,
                    m.icono,
                    m.categoria,
                    m.instalado,
                    m.activo,
                    m.dependencias,
                    m.autor,
                    m.fecha_instalacion,
                    m.fecha_activacion,
                    mc.valor as menu_order,
                    CASE
                        WHEN m.instalado = 1 AND m.activo = 1 THEN 'activo'
                        WHEN m.instalado = 1 AND m.activo = 0 THEN 'inactivo'
                        WHEN m.instalado = 0 THEN 'no_instalado'
                    END as estado
                FROM modulos m
                LEFT JOIN modulo_configuracion mc ON m.id = mc.modulo_id AND mc.clave = 'menu_order'
                ORDER BY
                    CASE WHEN m.instalado = 0 THEN 2 ELSE 1 END,
                    CAST(COALESCE(mc.valor, '999') AS UNSIGNED) ASC,
                    m.nombre ASC
            ";

            $stmt = $pdo->query($sql);
            $modulos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Procesar dependencias JSON
            foreach ($modulos as &$modulo) {
                $modulo['dependencias'] = $modulo['dependencias'] ? json_decode($modulo['dependencias'], true) : [];
                $modulo['puede_activar'] = true;
                $modulo['puede_desinstalar'] = $modulo['nombre_tecnico'] !== 'dashboard'; // El dashboard no se puede desinstalar
            }

            echo json_encode([
                'success' => true,
                'message' => 'Lista de módulos obtenida correctamente',
                'data' => $modulos
            ]);
            break;

        case 'POST':
            // Instalar un módulo
            $input = json_decode(file_get_contents('php://input'), true);

            if (!isset($input['modulo_id'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Se requiere el ID del módulo'
                ]);
                exit();
            }

            $moduloId = $input['modulo_id'];

            // Verificar que el módulo existe y no está instalado
            $stmt = $pdo->prepare("SELECT nombre, nombre_tecnico, dependencias, instalado FROM modulos WHERE id = :id");
            $stmt->bindParam(':id', $moduloId);
            $stmt->execute();
            $modulo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$modulo) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Módulo no encontrado'
                ]);
                exit();
            }

            if ($modulo['instalado']) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'El módulo ya está instalado'
                ]);
                exit();
            }

            // Verificar dependencias
            $dependencias = $modulo['dependencias'] ? json_decode($modulo['dependencias'], true) : [];
            $dependenciasFaltantes = [];

            if (!empty($dependencias)) {
                $placeholders = str_repeat('?,', count($dependencias) - 1) . '?';
                $stmt = $pdo->prepare("
                    SELECT nombre_tecnico
                    FROM modulos
                    WHERE nombre_tecnico IN ($placeholders) AND (instalado = 0 OR activo = 0)
                ");
                $stmt->execute($dependencias);
                $faltantes = $stmt->fetchAll(PDO::FETCH_COLUMN);

                if (!empty($faltantes)) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Faltan dependencias requeridas: ' . implode(', ', $faltantes)
                    ]);
                    exit();
                }
            }

            // Instalar el módulo
            $pdo->beginTransaction();

            try {
                // Marcar como instalado
                $stmt = $pdo->prepare("
                    UPDATE modulos
                    SET instalado = 1,
                        fecha_instalacion = NOW(),
                        updated_at = NOW()
                    WHERE id = :id
                ");
                $stmt->bindParam(':id', $moduloId);
                $stmt->execute();

                // Insertar configuración por defecto
                $stmt = $pdo->prepare("
                    INSERT IGNORE INTO modulo_configuracion (modulo_id, clave, valor, tipo, descripcion)
                    VALUES (:modulo_id, 'menu_order', '999', 'numero', 'Orden en el menú')
                ");
                $stmt->bindParam(':modulo_id', $moduloId);
                $stmt->execute();

                // Insertar permisos por defecto para admin
                $permisos = ['ver', 'crear', 'editar', 'eliminar', 'listar', 'configurar'];
                foreach ($permisos as $permiso) {
                    $stmt = $pdo->prepare("
                        INSERT IGNORE INTO modulo_permisos (modulo_id, rol, permiso, concedido)
                        VALUES (:modulo_id, 'admin', :permiso, 1)
                    ");
                    $stmt->bindParam(':modulo_id', $moduloId);
                    $stmt->bindParam(':permiso', $permiso);
                    $stmt->execute();
                }

                $pdo->commit();

                echo json_encode([
                    'success' => true,
                    'message' => "Módulo '{$modulo['nombre']}' instalado correctamente",
                    'data' => [
                        'modulo_id' => $moduloId,
                        'nombre' => $modulo['nombre']
                    ]
                ]);

            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;

        case 'PUT':
            // Activar/Desactivar un módulo instalado
            $input = json_decode(file_get_contents('php://input'), true);

            if (!isset($input['modulo_id']) || !isset($input['accion'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Se requiere el ID del módulo y la acción (activar/desactivar)'
                ]);
                exit();
            }

            $moduloId = $input['modulo_id'];
            $accion = $input['accion'];

            if (!in_array($accion, ['activar', 'desactivar'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Acción no válida. Use "activar" o "desactivar"'
                ]);
                exit();
            }

            // Verificar que el módulo existe y está instalado
            $stmt = $pdo->prepare("SELECT nombre, nombre_tecnico, instalado, activo FROM modulos WHERE id = :id");
            $stmt->bindParam(':id', $moduloId);
            $stmt->execute();
            $modulo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$modulo) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Módulo no encontrado'
                ]);
                exit();
            }

            if (!$modulo['instalado']) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'El módulo debe estar instalado antes de poder activarlo/desactivarlo'
                ]);
                exit();
            }

            // No permitir desactivar el dashboard
            if ($modulo['nombre_tecnico'] === 'dashboard' && $accion === 'desactivar') {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'No se puede desactivar el módulo Dashboard'
                ]);
                exit();
            }

            $nuevoEstado = ($accion === 'activar') ? 1 : 0;
            $estadoActual = $modulo['activo'];

            if ($estadoActual == $nuevoEstado) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => "El módulo ya está " . ($nuevoEstado ? 'activo' : 'inactivo')
                ]);
                exit();
            }

            // Actualizar estado
            $stmt = $pdo->prepare("
                UPDATE modulos
                SET activo = :activo,
                    fecha_activacion = " . ($accion === 'activar' ? 'NOW()' : 'NULL') . ",
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmt->bindParam(':activo', $nuevoEstado, PDO::PARAM_INT);
            $stmt->bindParam(':id', $moduloId);
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'message' => "Módulo '{$modulo['nombre']}' " . ($nuevoEstado ? 'activado' : 'desactivado') . " correctamente",
                'data' => [
                    'modulo_id' => $moduloId,
                    'nombre' => $modulo['nombre'],
                    'accion' => $accion,
                    'nuevo_estado' => $nuevoEstado
                ]
            ]);
            break;

        case 'DELETE':
            // Desinstalar un módulo
            $moduloId = $_GET['id'] ?? null;

            if (!$moduloId) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Se requiere el ID del módulo a desinstalar'
                ]);
                exit();
            }

            // Verificar que el módulo existe
            $stmt = $pdo->prepare("SELECT nombre, nombre_tecnico, instalado FROM modulos WHERE id = :id");
            $stmt->bindParam(':id', $moduloId);
            $stmt->execute();
            $modulo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$modulo) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Módulo no encontrado'
                ]);
                exit();
            }

            if (!$modulo['instalado']) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'El módulo no está instalado'
                ]);
                exit();
            }

            // No permitir desinstalar módulos críticos
            $modulosProtegidos = ['dashboard', 'usuarios'];
            if (in_array($modulo['nombre_tecnico'], $modulosProtegidos)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'No se puede desinstalar un módulo crítico del sistema'
                ]);
                exit();
            }

            // Verificar que ningún otro módulo activo dependa de este
            $stmt = $pdo->prepare("
                SELECT m.nombre
                FROM modulos m
                WHERE m.instalado = 1 AND m.activo = 1
                AND m.nombre_tecnico != :nombre_tecnico
                AND JSON_CONTAINS(m.dependencias, :nombre_tecnico_json)
            ");
            $nombreTecnico = $modulo['nombre_tecnico'];
            $nombreTecnicoJson = '"' . $nombreTecnico . '"';
            $stmt->bindParam(':nombre_tecnico', $nombreTecnico);
            $stmt->bindParam(':nombre_tecnico_json', $nombreTecnicoJson);
            $stmt->execute();
            $modulosDependientes = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($modulosDependientes)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'No se puede desinstalar. Módulos que dependen de este: ' . implode(', ', $modulosDependientes)
                ]);
                exit();
            }

            // Desinstalar el módulo
            $pdo->beginTransaction();

            try {
                // Desactivar primero
                $stmt = $pdo->prepare("
                    UPDATE modulos
                    SET activo = 0,
                        fecha_activacion = NULL,
                        updated_at = NOW()
                    WHERE id = :id
                ");
                $stmt->bindParam(':id', $moduloId);
                $stmt->execute();

                // Marcar como no instalado
                $stmt = $pdo->prepare("
                    UPDATE modulos
                    SET instalado = 0,
                        fecha_instalacion = NULL,
                        updated_at = NOW()
                    WHERE id = :id
                ");
                $stmt->bindParam(':id', $moduloId);
                $stmt->execute();

                // Eliminar configuración y permisos (se restaurarán al reinstalar)
                $stmt = $pdo->prepare("DELETE FROM modulo_configuracion WHERE modulo_id = :id");
                $stmt->bindParam(':id', $moduloId);
                $stmt->execute();

                $stmt = $pdo->prepare("DELETE FROM modulo_permisos WHERE modulo_id = :id");
                $stmt->bindParam(':id', $moduloId);
                $stmt->execute();

                $pdo->commit();

                echo json_encode([
                    'success' => true,
                    'message' => "Módulo '{$modulo['nombre']}' desinstalado correctamente",
                    'data' => [
                        'modulo_id' => $moduloId,
                        'nombre' => $modulo['nombre']
                    ]
                ]);

            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;

        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método HTTP no permitido'
            ]);
            break;
    }

} catch (PDOException $e) {
    error_log("Error en la base de datos en gestión de módulos: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor (BD)'
    ]);
} catch (Exception $e) {
    error_log("Error general en gestión de módulos: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error inesperado en el servidor'
    ]);
}
?>
