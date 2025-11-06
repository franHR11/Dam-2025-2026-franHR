# Explicación del Ejercicio - La Milla Extra: Sistema de Gestión de Inventario y Pedidos (GMIP)

## Introducción breve y contextualización - 25% de la nota del ejercicio

En este ejercicio de la milla extra para la asignatura de Acceso a Datos, decidí crear un sistema completo de gestión de inventario y pedidos llamado GMIP. Este sistema permite manejar proveedores, productos, pedidos y controlar el stock automáticamente. Lo hice con PHP usando PDO para conectarme a MySQL, siguiendo un patrón MVC básico con controladores, modelos y servicios. El sistema incluye procedimientos almacenados para procesar pedidos y actualizar inventarios, además de un componente de acceso a datos reutilizable. Pertenece principalmente a la Unidad 3 (Programación con bases de datos) y Unidad 4 (Procedimientos almacenados y triggers), pero también toca conceptos de la Unidad 1 (Modelado de datos) y Unidad 2 (Lenguajes de consulta SQL).

## Desarrollo detallado y preciso - 25% de la nota del ejercicio

El sistema GMIP está diseñado para gestionar un inventario de productos suministrados por proveedores y procesar pedidos que afectan el stock. Utiliza una base de datos relacional MySQL con tablas relacionadas: providers (proveedores), products (productos con clave foránea a providers), orders (pedidos), order_items (líneas de pedido con FK a orders y products), y logs (registro de operaciones).

La conexión a la base de datos se realiza mediante PDO con configuración centralizada en un archivo .env, usando prepared statements para evitar inyecciones SQL. Incluye un DataAccessComponent que encapsula las operaciones CRUD con eventos para logging y validaciones.

Para el procesamiento de pedidos, implementé un procedimiento almacenado sp_process_order que usa cursores para iterar sobre los items del pedido, actualiza el stock de cada producto y marca el pedido como procesado. También registra logs de las operaciones.

El código sigue buenas prácticas como manejo de excepciones, transacciones implícitas en el SP, y separación de responsabilidades en capas (Controller para lógica HTTP, Model para acceso a datos, Service para lógica de negocio).

## Aplicación práctica - 25% de la nota del ejercicio

Para mostrar cómo funciona el sistema, creé una API REST en PHP que permite gestionar proveedores, productos y pedidos. Aquí está el código principal del DataAccessComponent que uso para todas las consultas:

```php
<?php

declare(strict_types=1);

namespace GMIP\Model;

use PDO;

/**
 * Componente reutilizable de acceso a datos con eventos y persistencia.
 */
class DataAccessComponent
{
    private ?PDO $pdo = null;
    private array $events = [];
    private array $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function withPdo(PDO $pdo): self
    {
        $this->pdo = $pdo;
        return $this;
    }

    public function on(string $event, callable $handler): void
    {
        $this->events[$event][] = $handler;
    }

    private function emit(string $event, array $payload = []): void
    {
        if (!empty($this->events[$event])) {
            foreach ($this->events[$event] as $h) {
                try {
                    $h($payload);
                } catch (\Throwable $e) { /* no-op */
                }
            }
        }
    }

    public function query(string $sql, array $params = []): array
    {
        if (!$this->pdo) throw new \RuntimeException('PDO no inicializado');
        $this->emit('beforeQuery', ['sql' => $sql, 'params' => $params]);
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->emit('afterQuery', ['sql' => $sql, 'params' => $params, 'result' => $result]);
            return $result;
        } catch (\Throwable $e) {
            $this->emit('queryError', ['sql' => $sql, 'params' => $params, 'error' => $e]);
            throw $e;
        }
    }

    public function execute(string $sql, array $params = []): int
    {
        if (!$this->pdo) throw new \RuntimeException('PDO no inicializado');
        $this->emit('beforeExecute', ['sql' => $sql, 'params' => $params]);
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $affected = $stmt->rowCount();
            $this->emit('afterExecute', ['sql' => $sql, 'params' => $params, 'affected' => $affected]);
            return $affected;
        } catch (\Throwable $e) {
            $this->emit('executeError', ['sql' => $sql, 'params' => $params, 'error' => $e]);
            throw $e;
        }
    }

    public function transaction(callable $callback): mixed
    {
        if (!$this->pdo) throw new \RuntimeException('PDO no inicializado');
        $this->pdo->beginTransaction();
        try {
            $result = $callback($this);
            $this->pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
```

También implementé el frontend con HTML para la estructura de la interfaz:

```html
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GMIP • Demo UI</title>
  <link rel="stylesheet" href="./assets/app.css" />
</head>

<body>
  <div class="layout">
    <aside class="sidebar">
      <div class="brand">GMIP Admin</div>
      <div class="nav">
        <button class="active" data-view="home">Home</button>
        <button data-view="orders">Pedidos</button>
        <button data-view="providers">Proveedores</button>
        <button data-view="products">Productos</button>
      </div>
    </aside>
    <div class="content">
      <header>
        <h1>GMIP • Demo UI</h1>
        <span id="health" class="status">Cargando estado…</span>
        <div style="margin-left:auto" class="row small">
          <button id="refreshAll" class="btn-primary">Recargar datos</button>
        </div>
      </header>

      <main>
        <section id="view-home" class="view active">
          <div class="panel">
            <h2>Resumen</h2>
            <div class="grid-col">
              <div class="row small">
                <span class="muted">Origen API: <code id="apiOrigin"></code></span>
              </div>
              <div id="homeStats" class="row" style="gap:24px">
                <div>
                  <div class="muted small">Productos</div>
                  <div id="statProducts" class="ok">-</div>
                </div>
                <div>
                  <div class="muted small">Proveedores</div>
                  <div id="statProviders" class="ok">-</div>
                </div>
                <div>
                  <div class="muted small">Pedidos</div>
                  <div id="statOrders" class="ok">-</div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <section id="view-products" class="view panel">
          <h2>Productos</h2>
          <div class="row small">
            <button id="refreshProducts">Actualizar</button>
          </div>
          <div class="row small" id="prodFilters">
            <input id="prodSearch" placeholder="Buscar nombre/sku" />
            <select id="prodProviderFilter">
              <option value="">Todos proveedores</option>
            </select>
            <input id="prodPriceMin" type="number" min="0" step="0.01" placeholder="Precio min" style="width:120px" />
            <input id="prodPriceMax" type="number" min="0" step="0.01" placeholder="Precio max" style="width:120px" />
            <label class="small"><input id="prodStockOnly" type="checkbox" /> Stock > 0</label>
          </div>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>SKU</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Proveedor</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="productsBody"></tbody>
          </table>
          <div id="productsError" class="error"></div>
          <div class="grid-col" style="margin-top:12px">
            <div class="small muted">Crear producto</div>
            <div class="row">
              <input id="prodName" placeholder="Nombre" />
              <input id="prodSku" placeholder="SKU" />
              <input id="prodPrice" type="number" min="0" step="0.01" placeholder="Precio" style="width:120px" />
              <input id="prodStock" type="number" min="0" step="1" placeholder="Stock" style="width:100px" />
              <select id="prodProviderSel">
                <option value="">Sin proveedor</option>
              </select>
              <button id="createProduct" class="btn-primary">Crear</button>
            </div>
            <div id="prodCreateError" class="error"></div>
            <div id="prodCreateOk" class="ok"></div>
          </div>
        </section>

        <section id="view-providers" class="view panel">
          <h2>Proveedores</h2>
          <div class="row small">
            <button id="refreshProviders">Actualizar</button>
          </div>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Creado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="providersBody"></tbody>
          </table>
          <div id="providersError" class="error"></div>
          <div class="grid-col">
            <div class="small muted">Crear proveedor rápido</div>
            <div class="row">
              <input id="provName" placeholder="Nombre" />
              <input id="provEmail" placeholder="Email" />
              <input id="provPhone" placeholder="Teléfono" />
              <button id="createProvider" class="btn-primary">Crear</button>
            </div>
            <div id="provCreateError" class="error"></div>
            <div id="provCreateOk" class="ok"></div>
          </div>
        </section>

        <section id="view-orders" class="view panel">
          <h2>Pedidos</h2>
          <div class="row small">
            <button id="refreshOrders">Actualizar</button>
          </div>
          <div class="row small" id="ordFilters">
            <input id="orderCodeSearch" placeholder="Buscar código" />
            <select id="orderStatusFilter">
              <option value="">Todos estados</option>
              <option value="pending">pending</option>
              <option value="processed">processed</option>
            </select>
          </div>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Creado</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="ordersBody"></tbody>
          </table>
          <div id="ordersError" class="error"></div>
        </section>

        <section id="create-order-panel" class="view panel grid-col" style="grid-column: 1 / span 2;">
          <h2>Crear pedido rápido</h2>
          <div class="small muted">Completa las líneas y pulsa “Crear pedido”. El código es opcional.</div>
          <div class="row">
            <label for="code">Código:</label>
            <input id="code" placeholder="ORD-..." />
          </div>
          <div id="orderItems"></div>
          <div class="row">
            <button id="addItem">Añadir línea</button>
            <button id="createOrder" class="btn-primary">Crear pedido</button>
          </div>
          <div id="createError" class="error"></div>
          <div id="createOk" class="ok"></div>
        </section>
      </main>
    </div>
  </div>

  <script src="./assets/app.js" defer></script>
</body>

</html>
```

Y el JavaScript para la funcionalidad:

```javascript
const API = './index.php'; // relativo a gmip/public — sin dominios hardcodeados
const apiOriginEl = document.getElementById('apiOrigin');
const healthEl = document.getElementById('health');
const productsBody = document.getElementById('productsBody');
const ordersBody = document.getElementById('ordersBody');
const productsError = document.getElementById('productsError');
const ordersError = document.getElementById('ordersError');
const providersBody = document.getElementById('providersBody');
const providersError = document.getElementById('providersError');
const addItemBtn = document.getElementById('addItem');
const createBtn = document.getElementById('createOrder');
const codeInput = document.getElementById('code');
const itemsContainer = document.getElementById('orderItems');
const createError = document.getElementById('createError');
const createOk = document.getElementById('createOk');
const provName = document.getElementById('provName');
const provEmail = document.getElementById('provEmail');
const provPhone = document.getElementById('provPhone');
const provCreateError = document.getElementById('provCreateError');
const provCreateOk = document.getElementById('provCreateOk');
const prodName = document.getElementById('prodName');
const prodSku = document.getElementById('prodSku');
const prodPrice = document.getElementById('prodPrice');
const prodStock = document.getElementById('prodStock');
const prodProviderSel = document.getElementById('prodProviderSel');
const prodCreateError = document.getElementById('prodCreateError');
const prodCreateOk = document.getElementById('prodCreateOk');
const prodSearch = document.getElementById('prodSearch');
const prodProviderFilter = document.getElementById('prodProviderFilter');
const prodPriceMin = document.getElementById('prodPriceMin');
const prodPriceMax = document.getElementById('prodPriceMax');
const prodStockOnly = document.getElementById('prodStockOnly');
const orderCodeSearch = document.getElementById('orderCodeSearch');
const orderStatusFilter = document.getElementById('orderStatusFilter');
const statProducts = document.getElementById('statProducts');
const statProviders = document.getElementById('statProviders');
const statOrders = document.getElementById('statOrders');
const navButtons = document.querySelectorAll('.nav button');

let productsCache = [];
let providersCache = [];
let ordersCache = [];

function fmt(n) { return Number(n).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
function esc(s) { return String(s).replace(/[&<>"]+/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c])); }

async function fetchJSON(url, options) {
    const res = await fetch(url, Object.assign({ headers: { 'Content-Type': 'application/json' } }, options || {}));
    const text = await res.text();
    let json;
    try { json = JSON.parse(text); } catch { json = { error: 'Respuesta no JSON', raw: text }; }
    if (!res.ok) throw { status: res.status, body: json };
    return json;
}

async function loadHealth() {
    apiOriginEl.textContent = window.location.origin;
    try {
        const data = await fetchJSON(`${API}?ruta=health`);
        healthEl.innerHTML = `<span class="ok">${esc(data.status)}</span> · ${esc(data.timestamp)} · ${esc(data.app)}`;
    } catch (e) {
        healthEl.innerHTML = `<span class="error">Error health (${e.status})</span>`;
    }
}

async function loadProducts() {
    productsError.textContent = '';
    productsBody.innerHTML = '<tr><td colspan="7" class="muted">Cargando…</td></tr>';
    try {
        const rows = await fetchJSON(`${API}?ruta=productos`);
        productsCache = Array.isArray(rows) ? rows : [];
        applyProductsFilter();
        refreshItemEditors();
        if (statProducts) statProducts.textContent = String(productsCache.length);
    } catch (e) {
        productsBody.innerHTML = '';
        productsError.textContent = `Error productos (${e.status}): ${JSON.stringify(e.body)}`;
    }
}

// [Código JS completo truncado para brevedad, pero incluido en el archivo real]

document.getElementById('refreshProducts').addEventListener('click', loadProducts);
document.getElementById('refreshOrders').addEventListener('click', loadOrders);
document.getElementById('refreshProviders').addEventListener('click', loadProviders);
document.getElementById('refreshAll').addEventListener('click', () => { loadHealth(); loadProducts(); loadOrders(); loadProviders(); });
addItemBtn.addEventListener('click', addItemRow);
createBtn.addEventListener('click', createOrder);
document.getElementById('createProvider').addEventListener('click', createProvider);
document.getElementById('createProduct').addEventListener('click', createProduct);

// navegación entre vistas
navButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        navButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const target = btn.getAttribute('data-view');
        document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
        if (target === 'orders') {
            document.getElementById('view-orders').classList.add('active');
            document.getElementById('create-order-panel').classList.add('active');
        } else if (target === 'providers') {
            document.getElementById('view-providers').classList.add('active');
        } else if (target === 'products') {
            document.getElementById('view-products').classList.add('active');
        } else {
            document.getElementById('view-home').classList.add('active');
        }
    });
});

// filtros
prodSearch.addEventListener('input', applyProductsFilter);
prodProviderFilter.addEventListener('change', applyProductsFilter);
prodPriceMin.addEventListener('input', applyProductsFilter);
prodPriceMax.addEventListener('input', applyProductsFilter);
prodStockOnly.addEventListener('change', applyProductsFilter);
orderCodeSearch.addEventListener('input', applyOrdersFilter);
orderStatusFilter.addEventListener('change', applyOrdersFilter);

// init
loadHealth();
loadProviders().then(() => loadProducts().then(() => addItemRow()));
loadOrders();
```

Y el CSS para el estilo de la interfaz:

```css
:root {
    --bg: #0f172a;
    --panel: #111827;
    --text: #e5e7eb;
    --muted: #9ca3af;
    --accent: #3b82f6;
    --danger: #ef4444;
    --ok: #10b981;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, "Fira Sans", "Droid Sans", "Helvetica Neue", Arial, "Noto Sans", sans-serif;
    background: var(--bg);
    color: var(--text);
}

header {
    padding: 16px 20px;
    border-bottom: 1px solid #1f2937;
    display: flex;
    align-items: center;
    gap: 16px;
}

header h1 {
    margin: 0;
    font-size: 20px;
}

.status {
    font-size: 12px;
    color: var(--muted);
}

main {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    padding: 16px;
}

.panel {
    background: var(--panel);
    border: 1px solid #1f2937;
    border-radius: 8px;
    padding: 12px;
}

.panel h2 {
    margin: 0 0 12px 0;
    font-size: 16px;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

th,
td {
    border-bottom: 1px solid #1f2937;
    padding: 8px;
    text-align: left;
    vertical-align: top;
}

th {
    color: var(--muted);
    font-weight: 600;
}

.actions {
    display: flex;
    gap: 8px;
}

button,
select,
input {
    background: #0b1220;
    border: 1px solid #1f2937;
    color: var(--text);
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 14px;
}

button:hover {
    border-color: #334155;
    cursor: pointer;
}

.btn-primary {
    background: #0b2a5b;
    border-color: #1d4ed8;
}

.btn-danger {
    background: #3a0d0d;
    border-color: #7f1d1d;
}

.muted {
    color: var(--muted);
}

.ok {
    color: var(--ok);
}

.error {
    color: var(--danger);
    white-space: pre-wrap;
}

.grid-col {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}

.row {
    display: flex;
    gap: 8px;
    align-items: center;
}

.small {
    font-size: 12px;
}

.badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 12px;
    border: 1px solid #374151;
}

.badge.pending {
    color: #f59e0b;
    border-color: #6b4d1b;
}

.badge.processed {
    color: var(--ok);
    border-color: #14532d;
}

.details {
    background: #0b1220;
    border: 1px dashed #374151;
    border-radius: 6px;
    padding: 8px;
    margin-top: 6px;
}

/* Admin layout */
.layout {
    display: grid;
    grid-template-columns: 240px 1fr;
    min-height: 100vh;
}

.sidebar {
    background: #0b1220;
    border-right: 1px solid #1f2937;
    padding: 16px 12px;
}

.brand {
    font-weight: 700;
    margin-bottom: 12px;
}

.nav {
    display: grid;
    gap: 6px;
}

.nav button {
    text-align: left;
    padding: 8px 10px;
    border-radius: 6px;
    border: 1px solid transparent;
    background: transparent;
    color: var(--text);
}

.nav button:hover {
    background: #0f1a30;
    border-color: #1f2937;
    cursor: pointer;
}

.nav button.active {
    background: #0b2a5b;
    border-color: #1d4ed8;
}

.content main {
    grid-template-columns: 1fr;
}

.view {
    display: none;
}

.view.active {
    display: block;
}
```

Este componente permite ejecutar consultas seguras, manejar transacciones y emitir eventos. Los controladores lo usan para operaciones CRUD, y el procedimiento almacenado maneja la lógica compleja de procesamiento de pedidos.

Errores comunes: olvidar validar inputs antes de consultas, no usar transacciones para operaciones multi-tabla, o no manejar excepciones PDO.

## Conclusión breve - 25% de la nota del ejercicio

En resumen, este ejercicio me permitió aplicar conocimientos avanzados de acceso a datos como procedimientos almacenados con cursores, componentes de acceso a datos reutilizables, y manejo de transacciones. Me sirvió para entender cómo integrar PHP con MySQL de forma segura y eficiente. Esto se conecta con lo visto en la Unidad 3 sobre programación con bases de datos y la Unidad 4 sobre procedimientos almacenados, además de reforzar conceptos de la Unidad 2 sobre joins y constraints.
