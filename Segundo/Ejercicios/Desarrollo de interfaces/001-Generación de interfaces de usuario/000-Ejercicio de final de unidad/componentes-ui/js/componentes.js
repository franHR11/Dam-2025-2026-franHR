// Librería de componentes UI personalizados
// Autor: Fran
// Descripción: Funcionalidades para mis componentes de interfaz

// Clase para manejar los componentes
class ComponentesUI {
    constructor() {
        this.init();
    }

    // Inicializar todos los componentes
    init() {
        this.initBotones();
        this.initInputs();
        this.initModales();
        this.initAlertas();
    }

    // Inicializar botones con eventos
    initBotones() {
        document.querySelectorAll('.btn').forEach(boton => {
            boton.addEventListener('click', function(e) {
                // Efecto de ripple cuando se hace clic
                const ripple = document.createElement('span');
                ripple.classList.add('ripple');
                this.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    }

    // Validar inputs personalizados
    initInputs() {
        document.querySelectorAll('.input-control').forEach(input => {
            // Validación al perder el foco
            input.addEventListener('blur', function() {
                const grupo = this.closest('.input-grupo');
                const mensaje = grupo.querySelector('.input-mensaje');

                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.classList.add('error');
                    this.classList.remove('exito');
                    if (mensaje) {
                        mensaje.textContent = 'Este campo es obligatorio';
                        mensaje.classList.add('error');
                        mensaje.classList.remove('exito');
                    }
                } else if (this.value.trim()) {
                    this.classList.add('exito');
                    this.classList.remove('error');
                    if (mensaje) {
                        mensaje.textContent = 'Campo válido';
                        mensaje.classList.add('exito');
                        mensaje.classList.remove('error');
                    }
                }
            });

            // Limpiar validación al escribir
            input.addEventListener('input', function() {
                this.classList.remove('error', 'exito');
                const mensaje = this.closest('.input-grupo').querySelector('.input-mensaje');
                if (mensaje) {
                    mensaje.textContent = '';
                    mensaje.classList.remove('error', 'exito');
                }
            });
        });
    }

    // Manejar modales
    initModales() {
        // Abrir modal
        document.querySelectorAll('[data-modal-abrir]').forEach(boton => {
            boton.addEventListener('click', function() {
                const modalId = this.getAttribute('data-modal-abrir');
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            });
        });

        // Cerrar modal
        document.querySelectorAll('[data-modal-cerrar]').forEach(boton => {
            boton.addEventListener('click', function() {
                const modal = this.closest('.modal-overlay');
                if (modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        });

        // Cerrar modal al hacer clic fuera
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        });
    }

    // Inicializar alertas con auto-ocultar
    initAlertas() {
        document.querySelectorAll('.alerta[data-auto-ocultar]').forEach(alerta => {
            const tiempo = parseInt(alerta.getAttribute('data-auto-ocultar')) || 5000;
            setTimeout(() => {
                alerta.style.opacity = '0';
                setTimeout(() => {
                    alerta.remove();
                }, 300);
            }, tiempo);
        });
    }

    // Método estático para crear alertas dinámicamente
    static crearAlerta(mensaje, tipo = 'info', autoOcultar = 5000) {
        const alerta = document.createElement('div');
        alerta.className = `alerta alerta-${tipo}`;
        if (autoOcultar) {
            alerta.setAttribute('data-auto-ocultar', autoOcultar);
        }
        alerta.textContent = mensaje;

        // Insertar al principio del body
        document.body.insertBefore(alerta, document.body.firstChild);

        // Inicializar auto-ocultar
        if (autoOcultar) {
            setTimeout(() => {
                alerta.style.opacity = '0';
                alerta.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    alerta.remove();
                }, 300);
            }, autoOcultar);
        }

        return alerta;
    }

    // Método estático para mostrar confirmación
    static confirmar(mensaje, callback) {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.style.display = 'flex';
        modal.innerHTML = `
            <div class="modal">
                <div class="modal-header">
                    <h3 class="modal-title">Confirmar acción</h3>
                    <button class="modal-close" data-modal-cerrar>&times;</button>
                </div>
                <div class="modal-body">
                    <p>${mensaje}</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secundario" data-modal-cerrar>Cancelar</button>
                    <button class="btn btn-error" id="confirmar-accion">Confirmar</button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';

        // Manejar botón de confirmar
        modal.querySelector('#confirmar-accion').addEventListener('click', function() {
            callback();
            modal.remove();
            document.body.style.overflow = 'auto';
        });

        // Manejar cierre
        modal.querySelectorAll('[data-modal-cerrar]').forEach(boton => {
            boton.addEventListener('click', function() {
                modal.remove();
                document.body.style.overflow = 'auto';
            });
        });

        // Cerrar al hacer clic fuera
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
                document.body.style.overflow = 'auto';
            }
        });
    }
}

// Estilo para el efecto ripple en botones
const estiloRipple = document.createElement('style');
estiloRipple.textContent = `
    .btn {
        position: relative;
        overflow: hidden;
    }
    .ripple {
        position: absolute;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s ease-out;
        pointer-events: none;
    }
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(estiloRipple);

// Inicializar componentes cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    new ComponentesUI();
});

// Exportar la clase para uso global
window.ComponentesUI = ComponentesUI;
