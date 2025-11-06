const API = './index.php'; // relativo a gmip/public â€” sin dominios hardcodeados
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
        healthEl.innerHTML = `<span class="ok">${esc(data.status)}</span> Â· ${esc(data.timestamp)} Â· ${esc(data.app)}`;
    } catch (e) {
        healthEl.innerHTML = `<span class="error">Error health (${e.status})</span>`;
    }
}

async function loadProducts() {
    productsError.textContent = '';
    productsBody.innerHTML = '<tr><td colspan="7" class="muted">Cargandoâ€¦</td></tr>';
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

function applyProductsFilter() {
    let rows = [...productsCache];
    const q = (prodSearch.value || '').toLowerCase();
    const pid = prodProviderFilter.value;
    const minP = prodPriceMin.value ? Number(prodPriceMin.value) : null;
    const maxP = prodPriceMax.value ? Number(prodPriceMax.value) : null;
    const onlyStock = prodStockOnly.checked;

    if (q) rows = rows.filter(p => String(p.name).toLowerCase().includes(q) || String(p.sku).toLowerCase().includes(q));
    if (pid) rows = rows.filter(p => String(p.providerId || '') === String(pid));
    if (minP != null) rows = rows.filter(p => Number(p.price) >= minP);
    if (maxP != null) rows = rows.filter(p => Number(p.price) <= maxP);
    if (onlyStock) rows = rows.filter(p => Number(p.stock) > 0);

    if (!rows.length) {
        productsBody.innerHTML = '<tr><td colspan="7" class="muted">Sin resultados</td></tr>';
        return;
    }
    productsBody.innerHTML = rows.map(p => `
        <tr>
          <td>${p.id}</td>
          <td>${esc(p.name)}</td>
          <td class="muted">${esc(p.sku)}</td>
          <td>${fmt(p.price)} €</td>
          <td>${p.stock}</td>
          <td>${p.providerId ?? '-'}</td>
          <td class="actions">
            <button class="editProd" data-id="${p.id}">Editar</button>
            <button class="delProd" data-id="${p.id}">Eliminar</button>
          </td>
        </tr>
        <tr class="details-row" data-for-prod="${p.id}" style="display:none">
          <td colspan="7">
            <div class="details" id="prod-edit-${p.id}"></div>
          </td>
        </tr>
      `).join('');
    attachProductRowEvents(rows);
}

async function loadProviders() {
    providersError.textContent = '';
    providersBody.innerHTML = '<tr><td colspan="6" class="muted">Cargandoâ€¦</td></tr>';
    try {
        const rows = await fetchJSON(`${API}?ruta=proveedores`);
        providersCache = Array.isArray(rows) ? rows : [];
        if (!providersCache.length) {
            providersBody.innerHTML = '<tr><td colspan="6" class="muted">Sin proveedores</td></tr>';
        } else {
            providersBody.innerHTML = providersCache.map(v => `
            <tr>
              <td>${v.id}</td>
              <td>${esc(v.name)}</td>
              <td class="muted">${esc(v.email)}</td>
              <td>${esc(v.phone)}</td>
              <td class="small muted">${esc(v.createdAt)}</td>
              <td class="actions">
                <button class="editProv" data-id="${v.id}">Editar</button>
                <button class="delProv" data-id="${v.id}">Eliminar</button>
              </td>
            </tr>
            <tr class="details-row" data-for-prov="${v.id}" style="display:none">
              <td colspan="6">
                <div class="details" id="prov-edit-${v.id}"></div>
              </td>
            </tr>
          `).join('');
        }
        // refrescar filtro de proveedores en productos
        const current = prodProviderFilter.value;
        prodProviderFilter.innerHTML = '<option value="">Todos proveedores</option>' + providersCache.map(v => `<option value="${v.id}">${esc(v.name)}</option>`).join('');
        if (current) prodProviderFilter.value = current;
        // refrescar selector de proveedor en creaciÃ³n de producto
        if (prodProviderSel) {
            const cur2 = prodProviderSel.value;
            prodProviderSel.innerHTML = '<option value="">Sin proveedor</option>' + providersCache.map(v => `<option value="${v.id}">${esc(v.name)}</option>`).join('');
            if (cur2) prodProviderSel.value = cur2;
        }
        if (statProviders) statProviders.textContent = String(providersCache.length);
        attachProviderRowEvents(providersCache);
    } catch (e) {
        providersBody.innerHTML = '';
        providersError.textContent = `Error proveedores (${e.status}): ${JSON.stringify(e.body)}`;
    }
}

async function createProvider() {
    provCreateError.textContent = '';
    provCreateOk.textContent = '';
    const name = (provName.value || '').trim();
    const email = (provEmail.value || '').trim();
    const phone = (provPhone.value || '').trim();
    if (!name || !email || !phone) {
        provCreateError.textContent = 'Nombre, Email y TelÃ©fono son obligatorios';
        return;
    }
    try {
        const res = await fetchJSON(`${API}?ruta=proveedores`, { method: 'POST', body: JSON.stringify({ name, email, phone }) });
        provCreateOk.textContent = `Proveedor creado: id ${res.id}`;
        provName.value = '';
        provEmail.value = '';
        provPhone.value = '';
        await loadProviders();
    } catch (e) {
        provCreateError.textContent = `Error crear (${e.status}): ${JSON.stringify(e.body)}`;
    }
}

async function loadOrders() {
    ordersError.textContent = '';
    ordersBody.innerHTML = '<tr><td colspan="5" class="muted">Cargando…</td></tr>';
    try {
        const rows = await fetchJSON(`${API}?ruta=pedidos`);
        ordersCache = Array.isArray(rows) ? rows : [];
        applyOrdersFilter();
        if (statOrders) statOrders.textContent = String(ordersCache.length);
    } catch (e) {
        ordersBody.innerHTML = '';
        ordersError.textContent = `Error pedidos (${e.status}): ${JSON.stringify(e.body)}`;
    }
}

function applyOrdersFilter() {
    let rows = [...ordersCache];
    const q = (orderCodeSearch.value || '').toLowerCase();
    const st = orderStatusFilter.value;
    if (q) rows = rows.filter(o => String(o.code).toLowerCase().includes(q));
    if (st) rows = rows.filter(o => String(o.status) === st);
    if (!rows.length) {
        ordersBody.innerHTML = '<tr><td colspan="5" class="muted">Sin resultados</td></tr>';
        return;
    }
    ordersBody.innerHTML = rows.map(o => renderOrderRow(o)).join('');
    attachOrderRowEvents(rows);
}

function renderOrderRow(o) {
    const badgeCls = o.status === 'processed' ? 'badge processed' : 'badge pending';
    const canProcess = o.status !== 'processed';
    return `
        <tr data-id="${o.id}">
          <td>${o.id}</td>
          <td>${esc(o.code)}</td>
          <td class="muted small">${esc(o.createdAt)}</td>
          <td><span class="${badgeCls}">${esc(o.status)}</span></td>
          <td class="actions">
            <button class="viewBtn">Ver</button>
            <button class="processBtn" ${canProcess ? '' : 'disabled'}>Procesar</button>
          </td>
        </tr>
        <tr class="details-row" data-for="${o.id}" style="display:none">
          <td colspan="5">
             <div class="details" id="details-${o.id}">Cargando detalles…</div>
          </td>
        </tr>
      `;
}

function attachOrderRowEvents(rows) {
    rows.forEach(o => {
        const tr = ordersBody.querySelector(`tr[data-id="${o.id}"]`);
        const viewBtn = tr.querySelector('.viewBtn');
        const procBtn = tr.querySelector('.processBtn');
        const detailsTr = ordersBody.querySelector(`tr.details-row[data-for="${o.id}"]`);
        const detailsDiv = document.getElementById(`details-${o.id}`);

        viewBtn.addEventListener('click', async () => {
            const visible = detailsTr.style.display !== 'none';
            detailsTr.style.display = visible ? 'none' : '';
            if (!visible) {
                try {
                    const full = await fetchJSON(`${API}?ruta=pedidos&id=${o.id}`);
                    const items = Array.isArray(full.items) && full.items.length
                        ? `<ul>${full.items.map(it => `<li>${esc(it.productName)} · qty ${it.quantity} · ${fmt(it.price)} €</li>`).join('')}</ul>`
                        : '<div class="muted">Sin líneas</div>';
                    detailsDiv.innerHTML = `<div><b>Pedido:</b> ${esc(full.code)}</div>${items}`;
                } catch (e) {
                    detailsDiv.innerHTML = `<div class="error">Error detalles (${e.status})</div>`;
                }
            }
        });

        procBtn.addEventListener('click', async () => {
            procBtn.disabled = true;
            try {
                const res = await fetchJSON(`${API}?ruta=procesar-pedido&id=${o.id}`, { method: 'POST' });
                tr.querySelector('td:nth-child(4)').innerHTML = `<span class="badge processed">${esc(res.status)}</span>`;
            } catch (e) {
                 alert(`Error al procesar: ${e.status} → ${JSON.stringify(e.body)}`);
                procBtn.disabled = false;
            }
        });
    });
}

function attachProductRowEvents(rows) {
    rows.forEach(p => {
        const editBtn = productsBody.querySelector(`button.editProd[data-id='${p.id}']`);
        const delBtn = productsBody.querySelector(`button.delProd[data-id='${p.id}']`);
        const detailsTr = productsBody.querySelector(`tr.details-row[data-for-prod='${p.id}']`);
        const detailsDiv = document.getElementById(`prod-edit-${p.id}`);
        if (editBtn) {
            editBtn.addEventListener('click', () => {
                const visible = detailsTr.style.display !== 'none';
                detailsTr.style.display = visible ? 'none' : '';
                if (!visible) {
                    renderProductEditor(detailsDiv, p);
                }
            });
        }
        if (delBtn) {
            delBtn.addEventListener('click', async () => {
                if (!confirm(`Eliminar producto ${p.name}?`)) return;
                try {
                    await fetchJSON(`${API}?ruta=productos&id=${p.id}`, { method: 'DELETE' });
                    await loadProducts();
                    refreshItemEditors();
                } catch (e) {
                    alert(`Error eliminar: ${e.status} â†’ ${JSON.stringify(e.body)}`);
                }
            });
        }
    });
}

function renderProductEditor(container, p) {
    container.innerHTML = `
        <div class="grid-col">
          <div class="row">
            <input id="ep-name" value="${esc(p.name)}" placeholder="Nombre" />
            <input id="ep-sku" value="${esc(p.sku)}" placeholder="SKU" />
            <input id="ep-price" type="number" min="0" step="0.01" value="${Number(p.price)}" style="width:120px" />
            <input id="ep-stock" type="number" min="0" step="1" value="${Number(p.stock)}" style="width:100px" />
            <select id="ep-prov"><option value="">Sin proveedor</option>${providersCache.map(v => `<option value='${v.id}' ${String(p.providerId || '') === String(v.id) ? 'selected' : ''}>${esc(v.name)}</option>`).join('')}</select>
            <button id="ep-save" class="btn-primary">Guardar</button>
          </div>
          <div id="ep-error" class="error"></div>
          <div id="ep-ok" class="ok"></div>
        </div>
      `;
    const epName = container.querySelector('#ep-name');
    const epSku = container.querySelector('#ep-sku');
    const epPrice = container.querySelector('#ep-price');
    const epStock = container.querySelector('#ep-stock');
    const epProv = container.querySelector('#ep-prov');
    const epError = container.querySelector('#ep-error');
    const epOk = container.querySelector('#ep-ok');
    const saveBtn = container.querySelector('#ep-save');
    saveBtn.addEventListener('click', async () => {
        epError.textContent = '';
        epOk.textContent = '';
        const name = epName.value.trim();
        const sku = epSku.value.trim();
        const price = Number(epPrice.value);
        const stock = Number(epStock.value);
        const providerId = epProv.value ? Number(epProv.value) : null;
        if (!name || !sku || !(price >= 0) || !(stock >= 0)) {
            epError.textContent = 'Campos invÃ¡lidos';
            return;
        }
        try {
            await fetchJSON(`${API}?ruta=productos&id=${p.id}`, { method: 'PUT', body: JSON.stringify({ name, sku, price, stock, providerId }) });
            epOk.textContent = 'Guardado';
            await loadProducts();
        } catch (e) {
            epError.textContent = `Error guardar (${e.status}): ${JSON.stringify(e.body)}`;
        }
    });
}

function attachProviderRowEvents(rows) {
    rows.forEach(v => {
        const editBtn = providersBody.querySelector(`button.editProv[data-id='${v.id}']`);
        const delBtn = providersBody.querySelector(`button.delProv[data-id='${v.id}']`);
        const detailsTr = providersBody.querySelector(`tr.details-row[data-for-prov='${v.id}']`);
        const detailsDiv = document.getElementById(`prov-edit-${v.id}`);
        if (editBtn) {
            editBtn.addEventListener('click', () => {
                const visible = detailsTr.style.display !== 'none';
                detailsTr.style.display = visible ? 'none' : '';
                if (!visible) {
                    renderProviderEditor(detailsDiv, v);
                }
            });
        }
        if (delBtn) {
            delBtn.addEventListener('click', async () => {
                if (!confirm(`Eliminar proveedor ${v.name}?`)) return;
                try {
                    await fetchJSON(`${API}?ruta=proveedores&id=${v.id}`, { method: 'DELETE' });
                    await loadProviders();
                } catch (e) {
                    alert(`Error eliminar: ${e.status} â†’ ${JSON.stringify(e.body)}`);
                }
            });
        }
    });
}

function renderProviderEditor(container, v) {
    container.innerHTML = `
        <div class="grid-col">
          <div class="row">
            <input id="ev-name" value="${esc(v.name)}" placeholder="Nombre" />
            <input id="ev-email" value="${esc(v.email)}" placeholder="Email" />
            <input id="ev-phone" value="${esc(v.phone)}" placeholder="TelÃ©fono" />
            <button id="ev-save" class="btn-primary">Guardar</button>
          </div>
          <div id="ev-error" class="error"></div>
          <div id="ev-ok" class="ok"></div>
        </div>
      `;
    const evName = container.querySelector('#ev-name');
    const evEmail = container.querySelector('#ev-email');
    const evPhone = container.querySelector('#ev-phone');
    const evError = container.querySelector('#ev-error');
    const evOk = container.querySelector('#ev-ok');
    const saveBtn = container.querySelector('#ev-save');
    saveBtn.addEventListener('click', async () => {
        evError.textContent = '';
        evOk.textContent = '';
        const name = evName.value.trim();
        const email = evEmail.value.trim();
        const phone = evPhone.value.trim();
        if (!name || !email || !phone) {
            evError.textContent = 'Nombre, Email y TelÃ©fono son obligatorios';
            return;
        }
        try {
            await fetchJSON(`${API}?ruta=proveedores&id=${v.id}`, { method: 'PUT', body: JSON.stringify({ name, email, phone }) });
            evOk.textContent = 'Guardado';
            await loadProviders();
        } catch (e) {
            evError.textContent = `Error guardar (${e.status}): ${JSON.stringify(e.body)}`;
        }
    });
}

function refreshItemEditors() {
    // regenerar selects existentes con catÃ¡logo mÃ¡s reciente
    const selects = itemsContainer.querySelectorAll('select[data-role="productSel"]');
    selects.forEach(sel => {
        const current = sel.value;
        sel.innerHTML = productsCache.map(p => `<option value="${p.id}">${esc(p.name)} (SKU ${esc(p.sku)})</option>`).join('');
        if (current) sel.value = current;
    });
}

function addItemRow() {
    const id = `it-${Date.now()}-${Math.floor(Math.random() * 999)}`;
    const row = document.createElement('div');
    row.className = 'row order-item-row';
    row.style.cssText = `
        margin-bottom: 12px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-start;
    `;
    row.innerHTML = `
        <div class="item-fields" style="display: flex; flex-direction: column; min-width: 200px;">
            <label style="font-weight: bold; margin-bottom: 4px;">Producto:</label>
            <select id="${id}-p" data-role="productSel"></select>
        </div>
        <div class="item-fields" style="display: flex; flex-direction: column; min-width: 100px;">
            <label style="font-weight: bold; margin-bottom: 4px;">Cantidad:</label>
            <input id="${id}-q" type="number" min="1" value="1" style="width:90px" />
        </div>
        <div class="item-fields" style="display: flex; flex-direction: column; min-width: 150px;">
            <label style="font-weight: bold; margin-bottom: 4px;">Precio (€):</label>
            <input id="${id}-pr" type="number" min="0" step="0.01" placeholder="Precio (auto)" style="width:140px" />
        </div>
        <div class="item-info" style="display: flex; flex-direction: column; min-width: 200px; margin-top: 20px;">
            <span id="${id}-stock" class="muted small">Stock disponible: -</span>
            <span id="${id}-price-info" class="muted small">Precio sugerido: - €</span>
        </div>
        <button class="btn-danger" id="${id}-rm" style="align-self: flex-end; margin-top: 24px;">Eliminar</button>
      `;
    itemsContainer.appendChild(row);

    // inicializar opciones
    const sel = row.querySelector(`#${id}-p`);
    sel.innerHTML = productsCache.map(p => `<option value="${p.id}">${esc(p.name)} (SKU ${esc(p.sku)})</option>`).join('');
    const stockSpan = row.querySelector(`#${id}-stock`);
    const priceInfoSpan = row.querySelector(`#${id}-price-info`);
    const priceInput = row.querySelector(`#${id}-pr`);

    sel.addEventListener('change', () => {
        const p = productsCache.find(x => String(x.id) === sel.value);
        if (p) {
            priceInput.value = Number(p.price);
            stockSpan.textContent = `Stock disponible: ${p.stock}`;
            priceInfoSpan.textContent = `Precio sugerido: ${fmt(p.price)} €`;
        } else {
            stockSpan.textContent = 'Stock disponible: -';
            priceInfoSpan.textContent = 'Precio sugerido: - €';
        }
    });
    // set precio inicial
    sel.dispatchEvent(new Event('change'));

    // eliminar
    row.querySelector(`#${id}-rm`).addEventListener('click', () => {
        row.remove();
    });
}

async function createOrder() {
    createError.textContent = '';
    createOk.textContent = '';
    const items = [];
    itemsContainer.querySelectorAll('.row').forEach(row => {
        const sel = row.querySelector('select');
        const qty = row.querySelector('input[type="number"][id$="-q"]');
        const pri = row.querySelector('input[type="number"][id$="-pr"]');
        const productId = Number(sel.value);
        const quantity = Number(qty.value);
        const price = Number(pri.value);
        if (productId && quantity > 0 && price >= 0) {
            items.push({ productId, quantity, price });
        }
    });
    if (!items.length) {
        createError.textContent = 'Añade al menos una línea válida';
        return;
    }
    const payload = { items };
    const code = codeInput.value.trim();
    if (code) payload.code = code;
    try {
        const res = await fetchJSON(`${API}?ruta=pedidos`, { method: 'POST', body: JSON.stringify(payload) });
        createOk.textContent = `Pedido creado: id ${res.id}, code ${res.code}`;
        codeInput.value = '';
        itemsContainer.innerHTML = '';
        addItemRow();
        await loadOrders();
    } catch (e) {
        createError.textContent = `Error crear (${e.status}): ${JSON.stringify(e.body)}`;
    }
}

async function createProduct() {
    prodCreateError.textContent = '';
    prodCreateOk.textContent = '';
    const name = (prodName.value || '').trim();
    const sku = (prodSku.value || '').trim();
    const price = prodPrice.value ? Number(prodPrice.value) : null;
    const stock = prodStock.value ? Number(prodStock.value) : null;
    const providerId = prodProviderSel && prodProviderSel.value ? Number(prodProviderSel.value) : null;
    if (!name || !sku || price == null || price < 0 || stock == null || stock < 0) {
        prodCreateError.textContent = 'Nombre, SKU, Precio (>=0) y Stock (>=0) son obligatorios';
        return;
    }
    try {
        const res = await fetchJSON(`${API}?ruta=productos`, { method: 'POST', body: JSON.stringify({ name, sku, price, stock, providerId }) });
        prodCreateOk.textContent = `Producto creado: id ${res.id}`;
        prodName.value = '';
        prodSku.value = '';
        prodPrice.value = '';
        prodStock.value = '';
        if (prodProviderSel) prodProviderSel.value = '';
        await loadProducts();
        refreshItemEditors();
    } catch (e) {
        prodCreateError.textContent = `Error crear (${e.status}): ${JSON.stringify(e.body)}`;
    }
}

document.getElementById('refreshProducts').addEventListener('click', loadProducts);
document.getElementById('refreshOrders').addEventListener('click', loadOrders);
document.getElementById('refreshProviders').addEventListener('click', loadProviders);
document.getElementById('refreshAll').addEventListener('click', () => { loadHealth(); loadProducts(); loadOrders(); loadProviders(); });
addItemBtn.addEventListener('click', addItemRow);
createBtn.addEventListener('click', createOrder);
document.getElementById('createProvider').addEventListener('click', createProvider);
document.getElementById('createProduct').addEventListener('click', createProduct);

// navegaciÃ³n entre vistas
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