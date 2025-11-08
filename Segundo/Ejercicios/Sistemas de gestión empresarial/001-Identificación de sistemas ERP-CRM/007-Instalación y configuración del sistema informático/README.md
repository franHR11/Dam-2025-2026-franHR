# Sistema ERP - Configuración y acceso a base de datos MySQL

## Descripción

Este proyecto es un ejercicio práctico que demuestra cómo configurar y acceder a una base de datos MySQL para un sistema ERP. Incluye un formulario de autenticación de usuarios y un pequeño juego de pesca integrado.

## Archivos del proyecto

- `config.php` - Archivo de configuración para la conexión a la base de datos
- `iniciarsesion.php` - Script PHP para validar usuarios contra la base de datos
- `index.html` - Interfaz de usuario con formulario de inicio de sesión y juego
- `base_datos.sql` - Script SQL para crear la base de datos y tabla de usuarios
- `explicacion_ejercicio.md` - Documentación completa del ejercicio
- `README.md` - Este archivo con instrucciones de uso

## Instalación

1. **Requisitos previos**:
   - Servidor web con PHP (Apache, Nginx)
   - Base de datos MySQL
   - phpMyAdmin o similar para gestionar la base de datos

2. **Configurar la base de datos**:
   - Importa el archivo `base_datos.sql` en tu base de datos MySQL usando phpMyAdmin
   - Esto creará la base de datos "erp" y la tabla "usuarios" con usuarios de ejemplo

3. **Configurar la conexión**:
   - Abre el archivo `config.php`
   - Modifica las siguientes líneas con tus datos de MySQL:
   ```php
   $username = "tu_usuario_mysql";
   $password = "tu_contraseña_mysql";
   ```

4. **Acceder a la aplicación**:
   - Coloca todos los archivos en tu servidor web
   - Abre `index.html` en tu navegador

## Uso

1. **Iniciar sesión**:
   - Usa uno de los usuarios de ejemplo:
     - Usuario: admin, Contraseña: admin123
     - Usuario: juan, Contraseña: juan456
     - Usuario: maria, Contraseña: maria789
   - O crea nuevos usuarios directamente en la base de datos

2. **Jugar a la pesca**:
   - Haz clic en el botón "🎣 Jugar a la Pesca"
   - El sistema generará un número aleatorio entre 1 y 50
   - Si el número es par, "pescarás" un pez
   - Si el número es impar, no pescarás nada

## Notas técnicas

- La conexión a la base de datos utiliza PDO (PHP Data Objects)
- La comunicación entre el cliente y el servidor se realiza mediante JSON
- El diseño es responsivo y utiliza CSS3
- No se utilizan librerías externas, solo HTML, CSS y JavaScript nativos

## Posibles problemas

- Si recibes un error de conexión, verifica que los datos en `config.php` son correctos
- Asegúrate de que la base de datos "erp" y la tabla "usuarios" existen
- Verifica que tu servidor web tiene PHP instalado y configurado correctamente