# Pautas de diseño del aspecto de la interfaz de usuario

## 1. Introducción y contextualización

En este ejercicio he querido profundizar en las **pautas de diseño de interfaces**, un conjunto de reglas y recomendaciones que nos ayudan a crear aplicaciones no solo bonitas, sino funcionales y fáciles de usar.

Estas pautas son fundamentales porque nos permiten guiar al usuario a través de la interfaz de manera intuitiva, reduciendo su carga cognitiva. Se utilizan en cualquier desarrollo profesional para asegurar la coherencia, la accesibilidad y una buena experiencia de usuario (UX). Sin ellas, las aplicaciones suelen verse desordenadas y confusas.

## 2. Desarrollo técnico correcto y preciso

Para aplicar estos conocimientos, he diseñado una pantalla de **"Login" (Inicio de Sesión)**, ya que es un elemento común donde el diseño juega un papel crítico. He aplicado los siguientes principios técnicos:

1.  **Jerarquía Visual**: He utilizado diferentes tamaños de fuente y pesos (negrita para etiquetas principales, normal para texto) para indicar qué es lo más importante. El título "Bienvenido" destaca sobre el resto.
2.  **Consistencia**: He mantenido una paleta de colores coherente (tonos azules para acciones principales y grises para textos secundarios) y un espaciado uniforme (padding y margin consistentes).
3.  **Feedback (Retroalimentación)**: He implementado estados `:hover` y `:focus` en los campos de entrada y el botón. Cuando el usuario pasa el ratón por encima del botón, este cambia de color, y cuando hace clic en un input, el borde se ilumina. Esto "conversa" con el usuario, confirmándole que el sistema está respondiendo.
4.  **Espacio en blanco (Whitespace)**: No he saturado la interfaz. He dejado márgenes amplios alrededor del formulario para que "respire", lo que mejora la legibilidad y da una sensación de limpieza y modernidad.

## 3. Aplicación práctica con ejemplo claro

A continuación muestro el código que he desarrollado para este ejemplo de Login, donde se ven plasmadas estas pautas.

### Archivo HTML (`index.html`)
Estructura semántica y limpia.

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejemplo de Pautas de Diseño - Login</title>
    <link rel="stylesheet" href="style.css">
    <!-- Fuente Inter para tipografía moderna y limpia -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <header class="login-header">
            <div class="logo-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1>Bienvenido</h1>
            <p>Ingresa tus datos para acceder</p>
        </header>

        <form class="login-form">
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <div class="input-wrapper">
                    <input type="email" id="email" placeholder="ejemplo@correo.com" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-wrapper">
                    <input type="password" id="password" placeholder="••••••••" required>
                </div>
            </div>

            <div class="form-actions">
                <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="btn-primary">Iniciar Sesión</button>
        </form>

        <footer class="login-footer">
            <p>¿No tienes cuenta? <a href="#">Regístrate aquí</a></p>
        </footer>
    </div>
</body>
</html>
```

### Archivo CSS (`style.css`)
Aquí es donde se define la "personalidad" y las reglas visuales.

```css
/* Reset básico para eliminar estilos por defecto del navegador */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    /* Variables para asegurar consistencia en los colores */
    --primary-color: #2563eb;
    --primary-hover: #1d4ed8;
    --background-color: #f8fafc;
    --surface-color: #ffffff;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --border-color: #e2e8f0;
}

body {
    font-family: 'Inter', sans-serif; /* Tipografía limpia */
    background-color: var(--background-color);
    min-height: 100vh;
    display: flex; /* Centrado vertical y horizontal */
    justify-content: center;
    align-items: center;
}

.login-container {
    background-color: var(--surface-color);
    padding: 2.5rem; /* Espaciado interno generoso */
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); /* Sutil sombra para dar profundidad */
    width: 100%;
    max-width: 400px;
}

.login-header {
    text-align: center;
    margin-bottom: 2rem;
}

/* Estilos de tipografía para jerarquía */
.login-header h1 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
}

.login-header p {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.form-group {
    margin-bottom: 1.5rem; /* Espacio entre campos */
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary);
}

.input-wrapper input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    transition: all 0.2s ease; /* Transición suave para el feedback */
}

/* Feedback visual al usuario */
.input-wrapper input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.btn-primary {
    width: 100%;
    padding: 0.75rem;
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: 0.5rem;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.2s ease;
}

.btn-primary:hover {
    background-color: var(--primary-hover); /* Feedback al pasar el ratón */
}

.login-footer {
    margin-top: 1.5rem;
    text-align: center;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.login-footer a {
    color: var(--primary-color);
    text-decoration: none;
}
```

## 4. Cierre y conclusión

Realizar este ejercicio me ha servido para entender que el diseño no es solo "poner cosas bonitas". He aprendido que al aplicar pautas estrictas de espaciado, alineación y color, la interfaz se vuelve mucho más profesional y confiable para el usuario final.

En el futuro, aplicaré estos conceptos en cualquier proyecto web, ya que una buena interfaz puede ser la diferencia entre que un usuario utilice mi aplicación cómodamente o la abandone por frustración. Mantener la consistencia es clave.
