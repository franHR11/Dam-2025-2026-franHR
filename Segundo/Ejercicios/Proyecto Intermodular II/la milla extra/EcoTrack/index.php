<?php
session_start();

// Cargar controladores
require_once __DIR__ . '/app/controllers/UserController.php';
require_once __DIR__ . '/app/controllers/HabitController.php';
require_once __DIR__ . '/app/controllers/EcoController.php';

// Variables globales
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? '';

$userController = new UserController();
$habitController = new HabitController();
$ecoController = new EcoController();

// Sistema de rutas
switch ($action) {
    case 'logout':
        $userController->logout();
        break;
    case 'register':
        $userController->register();
        break;
    case 'login':
        $userController->login();
        break;
    case 'export':
        $ecoController->exportData();
        break;
}

switch ($page) {
    case 'login':
        $page_title = 'Iniciar Sesión';
        include __DIR__ . '/app/views/layout/header.php';
        $userController->login();
        include __DIR__ . '/app/views/layout/footer.php';
        break;

    case 'register':
        $page_title = 'Registrarse';
        include __DIR__ . '/app/views/layout/header.php';
        $userController->register();
        include __DIR__ . '/app/views/layout/footer.php';
        break;

    case 'dashboard':
        $page_title = 'Dashboard';
        include __DIR__ . '/app/views/layout/header.php';
        $ecoController->dashboard();
        include __DIR__ . '/app/views/layout/footer.php';
        break;

    case 'habit_form':
        $page_title = 'Nuevo Hábito';
        include __DIR__ . '/app/views/layout/header.php';
        $habitController->create();
        include __DIR__ . '/app/views/layout/footer.php';
        break;

    case 'results':
        $page_title = 'Resultados';
        include __DIR__ . '/app/views/layout/header.php';
        $ecoController->results();
        include __DIR__ . '/app/views/layout/footer.php';
        break;

    case 'history':
        $page_title = 'Historial';
        include __DIR__ . '/app/views/layout/header.php';
        $habitController->history();
        include __DIR__ . '/app/views/layout/footer.php';
        break;

    case 'achievements':
        $page_title = 'Logros';
        include __DIR__ . '/app/views/layout/header.php';
        $ecoController->achievements();
        include __DIR__ . '/app/views/layout/footer.php';
        break;

    case 'compare':
        $page_title = 'Comparar';
        include __DIR__ . '/app/views/layout/header.php';
        $ecoController->compare();
        include __DIR__ . '/app/views/layout/footer.php';
        break;

    case 'profile':
        $page_title = 'Mi Perfil';
        include __DIR__ . '/app/views/layout/header.php';
        $userController->profile();
        include __DIR__ . '/app/views/layout/footer.php';
        break;

    case 'about':
        $page_title = 'Acerca de';
        include __DIR__ . '/app/views/about.php';
        break;

    case 'help':
        $page_title = 'Ayuda';
        include __DIR__ . '/app/views/help.php';
        break;

    case 'privacy':
        $page_title = 'Privacidad';
        include __DIR__ . '/app/views/privacy.php';
        break;

    case 'terms':
        $page_title = 'Términos';
        include __DIR__ . '/app/views/terms.php';
        break;

    case 'newsletter':
        // Manejo de suscripción newsletter
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['success'] = "¡Gracias por suscribirte a nuestro newsletter!";
                // Aquí se podría guardar en base de datos
            } else {
                $_SESSION['error'] = "Email inválido";
            }
            header('Location: index.php');
            exit;
        }
        break;

    default:
        include __DIR__ . '/app/views/home.php';
        break;
}
?>
