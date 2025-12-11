-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 11, 2025 at 08:33 PM
-- Server version: 8.0.31
-- PHP Version: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `job_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

DROP TABLE IF EXISTS `applications`;
CREATE TABLE IF NOT EXISTS `applications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_id` int NOT NULL,
  `user_id` int NOT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `cover_letter` text,
  `applied_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_job` (`job_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_applied` (`applied_at`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `job_id`, `user_id`, `resume_path`, `cover_letter`, `applied_at`) VALUES
(1, 1, 2, 'uploads/resumes/resume_2_1765484611.pdf', 'We are seeking an experienced Senior PHP Developer to join our dynamic team. You will lead the development of robust backend systems, mentor junior developers, and collaborate with frontend teams to build scalable web applications. This role is perfect for someone passionate about clean code, performance optimization, and modern PHP frameworks.\r\nKey Responsibilities\r\n\r\nDesign, develop, and maintain server-side logic using PHP and frameworks like Laravel or Symfony.\r\nIntegrate user-facing elements with server-side logic, working closely with frontend developers.', '2025-12-11 20:23:31');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `company` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `salary` varchar(50) DEFAULT 'Not disclosed',
  `type` varchar(50) DEFAULT 'Full-time',
  `experience_level` varchar(50) DEFAULT 'Not specified',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `company`, `location`, `description`, `created_at`, `salary`, `type`, `experience_level`) VALUES
(1, 'PHP Developer', 'Hibertech Solutions', 'Lagos/Remote', 'We are seeking an experienced Senior PHP Developer to join our dynamic team. You will lead the development of robust backend systems, mentor junior developers, and collaborate with frontend teams to build scalable web applications. This role is perfect for someone passionate about clean code, performance optimization, and modern PHP frameworks.\r\n\r\nKey Responsibilities\r\nDesign, develop, and maintain server-side logic using PHP and frameworks like Laravel or Symfony.\r\nIntegrate user-facing elements with server-side logic, working closely with frontend developers.\r\n\r\nRequired Skills and Qualifications\r\nStrong proficiency in PHP 8+, OOP concepts, and MVC architecture.\r\nExpertise in Laravel (or similar frameworks like Symfony/CodeIgniter).\r\nSolid understanding of front-end technologies: HTML5, CSS3, JavaScript, and AJAX.\r\n\r\nNice-to-Have\r\nExperience with cloud services (AWS/Azure).\r\nKnowledge of JavaScript frameworks (Vue.js/React).\r\nContributions to open-source projects or a strong GitHub portfolio.\r\n\r\nWhat We Offer\r\nCompetitive salary and benefits.\r\nFlexible remote work options.\r\nOpportunities for professional growth and leadership roles.\r\nCollaborative and innovative work environment.\r\n\r\nIf you\'re a PHP expert ready to take on challenging projects, apply now and help us build the future of web applications!', '2025-12-11 19:47:20', '80,000', 'Part-time', 'Fresher');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `experience` varchar(50) DEFAULT NULL,
  `education` varchar(100) DEFAULT NULL,
  `skills` text,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `phone`, `location`, `experience`, `education`, `skills`, `password`, `role`, `created_at`) VALUES
(1, 'Super Admin', 'admin', 'admin@jobportal.com', '08112211232', NULL, NULL, NULL, NULL, '$2y$10$FMqSsLOaAl0tUfsGPjcro.08YsZA3pQZcdBY8GWJ7N2DV.XOKhRyq', 'admin', '2025-12-11 07:07:23'),
(2, 'Fawwas Ayomide Olajide', 'emblem', 'emblemprogram08@yahoo.com', '+2347089410451', 'Osun', '1-3 years', 'HND in Computer Science', 'PHP, Laravel, JavaScript, MySQL, Bootstrap', '$2y$10$PZobGWsMcFCewAA4Zmfyp.YjKJirwbMdF4Ip2i2ui13Ps9Xmbtbxe', 'user', '2025-12-11 20:06:14');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
