<?php
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar solicitud OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Verificar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(["ok" => false, "error" => "Método no permitido"]);
        exit;
    }

    // Verificar que se haya subido un archivo
    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "No se ha subido ninguna imagen o hubo un error en la subida"]);
        exit;
    }

    $file = $_FILES['imagen'];

    // Validaciones del archivo
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    // Validar tipo de archivo
    if (!in_array($file['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "Tipo de archivo no permitido. Solo se aceptan JPEG, PNG, GIF y WebP"]);
        exit;
    }

    // Validar tamaño del archivo
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode(["ok" => false, "error" => "El archivo es demasiado grande. El tamaño máximo es 2MB"]);
        exit;
    }

    // Crear directorio de uploads si no existe
    $uploadDir = __DIR__ . '/../../../uploads/categorias/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            http_response_code(500);
            echo json_encode(["ok" => false, "error" => "No se pudo crear el directorio de uploads"]);
            exit;
        }
    }

    // Generar nombre de archivo único
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'categoria_' . time() . '_' . uniqid() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;

    // Mover el archivo al directorio de uploads
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "Error al guardar el archivo"]);
        exit;
    }

    // Generar URL relativa para guardar en la base de datos (desde el root del proyecto)
    $relativePath = 'uploads/categorias/' . $fileName;

    echo json_encode([
        "ok" => true,
        "message" => "Imagen subida correctamente",
        "ruta" => $relativePath,
        "url" => '/' . $relativePath
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => "Error en el servidor: " . $e->getMessage()]);
}
?>
