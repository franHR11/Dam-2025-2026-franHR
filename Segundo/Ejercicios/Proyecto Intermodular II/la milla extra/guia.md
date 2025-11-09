Excelente elección — EcoTrack encaja perfectamente con programación + digitalización + sostenibilidad, y tu idea está muy bien enfocada.
A continuación te dejo una guía completa en formato Markdown (GUIDE_ECOTRACK.md), lista para que cualquier IA o desarrollador pueda construir el proyecto desde cero siguiendo una arquitectura vanilla escalable con PHP, HTML, CSS y JS, con un enfoque limpio, moderno y sostenible.

# 🌍 Proyecto: EcoTrack — Calculadora ecológica personal

## 🧩 Descripción general
**EcoTrack** es una web que permite a los usuarios **calcular, visualizar y reducir su huella ecológica** (CO₂) a partir de sus hábitos diarios.  
El sistema utiliza tecnologías **vanilla** (PHP, HTML, CSS, JS) y un **diseño modular escalable** con patrón MVC simple.

El objetivo es **digitalizar la conciencia ambiental**, ofreciendo estadísticas, consejos personalizados y gamificación ecológica.

---

## 🎯 Objetivos principales
1. Promover hábitos sostenibles mediante una web accesible y visual.
2. Reducir el consumo de papel gracias a la digitalización de los cálculos y reportes.
3. Motivar a los usuarios a mejorar su impacto ambiental mediante estadísticas y logros.
4. Integrar conceptos de **programación, bases de datos, diseño web, digitalización y sostenibilidad**.

---

## 🏗️ Estructura del proyecto



EcoTrack/
│
├── index.php
├── config/
│ └── database.php
│
├── app/
│ ├── controllers/
│ │ ├── UserController.php
│ │ ├── HabitController.php
│ │ └── EcoController.php
│ │
│ ├── models/
│ │ ├── User.php
│ │ ├── Habit.php
│ │ └── EcoCalculator.php
│ │
│ └── views/
│ ├── layout/
│ │ ├── header.php
│ │ ├── footer.php
│ │ └── navbar.php
│ │
│ ├── home.php
│ ├── login.php
│ ├── register.php
│ ├── dashboard.php
│ └── results.php
│
├── public/
│ ├── css/
│ │ ├── main.css
│ │ └── components/
│ │ ├── buttons.css
│ │ ├── forms.css
│ │ └── charts.css
│ │
│ ├── js/
│ │ ├── main.js
│ │ ├── api.js
│ │ ├── chartHandler.js
│ │ └── helpers/
│ │ └── validator.js
│ │
│ ├── img/
│ └── icons/
│
├── docs/
│ ├── README.md
│ ├── DB_STRUCTURE.md
│ └── API_GUIDE.md
│
└── .env


---

## 🧱 Arquitectura
Patrón de diseño **MVC simple (Modelo - Vista - Controlador)** con **rutas básicas en PHP**.

- **Modelos** → gestionan datos y lógica (usuarios, hábitos, cálculos).  
- **Controladores** → procesan peticiones y renderizan vistas.  
- **Vistas** → HTML estructurado con componentes reusables.  
- **Assets** (CSS/JS) → organizados por módulos y componentes.

---

## 🧮 Base de datos (MySQL)

**Nombre:** `ecotrack_db`

### Tablas principales

#### `users`
| Campo      | Tipo         | Descripción         |
| ---------- | ------------ | ------------------- |
| id         | INT (PK, AI) | Identificador único |
| name       | VARCHAR(100) | Nombre del usuario  |
| email      | VARCHAR(120) | Correo electrónico  |
| password   | VARCHAR(255) | Contraseña hasheada |
| created_at | DATETIME     | Fecha de registro   |

#### `habits`
| Campo         | Tipo                                                              | Descripción                     |
| ------------- | ----------------------------------------------------------------- | ------------------------------- |
| id            | INT (PK, AI)                                                      | Identificador del hábito        |
| user_id       | INT (FK → users.id)                                               | Usuario propietario             |
| transport     | ENUM('coche', 'moto', 'transporte público', 'bicicleta', 'a pie') | Medio de transporte             |
| energy_use    | FLOAT                                                             | Consumo eléctrico mensual (kWh) |
| diet_type     | ENUM('vegetariana', 'mixta', 'carnívora')                         | Tipo de dieta                   |
| recycling     | BOOLEAN                                                           | Si recicla o no                 |
| date_recorded | DATE                                                              | Fecha del registro              |

#### `eco_scores`
| Campo      | Tipo                | Descripción                 |
| ---------- | ------------------- | --------------------------- |
| id         | INT (PK, AI)        | Identificador               |
| user_id    | INT (FK → users.id) | Usuario asociado            |
| co2_kg     | FLOAT               | Huella de carbono calculada |
| advice     | TEXT                | Consejos personalizados     |
| created_at | DATETIME            | Fecha del cálculo           |

---

## 🔢 Lógica de cálculo (simplificada)
Ejemplo básico para `EcoCalculator.php`:

```php
function calculateCO2($transport, $energy, $diet, $recycle) {
    $co2 = 0;

    switch($transport) {
        case 'coche': $co2 += 4.6; break;
        case 'moto': $co2 += 2.0; break;
        case 'transporte público': $co2 += 1.0; break;
        case 'bicicleta':
        case 'a pie': $co2 += 0.0; break;
    }

    $co2 += $energy * 0.233;

    switch($diet) {
        case 'carnívora': $co2 += 3.0; break;
        case 'mixta': $co2 += 1.5; break;
        case 'vegetariana': $co2 += 0.8; break;
    }

    if ($recycle) $co2 -= 0.5;

    return max($co2, 0);
}

🎨 Diseño visual

Estilo: limpio, ecológico y moderno.

Paleta: tonos verdes, blancos y suaves grises.

Fuentes: Google Fonts → Poppins o Inter.

Iconos: FontAwesome
 o Lucide
.

Gráficos: Chart.js
.

Estructura CSS: BEM (Block Element Modifier).

Ejemplo BEM:

.card { ... }
.card__title { ... }
.card--eco { background: #c9f5c9; }

📊 Funcionalidades clave
Módulo	Descripción
🔐 Autenticación	Registro y login de usuarios (hash de contraseñas con password_hash).
📋 Formulario de hábitos	Recoge información de transporte, energía, dieta y reciclaje.
⚙️ Cálculo de CO₂	Lógica PHP modular en EcoCalculator.
📈 Panel de resultados	Gráfico de huella ecológica con Chart.js.
🧠 Consejos personalizados	Mensajes adaptados al resultado del usuario.
🏅 Sistema de logros	Desbloqueo de insignias según mejoras.
📅 Historial	Consulta de cálculos pasados con fecha y valores.
🧰 Tecnologías utilizadas
Tecnología	Uso
PHP 8+	Backend y lógica de negocio
MySQL	Base de datos
HTML5 / CSS3 / JS Vanilla	Frontend
Chart.js	Visualización de gráficos
FontAwesome / Lucide	Iconografía
BEM CSS	Organización visual
Pattern MVC	Estructura escalable y modular
🌱 Extensiones futuras

Integración con APIs de emisiones reales.

Modo oscuro.

Ranking global de usuarios.

API REST para conectar una app móvil.

Panel administrativo para ver estadísticas globales.

🧾 Créditos y autoría

Autor del proyecto original: Fran
IA diseñadora de la guía: GPT-5
Asignaturas relacionadas:

Proyecto Intermodular

Digitalización

Sostenibilidad
Objetivo: Cambiar el mundo con tecnología responsable 🌍

🚀 Instrucciones de despliegue local

Instalar Apache + MySQL (XAMPP o similar).

Clonar el proyecto en la carpeta htdocs.

Crear la base de datos ecotrack_db e importar docs/DB_STRUCTURE.md.

Configurar credenciales en .env.

Iniciar el servidor y acceder desde http://localhost/EcoTrack.

✅ Conclusión

EcoTrack es un proyecto educativo y funcional que combina programación, digitalización y sostenibilidad, demostrando cómo una web puede ayudar a concienciar, medir y reducir el impacto ambiental de cada persona.
Su estructura escalable permite ampliarlo fácilmente con nuevas funciones, manteniendo la simplicidad del stack vanilla PHP + JS + MySQL.