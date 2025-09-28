# Memorias del Proyecto

## Registro de Cambios y Decisiones Clave

### **Fecha:** 25/05/2024
- **Cambio:** Refactorización del sistema de autenticación a una arquitectura desacoplada.
### **Fecha:** 22/09/2025
- **Cambio:** Separación de rutas en `listadoModulos.php` para categorías y aplicaciones, con funcionalidad de búsqueda.
- **Simplificación del código:** Se eliminaron variables intermedias y alias complejos para facilitar la lectura a estudiantes de DAM, manteniendo la funcionalidad intacta.
- **Decisión:** Se modificó el endpoint para manejar rutas separadas (?ruta=categorias y ?ruta=aplicaciones), consultando tablas específicas. Se agregó parámetro opcional 'busqueda' para filtrar categorías. Las aplicaciones ahora incluyen el nombre de la categoría mediante JOIN. Esto facilita la expansión futura de rutas y mejora la organización de datos.
- **Decisión:** Se elimina la gestión de sesiones del backend. El backend (`Backend/login/login.php`) ahora solo valida credenciales y devuelve datos de usuario en formato JSON. El frontend (`Frontend/Login/login.php` y su `javascript.js`) maneja un flujo de dos pasos: primero autentica contra el backend y, si tiene éxito, crea una sesión local a través de un nuevo endpoint (`Frontend/componentes/Auth/create_session.php`) que utiliza `SessionManager.php`. Esta solución resuelve el bucle de redirección y crea un módulo de autenticación más robusto y reutilizable.

### **Fecha:** 23/09/2025
- **Cambio:** Solución de problemas de CORS en la comunicación entre Frontend y Backend.
- **Decisión:** Se implementó el uso de variables de entorno para las URLs en lugar de URLs hardcodeadas para mejorar la seguridad y flexibilidad.
- **Cambios realizados:**
  - Se actualizó el archivo `.env` del Backend para incluir las URLs necesarias para CORS.
  - Se modificó el archivo `config.php` del Backend para usar las variables de entorno en la configuración CORS.
  - Se actualizó el archivo `listadoModulos.php` del Backend para cargar y usar variables de entorno.
  - Se corrigió la ruta en el archivo `listadoModulos.js` del Frontend para usar correctamente la variable de entorno `API_BASE_URL`.
- **Resultado:** Se resolvió el error de CORS que impedía la comunicación entre Frontend y Backend, manteniendo las buenas prácticas de seguridad al no usar URLs hardcodeadas.