class ModulosDashboard {
  constructor() {
    this.modulos = [];
    this.modal = null;
    this.modalProgreso = null;
    this.filtroActual = "todos";
    this.init();
  }

  init() {
    // Obtener elementos del DOM
    this.modal = document.getElementById("modal-confirmacion");
    this.modalProgreso = document.getElementById("modal-progreso");

    // Obtener datos de los módulos desde PHP
    this.cargarModulosDesdePHP();

    // Configurar eventos
    this.setupEventListeners();

    // Configurar filtros
    this.setupFiltros();
  }

  cargarModulosDesdePHP() {
    // Los módulos ya están cargados en el DOM como datos attributes
    const modulosElements = document.querySelectorAll(".modulo-card");
    this.modulos = Array.from(modulosElements).map((element) => {
      return {
        id: parseInt(element.dataset.moduloId),
        estado: element.dataset.estado,
        nombreTecnico: element.dataset.nombreTecnico,
        element: element,
      };
    });
  }

  setupEventListeners() {
    // Cerrar modales con Escape
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        this.cerrarModal();
      }
    });

    // Cerrar modales al hacer clic fuera
    document.querySelectorAll(".modal").forEach((modal) => {
      modal.addEventListener("click", (e) => {
        if (e.target === modal) {
          this.cerrarModal();
        }
      });
    });
  }

  setupFiltros() {
    const filtroBtns = document.querySelectorAll(".filtro-btn");

    filtroBtns.forEach((btn) => {
      btn.addEventListener("click", () => {
        // Actualizar botón activo
        filtroBtns.forEach((b) => b.classList.remove("active"));
        btn.classList.add("active");

        // Aplicar filtro
        this.filtroActual = btn.dataset.filtro;
        this.aplicarFiltro();
      });
    });
  }

  aplicarFiltro() {
    const moduloCards = document.querySelectorAll(".modulo-card");

    moduloCards.forEach((card) => {
      const estado = card.dataset.estado;
      let mostrar = false;

      switch (this.filtroActual) {
        case "todos":
          mostrar = true;
          break;
        case "activos":
          mostrar = estado === "activo";
          break;
        case "inactivos":
          mostrar = estado === "inactivo";
          break;
        case "no-instalados":
          mostrar = estado === "no-instalado";
          break;
      }

      card.style.display = mostrar ? "block" : "none";
    });
  }

  // Métodos de gestión de módulos
  async instalarModulo(moduloId) {
    const modulo = this.modulos.find((m) => m.id === moduloId);
    if (!modulo) return;

    this.mostrarModalConfirmacion(
      "Instalar Módulo",
      `¿Estás seguro de que quieres instalar el módulo <strong>${this.getModuloNombre(moduloId)}</strong>?`,
      "<p>Este módulo se añadirá al sistema y podrá ser activado posteriormente.</p>",
      async () => {
        await this.realizarAccion(
          moduloId,
          "POST",
          "Instalando módulo...",
          { id: moduloId }
        );
      },
    );
  }

  async activarModulo(moduloId) {
    const modulo = this.modulos.find((m) => m.id === moduloId);
    if (!modulo) return;

    this.mostrarModalConfirmacion(
      "Activar Módulo",
      `¿Estás seguro de que quieres activar el módulo <strong>${this.getModuloNombre(moduloId)}</strong>?`,
      "<p>El módulo aparecerá en el menú principal y estará disponible para los usuarios.</p>",
      async () => {
        await this.realizarAccion(
          moduloId,
          "PUT",
          "Activando módulo...",
          { id: moduloId, accion: "activar" }
        );
      },
    );
  }

  async desactivarModulo(moduloId) {
    const modulo = this.modulos.find((m) => m.id === moduloId);
    if (!modulo) return;

    this.mostrarModalConfirmacion(
      "Desactivar Módulo",
      `¿Estás seguro de que quieres desactivar el módulo <strong>${this.getModuloNombre(moduloId)}</strong>?`,
      "<p><strong>Advertencia:</strong> El módulo dejará de estar disponible para todos los usuarios.</p>",
      async () => {
        await this.realizarAccion(
          moduloId,
          "PUT",
          "Desactivando módulo...",
          { id: moduloId, accion: "desactivar" }
        );
      },
    );
  }

  async desinstalarModulo(moduloId) {
    const modulo = this.modulos.find((m) => m.id === moduloId);
    if (!modulo) return;

    this.mostrarModalConfirmacion(
      "Desinstalar Módulo",
      `¿Estás seguro de que quieres desinstalar el módulo <strong>${this.getModuloNombre(moduloId)}</strong>?`,
      "<p><strong>¡ADVERTENCIA!</strong> Esta acción eliminará:</p>" +
        "<ul>" +
        "<li>Toda la configuración del módulo</li>" +
        "<li>Permisos asignados</li>" +
        "<li>Datos asociados (si existen)</li>" +
        "</ul>" +
        "<p>Esta acción no se puede deshacer.</p>",
      async () => {
        await this.realizarAccion(
          moduloId,
          "DELETE",
          "Desinstalando módulo...",
          null
        );
      },
    );
  }

  async realizarAccion(moduloId, method, datosProgreso, datosBody) {
    this.cerrarModal();
    this.mostrarModalProgreso("Procesando...", datosProgreso);

    try {
      const opciones = {
        method: method,
        headers: {
          "Content-Type": "application/json",
        },
      };

      if (method !== "GET") {
        if (method === "DELETE") {
          opciones.body = null;
        } else {
          if (typeof datosBody === "object" && datosBody) {
            opciones.body = JSON.stringify(datosBody);
          }
        }
      }

      const url =
        method === "DELETE"
          ? `/modulos/api/gestion_modulos.php?id=${moduloId}`
          : `/modulos/api/gestion_modulos.php`;

      const response = await fetch(url, opciones);
      const data = await response.json();

      if (!data.success) {
        throw new Error(data.message || "Error en la operación");
      }

      this.cerrarModal();
      this.mostrarExito(data.message);

      // Actualizar la interfaz
      await this.actualizarModuloEnDOM(moduloId, method, datosBody);

      // Actualizar menú principal si está disponible
      this.actualizarMenuPrincipal();
    } catch (error) {
      console.error("Error en la acción:", error);
      this.cerrarModal();
      this.mostrarError(error.message || "Error al realizar la acción");
    }
  }

  async actualizarModuloEnDOM(moduloId, method, datosBody) {
    const elemento = document.querySelector(`[data-modulo-id="${moduloId}"]`);
    if (!elemento) return;

    let nuevoEstado;

    if (method === "POST") {
      nuevoEstado = "inactivo";
    } else if (method === "PUT") {
      if (datosBody && datosBody.accion === "activar") {
        nuevoEstado = "activo";
      } else {
        nuevoEstado = "inactivo";
      }
    } else if (method === "DELETE") {
      nuevoEstado = "no-instalado";
    }

    // Actualizar clases
    elemento.className = elemento.className.replace(
      /estado-\w+/,
      `estado-${nuevoEstado}`,
    );
    elemento.dataset.estado = nuevoEstado;

    // Actualizar header
    const estadoElement = elemento.querySelector(".modulo-estado i");
    const estadoText = elemento.querySelector(".modulo-estado span");

    if (estadoElement) {
      estadoElement.className =
        nuevoEstado === "activo"
          ? "fas fa-check-circle"
          : nuevoEstado === "inactivo"
            ? "fas fa-pause-circle"
            : "fas fa-times-circle";
    }

    if (estadoText) {
      estadoText.textContent = this.getEstadoTexto(nuevoEstado);
    }

    // Actualizar botones de acción
    this.actualizarBotonesAccion(elemento, nuevoEstado);

    // Actualizar estadísticas en el header
    this.actualizarEstadisticas();
  }

  actualizarBotonesAccion(elemento, estado) {
    const actionsContainer = elemento.querySelector(".modulo-actions");
    if (!actionsContainer) return;

    const nombreTecnico = elemento.dataset.nombreTecnico;
    const moduloId = elemento.dataset.moduloId;

    let botonesHTML = "";

    switch (estado) {
      case "no-instalado":
        botonesHTML = `
                    <button class="btn btn-primary" onclick="modulosDashboard.instalarModulo(${moduloId})">
                        <i class="fas fa-download"></i> Instalar
                    </button>
                `;
        break;

      case "inactivo":
        botonesHTML = `
                    <button class="btn btn-success" onclick="modulosDashboard.activarModulo(${moduloId})">
                        <i class="fas fa-play"></i> Activar
                    </button>
                    <button class="btn btn-danger" onclick="modulosDashboard.desinstalarModulo(${moduloId})">
                        <i class="fas fa-trash"></i> Desinstalar
                    </button>
                `;
        break;

      case "activo":
        if (nombreTecnico !== "dashboard") {
          botonesHTML = `
                        <button class="btn btn-warning" onclick="modulosDashboard.desactivarModulo(${moduloId})">
                            <i class="fas fa-pause"></i> Desactivar
                        </button>
                        <button class="btn btn-danger" onclick="modulosDashboard.desinstalarModulo(${moduloId})">
                            <i class="fas fa-trash"></i> Desinstalar
                        </button>
                    `;
        } else {
          botonesHTML = `
                        <button class="btn btn-secondary" disabled>
                            <i class="fas fa-lock"></i> Módulo del Sistema
                        </button>
                    `;
        }
        break;
    }

    actionsContainer.innerHTML = botonesHTML;
  }

  actualizarEstadisticas() {
    const totalModulos = document.querySelectorAll(".modulo-card").length;
    const modulosActivos = document.querySelectorAll(".estado-activo").length;
    const modulosInactivos =
      document.querySelectorAll(".estado-inactivo").length;

    // Actualizar estadísticas en el header
    const statNumbers = document.querySelectorAll(".stat-number");
    if (statNumbers.length >= 3) {
      statNumbers[0].textContent = totalModulos;
      statNumbers[1].textContent = modulosActivos;
      statNumbers[2].textContent = modulosInactivos;
    }
  }

  actualizarMenuPrincipal() {
    // Si existe el gestor del menú principal, recargarlo
    if (
      window.menuManager &&
      typeof window.menuManager.actualizarMenu === "function"
    ) {
      window.menuManager.actualizarMenu();
    }
  }

  // Métodos auxiliares
  getModuloNombre(moduloId) {
    const elemento = document.querySelector(
      `[data-modulo-id="${moduloId}"] h3`,
    );
    return elemento ? elemento.textContent : `Módulo ${moduloId}`;
  }

  getEstadoTexto(estado) {
    const textos = {
      activo: "Activo",
      inactivo: "Inactivo",
      "no-instalado": "No Instalado",
    };
    return textos[estado] || estado;
  }

  // Métodos para modales
  mostrarModalConfirmacion(titulo, mensaje, detalles, onConfirmar) {
    if (!this.modal) return;

    document.getElementById("modal-titulo").textContent = titulo;
    document.getElementById("modal-mensaje").innerHTML = mensaje;
    document.getElementById("modal-detalles").innerHTML = detalles || "";

    const btnConfirmar = document.getElementById("modal-confirmar");
    btnConfirmar.onclick = onConfirmar;

    this.modal.style.display = "block";
  }

  mostrarModalProgreso(titulo, mensaje) {
    if (!this.modalProgreso) return;

    document.getElementById("progreso-titulo").textContent = titulo;
    document.getElementById("progreso-mensaje").textContent = mensaje;
    this.modalProgreso.style.display = "block";
  }

  cerrarModal() {
    if (this.modal) this.modal.style.display = "none";
    if (this.modalProgreso) this.modalProgreso.style.display = "none";
  }

  // Métodos de notificaciones
  mostrarExito(mensaje) {
    this.mostrarNotificacion(mensaje, "success");
  }

  mostrarError(mensaje) {
    this.mostrarNotificacion(mensaje, "error");
  }

  mostrarNotificacion(mensaje, tipo) {
    // Crear notificación
    const notificacion = document.createElement("div");
    notificacion.className = `notificacion notificacion-${tipo}`;
    notificacion.innerHTML = `
            <i class="fas ${tipo === "success" ? "fa-check-circle" : "fa-exclamation-circle"}"></i>
            <span>${mensaje}</span>
        `;

    document.body.appendChild(notificacion);

    // Mostrar animación
    setTimeout(() => notificacion.classList.add("mostrar"), 100);

    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
      notificacion.classList.remove("mostrar");
      setTimeout(() => notificacion.remove(), 300);
    }, 5000);
  }
}

// Inicializar cuando el DOM esté listo
let modulosDashboard;

document.addEventListener("DOMContentLoaded", () => {
  modulosDashboard = new ModulosDashboard();
});

// Hacer disponible globalmente para acceso desde onclick
window.modulosDashboard = modulosDashboard;
