<?php
// componentes/login.php
session_start();

$mensaje_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizamos la entrada
    $usuario = htmlspecialchars($_POST['usuario']);
    $password = $_POST['password'];

    // Simulación simple de validación
    if ($usuario === "admin" && $password === "1234") {
        $_SESSION['usuario'] = $usuario;
        header("Location: 007-maestro.php");
        exit;
    } else {
        $mensaje_error = "Credenciales incorrectas. Inténtalo de nuevo.";
    }
}
?>
