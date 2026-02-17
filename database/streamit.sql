-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 15, 2026 at 05:36 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `streamit`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPopularVideos` (IN `limit_count` INT)   BEGIN
    SELECT v.*, u.username, c.name as category_name
    FROM videos v
    JOIN users u ON v.user_id = u.id
    JOIN categories c ON v.category_id = c.id
    WHERE v.status = 'approved'
    ORDER BY v.views DESC, v.likes DESC
    LIMIT limit_count;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetVideosByCategory` (IN `cat_id` INT, IN `limit_count` INT)   BEGIN
    SELECT v.*, u.username, c.name as category_name
    FROM videos v
    JOIN users u ON v.user_id = u.id
    JOIN categories c ON v.category_id = c.id
    WHERE v.category_id = cat_id AND v.status = 'approved'
    ORDER BY v.upload_date DESC
    LIMIT limit_count;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SearchVideos` (IN `search_term` VARCHAR(255), IN `limit_count` INT)   BEGIN
    SELECT v.*, u.username, c.name as category_name,
           MATCH(v.title, v.description) AGAINST(search_term) as relevance
    FROM videos v
    JOIN users u ON v.user_id = u.id
    JOIN categories c ON v.category_id = c.id
    WHERE v.status = 'approved' 
    AND (MATCH(v.title, v.description) AGAINST(search_term) 
         OR v.title LIKE CONCAT('%', search_term, '%'))
    ORDER BY relevance DESC, v.views DESC
    LIMIT limit_count;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','moderator') DEFAULT 'admin',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'vicky', 'vikcy@gmail.com', '$2y$10$OnMbvFq9ulTSV66Ez7lXJehZS8laC1h9xfsq7MHnzudEFrbuaiK86', 'System Administrator', 'admin', 1, NULL, '2025-07-31 17:54:28', '2025-08-01 05:49:58'),
(2, 'sayyam', 'sayyam@gmail.com', '$2y$10$OnMbvFq9ulTSV66Ez7lXJehZS8laC1h9xfsq7MHnzudEFrbuaiK86', 'Content Moderator', 'moderator', 1, NULL, '2025-07-31 17:54:28', '2025-08-01 05:50:38');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(60) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Tutorial', 'Educational tutorials and how-to videos', 'tutorial', 1, '2025-07-31 17:54:16', '2025-07-31 17:54:16'),
(2, 'Demo', 'Product demonstrations and previews', 'demo', 1, '2025-07-31 17:54:16', '2025-07-31 17:54:16'),
(3, 'Educational', 'Educational content and lectures', 'educational', 1, '2025-07-31 17:54:16', '2025-07-31 17:54:16'),
(4, 'Entertainment', 'Entertainment and recreational content', 'entertainment', 1, '2025-07-31 17:54:16', '2025-07-31 17:54:16'),
(5, 'Technology', 'Technology reviews and discussions', 'technology', 1, '2025-07-31 17:54:16', '2025-07-31 17:54:16'),
(6, 'Music', 'Music videos and performances', 'music', 1, '2025-07-31 17:54:16', '2025-07-31 17:54:16'),
(7, 'Sports', 'Sports content and highlights', 'sports', 1, '2025-07-31 17:54:16', '2025-07-31 17:54:16'),
(8, 'News', 'News and current events', 'news', 1, '2025-07-31 17:54:16', '2025-07-31 17:54:16'),
(9, 'Gaming', 'Gaming content and gameplay', 'gaming', 1, '2025-07-31 17:54:16', '2025-07-31 17:54:16'),
(10, 'Cooking', 'Cooking tutorials and recipes', 'cooking', 1, '2025-07-31 17:54:16', '2025-07-31 17:54:16');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `is_approved` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `video_id`, `parent_id`, `comment`, `is_approved`, `created_at`, `updated_at`) VALUES
(1, 2, 1, NULL, 'Great tutorial! Very helpful for beginners.', 1, '2025-07-31 17:55:07', '2025-07-31 17:55:07'),
(2, 3, 1, NULL, 'Thanks for this guide. The platform looks amazing!', 1, '2025-07-31 17:55:07', '2025-07-31 17:55:07'),
(3, 1, 3, NULL, 'Excellent editing tips. Looking forward to more content.', 1, '2025-07-31 17:55:07', '2025-07-31 17:55:07'),
(5, 4, 7, NULL, 'nothing', 1, '2026-02-14 19:53:40', '2026-02-14 19:53:40'),
(6, 7, 7, NULL, 'ohhk', 1, '2026-02-14 19:54:27', '2026-02-14 19:54:27'),
(7, 8, 9, NULL, 'shubham is handsome', 1, '2026-02-15 03:39:44', '2026-02-15 03:39:44'),
(8, 8, 7, NULL, 'jasldhfouisid', 1, '2026-02-15 03:40:06', '2026-02-15 03:40:06'),
(9, 4, 9, NULL, 'asodilajwiod', 1, '2026-02-15 03:41:06', '2026-02-15 03:41:06');

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE `email_logs` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `success` tinyint(1) DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_logs`
--

INSERT INTO `email_logs` (`id`, `email`, `type`, `success`, `ip_address`, `created_at`) VALUES
(1, 'test@example.com', 'otp', 1, '::1', '2026-02-14 17:58:55'),
(2, 'vk@gmail.com', 'otp', 1, '::1', '2026-02-14 18:04:01'),
(3, 'vk@gmail.com', 'otp', 0, '::1', '2026-02-14 18:04:09'),
(4, 'vk@gmail.com', 'otp', 1, '::1', '2026-02-14 18:08:41'),
(5, 'vk@gmail.com', 'otp', 1, '::1', '2026-02-14 18:08:58'),
(6, 'vk@gmail.com', 'otp', 0, '::1', '2026-02-14 18:09:04'),
(7, 'vk@gmail.com', 'otp', 1, '::1', '2026-02-14 18:14:55'),
(8, 'vk@gmail.com', 'otp', 0, '::1', '2026-02-14 18:15:02'),
(9, 'vk@gmail.com', 'otp', 1, '::1', '2026-02-14 18:20:41'),
(10, 'vk@gmail.com', 'password_reset_link', 1, '::1', '2026-02-14 18:32:21'),
(11, 'vk@gmail.com', 'password_reset_link', 1, '::1', '2026-02-14 18:32:31'),
(12, 'vk@gmail.com', 'password_reset_link', 1, '::1', '2026-02-14 18:36:25'),
(13, 'pr@gmail.com', 'password_reset_link', 1, '::1', '2026-02-14 19:02:05'),
(14, 'vk@gmail.com', 'password_reset_link', 1, '::1', '2026-02-14 19:18:41'),
(15, 'vk@gmail.com', 'reset_confirmation', 1, '::1', '2026-02-14 19:19:24'),
(16, 'srr@gmail.com', 'password_reset_link', 0, '::1', '2026-02-15 03:37:05'),
(17, 'srr@gmail.com', 'password_reset_link', 1, '::1', '2026-02-15 03:38:18'),
(18, 'srr@gmail.com', 'reset_confirmation', 1, '::1', '2026-02-15 03:38:55');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `success` tinyint(1) DEFAULT 0,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `email`, `otp`, `token`, `expires_at`, `verified`, `ip_address`, `created_at`) VALUES
(12, 7, 'pr@gmail.com', '', 'cafa56151a7d7734c7eeb71c60dcef39d61db15a7b62d7069475aa423d21e8af', '2026-02-14 20:01:56', 0, '::1', '2026-02-14 19:01:56'),
(13, 4, 'vk@gmail.com', '', '5553e0cfaf5b32f5b00bf33789fa780fccb45acd25f655d3c517667472978b9f', '2026-02-14 20:18:32', 1, '::1', '2026-02-14 19:18:32'),
(15, 8, 'srr@gmail.com', '', '931e0a6e6513d1a124a34f8f72c844f8f2250b31f7361eba97f81ca215349a82', '2026-02-15 04:38:12', 1, '::1', '2026-02-15 03:38:12');

-- --------------------------------------------------------

--
-- Table structure for table `playlists`
--

CREATE TABLE `playlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlist_videos`
--

CREATE TABLE `playlist_videos` (
  `id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `security_events`
--

CREATE TABLE `security_events` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'site_name', 'StreamIt', 'Website name', '2025-07-31 17:54:37'),
(2, 'site_description', 'Your personal video streaming platform', 'Website description', '2025-07-31 17:54:37'),
(3, 'max_upload_size', '104857600', 'Maximum upload size in bytes (100MB)', '2025-07-31 17:54:37'),
(4, 'allowed_video_types', 'mp4,avi,mov,wmv,webm', 'Allowed video file extensions', '2025-07-31 17:54:37'),
(5, 'videos_per_page', '12', 'Number of videos to display per page', '2025-07-31 17:54:37'),
(6, 'auto_approve_videos', '0', 'Auto approve uploaded videos (1=yes, 0=no)', '2025-07-31 17:54:37'),
(7, 'enable_comments', '1', 'Enable video comments (1=yes, 0=no)', '2025-07-31 17:54:37'),
(8, 'enable_likes', '1', 'Enable video likes/dislikes (1=yes, 0=no)', '2025-07-31 17:54:37'),
(9, 'enable_subscriptions', '1', 'Enable user subscriptions (1=yes, 0=no)', '2025-07-31 17:54:37'),
(10, 'maintenance_mode', '0', 'Maintenance mode (1=on, 0=off)', '2025-07-31 17:54:37');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `channel_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `email_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `profile_image`, `is_active`, `email_verified`, `created_at`, `updated_at`) VALUES
(1, 'john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Doe', NULL, 1, 1, '2025-07-31 17:54:49', '2025-07-31 17:54:49'),
(2, 'jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Smith', NULL, 1, 1, '2025-07-31 17:54:49', '2025-07-31 17:54:49'),
(3, 'mike_dev', 'mike@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike', 'Developer', NULL, 1, 1, '2025-07-31 17:54:49', '2025-07-31 17:54:49'),
(4, 'vk', 'vk@gmail.com', '$2y$10$VrbP5iaqyK8/B4nCpZKhZeApX/9Mksyn3XR4rEcoZouNGpbQybrRa', NULL, NULL, NULL, 1, 0, '2025-07-31 18:09:28', '2026-02-14 19:19:11'),
(5, 'shubham', 'sr@gmail.com', '$2y$10$LOnnDnPD6kpz87sy.90D0utP6g2cx90IvtgN8c5YH0LCcmV8G0GGi', NULL, NULL, NULL, 1, 0, '2025-08-09 17:03:20', '2025-08-09 17:03:20'),
(6, 'vkk', 'vk@gmail.in', '$2y$10$cB.78WpSxDgyGpfzeVTR0utITQq7rrg6eSfyySyw0gdo9Zn82xRqG', NULL, NULL, NULL, 1, 0, '2026-02-04 18:13:37', '2026-02-04 18:13:37'),
(7, 'pr', 'pr@gmail.com', '$2y$10$w9Dy/mWisFVFkmLAN1NRiOhxqCyBnurk9xi7ahxYWPKIf7NEMA3Ea', NULL, NULL, NULL, 1, 0, '2026-02-14 19:01:41', '2026-02-14 19:01:41'),
(8, 'shubham rathod', 'srr@gmail.com', '$2y$10$0OAmWTu7vF5ZPYRCtQy.5OAzz4Efhq9BVYMk9b8vnC8rvHXS5F/0q', NULL, NULL, NULL, 1, 0, '2026-02-15 03:36:43', '2026-02-15 03:38:49');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `original_filename` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `duration` int(11) DEFAULT 0,
  `file_size` bigint(20) DEFAULT 0,
  `video_format` varchar(20) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected','processing') DEFAULT 'pending',
  `views` int(11) DEFAULT 0,
  `likes` int(11) DEFAULT 0,
  `dislikes` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_private` tinyint(1) DEFAULT 0,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_date` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `user_id`, `title`, `description`, `filename`, `original_filename`, `thumbnail`, `duration`, `file_size`, `video_format`, `category_id`, `status`, `views`, `likes`, `dislikes`, `is_featured`, `is_private`, `upload_date`, `approved_date`, `updated_at`) VALUES
(1, 1, 'Getting Started with StreamIt', 'Learn how to use the StreamIt platform effectively. This tutorial covers the basics of uploading, managing, and sharing your videos.', 'sample_video_1.mp4', 'getting_started.mp4', NULL, 0, 0, NULL, 1, 'approved', 153, 2, 0, 1, 0, '2025-07-31 17:55:00', NULL, '2025-07-31 18:04:04'),
(2, 1, 'Platform Features Overview', 'Comprehensive overview of all StreamIt platform features including video management, user accounts, and admin panel.', 'sample_video_2.mp4', 'features_overview.mp4', NULL, 0, 0, NULL, 2, 'approved', 99, 0, 0, 1, 0, '2025-07-31 17:55:00', NULL, '2025-08-01 05:37:18'),
(3, 2, 'Advanced Video Editing Tips', 'Professional video editing techniques and tips for content creators.', 'sample_video_3.mp4', 'editing_tips.mp4', NULL, 0, 0, NULL, 1, 'approved', 76, 1, 0, 0, 0, '2025-07-31 17:55:00', NULL, '2025-07-31 17:55:22'),
(4, 2, 'CSS Grid Tutorial', 'Complete guide to CSS Grid layout system with practical examples.', 'sample_video_4.mp4', 'css_grid.mp4', NULL, 0, 0, NULL, 5, 'approved', 3, 0, 0, 0, 0, '2025-07-31 17:55:00', NULL, '2026-02-14 19:43:51'),
(7, 4, 'Urjaviraj', 'asdfg', '1753987776_688bbac0574da.mp4', NULL, NULL, 0, 0, NULL, 8, 'approved', 36, 1, 0, 0, 0, '2025-07-31 18:49:36', NULL, '2026-02-15 03:41:27'),
(8, 4, 'gjbvghvbhjgbvgh', 'gjb n', '1754642107_6895b6bbcb7b7.mp4', NULL, NULL, 0, 0, NULL, 9, 'approved', 9, 1, 0, 0, 0, '2025-08-08 08:35:07', NULL, '2026-02-14 20:10:44'),
(9, 5, 'Shubham', 'dsfgfdgsfgrf', '1754759182_6897800e84a3c.mp4', NULL, NULL, 0, 0, NULL, 1, 'approved', 18, 2, 0, 0, 0, '2025-08-09 17:06:22', NULL, '2026-02-15 03:41:34');

-- --------------------------------------------------------

--
-- Table structure for table `video_reactions`
--

CREATE TABLE `video_reactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `reaction` enum('like','dislike') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `video_reactions`
--

INSERT INTO `video_reactions` (`id`, `user_id`, `video_id`, `reaction`, `created_at`) VALUES
(1, 2, 1, 'like', '2025-07-31 17:55:13'),
(2, 3, 1, 'like', '2025-07-31 17:55:13'),
(3, 1, 3, 'like', '2025-07-31 17:55:13'),
(8, 4, 8, 'like', '2026-02-14 20:10:27'),
(10, 8, 9, 'like', '2026-02-15 03:39:50'),
(11, 8, 7, 'like', '2026-02-15 03:40:08'),
(12, 4, 7, 'like', '2026-02-15 03:41:27'),
(13, 4, 9, 'like', '2026-02-15 03:41:32');

--
-- Triggers `video_reactions`
--
DELIMITER $$
CREATE TRIGGER `update_video_stats_on_delete` AFTER DELETE ON `video_reactions` FOR EACH ROW BEGIN
    IF OLD.reaction = 'like' THEN
        UPDATE videos SET likes = likes - 1 WHERE id = OLD.video_id;
    ELSEIF OLD.reaction = 'dislike' THEN
        UPDATE videos SET dislikes = dislikes - 1 WHERE id = OLD.video_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `video_stats`
-- (See below for the actual view)
--
CREATE TABLE `video_stats` (
`id` int(11)
,`title` varchar(200)
,`views` int(11)
,`likes` int(11)
,`dislikes` int(11)
,`uploader` varchar(50)
,`category` varchar(50)
,`upload_date` timestamp
,`status` enum('pending','approved','rejected','processing')
);

-- --------------------------------------------------------

--
-- Table structure for table `watch_history`
--

CREATE TABLE `watch_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `watch_duration` int(11) DEFAULT 0,
  `completed` tinyint(1) DEFAULT 0,
  `last_watched` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure for view `video_stats`
--
DROP TABLE IF EXISTS `video_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `video_stats`  AS SELECT `v`.`id` AS `id`, `v`.`title` AS `title`, `v`.`views` AS `views`, `v`.`likes` AS `likes`, `v`.`dislikes` AS `dislikes`, `u`.`username` AS `uploader`, `c`.`name` AS `category`, `v`.`upload_date` AS `upload_date`, `v`.`status` AS `status` FROM ((`videos` `v` join `users` `u` on(`v`.`user_id` = `u`.`id`)) join `categories` `c` on(`v`.`category_id` = `c`.`id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_video_id` (`video_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_parent_id` (`parent_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `email` (`email`),
  ADD KEY `idx_password_resets_lookup` (`email`,`verified`,`expires_at`),
  ADD KEY `idx_token` (`token`);

--
-- Indexes for table `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_public` (`is_public`);

--
-- Indexes for table `playlist_videos`
--
ALTER TABLE `playlist_videos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_playlist_video` (`playlist_id`,`video_id`),
  ADD KEY `idx_playlist_id` (`playlist_id`),
  ADD KEY `idx_video_id` (`video_id`),
  ADD KEY `idx_position` (`position`);

--
-- Indexes for table `security_events`
--
ALTER TABLE `security_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_subscription` (`subscriber_id`,`channel_id`),
  ADD KEY `idx_subscriber` (`subscriber_id`),
  ADD KEY `idx_channel` (`channel_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_upload_date` (`upload_date`),
  ADD KEY `idx_views` (`views`),
  ADD KEY `idx_featured` (`is_featured`),
  ADD KEY `idx_videos_search` (`title`,`status`),
  ADD KEY `idx_videos_featured` (`is_featured`,`status`,`upload_date`),
  ADD KEY `idx_videos_category_status` (`category_id`,`status`,`upload_date`);
ALTER TABLE `videos` ADD FULLTEXT KEY `idx_search` (`title`,`description`);

--
-- Indexes for table `video_reactions`
--
ALTER TABLE `video_reactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_video` (`user_id`,`video_id`),
  ADD KEY `idx_video_id` (`video_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `watch_history`
--
ALTER TABLE `watch_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_video_history` (`user_id`,`video_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_video_id` (`video_id`),
  ADD KEY `idx_last_watched` (`last_watched`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `playlists`
--
ALTER TABLE `playlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playlist_videos`
--
ALTER TABLE `playlist_videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `security_events`
--
ALTER TABLE `security_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `video_reactions`
--
ALTER TABLE `video_reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `watch_history`
--
ALTER TABLE `watch_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `activity_logs_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `playlists`
--
ALTER TABLE `playlists`
  ADD CONSTRAINT `playlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `playlist_videos`
--
ALTER TABLE `playlist_videos`
  ADD CONSTRAINT `playlist_videos_ibfk_1` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `playlist_videos_ibfk_2` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`subscriber_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`channel_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `videos_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `video_reactions`
--
ALTER TABLE `video_reactions`
  ADD CONSTRAINT `video_reactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `video_reactions_ibfk_2` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `watch_history`
--
ALTER TABLE `watch_history`
  ADD CONSTRAINT `watch_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `watch_history_ibfk_2` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
