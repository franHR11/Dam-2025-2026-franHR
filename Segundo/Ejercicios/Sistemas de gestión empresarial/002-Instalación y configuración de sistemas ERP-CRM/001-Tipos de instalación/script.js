document.addEventListener('DOMContentLoaded', function() {
    // Obtener todos los botones de "Ver detalles"
    const botonesVerMas = document.querySelectorAll('.btn-ver-mas');
    const seccionDetalles = document.getElementById('detalles-seccion');
    const contenidoDetalles = document.getElementById('contenido-detalles');
    
    // Información detallada sobre cada tipo de instalación
    const detallesInstalacion = {
        local: {
            titulo: 'Instalación Local - Detalles',
            contenido: `
                <h4>Requisitos técnicos:</h4>
                <ul>
                    <li>Servidor con mínimo 16GB RAM y 500GB almacenamiento</li>
                    <li>Sistema operativo Windows Server o Linux</li>
                    <li>Base de datos MySQL o PostgreSQL</li>
                    <li>Conexión a internet estable</li>
                </ul>
                <h4>Ventajas para empresa pesquera:</h4>
                <ul>
                    <li>Control total sobre datos sensibles de clientes y proveedores</li>
                    <li>Acceso sin dependencia de conexión externa</li>
                    <li>Personalización completa según necesidades específicas</li>
                    <li>Integración con sistemas locales existentes</li>
                </ul>
                <h4>Desventajas:</h4>
                <ul>
                    <li>Coste inicial elevado (hardware + licencias)</li>
                    <li>Personal técnico especializado necesario</li>
                    <li>Tiempo de implementación más prolongado</li>
                </ul>
            `
        },
        nube: {
            titulo: 'Instalación en la Nube - Detalles',
            contenido: `
                <h4>Requisitos técnicos:</h4>
                <ul>
                    <li>Ordenadores con navegador web actualizado</li>
                    <li>Conexión a internet de calidad</li>
                    <li>Cuentas de usuario para cada empleado</li>
                </ul>
                <h4>Ventajas para empresa pesquera:</h4>
                <ul>
                    <li>Acceso desde barcos, oficinas o cualquier ubicación</li>
                    <li>Actualizaciones automáticas incluidas</li>
                    <li>Escalabilidad según crecimiento del negocio</li>
                    <li>Soporte técnico incluido en el servicio</li>
                </ul>
                <h4>Desventajas:</h4>
                <ul>
                    <li>Dependencia de conexión a internet</li>
                    <li>Menor control sobre los datos</li>
                    <li>Costes recurrentes de suscripción</li>
                    <li>Personalización limitada</li>
                </ul>
            `
        }
    };
    
    // Agregar evento click a cada botón
    botonesVerMas.forEach(boton => {
        boton.addEventListener('click', function() {
            const tipo = this.getAttribute('data-tipo');
            const detalles = detallesInstalacion[tipo];
            
            // Mostrar la sección de detalles
            seccionDetalles.style.display = 'block';
            
            // Actualizar el contenido con la información correspondiente
            contenidoDetalles.innerHTML = `
                <h4>${detalles.titulo}</h4>
                ${detalles.contenido}
            `;
            
            // Desplazarse a la sección de detalles
            seccionDetalles.scrollIntoView({ behavior: 'smooth' });
        });
    });
    
    // Agregar interactividad a las tarjetas de módulos
    const modulosCards = document.querySelectorAll('.modulo-card');
    modulosCards.forEach(card => {
        card.addEventListener('click', function() {
            const titulo = this.querySelector('h4').textContent;
            const descripcion = this.querySelector('p').textContent;
            
            // Crear una alerta personalizada simple
            const alerta = document.createElement('div');
            alerta.style.position = 'fixed';
            alerta.style.top = '50%';
            alerta.style.left = '50%';
            alerta.style.transform = 'translate(-50%, -50%)';
            alerta.style.backgroundColor = '#2c3e50';
            alerta.style.color = 'white';
            alerta.style.padding = '2rem';
            alerta.style.borderRadius = '8px';
            alerta.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.3)';
            alerta.style.zIndex = '1000';
            alerta.style.maxWidth = '400px';
            alerta.innerHTML = `
                <h3>${titulo}</h3>
                <p>${descripcion}</p>
                <p>Este módulo es fundamental para la gestión eficiente de los procesos en tu empresa pesquera.</p>
                <button id="cerrar-alerta" style="margin-top: 1rem; padding: 0.5rem 1rem; background-color: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer;">Cerrar</button>
            `;
            
            document.body.appendChild(alerta);
            
            // Agregar evento para cerrar la alerta
            document.getElementById('cerrar-alerta').addEventListener('click', function() {
                document.body.removeChild(alerta);
            });
        });
    });
});