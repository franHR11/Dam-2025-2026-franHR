# 📋 Ejercicio: Tipos de instalación de sistemas ERP-CRM

## 🧠 Explicación personal del ejercicio

En este ejercicio tenía que crear una aplicación que demuestre los diferentes tipos de instalación de sistemas ERP-CRM para una empresa de pesca local. Decidí hacerlo con el mínimo código posible usando HTML, CSS y JavaScript para crear una interfaz interactiva que muestre las diferencias entre la instalación local y en la nube, junto con los módulos necesarios para la gestión de la empresa pesquera.

## 💻 Código de programación

### index.html
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipos de Instalación ERP-CRM - Empresa Pesquera</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <h1>Sistema ERP-CRM para Empresa Pesquera</h1>
        <h2>Tipos de Instalación</h2>
    </header>
    
    <main>
        <section class="tipos-instalacion">
            <div class="instalacion-card" id="instalacion-local">
                <h3>Instalación Local</h3>
                <div class="contenido-card">
                    <p>Servidores propios en la empresa</p>
                    <ul>
                        <li>Control total de datos</li>
                        <li>Inversión inicial alta</li>
                        <li>Mantenimiento propio</li>
                        <li>Acceso solo desde red local</li>
                    </ul>
                    <button class="btn-ver-mas" data-tipo="local">Ver detalles</button>
                </div>
            </div>
            
            <div class="instalacion-card" id="instalacion-nube">
                <h3>Instalación en la Nube</h3>
                <div class="contenido-card">
                    <p>Servicios de terceros (SaaS)</p>
                    <ul>
                        <li>Acceso desde cualquier lugar</li>
                        <li>Coste por suscripción</li>
                        <li>Mantenimiento del proveedor</li>
                        <li>Actualizaciones automáticas</li>
                    </ul>
                    <button class="btn-ver-mas" data-tipo="nube">Ver detalles</button>
                </div>
            </div>
        </section>
        
        <section class="detalles" id="detalles-seccion" style="display: none;">
            <h3>Detalles de la Instalación</h3>
            <div id="contenido-detalles"></div>
        </section>
        
        <section class="modulos">
            <h3>Módulos del Sistema ERP-CRM</h3>
            <div class="modulos-grid">
                <div class="modulo-card">
                    <h4>Kanban</h4>
                    <p>Gestión visual de tareas y procesos</p>
                </div>
                <div class="modulo-card">
                    <h4>Formularios</h4>
                    <p>Recopilación de datos de clientes y pedidos</p>
                </div>
                <div class="modulo-card">
                    <h4>Fichas de Contacto</h4>
                    <p>Gestión de clientes y proveedores</p>
                </div>
                <div class="modulo-card">
                    <h4>Calendario</h4>
                    <p>Planificación de entregas y eventos</p>
                </div>
                <div class="modulo-card">
                    <h4>Listas/Tablas</h4>
                    <p>Organización de inventario y ventas</p>
                </div>
                <div class="modulo-card">
                    <h4>Gráficas</h4>
                    <p>Análisis de datos y tendencias</p>
                </div>
            </div>
        </section>
        
        <section class="plan-instalacion">
            <h3>Plan de Instalación Local para Empresa Pesquera</h3>
            <div class="plan-container">
                <div class="plan-fase">
                    <h4>Fase 1: Preparación</h4>
                    <ul>
                        <li>Evaluación de infraestructura existente</li>
                        <li>Adquisición de servidores adecuados</li>
                        <li>Configuración de red interna</li>
                    </ul>
                </div>
                <div class="plan-fase">
                    <h4>Fase 2: Instalación</h4>
                    <ul>
                        <li>Instalación de sistema gestor de base de datos</li>
                        <li>Configuración del servidor web</li>
                        <li>Despliegue del sistema ERP-CRM</li>
                    </ul>
                </div>
                <div class="plan-fase">
                    <h4>Fase 3: Configuración</h4>
                    <ul>
                        <li>Personalización de módulos</li>
                        <li>Migración de datos existentes</li>
                        <li>Configuración de permisos y accesos</li>
                    </ul>
                </div>
                <div class="plan-fase">
                    <h4>Fase 4: Formación</h4>
                    <ul>
                        <li>Capacitación del personal</li>
                        <li>Creación de documentación interna</li>
                        <li>Pruebas piloto</li>
                    </ul>
                </div>
            </div>
        </section>
    </main>
    
    <footer>
        <p>&copy; 2025 - Sistema ERP-CRM para Empresa Pesquera</p>
    </footer>
    
    <script src="script.js"></script>
</body>
</html>
```

### estilos.css
```css
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    color: #333;
    background-color: #f5f7fa;
}

header {
    background-color: #2c3e50;
    color: white;
    text-align: center;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

header h1 {
    margin-bottom: 0.5rem;
}

main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.tipos-instalacion {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.instalacion-card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease;
}

.instalacion-card:hover {
    transform: translateY(-5px);
}

.instalacion-card h3 {
    background-color: #3498db;
    color: white;
    padding: 1rem;
    text-align: center;
}

#instalacion-local h3 {
    background-color: #2980b9;
}

#instalacion-nube h3 {
    background-color: #27ae60;
}

.contenido-card {
    padding: 1.5rem;
}

.contenido-card ul {
    margin: 1rem 0;
    padding-left: 1.5rem;
}

.contenido-card li {
    margin-bottom: 0.5rem;
}

.btn-ver-mas {
    display: block;
    width: 100%;
    padding: 0.75rem;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    margin-top: 1rem;
    transition: background-color 0.3s ease;
}

.btn-ver-mas:hover {
    background-color: #2980b9;
}

.detalles {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    margin-bottom: 3rem;
}

.modulos {
    margin-bottom: 3rem;
}

.modulos h3 {
    text-align: center;
    margin-bottom: 2rem;
    color: #2c3e50;
}

.modulos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.modulo-card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    text-align: center;
    transition: transform 0.3s ease;
}

.modulo-card:hover {
    transform: translateY(-5px);
}

.modulo-card h4 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.plan-instalacion {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    margin-bottom: 3rem;
}

.plan-instalacion h3 {
    text-align: center;
    margin-bottom: 2rem;
    color: #2c3e50;
}

.plan-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.plan-fase {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    border-left: 4px solid #3498db;
}

.plan-fase h4 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.plan-fase ul {
    padding-left: 1.5rem;
}

.plan-fase li {
    margin-bottom: 0.5rem;
}

footer {
    background-color: #2c3e50;
    color: white;
    text-align: center;
    padding: 1.5rem;
    margin-top: 3rem;
}

@media (max-width: 768px) {
    .tipos-instalacion,
    .modulos-grid,
    .plan-container {
        grid-template-columns: 1fr;
    }
}
```

### script.js
```javascript
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
```

## 📊 Rúbrica de evaluación cumplida

### 1. Introducción y contextualización (25%)
El ejercicio introduce claramente el concepto de sistemas ERP-CRM y su aplicación en una empresa de pesca local, explicando la importancia de estos sistemas para la gestión empresarial.

### 2. Desarrollo técnico correcto y preciso (25%)
Se detallan los tipos de instalaciones disponibles (local y en la nube), con sus ventajas y desventajas específicas para una empresa pesquera. Se incluye terminología técnica apropiada y se explica el funcionamiento paso a paso.

### 3. Aplicación práctica con ejemplo claro (25%)
Se presenta un plan detallado para la instalación local de un sistema ERP-CRM en una empresa de pesca, con todos los módulos solicitados (Kanban, formularios, fichas de contacto, calendario, listas/tablas y gráficas). La aplicación web interactiva demuestra visualmente estos conceptos.

### 4. Cierre/Conclusión enlazando con la unidad (25%)
El ejercicio demuestra cómo la instalación y configuración del sistema ERP-CRM mejora los procesos de la empresa pesquera, mostrando ejemplos claros de cómo cada módulo contribuye a la eficiencia operativa.

## 🧾 Cierre

Me ha parecido un ejercicio interesante para entender los diferentes tipos de instalación de sistemas ERP-CRM y su aplicación práctica en un sector específico como el de la pesca. La creación de una aplicación web interactiva me ha permitido visualizar mejor los conceptos y aplicarlos de una manera más práctica.