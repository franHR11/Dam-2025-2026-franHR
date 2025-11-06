# Documentación Técnica - UI Library

## Índice
1. [Introducción](#introducción)
2. [Instalación](#instalación)
3. [Componentes](#componentes)
4. [API Reference](#api-reference)
5. [Ejemplos](#ejemplos)
6. [Personalización](#personalización)
7. [Contribución](#contribución)

---

## Introducción

UI Library es una librería de componentes de interfaz de usuario desarrollada con JavaScript vanilla, HTML5 y CSS3. Diseñada para ofrecer soluciones reutilizables y configurables sin dependencias externas.

### Características Principales
- ✅ **Sin dependencias**: Cero dependencias externas
- ✅ **Modular**: Cada componente es independiente
- ✅ **Responsive**: Diseño adaptable a todos los dispositivos
- ✅ **Accesible**: Cumple con estándares WCAG 2.1
- ✅ **Personalizable**: Sistema de temas y configuración flexible
- ✅ **Ligera**: Tamaño optimizado para producción

---

## Instalación

### CDN
```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ui-library@latest/dist/ui-library.css">
<script src="https://cdn.jsdelivr.net/npm/ui-library@latest/dist/ui-library.js"></script>
```

### Local
```html
<link rel="stylesheet" href="assets/css/ui-library.css">
<script src="assets/js/ui-library.js"></script>
```

### Módulos ES6
```javascript
import { TableComponent, ChartComponent, FormComponent, ReportComponent } from 'ui-library';
```

---

## Componentes

### TableComponent

Componente para la visualización de datos tabulares con funcionalidades avanzadas.

#### Configuración Básica
```javascript
const table = new TableComponent({
    container: '#my-table',
    data: [],
    columns: []
});
```

#### Opciones de Configuración
| Opción | Tipo | Default | Descripción |
|--------|------|---------|-------------|
| `data` | Array | `[]` | Array de objetos con los datos |
| `columns` | Array | `[]` | Configuración de columnas |
| `pagination` | Boolean | `true` | Habilitar paginación |
| `pageSize` | Number | `10` | Elementos por página |
| `sortable` | Boolean | `true` | Permitir ordenamiento |
| `searchable` | Boolean | `true` | Habilitar búsqueda |
| `selectable` | Boolean | `false` | Permitir selección de filas |
| `exportable` | Boolean | `true` | Habilitar exportación CSV |

#### Configuración de Columnas
```javascript
columns: [
    {
        key: 'id',           // Clave del dato
        label: 'ID',         // Etiqueta del encabezado
        type: 'number',      // Tipo de dato (text, number, date)
        sortable: true,      // Permitir ordenamiento
        formatter: (value) => { // Función de formateo
            return value ? `#${value}` : '-';
        }
    }
]
```

### ChartComponent

Componente para visualización de datos gráficos usando Canvas API nativa.

#### Tipos de Gráficos
- `line`: Gráfico de líneas
- `bar`: Gráfico de barras
- `pie`: Gráfico circular

#### Configuración
```javascript
const chart = new ChartComponent({
    container: '#my-chart',
    type: 'line',
    data: {
        labels: ['Ene', 'Feb', 'Mar'],
        datasets: [{
            label: 'Ventas',
            data: [100, 200, 150]
        }]
    },
    options: {
        title: 'Ventas Mensuales',
        colors: ['#3498db', '#e74c3c', '#2ecc71'],
        responsive: true
    }
});
```

### FormComponent

Componente para la creación dinámica de formularios con validación.

#### Tipos de Campos
- `text`: Campo de texto
- `email`: Campo de correo
- `number`: Campo numérico
- `password`: Campo de contraseña
- `tel`: Campo telefónico
- `date`: Campo de fecha
- `select`: Lista desplegable
- `textarea`: Área de texto
- `checkbox`: Casilla de verificación
- `radio`: Botones de opción

#### Configuración
```javascript
const form = new FormComponent({
    container: '#my-form',
    fields: [
        {
            name: 'nombre',
            type: 'text',
            label: 'Nombre Completo',
            required: true,
            minLength: 3,
            maxLength: 50,
            placeholder: 'Ingrese su nombre',
            validation: (value) => {
                if (value.length < 3) {
                    return 'El nombre debe tener al menos 3 caracteres';
                }
                return true;
            }
        },
        {
            name: 'email',
            type: 'email',
            label: 'Correo Electrónico',
            required: true
        }
    ],
    onSubmit: (formData) => {
        console.log('Formulario enviado:', formData);
    }
});
```

### ReportComponent

Componente para generar informes complejos combinando múltiples elementos.

#### Tipos de Secciones
- `table`: Tabla de datos
- `chart`: Gráfica
- `summary`: Tarjetas de métricas
- `text`: Contenido de texto
- `custom`: Contenido personalizado

#### Configuración
```javascript
const report = new ReportComponent({
    container: '#my-report',
    title: 'Informe de Rendimiento',
    layout: 'vertical',
    sections: [
        {
            type: 'summary',
            title: 'Resumen',
            metrics: [
                {
                    label: 'Ventas Totales',
                    value: 125000,
                    format: 'currency',
                    change: 12.5
                }
            ]
        },
        {
            type: 'chart',
            chartType: 'line',
            title: 'Evolución de Ventas',
            data: chartData
        },
        {
            type: 'table',
            title: 'Top Productos',
            data: productsData,
            columns: productColumns
        }
    ]
});
```

---

## API Reference

### Métodos Comunes

Todos los componentes heredan de `BaseComponent` y comparten estos métodos:

#### Métodos de Instancia
```javascript
// Actualizar datos
component.updateData(newData);

// Destruir componente
component.destroy();

// Eventos
component.on('event', callback);
component.emit('event', data);

// Estados de carga
component.showLoading();
component.hideLoading();
```

### TableComponent

#### Métodos Específicos
```javascript
// Navegación de páginas
table.goToPage(2);
table.nextPage();
table.previousPage();

// Exportación
table.exportToCSV();

// Selección
table.toggleSelectAll(true);
table.getSelectedRows();
```

#### Eventos
- `selection`: Se emite cuando cambia la selección
- `pageChange`: Se emite al cambiar de página
- `sort`: Se emite al ordenar datos
- `filter`: Se emite al filtrar datos

### ChartComponent

#### Métodos Específicos
```javascript
// Actualizar gráfico
chart.updateData(newData);

// Redimensionar
chart.resize();

// Cambiar tipo
chart.setType('bar');
```

#### Eventos
- `dataUpdate`: Se emite al actualizar datos
- `resize`: Se emite al redimensionar

### FormComponent

#### Métodos Específicos
```javascript
// Validación
form.validateForm();
form.validateField(fieldName);

// Obtener/Establecer valores
form.getFieldValue(fieldName);
form.setFieldValue(fieldName, value);
form.getFormData();

// Resetear formulario
form.reset();
```

#### Eventos
- `submit`: Se emite al enviar el formulario
- `validationError`: Se emite en error de validación
- `fieldChange`: Se emite al cambiar un campo
- `reset`: Se emite al resetear el formulario

### ReportComponent

#### Métodos Específicos
```javascript
// Gestión de secciones
report.addSection(section);
report.removeSection(index);
report.updateSection(index, data);

// Exportación
report.print();
report.exportToPDF();
```

---

## Ejemplos

### Ejemplo 1: Dashboard Interactivo

```html
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="assets/css/ui-library.css">
</head>
<body>
    <div id="dashboard"></div>
    
    <script src="assets/js/ui-library.js"></script>
    <script>
        // Datos
        const salesData = {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
            datasets: [{
                label: 'Ventas 2024',
                data: [12000, 19000, 15000, 25000, 22000]
            }]
        };

        const usersData = [
            { id: 1, name: 'Juan', sales: 15, rating: 4.5 },
            { id: 2, name: 'María', sales: 22, rating: 4.8 },
            { id: 3, name: 'Carlos', sales: 18, rating: 4.2 }
        ];

        // Crear dashboard
        const dashboard = new ReportComponent({
            container: '#dashboard',
            title: 'Panel de Control',
            sections: [
                {
                    type: 'summary',
                    title: 'Métricas Clave',
                    metrics: [
                        { label: 'Ventas Totales', value: 93000, format: 'currency', change: 12.5 },
                        { label: 'Usuarios Activos', value: 1247, change: 8.3 },
                        { label: 'Conversión', value: 3.8, format: 'percentage', change: -2.1 }
                    ]
                },
                {
                    type: 'chart',
                    chartType: 'line',
                    title: 'Tendencia de Ventas',
                    data: salesData
                },
                {
                    type: 'table',
                    title: 'Top Vendedores',
                    data: usersData,
                    columns: [
                        { key: 'name', label: 'Nombre' },
                        { key: 'sales', label: 'Ventas', type: 'number' },
                        { key: 'rating', label: 'Rating', formatter: (val) => '⭐'.repeat(Math.floor(val)) }
                    ]
                }
            ]
        });
    </script>
</body>
</html>
```

### Ejemplo 2: Formulario de Contacto

```javascript
const contactForm = new FormComponent({
    container: '#contact-form',
    fields: [
        {
            name: 'subject',
            type: 'select',
            label: 'Asunto',
            required: true,
            options: [
                { value: '', label: 'Seleccione un asunto' },
                { value: 'support', label: 'Soporte Técnico' },
                { value: 'sales', label: 'Ventas' },
                { value: 'info', label: 'Información General' }
            ]
        },
        {
            name: 'priority',
            type: 'radio',
            label: 'Prioridad',
            required: true,
            options: [
                { value: 'low', label: 'Baja' },
                { value: 'medium', label: 'Media' },
                { value: 'high', label: 'Alta' }
            ]
        },
        {
            name: 'message',
            type: 'textarea',
            label: 'Mensaje',
            required: true,
            minLength: 10,
            maxLength: 1000,
            rows: 5
        }
    ],
    onSubmit: async (formData) => {
        try {
            const response = await fetch('/api/contact', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            
            if (response.ok) {
                alert('Mensaje enviado correctamente');
                this.reset();
            }
        } catch (error) {
            alert('Error al enviar el mensaje');
        }
    }
});
```

---

## Personalización

### Variables CSS

La librería utiliza variables CSS personalizables:

```css
:root {
    /* Colores */
    --primary-color: #3498db;
    --secondary-color: #2ecc71;
    --accent-color: #e74c3c;
    
    /* Espaciado */
    --spacing-sm: 0.5rem;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    
    /* Bordes */
    --border-radius: 8px;
    
    /* Sombras */
    --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
}
```

### Temas

#### Tema Oscuro
```css
[data-theme="dark"] {
    --background: #1a1a1a;
    --surface: #2d2d2d;
    --text-primary: #ffffff;
    --text-secondary: #b3b3b3;
    --border-color: #404040;
}
```

#### Tema Personalizado
```css
.custom-theme {
    --primary-color: #6366f1;
    --secondary-color: #8b5cf6;
    --accent-color: #ec4899;
    --border-radius: 12px;
}
```

### Extensiones

Puedes extender los componentes:

```javascript
class CustomTableComponent extends TableComponent {
    constructor(options) {
        super(options);
        this.customFeature = true;
    }
    
    render() {
        super.render();
        this.addCustomFeatures();
    }
    
    addCustomFeatures() {
        // Tu código personalizado
    }
}
```

---

## Contribución

### Guía de Desarrollo

1. **Clonar el repositorio**
```bash
git clone https://github.com/tu-usuario/ui-library.git
cd ui-library
```

2. **Instalar dependencias**
```bash
npm install
```

3. **Iniciar desarrollo**
```bash
npm run dev
```

4. **Ejecutar tests**
```bash
npm test
```

5. **Construir para producción**
```bash
npm run build
```

### Estándares de Código

- Usar ES6+ para todo el código JavaScript
- Seguir convenciones BEM para CSS
- Documentar todas las funciones y métodos
- Mantener compatibilidad con navegadores modernos
- Escribir tests para nuevas funcionalidades

### Estructura de Archivos

```
ui-library/
├── src/
│   ├── components/
│   │   ├── BaseComponent.js
│   │   ├── TableComponent.js
│   │   ├── ChartComponent.js
│   │   ├── FormComponent.js
│   │   └── ReportComponent.js
│   ├── styles/
│   │   ├── base.css
│   │   ├── components/
│   │   └── themes/
│   └── utils/
├── dist/
├── docs/
├── examples/
└── tests/
```

### Envío de Pull Requests

1. Crear una rama desde `develop`
2. Implementar la funcionalidad con tests
3. Actualizar documentación
4. Enviar PR con descripción detallada

---

## Licencia

MIT License - Ver archivo [LICENSE](../LICENSE) para detalles completos.

---

## Soporte

- **Issues**: [GitHub Issues](https://github.com/tu-usuario/ui-library/issues)
- **Discusiones**: [GitHub Discussions](https://github.com/tu-usuario/ui-library/discussions)
- **Email**: support@ui-library.com

---

## Changelog

### v1.0.0 (2024-12-01)
- ✨ Lanzamiento inicial
- 📊 TableComponent con paginación y ordenamiento
- 📈 ChartComponent con 3 tipos de gráficos
- 📝 FormComponent con validación avanzada
- 📋 ReportComponent para informes complejos
- 🎨 Sistema de temas personalizable
- 📱 Diseño responsive
- ♿ Accesibilidad WCAG 2.1

---

## Roadmap

### v1.1.0 (Próximo)
- [ ] Componente de Calendario
- [ ] Componente de Modal
- [ ] Componente de Tabs
- [ ] Mejoras en animaciones
- [ ] Soporte para国际化

### v1.2.0
- [ ] Componente de Drag & Drop
- [ ] Componente de File Upload
- [ ] Componente de Rich Text Editor
- [ ] Sistema de plugins

### v2.0.0
- [ ] Migración a TypeScript
- [ ] Componentes web nativos
- [ ] Mejora de performance
- [ ] API más intuitiva