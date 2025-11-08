<?php
header('Content-Type: application/json');

// Incluir archivo de configuración
require_once 'config.php';

// Recibir datos del formulario
$usuario = $_POST['usuario'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

$response = ['success' => false, 'message' => ''];

if (empty($usuario) || empty($contrasena)) {
    $response['message'] = 'Por favor, complete todos los campos';
    echo json_encode($response);
    exit;
}

try {
    // Consultar usuario en la base de datos
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario AND contrasena = :contrasena");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':contrasena', $contrasena);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['success'] = true;
        $response['message'] = 'Inicio de sesión correcto';
        $response['nombre'] = $userData['nombrecompleto'];
    } else {
        $response['message'] = 'Usuario o contraseña incorrectos';
    }
} catch (PDOException $e) {
    $response['message'] = 'Error en la base de datos: ' . $e->getMessage();
}

echo json_encode($response);
?>