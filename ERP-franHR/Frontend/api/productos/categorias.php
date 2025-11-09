<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar solicitud OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $db = getConnection();
    if (!$db) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "No se pudo conectar a la base de datos."]);
        exit;
    }

    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true);

    switch ($method) {
        case 'GET':
            obtenerCategorias($db);
            break;
        case 'POST':
            crearCategoria($db, $input);
            break;
        case 'PUT':
            actualizarCategoria($db, $input);
            break;
        case 'DELETE':
            eliminarCategoria($db, $_GET['id'] ?? null);
            break;
        default:
            http_response_code(405);
            echo json_encode(["ok" => false, "error" => "Método no permitido"]);
            break;
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}

function obtenerCategorias($db) {
    try {
        $sql = "SELECT
                    id,
                    nombre,
                    descripcion,
                    categoria_padre_id,
                    imagen,
                    activo,
                    created_at,
                    updated_at
                FROM productos_categorias
                ORDER BY nombre";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convertir valores numéricos y booleanos
        foreach ($categorias as &$categoria) {
            $categoria['activo'] = (bool)$categoria['activo'];
        }

        echo json_encode(["ok" => true, "categorias" => $categorias]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "Error al obtener categorías: " . $e->getMessage()]);
    }
}

function crearCategoria($db, $data) {
    try {
        // Validar datos requeridos
        if (empty($data['nombre'])) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "El nombre de la categoría es obligatorio"]);
            return;
        }

        // Verificar que no exista una categoría con el mismo nombre
        $stmt = $db->prepare("SELECT id FROM productos_categorias WHERE nombre = ?");
        $stmt->execute([$data['nombre']]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "Ya existe una categoría con ese nombre"]);
            return;
        }

        // Insertar nueva categoría
        $sql = "INSERT INTO productos_categorias (
                    nombre,
                    descripcion,
                    categoria_padre_id,
                    imagen,
                    activo,
                    created_at,
                    updated_at
                ) VALUES (?, ?, ?, ?, ?, NOW(), NOW())";

        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            $data['nombre'],
            $data['descripcion'] ?? null,
            !empty($data['categoria_padre_id']) ? $data['categoria_padre_id'] : null,
            $data['imagen'] ?? null,
            isset($data['activo']) ? ($data['activo'] ? 1 : 0) : 1
        ]);

        if ($result) {
            $id = $db->lastInsertId();
            echo json_encode([
                "ok" => true,
                "message" => "Categoría creada correctamente",
                "id" => $id
            ]);
        } else {
            throw new Exception("Error al crear la categoría");
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "Error al crear categoría: " . $e->getMessage()]);
    }
}

function actualizarCategoria($db, $data) {
    try {
        if (empty($data['id']) || empty($data['nombre'])) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "ID y nombre son obligatorios"]);
            return;
        }

        // Verificar que la categoría exista
        $stmt = $db->prepare("SELECT id FROM productos_categorias WHERE id = ?");
        $stmt->execute([$data['id']]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(["ok" => false, "error" => "La categoría no existe"]);
            return;
        }

        // Verificar que no exista otra categoría con el mismo nombre
        $stmt = $db->prepare("SELECT id FROM productos_categorias WHERE nombre = ? AND id != ?");
        $stmt->execute([$data['nombre'], $data['id']]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "Ya existe otra categoría con ese nombre"]);
            return;
        }

        // Actualizar categoría
        $sql = "UPDATE productos_categorias SET
                    nombre = ?,
                    descripcion = ?,
                    categoria_padre_id = ?,
                    imagen = ?,
                    activo = ?,
                    updated_at = NOW()
                WHERE id = ?";

        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            $data['nombre'],
            $data['descripcion'] ?? null,
            !empty($data['categoria_padre_id']) ? $data['categoria_padre_id'] : null,
            $data['imagen'] ?? null,
            isset($data['activo']) ? ($data['activo'] ? 1 : 0) : 1,
            $data['id']
        ]);

        if ($result) {
            echo json_encode(["ok" => true, "message" => "Categoría actualizada correctamente"]);
        } else {
            throw new Exception("Error al actualizar la categoría");
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "Error al actualizar categoría: " . $e->getMessage()]);
    }
}

function eliminarCategoria($db, $id) {
    try {
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(["ok" => false, "error" => "ID de categoría es obligatorio"]);
            return;
        }

        // Verificar que la categoría exista
        $stmt = $db->prepare("SELECT id, nombre FROM productos_categorias WHERE id = ?");
        $stmt->execute([$id]);
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$categoria) {
            http_response_code(404);
            echo json_encode(["ok" => false, "error" => "La categoría no existe"]);
            return;
        }

        // Verificar si hay categorías hijas
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM productos_categorias WHERE categoria_padre_id = ?");
        $stmt->execute([$id]);
        $hijosCount = $stmt->fetch()['count'];

        if ($hijosCount > 0) {
            http_response_code(400);
            echo json_encode([
                "ok" => false,
                "error" => "No se puede eliminar la categoría porque tiene categorías hijas asociadas"
            ]);
            return;
        }

        // Verificar si hay productos asociados
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM productos WHERE categoria_id = ?");
        $stmt->execute([$id]);
        $productosCount = $stmt->fetch()['count'];

        // Iniciar transacción
        $db->beginTransaction();

        try {
            if ($productosCount > 0) {
                // Actualizar productos para que queden sin categoría
                $stmt = $db->prepare("UPDATE productos SET categoria_id = NULL WHERE categoria_id = ?");
                $stmt->execute([$id]);
            }

            // Eliminar la categoría
            $stmt = $db->prepare("DELETE FROM productos_categorias WHERE id = ?");
            $result = $stmt->execute([$id]);

            if ($result) {
                $db->commit();
                $message = $productosCount > 0
                    ? "Categoría eliminada correctamente. {$productosCount} productos quedaron sin categoría."
                    : "Categoría eliminada correctamente";

                echo json_encode([
                    "ok" => true,
                    "message" => $message,
                    "productos_actualizados" => $productosCount
                ]);
            } else {
                throw new Exception("Error al eliminar la categoría");
            }
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "Error al eliminar categoría: " . $e->getMessage()]);
    }
}
