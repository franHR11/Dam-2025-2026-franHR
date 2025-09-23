# Memorias del Proyecto

## Registro de Cambios y Decisiones Clave

### 2024
- 15/01/2024: Refactorización del sistema de autenticación para mejorar la seguridad.
- 28/02/2024: Simplificación del código en los componentes principales.

### 2025
- 10/05/2025: Implementación de la nueva arquitectura de autenticación y separación de rutas.
- 23/09/2025: Solución de problemas CORS mediante el uso de variables de entorno:
  - Se agregaron variables de entorno en los archivos `.env` del backend y frontend.
  - Se modificó `config.php` para usar la variable `API_FRONT_URL` en las cabeceras CORS.
  - Se actualizó `listadoModulos.php` para cargar variables de entorno y usar `API_FRONT_URL`.
  - Se corrigió la ruta en `listadoModulos.js` para usar correctamente `API_BASE_URL`.
  - Resultado: Se resolvió el error CORS que impedía la comunicación entre frontend y backend.
- 24/09/2025: Corrección adicional de problemas CORS y URLs hardcodeadas:
  - Se corrigió la ruta del archivo `.env` en `listadoModulos.php`.
  - Se agregó código de depuración para identificar problemas con las variables de entorno.
  - Se modificaron las cabeceras CORS para permitir cualquier origen durante la depuración.
  - Se actualizó `javascript.js` para usar variables de entorno en lugar de URLs hardcodeadas.
  - Se corrigió la ruta en `listadoModulos.js` eliminando el prefijo 'Backend/' redundante.