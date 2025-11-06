# help_section.py - Sección de ayuda para SmartFit
# Fran - Desarrollo de interfaces

import tkinter as tk
from datetime import datetime
from tkinter import messagebox, ttk
from typing import Dict, List, Optional


class HelpSection:
    """
    Sección de interfaz para ayuda y documentación
    Unidad: Documentación de la aplicación
    Subunidad: Sistema de ayuda y documentación interactiva

    Esta clase maneja:
    - Documentación de usuario
    - Tutoriales interactivos
    - Preguntas frecuentes (FAQ)
    - Atajos de teclado
    - Información técnica
    - Soporte y contacto
    """

    def __init__(self, parent_notebook, db_manager, user_manager, main_window):
        """Inicializa la sección de ayuda"""
        self.notebook = parent_notebook
        self.db = db_manager
        self.user_manager = user_manager
        self.main_window = main_window

        # Variables de la sección
        self.current_user = None
        self.help_content = self.initialize_help_content()
        self.quick_tips = self.initialize_quick_tips()

        # Crear frame principal
        self.frame = ttk.Frame(self.notebook)

        # Crear la interfaz
        self.create_help_interface()

    def initialize_help_content(self):
        """Inicializa el contenido de ayuda"""
        return {
            "getting_started": {
                "title": "🚀 Primeros Pasos",
                "content": """
BIENVENIDO A SMARTFIT
====================

SmartFit es tu entrenador personal digital que te ayuda a:
• Crear y seguir rutinas de entrenamiento
• Controlar tu nutrición diaria
• Generar informes de progreso
• Mantener un registro de tus actividades

PASOS PARA COMENZAR:
1. Crear tu perfil de usuario
2. Configurar tus objetivos
3. Explorar las diferentes secciones
4. Comenzar a registrar tus actividades

PRIMERA VEZ:
Si es tu primera vez usando SmartFit, te recomendamos:
• Empezar con rutinas simples
• Establecer objetivos realistas
• Ser constante con los registros
• Revisar tu progreso semanalmente
                """,
            },
            "user_guide": {
                "title": "📖 Guía de Usuario",
                "content": """
GUÍA DETALLADA DE USO
===================

SECCIÓN USUARIO:
• Perfil: Edita tu información personal
• Estadísticas: Ve tu progreso general
• Gestión: Administra usuarios

SECCIÓN ENTRENAMIENTOS:
• Rutinas: Crea y gestiona tus entrenamientos
• Historial: Revisa entrenamientos pasados
• Ejercicios: Explora la base de datos de ejercicios

SECCIÓN NUTRICIÓN:
• Consumo diario: Registra lo que comes
• Base de datos: Consulta información nutricional
• Objetivos: Establece metas nutricionales

SECCIÓN INFORMES:
• Generador: Crea informes personalizados
• Estadísticas: Visualiza tus datos
• Exportación: Descarga tus reportes

CONSEJOS DE USO:
• Mantén la información actualizada
• Registra actividades regularmente
• Revisa informes semanalmente
• Ajusta objetivos según progreso
                """,
            },
            "shortcuts": {
                "title": "⌨️ Atajos de Teclado",
                "content": """
ATAJOS DE TECLADO DISPONIBLES
=============================

NAVEGACIÓN GENERAL:
• F1: Ir a Inicio/Dashboard
• F2: Ir a Rutinas
• F3: Ir a Entrenamientos
• F4: Ir a Nutrición
• F5: Ir a Informes
• F6: Ir a Ayuda
• Ctrl+N: Nuevo usuario
• Ctrl+S: Guardar
• Ctrl+E: Exportar datos
• F11: Pantalla completa
• ESC: Cerrar diálogos

DENTRO DE LAS SECCIONES:
• Ctrl+F: Buscar en la lista actual
• Ctrl+A: Seleccionar todo
• Ctrl+C: Copiar
• Ctrl+V: Pegar
• Delete: Eliminar elemento seleccionado
• Enter: Confirmar acción
• Espacio: Seleccionar/deseleccionar

EN FORMULARIOS:
• Tab: Siguiente campo
• Shift+Tab: Campo anterior
• Enter: Enviar formulario
• Escape: Cancelar y cerrar

CONSEJOS:
• Los atajos mejoran la velocidad de uso
• Algunas funciones requieren selección previa
• Los atajos pueden variar según la sección activa
                """,
            },
            "faq": {
                "title": "❓ Preguntas Frecuentes",
                "content": """
PREGUNTAS FRECUENTES
===================

PREGUNTAS GENERALES:
P: ¿Cómo creo mi primer usuario?
R: Ve a la sección Usuario y haz clic en "Nuevo Usuario" o usa Ctrl+N

P: ¿Puedo cambiar mi información después?
R: Sí, siempre puedes editar tu perfil desde la sección Usuario

P: ¿Los datos se guardan automáticamente?
R: Sí, todos los datos se guardan en la base de datos local

P: ¿Puedo exportar mis datos?
R: Sí, desde la sección Informes → Exportar

ENTRENAMIENTOS:
P: ¿Cómo creo una rutina?
R: Ve a Entrenamientos → Rutinas → Nueva Rutina

P: ¿Puedo ver mi historial?
R: Sí, en Entrenamientos → Historial

P: ¿Cómo registro un entrenamiento?
R: Ve a Entrenamientos → Nuevo y completa el formulario

NUTRICIÓN:
P: ¿Cómo añado un alimento?
R: Ve a Nutrición → Consumo Diario, busca el alimento y especifica la cantidad

P: ¿Puedo crear alimentos personalizados?
R: Sí, en Nutrición → Base de Datos → Nuevo Alimento

P: ¿Cómo veo mis calorías del día?
R: La información aparece automáticamente en el resumen diario

INFORMES:
P: ¿Cómo genero un informe?
R: Ve a Informes → Generador, configura las opciones y haz clic en "Generar"

P: ¿En qué formatos puedo exportar?
R: PDF, Excel, CSV y JSON

PRECÁUCIÓN:
P: ¿Qué pasa si elimino algo por error?
R: Algunas eliminaciones son permanentes. Siempre confirma antes de eliminar

P: ¿Puedo recuperar datos eliminados?
R: No en la versión actual. Haz copias de seguridad regularmente
                """,
            },
            "troubleshooting": {
                "title": "🔧 Solución de Problemas",
                "content": """
SOLUCIÓN DE PROBLEMAS COMUNES
============================

PROBLEMAS DE RENDIMIENTO:
• Si la aplicación va lenta: Cierra otras aplicaciones pesadas
• Si se congela: Reinicia la aplicación
• Si consumes mucha RAM: Reinicia el sistema

PROBLEMAS DE DATOS:
• Si no se guardan datos: Verifica permisos de carpeta
• Si hay errores de base de datos: Reinicia la aplicación
• Si faltan datos: Revisa que el usuario esté seleccionado

PROBLEMAS DE INTERFAZ:
• Si la interfaz se ve mal: Ajusta la resolución de pantalla
• Si los textos se ven cortados: Cambia el tamaño de la ventana
• Si los botones no responden: Haz clic en otra área y vuelve a intentar

PROBLEMAS DE EXPORTACIÓN:
• Si falla la exportación: Verifica que tengas espacio en disco
• Si el archivo no se crea: Revisa los permisos de escritura
• Si el formato es incorrecto: Verifica la configuración antes de exportar

MENSAJES DE ERROR COMUNES:
• "No hay usuario seleccionado": Selecciona o crea un usuario
• "Error de base de datos": Reinicia la aplicación
• "Permiso denegado": Ejecuta como administrador
• "Archivo en uso": Cierra el archivo si está abierto en otro programa

RECOMENDACIONES GENERALES:
• Haz copias de seguridad regulares
• Mantén la aplicación actualizada
• Cierra otras aplicaciones si experimentas lentitud
• Reinicia la aplicación si experimentas errores

Si el problema persiste, contacta con el soporte técnico.
                """,
            },
            "technical": {
                "title": "🔧 Información Técnica",
                "content": """
INFORMACIÓN TÉCNICA
==================

ESPECIFICACIONES DEL SISTEMA:
• Sistema Operativo: Windows 7+, macOS 10.12+, Linux Ubuntu 18.04+
• Memoria RAM: Mínimo 2GB, recomendado 4GB
• Espacio en disco: 100MB libres
• Resolución mínima: 1024x768
• Resolución recomendada: 1920x1080

TECNOLOGÍAS UTILIZADAS:
• Python 3.8+
• Tkinter para la interfaz gráfica
• SQLite para la base de datos
• Matplotlib para gráficos (futuras versiones)
• ReportLab para generación de PDFs (futuras versiones)

ESTRUCTURA DE LA BASE DE DATOS:
• smartfit.db: Archivo principal de datos
• Tablas: usuarios, rutinas, ejercicios, entrenamientos, alimentos, consumo_diario
• Backup automático: Se crea al cerrar la aplicación

ARCHIVOS IMPORTANTES:
• smartfit.db: Base de datos principal
• gauge_*.json: Configuración de medidores
• log_*.txt: Archivos de log (si están habilitados)
• backup_*.db: Copias de seguridad automáticas

UBICACIÓN DE ARCHIVOS:
• Windows: %APPDATA%/SmartFit/
• macOS: ~/Library/Application Support/SmartFit/
• Linux: ~/.local/share/SmartFit/

COMANDOS DE LÍNEA (OPCIONAL):
• --reset: Resetear configuración
• --backup: Crear copia de seguridad
• --restore: Restaurar desde copia
• --export-all: Exportar todos los datos
• --version: Mostrar versión

SEGURIDAD:
• Todos los datos se almacenan localmente
• No se envían datos a servidores externos
• Las copias de seguridad están encriptadas
• Soporte para múltiples usuarios en el mismo equipo

ACTUALIZACIONES:
• Las actualizaciones se descargan automáticamente
• Se crea copia de seguridad antes de actualizar
• Posibilidad de revertir a versión anterior
                """,
            },
        }

    def initialize_quick_tips(self):
        """Inicializa consejos rápidos"""
        return [
            "💡 Usa Ctrl+N para crear usuarios rápidamente",
            "🏃 Registra tus entrenamientos justo después de hacerlos",
            "📊 Revisa tus informes semanalmente para ver tu progreso",
            "🥗 Mantén un registro constante de tu alimentación",
            "🎯 Establece objetivos realistas y específicos",
            "💾 Exporta tus datos regularmente como respaldo",
            "⌨️ Aprende los atajos de teclado para mayor eficiencia",
            "📅 Programa recordatorios para usar la aplicación",
            "🔄 Sincroniza tu progreso con tus metas personales",
            "📱 Considera tomar fotos de tus comidas para recordar porciones",
        ]

    def create_help_interface(self):
        """Crea la interfaz de la sección de ayuda"""
        # Frame principal
        main_frame = ttk.Frame(self.frame)
        main_frame.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)

        # Título de la sección
        title_label = ttk.Label(
            main_frame, text="❓ Centro de Ayuda", font=("Arial", 16, "bold")
        )
        title_label.pack(pady=(0, 20))

        # Crear notebook interno para subsecciones
        self.help_notebook = ttk.Notebook(main_frame)
        self.help_notebook.pack(fill=tk.BOTH, expand=True)

        # Crear subsecciones
        self.create_overview_subtab()
        self.create_tutorials_subtab()
        self.create_shortcuts_subtab()
        self.create_faq_subtab()
        self.create_contact_subtab()

    def create_overview_subtab(self):
        """Crea la pestaña de vista general"""
        # Frame de vista general
        overview_frame = ttk.Frame(self.help_notebook)
        self.help_notebook.add(overview_frame, text="📋 Vista General")

        # Panel de información de la aplicación
        app_info_frame = ttk.LabelFrame(overview_frame, text="Información de SmartFit")
        app_info_frame.pack(fill=tk.X, padx=20, pady=10)

        # Información de la app
        app_info_text = f"""
SMARTFIT - TU ENTRENADOR PERSONAL DIGITAL
==========================================

Versión: 1.0.0
Fecha de lanzamiento: Enero 2024
Desarrollado por: Fran - DAM

CARACTERÍSTICAS PRINCIPALES:
• 🏃 Gestión completa de entrenamientos
• 🥗 Control nutricional detallado
• 📊 Informes y estadísticas avanzadas
• 💾 Sistema de respaldo automático
• 🔒 Datos seguros y privados
• 💻 Interfaz moderna y fácil de usar
• ⌨️ Atajos de teclado optimizados

BENEFICIOS:
• Mejora tu forma física de manera estructurada
• Controla tu alimentación de forma precisa
• Visualiza tu progreso de forma clara
• Mantén un registro completo de tu actividad
• Accede a informes profesionales
• Datos seguros en tu propio equipo

COMENZAR AHORA:
1. Ve a la sección "Usuario" y crea tu perfil
2. Explora las diferentes funcionalidades
3. Empieza a registrar tus actividades
4. Revisa tus progreso regularmente

¡Disfruta de SmartFit!
        """

        # Widget de texto para información
        info_text_widget = tk.Text(
            app_info_frame,
            height=15,
            font=("Arial", 10),
            wrap=tk.WORD,
            padx=10,
            pady=10,
        )
        info_text_widget.pack(fill=tk.BOTH, expand=True)
        info_text_widget.insert(1.0, app_info_text)
        info_text_widget.config(state=tk.DISABLED)

        # Frame de consejos rápidos
        tips_frame = ttk.LabelFrame(overview_frame, text="💡 Consejos Rápidos")
        tips_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=10)

        # Lista de consejos
        self.tips_listbox = tk.Listbox(
            tips_frame, font=("Arial", 10), height=8, selectbackground="#4A90E2"
        )
        tips_scrollbar = ttk.Scrollbar(
            tips_frame, orient=tk.VERTICAL, command=self.tips_listbox.yview
        )
        self.tips_listbox.configure(yscrollcommand=tips_scrollbar.set)

        # Empaquetar
        self.tips_listbox.pack(
            side=tk.LEFT, fill=tk.BOTH, expand=True, padx=(10, 5), pady=10
        )
        tips_scrollbar.pack(side=tk.RIGHT, fill=tk.Y, padx=(0, 10), pady=10)

        # Llenar consejos
        for tip in self.quick_tips:
            self.tips_listbox.insert(tk.END, tip)

        # Botón para consejo aleatorio
        random_tip_btn = ttk.Button(
            tips_frame, text="🎲 Consejo Aleatorio", command=self.show_random_tip
        )
        random_tip_btn.pack(pady=5)

    def create_tutorials_subtab(self):
        """Crea la pestaña de tutoriales"""
        # Frame de tutoriales
        tutorials_frame = ttk.Frame(self.help_notebook)
        self.help_notebook.add(tutorials_frame, text="🎓 Tutoriales")

        # Selector de tutorial
        tutorial_selector_frame = ttk.LabelFrame(
            tutorials_frame, text="Seleccionar Tutorial"
        )
        tutorial_selector_frame.pack(fill=tk.X, padx=20, pady=10)

        # Lista de tutoriales disponibles
        tutorials = [
            ("Primeros Pasos", "Configuración inicial y primer uso"),
            ("Crear Usuario", "Cómo crear y configurar tu perfil"),
            ("Gestionar Entrenamientos", "Crear y seguir rutinas"),
            ("Control Nutricional", "Registrar alimentos y calorías"),
            ("Generar Informes", "Crear y exportar reportes"),
            ("Atajos de Teclado", "Optimizar tu flujo de trabajo"),
        ]

        tutorial_frame = ttk.Frame(tutorial_selector_frame)
        tutorial_frame.pack(fill=tk.X, pady=10)

        # Variables
        self.selected_tutorial = tk.StringVar()
        self.tutorial_content = tk.StringVar()

        # Lista de tutoriales
        tutorial_list = tk.Listbox(
            tutorial_frame, height=6, font=("Arial", 10), selectbackground="#4A90E2"
        )
        tutorial_list.pack(side=tk.LEFT, fill=tk.BOTH, expand=True, padx=(10, 5))
        tutorial_scrollbar = ttk.Scrollbar(
            tutorial_frame, orient=tk.VERTICAL, command=tutorial_list.yview
        )
        tutorial_list.configure(yscrollcommand=tutorial_scrollbar.set)

        # Empaquetar
        tutorial_list.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        tutorial_scrollbar.pack(side=tk.RIGHT, fill=tk.Y)

        # Llenar lista
        for i, (title, description) in enumerate(tutorials):
            tutorial_list.insert(tk.END, f"{i + 1}. {title}")
            tutorial_list.insert(tk.END, f"   {description}")
            tutorial_list.insert(tk.END, "")  # Línea en blanco

        # Contenido del tutorial
        content_frame = ttk.LabelFrame(tutorials_frame, text="Contenido del Tutorial")
        content_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=10)

        # Widget de texto para tutorial
        self.tutorial_text = tk.Text(
            content_frame, height=15, font=("Arial", 10), wrap=tk.WORD
        )
        tutorial_content_scrollbar = ttk.Scrollbar(
            content_frame, orient=tk.VERTICAL, command=self.tutorial_text.yview
        )
        self.tutorial_text.configure(yscrollcommand=tutorial_content_scrollbar.set)

        # Empaquetar
        self.tutorial_text.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        tutorial_content_scrollbar.pack(side=tk.RIGHT, fill=tk.Y)

        # Botón para cargar tutorial
        load_tutorial_btn = ttk.Button(
            content_frame,
            text="Cargar Tutorial Seleccionado",
            command=self.load_selected_tutorial,
        )
        load_tutorial_btn.pack(pady=5)

        # Bind para selección
        tutorial_list.bind("<<ListboxSelect>>", self.on_tutorial_select)

    def create_shortcuts_subtab(self):
        """Crea la pestaña de atajos de teclado"""
        # Frame de atajos
        shortcuts_frame = ttk.Frame(self.help_notebook)
        self.help_notebook.add(shortcuts_frame, text="⌨️ Atajos")

        # Crear canvas con scroll
        canvas = tk.Canvas(shortcuts_frame, height=500)
        shortcuts_scrollbar = ttk.Scrollbar(
            shortcuts_frame, orient=tk.VERTICAL, command=canvas.yview
        )
        shortcuts_content_frame = ttk.Frame(canvas)

        shortcuts_content_frame.bind(
            "<Configure>", lambda e: canvas.configure(scrollregion=canvas.bbox("all"))
        )
        canvas.create_window((0, 0), window=shortcuts_content_frame, anchor="nw")
        canvas.configure(yscrollcommand=shortcuts_scrollbar.set)

        canvas.pack(side="left", fill="both", expand=True)
        shortcuts_scrollbar.pack(side="right", fill="y")

        # Atajos por categoría
        shortcut_categories = [
            (
                "🚀 Navegación General",
                [
                    ("F1", "Ir a Dashboard/Inicio"),
                    ("F2", "Ir a Rutinas"),
                    ("F3", "Ir a Entrenamientos"),
                    ("F4", "Ir a Nutrición"),
                    ("F5", "Ir a Informes"),
                    ("F6", "Ir a Ayuda"),
                    ("F11", "Pantalla completa"),
                    ("ESC", "Cerrar diálogos"),
                    ("Ctrl+Q", "Salir de la aplicación"),
                ],
            ),
            (
                "👤 Gestión de Usuarios",
                [
                    ("Ctrl+N", "Crear nuevo usuario"),
                    ("Ctrl+E", "Editar usuario actual"),
                    ("Ctrl+D", "Cambiar usuario"),
                    ("Ctrl+Del", "Eliminar usuario"),
                ],
            ),
            (
                "💪 Entrenamientos",
                [
                    ("Ctrl+R", "Crear nueva rutina"),
                    ("Ctrl+W", "Registrar entrenamiento"),
                    ("Ctrl+L", "Ver historial"),
                    ("Space", "Iniciar/parar temporizador"),
                ],
            ),
            (
                "🥗 Nutrición",
                [
                    ("Ctrl+A", "Añadir alimento"),
                    ("Ctrl+F", "Buscar alimento"),
                    ("Ctrl+U", "Ver día anterior"),
                    ("Ctrl+N", "Ver día siguiente"),
                ],
            ),
            (
                "📊 Informes",
                [
                    ("Ctrl+G", "Generar informe"),
                    ("Ctrl+X", "Exportar datos"),
                    ("Ctrl+P", "Imprimir informe"),
                    ("Ctrl+I", "Vista previa"),
                ],
            ),
            (
                "🔧 Comandos Generales",
                [
                    ("Ctrl+S", "Guardar datos"),
                    ("Ctrl+Z", "Deshacer acción"),
                    ("Ctrl+Y", "Rehacer acción"),
                    ("Ctrl+F", "Buscar en la lista actual"),
                    ("Ctrl+A", "Seleccionar todo"),
                ],
            ),
        ]

        for i, (category, shortcuts) in enumerate(shortcut_categories):
            # Frame de categoría
            category_frame = ttk.LabelFrame(shortcuts_content_frame, text=category)
            category_frame.pack(fill=tk.X, padx=20, pady=(10 if i == 0 else 5, 10))

            # Grid para atajos
            for j, (key, description) in enumerate(shortcuts):
                key_frame = ttk.Frame(category_frame)
                key_frame.pack(fill=tk.X, padx=15, pady=2)

                # Mostrar tecla
                key_label = tk.Label(
                    key_frame,
                    text=key,
                    font=("Courier", 10, "bold"),
                    bg="#f0f0f0",
                    width=15,
                    relief="ridge",
                )
                key_label.pack(side=tk.LEFT)

                # Mostrar descripción
                desc_label = tk.Label(key_frame, text=description, font=("Arial", 10))
                desc_label.pack(side=tk.LEFT, padx=(15, 0))

    def create_faq_subtab(self):
        """Crea la pestaña de preguntas frecuentes"""
        # Frame de FAQ
        faq_frame = ttk.Frame(self.help_notebook)
        self.help_notebook.add(faq_frame, text="❓ FAQ")

        # Crear notebook para categorías de FAQ
        self.faq_notebook = ttk.Notebook(faq_frame)
        self.faq_notebook.pack(fill=tk.BOTH, expand=True, padx=20, pady=10)

        # Categorías de FAQ
        self.faq_data = {
            "General": [
                {
                    "q": "¿Cómo empiezo a usar SmartFit?",
                    "a": "1. Crea tu perfil en la sección Usuario\n2. Establece tus objetivos\n3. Explora las diferentes secciones\n4. Empieza a registrar tus actividades",
                },
                {
                    "q": "¿Mis datos están seguros?",
                    "a": "Sí, todos los datos se almacenan localmente en tu equipo. No se envían a servidores externos y mantienes control total sobre tu información.",
                },
                {
                    "q": "¿Puedo usar SmartFit sin conexión?",
                    "a": "Sí, SmartFit funciona completamente sin conexión a internet. Todos los datos se guardan localmente.",
                },
                {
                    "q": "¿Cómo hago una copia de seguridad?",
                    "a": "Ve a Informes → Exportar y selecciona el formato que prefieras. También puedes copiar manualmente el archivo smartfit.db",
                },
            ],
            "Usuarios": [
                {
                    "q": "¿Puedo tener varios usuarios?",
                    "a": "Sí, SmartFit soporta múltiples usuarios en el mismo equipo. Puedes cambiar entre usuarios desde el botón en la parte superior.",
                },
                {
                    "q": "¿Cómo cambio mi información personal?",
                    "a": "Ve a la sección Usuario, selecciona la pestaña 'Perfil' y haz clic en 'Editar Perfil'.",
                },
                {
                    "q": "¿Puedo eliminar mi cuenta?",
                    "a": "Sí, desde la sección Usuario → Gestión, selecciona el usuario y haz clic en 'Eliminar'. Esta acción es irreversible.",
                },
            ],
            "Entrenamientos": [
                {
                    "q": "¿Cómo creo una rutina personalizada?",
                    "a": "Ve a Entrenamientos → Rutinas → Nueva Rutina. Completa el formulario con nombre, descripción, duración y dificultad.",
                },
                {
                    "q": "¿Puedo ver mi progreso de entrenamientos?",
                    "a": "Sí, en la sección Entrenamientos → Progreso puedes ver estadísticas detalladas de tu evolución.",
                },
                {
                    "q": "¿Cómo registro un entrenamiento completado?",
                    "a": "Ve a Entrenamientos → Nuevo y completa el formulario con los detalles de tu sesión de entrenamiento.",
                },
            ],
            "Nutrición": [
                {
                    "q": "¿Cómo añado un alimento?",
                    "a": "En Nutrición → Consumo Diario, busca el alimento en el combo, especifica la cantidad y haz clic en 'Añadir'.",
                },
                {
                    "q": "¿Puedo crear alimentos personalizados?",
                    "a": "Sí, en Nutrición → Base de Datos → Nuevo Alimento puedes crear alimentos con información nutricional personalizada.",
                },
                {
                    "q": "¿Cómo veo mis calorías del día?",
                    "a": "El resumen de calorías aparece automáticamente en la parte superior de la sección Nutrición.",
                },
            ],
        }

        # Crear pestañas para cada categoría
        for category, faqs in self.faq_data.items():
            self.create_faq_category_tab(category, faqs)

    def create_faq_category_tab(self, category, faqs):
        """Crea una pestaña de categoría FAQ"""
        # Frame de la categoría
        category_frame = ttk.Frame(self.faq_notebook)
        self.faq_notebook.add(category_frame, text=category)

        # Frame con scroll
        canvas = tk.Canvas(category_frame, height=400)
        faq_scrollbar = ttk.Scrollbar(
            category_frame, orient=tk.VERTICAL, command=canvas.yview
        )
        faq_content_frame = ttk.Frame(canvas)

        faq_content_frame.bind(
            "<Configure>", lambda e: canvas.configure(scrollregion=canvas.bbox("all"))
        )
        canvas.create_window((0, 0), window=faq_content_frame, anchor="nw")
        canvas.configure(yscrollcommand=faq_scrollbar.set)

        canvas.pack(side="left", fill="both", expand=True)
        faq_scrollbar.pack(side="right", fill="y")

        # Crear preguntas y respuestas
        for i, faq in enumerate(faqs):
            # Frame de la pregunta
            q_frame = ttk.LabelFrame(faq_content_frame, text=f"Pregunta {i + 1}")
            q_frame.pack(fill=tk.X, padx=10, pady=5)

            # Pregunta
            q_label = tk.Label(
                q_frame,
                text=f"❓ {faq['q']}",
                font=("Arial", 10, "bold"),
                wraplength=600,
            )
            q_label.pack(anchor=tk.W, padx=10, pady=5)

            # Respuesta
            a_label = tk.Label(
                q_frame,
                text=f"💡 {faq['a']}",
                font=("Arial", 9),
                wraplength=600,
                justify=tk.LEFT,
            )
            a_label.pack(anchor=tk.W, padx=20, pady=(0, 10))

    def create_contact_subtab(self):
        """Crea la pestaña de contacto"""
        # Frame de contacto
        contact_frame = ttk.Frame(self.help_notebook)
        self.contact_frame = contact_frame  # Guardar referencia
        self.help_notebook.add(contact_frame, text="📞 Contacto")

        # Información de contacto
        contact_info_frame = ttk.LabelFrame(
            contact_frame, text="Información de Contacto"
        )
        contact_info_frame.pack(fill=tk.X, padx=20, pady=10)

        contact_info = """
DESARROLLADO POR:
Fran - Desarrollo de Aplicaciones Multiplataforma (DAM)

EMAIL DE SOPORTE:
soporte.smartfit@ejemplo.com

SITIO WEB:
www.smartfit-ejemplo.com

GITHUB:
github.com/smartfit-app

DOCUMENTACIÓN:
docs.smartfit-ejemplo.com

HORARIOS DE ATENCIÓN:
Lunes a Viernes: 9:00 - 18:00 CET
Sábados: 10:00 - 14:00 CET
Domingos: Cerrado

TIEMPO DE RESPUESTA:
• Consultas generales: 24-48 horas
• Problemas técnicos: 12-24 horas
• Solicitudes de características: 1-2 semanas
        """

        info_widget = tk.Text(
            contact_info_frame,
            height=12,
            font=("Arial", 10),
            wrap=tk.WORD,
            padx=10,
            pady=10,
        )
        info_widget.pack(fill=tk.X)
        info_widget.insert(1.0, contact_info)
        info_widget.config(state=tk.DISABLED)

        # Formulario de contacto
        contact_form_frame = ttk.LabelFrame(contact_frame, text="Enviar Consulta")
        contact_form_frame.pack(fill=tk.X, padx=20, pady=10)

        # Variables del formulario
        self.contact_vars = {
            "name": tk.StringVar(),
            "email": tk.StringVar(),
            "subject": tk.StringVar(),
            "category": tk.StringVar(),
            "message": tk.StringVar(),
        }

        # Configurar categorías
        self.contact_vars["category"].set("General")
        categories = [
            "General",
            "Soporte Técnico",
            "Solicitud de Característica",
            "Reporte de Error",
            "Comentarios",
        ]

        # Formulario
        form_grid = ttk.Frame(contact_form_frame)
        form_grid.pack(fill=tk.X, pady=10)

        # Campos
        fields = [
            ("Nombre:", "name"),
            ("Email:", "email"),
            ("Asunto:", "subject"),
            ("Categoría:", "category"),
        ]

        for i, (label, key) in enumerate(fields):
            ttk.Label(form_grid, text=label).grid(
                row=i, column=0, sticky=tk.W, padx=10, pady=5
            )

            if key == "category":
                combo = ttk.Combobox(
                    form_grid,
                    textvariable=self.contact_vars[key],
                    state="readonly",
                    width=30,
                )
                combo["values"] = categories
                combo.grid(row=i, column=1, padx=10, pady=5, sticky=tk.W)
            else:
                ttk.Entry(
                    form_grid, textvariable=self.contact_vars[key], width=33
                ).grid(row=i, column=1, padx=10, pady=5, sticky=tk.W)

        # Mensaje
        ttk.Label(form_grid, text="Mensaje:").grid(
            row=4, column=0, sticky=tk.NW, padx=10, pady=5
        )

        self.message_text = tk.Text(form_grid, width=30, height=6)
        self.message_text.grid(row=4, column=1, padx=10, pady=5, sticky=tk.W)

        # Botones
        buttons_frame = ttk.Frame(contact_form_frame)
        buttons_frame.pack(fill=tk.X, pady=10)

        ttk.Button(
            buttons_frame, text="📧 Enviar Consulta", command=self.send_contact_form
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            buttons_frame, text="🗑️ Limpiar", command=self.clear_contact_form
        ).pack(side=tk.LEFT, padx=5)

        # Información adicional
        additional_info_frame = ttk.LabelFrame(
            contact_frame, text="Información Adicional"
        )
        additional_info_frame.pack(fill=tk.X, padx=20, pady=10)

        additional_info = """
ANTES DE CONTACTAR:
• Revisa la sección de FAQ para respuestas rápidas
• Consulta la documentación técnica
• Verifica que estés usando la última versión
• Describe el problema con el mayor detalle posible

INFORMACIÓN ÚTIL A INCLUIR:
• Versión de SmartFit que estás usando
• Sistema operativo
• Pasos para reproducir el problema
• Mensajes de error (si los hay)
• Capturas de pantalla (si es relevante)

COMUNIDAD:
• Únete a nuestro grupo de usuarios en Telegram
• Participa en el foro de la comunidad
• Comparte tus consejos y trucos
• Ayuda a otros usuarios

¡GRACIAS POR USAR SMARTFIT!
        """

        additional_widget = tk.Text(
            additional_info_frame,
            height=10,
            font=("Arial", 10),
            wrap=tk.WORD,
            padx=10,
            pady=10,
        )
        additional_widget.pack(fill=tk.X)
        additional_widget.insert(1.0, additional_info)
        additional_widget.config(state=tk.DISABLED)

    def load_user_data(self):
        """Carga los datos del usuario actual"""
        # Por ahora no hay datos específicos de usuario para la sección de ayuda
        pass

    def show_random_tip(self):
        """Muestra un consejo aleatorio"""
        import random

        tip = random.choice(self.quick_tips)
        messagebox.showinfo("💡 Consejo Aleatorio", tip)

    def on_tutorial_select(self, event):
        """Maneja la selección de un tutorial"""
        # Esta función se podría usar para mostrar vista previa del tutorial
        # Por ahora, simplemente guarda la selección
        pass

    def load_selected_tutorial(self):
        """Carga el tutorial seleccionado"""
        # Aquí se cargaría el contenido del tutorial específico
        tutorial_content = """
TUTORIAL SELECCIONADO
====================

Este es un tutorial interactivo. En la versión completa de SmartFit,
este contenido incluiría:

• Instrucciones paso a paso
• Capturas de pantalla interactivas
• Ejercicios prácticos
• Verificación de comprensión
• Consejos y trucos avanzados

Por el momento, consulta la documentación completa en las otras secciones
del centro de ayuda.
        """

        self.tutorial_text.delete(1.0, tk.END)
        self.tutorial_text.insert(1.0, tutorial_content)

    def send_contact_form(self):
        """Envía el formulario de contacto"""
        try:
            # Validar campos
            name = self.contact_vars["name"].get().strip()
            email = self.contact_vars["email"].get().strip()
            subject = self.contact_vars["subject"].get().strip()
            category = self.contact_vars["category"].get()
            message = self.message_text.get(1.0, tk.END).strip()

            if not all([name, email, subject, message]):
                messagebox.showerror("Error", "Todos los campos son obligatorios")
                return

            # Validar email básico
            if "@" not in email or "." not in email:
                messagebox.showerror("Error", "Por favor, introduce un email válido")
                return

            # Simular envío
            # En una implementación real, aquí se enviaría el email
            messagebox.showinfo(
                "Consulta Enviada",
                f"Tu consulta ha sido enviada correctamente.\n\n"
                f"Categoría: {category}\n"
                f"Asunto: {subject}\n\n"
                f"Recibirás una respuesta en las próximas 24-48 horas.",
            )

            # Limpiar formulario
            self.clear_contact_form()

        except Exception as e:
            messagebox.showerror("Error", f"Error al enviar la consulta: {e}")

    def clear_contact_form(self):
        """Limpia el formulario de contacto"""
        for key, var in self.contact_vars.items():
            var.set("")
        self.message_text.delete(1.0, tk.END)
        self.contact_vars["category"].set("General")
