<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>EcoTrack</title>
    <meta name="description" content="Calcula y reduce tu huella ecológica con EcoTrack">

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="./public/css/main.css">
    <link rel="stylesheet" href="./public/css/components/buttons.css">
    <link rel="stylesheet" href="./public/css/components/forms.css">
    <link rel="stylesheet" href="./public/css/components/charts.css">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <div class="navbar__brand">
                    <a href="index.php" class="brand">
                        <i class="fas fa-leaf"></i>
                        <span>EcoTrack</span>
                    </a>
                </div>

                <ul class="navbar__menu">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="navbar__item">
                            <a href="index.php?page=dashboard" class="navbar__link">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="navbar__item">
                            <a href="index.php?page=habit_form" class="navbar__link">
                                <i class="fas fa-plus-circle"></i>
                                Nuevo Hábito
                            </a>
                        </li>
                        <li class="navbar__item">
                            <a href="index.php?page=history" class="navbar__link">
                                <i class="fas fa-history"></i>
                                Historial
                            </a>
                        </li>
                        <li class="navbar__item">
                            <a href="index.php?page=achievements" class="navbar__link">
                                <i class="fas fa-trophy"></i>
                                Logros
                            </a>
                        </li>
                        <li class="navbar__item navbar__dropdown">
                            <a href="#" class="navbar__link">
                                <i class="fas fa-user"></i>
                                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                                <i class="fas fa-chevron-down"></i>
                            </a>
                            <ul class="dropdown__menu">
                                <li><a href="index.php?page=profile">Mi Perfil</a></li>
                                <li><a href="index.php?page=compare">Comparar</a></li>
                                <li class="dropdown__divider"></li>
                                <li><a href="index.php?action=logout">Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="navbar__item">
                            <a href="index.php?page=login" class="navbar__link">
                                <i class="fas fa-sign-in-alt"></i>
                                Iniciar Sesión
                            </a>
                        </li>
                        <li class="navbar__item">
                            <a href="index.php?page=register" class="btn btn--primary">Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>

                <button class="navbar__toggle" id="navbarToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </nav>
        </div>
    </header>

    <main class="main">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert--error">
                <i class="fas fa-exclamation-circle"></i>
                <?php
                    echo htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert--success">
                <i class="fas fa-check-circle"></i>
                <?php
                    echo htmlspecialchars($_SESSION['success']);
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <div class="container">
