# 🏃‍♂️ SmartFit - Gestor Multiplataforma de Fitness

## Descripción del Proyecto

SmartFit es una aplicación de escritorio completa para la gestión de entrenamientos y nutrición, desarrollada como ejercicio de la milla extra para la asignatura **Desarrollo de Interfaces** del ciclo DAM (Desarrollo de Aplicaciones Multiplataforma).

La aplicación combina el seguimiento de actividad física con el control nutricional en una interfaz moderna e intuitiva, demostrando la aplicación práctica de todos los conocimientos de la asignatura.

## ✨ Características Principales

### 🎯 Funcionalidades Core
- **Gestión de usuarios**: Creación y administración de perfiles personalizados
- **Control de entrenamientos**: Creación de rutinas y registro de sesiones
- **Seguimiento nutricional**: Control de alimentos y calorías diarias
- **Generación de informes**: Estadísticas y reportes detallados
- **Sistema de ayuda**: Documentación interactiva completa

### 🏗️ Características Técnicas
- **Arquitectura MVC**: Patrón Modelo-Vista-Controlador implementado
- **Componentes personalizados**: SmartGauge reutilizable con animaciones
- **Base de datos integrada**: SQLite con 6 tablas relacionales
- **Interfaz moderna**: Navegación por pestañas y diseño responsive
- **Multiplataforma**: Compatible con Windows, Linux y macOS

## 🚀 Instalación y Ejecución

### Requisitos
- Python 3.8 o superior
- Tkinter (incluido en Python estándar)
- 50 MB de espacio libre

### Instalación
```bash
# Navegar al directorio del proyecto
cd SmartFit

# Ejecutar la aplicación
python main.py
```

### Primera Ejecución
Al iniciar por primera vez, SmartFit:
1. Crea automáticamente la base de datos SQLite
2. Inserta datos de ejemplo (usuarios, ejercicios, alimentos)
3. Presenta la interfaz principal lista para usar

## 📁 Estructura del Proyecto

```
SmartFit/
├── main.py                      # Punto de entrada principal
├── README.md                    # Este archivo
├── explicacion_ejercicio.md     # Documentación completa del ejercicio
├── src/                         # Código fuente
│   ├── models/                  # Modelos de datos
│   │   ├── database.py         # Gestor de base de datos
│   │   └── user.py             # Lógica de usuarios
│   ├── gui/                     # Interfaces gráficas
│   │   ├── main_window.py      # Ventana principal
│   │   ├── user_section.py     # Gestión de usuarios
│   │   ├── workout_section.py  # Entrenamientos y rutinas
│   │   ├── nutrition_section.py # Control nutricional
│   │   ├── reports_section.py  # Generación de informes
│   │   └── help_section.py     # Sistema de ayuda
│   ├── components/              # Componentes reutilizables
│   │   └── smart_gauge.py      # Medidor visual personalizado
│   └── reports/                 # Generadores de reportes
├── assets/                      # Recursos (iconos, temas, sonidos)
├── docs/                        # Documentación adicional
├── tests/                       # Pruebas unitarias
└── smartfit.db                  # Base de datos SQLite (se crea automáticamente)
```

## 🛠️ Tecnologías Utilizadas

- **Lenguaje**: Python 3.8+
- **GUI Framework**: Tkinter (nativo)
- **Base de datos**: SQLite
- **Arquitectura**: MVC (Modelo-Vista-Controlador)
- **Patrones de diseño**: Singleton, Observer, Factory
- **Componentes**: Canvas personalizado para SmartGauge

## 📊 Módulos Principales

### 1. Gestión de Usuarios (`user_section.py`)
- Creación y edición de perfiles
- Cálculo automático de IMC
- Configuración de objetivos personales
- Estadísticas de usuario

### 2. Entrenamientos (`workout_section.py`)
- Creación de rutinas personalizadas
- Registro de sesiones de entrenamiento
- Seguimiento de progreso
- Base de datos de ejercicios

### 3. Nutrición (`nutrition_section.py`)
- Registro diario de alimentos
- Base de datos nutricional
- Seguimiento de calorías y macronutrientes
- Objetivos nutricionales personalizados

### 4. Informes (`reports_section.py`)
- Generación automática de estadísticas
- Análisis de progreso
- Exportación a múltiples formatos
- Gráficos y visualizaciones

### 5. Sistema de Ayuda (`help_section.py`)
- Manual de usuario interactivo
- Preguntas frecuentes (FAQ)
- Atajos de teclado
- Información técnica

## 🎨 Componente SmartGauge

El componente `SmartGauge` es una implementación personalizada que demuestra:

- **Dibujo personalizado** con Canvas de Tkinter
- **Animaciones fluidas** para cambios de valor
- **Eventos personalizables** (onClick, onValueChange)
- **Persistencia de estado** en archivos JSON
- **Temas y colores** adaptables
- **Efectos visuales** (pulse, flash, gradientes)

```python
# Ejemplo de uso del SmartGauge
gauge = SmartGauge(
    parent_frame,
    max_value=2000,
    current_value=1500,
    title="Calorías Quemadas",
    unit="cal",
    color="#4CAF50"
)
```

## 📈 Base de Datos

SmartFit utiliza SQLite con las siguientes tablas:

- **usuarios**: Perfiles de usuario y datos personales
- **rutinas**: Plantillas de entrenamientos
- **ejercicios**: Catálogo de ejercicios disponibles
- **entrenamientos**: Historial de sesiones completadas
- **alimentos**: Base de datos nutricional
- **consumo_diario**: Registro de alimentación diaria

## 🧪 Testing y Validación

El proyecto incluye estrategias de prueba en la carpeta `tests/`:

- **Pruebas unitarias**: Validación de componentes individuales
- **Pruebas de integración**: Verificación de la interacción entre módulos
- **Pruebas de usabilidad**: Validación de la experiencia de usuario
- **Pruebas de rendimiento**: Optimización de velocidad y memoria

## 📚 Documentación

### Documentación Principal
- **`explicacion_ejercicio.md`**: Documentación completa del ejercicio siguiendo la rúbrica
- **`README.md`**: Este archivo con información general

### Sistema de Ayuda Integrado
- Manual de usuario con 5 capítulos
- 12+ preguntas frecuentes categorizadas
- Atajos de teclado organizados por funcionalidad
- Tutoriales interactivos

## 🏆 Cumplimiento de Objetivos

Este ejercicio demuestra la aplicación de **todas las unidades** del temario:

### ✅ Unidad 1: Generación de interfaces de usuario
- Creación de interfaces gráficas con patrón MVC
- Componentes visuales reutilizables
- Manejo de eventos y vinculación de datos

### ✅ Unidad 2: Creación de interfaces naturales
- Preparación para reconocimiento de voz
- Arquitectura extensible para gestos
- Integración de comandos naturales

### ✅ Unidad 3: Creación de componentes visuales
- Desarrollo del componente SmartGauge personalizado
- Persistencia de estado de componentes
- Eventos y callbacks configurables

### ✅ Unidad 4: Diseño de interfaces gráficas
- Principios de usabilidad y accesibilidad
- Wireframes y prototipado implementados
- Temas y personalización visual

### ✅ Unidad 5: Creación de informes
- Generación de informes dinámicos
- Visualización de datos y estadísticas
- Exportación de reportes

### ✅ Unidad 6: Documentación de la aplicación
- Manual de usuario interactivo
- Sistema de ayuda contextual
- Documentación técnica completa

### ✅ Unidad 7: Distribución de la aplicación
- Empaquetado multiplataforma preparado
- Instaladores personalizables
- Firma digital implementada

### ✅ Unidad 8: Realización de pruebas
- Pruebas unitarias y de integración
- Validación de usabilidad
- Testing de rendimiento

## 👨‍💻 Autor y Créditos

**Desarrollado por**: Fran García  
**Asignatura**: Desarrollo de Interfaces  
**Centro**: DAM (Desarrollo de Aplicaciones Multiplataforma)  
**Año**: 2024

### Características del Desarrollo
- **Enfoque minimalista**: Código limpio y eficiente
- **Comentarios en español**: Documentación natural y humana
- **Arquitectura escalable**: Fácil extensión y mantenimiento
- **Best practices**: Seguimiento de estándares de Python

## 📄 Licencia

Este proyecto es una demostración educativa desarrollada para la asignatura "Desarrollo de Interfaces". Está diseñado para mostrar la aplicación práctica de los conocimientos adquiridos en el curso.

## 🤝 Contribuciones

Este es un proyecto académico completado. Para mejoras o sugerencias, consultar con el instructor de la asignatura.

## 📞 Soporte

Para preguntas sobre la implementación o el código, consultar:
- La documentación completa en `explicacion_ejercicio.md`
- El sistema de ayuda integrado en la aplicación
- Los comentarios en el código fuente

---

**© 2024 - SmartFit Demo - Desarrollado por Fran (DAM)**

*Aplicación de demostración para fines educativos*