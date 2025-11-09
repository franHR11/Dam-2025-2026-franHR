// Clase principal para la gestión de clientes
class ClientesPage {
  constructor() {
    this.clientes = [];
    this.paginaActual = 1;
    this.clientesPorPagina = 10;
    this.totalClientes = 0;
    this.clienteActual = null;
    this.clientesSeleccionados = [];
    this.filtros = {
      tipo: "",
      estado: "",
      busqueda: "",
    };

    // Elementos del DOM
    this.elementos = {
      tbody: null,
      modal: null,
      modalDetalles: null,
      modalEliminar: null,
      form: null,
      busqueda: null,
      filtros: {},
      seleccionarTodos: null,
      noClientes: null,
      pagination: null,
      clientesDesde: null,
      clientesHasta: null,
      clientesTotal: null,
    };

    this.init();
  }

  async init() {
    try {
      this.cargarElementosDOM();
      this.configurarEventListeners();
      await this.cargarClientes();
      this.renderizarTabla();
    } catch (error) {
      console.error("Error al inicializar la página de clientes:", error);
      this.mostrarAlerta("Error al cargar la página", "danger");
    }
  }

  cargarElementosDOM() {
    this.elementos = {
      tbody: document.getElementById("clientes-tbody"),
      modal: document.getElementById("modal-cliente"),
      modalDetalles: document.getElementById("modal-detalles"),
      modalEliminar: document.getElementById("modal-eliminar"),
      form: document.getElementById("form-cliente"),
      busqueda: document.getElementById("buscar-cliente"),
      filtroTipo: document.getElementById("filtro-tipo"),
      filtroEstado: document.getElementById("filtro-estado"),
      seleccionarTodos: document.getElementById("seleccionar-todos"),
      noClientes: document.getElementById("no-clientes"),
      pagination: document.getElementById("pagination"),
      clientesDesde: document.getElementById("clientes-desde"),
      clientesHasta: document.getElementById("clientes-hasta"),
      clientesTotal: document.getElementById("clientes-total"),
    };
  }

  configurarEventListeners() {
    // Botones principales
    document
      .getElementById("nuevo-cliente-btn")
      ?.addEventListener("click", () => this.nuevoCliente());
    document
      .getElementById("guardar-cliente")
      ?.addEventListener("click", () => this.guardarCliente());
    document
      .getElementById("confirmar-eliminar")
      ?.addEventListener("click", () => this.confirmarEliminar());
    document
      .getElementById("editar-desde-detalles")
      ?.addEventListener("click", () => this.editarDesdeDetalles());
    document
      .getElementById("exportar-clientes-btn")
      ?.addEventListener("click", () => this.exportarClientes());
    document
      .getElementById("importar-clientes-btn")
      ?.addEventListener("click", () => this.importarClientes());

    // Búsqueda y filtros
    this.elementos.busqueda?.addEventListener("input", (e) =>
      this.buscarCliente(e.target.value),
    );
    this.elementos.filtroTipo?.addEventListener("change", (e) =>
      this.aplicarFiltro("tipo", e.target.value),
    );
    this.elementos.filtroEstado?.addEventListener("change", (e) =>
      this.aplicarFiltro("estado", e.target.value),
    );

    // Selección múltiple
    this.elementos.seleccionarTodos?.addEventListener("change", (e) =>
      this.seleccionarTodos(e.target.checked),
    );

    // Validación de formulario
    this.elementos.form?.addEventListener("submit", (e) => {
      e.preventDefault();
      this.guardarCliente();
    });

    // Auto-generar código cuando cambia el tipo de cliente
    document
      .getElementById("tipo_cliente")
      ?.addEventListener("change", () => this.generarCodigo());
  }

  async cargarClientes() {
    try {
      this.mostrarLoading(true);
      const response = await fetch("../../api/clientes/obtener_clientes.php");
      const data = await response.json();

      if (data.ok) {
        this.clientes = data.clientes;
        this.totalClientes = data.total;
      } else {
        throw new Error(data.error || "Error al cargar clientes");
      }
    } catch (error) {
      console.error("Error al cargar clientes:", error);
      this.mostrarAlerta(
        "Error al cargar los clientes: " + error.message,
        "danger",
      );
    } finally {
      this.mostrarLoading(false);
    }
  }

  renderizarTabla() {
    if (!this.elementos.tbody) return;

    const clientesFiltrados = this.filtrarClientes();
    const clientesPaginados = this.paginarClientes(clientesFiltrados);

    if (clientesPaginados.length === 0) {
      this.elementos.tbody.innerHTML = "";
      this.elementos.noClientes.style.display = "block";
      this.actualizarInfoPaginacion(0, 0, 0);
    } else {
      this.elementos.noClientes.style.display = "none";
      this.elementos.tbody.innerHTML = clientesPaginados
        .map((cliente) => this.renderizarFilaCliente(cliente))
        .join("");

      this.actualizarInfoPaginacion(
        (this.paginaActual - 1) * this.clientesPorPagina + 1,
        Math.min(
          this.paginaActual * this.clientesPorPagina,
          clientesFiltrados.length,
        ),
        clientesFiltrados.length,
      );
    }

    this.renderizarPaginacion(clientesFiltrados.length);
  }

  renderizarFilaCliente(cliente) {
    const estadoBadge = cliente.activo
      ? cliente.bloqueado
        ? '<span class="badge-estado badge-bloqueado">Bloqueado</span>'
        : '<span class="badge-estado badge-activo">Activo</span>'
      : '<span class="badge-estado badge-inactivo">Inactivo</span>';

    const tipoBadge = this.getTipoBadge(cliente.tipo_cliente);

    return `
      <tr class="${this.clientesSeleccionados.includes(cliente.id) ? "cliente-seleccionado" : ""}">
        <td>
          <input type="checkbox" class="form-check-input seleccionar-cliente"
                 value="${cliente.id}"
                 ${this.clientesSeleccionados.includes(cliente.id) ? "checked" : ""}>
        </td>
        <td><strong>${cliente.codigo}</strong></td>
        <td>${cliente.nombre_comercial}</td>
        <td>${cliente.nif_cif || "-"}</td>
        <td>${tipoBadge}</td>
        <td>${cliente.telefono || "-"}</td>
        <td>${cliente.email || "-"}</td>
        <td>${estadoBadge}</td>
        <td>
          <button class="btn btn-action btn-ver" onclick="clientesPage.verCliente(${
            cliente.id
          })" title="Ver detalles">
            <i class="fas fa-eye"></i>
          </button>
          <button class="btn btn-action btn-editar" onclick="clientesPage.editarCliente(${
            cliente.id
          })" title="Editar">
            <i class="fas fa-edit"></i>
          </button>
          <button class="btn btn-action btn-eliminar" onclick="clientesPage.eliminarCliente(${
            cliente.id
          })" title="Eliminar">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      </tr>
    `;
  }

  getTipoBadge(tipo) {
    const badges = {
      particular: '<span class="badge-tipo badge-particular">Particular</span>',
      empresa: '<span class="badge-tipo badge-empresa">Empresa</span>',
      autonomo: '<span class="badge-tipo badge-autonomo">Autónomo</span>',
      ong: '<span class="badge-tipo badge-ong">ONG</span>',
      publico: '<span class="badge-tipo badge-publico">Público</span>',
    };
    return badges[tipo] || tipo;
  }

  filtrarClientes() {
    return this.clientes.filter((cliente) => {
      // Filtro de búsqueda
      if (this.filtros.busqueda) {
        const busqueda = this.filtros.busqueda.toLowerCase();
        if (
          !cliente.nombre_comercial.toLowerCase().includes(busqueda) &&
          !cliente.razon_social?.toLowerCase().includes(busqueda) &&
          !cliente.codigo.toLowerCase().includes(busqueda) &&
          !cliente.email?.toLowerCase().includes(busqueda) &&
          !cliente.nif_cif?.toLowerCase().includes(busqueda)
        ) {
          return false;
        }
      }

      // Filtro de tipo
      if (this.filtros.tipo && cliente.tipo_cliente !== this.filtros.tipo) {
        return false;
      }

      // Filtro de estado
      if (this.filtros.estado) {
        if (this.filtros.estado === "activos" && !cliente.activo) return false;
        if (this.filtros.estado === "bloqueados" && !cliente.bloqueado)
          return false;
      }

      return true;
    });
  }

  paginarClientes(clientes) {
    const inicio = (this.paginaActual - 1) * this.clientesPorPagina;
    const fin = inicio + this.clientesPorPagina;
    return clientes.slice(inicio, fin);
  }

  renderizarPaginacion(totalItems) {
    if (!this.elementos.pagination) return;

    const totalPages = Math.ceil(totalItems / this.clientesPorPagina);

    if (totalPages <= 1) {
      this.elementos.pagination.innerHTML = "";
      return;
    }

    let html = "";

    // Botón anterior
    html += `
      <li class="page-item ${this.paginaActual === 1 ? "disabled" : ""}">
        <a class="page-link" href="#" onclick="clientesPage.cambiarPagina(${this.paginaActual - 1})">
          <i class="fas fa-chevron-left"></i>
        </a>
      </li>
    `;

    // Números de página
    for (let i = 1; i <= totalPages; i++) {
      if (
        i === 1 ||
        i === totalPages ||
        (i >= this.paginaActual - 2 && i <= this.paginaActual + 2)
      ) {
        html += `
          <li class="page-item ${i === this.paginaActual ? "active" : ""}">
            <a class="page-link" href="#" onclick="clientesPage.cambiarPagina(${i})">${i}</a>
          </li>
        `;
      } else if (i === this.paginaActual - 3 || i === this.paginaActual + 3) {
        html += `
          <li class="page-item disabled">
            <a class="page-link" href="#">...</a>
          </li>
        `;
      }
    }

    // Botón siguiente
    html += `
      <li class="page-item ${this.paginaActual === totalPages ? "disabled" : ""}">
        <a class="page-link" href="#" onclick="clientesPage.cambiarPagina(${this.paginaActual + 1})">
          <i class="fas fa-chevron-right"></i>
        </a>
      </li>
    `;

    this.elementos.pagination.innerHTML = html;
  }

  cambiarPagina(pagina) {
    event.preventDefault();
    const totalPages = Math.ceil(
      this.filtrarClientes().length / this.clientesPorPagina,
    );

    if (pagina >= 1 && pagina <= totalPages) {
      this.paginaActual = pagina;
      this.renderizarTabla();
    }
  }

  actualizarInfoPaginacion(desde, hasta, total) {
    if (this.elementos.clientesDesde)
      this.elementos.clientesDesde.textContent = desde || 0;
    if (this.elementos.clientesHasta)
      this.elementos.clientesHasta.textContent = hasta || 0;
    if (this.elementos.clientesTotal)
      this.elementos.clientesTotal.textContent = total || 0;
  }

  async nuevoCliente() {
    this.clienteActual = null;
    this.limpiarFormulario();
    document.getElementById("modal-cliente-title").textContent =
      "Nuevo Cliente";

    const modal = new bootstrap.Modal(this.elementos.modal);
    modal.show();

    // Generar código automático
    setTimeout(() => this.generarCodigo(), 100);
  }

  async editarCliente(id) {
    try {
      const cliente = this.clientes.find((c) => c.id === id);
      if (!cliente) throw new Error("Cliente no encontrado");

      this.clienteActual = cliente;
      this.cargarFormulario(cliente);
      document.getElementById("modal-cliente-title").textContent =
        "Editar Cliente";

      const modal = new bootstrap.Modal(this.elementos.modal);
      modal.show();
    } catch (error) {
      console.error("Error al editar cliente:", error);
      this.mostrarAlerta(
        "Error al cargar el cliente: " + error.message,
        "danger",
      );
    }
  }

  cargarFormulario(cliente) {
    const campos = [
      "cliente-id",
      "codigo",
      "nombre_comercial",
      "razon_social",
      "nif_cif",
      "direccion",
      "codigo_postal",
      "ciudad",
      "provincia",
      "pais",
      "telefono",
      "telefono2",
      "email",
      "web",
      "tipo_cliente",
      "forma_pago",
      "dias_credito",
      "limite_credito",
      "importe_acumulado",
      "saldo_pendiente",
      "observaciones",
      "contacto_principal",
      "cargo_contacto",
    ];

    campos.forEach((campo) => {
      const elemento = document.getElementById(campo);
      if (elemento) {
        if (campo === "cliente-id") {
          elemento.value = cliente.id || "";
        } else {
          elemento.value = cliente[campo] || "";
        }
      }
    });

    // Checkboxes
    document.getElementById("activo").checked = cliente.activo === 1;
    document.getElementById("bloqueado").checked = cliente.bloqueado === 1;
  }

  limpiarFormulario() {
    this.elementos.form?.reset();

    // Resetear a valores por defecto
    document.getElementById("pais").value = "España";
    document.getElementById("dias_credito").value = "0";
    document.getElementById("limite_credito").value = "0";
    document.getElementById("importe_acumulado").value = "0";
    document.getElementById("saldo_pendiente").value = "0";
    document.getElementById("activo").checked = true;
    document.getElementById("bloqueado").checked = false;

    // Limpiar validación
    this.elementos.form
      ?.querySelectorAll(".is-invalid, .is-valid")
      .forEach((el) => {
        el.classList.remove("is-invalid", "is-valid");
      });
  }

  async guardarCliente() {
    if (!this.validarFormulario()) return;

    try {
      const formData = this.obtenerDatosFormulario();
      const url = this.clienteActual
        ? "../../api/clientes/actualizar_cliente.php"
        : "../../api/clientes/guardar_cliente.php";

      const response = await fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
      });

      const data = await response.json();

      if (data.ok) {
        this.mostrarAlerta(
          this.clienteActual
            ? "Cliente actualizado correctamente"
            : "Cliente creado correctamente",
          "success",
        );

        bootstrap.Modal.getInstance(this.elementos.modal).hide();
        await this.cargarClientes();
        this.renderizarTabla();
      } else {
        throw new Error(data.error || "Error al guardar el cliente");
      }
    } catch (error) {
      console.error("Error al guardar cliente:", error);
      this.mostrarAlerta(
        "Error al guardar el cliente: " + error.message,
        "danger",
      );
    }
  }

  validarFormulario() {
    let valido = true;
    const camposRequeridos = ["codigo", "nombre_comercial", "tipo_cliente"];

    camposRequeridos.forEach((campo) => {
      const elemento = document.getElementById(campo);
      if (elemento && !elemento.value.trim()) {
        elemento.classList.add("is-invalid");
        this.mostrarMensajeError(elemento, "Este campo es obligatorio");
        valido = false;
      } else if (elemento) {
        elemento.classList.remove("is-invalid");
        elemento.classList.add("is-valid");
        this.limpiarMensajeError(elemento);
      }
    });

    // Validar email si se proporciona
    const emailElement = document.getElementById("email");
    if (emailElement && emailElement.value.trim()) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(emailElement.value.trim())) {
        emailElement.classList.add("is-invalid");
        this.mostrarMensajeError(emailElement, "Ingrese un email válido");
        valido = false;
      } else {
        emailElement.classList.remove("is-invalid");
        emailElement.classList.add("is-valid");
        this.limpiarMensajeError(emailElement);
      }
    }

    // Validar NIF/CIF si se proporciona
    const nifElement = document.getElementById("nif_cif");
    if (nifElement && nifElement.value.trim()) {
      if (!this.validarNIF(nifElement.value.trim())) {
        nifElement.classList.add("is-invalid");
        this.mostrarMensajeError(
          nifElement,
          "Formato de NIF/CIF inválido. Ejemplos válidos: 12345678Z, X1234567L, B12345674",
        );
        valido = false;
      } else {
        nifElement.classList.remove("is-invalid");
        nifElement.classList.add("is-valid");
        this.limpiarMensajeError(nifElement);
      }
    }

    if (!valido) {
      this.mostrarAlerta(
        "Por favor, complete los campos obligatorios correctamente",
        "warning",
      );
    }

    return valido;
  }

  validarNIF(nif) {
    // Convertir a mayúsculas para validación
    const nifUpper = nif.toUpperCase().trim();

    // Validación NIF/DNI español (8 dígitos + letra) o NIE (7 dígitos + letra)
    const nifRegex = /^[XYZ]?\d{7,8}[A-HJ-NP-TV-Z]$/;

    // Validación CIF español (letra + 7 dígitos + letra/número)
    const cifRegex = /^[ABCDEFGHJKLMNPQRSUVW]\d{7}[0-9A-J]$/;

    if (nifRegex.test(nifUpper)) {
      // Validación adicional del NIF/DNI - letra de control correcta
      return this.validarLetraNIF(nifUpper);
    } else if (cifRegex.test(nifUpper)) {
      // Validación CIF básica
      return true;
    }

    return false;
  }

  validarLetraNIF(nif) {
    const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
    let numero, letra;

    if (nif.startsWith("X") || nif.startsWith("Y") || nif.startsWith("Z")) {
      // NIE
      numero = nif.substring(1, 8);
      letra = nif.substring(8, 9);
    } else {
      // NIF/DNI
      numero = nif.substring(0, 8);
      letra = nif.substring(8, 9);
    }

    // Para NIE, X=0, Y=1, Z=2
    if (nif.startsWith("Y")) numero = "1" + numero;
    if (nif.startsWith("Z")) numero = "2" + numero;

    const resto = numero % 23;
    return letra === letras.substring(resto, resto + 1);
  }

  obtenerDatosFormulario() {
    const formData = {};
    const campos = [
      "cliente-id",
      "codigo",
      "nombre_comercial",
      "razon_social",
      "nif_cif",
      "direccion",
      "codigo_postal",
      "ciudad",
      "provincia",
      "pais",
      "telefono",
      "telefono2",
      "email",
      "web",
      "tipo_cliente",
      "forma_pago",
      "dias_credito",
      "limite_credito",
      "importe_acumulado",
      "saldo_pendiente",
      "observaciones",
      "contacto_principal",
      "cargo_contacto",
    ];

    campos.forEach((campo) => {
      const elemento = document.getElementById(campo);
      if (campo === "cliente-id") {
        formData[campo] = elemento ? elemento.value : "";
      } else if (elemento && elemento.value) {
        formData[campo] = elemento.value.trim();
      } else {
        formData[campo] = "";
      }
    });

    // Campos numéricos
    formData.dias_credito = parseInt(formData.dias_credito) || 0;
    formData.limite_credito = parseFloat(formData.limite_credito) || 0;
    formData.importe_acumulado = parseFloat(formData.importe_acumulado) || 0;
    formData.saldo_pendiente = parseFloat(formData.saldo_pendiente) || 0;

    // Checkboxes
    formData.activo = document.getElementById("activo").checked ? 1 : 0;
    formData.bloqueado = document.getElementById("bloqueado").checked ? 1 : 0;

    // Convertir cliente-id a id para el backend
    if (formData["cliente-id"]) {
      formData.id = formData["cliente-id"];
      delete formData["cliente-id"];
    }

    return formData;
  }

  async verCliente(id) {
    try {
      const cliente = this.clientes.find((c) => c.id === id);
      if (!cliente) throw new Error("Cliente no encontrado");

      const detallesHtml = this.generarHtmlDetalles(cliente);
      document.getElementById("detalles-cliente-content").innerHTML =
        detallesHtml;

      const modal = new bootstrap.Modal(this.elementos.modalDetalles);
      modal.show();
    } catch (error) {
      console.error("Error al ver cliente:", error);
      this.mostrarAlerta("Error al cargar los detalles del cliente", "danger");
    }
  }

  generarHtmlDetalles(cliente) {
    return `
      <div class="row">
        <div class="col-md-6">
          <h6 class="text-primary mb-3">Información General</h6>
          <table class="table table-sm">
            <tr><td><strong>Código:</strong></td><td>${cliente.codigo}</td></tr>
            <tr><td><strong>Nombre Comercial:</strong></td><td>${cliente.nombre_comercial}</td></tr>
            <tr><td><strong>Razón Social:</strong></td><td>${cliente.razon_social || "-"}</td></tr>
            <tr><td><strong>NIF/CIF:</strong></td><td>${cliente.nif_cif || "-"}</td></tr>
            <tr><td><strong>Tipo:</strong></td><td>${this.getTipoBadge(cliente.tipo_cliente)}</td></tr>
            <tr><td><strong>Estado:</strong></td><td>${cliente.activo ? (cliente.bloqueado ? "Bloqueado" : "Activo") : "Inactivo"}</td></tr>
          </table>
        </div>
        <div class="col-md-6">
          <h6 class="text-primary mb-3">Información de Contacto</h6>
          <table class="table table-sm">
            <tr><td><strong>Contacto Principal:</strong></td><td>${cliente.contacto_principal || "-"}</td></tr>
            <tr><td><strong>Cargo:</strong></td><td>${cliente.cargo_contacto || "-"}</td></tr>
            <tr><td><strong>Teléfono:</strong></td><td>${cliente.telefono || "-"}</td></tr>
            <tr><td><strong>Teléfono 2:</strong></td><td>${cliente.telefono2 || "-"}</td></tr>
            <tr><td><strong>Email:</strong></td><td>${cliente.email || "-"}</td></tr>
            <tr><td><strong>Web:</strong></td><td>${cliente.web ? `<a href="${cliente.web}" target="_blank">${cliente.web}</a>` : "-"}</td></tr>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <h6 class="text-primary mb-3">Dirección</h6>
          <table class="table table-sm">
            <tr><td><strong>Dirección:</strong></td><td>${cliente.direccion || "-"}</td></tr>
            <tr><td><strong>C.P.:</strong></td><td>${cliente.codigo_postal || "-"}</td></tr>
            <tr><td><strong>Ciudad:</strong></td><td>${cliente.ciudad || "-"}</td></tr>
            <tr><td><strong>Provincia:</strong></td><td>${cliente.provincia || "-"}</td></tr>
            <tr><td><strong>País:</strong></td><td>${cliente.pais || "-"}</td></tr>
          </table>
        </div>
        <div class="col-md-6">
          <h6 class="text-primary mb-3">Información de Facturación</h6>
          <table class="table table-sm">
            <tr><td><strong>Forma de Pago:</strong></td><td>${cliente.forma_pago || "-"}</td></tr>
            <tr><td><strong>Días de Crédito:</strong></td><td>${cliente.dias_credito || 0}</td></tr>
            <tr><td><strong>Límite de Crédito:</strong></td><td>€${parseFloat(cliente.limite_credito || 0).toFixed(2)}</td></tr>
            <tr><td><strong>Saldo Pendiente:</strong></td><td>€${parseFloat(cliente.saldo_pendiente || 0).toFixed(2)}</td></tr>
            <tr><td><strong>Importe Acumulado:</strong></td><td>€${parseFloat(cliente.importe_acumulado || 0).toFixed(2)}</td></tr>
          </table>
        </div>
      </div>
      ${
        cliente.observaciones
          ? `
        <div class="row">
          <div class="col-12">
            <h6 class="text-primary mb-3">Observaciones</h6>
            <p class="text-muted">${cliente.observaciones}</p>
          </div>
        </div>
      `
          : ""
      }
    `;
  }

  editarDesdeDetalles() {
    if (this.clienteActual) {
      bootstrap.Modal.getInstance(this.elementos.modalDetalles).hide();
      this.editarCliente(this.clienteActual.id);
    }
  }

  eliminarCliente(id) {
    const cliente = this.clientes.find((c) => c.id === id);
    if (!cliente) return;

    this.clienteActual = cliente;
    document.getElementById("nombre-cliente-eliminar").textContent =
      cliente.nombre_comercial;

    const modal = new bootstrap.Modal(this.elementos.modalEliminar);
    modal.show();
  }

  async confirmarEliminar() {
    if (!this.clienteActual) return;

    try {
      const response = await fetch("../../api/clientes/eliminar_cliente.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: this.clienteActual.id }),
      });

      const data = await response.json();

      if (data.ok) {
        this.mostrarAlerta("Cliente eliminado correctamente", "success");
        bootstrap.Modal.getInstance(this.elementos.modalEliminar).hide();
        await this.cargarClientes();
        this.renderizarTabla();
      } else {
        throw new Error(data.error || "Error al eliminar el cliente");
      }
    } catch (error) {
      console.error("Error al eliminar cliente:", error);
      this.mostrarAlerta(
        "Error al eliminar el cliente: " + error.message,
        "danger",
      );
    }
  }

  buscarCliente(termino) {
    this.filtros.busqueda = termino;
    this.paginaActual = 1;
    this.renderizarTabla();
  }

  aplicarFiltro(tipo, valor) {
    this.filtros[tipo] = valor;
    this.paginaActual = 1;
    this.renderizarTabla();
  }

  seleccionarTodos(seleccionado) {
    this.clientesSeleccionados = seleccionado
      ? this.filtrarClientes().map((c) => c.id)
      : [];

    document.querySelectorAll(".seleccionar-cliente").forEach((checkbox) => {
      checkbox.checked = seleccionado;
    });

    this.renderizarTabla();
  }

  generarCodigo() {
    const tipo = document.getElementById("tipo_cliente").value;
    if (!tipo) return;

    // Encontrar el último código para este tipo
    const tipoPrefix = {
      particular: "PAR",
      empresa: "EMP",
      autonomo: "AUT",
      ong: "ONG",
      publico: "PUB",
    };

    const prefix = tipoPrefix[tipo] || "CLI";
    const clientesTipo = this.clientes.filter((c) => c.tipo_cliente === tipo);
    let maxNum = 0;

    clientesTipo.forEach((cliente) => {
      const match = cliente.codigo.match(new RegExp(`^${prefix}(\\d+)$`));
      if (match) {
        const num = parseInt(match[1]);
        if (num > maxNum) maxNum = num;
      }
    });

    const nuevoCodigo = `${prefix}${String(maxNum + 1).padStart(4, "0")}`;
    document.getElementById("codigo").value = nuevoCodigo;
  }

  async exportarClientes() {
    try {
      const clientesFiltrados = this.filtrarClientes();

      // Crear CSV
      const headers = [
        "Código",
        "Nombre Comercial",
        "Razón Social",
        "NIF/CIF",
        "Tipo",
        "Teléfono",
        "Email",
        "Dirección",
        "Ciudad",
        "Provincia",
        "País",
        "Forma Pago",
        "Días Crédito",
        "Límite Crédito",
        "Activo",
      ];

      const csvContent = [
        headers.join(";"),
        ...clientesFiltrados.map((cliente) =>
          [
            cliente.codigo,
            cliente.nombre_comercial,
            cliente.razon_social || "",
            cliente.nif_cif || "",
            cliente.tipo_cliente,
            cliente.telefono || "",
            cliente.email || "",
            cliente.direccion || "",
            cliente.ciudad || "",
            cliente.provincia || "",
            cliente.pais || "",
            cliente.forma_pago || "",
            cliente.dias_credito || 0,
            cliente.limite_credito || 0,
            cliente.activo ? "Sí" : "No",
          ].join(";"),
        ),
      ].join("\n");

      // Descargar archivo
      const blob = new Blob(["\ufeff" + csvContent], {
        type: "text/csv;charset=utf-8;",
      });
      const link = document.createElement("a");
      link.href = URL.createObjectURL(blob);
      link.download = `clientes_${new Date().toISOString().split("T")[0]}.csv`;
      link.click();

      this.mostrarAlerta("Clientes exportados correctamente", "success");
    } catch (error) {
      console.error("Error al exportar clientes:", error);
      this.mostrarAlerta(
        "Error al exportar clientes: " + error.message,
        "danger",
      );
    }
  }

  importarClientes() {
    // Crear input file oculto
    const input = document.createElement("input");
    input.type = "file";
    input.accept = ".csv,.xlsx,.xls";

    input.onchange = async (e) => {
      const file = e.target.files[0];
      if (!file) return;

      try {
        this.mostrarLoading(true);
        // Aquí iría la lógica de importación
        // Por ahora solo mostramos un mensaje
        this.mostrarAlerta("Función de importación en desarrollo", "info");
      } catch (error) {
        console.error("Error al importar clientes:", error);
        this.mostrarAlerta(
          "Error al importar clientes: " + error.message,
          "danger",
        );
      } finally {
        this.mostrarLoading(false);
      }
    };

    input.click();
  }

  mostrarLoading(mostrar) {
    if (mostrar) {
      this.elementos.tbody.innerHTML =
        '<tr><td colspan="9" class="text-center"><div class="loading"></div></td></tr>';
    }
  }

  mostrarAlerta(mensaje, tipo = "info") {
    // Crear alerta
    const alert = document.createElement("div");
    alert.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
    alert.style.cssText =
      "top: 20px; right: 20px; z-index: 9999; min-width: 300px;";
    alert.innerHTML = `
      ${mensaje}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alert);

    // Auto-eliminar después de 5 segundos
    setTimeout(() => {
      if (alert.parentNode) {
        alert.parentNode.removeChild(alert);
      }
    }, 5000);
  }

  mostrarMensajeError(elemento, mensaje) {
    // Eliminar mensaje anterior si existe
    this.limpiarMensajeError(elemento);

    // Crear mensaje de error
    const errorDiv = document.createElement("div");
    errorDiv.className = "invalid-feedback";
    errorDiv.textContent = mensaje;

    // Insertar después del elemento
    elemento.parentNode.appendChild(errorDiv);
  }

  limpiarMensajeError(elemento) {
    // Buscar y eliminar mensajes de error existentes
    const mensajesError =
      elemento.parentNode.querySelectorAll(".invalid-feedback");
    mensajesError.forEach((msg) => msg.remove());
  }
}

// Inicializar la página cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
  window.clientesPage = new ClientesPage();
});
