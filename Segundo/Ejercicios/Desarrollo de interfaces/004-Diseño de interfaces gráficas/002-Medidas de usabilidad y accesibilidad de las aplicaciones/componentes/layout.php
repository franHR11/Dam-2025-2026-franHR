<?php
// componentes/layout.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplicación Empresarial</title>
    <style>
        /* Reset y estilos base para accesibilidad */
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            color: #333;
        }

        /* Menú lateral accesible */
        aside {
            background-color: #2c3e50;
            color: white;
            width: 250px;
            padding: 20px;
            box-sizing: border-box;
            transition: transform 0.3s ease;
        }

        aside nav ul {
            list-style: none;
            padding: 0;
        }

        aside nav ul li {
            margin-bottom: 15px;
        }

        aside nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 1.1em;
            display: block;
            padding: 10px;
            border-radius: 4px;
        }

        aside nav ul li a:hover,
        aside nav ul li a:focus {
            background-color: #34495e;
            outline: 2px solid #fff; /* Foco visible */
        }

        /* Sección central */
        main {
            flex: 1;
            padding: 40px;
            background-color: #f4f7f6;
            overflow-y: auto;
        }

        /* Diseño responsive */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            aside {
                width: 100%;
                text-align: center;
                padding: 10px;
            }
            main {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <aside>
        <nav role="navigation" aria-label="Menú principal">
            <h2>Menú</h2>
            <ul>
                <li><a href="007-maestro.php">Inicio</a></li>
                <li><a href="#">Clientes</a></li>
                <li><a href="#">Facturación</a></li>
                <li><a href="003-login.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </aside>
    <main role="main">
        <!-- El contenido específico iría aquí -->
        <?php if (isset($contenido_central)) echo $contenido_central; ?>
