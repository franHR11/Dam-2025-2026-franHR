// Configuración del frontend
// Cargar variables de entorno desde .env (simulado para JavaScript)
const config = {
    API_BASE_URL: '/api/'  // Valor por defecto - ruta relativa
};

// Intentar cargar desde .env si existe (requiere servidor o build tool)
fetch('/.env')
    .then(response => response.text())
    .then(text => {
        const lines = text.split('\n');
        lines.forEach(line => {
            const [key, value] = line.split('=');
            if (key && value) {
                config[key.trim()] = value.trim();
            }
        });
    })
    .catch(() => {
        // Si no se puede cargar .env, usar valores por defecto
        console.log('Usando configuración por defecto');
    });

// Exportar configuración
window.CONFIG = config;