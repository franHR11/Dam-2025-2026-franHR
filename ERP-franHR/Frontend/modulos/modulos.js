// JavaScript para gestión de módulos - Sistema independiente
class ModulosManager {
  constructor() {
    this.apiBase = "/modulos/api/gestion_modulos.php";
    this.init();
  }

  init() {
    console.log("Gestor de módulos inicializado");
    this.setupEventListeners();
  }

  setupEventListeners() {
    // Los botones ya están configurados en el HTML con onclick
    // Esta función está disponible para futuras extensiones
  }

  // Mostrar modal de confirmación personalizado
  mostrarConfirmacion(titulo, mensaje, onConfirmar) {
    // Crear modal dinámico
    const modal = document.createElement("div");
    modal.className = "modal-confirmacion";
    modal.innerHTML = `
            <div class="modal-contenido">
                <div class="modal-header">
                    <h3>${titulo}</h3>
                    <button class="modal-cerrar" onclick="this.closest('.modal-confirmacion').remove()">&times;</button>
                </div>
                <div class="modal-cuerpo">
                    <p>${mensaje}</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="this.closest('.modal-confirmacion').remove()">Cancelar</button>
                    <button class="btn btn-primary" id="btn-confirmar">Confirmar</button>
                </div>
            </div>
        `;

    // Añadir estilos si no existen
    if (!document.querySelector("#modal-estilos")) {
      const estilos = document.createElement("style");
      estilos.id = "modal-estilos";
      estilos.textContent = `
                .modal-confirmacion {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                    animation: fadeIn 0.3s ease;
                }
                .modal-contenido {
                    background: white;
                    border-radius: 12px;
                    max-width: 500px;
                    width: 90%;
                    animation: slideIn 0.3s ease;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                }
                .modal-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 20px 25px;
                    border-bottom: 1px solid #f0f0f0;
                    background: #f8f9fa;
                    border-radius: 12px 12px 0 0;
                }
                .modal-header h3 {
                    margin: 0;
                    color: #333;
                    font-size: 1.3em;
                }
                .modal-cerrar {
                    background: none;
                    border: none;
                    font-size: 1.5em;
                    cursor: pointer;
                    color: #999;
                    padding: 0;
                    width: 30px;
                    height: 30px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 50%;
                    transition: all 0.3s ease;
                }
                .modal-cerrar:hover {
                    background: #f0f0f0;
                    color: #333;
                }
                .modal-cuerpo {
                    padding: 25px;
                }
                .modal-cuerpo p {
                    margin: 0;
                    color: #555;
                    line-height: 1.6;
                }
                .modal-footer {
                    display: flex;
                    justify-content: flex-end;
                    gap: 10px;
                    padding: 20px 25px;
                    border-top: 1px solid #f0f0f0;
                    background: #f8f9fa;
                    border-radius: 0 0 12px 12px;
                }
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                @keyframes slideIn {
                    from { opacity: 0; transform: scale(0.9); }
                    to { opacity: 1; transform: scale(1); }
                }
            `;
      document.head.appendChild(estilos);
    }

    document.body.appendChild(modal);

    // Configurar botón de confirmar
    const btnConfirmar = modal.querySelector("#btn-confirmar");
    btnConfirmar.addEventListener("click", () => {
      modal.remove();
      if (onConfirmar) onConfirmar();
    });

    // Cerrar al hacer clic fuera
    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        modal.remove();
      }
    });
  }

  // Mostrar notificación
  mostrarNotificacion(mensaje, tipo = "success") {
    const notificacion = document.createElement("div");
    notificacion.className = `notificacion notificacion-${tipo}`;
    notificacion.innerHTML = `
            <i class="fas ${tipo === "success" ? "fa-check-circle" : "fa-exclamation-circle"}"></i>
            <span>${mensaje}</span>
        `;

    // Añadir estilos de notificación si no existen
    if (!document.querySelector("#notificacion-estilos")) {
      const estilos = document.createElement("style");
      estilos.id = "notificacion-estilos";
      estilos.textContent = `
                .notificacion {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 15px 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    z-index: 20000;
                    transform: translateX(400px);
                    transition: transform 0.3s ease;
                    max-width: 400px;
                }
                .notificacion.mostrar {
                    transform: translateX(0);
                }
                .notificacion-success {
                    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
                    color: #155724;
                    border-left: 4px solid #28a745;
                }
                .notificacion-error {
                    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
                    color: #721c24;
                    border-left: 4px solid #dc3545;
                }
                .notificacion i {
                    font-size: 1.2em;
                }
            `;
      document.head.appendChild(estilos);
    }

    document.body.appendChild(notificacion);

    // Mostrar animación
    setTimeout(() => notificacion.classList.add("mostrar"), 100);

    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
      notificacion.classList.remove("mostrar");
      setTimeout(() => notificacion.remove(), 300);
    }, 5000);
  }

  // Gestionar módulo con API
  async gestionarModulo(moduloId, accion) {
    try {
      const mensajes = {
        instalar: {
          titulo: "Instalar Módulo",
          mensaje: "¿Estás seguro de que quieres instalar este módulo?",
          metodo: "POST",
        },
        activar: {
          titulo: "Activar Módulo",
          mensaje:
            "¿Estás seguro de que quieres activar este módulo? El módulo aparecerá en el menú principal.",
          metodo: "PUT",
        },
        desactivar: {
          titulo: "Desactivar Módulo",
          mensaje:
            "¿Estás seguro de que quieres desactivar este módulo? El módulo dejará de estar disponible para los usuarios.",
          metodo: "PUT",
        },
        desinstalar: {
          titulo: "Desinstalar Módulo",
          mensaje:
            "¿Estás seguro de que quieres desinstalar este módulo? Esta acción eliminará toda la configuración del módulo y no se puede deshacer.",
          metodo: "DELETE",
        },
      };

      const config = mensajes[accion];
      if (!config) {
        throw new Error("Acción no válida");
      }

      // Mostrar confirmación
      this.mostrarConfirmacion(config.titulo, config.mensaje, async () => {
        await this.ejecutarAccion(moduloId, accion, config.metodo);
      });
    } catch (error) {
      console.error("Error en gestión de módulo:", error);
      this.mostrarNotificacion("Error: " + error.message, "error");
    }
  }

  // Ejecutar acción en la API
  async ejecutarAccion(moduloId, accion, metodo) {
    try {
      const opciones = {
        method: metodo,
        headers: {
          "Content-Type": "application/json",
        },
      };

      // Para PUT y DELETE, necesitamos diferentes configuraciones
      if (metodo === "PUT") {
        opciones.body = JSON.stringify({
          id: moduloId,
          accion: accion,
        });
      } else if (metodo === "POST") {
        opciones.body = JSON.stringify({
          id: moduloId,
        });
      }

      // Construir URL
      const url =
        metodo === "DELETE" ? `${this.apiBase}?id=${moduloId}` : this.apiBase;

      // Mostrar log para depuración
      console.log(`Ejecutando ${metodo} en ${url} con datos:`, opciones.body);
      console.log(`URL completa: ${url}`);
      console.log(`Headers:`, opciones.headers);

      // Mostrar indicador de carga
      this.mostrarNotificacion("Procesando...", "info");

      const response = await fetch(url, opciones);
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || "Error en la respuesta del servidor");
      }

      if (!data.success) {
        throw new Error(data.message || "Error en la operación");
      }

      // Mostrar éxito
      this.mostrarNotificacion(data.message, "success");

      // Recargar página para reflejar cambios
      setTimeout(() => {
        location.reload();
      }, 1500);
    } catch (error) {
      console.error("Error ejecutando acción:", error);
      this.mostrarNotificacion("Error: " + error.message, "error");
    }
  }

  // Método para actualizar módulo específico sin recargar página completa
  async actualizarModulo(moduloId, nuevoEstado) {
    try {
      const elemento = document.querySelector(`[data-modulo-id="${moduloId}"]`);
      if (!elemento) return;

      // Actualizar clases y datos
      elemento.className = elemento.className.replace(
        /estado-\w+/,
        `estado-${nuevoEstado}`,
      );
      elemento.dataset.estado = nuevoEstado;

      // Actualizar icono y texto de estado
      const estadoIcono = elemento.querySelector(".module-status i");
      const estadoTexto = elemento.querySelector(".module-status span");
      const moduleIcon = elemento.querySelector(".module-icon");

      if (estadoIcono && estadoTexto) {
        const iconos = {
          activo: "fa-check-circle",
          inactivo: "fa-pause-circle",
          "no-instalado": "fa-times-circle",
        };

        const textos = {
          activo: "Activo",
          inactivo: "Inactivo",
          "no-instalado": "No Instalado",
        };

        estadoIcono.className = `fas ${iconos[nuevoEstado]}`;
        estadoTexto.textContent = textos[nuevoEstado];
      }

      // Actualizar color del icono del módulo
      if (moduleIcon) {
        const colores = {
          activo: "linear-gradient(135deg, #28a745 0%, #20c997 100%)",
          inactivo: "linear-gradient(135deg, #ffc107 0%, #ff9800 100%)",
          "no-instalado": "linear-gradient(135deg, #6c757d 0%, #495057 100%)",
        };

        moduleIcon.style.background = colores[nuevoEstado];
      }

      // Actualizar botones de acción
      this.actualizarBotones(elemento, nuevoEstado);

      // Actualizar estadísticas
      this.actualizarEstadisticas();
    } catch (error) {
      console.error("Error actualizando módulo:", error);
    }
  }

  // Actualizar botones de acción
  actualizarBotones(elemento, estado) {
    const actionsContainer = elemento.querySelector(".module-actions");
    if (!actionsContainer) return;

    const nombreTecnico = elemento.dataset.nombreTecnico;
    const moduloId = elemento.dataset.moduloId;

    let botonesHTML = "";

    switch (estado) {
      case "no-instalado":
        botonesHTML = `
                    <button class="btn btn-primary" onclick="modulosManager.gestionarModulo(${moduloId}, 'instalar')">
                        <i class="fas fa-download"></i> Instalar
                    </button>
                `;
        break;
      case "inactivo":
        botonesHTML = `
                    <button class="btn btn-success" onclick="modulosManager.gestionarModulo(${moduloId}, 'activar')">
                        <i class="fas fa-play"></i> Activar
                    </button>
                    <button class="btn btn-danger" onclick="modulosManager.gestionarModulo(${moduloId}, 'desinstalar')">
                        <i class="fas fa-trash"></i> Desinstalar
                    </button>
                `;
        break;
      case "activo":
        if (nombreTecnico !== "dashboard") {
          botonesHTML = `
                        <button class="btn btn-warning" onclick="modulosManager.gestionarModulo(${moduloId}, 'desactivar')">
                            <i class="fas fa-pause"></i> Desactivar
                        </button>
                        <button class="btn btn-danger" onclick="modulosManager.gestionarModulo(${moduloId}, 'desinstalar')">
                            <i class="fas fa-trash"></i> Desinstalar
                        </button>
                    `;
        } else {
          botonesHTML = `
                        <button class="btn btn-warning" disabled>
                            <i class="fas fa-lock"></i> Módulo del Sistema
                        </button>
                    `;
        }
        break;
    }

    actionsContainer.innerHTML = botonesHTML;
  }

  // Actualizar estadísticas del header
  actualizarEstadisticas() {
    const totalModulos = document.querySelectorAll(".module-card").length;
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

  // Actualizar menú principal si existe
  actualizarMenuPrincipal() {
    try {
      // Intentar actualizar el menú principal si existe la función
      if (typeof window.actualizarMenuPrincipal === "function") {
        window.actualizarMenuPrincipal();
      }

      // También intentar con el gestor de menú si existe
      if (
        window.menuManager &&
        typeof window.menuManager.actualizarMenu === "function"
      ) {
        window.menuManager.actualizarMenu();
      }
    } catch (error) {
      console.log("No se pudo actualizar el menú principal:", error);
    }
  }
}

// Crear instancia global
let modulosManager;

// Inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
  modulosManager = new ModulosManager();

  // Hacer disponible globalmente
  window.modulosManager = modulosManager;

  console.log("Sistema de gestión de módulos cargado correctamente");
});

// Funciones globales para compatibilidad con onclick en HTML
window.gestionarModulo = (moduloId, accion) => {
  console.log("Llamada a gestionarModulo con:", { moduloId, accion });
  if (window.modulosManager) {
    window.modulosManager.gestionarModulo(moduloId, accion);
  } else {
    console.error("modulosManager no está disponible");
    alert("Error: El sistema de módulos no está inicializado");
  }
};

window.filtrarModulos = (filtro) => {
  const cards = document.querySelectorAll(".module-card");
  const buttons = document.querySelectorAll(".filter-btn");

  // Actualizar botón activo
  buttons.forEach((btn) => btn.classList.remove("active"));
  if (event && event.target) {
    event.target.classList.add("active");
  }

  // Filtrar tarjetas
  cards.forEach((card) => {
    const estado = card.dataset.estado;
    let mostrar = false;

    switch (filtro) {
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
};
