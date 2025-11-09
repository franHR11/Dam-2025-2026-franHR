# Página de Clientes - ERP franHR

## Descripción
Módulo completo para la gestión de clientes con funcionalidades CRUD (Crear, Leer, Actualizar, Eliminar), siguiendo los patrones arquitectónicos del proyecto ERP.

## 📁 Estructura de Archivos

```
clientes/
├── clientes.php              # Página principal de gestión de clientes
├── css/
│   └── clientes.css         # Estilos específicos de la página
├── js/
│   └── clientes.js          # Lógica JavaScript completa
├── api/                     # (vacio - APIs en /api/clientes/)
└── README.md               # Esta documentación

api/clientes/               # APIs del backend
├── obtener_clientes.php    # GET - Obtener todos los clientes
├── guardar_cliente.php     # POST - Crear nuevo cliente
├── actualizar_cliente.php  # POST - Actualizar cliente existente
└── eliminar_cliente.php    # POST - Eliminar cliente
```

## 🎯 Funcionalidades

### ✅ CRUD Completo
- **Crear**: Nuevo cliente con autogeneración de códigos
- **Leer**: Listado con búsqueda, filtros y paginación
- **Actualizar**: Edición completa con tabs organizados
- **Eliminar**: Eliminación segura con validaciones

### 🔍 Búsqueda y Filtros
- Búsqueda por nombre comercial, razón social, código, email, NIF/CIF
- Filtro por tipo de cliente (Particular, Empresa, Autónomo, ONG, Público)
- Filtro por estado (Activos, Bloqueados)
- Paginación automática

### 💾 Gestión de Datos
- Autogeneración de códigos por tipo de cliente
- Validación de NIF/CIF español
- Validación de email
- Exportación a CSV
- Importación (en desarrollo)

### 🎨 Interfaz de Usuario
- Diseño moderno con gradientes y animaciones
- Tabs organizados: Datos Generales, Contacto, Facturación, Observaciones
- Badges visuales para estados y tipos
- Modales para edición y detalles
- Alertas dinámicas
- Loading states

## 📊 Base de Datos

### Tabla: `clientes`
```sql
CREATE TABLE `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `nombre_comercial` varchar(200) NOT NULL,
  `razon_social` varchar(200) DEFAULT NULL,
  `nif_cif` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `pais` varchar(100) DEFAULT 'España',
  `telefono` varchar(20) DEFAULT NULL,
  `telefono2` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `web` varchar(255) DEFAULT NULL,
  `tipo_cliente` enum('particular','empresa','autonomo','ong','publico') NOT NULL DEFAULT 'empresa',
  `forma_pago` enum('contado','transferencia','tarjeta','cheque','paypal') DEFAULT 'transferencia',
  `dias_credito` int DEFAULT '0',
  `limite_credito` decimal(12,2) DEFAULT '0.00',
  `importe_acumulado` decimal(12,2) DEFAULT '0.00',
  `saldo_pendiente` decimal(12,2) DEFAULT '0.00',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `bloqueado` tinyint(1) NOT NULL DEFAULT '0',
  `observaciones` text,
  `contacto_principal` varchar(100) DEFAULT NULL,
  `cargo_contacto` varchar(100) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  UNIQUE KEY `nif_cif` (`nif_cif`)
);
```

## 🔄 Endpoints API

### Obtener Clientes
```
GET /api/clientes/obtener_clientes.php
```
**Respuesta:**
```json
{
  "ok": true,
  "clientes": [...],
  "total": 150
}
```

### Crear Cliente
```
POST /api/clientes/guardar_cliente.php
Content-Type: application/json
```
**Request:**
```json
{
  "codigo": "EMP0001",
  "nombre_comercial": "Empresa S.L.",
  "tipo_cliente": "empresa",
  "activo": 1,
  ...
}
```

### Actualizar Cliente
```
POST /api/clientes/actualizar_cliente.php
Content-Type: application/json
```
**Request:**
```json
{
  "id": 123,
  "nombre_comercial": "Empresa Actualizada S.L.",
  ...
}
```

### Eliminar Cliente
```
POST /api/clientes/eliminar_cliente.php
Content-Type: application/json
```
**Request:**
```json
{
  "id": 123
}
```

## 🎨 Componentes UI

### Toolbar
- Botones: Nuevo, Importar, Exportar
- Campo de búsqueda en tiempo real
- Filtros por tipo y estado
- Diseño con gradientes modernos

### Tabla Principal
- Selección múltiple con checkbox
- Badges para tipos y estados
- Acciones rápidas: Ver, Editar, Eliminar
- Paginación con navegación completa

### Modal Crear/Editar
- **Pestaña Datos Generales**: Información básica, dirección, estado
- **Pestaña Contacto**: Teléfonos, email, contacto principal
- **Pestaña Facturación**: Forma de pago, créditos, límites
- **Pestaña Observaciones**: Notas adicionales

### Modal Detalles
- Vista completa en formato tabla
- Información organizada por secciones
- Botón para edición rápida

## 🔧 Configuración

### Variables de Entorno
El sistema utiliza las variables definidas en `.env`:
- `DB_HOST`: Servidor de base de datos
- `DB_NAME`: Nombre de la base de datos
- `DB_USER`: Usuario de la base de datos
- `DB_PASS`: Contraseña de la base de datos

### Dependencias
- **Bootstrap 5.3.0**: Framework CSS
- **Font Awesome**: Iconos
- **PHP 8.3+**: Backend
- **MySQL 8.0+**: Base de datos

## 🚀 Instalación y Uso

1. **Acceso a la página:**
   ```
   http://localhost/Paginas/clientes/clientes.php
   ```

2. **Requisitos previos:**
   - Sesión de usuario activa (SessionManager)
   - Permisos de acceso a la sección de clientes

3. **Configuración inicial:**
   - Verificar que las variables de entorno estén configuradas
   - Asegurar que la tabla `clientes` exista en la base de datos

## 🛡️ Seguridad

### Validaciones Implementadas
- Validación de campos obligatorios
- Verificación de duplicados (código, NIF/CIF)
- Validación de formato de email
- Validación de NIF/CIF español
- Protección contra SQL injection (PDO prepared statements)

### Permisos y Acceso
- Verificación de sesión obligatoria
- Protección de endpoints del backend
- Validación de datos de entrada

## 📱 Responsive Design

### Breakpoints
- **Desktop (>768px):** Layout completo con todas las columnas
- **Tablet (768px):** Ajuste de columnas y botones compactos
- **Mobile (<768px):** Tabla optimizada, modales a pantalla completa

### Adaptaciones Móviles
- Reducción de tamaño de botones
- Compresión de tabs
- Ajuste de fuentes y espaciados

## 🔄 Mantenimiento

### Logs y Debug
- Consola JavaScript para debugging
- Logs de errores del backend
- Alertas visuales para el usuario

### Optimización
- Paginación para manejar grandes volúmenes
- Carga asíncrona de datos
- Caching local de clientes

## 🧪 Testing

### Casos de prueba recomendados
1. **Creación:** Verificar autogeneración de códigos
2. **Validación:** Probar límites y formatos
3. **Búsqueda:** Verificar filtros combinados
4. **Edición:** Actualizar todos los campos
5. **Eliminación:** Verificar restricciones con facturas

## 🐛 Issues Conocidos

- La función de importación está en desarrollo
- No se pueden eliminar clientes con facturas asociadas (por diseño)
- Los contactos asociados se eliminan en cascada

## 🔄 Versiones

### v1.0.0 (Actual)
- ✅ CRUD completo
- ✅ Búsqueda y filtros
- ✅ Exportación CSV
- ✅ Validaciones
- ✅ Responsive design
- ✅ Integración con SessionManager

### Próximas versiones
- 🔄 Importación masiva
- 🔄 Gestión de contactos asociados
- 🔄 Historial de cambios
- 🔄 Reportes personalizados