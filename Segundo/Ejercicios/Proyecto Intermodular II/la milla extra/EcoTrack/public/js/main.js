/**
 * Main.js - Funcionalidades principales de EcoTrack
 */

// Variables globales
const EcoTrack = {
    init: function() {
        this.initEventListeners();
        this.initTheme();
        this.initAnimations();
    },

    // Inicializar event listeners
    initEventListeners: function() {
        // Navegación móvil
        const navbarToggle = document.getElementById('navbarToggle');
        const navbarMenu = document.querySelector('.navbar__menu');

        if (navbarToggle && navbarMenu) {
            navbarToggle.addEventListener('click', function() {
                navbarMenu.classList.toggle('navbar__menu--active');
                navbarToggle.classList.toggle('navbar__toggle--active');
            });
        }

        // Dropdowns
        const dropdowns = document.querySelectorAll('.navbar__dropdown');
        dropdowns.forEach(dropdown => {
            const link = dropdown.querySelector('.navbar__link');
            const menu = dropdown.querySelector('.dropdown__menu');

            if (link && menu) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    dropdown.classList.toggle('navbar__dropdown--active');
                });
            }
        });

        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.navbar__dropdown')) {
                dropdowns.forEach(dropdown => {
                    dropdown.classList.remove('navbar__dropdown--active');
                });
            }
        });

        // Smooth scroll para enlaces ancla
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animación de números
        this.animateNumbers();

        // Lazy loading de imágenes
        this.lazyLoadImages();
    },

    // Inicializar tema (claro/oscuro)
    initTheme: function() {
        const savedTheme = localStorage.getItem('ecotrack-theme') || 'light';
        document.body.setAttribute('data-theme', savedTheme);

        // Botón de cambio de tema (si existe)
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', this.toggleTheme.bind(this));
        }
    },

    // Cambiar tema
    toggleTheme: function() {
        const currentTheme = document.body.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';

        document.body.setAttribute('data-theme', newTheme);
        localStorage.setItem('ecotrack-theme', newTheme);

        // Actualizar icono
        const themeIcon = document.getElementById('themeIcon');
        if (themeIcon) {
            themeIcon.className = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
        }
    },

    // Animar números contadores
    animateNumbers: function() {
        const counters = document.querySelectorAll('[data-counter]');
        const speed = 200;

        const countUp = (counter) => {
            const target = +counter.getAttribute('data-counter');
            const count = +counter.innerText;
            const increment = target / speed;

            if (count < target) {
                counter.innerText = Math.ceil(count + increment);
                setTimeout(() => countUp(counter), 1);
            } else {
                counter.innerText = target;
            }
        };

        // Observer para animación cuando sea visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                    entry.target.classList.add('animated');
                    countUp(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => observer.observe(counter));
    },

    // Lazy loading de imágenes
    lazyLoadImages: function() {
        const images = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.getAttribute('data-src');
                    img.onload = () => img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    },

    // Inicializar animaciones
    initAnimations: function() {
        // Animar elementos al hacer scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, { threshold: 0.1 });

        // Observar elementos con clase animate
        document.querySelectorAll('.animate').forEach(el => {
            observer.observe(el);
        });
    },

    // Utilidades
    utils: {
        // Formatear número
        formatNumber: function(num, decimals = 0) {
            return parseFloat(num).toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },

        // Formatear fecha
        formatDate: function(date, format = 'short') {
            const d = new Date(date);

            if (format === 'short') {
                return d.toLocaleDateString('es-ES');
            } else if (format === 'long') {
                return d.toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            } else if (format === 'time') {
                return d.toLocaleString('es-ES');
            }
        },

        // Copiar al portapapeles
        copyToClipboard: function(text) {
            if (navigator.clipboard) {
                return navigator.clipboard.writeText(text);
            } else {
                // Fallback para navegadores antiguos
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
            }
        },

        // Mostrar notificación
        showNotification: function(message, type = 'info', duration = 5000) {
            const notification = document.createElement('div');
            notification.className = `notification notification--${type}`;
            notification.innerHTML = `
                <div class="notification__content">
                    <i class="fas ${this.getNotificationIcon(type)}"></i>
                    <span>${message}</span>
                    <button class="notification__close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            document.body.appendChild(notification);

            // Animar entrada
            setTimeout(() => notification.classList.add('notification--show'), 10);

            // Cerrar automáticamente
            const timer = setTimeout(() => {
                this.closeNotification(notification);
            }, duration);

            // Cerrar manualmente
            const closeBtn = notification.querySelector('.notification__close');
            closeBtn.addEventListener('click', () => {
                clearTimeout(timer);
                this.closeNotification(notification);
            });
        },

        // Obtener icono de notificación
        getNotificationIcon: function(type) {
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            return icons[type] || icons.info;
        },

        // Cerrar notificación
        closeNotification: function(notification) {
            notification.classList.remove('notification--show');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        },

        // Validar email
        isValidEmail: function(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },

        // Validar número
        isNumber: function(value) {
            return !isNaN(parseFloat(value)) && isFinite(value);
        },

        // Debounce
        debounce: function(func, wait, immediate) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    timeout = null;
                    if (!immediate) func(...args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func(...args);
            };
        }
    },

    // API helpers
    api: {
        // GET request
        get: async function(url) {
            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                return await response.json();
            } catch (error) {
                console.error('GET Error:', error);
                throw error;
            }
        },

        // POST request
        post: async function(url, data) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                });
                return await response.json();
            } catch (error) {
                console.error('POST Error:', error);
                throw error;
            }
        },

        // Form data POST
        postForm: async function(url, formData) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                return await response.json();
            } catch (error) {
                console.error('Form POST Error:', error);
                throw error;
            }
        }
    }
};

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    EcoTrack.init();

    // Exponer utilidades globalmente
    window.EcoTrack = EcoTrack;
    window.utils = EcoTrack.utils;
});

// Exportar para módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EcoTrack;
}
