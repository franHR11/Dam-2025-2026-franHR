// Clase principal para la gestión de categorías
class CategoriasPage {
  constructor() {
    this.categorias = [];
    this.paginaActual = 1;
    this.categoriasPorPagina = 10;
    this.totalCategorias = 0;
    this.categoriaActual = null;
    this.filtros = {
      estado: "",
      padre: "",
      busqueda: "",
    };

    // Elementos del DOM
    this.elementos = {
      tbody: null,
      modal: null,
      form: null,
      busqueda: null,
      filtros: {},
    };

    this.init();
  }

  async init() {
    try {
      this.cargarElementosDOM();
      this.configurarEventListeners();
      await this.cargarDatosIniciales();
      this.renderizarTabla();
    } catch (error) {
      console.error("Error al inicializar la página de categorías:", error);
      this.mostrarAlerta("Error al cargar la página", "danger");
    }
  }

  cargarElementosDOM() {
    this.elementos = {
      tbody: document.getElementById("categorias-tbody"),
      modal: document.getElementById("modal-categoria"),
      form: document.getElementById("form-categoria"),
      busqueda: document.getElementById("buscar-categoria"),
      filtroEstado: document.getElementById("filtro-estado"),
      filtroPadre: document.getElementById("filtro-padre"),
      sinCategorias: document.getElementById("sin-categorias"),
      modalConfirmacion: document.getElementById("modal-confirmacion"),
      pagination: document.getElementById("paginacion-categorias"),
      mostrandoCategorias: document.getElementById("mostrando-categorias"),
      totalCategorias: document.getElementById("total-categorias"),
    };
  }

  configurarEventListeners() {
    // Botones principales
    document
      .getElementById("nueva-categoria-btn")
      ?.addEventListener("click", () => this.nuevaCategoria());
    document
      .getElementById("guardar-categoria")
      ?.addEventListener("click", () => this.guardarCategoria());
    document
      .getElementById("eliminar-categoria")
      ?.addEventListener("click", () => this.mostrarConfirmacionEliminacion());
    document
      .getElementById("confirmar-eliminar")
      ?.addEventListener("click", () => this.confirmarEliminar());

    // Búsqueda y filtros
    this.elementos.busqueda?.addEventListener("input", (e) =>
      this.buscarCategoria(e.target.value),
    );
    this.elementos.filtroEstado?.addEventListener("change", (e) =>
      this.aplicarFiltro("estado", e.target.value),
    );
    this.elementos.filtroPadre?.addEventListener("change", (e) =>
      this.aplicarFiltro("padre", e.target.value),
    );

    // Vista previa de imagen
    document
      .getElementById("categoria-imagen")
      ?.addEventListener("change", (e) => this.vistaPreviaImagen(e));

    // Resetear formulario al cerrar modal
    this.elementos.modal?.addEventListener("hidden.bs.modal", () => {
      this.limpiarFormulario();
    });
  }

  async apiCall(url, options = {}) {
    try {
      const response = await fetch(url, {
        headers: {
          "Content-Type": "application/json",
          ...options.headers,
        },
        ...options,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      return await response.json();
    } catch (error) {
      console.error("Error en llamada API:", error);
      throw error;
    }
  }

  async cargarDatosIniciales() {
    try {
      const categoriasResp = await this.apiCall(
        "../../api/productos/categorias.php",
      );
      this.categorias = categoriasResp.categorias || [];
      this.totalCategorias = this.categorias.length;

      // Cargar selects de categorías
      this.cargarSelectCategorias();
    } catch (error) {
      console.error("Error al cargar datos iniciales:", error);
      throw error;
    }
  }

  cargarSelectCategorias() {
    // Cargar filtro de categorías padre
    const filtroPadre = this.elementos.filtroPadre;
    if (filtroPadre) {
      filtroPadre.innerHTML =
        '<option value="">Todas las categorías</option>' +
        this.categorias
          .map(
            (cat) =>
              `<option value="${cat.id}">${this.escapeHtml(cat.nombre)}</option>`,
          )
          .join("");
    }

    // Cargar select de categoría padre en el modal
    const categoriaPadreSelect = document.getElementById("categoria-padre");
    if (categoriaPadreSelect) {
      categoriaPadreSelect.innerHTML =
        '<option value="">Sin categoría padre (raíz)</option>' +
        this.categorias
          .filter(
            (cat) =>
              !this.categoriaActual || cat.id !== this.categoriaActual.id,
          )
          .map(
            (cat) =>
              `<option value="${cat.id}">${this.escapeHtml(cat.nombre)}</option>`,
          )
          .join("");
    }
  }

  renderizarTabla() {
    if (!this.elementos.tbody) return;

    const categoriasFiltradas = this.obtenerCategoriasFiltradas();
    const inicio = (this.paginaActual - 1) * this.categoriasPorPagina;
    const fin = inicio + this.categoriasPorPagina;
    const categoriasPagina = categoriasFiltradas.slice(inicio, fin);

    if (categoriasPagina.length === 0 && categoriasFiltradas.length === 0) {
      this.elementos.tbody.innerHTML = "";
      this.elementos.sinCategorias.style.display = "block";
      return;
    }

    this.elementos.sinCategorias.style.display = "none";
    this.elementos.tbody.innerHTML = categoriasPagina
      .map((categoria) => this.generarFilaCategoria(categoria))
      .join("");

    // Actualizar contador
    this.actualizarContador(categoriasFiltradas.length);

    // Actualizar paginación
    this.actualizarPaginacion(categoriasFiltradas.length);
  }

  generarFilaCategoria(categoria) {
    const categoriaPadre = this.categorias.find(
      (c) => c.id === categoria.categoria_padre_id,
    );
    const productosCount = this.contarProductosPorCategoria(categoria.id);

    return `
      <tr>
        <td>
          <div class="categoria-icono ${categoria.imagen ? "con-imagen" : ""}" title="${categoria.nombre || "Sin nombre"}">
            ${
              categoria.imagen
                ? `<img src="${categoria.imagen.startsWith("/") ? categoria.imagen : "/" + categoria.imagen}" alt="${this.escapeHtml(categoria.nombre)}" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\\'fas fa-folder\\'></i>';">`
                : `<i class="fas fa-folder"></i>`
            }
          </div>
        </td>
        <td>
          <strong>${this.escapeHtml(categoria.nombre)}</strong>
        </td>
        <td>
          <span class="text-muted">${categoria.descripcion ? this.escapeHtml(categoria.descripcion.substring(0, 50)) + (categoria.descripcion.length > 50 ? "..." : "") : "Sin descripción"}</span>
        </td>
        <td>
          ${
            categoriaPadre
              ? `<span class="categoria-padre-indicador">${this.escapeHtml(categoriaPadre.nombre)}</span>`
              : '<span class="text-muted">Raíz</span>'
          }
        </td>
        <td>
          <span class="productos-count">${productosCount} productos</span>
        </td>
        <td>
          <span class="estado-badge ${categoria.activo ? "activo" : "inactivo"}">
            ${categoria.activo ? "Activa" : "Inactiva"}
          </span>
        </td>
        <td>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="categoriasPage.editarCategoria(${categoria.id})" title="Editar">
              <i class="fas fa-edit"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="categoriasPage.eliminarCategoria(${categoria.id})" title="Eliminar">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </td>
      </tr>
    `;
  }

  contarProductosPorCategoria(categoriaId) {
    // Esta función debería contar productos de la base de datos
    // Por ahora, retornamos un valor simulado
    return Math.floor(Math.random() * 20);
  }

  obtenerCategoriasFiltradas() {
    let filtradas = [...this.categorias];

    // Filtro de búsqueda
    if (this.filtros.busqueda) {
      const busquedaLower = this.filtros.busqueda.toLowerCase();
      filtradas = filtradas.filter(
        (cat) =>
          cat.nombre.toLowerCase().includes(busquedaLower) ||
          (cat.descripcion &&
            cat.descripcion.toLowerCase().includes(busquedaLower)),
      );
    }

    // Filtro de estado
    if (this.filtros.estado !== "") {
      filtradas = filtradas.filter((cat) => cat.activo == this.filtros.estado);
    }

    // Filtro de categoría padre
    if (this.filtros.padre !== "") {
      filtradas = filtradas.filter(
        (cat) => cat.categoria_padre_id == this.filtros.padre,
      );
    }

    return filtradas;
  }

  actualizarContador(totalFiltradas) {
    if (this.elementos.mostrandoCategorias && this.elementos.totalCategorias) {
      this.elementos.mostrandoCategorias.textContent = Math.min(
        totalFiltradas,
        this.categoriasPorPagina,
      );
      this.elementos.totalCategorias.textContent = totalFiltradas;
    }
  }

  actualizarPaginacion(totalItems) {
    if (!this.elementos.pagination) return;

    const totalPages = Math.ceil(totalItems / this.categoriasPorPagina);
    if (totalPages <= 1) {
      this.elementos.pagination.innerHTML = "";
      return;
    }

    let paginationHTML = "";

    // Botón anterior
    paginationHTML += `
      <li class="page-item ${this.paginaActual === 1 ? "disabled" : ""}">
        <a class="page-link" href="#" onclick="categoriasPage.cambiarPagina(${this.paginaActual - 1}); return false;">
          <i class="fas fa-chevron-left"></i>
        </a>
      </li>
    `;

    // Páginas
    const maxVisiblePages = 5;
    let startPage = Math.max(
      1,
      this.paginaActual - Math.floor(maxVisiblePages / 2),
    );
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

    if (endPage - startPage < maxVisiblePages - 1) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    if (startPage > 1) {
      paginationHTML += `
        <li class="page-item">
          <a class="page-link" href="#" onclick="categoriasPage.cambiarPagina(1); return false;">1</a>
        </li>
      `;
      if (startPage > 2) {
        paginationHTML += `
          <li class="page-item disabled">
            <span class="page-link">...</span>
          </li>
        `;
      }
    }

    for (let i = startPage; i <= endPage; i++) {
      paginationHTML += `
        <li class="page-item ${i === this.paginaActual ? "active" : ""}">
          <a class="page-link" href="#" onclick="categoriasPage.cambiarPagina(${i}); return false;">${i}</a>
        </li>
      `;
    }

    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        paginationHTML += `
          <li class="page-item disabled">
            <span class="page-link">...</span>
          </li>
        `;
      }
      paginationHTML += `
        <li class="page-item">
          <a class="page-link" href="#" onclick="categoriasPage.cambiarPagina(${totalPages}); return false;">${totalPages}</a>
        </li>
      `;
    }

    // Botón siguiente
    paginationHTML += `
      <li class="page-item ${this.paginaActual === totalPages ? "disabled" : ""}">
        <a class="page-link" href="#" onclick="categoriasPage.cambiarPagina(${this.paginaActual + 1}); return false;">
          <i class="fas fa-chevron-right"></i>
        </a>
      </li>
    `;

    this.elementos.pagination.innerHTML = paginationHTML;
  }

  cambiarPagina(pagina) {
    const totalFiltradas = this.obtenerCategoriasFiltradas().length;
    const totalPages = Math.ceil(totalFiltradas / this.categoriasPorPagina);

    if (pagina >= 1 && pagina <= totalPages) {
      this.paginaActual = pagina;
      this.renderizarTabla();
    }
  }

  nuevaCategoria() {
    this.categoriaActual = null;
    this.limpiarFormulario();
    document.getElementById("modal-categoria-titulo").textContent =
      "Nueva Categoría";
    document.getElementById("eliminar-categoria").style.display = "none";
    this.cargarSelectCategorias();

    const modal = new bootstrap.Modal(this.elementos.modal);
    modal.show();
  }

  editarCategoria(id) {
    const categoria = this.categorias.find((cat) => cat.id === id);
    if (!categoria) return;

    this.categoriaActual = categoria;
    this.cargarFormulario(categoria);
    document.getElementById("modal-categoria-titulo").textContent =
      "Editar Categoría";
    document.getElementById("eliminar-categoria").style.display = "block";
    this.cargarSelectCategorias();

    const modal = new bootstrap.Modal(this.elementos.modal);
    modal.show();
  }

  eliminarCategoria(id) {
    const categoria = this.categorias.find((cat) => cat.id === id);
    if (!categoria) return;

    this.categoriaActual = categoria;
    this.mostrarConfirmacionEliminacion();
  }

  cargarFormulario(categoria) {
    document.getElementById("categoria-nombre").value = categoria.nombre || "";
    document.getElementById("categoria-descripcion").value =
      categoria.descripcion || "";
    document.getElementById("categoria-padre").value =
      categoria.categoria_padre_id || "";
    document.getElementById("categoria-activo").checked = categoria.activo == 1;

    if (categoria.imagen) {
      this.mostrarVistaPreviaImagen(
        categoria.imagen.startsWith("/")
          ? categoria.imagen
          : "/" + categoria.imagen,
      );
    }
  }

  limpiarFormulario() {
    if (this.elementos.form) {
      this.elementos.form.reset();
      document.getElementById("categoria-activo").checked = true;
      document.getElementById("vista-previa-imagen").innerHTML = "";

      // Eliminar campo oculto de ruta de imagen si existe
      const rutaImagenInput = document.getElementById("categoria-imagen-ruta");
      if (rutaImagenInput) {
        rutaImagenInput.remove();
      }
    }
  }

  async guardarCategoria() {
    if (!this.validarFormulario()) return;

    try {
      // Obtener ruta de la imagen del campo oculto o de la categoría actual
      const rutaImagenInput = document.getElementById("categoria-imagen-ruta");
      let imagenPath = this.categoriaActual?.imagen || null;
      if (rutaImagenInput && rutaImagenInput.value) {
        imagenPath = rutaImagenInput.value;
      }

      const categoriaData = {
        nombre: document.getElementById("categoria-nombre").value.trim(),
        descripcion:
          document.getElementById("categoria-descripcion").value.trim() || null,
        categoria_padre_id:
          document.getElementById("categoria-padre").value || null,
        activo: document.getElementById("categoria-activo").checked ? 1 : 0,
        imagen: imagenPath,
      };

      if (this.categoriaActual) {
        categoriaData.id = this.categoriaActual.id;
      }

      const url = "../../api/productos/categorias.php";
      const method = this.categoriaActual ? "PUT" : "POST";

      const response = await fetch(url, {
        method: method,
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(categoriaData),
      });

      const result = await response.json();

      if (result.ok) {
        this.mostrarAlerta(
          this.categoriaActual
            ? "Categoría actualizada correctamente"
            : "Categoría creada correctamente",
          "success",
        );
        bootstrap.Modal.getInstance(this.elementos.modal).hide();
        await this.cargarDatosIniciales();
        this.renderizarTabla();
      } else {
        throw new Error(result.error || "Error al guardar la categoría");
      }
    } catch (error) {
      console.error("Error guardando categoría:", error);
      this.mostrarAlerta(error.message, "danger");
    }
  }

  mostrarConfirmacionEliminacion() {
    const modal = new bootstrap.Modal(this.elementos.modalConfirmacion);
    modal.show();
  }

  async confirmarEliminar() {
    if (!this.categoriaActual) return;

    try {
      const response = await this.apiCall(
        `../../api/productos/categorias.php?id=${this.categoriaActual.id}`,
        {
          method: "DELETE",
        },
      );

      if (response.ok) {
        this.mostrarAlerta("Categoría eliminada correctamente", "success");
        await this.cargarDatosIniciales();
        this.renderizarTabla();

        const modal = bootstrap.Modal.getInstance(
          this.elementos.modalConfirmacion,
        );
        modal.hide();

        const modalCategoria = bootstrap.Modal.getInstance(
          this.elementos.modal,
        );
        if (modalCategoria) modalCategoria.hide();
      } else {
        throw new Error(response.message || "Error al eliminar categoría");
      }
    } catch (error) {
      console.error("Error al eliminar categoría:", error);
      this.mostrarAlerta("Error al eliminar la categoría", "danger");
    }
  }

  validarFormulario() {
    const nombre = document.getElementById("categoria-nombre").value.trim();

    if (!nombre) {
      this.mostrarAlerta("El nombre de la categoría es obligatorio", "warning");
      document.getElementById("categoria-nombre").focus();
      return false;
    }

    // Verificar nombre duplicado
    const duplicado = this.categorias.find(
      (cat) =>
        cat.nombre.toLowerCase() === nombre.toLowerCase() &&
        (!this.categoriaActual || cat.id !== this.categoriaActual.id),
    );

    if (duplicado) {
      this.mostrarAlerta("Ya existe una categoría con ese nombre", "warning");
      document.getElementById("categoria-nombre").focus();
      return false;
    }

    return true;
  }

  buscarCategoria(termino) {
    this.filtros.busqueda = termino.trim().toLowerCase();
    this.paginaActual = 1;
    this.renderizarTabla();
  }

  aplicarFiltro(tipo, valor) {
    this.filtros[tipo] = valor;
    this.paginaActual = 1;
    this.renderizarTabla();
  }

  async vistaPreviaImagen(event) {
    const archivo = event.target.files[0];
    if (!archivo) return;

    // Validar tipo de archivo
    if (!archivo.type.startsWith("image/")) {
      this.mostrarAlerta("El archivo debe ser una imagen", "warning");
      event.target.value = "";
      return;
    }

    // Validar tamaño (máximo 2MB)
    if (archivo.size > 2 * 1024 * 1024) {
      this.mostrarAlerta("La imagen no debe superar los 2MB", "warning");
      event.target.value = "";
      return;
    }

    // Mostrar vista previa local
    const reader = new FileReader();
    reader.onload = (e) => {
      this.mostrarVistaPreviaImagen(e.target.result);

      // Subir imagen al servidor
      this.subirImagen(archivo);
    };
    reader.readAsDataURL(archivo);
  }

  async subirImagen(archivo) {
    try {
      const formData = new FormData();
      formData.append("imagen", archivo);

      const response = await fetch(
        "../../api/productos/upload/categoria_imagen.php",
        {
          method: "POST",
          body: formData,
        },
      );

      const result = await response.json();

      if (result.ok) {
        // Guardar la ruta de la imagen en un campo oculto para usarla al guardar
        let rutaImagenInput = document.getElementById("categoria-imagen-ruta");
        if (!rutaImagenInput) {
          rutaImagenInput = document.createElement("input");
          rutaImagenInput.type = "hidden";
          rutaImagenInput.id = "categoria-imagen-ruta";
          rutaImagenInput.name = "imagen_ruta";
          document
            .getElementById("form-categoria")
            .appendChild(rutaImagenInput);
        }
        rutaImagenInput.value = result.ruta;

        this.mostrarAlerta("Imagen subida correctamente", "success");
      } else {
        throw new Error(result.error || "Error al subir la imagen");
      }
    } catch (error) {
      console.error("Error subiendo imagen:", error);
      this.mostrarAlerta(
        "Error al subir la imagen: " + error.message,
        "danger",
      );
    }
  }

  mostrarVistaPreviaImagen(src) {
    const vistaPrevia = document.getElementById("vista-previa-imagen");
    if (vistaPrevia) {
      vistaPrevia.innerHTML = `
        <img src="${src}" alt="Vista previa" class="img-thumbnail" style="max-height: 60px;">
      `;
    }
  }

  mostrarAlerta(mensaje, tipo = "info") {
    // Crear alerta si no existe un contenedor
    let alertContainer = document.getElementById("alert-container");
    if (!alertContainer) {
      alertContainer = document.createElement("div");
      alertContainer.id = "alert-container";
      alertContainer.style.position = "fixed";
      alertContainer.style.top = "20px";
      alertContainer.style.right = "20px";
      alertContainer.style.zIndex = "9999";
      alertContainer.style.width = "300px";
      document.body.appendChild(alertContainer);
    }

    const alertId = "alert-" + Date.now();
    const alertHTML = `
      <div id="${alertId}" class="alert alert-${tipo} alert-dismissible fade show" role="alert">
        ${this.escapeHtml(mensaje)}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    `;

    alertContainer.insertAdjacentHTML("beforeend", alertHTML);

    // Auto-eliminar después de 5 segundos
    setTimeout(() => {
      const alert = document.getElementById(alertId);
      if (alert) {
        const bsAlert = bootstrap.Alert.getInstance(alert);
        if (bsAlert) {
          bsAlert.close();
        } else {
          alert.remove();
        }
      }
    }, 5000);
  }

  escapeHtml(text) {
    const map = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
    };
    return text.replace(/[&<>"']/g, (m) => map[m]);
  }
}

// Inicializar la página cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
  window.categoriasPage = new CategoriasPage();
});
