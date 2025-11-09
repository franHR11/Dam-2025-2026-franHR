<?php

class UserController {
    private $db;
    private $user;

    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        require_once __DIR__ . '/../models/User.php';

        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->user->name = $_POST['name'] ?? '';
            $this->user->email = $_POST['email'] ?? '';
            $this->user->password = $_POST['password'] ?? '';

            if (empty($this->user->name) || empty($this->user->email) || empty($this->user->password)) {
                $_SESSION['error'] = "Todos los campos son obligatorios";
                header('Location: index.php?page=register');
                exit;
            }

            if (!filter_var($this->user->email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Email inválido";
                header('Location: index.php?page=register');
                exit;
            }

            if (strlen($this->user->password) < 6) {
                $_SESSION['error'] = "La contraseña debe tener al menos 6 caracteres";
                header('Location: index.php?page=register');
                exit;
            }

            $this->user->email = $this->user->email;
            if ($this->user->emailExists()) {
                $_SESSION['error'] = "El email ya está registrado";
                header('Location: index.php?page=register');
                exit;
            }

            if ($this->user->create()) {
                $_SESSION['success'] = "Usuario registrado correctamente";
                header('Location: index.php?page=login');
                exit;
            } else {
                $_SESSION['error'] = "Error al registrar el usuario";
                header('Location: index.php?page=register');
                exit;
            }
        }

        include __DIR__ . '/../views/register.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->user->email = $_POST['email'] ?? '';
            $this->user->password = $_POST['password'] ?? '';

            if (empty($this->user->email) || empty($this->user->password)) {
                $_SESSION['error'] = "Todos los campos son obligatorios";
                include __DIR__ . '/../views/login.php';
                return;
            }

            if ($this->user->login()) {
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['user_name'] = $this->user->name;
                $_SESSION['user_email'] = $this->user->email;

                header('Location: index.php?page=dashboard');
                exit;
            } else {
                $_SESSION['error'] = "Email o contraseña incorrectos";
                include __DIR__ . '/../views/login.php';
                return;
            }
        }

        include __DIR__ . '/../views/login.php';
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: index.php');
        exit;
    }

    public function profile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $this->user->getById($_SESSION['user_id']);
        include __DIR__ . '/../views/profile.php';
    }
}
?>
