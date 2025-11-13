## Objetivo
- Crear una web dentro del ERP que permita generar, personalizar, copiar y guardar prompts profesionales para herramientas de IA (Claude Code, Gemini CLI, Windsurf, Trae Editor, VS Code).
- Facilitar prompts orientados a desarrollo (nuevas características, corrección de bugs, refactor, estructura de carpetas) y SEO, garantizando que las instrucciones no rompan código funcional y sigan buenas prácticas.

## Alcance
- Módulo completo de "Prompts IA" con UI (listado, búsqueda, editor, copia rápida) y API (CRUD de plantillas y prompts renderizados).
- Plantillas iniciales de alta calidad con parámetros reutilizables (lenguaje, framework, stack, convenciones del repo, etc.).

## Arquitectura y Convenciones (adaptada al repo)
- Ubicación: `ERP-franHR/Frontend/Paginas/prompts/` (UI) y `ERP-franHR/Frontend/api/prompts/` (API), siguiendo la convención de páginas y endpoints existente.
- Backend: PHP procedimental como el resto del ERP, reutilizando `config.php` de la API para conexión a BD y `componentes/Auth` para sesión.
- Frontend: PHP + HTML + CSS + JS (vanilla), estructura `css/`, `js/`, `*.php` como en módulos actuales.
- Seguridad: uso de sesión de `SessionManager.php`, validaciones y saneo de entrada/salida, prepared statements.

## Modelo de Datos (SQL)
- `prompt_templates`
  - `id` (PK), `name`, `tool` (enum: claude|gemini_cli|windsurf|trae_editor|vscode), `category` (feature|bugfix|refactor|seo|structure), `description`, `content` (TEXT), `parameters_json` (JSON), `is_public` (bool), `created_at`, `updated_at`, `created_by`.
- `prompt_tags`
  - `id` (PK), `tag`, `created_at`.
- `prompt_template_tags`
  - `template_id` (FK), `tag_id` (FK).
- `prompts`
  - `id` (PK), `title`, `tool`, `rendered_content` (TEXT), `template_id` (FK nullable), `parameter_values_json` (JSON), `tags_csv`, `created_by`, `created_at`.

## API Endpoints (PHP)
- `GET /Frontend/api/prompts/obtener_templates.php` → lista + búsqueda + filtros (tool, category, tag, texto).
- `POST /Frontend/api/prompts/guardar_template.php` → crea/edita plantilla (valida parámetros, sanea HTML/Texto).
- `DELETE /Frontend/api/prompts/eliminar_template.php` → borrado seguro si no usado.
- `POST /Frontend/api/prompts/render_prompt.php` → renderiza `content` con `parameter_values_json` (server-side), aplica buenas prácticas.
- `POST /Frontend/api/prompts/guardar_prompt.php` → guarda el prompt ya renderizado (para histórico/compartir).
- `GET /Frontend/api/prompts/obtener_prompts.php` → historial del usuario, búsqueda por título/etiquetas.

## UI y Flujo
- Página `prompts.php`
  - Búsqueda y filtros (tool, categoría, etiquetas), listado de plantillas con `Copiar` y `Usar`.
  - Botón `Nueva plantilla` (solo usuarios autorizados).
- Página `editor.php`
  - Formulario de plantilla: nombre, tool, categoría, etiquetas, descripción, contenido con variables (`{{project_name}}`, `{{language}}`, ...).
  - Panel de parámetros: generador de valores y vista previa renderizada.
  - Acciones: `Guardar plantilla`, `Guardar prompt renderizado`, `Copiar al portapapeles`.
- UX
  - Copia rápida: botón que escribe al portapapeles (API Clipboard), feedback visual.
  - LocalStorage para borradores, confirmaciones previas a descartar cambios.
  - Accesibilidad (roles ARIA, focos), internacionalización mínima (ES).

## Buenas Prácticas incorporadas (autoinyectadas en render)
- No romper código funcional: pedir cambios mínimos, cubiertos por tests, con feature flags o toggles si aplica.
- Seguir convenciones del repo: estilo de archivos, rutas, patrones de módulos existentes.
- Añadir/verificar tests: unit/integración cuando se toque lógica.
- Seguridad: no introducir secretos, sanitizar inputs, usar libs ya presentes.
- Rendimiento y SEO (cuando aplique): metadatos, estructura semántica, lazy-loading.

## Plantillas Iniciales (ejemplos)
- Nueva característica (Claude Code)
  - Objetivo, alcance, impacto, archivos afectados, pasos, criterios de aceptación, tests, migraciones de datos, verificación manual, rollback.
- Corrección de bug (VS Code / Windsurf)
  - Contexto, síntomas, reproducción, diagnóstico, fix mínimo, no romper otros flujos, añadir test regresión, validación, changelog.
- Refactor seguro (Trae Editor)
  - Problema, objetivos, refactor incremental, compatibilidad, cobertura de tests, revisión de performance.
- Estructura de carpetas y módulos (Gemini CLI)
  - Árbol deseado, convenciones de nombres, puntos de extensión, dependencias.
- SEO en páginas de producto (Claude)
  - Title/description, schema.org, performance, accesibilidad, checklist de verificación.

## SEO del Módulo
- Metatags estándar en páginas nuevas, `canonical` interno, HTML semántico, `aria-*`.
- Sitemaps no aplica (módulo interno), pero estructura clara y accesible.

## Seguridad
- Requiere login (sesión), perfil con permiso para crear/editar plantillas.
- Validación server-side y client-side, límites de tamaño en `content`, rate-limit básico por sesión.

## Verificación y Pruebas
- Datos semilla: importar 5–10 plantillas iniciales.
- Pruebas manuales: flujo completo crear→render→copiar→guardar, búsqueda y filtros, permisos.
- Pruebas de endpoints: scripts de prueba (PHP) y validación de SQL (inserción/consulta/borrado).

## Cambios previstos en el repo
- Nuevos directorios y archivos:
  - `Frontend/Paginas/prompts/` → `prompts.php`, `editor.php`, `css/prompts.css`, `js/prompts.js`.
  - `Frontend/api/prompts/` → `obtener_templates.php`, `guardar_template.php`, `eliminar_template.php`, `render_prompt.php`, `guardar_prompt.php`, `obtener_prompts.php`.
  - `Frontend/api/basededatos/prompts.sql` → creación de tablas.
- Integración de menú: añadir entrada "Prompts IA" en menú admin.

## Fases de Implementación
- Fase 1: Modelo de datos y endpoints básicos (listar/guardar/renderizar).
- Fase 2: UI (listado y editor), copia rápida, borradores.
- Fase 3: Semillas de plantillas, seguridad (permisos), afinado UX/SEO.
- Fase 4: Validación y ajustes por feedback.

## Entregables
- Módulo funcional de gestión/generación de prompts.
- SQL de tablas y semillas.
- Documentación breve de uso (en el propio editor y tooltips).

## Próximo paso
- Tras confirmación, implemento Fase 1 con el esquema SQL, endpoints y páginas base siguiendo el estilo del ERP, y subo 5 plantillas de ejemplo para empezar a trabajar inmediatamente.