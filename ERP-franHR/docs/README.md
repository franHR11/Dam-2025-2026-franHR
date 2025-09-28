# Proyecto ERP-franHR

## Sección Comercial
ERP-franHR es una solución de gestión empresarial diseñada para simplificar y automatizar los procesos internos de tu negocio.

## Descripción y Características
Este proyecto implementa un sistema ERP (Enterprise Resource Planning) con una arquitectura desacoplada de frontend y backend.

### Características Actuales
- Sistema de autenticación de usuarios con sesiones seguras.
- Arquitectura separada entre frontend y backend para mayor escalabilidad.

## Sección Técnica

### Prerrequisitos
- Servidor web local (ej. Laragon, XAMPP) con PHP y MySQL.
- Configurar hosts virtuales para `frontend.test` y `backend.test`.

### Instalación
1. Clona el repositorio.
2. Configura los archivos `.env` en las carpetas `Frontend` y `Backend` con las credenciales de tu base de datos y las URLs de la aplicación.
   - En `Backend/.env` asegúrate de incluir:
     ```
     DB_HOST=localhost
     DB_NAME=erp-dam
     DB_USER=tu_usuario
     DB_PASS=tu_contraseña
     DB_CHARSET=utf8mb4
     
     # URLs para CORS
     API_FRONT_URL=http://frontend.test
     API_BASE_URL=http://backend.test
     ```
   - En `Frontend/.env` asegúrate de incluir:
     ```
     API_BASE_URL=http://backend.test/
     API_FRONT_URL=http://frontend.test/
     
     # URLs de redirección para autenticación
     LOGIN_URL=http://frontend.test/Login/login.php
     DASHBOARD_URL=http://frontend.test/escritorio/escritorio.php
     LOGOUT_URL=http://backend.test/logout.php
     SESSION_TIMEOUT=1800
     ```
3. Importa la estructura de la base de datos desde `Backend/basededatos/estructura.sql` y los datos iniciales desde `Backend/basededatos/datos.sql`.
4. Accede a `http://frontend.test/Login/login.php` en tu navegador.

### Buenas Prácticas
- El sistema utiliza variables de entorno para todas las URLs, evitando el uso de URLs hardcodeadas en el código.
- La configuración CORS se maneja automáticamente utilizando las variables de entorno definidas en los archivos `.env`.