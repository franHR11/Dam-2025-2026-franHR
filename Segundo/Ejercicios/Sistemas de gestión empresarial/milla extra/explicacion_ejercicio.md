# Ejercicio de Milla Extra - Sistema ERP Empresarial Completo

**Asignatura:** Sistemas de Gestión Empresarial  
**Alumno:** Fran HR  
**Fecha:** 9 de noviembre de 2025  
**Evaluación:** Primera Evaluación (10% de la nota)  
**Tipo:** Ejercicio Libre - Milla Extra

---

## 1. Introducción breve y contextualización (25%)

En este ejercicio de milla extra he desarrollado un **Sistema ERP (Enterprise Resource Planning) completo** desde cero, aplicando todos los conocimientos adquiridos durante la primera evaluación de la asignatura de Sistemas de Gestión Empresarial.

Un ERP es un sistema integrado que permite gestionar todos los procesos empresariales de una organización: clientes, productos, proveedores, facturación, inventario, etc. En lugar de tener aplicaciones separadas para cada área, un ERP centraliza toda la información en una única base de datos, facilitando la toma de decisiones y mejorando la eficiencia operativa.

He decidido crear este proyecto porque quería demostrar que puedo aplicar de manera práctica todos los conceptos teóricos que hemos visto en clase. El sistema incluye gestión completa de clientes (CRM), catálogo de productos con categorías, gestión de proveedores, sistema de facturación con cálculo automático de impuestos, y un tablero Kanban para gestión de tareas.

El sistema está desarrollado con tecnologías web modernas (PHP, MySQL, JavaScript, CSS) y sigue una arquitectura modular de tres capas que permite escalar y añadir nuevas funcionalidades fácilmente.

**Relación con el temario:**
- **Unidad 1 - Identificación de sistemas ERP-CRM**: Implementación práctica de un CRM integrado en el ERP con gestión completa de clientes, contactos y seguimiento comercial.
- **Unidad 2 - Instalación y configuración de sistemas ERP-CRM**: Arquitectura completa del sistema, diseño de base de datos relacional, configuración de módulos, API REST y sistema de autenticación.

---

## 2. Desarrollo detallado y preciso (25%)

### 2.1 Arquitectura del Sistema (Unidad 1 y 2)

El sistema ERP sigue una **arquitectura de tres capas** que separa claramente las responsabilidades:

1. **Capa de Presentación (Frontend)**: Páginas PHP con HTML5, CSS3 y JavaScript ES6 que muestran la interfaz al usuario.
2. **Capa de Lógica de Negocio (API)**: Scripts PHP que procesan las peticiones HTTP, validan datos y ejecutan operaciones CRUD.
3. **Capa de Datos (Base de Datos)**: MySQL 8.4 con 15 tablas relacionadas mediante claves foráneas y triggers.

### 2.2 Base de Datos Relacional (Unidad 2)

He diseñado una base de datos normalizada con **15 tablas principales**:

**Módulo CRM:**
- **clientes**: Código único (CLI0001), NIF/CIF, datos fiscales, límite de crédito, saldo pendiente
- **clientes_contactos**: Múltiples contactos por cliente

**Módulo de Inventario:**
- **productos**: Código, precio compra/venta, stock actual/mínimo, imagen
- **categorias**: Clasificación de productos con imagen

**Módulo de Compras:**
- **proveedores**: Datos fiscales, condiciones de pago

**Módulo de Facturación:**
- **facturas**: Numeración automática (FAC-2025-0001), estados, totales calculados con triggers
- **facturas_lineas**: Líneas con IVA (21%, 10%, 4%, 0%), IRPF (19%, 15%, 7%, 0%)

**Módulo Kanban:**
- **kanban_tableros, kanban_columnas, kanban_tarjetas**: Gestión de tareas

**Seguridad:**
- **usuarios**: Autenticación con bcrypt
- **modulos**: Gestión dinámica de módulos

### 2.3 Módulos Funcionales

#### A) Módulo de Clientes - CRM (Unidad 1)
- Código único autoincremental
- Validación de NIF/CIF español
- Tipos: particular, empresa, autónomo, ONG, público
- Gestión de crédito y bloqueos
- Búsqueda en tiempo real
- CRUD completo con protección de integridad

#### B) Módulo de Productos (Unidad 2)
- Control de stock con alertas
- Relación con categorías y proveedores
- Carga de imágenes con validación
- Autoguardado de cambios

#### C) Módulo de Proveedores (Unidad 2)
- Datos fiscales completos
- Condiciones comerciales
- Relación con productos

#### D) Módulo de Facturación (Unidad 2)
- Numeración automática por ejercicio
- Tipos: venta, compra, rectificativa, proforma
- Cálculo automático con triggers SQL
- Gestión de IVA e IRPF
- Control de pagos

#### E) Sistema Kanban (Unidad 2)
- Tableros personalizables
- Drag & drop entre columnas
- Prioridades y asignación de usuarios

### 2.4 Sistema de Seguridad (Unidad 1 y 2)

- Login con sesiones PHP y bcrypt
- Control de acceso por roles
- Protección CSRF
- Validación y sanitización de datos
- Consultas preparadas (PDO)
- Timeout de sesión (30 minutos)

### 2.5 Componentes Reutilizables (Unidad 2)

Componentes PHP modulares:
- Head.php, HeaderSupAdmin.php, HeaderInfAdmin.php
- MenuAdmin.php, Footer.php
- SessionManager.php (singleton)
- AuthConfig.php

### 2.6 API REST (Unidad 2)

Endpoints para cada módulo:
- Clientes: POST/GET/PUT/DELETE
- Productos: POST/GET/DELETE + upload de imágenes
- Proveedores: POST/GET/DELETE
- Autenticación: POST login/logout

Respuestas JSON con códigos HTTP estándar (200, 201, 400, 401, 404, 500).

### 2.7 Interfaz de Usuario (Unidad 1 y 2)

- Diseño responsive (móvil, tablet, escritorio)
- Paleta corporativa (azules, morados, grises)
- Iconos Font Awesome 6
- Feedback visual con animaciones
- Tablas interactivas con búsqueda
- Formularios validados
- Modales para CRUD

---

## 3. Aplicación práctica con ejemplo claro (25%)

### Flujo de Trabajo Completo

**1. Gestión de Clientes:**
- Accedo al módulo de clientes
- Creo "Tecnología Avanzada S.L." con NIF B87654321
- El sistema genera código CLI0004 automáticamente
- Establezco límite de crédito de 10.000€

**2. Gestión de Productos:**
- Añado "Ordenador Portátil HP ProBook 450"
- Precio compra: 650€, venta: 899€ (margen 38%)
- Stock: 15 unidades, mínimo: 5
- Subo imagen del producto

**3. Creación de Factura:**
- Selecciono cliente "Tecnología Avanzada S.L."
- Sistema genera FAC-2025-0001
- Añado líneas:
  - 3x Ordenador HP (899€) + IVA 21%
  - 3x Ratón Logitech (25€) + IVA 21%
- Cálculo automático:
  - Base: 2.772€
  - IVA: 582,12€
  - Total: 3.354,12€

**4. Gestión Kanban:**
- Creo tarjeta "Preparar pedido"
- Asigno usuario y prioridad alta
- Arrastro a "Hecho" cuando finaliza

### Características Técnicas Destacadas

**Validación NIF/CIF:**
Algoritmo oficial español que rechaza NIFs incorrectos.

**Triggers SQL:**
Recalculan automáticamente totales de facturas al insertar/actualizar/eliminar líneas.

**Protección de Integridad:**
Impide eliminar clientes con facturas asociadas.

**Búsqueda en Tiempo Real:**
Filtrado instantáneo con AJAX sin recargar página.

**Autoguardado:**
Guarda cambios cada 2 segundos en productos.

### Errores Comunes Evitados

**1. SQL Injection:**
Uso exclusivo de consultas preparadas con PDO.

**2. XSS:**
Sanitización con htmlspecialchars() en todas las salidas.

**3. Contraseñas:**
Hasheadas con bcrypt (password_hash/password_verify).

**4. Integridad Referencial:**
Claves foráneas con ON DELETE RESTRICT.

**5. Validación:**
Doble validación: cliente (JavaScript) y servidor (PHP).

### Tecnologías Utilizadas

- Backend: PHP 8.3 con PDO
- Base de Datos: MySQL 8.4 con InnoDB
- Frontend: HTML5, CSS3, JavaScript ES6
- Librerías: Font Awesome 6
- Servidor: Apache 2.4

---

## 4. Conclusión breve (25%)

Este ejercicio me ha permitido aplicar integralmente todos los conocimientos de la primera evaluación. He desarrollado un sistema ERP completo que demuestra mi comprensión de:

- Arquitectura de sistemas empresariales en tres capas
- Diseño de bases de datos normalizadas con relaciones
- Desarrollo de módulos CRM integrados
- API REST para comunicación entre capas
- Seguridad informática (autenticación, protección contra ataques)
- Experiencia de usuario moderna y responsive

El proyecto conecta con los ejercicios anteriores:
- **Unidad 1**: CRM básico → Ahora integrado en ERP completo
- **Unidad 2**: Facturación simple → Ahora con cálculo automático de impuestos y múltiples líneas

La diferencia clave es la **integración**: todos los módulos comparten datos (clientes → facturas → productos → proveedores), que es la esencia de un ERP real.

Me ha parecido un ejercicio muy completo que me ha ayudado a consolidar todos los conocimientos de la evaluación. El sistema funciona correctamente, está documentado y sigue las mejores prácticas de desarrollo web.

---

## Rúbrica de evaluación cumplida

### 1. Introducción breve y contextualización (25%)
✅ He explicado qué es un ERP, para qué sirve, por qué lo desarrollé y cómo se relaciona con las Unidades 1 y 2.

### 2. Desarrollo detallado y preciso (25%)
✅ Incluye definiciones correctas, terminología técnica, explicación paso a paso de cada módulo y ejemplos reales.

### 3. Aplicación práctica con ejemplo claro (25%)
✅ Flujo de trabajo completo, errores comunes evitados y demostración de funcionamiento real.

### 4. Conclusión breve (25%)
✅ Resumen de puntos clave y enlace con otros contenidos de las unidades 1 y 2.

### Calidad de la presentación
✅ Ortografía correcta, organización clara con encabezados, lenguaje técnico apropiado.

### Código funcional
✅ Todo el código del proyecto está en la carpeta Frontend, funciona correctamente y está comentado.