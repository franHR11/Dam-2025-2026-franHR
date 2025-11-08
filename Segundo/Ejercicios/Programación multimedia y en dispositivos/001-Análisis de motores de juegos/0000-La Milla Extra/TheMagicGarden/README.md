# The Magic Garden - Jardín Mágico Interactivo

## Descripción del proyecto

"The Magic Garden" es una aplicación multimedia interactiva desarrollada en Python con Pygame que simula un jardín mágico donde las flores crecen, las mariposas vuelan y las luces flotantes crean un ambiente encantado.

Este proyecto demuestra el uso de librerías multimedia para crear animaciones 2D, gestionar eventos de usuario, reproducir sonido y controlar estados de ejecución.

## Características principales

### 🌸 Flores animadas
- Crecimiento progresivo cuando el usuario hace clic en ellas
- Animación de balanceo natural
- Diferentes colores y tamaños aleatorios
- Tallos y hojas que crecen junto con la flor

### 🦋 Mariposas voladoras
- Movimiento sinusoidal suave y realista
- Animación de alas con parpadeo
- Colores aleatorios para cada mariposa
- Rebote en los bordes de la pantalla

### ✨ Efectos de luz mágica
- Luces flotantes con transparencia
- Parpadeo y pulso suave
- Movimiento horizontal automático
- Efecto de brillo con múltiples capas

### 🎵 Sistema de audio
- Música ambiental generada programáticamente
- Efectos de sonido al interactuar
- Control de volumen
- Cambios según modo día/noche

### 🎮 Controles del usuario
- **ESPACIO**: Pausar/reanudar el juego
- **D**: Cambiar entre día y noche
- **R**: Reiniciar la escena
- **Clic del ratón**: Hacer crecer las flores
- **ESC**: Salir del juego

## Instalación y ejecución

### Requisitos
- Python 3.7 o superior
- Pygame 2.1.0 o superior
- NumPy 1.21.0 o superior

### Instalación de dependencias
```bash
pip install -r requirements.txt
```

### Ejecución del juego
```bash
cd src
python main.py
```

## Estructura del proyecto

```
TheMagicGarden/
├── src/
│   ├── main.py              # Archivo principal del juego
│   ├── garden_scene.py      # Gestión de la escena principal
│   ├── flower.py            # Clase Flower para las flores
│   ├── butterfly.py         # Clase Butterfly para mariposas
│   ├── light_effect.py      # Clase LightEffect para luces
│   └── audio_manager.py     # Gestión de audio
├── assets/                  # Recursos multimedia
│   ├── flowers/            # Imágenes de flores
│   ├── butterflies/        # Imágenes de mariposas
│   ├── sounds/             # Efectos de sonido
│   └── music/              # Música de fondo
├── requirements.txt         # Dependencias del proyecto
└── README.md               # Este archivo
```

## Unidades y subunidades cubiertas

### Unidad: Utilización de librerías multimedia integradas
- **Subunidad: Conceptos sobre aplicaciones multimedia**
  - Integración de audio, gráficos y eventos en una aplicación coherente
  - Análisis del API de Pygame y sus componentes principales

- **Subunidad: Arquitectura del API utilizado**
  - Implementación orientada a objetos con clases y herencia
  - Uso de sprites y superficies de Pygame

### Unidad: Animación de objetos
- **Subunidad: Procesamiento de objetos multimedia**
  - Estados de objetos: inactivo, activo, creciendo
  - Métodos update() y draw() para control frame a frame

- **Subunidad: Reproducción de objetos multimedia**
  - Control de música y efectos de sonido con pygame.mixer
  - Estados de reproducción: play, pause, stop

- **Subunidad: Animación de objetos**
  - Uso de temporizadores y control de FPS
  - Animaciones por transformación: escalado, rotación, movimiento

### Unidad: Análisis de motores de juegos (introducción a 2D)
- **Subunidad: Arquitectura del juego. Componentes**
  - Bucle principal de juego (game loop)
  - Jerarquía: Main → Scene → Objects

- **Subunidad: Animación 2D y librerías utilizadas**
  - Renderizado por superficie
  - Control del tiempo y eventos
  - Capas de sprites

- **Subunidad: Análisis de ejecución. Optimización**
  - Control de FPS a 60 para rendimiento estable
  - Reutilización de recursos en memoria

### Unidad: Desarrollo de juegos 2D (base técnica)
- **Subunidad: Fases de desarrollo**
  - Diseño, carga de recursos, creación de clases
  - Animación, control de eventos y optimización

- **Subunidad: Componentes físicos y visuales**
  - Movimiento con funciones trigonométricas
  - Propiedades visuales: tamaño, color, transparencia

- **Subunidad: Audio y efectos**
  - Sonido ambiental y efectos interactivos
  - Control de volumen y estados

- **Subunidad: Cámaras e iluminación**
  - Efectos de iluminación simulada
  - Transiciones de color según hora del día

## Aspectos técnicos destacados

### Rendimiento y optimización
- Limitación a 60 FPS para mantener fluidez
- Reutilización de superficies y objetos
- Dibujado eficiente con capas ordenadas

### Interactividad
- Sistema completo de eventos de teclado y ratón
- Respuesta inmediata a acciones del usuario
- Estados de pausa y reinicio

### Diseño modular
- Clases independientes con responsabilidades claras
- Separación entre lógica y renderizado
- Fácil extensión y mantenimiento

## Autor

**Fran** - Desarrollo de Aplicaciones Multimedia y en Dispositivos

Este proyecto representa una práctica completa del temario visto en clase, demostrando el dominio de conceptos multimedia, animación 2D y desarrollo de juegos.