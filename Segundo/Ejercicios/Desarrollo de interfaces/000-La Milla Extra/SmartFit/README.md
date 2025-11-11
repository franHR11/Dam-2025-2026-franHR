# 🏃‍♂️ SmartFit – Ecosistema Integral de Fitness

## 📌 Descripción del Proyecto
SmartFit es un **software de escritorio multiplataforma** diseñado para entrenadores personales, nutricionistas y usuarios finales que desean gestionar su progreso físico y nutricional en un solo lugar. El proyecto combina una interfaz moderna en Tkinter con una base de datos relacional en SQLite, proporcionando un flujo completo desde la creación del perfil hasta la generación de informes avanzados.

## ✨ Características Destacadas
- 🛠️ **Arquitectura modular MVC** con separación clara entre datos, lógica y vistas.
- 📊 **Dashboard interactivo** con indicadores dinámicos y componentes personalizados como `SmartGauge`.
- 👥 **Gestión avanzada de usuarios** con cálculo de IMC, métricas de salud y objetivos personalizables.
- 💪 **Planificador de entrenamientos** con rutinas, historial, seguimiento de progreso y analítica.
- 🥗 **Control nutricional diario** con base de datos de alimentos y calculadora de macronutrientes.
- 🧠 **Sistema de ayuda contextual** con manual interactivo, FAQs y atajos de teclado.
- 🧪 **Suite de pruebas y entorno de verificación** para asegurar estabilidad antes de entregar el proyecto.
- 🔐 **Persistencia local segura** con inicialización automática de datos y cierre controlado de recursos.

## ⚙️ Funcionalidades
1. **Core de la Aplicación** (`main.py`)
   - Inicialización de base de datos (`DatabaseManager`) y seed de datos de ejemplo.
   - Gestor de usuarios (`UserManager`) y diálogo de primera ejecución.
   - Creación de la ventana principal (`MainWindow`) y ciclo de vida de la app.

2. **Interfaz principal** (`src/gui/main_window.py`)
   - Barra superior con selector de usuario y navegación inteligente.
   - Pestañas para dashboard, nutrición, entrenamientos, informes y ayuda.
   - Componentes reutilizables (`ModernButton`, `InfoCard`, `SmartGauge`).

3. **Gestión de usuarios** (`src/gui/user_section.py` & `dialogs/user_dialog.py`)
   - Formulario validado, edición de perfiles y estadísticas personalizadas.
   - Cálculo de IMC, calorías basales y objetivos según nivel de actividad.

4. **Entrenamientos** (`src/gui/workout_section.py`)
   - Agenda con filtros por fecha, creación de rutinas y progreso semanal.
   - Treeviews interactivos, exportación y analítica de entrenamientos.

5. **Nutrición** (`src/gui/nutrition_section.py`)
   - Registro de ingesta diaria, metas de macros y navegación por calendario.
   - Control de alimentos propios y base de datos inicial con INSERT OR IGNORE.

6. **Informes y ayuda** (`src/gui/reports_section.py`, `src/gui/help_section.py`)
   - Generación de reportes, estadísticas y documentación integrada.
   - Sistema de atajos, tutoriales paso a paso y soporte dentro de la app.

7. **Componentes y utilidades** (`src/components/smart_gauge.py`, `src/gui/widgets/`)
   - Canvas animado para métricas clave.
   - Widgets estilizados con ttk y gestión de temas personalizados.

## 🔧 Tecnologías Utilizadas
- 🐍 **Python 3.11** (compatible 3.8+)
- 🪟 **Tkinter + ttk** para GUI nativa multiplataforma
- 🗃️ **SQLite** como motor de persistencia local
- 📄 **JSON** para almacenamiento ligero y configuración
- 🧪 **unittest / scripts de verificación** en `__tests__/`
- 📦 **Estructura modular** con paquetes `src.gui`, `src.components`, `src.models`

## 📁 Estructura del Proyecto
```
SmartFit/
├── main.py                # Punto de entrada completo
├── main_simple.py         # Variante simplificada para pruebas rápidas
├── main_test.py           # Runner enfocado a validaciones
├── __tests__/             # Suite de verificación y utilidades QA
├── src/
│   ├── models/            # Acceso a datos (SQLite, usuarios)
│   ├── gui/               # Interfaz modular (secciones, diálogos, widgets)
│   └── components/        # Componentes visuales reutilizables
├── assets/                # Recursos estáticos (iconos, temas)
├── docs/                  # Documentación adicional y anexos
├── explicacion_ejercicio.md
├── smartfit.db            # Base de datos generada en primera ejecución
└── README.md              # Documento que estás leyendo
```

## 🚀 Instrucciones de Uso
### 1. Requisitos previos
- Python 3.8 o superior
- Tkinter incluido (en distribuciones oficiales)
- pip actualizado (`python -m pip install --upgrade pip`)

### 2. Instalación de entorno
```powershell
# Crear entorno virtual (opcional pero recomendado)
python -m venv .venv
.\.venv\Scripts\activate

# Instalar dependencias si se añaden requisitos
python -m pip install -r requirements.txt  # (crear según necesidades)
```

### 3. Configuración inicial
1. Duplica el archivo `.env.example` (si se incluye) y renómbralo a `.env`.
2. Ajusta valores ficticios, por ejemplo:
   ```env
   SMARTFIT_DB_PATH=smartfit.db
   SMARTFIT_THEME=default
   ```
3. Verifica permisos de escritura en el directorio para la base de datos.

### 4. Ejecución local
```powershell
python main.py           # Versión completa
# o
python main_simple.py    # Interfaz reducida para demostraciones
```
- La primera ejecución crea `smartfit.db`, genera tablas (`usuarios`, `rutinas`, `ejercicios`, etc.) e inserta datos de ejemplo.
- Selecciona o crea un usuario para desbloquear todas las vistas.

### 5. Scripts útiles
```powershell
python main_test.py      # Pruebas manuales guiadas
python -m unittest       # Ejecuta pruebas unitarias en __tests__
```

### 6. Empaquetado y despliegue
- Utiliza herramientas como `pyinstaller` o `cx_Freeze` para generar ejecutables.
- Define iconos e instala requisitos en el instalador según plataforma.
- Comprueba el funcionamiento en Windows, Linux y macOS antes de distribuir.

## 🧪 Ejemplos de Uso
```python
from src.models.database import DatabaseManager
from src.models.user import UserManager

db = DatabaseManager("smartfit.db")
db.check_connection()
db.create_tables()

users = UserManager(db)
user_id = users.crear_usuario("Laura Trainer", edad=29, peso=62, altura=1.70)
perfil = users.obtener_usuario_por_id(user_id)

print(perfil["nombre"], users.calcular_imc(perfil["peso"], perfil["altura"]))
```
> Resultado esperado: creación de un perfil persistente y cálculo de IMC para integraciones externas.

## 📞 Soporte y Contacto
- 📅 **Año**: 2025  
- 📨 **Autor**: Francisco José Herreros (franHR)  
- 📧 **Email**: [desarrollo@pcprogramacion.es](mailto:desarrollo@pcprogramacion.es)  
- 🌐 **Web**: [https://www.pcprogramacion.es](https://www.pcprogramacion.es)  
- 💼 **LinkedIn**: [Francisco José Herreros](https://www.linkedin.com/in/francisco-jose-herreros)  
- 🖥️ **Portfolio**: [https://franhr.pcprogramacion.es/](https://franhr.pcprogramacion.es/)  

## 🖼️ Imágenes del Proyecto
- 📸 *Pendiente de adjuntar capturas de la interfaz (dashboard, secciones de nutrición y entrenamientos).*  
  Recomiendo añadir archivos en `docs/` o `assets/` y enlazarlos aquí para potenciar la presentación visual.

## 🛡️ Licencia
### Español
Copyright (c) 2025 Francisco José Herreros (franHR) / PCProgramación

Todos los derechos reservados.

Este software es propiedad de Francisco José Herreros (franHR), desarrollador de PCProgramación (https://www.pcprogramacion.es). No está permitido copiar, modificar, distribuir o utilizar este código, ni total ni parcialmente, sin una autorización expresa y por escrito del autor.

El acceso a este repositorio tiene únicamente fines de revisión, auditoría o demostración, y no implica la cesión de ningún derecho de uso o explotación.

Para solicitar una licencia o permiso de uso, contacta con: desarrollo@pcprogramacion.es

### English
Copyright (c) 2025 Francisco José Herreros (franHR) / PCProgramación

All rights reserved.

This software is the property of Francisco José Herreros (franHR), developer of PCProgramación (https://www.pcprogramacion.es). It is not allowed to copy, modify, distribute or use this code, either totally or partially, without express and written authorization from the author.

Access to this repository has only review, audit or demonstration purposes, and does not imply the transfer of any right of use or exploitation.

To request a license or permission to use, contact: desarrollo@pcprogramacion.es

## 🔝 Hashtags Recomendados para LinkedIn
`#SmartFit #Python #Tkinter #SQLite #DesarrolloDeInterfaces #DAM #FitnessTech #DesktopApp #SoftwareEducativo #PCProgramacion`
