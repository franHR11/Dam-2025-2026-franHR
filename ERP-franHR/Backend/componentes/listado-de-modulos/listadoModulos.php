<?php
// Cargar variables de entorno desde .env
if (file_exists(__DIR__ . '/../../../../.env')) {
    $env = parse_ini_file(__DIR__ . '/../../../../.env');
    foreach ($env as $key => $value) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

// Obtener la URL del frontend desde las variables de entorno
$frontendUrl = getenv('API_FRONT_URL') ?: 'http://frontend.test/';
$frontendUrl = rtrim($frontendUrl, '/');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: $frontendUrl");
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit(0);
}

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: $frontendUrl");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Verificar si se solicita la ruta de categorías o aplicaciones
if (isset($_GET['ruta']) && ($_GET['ruta'] == 'categorias' || $_GET['ruta'] == 'aplicaciones')) {
    try {
        // Incluir la configuración de la base de datos
        require_once '../../config.php';

        // Obtener conexión a la base de datos
        $pdo = getConnection();
        if (!$pdo) {
            throw new Exception('No se pudo conectar a la base de datos');
        }

        if ($_GET['ruta'] == 'categorias') {
            // Preparar consulta para obtener categorías
            if (isset($_GET['busqueda']) && trim($_GET['busqueda']) != '') {
                $stmt = $pdo->prepare("SELECT * FROM categorias_aplicaciones WHERE nombre LIKE ?");
                $stmt->execute(['%' . trim($_GET['busqueda']) . '%']);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM categorias_aplicaciones");
                $stmt->execute();
            }

            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'data' => $resultado,
                    'message' => 'Categorías obtenidas correctamente'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'data' => [],
                    'message' => 'No se encontraron categorías'
                ]);
            }
        } elseif ($_GET['ruta'] == 'aplicaciones') {
            // Preparar consulta para obtener aplicaciones con nombre de categoría
            $stmt = $pdo->prepare("SELECT aplicaciones.*, categorias_aplicaciones.nombre AS nombre_categoria FROM aplicaciones JOIN categorias_aplicaciones ON aplicaciones.categoria = categorias_aplicaciones.identificador");
            $stmt->execute();

            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'data' => $resultado,
                    'message' => 'Aplicaciones obtenidas correctamente'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'data' => [],
                    'message' => 'No se encontraron aplicaciones'
                ]);
            }
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
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Ruta no válida'
    ]);
}
