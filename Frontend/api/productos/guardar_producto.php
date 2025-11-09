<?php
// Crear/Actualizar producto
require_once '../config.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit();
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Cuerpo JSON inválido']);
        exit();
    }

    // Validaciones mínimas
    foreach (['codigo', 'nombre'] as $campo) {
        if (!isset($input[$campo]) || trim($input[$campo]) === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Falta el campo obligatorio: $campo"]);
            exit();
        }
    }

    $pdo = getConnection();
    if (!$pdo) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
        exit();
    }

    // Campos permitidos
    $campos = [
        'codigo','codigo_barras','nombre','descripcion','categoria_id','tipo_producto','unidad_medida',
        'precio_coste','precio_venta','margen','iva_tipo','stock_actual','stock_minimo','stock_maximo',
        'control_stock','peso','dimensiones','imagen','activo','es_venta_online','requiere_receta',
        'fecha_caducidad_control','tags','observaciones','proveedor_principal_id'
    ];

    $data = [];
    foreach ($campos as $c) {
        if (array_key_exists($c, $input)) {
            $data[$c] = $input[$c];
        }
    }

    // Normalizar tipos numéricos básicos
    $enteros = ['categoria_id','stock_actual','stock_minimo','stock_maximo','control_stock','activo','es_venta_online','requiere_receta','fecha_caducidad_control','proveedor_principal_id'];
    foreach ($enteros as $e) { if (isset($data[$e])) $data[$e] = (int)$data[$e]; }
    $decimales = ['precio_coste','precio_venta','margen'];
    foreach ($decimales as $d) { if (isset($data[$d])) $data[$d] = (float)$data[$d]; }

    if (isset($input['id']) && $input['id']) {
        // UPDATE
        $id = (int)$input['id'];
        $sets = [];
        foreach ($data as $k => $v) { $sets[] = "$k = :$k"; }
        $sql = "UPDATE productos SET " . implode(', ', $sets) . ", updated_at = NOW() WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        foreach ($data as $k => $v) { $stmt->bindValue(":".$k, $v); }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Devolver el producto actualizado
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'producto' => $producto]);
        exit();
    } else {
        // INSERT
        $cols = array_keys($data);
        $placeholders = array_map(fn($c) => ":$c", $cols);
        $sql = "INSERT INTO productos (" . implode(',', $cols) . ", created_at, updated_at) VALUES (" . implode(',', $placeholders) . ", NOW(), NOW())";
        $stmt = $pdo->prepare($sql);
        foreach ($data as $k => $v) { $stmt->bindValue(":".$k, $v); }
        $stmt->execute();
        $id = (int)$pdo->lastInsertId();

        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'producto' => $producto]);
        exit();
    }
} catch (PDOException $e) {
    error_log('guardar_producto PDO error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de base de datos']);
} catch (Throwable $e) {
    error_log('guardar_producto error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}
?>