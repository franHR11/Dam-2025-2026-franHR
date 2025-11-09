<?php
// Crear sesión temporal de administrador para desarrollo
session_start();

// Destruir sesión anterior si existe
session_destroy();

// Iniciar nueva sesión
session_start();

// Establecer datos de administrador
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_rol'] = 'admin';
$_SESSION['user_nombre'] = 'Administrador';
$_SESSION['user_apellidos'] = 'Sistema';

// Redirigir al dashboard
header('Location: listadoModulos.php');
exit();
?>
