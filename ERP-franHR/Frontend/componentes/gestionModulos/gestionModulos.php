<?php
// Verificación de sesión
require_once '../Auth/SessionManager.php';
SessionManager::checkSession(); // Protege la página

// Verificar que el usuario sea administrador
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    header('Location: /Login/login.php');
    exit();
}

$currentUser = SessionManager::getUserInfo();
?>

<!-- Estilos específicos -->
<style>
    <?php include "gestionModulos.css"; ?>
</style>

<main id="gestion-modulos">
    <header class="gestion-header">
        <div class="header-content">
            <h1><i class="fas fa-puzzle-piece"></i> Gestión de Módulos</h1>
            <p>Administra los módulos del sistema ERP. Instala, activa, desactiva o desinstala módulos según tus necesidades.</p>
        </div>
        <div class="header-actions">
            <button class="btn-refresh" onclick="gestionModulos.recargar()">
                <i class="fas fa-sync-alt"></i> Actualizar
            </button>
        </div>
    </header>

    <div class="gestion-filtros">
        <div class="filtro-grupo">
            <label for="filtro-estado">Estado:</label>
            <select id="filtro-estado" onchange="gestionModulos.filtrar()">
                <option value="todos">Todos los módulos</option>
                <option value="instalados">Instalados</option>
                <option value="activos">Activos</option>
                <option value="inactivos">Inactivos</option>
                <option value="no-instalados">No instalados</option>
            </select>
        </div>
        <div class="filtro-grupo">
            <label for="filtro-categoria">Categoría:</label>
            <select id="filtro-categoria" onchange="gestionModulos.filtrar()">
                <option value="todas">Todas las categorías</option>
                <option value="sistema">Sistema</option>
                <option value="crm">CRM</option>
                <option value="ventas">Ventas</option>
                <option value="compras">Compras</option>
                <option value="inventario">Inventario</option>
                <option value="contabilidad">Contabilidad</option>
                <option value="rrhh">Recursos Humanos</option>
                <option value="produccion">Producción</option>
            </select>
        </div>
        <div class="filtro-grupo">
            <input type="text" id="filtro-busqueda" placeholder="Buscar módulo..." onkeyup="gestionModulos.filtrar()">
        </div>
    </div>

    <div class="gestion-contenido">
        <div class="cargando" id="cargando">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Cargando módulos...</p>
        </div>

        <div class="modulos-grid" id="modulos-grid" style="display: none;">
            <!-- Los módulos se cargarán dinámicamente -->
        </div>

        <div class="estado-vacio" id="estado-vacio" style="display: none;">
            <i class="fas fa-search"></i>
            <p>No se encontraron módulos que coincidan con los filtros.</p>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal" id="modal-confirmacion" style="display: none;">
        <div class="modal-contenido">
            <div class="modal-header">
                <h3 id="modal-titulo">Confirmar Acción</h3>
                <button class="modal-cerrar" onclick="gestionModulos.cerrarModal()">&times;</button>
            </div>
            <div class="modal-cuerpo">
                <p id="modal-mensaje">¿Estás seguro de realizar esta acción?</p>
                <div id="modal-detalles" class="modal-detalles"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="gestionModulos.cerrarModal()">Cancelar</button>
                <button class="btn btn-primary" id="modal-confirmar">Confirmar</button>
            </div>
        </div>
    </div>

    <!-- Modal de progreso -->
    <div class="modal" id="modal-progreso" style="display: none;">
        <div class="modal-contenido modal-progreso">
            <div class="modal-cuerpo">
                <div class="progreso-contenido">
                    <i class="fas fa-spinner fa-spin"></i>
                    <h3 id="progreso-titulo">Procesando...</h3>
                    <p id="progreso-mensaje">Por favor, espera un momento.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- JavaScript específico -->
<script>
    class GestionModulos {
        constructor() {
            this.modulos = [];
            this.modulosOriginales = [];
            this.init();
        }

        async init() {
            try {
                await this.cargarModulos();
                this.setupEventListeners();
                this.ocultarCargando();
                this.renderizarModulos();
            } catch (error) {
                console.error("Error al inicializar gestión de módulos:", error);
                this.mostrarError("Error al cargar los módulos. Por favor, inténtalo de nuevo.");
            }
        }

        async cargarModulos() {
            try {
                const response = await fetch("/modulos/api/gestion_modulos.php");
                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || "Error al cargar módulos");
                }

                this.modulos = data.data;
                this.modulosOriginales = [...this.modulos];
            } catch (error) {
                console.error("Error al cargar módulos:", error);
                throw error;
            }
        }

        renderizarModulos() {
            const grid = document.getElementById("modulos-grid");
            const estadoVacio = document.getElementById("estado-vacio");

            if (this.modulos.length === 0) {
                grid.style.display = "none";
                estadoVacio.style.display = "block";
                return;
            }

            grid.style.display = "grid";
            estadoVacio.style.display = "none";

            const modulosHTML = this.modulos.map(modulo => this.crearTarjetaModulo(modulo)).join("");
            grid.innerHTML = modulosHTML;
        }

        crearTarjetaModulo(modulo) {
            const estadoClase = this.getEstadoClase(modulo.estado);
            const estadoIcono = this.getEstadoIcono(modulo.estado);
            const estadoTexto = this.getEstadoTexto(modulo.estado);

            return `
                <div class="modulo-tarjeta ${estadoClase}" data-modulo-id="${modulo.id}" data-categoria="${modulo.categoria}" data-estado="${modulo.estado}">
                    <div class="modulo-header">
                        <div class="modulo-icono">
                            <i class="${modulo.icono}"></i>
                        </div>
                        <div class="modulo-info">
                            <h3 class="modulo-nombre">${modulo.nombre}</h3>
                            <p class="modulo-tecnico">${modulo.nombre_tecnico}</p>
                            <div class="modulo-version">v${modulo.version}</div>
                        </div>
                        <div class="modulo-estado">
                            <i class="${estadoIcono}"></i>
                            <span class="estado-texto">${estadoTexto}</span>
                        </div>
                    </div>

                    <div class="modulo-cuerpo">
                        <p class="modulo-descripcion">${modulo.descripcion}</p>

                        <div class="modulo-meta">
                            <span class="meta-item">
                                <i class="fas fa-layer-group"></i>
                                ${this.getCategoriaNombre(modulo.categoria)}
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-user"></i>
                                ${modulo.autor || 'Desconocido'}
                            </span>
                        </div>

                        ${modulo.dependencias && modulo.dependencias.length > 0 ? `
                            <div class="modulo-dependencias">
                                <strong>Dependencias:</strong>
                                <ul>
                                    ${modulo.dependencias.map(dep => `<li>${dep}</li>`).join('')}
                                </ul>
                            </div>
                        ` : ''}
                    </div>

                    <div class="modulo-acciones">
                        ${this.generarBotonesAccion(modulo)}
                    </div>
                </div>
            `;
        }

        generarBotonesAccion(modulo) {
            let botones = '';

            switch (modulo.estado) {
                case 'no_instalado':
                    botones = `
                        <button class="btn btn-success" onclick="gestionModulos.instalarModulo(${modulo.id})">
                            <i class="fas fa-download"></i> Instalar
                        </button>
                    `;
                    break;

                case 'inactivo':
                    botones = `
                        <button class="btn btn-primary" onclick="gestionModulos.activarModulo(${modulo.id})">
                            <i class="fas fa-play"></i> Activar
                        </button>
                        <button class="btn btn-danger" onclick="gestionModulos.desinstalarModulo(${modulo.id})">
                            <i class="fas fa-trash"></i> Desinstalar
                        </button>
                    `;
                    break;

                case 'activo':
                    if (modulo.nombre_tecnico !== 'dashboard') {
                        botones = `
                            <button class="btn btn-warning" onclick="gestionModulos.desactivarModulo(${modulo.id})">
                                <i class="fas fa-pause"></i> Desactivar
                            </button>
                            <button class="btn btn-danger" onclick="gestionModulos.desinstalarModulo(${modulo.id})">
                                <i class="fas fa-trash"></i> Desinstalar
                            </button>
                        `;
                    }
                    break;
            }

            return botones;
        }

        getEstadoClase(estado) {
            const clases = {
                'activo': 'estado-activo',
                'inactivo': 'estado-inactivo',
                'no_instalado': 'estado-no-instalado'
            };
            return clases[estado] || 'estado-desconocido';
        }

        getEstadoIcono(estado) {
            const iconos = {
                'activo': 'fas fa-check-circle',
                'inactivo': 'fas fa-pause-circle',
                'no_instalado': 'fas fa-times-circle'
            };
            return iconos[estado] || 'fas fa-question-circle';
        }

        getEstadoTexto(estado) {
            const textos = {
                'activo': 'Activo',
                'inactivo': 'Inactivo',
                'no_instalado': 'No Instalado'
            };
            return textos[estado] || estado;
        }

        getCategoriaNombre(categoria) {
            const nombres = {
                'sistema': 'Sistema',
                'crm': 'CRM',
                'ventas': 'Ventas',
                'compras': 'Compras',
                'inventario': 'Inventario',
                'contabilidad': 'Contabilidad',
                'rrhh': 'Recursos Humanos',
                'produccion': 'Producción'
            };
            return nombres[categoria] || categoria;
        }

        async instalarModulo(moduloId) {
            const modulo = this.modulos.find(m => m.id === moduloId);
            if (!modulo) return;

            this.mostrarModalConfirmacion(
                'Instalar Módulo',
                `¿Estás seguro de que quieres instalar el módulo <strong>${modulo.nombre}</strong>?`,
                `<p>Este módulo se añadirá al sistema y podrá ser activado posteriormente.</p>`,
                async () => {
                    await this.realizarAccion(moduloId, 'POST', 'Instalando módulo...', { id: moduloId });
                }
            );
        }

        async activarModulo(moduloId) {
            const modulo = this.modulos.find(m => m.id === moduloId);
            if (!modulo) return;

            this.mostrarModalConfirmacion(
                'Activar Módulo',
                `¿Estás seguro de que quieres activar el módulo <strong>${modulo.nombre}</strong>?`,
                `<p>El módulo aparecerá en el menú principal y estará disponible para los usuarios.</p>`,
                async () => {
                    await this.realizarAccion(moduloId, 'PUT', 'Activando módulo...', { id: moduloId, accion: 'activar' });
                }
            );
        }

        async desactivarModulo(moduloId) {
            const modulo = this.modulos.find(m => m.id === moduloId);
            if (!modulo) return;

            this.mostrarModalConfirmacion(
                'Desactivar Módulo',
                `¿Estás seguro de que quieres desactivar el módulo <strong>${modulo.nombre}</strong>?`,
                `<p><strong>Advertencia:</strong> El módulo dejará de estar disponible para todos los usuarios.</p>`,
                async () => {
                    await this.realizarAccion(moduloId, 'PUT', 'Desactivando módulo...', { id: moduloId, accion: 'desactivar' });
                }
            );
        }

        async desinstalarModulo(moduloId) {
            const modulo = this.modulos.find(m => m.id === moduloId);
            if (!modulo) return;

            this.mostrarModalConfirmacion(
                'Desinstalar Módulo',
                `¿Estás seguro de que quieres desinstalar el módulo <strong>${modulo.nombre}</strong>?`,
                `<p><strong>¡ADVERTENCIA!</strong> Esta acción eliminará:</p>
                 <ul>
                    <li>Toda la configuración del módulo</li>
                    <li>Permisos asignados</li>
                    <li>Datos asociados (si existen)</li>
                 </ul>
                 <p>Esta acción no se puede deshacer.</p>`,
                async () => {
                    await this.realizarAccion(moduloId, 'DELETE', 'Desinstalando módulo...', null);
                }
            );
        }

        async realizarAccion(moduloId, method, datosProgreso, datosBody) {
            this.cerrarModal();
            this.mostrarModalProgreso('Procesando...', datosProgreso);

            try {
                const opciones = {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json'
                    }
                };

                if (method !== 'GET') {
                    if (method === 'DELETE') {
                        opciones.body = null;
                        // Para DELETE, el ID va en la URL
                    } else {
                        // datosBody contiene los datos para el body de la petición
                        if (typeof datosBody === 'object') {
                            opciones.body = JSON.stringify(datosBody);
                        }
                    }
                }

                const url = method === 'DELETE'
                    ? `/modulos/api/gestion_modulos.php?id=${moduloId}`
                    : `/modulos/api/gestion_modulos.php`;

                const response = await fetch(url, opciones);
                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Error en la operación');
                }

                this.cerrarModal();
                this.mostrarExito(data.message);

                // Recargar módulos y actualizar menú
                await this.cargarModulos();
                this.renderizarModulos();

                // Actualizar menú principal si está disponible
                if (window.menuManager) {
                    await window.menuManager.actualizarMenu();
                }

            } catch (error) {
                console.error("Error en la acción:", error);
                this.cerrarModal();
                this.mostrarError(error.message || 'Error al realizar la acción');
            }
        }

        filtrar() {
            const estado = document.getElementById('filtro-estado').value;
            const categoria = document.getElementById('filtro-categoria').value;
            const busqueda = document.getElementById('filtro-busqueda').value.toLowerCase();

            this.modulos = this.modulosOriginales.filter(modulo => {
                // Filtrar por estado
                if (estado !== 'todos') {
                    const estadoMatch = {
                        'instalados': modulo.instalado === 1,
                        'activos': modulo.estado === 'activo',
                        'inactivos': modulo.estado === 'inactivo',
                        'no-instalados': modulo.estado === 'no_instalado'
                    };
                    if (!estadoMatch[estado]) return false;
                }

                // Filtrar por categoría
                if (categoria !== 'todas' && modulo.categoria !== categoria) {
                    return false;
                }

                // Filtrar por búsqueda
                if (busqueda && !(
                    modulo.nombre.toLowerCase().includes(busqueda) ||
                    modulo.nombre_tecnico.toLowerCase().includes(busqueda) ||
                    modulo.descripcion.toLowerCase().includes(busqueda)
                )) {
                    return false;
                }

                return true;
            });

            this.renderizarModulos();
        }

        recargar() {
            const btn = document.querySelector('.btn-refresh i');
            btn.classList.add('fa-spin');

            this.init().finally(() => {
                btn.classList.remove('fa-spin');
            });
        }

        // Métodos para modales
        mostrarModalConfirmacion(titulo, mensaje, detalles, onConfirmar) {
            const modal = document.getElementById('modal-confirmacion');
            document.getElementById('modal-titulo').textContent = titulo;
            document.getElementById('modal-mensaje').innerHTML = mensaje;
            document.getElementById('modal-detalles').innerHTML = detalles || '';

            const btnConfirmar = document.getElementById('modal-confirmar');
            btnConfirmar.onclick = onConfirmar;

            modal.style.display = 'block';
        }

        mostrarModalProgreso(titulo, mensaje) {
            const modal = document.getElementById('modal-progreso');
            document.getElementById('progreso-titulo').textContent = titulo;
            document.getElementById('progreso-mensaje').textContent = mensaje;
            modal.style.display = 'block';
        }

        cerrarModal() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.style.display = 'none';
            });
        }

        // Métodos de mensajes
        mostrarExito(mensaje) {
            this.mostrarNotificacion(mensaje, 'success');
        }

        mostrarError(mensaje) {
            this.mostrarNotificacion(mensaje, 'error');
        }

        mostrarNotificacion(mensaje, tipo) {
            // Crear notificación
            const notificacion = document.createElement('div');
            notificacion.className = `notificacion notificacion-${tipo}`;
            notificacion.innerHTML = `
                <i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${mensaje}</span>
            `;

            document.body.appendChild(notificacion);

            // Mostrar animación
            setTimeout(() => notificacion.classList.add('mostrar'), 100);

            // Auto-ocultar después de 5 segundos
            setTimeout(() => {
                notificacion.classList.remove('mostrar');
                setTimeout(() => notificacion.remove(), 300);
            }, 5000);
        }

        setupEventListeners() {
            // Cerrar modales con Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.cerrarModal();
                }
            });

            // Cerrar modulos al hacer clic fuera
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        this.cerrarModal();
                    }
                });
            });
        }

        ocultarCargando() {
            document.getElementById('cargando').style.display = 'none';
        }
    }

    // Inicializar cuando el DOM esté listo
    let gestionModulos;
    document.addEventListener('DOMContentLoaded', () => {
        gestionModulos = new GestionModulos();
    });

    // Hacer disponible globalmente
    window.gestionModulos = gestionModulos;
</script>

<?php include '../Footer/Footer.php'; ?>
