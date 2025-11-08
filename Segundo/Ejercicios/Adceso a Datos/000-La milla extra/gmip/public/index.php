<?php
declare(strict_types=1);

// Bootstrap del sistema y configuración
require_once __DIR__ . '/../src/bootstrap.php';

use GMIP\Config\Config;
use GMIP\DB\Database;
use GMIP\Controller\ProductController;
use GMIP\Controller\ProviderController;
use GMIP\Controller\OrderController;

// Headers comunes (JSON + CORS desde variable de entorno)
$cors = Config::get('app.corsAllowOrigin');
header('Content-Type: application/json; charset=utf-8');
if ($cors) {
    header('Access-Control-Allow-Origin: ' . $cors);
}
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$ruta = $_GET['ruta'] ?? 'health';

try {
    switch ($ruta) {
        case 'health':
            echo json_encode([
                'status' => 'ok',
                'timestamp' => date(DATE_ATOM),
                'app' => 'GMIP'
            ]);
            break;

        case 'config-check':
            $report = Config::validate();
            echo json_encode([
                'status' => 'ok',
                'report' => $report
            ]);
            break;

        case 'productos':
            $pdo = Database::getPdo();
            $ctrl = new ProductController();
            $ctrl->handle($pdo, $_SERVER['REQUEST_METHOD']);
            break;

        case 'proveedores':
            $pdo = Database::getPdo();
            $ctrl = new ProviderController();
            $ctrl->handle($pdo, $_SERVER['REQUEST_METHOD']);
            break;

        case 'pedidos':
            $pdo = Database::getPdo();
            $ctrl = new OrderController();
            $ctrl->handle($pdo, $_SERVER['REQUEST_METHOD']);
            break;

        case 'procesar-pedido':
            $pdo = Database::getPdo();
            $ctrl = new OrderController();
            // Forzar acción procesar
            $_GET['action'] = 'procesar';
            $ctrl->handle($pdo, $_SERVER['REQUEST_METHOD']);
            break;

        default:
            http_response_code(404);
            echo json_encode([
                'error' => 'Ruta no encontrada',
                'ruta' => $ruta,
            ]);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error del servidor',
        'message' => $e->getMessage(),
    ]);
}