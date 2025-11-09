/**
 * Validator.js - Validación de formularios para EcoTrack
 */

class FormValidator {
    constructor(form) {
        this.form = form;
        this.rules = {};
        this.errors = {};
        this.isValid = false;
    }

    // Regla de validación
    addRule(fieldName, rule) {
        if (!this.rules[fieldName]) {
            this.rules[fieldName] = [];
        }
        this.rules[fieldName].push(rule);
        return this;
    }

    // Validar todo el formulario
    validate() {
        this.errors = {};
        this.isValid = true;

        Object.keys(this.rules).forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                const value = this.getFieldValue(field);
                this.validateField(fieldName, value, field);
            }
        });

        return this.isValid;
    }

    // Validar campo específico
    validateField(fieldName, value, field) {
        const rules = this.rules[fieldName] || [];
        let fieldErrors = [];

        rules.forEach(rule => {
            if (!this.applyRule(value, rule)) {
                fieldErrors.push(rule.message);
            }
        });

        if (fieldErrors.length > 0) {
            this.errors[fieldName] = fieldErrors;
            this.isValid = false;
            this.showFieldError(field, fieldErrors);
        } else {
            this.clearFieldError(field);
        }

        return fieldErrors.length === 0;
    }

    // Aplicar regla específica
    applyRule(value, rule) {
        switch (rule.type) {
            case 'required':
                return this.validateRequired(value);
            case 'email':
                return this.validateEmail(value);
            case 'minLength':
                return this.validateMinLength(value, rule.value);
            case 'maxLength':
                return this.validateMaxLength(value, rule.value);
            case 'min':
                return this.validateMin(value, rule.value);
            case 'max':
                return this.validateMax(value, rule.value);
            case 'pattern':
                return this.validatePattern(value, rule.value);
            case 'equals':
                return this.validateEquals(value, rule.value);
            case 'phone':
                return this.validatePhone(value);
            case 'url':
                return this.validateUrl(value);
            case 'numeric':
                return this.validateNumeric(value);
            case 'alpha':
                return this.validateAlpha(value);
            case 'alphaNumeric':
                return this.validateAlphaNumeric(value);
            case 'strongPassword':
                return this.validateStrongPassword(value);
            case 'ecoTransport':
                return this.validateEcoTransport(value);
            case 'dietType':
                return this.validateDietType(value);
            case 'energyUsage':
                return this.validateEnergyUsage(value);
            default:
                return true;
        }
    }

    // Métodos de validación específicos
    validateRequired(value) {
        if (typeof value === 'string') {
            return value.trim().length > 0;
        }
        return value !== null && value !== undefined && value !== '';
    }

    validateEmail(value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(value);
    }

    validateMinLength(value, minLength) {
        return value && value.length >= minLength;
    }

    validateMaxLength(value, maxLength) {
        return !value || value.length <= maxLength;
    }

    validateMin(value, minValue) {
        const numValue = parseFloat(value);
        return !isNaN(numValue) && numValue >= minValue;
    }

    validateMax(value, maxValue) {
        const numValue = parseFloat(value);
        return !isNaN(numValue) && numValue <= maxValue;
    }

    validatePattern(value, pattern) {
        const regex = new RegExp(pattern);
        return regex.test(value);
    }

    validateEquals(value, fieldName) {
        const compareField = this.form.querySelector(`[name="${fieldName}"]`);
        return compareField && value === compareField.value;
    }

    validatePhone(value) {
        const phoneRegex = /^[+]?[\d\s\-\(\)]+$/;
        return phoneRegex.test(value) && value.replace(/\D/g, '').length >= 9;
    }

    validateUrl(value) {
        try {
            new URL(value);
            return true;
        } catch {
            return false;
        }
    }

    validateNumeric(value) {
        return !isNaN(value) && value.trim() !== '';
    }

    validateAlpha(value) {
        const alphaRegex = /^[a-zA-Z\s]+$/;
        return alphaRegex.test(value);
    }

    validateAlphaNumeric(value) {
        const alphaNumericRegex = /^[a-zA-Z0-9\s]+$/;
        return alphaNumericRegex.test(value);
    }

    validateStrongPassword(value) {
        const minLength = value.length >= 8;
        const hasUpperCase = /[A-Z]/.test(value);
        const hasLowerCase = /[a-z]/.test(value);
        const hasNumbers = /\d/.test(value);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(value);

        return minLength && hasUpperCase && hasLowerCase && hasNumbers && hasSpecialChar;
    }

    validateEcoTransport(value) {
        const validOptions = ['coche', 'moto', 'transporte público', 'bicicleta', 'a pie'];
        return validOptions.includes(value);
    }

    validateDietType(value) {
        const validOptions = ['vegetariana', 'mixta', 'carnívora'];
        return validOptions.includes(value);
    }

    validateEnergyUsage(value) {
        const numValue = parseFloat(value);
        return !isNaN(numValue) && numValue >= 0 && numValue <= 5000;
    }

    // Obtener valor de campo
    getFieldValue(field) {
        if (field.type === 'checkbox' || field.type === 'radio') {
            return field.checked;
        }
        return field.value;
    }

    // Mostrar error de campo
    showFieldError(field, errors) {
        const container = field.closest('.form-group, .form-control');
        if (!container) return;

        // Remover clases previas
        container.classList.remove('has-success');
        container.classList.add('has-error');

        // Crear o actualizar mensaje de error
        let errorElement = container.querySelector('.form-error');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'form-error';
            container.appendChild(errorElement);
        }

        errorElement.textContent = errors[0];
        errorElement.style.display = 'block';

        // Añadir aria-invalid para accesibilidad
        field.setAttribute('aria-invalid', 'true');
        field.setAttribute('aria-describedby', errorElement.id);
    }

    // Limpiar error de campo
    clearFieldError(field) {
        const container = field.closest('.form-group, .form-control');
        if (!container) return;

        // Remover clases de error
        container.classList.remove('has-error');
        container.classList.add('has-success');

        // Ocultar mensaje de error
        const errorElement = container.querySelector('.form-error');
        if (errorElement) {
            errorElement.style.display = 'none';
        }

        // Remover atributos de error
        field.removeAttribute('aria-invalid');
        field.removeAttribute('aria-describedby');

        // Remover clase de éxito después de 2 segundos
        setTimeout(() => {
            container.classList.remove('has-success');
        }, 2000);
    }

    // Obtener todos los errores
    getErrors() {
        return this.errors;
    }

    // Obtener errores de un campo específico
    getFieldErrors(fieldName) {
        return this.errors[fieldName] || [];
    }

    // Limpiar todas las validaciones
    clear() {
        this.errors = {};
        this.isValid = false;

        // Limpiar estados visuales
        this.form.querySelectorAll('.has-error, .has-success').forEach(container => {
            container.classList.remove('has-error', 'has-success');
        });

        // Ocultar mensajes de error
        this.form.querySelectorAll('.form-error').forEach(error => {
            error.style.display = 'none';
        });

        // Limpiar atributos de accesibilidad
        this.form.querySelectorAll('[aria-invalid]').forEach(field => {
            field.removeAttribute('aria-invalid');
            field.removeAttribute('aria-describedby');
        });
    }

    // Validación en tiempo real
    enableRealTimeValidation() {
        Object.keys(this.rules).forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.addEventListener('blur', () => {
                    const value = this.getFieldValue(field);
                    this.validateField(fieldName, value, field);
                });

                field.addEventListener('input', () => {
                    if (field.classList.contains('has-error')) {
                        const value = this.getFieldValue(field);
                        this.validateField(fieldName, value, field);
                    }
                });
            }
        });
    }
}

// Funciones de utilidad para validación
const ValidationRules = {
    // Regla requerida
    required(message = 'Este campo es obligatorio') {
        return {
            type: 'required',
            message
        };
    },

    // Validación de email
    email(message = 'Email inválido') {
        return {
            type: 'email',
            message
        };
    },

    // Longitud mínima
    minLength(min, message = null) {
        return {
            type: 'minLength',
            value: min,
            message: message || `Debe tener al menos ${min} caracteres`
        };
    },

    // Longitud máxima
    maxLength(max, message = null) {
        return {
            type: 'maxLength',
            value: max,
            message: message || `No puede exceder ${max} caracteres`
        };
    },

    // Valor mínimo
    min(min, message = null) {
        return {
            type: 'min',
            value: min,
            message: message || `El valor mínimo es ${min}`
        };
    },

    // Valor máximo
    max(max, message = null) {
        return {
            type: 'max',
            value: max,
            message: message || `El valor máximo es ${max}`
        };
    },

    // Patrón regex
    pattern(pattern, message = 'Formato inválido') {
        return {
            type: 'pattern',
            value: pattern,
            message
        };
    },

    // Igual a otro campo
    equals(fieldName, message = null) {
        return {
            type: 'equals',
            value: fieldName,
            message: message || `Debe ser igual al campo ${fieldName}`
        };
    },

    // Teléfono
    phone(message = 'Teléfono inválido') {
        return {
            type: 'phone',
            message
        };
    },

    // URL
    url(message = 'URL inválida') {
        return {
            type: 'url',
            message
        };
    },

    // Numérico
    numeric(message = 'Debe ser un número válido') {
        return {
            type: 'numeric',
            message
        };
    },

    // Solo letras
    alpha(message = 'Solo se permiten letras') {
        return {
            type: 'alpha',
            message
        };
    },

    // Alfanumérico
    alphaNumeric(message = 'Solo se permiten letras y números') {
        return {
            type: 'alphaNumeric',
            message
        };
    },

    // Contraseña fuerte
    strongPassword(message = 'La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas, números y caracteres especiales') {
        return {
            type: 'strongPassword',
            message
        };
    },

    // Transporte ecológico
    ecoTransport(message = 'Seleccione una opción válida de transporte') {
        return {
            type: 'ecoTransport',
            message
        };
    },

    // Tipo de dieta
    dietType(message = 'Seleccione un tipo de dieta válido') {
        return {
            type: 'dietType',
            message
        };
    },

    // Consumo energético
    energyUsage(message = 'El consumo debe estar entre 0 y 5000 kWh') {
        return {
            type: 'energyUsage',
            message
        };
    }
};

// Inicialización automática
document.addEventListener('DOMContentLoaded', function() {
    // Encontrar formularios con data-validate
    document.querySelectorAll('form[data-validate]').forEach(form => {
        const validator = new FormValidator(form);

        // Agregar reglas desde atributos data
        form.querySelectorAll('[data-rules]').forEach(field => {
            const rules = field.dataset.rules;
            const fieldName = field.name;

            if (rules && fieldName) {
                try {
                    const ruleConfigs = JSON.parse(rules);
                    ruleConfigs.forEach(ruleConfig => {
                        if (ValidationRules[ruleConfig.type]) {
                            const rule = ValidationRules[ruleConfig.type](...ruleConfig.args);
                            validator.addRule(fieldName, rule);
                        }
                    });
                } catch (error) {
                    console.warn('Error parsing validation rules:', error);
                }
            }
        });

        // Evento de submit
        form.addEventListener('submit', function(e) {
            if (!validator.validate()) {
                e.preventDefault();

                // Mostrar notificación si está disponible
                if (window.utils && window.utils.showNotification) {
                    window.utils.showNotification('Por favor, corrige los errores en el formulario', 'error');
                }

                // Enfocar primer campo con error
                const firstError = form.querySelector('.has-error input, .has-error select, .has-error textarea');
                if (firstError) {
                    firstError.focus();
                }
            }
        });

        // Habilitar validación en tiempo real
        validator.enableRealTimeValidation();

        // Exponer validador
        form.validator = validator;
    });
});

// Exponer globalmente
window.FormValidator = FormValidator;
window.ValidationRules = ValidationRules;
