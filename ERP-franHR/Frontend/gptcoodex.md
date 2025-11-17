# 🧩 ERP franHR Frontend

## 📌 Descripción del proyecto

ERP franHR es la capa frontend de un sistema modular de gestión empresarial orientado a pymes y departamentos internos que necesitan administrar clientes, proveedores, productos y finanzas desde un único panel web. La solución combina una interfaz PHP/Bootstrap con componentes reutilizables, autenticación centralizada y un gestor de módulos que permite activar o desinstalar funcionalidades según el rol del usuario.@Frontend/index.php#1-22 @Frontend/componentes/Menu-Admin/MenuAdmin.js#1-70

- **Enfoque profesional:** pensado para consultoras TI, despachos y equipos de implantación ERP que requieren entregar rápidamente un portal corporativo personalizable.
- **Usuarios objetivo:** responsables de operaciones, comerciales y administradores del sistema que trabajan con catálogos de clientes/proveedores, facturación y cuadros de mando.

## ✨ Características destacadas

- 🛡️ **Autenticación segura:** login con doble fase (API + sesión PHP) y protección de rutas esenciales.@Frontend/Login/javascript.js#1-54 @Frontend/componentes/Auth/SessionManager.php#1-114
- 📦 **Gestor de módulos tipo "tarjetas":** instala, activa o desinstala módulos desde el dashboard mediante tarjetas interactivas con estados, confirmaciones y notificaciones.@Frontend/escritorio/javascript.js#37-343 @Frontend/modulos/index.php#1-215
- 📊 **Dashboard administrativo:** cabeceras superiores, menú lateral responsivo y widgets configurables para el escritorio principal.@Frontend/Paginas/clientes/clientes.php#1-354
- ⚙️ **APIs REST en PHP:** endpoints para login, clientes, productos y orquestación de módulos, todos respaldados por PDO y manejo de errores estructurado.@Frontend/api/login/login.php#1-112 @Frontend/api/modulos/obtener_modulos.php#1-186 @Frontend/modulos/api/gestion_modulos.php#1-358
- 🗄️ **Modelo de datos empresarial:** script SQL con tablas de clientes, contactos, facturas, líneas y triggers de consistencia que facilitan la implantación en MySQL 8.@Frontend/basededatos.sql#1-200
- 🔌 **Configuración flexible:** variables .env reutilizadas en PHP y JavaScript para rutas, sesiones y conexión a base de datos.@Frontend/.env#1-14 @Frontend/api/config.php#1-85

## ⚙️ Funcionalidades

### 1. Autenticación y sesiones

- Formulario responsive que valida campos, invoca `/api/login/login.php` y crea la sesión local antes de redirigir al escritorio.@Frontend/Login/javascript.js#1-54
- `SessionManager` comprueba credenciales en cada página protegida, controla el _timeout_ y expone utilidades para destruir la sesión cuando caduca.@Frontend/componentes/Auth/SessionManager.php#1-114

### 2. Escritorio modular y tarjetas de módulos

- `ModulosManager` consume `/modulos/api/gestion_modulos.php` para cargar tarjetas con iconos, estados y botones de acción (Instalar, Activar, Desactivar, Desinstalar).@Frontend/escritorio/javascript.js#37-343
- Cada tarjeta muestra versión, nombre técnico, categoría y controla animaciones/confirmaciones personalizadas para el instalador visual.@Frontend/modulos/index.php#1-215

### 3. Gestión 360º de clientes

- Pantallas con toolbar de filtros, tabla paginada, modales multi-pestaña (datos generales, contacto, facturación, observaciones) y acciones CRUD vía `Paginas/clientes/js/clientes.js` y los endpoints `/api/clientes/*.php`.@Frontend/Paginas/clientes/clientes.php#1-354 @Frontend/api/clientes/guardar_cliente.php#1-200

### 4. APIs y backend integrado

- Configuración PDO con fallback automático para entornos Laragon/XAMPP y cabeceras JSON unificadas en `api/config.php` y endpoints específicos para módulos, login y recursos maestros.@Frontend/api/config.php#1-85 @Frontend/api/modulos/obtener_modulos.php#1-186
- Reglas `.htaccess` canalizan rutas amigables hacia login y escritorio, manteniendo las APIs accesibles desde `/api/*`.@Frontend/.htaccess#1-18

### 5. Base de datos y scripts de soporte

- `basededatos.sql`, `consultas_completas.sql` y `limpiar_y_recrear_corregido.sql` permiten instalar, depurar o resetear las tablas de la solución, incluyendo triggers de totales para facturas.@Frontend/basededatos.sql#1-200

## 🔧 Tecnologías utilizadas

- 🐘 **PHP 8.x** – plantillas, APIs REST y componentes de sesión.
- 🐬 **MySQL 8 / MariaDB** – base de datos relacional con triggers.
- 🎨 **Bootstrap 5.3** – UI responsiva en paneles y formularios.@Frontend/componentes/Head/Head.php#4-14
- 🧩 **JavaScript Vanilla + Fetch API** – consumo de endpoints, instalador y tarjetas dinámicas.@Frontend/escritorio/javascript.js#1-414
- 🧱 **Font Awesome 6** – iconografía en menús y tarjetas.@Frontend/componentes/Head/Head.php#8-14
- 🔐 **.env + PDO** – configuración segura y conexión multientorno.@Frontend/.env#1-14 @Frontend/api/config.php#1-85

## 📁 Estructura del proyecto

| Carpeta           | Descripción                                                                                                                                                                             |
| ----------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `Login/`          | Formularios y JS del proceso de autenticación.@Frontend/Login/javascript.js#1-54                                                                                                        |
| `escritorio/`     | Dashboard principal, estilos y scripts de tarjetas de módulos.@Frontend/escritorio/javascript.js#1-414                                                                                  |
| `Paginas/`        | Módulos funcionales (clientes, proveedores, kanban, etc.) con sus assets dedicados.@Frontend/Paginas/clientes/clientes.php#1-354                                                        |
| `componentes/`    | Reutilizables (Head, menús, headers, Auth, listadoModulos) que se inyectan según la página.@Frontend/componentes/Menu-Admin/MenuAdmin.js#1-70                                           |
| `api/`            | Endpoints REST agrupados por dominio (login, clientes, productos, modulos, instalador).@Frontend/api/modulos/obtener_modulos.php#1-186                                                  |
| `modulos/`        | UI independiente del marketplace, API específica y scripts para la administración avanzada de módulos.@Frontend/modulos/index.php#1-215 @Frontend/modulos/api/gestion_modulos.php#1-358 |
| `comun/`          | Configuración JS global (`config.js`) e importes de estilos compartidos.@Frontend/comun/config.js#1-27                                                                                  |
| `basededatos.sql` | Script completo para levantar el esquema ERP con datos semilla.@Frontend/basededatos.sql#1-200                                                                                          |

## 🚀 Instrucciones de uso

1. **Prerrequisitos**
   - PHP 8.1+, MySQL 8, Composer opcional (para futuras dependencias) y un stack local como Laragon/XAMPP.
2. **Clonar y configurar entorno**
   ```bash
   git clone <repo>
   cd ERP-franHR/Frontend
   ```
3. **Configurar variables**
   - Duplica `.env` o edítalo con tus credenciales reales:
     ```env
     DB_HOST=localhost
     DB_NAME=erp-dam
     DB_USER=erp-dam2
     DB_PASS=erp-dam2
     API_BASE_URL=/api/
     LOGIN_URL=/Login/login.php
     DASHBOARD_URL=/escritorio/escritorio.php
     SESSION_TIMEOUT=1800
     ```
     @Frontend/.env#1-14
4. **Importar base de datos**
   - Usa `basededatos.sql` en phpMyAdmin/MySQL Workbench.
   - Ejecuta `limpiar_y_recrear_corregido.sql` si necesitas resetear datos de ejemplo.
5. **Configurar host virtual (opcional)**
   - Apunta tu servidor (Apache/Nginx) al directorio `Frontend/`. El `.htaccess` ya redirige la raíz al login.@Frontend/.htaccess#1-18
6. **Levantar entorno local rápido**
   ```bash
   php -S localhost:5173 -t Frontend
   ```
   - Asegúrate de que el backend PHP pueda acceder a MySQL mediante las credenciales configuradas.
7. **Iniciar sesión**
   - Accede a `http://localhost:5173` (o tu dominio) y usa las credenciales de la tabla `usuarios` (ej. `admin / admin`).
8. **Instalar/activar módulos (tarjetas)**
   - Desde el escritorio, cada tarjeta ofrece botones para instalar o activar el módulo. Confirma el _modal_ y espera la notificación.
9. **Construcción / despliegue**
   - Para producción, copia el directorio `Frontend/` a tu hosting Apache/Nginx con PHP 8, configura el `.env` y asegura HTTPS. No hay _build_ adicional al tratarse de PHP clásico.
10. **Pruebas**
    - Endpoints de prueba disponibles en `api/clientes/test*.php` para validar cálculos, NIF y formularios.

## 🧪 Ejemplos de uso

| Caso               | Endpoint / Archivo                                                  | Descripción                                                                                                                               |
| ------------------ | ------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------- |
| Login              | `POST /api/login/login.php`                                         | Recibe `{"username","password"}` y devuelve datos del usuario autenticado.@Frontend/api/login/login.php#1-112                             |
| Obtener módulos    | `GET /api/modulos/obtener_modulos.php`                              | Devuelve módulos activos, permisos por rol y usuario actualizado.@Frontend/api/modulos/obtener_modulos.php#1-186                          |
| Instalar módulo    | `POST /modulos/api/gestion_modulos.php`                             | Instala el módulo seleccionado y actualiza estados en el dashboard.@Frontend/modulos/api/gestion_modulos.php#1-272                        |
| Activar/Desactivar | `PUT /modulos/api/gestion_modulos.php`                              | Cambia el estado operativo del módulo (requiere `accion`).@Frontend/modulos/api/gestion_modulos.php#274-356                               |
| CRUD Clientes      | `/api/clientes/guardar_cliente.php`, `actualizar_cliente.php`, etc. | Endpoints especializados para operaciones de clientes con validaciones y respuestas JSON.@Frontend/api/clientes/guardar_cliente.php#1-200 |

## 📞 Soporte y contacto

- 📅 **Año:** 2025
- 📨 **Autor:** Francisco José Herreros (franHR)
- 📧 **Email:** [desarrollo@pcprogramacion.es](mailto:desarrollo@pcprogramacion.es)
- 🌐 **Web:** [https://www.pcprogramacion.es](https://www.pcprogramacion.es)
- 💼 **LinkedIn:** [Francisco José Herreros](https://www.linkedin.com/in/francisco-jose-herreros)
- 🖥️ **Portfolio:** [https://franhr.pcprogramacion.es/](https://franhr.pcprogramacion.es/)

## 🖼️ Imágenes del proyecto

Inserta aquí capturas del login, escritorio y tarjetas de instalación (1200×630 px, WebP recomendado) para usarlas en GitHub o redes sociales.

## 🛡️ Licencia

### Español

Copyright (c) 2025 Francisco José Herreros (franHR) / PCProgramación

Todos los derechos reservados.

Este software es propiedad de Francisco José Herreros (franHR), desarrollador de PCProgramación (https://www.pcprogramacion.es). No está permitido copiar, modificar, distribuir o utilizar este código, ni total ni parcialmente, sin una autorización expresa y por escrito del autor.

El acceso a este repositorio tiene únicamente fines de revisión, auditoría o demostración, y no implica la cesión de ningún derecho de uso o explotación.

Para solicitar una licencia o permiso de uso, contacta con: desarrollo@pcprogramacion.es

### English

Copyright (c) 2025 Francisco José Herreros (franHR) / PCProgramación

All rights reserved.

This software is the property of Francisco José Herreros (franHR), developer of PCProgramación (https://www.pcprogramacion.es). It is not allowed to copy, modify, distribute or use this code, either totally or partially, without express and written authorization from the author.

Access to this repository has only review, audit or demonstration purposes, and does not imply the transfer of any right of use or exploitation.

To request a license or permission to use, contact: desarrollo@pcprogramacion.es

## 🔝 Hashtags recomendados

`#ERP`, `#PHP`, `#MySQL`, `#Bootstrap`, `#EnterpriseSoftware`, `#ModularArchitecture`, `#FullStack`, `#DigitalTransformation`, `#PCProgramacion`, `#franHR`
