# 🌍 EcoTrack - Calculadora Ecológica Personal

## 📋 Descripción

**EcoTrack** es una aplicación web moderna que permite a los usuarios calcular, visualizar y reducir su huella ecológica (CO₂) a partir de sus hábitos diarios. El sistema utiliza un enfoque digital para promover la sostenibilidad ambiental y concienciar sobre el impacto personal en el medio ambiente.

## 🎯 Objetivos del Proyecto

1. **Digitalización Ecológica**: Reducir el consumo de papel mediante cálculos y reportes 100% digitales
2. **Conciencia Ambiental**: Educar a los usuarios sobre su impacto ecológico personal
3. **Motivación Gamificada**: Fomentar hábitos sostenibles mediante logros y recompensas
4. **Análisis de Datos**: Proporcionar visualizaciones claras del progreso ambiental
5. **Comunidad Verde**: Crear una red de usuarios comprometidos con la sostenibilidad

## 🏗️ Arquitectura del Sistema

### Patrón MVC
- **Modelos**: Gestión de datos y lógica de negocio
- **Vistas**: Presentación HTML con componentes reutilizables
- **Controladores**: Procesamiento de peticiones y coordinación

### Estructura de Directorios
```
EcoTrack/
├── index.php                 # Punto de entrada y sistema de rutas
├── .env                     # Variables de configuración
├── config/
│   └── database.php        # Configuración de base de datos
├── app/
│   ├── controllers/
│   │   ├── UserController.php
│   │   ├── HabitController.php
│   │   └── EcoController.php
│   ├── models/
│   │   ├── User.php
│   │   ├── Habit.php
│   │   └── EcoCalculator.php
│   └── views/
│       ├── layout/
│       │   ├── header.php
│       │   └── footer.php
│       ├── home.php
│       ├── login.php
│       ├── register.php
│       ├── dashboard.php
│       └── results.php
├── public/
│   ├── css/
│   │   ├── main.css
│   │   └── components/
│   ├── js/
│   │   ├── main.js
│   │   ├── api.js
│   │   └── chartHandler.js
│   ├── img/
│   └── icons/
└── docs/
    ├── README.md
    └── DB_STRUCTURE.sql
```

## 🗄️ Base de Datos

### Tablas Principales

#### `users`
Almacenamiento de información de usuarios con autenticación segura.
- Campos: id, name, email, password, created_at
- Seguridad: Hash de contraseñas con `password_hash()`

#### `habits`
Registro de hábitos ecológicos diarios.
- Campos: id, user_id, transport, energy_use, diet_type, recycling, date_recorded
- Tipos: ENUM para opciones predefinidas

#### `eco_scores`
Puntuaciones y cálculos de huella de carbono.
- Campos: id, user_id, co2_kg, advice, created_at
- Cálculos: Desglose por transporte, energía, dieta y reciclaje

#### `achievements`
Sistema de gamificación con logros desbloqueables.
- Campos: code, name, description, icon, condition_type, condition_value

### Vista General
```sql
CREATE VIEW user_stats AS
SELECT 
    u.id, u.name, u.email,
    COUNT(es.id) as total_calculations,
    AVG(es.co2_kg) as avg_co2,
    COUNT(ua.id) as achievements_count
FROM users u
LEFT JOIN eco_scores es ON u.id = es.user_id
LEFT JOIN user_achievements ua ON u.id = ua.user_id
GROUP BY u.id;
```

## 🧮 Lógica de Cálculo de CO₂

### Algoritmo Principal
```php
function calculateCO2($transport, $energy, $diet, $recycle) {
    $co2 = 0;
    
    // Transporte diario (kg CO2)
    switch($transport) {
        case 'coche': $co2 += 4.6; break;
        case 'moto': $co2 += 2.0; break;
        case 'transporte público': $co2 += 1.0; break;
        case 'bicicleta':
        case 'a pie': $co2 += 0.0; break;
    }
    
    // Energía eléctrica (mensual a diario)
    $daily_energy = $energy / 30;
    $co2 += $daily_energy * 0.233;
    
    // Dieta diaria (kg CO2)
    switch($diet) {
        case 'carnívora': $co2 += 3.0; break;
        case 'mixta': $co2 += 1.5; break;
        case 'vegetariana': $co2 += 0.8; break;
    }
    
    // Reducción por reciclaje
    if ($recycle) $co2 -= 0.5;
    
    return max($co2, 0);
}
```

### Niveles Ecológicos
- **Eco Héroe** (≤ 3 kg CO₂/día): Excelente impacto ambiental
- **Eco Consciente** (3-5 kg CO₂/día): Buen nivel de sostenibilidad
- **Eco Aprendiz** (5-7 kg CO₂/día): Margin de mejora notable
- **Eco Principiante** (> 7 kg CO₂/día): Necesita cambios significativos

## 🎨 Diseño y UX

### Principios de Diseño
- **Minimalismo**: Interfaz limpia y sin distracciones
- **Ecología**: Paleta de colores verdes y naturales
- **Accesibilidad**: Cumplimiento WCAG 2.1 AA
- **Responsive**: Adaptación a todos los dispositivos

### Sistema de Colores
```css
:root {
    --primary-color: #22c55e;      /* Verde principal */
    --secondary-color: #84cc16;     /* Lima secundario */
    --accent-color: #eab308;        /* Amarillo acento */
    --success-color: #10b981;       /* Verde éxito */
    --warning-color: #f59e0b;       /* Naranja advertencia */
    --error-color: #ef4444;         /* Rojo error */
}
```

### Componentes UI
- **Cards**: Presentación modular de información
- **Charts**: Visualizaciones interactivas con Chart.js
- **Forms**: Validación en tiempo real
- **Alerts**: Notificaciones contextuales
- **Progress**: Indicadores de progreso visual

## 🔧 Tecnologías Utilizadas

### Backend
- **PHP 8+**: Lenguaje principal del servidor
- **MySQL**: Sistema de gestión de bases de datos
- **PDO**: Abstracción de base de datos segura
- **Sessions**: Gestión de estado de usuario

### Frontend
- **HTML5**: Estructura semántica
- **CSS3**: Estilos con BEM y CSS Grid
- **JavaScript Vanilla**: Funcionalidad sin frameworks
- **Chart.js**: Visualización de datos
- **Font Awesome**: Iconografía

### Metodologías
- **BEM**: Block Element Modifier para CSS
- **MVC**: Model-View-Controller para PHP
- **REST**: Principios de API RESTful
- **Responsive First**: Diseño adaptativo

## 📊 Funcionalidades Principales

### 1. Gestión de Usuarios
- Registro con validación de email
- Login con hash seguro de contraseñas
- Perfil personalizado con estadísticas
- Preferencias configurables

### 2. Cálculo de Huella Ecológica
- Formulario intuitivo de hábitos
- Cálculo automático de CO₂
- Desglose por categorías
- Comparación temporal

### 3. Visualización de Datos
- Dashboard con estadísticas principales
- Gráficos de evolución mensual
- Comparativas con promedios
- Exportación de datos CSV

### 4. Sistema de Gamificación
- Logros desbloqueables
- Sistema de niveles ecológicos
- Récords personales
- Insignias visuales

### 5. Historial y Seguimiento
- Registro completo de cálculos
- Filtrado por fechas
- Tendencias y patrones
- Consejos personalizados

## 🔒 Seguridad

### Medidas Implementadas
- **Hashing**: Contraseñas con `password_hash()`
- **Prepared Statements**: Prevención de SQL Injection
- **XSS Protection**: Escaping de salida HTML
- **CSRF Tokens**: Protección en formularios
- **Session Management**: Configuración segura de sesiones
- **Input Validation**: Validación y sanitización de datos

### Recomendaciones Adicionales
- Implementar HTTPS obligatorio
- Rate limiting en login
- Auditoría de logs
- Política de contraseñas robusta
- Autenticación de dos factores

## 🚀 Instalación y Despliegue

### Requisitos Previos
- PHP 8.0 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Extensiones PHP: PDO, PDO_MySQL, mbstring

### Pasos de Instalación

1. **Clonar el Proyecto**
```bash
git clone <repository-url>
cd EcoTrack
```

2. **Configurar Base de Datos**
```sql
-- Importar el archivo SQL
mysql -u root -p < docs/DB_STRUCTURE.sql
```

3. **Configurar Variables de Entorno**
```bash
cp .env.example .env
# Editar .env con credenciales correctas
```

4. **Configurar Servidor Web**
- Apuntar document root a la carpeta del proyecto
- Configurar VirtualHost
- Habilitar mod_rewrite (Apache)

5. **Verificar Instalación**
- Acceder a `http://localhost/EcoTrack`
- Crear cuenta de usuario
- Realizar primer cálculo de huella ecológica

### Configuración Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

## 🧪 Testing

### Pruebas Funcionales
- Registro y login de usuarios
- Cálculo de huella ecológica
- Generación de estadísticas
- Desbloqueo de logros
- Exportación de datos

### Pruebas de Rendimiento
- Tiempo de respuesta < 2 segundos
- Carga concurrente de 100 usuarios
- Optimización de consultas SQL
- Compresión de assets

### Validación
- HTML5 W3C Validator
- CSS3 Validator
- Accesibilidad WCAG 2.1
- Cross-browser testing

## 📱 Compatibilidad

### Navegadores Soportados
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

### Dispositivos
- Desktop (1024px+)
- Tablet (768px-1023px)
- Mobile (320px-767px)

## 🔄 Mantenimiento

### Tareas Programadas
- Limpieza de sesiones expiradas
- Backup de base de datos
- Actualización de logros
- Optimización de tablas

### Monitorización
- Logs de errores PHP
- Métricas de rendimiento
- Uso de recursos
- Análisis de tráfico

## 🌱 Extensiones Futuras

### Version 2.0
- **API REST**: Para aplicaciones móviles
- **Modo Oscuro**: Tema oscuro opcional
- **Ranking Global**: Comparación entre usuarios
- **Integración IoT**: Datos de dispositivos inteligentes
- **Machine Learning**: Predicciones personalizadas

### Funcionalidades Adicionales
- Integración con APIs ambientales reales
- Panel administrativo avanzado
- Sistema de newsletters
- Comunidad y social features
- Análisis predictivo
- Gamificación extendida

## 📄 Licencia

Este proyecto está licenciado bajo la Licencia MIT. Ver archivo `LICENSE` para más detalles.

## 👥 Créditos

- **Desarrollo Principal**: Fran
- **Diseño UI/UX**: Equipo de diseño
- **Asignaturas Relacionadas**:
  - Proyecto Intermodular
  - Digitalización
  - Sostenibilidad
  - Desarrollo Web

## 📞 Soporte

Para reportar problemas o solicitar características:
- Crear issue en el repositorio
- Enviar correo a: support@ecotrack.com
- Documentación completa en: `docs/`

## 🌍 Impacto Ambiental

EcoTrack contribuye a la digitalización sostenible mediante:
- Reducción del consumo de papel
- Concienciación ambiental
- Fomento de hábitos sostenibles
- Creación de comunidad verde
- Educación en sostenibilidad

**Objetivo**: Cambiar el mundo con tecnología responsable 🌍

---

*"La sostenibilidad no es una elección, es nuestra responsabilidad con las futuras generaciones."*