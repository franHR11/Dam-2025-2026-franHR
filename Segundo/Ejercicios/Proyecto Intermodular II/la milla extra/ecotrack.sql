-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 09, 2025 at 06:42 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecotrack`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetMonthlyStats` (IN `user_id_param` INT)   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPendingAchievements` (IN `user_id_param` INT)   BEGIN
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
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `CalculateEcoLevel` (`co2_kg` DECIMAL(10,4)) RETURNS VARCHAR(20) CHARSET utf8mb3 COLLATE utf8mb3_spanish_ci DETERMINISTIC READS SQL DATA BEGIN
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
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `id` int NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Código único del logro',
  `name` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  `description` text COLLATE utf8mb4_spanish_ci,
  `icon` varchar(50) COLLATE utf8mb4_spanish_ci DEFAULT 'fas fa-trophy',
  `badge_color` varchar(20) COLLATE utf8mb4_spanish_ci DEFAULT '#22c55e',
  `condition_type` enum('calculations','co2_level','streak','consistency') COLLATE utf8mb4_spanish_ci NOT NULL,
  `condition_value` decimal(10,2) NOT NULL COMMENT 'Valor para desbloquear',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Catálogo de logros del sistema de gamificación';

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`id`, `code`, `name`, `description`, `icon`, `badge_color`, `condition_type`, `condition_value`, `is_active`, `created_at`) VALUES
(1, 'first_calculation', 'Primer Paso', 'Realizaste tu primer cálculo de huella ecológica', 'fas fa-footprints', '#22c55e', 'calculations', 1.00, 1, '2025-11-09 18:45:49'),
(2, 'week_warrior', 'Guerrero Semanal', 'Realizaste cálculos durante una semana completa', 'fas fa-calendar-week', '#3b82f6', 'calculations', 7.00, 1, '2025-11-09 18:45:49'),
(3, 'monthly_master', 'Maestro Mensual', 'Realizaste cálculos durante un mes completo', 'fas fa-calendar-alt', '#8b5cf6', 'calculations', 30.00, 1, '2025-11-09 18:45:49'),
(4, 'eco_hero', 'Héroe Ecológico', 'Alcanzaste una huella de carbono inferior a 3 kg CO2/día', 'fas fa-medal', '#10b981', 'co2_level', 3.00, 1, '2025-11-09 18:45:49'),
(5, 'eco_expert', 'Experto Ecológico', 'Mantuviste tu huella por debajo de 5 kg CO2/día durante una semana', 'fas fa-award', '#06b6d4', 'consistency', 5.00, 1, '2025-11-09 18:45:49'),
(6, 'green_streak', 'Racha Verde', 'Registraste hábitos durante 7 días seguidos', 'fas fa-fire', '#f59e0b', 'streak', 7.00, 1, '2025-11-09 18:45:49'),
(7, 'carbon_cutter', 'Cortador de Carbono', 'Reduciste tu huella en un 25% respecto a tu promedio', 'fas fa-chart-line', '#ef4444', 'co2_level', 25.00, 1, '2025-11-09 18:45:49'),
(8, 'sustainability_champion', 'Campeón de Sostenibilidad', 'Alcanzaste más de 100 cálculos totales', 'fas fa-crown', '#eab308', 'calculations', 100.00, 1, '2025-11-09 18:45:49');

-- --------------------------------------------------------

--
-- Table structure for table `eco_scores`
--

CREATE TABLE `eco_scores` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `habit_id` int DEFAULT NULL,
  `co2_kg` decimal(10,4) NOT NULL COMMENT 'Huella de carbono calculada en kg CO2',
  `transport_co2` decimal(10,4) DEFAULT '0.0000' COMMENT 'CO2 del transporte',
  `energy_co2` decimal(10,4) DEFAULT '0.0000' COMMENT 'CO2 del consumo energético',
  `diet_co2` decimal(10,4) DEFAULT '0.0000' COMMENT 'CO2 de la dieta',
  `recycling_reduction` decimal(10,4) DEFAULT '0.0000' COMMENT 'Reducción por reciclaje',
  `advice` text COLLATE utf8mb4_spanish_ci,
  `eco_level` enum('Eco Heroe','Eco Consciente','Eco Aprendiz','Eco Principiante') COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Puntuaciones de huella de carbono calculadas';

--
-- Dumping data for table `eco_scores`
--

INSERT INTO `eco_scores` (`id`, `user_id`, `habit_id`, `co2_kg`, `transport_co2`, `energy_co2`, `diet_co2`, `recycling_reduction`, `advice`, `eco_level`, `created_at`) VALUES
(1, 2, NULL, 7.9300, 0.0000, 0.0000, 0.0000, 0.0000, 'Tu huella de carbono es moderada. Pequeños cambios pueden marcar la diferencia. Intenta combinar diferentes medios de transporte más sostenibles. Considera incluir más días vegetarianos en tu dieta.', NULL, '2025-11-09 19:01:47');

-- --------------------------------------------------------

--
-- Table structure for table `habits`
--

CREATE TABLE `habits` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `transport` enum('coche','moto','transporte público','bicicleta','a pie') COLLATE utf8mb4_spanish_ci NOT NULL,
  `energy_use` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Consumo eléctrico mensual en kWh',
  `diet_type` enum('vegetariana','mixta','carnívora') COLLATE utf8mb4_spanish_ci NOT NULL,
  `recycling` tinyint(1) DEFAULT '0',
  `date_recorded` date NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Registros de hábitos ecológicos diarios de los usuarios';

--
-- Dumping data for table `habits`
--

INSERT INTO `habits` (`id`, `user_id`, `transport`, `energy_use`, `diet_type`, `recycling`, `date_recorded`, `created_at`) VALUES
(1, 2, 'coche', 300.00, 'mixta', 1, '2025-11-09', '2025-11-09 19:01:47');

-- --------------------------------------------------------

--
-- Stand-in structure for view `recent_achievements`
-- (See below for the actual view)
--
CREATE TABLE `recent_achievements` (
`achievement_description` text
,`achievement_id` int
,`achievement_name` varchar(100)
,`badge_color` varchar(20)
,`icon` varchar(50)
,`id` int
,`unlocked_at` datetime
,`user_id` int
,`user_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `recent_habits`
-- (See below for the actual view)
--
CREATE TABLE `recent_habits` (
`co2_kg` decimal(10,4)
,`created_at` datetime
,`date_recorded` date
,`diet_type` enum('vegetariana','mixta','carnívora')
,`eco_level` enum('Eco Heroe','Eco Consciente','Eco Aprendiz','Eco Principiante')
,`energy_use` decimal(10,2)
,`id` int
,`recycling` tinyint(1)
,`transport` enum('coche','moto','transporte público','bicicleta','a pie')
,`user_id` int
,`user_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_spanish_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Tabla principal de usuarios del sistema EcoTrack';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `updated_at`, `is_active`) VALUES
(1, 'Francisco Jose ', 'franhr1113@gmail.com', '$2y$10$JXjU8UWCMG47wAv/zqImkO5ev6be3rWrXEzzNSsBNBkYxDnw2wDlW', '2025-11-09 18:57:17', '2025-11-09 18:57:17', 1),
(2, 'Francisco jose', 'ibericawarez2@gmail.com', '$2y$10$P5U9Xv6h7BEz4F8sAFyP6O8CbDERCC/KvM.pLY8nQlgjxpO3f5YdS', '2025-11-09 18:58:49', '2025-11-09 18:58:49', 1);

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `create_user_preferences` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    INSERT INTO user_preferences (user_id)
    VALUES (NEW.id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_achievements`
--

CREATE TABLE `user_achievements` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `achievement_id` int NOT NULL,
  `unlocked_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Logros desbloqueados por cada usuario';

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `theme` enum('light','dark','auto') COLLATE utf8mb4_spanish_ci DEFAULT 'light',
  `language` varchar(5) COLLATE utf8mb4_spanish_ci DEFAULT 'es',
  `notifications_email` tinyint(1) DEFAULT '1',
  `notifications_reminders` tinyint(1) DEFAULT '1',
  `privacy_profile` enum('public','private') COLLATE utf8mb4_spanish_ci DEFAULT 'private',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Configuración personalizada de cada usuario';

--
-- Dumping data for table `user_preferences`
--

INSERT INTO `user_preferences` (`id`, `user_id`, `theme`, `language`, `notifications_email`, `notifications_reminders`, `privacy_profile`, `created_at`, `updated_at`) VALUES
(1, 1, 'light', 'es', 1, 1, 'private', '2025-11-09 18:57:17', '2025-11-09 18:57:17'),
(2, 2, 'light', 'es', 1, 1, 'private', '2025-11-09 18:58:49', '2025-11-09 18:58:49');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `session_token` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_spanish_ci,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Control de sesiones activas para seguridad';

--
-- Triggers `user_sessions`
--
DELIMITER $$
CREATE TRIGGER `cleanup_expired_sessions` BEFORE INSERT ON `user_sessions` FOR EACH ROW BEGIN
    DELETE FROM user_sessions
    WHERE expires_at < NOW();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `user_stats`
-- (See below for the actual view)
--
CREATE TABLE `user_stats` (
`achievements_count` bigint
,`avg_co2` decimal(14,8)
,`email` varchar(120)
,`last_calculation` datetime
,`max_co2` decimal(10,4)
,`min_co2` decimal(10,4)
,`name` varchar(100)
,`total_calculations` bigint
,`total_habits` bigint
,`user_id` int
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `eco_scores`
--
ALTER TABLE `eco_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_eco_scores_user_id` (`user_id`),
  ADD KEY `fk_eco_scores_habit_id` (`habit_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_co2_kg` (`co2_kg`),
  ADD KEY `idx_eco_scores_user_co2` (`user_id`,`co2_kg`),
  ADD KEY `idx_eco_scores_user_date` (`user_id`,`created_at` DESC);

--
-- Indexes for table `habits`
--
ALTER TABLE `habits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_habits_user_id` (`user_id`),
  ADD KEY `idx_date_recorded` (`date_recorded`),
  ADD KEY `idx_habits_user_date` (`user_id`,`date_recorded` DESC);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_achievement` (`user_id`,`achievement_id`),
  ADD KEY `fk_user_achievements_user_id` (`user_id`),
  ADD KEY `fk_user_achievements_achievement_id` (`achievement_id`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `fk_sessions_user_id` (`user_id`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `eco_scores`
--
ALTER TABLE `eco_scores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `habits`
--
ALTER TABLE `habits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_achievements`
--
ALTER TABLE `user_achievements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Structure for view `recent_achievements`
--
DROP TABLE IF EXISTS `recent_achievements`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `recent_achievements`  AS SELECT `ua`.`id` AS `id`, `ua`.`user_id` AS `user_id`, `ua`.`achievement_id` AS `achievement_id`, `ua`.`unlocked_at` AS `unlocked_at`, `u`.`name` AS `user_name`, `a`.`name` AS `achievement_name`, `a`.`description` AS `achievement_description`, `a`.`icon` AS `icon`, `a`.`badge_color` AS `badge_color` FROM ((`user_achievements` `ua` join `users` `u` on((`ua`.`user_id` = `u`.`id`))) join `achievements` `a` on((`ua`.`achievement_id` = `a`.`id`))) ORDER BY `ua`.`unlocked_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `recent_habits`
--
DROP TABLE IF EXISTS `recent_habits`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `recent_habits`  AS SELECT `h`.`id` AS `id`, `h`.`user_id` AS `user_id`, `h`.`transport` AS `transport`, `h`.`energy_use` AS `energy_use`, `h`.`diet_type` AS `diet_type`, `h`.`recycling` AS `recycling`, `h`.`date_recorded` AS `date_recorded`, `h`.`created_at` AS `created_at`, `u`.`name` AS `user_name`, `es`.`co2_kg` AS `co2_kg`, `es`.`eco_level` AS `eco_level` FROM ((`habits` `h` join `users` `u` on((`h`.`user_id` = `u`.`id`))) left join `eco_scores` `es` on((`h`.`id` = `es`.`habit_id`))) ORDER BY `h`.`date_recorded` DESC, `h`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `user_stats`
--
DROP TABLE IF EXISTS `user_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_stats`  AS SELECT `u`.`id` AS `user_id`, `u`.`name` AS `name`, `u`.`email` AS `email`, count(`es`.`id`) AS `total_calculations`, avg(`es`.`co2_kg`) AS `avg_co2`, min(`es`.`co2_kg`) AS `min_co2`, max(`es`.`co2_kg`) AS `max_co2`, count(distinct `h`.`id`) AS `total_habits`, max(`es`.`created_at`) AS `last_calculation`, (select count(0) from `user_achievements` `ua` where (`ua`.`user_id` = `u`.`id`)) AS `achievements_count` FROM ((`users` `u` left join `eco_scores` `es` on((`u`.`id` = `es`.`user_id`))) left join `habits` `h` on((`u`.`id` = `h`.`user_id`))) GROUP BY `u`.`id`, `u`.`name`, `u`.`email` ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `eco_scores`
--
ALTER TABLE `eco_scores`
  ADD CONSTRAINT `fk_eco_scores_habit_id` FOREIGN KEY (`habit_id`) REFERENCES `habits` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_eco_scores_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `habits`
--
ALTER TABLE `habits`
  ADD CONSTRAINT `fk_habits_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD CONSTRAINT `fk_user_achievements_achievement_id` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_achievements_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `fk_user_preferences_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `fk_sessions_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
