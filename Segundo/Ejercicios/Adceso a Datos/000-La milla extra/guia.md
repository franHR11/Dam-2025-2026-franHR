Actividad
Desarrolla un ejercicio libre. El ejercicio debe ilustrar todos los conocimientos, o el máximo número posible de conocimientos, de la evaluación, en la presente asignatura.

La temática del ejercicio es libre.

La extensión del ejercicio debe ser considerable: representa el 10% de la nota de evaluación del trimestre.

El ejercicio de la milla extra debe estar realizado en base a la rúbrica de evaluación (la misma rúbrica de evaluación que en los ejercicios).

Detalla, en cada parte del ejercicio, la unidad y la subunidad a la que pertenecen, en base al temario que hemos trabajado en clase.

El ejercicio de milla extra se presenta en el día del examen. Importante: el ejercicio debe funcionar/compilar/ejecutar correctamente, en el momento del examen. Una vez presentado en el examen, se pasa a rellenar la rúbrica de evaluación.



esto es lo dado en el trimestre ctual




Manejo de ficheros:
— Clases asociadas a las operaciones de gestión de ficheros y directorios: creación, borrado, copia, movimiento, recorrido, entre otras.
— Formas de acceso a un fichero. Ventajas.
— Clases para gestión de flujos de datos desde/hacia ficheros. Flujos de bytes y de caracteres.
— Operaciones sobre ficheros secuenciales y aleatorios.
— Serialización/deserialización de objetos.
— Trabajo con ficheros: de intercambio de datos (XML y JSON, entre otros). Analizadores sintácticos (parser) y vinculación (binding). Conversión entre diferentes formatos.
— Excepciones: detección y tratamiento.
— Desarrollo de aplicaciones que utilizan ficheros.

Manejo de conectores:
— El desfase objeto-relacional.
— Protocolos de acceso a bases de datos. Conectores.
— Establecimiento de conexiones. Pooling de conexiones.
— Ejecución de sentencias de descripción de datos.
— Ejecución de sentencias de modificación de datos.
— Ejecución de consultas. Manipulación del resultado.
— Ejecución de procedimientos almacenados en la base de datos. Parámetros.
— Gestión de transacciones.
— Desarrollo de programas que utilizan bases de datos.

Herramientas de mapeo objeto relacional (ORM):
— Concepto de mapeo objeto relacional.
— Características de las herramientas ORM. Herramientas ORM más utilizadas.
— Instalación de una herramienta ORM. Configuración.
— Estructura de un fichero de mapeo. Elementos, propiedades.
— Mapeo basado en anotaciones.
— Clases persistentes.
— Sesiones; estados de un objeto.
— Carga, almacenamiento y modificación de objetos.
— Consultas SQL.
— Gestión de transacciones.
— Desarrollo de programas que utilizan bases de datos a través de herramientas ORM.

— Gestores de bases de datos objeto relacionales. Características. Ventajas.
— Gestión de objetos con SQL; ANSI SQL.
— Acceso a las funciones del gestor de base de datos objeto-relacional desde el lenguaje de programación.
— Gestores de bases de datos orientadas a objetos. Características. Ventajas.
— Gestión de la persistencia de objetos.
— El interfaz de programación de aplicaciones de la base de datos orientada a objetos. Consultas y persistencia de datos. Lenguaje OQL.
— Gestión de transacciones.
— Desarrollo de programas que gestionan objetos en bases de datos.

— Bases de datos documentales nativas. Características. Ventajas.
— Establecimiento y cierre de conexiones.
— Colecciones y documentos.
— Creación y borrado de colecciones; clases y métodos.
— Añadir, modificar y eliminar documentos; clases y métodos.
— Lenguajes de consulta. Realización de consultas; clases y métodos.
— Desarrollo de programas que utilizan bases de datos documentales.

Programación de componentes de acceso a datos:
— Concepto de componente; características. Ventajas.
— Propiedades y atributos.
— Eventos; asociación de acciones a eventos.
— Persistencia del componente. Serialización.
— Herramientas para desarrollo de componentes.
— Desarrollo, empaquetado y utilización de componentes.



mi propuesta es hacer un ejercicio de milla extra que utilice componentes de acceso a datos.

1) Idea general (tema)

"Gestor Multiformato de Inventario y Pedidos (GMIP)"
Una aplicación web para gestionar productos, proveedores y pedidos. Permite:

Gestión CRUD de productos y proveedores (MySQL + Doctrine ORM).

Registro de pedidos con transacciones y llamadas a procedimiento almacenado para procesar stock.

Export/import de datos en CSV, JSON, XML; conversión y parsing (binding).

Almacenamiento y consulta de documentos relacionados (logs, notas, attachments) en MongoDB (base documental).

Operaciones con ficheros: subida de imágenes, copias, movimientos, acceso secuencial y aleatorio a un fichero de logs binario.

Serialización/deserialización de objetos PHP (serialize / json_encode) y demostración de seguridad/excepciones.

Componente reutilizable de acceso a datos (DataAccessComponent) con eventos/hooks y persistencia del componente.

Interfaz agradable (TailwindCSS o Bootstrap) + SPA ligera (vanilla JS o Web Components) para demostrar componentes de UI.

Motivo: cubre casi todas las competencias del temario y es grande y demostrable en el examen.

2) Stack tecnológico y por qué (rápido)

Backend: PHP 8 (XAMPP / Plesk).

Relacional: MySQL/MariaDB (PDO + procedimientos almacenados).

ORM: Doctrine ORM (común en PHP, usa anotaciones y entidades).

Documental: MongoDB + extensión PHP (mongodb).

Frontend: HTML5 + Tailwind/Bootstrap + Web Components (para demostrar componentes).

Serialización / ficheros: funciones nativas PHP (fopen, fread, fwrite, fseek), SimpleXML, json_encode/decode, Serializable.

Composer para dependencias (doctrine/orm, mongodb/mongodb).

Tests/basicos: scripts de seed SQL/JSON para datos de prueba.

3) Funcionalidades detalladas y su vínculo con el temario (unidad/subunidad)

Voy a listar cada funcionalidad y la unidad/subunidad correspondiente (según el temario que diste).

CRUD Productos / Proveedores con Doctrine ORM

Unidad: Herramientas de mapeo objeto relacional (ORM).

Subunidades: Mapeo basado en anotaciones; Clases persistentes; Sesiones; Carga/almacenamiento/modificación; Gestión de transacciones con ORM.

Acceso a BD con PDO (conector)

Unidad: Manejo de conectores.

Subunidades: Establecimiento de conexiones; Pooling (persistencia de conexión en PHP); Ejecución de sentencias de descripción y modificación; Ejecución de consultas y manipulación del resultado; Gestión de transacciones.

Procedimiento almacenado para procesar pedidos (stock + logs)

Unidad: Manejo de conectores / Gestores objeto-relacionales.

Subunidades: Ejecución de procedimientos almacenados; Parámetros; Gestión de transacciones.

Import / Export en CSV, XML, JSON (parser + binding)

Unidad: Manejo de ficheros.

Subunidades: Trabajo con ficheros de intercambio (XML y JSON); Analizadores sintácticos (DOM / SimpleXML / json_decode); Conversión entre formatos; Flujos de caracteres.

Fichero binario de logs con acceso aleatorio

Unidad: Manejo de ficheros.

Subunidades: Formas de acceso a un fichero; Operaciones sobre ficheros secuenciales y aleatorios; Flujos de bytes.

Serialización / deserialización de objetos y seguridad

Unidad: Manejo de ficheros.

Subunidades: Serialización/deserialización; Excepciones: detección y tratamiento.

MongoDB: colecciones y documentos

Unidad: Bases de datos documentales nativas.

Subunidades: Establecimiento y cierre de conexiones; Colecciones y documentos; Crear/añadir/modificar/eliminar documentos; Lenguajes de consulta; Desarrollo de programas que usan BD documentales.

Componente DataAccessComponent (persistente + eventos)

Unidad: Programación de componentes de acceso a datos.

Subunidades: Concepto de componente; Eventos; Persistencia del componente (serialización); Desarrollo y empaquetado de componentes.

Interfaz web con Web Component para formulario reutilizable

Unidad: Programación de componentes de acceso a datos (componentes UI + asociación de acciones a eventos).

Gestión de errores y excepciones

Unidad: Manejo de ficheros / Manejo de conectores.

Subunidades: Excepciones: detección y tratamiento; Manejo de fallos en transacciones y rollback.

4) Estructura del proyecto (carpetas y archivos clave)
gmip/
├─ public/
│  ├─ index.php
│  ├─ assets/ (css, js)
│  └─ uploads/ (imagenes subidas)
├─ src/
│  ├─ Controller/
│  │   ├─ ProductController.php
│  │   └─ OrderController.php
│  ├─ Model/
│  │   ├─ Entity/
│  │   │   ├─ Product.php   <-- Doctrine entity (anotaciones)
│  │   │   └─ Provider.php
│  │   └─ DataAccessComponent.php  <-- componente reutilizable
│  ├─ Service/
│  │   ├─ ImportExportService.php
│  │   ├─ FileService.php
│  │   └─ MongoService.php
│  └─ bootstrap.php
├─ config/
│  ├─ doctrine.php
│  ├─ database.php
│  └─ mongo.php
├─ scripts/
│  ├─ seed.sql
│  └─ create_sp.sql
├─ composer.json
└─ README.md
