# Gestión de Bases de Datos

## Tablas Utilizadas

- **usuarios**:
  - **Propósito:** Almacena la información de los usuarios registrados en el sistema.
- **categorias_aplicaciones**:
  - **Propósito:** Almacena las categorías de las aplicaciones del ERP.
  - **Campos Relevantes:** `identificador`, `nombre`.

- **aplicaciones**:
  - **Propósito:** Almacena las aplicaciones disponibles en el ERP, asociadas a categorías.
  - **Campos Relevantes:** `identificador`, `nombre`, `descripcion`, `icono`, `categoria` (FK a categorias_aplicaciones).
  - **Campos Relevantes:** `Identificador`, `usuario`, `contrasena`, `email`, `nombrecompleto`.

- **Consulta de Categorías (en `Backend/componentes/listado-de-modulos/listadoModulos.php`):**
  ```sql
  SELECT * FROM categorias_aplicaciones WHERE nombre LIKE '%busqueda%'  -- (opcional con parámetro busqueda)
  ```
  - **Propósito:** Obtiene la lista de categorías, con posibilidad de filtrar por nombre.

- **Consulta de Aplicaciones (en `Backend/componentes/listado-de-modulos/listadoModulos.php`):**
  ```sql
  SELECT a.*, ca.nombre as categoria_nombre FROM aplicaciones a JOIN categorias_aplicaciones ca ON a.categoria = ca.identificador
  ```
  - **Propósito:** Obtiene la lista de aplicaciones incluyendo el nombre de su categoría mediante JOIN.
## Consultas SQL Importantes

- **Consulta de Autenticación (en `Backend/login/login.php`):**
  ```sql
  SELECT * FROM usuarios WHERE usuario = :username
  ```
  - **Propósito:** Verifica si un usuario existe en la base de datos durante el proceso de inicio de sesión.

## Estructura General
La base de datos `erp-dam` contiene las tablas necesarias para el funcionamiento del ERP. La conexión se gestiona a través del archivo `Backend/config.php` utilizando PDO.