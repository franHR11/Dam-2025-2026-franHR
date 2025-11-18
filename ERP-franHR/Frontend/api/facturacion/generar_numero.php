<?php
require_once __DIR__ . '/facturacion_utils.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
        exit;
    }

    $ejercicio = isset($_GET['ejercicio']) ? (int) $_GET['ejercicio'] : date('Y');
    $serie = isset($_GET['serie']) ? strtoupper(trim($_GET['serie'])) : 'FAC';

    if ($ejercicio <= 0) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Ejercicio inválido']);
        exit;
    }

    $db = facturacion_db();
    $numero = factura_generar_numero($db, $ejercicio, $serie);

    echo json_encode([
        'ok' => true,
        'numero' => $numero,
        'ejercicio' => $ejercicio,
        'serie' => $serie
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}