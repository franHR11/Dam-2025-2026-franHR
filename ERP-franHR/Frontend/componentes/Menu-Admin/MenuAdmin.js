// Sistema de menú dinámico modular
class MenuManager {
  constructor() {
    this.modulos = [];
    this.usuario = null;
    this.menuContainer = document.getElementById("menu");
    this.init();
  }

  async init() {
    try {
      await this.cargarModulos();
      this.renderizarMenu();
      this.setupEventListeners();
    } catch (error) {
      console.error("Error al inicializar el menú:", error);
      this.mostrarError();
    }
  }

  async cargarModulos() {
    try {
      const response = await fetch("/api/modulos/obtener_modulos.php");
      const data = await response.json();

      if (!data.success) {
        throw new Error(data.message || "Error al cargar módulos");
      }

      this.modulos = data.data.modulos;
      this.usuario = data.data.usuario;

      // Deduplicar por id para evitar entradas repetidas por agregaciones de permisos
      const vistos = new Set();
      this.modulos = this.modulos.filter((m) => {
        if (vistos.has(m.id)) return false;
        vistos.add(m.id);
        return true;
      });

      this.agregarModulosBase();

      // Guardar en localStorage para acceso rápido
      localStorage.setItem("erp_modulos", JSON.stringify(this.modulos));
      localStorage.setItem("erp_usuario", JSON.stringify(this.usuario));
    } catch (error) {
      // Intentar cargar desde cache si hay error de red
      const cachedModulos = localStorage.getItem("erp_modulos");
      const cachedUsuario = localStorage.getItem("erp_usuario");

      if (cachedModulos && cachedUsuario) {
        this.modulos = JSON.parse(cachedModulos);
        this.usuario = JSON.parse(cachedUsuario);
        this.agregarModulosBase();
        console.warn("Usando caché de módulos debido a error de red:", error);
      } else {
        throw error;
      }
    }
  }

  agregarModulosBase() {
    const existeKanban = this.modulos.some(
      (modulo) => modulo?.nombre_tecnico?.toLowerCase() === "kanban"
    );

    if (!existeKanban) {
      this.modulos.push({
        id: "mod-kanban-local",
        nombre: "Tablero Kanban",
        nombre_tecnico: "kanban",
        descripcion: "Organiza tareas con columnas y tarjetas drag & drop.",
        icono: "fas fa-columns",
        categoria: "proyectos",
        version: "1.0.0",
        menu_order: 95,
        permisos: {
          ver: true,
          crear: true,
          editar: true,
          eliminar: true,
          listar: true,
          configurar: true,
        },
      });
    }
  }

  renderizarMenu() {
    if (!this.menuContainer) {
      console.error("No se encontró el contenedor del menú");
      return;
    }

    const menuHTML = this.generarMenuHTML();
    this.menuContainer.innerHTML = menuHTML;

    // Agregar animación de entrada
    this.animarMenu();
  }

  generarMenuHTML() {
    let html = `
            <nav class="menu-navegacion">
                <div class="menu-header">
                    <div class="usuario-info">
                        <i class="fas fa-user-circle"></i>
                        <div class="usuario-detalles">
                            <span class="usuario-nombre">${
                              this.usuario?.rol || "Invitado"
                            }</span>
                            <span class="usuario-rol">${this.getRolDisplay(
                              this.usuario?.rol
                            )}</span>
                        </div>
                    </div>
                </div>
                <ul class="menu-lista">
        `;

    // Agrupar módulos por categoría
    const modulosPorCategoria = {};
    this.modulos.forEach((modulo) => {
      if (!modulosPorCategoria[modulo.categoria]) {
        modulosPorCategoria[modulo.categoria] = [];
      }
      modulosPorCategoria[modulo.categoria].push(modulo);
    });

    // Renderizar cada categoría
    Object.keys(modulosPorCategoria).forEach((categoria) => {
      const categoriaNombre = this.getCategoriaDisplay(categoria);
      const iconoCategoria = this.getCategoriaIcono(categoria);
      const modulos = modulosPorCategoria[categoria];

      html += `
                <li class="menu-categoria">
                    <div class="categoria-header" onclick="menuManager.toggleCategoria('${categoria}')">
                        <i class="${iconoCategoria}"></i>
                        <span class="categoria-nombre">${categoriaNombre}</span>
                        <i class="fas fa-chevron-down categoria-toggle"></i>
                    </div>
                    <ul class="categoria-modulos" id="categoria-${categoria}">
            `;

      modulos.forEach((modulo) => {
        const rutaModulo = this.getRutaModulo(modulo.nombre_tecnico);
        const activo = this.esModuloActivo(modulo.nombre_tecnico);

        html += `
                    <li class="menu-item ${
                      activo ? "active" : ""
                    }" data-modulo="${modulo.nombre_tecnico}">
                        <a href="${rutaModulo}" class="menu-link" onclick="menuManager.navegarAModulo(event, '${
          modulo.nombre_tecnico
        }', '${rutaModulo}')">
                            <i class="${modulo.icono}"></i>
                            <span class="modulo-nombre">${modulo.nombre}</span>
                            ${
                              modulo.version
                                ? `<span class="modulo-version">v${modulo.version}</span>`
                                : ""
                            }
                        </a>
                        ${
                          modulo.permisos?.configurar
                            ? `
                            <button class="modulo-config" onclick="menuManager.configurarModulo(event, ${modulo.id})" title="Configurar módulo">
                                <i class="fas fa-cog"></i>
                            </button>
                        `
                            : ""
                        }
                    </li>
                `;
      });

      html += `
                    </ul>
                </li>
            `;
    });

    // Agregar opción de gestión de módulos para administradores
    if (this.usuario?.rol === "admin") {
      html += `
                <li class="menu-item menu-admin">
                    <a href="#" class="menu-link" onclick="menuManager.abrirGestionModulos(event)">
                        <i class="fas fa-puzzle-piece"></i>
                        <span class="modulo-nombre">Gestionar Módulos</span>
                    </a>
                </li>
            `;
    }

    html += `
                </ul>
            </nav>
        `;

    return html;
  }

  getCategoriaDisplay(categoria) {
    const categorias = {
      sistema: "Sistema",
      crm: "CRM",
      ventas: "Ventas",
      compras: "Compras",
      inventario: "Inventario",
      contabilidad: "Contabilidad",
      rrhh: "Recursos Humanos",
      produccion: "Producción",
      proyectos: "Proyectos",
    };
    return (
      categorias[categoria] ||
      categoria.charAt(0).toUpperCase() + categoria.slice(1)
    );
  }

  getCategoriaIcono(categoria) {
    const iconos = {
      sistema: "fas fa-cogs",
      crm: "fas fa-users",
      ventas: "fas fa-shopping-cart",
      compras: "fas fa-truck",
      inventario: "fas fa-box",
      contabilidad: "fas fa-calculator",
      rrhh: "fas fa-user-tie",
      produccion: "fas fa-industry",
      proyectos: "fas fa-diagram-project",
    };
    return iconos[categoria] || "fas fa-folder";
  }

  getRutaModulo(nombreTecnico) {
    const rutas = {
      dashboard: "/escritorio/escritorio.php",
      clientes: "/Paginas/clientes/clientes.php",
      proveedores: "/Paginas/proveedores/proveedores.php",
      productos: "/Paginas/productos/productos.php",
      presupuestos: "/Paginas/presupuestos/presupuestos.php",
      facturacion: "/Paginas/facturacion/facturacion.php",
      usuarios: "/Paginas/usuarios/usuarios.php",
      configuracion: "/Paginas/configuracion/configuracion.php",
      kanban: "/Paginas/kanban/111.php",
    };
    return (
      rutas[nombreTecnico] || `/Paginas/${nombreTecnico}/${nombreTecnico}.php`
    );
  }

  getRolDisplay(rol) {
    const roles = {
      admin: "Administrador",
      usuario: "Usuario",
      gerente: "Gerente",
    };
    return roles[rol] || rol.charAt(0).toUpperCase() + rol.slice(1);
  }

  esModuloActivo(nombreTecnico) {
    const pathActual = window.location.pathname;
    return pathActual.includes(nombreTecnico);
  }

  toggleCategoria(categoria) {
    const categoriaElement = document.getElementById(`categoria-${categoria}`);
    const toggle =
      categoriaElement.previousElementSibling.querySelector(
        ".categoria-toggle"
      );

    if (categoriaElement.style.display === "none") {
      categoriaElement.style.display = "block";
      toggle.classList.remove("fa-chevron-right");
      toggle.classList.add("fa-chevron-down");
    } else {
      categoriaElement.style.display = "none";
      toggle.classList.remove("fa-chevron-down");
      toggle.classList.add("fa-chevron-right");
    }
  }

  navegarAModulo(event, nombreTecnico, ruta) {
    event.preventDefault();

    // Marcar como activo
    document.querySelectorAll(".menu-item").forEach((item) => {
      item.classList.remove("active");
    });

    const itemActual = document.querySelector(
      `[data-modulo="${nombreTecnico}"]`
    );
    if (itemActual) {
      itemActual.classList.add("active");
    }

    // Navegar a la ruta
    window.location.href = ruta;
  }

  async configurarModulo(event, moduloId) {
    event.preventDefault();
    event.stopPropagation();

    try {
      const modulo = this.modulos.find((m) => m.id === moduloId);
      if (!modulo) return;

      // Aquí puedes abrir un modal de configuración
      // Por ahora, mostramos una alerta simple
      alert(
        `Configuración del módulo: ${modulo.nombre}\n\nEsta funcionalidad estará disponible próximamente.`
      );
    } catch (error) {
      console.error("Error al configurar módulo:", error);
    }
  }

  async abrirGestionModulos(event) {
    event.preventDefault();

    try {
      // Cargar el contenido dinámicamente
      const response = await fetch(
        "/componentes/gestionModulos/gestionModulos.php"
      );
      const html = await response.text();

      // Actualizar el área de contenido
      const contentArea = document.getElementById("content-area");
      if (contentArea) {
        contentArea.innerHTML = html;

        // Cargar el script de gestión de módulos
        const script = document.createElement("script");
        script.src = "/componentes/gestionModulos/gestionModulos.js";
        contentArea.appendChild(script);
      }
    } catch (error) {
      console.error("Error al cargar gestión de módulos:", error);
      alert(
        "Error al cargar la gestión de módulos. Por favor, inténtalo de nuevo."
      );
    }
  }

  animarMenu() {
    const items = this.menuContainer.querySelectorAll(
      ".menu-item, .menu-categoria"
    );
    items.forEach((item, index) => {
      item.style.opacity = "0";
      item.style.transform = "translateX(-20px)";

      setTimeout(() => {
        item.style.transition = "all 0.3s ease";
        item.style.opacity = "1";
        item.style.transform = "translateX(0)";
      }, index * 50);
    });
  }

  setupEventListeners() {
    // Expandir categorías por defecto si hay módulos activos
    const categorias = document.querySelectorAll(".categoria-modulos");
    categorias.forEach((categoria) => {
      const itemsActivos = categoria.querySelectorAll(".menu-item.active");
      if (itemsActivos.length > 0) {
        categoria.style.display = "block";
        const toggle =
          categoria.previousElementSibling.querySelector(".categoria-toggle");
        if (toggle) {
          toggle.classList.remove("fa-chevron-right");
          toggle.classList.add("fa-chevron-down");
        }
      }
    });
  }

  mostrarError() {
    if (!this.menuContainer) return;

    this.menuContainer.innerHTML = `
            <div class="menu-error">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Error al cargar el menú</p>
                <button onclick="menuManager.recargar()" class="btn-recargar">
                    <i class="fas fa-sync"></i> Recargar
                </button>
            </div>
        `;
  }

  async recargar() {
    try {
      await this.cargarModulos();
      this.renderizarMenu();
      this.setupEventListeners();
    } catch (error) {
      console.error("Error al recargar el menú:", error);
      this.mostrarError();
    }
  }

  // Método público para actualizar el menú cuando se instala/desinstala un módulo
  async actualizarMenu() {
    // Limpiar cache
    localStorage.removeItem("erp_modulos");
    localStorage.removeItem("erp_usuario");

    // Recargar módulos
    await this.recargar();
  }
}

// Inicializar el gestor de menú cuando el DOM esté listo
let menuManager;

document.addEventListener("DOMContentLoaded", () => {
  menuManager = new MenuManager();
});

// Hacer disponible globalmente para acceso desde otros scripts
window.menuManager = menuManager;
