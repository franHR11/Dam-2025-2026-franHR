# 📋 PLANTILLA MODULAR - SISTEMA ERP

## 🎯 Descripción

Esta plantilla proporciona una base sólida y reutilizable para crear nuevas páginas en el sistema ERP. Incluye una estructura modular con PHP, JavaScript y CSS que facilita el desarrollo rápido y consistente de nuevas funcionalidades.

## 📁 Estructura de Archivos

```
plantilla/
├── plantilla.php     # Estructura HTML base con Bootstrap
├── plantilla.js      # Lógica JavaScript modular
├── plantilla.css     # Estilos CSS personalizados
└── README.md         # Esta documentación
```

## 🚀 Características

### ✅ Funcionalidades Incluidas

- **Header responsivo** con información del usuario
- **Toolbar configurable** con botones de acción
- **Área de contenido dinámico** para mostrar datos
- **Modal genérico** para formularios y confirmaciones
- **Sistema de notificaciones** integrado
- **Búsqueda en tiempo real** con filtros
- **Diseño responsive** para móviles y tablets
- **Estilos modernos** con variables CSS
- **Modo oscuro** automático (opcional)

### 🛠️ Componentes Técnicos

- **ApiService**: Clase para comunicación con el backend
- **PlantillaPage**: Clase principal para gestión de la página
- **Event Listeners**: Sistema de eventos modular
- **Loading States**: Indicadores de carga automáticos
- **Error Handling**: Manejo de errores centralizado

## 📖 Cómo Usar la Plantilla

### 1. Copiar la Carpeta
```bash
cp -r plantilla/ nueva-pagina/
```

### 2. Renombrar Archivos
```bash
mv plantilla.php nueva-pagina.php
mv plantilla.js nueva-pagina.js
mv plantilla.css nueva-pagina.css
```

### 3. Personalizar el PHP

```php
// Cambiar el título de la página
$pageTitle = "Nueva Página";
$pageIcon = "fas fa-new-icon";

// Personalizar botones del toolbar
<button class="btn btn-primary" id="nuevo-btn">
    <i class="fas fa-plus"></i> Nuevo
</button>
```

### 4. Adaptar el JavaScript

```javascript
// Cambiar el nombre de la clase
class NuevaPaginaPage {
    constructor() {
        this.apiService = new ApiService();
        this.init();
    }
    
    // Personalizar métodos según necesidades
    async cargarDatos() {
        // Tu lógica aquí
    }
}

// Inicializar con el nuevo nombre
document.addEventListener('DOMContentLoaded', () => {
    new NuevaPaginaPage();
});
```

### 5. Personalizar Estilos CSS

```css
/* Cambiar variables de color si es necesario */
:root {
    --primary-color: #tu-color-primario;
    --secondary-color: #tu-color-secundario;
}

/* Agregar estilos específicos de tu página */
.mi-componente-especial {
    /* Tus estilos aquí */
}
```

## 🎨 Personalización Avanzada

### Cambiar Colores del Tema

```css
:root {
    --primary-color: #875A7B;    /* Color principal */
    --secondary-color: #6c757d;  /* Color secundario */
    --success-color: #28a745;    /* Verde de éxito */
    --danger-color: #dc3545;     /* Rojo de error */
    --warning-color: #ffc107;    /* Amarillo de advertencia */
    --info-color: #17a2b8;       /* Azul de información */
}
```

### Agregar Nuevos Botones

```html
<!-- En el toolbar -->
<div class="btn-group" role="group">
    <button class="btn btn-primary" id="mi-nuevo-btn">
        <i class="fas fa-star"></i> Mi Acción
    </button>
</div>
```

```javascript
// En el JavaScript
setupEventListeners() {
    document.getElementById('mi-nuevo-btn')?.addEventListener('click', () => {
        this.miNuevaFuncion();
    });
}
```

### Personalizar el Modal

```html
<!-- Cambiar el contenido del modal -->
<div class="modal-body">
    <form id="mi-formulario">
        <div class="mb-3">
            <label class="form-label">Mi Campo</label>
            <input type="text" class="form-control" id="mi-campo">
        </div>
    </form>
</div>
```

## 🔧 API Service

La clase `ApiService` proporciona métodos para comunicarse con el backend:

```javascript
// GET request
const datos = await this.apiService.get('/api/mi-endpoint');

// POST request
const resultado = await this.apiService.post('/api/crear', {
    nombre: 'Nuevo elemento',
    descripcion: 'Descripción del elemento'
});

// PUT request
const actualizado = await this.apiService.put('/api/actualizar/1', {
    nombre: 'Nombre actualizado'
});

// DELETE request
await this.apiService.delete('/api/eliminar/1');
```

## 📱 Responsive Design

La plantilla incluye breakpoints responsivos:

- **Desktop**: > 992px
- **Tablet**: 768px - 991px
- **Mobile**: < 768px

Los estilos se adaptan automáticamente a cada dispositivo.

## 🎯 Mejores Prácticas

### 1. Nomenclatura
- Usar nombres descriptivos para IDs y clases
- Mantener consistencia con el resto del sistema
- Usar kebab-case para IDs HTML y camelCase para JavaScript

### 2. Estructura
- Mantener la separación de responsabilidades
- Un archivo por funcionalidad principal
- Comentar código complejo

### 3. Performance
- Cargar datos de forma asíncrona
- Usar debounce para búsquedas
- Minimizar manipulaciones del DOM

### 4. Accesibilidad
- Usar etiquetas semánticas
- Incluir atributos ARIA cuando sea necesario
- Mantener contraste de colores adecuado

## 🐛 Solución de Problemas

### Error: "Cannot read properties of null"
- Verificar que los IDs de elementos HTML coincidan con el JavaScript
- Asegurar que el DOM esté cargado antes de acceder a elementos

### Estilos no se aplican
- Verificar que el archivo CSS esté correctamente enlazado
- Comprobar la especificidad de los selectores CSS
- Usar herramientas de desarrollo del navegador

### API no responde
- Verificar la URL del endpoint
- Comprobar que el servidor esté ejecutándose
- Revisar la consola del navegador para errores

## 📚 Recursos Adicionales

- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.0/)
- [Font Awesome Icons](https://fontawesome.com/icons)
- [MDN Web Docs](https://developer.mozilla.org/)

## 🤝 Contribuir

Para mejorar esta plantilla:

1. Identifica áreas de mejora
2. Implementa cambios manteniendo compatibilidad
3. Actualiza esta documentación
4. Prueba en diferentes navegadores y dispositivos

---

**Versión**: 1.0  
**Última actualización**: Enero 2025  
**Compatibilidad**: Bootstrap 5, ES6+, PHP 7.4+