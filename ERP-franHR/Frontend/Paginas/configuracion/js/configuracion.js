class ConfiguracionPage {
  constructor() {
    this.state = {
      modulos: [],
      modulosFiltrados: [],
      roles: [],
      permisosDisponibles: [],
      moduloActivo: null,
    };
    this.alertEl = document.getElementById("configuracion-alert");
    this.tablaModulosBody = document.querySelector("#tabla-modulos tbody");
    this.tablaConfigsBody = document.querySelector("#tabla-configuraciones tbody");
    this.permisosContainer = document.getElementById("permisos-container");
    this.modal = new bootstrap.Modal(document.getElementById("modal-configuracion"));
    this.configInputWrapper = document.getElementById("config-input-wrapper");
    this.menuOrderInput = document.getElementById("input-menu-order");
    this.toggleActivo = document.getElementById("toggle-activo");
    this.toggleInstalado = document.getElementById("toggle-instalado");
    this.bindEvents();
    this.cargarDatos();
  }

  bindEvents() {
    document.getElementById("buscar-modulo").addEventListener("input", () => this.aplicarFiltros());
    document.getElementById("filtro-estado").addEventListener("change", () => this.aplicarFiltros());
    document.getElementById("filtro-categoria").addEventListener("change", () => this.aplicarFiltros());
    this.toggleActivo.addEventListener("change", (e) => this.onToggleModulo(e, "activo"));
    this.toggleInstalado.addEventListener("change", (e) => this.onToggleModulo(e, "instalado"));
    this.menuOrderInput.addEventListener("change", (e) => this.actualizarOrdenMenu(e.target.value));
    document.getElementById("btn-guardar-configuracion").addEventListener("click", () => this.guardarConfiguracion());
  }

  async cargarDatos() {
    this.mostrarAlerta("Cargando configuración...", "info");
    try {
      const response = await fetch("/api/configuracion/obtener_configuracion.php");
      const data = await response.json();
      if (!data.success) throw new Error(data.message || "No se pudo cargar la configuración");
      this.state.modulos = data.data.modulos;
      this.state.modulosFiltrados = data.data.modulos;
      this.state.roles = data.data.roles;
      this.state.permisosDisponibles = data.data.permisos_disponibles;
      this.renderEstadisticas(data.data.estadisticas);
      this.renderTablaModulos();
      this.mostrarAlerta("Configuración cargada correctamente", "success", true);
    } catch (error) {
      console.error(error);
      this.mostrarAlerta(error.message, "danger");
    }
  }

  renderEstadisticas(stats) {
    document.getElementById("stat-total-modulos").textContent = stats.total_modulos;
    document.getElementById("stat-modulos-instalados").textContent = stats.modulos_instalados;
    document.getElementById("stat-modulos-activos").textContent = stats.modulos_activos;
  }

  aplicarFiltros() {
    const texto = document.getElementById("buscar-modulo").value.toLowerCase();
    const estado = document.getElementById("filtro-estado").value;
    const categoria = document.getElementById("filtro-categoria").value;
    this.state.modulosFiltrados = this.state.modulos.filter((modulo) => {
      const coincideTexto =
        modulo.nombre.toLowerCase().includes(texto) ||
        modulo.nombre_tecnico.toLowerCase().includes(texto);
      const coincideEstado = estado ? moduloEstado(modulo) === estado : true;
      const coincideCategoria = categoria ? modulo.categoria === categoria : true;
      return coincideTexto && coincideEstado && coincideCategoria;
    });
    function moduloEstado(modulo) {
      if (!modulo.instalado) return "no_instalado";
      return modulo.activo ? "activo" : "inactivo";
    }
    this.renderTablaModulos();
  }

  renderTablaModulos() {
    this.tablaModulosBody.innerHTML = "";
    const contador = document.getElementById("contador-modulos");
    if (this.state.modulosFiltrados.length === 0) {
      document.getElementById("modulos-empty").style.display = "block";
      contador.textContent = "0 módulos";
      return;
    }
    document.getElementById("modulos-empty").style.display = "none";
    contador.textContent = `${this.state.modulosFiltrados.length} módulos`;
    this.state.modulosFiltrados.forEach((modulo) => {
      const tr = document.createElement("tr");
      tr.dataset.moduloId = modulo.id;
      tr.innerHTML = `
        <td>
          <div class="d-flex align-items-center gap-2">
            <span class="icono-modulo ${modulo.icono}"></span>
            <div>
              <strong>${modulo.nombre}</strong>
              <div class="text-muted small">${modulo.nombre_tecnico}</div>
            </div>
          </div>
        </td>
        <td><span class="badge bg-light text-dark text-uppercase">${modulo.categoria}</span></td>
        <td>${modulo.version}</td>
        <td>${this.renderEstadoPill(modulo)}</td>
        <td>${modulo.menu_order ?? "-"}</td>
        <td class="text-end">
          <button class="btn btn-sm btn-outline-primary config-btn" data-modulo="${modulo.id}">
            <i class="fas fa-sliders-h"></i>
          </button>
        </td>`;
      tr.addEventListener("click", (e) => {
        if (e.target.closest("button")) return;
        this.seleccionarModulo(modulo.id);
      });
      tr.querySelector("button").addEventListener("click", () => this.seleccionarModulo(modulo.id));
      this.tablaModulosBody.appendChild(tr);
    });
  }

  renderEstadoPill(modulo) {
    let estado = "no_instalado";
    if (modulo.instalado) estado = modulo.activo ? "activo" : "inactivo";
    const iconos = {
      activo: "fas fa-check",
      inactivo: "fas fa-pause",
      no_instalado: "fas fa-times",
    };
    const textos = {
      activo: "Activo",
      inactivo: "Inactivo",
      no_instalado: "No instalado",
    };
    return `<span class="estado-pill estado-${estado}"><i class="${iconos[estado]}"></i>${textos[estado]}</span>`;
  }

  seleccionarModulo(id) {
    const modulo = this.state.modulos.find((m) => Number(m.id) === Number(id));
    if (!modulo) return;
    this.state.moduloActivo = modulo;
    document.querySelectorAll("#tabla-modulos tbody tr").forEach((tr) => {
      tr.classList.toggle("active-row", Number(tr.dataset.moduloId) === Number(id));
    });
    document.getElementById("detalle-nombre").textContent = modulo.nombre;
    document.getElementById("detalle-descripcion").textContent = modulo.descripcion || "Sin descripción";
    document.getElementById("detalle-tecnico").textContent = modulo.nombre_tecnico;
    document.getElementById("detalle-categoria").textContent = modulo.categoria;
    document.getElementById("detalle-version").textContent = modulo.version;
    this.toggleActivo.disabled = false;
    this.toggleInstalado.disabled = false;
    this.menuOrderInput.disabled = false;
    this.toggleActivo.checked = modulo.activo;
    this.toggleInstalado.checked = modulo.instalado;
    this.menuOrderInput.value = modulo.menu_order ?? "";
    this.renderConfiguraciones(modulo);
    this.renderPermisos(modulo);
  }

  renderConfiguraciones(modulo) {
    this.tablaConfigsBody.innerHTML = "";
    const configs = modulo.configuraciones || [];
    document.getElementById("contador-configs").textContent = configs.length;
    const emptyState = document.getElementById("configs-empty");
    if (configs.length === 0) {
      emptyState.style.display = "block";
      return;
    }
    emptyState.style.display = "none";
    configs.forEach((cfg) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${cfg.clave}</td>
        <td>${cfg.valor ?? "-"}</td>
        <td><span class="badge bg-light text-dark">${cfg.tipo}</span></td>
        <td class="text-end">
          <button class="btn btn-sm btn-outline-secondary" ${cfg.editable ? "" : "disabled"} data-config="${cfg.id}">
            <i class="fas fa-pen"></i>
          </button>
        </td>`;
      const btn = tr.querySelector("button");
      if (cfg.editable) btn.addEventListener("click", () => this.abrirModalConfiguracion(cfg));
      this.tablaConfigsBody.appendChild(tr);
    });
  }

  renderPermisos(modulo) {
    this.permisosContainer.innerHTML = "";
    if (!modulo.permisos) {
      this.permisosContainer.innerHTML = "<p class='text-muted'>No hay permisos configurados.</p>";
      return;
    }
    this.state.roles.forEach((rol) => {
      const row = document.createElement("div");
      row.className = "permiso-row";
      row.innerHTML = `<div class="rol-label">${rol}</div>`;
      this.state.permisosDisponibles.forEach((permisoKey) => {
        const toggleId = `permiso-${modulo.id}-${rol}-${permisoKey}`;
        const checked = modulo.permisos[rol]?.[permisoKey] ? "checked" : "";
        row.innerHTML += `
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="${toggleId}" data-rol="${rol}" data-permiso="${permisoKey}" ${checked}>
            <label class="form-check-label permiso-toggle" for="${toggleId}">${permisoKey}</label>
          </div>`;
      });
      this.permisosContainer.appendChild(row);
    });
    this.permisosContainer.querySelectorAll("input[type='checkbox']").forEach((checkbox) => {
      checkbox.addEventListener("change", (e) => this.actualizarPermiso(e));
    });
  }

  abrirModalConfiguracion(cfg) {
    document.getElementById("config-id").value = cfg.id;
    document.getElementById("config-clave").value = cfg.clave;
    document.getElementById("config-descripcion").textContent = cfg.descripcion || "-";
    let inputHtml = "";
    switch (cfg.tipo) {
      case "numero":
        inputHtml = `<input type="number" class="form-control" id="config-valor" value="${cfg.valor ?? ""}">`;
        break;
      case "booleano":
        inputHtml = `
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="config-valor" ${cfg.valor == 1 ? "checked" : ""}>
            <label class="form-check-label">${cfg.valor == 1 ? "Activado" : "Desactivado"}</label>
          </div>`;
        break;
      case "json":
        inputHtml = `<textarea class="form-control" id="config-valor" rows="4">${cfg.valor ?? ""}</textarea>`;
        break;
      default:
        inputHtml = `<input type="text" class="form-control" id="config-valor" value="${cfg.valor ?? ""}">`;
        break;
    }
    this.configInputWrapper.innerHTML = inputHtml;
    this.configInputWrapper.dataset.tipo = cfg.tipo;
    this.modal.show();
  }

  async guardarConfiguracion() {
    const id = document.getElementById("config-id").value;
    const tipo = this.configInputWrapper.dataset.tipo;
    const input = document.getElementById("config-valor");
    let valor;
    if (tipo === "booleano") {
      valor = input.checked ? 1 : 0;
    } else {
      valor = input.value;
      if (tipo === "numero" && valor !== "") {
        valor = Number(valor);
      }
    }
    try {
      this.toggleBotonGuardar(true);
      const response = await fetch("/api/configuracion/actualizar_configuracion.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          accion: "actualizar_configuracion",
          configuracion: { id, valor },
        }),
      });
      const data = await response.json();
      if (!data.success) throw new Error(data.message || "No se pudo guardar");
      this.modal.hide();
      this.mostrarAlerta("Configuración actualizada", "success");
      this.cargarDatos();
    } catch (error) {
      this.mostrarAlerta(error.message, "danger");
    } finally {
      this.toggleBotonGuardar(false);
    }
  }

  toggleBotonGuardar(disabled) {
    const btn = document.getElementById("btn-guardar-configuracion");
    btn.disabled = disabled;
    btn.innerHTML = disabled ? '<span class="spinner-border spinner-border-sm"></span>' : '<i class="fas fa-save"></i> Guardar cambios';
  }

  async onToggleModulo(event, campo) {
    if (!this.state.moduloActivo) return;
    try {
      const response = await fetch("/api/configuracion/actualizar_configuracion.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          accion: "actualizar_estado_modulo",
          modulo_id: this.state.moduloActivo.id,
          campo,
          valor: event.target.checked ? 1 : 0,
        }),
      });
      const data = await response.json();
      if (!data.success) throw new Error(data.message || "No se pudo actualizar el módulo");
      this.mostrarAlerta("Estado del módulo actualizado", "success", true);
      this.cargarDatos();
    } catch (error) {
      this.mostrarAlerta(error.message, "danger");
      event.target.checked = !event.target.checked;
    }
  }

  async actualizarOrdenMenu(valor) {
    if (!this.state.moduloActivo || valor === "") return;
    try {
      const response = await fetch("/api/configuracion/actualizar_configuracion.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          accion: "actualizar_estado_modulo",
          modulo_id: this.state.moduloActivo.id,
          campo: "menu_order",
          valor,
        }),
      });
      const data = await response.json();
      if (!data.success) throw new Error(data.message || "No se pudo actualizar el orden");
      this.mostrarAlerta("Orden actualizado", "success", true);
      this.cargarDatos();
    } catch (error) {
      this.mostrarAlerta(error.message, "danger");
    }
  }

  async actualizarPermiso(event) {
    if (!this.state.moduloActivo) return;
    const checkbox = event.target;
    const rol = checkbox.dataset.rol;
    const key = checkbox.dataset.permiso;
    const valor = checkbox.checked ? 1 : 0;
    try {
      const response = await fetch("/api/configuracion/actualizar_configuracion.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          accion: "actualizar_permiso",
          permiso: {
            modulo_id: this.state.moduloActivo.id,
            rol,
            key,
            valor,
          },
        }),
      });
      const data = await response.json();
      if (!data.success) throw new Error(data.message || "No se pudo actualizar el permiso");
      this.mostrarAlerta("Permiso actualizado", "success", true);
    } catch (error) {
      checkbox.checked = !checkbox.checked;
      this.mostrarAlerta(error.message, "danger");
    }
  }

  mostrarAlerta(mensaje, tipo = "info", autoHide = false) {
    if (!this.alertEl) return;
    this.alertEl.className = `alert alert-${tipo}`;
    this.alertEl.textContent = mensaje;
    this.alertEl.style.display = "block";
    if (autoHide) {
      setTimeout(() => {
        this.alertEl.style.display = "none";
      }, 2500);
    }
  }
}

document.addEventListener("DOMContentLoaded", () => {
  window.configuracionPage = new ConfiguracionPage();
});
