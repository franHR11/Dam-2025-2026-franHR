# GMIP – Gestor Multiformato de Inventario y Pedidos

Aplicación educativa que cubre manejo de ficheros, conectores BD, ORM, bases documentales y componentes de acceso a datos. Diseñada para ser defendida en examen: funciona con configuración vía `.env` y sin URLs/credenciales hardcodeadas.

## Estructura

```
gmip/
├─ public/               # Punto de entrada API (JSON)
├─ src/                  # Código fuente (componentes, entidades y servicios)
├─ config/               # Carga y validación de entorno + conexiones
├─ scripts/              # SQL de semillas y SP
├─ composer.json         # Dependencias (Doctrine y Mongo)
└─ .env.example          # Ejemplo de variables
```

## Variables de entorno (.env)

Copiar y renombrar `.env.example` a `.env` y ajustar valores:

```
GMIP_DB_HOST=localhost
GMIP_DB_NAME=gmip
GMIP_DB_USER=usuario
GMIP_DB_PASS=contraseña
GMIP_DB_CHARSET=utf8mb4

GMIP_MONGO_URI=mongodb://localhost:27017
GMIP_MONGO_DB=gmip

GMIP_APP_BASE_URL=http://gmip.test
GMIP_CORS_ALLOW_ORIGIN=http://gmip.test
```

## Puesta en marcha (Laragon)

- Directorio raíz servido: `gmip/public`.
- Endpoint de salud: `GET /index.php?ruta=health`.
- Chequeo configuración: `GET /index.php?ruta=config-check`.

## Scripts SQL

- `scripts/seed.sql`: crea tablas y datos mínimos.
- `scripts/create_sp.sql`: procedimiento `sp_process_order` (stock + log).

## Cobertura del temario

- Manejo de ficheros: `src/Service/FileService.php` (secuencial/aleatorio, binario), `ImportExportService` (CSV/JSON/XML, parsing y binding).
- Conectores: `config/database.php` (PDO; transacciones a través del componente), procedimientos almacenados.
- ORM (pendiente de instalación): entidades en `src/Model/Entity`, preparadas para anotaciones/attributes con Doctrine.
- Base documental: `src/Service/MongoService.php` con `config/mongo.php`.
- Componentes: `src/Model/DataAccessComponent.php` (eventos hooks y persistencia por serialización).
- Errores y excepciones: try/catch y mensajes descriptivos en todas las operaciones críticas.

## Dependencias (Composer)

Si se desea instalar ORM y cliente MongoDB:

```
composer install
```

> Nota: puedes usar el autoloader propio (sin Composer) para defender el MVP y añadir Composer después si el entorno del examen lo permite.

## Demostración en examen (guía rápida)

1. Mostrar `health` y `config-check` funcionando.
2. Ejecutar `seed.sql` y enseñar tablas pobladas.
3. Insertar un pedido y ejecutar `sp_process_order`; comprobar stock y `logs`.
4. Exportar `products` a CSV/XML/JSON; importar desde CSV.
5. Escribir y leer un log binario con offsets.
6. Insertar y consultar documentos en Mongo (si librería disponible).
7. Explicar el componente `DataAccessComponent` y sus eventos.

## Calidad y seguridad

- Configuración centralizada en `config/env.php`; nada hardcodeado.
- Validación de variables y formatos; errores descriptivos.
- Código limpio, nombres consistentes y responsabilidades únicas.