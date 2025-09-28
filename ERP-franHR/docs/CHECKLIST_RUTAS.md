# Checklist de Rutas y Tareas

## Arquitectura de Autenticación Desacoplada

### Fase de Implementación

- [x] **Backend - `config.php`**: Revertir la configuración de `session.cookie_domain` para desacoplar las sesiones.
- [x] **Backend - `login/login.php`**: Modificar el endpoint para que valide credenciales y devuelva únicamente datos de usuario en formato JSON, sin gestionar sesiones.
- [x] **Frontend - `componentes/Auth/create_session.php`**: Crear un nuevo endpoint para recibir datos de usuario (POST) e inicializar una sesión local en el frontend.
- [x] **Frontend - `componentes/Auth/SessionManager.php`**: Actualizar el método `initUserSession` para que reciba un array de usuario y popule la sesión.
- [x] **Frontend - `Login/javascript.js`**: Implementar un flujo de autenticación asíncrono en dos pasos:
    1.  Llamada `fetch` al backend para validar credenciales.
    2.  Si la validación es exitosa, segunda llamada `fetch` al endpoint `create_session.php` para iniciar la sesión en el frontend.
## Separación de Rutas para Categorías y Aplicaciones

### Fase de Implementación

- [x] **Backend - `componentes/listado-de-modulos/listadoModulos.php`**: Modificar el endpoint para manejar rutas separadas:
    - Ruta `?ruta=categorias`: Consulta tabla `categorias_aplicaciones` y devuelve lista de categorías.
    - Ruta `?ruta=aplicaciones`: Consulta tabla `aplicaciones` con JOIN a `categorias_aplicaciones` para incluir nombre de categoría.
    - Parámetro opcional `?busqueda=texto`: Filtra categorías por nombre usando LIKE.
- [x] **Validación**: Agregar isset para verificar existencia del parámetro 'ruta'.
- [x] **Respuestas JSON**: Actualizar mensajes específicos para categorías y aplicaciones.
- [x] **Pruebas**: Verificar sintaxis PHP y funcionalidad de consultas separadas.
- [x] **Frontend - `escritorio/escritorio.php`**: Verificar que la protección de la ruta mediante `SessionManager::checkSession()` sigue funcionando correctamente.

## Solución de Problemas CORS y Uso de Variables de Entorno

### Fase de Implementación

- [x] **Backend - `.env`**: Actualizar el archivo para incluir las URLs necesarias para CORS:
    - Agregar `API_FRONT_URL=http://frontend.test`
    - Agregar `API_BASE_URL=http://backend.test`
- [x] **Backend - `config.php`**: Modificar la configuración CORS para usar variables de entorno:
    - Reemplazar `header('Access-Control-Allow-Origin: *')` por `header("Access-Control-Allow-Origin: $frontendUrl")`
    - Agregar obtención de la URL del frontend desde variables de entorno
- [x] **Backend - `componentes/listado-de-modulos/listadoModulos.php`**: Actualizar para usar variables de entorno:
    - Agregar carga de variables de entorno desde archivo `.env`
    - Modificar headers CORS para usar la URL del frontend desde variables de entorno
- [x] **Frontend - `componentes/listadoModulos/listadoModulos.js`**: Corregir la ruta de la API:
    - Reemplazar `window.API_BASE_URL + '../../Backend/componentes/listado-de-modulos/listadoModulos.php?ruta=aplicaciones'` por `window.API_BASE_URL + 'Backend/componentes/listado-de-modulos/listadoModulos.php?ruta=aplicaciones'`
- [x] **Pruebas**: Verificar que la comunicación entre Frontend y Backend funciona correctamente sin errores CORS.
