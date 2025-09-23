// HeaderSupAdmin.js - Funcionalidad del header estilo Odoo
class HeaderSupAdmin {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupNotificationSystem();
        console.log('HeaderSupAdmin inicializado');
    }

    bindEvents() {
        // Toggle del dropdown del usuario
        const userMenuBtn = document.getElementById('user-menu-btn');
        const userDropdown = document.getElementById('user-dropdown');

        if (userMenuBtn && userDropdown) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleUserDropdown();
            });

            // Cerrar dropdown al hacer click fuera
            document.addEventListener('click', (e) => {
                if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                    this.closeUserDropdown();
                }
            });
        }

        // Botón de menú de aplicaciones
        const appsMenuBtn = document.getElementById('apps-menu-btn');
        if (appsMenuBtn) {
            appsMenuBtn.addEventListener('click', () => {
                this.handleAppsMenu();
            });
        }

        // Botón de notificaciones
        const notificationsBtn = document.getElementById('notifications-btn');
        const notificationsDropdown = document.getElementById('notifications-dropdown');
        if (notificationsBtn) {
            notificationsBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.handleNotifications();
            });
        }
        // Cerrar notificaciones al hacer click fuera
        if (notificationsBtn && notificationsDropdown) {
            document.addEventListener('click', (e) => {
                if (!notificationsBtn.contains(e.target) && !notificationsDropdown.contains(e.target)) {
                    this.closeNotificationsDropdown();
                }
            });
        }

        // Botón de chat
        const chatBtn = document.getElementById('chat-btn');
        if (chatBtn) {
            chatBtn.addEventListener('click', () => {
                this.handleChat();
            });
        }

        // Botón de logout
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleLogout();
            });
        }
    }

    toggleUserDropdown() {
        const dropdown = document.getElementById('user-dropdown');
        if (dropdown) {
            dropdown.classList.toggle('show');
        }
    }

    closeUserDropdown() {
        const dropdown = document.getElementById('user-dropdown');
        if (dropdown) {
            dropdown.classList.remove('show');
        }
    }

    handleAppsMenu() {
        // Simular la apertura del menú de aplicaciones
        this.showToast('Abriendo menú de aplicaciones...', 'info');

        // Aquí podrías implementar la lógica para mostrar el menú de apps
        // Por ejemplo, emitir un evento personalizado o mostrar un modal
        console.log('Menú de aplicaciones clickeado');

        // Ejemplo de cómo podrías integrar con un sistema de navegación
        if (typeof this.onAppsMenuClick === 'function') {
            this.onAppsMenuClick();
        }
    }

    handleNotifications() {
        // Toggle del dropdown de notificaciones
        this.toggleNotificationsDropdown();
    }

    toggleNotificationsDropdown() {
        const dropdown = document.getElementById('notifications-dropdown');
        if (dropdown) {
            dropdown.classList.toggle('show');
        }
    }

    closeNotificationsDropdown() {
        const dropdown = document.getElementById('notifications-dropdown');
        if (dropdown) {
            dropdown.classList.remove('show');
        }
    }

    handleChat() {
        // Simular la apertura del chat
        this.showToast('Abriendo chat...', 'info');

        // Aquí podrías implementar la lógica para abrir el chat
        console.log('Chat clickeado');
    }

    handleLogout() {
        console.log('Cerrando sesión...');
        
        // Primero eliminamos cualquier cookie o almacenamiento local que pueda estar manteniendo la sesión
        document.cookie.split(";").forEach(function(c) {
            document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
        });
        
        // Limpiamos localStorage por si acaso
        localStorage.clear();
        sessionStorage.clear();
        
        fetch(window.LOGOUT_URL, {
            method: 'POST',
            credentials: 'include', // Para enviar cookies de sesión
            cache: 'no-store' // Evitar cacheo
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta de logout:', data);
            
            // Pequeña pausa antes de redireccionar para asegurar que todo se procese
            setTimeout(() => {
                // Usar una redirección directa con URL completa
                window.location.href = window.LOGIN_URL;
            }, 100);
        })
        .catch(error => {
            console.error('Error en la petición de logout:', error);
            this.showToast('Error de red al cerrar sesión', 'error');
            
            // Redireccionar incluso si hay error
            setTimeout(() => {
                window.location.href = window.LOGIN_URL;
            }, 100);
        });
    }

    updateNotificationCount(change = 0) {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            let currentCount = parseInt(badge.textContent) || 0;
            currentCount = Math.max(0, currentCount + change);

            if (currentCount === 0) {
                badge.style.display = 'none';
            } else {
                badge.style.display = 'flex';
                badge.textContent = currentCount;
            }
        }
    }

    setupNotificationSystem() {
        // Simular notificaciones en tiempo real
        setInterval(() => {
            // Simular una nueva notificación cada 30 segundos (solo para demo)
            if (Math.random() < 0.3) { // 30% de probabilidad
                this.addNotification('Nueva notificación del sistema');
            }
        }, 30000);
    }

    addNotification(message, type = 'info') {
        this.updateNotificationCount(1);

        // Mostrar toast notification
        this.showToast(message, type);

        // Aquí podrías agregar la notificación a un panel de notificaciones
        console.log(`Nueva notificación: ${message}`);
    }

    showToast(message, type = 'info') {
        // Crear elemento toast
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;

        // Estilos del toast
        Object.assign(toast.style, {
            position: 'fixed',
            top: '70px',
            right: '20px',
            background: type === 'success' ? '#28a745' :
                type === 'error' ? '#dc3545' :
                    type === 'warning' ? '#ffc107' : '#17a2b8',
            color: 'white',
            padding: '12px 20px',
            borderRadius: '6px',
            boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
            zIndex: '10000',
            transform: 'translateX(100%)',
            transition: 'transform 0.3s ease',
            maxWidth: '300px',
            wordWrap: 'break-word'
        });

        document.body.appendChild(toast);

        // Animar entrada
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);

        // Remover después de 4 segundos
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 4000);
    }

    // Métodos públicos para integración con otros componentes
    setAppsMenuHandler(handler) {
        this.onAppsMenuClick = handler;
    }

    setUserName(name) {
        const userNameElement = document.querySelector('.user-name');
        if (userNameElement) {
            userNameElement.textContent = name;
        }
    }

    setNotificationCount(count) {
        this.updateNotificationCount(count - (parseInt(document.querySelector('.notification-badge')?.textContent) || 0));
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.headerSupAdmin = new HeaderSupAdmin();
});

// Exportar para uso en módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = HeaderSupAdmin;
}