class FacturacionPage {
  constructor() {
    this.facturas = [];
    this.clientes = [];
    this.proveedores = [];
    this.productos = [];
    this.presupuestos = [];
    this.facturaActual = null;
    this.lineas = [];
    this.elementos = {};
    this.modales = {};

    document.addEventListener("DOMContentLoaded", () => this.init());
  }

  async init() {
    this.cargarElementos();
    this.configurarEventos();

    try {
      await Promise.all([
        this.cargarClientes(),
        this.cargarProveedores(),
        this.cargarProductos(),
        this.cargarPresupuestos(),
      ]);
      await this.cargarFacturas();
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
      tbody: document.getElementById("facturas-tbody"),
      noRows: document.getElementById("no-facturas"),
      filtroTipo: document.getElementById("filtro-tipo"),
      filtroEstado: document.getElementById("filtro-estado"),
      filtroCliente: document.getElementById("filtro-cliente"),
      buscador: document.getElementById("buscar-factura"),
      form: document.getElementById("form-factura"),
      lineasTbody: document.getElementById("lineas-tbody"),
      totalBase: document.getElementById("total-base"),
      totalDescuento: document.getElementById("total-descuento"),
      totalBaseDescuento: document.getElementById("total-base-descuento"),
      totalIva: document.getElementById("total-iva"),
      totalIrpf: document.getElementById("total-irpf"),
      totalGeneral: document.getElementById("total-general"),
      numeroFactura: document.getElementById("numero_factura"),
      numeroSerie: document.getElementById("numero_serie"),
      tipoFactura: document.getElementById("tipo_factura"),
      ejercicioInput: document.getElementById("ejercicio"),
      fechaInput: document.getElementById("fecha"),
      fechaVencimientoInput: document.getElementById("fecha_vencimiento"),
      tipoTercero: document.getElementById("tipo_tercero"),
      clienteSelect: document.getElementById("cliente_id"),
      proveedorSelect: document.getElementById("proveedor_id"),
      estadoSelect: document.getElementById("estado"),
      monedaInput: document.getElementById("moneda"),
      formaPagoSelect: document.getElementById("forma_pago"),
      numeroCuentaInput: document.getElementById("numero_cuenta"),
      facturaRectificadaSelect: document.getElementById(
        "factura_rectificada_id"
      ),
      motivoRectificacionTextarea: document.getElementById(
        "motivo_rectificacion"
      ),
      direccionEnvioSelect: document.getElementById("direccion_envio_id"),
      contactoSelect: document.getElementById("contacto_id"),
      presupuestoSelect: document.getElementById("presupuesto_id"),
      fechaPagoInput: document.getElementById("fecha_pago"),
      importePagadoInput: document.getElementById("importe_pagado"),
      metodoPagoInput: document.getElementById("metodo_pago"),
      referenciaPagoInput: document.getElementById("referencia_pago"),
      terminosTextarea: document.getElementById("terminos_condiciones"),
      notasTextarea: document.getElementById("notas_internas"),
      presupuestosTbody: document.getElementById("presupuestos-tbody"),
    };

    this.modales = {
      editor: new bootstrap.Modal(document.getElementById("modal-factura")),
      detalles: new bootstrap.Modal(document.getElementById("modal-detalles")),
      eliminar: new bootstrap.Modal(document.getElementById("modal-eliminar")),
      seleccionarPresupuesto: new bootstrap.Modal(
        document.getElementById("modal-seleccionar-presupuesto")
      ),
    };
  }

  configurarEventos() {
    // Botones principales
    document
      .getElementById("nueva-factura-btn")
      ?.addEventListener("click", () => this.nuevaFactura());

    document
      .getElementById("nueva-rectificativa-btn")
      ?.addEventListener("click", () => this.nuevaFacturaRectificativa());

    document
      .getElementById("nueva-proforma-btn")
      ?.addEventListener("click", () => this.nuevaProforma());

    document
      .getElementById("desde-presupuesto-btn")
      ?.addEventListener("click", () => this.desdePresupuesto());

    document
      .getElementById("guardar-factura-btn")
      ?.addEventListener("click", () => this.guardarFactura());

    document
      .getElementById("agregar-linea-btn")
      ?.addEventListener("click", () => this.agregarLinea());

    document
      .getElementById("confirmar-eliminar")
      ?.addEventListener("click", () => this.confirmarEliminar());

    // Filtros y búsqueda
    this.elementos.buscador?.addEventListener("input", () =>
      this.renderTabla()
    );
    this.elementos.filtroTipo?.addEventListener("change", () =>
      this.renderTabla()
    );
    this.elementos.filtroEstado?.addEventListener("change", () =>
      this.renderTabla()
    );
    this.elementos.filtroCliente?.addEventListener("change", () =>
      this.renderTabla()
    );

    // Cambios en formulario
    this.elementos.tipoFactura?.addEventListener("change", () => {
      this.actualizarCamposTipoFactura();
      this.actualizarNumeroFactura();
    });

    this.elementos.tipoTercero?.addEventListener("change", () => {
      this.actualizarSelectorTercero();
    });

    this.elementos.clienteSelect?.addEventListener("change", () => {
      this.cargarDireccionesContactos();
    });

    this.elementos.proveedorSelect?.addEventListener("change", () => {
      this.cargarDireccionesContactos();
    });

    this.elementos.ejercicioInput?.addEventListener("change", () => {
      this.actualizarNumeroFactura();
    });

    this.elementos.numeroSerie?.addEventListener("change", () => {
      this.actualizarNumeroFactura();
    });

    // Auto-cálculo de vencimiento
    this.elementos.fechaInput?.addEventListener("change", () => {
      this.calcularVencimiento();
    });
  }

  async cargarFacturas() {
    const tipo = this.elementos.filtroTipo?.value || "";
    const estado = this.elementos.filtroEstado?.value || "";
    const cliente = this.elementos.filtroCliente?.value || "";
    const q = this.elementos.buscador?.value || "";

    const params = new URLSearchParams();
    if (tipo) params.append("tipo", tipo);
    if (estado) params.append("estado", estado);
    if (cliente) params.append("cliente_id", cliente);
    if (q) params.append("q", q);

    const resp = await fetch(
      `../../api/facturacion/listar_facturas.php?${params.toString()}`
    );
    const data = await resp.json();
    if (!data.ok) throw new Error(data.error || "Error al listar facturas");
    this.facturas = data.facturas || [];
  }

  async cargarClientes() {
    const resp = await fetch("../../api/clientes/obtener_clientes.php");
    const data = await resp.json();
    if (!data.ok) throw new Error(data.error || "Error al cargar clientes");
    this.clientes = data.clientes || [];

    // Poblar select de clientes
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

    // Poblar filtro de clientes
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

  async cargarProveedores() {
    const resp = await fetch("../../api/proveedores/obtener_proveedores.php");
    const data = await resp.json();
    if (!data.ok) throw new Error(data.error || "Error al cargar proveedores");
    this.proveedores = data.proveedores || [];

    // Poblar select de proveedores
    const fragment = document.createDocumentFragment();
    this.proveedores.forEach((proveedor) => {
      const option = document.createElement("option");
      option.value = proveedor.id;
      option.textContent = `${proveedor.nombre_comercial} (${proveedor.codigo})`;
      fragment.appendChild(option);
    });

    if (this.elementos.proveedorSelect) {
      this.elementos.proveedorSelect.innerHTML = "";
      this.elementos.proveedorSelect.appendChild(fragment.cloneNode(true));
    }
  }

  async cargarProductos() {
    const resp = await fetch("../../api/productos/obtener_productos.php");
    const data = await resp.json();
    if (!data.ok) throw new Error(data.error || "Error al cargar productos");
    this.productos = data.productos || [];
  }

  async cargarPresupuestos() {
    const resp = await fetch(
      "../../api/presupuestos/listar_presupuestos.php?estado=aceptado"
    );
    const data = await resp.json();
    if (!data.ok) throw new Error(data.error || "Error al cargar presupuestos");
    this.presupuestos = data.presupuestos || [];
  }

  async cargarFacturasParaRectificativa() {
    const resp = await fetch(
      "../../api/facturacion/listar_facturas.php?tipo=venta"
    );
    const data = await resp.json();
    if (!data.ok) throw new Error(data.error || "Error al cargar facturas");

    const facturasVenta = data.facturas || [];
    const fragment = document.createDocumentFragment();

    facturasVenta.forEach((factura) => {
      const option = document.createElement("option");
      option.value = factura.id;
      option.textContent = `${factura.numero_factura} - ${factura.cliente_nombre}`;
      fragment.appendChild(option);
    });

    if (this.elementos.facturaRectificadaSelect) {
      this.elementos.facturaRectificadaSelect.innerHTML = "";
      this.elementos.facturaRectificadaSelect.appendChild(fragment);
    }
  }

  filtrarFacturas() {
    const tipo = this.elementos.filtroTipo?.value || "";
    const estado = this.elementos.filtroEstado?.value || "";
    const cliente = this.elementos.filtroCliente?.value || "";
    const q = (this.elementos.buscador?.value || "").toLowerCase();

    return this.facturas.filter((f) => {
      const coincideTipo = tipo ? f.tipo_factura === tipo : true;
      const coincideEstado = estado ? f.estado === estado : true;
      const coincideCliente = cliente ? String(f.cliente_id) === cliente : true;
      const coincideBusqueda = q
        ? f.numero_factura.toLowerCase().includes(q) ||
          (f.cliente_nombre && f.cliente_nombre.toLowerCase().includes(q))
        : true;
      return (
        coincideTipo && coincideEstado && coincideCliente && coincideBusqueda
      );
    });
  }

  renderTabla() {
    const registros = this.filtrarFacturas();
    if (!registros.length) {
      this.elementos.tbody.innerHTML = "";
      this.elementos.noRows.style.display = "block";
      return;
    }
    this.elementos.noRows.style.display = "none";
    this.elementos.tbody.innerHTML = registros
      .map((f) => this.renderFila(f))
      .join("");
  }

  renderFila(f) {
    const nombreTercero =
      f.cliente_nombre || f.proveedor_nombre || "Sin asignar";
    const claseEstado = f.estado ? `factura-${f.estado}` : "";
    const claseTipo = f.tipo_factura ? `badge-${f.tipo_factura}` : "";

    return `
      <tr class="${claseEstado}">
        <td>${f.id}</td>
        <td>
          <span class="fw-semibold">${f.numero_factura}</span>
          <br><small class="text-muted">${f.numero_serie}-${f.ejercicio}</small>
        </td>
        <td>
          <span class="badge-tipo ${claseTipo}">${
      f.tipo_factura
        ? f.tipo_factura.charAt(0).toUpperCase() + f.tipo_factura.slice(1)
        : ""
    }</span>
        </td>
        <td>${nombreTercero}</td>
        <td>
          <div>${this.formatearFecha(f.fecha)}</div>
        </td>
        <td>
          <div>${this.formatearFecha(f.fecha_vencimiento)}</div>
          ${
            this.estaVencida(f)
              ? '<small class="text-danger">Vencida</small>'
              : ""
          }
        </td>
        <td><strong>${this.formatearMoneda(f.total)}</strong></td>
        <td>${this.renderBadgeEstado(f.estado)}</td>
        <td>
          <div class="btn-group">
            <button class="btn-action" title="Ver" onclick="facturacionPage.verFactura(${
              f.id
            })"><i class="fas fa-eye"></i></button>
            <button class="btn-action" title="Editar" onclick="facturacionPage.editarFactura(${
              f.id
            })"><i class="fas fa-pen"></i></button>
            <button class="btn-action btn-action-pdf" title="PDF" onclick="facturacionPage.generarPDF(${
              f.id
            })"><i class="fas fa-file-pdf"></i></button>
            <button class="btn-action btn-action-email" title="Email" onclick="facturacionPage.enviarEmail(${
              f.id
            })"><i class="fas fa-envelope"></i></button>
            <button class="btn-action" title="Eliminar" onclick="facturacionPage.eliminarFactura(${
              f.id
            }, '${f.numero_factura}')"><i class="fas fa-trash"></i></button>
          </div>
        </td>
      </tr>
    `;
  }

  renderBadgeEstado(estado) {
    const clases = {
      borrador: "badge-borrador",
      pendiente: "badge-pendiente",
      pagada: "badge-pagada",
      vencida: "badge-vencida",
      cancelada: "badge-cancelada",
      cobrada: "badge-cobrada",
    };
    return `<span class="badge-estado ${clases[estado] || ""}">${
      estado ? estado.charAt(0).toUpperCase() + estado.slice(1) : ""
    }</span>`;
  }

  nuevaFactura(tipo = "venta") {
    this.facturaActual = null;
    this.lineas = [];
    this.elementos.form.reset();
    this.elementos.tipoFactura.value = tipo;
    this.elementos.numeroSerie.value = "FAC";
    this.elementos.ejercicioInput.value = new Date().getFullYear();
    this.elementos.monedaInput.value = "EUR";
    this.elementos.formaPagoSelect.value = "transferencia";

    const hoy = new Date().toISOString().split("T")[0];
    this.elementos.fechaInput.value = hoy;

    this.actualizarCamposTipoFactura();
    this.actualizarSelectorTercero();
    this.actualizarNumeroFactura();
    this.calcularVencimiento();
    this.agregarLinea();
    this.actualizarTotales();

    // Mostrar primera pestaña
    document.getElementById("datos-tab").click();

    this.modales.editor.show();
  }

  nuevaFacturaRectificativa() {
    this.nuevaFactura("rectificativa");
  }

  nuevaProforma() {
    this.nuevaFactura("proforma");
  }

  async desdePresupuesto() {
    try {
      this.renderTablaPresupuestos();
      this.modales.seleccionarPresupuesto.show();
    } catch (error) {
      this.mostrarAlerta(error.message, "danger");
    }
  }

  renderTablaPresupuestos() {
    if (!this.presupuestos.length) {
      this.elementos.presupuestosTbody.innerHTML = `
        <tr>
          <td colspan="5" class="text-center text-muted">
            No hay presupuestos aceptados disponibles
          </td>
        </tr>
      `;
      return;
    }

    this.elementos.presupuestosTbody.innerHTML = this.presupuestos
      .map(
        (p) => `
        <tr>
          <td>${p.numero_presupuesto}</td>
          <td>${p.cliente_nombre}</td>
          <td>${this.formatearFecha(p.fecha)}</td>
          <td><strong>${this.formatearMoneda(p.total)}</strong></td>
          <td>
            <button class="btn btn-primary btn-sm" onclick="facturacionPage.convertirPresupuesto(${
              p.id
            })">
              <i class="fas fa-arrow-right"></i> Convertir
            </button>
          </td>
        </tr>
      `
      )
      .join("");
  }

  async convertirPresupuesto(presupuestoId) {
    try {
      const resp = await fetch(
        `../../api/presupuestos/obtener_presupuesto.php?id=${presupuestoId}`
      );
      const data = await resp.json();
      if (!data.ok)
        throw new Error(data.error || "Error al cargar presupuesto");

      const presupuesto = data.presupuesto;
      this.modales.seleccionarPresupuesto.hide();

      // Crear factura desde presupuesto
      this.nuevaFactura("venta");
      this.elementos.clienteSelect.value = presupuesto.cliente_id;
      this.elementos.presupuestoSelect.value = presupuesto.id;
      this.elementos.terminosTextarea.value =
        presupuesto.terminos_condiciones || "";
      this.elementos.notasTextarea.value = presupuesto.notas_internas || "";

      // Convertir líneas
      this.lineas = presupuesto.lineas.map((linea, index) => ({
        ...linea,
        linea: index + 1,
        irpf_tipo: null,
        importe_irpf: 0,
        total_linea: linea.total_linea,
      }));

      this.renderLineas();
      this.actualizarTotales();
      this.cargarDireccionesContactos();
    } catch (error) {
      this.mostrarAlerta(error.message, "danger");
    }
  }

  actualizarCamposTipoFactura() {
    const tipo = this.elementos.tipoFactura.value;
    const rectificativaFields = document.getElementById("rectificativa-fields");

    if (tipo === "rectificativa") {
      rectificativaFields.style.display = "block";
      this.cargarFacturasParaRectificativa();
    } else {
      rectificativaFields.style.display = "none";
    }

    // Actualizar selector de terceros según tipo
    if (tipo === "compra") {
      this.elementos.tipoTercero.value = "proveedor";
    } else {
      this.elementos.tipoTercero.value = "cliente";
    }
    this.actualizarSelectorTercero();
  }

  actualizarSelectorTercero() {
    const tipoTercero = this.elementos.tipoTercero.value;

    if (tipoTercero === "cliente") {
      this.elementos.clienteSelect.style.display = "block";
      this.elementos.proveedorSelect.style.display = "none";
      this.elementos.clienteSelect.setAttribute("required", "required");
      this.elementos.proveedorSelect.removeAttribute("required");
    } else {
      this.elementos.clienteSelect.style.display = "none";
      this.elementos.proveedorSelect.style.display = "block";
      this.elementos.clienteSelect.removeAttribute("required");
      this.elementos.proveedorSelect.setAttribute("required", "required");
    }
  }

  async actualizarNumeroFactura() {
    const ejercicio = this.elementos.ejercicioInput.value;
    const serie = this.elementos.numeroSerie.value;

    if (!ejercicio || !serie) return;

    try {
      const resp = await fetch(
        `../../api/facturacion/generar_numero.php?ejercicio=${ejercicio}&serie=${serie}`
      );
      const data = await resp.json();
      if (data.ok) {
        this.elementos.numeroFactura.value = data.numero;
      }
    } catch (error) {
      console.error("Error al generar número:", error);
    }
  }

  calcularVencimiento() {
    const fecha = this.elementos.fechaInput.value;
    if (!fecha) return;

    // Por defecto, 30 días después de la fecha de factura
    const fechaVenc = new Date(fecha);
    fechaVenc.setDate(fechaVenc.getDate() + 30);
    this.elementos.fechaVencimientoInput.value = fechaVenc
      .toISOString()
      .split("T")[0];
  }

  async cargarDireccionesContactos() {
    const tipoTercero = this.elementos.tipoTercero.value;
    const terceroId =
      tipoTercero === "cliente"
        ? this.elementos.clienteSelect.value
        : this.elementos.proveedorSelect.value;

    if (!terceroId) return;

    try {
      // Aquí se cargarían las direcciones y contactos del tercero
      // Por ahora, dejamos las opciones por defecto
      this.elementos.direccionEnvioSelect.innerHTML = `
        <option value="">Dirección fiscal</option>
      `;

      this.elementos.contactoSelect.innerHTML = `
        <option value="">Contacto principal</option>
      `;
    } catch (error) {
      console.error("Error al cargar direcciones y contactos:", error);
    }
  }

  async editarFactura(id) {
    try {
      const resp = await fetch(
        `../../api/facturacion/obtener_factura.php?id=${id}`
      );
      const data = await resp.json();
      if (!data.ok) throw new Error(data.error || "Error al cargar");
      this.facturaActual = data.factura;
      this.cargarFormulario(this.facturaActual);
      this.modales.editor.show();
    } catch (error) {
      this.mostrarAlerta(error.message, "danger");
    }
  }

  cargarFormulario(f) {
    this.elementos.numeroFactura.value = f.numero_factura;
    this.elementos.numeroSerie.value = f.numero_serie;
    this.elementos.tipoFactura.value = f.tipo_factura;
    this.elementos.ejercicioInput.value = f.ejercicio;
    this.elementos.fechaInput.value = f.fecha;
    this.elementos.fechaVencimientoInput.value = f.fecha_vencimiento;
    this.elementos.estadoSelect.value = f.estado;
    this.elementos.monedaInput.value = f.moneda;
    this.elementos.formaPagoSelect.value = f.forma_pago;
    this.elementos.numeroCuentaInput.value = f.numero_cuenta || "";
    this.elementos.facturaRectificadaSelect.value =
      f.factura_rectificada_id || "";
    this.elementos.motivoRectificacionTextarea.value =
      f.motivo_rectificacion || "";
    this.elementos.direccionEnvioSelect.value = f.direccion_envio_id || "";
    this.elementos.contactoSelect.value = f.contacto_id || "";
    this.elementos.presupuestoSelect.value = f.presupuesto_id || "";
    this.elementos.fechaPagoInput.value = f.fecha_pago
      ? f.fecha_pago.replace(" ", "T")
      : "";
    this.elementos.importePagadoInput.value = f.importe_pagado || 0;
    this.elementos.metodoPagoInput.value = f.metodo_pago || "";
    this.elementos.referenciaPagoInput.value = f.referencia_pago || "";
    this.elementos.terminosTextarea.value = f.terminos_condiciones || "";
    this.elementos.notasTextarea.value = f.notas_internas || "";

    document.getElementById("factura-id").value = f.id;

    // Configurar tipo de tercero
    if (f.cliente_id) {
      this.elementos.tipoTercero.value = "cliente";
      this.elementos.clienteSelect.value = f.cliente_id;
    } else if (f.proveedor_id) {
      this.elementos.tipoTercero.value = "proveedor";
      this.elementos.proveedorSelect.value = f.proveedor_id;
    }

    this.actualizarSelectorTercero();
    this.actualizarCamposTipoFactura();
    this.cargarDireccionesContactos();

    this.lineas = (f.lineas || []).map((linea) => ({ ...linea }));
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
          <input type="number" min="0" step="0.001" class="form-control form-control-sm" data-action="cantidad" data-index="${index}" value="${
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
          <select class="form-select form-select-sm" data-action="iva" data-index="${index}">
            <option value="21" ${
              linea.iva_tipo === "21" ? "selected" : ""
            }>21%</option>
            <option value="10" ${
              linea.iva_tipo === "10" ? "selected" : ""
            }>10%</option>
            <option value="4" ${
              linea.iva_tipo === "4" ? "selected" : ""
            }>4%</option>
            <option value="0" ${
              linea.iva_tipo === "0" ? "selected" : ""
            }>0%</option>
            <option value="exento" ${
              linea.iva_tipo === "exento" ? "selected" : ""
            }>Exento</option>
          </select>
        </td>
        <td>
          <select class="form-select form-select-sm" data-action="irpf" data-index="${index}">
            <option value="">--</option>
            <option value="19" ${
              linea.irpf_tipo === "19" ? "selected" : ""
            }>19%</option>
            <option value="15" ${
              linea.irpf_tipo === "15" ? "selected" : ""
            }>15%</option>
            <option value="7" ${
              linea.irpf_tipo === "7" ? "selected" : ""
            }>7%</option>
            <option value="0" ${
              linea.irpf_tipo === "0" ? "selected" : ""
            }>0%</option>
          </select>
        </td>
        <td class="text-end fw-semibold">${this.formatearMoneda(
          linea.total_linea || 0
        )}</td>
        <td class="text-center">
          <button type="button" class="btn btn-danger btn-sm" onclick="facturacionPage.eliminarLinea(${index})">
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
      unidad_medida: "unidades",
      precio_unitario: 0,
      descuento_linea: 0,
      iva_tipo: "21",
      irpf_tipo: null,
      importe_descuento: 0,
      subtotal: 0,
      importe_iva: 0,
      importe_irpf: 0,
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
    if (action === "iva") linea.iva_tipo = elemento.value;
    if (action === "irpf") linea.irpf_tipo = elemento.value || null;

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

    const irpfValor = linea.irpf_tipo ? parseFloat(linea.irpf_tipo) : 0;
    const irpf = base * (irpfValor / 100);

    linea.subtotal = this.redondear(subtotal);
    linea.importe_descuento = this.redondear(descuento);
    linea.importe_iva = this.redondear(iva);
    linea.importe_irpf = this.redondear(irpf);
    linea.total_linea = this.redondear(base + iva - irpf);
  }

  actualizarTotales() {
    this.lineas.forEach((linea) => this.recalcularLinea(linea));
    const totales = this.lineas.reduce(
      (acc, linea) => {
        acc.base += linea.subtotal;
        acc.descuento += linea.importe_descuento;
        acc.iva += linea.importe_iva;
        acc.irpf += linea.importe_irpf;
        acc.total += linea.total_linea;
        return acc;
      },
      { base: 0, descuento: 0, iva: 0, irpf: 0, total: 0 }
    );

    const baseDescuento = totales.base - totales.descuento;

    this.elementos.totalBase.textContent = this.formatearMoneda(totales.base);
    this.elementos.totalDescuento.textContent = this.formatearMoneda(
      totales.descuento
    );
    this.elementos.totalBaseDescuento.textContent =
      this.formatearMoneda(baseDescuento);
    this.elementos.totalIva.textContent = this.formatearMoneda(totales.iva);
    this.elementos.totalIrpf.textContent = this.formatearMoneda(totales.irpf);
    this.elementos.totalGeneral.textContent = this.formatearMoneda(
      totales.total
    );
  }

  async guardarFactura() {
    try {
      const tipoTercero = this.elementos.tipoTercero.value;
      const terceroId =
        tipoTercero === "cliente"
          ? this.elementos.clienteSelect.value
          : this.elementos.proveedorSelect.value;

      if (!terceroId && this.elementos.tipoFactura.value !== "proforma") {
        this.mostrarAlerta("Selecciona un cliente o proveedor", "warning");
        return;
      }

      const payload = {
        id: document.getElementById("factura-id").value || undefined,
        numero_factura: this.elementos.numeroFactura.value || undefined,
        numero_serie: this.elementos.numeroSerie.value,
        tipo_factura: this.elementos.tipoFactura.value,
        ejercicio: this.elementos.ejercicioInput.value,
        fecha: this.elementos.fechaInput.value,
        fecha_vencimiento: this.elementos.fechaVencimientoInput.value,
        estado: this.elementos.estadoSelect.value,
        cliente_id: tipoTercero === "cliente" ? terceroId : null,
        proveedor_id: tipoTercero === "proveedor" ? terceroId : null,
        moneda: this.elementos.monedaInput.value,
        forma_pago: this.elementos.formaPagoSelect.value,
        numero_cuenta: this.elementos.numeroCuentaInput.value,
        factura_rectificada_id:
          this.elementos.facturaRectificadaSelect.value || null,
        motivo_rectificacion: this.elementos.motivoRectificacionTextarea.value,
        direccion_envio_id: this.elementos.direccionEnvioSelect.value || null,
        contacto_id: this.elementos.contactoSelect.value || null,
        presupuesto_id: this.elementos.presupuestoSelect.value || null,
        fecha_pago: this.elementos.fechaPagoInput.value || null,
        importe_pagado: this.elementos.importePagadoInput.value || 0,
        metodo_pago: this.elementos.metodoPagoInput.value,
        referencia_pago: this.elementos.referenciaPagoInput.value,
        terminos_condiciones: this.elementos.terminosTextarea.value,
        notas_internas: this.elementos.notasTextarea.value,
        lineas: this.lineas,
      };

      const resp = await fetch("../../api/facturacion/guardar_factura.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const data = await resp.json();
      if (!data.ok) throw new Error(data.error || "Error al guardar");

      this.modales.editor.hide();
      this.mostrarAlerta("Factura guardada correctamente", "success");
      await this.cargarFacturas();
      this.renderTabla();
    } catch (error) {
      this.mostrarAlerta(error.message, "danger");
    }
  }

  async verFactura(id) {
    try {
      const resp = await fetch(
        `../../api/facturacion/obtener_factura.php?id=${id}`
      );
      const data = await resp.json();
      if (!data.ok) throw new Error(data.error || "Error al obtener");
      const f = data.factura;

      const html = `
        <div class="row">
          <div class="col-md-6">
            <h6>Información general</h6>
            <p class="mb-1"><strong>Número:</strong> ${f.numero_factura}</p>
            <p class="mb-1"><strong>Tipo:</strong> ${f.tipo_factura}</p>
            <p class="mb-1"><strong>Estado:</strong> ${this.renderBadgeEstado(
              f.estado
            )}</p>
            <p class="mb-1"><strong>Fecha:</strong> ${this.formatearFecha(
              f.fecha
            )}</p>
            <p class="mb-1"><strong>Vencimiento:</strong> ${this.formatearFecha(
              f.fecha_vencimiento
            )}</p>
          </div>
          <div class="col-md-6">
            <h6>Tercero</h6>
            <p class="mb-1"><strong>Cliente/Proveedor:</strong> ${
              f.cliente_nombre || f.proveedor_nombre || "Sin asignar"
            }</p>
            <p class="mb-1"><strong>Forma de pago:</strong> ${f.forma_pago}</p>
            <p class="mb-1"><strong>Moneda:</strong> ${f.moneda}</p>
          </div>
        </div>
        
        <div class="mt-3">
          <h6>Líneas de la factura</h6>
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Descripción</th>
                  <th class="text-end">Cant.</th>
                  <th class="text-end">Precio</th>
                  <th class="text-end">Dto.</th>
                  <th class="text-end">IVA</th>
                  <th class="text-end">Total</th>
                </tr>
              </thead>
              <tbody>
                ${(f.lineas || [])
                  .map(
                    (linea) => `
                      <tr>
                        <td>${linea.linea}</td>
                        <td>${linea.descripcion}</td>
                        <td class="text-end">${linea.cantidad}</td>
                        <td class="text-end">${this.formatearMoneda(
                          linea.precio_unitario
                        )}</td>
                        <td class="text-end">${linea.descuento_linea}%</td>
                        <td class="text-end">${linea.iva_tipo}%</td>
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
        </div>
        
        <div class="row mt-3">
          <div class="col-md-6">
            ${
              f.terminos_condiciones
                ? `
              <h6>Términos y condiciones</h6>
              <p class="small">${f.terminos_condiciones}</p>
            `
                : ""
            }
          </div>
          <div class="col-md-6">
            <div class="text-end">
              <p class="mb-1"><strong>Base imponible:</strong> ${this.formatearMoneda(
                f.base_imponible
              )}</p>
              <p class="mb-1"><strong>Descuento:</strong> ${this.formatearMoneda(
                f.importe_descuento
              )}</p>
              <p class="mb-1"><strong>Base descuento:</strong> ${this.formatearMoneda(
                f.base_imponible_descuento
              )}</p>
              <p class="mb-1"><strong>IVA:</strong> ${this.formatearMoneda(
                f.importe_iva
              )}</p>
              <p class="mb-1"><strong>IRPF:</strong> ${this.formatearMoneda(
                f.importe_irpf
              )}</p>
              <p class="fs-5"><strong>Total:</strong> ${this.formatearMoneda(
                f.total
              )}</p>
              ${
                f.importe_pagado > 0
                  ? `<p class="mb-1"><strong>Pagado:</strong> ${this.formatearMoneda(
                      f.importe_pagado
                    )}</p>`
                  : ""
              }
            </div>
          </div>
        </div>
      `;
      document.getElementById("detalles-factura-content").innerHTML = html;
      this.modales.detalles.show();
    } catch (error) {
      this.mostrarAlerta(error.message, "danger");
    }
  }

  eliminarFactura(id, numero) {
    this.facturaActual = { id };
    document.getElementById("factura-eliminar-numero").textContent = numero;
    this.modales.eliminar.show();
  }

  async confirmarEliminar() {
    if (!this.facturaActual?.id) return;
    try {
      const resp = await fetch("../../api/facturacion/eliminar_factura.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: this.facturaActual.id }),
      });
      const data = await resp.json();
      if (!data.ok) throw new Error(data.error || "Error al eliminar");
      this.modales.eliminar.hide();
      this.mostrarAlerta("Factura eliminada", "success");
      await this.cargarFacturas();
      this.renderTabla();
    } catch (error) {
      this.mostrarAlerta(error.message, "danger");
    }
  }

  async generarPDF(id) {
    try {
      window.open(`../../api/facturacion/generar_pdf.php?id=${id}`, "_blank");
    } catch (error) {
      this.mostrarAlerta("Error al generar PDF", "danger");
    }
  }

  async enviarEmail(id) {
    try {
      const email = prompt("Introduce el email del destinatario:");
      if (!email) return;

      const resp = await fetch("../../api/facturacion/enviar_email.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id, email }),
      });
      const data = await resp.json();
      if (!data.ok) throw new Error(data.error || "Error al enviar email");

      this.mostrarAlerta("Email enviado correctamente", "success");
    } catch (error) {
      this.mostrarAlerta(error.message, "danger");
    }
  }

  estaVencida(factura) {
    if (!factura.fecha_vencimiento) return false;
    const vencimiento = new Date(factura.fecha_vencimiento);
    const hoy = new Date();
    return (
      vencimiento < hoy &&
      factura.estado !== "pagada" &&
      factura.estado !== "cobrada"
    );
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
    alert.className = `alert alert-${tipo} fade-in`;
    alert.innerHTML = mensaje;
    document.querySelector("#facturacion-content")?.prepend(alert);
    setTimeout(() => alert.remove(), 4000);
  }
}

const facturacionPage = new FacturacionPage();
window.facturacionPage = facturacionPage;
