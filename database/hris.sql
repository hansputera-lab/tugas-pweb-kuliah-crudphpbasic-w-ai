/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.18-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: hris
-- ------------------------------------------------------
-- Server version	10.11.18-MariaDB-0+deb12u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `subject_type` varchar(255) NOT NULL,
  `subject_id` bigint(20) unsigned NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_subject` (`subject_type`,`subject_id`),
  KEY `index_user` (`user_id`),
  KEY `index_created` (`created_at`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appraisal_details`
--

DROP TABLE IF EXISTS `appraisal_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `appraisal_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `appraisal_id` bigint(20) unsigned NOT NULL,
  `kpi_id` bigint(20) unsigned NOT NULL,
  `score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `weight` int(11) NOT NULL DEFAULT 0,
  `achievement` text DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appraisal_details_appraisal_id_foreign` (`appraisal_id`),
  KEY `appraisal_details_kpi_id_foreign` (`kpi_id`),
  CONSTRAINT `appraisal_details_appraisal_id_foreign` FOREIGN KEY (`appraisal_id`) REFERENCES `appraisals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appraisal_details_kpi_id_foreign` FOREIGN KEY (`kpi_id`) REFERENCES `kpis` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appraisal_details`
--

LOCK TABLES `appraisal_details` WRITE;
/*!40000 ALTER TABLE `appraisal_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `appraisal_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appraisals`
--

DROP TABLE IF EXISTS `appraisals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `appraisals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `period` varchar(255) NOT NULL,
  `reviewer_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('draft','completed') NOT NULL DEFAULT 'draft',
  `total_score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `final_grade` varchar(255) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `appraisals_employee_id_period_unique` (`employee_id`,`period`),
  KEY `appraisals_reviewer_id_foreign` (`reviewer_id`),
  CONSTRAINT `appraisals_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appraisals_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appraisals`
--

LOCK TABLES `appraisals` WRITE;
/*!40000 ALTER TABLE `appraisals` DISABLE KEYS */;
/*!40000 ALTER TABLE `appraisals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendances`
--

DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `status` enum('present','absent','late','half_day') NOT NULL DEFAULT 'absent',
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendances_employee_id_date_unique` (`employee_id`,`date`),
  CONSTRAINT `attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendances`
--

LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;
INSERT INTO `attendances` VALUES
(1,1,'2026-07-15','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(2,2,'2026-07-15','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(3,3,'2026-07-15','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(4,4,'2026-07-15','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(5,5,'2026-07-15','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(6,6,'2026-07-15','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(7,7,'2026-07-15',NULL,NULL,'absent',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(8,8,'2026-07-15',NULL,NULL,'absent',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(9,9,'2026-07-15','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(10,1,'2026-07-14','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(11,2,'2026-07-14','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(12,3,'2026-07-14','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(13,4,'2026-07-14','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(14,5,'2026-07-14','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(15,6,'2026-07-14','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(16,7,'2026-07-14','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(17,8,'2026-07-14','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(18,9,'2026-07-14','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(19,1,'2026-07-13','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(20,2,'2026-07-13','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(21,3,'2026-07-13','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(22,4,'2026-07-13',NULL,NULL,'absent',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(23,5,'2026-07-13','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(24,6,'2026-07-13',NULL,NULL,'absent',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(25,7,'2026-07-13','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(26,8,'2026-07-13',NULL,NULL,'absent',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(27,9,'2026-07-13','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(28,1,'2026-07-12','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(29,2,'2026-07-12','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(30,3,'2026-07-12','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(31,4,'2026-07-12','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(32,5,'2026-07-12','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(33,6,'2026-07-12','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(34,7,'2026-07-12',NULL,NULL,'absent',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(35,8,'2026-07-12',NULL,NULL,'absent',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(36,9,'2026-07-12','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(37,1,'2026-07-11',NULL,NULL,'absent',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(38,2,'2026-07-11',NULL,NULL,'absent',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(39,3,'2026-07-11','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(40,4,'2026-07-11','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(41,5,'2026-07-11',NULL,NULL,'absent',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(42,6,'2026-07-11','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(43,7,'2026-07-11','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(44,8,'2026-07-11','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(45,9,'2026-07-11','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(46,1,'2026-07-10','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(47,2,'2026-07-10','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(48,3,'2026-07-10','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(49,4,'2026-07-10','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(50,5,'2026-07-10',NULL,NULL,'absent',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(51,6,'2026-07-10','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(52,7,'2026-07-10','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(53,8,'2026-07-10','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(54,9,'2026-07-10','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(55,1,'2026-07-09','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(56,2,'2026-07-09','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(57,3,'2026-07-09','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(58,4,'2026-07-09','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(59,5,'2026-07-09','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(60,6,'2026-07-09','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(61,7,'2026-07-09',NULL,NULL,'absent',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(62,8,'2026-07-09','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(63,9,'2026-07-09','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(64,1,'2026-07-08','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(65,2,'2026-07-08','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(66,3,'2026-07-08','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(67,4,'2026-07-08','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(68,5,'2026-07-08','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(69,6,'2026-07-08','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(70,7,'2026-07-08','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(71,8,'2026-07-08','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(72,9,'2026-07-08','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(73,1,'2026-07-07','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(74,2,'2026-07-07','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(75,3,'2026-07-07','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(76,4,'2026-07-07','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(77,5,'2026-07-07','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(78,6,'2026-07-07','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(79,7,'2026-07-07','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(80,8,'2026-07-07','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(81,9,'2026-07-07',NULL,NULL,'absent',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(82,1,'2026-07-06','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(83,2,'2026-07-06','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(84,3,'2026-07-06',NULL,NULL,'absent',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(85,4,'2026-07-06','08:20:00','17:00:00','late',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(86,5,'2026-07-06','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(87,6,'2026-07-06','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(88,7,'2026-07-06','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(89,8,'2026-07-06','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(90,9,'2026-07-06','07:45:00','17:00:00','present',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46');
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `candidates`
--

DROP TABLE IF EXISTS `candidates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `candidates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `source` enum('job_board','referral','social_media','direct_apply','other') NOT NULL DEFAULT 'direct_apply',
  `resume_path` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `candidates`
--

LOCK TABLES `candidates` WRITE;
/*!40000 ALTER TABLE `candidates` DISABLE KEYS */;
/*!40000 ALTER TABLE `candidates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES
(1,'Human Resources','HR','Human Resources Department','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(2,'Information Technology','IT','IT Department','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(3,'Finance','FIN','Finance and Accounting','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(4,'Marketing','MKT','Marketing and Sales','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(5,'Operations','OPS','Operations Department','2026-07-16 13:42:46','2026-07-16 13:42:46');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_positions`
--

DROP TABLE IF EXISTS `employee_positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_positions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `position_id` bigint(20) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_positions_employee_id_foreign` (`employee_id`),
  KEY `employee_positions_position_id_foreign` (`position_id`),
  CONSTRAINT `employee_positions_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_positions_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_positions`
--

LOCK TABLES `employee_positions` WRITE;
/*!40000 ALTER TABLE `employee_positions` DISABLE KEYS */;
INSERT INTO `employee_positions` VALUES
(1,1,5,'2021-03-01',NULL,1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(2,2,7,'2023-01-15',NULL,1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(3,3,3,'2022-06-01',NULL,1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(4,4,5,'2020-01-01',NULL,1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(5,5,3,'2019-06-01',NULL,1,'2026-07-16 13:42:46','2026-07-16 13:42:46');
/*!40000 ALTER TABLE `employee_positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_shifts`
--

DROP TABLE IF EXISTS `employee_shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_shifts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `shift_id` bigint(20) unsigned NOT NULL,
  `effective_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_shifts_shift_id_foreign` (`shift_id`),
  KEY `employee_shifts_employee_id_effective_date_index` (`employee_id`,`effective_date`),
  CONSTRAINT `employee_shifts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_shifts_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_shifts`
--

LOCK TABLES `employee_shifts` WRITE;
/*!40000 ALTER TABLE `employee_shifts` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `nip` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `gender` enum('L','P') NOT NULL,
  `date_of_birth` date NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `join_date` date NOT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `department_id` bigint(20) unsigned NOT NULL,
  `manager_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_nip_unique` (`nip`),
  KEY `employees_user_id_foreign` (`user_id`),
  KEY `employees_department_id_foreign` (`department_id`),
  KEY `employees_manager_id_foreign` (`manager_id`),
  CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `employees_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES
(1,5,'EMP003','Budi Santoso','L','1990-03-10','081234567892','Jl. Buah Batu No. 50, Bandung',NULL,'2021-03-01','active',2,NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(2,3,'EMP001','Employee User','L','1995-06-15','081234567890','Jl. Merdeka No. 10, Bandung',NULL,'2023-01-15','active',2,1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(3,4,'EMP002','Rina Susanti','P','1993-08-20','081234567891','Jl. Dago No. 25, Bandung',NULL,'2022-06-01','active',1,NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(4,6,'EMP004','Manager User','L','1988-11-20','081234567893','Jl. Setiabudi No. 15, Bandung',NULL,'2020-01-01','active',2,1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(5,2,'EMP005','HR Manager','P','1985-05-15','081234567894','Jl. Asia Afrika No. 8, Bandung',NULL,'2019-06-01','active',1,NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(6,7,'EMP006','Payroll Specialist','P','1992-07-22','081234567895','Jl. Cihampelas No. 30, Bandung',NULL,'2022-03-01','active',3,NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(7,8,'EMP007','Executive User','L','1978-01-10','081234567896','Jl. Braga No. 5, Bandung',NULL,'2018-01-01','active',1,NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(8,9,'EMP008','Recruiter User','P','1994-09-05','081234567897','Jl. Riau No. 12, Bandung',NULL,'2023-06-01','active',1,NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(9,10,'EMP009','IT Admin','L','1987-04-18','081234567898','Jl. Sukajadi No. 22, Bandung',NULL,'2021-01-15','active',2,NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_categories`
--

DROP TABLE IF EXISTS `expense_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `requires_receipt` tinyint(1) NOT NULL DEFAULT 1,
  `approval_levels` int(11) NOT NULL DEFAULT 2,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_categories`
--

LOCK TABLES `expense_categories` WRITE;
/*!40000 ALTER TABLE `expense_categories` DISABLE KEYS */;
INSERT INTO `expense_categories` VALUES
(1,'Travel','Transport and travel expenses',1,2,1,1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(2,'Meals','Meal and entertainment',1,1,1,2,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(3,'Medical','Health and medical expenses',1,2,1,3,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(4,'Training','Courses and certifications',1,3,1,4,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(5,'Office Supplies','Work equipment and supplies',0,1,1,5,'2026-07-16 13:42:46','2026-07-16 13:42:46');
/*!40000 ALTER TABLE `expense_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback_360`
--

DROP TABLE IF EXISTS `feedback_360`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedback_360` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `appraisal_id` bigint(20) unsigned NOT NULL,
  `reviewer_id` bigint(20) unsigned DEFAULT NULL,
  `reviewer_name` varchar(255) NOT NULL,
  `relationship` enum('manager','peer','subordinate','self') NOT NULL DEFAULT 'peer',
  `rating` decimal(5,2) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `feedback_360_appraisal_id_foreign` (`appraisal_id`),
  KEY `feedback_360_reviewer_id_foreign` (`reviewer_id`),
  CONSTRAINT `feedback_360_appraisal_id_foreign` FOREIGN KEY (`appraisal_id`) REFERENCES `appraisals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `feedback_360_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedback_360`
--

LOCK TABLES `feedback_360` WRITE;
/*!40000 ALTER TABLE `feedback_360` DISABLE KEYS */;
/*!40000 ALTER TABLE `feedback_360` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `interviews`
--

DROP TABLE IF EXISTS `interviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `interviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_application_id` bigint(20) unsigned NOT NULL,
  `interviewer_id` bigint(20) unsigned NOT NULL,
  `scheduled_at` timestamp NOT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 60,
  `location` varchar(255) DEFAULT NULL,
  `meeting_link` varchar(255) DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled','no_show') NOT NULL DEFAULT 'scheduled',
  `feedback` text DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interviews_job_application_id_foreign` (`job_application_id`),
  KEY `interviews_interviewer_id_foreign` (`interviewer_id`),
  CONSTRAINT `interviews_interviewer_id_foreign` FOREIGN KEY (`interviewer_id`) REFERENCES `users` (`id`),
  CONSTRAINT `interviews_job_application_id_foreign` FOREIGN KEY (`job_application_id`) REFERENCES `job_applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interviews`
--

LOCK TABLES `interviews` WRITE;
/*!40000 ALTER TABLE `interviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `interviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_applications`
--

DROP TABLE IF EXISTS `job_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_posting_id` bigint(20) unsigned NOT NULL,
  `candidate_id` bigint(20) unsigned NOT NULL,
  `status` enum('applied','screening','interview','offer','hired','rejected') NOT NULL DEFAULT 'applied',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `job_applications_job_posting_id_candidate_id_unique` (`job_posting_id`,`candidate_id`),
  KEY `job_applications_candidate_id_foreign` (`candidate_id`),
  KEY `job_applications_reviewed_by_foreign` (`reviewed_by`),
  CONSTRAINT `job_applications_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_applications_job_posting_id_foreign` FOREIGN KEY (`job_posting_id`) REFERENCES `job_postings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_applications_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_applications`
--

LOCK TABLES `job_applications` WRITE;
/*!40000 ALTER TABLE `job_applications` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_postings`
--

DROP TABLE IF EXISTS `job_postings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_postings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned NOT NULL,
  `position_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `requirements` text DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contract','internship') NOT NULL DEFAULT 'full_time',
  `salary_min` decimal(15,2) DEFAULT NULL,
  `salary_max` decimal(15,2) DEFAULT NULL,
  `status` enum('draft','open','closed') NOT NULL DEFAULT 'draft',
  `posted_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_postings_department_id_foreign` (`department_id`),
  KEY `job_postings_position_id_foreign` (`position_id`),
  KEY `job_postings_created_by_foreign` (`created_by`),
  CONSTRAINT `job_postings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `job_postings_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `job_postings_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_postings`
--

LOCK TABLES `job_postings` WRITE;
/*!40000 ALTER TABLE `job_postings` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_postings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kpis`
--

DROP TABLE IF EXISTS `kpis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `kpis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'competency',
  `target_value` decimal(10,2) DEFAULT NULL,
  `weight` int(11) NOT NULL DEFAULT 0,
  `measurement_unit` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kpis`
--

LOCK TABLES `kpis` WRITE;
/*!40000 ALTER TABLE `kpis` DISABLE KEYS */;
INSERT INTO `kpis` VALUES
(1,'Code Quality','Clean, maintainable, well-tested code','competency',90.00,25,'%',1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(2,'Productivity','Tasks delivered per sprint','goal',100.00,25,'tasks',1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(3,'Team Collaboration','Communication and collaboration','behavior',90.00,20,'%',1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(4,'Initiative','Proactiveness and problem solving','behavior',85.00,15,'%',1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(5,'Attendance & Discipline','Punctuality and adherence to policy','competency',95.00,15,'%',1,'2026-07-16 13:42:46','2026-07-16 13:42:46');
/*!40000 ALTER TABLE `kpis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_balances`
--

DROP TABLE IF EXISTS `leave_balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_balances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `leave_type_id` bigint(20) unsigned NOT NULL,
  `year` year(4) NOT NULL,
  `total_days` int(11) NOT NULL DEFAULT 0,
  `used_days` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_balances_employee_id_leave_type_id_year_unique` (`employee_id`,`leave_type_id`,`year`),
  KEY `leave_balances_leave_type_id_foreign` (`leave_type_id`),
  CONSTRAINT `leave_balances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leave_balances_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_balances`
--

LOCK TABLES `leave_balances` WRITE;
/*!40000 ALTER TABLE `leave_balances` DISABLE KEYS */;
INSERT INTO `leave_balances` VALUES
(1,1,1,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(2,1,2,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(3,1,3,2026,0,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(4,1,4,2026,90,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(5,1,5,2026,3,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(6,2,1,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(7,2,2,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(8,2,3,2026,0,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(9,2,4,2026,90,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(10,2,5,2026,3,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(11,3,1,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(12,3,2,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(13,3,3,2026,0,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(14,3,4,2026,90,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(15,3,5,2026,3,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(16,4,1,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(17,4,2,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(18,4,3,2026,0,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(19,4,4,2026,90,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(20,4,5,2026,3,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(21,5,1,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(22,5,2,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(23,5,3,2026,0,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(24,5,4,2026,90,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(25,5,5,2026,3,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(26,6,1,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(27,6,2,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(28,6,3,2026,0,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(29,6,4,2026,90,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(30,6,5,2026,3,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(31,7,1,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(32,7,2,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(33,7,3,2026,0,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(34,7,4,2026,90,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(35,7,5,2026,3,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(36,8,1,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(37,8,2,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(38,8,3,2026,0,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(39,8,4,2026,90,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(40,8,5,2026,3,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(41,9,1,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(42,9,2,2026,12,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(43,9,3,2026,0,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(44,9,4,2026,90,0,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(45,9,5,2026,3,0,'2026-07-16 13:42:46','2026-07-16 13:42:46');
/*!40000 ALTER TABLE `leave_balances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_requests`
--

DROP TABLE IF EXISTS `leave_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `leave_type_id` bigint(20) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int(11) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_requests_employee_id_foreign` (`employee_id`),
  KEY `leave_requests_leave_type_id_foreign` (`leave_type_id`),
  KEY `leave_requests_reviewed_by_foreign` (`reviewed_by`),
  CONSTRAINT `leave_requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leave_requests_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`),
  CONSTRAINT `leave_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_requests`
--

LOCK TABLES `leave_requests` WRITE;
/*!40000 ALTER TABLE `leave_requests` DISABLE KEYS */;
INSERT INTO `leave_requests` VALUES
(1,2,2,'2026-07-16','2026-07-16',1,'sakit demam','rejected',2,NULL,'2026-07-16 14:03:25','2026-07-16 14:02:29','2026-07-16 14:03:25');
/*!40000 ALTER TABLE `leave_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_types`
--

DROP TABLE IF EXISTS `leave_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `code` varchar(20) NOT NULL,
  `days_per_year` int(11) NOT NULL DEFAULT 0,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_types_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_types`
--

LOCK TABLES `leave_types` WRITE;
/*!40000 ALTER TABLE `leave_types` DISABLE KEYS */;
INSERT INTO `leave_types` VALUES
(1,'Annual Leave','ANNUAL',12,1,'Paid annual leave','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(2,'Sick Leave','SICK',12,1,'Paid sick leave','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(3,'Unpaid Leave','UNPAID',0,0,'Unpaid leave','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(4,'Maternity Leave','MATERNITY',90,1,'Maternity leave','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(5,'Personal Leave','PERSONAL',3,1,'Personal leave for urgent matters','2026-07-16 13:42:46','2026-07-16 13:42:46');
/*!40000 ALTER TABLE `leave_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'2026_01_01_000001_create_users_table',1),
(2,'2026_01_01_000002_create_cache_table',1),
(3,'2026_01_01_000003_create_jobs_table',1),
(4,'2026_01_01_000004_create_sessions_table',1),
(5,'2026_01_01_000005_create_personal_access_tokens_table',1),
(6,'2026_01_01_000006_create_password_reset_tokens_table',1),
(7,'2026_02_01_000001_create_departments_table',1),
(8,'2026_02_01_000002_create_positions_table',1),
(9,'2026_02_01_000003_create_employees_table',1),
(10,'2026_02_01_000004_create_employee_positions_table',1),
(11,'2026_02_01_000005_create_attendances_table',1),
(12,'2026_02_01_000006_create_leave_types_table',1),
(13,'2026_02_01_000007_create_leave_requests_table',1),
(14,'2026_02_01_000008_create_leave_balances_table',1),
(15,'2026_02_01_000009_create_settings_table',1),
(16,'2026_02_01_000010_create_activity_logs_table',1),
(17,'2026_07_17_000001_create_payroll_tables',1),
(18,'2026_07_17_000002_create_reimbursement_tables',1),
(19,'2026_07_17_000003_create_shift_tables',1),
(20,'2026_07_17_000004_create_performance_tables',1),
(21,'2026_07_17_000005_add_suspend_to_users_and_employees',1),
(22,'2026_07_18_000001_create_permissions_table',1),
(23,'2026_07_18_000002_create_roles_table',1),
(24,'2026_07_18_000003_create_role_permission_table',1),
(25,'2026_07_18_000004_create_user_role_table',1),
(26,'2026_07_18_000005_update_users_role_column',1),
(27,'2026_07_18_000006_add_manager_id_to_employees',1),
(28,'2026_07_18_000007_create_recruitment_tables',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `onboarding`
--

DROP TABLE IF EXISTS `onboarding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `onboarding` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `job_application_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
  `checklist` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`checklist`)),
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `onboarding_employee_id_foreign` (`employee_id`),
  KEY `onboarding_job_application_id_foreign` (`job_application_id`),
  KEY `onboarding_assigned_to_foreign` (`assigned_to`),
  CONSTRAINT `onboarding_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `onboarding_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `onboarding_job_application_id_foreign` FOREIGN KEY (`job_application_id`) REFERENCES `job_applications` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `onboarding`
--

LOCK TABLES `onboarding` WRITE;
/*!40000 ALTER TABLE `onboarding` DISABLE KEYS */;
/*!40000 ALTER TABLE `onboarding` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `overtime_requests`
--

DROP TABLE IF EXISTS `overtime_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `overtime_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `hours` decimal(5,2) NOT NULL DEFAULT 0.00,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `overtime_requests_approved_by_foreign` (`approved_by`),
  KEY `overtime_requests_employee_id_date_index` (`employee_id`,`date`),
  CONSTRAINT `overtime_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `overtime_requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `overtime_requests`
--

LOCK TABLES `overtime_requests` WRITE;
/*!40000 ALTER TABLE `overtime_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `overtime_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_components`
--

DROP TABLE IF EXISTS `payroll_components`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_components` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('allowance','deduction') NOT NULL,
  `calculation` enum('fixed','percentage','attendance_based') NOT NULL DEFAULT 'fixed',
  `value` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_components`
--

LOCK TABLES `payroll_components` WRITE;
/*!40000 ALTER TABLE `payroll_components` DISABLE KEYS */;
INSERT INTO `payroll_components` VALUES
(1,'Tunjangan Transport','allowance','fixed',500000.00,1,1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(2,'Tunjangan Makan','allowance','fixed',500000.00,1,2,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(3,'Tunjangan Jabatan','allowance','percentage',5.00,0,3,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(4,'Pajak Penghasilan','deduction','percentage',5.00,1,1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(5,'BPJS Kesehatan','deduction','percentage',1.00,1,2,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(6,'BPJS JHT','deduction','percentage',2.00,1,3,'2026-07-16 13:42:46','2026-07-16 13:42:46');
/*!40000 ALTER TABLE `payroll_components` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_items`
--

DROP TABLE IF EXISTS `payroll_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payroll_period_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `base_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `allowance_transport` decimal(12,2) NOT NULL DEFAULT 0.00,
  `allowance_meal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `allowance_other` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_allowance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `deduction_late` decimal(12,2) NOT NULL DEFAULT 0.00,
  `deduction_absent` decimal(12,2) NOT NULL DEFAULT 0.00,
  `deduction_other` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `overtime_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `overtime_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','finalized','paid') NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_items_payroll_period_id_employee_id_unique` (`payroll_period_id`,`employee_id`),
  KEY `payroll_items_employee_id_foreign` (`employee_id`),
  CONSTRAINT `payroll_items_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_items_payroll_period_id_foreign` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_items`
--

LOCK TABLES `payroll_items` WRITE;
/*!40000 ALTER TABLE `payroll_items` DISABLE KEYS */;
INSERT INTO `payroll_items` VALUES
(1,1,1,12000000.00,500000.00,500000.00,0.00,1000000.00,818181.82,545454.55,960000.00,2323636.37,0.00,0.00,10676363.63,'draft',NULL,'2026-07-16 14:20:49','2026-07-16 14:20:49'),
(2,1,2,5500000.00,500000.00,500000.00,0.00,1000000.00,500000.00,250000.00,440000.00,1190000.00,0.00,0.00,5310000.00,'draft',NULL,'2026-07-16 14:20:49','2026-07-16 14:20:49'),
(3,1,7,0.00,500000.00,500000.00,0.00,1000000.00,0.00,0.00,0.00,0.00,0.00,0.00,1000000.00,'draft',NULL,'2026-07-16 14:20:49','2026-07-16 14:20:49'),
(4,1,5,5000000.00,500000.00,500000.00,0.00,1000000.00,340909.09,454545.45,400000.00,1195454.54,0.00,0.00,4804545.46,'draft',NULL,'2026-07-16 14:20:49','2026-07-16 14:20:49'),
(5,1,9,0.00,500000.00,500000.00,0.00,1000000.00,0.00,0.00,0.00,0.00,0.00,0.00,1000000.00,'draft',NULL,'2026-07-16 14:20:49','2026-07-16 14:20:49'),
(6,1,4,12000000.00,500000.00,500000.00,0.00,1000000.00,545454.55,545454.55,960000.00,2050909.10,0.00,0.00,10949090.90,'draft',NULL,'2026-07-16 14:20:49','2026-07-16 14:20:49'),
(7,1,6,0.00,500000.00,500000.00,0.00,1000000.00,0.00,0.00,0.00,0.00,0.00,0.00,1000000.00,'draft',NULL,'2026-07-16 14:20:49','2026-07-16 14:20:49'),
(8,1,8,0.00,500000.00,500000.00,0.00,1000000.00,0.00,0.00,0.00,0.00,0.00,0.00,1000000.00,'draft',NULL,'2026-07-16 14:20:49','2026-07-16 14:20:49'),
(9,1,3,5000000.00,500000.00,500000.00,0.00,1000000.00,227272.73,227272.73,400000.00,854545.46,0.00,0.00,5145454.54,'draft',NULL,'2026-07-16 14:20:49','2026-07-16 14:20:49');
/*!40000 ALTER TABLE `payroll_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_periods`
--

DROP TABLE IF EXISTS `payroll_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_periods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `status` enum('draft','finalized','paid') NOT NULL DEFAULT 'draft',
  `finalized_at` timestamp NULL DEFAULT NULL,
  `finalized_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_periods_year_month_unique` (`year`,`month`),
  KEY `payroll_periods_finalized_by_foreign` (`finalized_by`),
  CONSTRAINT `payroll_periods_finalized_by_foreign` FOREIGN KEY (`finalized_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_periods`
--

LOCK TABLES `payroll_periods` WRITE;
/*!40000 ALTER TABLE `payroll_periods` DISABLE KEYS */;
INSERT INTO `payroll_periods` VALUES
(1,2026,7,'draft',NULL,NULL,'2026-07-16 14:20:49','2026-07-16 14:20:49');
/*!40000 ALTER TABLE `payroll_periods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payslips`
--

DROP TABLE IF EXISTS `payslips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payslips` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payroll_item_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `payroll_period_id` bigint(20) unsigned NOT NULL,
  `payslip_number` varchar(255) NOT NULL,
  `generated_at` timestamp NULL DEFAULT NULL,
  `viewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payslips_payslip_number_unique` (`payslip_number`),
  KEY `payslips_payroll_item_id_foreign` (`payroll_item_id`),
  KEY `payslips_employee_id_foreign` (`employee_id`),
  KEY `payslips_payroll_period_id_foreign` (`payroll_period_id`),
  CONSTRAINT `payslips_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payslips_payroll_item_id_foreign` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payslips_payroll_period_id_foreign` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payslips`
--

LOCK TABLES `payslips` WRITE;
/*!40000 ALTER TABLE `payslips` DISABLE KEYS */;
/*!40000 ALTER TABLE `payslips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `label` varchar(200) NOT NULL,
  `module` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES
(1,'view_employees','View Employees','employees','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(2,'view_employee_sensitive','View Sensitive Employee Data','employees','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(3,'manage_employees','Manage Employees (CRUD)','employees','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(4,'suspend_employees','Suspend/Unsuspend Employees','employees','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(5,'view_departments','View Departments','departments','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(6,'manage_departments','Manage Departments','departments','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(7,'view_positions','View Positions','positions','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(8,'manage_positions','Manage Positions','positions','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(9,'view_attendance','View All Attendance','attendance','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(10,'manage_attendance','Manage Attendance','attendance','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(11,'view_own_attendance','View Own Attendance','attendance','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(12,'view_leave','View All Leave Requests','leave','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(13,'manage_leave','Manage Leave Types/Balances','leave','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(14,'approve_leave','Approve/Reject Leave','leave','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(15,'view_own_leave','View Own Leave Requests','leave','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(16,'request_leave','Submit Leave Request','leave','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(17,'view_payroll','View Payroll Data','payroll','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(18,'manage_payroll','Manage Payroll (CRUD)','payroll','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(19,'view_own_payslip','View Own Payslip','payroll','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(20,'view_reimbursement','View All Reimbursement Claims','reimbursement','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(21,'manage_reimbursement','Manage Reimbursement Settings','reimbursement','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(22,'approve_reimbursement','Approve/Reject Reimbursement','reimbursement','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(23,'view_own_claims','View Own Reimbursement Claims','reimbursement','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(24,'submit_claim','Submit Reimbursement Claim','reimbursement','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(25,'view_shifts','View Shift Configurations','shifts','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(26,'manage_shifts','Manage Shift Configurations','shifts','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(27,'view_own_schedule','View Own Shift Schedule','shifts','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(28,'manage_overtime','Manage Overtime Requests','shifts','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(29,'approve_overtime','Approve/Reject Overtime','shifts','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(30,'view_own_overtime','View Own Overtime Requests','shifts','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(31,'request_overtime','Submit Overtime Request','shifts','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(32,'view_performance','View All Performance Data','performance','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(33,'manage_performance','Manage KPIs and Appraisals','performance','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(34,'view_own_appraisal','View Own Appraisal','performance','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(35,'view_recruitment','View Recruitment Data','recruitment','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(36,'manage_recruitment','Manage Job Postings','recruitment','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(37,'manage_candidates','Manage Candidates & Applications','recruitment','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(38,'view_settings','View Settings','settings','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(39,'manage_settings','Manage Settings','settings','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(40,'view_users','View Users','users','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(41,'manage_users','Manage Users','users','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(42,'manage_roles','Manage Roles & Permissions','users','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(43,'view_reports','View Reports','reports','2026-07-16 13:42:43','2026-07-16 13:42:43'),
(44,'view_dashboard_admin','View Admin Dashboard','dashboard','2026-07-16 13:42:43','2026-07-16 13:42:43');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `positions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `base_salary` decimal(15,2) NOT NULL,
  `default_annual_leave_days` int(11) NOT NULL DEFAULT 12,
  `default_sick_leave_days` int(11) NOT NULL DEFAULT 12,
  `level` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `positions_code_unique` (`code`),
  KEY `positions_department_id_foreign` (`department_id`),
  CONSTRAINT `positions_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `positions`
--

LOCK TABLES `positions` WRITE;
/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
INSERT INTO `positions` VALUES
(1,1,'HR Director','HR-DIR',15000000.00,15,12,1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(2,1,'HR Manager','HR-MGR',10000000.00,12,12,2,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(3,1,'HR Staff','HR-STF',5000000.00,12,12,3,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(4,2,'IT Director','IT-DIR',18000000.00,15,12,1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(5,2,'IT Manager','IT-MGR',12000000.00,12,12,2,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(6,2,'Senior Developer','IT-SRD',9000000.00,12,12,3,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(7,2,'Junior Developer','IT-JRD',5500000.00,12,12,4,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(8,3,'Finance Director','FN-DIR',16000000.00,15,12,1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(9,3,'Accountant','FN-ACC',6000000.00,12,12,3,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(10,4,'Marketing Manager','MK-MGR',11000000.00,12,12,2,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(11,4,'Marketing Staff','MK-STF',5500000.00,12,12,3,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(12,5,'Operations Manager','OP-MGR',10000000.00,12,12,2,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(13,5,'Operations Staff','OP-STF',5000000.00,12,12,3,'2026-07-16 13:42:46','2026-07-16 13:42:46');
/*!40000 ALTER TABLE `positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reimbursement_approvals`
--

DROP TABLE IF EXISTS `reimbursement_approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reimbursement_approvals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reimbursement_claim_id` bigint(20) unsigned NOT NULL,
  `approver_id` bigint(20) unsigned DEFAULT NULL,
  `level` int(11) NOT NULL,
  `action` enum('approved','rejected') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reimbursement_approvals_reimbursement_claim_id_foreign` (`reimbursement_claim_id`),
  KEY `reimbursement_approvals_approver_id_foreign` (`approver_id`),
  CONSTRAINT `reimbursement_approvals_approver_id_foreign` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reimbursement_approvals_reimbursement_claim_id_foreign` FOREIGN KEY (`reimbursement_claim_id`) REFERENCES `reimbursement_claims` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reimbursement_approvals`
--

LOCK TABLES `reimbursement_approvals` WRITE;
/*!40000 ALTER TABLE `reimbursement_approvals` DISABLE KEYS */;
/*!40000 ALTER TABLE `reimbursement_approvals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reimbursement_claims`
--

DROP TABLE IF EXISTS `reimbursement_claims`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reimbursement_claims` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `expense_category_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `expense_date` date NOT NULL,
  `receipt_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `current_approval_level` int(11) NOT NULL DEFAULT 1,
  `total_approval_levels` int(11) NOT NULL DEFAULT 2,
  `rejected_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reimbursement_claims_employee_id_foreign` (`employee_id`),
  KEY `reimbursement_claims_expense_category_id_foreign` (`expense_category_id`),
  CONSTRAINT `reimbursement_claims_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reimbursement_claims_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reimbursement_claims`
--

LOCK TABLES `reimbursement_claims` WRITE;
/*!40000 ALTER TABLE `reimbursement_claims` DISABLE KEYS */;
/*!40000 ALTER TABLE `reimbursement_claims` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permission`
--

DROP TABLE IF EXISTS `role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permission` (
  `role_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `role_permission_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permission_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permission_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permission`
--

LOCK TABLES `role_permission` WRITE;
/*!40000 ALTER TABLE `role_permission` DISABLE KEYS */;
INSERT INTO `role_permission` VALUES
(1,1),
(1,2),
(1,3),
(1,4),
(1,5),
(1,6),
(1,7),
(1,8),
(1,9),
(1,10),
(1,11),
(1,12),
(1,13),
(1,14),
(1,15),
(1,16),
(1,17),
(1,18),
(1,19),
(1,20),
(1,21),
(1,22),
(1,23),
(1,24),
(1,25),
(1,26),
(1,27),
(1,28),
(1,29),
(1,30),
(1,31),
(1,32),
(1,33),
(1,34),
(1,35),
(1,36),
(1,37),
(1,38),
(1,39),
(1,40),
(1,41),
(1,42),
(1,43),
(1,44),
(2,1),
(2,2),
(2,3),
(2,4),
(2,5),
(2,6),
(2,7),
(2,8),
(2,9),
(2,10),
(2,11),
(2,12),
(2,13),
(2,14),
(2,15),
(2,16),
(2,17),
(2,19),
(2,20),
(2,21),
(2,22),
(2,23),
(2,24),
(2,25),
(2,26),
(2,27),
(2,28),
(2,29),
(2,30),
(2,31),
(2,32),
(2,33),
(2,34),
(2,35),
(2,36),
(2,37),
(2,38),
(2,39),
(2,43),
(2,44),
(3,1),
(3,10),
(3,11),
(3,12),
(3,14),
(3,15),
(3,16),
(3,19),
(3,20),
(3,23),
(3,24),
(3,27),
(3,30),
(3,31),
(3,34),
(4,1),
(4,2),
(4,9),
(4,11),
(4,12),
(4,14),
(4,15),
(4,16),
(4,19),
(4,23),
(4,24),
(4,27),
(4,29),
(4,30),
(4,31),
(4,32),
(4,34),
(4,43),
(4,44),
(5,1),
(5,17),
(5,18),
(5,19),
(5,38),
(5,43),
(6,1),
(6,9),
(6,12),
(6,17),
(6,32),
(6,35),
(6,43),
(6,44),
(7,1),
(7,5),
(7,7),
(7,11),
(7,15),
(7,16),
(7,19),
(7,23),
(7,24),
(7,27),
(7,30),
(7,31),
(7,34),
(7,35),
(7,36),
(7,37),
(8,1),
(8,5),
(8,7),
(8,11),
(8,15),
(8,16),
(8,19),
(8,23),
(8,24),
(8,27),
(8,30),
(8,31),
(8,34),
(8,38),
(8,39),
(8,40),
(8,41),
(8,42),
(9,11),
(9,15),
(9,16),
(9,19),
(9,23),
(9,24),
(9,27),
(9,30),
(9,31),
(9,34);
/*!40000 ALTER TABLE `role_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'super_admin','Super Admin',1,'2026-07-16 13:42:43','2026-07-16 13:42:43'),
(2,'hr_manager','HR Manager',1,'2026-07-16 13:42:43','2026-07-16 13:42:43'),
(3,'hr_staff','HR Staff',1,'2026-07-16 13:42:43','2026-07-16 13:42:43'),
(4,'manager','Manager / Supervisor',1,'2026-07-16 13:42:43','2026-07-16 13:42:43'),
(5,'payroll_specialist','Payroll Specialist',1,'2026-07-16 13:42:43','2026-07-16 13:42:43'),
(6,'executive','Executive',1,'2026-07-16 13:42:43','2026-07-16 13:42:43'),
(7,'recruiter','Recruiter',1,'2026-07-16 13:42:43','2026-07-16 13:42:43'),
(8,'it_admin','IT Admin',1,'2026-07-16 13:42:43','2026-07-16 13:42:43'),
(9,'employee','Employee',1,'2026-07-16 13:42:43','2026-07-16 13:42:43');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('bFu6uqCactrq7k6nYNQhc65Y1blZJFE9YNSCc38L',NULL,'23.27.145.119','Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYVhDbThJTkdkV21EcDZXU2pDVW9OUkJIbHNlOEp6WTNSTVdmN1hzVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDc6Imh0dHBzOi8vdHVnYXNjcnVkYWkucHdlYi51bmlrb20uaGFuaWZ1LmlkL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1784210882),
('H1QORWeEoaJxfjoK8K92PSGyxUL3IsLABp5mJoV3',NULL,'3.83.240.87','Mozilla/5.0 (Linux; Android 16; SM-S921U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.7632.6 Mobile Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiS0JLamFuMUV6dWlreHRxZWt6WXNDSjdidVJTMVFGRmVSOGNBVUNubyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDc6Imh0dHBzOi8vdHVnYXNjcnVkYWkucHdlYi51bmlrb20uaGFuaWZ1LmlkL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1784210266),
('ia3BJ5Yc9DwIkChHERAPRzHpe8FSyFgIEAmxbj0f',NULL,'3.90.47.203','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.7632.6 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRU1uVUpGbjk2WmZ2dnFDV1pLMmNXdFA3bHZlODl2VjdIcDFiWmlHTyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDc6Imh0dHBzOi8vdHVnYXNjcnVkYWkucHdlYi51bmlrb20uaGFuaWZ1LmlkL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1784210269),
('RaKedYj9K1w3QDlQxmhO1oWhumwgSB5fJvm467yt',NULL,'113.11.180.3','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTmV0Mmx6aXFUSVpzaFBsaHdRTDhITDR6a0FRdkJsRUlXR3VscmtidiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDc6Imh0dHBzOi8vdHVnYXNjcnVkYWkucHdlYi51bmlrb20uaGFuaWZ1LmlkL2xvZ2luIjt9fQ==',1784211750);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `type` enum('string','integer','boolean','json') NOT NULL DEFAULT 'string',
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES
(1,'work_start_time','08:00','string','Office start time','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(2,'work_end_time','17:00','string','Office end time','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(3,'grace_period_minutes','15','integer','Minutes after start time before marked late','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(4,'company_name','HRIS System','string','Company name','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(5,'default_annual_leave_days','12','integer','Default annual leave days per year','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(6,'default_sick_leave_days','12','integer','Default sick leave days per year','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(7,'payroll_working_days','22','integer','Working days per month used to compute daily rate','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(8,'payroll_late_deduction_rate','0.5','string','Late deduction rate (fraction of daily rate per late day)','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(9,'payroll_absent_deduction_rate','1.0','string','Absent deduction rate (fraction of daily rate per absent day)','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(10,'payroll_ot_hourly_multiplier','1.5','string','Overtime hourly rate multiplier of regular hourly rate','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(11,'kpi_grade_a_min','90','integer','Minimum score for grade A','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(12,'kpi_grade_b_min','80','integer','Minimum score for grade B','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(13,'kpi_grade_c_min','70','integer','Minimum score for grade C','2026-07-16 13:42:46','2026-07-16 13:42:46'),
(14,'kpi_grade_d_min','60','integer','Minimum score for grade D','2026-07-16 13:42:46','2026-07-16 13:42:46');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shifts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `late_threshold` time DEFAULT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(255) NOT NULL DEFAULT '#6366f1',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shifts`
--

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
INSERT INTO `shifts` VALUES
(1,'Morning','07:00:00','15:00:00','07:15:00',NULL,'#6366f1',1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(2,'Afternoon','15:00:00','23:00:00','15:15:00',NULL,'#f59e0b',1,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(3,'Night','23:00:00','07:00:00','23:15:00',NULL,'#0ea5e9',1,'2026-07-16 13:42:46','2026-07-16 13:42:46');
/*!40000 ALTER TABLE `shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_role` (
  `user_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_role_role_id_foreign` (`role_id`),
  CONSTRAINT `user_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_role_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` VALUES
(1,1),
(2,2),
(3,9),
(4,9),
(5,9),
(6,4),
(7,5),
(8,6),
(9,7),
(10,8);
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'employee',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Super Admin','admin@hris.test',NULL,'super_admin',1,'$2y$12$REG4AQhwkKaqgsLDB/3Oxe6FUg7peHqHpHZqfBeZMiYHD5KeGuCea',NULL,'2026-07-16 13:42:44','2026-07-16 13:42:44'),
(2,'HR Manager','hr@hris.test',NULL,'hr_manager',1,'$2y$12$d0X35kMzyBCGjweVShGZx.6P/tLJZwOa8V3b1/Kx9/USGFcmczdAi',NULL,'2026-07-16 13:42:44','2026-07-16 13:42:44'),
(3,'Employee User','employee@hris.test',NULL,'employee',1,'$2y$12$V5lCqqLTFC76RUoQnICUGOcfpqfSUupEX/1qKMny93daKDo5YvVva',NULL,'2026-07-16 13:42:44','2026-07-16 13:42:44'),
(4,'Rina Susanti','rina@hris.test',NULL,'employee',1,'$2y$12$FJMcIXFEFY0LXf7u/LCMteyOygMFkT8/3k5/9T1ESKsOpQkvB1iiy',NULL,'2026-07-16 13:42:44','2026-07-16 13:42:44'),
(5,'Budi Santoso','budi@hris.test',NULL,'employee',1,'$2y$12$ngTBRQSfTvy7HIda.C6dBebqNRHfVV9OLSgx3NO8YbusqDoUDBEGO',NULL,'2026-07-16 13:42:45','2026-07-16 13:42:45'),
(6,'Manager User','manager@hris.test',NULL,'manager',1,'$2y$12$dbkcKOALle5WYsB6YhouluGHLy4eIzxfmrfflhHTkt9NXCeTqYBIu',NULL,'2026-07-16 13:42:45','2026-07-16 13:42:45'),
(7,'Payroll Specialist','payroll@hris.test',NULL,'payroll_specialist',1,'$2y$12$I2Ez/Mvgc//5TRLGI9pRr.c06WSkeJW6SluBxXejyzQ.PBY9o.Xkq',NULL,'2026-07-16 13:42:45','2026-07-16 13:42:45'),
(8,'Executive User','executive@hris.test',NULL,'executive',1,'$2y$12$xSp201p6kZtetWZxzU5pGui/i8zUxUrNR3aiB7bfaAFFYwh/jVgPa',NULL,'2026-07-16 13:42:45','2026-07-16 13:42:45'),
(9,'Recruiter User','recruiter@hris.test',NULL,'recruiter',1,'$2y$12$.1sHavsZruI4lFhWQLFZYOk8n/VA3s29pI0nEg2eOYg01Fq.YNeJu',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46'),
(10,'IT Admin','itadmin@hris.test',NULL,'it_admin',1,'$2y$12$/pNNo0iaIUZbOQP09RK4U.M8xPlxuYZ.dgzKW.oeCci4vys9twqoq',NULL,'2026-07-16 13:42:46','2026-07-16 13:42:46');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-16 21:25:53
