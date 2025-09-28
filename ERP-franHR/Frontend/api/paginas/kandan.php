<?php
// Cargar variables de entorno desde .env
if (file_exists(__DIR__ . '/../../../.env')) {
    $env = parse_ini_file(__DIR__ . '/../../../.env');
    foreach ($env as $key => $value) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

// Incluir la configuración de la base de datos
require_once '../config.php';

// Configuración de headers para JSON
header('Content-Type: application/json; charset=utf-8');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Obtener la ruta solicitada
$ruta = $_GET['ruta'] ?? '';

try {
    // Obtener conexión a la base de datos
    $pdo = getConnection();
    if (!$pdo) {
        throw new Exception('No se pudo conectar a la base de datos');
    }

    switch ($ruta) {
        case 'usuarios':
            handleUsuarios($pdo);
            break;
        
        case 'tablero':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $id = $_GET['id'] ?? null;
                if ($id) {
                    getTablero($pdo, $id);
                } else {
                    getTableros($pdo);
                }
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                createTablero($pdo);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                updateTablero($pdo);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                deleteTablero($pdo);
            }
            break;
        
        case 'columnas':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $tableroId = $_GET['tablero_id'] ?? null;
                getColumnas($pdo, $tableroId);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                createColumna($pdo);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                updateColumna($pdo);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                deleteColumna($pdo);
            }
            break;
        
        case 'tarjetas':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $columnaId = $_GET['columna_id'] ?? null;
                getTarjetas($pdo, $columnaId);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                createTarjeta($pdo);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                updateTarjeta($pdo);
            } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                deleteTarjeta($pdo);
            }
            break;
        
        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Ruta no válida'
            ]);
            break;
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
}

// ===== FUNCIONES DE USUARIOS =====
function handleUsuarios($pdo) {
    $stmt = $pdo->query("SELECT Identificador as id, usuario, nombrecompleto as nombre, email FROM usuarios");
    $usuarios = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $usuarios,
        'message' => 'Usuarios obtenidos correctamente'
    ]);
}

// ===== FUNCIONES DE TABLEROS =====
function getTableros($pdo) {
    $stmt = $pdo->query("SELECT * FROM kanban_tableros ORDER BY fecha_creacion DESC");
    $tableros = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $tableros,
        'message' => 'Tableros obtenidos correctamente'
    ]);
}

function getTablero($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM kanban_tableros WHERE Identificador = ?");
    $stmt->execute([$id]);
    $tablero = $stmt->fetch();
    
    if ($tablero) {
        // Obtener columnas del tablero
        $stmt = $pdo->prepare("SELECT * FROM kanban_columnas WHERE tablero_id = ? ORDER BY orden");
        $stmt->execute([$id]);
        $columnas = $stmt->fetchAll();
        
        // Obtener tarjetas para cada columna (enriquecidas con asignado_nombre)
        foreach ($columnas as &$columna) {
            $stmt = $pdo->prepare("SELECT kt.*, u.nombrecompleto AS asignado_nombre FROM kanban_tarjetas kt LEFT JOIN usuarios u ON kt.asignado_a = u.Identificador WHERE kt.columna_id = ? ORDER BY kt.orden");
            $stmt->execute([$columna['Identificador']]);
            $columna['tarjetas'] = $stmt->fetchAll();
        }
        
        $tablero['columnas'] = $columnas;
        
        echo json_encode([
            'success' => true,
            'data' => $tablero,
            'message' => 'Tablero obtenido correctamente'
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Tablero no encontrado'
        ]);
    }
}

function createTablero($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $nombre = $input['nombre'] ?? 'Mi Tablero Kanban';
    $descripcion = $input['descripcion'] ?? '';
    $usuario_propietario = $input['usuario_id'] ?? 1;
    
    $stmt = $pdo->prepare("INSERT INTO kanban_tableros (nombre, descripcion, usuario_propietario, fecha_creacion) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$nombre, $descripcion, $usuario_propietario]);
    
    $tableroId = $pdo->lastInsertId();
    
    // Crear columnas por defecto
    $columnasDefault = [
        ['nombre' => 'Por hacer', 'color' => '#e74c3c', 'orden' => 1],
        ['nombre' => 'En progreso', 'color' => '#f39c12', 'orden' => 2],
        ['nombre' => 'Completado', 'color' => '#27ae60', 'orden' => 3]
    ];
    
    foreach ($columnasDefault as $columna) {
        $stmt = $pdo->prepare("INSERT INTO kanban_columnas (tablero_id, nombre, color, orden) VALUES (?, ?, ?, ?)");
        $stmt->execute([$tableroId, $columna['nombre'], $columna['color'], $columna['orden']]);
    }
    
    echo json_encode([
        'success' => true,
        'data' => ['id' => $tableroId],
        'message' => 'Tablero creado correctamente'
    ]);
}

function updateTablero($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = $input['id'] ?? null;
    $nombre = $input['nombre'] ?? null;
    $descripcion = $input['descripcion'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID del tablero requerido'
        ]);
        return;
    }
    
    $stmt = $pdo->prepare("UPDATE kanban_tableros SET nombre = ?, descripcion = ? WHERE Identificador = ?");
    $stmt->execute([$nombre, $descripcion, $id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Tablero actualizado correctamente'
    ]);
}

function deleteTablero($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID del tablero requerido'
        ]);
        return;
    }
    
    // Eliminar tarjetas primero
    $pdo->prepare("DELETE FROM kanban_tarjetas WHERE columna_id IN (SELECT Identificador FROM kanban_columnas WHERE tablero_id = ?)")->execute([$id]);
    
    // Eliminar columnas
    $pdo->prepare("DELETE FROM kanban_columnas WHERE tablero_id = ?")->execute([$id]);
    
    // Eliminar tablero
    $pdo->prepare("DELETE FROM kanban_tableros WHERE Identificador = ?")->execute([$id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Tablero eliminado correctamente'
    ]);
}

// ===== FUNCIONES DE COLUMNAS =====
function getColumnas($pdo, $tableroId) {
    if (!$tableroId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID del tablero requerido'
        ]);
        return;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM kanban_columnas WHERE tablero_id = ? ORDER BY orden");
    $stmt->execute([$tableroId]);
    $columnas = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $columnas,
        'message' => 'Columnas obtenidas correctamente'
    ]);
}

function createColumna($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $tableroId = $input['tablero_id'] ?? null;
    $nombre = $input['nombre'] ?? '';
    $color = $input['color'] ?? '#3498db';
    $orden = $input['posicion'] ?? 1;
    
    if (!$tableroId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID del tablero requerido'
        ]);
        return;
    }
    
    $stmt = $pdo->prepare("INSERT INTO kanban_columnas (tablero_id, nombre, color, orden) VALUES (?, ?, ?, ?)");
    $stmt->execute([$tableroId, $nombre, $color, $orden]);
    
    echo json_encode([
        'success' => true,
        'data' => ['id' => $pdo->lastInsertId()],
        'message' => 'Columna creada correctamente'
    ]);
}

function updateColumna($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = $input['id'] ?? null;
    $nombre = $input['nombre'] ?? null;
    $color = $input['color'] ?? null;
    $orden = $input['posicion'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID de la columna requerido'
        ]);
        return;
    }
    
    $stmt = $pdo->prepare("UPDATE kanban_columnas SET nombre = ?, color = ?, orden = ? WHERE Identificador = ?");
    $stmt->execute([$nombre, $color, $orden, $id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Columna actualizada correctamente'
    ]);
}

function deleteColumna($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID de la columna requerido'
        ]);
        return;
    }
    
    // Eliminar tarjetas de la columna primero
    $pdo->prepare("DELETE FROM kanban_tarjetas WHERE columna_id = ?")->execute([$id]);
    
    // Eliminar columna
    $pdo->prepare("DELETE FROM kanban_columnas WHERE Identificador = ?")->execute([$id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Columna eliminada correctamente'
    ]);
}

// ===== FUNCIONES DE TARJETAS =====
function getTarjetas($pdo, $columnaId) {
    if (!$columnaId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID de la columna requerido'
        ]);
        return;
    }

    // Enriquecer respuesta con nombre del usuario asignado
    $stmt = $pdo->prepare("SELECT kt.*, u.nombrecompleto AS asignado_nombre FROM kanban_tarjetas kt LEFT JOIN usuarios u ON kt.asignado_a = u.Identificador WHERE kt.columna_id = ? ORDER BY kt.orden");
    $stmt->execute([$columnaId]);
    $tarjetas = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => $tarjetas,
        'message' => 'Tarjetas obtenidas correctamente'
    ]);
}

// Utilidad para mapear prioridad (admite string o entero)
function mapPrioridad($value) {
    // Devuelve entero 1-4 o null si no se proporciona
    if ($value === null || $value === '') return null;
    if (is_numeric($value)) {
        $num = intval($value);
        if ($num >= 1 && $num <= 4) return $num;
        // Normalizar fuera de rango a media (2)
        return 2;
    }
    $map = [
        'low' => 1,
        'baja' => 1,
        'medium' => 2,
        'media' => 2,
        'high' => 3,
        'alta' => 3,
        'urgent' => 4,
        'urgente' => 4
    ];
    $key = strtolower(trim($value));
    return $map[$key] ?? 2;
}

function createTarjeta($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);

    $columnaId = $input['columna_id'] ?? null;
    $titulo = $input['titulo'] ?? '';
    $descripcion = $input['descripcion'] ?? '';
    $orden = $input['posicion'] ?? null; // calcular si no viene
    $asignado_a = $input['asignado_a'] ?? null;
    $prioridad = mapPrioridad($input['prioridad'] ?? null);
    $fecha_vencimiento = $input['fecha_vencimiento'] ?? null; // formato YYYY-MM-DD
    $etiquetas = $input['etiquetas'] ?? null; // texto separado por comas
    $creado_por = $input['creado_por'] ?? 1;

    if (!$columnaId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID de la columna requerido'
        ]);
        return;
    }

    // Si no viene la posición, calcular siguiente orden
    if ($orden === null) {
        $stmtMax = $pdo->prepare("SELECT COALESCE(MAX(orden), 0) + 1 AS siguiente FROM kanban_tarjetas WHERE columna_id = ?");
        $stmtMax->execute([$columnaId]);
        $orden = intval($stmtMax->fetchColumn());
    }

    $stmt = $pdo->prepare("INSERT INTO kanban_tarjetas (columna_id, titulo, descripcion, orden, asignado_a, prioridad, fecha_vencimiento, etiquetas, creado_por, fecha_creacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$columnaId, $titulo, $descripcion, $orden, $asignado_a, $prioridad, $fecha_vencimiento, $etiquetas, $creado_por]);

    echo json_encode([
        'success' => true,
        'data' => ['id' => $pdo->lastInsertId()],
        'message' => 'Tarjeta creada correctamente'
    ]);
}

function updateTarjeta($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);

    $id = $input['id'] ?? null;
    $titulo = $input['titulo'] ?? null;
    $descripcion = $input['descripcion'] ?? null;
    $orden = $input['posicion'] ?? null;
    $columnaId = $input['columna_id'] ?? null;
    $asignado_a = $input['asignado_a'] ?? null;
    $prioridad = mapPrioridad($input['prioridad'] ?? null);
    $fecha_vencimiento = $input['fecha_vencimiento'] ?? null;
    $etiquetas = $input['etiquetas'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID de la tarjeta requerido'
        ]);
        return;
    }

    // Obtener valores actuales para preservar si no se envían
    $stmtSel = $pdo->prepare("SELECT * FROM kanban_tarjetas WHERE Identificador = ?");
    $stmtSel->execute([$id]);
    $actual = $stmtSel->fetch();
    if (!$actual) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Tarjeta no encontrada']);
        return;
    }

    $titulo = $titulo ?? $actual['titulo'];
    $descripcion = $descripcion ?? $actual['descripcion'];
    $orden = $orden ?? $actual['orden'];
    $columnaId = $columnaId ?? $actual['columna_id'];
    $asignado_a = ($asignado_a === null) ? $actual['asignado_a'] : $asignado_a;
    $prioridad = ($prioridad === null) ? $actual['prioridad'] : $prioridad;
    $fecha_vencimiento = ($fecha_vencimiento === null) ? $actual['fecha_vencimiento'] : $fecha_vencimiento;
    $etiquetas = ($etiquetas === null) ? $actual['etiquetas'] : $etiquetas;

    $stmt = $pdo->prepare("UPDATE kanban_tarjetas SET titulo = ?, descripcion = ?, orden = ?, columna_id = ?, asignado_a = ?, prioridad = ?, fecha_vencimiento = ?, etiquetas = ?, fecha_actualizacion = NOW() WHERE Identificador = ?");
    $stmt->execute([$titulo, $descripcion, $orden, $columnaId, $asignado_a, $prioridad, $fecha_vencimiento, $etiquetas, $id]);

    echo json_encode([
        'success' => true,
        'message' => 'Tarjeta actualizada correctamente'
    ]);
}

function deleteTarjeta($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID de la tarjeta requerido'
        ]);
        return;
    }
    
    $pdo->prepare("DELETE FROM kanban_tarjetas WHERE Identificador = ?")->execute([$id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Tarjeta eliminada correctamente'
    ]);
}
?>