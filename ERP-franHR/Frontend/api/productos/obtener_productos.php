<?php
require_once __DIR__ . '/../config.php';

try {
    $db = getConnection();
    if (!$db) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "No se pudo conectar a la base de datos."]);
        exit;
    }

    // Traer todos los productos. El filtrado y paginado se hace en el frontend.
    $sql = "SELECT 
                id,
                codigo,
                codigo_barras,
                nombre,
                descripcion,
                categoria_id,
                tipo_producto,
                unidad_medida,
                iva_tipo,
                precio_coste,
                margen,
                precio_venta,
                stock_actual,
                stock_minimo,
                stock_maximo,
                activo,
                es_venta_online,
                control_stock,
                requiere_receta,
                fecha_caducidad_control,
                tags,
                observaciones,
                proveedor_principal_id,
                imagen AS imagen,
                CASE WHEN imagen IS NULL OR imagen = '' THEN NULL ELSE CONCAT('/uploads/productos/', imagen) END AS imagen_url,
                created_at,
                updated_at
            FROM productos";

    $stmt = $db->query($sql);
    $productos = $stmt->fetchAll();

    echo json_encode(["ok" => true, "productos" => $productos, "total" => count($productos)]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}