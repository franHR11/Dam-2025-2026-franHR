# 🚀 Sistema ERP Empresarial Completo

<div align="center">

![ERP Banner](https://img.shields.io/badge/ERP-Sistema%20Empresarial-blue?style=for-the-badge&logo=enterprise&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![License](https://img.shields.io/badge/License-Proprietary-red?style=for-the-badge)

**Sistema de Gestión Empresarial Modular y Escalable**

[🌐 Demo](#) · [📖 Documentación](#instrucciones-de-uso) · [🐛 Reportar Bug](mailto:desarrollo@pcprogramacion.es)

</div>

---

## 📌 DESCRIPCIÓN DEL PROYECTO

**ERP-franHR** es un **Sistema de Planificación de Recursos Empresariales (ERP)** completo y modular, desarrollado desde cero con tecnologías web modernas. Este sistema integra todos los procesos críticos de una organización en una única plataforma centralizada.

### 🎯 ¿Para quién está diseñado?

- **Pequeñas y medianas empresas** que necesitan digitalizar sus procesos
- **Autónomos y profesionales** que buscan gestionar clientes y facturación
- **Organizaciones** que requieren control total de inventario y compras
- **Equipos de trabajo** que necesitan gestión de tareas con metodología Kanban

### 💼 Utilidad Real

Este ERP elimina la necesidad de múltiples aplicaciones desconectadas, centralizando:
- Gestión de clientes (CRM)
- Control de productos e inventario
- Facturación con cálculo automático de impuestos
- Gestión de proveedores
- Tablero Kanban para organización de tareas
- Sistema de módulos instalables dinámicamente

---

## ✨ CARACTERÍSTICAS DESTACADAS

### 🔐 Sistema de Autenticación Robusto
- Login seguro con sesiones PHP
- Contraseñas hasheadas con **bcrypt**
- Control de timeout de sesión (30 minutos)
- Protección contra ataques CSRF y SQL Injection

### 📊 Gestión Modular de Aplicaciones
- **Sistema de tarjetas de módulos** con estados visuales (Activo, Inactivo, No Instalado)
- Instalación/desinstalación dinámica de módulos
- Activación/desactivación sin pérdida de datos
- Categorización por áreas (CRM, Ventas, Compras, Inventario, etc.)
- Interfaz visual con animaciones y gradientes modernos

### 🎨 Interfaz Moderna y Responsive
- Diseño **mobile-first** adaptable a todos los dispositivos
- Paleta de colores corporativa (azules, morados, gradientes)
- Iconos **Font Awesome 6**
- Animaciones suaves y feedback visual
- Tablas interactivas con búsqueda en tiempo real

### 🔄 API REST Completa
- Endpoints para todos los módulos
- Respuestas JSON estandarizadas
- Códigos HTTP apropiados (200, 201, 400, 401, 404, 500)
- Validación de datos en servidor

### 🛡️ Seguridad de Nivel Empresarial
- Consultas preparadas con **PDO** (prevención SQL Injection)
- Sanitización de salidas con `htmlspecialchars()` (prevención XSS)
- Validación doble: cliente (JavaScript) y servidor (PHP)
- Integridad referencial con claves foráneas

---

## ⚙️ FUNCIONALIDADES

### 📋 Módulo de Clientes (CRM)
- ✅ Código único autoincremental (CLI0001, CLI0002...)
- ✅ Validación de NIF/CIF español con algoritmo oficial
- ✅ Tipos de cliente: Particular, Empresa, Autónomo, ONG, Público
- ✅ Gestión de crédito y límites
- ✅ Control de bloqueos por impago
- ✅ Búsqueda y filtrado en tiempo real
- ✅ CRUD completo con protección de integridad
- ✅ Múltiples contactos por cliente
- ✅ Historial de facturación

### 📦 Módulo de Productos
- ✅ Control de stock con alertas de mínimo
- ✅ Relación con categorías y proveedores
- ✅ Carga de imágenes con validación
- ✅ Precios de compra y venta
- ✅ Cálculo automático de márgenes
- ✅ Autoguardado de cambios cada 2 segundos

### 🚚 Módulo de Proveedores
- ✅ Datos fiscales completos
- ✅ Condiciones comerciales
- ✅ Relación con productos suministrados
- ✅ Gestión de contactos

### 💰 Módulo de Facturación
- ✅ Numeración automática por ejercicio (FAC-2025-0001)
- ✅ Tipos: Venta, Compra, Rectificativa, Proforma
- ✅ Cálculo automático con **triggers SQL**
- ✅ Gestión de IVA (21%, 10%, 4%, 0%)
- ✅ Gestión de IRPF (19%, 15%, 7%, 0%)
- ✅ Control de estados (Borrador, Pendiente, Pagada, Vencida, Cancelada)
- ✅ Múltiples líneas de factura
- ✅ Descuentos globales y por línea

### 📊 Sistema Kanban
- ✅ Tableros personalizables
- ✅ Drag & drop entre columnas
- ✅ Prioridades (Alta, Media, Baja)
- ✅ Asignación de usuarios
- ✅ Fechas de vencimiento
- ✅ Estados personalizados

### 🧩 Sistema de Módulos Dinámico
- ✅ **Tarjetas visuales** con estados diferenciados por colores
- ✅ Instalación con un clic
- ✅ Activación/desactivación sin pérdida de datos
- ✅ Gestión de dependencias entre módulos
- ✅ Configuración personalizada por módulo
- ✅ Orden personalizable en el menú
- ✅ Estadísticas en tiempo real (Total, Activos, Inactivos)

---

## 🔧 TECNOLOGÍAS UTILIZADAS

### Backend
- 🐘 **PHP 8.3** - Lenguaje del lado del servidor
- 🐬 **MySQL 8.4** - Base de datos relacional con InnoDB
- 📦 **PDO** - Capa de abstracción de base de datos
- 🔒 **bcrypt** - Hash de contraseñas

### Frontend
- 🌐 **HTML5** - Estructura semántica
- 🎨 **CSS3** - Estilos modernos con gradientes y animaciones
- 🟨 **JavaScript ES6** - Lógica del cliente
- ⚡ **AJAX/Fetch API** - Comunicación asíncrona

### Librerías y Frameworks
- 🎭 **Font Awesome 6** - Iconografía
- 🎨 **Bootstrap 5.3** - Framework CSS (en módulos específicos)
- 📱 **Responsive Design** - Mobile-first approach

### Servidor y Entorno
- 🖥️ **Apache 2.4** - Servidor web
- 🔧 **Laragon** - Entorno de desarrollo local
- 📝 **.env** - Gestión de variables de entorno

---

## 📁 ESTRUCTURA DEL PROYECTO

```
Frontend/
├── 📂 Login/                    # Sistema de autenticación
│   ├── login.php               # Página de inicio de sesión
│   ├── estilo.css              # Estilos del login
│   └── javascript.js           # Lógica de autenticación
│
├── 📂 escritorio/               # Dashboard principal
│   ├── escritorio.php          # Panel de control
│   ├── escritorio.css          # Estilos del dashboard
│   └── javascript.js           # Funcionalidad del escritorio
│
├── 📂 modulos/                  # Sistema de gestión de módulos
│   ├── index.php               # Interfaz de tarjetas de módulos
│   ├── modulos.js              # Lógica de instalación/activación
│   └── api/                    # API de gestión de módulos
│       ├── gestion_modulos.php # CRUD de módulos
│       └── obtener_modulos.php # Listado de módulos
│
├── 📂 Paginas/                  # Módulos funcionales
│   ├── clientes/               # Gestión de clientes (CRM)
│   │   ├── clientes.php        # Interfaz principal
│   │   ├── css/clientes.css    # Estilos específicos
│   │   └── js/clientes.js      # Lógica del módulo
│   ├── categorias/             # Gestión de categorías
│   ├── kanban/                 # Tablero Kanban
│   │   ├── kanban-content.php  # Contenido del tablero
│   │   ├── kanban.css          # Estilos del Kanban
│   │   └── kanban.js           # Drag & drop y lógica
│   └── plantilla/              # Plantilla base para nuevos módulos
│
├── 📂 api/                      # API REST del sistema
│   ├── config.php              # Configuración de BD y conexión
│   ├── clientes/               # Endpoints de clientes
│   │   ├── guardar_cliente.php
│   │   ├── actualizar_cliente.php
│   │   ├── eliminar_cliente.php
│   │   └── obtener_clientes.php
│   ├── basededatos/            # Scripts SQL
│   │   ├── estructura.sql      # Estructura de tablas
│   │   ├── datos.sql           # Datos de ejemplo
│   │   └── kanban_estructura.sql
│   └── componentes/            # Componentes de API
│
├── 📂 componentes/              # Componentes reutilizables
│   ├── Auth/                   # Autenticación y sesiones
│   │   ├── SessionManager.php  # Gestor de sesiones (Singleton)
│   │   ├── AuthConfig.php      # Configuración de autenticación
│   │   └── create_session.php  # Creación de sesiones
│   ├── Head/                   # <head> HTML común
│   ├── Footer/                 # Footer común
│   ├── header-sup-admin/       # Header superior del admin
│   ├── header-inf-admin/       # Header inferior del admin
│   ├── Menu-Admin/             # Menú lateral dinámico
│   └── listadoModulos/         # Componente de listado de módulos
│
├── 📂 uploads/                  # Archivos subidos
│   ├── categorias/             # Imágenes de categorías
│   └── productos/              # Imágenes de productos
│
├── 📂 comun/                    # Recursos compartidos
│   ├── style.css               # Estilos globales
│   └── config.js               # Configuración JavaScript
│
├── 📄 .env                      # Variables de entorno
├── 📄 config.php                # Configuración PHP global
├── 📄 index.php                 # Router principal
├── 📄 basededatos.sql           # Dump completo de la BD
└── 📄 README.md                 # Este archivo
```

---

## 🚀 INSTRUCCIONES DE USO

### 📋 Requisitos Previos

- **PHP** >= 8.0
- **MySQL** >= 8.0
- **Apache** con mod_rewrite habilitado
- **Extensiones PHP**: PDO, pdo_mysql, mbstring, json

### 🔧 Instalación

#### 1️⃣ Clonar o descargar el proyecto

```bash
git clone https://github.com/tu-usuario/ERP-franHR.git
cd ERP-franHR/Frontend
```

#### 2️⃣ Configurar la base de datos

```sql
-- Crear la base de datos
CREATE DATABASE `erp-dam` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario (opcional)
CREATE USER 'erp-dam2'@'localhost' IDENTIFIED BY 'erp-dam2';
GRANT ALL PRIVILEGES ON `erp-dam`.* TO 'erp-dam2'@'localhost';
FLUSH PRIVILEGES;

-- Importar estructura y datos
SOURCE basededatos.sql;
```

#### 3️⃣ Configurar variables de entorno

Edita el archivo `.env` con tus credenciales:

```env
# Base de datos
DB_HOST=localhost
DB_NAME=erp-dam
DB_USER=erp-dam2
DB_PASS=erp-dam2
DB_CHARSET=utf8mb4

# URLs de la aplicación
API_BASE_URL=/api/
LOGIN_URL=/Login/login.php
DASHBOARD_URL=/escritorio/escritorio.php
LOGOUT_URL=/api/logout.php
SESSION_TIMEOUT=1800
```

#### 4️⃣ Configurar Apache (Virtual Host)

```apache
<VirtualHost *:80>
    ServerName erp-franhr.local
    DocumentRoot "C:/laragon/www/Dam-2025-2026-franHR/ERP-franHR/Frontend"
    
    <Directory "C:/laragon/www/Dam-2025-2026-franHR/ERP-franHR/Frontend">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/erp-franhr-error.log"
    CustomLog "logs/erp-franhr-access.log" common
</VirtualHost>
```

Añade a tu archivo `hosts`:
```
127.0.0.1    erp-franhr.local
```

#### 5️⃣ Iniciar el servidor

```bash
# Con Laragon: Iniciar servicios Apache y MySQL
# Con XAMPP: Iniciar Apache y MySQL desde el panel de control

# Acceder a la aplicación
http://erp-franhr.local
```

#### 6️⃣ Credenciales de acceso por defecto

```
Usuario: admin
Contraseña: admin123
```

> ⚠️ **IMPORTANTE**: Cambia estas credenciales en producción

---

## 🧪 EJEMPLOS DE USO

### 📦 Gestión de Módulos

#### Instalar un módulo nuevo

1. Accede a **Sistema de Módulos** desde el menú
2. Localiza el módulo con estado "No Instalado"
3. Haz clic en el botón **"Instalar"**
4. El sistema creará las tablas necesarias y configurará el módulo
5. El módulo aparecerá como "Inactivo"

#### Activar un módulo

1. Localiza el módulo con estado "Inactivo"
2. Haz clic en el botón **"Activar"**
3. El módulo aparecerá en el menú lateral
4. Podrás acceder a todas sus funcionalidades

#### Desactivar un módulo

1. Localiza el módulo con estado "Activo"
2. Haz clic en el botón **"Desactivar"**
3. El módulo desaparecerá del menú pero conservará sus datos

### 👥 Gestión de Clientes

```javascript
// Crear un nuevo cliente
POST /api/clientes/guardar_cliente.php
{
  "nombre_comercial": "Tecnología Avanzada S.L.",
  "razon_social": "Tecnología Avanzada Soluciones S.L.",
  "nif_cif": "B87654321",
  "tipo_cliente": "empresa",
  "limite_credito": 10000.00,
  "forma_pago": "transferencia",
  "dias_credito": 30
}

// Respuesta
{
  "success": true,
  "message": "Cliente creado correctamente",
  "cliente_id": 4,
  "codigo": "CLI0004"
}
```

### 📊 Crear una factura

```javascript
// Endpoint de facturación
POST /api/facturas/crear_factura.php
{
  "cliente_id": 4,
  "fecha": "2025-11-17",
  "lineas": [
    {
      "producto_id": 1,
      "cantidad": 3,
      "precio_unitario": 899.00,
      "iva": 21,
      "irpf": 0
    },
    {
      "producto_id": 2,
      "cantidad": 3,
      "precio_unitario": 25.00,
      "iva": 21,
      "irpf": 0
    }
  ]
}

// El sistema calcula automáticamente:
// Base: 2.772,00 €
// IVA (21%): 582,12 €
// Total: 3.354,12 €
// Número: FAC-2025-0001
```

### 🎯 Tablero Kanban

```javascript
// Crear una tarjeta
POST /api/kanban/crear_tarjeta.php
{
  "titulo": "Preparar pedido CLI0004",
  "descripcion": "3x Ordenador HP + 3x Ratón Logitech",
  "columna_id": 1,
  "prioridad": "alta",
  "usuario_asignado": 1,
  "fecha_vencimiento": "2025-11-20"
}
```

---

## 📞 Soporte y Contacto

### 🆘 Obtener Ayuda

Si encuentras algún problema o necesitas asistencia:

- 📧 **Email**: [desarrollo@pcprogramacion.es](mailto:desarrollo@pcprogramacion.es)
- 🌐 **Web**: [https://www.pcprogramacion.es](https://www.pcprogramacion.es)
- 💼 **LinkedIn**: [Francisco José Herreros](https://www.linkedin.com/in/francisco-jose-herreros)
- 🖥️ **Portfolio**: [https://franhr.pcprogramacion.es/](https://franhr.pcprogramacion.es/)

### 👨‍💻 Autor

**Francisco José Herreros (franHR)**  
Desarrollador Full Stack | PCProgramación  
📅 Año: 2025

---

## 🖼️ Imágenes del proyecto

### Sistema de Módulos con Tarjetas
![Sistema de Módulos](https://via.placeholder.com/1200x600/667eea/ffffff?text=Sistema+de+Módulos+ERP)

*Interfaz visual de gestión de módulos con estados diferenciados por colores: Verde (Activo), Amarillo (Inactivo), Gris (No Instalado)*

### Dashboard Principal
![Dashboard](https://via.placeholder.com/1200x600/764ba2/ffffff?text=Dashboard+ERP)

*Panel de control con acceso rápido a todos los módulos instalados*

### Gestión de Clientes (CRM)
![CRM](https://via.placeholder.com/1200x600/28a745/ffffff?text=Gestión+de+Clientes)

*Módulo completo de gestión de clientes con búsqueda en tiempo real*

### Tablero Kanban
![Kanban](https://via.placeholder.com/1200x600/007bff/ffffff?text=Tablero+Kanban)

*Sistema de gestión de tareas con drag & drop*

---

## 🛡️ LICENCIA

### Español

Copyright (c) 2025 Francisco José Herreros (franHR) / PCProgramación

**Todos los derechos reservados.**

Este software es propiedad de Francisco José Herreros (franHR), desarrollador de PCProgramación ([https://www.pcprogramacion.es](https://www.pcprogramacion.es)). No está permitido copiar, modificar, distribuir o utilizar este código, ni total ni parcialmente, sin una autorización expresa y por escrito del autor.

El acceso a este repositorio tiene únicamente fines de revisión, auditoría o demostración, y no implica la cesión de ningún derecho de uso o explotación.

Para solicitar una licencia o permiso de uso, contacta con: [desarrollo@pcprogramacion.es](mailto:desarrollo@pcprogramacion.es)

### English

Copyright (c) 2025 Francisco José Herreros (franHR) / PCProgramación

**All rights reserved.**

This software is the property of Francisco José Herreros (franHR), developer of PCProgramación ([https://www.pcprogramacion.es](https://www.pcprogramacion.es)). It is not allowed to copy, modify, distribute or use this code, either totally or partially, without express and written authorization from the author.

Access to this repository has only review, audit or demonstration purposes, and does not imply the transfer of any right of use or exploitation.

To request a license or permission to use, contact: [desarrollo@pcprogramacion.es](mailto:desarrollo@pcprogramacion.es)

---

## 🔝 HASHTAGS RECOMENDADOS PARA LINKEDIN

```
#ERP #SistemaEmpresarial #PHP #MySQL #JavaScript #DesarrolloWeb 
#FullStack #CRM #GestiónEmpresarial #SoftwareEmpresarial 
#Facturación #Inventario #Kanban #DesarrolloDeSoftware 
#ProgramaciónWeb #TecnologíaEmpresarial #SistemasDeGestión 
#WebDevelopment #BackendDevelopment #FrontendDevelopment 
#DatabaseDesign #APIRest #SeguridadInformática #UXDesign
```

---

<div align="center">

### 🌟 ¿Te ha gustado este proyecto?

Si este ERP te ha sido útil o te ha inspirado, no dudes en:

⭐ Darle una estrella en GitHub  
📧 Contactarme para colaboraciones  
💼 Conectar en LinkedIn  
🌐 Visitar mi portfolio

**Desarrollado con ❤️ por franHR | PCProgramación**

[🔝 Volver arriba](#-sistema-erp-empresarial-completo)

</div>
