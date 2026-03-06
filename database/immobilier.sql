-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Mar 06, 2026 at 03:27 PM
-- Server version: 8.0.44
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `immobilier`
--

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` bigint UNSIGNED NOT NULL,
  `property_id` bigint UNSIGNED NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint UNSIGNED DEFAULT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_cover` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` smallint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `property_id`, `path`, `disk`, `original_name`, `size`, `mime_type`, `is_cover`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'properties/seed_1_1.jpg', 'public', 'placeholder_1.jpg', 10254, 'image/jpeg', 1, 1, '2026-03-05 21:43:28', '2026-03-05 21:43:28'),
(2, 1, 'properties/seed_1_2.jpg', 'public', 'placeholder_2.jpg', 10307, 'image/jpeg', 0, 2, '2026-03-05 21:43:28', '2026-03-05 21:43:28'),
(3, 1, 'properties/seed_1_3.jpg', 'public', 'placeholder_3.jpg', 10333, 'image/jpeg', 0, 3, '2026-03-05 21:43:28', '2026-03-05 21:43:28'),
(4, 2, 'properties/seed_2_1.jpg', 'public', 'placeholder_1.jpg', 10795, 'image/jpeg', 1, 1, '2026-03-05 21:43:29', '2026-03-05 21:43:29'),
(5, 2, 'properties/seed_2_2.jpg', 'public', 'placeholder_2.jpg', 10840, 'image/jpeg', 0, 2, '2026-03-05 21:43:29', '2026-03-05 21:43:29'),
(6, 2, 'properties/seed_2_3.jpg', 'public', 'placeholder_3.jpg', 10765, 'image/jpeg', 0, 3, '2026-03-05 21:43:29', '2026-03-05 21:43:29'),
(7, 3, 'properties/seed_3_1.jpg', 'public', 'placeholder_1.jpg', 11120, 'image/jpeg', 1, 1, '2026-03-05 21:43:29', '2026-03-05 21:43:29'),
(8, 3, 'properties/seed_3_2.jpg', 'public', 'placeholder_2.jpg', 11050, 'image/jpeg', 0, 2, '2026-03-05 21:43:29', '2026-03-05 21:43:29'),
(9, 3, 'properties/seed_3_3.jpg', 'public', 'placeholder_3.jpg', 11133, 'image/jpeg', 0, 3, '2026-03-05 21:43:29', '2026-03-05 21:43:29'),
(10, 4, 'properties/seed_4_1.jpg', 'public', 'placeholder_1.jpg', 10512, 'image/jpeg', 1, 1, '2026-03-05 21:43:29', '2026-03-05 21:43:29'),
(11, 4, 'properties/seed_4_2.jpg', 'public', 'placeholder_2.jpg', 10590, 'image/jpeg', 0, 2, '2026-03-05 21:43:29', '2026-03-05 21:43:29'),
(12, 4, 'properties/seed_4_3.jpg', 'public', 'placeholder_3.jpg', 10505, 'image/jpeg', 0, 3, '2026-03-05 21:43:29', '2026-03-05 21:43:29'),
(13, 5, 'properties/seed_5_1.jpg', 'public', 'placeholder_1.jpg', 10484, 'image/jpeg', 1, 1, '2026-03-05 21:43:29', '2026-03-05 21:43:29'),
(14, 5, 'properties/seed_5_2.jpg', 'public', 'placeholder_2.jpg', 10417, 'image/jpeg', 0, 2, '2026-03-05 21:43:29', '2026-03-05 21:43:29'),
(15, 5, 'properties/seed_5_3.jpg', 'public', 'placeholder_3.jpg', 10469, 'image/jpeg', 0, 3, '2026-03-05 21:43:29', '2026-03-05 21:43:29'),
(16, 6, 'properties/6/qUyuatJQcr7dNuVLEhlV6wp4d4D3oUqPNIvomLFf.jpg', 'public', 'Tomato-Cucumber-Salad-750x750.jpg', 71092, 'image/jpeg', 1, 1, '2026-03-05 22:14:39', '2026-03-05 22:14:39'),
(17, 3, 'properties/3/f26aISpJbb3d19qzfQQDpFLgii2BGdJO8Q2xTxC5.jpg', 'public', '22.jpeg', 44833, 'image/jpeg', 0, 4, '2026-03-05 22:26:28', '2026-03-05 22:26:28'),
(18, 7, 'properties/7/o4TKP6cj1Co7unEL0etNbVaeui5wvXS9V260lnyT.jpg', 'public', '8654.jpeg', 32594, 'image/jpeg', 1, 1, '2026-03-05 22:27:55', '2026-03-05 22:27:55'),
(19, 7, 'properties/7/EGqDX0Tc6Lw6L3gAg6sM0RaohcVeRohVo67uCHIF.jpg', 'public', 'Hawaiian-Salmon-Poke-750x750.jpg', 72599, 'image/jpeg', 0, 2, '2026-03-05 22:29:25', '2026-03-05 22:29:25');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_01_01_000001_create_users_table', 1),
(2, '2024_01_01_000002_create_properties_table', 1),
(3, '2024_01_01_000003_create_images_table', 1),
(4, '2024_01_01_000004_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('appartement','villa','terrain','bureau','commerce','maison','studio') COLLATE utf8mb4_unicode_ci NOT NULL,
  `rooms` smallint UNSIGNED DEFAULT NULL,
  `surface` decimal(10,2) DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('disponible','vendu','location') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disponible',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `user_id`, `title`, `type`, `rooms`, `surface`, `price`, `city`, `address`, `description`, `status`, `is_published`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'Villa 5 pièces à Alger', 'villa', 5, 320.00, 45000000.00, 'Alger', 'Bab Ezzouar, Alger', 'Magnifique villa avec jardin et piscine, quartier résidentiel calme.', 'disponible', 1, '2026-03-05 21:43:28', '2026-03-05 21:43:28', NULL),
(2, 3, 'Appartement 3 pièces à Oran', 'appartement', 3, 90.00, 12500000.00, 'Oran', 'Hai Fellaoucene, Oran', 'F3 bien exposé, vue sur mer, proche de toutes commodités.', 'disponible', 1, '2026-03-05 21:43:29', '2026-03-05 21:43:29', NULL),
(3, 4, 'Terrain 500m² à Constantine', 'terrain', NULL, 500.00, 8000000.00, 'Constantine', 'Zone industrielle, Constantine', 'Terrain plat viabilisé, idéal pour construction.', 'disponible', 1, '2026-03-05 21:43:29', '2026-03-05 21:43:29', NULL),
(4, 2, 'Studio 1 pièce à Annaba - En location', 'studio', 1, 35.00, 25000.00, 'Annaba', 'Centre-ville, Annaba', 'Studio meublé en excellent état, idéal pour étudiant.', 'location', 1, '2026-03-05 21:43:29', '2026-03-05 21:43:29', NULL),
(5, 3, 'Bureau à Alger - En location', 'bureau', NULL, 150.00, 80000.00, 'Alger', 'Hydra, Alger', 'Plateau de bureau moderne, climatisé, parking inclus.', 'location', 0, '2026-03-05 21:43:29', '2026-03-05 21:43:29', NULL),
(6, 4, 'Studio 3 pièces à Alger', 'studio', 3, 14.00, 10000.00, 'alger', '13 rue des martyrs', 'Duplex a vendre', 'disponible', 1, '2026-03-05 21:53:24', '2026-03-05 22:15:26', '2026-03-05 22:15:26'),
(7, 4, 'Studio 2 pièces à Alger', 'studio', 2, 12.00, 80000.00, 'alger', '13 rue des martyrs', 'studio a vendre', 'disponible', 1, '2026-03-05 22:27:55', '2026-03-05 22:29:52', '2026-03-05 22:29:52');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','agent','guest') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'guest',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `phone`, `avatar`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrateur', 'admin@immobilier.dz', NULL, '$2y$12$1sM7jh8iriZz98PWydfl6u/Bf5ZFE9J3kOYGZnIs9XQdWbY.3FRQK', 'admin', NULL, NULL, 1, NULL, '2026-03-05 21:43:27', '2026-03-05 21:43:27'),
(2, 'Karim Benamara', 'karim@immobilier.dz', NULL, '$2y$12$7Rq3qBI4U8zlANB7YRWwMe.HzN6ix/QDUFKMHSIa7dJd8ieNh/SyS', 'agent', NULL, NULL, 1, NULL, '2026-03-05 21:43:28', '2026-03-05 21:43:28'),
(3, 'Sarah Meziane', 'sarah@immobilier.dz', NULL, '$2y$12$SpnwZHyLLuocL2qrZMf5jOFCJCyoGEi2byf5UHU6RdzPE5tynso6m', 'agent', NULL, NULL, 1, NULL, '2026-03-05 21:43:28', '2026-03-05 21:43:28'),
(4, 'Walid Bouazza', 'walid@immobilier.dz', NULL, '$2y$12$KByWzGmNSAUK1tzQlYNq5uRlnzYni1ABKR7igdYPamrzl4wTBwesO', 'agent', NULL, NULL, 1, NULL, '2026-03-05 21:43:28', '2026-03-05 21:43:28'),
(5, 'Visiteur Test', 'visiteur@example.com', NULL, '$2y$12$nGF.gnjgPyfuNCXm2MhOjOFTPtxpFHiTVOtbEYJ.G39SnWYVtIiCS', 'guest', NULL, NULL, 1, NULL, '2026-03-05 21:43:28', '2026-03-05 21:43:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `images_property_id_foreign` (`property_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `properties_user_id_foreign` (`user_id`),
  ADD KEY `properties_city_type_status_index` (`city`,`type`,`status`),
  ADD KEY `properties_price_index` (`price`);
ALTER TABLE `properties` ADD FULLTEXT KEY `properties_title_description_fulltext` (`title`,`description`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
