/**
 * Servicio API para comunicación con el backend
 */
class ApiService {
    constructor() {
        // Obtener la URL base desde variables de entorno o usar valor por defecto
        this.baseUrl = process.env.API_BASE_URL || '/erp-franhr/frontend/api';
        this.apiEndpoint = `${this.baseUrl}/plantilla.php`;
    }

    /**
     * Realizar petición HTTP
     * @param {string} method - Método HTTP (GET, POST, PUT, DELETE)
     * @param {string} ruta - Ruta específica de la API
     * @param {Object} data - Datos a enviar
     * @returns {Promise} Respuesta de la API
     */
    async request(method, ruta, data = null) {
        const url = `${this.apiEndpoint}?ruta=${ruta}`;
        
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        };

        if (data && (method === 'POST' || method === 'PUT')) {
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, options);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            return result;
        } catch (error) {
            console.error('Error en la petición API:', error);
            throw error;
        }
    }

    // Métodos específicos de la API
    async obtenerDatos(filtros = {}) {
        return await this.request('GET', 'obtener', filtros);
    }

    async crearElemento(data) {
        return await this.request('POST', 'crear', data);
    }

    async actualizarElemento(id, data) {
        return await this.request('PUT', `actualizar/${id}`, data);
    }

    async eliminarElemento(id) {
        return await this.request('DELETE', `eliminar/${id}`);
    }
}

/**
 * Clase principal para manejar la página de plantilla
 */
class PlantillaPage {
    constructor() {
        this.api = new ApiService();
        this.datos = [];
        this.filtros = {};
        this.elementoSeleccionado = null;
    }

    /**
     * Inicializar la página
     */
    async init() {
        this.setupEventListeners();
        await this.cargarDatos();
        this.renderizarContenido();
    }

    /**
     * Configurar event listeners
     */
    setupEventListeners() {
        // Función auxiliar para agregar event listener de forma segura
        const safeAddEventListener = (id, event, handler) => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener(event, handler);
            } else {
                console.warn(`Elemento con ID '${id}' no encontrado`);
            }
        };

        // Botones principales
        safeAddEventListener('nuevo-btn', 'click', () => this.abrirModalNuevo());
        safeAddEventListener('guardar-btn', 'click', () => this.guardarCambios());
        safeAddEventListener('actualizar-btn', 'click', () => this.actualizarDatos());

        // Búsqueda
        safeAddEventListener('buscar-btn', 'click', () => this.buscar());
        safeAddEventListener('buscar-input', 'keypress', (e) => {
            if (e.key === 'Enter') {
                this.buscar();
            }
        });

        // Modal
        safeAddEventListener('modal-confirmar', 'click', () => this.confirmarAccion());

        // Eventos globales
        document.addEventListener('keydown', (e) => this.manejarTeclado(e));
    }

    /**
     * Cargar datos desde la API
     */
    async cargarDatos() {
        try {
            this.mostrarCargando(true);
            const response = await this.api.obtenerDatos(this.filtros);
            
            if (response.success) {
                this.datos = response.data || [];
                this.mostrarNotificacion('Datos cargados correctamente', 'success');
            } else {
                throw new Error(response.message || 'Error al cargar datos');
            }
        } catch (error) {
            console.error('Error al cargar datos:', error);
            this.mostrarNotificacion('Error al cargar datos: ' + error.message, 'error');
            this.datos = [];
        } finally {
            this.mostrarCargando(false);
        }
    }

    /**
     * Renderizar el contenido principal
     */
    renderizarContenido() {
        const contenedor = document.getElementById('contenido-dinamico');
        if (!contenedor) return;

        if (this.datos.length === 0) {
            contenedor.innerHTML = this.obtenerHTMLVacio();
            return;
        }

        let html = '<div class="row">';
        
        this.datos.forEach(item => {
            html += this.crearElementoHTML(item);
        });
        
        html += '</div>';
        contenedor.innerHTML = html;

        // Configurar eventos de los elementos renderizados
        this.configurarEventosElementos();
    }

    /**
     * Crear HTML para un elemento individual
     */
    crearElementoHTML(item) {
        return `
            <div class="col-md-4 mb-3">
                <div class="card elemento-card" data-id="${item.id}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">${item.titulo || 'Sin título'}</h6>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary btn-editar" data-id="${item.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-eliminar" data-id="${item.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text">${item.descripcion || 'Sin descripción'}</p>
                        <small class="text-muted">
                            Creado: ${this.formatearFecha(item.fecha_creacion)}
                        </small>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Obtener HTML cuando no hay datos
     */
    obtenerHTMLVacio() {
        return `
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h3 class="text-muted">No hay datos disponibles</h3>
                <p class="text-muted">Haz clic en "Nuevo" para agregar el primer elemento.</p>
                <button class="btn btn-primary" onclick="window.plantillaPage.abrirModalNuevo()">
                    <i class="fas fa-plus"></i>
                    Crear Nuevo
                </button>
            </div>
        `;
    }

    /**
     * Configurar eventos de los elementos renderizados
     */
    configurarEventosElementos() {
        // Botones de editar
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                this.editarElemento(id);
            });
        });

        // Botones de eliminar
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                this.confirmarEliminacion(id);
            });
        });

        // Cards clickeables
        document.querySelectorAll('.elemento-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (!e.target.closest('.btn-group')) {
                    const id = card.dataset.id;
                    this.seleccionarElemento(id);
                }
            });
        });
    }

    /**
     * Abrir modal para crear nuevo elemento
     */
    abrirModalNuevo() {
        this.elementoSeleccionado = null;
        this.mostrarModal('Crear Nuevo Elemento', this.obtenerFormularioHTML());
    }

    /**
     * Editar elemento existente
     */
    editarElemento(id) {
        const elemento = this.datos.find(item => item.id == id);
        if (!elemento) {
            this.mostrarNotificacion('Elemento no encontrado', 'error');
            return;
        }

        this.elementoSeleccionado = elemento;
        this.mostrarModal('Editar Elemento', this.obtenerFormularioHTML(elemento));
    }

    /**
     * Obtener HTML del formulario
     */
    obtenerFormularioHTML(elemento = null) {
        return `
            <form id="formulario-elemento">
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título:</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" 
                           value="${elemento ? elemento.titulo || '' : ''}" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" 
                              rows="3">${elemento ? elemento.descripcion || '' : ''}</textarea>
                </div>
                <div class="mb-3">
                    <label for="categoria" class="form-label">Categoría:</label>
                    <select class="form-control" id="categoria" name="categoria">
                        <option value="">Seleccionar categoría</option>
                        <option value="importante" ${elemento && elemento.categoria === 'importante' ? 'selected' : ''}>Importante</option>
                        <option value="normal" ${elemento && elemento.categoria === 'normal' ? 'selected' : ''}>Normal</option>
                        <option value="baja" ${elemento && elemento.categoria === 'baja' ? 'selected' : ''}>Baja</option>
                    </select>
                </div>
            </form>
        `;
    }

    /**
     * Confirmar acción del modal
     */
    async confirmarAccion() {
        const formulario = document.getElementById('formulario-elemento');
        if (!formulario) return;

        const formData = new FormData(formulario);
        const datos = Object.fromEntries(formData.entries());

        try {
            this.mostrarCargando(true);
            
            let response;
            if (this.elementoSeleccionado) {
                // Actualizar elemento existente
                response = await this.api.actualizarElemento(this.elementoSeleccionado.id, datos);
            } else {
                // Crear nuevo elemento
                response = await this.api.crearElemento(datos);
            }

            if (response.success) {
                this.mostrarNotificacion(
                    this.elementoSeleccionado ? 'Elemento actualizado' : 'Elemento creado',
                    'success'
                );
                this.cerrarModal();
                await this.cargarDatos();
                this.renderizarContenido();
            } else {
                throw new Error(response.message || 'Error al guardar');
            }
        } catch (error) {
            console.error('Error al guardar:', error);
            this.mostrarNotificacion('Error al guardar: ' + error.message, 'error');
        } finally {
            this.mostrarCargando(false);
        }
    }

    /**
     * Confirmar eliminación
     */
    confirmarEliminacion(id) {
        const elemento = this.datos.find(item => item.id == id);
        if (!elemento) return;

        this.mostrarModal(
            'Confirmar Eliminación',
            `<p>¿Estás seguro de que deseas eliminar "${elemento.titulo}"?</p>
             <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>`,
            async () => await this.eliminarElemento(id)
        );
    }

    /**
     * Eliminar elemento
     */
    async eliminarElemento(id) {
        try {
            this.mostrarCargando(true);
            const response = await this.api.eliminarElemento(id);

            if (response.success) {
                this.mostrarNotificacion('Elemento eliminado', 'success');
                this.cerrarModal();
                await this.cargarDatos();
                this.renderizarContenido();
            } else {
                throw new Error(response.message || 'Error al eliminar');
            }
        } catch (error) {
            console.error('Error al eliminar:', error);
            this.mostrarNotificacion('Error al eliminar: ' + error.message, 'error');
        } finally {
            this.mostrarCargando(false);
        }
    }

    /**
     * Buscar elementos
     */
    async buscar() {
        const input = document.getElementById('buscar-input');
        if (!input) return;

        const termino = input.value.trim();
        this.filtros.busqueda = termino;
        
        await this.cargarDatos();
        this.renderizarContenido();
    }

    /**
     * Actualizar datos
     */
    async actualizarDatos() {
        await this.cargarDatos();
        this.renderizarContenido();
    }

    /**
     * Guardar cambios
     */
    async guardarCambios() {
        this.mostrarNotificacion('Función de guardado personalizada', 'info');
    }

    /**
     * Seleccionar elemento
     */
    seleccionarElemento(id) {
        // Remover selección anterior
        document.querySelectorAll('.elemento-card').forEach(card => {
            card.classList.remove('selected');
        });

        // Agregar selección actual
        const card = document.querySelector(`[data-id="${id}"]`);
        if (card) {
            card.classList.add('selected');
            this.elementoSeleccionado = this.datos.find(item => item.id == id);
        }
    }

    /**
     * Manejar eventos de teclado
     */
    manejarTeclado(e) {
        // Ctrl+N: Nuevo elemento
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            this.abrirModalNuevo();
        }
        
        // Ctrl+S: Guardar
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            this.guardarCambios();
        }
        
        // F5: Actualizar
        if (e.key === 'F5') {
            e.preventDefault();
            this.actualizarDatos();
        }
        
        // Escape: Cerrar modal
        if (e.key === 'Escape') {
            this.cerrarModal();
        }
    }

    /**
     * Mostrar modal
     */
    mostrarModal(titulo, contenido, onConfirm = null) {
        const modal = document.getElementById('modal-generico');
        const tituloEl = document.getElementById('modal-titulo');
        const cuerpoEl = document.getElementById('modal-cuerpo');
        const confirmarBtn = document.getElementById('modal-confirmar');

        if (modal && tituloEl && cuerpoEl) {
            tituloEl.textContent = titulo;
            cuerpoEl.innerHTML = contenido;
            
            // Configurar botón de confirmar
            if (onConfirm) {
                confirmarBtn.onclick = onConfirm;
            }
            
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        }
    }

    /**
     * Cerrar modal
     */
    cerrarModal() {
        const modal = document.getElementById('modal-generico');
        if (modal) {
            const bootstrapModal = bootstrap.Modal.getInstance(modal);
            if (bootstrapModal) {
                bootstrapModal.hide();
            }
        }
    }

    /**
     * Mostrar indicador de carga
     */
    mostrarCargando(mostrar) {
        const contenedor = document.getElementById('contenido-dinamico');
        if (!contenedor) return;

        if (mostrar) {
            contenedor.style.opacity = '0.5';
            contenedor.style.pointerEvents = 'none';
        } else {
            contenedor.style.opacity = '1';
            contenedor.style.pointerEvents = 'auto';
        }
    }

    /**
     * Mostrar notificación
     */
    mostrarNotificacion(mensaje, tipo = 'info') {
        // Crear elemento de notificación
        const notificacion = document.createElement('div');
        notificacion.className = `alert alert-${tipo === 'error' ? 'danger' : tipo} alert-dismissible fade show position-fixed`;
        notificacion.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        
        notificacion.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notificacion);

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (notificacion.parentNode) {
                notificacion.remove();
            }
        }, 5000);
    }

    /**
     * Formatear fecha
     */
    formatearFecha(fecha) {
        if (!fecha) return 'N/A';
        
        try {
            return new Date(fecha).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (error) {
            return fecha;
        }
    }
}

// Exportar para uso global
window.PlantillaPage = PlantillaPage;