// Configuración del frontend
// Cargar variables de entorno desde .env (simulado para JavaScript)
const config = {
  API_BASE_URL: "/api/", // Valor por defecto - ruta relativa
  BASE_URL: "", // URL base para las llamadas API (relativa a la raíz del proyecto)
};

// Intentar cargar desde .env si existe (requiere servidor o build tool)
fetch("/.env")
  .then((response) => response.text())
  .then((text) => {
    const lines = text.split("\n");
    lines.forEach((line) => {
      const [key, value] = line.split("=");
      if (key && value) {
        config[key.trim()] = value.trim();
      }
    });
  })
  .catch(() => {
    // Si no se puede cargar .env, usar valores por defecto
    console.log("Usando configuración por defecto");
  });

// Exportar configuración y también BASE_URL directamente
window.CONFIG = config;
window.BASE_URL = config.BASE_URL;
