<?php
// Listar productos
require_once '../config.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit();
    }

    $pdo = getConnection();
    if (!$pdo) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
        exit();
    }

    // Traer productos (se paginan/filtran en cliente)
    $stmt = $pdo->query("SELECT id, codigo, codigo_barras, nombre, descripcion, categoria_id, tipo_producto, unidad_medida, precio_coste, precio_venta, margen, iva_tipo, stock_actual, stock_minimo, stock_maximo, control_stock, peso, dimensiones, imagen, activo, es_venta_online, requiere_receta, fecha_caducidad_control, tags, observaciones, proveedor_principal_id, created_by, created_at, updated_at FROM productos ORDER BY updated_at DESC, id DESC");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'total' => count($productos)
    ]);
} catch (Throwable $e) {
    error_log('obtener_productos error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}
?>