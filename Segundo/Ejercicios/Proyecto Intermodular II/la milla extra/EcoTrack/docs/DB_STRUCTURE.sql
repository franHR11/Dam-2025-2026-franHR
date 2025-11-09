-- Base de datos: EcoTrack
-- Sistema de cálculo y seguimiento de huella ecológica personal

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS `ecotrack_db`
CHARACTER SET utf8mb4
COLLATE utf8mb4_spanish_ci;

-- Usar la base de datos
USE `ecotrack_db`;

-- Tabla de usuarios
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Tabla de hábitos ecológicos
CREATE TABLE `habits` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `transport` ENUM('coche', 'moto', 'transporte público', 'bicicleta', 'a pie') NOT NULL,
  `energy_use` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Consumo eléctrico mensual en kWh',
  `diet_type` ENUM('vegetariana', 'mixta', 'carnívora') NOT NULL,
  `recycling` BOOLEAN DEFAULT FALSE,
  `date_recorded` DATE NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_habits_user_id` (`user_id`),
  KEY `idx_date_recorded` (`date_recorded`),
  CONSTRAINT `fk_habits_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Tabla de puntuaciones ecológicas
CREATE TABLE `eco_scores` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `habit_id` INT(11) DEFAULT NULL,
  `co2_kg` DECIMAL(10,4) NOT NULL COMMENT 'Huella de carbono calculada en kg CO2',
  `transport_co2` DECIMAL(10,4) DEFAULT 0.00 COMMENT 'CO2 del transporte',
  `energy_co2` DECIMAL(10,4) DEFAULT 0.00 COMMENT 'CO2 del consumo energético',
  `diet_co2` DECIMAL(10,4) DEFAULT 0.00 COMMENT 'CO2 de la dieta',
  `recycling_reduction` DECIMAL(10,4) DEFAULT 0.00 COMMENT 'Reducción por reciclaje',
  `advice` TEXT,
  `eco_level` ENUM('Eco Heroe', 'Eco Consciente', 'Eco Aprendiz', 'Eco Principiante') DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_eco_scores_user_id` (`user_id`),
  KEY `fk_eco_scores_habit_id` (`habit_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_co2_kg` (`co2_kg`),
  CONSTRAINT `fk_eco_scores_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_eco_scores_habit_id` FOREIGN KEY (`habit_id`) REFERENCES `habits` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Tabla de logros y recompensas
CREATE TABLE `achievements` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) NOT NULL COMMENT 'Código único del logro',
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `icon` VARCHAR(50) DEFAULT 'fas fa-trophy',
  `badge_color` VARCHAR(20) DEFAULT '#22c55e',
  `condition_type` ENUM('calculations', 'co2_level', 'streak', 'consistency') NOT NULL,
  `condition_value` DECIMAL(10,2) NOT NULL COMMENT 'Valor para desbloquear',
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Tabla de logros desbloqueados por usuarios
CREATE TABLE `user_achievements` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `achievement_id` INT(11) NOT NULL,
  `unlocked_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_achievement` (`user_id`, `achievement_id`),
  KEY `fk_user_achievements_user_id` (`user_id`),
  KEY `fk_user_achievements_achievement_id` (`achievement_id`),
  CONSTRAINT `fk_user_achievements_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_achievements_achievement_id` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Tabla de preferencias de usuario
CREATE TABLE `user_preferences` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `theme` ENUM('light', 'dark', 'auto') DEFAULT 'light',
  `language` VARCHAR(5) DEFAULT 'es',
  `notifications_email` BOOLEAN DEFAULT TRUE,
  `notifications_reminders` BOOLEAN DEFAULT TRUE,
  `privacy_profile` ENUM('public', 'private') DEFAULT 'private',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `fk_user_preferences_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Tabla de sesiones (opcional, para mayor seguridad)
CREATE TABLE `user_sessions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `session_token` VARCHAR(255) NOT NULL,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `expires_at` DATETIME NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_token` (`session_token`),
  KEY `fk_sessions_user_id` (`user_id`),
  KEY `idx_expires_at` (`expires_at`),
  CONSTRAINT `fk_sessions_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Insertar logros predeterminados
INSERT INTO `achievements` (`code`, `name`, `description`, `icon`, `badge_color`, `condition_type`, `condition_value`) VALUES
('first_calculation', 'Primer Paso', 'Realizaste tu primer cálculo de huella ecológica', 'fas fa-footprints', '#22c55e', 'calculations', 1),
('week_warrior', 'Guerrero Semanal', 'Realizaste cálculos durante una semana completa', 'fas fa-calendar-week', '#3b82f6', 'calculations', 7),
('monthly_master', 'Maestro Mensual', 'Realizaste cálculos durante un mes completo', 'fas fa-calendar-alt', '#8b5cf6', 'calculations', 30),
('eco_hero', 'Héroe Ecológico', 'Alcanzaste una huella de carbono inferior a 3 kg CO2/día', 'fas fa-medal', '#10b981', 'co2_level', 3),
('eco_expert', 'Experto Ecológico', 'Mantuviste tu huella por debajo de 5 kg CO2/día durante una semana', 'fas fa-award', '#06b6d4', 'consistency', 5),
('green_streak', 'Racha Verde', 'Registraste hábitos durante 7 días seguidos', 'fas fa-fire', '#f59e0b', 'streak', 7),
('carbon_cutter', 'Cortador de Carbono', 'Reduciste tu huella en un 25% respecto a tu promedio', 'fas fa-chart-line', '#ef4444', 'co2_level', 25),
('sustainability_champion', 'Campeón de Sostenibilidad', 'Alcanzaste más de 100 cálculos totales', 'fas fa-crown', '#eab308', 'calculations', 100);

-- Crear vistas para consultas comunes

-- Vista para estadísticas resumidas por usuario
CREATE VIEW `user_stats` AS
SELECT
    u.id as user_id,
    u.name,
    u.email,
    COUNT(es.id) as total_calculations,
    AVG(es.co2_kg) as avg_co2,
    MIN(es.co2_kg) as min_co2,
    MAX(es.co2_kg) as max_co2,
    COUNT(DISTINCT h.id) as total_habits,
    MAX(es.created_at) as last_calculation,
    (SELECT COUNT(*) FROM user_achievements ua WHERE ua.user_id = u.id) as achievements_count
FROM users u
LEFT JOIN eco_scores es ON u.id = es.user_id
LEFT JOIN habits h ON u.id = h.user_id
GROUP BY u.id, u.name, u.email;

-- Vista para últimos registros de hábitos
CREATE VIEW `recent_habits` AS
SELECT
    h.*,
    u.name as user_name,
    es.co2_kg,
    es.eco_level
FROM habits h
JOIN users u ON h.user_id = u.id
LEFT JOIN eco_scores es ON h.id = es.habit_id
ORDER BY h.date_recorded DESC, h.created_at DESC;

-- Vista para logros recientes
CREATE VIEW `recent_achievements` AS
SELECT
    ua.*,
    u.name as user_name,
    a.name as achievement_name,
    a.description as achievement_description,
    a.icon,
    a.badge_color
FROM user_achievements ua
JOIN users u ON ua.user_id = u.id
JOIN achievements a ON ua.achievement_id = a.id
ORDER BY ua.unlocked_at DESC;

-- Índices adicionales para mejorar el rendimiento
CREATE INDEX idx_habits_user_date ON habits(user_id, date_recorded DESC);
CREATE INDEX idx_eco_scores_user_co2 ON eco_scores(user_id, co2_kg);
CREATE INDEX idx_eco_scores_user_date ON eco_scores(user_id, created_at DESC);

-- Procedimiento almacenado para calcular estadísticas mensuales
DELIMITER //
CREATE PROCEDURE GetMonthlyStats(IN user_id_param INT)
BEGIN
    SELECT
        DATE_FORMAT(created_at, '%Y-%m') as month,
        AVG(co2_kg) as avg_co2,
        MIN(co2_kg) as min_co2,
        MAX(co2_kg) as max_co2,
        COUNT(*) as calculations
    FROM eco_scores
    WHERE user_id = user_id_param
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12;
END //
DELIMITER ;

-- Procedimiento para obtener logros pendientes
DELIMITER //
CREATE PROCEDURE GetPendingAchievements(IN user_id_param INT)
BEGIN
    SELECT
        a.*,
        CASE
            WHEN a.condition_type = 'calculations' THEN
                (SELECT COUNT(*) FROM eco_scores WHERE user_id = user_id_param)
            WHEN a.condition_type = 'co2_level' THEN
                (SELECT MIN(co2_kg) FROM eco_scores WHERE user_id = user_id_param)
            WHEN a.condition_type = 'streak' THEN
                (SELECT COUNT(DISTINCT DATE(created_at))
                 FROM eco_scores
                 WHERE user_id = user_id_param
                 AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY))
            ELSE 0
        END as current_value
    FROM achievements a
    WHERE a.is_active = TRUE
    AND a.id NOT IN (
        SELECT achievement_id
        FROM user_achievements
        WHERE user_id = user_id_param
    );
END //
DELIMITER ;

-- Trigger para actualizar preferencias cuando se crea un usuario
DELIMITER //
CREATE TRIGGER create_user_preferences
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    INSERT INTO user_preferences (user_id)
    VALUES (NEW.id);
END //
DELIMITER ;

-- Trigger para limpiar sesiones expiradas
DELIMITER //
CREATE TRIGGER cleanup_expired_sessions
BEFORE INSERT ON user_sessions
FOR EACH ROW
BEGIN
    DELETE FROM user_sessions
    WHERE expires_at < NOW();
END //
DELIMITER ;

-- Función para calcular nivel ecológico
DELIMITER //
CREATE FUNCTION CalculateEcoLevel(co2_kg DECIMAL(10,4))
RETURNS VARCHAR(20)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE eco_level VARCHAR(20);

    IF co2_kg <= 3 THEN
        SET eco_level = 'Eco Heroe';
    ELSEIF co2_kg <= 5 THEN
        SET eco_level = 'Eco Consciente';
    ELSEIF co2_kg <= 7 THEN
        SET eco_level = 'Eco Aprendiz';
    ELSE
        SET eco_level = 'Eco Principiante';
    END IF;

    RETURN eco_level;
END //
DELIMITER ;

-- Comentarios para documentación
ALTER TABLE `users` COMMENT = 'Tabla principal de usuarios del sistema EcoTrack';
ALTER TABLE `habits` COMMENT = 'Registros de hábitos ecológicos diarios de los usuarios';
ALTER TABLE `eco_scores` COMMENT = 'Puntuaciones de huella de carbono calculadas';
ALTER TABLE `achievements` COMMENT = 'Catálogo de logros del sistema de gamificación';
ALTER TABLE `user_achievements` COMMENT = 'Logros desbloqueados por cada usuario';
ALTER TABLE `user_preferences` COMMENT = 'Configuración personalizada de cada usuario';
ALTER TABLE `user_sessions` COMMENT = 'Control de sesiones activas para seguridad';

-- Nota: Este script crea una estructura completa y optimizada para EcoTrack
-- Incluye tablas principales, logros, preferencias, sesiones y elementos avanzados
-- como vistas, procedimientos almacenados, triggers y funciones para mayor funcionalidad.
