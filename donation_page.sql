-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2026 at 11:23 PM
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
-- Database: `donation_page`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `role_id`, `full_name`, `avatar`, `email`, `password_hash`, `status`, `last_login_at`, `created_at`, `updated_at`) VALUES
(2, 1, 'David', 'assets/uploads/avatars/ava_2_1778240406.png', 'davidmeekmill11@gmail.com', '$2y$10$4gzamCY73oONN0RHKKgy5OG3JMBTHYOFXEChKvSMWj2VWo8mJGhNS', 'active', '2026-05-11 19:07:26', '2026-05-07 16:42:25', '2026-05-11 18:07:26');

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(190) NOT NULL,
  `message` text NOT NULL,
  `icon` varchar(50) DEFAULT 'fas fa-info-circle',
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`id`, `admin_id`, `type`, `title`, `message`, `icon`, `link`, `is_read`, `created_at`) VALUES
(1, NULL, '', 'New Donation!', 'A donation of NGN500,000 was received from Okechukwu Ajah', 'fas fa-hand-holding-heart', '?page=donations', 1, '2026-05-09 16:57:24'),
(2, NULL, '', 'New Donation!', 'A donation of NGN200,000 was received from Okechukwu Ajah', 'fas fa-hand-holding-heart', '?page=donations', 1, '2026-05-09 17:06:36'),
(3, NULL, '', 'New Donation Received!', 'A donation of ₦10,000 was received from ajahd887@gmail.com for Save the World', 'fas fa-hand-holding-heart', '?page=donations', 1, '2026-05-09 17:08:03'),
(4, NULL, '', 'New Donation Received!', 'A donation of ₦150,000 was received from ajahd887@gmail.com for Save the World', 'fas fa-hand-holding-heart', '?page=donations', 1, '2026-05-10 15:35:52'),
(5, NULL, '', 'New Donation Received!', 'A donation of ₦10,000 was received from ajahd887@gmail.com for Youth Empowerment and Entrepreneurship Program', 'fas fa-hand-holding-heart', '?page=donations', 1, '2026-05-10 22:20:47'),
(6, NULL, '', 'New Donation Received!', 'A donation of ₦10,000 was received from ajahd887@gmail.com for Save the World', 'fas fa-hand-holding-heart', '?page=donations', 1, '2026-05-10 22:21:53'),
(7, NULL, '', 'New Donation!', 'A donation of NGN100,000 was received from Okechukwu', 'fas fa-hand-holding-heart', '?page=donations', 1, '2026-05-12 07:21:55'),
(8, NULL, '', 'New Donation Received!', 'A donation of ₦100,000 was received from ajahd887@gmail.com for Uplifting Famiies', 'fas fa-hand-holding-heart', '?page=donations', 1, '2026-05-12 13:37:37'),
(9, NULL, '', 'New Donation!', 'A donation of NGN500,000 was received from Okechukwu', 'fas fa-hand-holding-heart', '?page=donations', 0, '2026-05-12 13:43:14'),
(10, NULL, '', 'New Donation!', 'A donation of NGN39,999 was received from Okechukwu Ajah', 'fas fa-hand-holding-heart', '?page=donations', 0, '2026-05-12 13:48:07'),
(11, NULL, '', 'New Donation!', 'A donation of NGN100,000 was received from Okechukwu Ajah', 'fas fa-hand-holding-heart', '?page=donations', 0, '2026-05-12 13:50:56'),
(12, NULL, '', 'New Donation!', 'A donation of NGN100,000 was received from Okechukwu Ajah', 'fas fa-hand-holding-heart', '?page=donations', 0, '2026-05-12 13:57:33'),
(13, NULL, '', 'New Donation!', 'A donation of NGN 200,000 was received from Okechukwu', 'fas fa-hand-holding-heart', '?page=donations', 0, '2026-05-12 14:19:00'),
(14, NULL, '', 'New Donation Received!', 'A donation of ₦900,000 was received from ajahd887@gmail.com for Uplifting Famiies', 'fas fa-hand-holding-heart', '?page=donations', 0, '2026-05-12 14:20:10'),
(15, NULL, '', 'New Donation!', 'A donation of NGN 200,000 was received from Anonymous Supporter', 'fas fa-hand-holding-heart', '?page=donations', 0, '2026-05-12 14:30:04'),
(16, NULL, '', 'New Contact Message', 'Okechukwu Ajah sent a new inquiry: Wordpress Site', 'fas fa-envelope', '?page=messages', 0, '2026-05-12 19:32:01'),
(17, NULL, '', 'New Contact Message', 'Okechukwu Ajah sent a new inquiry: Wordpress Site', 'fas fa-envelope', '?page=messages', 0, '2026-05-12 19:32:09');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(190) NOT NULL,
  `target_type` varchar(120) DEFAULT NULL,
  `target_id` bigint(20) UNSIGNED DEFAULT NULL,
  `details` longtext DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_comments`
--

CREATE TABLE `blog_comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `comment_text` text NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','approved','spam') DEFAULT 'pending',
  `is_admin_reply` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(190) NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone` varchar(60) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied','archived') DEFAULT 'unread',
  `admin_reply` text DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `status`, `admin_reply`, `replied_at`, `created_at`) VALUES
(1, 'Okechukwu Ajah', 'ajahd887@gmail.com', '07057210937', 'Wordpress Site', 'Hello, i seek an audience with your founder', 'read', NULL, NULL, '2026-05-12 19:31:51'),
(2, 'Okechukwu Ajah', 'ajahd887@gmail.com', '07057210937', 'Wordpress Site', 'Hello, i seek an audience with your founder', 'replied', 'Hello, how may we help you', '2026-05-12 20:46:42', '2026-05-12 19:32:01');

-- --------------------------------------------------------

--
-- Table structure for table `content_blocks`
--

CREATE TABLE `content_blocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `page_id` bigint(20) UNSIGNED NOT NULL,
  `block_key` varchar(120) NOT NULL,
  `block_label` varchar(190) NOT NULL,
  `block_type` enum('text','html','image','cta','json') DEFAULT 'text',
  `block_value` longtext DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `content_blocks`
--

INSERT INTO `content_blocks` (`id`, `page_id`, `block_key`, `block_label`, `block_type`, `block_value`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'hero_label', 'Hero Label', 'text', 'About Us', 0, '2026-05-09 17:22:40', '2026-05-09 17:22:40'),
(2, 1, 'hero_title', 'Hero Main Title', 'text', 'Step Forward Serve The Huminity Reach Out & Help', 0, '2026-05-09 17:22:40', '2026-05-09 17:22:40'),
(3, 1, 'hero_desc', 'Hero Description', 'text', 'The secret to happiness lies in helping others. Never underestimate the difference YOU can make in the lives of the poor, the abused and the helpless.', 0, '2026-05-09 17:22:40', '2026-05-09 17:22:40'),
(4, 1, 'hero_image', 'Hero Image', 'image', 'assets/images/about_img.png', 0, '2026-05-09 17:22:40', '2026-05-09 17:22:40'),
(5, 1, 'know_label', 'Know Us Label', 'text', 'Get to Know Us', 0, '2026-05-09 17:22:40', '2026-05-09 17:22:40'),
(6, 1, 'know_title', 'Know Us Title', 'text', 'Let Us Come Together To Make a Difference', 0, '2026-05-09 17:22:40', '2026-05-09 17:22:40'),
(7, 1, 'know_desc', 'Know Us Description', 'text', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', 0, '2026-05-09 17:22:40', '2026-05-09 17:22:40'),
(8, 1, 'skill_1_label', 'Skill 1 Name', 'text', 'Food Help', 0, '2026-05-09 17:22:40', '2026-05-09 17:22:40'),
(9, 1, 'skill_1_val', 'Skill 1 Percentage', 'text', '67%', 0, '2026-05-09 17:22:40', '2026-05-09 17:22:40'),
(10, 1, 'skill_2_label', 'Skill 2 Name', 'text', 'Medical Help', 0, '2026-05-09 17:22:40', '2026-05-09 17:22:40'),
(11, 1, 'skill_2_val', 'Skill 2 Percentage', 'text', '85%', 0, '2026-05-09 17:22:41', '2026-05-09 17:22:41'),
(12, 1, 'mission_text', 'Mission Points (JSON)', 'json', '[\"Nsectetur cing elit.\",\"Suspe ndisse suscipit sagittis leo.\",\"Entum estibulum dignissim posuere.\"]', 0, '2026-05-09 17:22:41', '2026-05-09 17:22:41'),
(13, 1, 'mission_image', 'Mission Image', 'image', 'assets/images/about_img_2.jpg', 0, '2026-05-09 17:22:41', '2026-05-09 17:22:41'),
(14, 1, 'mission_years', 'Mission Years', 'text', '14', 0, '2026-05-09 17:22:41', '2026-05-09 17:22:41'),
(18, 1, 'hero_image_1', 'Hero image 1', 'image', NULL, 0, '2026-05-09 19:04:51', '2026-05-09 22:22:43'),
(19, 1, 'hero_image_2', 'Hero image 2', 'image', '', 0, '2026-05-09 19:04:51', '2026-05-09 19:04:51'),
(20, 1, 'hero_image_3', 'Hero image 3', 'image', '', 0, '2026-05-09 19:04:51', '2026-05-09 19:04:51'),
(31, 1, 'vision_image', 'Vision image', 'image', 'assets/images/about_img_2.jpg', 0, '2026-05-09 19:04:51', '2026-05-09 19:04:51'),
(32, 1, 'vision_text', 'Vision text', 'text', '[\"Global Reach\", \"Innovation in Giving\", \"Lasting Impact\"]', 0, '2026-05-09 19:04:51', '2026-05-09 19:04:51'),
(33, 1, 'history_image', 'History image', 'image', 'assets/images/about_img_2.jpg', 0, '2026-05-09 19:04:51', '2026-05-09 19:04:51'),
(34, 1, 'history_text', 'History text', 'text', '[\"Founded in 2010\", \"Reached 1M+ Lives\", \"Award Winning NGO\"]', 0, '2026-05-09 19:04:51', '2026-05-09 19:04:51'),
(35, 1, 'feature_title', 'Feature title', 'text', 'Work As An Intern', 0, '2026-05-09 19:04:51', '2026-05-09 19:04:51'),
(36, 1, 'feature_desc', 'Feature desc', 'text', 'Sed quia consequuntur agni dolores eos qui ratoluptatem sequi nesciun porquis', 0, '2026-05-09 19:04:51', '2026-05-09 19:04:51'),
(37, 1, 'feature_icon', 'Feature icon', 'text', 'charity-volunteer_people', 0, '2026-05-09 19:04:51', '2026-05-09 19:04:51'),
(38, 1, 'feature_link', 'Feature link', 'text', 'become-volunteers.php', 0, '2026-05-09 19:04:51', '2026-05-09 19:04:51'),
(39, 1, 'contact_phone', 'Contact phone', 'text', '+1234567899', 0, '2026-05-09 19:04:51', '2026-05-09 19:04:51');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `donor_name` varchar(190) DEFAULT NULL,
  `donor_email` varchar(190) DEFAULT NULL,
  `donor_phone` varchar(60) DEFAULT NULL,
  `campaign` varchar(190) DEFAULT NULL,
  `currency` char(3) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `gateway` enum('paystack','stripe','manual') NOT NULL,
  `status` enum('pending','successful','failed','refunded') DEFAULT 'pending',
  `payment_reference` varchar(190) NOT NULL,
  `metadata` text DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `donor_name`, `donor_email`, `donor_phone`, `campaign`, `currency`, `amount`, `gateway`, `status`, `payment_reference`, `metadata`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 'Test User', 'test@example.com', NULL, NULL, 'NGN', 10.00, 'paystack', 'successful', 'test_ref_1778242714', '{\"test\":true}', '2026-05-08 13:18:34', '2026-05-08 12:18:34', '2026-05-08 12:18:34'),
(2, 'Okechukwu David', 'ajahd887@gmail.com', NULL, NULL, 'NGN', 10.00, 'paystack', 'successful', 'kbhycvcuj8', '[]', '2026-05-08 13:21:53', '2026-05-08 12:21:38', '2026-05-08 12:21:46'),
(4, 'Okechukwu David', 'ajahd887@gmail.com', NULL, NULL, 'NGN', 500000.00, 'paystack', 'successful', 'u2a8bmcebq', '[]', '2026-05-08 13:31:59', '2026-05-08 12:31:39', '2026-05-08 12:31:53'),
(6, 'Okechukwu David', 'ajahd887@gmail.com', NULL, NULL, 'NGN', 100000.00, 'paystack', 'successful', 'x94ef5gq7a', '[]', '2026-05-08 14:17:37', '2026-05-08 13:17:21', '2026-05-08 13:17:30'),
(8, 'Okechukwu David', 'ajahd887@gmail.com', NULL, NULL, 'NGN', 200000.00, 'paystack', 'successful', '87rm9fhezb', '[]', '2026-05-08 18:30:24', '2026-05-08 17:30:04', '2026-05-08 17:30:17'),
(10, 'Okechukwu Ajah', 'ajahd887@gmail.com', NULL, NULL, 'NGN', 10000.00, 'paystack', 'failed', 'raca941zf1', '[]', NULL, '2026-05-09 12:03:43', '2026-05-09 15:52:09'),
(11, 'Okechukwu Ajah', 'ajahd887@gmail.com', NULL, NULL, 'NGN', 20000.00, 'paystack', 'successful', 'vdmlgyo41s', '[]', '2026-05-09 13:10:55', '2026-05-09 12:10:46', '2026-05-09 12:10:58'),
(13, 'Okechukwu Ajah', 'ajahd887@gmail.com', NULL, NULL, 'NGN', 50000.00, 'paystack', 'successful', 'mls8i9cvaw', '[]', '2026-05-09 13:18:29', '2026-05-09 12:18:18', '2026-05-09 12:18:31'),
(15, 'Okechukwu Ajah', 'ajahd887@gmail.com', NULL, NULL, 'NGN', 10000.00, 'paystack', 'successful', 'up5d3z6371', '[]', '2026-05-09 14:33:57', '2026-05-09 13:33:49', '2026-05-09 13:33:59'),
(17, NULL, 'ajahd887@gmail.com', NULL, 'Friends at Heart Welfare Initiative', 'NGN', 100000.00, 'paystack', 'successful', 'DON_293960475', NULL, '2026-05-09 16:33:56', '2026-05-09 15:33:56', '2026-05-09 15:33:56'),
(18, NULL, 'ajahd887@gmail.com', NULL, 'Friends at Heart Welfare Initiative', 'NGN', 10000.00, 'paystack', 'successful', 'DON_478616613', NULL, '2026-05-09 16:38:12', '2026-05-09 15:38:12', '2026-05-09 15:38:12'),
(19, NULL, 'ajahd887@gmail.com', NULL, 'Friends at Heart Welfare Initiative', 'NGN', 390000.00, 'paystack', 'successful', 'DON_545550716', NULL, '2026-05-09 16:43:09', '2026-05-09 15:43:09', '2026-05-09 15:43:09'),
(20, NULL, 'ajahd887@gmail.com', NULL, 'Save the World', 'NGN', 2000000.00, 'paystack', 'successful', 'DON_950980674', NULL, '2026-05-09 16:59:18', '2026-05-09 15:59:18', '2026-05-09 15:59:18'),
(21, 'Okechukwu Ajah', 'ajahd887@gmail.com', NULL, NULL, 'NGN', 500000.00, 'paystack', 'successful', 'i2p45j959p', '[]', '2026-05-09 17:57:19', '2026-05-09 16:57:07', '2026-05-09 16:57:24'),
(23, 'Okechukwu Ajah', 'ajahd887@gmail.com', NULL, NULL, 'NGN', 200000.00, 'paystack', 'successful', '8zrv87e72n', '[]', '2026-05-09 18:06:00', '2026-05-09 17:06:23', '2026-05-10 01:10:26'),
(25, NULL, 'ajahd887@gmail.com', NULL, 'Save the World', 'NGN', 10000.00, 'paystack', 'successful', 'DON_275587175', NULL, '2026-05-09 18:08:03', '2026-05-09 17:08:03', '2026-05-09 17:08:03'),
(26, NULL, 'ajahd887@gmail.com', NULL, 'Save the World', 'NGN', 150000.00, 'paystack', 'successful', 'DON_501372951', NULL, '2026-05-10 16:35:52', '2026-05-10 15:35:52', '2026-05-10 15:35:52'),
(27, NULL, 'ajahd887@gmail.com', NULL, 'Youth Empowerment and Entrepreneurship Program', 'NGN', 10000.00, 'paystack', 'successful', 'DON_626849397', NULL, '2026-05-10 23:20:47', '2026-05-10 22:20:47', '2026-05-10 22:20:47'),
(28, NULL, 'ajahd887@gmail.com', NULL, 'Save the World', 'NGN', 10000.00, 'paystack', 'successful', 'DON_878574694', NULL, '2026-05-10 23:21:53', '2026-05-10 22:21:53', '2026-05-10 22:21:53'),
(29, 'Okechukwu', 'davidmeekmill11@gmail.com', NULL, NULL, 'NGN', 100000.00, 'paystack', 'successful', '8t4jkmpgr0', '[]', '2026-05-12 08:21:55', '2026-05-12 07:21:41', '2026-05-12 07:21:55'),
(31, NULL, 'ajahd887@gmail.com', NULL, 'Uplifting Famiies', 'NGN', 100000.00, 'paystack', 'successful', 'DON_115962725', NULL, '2026-05-12 14:37:37', '2026-05-12 13:37:37', '2026-05-12 13:37:37'),
(32, 'Okechukwu', 'ajahd887@gmail.com', NULL, NULL, 'NGN', 500000.00, 'paystack', 'successful', 'xwlp4lml9j', '[]', '2026-05-12 14:43:14', '2026-05-12 13:43:01', '2026-05-12 13:43:14'),
(34, 'Okechukwu Ajah', 'ajahd887@gmail.com', NULL, NULL, 'NGN', 39999.00, 'paystack', 'successful', '8fa5ufzy69', '[]', '2026-05-12 14:48:07', '2026-05-12 13:47:58', '2026-05-12 13:48:07'),
(36, 'Okechukwu Ajah', 'ajahd887@gmail.com', NULL, NULL, 'NGN', 100000.00, 'paystack', 'successful', 'nuvtnvc3gz', '[]', '2026-05-12 14:50:57', '2026-05-12 13:50:48', '2026-05-12 13:50:56'),
(38, 'Okechukwu Ajah', 'ajahd887@gmail.com', NULL, NULL, 'NGN', 100000.00, 'paystack', 'successful', 'b1dbp4q9mj', '{\"id\":6138615966,\"domain\":\"test\",\"status\":\"success\",\"reference\":\"b1dbp4q9mj\",\"receipt_number\":null,\"amount\":10000000,\"message\":null,\"gateway_response\":\"Successful\",\"response_code\":\"00\",\"paid_at\":\"2026-05-12T13:57:34.000Z\",\"created_at\":\"2026-05-12T13:57:28.000Z\",\"channel\":\"card\",\"currency\":\"NGN\",\"ip_address\":\"98.97.76.199\",\"metadata\":{\"donor_name\":\"Okechukwu Ajah\",\"custom_fields\":[{\"display_name\":\"Donor Name\",\"variable_name\":\"donor_name\",\"value\":\"Okechukwu Ajah\"}],\"referrer\":\"http:\\/\\/localhost\\/\"},\"log\":{\"start_time\":1778594248,\"time_spent\":4,\"attempts\":1,\"errors\":0,\"success\":true,\"mobile\":false,\"input\":[],\"history\":[{\"type\":\"action\",\"message\":\"Attempted to pay with card\",\"time\":3},{\"type\":\"success\",\"message\":\"Successfully paid with card\",\"time\":4}]},\"fees\":160000,\"fees_split\":null,\"authorization\":{\"authorization_code\":\"AUTH_ee5dquhbop\",\"bin\":\"408408\",\"last4\":\"4081\",\"exp_month\":\"12\",\"exp_year\":\"2030\",\"channel\":\"card\",\"card_type\":\"visa \",\"bank\":\"TEST BANK\",\"country_code\":\"NG\",\"brand\":\"visa\",\"reusable\":true,\"signature\":\"SIG_B2mTJo4bMN9S1Y81MjsX\",\"account_name\":null,\"receiver_bank_account_number\":null,\"receiver_bank\":null},\"customer\":{\"id\":340395212,\"first_name\":\"\",\"last_name\":\"\",\"email\":\"ajahd887@gmail.com\",\"customer_code\":\"CUS_wg4odojeqq1gxue\",\"phone\":\"\",\"metadata\":null,\"risk_action\":\"default\",\"international_format_phone\":null},\"plan\":null,\"split\":[],\"order_id\":null,\"paidAt\":\"2026-05-12T13:57:34.000Z\",\"createdAt\":\"2026-05-12T13:57:28.000Z\",\"requested_amount\":10000000,\"pos_transaction_data\":null,\"source\":null,\"fees_breakdown\":null,\"connect\":null,\"transaction_date\":\"2026-05-12T13:57:28.000Z\",\"plan_object\":[],\"subaccount\":[]}', '2026-05-12 14:57:34', '2026-05-12 13:57:25', '2026-05-12 13:57:33'),
(40, 'Okechukwu', 'ajahd887@gmail.com', NULL, NULL, 'NGN', 200000.00, 'paystack', 'successful', '54urd8u8wf', '{\"id\":6138663176,\"domain\":\"test\",\"status\":\"success\",\"reference\":\"54urd8u8wf\",\"receipt_number\":null,\"amount\":20000000,\"message\":null,\"gateway_response\":\"Successful\",\"response_code\":\"00\",\"paid_at\":\"2026-05-12T14:19:00.000Z\",\"created_at\":\"2026-05-12T14:18:54.000Z\",\"channel\":\"card\",\"currency\":\"NGN\",\"ip_address\":\"98.97.76.199\",\"metadata\":{\"donor_name\":\"Okechukwu\",\"custom_fields\":[{\"display_name\":\"Donor Name\",\"variable_name\":\"donor_name\",\"value\":\"Okechukwu\"}],\"referrer\":\"http:\\/\\/localhost\\/\"},\"log\":{\"start_time\":1778595534,\"time_spent\":5,\"attempts\":1,\"errors\":0,\"success\":true,\"mobile\":false,\"input\":[],\"history\":[{\"type\":\"action\",\"message\":\"Attempted to pay with card\",\"time\":4},{\"type\":\"success\",\"message\":\"Successfully paid with card\",\"time\":5}]},\"fees\":200000,\"fees_split\":null,\"authorization\":{\"authorization_code\":\"AUTH_vbwbelvcmr\",\"bin\":\"408408\",\"last4\":\"4081\",\"exp_month\":\"12\",\"exp_year\":\"2030\",\"channel\":\"card\",\"card_type\":\"visa \",\"bank\":\"TEST BANK\",\"country_code\":\"NG\",\"brand\":\"visa\",\"reusable\":true,\"signature\":\"SIG_B2mTJo4bMN9S1Y81MjsX\",\"account_name\":null,\"receiver_bank_account_number\":null,\"receiver_bank\":null},\"customer\":{\"id\":340395212,\"first_name\":\"\",\"last_name\":\"\",\"email\":\"ajahd887@gmail.com\",\"customer_code\":\"CUS_wg4odojeqq1gxue\",\"phone\":\"\",\"metadata\":null,\"risk_action\":\"default\",\"international_format_phone\":null},\"plan\":null,\"split\":[],\"order_id\":null,\"paidAt\":\"2026-05-12T14:19:00.000Z\",\"createdAt\":\"2026-05-12T14:18:54.000Z\",\"requested_amount\":20000000,\"pos_transaction_data\":null,\"source\":null,\"fees_breakdown\":null,\"connect\":null,\"transaction_date\":\"2026-05-12T14:18:54.000Z\",\"plan_object\":[],\"subaccount\":[],\"receipt_sent_at\":\"2026-05-12T15:19:04+01:00\",\"receipt_recipient\":\"ajahd887@gmail.com\"}', '2026-05-12 15:19:00', '2026-05-12 14:18:51', '2026-05-12 14:19:04'),
(42, NULL, 'ajahd887@gmail.com', NULL, 'Uplifting Famiies', 'NGN', 900000.00, 'paystack', 'successful', 'DON_450318327', NULL, '2026-05-12 15:20:10', '2026-05-12 14:20:10', '2026-05-12 14:20:10'),
(43, 'Anonymous Supporter', 'ajahd887@gmail.com', NULL, NULL, 'NGN', 200000.00, 'paystack', 'successful', 'DON_97679523', '{\"id\":6138687174,\"domain\":\"test\",\"status\":\"success\",\"reference\":\"DON_97679523\",\"receipt_number\":null,\"amount\":20000000,\"message\":null,\"gateway_response\":\"Successful\",\"response_code\":\"00\",\"paid_at\":\"2026-05-12T14:30:04.000Z\",\"created_at\":\"2026-05-12T14:30:00.000Z\",\"channel\":\"card\",\"currency\":\"NGN\",\"ip_address\":\"98.97.76.199\",\"metadata\":{\"cause_id\":1,\"custom_fields\":[{\"display_name\":\"Cause\",\"variable_name\":\"cause\",\"value\":\"Save the World\"}],\"referrer\":\"http:\\/\\/localhost\\/donation-page\\/cause\\/save-the-world\"},\"log\":{\"start_time\":1778596198,\"time_spent\":5,\"attempts\":1,\"errors\":0,\"success\":true,\"mobile\":false,\"input\":[],\"history\":[{\"type\":\"action\",\"message\":\"Attempted to pay with card\",\"time\":3},{\"type\":\"success\",\"message\":\"Successfully paid with card\",\"time\":5}]},\"fees\":200000,\"fees_split\":null,\"authorization\":{\"authorization_code\":\"AUTH_cvnmlgjgvt\",\"bin\":\"408408\",\"last4\":\"4081\",\"exp_month\":\"12\",\"exp_year\":\"2030\",\"channel\":\"card\",\"card_type\":\"visa \",\"bank\":\"TEST BANK\",\"country_code\":\"NG\",\"brand\":\"visa\",\"reusable\":true,\"signature\":\"SIG_B2mTJo4bMN9S1Y81MjsX\",\"account_name\":null,\"receiver_bank_account_number\":null,\"receiver_bank\":null},\"customer\":{\"id\":340395212,\"first_name\":\"\",\"last_name\":\"\",\"email\":\"ajahd887@gmail.com\",\"customer_code\":\"CUS_wg4odojeqq1gxue\",\"phone\":\"\",\"metadata\":null,\"risk_action\":\"default\",\"international_format_phone\":null},\"plan\":null,\"split\":[],\"order_id\":null,\"paidAt\":\"2026-05-12T14:30:04.000Z\",\"createdAt\":\"2026-05-12T14:30:00.000Z\",\"requested_amount\":20000000,\"pos_transaction_data\":null,\"source\":null,\"fees_breakdown\":null,\"connect\":null,\"transaction_date\":\"2026-05-12T14:30:00.000Z\",\"plan_object\":[],\"subaccount\":[],\"campaign\":\"Save the World\",\"cause_id\":1,\"receipt_sent_at\":\"2026-05-12T15:30:09+01:00\",\"receipt_recipient\":\"ajahd887@gmail.com\"}', '2026-05-12 15:30:04', '2026-05-12 14:30:04', '2026-05-12 14:30:09');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(190) NOT NULL,
  `slug` varchar(190) NOT NULL,
  `summary` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `venue` varchar(190) DEFAULT NULL,
  `city` varchar(120) DEFAULT NULL,
  `event_start` datetime NOT NULL,
  `event_end` datetime DEFAULT NULL,
  `registration_url` varchar(255) DEFAULT NULL,
  `status` enum('draft','published','cancelled','completed') DEFAULT 'draft',
  `is_featured` tinyint(1) DEFAULT 0,
  `meta_title` varchar(190) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `created_by`, `title`, `slug`, `summary`, `content`, `featured_image`, `venue`, `city`, `event_start`, `event_end`, `registration_url`, `status`, `is_featured`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(2, NULL, 'Back-to-School Outreach Launch', 'back-to-school-outreach-launch', 'Education support event focused on school kits, sponsor visibility, and volunteer activation.', '<p>The back-to-school outreach launch helps connect families with educational materials and programme support before the term begins. It is also an opportunity for sponsors and volunteers to engage directly with the initiative.</p>', 'assets/images/events/1778372349_WhatsAppImage2026-05-09at3.46.51PM2.jpeg', 'Ikeja Community Hall', 'Ikeja', '2026-09-12 09:00:00', '2026-09-12 13:00:00', 'http://localhost/donation-page/contact-us.php', 'published', 0, 'Events | Back-to-School Outreach Launch', 'Education support event focused on school kits, sponsor visibility, and volunteer activation.', '2026-05-07 16:25:48', '2026-05-10 00:19:09'),
(3, NULL, 'Health and Family Awareness Day', 'health-and-family-awareness-day', 'A public engagement event designed to connect families with guidance, support, and partner services.', '<p>This event creates a welcoming space for families to access information, practical support, and community resources through a collaborative outreach experience.</p>', 'assets/images/events/1778372327_WhatsAppImage2026-05-09at3.46.56PM.jpeg', 'Yaba Resource Center', 'Yaba', '2026-09-28 11:30:00', '2026-09-28 15:30:00', 'http://localhost/donation-page/contact-us.php', 'published', 0, 'Events | Health and Family Awareness Day', 'A public engagement event designed to connect families with guidance, support, and partner services.', '2026-05-07 16:25:48', '2026-05-10 00:18:47'),
(4, NULL, 'Partners and Sponsors Impact Brunch', 'partners-and-sponsors-impact-brunch', 'A relationship-building event for sharing programme updates, donor stories, and sponsorship opportunities.', '<p>This brunch format is designed for sponsor conversations, programme visibility, and stronger strategic relationships with long-term supporters.</p>', 'assets/images/events/1778372092_WhatsAppImage2026-05-09at3.46.55PM.jpeg', 'Victoria Island Conference Hub', 'Victoria Island', '2026-10-15 13:00:00', '2026-10-15 16:00:00', 'http://localhost/donation-page/contact-us.php', 'published', 0, 'Events | Partners and Sponsors Impact Brunch', 'A relationship-building event for sharing programme updates, donor stories, and sponsorship opportunities.', '2026-05-07 16:25:48', '2026-05-10 00:14:52');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` longtext NOT NULL,
  `category` varchar(120) DEFAULT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `category`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'How can someone support the organisation?', 'Support can come through donations, sponsorships, volunteering, media partnerships, or programme collaboration.', 'General', 'published', 1, '2026-05-07 16:25:49', '2026-05-07 16:25:49'),
(2, 'Can partners sponsor a specific project or campaign?', 'Yes. Sponsors can support specific projects, campaigns, or public events depending on the organisation???s current priorities.', 'Partners', 'published', 2, '2026-05-07 16:25:49', '2026-05-07 16:25:49'),
(3, 'How will updates be shared with supporters?', 'Updates can be shared through the gallery, programme pages, event summaries, and news stories published on the platform.', 'Communications', 'published', 3, '2026-05-07 16:25:49', '2026-05-07 16:25:49');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_items`
--

CREATE TABLE `gallery_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(190) NOT NULL,
  `media_type` enum('photo','video') DEFAULT 'photo',
  `media_path` varchar(255) NOT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_items`
--

INSERT INTO `gallery_items` (`id`, `title`, `media_type`, `media_path`, `thumbnail_path`, `description`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Image 1', 'photo', 'assets/uploads/gallery/1778454444_WhatsAppImage2026-05-09at3.46.51PM1.jpeg', 'assets/uploads/gallery/1778454444_WhatsAppImage2026-05-09at3.46.51PM1.jpeg', '', 'published', 0, '2026-05-10 23:07:24', '2026-05-11 18:12:04'),
(2, 'image 2', 'photo', 'assets/uploads/gallery/1778455078_WhatsAppImage2026-05-09at3.46.51PM.jpeg', 'assets/uploads/gallery/1778455078_WhatsAppImage2026-05-09at3.46.51PM.jpeg', '', 'published', 0, '2026-05-10 23:17:58', '2026-05-11 18:12:04');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(120) NOT NULL,
  `title` varchar(190) NOT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `slug`, `title`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'about-us', 'About Us', 'published', NULL, NULL, '2026-05-09 17:20:42', '2026-05-09 17:20:42');

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

CREATE TABLE `partners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(190) NOT NULL,
  `partner_type` enum('partner','sponsor') DEFAULT 'partner',
  `logo_path` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `tier` varchar(80) DEFAULT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `partners`
--

INSERT INTO `partners` (`id`, `name`, `partner_type`, `logo_path`, `website_url`, `description`, `tier`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, '22222', 'partner', 'assets/images/clients/1778435940_WhatsAppImage2026-05-10at4.43.41PM-Photoroom.png', '', '', 'Lead Sponsors', 'published', 0, '2026-05-09 16:37:34', '2026-05-10 17:59:00'),
(2, 'Okechukwu', 'partner', 'assets/images/clients/1778435931_WhatsAppImage2026-05-10at4.43.41PM-Photoroom.png', '', '', 'Lead Sponsors', 'published', 0, '2026-05-09 16:41:14', '2026-05-10 17:58:51'),
(3, 'Okechukwu', 'partner', 'assets/images/clients/1778435918_WhatsAppImage2026-05-10at4.43.41PM-Photoroom.png', '', '', 'Lead Sponsors', 'published', 0, '2026-05-09 16:41:35', '2026-05-10 17:58:38');

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `donation_id` bigint(20) UNSIGNED NOT NULL,
  `gateway` enum('paystack','stripe') NOT NULL,
  `gateway_reference` varchar(190) NOT NULL,
  `transaction_type` enum('charge','verification','webhook','refund') DEFAULT 'charge',
  `amount` decimal(15,2) DEFAULT NULL,
  `currency` char(3) DEFAULT NULL,
  `payload_json` longtext DEFAULT NULL,
  `gateway_status` varchar(120) DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `author_id` bigint(20) UNSIGNED DEFAULT NULL,
  `primary_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(190) NOT NULL,
  `slug` varchar(190) NOT NULL,
  `permalink_path` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `category` varchar(120) DEFAULT NULL,
  `author_name` varchar(120) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `meta_title` varchar(190) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `author_id`, `primary_category_id`, `title`, `slug`, `permalink_path`, `excerpt`, `content`, `featured_image`, `category`, `author_name`, `status`, `meta_title`, `meta_description`, `seo_keywords`, `canonical_url`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Grant Distributions Continue to Increase', 'grant-distributions-continue-to-increase', 'blog/impact-stories/grant-distributions-continue-to-increase', 'How stronger donor coordination is helping programmes reach more families with measurable impact.', '<p>Our latest grant cycle has expanded support across education, health, and nutrition programmes. By coordinating donor reporting more effectively, the organisation is now able to serve more communities with clearer visibility into outcomes and milestones.</p><p>This article structure is database-ready, which means admins can later edit the full story, update featured media, and control publishing without touching code.</p>', 'assets/images/blogs/1778376099_WhatsAppImage2026-05-09at3.46.51PM2.jpeg', 'Impact Stories', 'Admin Team', 'published', 'Blog | Grant Distributions Continue to Increase', 'How stronger donor coordination is helping programmes reach more families with measurable impact.', 'Impact-Stories, gracious charity', 'http://localhost/donation-page/blog/impact-stories/grant-distributions-continue-to-increase', '2026-05-01 09:00:00', '2026-05-07 16:25:48', '2026-05-10 01:21:39'),
(2, 2, 2, 'Community Volunteers Drive New Outreach Success', 'community-volunteers-drive-new-outreach-success', 'blog/news/community-volunteers-drive-new-outreach-success', 'A closer look at how trained volunteers improved delivery and participation during outreach week.', '<p>Volunteer coordination played a central role in the latest outreach campaign. From beneficiary onboarding to field logistics, the team helped improve both efficiency and trust.</p><p>Future admin editing will make it easy to update this story with quotes, outcome metrics, and partner mentions.</p>', 'assets/images/blogs/1778376085_WhatsAppImage2026-05-09at3.46.55PM.jpeg', 'News', 'Communications Desk', 'published', 'Blog | Community Volunteers Drive New Outreach Success', 'A closer look at how trained volunteers improved delivery and participation during outreach week.', 'News, gracious charity', 'http://localhost/donation-page/blog/news/community-volunteers-drive-new-outreach-success', '2026-05-03 11:30:00', '2026-05-07 16:25:48', '2026-05-10 01:21:25');

-- --------------------------------------------------------

--
-- Table structure for table `post_categories`
--

CREATE TABLE `post_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `seo_title` varchar(190) DEFAULT NULL,
  `seo_description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `post_categories`
--

INSERT INTO `post_categories` (`id`, `name`, `slug`, `description`, `seo_title`, `seo_description`, `created_at`, `updated_at`) VALUES
(1, 'Impact Stories', 'impact-stories', 'Stories that show measurable programme impact and donor outcomes.', 'Impact Stories | Gracious Charity', 'Impact stories and measurable outcomes from Gracious Charity initiatives.', '2026-05-07 17:03:41', '2026-05-07 17:03:41'),
(2, 'News', 'news', 'Latest platform news, updates, and announcements.', 'News | Gracious Charity', 'Latest news and field updates from Gracious Charity.', '2026-05-07 17:03:41', '2026-05-07 17:03:41'),
(3, 'Announcements', 'announcements', 'Important programme launches, notices, and public announcements.', 'Announcements | Gracious Charity', 'Official announcements and initiative launches from Gracious Charity.', '2026-05-07 17:03:41', '2026-05-07 17:03:41');

-- --------------------------------------------------------

--
-- Table structure for table `post_tags`
--

CREATE TABLE `post_tags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `post_tags`
--

INSERT INTO `post_tags` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Community Impact', 'community-impact', '2026-05-07 17:03:41'),
(2, 'Volunteers', 'volunteers', '2026-05-07 17:03:41'),
(3, 'Education', 'education', '2026-05-07 17:03:41'),
(4, 'Donors', 'donors', '2026-05-07 17:03:41'),
(5, 'Outreach', 'outreach', '2026-05-07 17:03:41'),
(6, 'Programmes', 'programmes', '2026-05-07 17:03:41');

-- --------------------------------------------------------

--
-- Table structure for table `post_tag_map`
--

CREATE TABLE `post_tag_map` (
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `tag_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `post_tag_map`
--

INSERT INTO `post_tag_map` (`post_id`, `tag_id`, `created_at`) VALUES
(1, 1, '2026-05-10 01:21:39'),
(1, 4, '2026-05-10 01:21:39'),
(1, 6, '2026-05-10 01:21:39'),
(2, 2, '2026-05-10 01:21:25'),
(2, 5, '2026-05-10 01:21:25'),
(3, 3, '2026-05-10 01:21:15'),
(3, 6, '2026-05-10 01:21:15');

-- --------------------------------------------------------

--
-- Table structure for table `programmes`
--

CREATE TABLE `programmes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(190) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `content_type` varchar(20) NOT NULL DEFAULT 'cause',
  `category` varchar(120) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `mission_statement` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `goal_amount` decimal(15,2) DEFAULT 0.00,
  `raised_amount` decimal(15,2) DEFAULT 0.00,
  `status` enum('draft','published','completed') DEFAULT 'draft',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `programmes`
--

INSERT INTO `programmes` (`id`, `title`, `slug`, `content_type`, `category`, `summary`, `mission_statement`, `content`, `featured_image`, `goal_amount`, `raised_amount`, `status`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(1, 'Save the World', 'save-the-world', 'cause', 'Health', 'Friends at Heart Welfare Initiative Offsets Hospital Bills for Indigent Patients During Hospital Visitation', NULL, 'Friends at Heart Welfare Initiative, a humanitarian and faith-driven non-governmental organisation committed to caring for the vulnerable, carried out a compassionate hospital visitation outreach on Saturday, 4th of January, 2026, where the organisation paid outstanding medical bills for patients who had been medically discharged but were unable to leave the hospital due to financial constraints.\r\n\r\nThe outreach took place at Abia State Teaching Hospital, Aba, Abia State, where members of the organisation identified patients who had completed treatment but remained detained in hospital wards because they could not afford to settle their hospital bills.\r\n\r\n In response, Friends at Heart Welfare Initiative intervened by fully offsetting the bills, bringing relief, freedom, and renewed hope to the affected patients and their families.\r\n\r\nSpeaking during the visitation, representatives of Friends at Heart Welfare Initiative emphasized that no individual should suffer prolonged pain, emotional distress, or loss of dignity simply because of an inability to pay for medical care.\r\n\r\n The Organisation described the outreach as part of its ongoing commitment to demonstrating love, compassion, and practical support to those in critical need.\r\nMany beneficiaries expressed deep gratitude, describing the intervention as timely and life-changing.\r\n\r\n Hospital management also commended the Organisation for its empathy-driven approach and positive impact on both patients and healthcare providers.\r\n\r\nFriends at Heart Welfare Initiative remains steadfast in its mission to uplift the vulnerable, promote human dignity, and extend support to individuals and families facing hardship. \r\n\r\nThe organization calls on well-meaning individuals, partners, and stakeholders to continue supporting its humanitarian efforts to reach more lives across communities.', 'assets/images/causes/1778338449_WhatsAppImage2026-05-09at3.46.51PM.jpeg', 5000000.00, 2399998.00, 'published', NULL, NULL, '2026-05-09 14:54:09', '2026-05-12 14:30:04'),
(11, 'Uplifting Famiies', 'uplifting-famiies', 'cause', 'Health', 'DONATE TO FRIENDS AT HEART WELFARE INITIATIVE \r\n', NULL, 'Every act of kindness creates hope...\r\n\r\nAt Friends at Heart Welfare Initiative (FAHWI), we believe that no one should be abandoned in their moment of need. Your donation helps us provide support to vulnerable children, struggling families, widows, underserved communities and individuals facing hardship.\r\n', 'assets/images/causes/1778593015_WhatsAppImage2026-05-09at3.46.54PM.jpeg', 1000000.00, 1000000.00, 'published', NULL, NULL, '2026-05-12 13:36:55', '2026-05-12 14:20:10');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'super_admin', 'Full access to platform administration', '2026-05-07 16:25:48'),
(2, 'admin', 'Manage content, donations, and operations', '2026-05-07 16:25:48'),
(3, 'editor', 'Manage public content only', '2026-05-07 16:25:48'),
(4, 'finance', 'Review donation and gateway activity', '2026-05-07 16:25:48');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `setting_group` varchar(80) NOT NULL,
  `setting_key` varchar(120) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'site', 'site_name', 'Friends At Heart Welfare Initiative', '2026-05-07 16:25:48', '2026-05-10 22:17:55'),
(2, 'site', 'contact_email', 'info@fahwi.org', '2026-05-07 16:25:48', '2026-05-10 22:17:55'),
(3, 'site', 'contact_phone', '+2348037444680', '2026-05-07 16:25:48', '2026-05-12 13:47:20'),
(4, 'payments', 'paystack_public_key', '', '2026-05-07 16:25:48', '2026-05-07 16:25:48'),
(5, 'payments', 'paystack_secret_key', '', '2026-05-07 16:25:48', '2026-05-07 16:25:48'),
(6, 'payments', 'stripe_public_key', '', '2026-05-07 16:25:48', '2026-05-07 16:25:48'),
(7, 'payments', 'stripe_secret_key', '', '2026-05-07 16:25:48', '2026-05-07 16:25:48'),
(8, 'about', 'about_hero_title', 'Save Humanity with Friends At Heart Welfare Initiative', '2026-05-09 22:50:40', '2026-05-11 18:34:53'),
(9, 'about', 'about_hero_label', 'About Us', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(10, 'about', 'about_hero_desc', 'Friends at Heart Welfare Initiative exists for children sent home over unpaid school fees, patients detained by medical bills, and families carrying hardship in silence. We step into those moments with practical help, dignity and hope.', '2026-05-09 22:50:40', '2026-05-11 18:36:40'),
(11, 'about', 'about_stat_1_val', '100K+', '2026-05-09 22:50:40', '2026-05-09 23:06:38'),
(12, 'about', 'about_stat_1_label', 'Lives Impacted', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(13, 'about', 'about_stat_2_val', '12+', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(14, 'about', 'about_stat_2_label', 'Active Programs', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(15, 'about', 'about_stat_3_val', '100%', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(16, 'about', 'about_stat_3_label', 'Direct Giving', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(17, 'about', 'about_stat_4_val', '15+', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(18, 'about', 'about_stat_4_label', 'Years Service', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(19, 'about', 'about_story_title', 'Our Journey of Compassion', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(20, 'about', 'about_story_lead', 'Founded on the principles of empathy and action, FAHWI has grown into a beacon of hope.', '2026-05-09 22:50:40', '2026-05-11 18:35:59'),
(21, 'about', 'about_story_text', 'Friends At Heart Welfare Initiative started with a simple idea: that small acts of kindness can change the world. Over the years, we have expanded our reach, touching thousands of lives through education, healthcare, and community development. Our team of dedicated volunteers and staff work tirelessly to ensure that every donation makes a real difference.', '2026-05-09 22:50:40', '2026-05-11 18:51:40'),
(22, 'about', 'about_quote_text', 'The best way to find yourself is to lose yourself in the service of others. We believe in the power of collective action to solve the world\'s most pressing problems.', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(23, 'about', 'about_quote_author', 'FAHWI Founder', '2026-05-09 22:50:40', '2026-05-11 18:37:27'),
(24, 'about', 'about_quote_role', 'Executive Director', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(25, 'about', 'about_time_1_year', '2010', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(26, 'about', 'about_time_1_title', 'The Beginning', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(27, 'about', 'about_time_1_desc', 'Official creation of Friends At Heart Welfare Initiative\r\n\r\nDevelopment of the NGO’s vision, mission and core values\r\n\r\nRegistering the Organisation with Corporate Affairs Commission and other regulatory agencies \r\n\r\nFormation of leadership and volunteer team\r\n\r\nCharity visit to Ngwa Road motherless babies home, Aba, Abia State.\r\n\r\nWelfare Visit to Father Basil motherless babies home, Aba, Abia State.\r\n\r\nSupporting vulnerable children and widows\r\n\r\nConducting food and clothing distribution programs', '2026-05-09 22:50:40', '2026-05-12 19:43:31'),
(28, 'about', 'about_time_2_year', '2015', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(29, 'about', 'about_time_2_title', 'Expanding Reach', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(30, 'about', 'about_time_2_desc', 'We launched our first national campaign for children\'s education.', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(31, 'about', 'about_time_3_year', '2018', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(32, 'about', 'about_time_3_title', 'Medical Relief', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(33, 'about', 'about_time_3_desc', 'Establishment of our mobile clinics providing free healthcare in rural areas.', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(34, 'about', 'about_time_4_year', '2023', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(35, 'about', 'about_time_4_title', 'Global Impact', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(36, 'about', 'about_time_4_desc', 'Recognized for our international relief efforts and sustainable development goals.', '2026-05-09 22:50:40', '2026-05-09 23:05:02'),
(37, 'about', 'about_img_1', 'assets/images/about/about_1778367917_WhatsAppImage2026-05-09at3.46.51PM2.jpeg', '2026-05-09 22:50:41', '2026-05-09 23:05:17'),
(38, 'about', 'about_img_2', 'assets/images/about/about_1778367964_WhatsAppImage2026-05-09at3.46.51PM.jpeg', '2026-05-09 22:50:41', '2026-05-09 23:06:04'),
(39, 'about', 'about_img_3', 'assets/images/about/about_1778368244_WhatsAppImage2026-05-09at3.46.55PM1.jpeg', '2026-05-09 22:50:41', '2026-05-09 23:10:44'),
(40, 'about', 'about_story_img', 'assets/images/about/about_1778368151_WhatsAppImage2026-05-09at3.46.56PM.jpeg', '2026-05-09 22:50:41', '2026-05-09 23:09:11'),
(41, 'about', 'about_founder_img', 'assets/images/about/about_1778368221_WhatsAppImage2026-05-09at3.46.55PM.jpeg', '2026-05-09 22:50:41', '2026-05-09 23:10:21'),
(484, 'footer', 'footer_newsletter_label', 'Stay Connected', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(485, 'footer', 'footer_newsletter_button', 'Join Newsletter', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(486, 'footer', 'footer_newsletter_title', 'Get updates on outreach, events, and impact stories.', '2026-05-10 15:53:52', '2026-05-10 16:00:33'),
(487, 'footer', 'footer_brand_text', 'We create credible programmes, visible impact, and trusted partnerships that supporters can follow with confidence.', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(488, 'footer', 'footer_address', '137 Market Road, Abia, Nigeria', '2026-05-10 15:53:52', '2026-05-10 16:00:33'),
(489, 'footer', 'footer_phone', '+2348035294025', '2026-05-10 15:53:52', '2026-05-10 16:00:33'),
(490, 'footer', 'footer_email', 'info@fahwi.org', '2026-05-10 15:53:52', '2026-05-10 16:04:25'),
(491, 'footer', 'footer_hours', 'Mon-Fri / 9:00 AM - 6:00 PM', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(492, 'footer', 'footer_cta_title', 'Give us a call', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(493, 'footer', 'footer_cta_phone', '+2348037444680', '2026-05-10 15:53:52', '2026-05-10 16:00:33'),
(494, 'footer', 'footer_copyright', 'FAHWI', '2026-05-10 15:53:52', '2026-05-10 16:04:25'),
(495, 'footer', 'footer_links_title', 'Quick Links', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(496, 'footer', 'footer_link_1_label', '', '2026-05-10 15:53:52', '2026-05-10 16:06:13'),
(497, 'footer', 'footer_link_1_url', '', '2026-05-10 15:53:52', '2026-05-10 16:06:13'),
(498, 'footer', 'footer_link_2_label', '', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(499, 'footer', 'footer_link_2_url', '', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(500, 'footer', 'footer_link_3_label', '', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(501, 'footer', 'footer_link_3_url', '', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(502, 'footer', 'footer_link_4_label', '', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(503, 'footer', 'footer_link_4_url', '', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(504, 'footer', 'footer_note_title', 'Support the Mission', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(505, 'footer', 'footer_note_text', 'Support our programmes, follow new stories, and stay close to the work happening in communities.', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(506, 'footer', 'footer_note_button', 'Donate Now', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(507, 'footer', 'footer_note_url', 'donation-page.php', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(508, 'footer', 'footer_social_facebook', 'https://addmehere.gt.tc', '2026-05-10 15:53:52', '2026-05-10 16:07:18'),
(509, 'footer', 'footer_social_twitter', 'https://addmehere.gt.tc', '2026-05-10 15:53:52', '2026-05-10 16:07:18'),
(510, 'footer', 'footer_social_instagram', 'https://addmehere.gt.tc', '2026-05-10 15:53:52', '2026-05-10 16:07:18'),
(511, 'footer', 'footer_social_youtube', 'https://addmehere.gt.tc', '2026-05-10 15:53:52', '2026-05-10 16:07:18'),
(512, 'footer', 'footer_bottom_link_1_label', '', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(513, 'footer', 'footer_bottom_link_1_url', '', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(514, 'footer', 'footer_bottom_link_2_label', '', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(515, 'footer', 'footer_bottom_link_2_url', '', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(516, 'footer', 'footer_bottom_link_3_label', '', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(517, 'footer', 'footer_bottom_link_3_url', '', '2026-05-10 15:53:52', '2026-05-10 15:53:52'),
(756, 'site', 'brand_logo', 'assets/uploads/branding/logo_1778433737.png', '2026-05-10 16:46:26', '2026-05-10 17:22:17'),
(772, 'site', 'site_favicon', 'assets/uploads/branding/favicon_1778433555.png', '2026-05-10 17:19:15', '2026-05-10 17:19:15'),
(783, 'footer', 'footer_brand_name', 'Friends At Heart Welfare Initiative', '2026-05-10 17:44:40', '2026-05-10 17:44:40'),
(818, 'programme', 'programme_hero_kicker', 'Friends at Heart Welfare Initiative', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(819, 'programme', 'programme_hero_title', 'Our Programs', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(820, 'programme', 'programme_hero_intro', 'At Friends at Heart Welfare Initiative, our programs are designed to bring hope, restore dignity and provide practical support to vulnerable individuals, families and underserved communities. We believe that true compassion goes beyond words, it requires action that changes lives and creates lasting impact.\r\n\r\nOur initiatives focus on education, healthcare, empowerment, humanitarian support and community development, ensuring that no one is left behind because of poverty or lack of opportunity.', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(821, 'programme', 'programme_media_heading', 'Stories in Motion', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(822, 'programme', 'programme_media_intro', 'A visual record of compassion in action, highlighting outreach moments, community response, and the lives being touched through each programme.', '2026-05-10 22:45:33', '2026-05-10 22:55:33'),
(823, 'programme', 'programme_section_1_title', 'Educational Support Program', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(824, 'programme', 'programme_section_1_body', 'Education is one of the strongest tools for breaking the cycle of poverty. Unfortunately, many children are forced out of school because their parents or guardians cannot afford school fees and educational materials.\r\n\r\nThrough our Educational Support Program, we provide:\r\n\r\n(i) School fee sponsorship\r\n\r\n(ii) Educational materials and supplies\r\n\r\n(iii) Back-to-school support\r\n\r\n(iv) Scholarships for vulnerable children\r\n\r\n(v) Learning assistance for underserved families\r\n\r\nOur goal is to ensure that every child has the opportunity to learn, grow and pursue a brighter future regardless of financial background.\r\n\r\nWe do not just sponsor education; we restore dreams and build future leaders.', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(825, 'programme', 'programme_section_2_title', 'Hospital Bill Support Program', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(826, 'programme', 'programme_section_2_body', 'No family should lose hope because they cannot afford medical care. Many patients remain stranded in hospitals due to unpaid medical bills, while others avoid treatment completely because of poverty.\r\n\r\nThrough this program, we provide:\r\n\r\n(i) Emergency hospital bill assistance\r\n\r\n(ii) Financial support for medical treatments\r\n\r\n(iii) Support for vulnerable patients\r\n\r\n(iv) Assistance for low-income families facing health emergencies\r\n\r\nOur mission is to help save lives, reduce suffering and restore dignity to patients and their families during difficult moments.', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(827, 'programme', 'programme_section_3_title', 'Free Medical Outreach Program', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(828, 'programme', 'programme_section_3_body', 'Healthcare should not be a privilege reserved for a few. Our Free Medical Outreach Program brings healthcare services directly to underserved communities where access to quality medical care is limited.\r\n\r\nOur outreach activities include:\r\n\r\n(i) Free medical consultations\r\n\r\n(ii) Basic health screenings\r\n\r\n(iii) Blood pressure and sugar level checks\r\n\r\n(iv) Distribution of medications\r\n\r\n(v) Health education and awareness campaigns\r\n\r\n(vi) Community health sensitization\r\n\r\nThrough partnerships with healthcare professionals and volunteers, we strive to improve community health and promote healthcare practices.', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(829, 'programme', 'programme_section_4_title', 'Women and Widows Empowerment Program', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(830, 'programme', 'programme_section_4_body', 'Women and widows often carry enormous responsibilities while facing financial hardship, social neglect, and limited opportunities.\r\n\r\nThis program is focused on:\r\n\r\n(i) Financial empowerment\r\n\r\n(ii) Small business support\r\n\r\n(iii) Skills acquisition and vocational training\r\n\r\n(iv) Startup assistance for petty businesses\r\n\r\n(v) Mentorship and encouragement programs\r\n\r\nWe are committed to helping women and widows become financially independent, self-reliant and empowered to support their families with dignity.', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(831, 'programme', 'programme_section_5_title', 'Youth Empowerment and Entrepreneurship Program', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(832, 'programme', 'programme_section_5_body', 'Many young people possess talent, passion and potential but lack the resources and support needed to succeed.\r\n\r\nThrough this initiative, we provide:\r\n\r\n(i) Entrepreneurial support\r\n\r\n(ii) Startup assistance for small businesses\r\n\r\n(iii) Skills development programs\r\n\r\n(iv) Mentorship opportunities\r\n\r\n(v) Career guidance and encouragement\r\n\r\nWe believe empowered youths are the foundation of a stronger and more productive society.', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(833, 'programme', 'programme_section_6_title', 'Humanitarian and Family Support Program', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(834, 'programme', 'programme_section_6_body', 'Many families silently struggle with hunger, emotional distress and basic survival needs. Through compassionate intervention, we support vulnerable households during periods of hardship and crisis.\r\n\r\nThis program includes:\r\n\r\n(i) Food and relief support\r\n\r\n(ii) Emergency family assistance\r\n\r\n(iii) Community outreach\r\n\r\n(iv) Support for vulnerable households\r\n\r\n(v) Compassionate care initiatives\r\n\r\nOur aim is to remind struggling families that they are not forgotten and that hope still exists.', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(835, 'programme', 'programme_section_7_title', 'Community Development and Advocacy', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(836, 'programme', 'programme_section_7_body', 'We believe lasting change happens when communities are empowered collectively. Through advocacy, partnerships and community engagement, we promote social responsibility, compassion and sustainable development.\r\n\r\nOur activities include:\r\n\r\n(i) Community awareness campaigns\r\n\r\n(ii) Partnership with individuals and organisations\r\n\r\n(iii) Volunteer engagement\r\n\r\n(iv) Advocacy for vulnerable groups\r\n\r\n(v) Promotion of transparency and accountability in humanitarian service', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(837, 'programme', 'programme_section_8_title', 'Orphanage Support Program', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(838, 'programme', 'programme_section_8_body', 'Every child deserves love, care, protection and the opportunity to grow in a safe and supportive environment. Through our Orphanage Support Program, we extend compassion and practical assistance to orphanages and vulnerable children who lack parental care and support.\r\n\r\nThis program focuses on:\r\n\r\n(i) Donation of food items and relief materials\r\n\r\n(ii) Educational support for orphaned children\r\n\r\n(iii) Provision of clothing and basic necessities\r\n\r\n(iv) Medical assistance and healthcare support\r\n\r\n(v) Emotional care and social support visits\r\n\r\n(vi) Partnership with orphanage homes and caregivers\r\n\r\nWe are committed to bringing hope, joy and dignity to every child, reminding them that they are valued, loved and never forgotten.\r\n\r\nAt Friends at Heart Welfare Initiative, we believe every child deserves a future filled with hope, opportunity and compassion.', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(839, 'programme', 'programme_commitment_title', 'Our Commitment', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(840, 'programme', 'programme_commitment_body', 'At Friends at Heart Welfare Initiative, we are committed to serving humanity with integrity, compassion, transparency and accountability.\r\n\r\nEvery school fee paid, every hospital bill settled, every medical outreach organized, every orphanage home visited and every individual empowered represents a life touched and a future restored.\r\n\r\nWe believe that kindness can heal wounds, compassion can restore hope and collective action can transform communities.\r\n\r\nTogether, we can build a society where no one is abandoned in their time of need.', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(841, 'programme', 'programme_cta_title', 'Bring the Work to Life', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(842, 'programme', 'programme_cta_text', 'Our work is rooted in compassion, guided by accountability, and strengthened through consistent community impact. Each programme reflects a practical commitment to restoring dignity, expanding opportunity, and standing with those who need support most.', '2026-05-10 22:45:33', '2026-05-10 22:57:49'),
(843, 'programme', 'programme_media_1', 'assets/uploads/programme/programme_media_1_1778453469_WhatsAppVideo2026-05-09at3.47.03PM.mp4', '2026-05-10 22:45:33', '2026-05-10 22:51:09'),
(844, 'programme', 'programme_media_2', 'assets/uploads/programme/programme_media_2_1778453133_WhatsAppImage2026-05-09at3.46.55PM.jpeg', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(845, 'programme', 'programme_media_3', 'assets/uploads/programme/programme_media_3_1778453133_WhatsAppImage2026-05-09at3.46.56PM.jpeg', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(846, 'programme', 'programme_media_4', 'assets/uploads/programme/programme_media_4_1778453133_WhatsAppImage2026-05-09at3.46.51PM2.jpeg', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(847, 'programme', 'programme_media_5', 'assets/uploads/programme/programme_media_5_1778453133_WhatsAppImage2026-05-09at3.46.51PM.jpeg', '2026-05-10 22:45:33', '2026-05-10 22:45:33'),
(924, 'programme', 'programme_media_6', 'assets/uploads/programme/programme_media_6_1778453797_WhatsAppImage2026-05-09at3.46.51PM1.jpeg', '2026-05-10 22:56:37', '2026-05-10 22:56:37'),
(950, 'gallery', 'gallery_hero_kicker', 'Visual Impact', '2026-05-10 23:16:52', '2026-05-10 23:16:52'),
(951, 'gallery', 'gallery_hero_title', 'Stories from the field, captured with clarity and purpose.', '2026-05-10 23:16:52', '2026-05-10 23:16:52'),
(952, 'gallery', 'gallery_hero_description', 'Browse a live collection of outreach moments, campaign highlights, and field documentation curated directly from the admin gallery.', '2026-05-10 23:16:52', '2026-05-10 23:16:52'),
(953, 'gallery', 'gallery_primary_button_label', 'Support A Story', '2026-05-10 23:16:52', '2026-05-10 23:16:52'),
(954, 'gallery', 'gallery_primary_button_url', 'donation-page.php', '2026-05-10 23:16:52', '2026-05-10 23:16:52'),
(955, 'gallery', 'gallery_secondary_button_label', 'Request Media Pack', '2026-05-10 23:16:52', '2026-05-10 23:16:52'),
(956, 'gallery', 'gallery_secondary_button_url', 'contact-us.php', '2026-05-10 23:16:52', '2026-05-10 23:16:52'),
(957, 'gallery', 'gallery_featured_kicker', 'Field Update', '2026-05-10 23:16:52', '2026-05-10 23:16:52'),
(958, 'gallery', 'gallery_featured_item_id', '', '2026-05-10 23:16:52', '2026-05-10 23:17:41'),
(959, 'gallery', 'gallery_featured_title', 'Community outreach moments worth revisiting.', '2026-05-10 23:16:52', '2026-05-10 23:16:52'),
(960, 'gallery', 'gallery_featured_description', 'The latest published gallery item appears here automatically as a featured field update.', '2026-05-10 23:16:52', '2026-05-10 23:16:52'),
(961, 'gallery', 'gallery_collection_kicker', 'Featured Collection', '2026-05-10 23:16:52', '2026-05-10 23:16:52'),
(962, 'gallery', 'gallery_collection_title', 'Campaigns, events, and programme highlights', '2026-05-10 23:16:52', '2026-05-10 23:16:52'),
(1031, 'about', 'about_values_title', 'Our Core Values', '2026-05-11 18:34:53', '2026-05-11 18:34:53'),
(1032, 'about', 'about_values_intro', 'The principles that guide how we serve, how we lead and how we remain accountable to the people and communities we support.', '2026-05-11 18:34:53', '2026-05-11 18:34:53'),
(1033, 'about', 'about_values_list', 'Compassion|We show love, empathy and care to individuals and communities in need.\r\nIntegrity|We uphold honesty, accountability and strong moral principles in all we do.\r\nTransparency|We remain open, trustworthy and responsible in our operations and use of resources.\r\nEquality|We believe every individual deserves fairness, dignity and equal opportunity regardless of background or status.\r\nVolunteerism|We encourage selfless service, teamwork and community participation to create lasting impact.\r\nEmpowerment|We believe in equipping people with opportunities, support and resources for a better future.\r\nExcellence|We strive for professionalism, quality and impactful service delivery in every outreach and project.\r\nInclusion|We promote unity, acceptance and equal participation for all members of society.\r\nTeamwork|We believe collaboration, unity and partnership strengthen our impact and mission.\r\nService To Humanity|We are dedicated to improving lives, restoring hope and supporting the vulnerable through humanitarian service.', '2026-05-11 18:34:53', '2026-05-11 18:34:53'),
(1165, 'about', 'about_home_intro_label', 'Friends at Heart Welfare Initiative', '2026-05-12 11:19:41', '2026-05-12 11:19:41'),
(1166, 'about', 'about_home_intro_title', 'Compassion in action for children, families and communities.', '2026-05-12 11:19:41', '2026-05-12 11:19:41'),
(1167, 'about', 'about_home_intro_desc', 'We support children kept out of school by unpaid fees, patients burdened by medical bills, and families facing severe hardship. Every donation helps us restore dignity, protect hope and deliver practical care where it is needed most.', '2026-05-12 11:19:41', '2026-05-12 11:19:41'),
(1168, 'about', 'about_home_intro_stat_1_value', '100', '2026-05-12 11:19:41', '2026-05-12 11:19:41'),
(1169, 'about', 'about_home_intro_stat_1_label', 'Lives Supported', '2026-05-12 11:19:41', '2026-05-12 11:19:41'),
(1170, 'about', 'about_home_intro_stat_2_value', '850', '2026-05-12 11:19:41', '2026-05-12 11:19:41'),
(1171, 'about', 'about_home_intro_stat_2_label', 'Community Donations', '2026-05-12 11:19:41', '2026-05-12 11:19:41'),
(1204, 'volunteer', 'volunteer_page_title', 'Volunteer With Friends at Heart Welfare Initiative', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1205, 'volunteer', 'volunteer_page_description', 'Support outreach, community care, and practical compassion by serving as a volunteer with Friends at Heart Welfare Initiative.', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1206, 'volunteer', 'volunteer_hero_label', 'Serve With Us', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1207, 'volunteer', 'volunteer_hero_title', 'Volunteer with Friends at Heart Welfare Initiative', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1208, 'volunteer', 'volunteer_hero_description', 'Join a compassionate network of volunteers helping children, patients and underserved families through practical community support.', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1209, 'volunteer', 'volunteer_intro_title', 'Where your time can make a real difference', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1210, 'volunteer', 'volunteer_intro_description', 'Our volunteers support outreach logistics, beneficiary care, event coordination, fundraising campaigns and administrative follow-through. We welcome people who are dependable, compassionate and ready to serve with dignity.', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1211, 'volunteer', 'volunteer_impact_title', 'Why people volunteer with us', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1212, 'volunteer', 'volunteer_impact_lines', 'Serve people directly with empathy and purpose.\r\nGain meaningful field and community experience.\r\nJoin a mission-driven team that values accountability and compassion.', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1213, 'volunteer', 'volunteer_primary_cta_label', 'Apply to Volunteer', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1214, 'volunteer', 'volunteer_primary_cta_url', 'contact-us.php', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1215, 'volunteer', 'volunteer_secondary_cta_label', 'Speak With Our Team', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1216, 'volunteer', 'volunteer_secondary_cta_url', 'contact-us.php', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1217, 'volunteer', 'volunteer_final_cta_title', 'Ready to serve with us?', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1218, 'volunteer', 'volunteer_final_cta_description', 'Take the next step and let us know how you would like to contribute your time, energy and skills.', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1219, 'volunteer', 'volunteer_opportunities_title', 'Volunteer opportunities', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1220, 'volunteer', 'volunteer_opportunities_intro', 'Choose the kind of contribution that best matches your strength, schedule and passion.', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1221, 'volunteer', 'volunteer_opportunity_1_title', '', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1222, 'volunteer', 'volunteer_opportunity_1_description', '', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1223, 'volunteer', 'volunteer_opportunity_2_title', '', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1224, 'volunteer', 'volunteer_opportunity_2_description', '', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1225, 'volunteer', 'volunteer_opportunity_3_title', '', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1226, 'volunteer', 'volunteer_opportunity_3_description', '', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1227, 'volunteer', 'volunteer_process_title', 'How joining works', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1228, 'volunteer', 'volunteer_process_intro', 'We keep the process simple so committed volunteers can get started clearly and confidently.', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1229, 'volunteer', 'volunteer_step_1_title', '', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1230, 'volunteer', 'volunteer_step_1_description', '', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1231, 'volunteer', 'volunteer_step_2_title', '', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1232, 'volunteer', 'volunteer_step_2_description', '', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1233, 'volunteer', 'volunteer_step_3_title', '', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1234, 'volunteer', 'volunteer_step_3_description', '', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1235, 'volunteer', 'volunteer_hero_image', 'assets/uploads/volunteer/volunteer_hero_image_1778585722_WhatsAppImage2026-05-09at3.46.52PM1.jpeg', '2026-05-12 11:35:22', '2026-05-12 11:35:22'),
(1236, 'partners', 'partners_registration_clicks', '2', '2026-05-12 12:57:40', '2026-05-12 13:02:27'),
(1237, 'partners', 'partners_clicks_test_click', '1', '2026-05-12 12:57:40', '2026-05-12 12:57:40'),
(1239, 'partners', 'partners_clicks_join_now', '1', '2026-05-12 13:02:27', '2026-05-12 13:02:27'),
(1240, 'about', 'about_home_slider_kicker', 'Restoring Hope', '2026-05-12 13:22:51', '2026-05-12 13:22:51'),
(1241, 'about', 'about_home_slider_title', 'For Children And Families', '2026-05-12 13:22:51', '2026-05-12 13:24:41'),
(1242, 'about', 'about_home_slider_primary_label', 'Join Us Now', '2026-05-12 13:22:51', '2026-05-12 13:22:51'),
(1243, 'about', 'about_home_slider_primary_url', 'causes-list.php', '2026-05-12 13:22:51', '2026-05-12 13:22:51'),
(1244, 'about', 'about_home_slider_video_label', 'Watch the video', '2026-05-12 13:22:51', '2026-05-12 13:22:51'),
(1245, 'about', 'about_home_slider_video_url', 'https://player.vimeo.com/video/7449107', '2026-05-12 13:22:51', '2026-05-12 13:22:51'),
(1346, 'about', 'about_home_donation_highlight_value', '100', '2026-05-12 19:41:41', '2026-05-12 19:41:41'),
(1347, 'about', 'about_home_donation_highlight_label', 'Lives Supported', '2026-05-12 19:41:41', '2026-05-12 19:41:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_admins_role` (`role_id`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notifications_admin` (`admin_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_audit_logs_admin` (`admin_id`);

--
-- Indexes for table `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `content_blocks`
--
ALTER TABLE `content_blocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_content_block` (`page_id`,`block_key`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_reference` (`payment_reference`),
  ADD UNIQUE KEY `payment_reference_2` (`payment_reference`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_events_created_by` (`created_by`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_items`
--
ALTER TABLE `gallery_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_pages_created_by` (`created_by`),
  ADD KEY `fk_pages_updated_by` (`updated_by`);

--
-- Indexes for table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payment_transactions_donation` (`donation_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `idx_posts_permalink_path` (`permalink_path`),
  ADD KEY `fk_posts_author` (`author_id`);

--
-- Indexes for table `post_categories`
--
ALTER TABLE `post_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `post_tags`
--
ALTER TABLE `post_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `post_tag_map`
--
ALTER TABLE `post_tag_map`
  ADD PRIMARY KEY (`post_id`,`tag_id`);

--
-- Indexes for table `programmes`
--
ALTER TABLE `programmes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_comments`
--
ALTER TABLE `blog_comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `content_blocks`
--
ALTER TABLE `content_blocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gallery_items`
--
ALTER TABLE `gallery_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `partners`
--
ALTER TABLE `partners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `post_categories`
--
ALTER TABLE `post_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `post_tags`
--
ALTER TABLE `post_tags`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `programmes`
--
ALTER TABLE `programmes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1429;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `fk_admins_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD CONSTRAINT `fk_notifications_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_logs_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`);

--
-- Constraints for table `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD CONSTRAINT `blog_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blog_comments_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `blog_comments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `content_blocks`
--
ALTER TABLE `content_blocks`
  ADD CONSTRAINT `fk_content_blocks_page` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_events_created_by` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`);

--
-- Constraints for table `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `fk_pages_created_by` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`),
  ADD CONSTRAINT `fk_pages_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `admins` (`id`);

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `fk_payment_transactions_donation` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_author` FOREIGN KEY (`author_id`) REFERENCES `admins` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
