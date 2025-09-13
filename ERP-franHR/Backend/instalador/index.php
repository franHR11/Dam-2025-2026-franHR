<?php
session_start();

// Función para ejecutar SQL
function ejecutarSQL($conn, $sql) {
    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
        return true;
    } else {
        return false;
    }
}

// Página 1: Configuración de BD
if (!isset($_POST['step']) || $_POST['step'] == 1) {
    if (isset($_POST['step']) && $_POST['step'] == 1) {
        // Procesar formulario de BD
        $host = $_POST['host'];
        $user = $_POST['user'];
        $pass = $_POST['pass'];
        $db_name = $_POST['db_name'];

        // Escapar inputs
        $host = htmlspecialchars($host);
        $user = htmlspecialchars($user);
        $pass = htmlspecialchars($pass);
        $db_name = htmlspecialchars($db_name);

        // Primero conectar sin especificar BD para verificar credenciales
        $conn = new mysqli($host, $user, $pass);
        if ($conn->connect_error) {
            echo "<h1>Error de Conexión</h1><p>Error de conexión a MySQL: " . $conn->connect_error . "</p><p><a href='index.php'>Volver al formulario</a></p>";
            exit;
        }

        echo "<p>Conectado exitosamente a MySQL.</p>";

        // Verificar si la BD existe
        $db_exists = false;
        $result = $conn->query("SHOW DATABASES LIKE '$db_name'");
        if ($result && $result->num_rows > 0) {
            $db_exists = true;
            echo "<p>La base de datos '$db_name' ya existe.</p>";
        } else {
            echo "<p>La base de datos '$db_name' no existe. Creándola...</p>";
        }

        // Crear la BD si no existe
        if (!$db_exists) {
            $sql_create_db = "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            echo "<p>Ejecutando: $sql_create_db</p>";

            if (!$conn->query($sql_create_db)) {
                echo "<h1>Error Creando Base de Datos</h1><p>Error creando BD '$db_name': " . $conn->error . "</p><p><a href='index.php'>Volver al formulario</a></p>";
                $conn->close();
                exit;
            }

            echo "<p>BD '$db_name' creada exitosamente.</p>";
        }

        // Seleccionar la base de datos
        if (!$conn->select_db($db_name)) {
            echo "<h1>Error Seleccionando BD</h1><p>Error seleccionando BD '$db_name': " . $conn->error . "</p><p><a href='index.php'>Volver al formulario</a></p>";
            $conn->close();
            exit;
        }

        echo "<p>Usando base de datos '$db_name'.</p>";

        // Verificar si la tabla usuarios ya existe
        $table_exists = false;
        $result = $conn->query("SHOW TABLES LIKE 'usuarios'");
        if ($result && $result->num_rows > 0) {
            $table_exists = true;
            echo "<p>La tabla 'usuarios' ya existe.</p>";
        } else {
            echo "<p>Creando tabla 'usuarios'...</p>";
        }

        // Crear la tabla si no existe
        if (!$table_exists) {
            $estructura_sql = "CREATE TABLE IF NOT EXISTS `usuarios` (
                `Identificador` INT NOT NULL AUTO_INCREMENT,
                `usuario` VARCHAR(100) NOT NULL UNIQUE,
                `contrasena` VARCHAR(255) NOT NULL,
                `nombrecompleto` VARCHAR(255) NOT NULL,
                `email` VARCHAR(100) NOT NULL UNIQUE,
                `telefono` VARCHAR(20) NOT NULL,
                PRIMARY KEY (`Identificador`)
            ) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";

            if (!$conn->query($estructura_sql)) {
                echo "<h1>Error Ejecutando Estructura</h1><p>Error creando tabla usuarios: " . $conn->error . "</p><p><a href='index.php'>Volver al formulario</a></p>";
                $conn->close();
                exit;
            }
            echo "<p>Tabla 'usuarios' creada exitosamente.</p>";
        }

        // Guardar en sesión
        $_SESSION['host'] = $host;
        $_SESSION['user'] = $user;
        $_SESSION['pass'] = $pass;
        $_SESSION['db_name'] = $db_name;
        $conn->close(); // Cerrar conexión temporal

        // Mostrar página 2
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Instalador ERP-franHR - Paso 2</title>
            <style>
                * { box-sizing: border-box; }
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    margin: 0;
                    padding: 0;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .container {
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
                    padding: 40px;
                    max-width: 500px;
                    width: 100%;
                    margin: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .header h1 {
                    color: #333;
                    margin: 0;
                    font-size: 24px;
                    font-weight: 600;
                }
                .header p {
                    color: #666;
                    margin: 10px 0 0 0;
                }
                .progress {
                    display: flex;
                    justify-content: center;
                    margin-bottom: 30px;
                }
                .step {
                    width: 30px;
                    height: 30px;
                    border-radius: 50%;
                    background: #e9ecef;
                    color: #666;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    margin: 0 5px;
                }
                .step.active {
                    background: #007bff;
                    color: white;
                }
                .step.completed {
                    background: #28a745;
                    color: white;
                }
                form {
                    display: flex;
                    flex-direction: column;
                }
                .form-group {
                    margin-bottom: 20px;
                }
                label {
                    display: block;
                    margin-bottom: 5px;
                    color: #333;
                    font-weight: 500;
                }
                input {
                    width: 100%;
                    padding: 12px;
                    border: 2px solid #e9ecef;
                    border-radius: 5px;
                    font-size: 16px;
                    transition: border-color 0.3s;
                }
                input:focus {
                    outline: none;
                    border-color: #007bff;
                    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
                }
                button {
                    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                    color: white;
                    border: none;
                    padding: 15px;
                    border-radius: 5px;
                    font-size: 16px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: transform 0.2s, box-shadow 0.2s;
                    margin-top: 10px;
                }
                button:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(0,123,255,0.3);
                }
                .logo {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .logo img {
                    max-width: 100px;
                    height: auto;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="logo">
                    <!-- Aquí puedes agregar un logo si tienes uno -->
                    <h2 style="color: #007bff; margin: 0;">ERP-franHR</h2>
                </div>
                <div class="header">
                    <h1>Instalador</h1>
                    <p>Paso 2: Crear Usuario Administrador</p>
                </div>
                <div class="progress">
                    <div class="step completed">1</div>
                    <div class="step active">2</div>
                </div>
                <form method="post">
                    <input type="hidden" name="step" value="2">
                    <div class="form-group">
                        <label for="usuario">Usuario:</label>
                        <input type="text" id="usuario" name="usuario" required>
                    </div>
                    <div class="form-group">
                        <label for="contrasena">Contraseña:</label>
                        <input type="password" id="contrasena" name="contrasena" required>
                    </div>
                    <div class="form-group">
                        <label for="nombrecompleto">Nombre Completo:</label>
                        <input type="text" id="nombrecompleto" name="nombrecompleto" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" id="telefono" name="telefono" required>
                    </div>
                    <button type="submit">Crear Usuario y Finalizar</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    } else {
        // Mostrar página 1
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Instalador ERP-franHR - Paso 1</title>
            <style>
                * { box-sizing: border-box; }
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    margin: 0;
                    padding: 0;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .container {
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
                    padding: 40px;
                    max-width: 500px;
                    width: 100%;
                    margin: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .header h1 {
                    color: #333;
                    margin: 0;
                    font-size: 24px;
                    font-weight: 600;
                }
                .header p {
                    color: #666;
                    margin: 10px 0 0 0;
                }
                .progress {
                    display: flex;
                    justify-content: center;
                    margin-bottom: 30px;
                }
                .step {
                    width: 30px;
                    height: 30px;
                    border-radius: 50%;
                    background: #e9ecef;
                    color: #666;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    margin: 0 5px;
                }
                .step.active {
                    background: #007bff;
                    color: white;
                }
                .step.completed {
                    background: #28a745;
                    color: white;
                }
                form {
                    display: flex;
                    flex-direction: column;
                }
                .form-group {
                    margin-bottom: 20px;
                }
                label {
                    display: block;
                    margin-bottom: 5px;
                    color: #333;
                    font-weight: 500;
                }
                input {
                    width: 100%;
                    padding: 12px;
                    border: 2px solid #e9ecef;
                    border-radius: 5px;
                    font-size: 16px;
                    transition: border-color 0.3s;
                }
                input:focus {
                    outline: none;
                    border-color: #007bff;
                    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
                }
                small {
                    display: block;
                    color: #666;
                    font-size: 14px;
                    margin-top: 5px;
                }
                button {
                    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                    color: white;
                    border: none;
                    padding: 15px;
                    border-radius: 5px;
                    font-size: 16px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: transform 0.2s, box-shadow 0.2s;
                    margin-top: 10px;
                }
                button:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(0,123,255,0.3);
                }
                .logo {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .logo img {
                    max-width: 100px;
                    height: auto;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="logo">
                    <!-- Aquí puedes agregar un logo si tienes uno -->
                    <h2 style="color: #007bff; margin: 0;">ERP-franHR</h2>
                </div>
                <div class="header">
                    <h1>Instalador</h1>
                    <p>Paso 1: Configuración de Base de Datos</p>
                </div>
                <div class="progress">
                    <div class="step active">1</div>
                    <div class="step">2</div>
                </div>
                <form method="post">
                    <input type="hidden" name="step" value="1">
                    <div class="form-group">
                        <label for="host">Host:</label>
                        <input type="text" id="host" name="host" value="localhost" required>
                    </div>
                    <div class="form-group">
                        <label for="user">Usuario MySQL:</label>
                        <input type="text" id="user" name="user" required>
                        <small>Nota: El usuario debe tener permisos para crear bases de datos y hacer GRANT (ej: 'root').</small>
                    </div>
                    <div class="form-group">
                        <label for="pass">Contraseña MySQL:</label>
                        <input type="password" id="pass" name="pass">
                    </div>
                    <div class="form-group">
                        <label for="db_name">Nombre de la Base de Datos:</label>
                        <input type="text" id="db_name" name="db_name" required>
                    </div>
                    <button type="submit">Siguiente</button>
                </form>
            </div>
        </body>
        </html>
        <?php
    }
} elseif ($_POST['step'] == 2) {
    // Página 2: Crear usuario
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    $nombrecompleto = $_POST['nombrecompleto'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];

    // Escapar inputs
    $usuario = htmlspecialchars($usuario);
    $contrasena = htmlspecialchars($contrasena);
    $nombrecompleto = htmlspecialchars($nombrecompleto);
    $email = htmlspecialchars($email);
    $telefono = htmlspecialchars($telefono);

    // Verificar que existan los datos de sesión
    if (!isset($_SESSION['host']) || !isset($_SESSION['user']) || !isset($_SESSION['db_name'])) {
        echo "<h1>Error de Sesión</h1><p>Los datos de configuración se han perdido. Por favor, reinicia el proceso de instalación.</p><p><a href='index.php'>Volver al formulario</a></p>";
        exit;
    }

    // Reconectar usando parámetros de sesión
    $host = $_SESSION['host'];
    $user = $_SESSION['user'];
    $pass = $_SESSION['pass'];
    $db_name = $_SESSION['db_name'];

    // Conectar a MySQL y seleccionar la base de datos
    $conn = new mysqli($host, $user, $pass);
    if ($conn->connect_error) {
        echo "<h1>Error de Conexión en Paso 2</h1><p>Error conectando a MySQL: " . $conn->connect_error . "</p><p><a href='index.php'>Volver al formulario</a></p>";
        exit;
    }

    // Seleccionar la base de datos
    if (!$conn->select_db($db_name)) {
        echo "<h1>Error Seleccionando BD en Paso 2</h1><p>Error seleccionando BD '$db_name': " . $conn->error . "</p><p><a href='index.php'>Volver al formulario</a></p>";
        $conn->close();
        exit;
    }

    // Escapar inputs
    $usuario = $conn->real_escape_string($usuario);
    $contrasena = $conn->real_escape_string($contrasena); // Contraseña en texto plano
    $nombrecompleto = $conn->real_escape_string($nombrecompleto);
    $email = $conn->real_escape_string($email);
    $telefono = $conn->real_escape_string($telefono);

    // Verificar si ya existe un usuario con el mismo nombre o email
    $check_sql = "SELECT COUNT(*) as count FROM `usuarios` WHERE `usuario` = '$usuario' OR `email` = '$email'";
    $result = $conn->query($check_sql);
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row['count'] > 0) {
            echo "<h1>Error: Usuario Duplicado</h1><p>Ya existe un usuario con ese nombre de usuario o email.</p><p><a href='index.php'>Volver al formulario</a></p>";
            $conn->close();
            exit;
        }
    }

    // Leer URL del frontend desde archivo .env

    $env_file = '../../Frontend/.env';
    if (file_exists($env_file)) {
        $env_content = file_get_contents($env_file);
        if (preg_match('/API_FRONT_URL=(.+)/', $env_content, $matches)) {
            $frontend_url = trim($matches[1]);
        }
    }
    
    // Insertar usuario
    $sql_insert = "INSERT INTO `usuarios` (`usuario`, `contrasena`, `nombrecompleto`, `email`, `telefono`) VALUES ('$usuario', '$contrasena', '$nombrecompleto', '$email', '$telefono')";

    if ($conn->query($sql_insert) === TRUE) {
        echo "<!DOCTYPE html>";
        echo "<html lang='es'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>Instalación Completada</title>";
        echo "<style>";
        echo "* { box-sizing: border-box; }";
        echo "body {";
        echo "    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;";
        echo "    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);";
        echo "    margin: 0;";
        echo "    padding: 0;";
        echo "    min-height: 100vh;";
        echo "    display: flex;";
        echo "    align-items: center;";
        echo "    justify-content: center;";
        echo "}";
        echo ".container {";
        echo "    background: white;";
        echo "    border-radius: 10px;";
        echo "    box-shadow: 0 15px 35px rgba(0,0,0,0.1);";
        echo "    padding: 40px;";
        echo "    max-width: 500px;";
        echo "    width: 100%;";
        echo "    margin: 20px;";
        echo "    text-align: center;";
        echo "}";
        echo ".logo {";
        echo "    margin-bottom: 20px;";
        echo "}";
        echo ".logo h2 {";
        echo "    color: #007bff;";
        echo "    margin: 0;";
        echo "}";
        echo ".success {";
        echo "    color: #28a745;";
        echo "    font-size: 28px;";
        echo "    font-weight: 600;";
        echo "    margin: 20px 0;";
        echo "}";
        echo ".info {";
        echo "    background: #f8f9fa;";
        echo "    padding: 20px;";
        echo "    border-radius: 8px;";
        echo "    margin: 20px 0;";
        echo "    border-left: 4px solid #28a745;";
        echo "}";
        echo ".info p {";
        echo "    margin: 10px 0;";
        echo "    color: #333;";
        echo "}";
        echo ".info strong {";
        echo "    color: #007bff;";
        echo "}";
        echo ".btn {";
        echo "    display: inline-block;";
        echo "    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);";
        echo "    color: white;";
        echo "    padding: 15px 30px;";
        echo "    text-decoration: none;";
        echo "    border-radius: 5px;";
        echo "    font-weight: 600;";
        echo "    transition: transform 0.2s, box-shadow 0.2s;";
        echo "    margin-top: 20px;";
        echo "}";
        echo ".btn:hover {";
        echo "    transform: translateY(-2px);";
        echo "    box-shadow: 0 5px 15px rgba(40,167,69,0.3);";
        echo "}";
        echo "</style>";
        echo "</head>";
        echo "<body>";
        echo "<div class='container'>";
        echo "<div class='logo'>";
        echo "<h2>ERP-franHR</h2>";
        echo "</div>";
        echo "<h1 class='success'>¡Instalación Completada Exitosamente!</h1>";
        echo "<div class='info'>";
        echo "<p><strong>Usuario administrador creado:</strong></p>";
        echo "<p>Usuario: <strong>$usuario</strong></p>";
        echo "<p>Email: <strong>$email</strong></p>";
        echo "<p>La contraseña se ha guardado en texto plano.</p>";
        echo "</div>";
        echo "<a href='$frontend_url" . "Login/' class='btn'>Ir al Login</a>";
        echo "</div>";
        echo "</body></html>";
    } else {
        echo "<h1>Error Insertando Usuario</h1><p>Error insertando usuario: " . $conn->error . "</p><p><a href='index.php'>Volver al formulario</a></p>";
    }

    $conn->close();
    session_destroy();
    exit; // Evitar mostrar información de debug
}

// Instalador completado - No mostrar información de debug en producción
?>