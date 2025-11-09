<?php
require_once __DIR__ . '/../config.php';

try {
    $db = getConnection();
    if (!$db) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "No se pudo conectar a la base de datos."]);
        exit;
    }

    // Obtener parámetros de paginación y filtrado
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 10;
    $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
    $tipo_filtro = isset($_GET['tipo']) ? $_GET['tipo'] : '';
    $estado_filtro = isset($_GET['estado']) ? $_GET['estado'] : '';

    $offset = ($pagina - 1) * $limite;

    // Construir consulta base
    $sql = "SELECT p.*,
                   IF(p.bloqueado = 1, 'bloqueado', IF(p.activo = 0, 'inactivo', 'activo')) as estado
            FROM proveedores p WHERE 1=1";

    $params = [];

    // Aplicar filtros
    if (!empty($busqueda)) {
        $sql .= " AND (p.codigo LIKE ? OR p.nombre_comercial LIKE ? OR p.razon_social LIKE ? OR p.nif_cif LIKE ?)";
        $busqueda_param = "%$busqueda%";
        $params = array_merge($params, [$busqueda_param, $busqueda_param, $busqueda_param, $busqueda_param]);
    }

    if (!empty($tipo_filtro)) {
        $sql .= " AND p.tipo_proveedor = ?";
        $params[] = $tipo_filtro;
    }

    if (!empty($estado_filtro)) {
        if ($estado_filtro === 'activo') {
            $sql .= " AND p.activo = 1 AND p.bloqueado = 0";
        } elseif ($estado_filtro === 'inactivo') {
            $sql .= " AND p.activo = 0";
        } elseif ($estado_filtro === 'bloqueado') {
            $sql .= " AND p.bloqueado = 1";
        }
    }

    // Contar total de registros
    $count_sql = "SELECT COUNT(*) as total FROM proveedores p WHERE 1=1";

    // Reaplicar filtros al conteo
    if (!empty($busqueda)) {
        $count_sql .= " AND (p.codigo LIKE ? OR p.nombre_comercial LIKE ? OR p.razon_social LIKE ? OR p.nif_cif LIKE ?)";
    }

    if (!empty($tipo_filtro)) {
        $count_sql .= " AND p.tipo_proveedor = ?";
    }

    if (!empty($estado_filtro)) {
        if ($estado_filtro === 'activo') {
            $count_sql .= " AND p.activo = 1 AND p.bloqueado = 0";
        } elseif ($estado_filtro === 'inactivo') {
            $count_sql .= " AND p.activo = 0";
        } elseif ($estado_filtro === 'bloqueado') {
            $count_sql .= " AND p.bloqueado = 1";
        }
    }

    $count_stmt = $db->prepare($count_sql);
    $count_stmt->execute($params);
    $total = $count_stmt->fetchColumn();

    // Obtener datos paginados
    $sql .= " ORDER BY p.nombre_comercial ASC LIMIT " . (int)$limite . " OFFSET " . (int)$offset;

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcular información de paginación
    $total_paginas = ceil($total / $limite);
    $desde = $offset + 1;
    $hasta = min($offset + $limite, $total);

    echo json_encode([
        "ok" => true,
        "proveedores" => $proveedores,
        "paginacion" => [
            "pagina_actual" => $pagina,
            "total_paginas" => $total_paginas,
            "total_registros" => $total,
            "registros_por_pagina" => $limite,
            "desde" => $total > 0 ? $desde : 0,
            "hasta" => $hasta
        ]
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
