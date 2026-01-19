-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 13, 2026 at 06:06 AM
-- Server version: 8.0.44-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `weather_notification_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--

CREATE TABLE `alerts` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `city_id` int NOT NULL,
  `operator` enum('>','<','>=','<=','=') NOT NULL,
  `threshold_value` decimal(8,2) NOT NULL,
  `status` enum('active','paused','triggered') DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `unit` varchar(10) NOT NULL,
  `condition_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `alerts`
--

INSERT INTO `alerts` (`id`, `user_id`, `city_id`, `operator`, `threshold_value`, `status`, `created_at`, `updated_at`, `unit`, `condition_id`) VALUES
(1, 1, 2, '>', 30.00, 'active', '2026-01-12 16:56:45', '2026-01-12 16:56:45', '°C', 3),
(5, 1, 1001, '<', 10.00, 'active', '2026-01-13 09:56:24', '2026-01-13 09:56:24', '°C', 3),
(6, 1, 1002, '>', 40.00, 'active', '2026-01-13 10:08:15', '2026-01-13 10:08:15', 'mm', 3);

-- --------------------------------------------------------

--
-- Table structure for table `alert_conditions`
--

CREATE TABLE `alert_conditions` (
  `id` int NOT NULL,
  `code` varchar(50) NOT NULL,
  `label` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `alert_conditions`
--

INSERT INTO `alert_conditions` (`id`, `code`, `label`) VALUES
(1, 'temp_above', 'Temperature Above'),
(2, 'temp_below', 'Temperature Below'),
(3, 'rain', 'Rainfall'),
(4, 'storm', 'Storm'),
(5, 'wind', 'Wind Speed'),
(999, 'test_cond', 'Test Condition');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `country_code` varchar(10) NOT NULL,
  `country` varchar(100) NOT NULL,
  `lat` decimal(9,6) DEFAULT NULL,
  `lon` decimal(9,6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`, `country_code`, `country`, `lat`, `lon`) VALUES
(1, 'Test City 1768216692', 'XX', 'Unknown', 10.000000, 20.000000),
(2, 'Delhi', 'XX', 'Unknown', 28.651718, 77.221939),
(999, 'TestVerifyCity', 'TL', 'TestLand', 10.000000, 20.000000),
(1000, 'Paris', 'FR', 'FR', 48.858890, 2.320041),
(1001, 'Los Angeles', 'XX', 'Unknown', 34.053691, -118.242766),
(1002, 'Paris', 'XX', 'Unknown', 33.661796, -95.555513);

-- --------------------------------------------------------

--
-- Table structure for table `cron_logs`
--

CREATE TABLE `cron_logs` (
  `id` int NOT NULL,
  `job_name` varchar(100) NOT NULL,
  `status` enum('success','error') NOT NULL,
  `message` text,
  `executed_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `subscription_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `condition_type` enum('temperature','weather') NOT NULL,
  `actual_value` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `status` enum('sent','failed') DEFAULT 'sent',
  `sent_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------



-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `plan_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_gateway` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','success','failed') DEFAULT 'pending',
  `valid_from` date NOT NULL,
  `valid_till` date NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `billing_cycle` enum('monthly','yearly') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` int NOT NULL,
  `slug` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `monthly_price` decimal(10,2) NOT NULL,
  `yearly_price` decimal(10,2) NOT NULL,
  `trial_days` int DEFAULT '0',
  `is_popular` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `slug`, `name`, `description`, `monthly_price`, `yearly_price`, `trial_days`, `is_popular`, `is_active`, `created_at`) VALUES
(4, 'free', 'Free', 'Basic alerts with limited monitoring', 0.00, 0.00, 0, 0, 1, '2025-12-31 12:50:08'),
(5, 'pro', 'Pro', 'Advanced alerts for daily monitoring', 199.00, 1999.00, 7, 0, 1, '2025-12-31 12:50:08'),
(6, 'premium', 'Premium', 'Unlimited alerts with fastest updates', 499.00, 4999.00, 14, 0, 1, '2025-12-31 12:50:08');

-- --------------------------------------------------------

--
-- Table structure for table `plan_features`
--

CREATE TABLE `plan_features` (
  `id` int NOT NULL,
  `plan_id` int NOT NULL,
  `feature` varchar(255) NOT NULL,
  `is_available` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `plan_features`
--

INSERT INTO `plan_features` (`id`, `plan_id`, `feature`, `is_available`) VALUES
(31, 4, 'max_alerts=3', 1),
(32, 4, 'cities_limit=1', 1),
(33, 4, 'notification_channels=email', 1),
(34, 4, 'refresh_interval_minutes=60', 1),
(35, 4, 'auto_renew=false', 1),
(36, 5, 'max_alerts=10', 1),
(37, 5, 'cities_limit=5', 1),
(38, 5, 'notification_channels=email,sms', 1),
(39, 5, 'refresh_interval_minutes=15', 1),
(40, 5, 'auto_renew=true', 1),
(41, 6, 'max_alerts=50', 1),
(42, 6, 'cities_limit=unlimited', 1),
(43, 6, 'notification_channels=email,sms,push', 1),
(44, 6, 'refresh_interval_minutes=5', 1),
(45, 6, 'auto_renew=true', 1);

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `plan_id` int NOT NULL,
  `city` varchar(100) NOT NULL,
  `alert_type` enum('temp_above','temp_below','rain','storm','wind_above','humidity_above') DEFAULT NULL,
  `threshold_value` decimal(6,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `valid_from` date NOT NULL,
  `valid_till` date DEFAULT NULL,
  `last_checked_at` datetime DEFAULT NULL,
  `last_alert_sent_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `billing_cycle` enum('monthly','yearly') NOT NULL DEFAULT 'monthly',
  `status` enum('trial','active','expired','cancelled','pending_payment') NOT NULL DEFAULT 'active',
  `trial_end_date` date DEFAULT NULL,
  `auto_renew` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `current_plan_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `is_active`, `created_at`, `current_plan_id`) VALUES
(1, 'Test', 'test@gmail.com', '$2y$12$XSmukMawCX5pmfphTIeCbO2ZD5eNvOFxvR827hIgIBcXVDdncu.YW', 1, '2025-12-22 11:24:46', 1),
(2, 'Test1', 'test1@gmail.com', '$2y$12$Mgc6ry40/gNx7ALu4uxa3OCvp8hAIRJVJUr9uTYTiSlI.ZMiXfuse', 1, '2025-12-26 16:29:56', 1),
(3, 'qwerty', 'qwerty@gmail.com', '$2y$12$LgibHjbZ4VGHwla4BMWlJupalriSNzpPLjnULVPgaHupN1Wg/wgcS', 1, '2025-12-26 18:46:15', 1),
(4, 'Anni', 'anni@gmail.com', '$2y$12$GuWglBcCEiBtevwJi7rsU.b3sEtQ6UmJAnJEdVK29H6YIHpLnH8d2', 1, '2025-12-26 19:08:00', 1),
(5, 'Abcd', 'Abcd@gmail.com', '$2y$12$VNqHiBc5a8LVlhDsRi9mEeb3IkMLvgrfWYuyJxA1LgBwalB2QSc8.', 1, '2025-12-30 17:05:59', 0),
(6, 'Test', 'test2@gmail.com', '$2y$12$YVnkwBk.A4Id9rMBUEMsl.TsiKvr9AWZ6M4vQWHirFQ2xfAi8snB.', 1, '2025-12-31 13:20:11', 0),
(7, 'Abc', 'abc@gmail.com', '$2y$12$/82scWkfhJ.tf97coVr/NuLNAh3fIKFQYx6oHWhNr14TLcd5vzcY6', 1, '2025-12-31 15:01:30', 0),
(8, 'Test', 'test3@gmail.com', '$2y$12$mxE2ZEtOHDs1GdR9qJDn6ufnn.IseNagWi0kMkZODgyNVIy5tQpXW', NULL, '2025-12-31 16:21:25', NULL),
(9, 'Abcd', 'Abcde@gmail.com', '$2y$12$Wf3zn3oOV05/qDKeFzJTq.aJipkdIEq/Rqq/KcOpfnhR9PtxPqA9S', NULL, '2026-01-09 14:57:58', NULL);

-- --------------------------------------------------------


-- --------------------------------------------------------

--
-- Table structure for table `weather_latest`
--

CREATE TABLE `weather_latest` (
  `city_id` int NOT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `rainfall` varchar(50) DEFAULT NULL,
  `wind_speed` decimal(5,2) DEFAULT NULL,
  `uv_index` int DEFAULT NULL,
  `storm_status` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `weather_logs`
--

CREATE TABLE `weather_logs` (
  `id` int NOT NULL,
  `city` varchar(100) NOT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `feels_like` decimal(5,2) DEFAULT NULL,
  `humidity` int DEFAULT NULL,
  `pressure` int DEFAULT NULL,
  `weather_main` varchar(50) DEFAULT NULL,
  `weather_description` varchar(100) DEFAULT NULL,
  `wind_speed` decimal(5,2) DEFAULT NULL,
  `visibility` int DEFAULT NULL,
  `api_response` json DEFAULT NULL,
  `fetched_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alerts`
--
ALTER TABLE `alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_alert_user` (`user_id`),
  ADD KEY `fk_alert_city` (`city_id`),
  ADD KEY `fk_alert_condition` (`condition_id`);

--
-- Indexes for table `alert_conditions`
--
ALTER TABLE `alert_conditions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_city_country` (`name`,`country_code`);

--
-- Indexes for table `cron_logs`
--
ALTER TABLE `cron_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_subscription` (`subscription_id`),
  ADD KEY `idx_sent_at` (`sent_at`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payment_user` (`user_id`),
  ADD KEY `fk_payment_plan` (`plan_id`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `plan_features`
--
ALTER TABLE `plan_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plan_id` (`plan_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_city` (`city`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_active_subs` (`is_active`,`city`),
  ADD KEY `idx_user_subs` (`user_id`),
  ADD KEY `fk_subscription_plan` (`plan_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uniq_email` (`email`);


--
-- Indexes for table `weather_latest`
--
ALTER TABLE `weather_latest`
  ADD PRIMARY KEY (`city_id`);

--
-- Indexes for table `weather_logs`
--
ALTER TABLE `weather_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_city` (`city`),
  ADD KEY `idx_fetched` (`fetched_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alerts`
--
ALTER TABLE `alerts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `alert_conditions`
--
ALTER TABLE `alert_conditions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT for table `cron_logs`
--
ALTER TABLE `cron_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `plan_features`
--
ALTER TABLE `plan_features`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;


--
-- AUTO_INCREMENT for table `weather_logs`
--
ALTER TABLE `weather_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alerts`
--
ALTER TABLE `alerts`
  ADD CONSTRAINT `fk_alert_city` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`),
  ADD CONSTRAINT `fk_alert_condition` FOREIGN KEY (`condition_id`) REFERENCES `alert_conditions` (`id`),
  ADD CONSTRAINT `fk_alert_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notification_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_notification_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payment_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`),
  ADD CONSTRAINT `fk_payment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `plan_features`
--
ALTER TABLE `plan_features`
  ADD CONSTRAINT `plan_features_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `fk_subscription_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`),
  ADD CONSTRAINT `fk_subscription_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;


--
-- Constraints for table `weather_latest`
--
ALTER TABLE `weather_latest`
  ADD CONSTRAINT `fk_weather_city` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
