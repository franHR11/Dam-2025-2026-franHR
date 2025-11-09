document.addEventListener('DOMContentLoaded', function () {
    const logoutButton = document.getElementById('logout-btn'); // Asume que tu botón de logout tiene este ID

    if (logoutButton) {
        logoutButton.addEventListener('click', function (e) {
            e.preventDefault();

            const apiUrl = window.CONFIG?.API_BASE_URL || '/api/';
            const logoutUrl = apiUrl + 'logout.php'; // URL del endpoint de logout

            fetch(logoutUrl, {
                method: 'POST',
                credentials: 'include', // Importante para enviar cookies de sesión
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirigir al login si el logout fue exitoso
                    window.location.href = window.LOGIN_URL;
                } else {
                    // Manejar el caso en que el logout falla
                    console.error('Error al cerrar sesión:', data.message);
                    alert('No se pudo cerrar la sesión. Por favor, inténtalo de nuevo.');
                }
            })
            .catch(error => {
                console.error('Error en la petición de logout:', error);
                alert('Ocurrió un error de red. No se pudo cerrar la sesión.');
            });
        });
    }
});

// Clase corregida para gestión de módulos - Sistema independiente
class ModulosManager {
    constructor() {
        this.apiBase = "/modulos/api/gestion_modulos.php"; // URL corregida
        this.init();
    }

    init() {
        console.log("Gestor de módulos inicializado correctamente");
        this.setupEventListeners();
        this.cargarModulos();
    }

    setupEventListeners() {
        // Configurar listeners para los botones del dashboard
        document.addEventListener('DOMContentLoaded', () => {
            this.configurarBotonesModulos();
        });
    }

    configurarBotonesModulos() {
        const botones = document.querySelectorAll('button[onclick*="ModulosDashboard"]');
        botones.forEach(boton => {
            const onclickOriginal = boton.getAttribute('onclick');
            if (onclickOriginal) {
                // Extraer el nombre de la función y los parámetros
                const match = onclickOriginal.match(/(\w+)\((.*?)\)/);
                if (match) {
                    const [, funcion, parametros] = match;
                    boton.removeAttribute('onclick');
                    boton.addEventListener('click', (e) => {
                        e.preventDefault();
                        // Llamar a la función corregida
                        if (typeof this[funcion] === 'function') {
                            this[funcion](...parametros.split(',').map(p => p.trim()));
                        }
                    });
                }
            }
        });
    }

    async cargarModulos() {
        try {
            const response = await fetch(this.apiBase);
            const data = await response.json();

            if (data.success) {
                this.modulos = data.data || [];
                this.actualizarInterfaz();
                this.actualizarEstadisticas();
            } else {
                throw new Error(data.message || 'Error al cargar módulos');
            }
        } catch (error) {
            console.error("Error cargando módulos:", error);
            this.mostrarNotificacion("Error cargando módulos: " + error.message, "error");
        }
    }

    // Mostrar modal de confirmación
    mostrarConfirmacion(titulo, mensaje, onConfirmar) {
        let modal = document.querySelector('.modal-confirmacion');

        if (!modal) {
            modal = document.createElement('div');
            modal.className = 'modal-confirmacion';
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
            document.body.appendChild(modal);
        }

        const btnConfirmar = modal.querySelector('#btn-confirmar');
        btnConfirmar.addEventListener('click', () => {
            modal.remove();
            if (onConfirmar) onConfirmar();
        });

        modal.style.display = 'flex';
    }

    // Mostrar notificación
    mostrarNotificacion(mensaje, tipo = 'success') {
        let notificacion = document.querySelector('.notificacion');

        if (!notificacion) {
            notificacion = document.createElement('div');
            notificacion.className = `notificacion notificacion-${tipo}`;
            document.body.appendChild(notificacion);
        }

        notificacion.innerHTML = `
            <i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${mensaje}</span>
        `;

        notificacion.classList.add('mostrar');

        setTimeout(() => {
            notificacion.classList.remove('mostrar');
        }, 5000);
    }

    // Gestionar módulo con API
    async gestionarModulo(moduloId, accion) {
        try {
            const mensajes = {
                'instalar': {
                    titulo: 'Instalar Módulo',
                    mensaje: '¿Estás seguro de que quieres instalar este módulo?',
                    metodo: 'POST'
                },
                'activar': {
                    titulo: 'Activar Módulo',
                    mensaje: '¿Estás seguro de que quieres activar este módulo? El módulo aparecerá en el menú principal.',
                    metodo: 'PUT'
                },
                'desactivar': {
                    titulo: 'Desactivar Módulo',
                    mensaje: '¿Estás seguro de que quieres desactivar este módulo? El módulo dejará de estar disponible para los usuarios.',
                    metodo: 'PUT'
                },
                'desinstalar': {
                    titulo: 'Desinstalar Módulo',
                    mensaje: '¿Estás seguro de que quieres desinstalar este módulo? Esta acción eliminará toda la configuración del módulo y no se puede deshacer.',
                    metodo: 'DELETE'
                }
            };

            const config = mensajes[accion];
            if (!config) {
                throw new Error('Acción no válida');
            }

            // Mostrar confirmación
            this.mostrarConfirmacion(config.titulo, config.mensaje, async () => {
                await this.ejecutarAccion(moduloId, accion, config.metodo);
            });

        } catch (error) {
            console.error('Error en gestión de módulo:', error);
            this.mostrarNotificacion('Error: ' + error.message, 'error');
        }
    }

    // Ejecutar acción en la API
    async ejecutarAccion(moduloId, accion, metodo) {
        try {
            const opciones = {
                method: metodo,
                headers: {
                    'Content-Type': 'application/json',
                },
            };

            // Para PUT y DELETE, necesitamos diferentes configuraciones
            if (metodo === 'PUT') {
                opciones.body = JSON.stringify({
                    id: moduloId,
                    accion: accion
                });
            } else if (metodo === 'POST') {
                opciones.body = JSON.stringify({
                    id: moduloId
                });
            }

            // Construir URL
            const url = metodo === 'DELETE'
                ? `${this.apiBase}?id=${moduloId}`
                : this.apiBase;

            // Mostrar indicador de carga
            this.mostrarNotificacion('Procesando...', 'info');

            const response = await fetch(url, opciones);

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Error en la operación');
            }

            // Mostrar éxito
            this.mostrarNotificacion(data.message, 'success');

            // Recargar módulos y actualizar interfaz
            await this.cargarModulos();

            // Actualizar menú principal para reflejar activación/desactivación/desinstalación
            if (window.menuManager && typeof window.menuManager.actualizarMenu === 'function') {
                window.menuManager.actualizarMenu();
            }

        } catch (error) {
            console.error('Error ejecutando acción:', error);
            this.mostrarNotificacion('Error: ' + error.message, 'error');
        }
    }

    // Actualizar interfaz del dashboard
    actualizarInterfaz() {
        const contenedorModulos = document.querySelector('#modules-container');
        if (!contenedorModulos) return;

        let html = '';
        this.modulos.forEach((modulo, index) => {
            const estadoClase = this.getEstadoClase(modulo.estado);
            const iconoEstado = this.getIconoEstado(modulo.estado);

            html += `
                <div class="module-card ${estadoClase}" data-modulo-id="${modulo.id}" data-estado="${modulo.estado}">
                    <div class="module-header">
                        <div class="module-icon">
                            <i class="${modulo.icono}"></i>
                        </div>
                        <div class="module-info">
                            <h3>${modulo.nombre}</h3>
                            <span class="module-version">v${modulo.version}</span>
                            <span class="module-tech-name">${modulo.nombre_tecnico}</span>
                        </div>
                        <div class="module-status">
                            <i class="${iconoEstado}"></i>
                            <span>${this.getEstadoTexto(modulo.estado)}</span>
                        </div>
                    </div>

                    <div class="module-body">
                        <p class="module-description">${modulo.descripcion || 'Sin descripción disponible'}</p>
                    </div>

                    <div class="module-actions">
                        ${this.generarBotonesAccion(modulo)}
                    </div>
                </div>
            `;
        });

        contenedorModulos.innerHTML = html;

        // Configurar eventos para los nuevos botones
        setTimeout(() => {
            this.configurarBotonesModulos();
        }, 100);
    }

    // Generar botones de acción según el estado
    generarBotonesAccion(modulo) {
        let botones = '';

        switch (modulo.estado) {
            case 'no-instalado':
                botones = `
                    <button class="btn btn-primary" onclick="modulosManager.gestionarModulo(${modulo.id}, 'instalar')">
                        <i class="fas fa-download"></i> Instalar
                    </button>
                `;
                break;
            case 'inactivo':
                botones = `
                    <button class="btn btn-success" onclick="modulosManager.gestionarModulo(${modulo.id}, 'activar')">
                        <i class="fas fa-play"></i> Activar
                    </button>
                    <button class="btn btn-danger" onclick="modulosManager.gestionarModulo(${modulo.id}, 'desinstalar')">
                        <i class="fas fa-trash"></i> Desinstalar
                    </button>
                `;
                break;
            case 'activo':
                if (modulo.nombre_tecnico !== 'dashboard') {
                    botones = `
                        <button class="btn btn-warning" onclick="modulosManager.gestionarModulo(${modulo.id}, 'desactivar')">
                            <i class="fas fa-pause"></i> Desactivar
                        </button>
                        <button class="btn btn-danger" onclick="modulosManager.gestionarModulo(${modulo.id}, 'desinstalar')">
                            <i class="fas fa-trash"></i> Desinstalar
                        </button>
                    `;
                } else {
                    botones = `
                        <button class="btn btn-warning" disabled>
                            <i class="fas fa-lock"></i> Módulo del Sistema
                        </button>
                    `;
                }
                break;
        }

        return botones;
    }

    // Actualizar estadísticas del dashboard
    actualizarEstadisticas() {
        const stats = {
            total: this.modulos.length,
            activos: this.modulos.filter(m => m.estado === 'activo').length,
            inactivos: this.modulos.filter(m => m.estado === 'inactivo').length,
            noInstalados: this.modulos.filter(m => m.estado === 'no-instalado').length
        };

        // Actualizar elementos del DOM si existen
        const elementosStats = document.querySelectorAll('.stat-number');
        if (elementosStats.length >= 4) {
            elementosStats[0].textContent = stats.total;
            elementosStats[1].textContent = stats.activos;
            elementosStats[2].textContent = stats.inactivos;
            elementosStats[3].textContent = stats.noInstalados;
        }
    }

    // Métodos auxiliares
    getEstadoClase(estado) {
        const clases = {
            'activo': 'estado-activo',
            'inactivo': 'estado-inactivo',
            'no-instalado': 'estado-no-instalado'
        };
        return clases[estado] || 'estado-desconocido';
    }

    getIconoEstado(estado) {
        const iconos = {
            'activo': 'fas fa-check-circle',
            'inactivo': 'fas fa-pause-circle',
            'no-instalado': 'fas fa-times-circle'
        };
        return iconos[estado] || 'fas fa-question-circle';
    }

    getEstadoTexto(estado) {
        const textos = {
            'activo': 'Activo',
            'inactivo': 'Inactivo',
            'no-instalado': 'No Instalado'
        };
        return textos[estado] || 'Desconocido';
    }
}

// Inicializar cuando el DOM esté listo
let modulosManager;

document.addEventListener('DOMContentLoaded', () => {
    modulosManager = new ModulosManager();

    // Hacer disponible globalmente
    window.modulosManager = modulosManager;

    // Para compatibilidad con onclick en HTML
    window.gestionarModulo = (moduloId, accion) => {
        if (window.modulosManager) {
            window.modulosManager.gestionarModulo(moduloId, accion);
        } else {
            console.error("modulosManager no está disponible");
            alert("Error: El sistema de módulos no está inicializado");
        }
    };

    console.log("Sistema de gestión de módulos cargado correctamente");
});
