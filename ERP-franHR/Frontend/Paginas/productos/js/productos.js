// Clase principal para la gestión de productos
class ProductosPage {
  constructor() {
    this.productos = [];
    this.categorias = [];
    this.proveedores = [];
    this.paginaActual = 1;
    this.productosPorPagina = 10;
    this.totalProductos = 0;
    this.productoActual = null;
    this.filtros = {
      categoria: "",
      stock: "",
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
      console.error("Error al inicializar la página de productos:", error);
      this.mostrarAlerta("Error al cargar la página", "danger");
    }
  }

  cargarElementosDOM() {
    this.elementos = {
      tbody: document.getElementById("productos-tbody"),
      modal: document.getElementById("modal-producto"),
      form: document.getElementById("form-producto"),
      busqueda: document.getElementById("buscar-producto"),
      filtroCategoria: document.getElementById("filtro-categoria"),
      filtroStock: document.getElementById("filtro-stock"),
      seleccionarTodos: document.getElementById("seleccionar-todos"),
      noProductos: document.getElementById("no-productos"),
      modalEliminar: document.getElementById("modal-eliminar"),
      modalDetalles: document.getElementById("modal-detalles"),
      pagination: document.getElementById("pagination"),
      productosDesde: document.getElementById("productos-desde"),
      productosHasta: document.getElementById("productos-hasta"),
      productosTotal: document.getElementById("productos-total"),
    };
  }

  configurarEventListeners() {
    // Botones principales
    document
      .getElementById("nuevo-producto-btn")
      ?.addEventListener("click", () => this.nuevoProducto());
    document
      .getElementById("guardar-producto")
      ?.addEventListener("click", () => this.guardarProducto());
    document
      .getElementById("confirmar-eliminar")
      ?.addEventListener("click", () => this.confirmarEliminar());
    document
      .getElementById("editar-desde-detalles")
      ?.addEventListener("click", () => this.editarDesdeDetalles());

    // Búsqueda y filtros
    this.elementos.busqueda?.addEventListener("input", (e) =>
      this.buscarProducto(e.target.value),
    );
    this.elementos.filtroCategoria?.addEventListener("change", (e) =>
      this.aplicarFiltro("categoria", e.target.value),
    );
    this.elementos.filtroStock?.addEventListener("change", (e) =>
      this.aplicarFiltro("stock", e.target.value),
    );

    // Selección múltiple
    this.elementos.seleccionarTodos?.addEventListener("change", (e) =>
      this.seleccionarTodos(e.target.checked),
    );

    // Vista previa de imagen
    document
      .getElementById("imagen")
      ?.addEventListener("change", (e) => this.vistaPreviaImagen(e));

    // Cálculo automático de precios
    document
      .getElementById("precio-coste")
      ?.addEventListener("input", () => this.calcularMargen());
    document
      .getElementById("precio-venta")
      ?.addEventListener("input", () => this.calcularMargen());
    document
      .getElementById("margen")
      ?.addEventListener("input", () => this.calcularPrecioVenta());

    // Alertas de stock
    document
      .getElementById("stock-actual")
      ?.addEventListener("input", () => this.validarStock());
    document
      .getElementById("stock-minimo")
      ?.addEventListener("input", () => this.validarStock());

    // Autoguardado
    let timeoutAutoguardar;
    this.elementos.form?.addEventListener("input", () => {
      clearTimeout(timeoutAutoguardar);
      timeoutAutoguardar = setTimeout(() => this.autoguardar(), 3000);
    });
  }

  async cargarDatosIniciales() {
    try {
      const [productosResp, categoriasResp, proveedoresResp] =
        await Promise.all([
          this.apiCall("/api/productos/obtener_productos.php"),
          this.apiCall("/api/productos/categorias.php"),
          this.apiCall("/api/productos/proveedores.php"),
        ]);

      this.productos = productosResp.productos || [];
      this.categorias = categoriasResp.categorias || [];
      this.proveedores = proveedoresResp.proveedores || [];

      this.totalProductos = this.productos.length;

      // Cargar selects
      this.cargarSelectCategorias();
      this.cargarSelectProveedores();
    } catch (error) {
      console.error("Error al cargar datos iniciales:", error);
      throw error;
    }
  }

  async apiCall(url, options = {}) {
    try {
      // Configurar headers por defecto solo si no hay FormData
      const defaultHeaders = {};
      if (!(options.body instanceof FormData)) {
        defaultHeaders["Content-Type"] = "application/json";
        defaultHeaders["X-Requested-With"] = "XMLHttpRequest";
      }

      const response = await fetch(url, {
        headers: {
          ...defaultHeaders,
          ...options.headers,
        },
        ...options,
      });

      const contentType = response.headers.get("Content-Type") || "";
      if (!response.ok) {
        // Intentar leer el cuerpo para obtener un mensaje más útil
        let errorMessage = `HTTP error! status: ${response.status}`;
        try {
          if (contentType.includes("application/json")) {
            const data = await response.json();
            errorMessage = data?.error || data?.message || errorMessage;
          } else {
            const text = await response.text();
            if (text) errorMessage = text;
          }
        } catch (_) {
          // Ignorar errores de parseo
        }
        throw new Error(errorMessage);
      }

      if (contentType.includes("application/json")) {
        return await response.json();
      }
      // Fallback: si no es JSON, devolver texto
      return await response.text();
    } catch (error) {
      console.error("Error en llamada API:", error);
      throw error;
    }
  }

  renderizarTabla() {
    if (!this.elementos.tbody) return;

    // Filtrar productos
    let productosFiltrados = this.filtrarProductos();

    // Paginación
    const inicio = (this.paginaActual - 1) * this.productosPorPagina;
    const fin = inicio + this.productosPorPagina;
    const productosPagina = productosFiltrados.slice(inicio, fin);

    // Actualizar información de paginación
    this.actualizarInfoPaginacion(inicio, fin, productosFiltrados.length);

    // Mostrar/ocultar mensaje de no hay productos
    if (productosFiltrados.length === 0) {
      this.elementos.tbody.innerHTML = "";
      this.elementos.noProductos.style.display = "block";
      return;
    } else {
      this.elementos.noProductos.style.display = "none";
    }

    // Renderizar filas
    this.elementos.tbody.innerHTML = productosPagina
      .map(
        (producto) => `
            <tr data-id="${producto.id}">
                <td>
                    <input type="checkbox" class="seleccionar-producto" value="${producto.id}">
                </td>
                <td>
                    <strong>${this.escapeHtml(producto.codigo)}</strong>
                    ${producto.codigo_barras ? `<br><small class="text-muted">${producto.codigo_barras}</small>` : ""}
                </td>
                <td>
                    ${(() => {
                      const imgSrc =
                        producto.imagen_url ||
                        (producto.imagen &&
                        producto.imagen.startsWith("/uploads/")
                          ? producto.imagen
                          : producto.imagen
                            ? `/uploads/productos/${producto.imagen}`
                            : null);
                      return imgSrc
                        ? `<img src="${imgSrc}" class="producto-imagen" alt="${this.escapeHtml(producto.nombre)}" onclick="window.productosPage.verImagenAmpliada('${imgSrc}', '${this.escapeHtml(producto.nombre)}')" style="cursor: pointer;">`
                        : '<div class="producto-imagen-placeholder"><i class="fas fa-image"></i></div>';
                    })()}
                </td>
                <td>
                    <div class="fw-bold">${this.escapeHtml(producto.nombre)}</div>
                    ${producto.descripcion ? `<div class="text-muted small text-truncate-2">${this.escapeHtml(producto.descripcion)}</div>` : ""}
                </td>
                <td>
                    ${this.getCategoriaNombre(producto.categoria_id)}
                    <br><small class="text-muted">${producto.tipo_producto}</small>
                </td>
                <td>
                    <strong>${this.formatearMoneda(producto.precio_venta)}</strong>
                    <br><small class="text-muted">IVA ${producto.iva_tipo}%</small>
                </td>
                <td>
                    <span class="stock-badge ${this.getStockClase(producto)}">
                        ${producto.stock_actual} ${producto.unidad_medida}
                    </span>
                    ${
                      producto.stock_minimo > 0 &&
                      producto.stock_actual <= producto.stock_minimo
                        ? '<br><small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Mínimo</small>'
                        : ""
                    }
                </td>
                <td>
                    <span class="estado-badge ${producto.activo ? "estado-activo" : "estado-inactivo"}">
                        ${producto.activo ? "Activo" : "Inactivo"}
                    </span>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-accion btn-ver" onclick="window.productosPage.verProducto(${producto.id})" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-accion btn-editar" onclick="window.productosPage.editarProducto(${producto.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-accion btn-eliminar" onclick="window.productosPage.eliminarProducto(${producto.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `,
      )
      .join("");

    // Renderizar paginación
    this.renderizarPaginacion(productosFiltrados.length);

    // Animar filas
    this.animarFilas();
  }

  filtrarProductos() {
    return this.productos.filter((producto) => {
      // Filtro de categoría
      if (
        this.filtros.categoria &&
        producto.categoria_id != this.filtros.categoria
      ) {
        return false;
      }

      // Filtro de stock
      if (this.filtros.stock) {
        switch (this.filtros.stock) {
          case "bajo":
            if (
              producto.stock_minimo > 0 &&
              producto.stock_actual > producto.stock_minimo
            ) {
              return false;
            }
            break;
          case "agotado":
            if (producto.stock_actual > 0) {
              return false;
            }
            break;
          case "disponible":
            if (producto.stock_actual <= 0) {
              return false;
            }
            break;
        }
      }

      // Filtro de búsqueda
      if (this.filtros.busqueda) {
        const busqueda = this.filtros.busqueda.toLowerCase();
        return [
          producto.codigo,
          producto.codigo_barras,
          producto.nombre,
          producto.descripcion,
        ].some((campo) => campo && campo.toLowerCase().includes(busqueda));
      }

      return true;
    });
  }

  renderizarPaginacion(totalItems) {
    if (!this.elementos.pagination) return;

    const totalPaginas = Math.ceil(totalItems / this.productosPorPagina);

    if (totalPaginas <= 1) {
      this.elementos.pagination.innerHTML = "";
      return;
    }

    let html = "";

    // Botón anterior
    html += `
            <li class="page-item ${this.paginaActual === 1 ? "disabled" : ""}">
                <a class="page-link" href="#" onclick="window.productosPage.cambiarPagina(${this.paginaActual - 1}); return false;">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;

    // Números de página
    for (let i = 1; i <= totalPaginas; i++) {
      if (
        i === 1 ||
        i === totalPaginas ||
        (i >= this.paginaActual - 2 && i <= this.paginaActual + 2)
      ) {
        html += `
                    <li class="page-item ${i === this.paginaActual ? "active" : ""}">
                        <a class="page-link" href="#" onclick="window.productosPage.cambiarPagina(${i}); return false;">${i}</a>
                    </li>
                `;
      } else if (i === this.paginaActual - 3 || i === this.paginaActual + 3) {
        html +=
          '<li class="page-item disabled"><a class="page-link">...</a></li>';
      }
    }

    // Botón siguiente
    html += `
            <li class="page-item ${this.paginaActual === totalPaginas ? "disabled" : ""}">
                <a class="page-link" href="#" onclick="window.productosPage.cambiarPagina(${this.paginaActual + 1}); return false;">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;

    this.elementos.pagination.innerHTML = html;
  }

  actualizarInfoPaginacion(inicio, fin, total) {
    if (
      !this.elementos.productosDesde ||
      !this.elementos.productosHasta ||
      !this.elementos.productosTotal
    ) {
      return;
    }

    this.elementos.productosDesde.textContent = total > 0 ? inicio + 1 : 0;
    this.elementos.productosHasta.textContent = Math.min(fin, total);
    this.elementos.productosTotal.textContent = total;
  }

  cambiarPagina(pagina) {
    const totalItems = this.filtrarProductos().length;
    const totalPaginas = Math.ceil(totalItems / this.productosPorPagina);

    if (pagina < 1 || pagina > totalPaginas) {
      return;
    }

    this.paginaActual = pagina;
    this.renderizarTabla();
  }

  async nuevoProducto() {
    this.productoActual = null;
    this.limpiarFormulario();
    document.getElementById("modal-producto-titulo").textContent =
      "Nuevo Producto";
    const modal = new bootstrap.Modal(this.elementos.modal);
    modal.show();
  }

  async editarProducto(id) {
    try {
      const producto = this.productos.find((p) => p.id == id);
      if (!producto) {
        throw new Error("Producto no encontrado");
      }

      this.productoActual = producto;
      this.cargarFormulario(producto);
      document.getElementById("modal-producto-titulo").textContent =
        "Editar Producto";
      const modal = new bootstrap.Modal(this.elementos.modal);
      modal.show();
    } catch (error) {
      console.error("Error al editar producto:", error);
      this.mostrarAlerta("Error al cargar el producto", "danger");
    }
  }

  async verProducto(id) {
    try {
      const producto = this.productos.find((p) => p.id == id);
      if (!producto) {
        throw new Error("Producto no encontrado");
      }

      this.productoActual = producto;
      this.mostrarDetallesProducto(producto);

      const modal = new bootstrap.Modal(this.elementos.modalDetalles);
      modal.show();
    } catch (error) {
      console.error("Error al ver producto:", error);
      this.mostrarAlerta("Error al cargar los detalles del producto", "danger");
    }
  }

  mostrarDetallesProducto(producto) {
    if (!this.elementos.modalDetalles) return;

    const detallesContenido = document.getElementById("detalles-contenido");
    if (!detallesContenido) return;

    detallesContenido.innerHTML = `
            <div class="row">
                <div class="col-md-4 text-center">
                    ${(() => {
                      const imgSrc =
                        producto.imagen_url ||
                        (producto.imagen &&
                        producto.imagen.startsWith("/uploads/")
                          ? producto.imagen
                          : producto.imagen
                            ? `/uploads/productos/${producto.imagen}`
                            : null);
                      return imgSrc
                        ? `<img src="${imgSrc}" class="img-fluid rounded mb-3" alt="${this.escapeHtml(producto.nombre)}">`
                        : '<div class="producto-imagen-placeholder mx-auto mb-3" style="width: 200px; height: 200px;"><i class="fas fa-image fa-3x"></i></div>';
                    })()}
                    <h4>${this.escapeHtml(producto.nombre)}</h4>
                    <p class="text-muted">${this.escapeHtml(producto.codigo)}</p>
                </div>
                <div class="col-md-8">
                    <h5>Información General</h5>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Código:</strong></td>
                            <td>${this.escapeHtml(producto.codigo)}</td>
                        </tr>
                        <tr>
                            <td><strong>Código de Barras:</strong></td>
                            <td>${producto.codigo_barras || "N/A"}</td>
                        </tr>
                        <tr>
                            <td><strong>Categoría:</strong></td>
                            <td>${this.getCategoriaNombre(producto.categoria_id)}</td>
                        </tr>
                        <tr>
                            <td><strong>Tipo:</strong></td>
                            <td>${producto.tipo_producto}</td>
                        </tr>
                        <tr>
                            <td><strong>Unidad:</strong></td>
                            <td>${producto.unidad_medida}</td>
                        </tr>
                        ${
                          producto.descripcion
                            ? `
                        <tr>
                            <td><strong>Descripción:</strong></td>
                            <td>${this.escapeHtml(producto.descripcion)}</td>
                        </tr>
                        `
                            : ""
                        }
                    </table>

                    <h5>Información de Precios</h5>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Precio Coste:</strong></td>
                            <td>${this.formatearMoneda(producto.precio_coste)}</td>
                        </tr>
                        <tr>
                            <td><strong>Precio Venta:</strong></td>
                            <td>${this.formatearMoneda(producto.precio_venta)}</td>
                        </tr>
                        <tr>
                            <td><strong>Margen:</strong></td>
                            <td>${producto.margen}%</td>
                        </tr>
                        <tr>
                            <td><strong>IVA:</strong></td>
                            <td>${producto.iva_tipo}%</td>
                        </tr>
                    </table>

                    <h5>Información de Stock</h5>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Stock Actual:</strong></td>
                            <td><span class="stock-badge ${this.getStockClase(producto)}">${producto.stock_actual} ${producto.unidad_medida}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Stock Mínimo:</strong></td>
                            <td>${producto.stock_minimo} ${producto.unidad_medida}</td>
                        </tr>
                        <tr>
                            <td><strong>Stock Máximo:</strong></td>
                            <td>${producto.stock_maximo} ${producto.unidad_medida}</td>
                        </tr>
                        <tr>
                            <td><strong>Control Stock:</strong></td>
                            <td>${producto.control_stock ? "Sí" : "No"}</td>
                        </tr>
                    </table>
                </div>
            </div>
        `;
  }

  async eliminarProducto(id) {
    try {
      const producto = this.productos.find((p) => p.id == id);
      if (!producto) {
        throw new Error("Producto no encontrado");
      }

      this.productoActual = producto;
      document.getElementById("nombre-producto-eliminar").textContent =
        producto.nombre;

      const modal = new bootstrap.Modal(this.elementos.modalEliminar);
      modal.show();
    } catch (error) {
      console.error("Error al preparar eliminación:", error);
      this.mostrarAlerta(
        "Error al preparar eliminación del producto",
        "danger",
      );
    }
  }

  async confirmarEliminar() {
    if (!this.productoActual) return;

    try {
      const response = await this.apiCall(
        "/api/productos/eliminar_producto.php",
        {
          method: "POST",
          body: JSON.stringify({ id: this.productoActual.id }),
        },
      );

      if (response.success) {
        // Eliminar del array local
        this.productos = this.productos.filter(
          (p) => p.id != this.productoActual.id,
        );

        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(this.elementos.modalEliminar);
        modal.hide();

        // Actualizar tabla
        this.renderizarTabla();

        // Mostrar éxito
        this.mostrarAlerta("Producto eliminado correctamente", "success");
      } else {
        throw new Error(response.message || "Error al eliminar el producto");
      }
    } catch (error) {
      console.error("Error al eliminar producto:", error);
      this.mostrarAlerta(
        error.message || "Error al eliminar el producto",
        "danger",
      );
    }
  }

  async guardarProducto() {
    if (!this.elementos.form) return;

    if (!this.validarFormulario()) {
      return;
    }

    try {
      const formData = new FormData(this.elementos.form);
      const imagenFile = formData.get("imagen");
      const tieneImagen = imagenFile && imagenFile.size > 0;

      // Obtener todos los campos excepto imagen
      const datos = {};
      for (let [key, value] of formData.entries()) {
        if (key !== "imagen") {
          datos[key] = value;
        }
      }

      // Forzar valores de checkboxes basados en el estado real del formulario
      datos.activo = document.getElementById("activo").checked ? 1 : 0;
      datos.es_venta_online = document.getElementById("venta-online").checked
        ? 1
        : 0;
      datos.control_stock = document.getElementById("control-stock").checked
        ? 1
        : 0;
      datos.requiere_receta = document.getElementById("requiere-receta").checked
        ? 1
        : 0;
      datos.fecha_caducidad_control = document.getElementById("fecha-caducidad")
        .checked
        ? 1
        : 0;

      // Añadir ID si es edición
      if (this.productoActual) {
        datos.id = this.productoActual.id;
      }

      // Preparar envío según si hay archivo o no
      const requestOptions = {
        method: "POST",
      };

      if (tieneImagen) {
        // Si hay imagen, enviar como FormData
        const formDataToSend = new FormData();

        // Añadir todos los datos
        Object.keys(datos).forEach((key) => {
          formDataToSend.append(key, datos[key]);
        });

        // Añadir el archivo de imagen
        formDataToSend.append("imagen", imagenFile);

        requestOptions.body = formDataToSend;

        // NO establecer Content-Type, el navegador lo hace automáticamente con boundary
      } else {
        // Si no hay imagen, enviar como JSON
        requestOptions.headers = {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        };
        requestOptions.body = JSON.stringify(datos);
      }

      const response = await this.apiCall(
        "/api/productos/guardar_producto.php",
        requestOptions,
      );

      if (response.success) {
        // Actualizar o añadir producto en el array local
        if (this.productoActual) {
          // Actualizar
          const index = this.productos.findIndex(
            (p) => p.id == response.producto.id,
          );
          if (index !== -1) {
            this.productos[index] = response.producto;
          }
        } else {
          // Añadir
          this.productos.unshift(response.producto);
        }

        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(this.elementos.modal);
        modal.hide();

        // Actualizar tabla
        this.renderizarTabla();

        // Mostrar éxito
        this.mostrarAlerta(
          this.productoActual
            ? "Producto actualizado correctamente"
            : "Producto creado correctamente",
          "success",
        );
      } else {
        throw new Error(response.message || "Error al guardar el producto");
      }
    } catch (error) {
      console.error("Error al guardar producto:", error);
      this.mostrarAlerta(
        error.message || "Error al guardar el producto",
        "danger",
      );
    }
  }

  validarFormulario() {
    if (!this.elementos.form) return false;

    const camposRequeridos = [
      "codigo",
      "nombre",
      "precio_venta",
      "stock_actual",
    ];
    let valido = true;

    camposRequeridos.forEach((campo) => {
      const elemento = this.elementos.form.querySelector(`[name="${campo}"]`);
      if (elemento && !elemento.value.trim()) {
        elemento.classList.add("is-invalid");
        valido = false;
      } else if (elemento) {
        elemento.classList.remove("is-invalid");
      }
    });

    if (!valido) {
      this.mostrarAlerta(
        "Por favor, complete todos los campos obligatorios",
        "warning",
      );
    }

    return valido;
  }

  limpiarFormulario() {
    if (!this.elementos.form) return;

    this.elementos.form.reset();

    // Limpiar clases de validación
    this.elementos.form
      .querySelectorAll(".is-invalid, .is-valid")
      .forEach((el) => {
        el.classList.remove("is-invalid", "is-valid");
      });

    // Limpiar vista previa de imagen
    const vistaPrevia = document.getElementById("vista-previa-imagen");
    if (vistaPrevia) {
      vistaPrevia.innerHTML = "";
    }

    // Restablecer valores por defecto
    document.getElementById("activo").checked = true;
    document.getElementById("venta-online").checked = true;
    document.getElementById("control-stock").checked = true;

    // Limpiar alertas
    document.getElementById("alerta-stock-bajo").style.display = "none";
    document.getElementById("alerta-stock-agotado").style.display = "none";
  }

  cargarFormulario(producto) {
    if (!this.elementos.form || !producto) return;

    // Cargar campos de texto y selects (excepto el campo de archivo imagen)
    Object.keys(producto).forEach((key) => {
      // Saltar el campo de archivo de imagen por seguridad
      if (key === "imagen") return;

      const elemento = this.elementos.form.querySelector(`[name="${key}"]`);
      if (elemento) {
        if (elemento.type === "checkbox") {
          elemento.checked = producto[key] == 1;
        } else {
          elemento.value = producto[key] || "";
        }
      }
    });

    // Cargar imagen existente si hay, con fallback
    const vistaPrevia = document.getElementById("vista-previa-imagen");
    if (vistaPrevia) {
      const imgSrc =
        producto.imagen_url ||
        (producto.imagen && producto.imagen.startsWith("/uploads/")
          ? producto.imagen
          : producto.imagen
            ? `/uploads/productos/${producto.imagen}`
            : null);
      if (imgSrc) {
        vistaPrevia.innerHTML = `<img src="${imgSrc}" alt="Imagen actual" class="img-thumbnail" style="max-height: 200px;">`;
      } else {
        vistaPrevia.innerHTML = "";
      }
    }

    // Cargar checkboxes especiales
    document.getElementById("activo").checked = producto.activo == 1;
    document.getElementById("venta-online").checked =
      producto.es_venta_online == 1;
    document.getElementById("control-stock").checked =
      producto.control_stock == 1;
    document.getElementById("requiere-receta").checked =
      producto.requiere_receta == 1;
    document.getElementById("fecha-caducidad").checked =
      producto.fecha_caducidad_control == 1;

    // Ya se gestiona arriba con el fallback

    // Validar stock
    this.validarStock();
  }

  cargarSelectCategorias() {
    const select = document.getElementById("categoria");
    const filtro = document.getElementById("filtro-categoria");

    if (!select || !filtro) return;

    const options =
      '<option value="">Seleccionar categoría</option>' +
      this.categorias
        .map(
          (cat) =>
            `<option value="${cat.id}">${this.escapeHtml(cat.nombre)}</option>`,
        )
        .join("");

    select.innerHTML = options;
    filtro.innerHTML =
      '<option value="">Todas las categorías</option>' + options;
  }

  cargarSelectProveedores() {
    const select = document.getElementById("proveedor");
    if (!select) return;

    select.innerHTML =
      '<option value="">Seleccionar proveedor</option>' +
      this.proveedores
        .map(
          (prov) =>
            `<option value="${prov.id}">${this.escapeHtml(prov.nombre)}</option>`,
        )
        .join("");
  }

  buscarProducto(termino) {
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
    const checkboxes = document.querySelectorAll(".seleccionar-producto");
    checkboxes.forEach((cb) => (cb.checked = seleccionado));
  }

  vistaPreviaImagen(event) {
    const archivo = event.target.files[0];
    if (!archivo) return;

    // Validar tipo de archivo
    if (!archivo.type.startsWith("image/")) {
      this.mostrarAlerta(
        "Por favor, seleccione un archivo de imagen válido",
        "warning",
      );
      event.target.value = "";
      return;
    }

    // Validar tamaño (5MB)
    if (archivo.size > 5 * 1024 * 1024) {
      this.mostrarAlerta("La imagen no debe superar los 5MB", "warning");
      event.target.value = "";
      return;
    }

    // Mostrar vista previa
    const reader = new FileReader();
    reader.onload = (e) => {
      const vistaPrevia = document.getElementById("vista-previa-imagen");
      if (vistaPrevia) {
        vistaPrevia.innerHTML = `<img src="${e.target.result}" alt="Vista previa" class="img-thumbnail">`;
      }
    };
    reader.readAsDataURL(archivo);
  }

  calcularMargen() {
    const precioCoste =
      parseFloat(document.getElementById("precio-coste").value) || 0;
    const precioVenta =
      parseFloat(document.getElementById("precio-venta").value) || 0;
    const margenInput = document.getElementById("margen");

    if (precioCoste > 0 && precioVenta > 0) {
      const margen = (
        ((precioVenta - precioCoste) / precioCoste) *
        100
      ).toFixed(2);
      margenInput.value = margen;
    }
  }

  calcularPrecioVenta() {
    const precioCoste =
      parseFloat(document.getElementById("precio-coste").value) || 0;
    const margen = parseFloat(document.getElementById("margen").value) || 0;
    const precioVentaInput = document.getElementById("precio-venta");

    if (precioCoste > 0 && margen > 0) {
      const precioVenta = precioCoste * (1 + margen / 100);
      precioVentaInput.value = precioVenta.toFixed(2);
    }
  }

  validarStock() {
    const stockActual =
      parseFloat(document.getElementById("stock-actual").value) || 0;
    const stockMinimo =
      parseFloat(document.getElementById("stock-minimo").value) || 0;

    const alertaBajo = document.getElementById("alerta-stock-bajo");
    const alertaAgotado = document.getElementById("alerta-stock-agotado");

    if (stockActual <= 0) {
      alertaAgotado.style.display = "block";
      alertaBajo.style.display = "none";
    } else if (stockMinimo > 0 && stockActual <= stockMinimo) {
      alertaBajo.style.display = "block";
      alertaAgotado.style.display = "none";
    } else {
      alertaBajo.style.display = "none";
      alertaAgotado.style.display = "none";
    }
  }

  async autoguardar() {
    if (!this.productoActual || !this.elementos.form) return;

    try {
      // Solo autoguardar si hay cambios significativos
      const formData = new FormData(this.elementos.form);
      const datos = Object.fromEntries(formData.entries());

      // Enviar al servidor como borrador
      await this.apiCall("/api/productos/autoguardar.php", {
        method: "POST",
        body: JSON.stringify({
          ...datos,
          id: this.productoActual.id,
          borrador: true,
        }),
      });

      console.log("Autoguardado completado");
    } catch (error) {
      console.error("Error en autoguardado:", error);
    }
  }

  editarDesdeDetalles() {
    if (!this.productoActual) return;

    // Cerrar modal de detalles
    const modalDetalles = bootstrap.Modal.getInstance(
      this.elementos.modalDetalles,
    );
    modalDetalles.hide();

    // Abrir modal de edición
    this.editarProducto(this.productoActual.id);
  }

  getCategoriaNombre(categoriaId) {
    const categoria = this.categorias.find((c) => c.id == categoriaId);
    return categoria ? categoria.nombre : "Sin categoría";
  }

  getStockClase(producto) {
    if (producto.stock_actual <= 0) return "stock-agotado";
    if (
      producto.stock_minimo > 0 &&
      producto.stock_actual <= producto.stock_minimo
    )
      return "stock-bajo";
    return "stock-disponible";
  }

  formatearMoneda(valor) {
    return new Intl.NumberFormat("es-ES", {
      style: "currency",
      currency: "EUR",
    }).format(valor || 0);
  }

  escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text || "";
    return div.innerHTML;
  }

  mostrarAlerta(mensaje, tipo = "info") {
    // Crear alerta
    const alerta = document.createElement("div");
    alerta.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
    alerta.style.cssText =
      "top: 20px; right: 20px; z-index: 9999; min-width: 300px;";
    alerta.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

    // Agregar al DOM
    document.body.appendChild(alerta);

    // Autoeliminar después de 5 segundos
    setTimeout(() => {
      if (alerta.parentNode) {
        alerta.parentNode.removeChild(alerta);
      }
    }, 5000);
  }

  animarFilas() {
    const filas = this.elementos.tbody?.querySelectorAll("tr");
    if (filas) {
      filas.forEach((fila, index) => {
        setTimeout(() => {
          fila.style.opacity = "0";
          fila.style.transform = "translateY(-10px)";
          setTimeout(() => {
            fila.style.transition = "all 0.3s ease";
            fila.style.opacity = "1";
            fila.style.transform = "translateY(0)";
          }, 50);
        }, index * 50);
      });
    }
  }

  verImagenAmpliada(src, alt = "Imagen del producto") {
    // Crear modal dinámicamente
    const modalHtml = `
      <div class="modal fade" id="modalImagenAmpliada" tabindex="-1" aria-labelledby="modalImagenLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalImagenLabel">${alt}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center">
              <img src="${src}" alt="${alt}" class="img-fluid rounded" style="max-height: 70vh; object-fit: contain;">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <a href="${src}" target="_blank" class="btn btn-primary">
                <i class="fas fa-external-link-alt me-2"></i>Abrir en nueva pestaña
              </a>
            </div>
          </div>
        </div>
      </div>
    `;

    // Eliminar modal anterior si existe
    const modalAnterior = document.getElementById("modalImagenAmpliada");
    if (modalAnterior) {
      modalAnterior.remove();
    }

    // Añadir nuevo modal al body
    document.body.insertAdjacentHTML("beforeend", modalHtml);

    // Mostrar modal
    const modal = new bootstrap.Modal(
      document.getElementById("modalImagenAmpliada"),
    );
    modal.show();

    // Limpiar modal al cerrarlo
    document
      .getElementById("modalImagenAmpliada")
      .addEventListener("hidden.bs.modal", function () {
        this.remove();
      });
  }
}

// Exportar para uso global
window.ProductosPage = ProductosPage;
