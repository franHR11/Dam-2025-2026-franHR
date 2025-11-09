<?php
// Configuración de headers CORS
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// Manejar peticiones OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir la configuración de la base de datos
require_once '../config.php';

// Verificar que la petición sea GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit();
}

// Obtener el ID del usuario desde la sesión o parámetro
session_start();
$userId = null;
$userRol = 'usuario'; // Rol por defecto

// Verificar si hay sesión activa
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    // Obtener rol del usuario
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT rol FROM usuarios WHERE Identificador = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $userRol = $user['rol'];
        }
    } catch (PDOException $e) {
        error_log("Error obteniendo rol de usuario: " . $e->getMessage());
    }
}

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

    // Consulta para obtener módulos activos con permisos del usuario (sin duplicados)
    $sql = "
        SELECT
            m.id,
            m.nombre,
            m.nombre_tecnico,
            m.descripcion,
            m.icono,
            m.categoria,
            m.version,
            mc.menu_order as menu_order,
            CASE
                WHEN :userRol = 'admin' THEN 1
                ELSE MAX(COALESCE(mp.concedido, 0))
            END as tiene_permiso,
            GROUP_CONCAT(
                DISTINCT mp.permiso
                ORDER BY mp.permiso
                SEPARATOR ','
            ) as permisos_usuario
        FROM modulos m
        INNER JOIN (
            SELECT LOWER(TRIM(nombre_tecnico)) AS nt, MAX(id) AS latest_id
            FROM modulos
            GROUP BY LOWER(TRIM(nombre_tecnico))
        ) lm ON LOWER(TRIM(m.nombre_tecnico)) = lm.nt AND m.id = lm.latest_id
        LEFT JOIN (
            SELECT 
                modulo_id,
                CAST(MIN(valor) AS UNSIGNED) AS menu_order
            FROM modulo_configuracion
            WHERE clave = 'menu_order'
            GROUP BY modulo_id
        ) mc ON m.id = mc.modulo_id
        LEFT JOIN modulo_permisos mp ON m.id = mp.modulo_id AND mp.rol = :userRol
        WHERE m.instalado = 1 AND m.activo = 1
        GROUP BY m.id, m.nombre, m.nombre_tecnico, m.descripcion, m.icono, m.categoria, m.version, mc.menu_order
        ORDER BY CAST(COALESCE(mc.menu_order, 999) AS UNSIGNED) ASC, m.nombre ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userRol', $userRol);
    $stmt->execute();

    $modulos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organizar módulos por categorías
    $modulosPorCategoria = [];
    $modulosConPermisos = [];

    foreach ($modulos as $modulo) {
        // Solo incluir módulos que el usuario puede ver
        if ($modulo['tiene_permiso']) {
            // Procesar permisos
            $permisosArray = $modulo['permisos_usuario'] ? explode(',', $modulo['permisos_usuario']) : [];

            $moduloData = [
                'id' => $modulo['id'],
                'nombre' => $modulo['nombre'],
                'nombre_tecnico' => $modulo['nombre_tecnico'],
                'descripcion' => $modulo['descripcion'],
                'icono' => $modulo['icono'],
                'categoria' => $modulo['categoria'],
                'version' => $modulo['version'],
                'menu_order' => $modulo['menu_order'],
                'permisos' => [
                    'ver' => in_array('ver', $permisosArray) || $userRol === 'admin',
                    'crear' => in_array('crear', $permisosArray) || $userRol === 'admin',
                    'editar' => in_array('editar', $permisosArray) || $userRol === 'admin',
                    'eliminar' => in_array('eliminar', $permisosArray) || $userRol === 'admin',
                    'listar' => in_array('listar', $permisosArray) || $userRol === 'admin',
                    'configurar' => in_array('configurar', $permisosArray) || $userRol === 'admin'
                ]
            ];

            $modulosConPermisos[] = $moduloData;

            // Agrupar por categoría para el menú
            $categoria = $modulo['categoria'];
            if (!isset($modulosPorCategoria[$categoria])) {
                $modulosPorCategoria[$categoria] = [
                    'nombre' => ucfirst($categoria),
                    'modulos' => []
                ];
            }
            $modulosPorCategoria[$categoria]['modulos'][] = $moduloData;
        }
    }

    // Respuesta exitosa
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Módulos obtenidos correctamente',
        'data' => [
            'modulos' => $modulosConPermisos,
            'modulos_por_categoria' => array_values($modulosPorCategoria),
            'usuario' => [
                'id' => $userId,
                'rol' => $userRol
            ]
        ]
    ]);
    exit();

} catch (PDOException $e) {
    error_log("Error en la base de datos al obtener módulos: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor (BD)'
    ]);
    exit();
} catch (Exception $e) {
    error_log("Error general al obtener módulos: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error inesperado en el servidor'
    ]);
    exit();
}
?>
