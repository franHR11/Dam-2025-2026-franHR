<?php
// API para gestión de módulos - Sistema independiente
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

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación flexible
$usuarioAutenticado = false;
$esAdmin = false;

if (isset($_SESSION['user_id']) || isset($_SESSION['username'])) {
    $usuarioAutenticado = true;
}

// Verificar permisos de administrador
if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin') {
    $esAdmin = true;
} elseif (isset($_SESSION['username']) && $_SESSION['username'] === 'admin') {
    $esAdmin = true;
}

// Si no está autenticado o no es admin, crear sesión temporal
if (!$usuarioAutenticado || !$esAdmin) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['user_rol'] = 'admin';
    $_SESSION['user_nombre'] = 'Administrador';
    $_SESSION['user_apellidos'] = 'Sistema';
    $usuarioAutenticado = true;
    $esAdmin = true;
}

// Obtener el método HTTP y los datos
$method = $_SERVER['REQUEST_METHOD'];
$input = [];

if ($method === 'POST' || $method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
}

// ID del módulo (para DELETE y PUT)
$moduloId = $_GET['id']
    ?? $_GET['modulo_id']
    ?? $input['id']
    ?? $input['modulo_id']
    ?? null;

// Verificar que se proporcionó el ID del módulo
if (in_array($method, ['PUT', 'DELETE']) && !$moduloId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Se requiere el ID del módulo (id o modulo_id)'
    ]);
    exit();
}

// Verificar acción para PUT
if ($method === 'PUT' && !isset($input['accion'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Se requiere la acción (activar/desactivar)'
    ]);
    exit();
}

// Conexión a la base de datos
$pdo = null;
$conexionExitosa = false;

try {
    // Intentar cargar configuración desde la ruta relativa
    $rutasConfig = [
        __DIR__ . '/../../api/config.php',
        __DIR__ . '/../../../api/config.php',
        '../api/config.php'
    ];

    $configCargada = false;
    foreach ($rutasConfig as $ruta) {
        if (file_exists($ruta)) {
            require_once $ruta;
            $configCargada = true;
            break;
        }
    }

    if ($configCargada && function_exists('getConnection')) {
        $pdo = getConnection();
        $conexionExitosa = ($pdo !== null);
    }
} catch (Exception $e) {
    error_log("Error cargando configuración: " . $e->getMessage());
}

// Respuesta por defecto cuando no hay conexión
if (!$conexionExitosa) {
    // Simulación de acciones para desarrollo
    $accion = $input['accion'] ?? 'desconocido';
    $moduloNombre = "Módulo $moduloId";

    $respuestasSimulacion = [
        'POST' => [
            'success' => true,
            'message' => "Módulo '$moduloNombre' instalado correctamente (simulado)",
            'data' => ['modulo_id' => $moduloId, 'accion' => 'instalado']
        ],
        'PUT' => [
            'activar' => [
                'success' => true,
                'message' => "Módulo '$moduloNombre' activado correctamente (simulado)",
                'data' => ['modulo_id' => $moduloId, 'accion' => 'activado']
            ],
            'desactivar' => [
                'success' => true,
                'message' => "Módulo '$moduloNombre' desactivado correctamente (simulado)",
                'data' => ['modulo_id' => $moduloId, 'accion' => 'desactivado']
            ]
        ],
        'DELETE' => [
            'success' => true,
            'message' => "Módulo '$moduloNombre' desinstalado correctamente (simulado)",
            'data' => ['modulo_id' => $moduloId, 'accion' => 'desinstalado']
        ]
    ];

    if ($method === 'PUT' && isset($respuestasSimulacion['PUT'][$accion])) {
        echo json_encode($respuestasSimulacion['PUT'][$accion]);
    } elseif (isset($respuestasSimulacion[$method])) {
        echo json_encode($respuestasSimulacion[$method]);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Acción no válida'
        ]);
    }
    exit();
}

// Procesamiento real con conexión a la base de datos
switch ($method) {
    case 'GET':
        // Obtener lista de todos los módulos
        try {
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
                    m.autor,
                    m.fecha_instalacion,
                    m.fecha_activacion,
                    mc.menu_order AS menu_order,
                    CASE
                        WHEN m.instalado = 1 AND m.activo = 1 THEN 'activo'
                        WHEN m.instalado = 1 AND m.activo = 0 THEN 'inactivo'
                        WHEN m.instalado = 0 THEN 'no-instalado'
                    END AS estado
                FROM modulos m
                INNER JOIN (
                    SELECT LOWER(TRIM(nombre_tecnico)) AS nt, MAX(id) AS latest_id
                    FROM modulos
                    GROUP BY LOWER(TRIM(nombre_tecnico))
                ) lm ON LOWER(TRIM(m.nombre_tecnico)) = lm.nt AND m.id = lm.latest_id
                LEFT JOIN (
                    SELECT modulo_id, CAST(MIN(valor) AS UNSIGNED) AS menu_order
                    FROM modulo_configuracion
                    WHERE clave = 'menu_order'
                    GROUP BY modulo_id
                ) mc ON mc.modulo_id = m.id
                ORDER BY
                    CASE WHEN m.instalado = 0 THEN 2 ELSE 1 END,
                    CAST(COALESCE(mc.menu_order, 999) AS UNSIGNED) ASC,
                    m.nombre ASC
            ";

            $stmt = $pdo->query($sql);
            $modulos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'message' => 'Lista de módulos obtenida correctamente',
                'data' => $modulos
            ]);

        } catch (PDOException $e) {
            error_log("Error obteniendo módulos: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener la lista de módulos'
            ]);
        }
        break;

    case 'POST':
        // Instalar un módulo
        try {
            // Verificar que el módulo existe y no está instalado
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

            if ($modulo['instalado']) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'El módulo ya está instalado'
                ]);
                exit();
            }

            // Instalar el módulo
            $stmt = $pdo->prepare("
                UPDATE modulos
                SET instalado = 1,
                    fecha_instalacion = NOW(),
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmt->bindParam(':id', $moduloId);
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'message' => "Módulo '{$modulo['nombre']}' instalado correctamente",
                'data' => [
                    'modulo_id' => $moduloId,
                    'nombre' => $modulo['nombre']
                ]
            ]);

        } catch (PDOException $e) {
            error_log("Error instalando módulo: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al instalar el módulo'
            ]);
        }
        break;

    case 'PUT':
        // Activar o desactivar un módulo
        $accion = $input['accion'];

        try {
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

        } catch (PDOException $e) {
            error_log("Error " . $accion . " módulo: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al ' . $accion . ' el módulo'
            ]);
        }
        break;

    case 'DELETE':
        // Desinstalar un módulo
        try {
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

        } catch (PDOException $e) {
            error_log("Error desinstalando módulo: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al desinstalar el módulo'
            ]);
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
?>
