<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_rol']) && (isset($_SESSION['username']) && $_SESSION['username'] === 'admin')) {
    $_SESSION['user_rol'] = 'admin';
}

if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Acceso denegado. Se requieren permisos de administrador.'
    ]);
    exit();
}

try {
    $pdo = getConnection();
    if (!$pdo) {
        throw new Exception('No se pudo establecer la conexión con la base de datos');
    }

    $permisosDisponibles = ['ver', 'crear', 'editar', 'eliminar', 'listar', 'configurar'];

    $estadisticasStmt = $pdo->query("SELECT 
            COUNT(*) AS total_modulos,
            SUM(CASE WHEN instalado = 1 THEN 1 ELSE 0 END) AS modulos_instalados,
            SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) AS modulos_activos
        FROM modulos");
    $estadisticas = $estadisticasStmt->fetch(PDO::FETCH_ASSOC) ?: [
        'total_modulos' => 0,
        'modulos_instalados' => 0,
        'modulos_activos' => 0
    ];

    $modulosStmt = $pdo->query("SELECT 
            m.id,
            m.nombre,
            m.nombre_tecnico,
            m.descripcion,
            m.icono,
            m.categoria,
            m.instalado,
            m.activo,
            m.version,
            CAST(COALESCE(mc.valor, '999') AS UNSIGNED) AS menu_order
        FROM modulos m
        LEFT JOIN (
            SELECT modulo_id, MIN(valor) AS valor
            FROM modulo_configuracion
            WHERE clave = 'menu_order'
            GROUP BY modulo_id
        ) mc ON mc.modulo_id = m.id
        ORDER BY menu_order ASC, m.nombre ASC");

    $modulos = [];
    while ($row = $modulosStmt->fetch(PDO::FETCH_ASSOC)) {
        $row['instalado'] = (bool) $row['instalado'];
        $row['activo'] = (bool) $row['activo'];
        $row['configuraciones'] = [];
        $row['permisos'] = [];
        $modulos[(int) $row['id']] = $row;
    }

    if ($modulos) {
        $configStmt = $pdo->query("SELECT 
                mc.id,
                mc.modulo_id,
                mc.clave,
                mc.valor,
                mc.tipo,
                mc.descripcion,
                mc.editable,
                mc.updated_at
            FROM modulo_configuracion mc
            ORDER BY mc.modulo_id ASC, mc.clave ASC");

        while ($config = $configStmt->fetch(PDO::FETCH_ASSOC)) {
            $moduloId = (int) $config['modulo_id'];
            if (!isset($modulos[$moduloId])) {
                continue;
            }
            $config['editable'] = (bool) $config['editable'];
            $modulos[$moduloId]['configuraciones'][] = $config;
        }

        $permisosStmt = $pdo->query("SELECT modulo_id, rol, permiso, concedido FROM modulo_permisos");
        while ($permiso = $permisosStmt->fetch(PDO::FETCH_ASSOC)) {
            $moduloId = (int) $permiso['modulo_id'];
            if (!isset($modulos[$moduloId])) {
                continue;
            }
            $rol = $permiso['rol'] ?: 'desconocido';
            if (!isset($modulos[$moduloId]['permisos'][$rol])) {
                $modulos[$moduloId]['permisos'][$rol] = [];
            }
            $modulos[$moduloId]['permisos'][$rol][$permiso['permiso']] = (bool) $permiso['concedido'];
        }
    }

    $rolesStmt = $pdo->query("SELECT DISTINCT rol FROM (
            SELECT rol FROM usuarios
            UNION
            SELECT rol FROM modulo_permisos
        ) roles
        WHERE rol IS NOT NULL AND rol <> ''
        ORDER BY rol ASC");
    $roles = $rolesStmt->fetchAll(PDO::FETCH_COLUMN);
    if (!$roles) {
        $roles = ['admin'];
    }

    foreach ($modulos as &$modulo) {
        foreach ($roles as $rol) {
            if (!isset($modulo['permisos'][$rol])) {
                $modulo['permisos'][$rol] = [];
            }
            foreach ($permisosDisponibles as $permiso) {
                if (!isset($modulo['permisos'][$rol][$permiso])) {
                    $modulo['permisos'][$rol][$permiso] = false;
                } else {
                    $modulo['permisos'][$rol][$permiso] = (bool) $modulo['permisos'][$rol][$permiso];
                }
            }
        }
    }
    unset($modulo);

    echo json_encode([
        'success' => true,
        'data' => [
            'estadisticas' => $estadisticas,
            'roles' => $roles,
            'permisos_disponibles' => $permisosDisponibles,
            'modulos' => array_values($modulos)
        ]
    ]);
} catch (Throwable $e) {
    error_log('[CONFIGURACION][GET] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener la configuración del sistema'
    ]);
}
