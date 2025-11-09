/**
 * API.js - Manejo de llamadas AJAX para EcoTrack
 */

class EcoAPI {
    constructor() {
        this.baseURL = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '/');
        this.csrfToken = this.getCSRFToken();
    }

    // Obtener token CSRF si existe
    getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    }

    // Configuración de headers por defecto
    getDefaultHeaders() {
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        };

        if (this.csrfToken) {
            headers['X-CSRF-Token'] = this.csrfToken;
        }

        return headers;
    }

    // Manejo de errores
    handleError(error, customMessage = null) {
        console.error('API Error:', error);

        let message = customMessage || 'Ha ocurrido un error inesperado';

        if (error.response) {
            // Error de respuesta HTTP
            switch (error.response.status) {
                case 400:
                    message = 'Solicitud inválida';
                    break;
                case 401:
                    message = 'No autorizado. Por favor inicia sesión';
                    break;
                case 403:
                    message = 'Acceso denegado';
                    break;
                case 404:
                    message = 'Recurso no encontrado';
                    break;
                case 422:
                    message = 'Datos inválidos';
                    break;
                case 500:
                    message = 'Error del servidor';
                    break;
                default:
                    message = `Error ${error.response.status}`;
            }

            // Extraer mensaje de error del body si existe
            if (error.response.data && error.response.data.message) {
                message = error.response.data.message;
            }
        } else if (error.request) {
            // Error de red
            message = 'Error de conexión. Verifica tu internet';
        }

        // Mostrar notificación si está disponible utils
        if (window.utils && window.utils.showNotification) {
            window.utils.showNotification(message, 'error');
        } else {
            alert(message);
        }

        return Promise.reject(error);
    }

    // GET request
    async get(endpoint, params = {}) {
        try {
            const url = new URL(endpoint, this.baseURL);
            Object.keys(params).forEach(key => {
                if (params[key] !== null && params[key] !== undefined) {
                    url.searchParams.append(key, params[key]);
                }
            });

            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: this.getDefaultHeaders()
            });

            if (!response.ok) {
                throw {
                    response: {
                        status: response.status,
                        data: await response.json().catch(() => ({}))
                    }
                };
            }

            return await response.json();
        } catch (error) {
            return this.handleError(error);
        }
    }

    // POST request con JSON
    async post(endpoint, data = {}) {
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    ...this.getDefaultHeaders(),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw {
                    response: {
                        status: response.status,
                        data: await response.json().catch(() => ({}))
                    }
                };
            }

            return await response.json();
        } catch (error) {
            return this.handleError(error);
        }
    }

    // POST request con FormData
    async postForm(endpoint, formData) {
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: this.getDefaultHeaders(),
                body: formData
            });

            if (!response.ok) {
                throw {
                    response: {
                        status: response.status,
                        data: await response.json().catch(() => ({}))
                    }
                };
            }

            return await response.json();
        } catch (error) {
            return this.handleError(error);
        }
    }

    // PUT request
    async put(endpoint, data = {}) {
        try {
            const response = await fetch(endpoint, {
                method: 'PUT',
                headers: {
                    ...this.getDefaultHeaders(),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw {
                    response: {
                        status: response.status,
                        data: await response.json().catch(() => ({}))
                    }
                };
            }

            return await response.json();
        } catch (error) {
            return this.handleError(error);
        }
    }

    // DELETE request
    async delete(endpoint) {
        try {
            const response = await fetch(endpoint, {
                method: 'DELETE',
                headers: this.getDefaultHeaders()
            });

            if (!response.ok) {
                throw {
                    response: {
                        status: response.status,
                        data: await response.json().catch(() => ({}))
                    }
                };
            }

            return await response.json();
        } catch (error) {
            return this.handleError(error);
        }
    }

    // Upload de archivos
    async upload(endpoint, file, additionalData = {}) {
        const formData = new FormData();
        formData.append('file', file);

        Object.keys(additionalData).forEach(key => {
            formData.append(key, additionalData[key]);
        });

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: this.getDefaultHeaders(),
                body: formData
            });

            if (!response.ok) {
                throw {
                    response: {
                        status: response.status,
                        data: await response.json().catch(() => ({}))
                    }
                };
            }

            return await response.json();
        } catch (error) {
            return this.handleError(error);
        }
    }
}

// API específicas para EcoTrack
class EcoTrackAPI extends EcoAPI {
    constructor() {
        super();
        this.endpoints = {
            // Usuarios
            login: 'index.php?action=login',
            register: 'index.php?action=register',
            logout: 'index.php?action=logout',
            profile: 'index.php?page=profile',

            // Hábitos
            habits: 'index.php?page=habits',
            createHabit: 'index.php?page=habit_form',
            deleteHabit: (id) => `index.php?page=habits&action=delete&id=${id}`,

            // Eco scores
            scores: 'index.php?page=scores',
            calculateCO2: 'index.php?page=habit_form&action=calculate',

            // Dashboard
            dashboard: 'index.php?page=dashboard',
            stats: 'index.php?page=dashboard&action=stats',

            // Logros
            achievements: 'index.php?page=achievements',

            // Export
            export: 'index.php?action=export',

            // Newsletter
            newsletter: 'index.php?page=newsletter'
        };
    }

    // Autenticación
    async login(credentials) {
        return this.post(this.endpoints.login, credentials);
    }

    async register(userData) {
        return this.post(this.endpoints.register, userData);
    }

    async logout() {
        return this.get(this.endpoints.logout);
    }

    // Hábitos
    async getHabits(userId) {
        return this.get(this.endpoints.habits, { user_id: userId });
    }

    async createHabit(habitData) {
        return this.postForm(this.endpoints.createHabit, habitData);
    }

    async deleteHabit(id) {
        return this.delete(this.endpoints.deleteHabit(id));
    }

    // Cálculo de CO2
    async calculateCO2(data) {
        return this.post(this.endpoints.calculateCO2, data);
    }

    // Dashboard
    async getDashboardStats() {
        return this.get(this.endpoints.stats);
    }

    // Logros
    async getAchievements() {
        return this.get(this.endpoints.achievements);
    }

    // Exportar datos
    async exportData() {
        window.location.href = this.endpoints.export;
    }

    // Newsletter
    async subscribeToNewsletter(email) {
        return this.post(this.endpoints.newsletter, { email });
    }
}

// Instancia global de la API
const api = new EcoTrackAPI();

// Exponer globalmente
window.api = api;
window.EcoTrackAPI = EcoTrackAPI;

// Event listeners para formularios AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Intercepter formularios con data-ajax
    document.querySelectorAll('form[data-ajax]').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Mostrar estado de carga
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

            try {
                const formData = new FormData(this);
                const endpoint = this.getAttribute('action') || window.location.href;

                const response = await api.postForm(endpoint, formData);

                // Manejar respuesta exitosa
                if (response.success || response.status === 'success') {
                    if (window.utils && window.utils.showNotification) {
                        window.utils.showNotification(response.message || 'Operación exitosa', 'success');
                    }

                    // Redireccionar si hay URL de redirección
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                }

            } catch (error) {
                // El error ya se maneja en handleError
                console.error('Form submission error:', error);
            } finally {
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    });
});
