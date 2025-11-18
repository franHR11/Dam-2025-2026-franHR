class PresupuestosPage {
  constructor() {
    this.presupuestos = [];
    this.clientes = [];
    this.productos = [];
    this.presupuestoActual = null;
    this.lineas = [];
    this.elementos = {};
    this.modales = {};

    document.addEventListener("DOMContentLoaded", () => this.init());
  }

  async init() {
    this.cargarElementos();
    this.configurarEventos();

    try {
      await Promise.all([this.cargarClientes(), this.cargarProductos()]);
      await this.cargarPresupuestos();
      this.renderTabla();
    } catch (error) {
      console.error(error);
      this.mostrarAlerta(
        "Error al cargar la información inicial. Recarga la página",
        "danger"
      );
    }
  }

  cargarElementos() {
    this.elementos = {
      tbody: document.getElementById("presupuestos-tbody"),
      noRows: document.getElementById("no-presupuestos"),
      filtroEstado: document.getElementById("filtro-estado"),
      filtroCliente: document.getElementById("filtro-cliente"),
      buscador: document.getElementById("buscar-presupuesto"),
      form: document.getElementById("form-presupuesto"),
      lineasTbody: document.getElementById("lineas-tbody"),
      totalBase: document.getElementById("total-base"),
      totalDescuento: document.getElementById("total-descuento"),
      totalIva: document.getElementById("total-iva"),
      totalGeneral: document.getElementById("total-general"),
      numeroPresupuesto: document.getElementById("numero_presupuesto"),
      clienteSelect: document.getElementById("cliente_id"),
      estadoSelect: document.getElementById("estado"),
      fechaInput: document.getElementById("fecha"),
      fechaValidoInput: document.getElementById("fecha_valido_hasta"),
      ejercicioInput: document.getElementById("ejercicio"),
      monedaInput: document.getElementById("moneda"),
      formaPagoSelect: document.getElementById("forma_pago"),
      plazoEntregaInput: document.getElementById("plazo_entrega"),
      garantiaInput: document.getElementById("garantia"),
      terminosTextarea: document.getElementById("terminos_condiciones"),
      notasTextarea: document.getElementById("notas_internas"),
    };

    this.modales = {
      editor: new bootstrap.Modal(document.getElementById("modal-presupuesto")),
      detalles: new bootstrap.Modal(document.getElementById("modal-detalles")),
      eliminar: new bootstrap.Modal(document.getElementById("modal-eliminar")),
    };
  }

  configurarEventos() {
    document
      .getElementById("nuevo-presupuesto-btn")
      ?.addEventListener("click", () => this.nuevoPresupuesto());

    document
      .getElementById("guardar-presupuesto-btn")
      ?.addEventListener("click", () => this.guardarPresupuesto());

    document
      .getElementById("agregar-linea-btn")
      ?.addEventListener("click", () => this.agregarLinea());

    document
      .getElementById("confirmar-eliminar")
      ?.addEventListener("click", () => this.confirmarEliminar());

    this.elementos.buscador?.addEventListener("input", () =>
      this.renderTabla()
    );
    this.elementos.filtroEstado?.addEventListener("change", () =>
      this.renderTabla()
    );
    this.elementos.filtroCliente?.addEventListener("change", () =>
      this.renderTabla()
    );
  }

  async cargarPresupuestos() {
    const estado = this.elementos.filtroEstado?.value || "";
    const cliente = this.elementos.filtroCliente?.value || "";
    const q = this.elementos.buscador?.value || "";

    const params = new URLSearchParams();
    if (estado) params.append("estado", estado);
    if (cliente) params.append("cliente_id", cliente);
    if (q) params.append("q", q);

    const resp = await fetch(
      `../../api/presupuestos/listar_presupuestos.php?${params.toString()}`
    );
    const data = await resp.json();
    if (!data.ok) throw new Error(data.error || "Error al listar presupuestos");
    this.presupuestos = data.presupuestos || [];
  }

  async cargarClientes() {
    const resp = await fetch("../../api/clientes/obtener_clientes.php");
    const data = await resp.json();
    if (!data.ok) throw new Error(data.error || "Error al cargar clientes");
    this.clientes = data.clientes || [];

    const fragment = document.createDocumentFragment();
    this.clientes.forEach((cliente) => {
      const option = document.createElement("option");
      option.value = cliente.id;
      option.textContent = `${cliente.nombre_comercial} (${cliente.codigo})`;
      fragment.appendChild(option);
    });

    if (this.elementos.clienteSelect) {
      this.elementos.clienteSelect.innerHTML = "";
      this.elementos.clienteSelect.appendChild(fragment.cloneNode(true));
    }

    if (this.elementos.filtroCliente) {
      this.elementos.filtroCliente.innerHTML =
        '<option value="">Todos los clientes</option>';
      this.clientes.forEach((cliente) => {
        const option = document.createElement("option");
        option.value = cliente.id;
        option.textContent = cliente.nombre_comercial;
        this.elementos.filtroCliente.appendChild(option);
      });
    }
  }

  async cargarProductos() {
    const resp = await fetch("../../api/productos/obtener_productos.php");
    const data = await resp.json();
    if (!data.ok) throw new Error(data.error || "Error al cargar productos");
    this.productos = data.productos || [];
  }

  filtrarPresupuestos() {
    const estado = this.elementos.filtroEstado?.value || "";
    const cliente = this.elementos.filtroCliente?.value || "";
    const q = (this.elementos.buscador?.value || "").toLowerCase();

    return this.presupuestos.filter((p) => {
      const coincideEstado = estado ? p.estado === estado : true;
      const coincideCliente = cliente ? String(p.cliente_id) === cliente : true;
      const coincideBusqueda = q
        ? p.numero_presupuesto.toLowerCase().includes(q) ||
          p.cliente_nombre.toLowerCase().includes(q)
        : true;
      return coincideEstado && coincideCliente && coincideBusqueda;
    });
  }

  renderTabla() {
    const registros = this.filtrarPresupuestos();
    if (!registros.length) {
      this.elementos.tbody.innerHTML = "";
      this.elementos.noRows.style.display = "block";
      return;
    }
    this.elementos.noRows.style.display = "none";
    this.elementos.tbody.innerHTML = registros
      .map((p) => this.renderFila(p))
      .join("");
  }

  renderFila(p) {
    return `
      <tr>
        <td>${p.id}</td>
        <td><span class="fw-semibold">${
          p.numero_presupuesto
        }</span><br><small class="text-muted">${p.ejercicio}</small></td>
        <td>${p.cliente_nombre}</td>
        <td>
          <div>${this.formatearFecha(p.fecha)}</div>
          <small class="text-muted">Válido: ${this.formatearFecha(
            p.fecha_valido_hasta
          )}</small>
        </td>
        <td><strong>${this.formatearMoneda(p.total)}</strong></td>
        <td>${this.renderBadgeEstado(p.estado)}</td>
        <td>
          <div class="btn-group">
            <button class="btn-action" title="Ver" onclick="presupuestosPage.verPresupuesto(${
              p.id
            })"><i class="fas fa-eye"></i></button>
            <button class="btn-action" title="Editar" onclick="presupuestosPage.editarPresupuesto(${
              p.id
            })"><i class="fas fa-pen"></i></button>
            <button class="btn-action" title="Eliminar" onclick="presupuestosPage.eliminarPresupuesto(${
              p.id
            }, '${p.numero_presupuesto}')"><i class="fas fa-trash"></i></button>
          </div>
        </td>
      </tr>
    `;
  }

  renderBadgeEstado(estado) {
    const clases = {
      borrador: "badge-borrador",
      enviado: "badge-enviado",
      aceptado: "badge-aceptado",
      rechazado: "badge-rechazado",
      cancelado: "badge-cancelado",
    };
    return `<span class="badge-estado ${clases[estado] || ""}">${
      estado.charAt(0).toUpperCase() + estado.slice(1)
    }</span>`;
  }

  nuevoPresupuesto() {
    this.presupuestoActual = null;
    this.lineas = [];
    this.elementos.form.reset();
    this.elementos.numeroPresupuesto.value = "";
    const hoy = new Date().toISOString().split("T")[0];
    this.elementos.fechaInput.value = hoy;
    const valido = new Date();
    valido.setDate(valido.getDate() + 30);
    this.elementos.fechaValidoInput.value = valido.toISOString().split("T")[0];
    this.elementos.ejercicioInput.value = new Date().getFullYear();
    this.agregarLinea();
    this.actualizarTotales();
    this.modales.editor.show();
  }

  async editarPresupuesto(id) {
    try {
      const resp = await fetch(
        `../../api/presupuestos/obtener_presupuesto.php?id=${id}`
      );
      const data = await resp.json();
      if (!data.ok) throw new Error(data.error || "Error al cargar");
      this.presupuestoActual = data.presupuesto;
      this.cargarFormulario(this.presupuestoActual);
      this.modales.editor.show();
    } catch (error) {
      this.mostrarAlerta(error.message, "danger");
    }
  }

  cargarFormulario(p) {
    this.elementos.numeroPresupuesto.value = p.numero_presupuesto;
    this.elementos.clienteSelect.value = p.cliente_id;
    this.elementos.estadoSelect.value = p.estado;
    this.elementos.fechaInput.value = p.fecha;
    this.elementos.fechaValidoInput.value = p.fecha_valido_hasta;
    this.elementos.ejercicioInput.value = p.ejercicio;
    this.elementos.monedaInput.value = p.moneda;
    this.elementos.formaPagoSelect.value = p.forma_pago;
    this.elementos.plazoEntregaInput.value = p.plazo_entrega || "";
    this.elementos.garantiaInput.value = p.garantia || "";
    this.elementos.terminosTextarea.value = p.terminos_condiciones || "";
    this.elementos.notasTextarea.value = p.notas_internas || "";
    document.getElementById("presupuesto-id").value = p.id;
    this.lineas = (p.lineas || []).map((linea) => ({ ...linea }));
    if (!this.lineas.length) this.agregarLinea();
    this.renderLineas();
    this.actualizarTotales();
  }

  renderLineas() {
    this.elementos.lineasTbody.innerHTML = this.lineas
      .map((linea, index) => this.renderLinea(linea, index))
      .join("");
    this.elementos.lineasTbody
      .querySelectorAll("[data-action]")
      .forEach((input) => {
        input.addEventListener("input", (event) =>
          this.actualizarLinea(event.target.dataset.index, event.target)
        );
      });
  }

  renderLinea(linea, index) {
    const productoOptions = [
      '<option value="">-- Manual --</option>',
      ...this.productos.map(
        (producto) =>
          `<option value="${producto.id}" ${
            producto.id === linea.producto_id ? "selected" : ""
          }>${producto.nombre}</option>`
      ),
    ].join("");

    return `
      <tr>
        <td>
          <select class="form-select form-select-sm" data-action="producto" data-index="${index}">
            ${productoOptions}
          </select>
        </td>
        <td>
          <textarea class="form-control form-control-sm" rows="1" data-action="descripcion" data-index="${index}">${
      linea.descripcion || ""
    }</textarea>
        </td>
        <td>
          <input type="number" min="0" step="0.01" class="form-control form-control-sm" data-action="cantidad" data-index="${index}" value="${
      linea.cantidad || 1
    }">
        </td>
        <td>
          <input type="number" min="0" step="0.01" class="form-control form-control-sm" data-action="precio" data-index="${index}" value="${
      linea.precio_unitario || 0
    }">
        </td>
        <td>
          <input type="number" min="0" max="100" step="0.1" class="form-control form-control-sm" data-action="descuento" data-index="${index}" value="${
      linea.descuento_linea || 0
    }">
        </td>
        <td>
          <input type="text" class="form-control form-control-sm" data-action="iva" data-index="${index}" value="${
      linea.iva_tipo || "21"
    }">
        </td>
        <td class="text-end fw-semibold">${this.formatearMoneda(
          linea.total_linea || 0
        )}</td>
        <td class="text-center">
          <button type="button" class="btn btn-danger btn-sm" onclick="presupuestosPage.eliminarLinea(${index})">
            <i class="fas fa-times"></i>
          </button>
        </td>
      </tr>
    `;
  }

  agregarLinea() {
    this.lineas.push({
      linea: this.lineas.length + 1,
      producto_id: null,
      descripcion: "",
      cantidad: 1,
      precio_unitario: 0,
      descuento_linea: 0,
      iva_tipo: "21",
      importe_descuento: 0,
      subtotal: 0,
      importe_iva: 0,
      total_linea: 0,
    });
    this.renderLineas();
    this.actualizarTotales();
  }

  eliminarLinea(index) {
    if (this.lineas.length === 1) {
      this.mostrarAlerta("Debe existir al menos una línea", "warning");
      return;
    }
    this.lineas.splice(index, 1);
    this.renderLineas();
    this.actualizarTotales();
  }

  actualizarLinea(index, elemento) {
    const linea = this.lineas[index];
    if (!linea) return;
    const { action } = elemento.dataset;

    if (action === "producto") {
      const productoSeleccionado = this.productos.find(
        (p) => String(p.id) === elemento.value
      );
      linea.producto_id = productoSeleccionado ? productoSeleccionado.id : null;
      if (productoSeleccionado) {
        linea.descripcion = productoSeleccionado.nombre;
        linea.precio_unitario =
          parseFloat(productoSeleccionado.precio_venta) || 0;
        linea.iva_tipo = productoSeleccionado.iva_tipo || "21";
      }
    }

    if (action === "descripcion") linea.descripcion = elemento.value;
    if (action === "cantidad")
      linea.cantidad = Math.max(0, parseFloat(elemento.value) || 0);
    if (action === "precio")
      linea.precio_unitario = Math.max(0, parseFloat(elemento.value) || 0);
    if (action === "descuento")
      linea.descuento_linea = Math.max(
        0,
        Math.min(100, parseFloat(elemento.value) || 0)
      );
    if (action === "iva") linea.iva_tipo = elemento.value.trim();

    this.recalcularLinea(linea);
    this.renderLineas();
    this.actualizarTotales();
  }

  recalcularLinea(linea) {
    const subtotal = linea.cantidad * linea.precio_unitario;
    const descuento = subtotal * (linea.descuento_linea / 100);
    const base = subtotal - descuento;
    const ivaValor =
      linea.iva_tipo.toLowerCase() === "exento"
        ? 0
        : parseFloat(linea.iva_tipo) || 0;
    const iva = base * (ivaValor / 100);
    linea.subtotal = this.redondear(subtotal);
    linea.importe_descuento = this.redondear(descuento);
    linea.importe_iva = this.redondear(iva);
    linea.total_linea = this.redondear(base + iva);
  }

  actualizarTotales() {
    this.lineas.forEach((linea) => this.recalcularLinea(linea));
    const totales = this.lineas.reduce(
      (acc, linea) => {
        acc.base += linea.subtotal;
        acc.descuento += linea.importe_descuento;
        acc.iva += linea.importe_iva;
        acc.total += linea.total_linea;
        return acc;
      },
      { base: 0, descuento: 0, iva: 0, total: 0 }
    );
    this.elementos.totalBase.textContent = this.formatearMoneda(totales.base);
    this.elementos.totalDescuento.textContent = this.formatearMoneda(
      totales.descuento
    );
    this.elementos.totalIva.textContent = this.formatearMoneda(totales.iva);
    this.elementos.totalGeneral.textContent = this.formatearMoneda(
      totales.total
    );
  }

  async guardarPresupuesto() {
    try {
      if (!this.elementos.clienteSelect.value) {
        this.mostrarAlerta("Selecciona un cliente", "warning");
        return;
      }
      const payload = {
        id: document.getElementById("presupuesto-id").value || undefined,
        numero_presupuesto: this.elementos.numeroPresupuesto.value || undefined,
        cliente_id: this.elementos.clienteSelect.value,
        estado: this.elementos.estadoSelect.value,
        fecha: this.elementos.fechaInput.value,
        fecha_valido_hasta: this.elementos.fechaValidoInput.value,
        ejercicio: this.elementos.ejercicioInput.value,
        moneda: this.elementos.monedaInput.value,
        forma_pago: this.elementos.formaPagoSelect.value,
        plazo_entrega: this.elementos.plazoEntregaInput.value,
        garantia: this.elementos.garantiaInput.value,
        terminos_condiciones: this.elementos.terminosTextarea.value,
        notas_internas: this.elementos.notasTextarea.value,
        lineas: this.lineas,
      };

      const resp = await fetch(
        "../../api/presupuestos/guardar_presupuesto.php",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload),
        }
      );
      const data = await resp.json();
      if (!data.ok) throw new Error(data.error || "Error al guardar");

      this.modales.editor.hide();
      this.mostrarAlerta("Presupuesto guardado correctamente", "success");
      await this.cargarPresupuestos();
      this.renderTabla();
    } catch (error) {
      this.mostrarAlerta(error.message, "danger");
    }
  }

  async verPresupuesto(id) {
    try {
      const resp = await fetch(
        `../../api/presupuestos/obtener_presupuesto.php?id=${id}`
      );
      const data = await resp.json();
      if (!data.ok) throw new Error(data.error || "Error al obtener");
      const p = data.presupuesto;
      const html = `
        <div class="mb-3">
          <h6>Información general</h6>
          <p class="mb-0"><strong>Número:</strong> ${p.numero_presupuesto}</p>
          <p class="mb-0"><strong>Cliente:</strong> ${p.cliente_nombre}</p>
          <p class="mb-0"><strong>Estado:</strong> ${p.estado}</p>
          <p class="mb-0"><strong>Fecha:</strong> ${this.formatearFecha(
            p.fecha
          )} · <strong>Válido hasta:</strong> ${this.formatearFecha(
        p.fecha_valido_hasta
      )}</p>
        </div>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>#</th>
                <th>Descripción</th>
                <th class="text-end">Cant.</th>
                <th class="text-end">Precio</th>
                <th class="text-end">Total</th>
              </tr>
            </thead>
            <tbody>
              ${(p.lineas || [])
                .map(
                  (linea) => `
                    <tr>
                      <td>${linea.linea}</td>
                      <td>${linea.descripcion}</td>
                      <td class="text-end">${linea.cantidad}</td>
                      <td class="text-end">${this.formatearMoneda(
                        linea.precio_unitario
                      )}</td>
                      <td class="text-end">${this.formatearMoneda(
                        linea.total_linea
                      )}</td>
                    </tr>
                  `
                )
                .join("")}
            </tbody>
          </table>
        </div>
        <div class="text-end">
          <p class="mb-1"><strong>Base:</strong> ${this.formatearMoneda(
            p.base_imponible
          )}</p>
          <p class="mb-1"><strong>Descuento:</strong> ${this.formatearMoneda(
            p.importe_descuento
          )}</p>
          <p class="mb-1"><strong>IVA:</strong> ${this.formatearMoneda(
            p.importe_iva
          )}</p>
          <p class="fs-5"><strong>Total:</strong> ${this.formatearMoneda(
            p.total
          )}</p>
        </div>
      `;
      document.getElementById("detalles-presupuesto-content").innerHTML = html;
      this.modales.detalles.show();
    } catch (error) {
      this.mostrarAlerta(error.message, "danger");
    }
  }

  eliminarPresupuesto(id, numero) {
    this.presupuestoActual = { id };
    document.getElementById("presupuesto-eliminar-numero").textContent = numero;
    this.modales.eliminar.show();
  }

  async confirmarEliminar() {
    if (!this.presupuestoActual?.id) return;
    try {
      const resp = await fetch(
        "../../api/presupuestos/eliminar_presupuesto.php",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id: this.presupuestoActual.id }),
        }
      );
      const data = await resp.json();
      if (!data.ok) throw new Error(data.error || "Error al eliminar");
      this.modales.eliminar.hide();
      this.mostrarAlerta("Presupuesto eliminado", "success");
      await this.cargarPresupuestos();
      this.renderTabla();
    } catch (error) {
      this.mostrarAlerta(error.message, "danger");
    }
  }

  formatearMoneda(valor) {
    const number = parseFloat(valor) || 0;
    return number.toLocaleString("es-ES", {
      style: "currency",
      currency: "EUR",
    });
  }

  formatearFecha(fecha) {
    if (!fecha) return "-";
    return new Date(fecha).toLocaleDateString("es-ES");
  }

  redondear(num) {
    return Math.round((num + Number.EPSILON) * 100) / 100;
  }

  mostrarAlerta(mensaje, tipo = "info") {
    const alert = document.createElement("div");
    alert.className = `alert alert-${tipo}`;
    alert.innerHTML = mensaje;
    document.querySelector("#presupuestos-content")?.prepend(alert);
    setTimeout(() => alert.remove(), 4000);
  }
}

const presupuestosPage = new PresupuestosPage();
window.presupuestosPage = presupuestosPage;
