# Configuración y acceso a una base de datos MySQL para un sistema ERP

## 1. Introducción y contextualización

En este ejercicio he tenido que configurar y acceder a una base de datos MySQL para un sistema ERP (Enterprise Resource Planning). Los sistemas ERP son fundamentales en las empresas ya que integran todas las áreas de negocio en una única plataforma, permitiendo gestionar recursos, procesos y datos de manera centralizada. Para que estos sistemas funcionen correctamente, necesitan una base de datos robusta que almacene toda la información de manera segura y accesible.

MySQL es uno de los sistemas gestores de bases de datos más utilizados en aplicaciones web y sistemas empresariales debido a su fiabilidad, rendimiento y compatibilidad con lenguajes como PHP. En este ejercicio, he creado una conexión básica entre una aplicación PHP y una base de datos MySQL, implementando un sistema de autenticación de usuarios y añadiendo un elemento interactivo como es un juego de pesca.

## 2. Desarrollo técnico correcto y preciso

Para realizar este ejercicio, he seguido los siguientes pasos técnicos:

### 2.1. Instalación y configuración de MySQL

Para este ejercicio e usado laragon  con phpMyAdmin para crear la base de datos y las tablas necesarias.

### 2.2. Creación de la base de datos y tabla

He creado un archivo SQL (`base_datos.sql`) que contiene las instrucciones necesarias para crear la base de datos "erp" y la tabla "usuarios" con las columnas solicitadas:

- Identificador: clave primaria autoincremental
- usuario: nombre de usuario único
- contrasena: contraseña del usuario
- nombrecompleto: nombre completo del usuario

### 2.3. Configuración de la conexión a la base de datos

El archivo `config.php` contiene los parámetros de conexión y establece el objeto PDO para interactuar con la base de datos:

```php
<?php
$host = "localhost";
$dbname = "erp";
$username = "tu_usuario_mysql";
$password = "tu_contraseña_mysql";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
```

### 2.4. Implementación del sistema de autenticación

El archivo `iniciarsesion.php` recibe los datos del formulario a través de POST, valida que los campos no estén vacíos y consulta la base de datos para verificar las credenciales. Devuelve una respuesta JSON que indica si el inicio de sesión fue exitoso o no.

### 2.5. Creación de la interfaz de usuario

He diseñado una interfaz HTML5 con CSS3 que incluye:
- Un formulario de inicio de sesión con campos de usuario y contraseña
- Estilos modernos y responsivos
- Un botón para acceder al juego de pesca
- Mensajes de respuesta para el usuario

## 3. Aplicación práctica con ejemplo claro

### 3.1. Código completo del proyecto

A continuación presento el código completo de todos los archivos que componen el proyecto:

#### 3.1.1. base_datos.sql

```sql
-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS erp;

-- Usar la base de datos
USE erp;

-- Crear la tabla usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    Identificador INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(100) NOT NULL,
    nombrecompleto VARCHAR(100) NOT NULL
);

-- Insertar algunos usuarios de ejemplo
INSERT INTO usuarios (usuario, contrasena, nombrecompleto) VALUES
('admin', 'admin123', 'Administrador del Sistema'),
('juan', 'juan456', 'Juan Pérez'),
('maria', 'maria789', 'María García');
```

#### 3.1.2. config.php

```php
<?php
$host = "localhost";
$dbname = "erp";
$username = "tu_usuario_mysql";
$password = "tu_contraseña_mysql";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
```

#### 3.1.3. iniciarsesion.php

```php
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
```

#### 3.1.4. index.html (parte HTML)

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema ERP - Inicio de Sesión</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        button:hover {
            background-color: #45a049;
        }
        
        #pesca-btn {
            background-color: #2196F3;
        }
        
        #pesca-btn:hover {
            background-color: #0b7dda;
        }
        
        .message {
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            text-align: center;
            display: none;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .pesca-result {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            display: none;
        }
        
        .pesca-success {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .pesca-fail {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sistema ERP</h1>
        <form id="login-form">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <button type="submit">Iniciar Sesión</button>
            <button type="button" id="pesca-btn">🎣 Jugar a la Pesca</button>
        </form>
        
        <div id="message" class="message"></div>
        <div id="pesca-result" class="pesca-result"></div>
    </div>
</body>
</html>
```

#### 3.1.5. index.html (parte JavaScript)

```javascript
document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const usuario = document.getElementById('usuario').value;
    const contrasena = document.getElementById('contrasena').value;
    const messageDiv = document.getElementById('message');
    
    // Crear FormData para enviar los datos
    const formData = new FormData();
    formData.append('usuario', usuario);
    formData.append('contrasena', contrasena);
    
    // Enviar la solicitud al servidor
    fetch('iniciarsesion.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        messageDiv.style.display = 'block';
        
        if (data.success) {
            messageDiv.className = 'message success';
            messageDiv.textContent = data.message + '. ¡Bienvenido/a, ' + data.nombre + '!';
        } else {
            messageDiv.className = 'message error';
            messageDiv.textContent = data.message;
        }
    })
    .catch(error => {
        messageDiv.style.display = 'block';
        messageDiv.className = 'message error';
        messageDiv.textContent = 'Error de conexión: ' + error;
    });
});

document.getElementById('pesca-btn').addEventListener('click', function() {
    const pescaResult = document.getElementById('pesca-result');
    const numero = Math.floor(Math.random() * 50) + 1;
    
    pescaResult.style.display = 'block';
    
    if (numero % 2 === 0) {
        pescaResult.className = 'pesca-result pesca-success';
        pescaResult.textContent = '¡Has pescado un pez! Número: ' + numero + ' (par)';
    } else {
        pescaResult.className = 'pesca-result pesca-fail';
        pescaResult.textContent = 'No has pescado nada. Número: ' + numero + ' (impar)';
    }
});
```

### 3.2. Funcionamiento del sistema

El sistema funciona de la siguiente manera:

1. El usuario introduce sus credenciales en el formulario
2. JavaScript envía estos datos al servidor mediante una petición fetch
3. El archivo `iniciarsesion.php` consulta la base de datos
4. Se devuelve una respuesta JSON que indica si las credenciales son correctas
5. Se muestra un mensaje al usuario con el resultado

### 3.3. Integración del juego de pesca

He añadido un botón que permite al usuario jugar un sencillo juego de pesca. El juego genera un número aleatorio entre 1 y 50. Si el número es par, el usuario "pesca" un pez; si es impar, no pescará nada.

## 4. Conclusión

Este ejercicio me ha permitido comprender la importancia de la configuración correcta de bases de datos para sistemas empresariales. La conexión entre PHP y MySQL mediante PDO es fundamental para desarrollar aplicaciones web seguras y eficientes. 

He aprendido que los sistemas ERP dependen completamente de una estructura de bases de datos bien diseñada para funcionar correctamente, y que pequeñas mejoras como añadir elementos interactivos pueden hacer que las interfaces de usuario sean más amigables y atractivas.

La implementación de un sistema de autenticación básico es el primer paso para cualquier aplicación empresarial, ya que garantiza que solo los usuarios autorizados puedan acceder a la información sensible de la empresa. Además, el uso de JSON para la comunicación entre el cliente y el servidor es una práctica moderna que mejora la experiencia del usuario al evitar recargas completas de la página.

Me ha parecido un ejercicio muy completo que combina aspectos técnicos fundamentales de desarrollo web con elementos prácticos de sistemas empresariales, demostrando cómo la tecnología puede aplicarse para resolver necesidades reales de negocio.