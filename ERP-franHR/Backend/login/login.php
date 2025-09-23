<?php
// Configuración de headers CORS primero
header('Access-Control-Allow-Origin: http://frontend.test');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// Manejar peticiones OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir la configuración de la base de datos
try {
    require_once '../config.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de configuración del servidor'
    ]);
    exit();
}

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit();
}

// Obtener los datos JSON del cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);

// Validar que se recibieron los datos necesarios
if (!isset($input['username']) || !isset($input['password'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Faltan datos requeridos (username y password)'
    ]);
    exit();
}

$username = trim($input['username']);
$password = trim($input['password']);

// Validar que los campos no estén vacíos
if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'El nombre de usuario y la contraseña son obligatorios'
    ]);
    exit();
}

try {
    // Obtener conexión a la base de datos
    $pdo = getConnection();

    if (!$pdo) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error de conexión a la base de datos'
        ]);
        exit();
    }

    // Preparar consulta para buscar el usuario
    $stmt = $pdo->prepare("SELECT Identificador, usuario, email, contrasena FROM usuarios WHERE usuario = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario existe y la contraseña es correcta
    // NOTA: En un entorno de producción, las contraseñas deben estar hasheadas.
    // Se usaría password_verify($password, $user['contrasena'])
    if ($user && $user['contrasena'] === $password) {
        // La contraseña es correcta. Devolver datos del usuario.
        unset($user['contrasena']); // No devolver el hash de la contraseña

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Login correcto',
            'user' => $user
        ]);
        exit();
    } else {
        // Usuario o contraseña incorrectos
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Usuario o contraseña incorrectos'
        ]);
        exit();
    }
} catch (PDOException $e) {
    error_log("Error en la base de datos durante el login: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor (BD)'
    ]);
    exit();
} catch (Exception $e) {
    error_log("Error general en login: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error inesperado en el servidor'
    ]);
    exit();
}
