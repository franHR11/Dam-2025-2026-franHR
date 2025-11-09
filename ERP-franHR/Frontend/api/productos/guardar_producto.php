<?php
require_once __DIR__ . '/../config.php';

function jsonInput() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function getInputData() {
    // Detectar si es FormData (con archivos) o JSON
    if (!empty($_FILES) || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false)) {
        // Es FormData - usar $_POST directamente
        return $_POST;
    } else {
        // Es JSON
        return jsonInput();
    }
}

function procesarSubidaImagen($file) {
    // Directorio de uploads (relativo al raíz del frontend)
    $uploadDir = __DIR__ . '/../../uploads/productos/';

    // Crear directorio si no existe
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return ['success' => false, 'error' => 'No se pudo crear el directorio de uploads'];
        }
    }

    // Validar archivo
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'error' => 'Tipo de archivo no permitido. Use JPG, PNG, GIF o WebP'];
    }

    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'El archivo es demasiado grande. Máximo 5MB'];
    }

    // Generar nombre único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nombreUnico = uniqid('producto_', true) . '.' . $extension;
    $rutaCompleta = $uploadDir . $nombreUnico;

    // Mover archivo
    if (move_uploaded_file($file['tmp_name'], $rutaCompleta)) {
        // URL relativa para acceso web
        $url = '/uploads/productos/' . $nombreUnico;
        return [
            'success' => true,
            'url' => $url,
            'nombre' => $nombreUnico,
            'ruta_completa' => $rutaCompleta
        ];
    } else {
        return ['success' => false, 'error' => 'Error al mover el archivo subido'];
    }
}

function b($val) {
    return isset($val) && ($val === true || $val === 1 || $val === '1' || $val === 'on');
}

function n($val) {
    return ($val === '' || $val === null) ? null : floatval($val);
}

function sanitizeTipoProducto($val) {
    $map = [
        'producto' => 'producto',
        'producto_simple' => 'producto',
        'servicio' => 'servicio',
        'producto_servicio' => 'servicio',
        'consumible' => 'consumible',
        'material' => 'material',
        'kit' => 'kit',
        'producto_compuesto' => 'kit',
        'producto_pack' => 'kit',
        'digital' => 'digital'
    ];
    $val = strtolower(trim((string)$val));
    return $map[$val] ?? 'producto';
}

function sanitizeUnidadMedida($val) {
    $allowed = ['unidades','kg','litros','metros','metros2','metros3','cajas','palets'];
    $map = [
        'ud' => 'unidades',
        'unidad' => 'unidades',
        'unidades' => 'unidades',
        'kg' => 'kg',
        'kilo' => 'kg',
        'kilos' => 'kg',
        'litro' => 'litros',
        'l' => 'litros',
        'lt' => 'litros',
        'metros' => 'metros',
        'm' => 'metros',
        'm2' => 'metros2',
        'metros2' => 'metros2',
        'm3' => 'metros3',
        'metros3' => 'metros3',
        'caja' => 'cajas',
        'cajas' => 'cajas',
        'palet' => 'palets',
        'palets' => 'palets'
    ];
    $val = strtolower(trim((string)$val));
    $normalized = $map[$val] ?? $val;
    return in_array($normalized, $allowed, true) ? $normalized : 'unidades';
}

function sanitizeIvaTipo($val) {
    $allowed = ['21','10','4','0','exento'];
    if ($val === null || $val === '') return '21';
    $s = strtolower(trim((string)$val));
    // Si es numérico con decimales (p.e. 21.00), normalizar
    if (is_numeric($s)) {
        $s = (string)(int)round(floatval($s));
    }
    return in_array($s, $allowed, true) ? $s : '21';
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(["success" => false, "error" => "Método no permitido"]);
        exit;
    }

    $db = getConnection();
    if (!$db) {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "No se pudo conectar a la base de datos"]);
        exit;
    }

    // Obtener datos
    $p = getInputData();

    // Procesar imagen si existe
    $imagenFinal = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagenResultado = procesarSubidaImagen($_FILES['imagen']);
        if ($imagenResultado['success']) {
            $imagenFinal = $imagenResultado['nombre'];
        } else {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => $imagenResultado['error']]);
            exit;
        }
    }

    // Validaciones mínimas
    if (empty($p['codigo']) || empty($p['nombre']) || !isset($p['precio_venta']) || !isset($p['stock_actual'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Faltan campos obligatorios (codigo, nombre, precio_venta, stock_actual)"]);
        exit;
    }

    $id = isset($p['id']) && $p['id'] !== '' ? intval($p['id']) : null;

    // Preparar parámetros
    $params = [
        'codigo' => $p['codigo'],
        'codigo_barras' => $p['codigo_barras'] ?? null,
        'nombre' => $p['nombre'],
        'descripcion' => $p['descripcion'] ?? null,
        'categoria_id' => isset($p['categoria_id']) && $p['categoria_id'] !== '' ? intval($p['categoria_id']) : null,
        'tipo_producto' => sanitizeTipoProducto($p['tipo_producto'] ?? null),
        'unidad_medida' => sanitizeUnidadMedida($p['unidad_medida'] ?? null),
        'iva_tipo' => sanitizeIvaTipo($p['iva_tipo'] ?? null),
        'precio_coste' => n($p['precio_coste'] ?? null),
        'margen' => n($p['margen'] ?? null),
        'precio_venta' => n($p['precio_venta']),
        'stock_actual' => n($p['stock_actual']),
        'stock_minimo' => n($p['stock_minimo'] ?? null),
        'stock_maximo' => n($p['stock_maximo'] ?? null),
        'activo' => b($p['activo'] ?? null) ? 1 : 0,
        'es_venta_online' => b($p['es_venta_online'] ?? null) ? 1 : 0,
        'control_stock' => b($p['control_stock'] ?? null) ? 1 : 0,
        'requiere_receta' => b($p['requiere_receta'] ?? null) ? 1 : 0,
        'fecha_caducidad_control' => b($p['fecha_caducidad_control'] ?? null) ? 1 : 0,
        'tags' => $p['tags'] ?? null,
        'observaciones' => $p['observaciones'] ?? null,
        'proveedor_principal_id' => isset($p['proveedor_principal_id']) && $p['proveedor_principal_id'] !== '' ? intval($p['proveedor_principal_id']) : null,
        'imagen' => $imagenFinal, // Usar el nombre del archivo subido o null
    ];

    try {
        // Validación de código duplicado
        if ($id) {
            $stmt = $db->prepare("SELECT id FROM productos WHERE codigo = :codigo AND id <> :id LIMIT 1");
            $stmt->execute(['codigo' => $params['codigo'], 'id' => $id]);
            if ($stmt->fetch()) {
                http_response_code(409);
                echo json_encode(["success" => false, "error" => "El código de producto ya existe."]);
                exit;
            }
        } else {
            $stmt = $db->prepare("SELECT id FROM productos WHERE codigo = :codigo LIMIT 1");
            $stmt->execute(['codigo' => $params['codigo']]);
            if ($stmt->fetch()) {
                http_response_code(409);
                echo json_encode(["success" => false, "error" => "El código de producto ya existe."]);
                exit;
            }
        }

        if ($id) {
            // Update
            $sql = "UPDATE productos SET
                        codigo = :codigo,
                        codigo_barras = :codigo_barras,
                        nombre = :nombre,
                        descripcion = :descripcion,
                        categoria_id = :categoria_id,
                        tipo_producto = :tipo_producto,
                        unidad_medida = :unidad_medida,
                        iva_tipo = :iva_tipo,
                        precio_coste = :precio_coste,
                        margen = :margen,
                        precio_venta = :precio_venta,
                        stock_actual = :stock_actual,
                        stock_minimo = :stock_minimo,
                        stock_maximo = :stock_maximo,
                        activo = :activo,
                        es_venta_online = :es_venta_online,
                        control_stock = :control_stock,
                        requiere_receta = :requiere_receta,
                        fecha_caducidad_control = :fecha_caducidad_control,
                        tags = :tags,
                        observaciones = :observaciones,
                        proveedor_principal_id = :proveedor_principal_id,
                        imagen = COALESCE(:imagen, imagen),
                        updated_at = NOW()
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $params['id'] = $id;
            $stmt->execute($params);
        } else {
            // Insert
            $sql = "INSERT INTO productos (
                        codigo, codigo_barras, nombre, descripcion, categoria_id, tipo_producto, unidad_medida,
                        iva_tipo, precio_coste, margen, precio_venta, stock_actual, stock_minimo, stock_maximo,
                        activo, es_venta_online, control_stock, requiere_receta, fecha_caducidad_control,
                        tags, observaciones, proveedor_principal_id, imagen, created_at, updated_at
                    ) VALUES (
                        :codigo, :codigo_barras, :nombre, :descripcion, :categoria_id, :tipo_producto, :unidad_medida,
                        :iva_tipo, :precio_coste, :margen, :precio_venta, :stock_actual, :stock_minimo, :stock_maximo,
                        :activo, :es_venta_online, :control_stock, :requiere_receta, :fecha_caducidad_control,
                        :tags, :observaciones, :proveedor_principal_id, :imagen, NOW(), NOW()
                    )";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $id = intval($db->lastInsertId());
        }

        // Devolver el producto guardado
        $stmt = $db->prepare("SELECT * FROM productos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $producto = $stmt->fetch();

        // Agregar URL de imagen si existe
        if ($producto && $producto['imagen']) {
            $producto['imagen_url'] = '/uploads/productos/' . $producto['imagen'];
        }

        echo json_encode(["success" => true, "producto" => $producto]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Error de base de datos: " . $e->getMessage()]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
