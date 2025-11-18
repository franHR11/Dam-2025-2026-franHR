<?php
require_once __DIR__ . '/facturacion_utils.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
        exit;
    }

    $periodo = $_GET['periodo'] ?? 'mes'; // 'mes', 'trimestre', 'año'
    $ejercicio = isset($_GET['ejercicio']) ? (int) $_GET['ejercicio'] : date('Y');
    $tipo = $_GET['tipo'] ?? 'venta'; // 'venta', 'compra', 'todos'

    $db = facturacion_db();

    // Estadísticas generales
    $stats = [
        'periodo' => $periodo,
        'ejercicio' => $ejercicio,
        'tipo' => $tipo,
        'generadas' => 0,
        'pendientes' => 0,
        'pagadas' => 0,
        'vencidas' => 0,
        'canceladas' => 0,
        'total_facturado' => 0,
        'total_pendiente' => 0,
        'promedio_factura' => 0,
        'evolucion_mensual' => [],
        'top_clientes' => [],
        'top_productos' => [],
        'facturas_por_estado' => [],
        'facturas_por_tipo' => [],
        'ingresos_mensuales' => []
    ];

    // Construir WHERE según filtros
    $where = ["ejercicio = ?"];
    $params = [$ejercicio];

    if ($tipo !== 'todos') {
        $where[] = "tipo_factura = ?";
        $params[] = $tipo;
    }

    $whereClause = implode(' AND ', $where);

    // Estadísticas por estado
    $sql = "SELECT estado, COUNT(*) as cantidad, SUM(total) as importe 
            FROM facturas WHERE $whereClause 
            GROUP BY estado";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $estados = $stmt->fetchAll();

    foreach ($estados as $estado) {
        $stats['facturas_por_estado'][] = [
            'estado' => $estado['estado'],
            'cantidad' => (int) $estado['cantidad'],
            'importe' => (float) $estado['importe']
        ];

        switch ($estado['estado']) {
            case 'pendiente':
                $stats['pendientes'] = (int) $estado['cantidad'];
                $stats['total_pendiente'] = (float) $estado['importe'];
                break;
            case 'pagada':
            case 'cobrada':
                $stats['pagadas'] = (int) $estado['cantidad'];
                break;
            case 'vencida':
                $stats['vencidas'] = (int) $estado['cantidad'];
                break;
            case 'cancelada':
                $stats['canceladas'] = (int) $estado['cantidad'];
                break;
        }
    }

    // Total facturas y promedio
    $sql = "SELECT COUNT(*) as total, SUM(total) as suma, AVG(total) as promedio 
            FROM facturas WHERE $whereClause";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $totales = $stmt->fetch();

    $stats['generadas'] = (int) $totales['total'];
    $stats['total_facturado'] = (float) $totales['suma'];
    $stats['promedio_factura'] = (float) $totales['promedio'];

    // Evolución mensual
    $sql = "SELECT DATE_FORMAT(fecha, '%Y-%m') as mes, 
                   COUNT(*) as cantidad, 
                   SUM(total) as importe
            FROM facturas 
            WHERE $whereClause AND fecha >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(fecha, '%Y-%m') 
            ORDER BY mes ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $evolucion = $stmt->fetchAll();

    foreach ($evolucion as $mes) {
        $stats['evolucion_mensual'][] = [
            'mes' => $mes['mes'],
            'cantidad' => (int) $mes['cantidad'],
            'importe' => (float) $mes['importe']
        ];
    }

    // Top clientes (solo para facturas de venta)
    if ($tipo === 'venta' || $tipo === 'todos') {
        $sql = "SELECT c.nombre_comercial, 
                       COUNT(f.id) as cantidad_facturas, 
                       SUM(f.total) as total_facturado
                FROM facturas f
                INNER JOIN clientes c ON c.id = f.cliente_id
                WHERE f.ejercicio = ? AND f.tipo_factura = 'venta'
                GROUP BY c.id, c.nombre_comercial
                ORDER BY total_facturado DESC
                LIMIT 10";
        $stmt = $db->prepare($sql);
        $stmt->execute([$ejercicio]);
        $clientes = $stmt->fetchAll();

        foreach ($clientes as $cliente) {
            $stats['top_clientes'][] = [
                'nombre' => $cliente['nombre_comercial'],
                'cantidad_facturas' => (int) $cliente['cantidad_facturas'],
                'total_facturado' => (float) $cliente['total_facturado']
            ];
        }
    }

    // Top productos
    $sql = "SELECT p.nombre, 
                   COUNT(fl.id) as veces_facturado, 
                   SUM(fl.cantidad) as total_unidades,
                   SUM(fl.total_linea) as total_importe
            FROM facturas_lineas fl
            INNER JOIN facturas f ON f.id = fl.factura_id
            INNER JOIN productos p ON p.id = fl.producto_id
            WHERE f.ejercicio = ?" . ($tipo !== 'todos' ? " AND f.tipo_factura = ?" : "") . "
            GROUP BY p.id, p.nombre
            HAVING p.id IS NOT NULL
            ORDER BY total_importe DESC
            LIMIT 10";

    $stmt = $db->prepare($sql);
    if ($tipo !== 'todos') {
        $stmt->execute([$ejercicio, $tipo]);
    } else {
        $stmt->execute([$ejercicio]);
    }
    $productos = $stmt->fetchAll();

    foreach ($productos as $producto) {
        $stats['top_productos'][] = [
            'nombre' => $producto['nombre'],
            'veces_facturado' => (int) $producto['veces_facturado'],
            'total_unidades' => (float) $producto['total_unidades'],
            'total_importe' => (float) $producto['total_importe']
        ];
    }

    // Facturas por tipo
    if ($tipo === 'todos') {
        $sql = "SELECT tipo_factura, COUNT(*) as cantidad, SUM(total) as importe
                FROM facturas WHERE ejercicio = ?
                GROUP BY tipo_factura";
        $stmt = $db->prepare($sql);
        $stmt->execute([$ejercicio]);
        $tipos = $stmt->fetchAll();

        foreach ($tipos as $tipoFactura) {
            $stats['facturas_por_tipo'][] = [
                'tipo' => $tipoFactura['tipo_factura'],
                'cantidad' => (int) $tipoFactura['cantidad'],
                'importe' => (float) $tipoFactura['importe']
            ];
        }
    }

    // Ingresos mensuales del año
    $sql = "SELECT DATE_FORMAT(fecha, '%m') as mes, 
                   SUM(total) as ingresos
            FROM facturas 
            WHERE ejercicio = ? AND tipo_factura = 'venta'
            GROUP BY DATE_FORMAT(fecha, '%m')
            ORDER BY mes";
    $stmt = $db->prepare($sql);
    $stmt->execute([$ejercicio]);
    $ingresos = $stmt->fetchAll();

    foreach ($ingresos as $ingreso) {
        $stats['ingresos_mensuales'][] = [
            'mes' => (int) $ingreso['mes'],
            'ingresos' => (float) $ingreso['ingresos']
        ];
    }

    echo json_encode([
        'ok' => true,
        'estadisticas' => $stats,
        'generado_en' => date('Y-m-d H:i:s')
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}