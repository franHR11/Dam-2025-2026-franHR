<?php
require_once 'SessionManager.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['user'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan datos de usuario']);
    exit();
}

$user = $input['user'];

// Inicia una sesión segura en el frontend
SessionManager::initUserSession($user);

http_response_code(200);
echo json_encode(['success' => true, 'message' => 'Sesión del frontend creada con éxito']);