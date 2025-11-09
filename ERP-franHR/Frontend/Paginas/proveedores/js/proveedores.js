class ProveedoresPage {
  constructor() {
    this.proveedores = [];
    this.paginaActual = 1;
    this.proveedoresPorPagina = 10;
    this.totalProveedores = 0;
    this.proveedorActual = null;
    this.filtros = {
      busqueda: "",
      tipo: "",
      estado: "",
    };

    this.init();
  }

  init() {
    this.cargarConfiguracion();
    this.inicializarEventos();
    this.cargarDatosIniciales();
  }

  cargarConfiguracion() {
    // Usar la variable definida en el PHP
    if (typeof window.API_BASE_URL !== "undefined") {
      this.baseUrl = window.API_BASE_URL;
    } else {
      // Fallback - detectar automáticamente la ruta base correcta
      if (typeof window !== "undefined" && window.location) {
        const pathname = window.location.pathname;

        // Si accedemos desde localhost/Paginas/proveedores/proveedores.php
        if (pathname.includes("/Paginas/")) {
          this.baseUrl = "../api/";
        }
        // Si accedemos desde localhost/Frontend/Paginas/proveedores/proveedores.php
        else if (pathname.includes("/Frontend/Paginas/")) {
          this.baseUrl = "../../api/";
        }
        // Si accedemos directamente desde raíz
        else {
          this.baseUrl = "api/";
        }
      } else {
        // Fallback final
        this.baseUrl = "../api/";
      }
    }

    console.log("Base URL configurada:", this.baseUrl);
  }

  inicializarEventos() {
    // Botón nuevo proveedor
    const nuevoBtn = document.getElementById("nuevo-proveedor-btn");
    if (nuevoBtn) {
      nuevoBtn.addEventListener("click", () => this.mostrarModalProveedor());
    }

    // Búsqueda
    const buscarInput = document.getElementById("buscar-proveedor");
    if (buscarInput) {
      buscarInput.addEventListener(
        "input",
        this.debounce(() => {
          this.filtros.busqueda = buscarInput.value.trim();
          this.paginaActual = 1;
          this.cargarProveedores();
        }, 500),
      );
    }

    // Filtros
    const filtroTipo = document.getElementById("filtro-tipo");
    if (filtroTipo) {
      filtroTipo.addEventListener("change", () => {
        this.filtros.tipo = filtroTipo.value;
        this.paginaActual = 1;
        this.cargarProveedores();
      });
    }

    const filtroEstado = document.getElementById("filtro-estado");
    if (filtroEstado) {
      filtroEstado.addEventListener("change", () => {
        this.filtros.estado = filtroEstado.value;
        this.paginaActual = 1;
        this.cargarProveedores();
      });
    }

    // Formulario del modal
    const form = document.getElementById("form-proveedor");
    if (form) {
      form.addEventListener("submit", (e) => {
        e.preventDefault();
        this.guardarProveedor();
      });
    }

    // Validación de NIF/CIF
    const nifInput = document.getElementById("nif_cif");
    if (nifInput) {
      nifInput.addEventListener("blur", () => {
        if (nifInput.value) {
          this.validarNIFCIF(nifInput.value);
        }
      });
    }
  }

  async cargarDatosIniciales() {
    try {
      await this.cargarProveedores();
    } catch (error) {
      console.error("Error al cargar datos iniciales:", error);
      this.mostrarNotificacion("Error al cargar los datos iniciales", "error");
    }
  }

  async cargarProveedores() {
    try {
      this.mostrarLoading(true);

      const params = new URLSearchParams({
        pagina: this.paginaActual,
        limite: this.proveedoresPorPagina,
        ...this.filtros,
      });

      const response = await this.apiCall(
        `proveedores/obtener_proveedores.php?${params}`,
      );

      if (response.ok) {
        this.proveedores = response.proveedores || [];
        this.totalProveedores = response.paginacion.total_registros;

        this.renderizarProveedores();
        this.renderizarPaginacion(response.paginacion);
        this.actualizarInfoPaginacion(response.paginacion);
      } else {
        throw new Error(response.error || "Error al cargar proveedores");
      }
    } catch (error) {
      console.error("Error al cargar proveedores:", error);
      this.mostrarNotificacion("Error al cargar los proveedores", "error");
    } finally {
      console.log("Entrando en finally de cargarProveedores");
      this.mostrarLoading(false);
      this.ocultarTodosLosLoadings();
    }
  }

  renderizarProveedores() {
    const tbody = document.getElementById("proveedores-tbody");
    const noProveedores = document.getElementById("no-proveedores");

    if (!tbody) return;

    if (this.proveedores.length === 0) {
      tbody.innerHTML = "";
      if (noProveedores) noProveedores.style.display = "block";
      return;
    }

    if (noProveedores) noProveedores.style.display = "none";

    tbody.innerHTML = this.proveedores
      .map(
        (proveedor) => `
            <tr class="fade-in">
                <td>
                    <input type="checkbox" class="form-check-input seleccionar-proveedor"
                           data-id="${proveedor.id}">
                </td>
                <td>
                    <div class="fw-bold">${this.escapeHtml(proveedor.codigo)}</div>
                </td>
                <td>
                    <div class="fw-semibold">${this.escapeHtml(proveedor.nombre_comercial)}</div>
                    ${proveedor.razon_social ? `<small class="text-muted">${this.escapeHtml(proveedor.razon_social)}</small>` : ""}
                </td>
                <td>
                    <div class="contacto-info">
                        ${proveedor.email ? `<div><a href="mailto:${proveedor.email}" class="email">${this.escapeHtml(proveedor.email)}</a></div>` : ""}
                        ${proveedor.telefono ? `<div><i class="fas fa-phone"></i> ${this.escapeHtml(proveedor.telefono)}</div>` : ""}
                        ${proveedor.contacto_principal ? `<div><small>Contacto: ${this.escapeHtml(proveedor.contacto_principal)}</small></div>` : ""}
                    </div>
                </td>
                <td>
                    <span class="tipo-proveedor tipo-${proveedor.tipo_proveedor}">
                        ${this.getTipoProveedorLabel(proveedor.tipo_proveedor)}
                    </span>
                </td>
                <td>
                    <span class="badge ${this.getEstadoBadgeClass(proveedor.estado)}">
                        ${this.getEstadoLabel(proveedor.estado)}
                    </span>
                    ${proveedor.es_proveedor_urgente ? '<span class="urgente-badge ms-1">URGENTE</span>' : ""}
                </td>
                <td>
                    <div class="importe-acumulado">€${this.formatearMoneda(proveedor.importe_acumulado)}</div>
                    ${proveedor.saldo_pendiente > 0 ? `<div class="saldo-pendiente">Pendiente: €${this.formatearMoneda(proveedor.saldo_pendiente)}</div>` : ""}
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-action"
                                onclick="proveedoresPage.editarProveedor(${proveedor.id})"
                                title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info btn-action"
                                onclick="proveedoresPage.verDetalles(${proveedor.id})"
                                title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-action"
                                onclick="proveedoresPage.eliminarProveedor(${proveedor.id})"
                                title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `,
      )
      .join("");

    // Actualizar checkboxes
    this.actualizarCheckboxes();

    // Forzar ocultar loading después de renderizar
    setTimeout(() => {
      this.mostrarLoading(false);
      this.ocultarTodosLosLoadings();
    }, 100);
  }

  renderizarPaginacion(paginacion) {
    const pagination = document.getElementById("pagination");
    if (!pagination) return;

    if (paginacion.total_paginas <= 1) {
      pagination.innerHTML = "";
      return;
    }

    let html = "";

    // Previous
    html += `
            <li class="page-item ${paginacion.pagina_actual === 1 ? "disabled" : ""}">
                <a class="page-link" href="#" onclick="proveedoresPage.cambiarPagina(${paginacion.pagina_actual - 1}); return false;">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;

    // Pages
    const startPage = Math.max(1, paginacion.pagina_actual - 2);
    const endPage = Math.min(
      paginacion.total_paginas,
      paginacion.pagina_actual + 2,
    );

    if (startPage > 1) {
      html +=
        '<li class="page-item"><a class="page-link" href="#" onclick="proveedoresPage.cambiarPagina(1); return false;">1</a></li>';
      if (startPage > 2) {
        html +=
          '<li class="page-item disabled"><a class="page-link">...</a></li>';
      }
    }

    for (let i = startPage; i <= endPage; i++) {
      html += `
                <li class="page-item ${i === paginacion.pagina_actual ? "active" : ""}">
                    <a class="page-link" href="#" onclick="proveedoresPage.cambiarPagina(${i}); return false;">${i}</a>
                </li>
            `;
    }

    if (endPage < paginacion.total_paginas) {
      if (endPage < paginacion.total_paginas - 1) {
        html +=
          '<li class="page-item disabled"><a class="page-link">...</a></li>';
      }
      html += `<li class="page-item"><a class="page-link" href="#" onclick="proveedoresPage.cambiarPagina(${paginacion.total_paginas}); return false;">${paginacion.total_paginas}</a></li>`;
    }

    // Next
    html += `
            <li class="page-item ${paginacion.pagina_actual === paginacion.total_paginas ? "disabled" : ""}">
                <a class="page-link" href="#" onclick="proveedoresPage.cambiarPagina(${paginacion.pagina_actual + 1}); return false;">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;

    pagination.innerHTML = html;
  }

  actualizarInfoPaginacion(paginacion) {
    const desde = document.getElementById("proveedores-desde");
    const hasta = document.getElementById("proveedores-hasta");
    const total = document.getElementById("proveedores-total");

    if (desde) desde.textContent = paginacion.desde;
    if (hasta) hasta.textContent = paginacion.hasta;
    if (total) total.textContent = paginacion.total_registros;
  }

  actualizarCheckboxes() {
    const checkboxTodos = document.getElementById("seleccionar-todos");
    const checkboxes = document.querySelectorAll(".seleccionar-proveedor");

    if (checkboxTodos) {
      checkboxTodos.addEventListener("change", (e) => {
        checkboxes.forEach((checkbox) => {
          checkbox.checked = e.target.checked;
        });
      });
    }
  }

  cambiarPagina(pagina) {
    if (
      pagina < 1 ||
      pagina > Math.ceil(this.totalProveedores / this.proveedoresPorPagina)
    ) {
      return;
    }
    this.paginaActual = pagina;
    this.cargarProveedores();
  }

  async mostrarModalProveedor(proveedorId = null) {
    try {
      this.proveedorActual = null;

      // Limpiar formulario
      const form = document.getElementById("form-proveedor");
      if (form) {
        form.reset();
        this.limpiarValidacion();
      }

      // Actualizar título
      const modalTitle = document.querySelector(
        "#modal-proveedor .modal-title",
      );
      if (modalTitle) {
        modalTitle.textContent = proveedorId
          ? "Editar Proveedor"
          : "Nuevo Proveedor";
      }

      if (proveedorId) {
        // Cargar datos del proveedor
        const response = await this.apiCall(
          `proveedores/obtener_proveedor.php?id=${proveedorId}`,
        );

        if (response.ok) {
          this.proveedorActual = response.proveedor;
          this.cargarFormularioProveedor(response.proveedor);
        } else {
          throw new Error(response.error || "Error al cargar el proveedor");
        }
      }

      // Mostrar modal
      const modal = new bootstrap.Modal(
        document.getElementById("modal-proveedor"),
      );
      modal.show();
    } catch (error) {
      console.error("Error al mostrar modal:", error);
      this.mostrarNotificacion("Error al cargar el formulario", "error");
    }
  }

  cargarFormularioProveedor(proveedor) {
    // Cargar campos principales
    const campos = [
      "id",
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
      "tipo_proveedor",
      "forma_pago",
      "cuenta_bancaria",
      "swift_bic",
      "dias_pago",
      "descuento_comercial",
      "observaciones",
      "contacto_principal",
      "cargo_contacto",
    ];

    campos.forEach((campo) => {
      const element = document.getElementById(campo);
      if (element && proveedor[campo] !== null) {
        element.value = proveedor[campo];
      }
    });

    // Checkboxes
    document.getElementById("activo").checked = proveedor.activo == 1;
    document.getElementById("bloqueado").checked = proveedor.bloqueado == 1;
    document.getElementById("es_proveedor_urgente").checked =
      proveedor.es_proveedor_urgente == 1;
  }

  async guardarProveedor() {
    try {
      if (!this.validarFormulario()) {
        return;
      }

      const formData = new FormData(document.getElementById("form-proveedor"));
      const data = Object.fromEntries(formData.entries());

      // Convertir checkboxes
      data.activo = document.getElementById("activo").checked ? 1 : 0;
      data.bloqueado = document.getElementById("bloqueado").checked ? 1 : 0;
      data.es_proveedor_urgente = document.getElementById(
        "es_proveedor_urgente",
      ).checked
        ? 1
        : 0;

      // Convertir valores numéricos
      if (data.dias_pago) data.dias_pago = parseFloat(data.dias_pago);
      if (data.descuento_comercial)
        data.descuento_comercial = parseFloat(data.descuento_comercial);

      // Añadir ID si es edición
      if (this.proveedorActual) {
        data.id = this.proveedorActual.id;
      }

      this.mostrarLoading(true, "Guardando proveedor...");

      const response = await this.apiCall("proveedores/guardar_proveedor.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });

      if (response.ok) {
        this.mostrarNotificacion(response.mensaje, "success");

        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(
          document.getElementById("modal-proveedor"),
        );
        modal.hide();

        // Recargar lista
        await this.cargarProveedores();
      } else {
        throw new Error(response.error || "Error al guardar el proveedor");
      }
    } catch (error) {
      console.error("Error al guardar proveedor:", error);
      this.mostrarNotificacion(
        error.message || "Error al guardar el proveedor",
        "error",
      );
    } finally {
      this.mostrarLoading(false);
    }
  }

  async editarProveedor(id) {
    await this.mostrarModalProveedor(id);
  }

  async verDetalles(id) {
    try {
      const response = await this.apiCall(
        `proveedores/obtener_proveedor.php?id=${id}`,
      );

      if (response.ok) {
        // Aquí podrías mostrar un modal con los detalles completos
        // Por ahora simplemente editamos
        await this.editarProveedor(id);
      } else {
        throw new Error(response.error || "Error al cargar los detalles");
      }
    } catch (error) {
      console.error("Error al ver detalles:", error);
      this.mostrarNotificacion(
        "Error al cargar los detalles del proveedor",
        "error",
      );
    }
  }

  async eliminarProveedor(id) {
    const proveedor = this.proveedores.find((p) => p.id === id);
    if (!proveedor) return;

    const confirmMessage = `¿Estás seguro de que deseas eliminar al proveedor "${proveedor.nombre_comercial}"?`;

    if (!confirm(confirmMessage)) {
      return;
    }

    try {
      this.mostrarLoading(true, "Eliminando proveedor...");

      const response = await this.apiCall(
        "proveedores/eliminar_proveedor.php",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ id }),
        },
      );

      if (response.ok) {
        this.mostrarNotificacion(response.mensaje, "success");
        await this.cargarProveedores();
      } else {
        throw new Error(response.error || "Error al eliminar el proveedor");
      }
    } catch (error) {
      console.error("Error al eliminar proveedor:", error);
      this.mostrarNotificacion(
        error.message || "Error al eliminar el proveedor",
        "error",
      );
    } finally {
      this.mostrarLoading(false);
    }
  }

  validarFormulario() {
    let valido = true;
    this.limpiarValidacion();

    // Validar campos obligatorios
    const obligatorios = ["nombre_comercial", "tipo_proveedor"];

    obligatorios.forEach((campo) => {
      const element = document.getElementById(campo);
      if (element && !element.value.trim()) {
        this.mostrarErrorValidacion(element, "Este campo es obligatorio");
        valido = false;
      }
    });

    // Validar email si se proporciona
    const email = document.getElementById("email");
    if (email && email.value.trim() && !this.validarEmail(email.value)) {
      this.mostrarErrorValidacion(email, "Email inválido");
      valido = false;
    }

    return valido;
  }

  limpiarValidacion() {
    document.querySelectorAll(".is-invalid").forEach((element) => {
      element.classList.remove("is-invalid");
    });
    document.querySelectorAll(".invalid-feedback").forEach((element) => {
      element.remove();
    });
  }

  mostrarErrorValidacion(element, mensaje) {
    element.classList.add("is-invalid");

    const feedback = document.createElement("div");
    feedback.className = "invalid-feedback";
    feedback.textContent = mensaje;

    element.parentNode.appendChild(feedback);
  }

  validarEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  validarNIFCIF(nif) {
    // Validación básica de NIF/CIF español
    const re = /^[XYZ]?\d{7,8}[A-Z]$/i;
    return re.test(nif.replace(/\s/g, "").toUpperCase());
  }

  // Métodos utilitarios
  getTipoProveedorLabel(tipo) {
    const tipos = {
      material: "Material",
      servicio: "Servicio",
      transporte: "Transporte",
      seguro: "Seguro",
      suministro: "Suministro",
      tecnologia: "Tecnología",
    };
    return tipos[tipo] || tipo;
  }

  getEstadoLabel(estado) {
    const estados = {
      activo: "Activo",
      inactivo: "Inactivo",
      bloqueado: "Bloqueado",
    };
    return estados[estado] || estado;
  }

  getEstadoBadgeClass(estado) {
    const clases = {
      activo: "badge-activo",
      inactivo: "badge-inactivo",
      bloqueado: "badge-bloqueado",
    };
    return clases[estado] || "bg-secondary";
  }

  formatearMoneda(valor) {
    return parseFloat(valor).toFixed(2).replace(".", ",");
  }

  escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  }

  debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  mostrarLoading(mostrar, mensaje = "") {
    const loadingOverlay = document.getElementById("loading-overlay");
    if (!loadingOverlay) {
      console.log("Loading overlay no encontrado");
      return;
    }

    console.log("mostrarLoading llamado con:", mostrar, mensaje);
    if (mostrar) {
      loadingOverlay.style.display = "flex";
      loadingOverlay.style.visibility = "visible";
      loadingOverlay.style.opacity = "1";
      const loadingText = loadingOverlay.querySelector(".loading-text");
      if (loadingText && mensaje) {
        loadingText.textContent = mensaje;
      }
    } else {
      loadingOverlay.style.display = "none";
      loadingOverlay.style.visibility = "hidden";
      loadingOverlay.style.opacity = "0";
      console.log("Loading overlay ocultado completamente");
    }
  }

  // Método de emergencia para ocultar todos los posibles elementos de carga
  ocultarTodosLosLoadings() {
    // Ocultar loading overlay principal
    const loadingOverlay = document.getElementById("loading-overlay");
    if (loadingOverlay) {
      loadingOverlay.style.display = "none";
      loadingOverlay.style.visibility = "hidden";
      loadingOverlay.style.opacity = "0";
    }

    // Ocultar todos los spinners
    const spinners = document.querySelectorAll(
      ".spinner-border, .loading-spinner",
    );
    spinners.forEach((spinner) => {
      spinner.style.display = "none";
    });

    // Ocultar elementos con clase loading
    const loadingElements = document.querySelectorAll(".loading");
    loadingElements.forEach((element) => {
      element.classList.remove("loading");
    });

    console.log("Todos los elementos de carga ocultados");
  }

  mostrarNotificacion(mensaje, tipo = "info") {
    // Crear toast si no existe
    let toastContainer = document.getElementById("toast-container");
    if (!toastContainer) {
      toastContainer = document.createElement("div");
      toastContainer.id = "toast-container";
      toastContainer.className =
        "toast-container position-fixed bottom-0 end-0 p-3";
      document.body.appendChild(toastContainer);
    }

    const toastId = "toast-" + Date.now();
    const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${tipo === "error" ? "danger" : tipo === "success" ? "success" : "primary"} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${this.escapeHtml(mensaje)}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

    toastContainer.insertAdjacentHTML("beforeend", toastHTML);

    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
      autohide: true,
      delay: 5000,
    });

    toast.show();

    toastElement.addEventListener("hidden.bs.toast", () => {
      toastElement.remove();
    });
  }

  async apiCall(url, options = {}) {
    try {
      const fullUrl = this.baseUrl + url;
      console.log("API Call:", fullUrl);

      const response = await fetch(fullUrl, {
        ...options,
        headers: {
          "Content-Type": "application/json",
          ...options.headers,
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      return data;
    } catch (error) {
      console.error("API call error:", error);
      throw error;
    }
  }
}

// Inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
  window.proveedoresPage = new ProveedoresPage();
});
