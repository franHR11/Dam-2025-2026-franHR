# Explicación del Ejercicio: Gestor Multiformato de Inventario y Pedidos (GMIP)

## 1. Análisis y Diseño del Proyecto (25%)

Este ejercicio de "milla extra" lo he diseñado como una aplicación web completa llamada **Gestor Multiformato de Inventario y Pedidos (GMIP)**, que me permite demostrar todos los conocimientos adquiridos en la asignatura de Acceso a Datos del segundo trimestre.

### Tema y Motivación
Elegí este tema porque abarca casi todas las unidades del temario, desde manejo básico de ficheros hasta bases de datos documentales avanzadas. La aplicación gestiona productos, proveedores y pedidos en un sistema de inventario, permitiendo operaciones CRUD completas, import/export en múltiples formatos, y gestión de documentos relacionados.

### Cobertura del Temario
He vinculado cada funcionalidad a las unidades y subunidades específicas:

- **Manejo de ficheros**: Import/export CSV, XML, JSON con parsers nativos; fichero binario de logs con acceso aleatorio; serialización/deserialización de objetos.
- **Conectores a BD**: Conexión PDO a MySQL con pooling implícito; ejecución de consultas, modificaciones y procedimientos almacenados.
- **ORM**: Uso de Doctrine ORM con entidades anotadas para mapeo objeto-relacional.
- **BD Documentales**: MongoDB para almacenamiento de documentos relacionados (logs, notas, attachments).
- **Componentes de acceso a datos**: Clase DataAccessComponent reutilizable con eventos, persistencia y serialización.

### Arquitectura General
La aplicación sigue una arquitectura MVC básica:
- **Backend**: PHP puro con controladores REST que manejan API endpoints.
- **Frontend**: HTML5 + JavaScript vanilla con navegación SPA ligera.
- **Base de datos**: MySQL para datos relacionales + MongoDB para documentos.
- **Persistencia**: Ficheros para logs binarios y serialización de componentes.

## 2. Tecnologías y Configuración (25%)

### Stack Tecnológico
- **Backend**: PHP 8.1+ (sin frameworks, puro para demostrar conocimientos).
- **Base de datos relacional**: MySQL/MariaDB con PDO para conectores.
- **ORM**: Doctrine 2.16 para mapeo objeto-relacional.
- **Base de datos documental**: MongoDB con extensión php-mongodb.
- **Frontend**: HTML5, CSS (TailwindCSS), JavaScript vanilla.
- **Gestión de dependencias**: Composer para Doctrine y MongoDB.

### Configuración del Entorno
Para ejecutar el proyecto, necesito:

1. **Servidor web**: XAMPP o Laragon con PHP 8.1+ y extensión mongodb.
2. **Bases de datos**:
   - MySQL con usuario y base de datos configurados en `.env`.
   - MongoDB corriendo en localhost con URI configurada.
3. **Dependencias**: Ejecutar `composer install` para instalar Doctrine y MongoDB driver.

### Estructura de Carpetas
```
gmip/
├── config/          # Configuración de BD y entornos
├── public/          # Archivos públicos (index.php, assets)
├── src/             # Código fuente (MVC)
│   ├── Controller/  # Controladores REST
│   ├── Model/       # Entidades y componentes
│   └── Service/     # Servicios de negocio
├── scripts/         # SQL para esquema y datos iniciales
└── vendor/          # Dependencias Composer
```

## 3. Funcionalidades Principales (25%)

### Gestión de Productos y Proveedores (CRUD con ORM)
- **Entidades**: Clase `Product` y `Provider` con anotaciones Doctrine.
- **Operaciones**: Crear, leer, actualizar, eliminar productos/proveedores.
- **Relaciones**: Productos pertenecen a proveedores (FK provider_id).
- **Validación**: SKU único, precios positivos, stock no negativo.

### Gestión de Pedidos con Transacciones
- **Estructura**: Pedidos con múltiples líneas de productos.
- **Transacciones**: Uso de procedimientos almacenados (`sp_process_order`) para actualizar stock de forma atómica.
- **Estados**: Pedidos pendientes → procesados, con rollback en caso de error.

### Import/Export de Datos
- **Formatos soportados**: CSV, XML, JSON.
- **Parsers**: Uso de funciones nativas PHP (fopen, json_decode, SimpleXML).
- **Conversión**: Transformación entre formatos con binding automático.

### Gestión de Ficheros y Serialización
- **Fichero binario de logs**: Acceso secuencial y aleatorio con `fopen`, `fread`, `fseek`.
- **Serialización**: Objetos PHP serializados con `serialize`/`unserialize`.
- **Subida de archivos**: Imágenes de productos con validación y movimiento.

### Base de Datos Documental
- **Colecciones**: Almacenamiento de logs, notas y attachments en MongoDB.
- **Consultas**: Búsqueda por tipo, fecha, contenido.
- **Integración**: Conexión con `MongoDB\Client` y operaciones CRUD.

### Componente de Acceso a Datos
- **Clase DataAccessComponent**: Reutilizable, con eventos (hooks) para before/after queries.
- **Persistencia**: Serialización del componente para guardar configuración.
- **Eventos**: Asociación de acciones a eventos (ej: logging automático).

### Interfaz Web
- **Navegación SPA**: JavaScript vanilla para cambiar vistas sin recargar.
- **Web Components**: Formularios reutilizables para productos/pedidos.
- **Estilos**: TailwindCSS para interfaz moderna y responsive.

## 4. Demostración y Pruebas (25%)

### Ejecución Paso a Paso
1. **Configuración inicial**:
   - Clonar proyecto y ejecutar `composer install`.
   - Configurar `.env` con credenciales MySQL y URI MongoDB.
   - Ejecutar scripts SQL (`create_sp.sql` y `seed.sql`) para crear esquema y datos iniciales.

2. **Inicio de la aplicación**:
   - Acceder a `public/index.php` en navegador.
   - Ver dashboard con estadísticas de productos, pedidos y proveedores.

3. **Funcionalidades clave**:
   - Crear producto: Ir a "Productos" → "Crear", rellenar formulario.
   - Importar datos: Usar formulario de import con archivo CSV/XML/JSON.
   - Crear pedido: En "Pedidos", añadir líneas de productos, procesar con SP.
   - Ver logs: Acceso aleatorio al fichero binario de logs.

### Manejo de Errores y Excepciones
- **Validaciones**: Mensajes de error en frontend para datos inválidos.
- **Excepciones**: Captura en controladores con códigos HTTP apropiados (400, 409, 500).
- **Transacciones**: Rollback automático en fallos de procesamiento de pedidos.

### Pruebas Realizadas
- **CRUD completo**: Verificado creación, lectura, actualización, eliminación.
- **Import/Export**: Probado con archivos de ejemplo en diferentes formatos.
- **Transacciones**: Simulado stock insuficiente para verificar rollback.
- **Serialización**: Persistido y restaurado componentes.
- **MongoDB**: Almacenado y consultado documentos relacionados.

### Rendimiento y Seguridad
- **Optimización**: Uso de prepared statements en PDO para prevenir SQL injection.
- **Validación**: Sanitización de inputs y control de tipos.
- **Logs**: Registro de todas las operaciones importantes para auditoría.

Este proyecto demuestra mi dominio completo de los conceptos de Acceso a Datos, desde conectores básicos hasta componentes avanzados, todo implementado en PHP puro sin frameworks para mostrar el conocimiento profundo de cada tecnología.
