-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: localhost    Database: ppk_room_reservation
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
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
-- Table structure for table `facilities`
--

DROP TABLE IF EXISTS `facilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facilities` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('kelas','aula','laboratorium','alat','lapangan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` int unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `operational_status` enum('dapat_digunakan','dalam_perbaikan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dapat_digunakan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_facilities_active_type` (`is_active`,`type`),
  KEY `idx_facilities_location` (`location`),
  KEY `idx_facilities_operational_status` (`operational_status`),
  CONSTRAINT `chk_facilities_capacity_positive` CHECK (((`capacity` is null) or (`capacity` > 0))),
  CONSTRAINT `chk_facilities_description_not_blank` CHECK ((char_length(trim(`description`)) > 0)),
  CONSTRAINT `chk_facilities_location_not_blank` CHECK ((char_length(trim(`location`)) > 0)),
  CONSTRAINT `chk_facilities_name_not_blank` CHECK ((char_length(trim(`name`)) > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facilities`
--

LOCK TABLES `facilities` WRITE;
/*!40000 ALTER TABLE `facilities` DISABLE KEYS */;
INSERT INTO `facilities` VALUES ('01a128f4-719a-700d-bb10-58e7acf98771','Ruang Kelas Teori A.101','kelas','Gedung Kuliah Bersama Lantai 1',45,'Ruang kelas ber-AC dilengkapi proyektor plafon, papan tulis ganda, dan podium dosen. Cocok untuk perkuliahan reguler dan seminar kecil.',1,'dapat_digunakan','2026-10-11 03:14:30','2026-10-11 03:14:30'),('01a128f4-71d3-722d-976e-72a4352cc282','Ruang Kelas Teori A.102','kelas','Gedung Kuliah Bersama Lantai 1',30,'Ruang kelas interaktif dengan meja melingkar (round table) untuk diskusi kelompok dan presentasi mahasiswa.',1,'dapat_digunakan','2026-10-11 03:14:30','2026-10-11 03:14:30'),('01a128f4-7210-73b3-939b-2daadca9a99b','Laboratorium Komputer & Jaringan','laboratorium','Gedung Sains & Komputer Lantai 3',36,'Laboratorium dengan 36 workstation PC berspesifikasi Core i7, gigabit ethernet switch, rak server mini, dan koneksi internet dedicated.',1,'dapat_digunakan','2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-723b-72f4-a836-76b26dfb33ed','Laboratorium Rekayasa Perangkat Lunak','laboratorium','Gedung Sains & Komputer Lantai 2',40,'Laboratorium pengembangan sistem dan kecerdasan buatan, dilengkapi dual-monitor display pada setiap workstation mahasiswa.',1,'dapat_digunakan','2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-726d-7241-9feb-b670eb2a6768','Aula Graha Utama Nusantara','aula','Gedung Rektorat Lantai 2',350,'Aula serbaguna berkapasitas besar dengan tata suara akustik, panggung utama, ruang rias, dan pencahayaan panggung standar pementasan/wisuda.',1,'dapat_digunakan','2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-72ab-724d-b3ce-02083b272eac','Lapangan Futsal Terbuka','lapangan','Kompleks Olahraga Kampus',20,'Lapangan futsal luar ruangan beralas rumput sintetis berkualitas tinggi dilengkapi lampu sorot malam hari.',1,'dapat_digunakan','2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-72d6-7216-9165-3360cc1b8e1d','Lapangan Basket Barat','lapangan','Kompleks Olahraga Kampus',30,'Lapangan basket outdoor standar PERBASI dengan ring hidrolik dan tribun penonton sisi barat.',1,'dapat_digunakan','2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-7301-7065-9cb1-f970f1f79a99','Proyektor Laser Epson EB-L520U','alat','Ruang Perlengkapan Gedung A',NULL,'Proyektor resolusi WUXGA 5.200 lumens yang menetap di trolley perlengkapan Gedung A. Tidak memiliki kapasitas orang.',1,'dapat_digunakan','2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-7334-70e2-905c-5bfd90b34ee2','Audio Mixer Yamaha TF5 & Sound Rig','alat','Gedung Rektorat (Ruang Kontrol Audio)',NULL,'Konsol mixer audio digital 32 channel beserta sound processor yang menetap di ruang kendali Aula Nusantara.',1,'dapat_digunakan','2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-735e-73c3-a5f5-c125d3f1db55','Studio Rekaman & Podcast Mahasiswa','laboratorium','Gedung Seni & Komunikasi Lantai 1',10,'Studio kedap suara untuk produksi audio dan siniar. Saat ini sedang dalam tahap perbaikan panel akustik.',1,'dalam_perbaikan','2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-739c-71f3-9611-cd065a4c889b','Gedung Kesenian Lama (Arsip)','aula','Area Belakang Kampus',100,'Fasilitas gedung non-aktif yang dialihfungsikan untuk gudang logistik dan dinonaktifkan oleh administrator.',0,'dapat_digunakan','2026-10-11 03:14:31','2026-10-11 03:14:31');
/*!40000 ALTER TABLE `facilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facility_photos`
--

DROP TABLE IF EXISTS `facility_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facility_photos` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `facility_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `primary_facility_id` char(36) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (if((`is_primary` = 1),`facility_id`,NULL)) VIRTUAL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_facility_photos_one_primary` (`primary_facility_id`),
  KEY `idx_facility_photos_primary` (`facility_id`,`is_primary`),
  KEY `idx_facility_photos_sort_order` (`facility_id`,`sort_order`),
  CONSTRAINT `facility_photos_facility_id_foreign` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facility_photos`
--

LOCK TABLES `facility_photos` WRITE;
/*!40000 ALTER TABLE `facility_photos` DISABLE KEYS */;
INSERT INTO `facility_photos` (`id`, `facility_id`, `photo_path`, `is_primary`, `sort_order`, `created_at`, `updated_at`) VALUES ('01a128f4-71b8-71fe-9763-4d19c0f3b92c','01a128f4-719a-700d-bb10-58e7acf98771','https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800&auto=format&fit=crop&q=80',1,0,'2026-10-11 03:14:30','2026-10-11 03:14:30'),('01a128f4-71f4-70f0-8138-74f9a312e57f','01a128f4-71d3-722d-976e-72a4352cc282','https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&auto=format&fit=crop&q=80',1,0,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-7226-73a7-bb3f-6071628cd984','01a128f4-7210-73b3-939b-2daadca9a99b','https://images.unsplash.com/photo-1562774053-701939374585?w=800&auto=format&fit=crop&q=80',1,0,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-7252-729a-a5b3-0dda0dcdcb20','01a128f4-723b-72f4-a836-76b26dfb33ed','https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&auto=format&fit=crop&q=80',1,0,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-728e-70b5-9996-86d86c8748b7','01a128f4-726d-7241-9feb-b670eb2a6768','https://images.unsplash.com/photo-1517457373958-b7bdd4587205?w=800&auto=format&fit=crop&q=80',1,0,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-72c0-702d-9698-5fd9d8209bc4','01a128f4-72ab-724d-b3ce-02083b272eac','https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&auto=format&fit=crop&q=80',1,0,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-72eb-71b9-adea-d3ec0aa0f7bc','01a128f4-72d6-7216-9165-3360cc1b8e1d','https://images.unsplash.com/photo-1546519638-68e109498ffc?w=800&auto=format&fit=crop&q=80',1,0,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-7317-7052-b67b-d25c2af6d78f','01a128f4-7301-7065-9cb1-f970f1f79a99','https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=800&auto=format&fit=crop&q=80',1,0,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-734a-729a-bb91-279bfc58aea1','01a128f4-7334-70e2-905c-5bfd90b34ee2','https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?w=800&auto=format&fit=crop&q=80',1,0,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-737e-7299-a563-97d952be158c','01a128f4-735e-73c3-a5f5-c125d3f1db55','https://images.unsplash.com/photo-1590602847861-f357a9332bbc?w=800&auto=format&fit=crop&q=80',1,0,'2026-10-11 03:14:31','2026-10-11 03:14:31');
/*!40000 ALTER TABLE `facility_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facility_status_logs`
--

DROP TABLE IF EXISTS `facility_status_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facility_status_logs` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `facility_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_by_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `change_type` enum('operational_status','is_active') COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_value` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_value` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `report_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `changed_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `facility_status_logs_changed_by_id_foreign` (`changed_by_id`),
  KEY `facility_status_logs_report_id_foreign` (`report_id`),
  KEY `idx_facility_status_logs_recap` (`facility_id`,`change_type`,`changed_at`),
  CONSTRAINT `facility_status_logs_changed_by_id_foreign` FOREIGN KEY (`changed_by_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `facility_status_logs_facility_id_foreign` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `facility_status_logs_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE SET NULL,
  CONSTRAINT `chk_facility_status_logs_operational_reason` CHECK (((`change_type` <> _utf8mb4'operational_status') or ((`reason` is not null) and (char_length(trim(`reason`)) > 0)))),
  CONSTRAINT `chk_facility_status_logs_value_changed` CHECK ((`old_value` <> `new_value`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facility_status_logs`
--

LOCK TABLES `facility_status_logs` WRITE;
/*!40000 ALTER TABLE `facility_status_logs` DISABLE KEYS */;
INSERT INTO `facility_status_logs` VALUES ('01a128f4-7c9a-7156-b1ee-be85805ce956','01a128f4-735e-73c3-a5f5-c125d3f1db55','01a128f4-714c-70b6-b81f-e3784f869a66','operational_status','dapat_digunakan','dalam_perbaikan','Perbaikan dan pemasangan ulang panel akustik kedap suara dinding studio yang terlepas.','01a128f4-7c02-71d7-a6ab-d5a7d5caf61b','2026-10-10 07:00:00','2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-7caf-7255-a468-d9e1add61d9a','01a128f4-7301-7065-9cb1-f970f1f79a99','01a128f4-714c-70b6-b81f-e3784f869a66','operational_status','dapat_digunakan','dalam_perbaikan','Pergantian modul pendingin laser proyektor dan pembersihan lensa optik.',NULL,'2026-09-15 03:00:00','2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-7cce-726b-835d-43ca4ee48e3c','01a128f4-7301-7065-9cb1-f970f1f79a99','01a128f4-714c-70b6-b81f-e3784f869a66','operational_status','dalam_perbaikan','dapat_digunakan','Pengujian modul pendingin laser selesai dan proyektor siap digunakan kembali.',NULL,'2026-09-18 08:30:00','2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-7cec-71e2-86ab-5786f341343e','01a128f4-739c-71f3-9611-cd065a4c889b','01a128f4-7162-70b6-9861-3bf1485e7e09','is_active','1','0','Fasilitas dialihfungsikan permanen menjadi gudang logistik dan arsip kampus.',NULL,'2026-08-15 03:00:00','2026-10-11 03:14:33','2026-10-11 03:14:33');
/*!40000 ALTER TABLE `facility_status_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
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
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
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
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
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
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_09_23_000003_create_facilities_table',1),(5,'2026_09_23_000004_create_facility_photos_table',1),(6,'2026_09_23_000005_create_reservations_table',1),(7,'2026_09_23_000006_create_reports_table',1),(8,'2026_09_23_000007_create_facility_status_logs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `report_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reporter_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `facility_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('kerusakan_fisik','kerusakan_elektronik','kebersihan','keamanan','lainnya') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('baru','diproses','selesai','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baru',
  `resolution_notes` text COLLATE utf8mb4_unicode_ci,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `processed_by_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `resolved_by_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `rejected_by_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reports_report_number_unique` (`report_number`),
  KEY `reports_processed_by_id_foreign` (`processed_by_id`),
  KEY `reports_resolved_by_id_foreign` (`resolved_by_id`),
  KEY `reports_rejected_by_id_foreign` (`rejected_by_id`),
  KEY `idx_reports_reporter_history` (`reporter_id`,`status`),
  KEY `idx_reports_queue_order` (`status`,`created_at`),
  KEY `idx_reports_facility` (`facility_id`),
  CONSTRAINT `reports_facility_id_foreign` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `reports_processed_by_id_foreign` FOREIGN KEY (`processed_by_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `reports_rejected_by_id_foreign` FOREIGN KEY (`rejected_by_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `reports_reporter_id_foreign` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `reports_resolved_by_id_foreign` FOREIGN KEY (`resolved_by_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `chk_reports_description_not_blank` CHECK ((char_length(trim(`description`)) > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES ('01a128f4-7b94-7146-9d7b-1bf1d9071f13','LAP-20261011-0001','01a128f4-6d97-711c-8880-d0cb41e84853','01a128f4-719a-700d-bb10-58e7acf98771','kerusakan_elektronik','Proyektor plafon di Ruang A.101 berkedip terus-menerus dan lampu indikator error berkedip merah saat disambungkan ke laptop melalui HDMI. Tampilan layar presentasi tidak muncul.','https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=800&auto=format&fit=crop&q=80','baru',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 01:30:00','2026-10-11 03:14:33'),('01a128f4-7bb2-707f-9ab4-60bf9eaf989a','LAP-20261011-0002','01a128f4-701b-73f2-8ea6-b56cd94575d4','01a128f4-71d3-722d-976e-72a4352cc282','kerusakan_fisik','Kait engsel jendela sisi barat patah sehingga jendela tidak dapat dikunci rapat saat hujan lebat. Selain itu, penutup stopkontak meja dosen terlepas dan kawat terbuka, berpotensi bahaya listrik.',NULL,'baru',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:15:00','2026-10-11 03:14:33'),('01a128f4-7bce-708f-9228-636995fd3a20','LAP-20261012-0003','01a128f4-705d-70e1-a4da-b71dcdacad6f','01a128f4-726d-7241-9feb-b670eb2a6768','kebersihan','Sisa konsumsi dan botol plastik dari kegiatan kemahasiswaan kemarin belum dibersihkan di deretan tribun penonton barat. Karpet panggung utama juga berdebu tebal dan perlu disedot vakum.',NULL,'baru',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-12 02:00:00','2026-10-11 03:14:33'),('01a128f4-7bec-7241-8b4a-324a95541ea1','LAP-20261011-0004','01a128f4-6f19-7155-a7cd-cdb8a9964b96','01a128f4-7210-73b3-939b-2daadca9a99b','kerusakan_elektronik','Port switch nomor 12 sampai 16 pada rak server mini mati total, menyebabkan 5 unit PC praktikum tidak mendapat koneksi LAN. Perlu penggantian switch atau crimping ulang kabel trunk.','https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=800&auto=format&fit=crop&q=80','diproses',NULL,NULL,'01a128f4-7100-7299-822a-fef2b5defad4','2026-10-11 04:00:00',NULL,NULL,NULL,NULL,'2026-10-11 03:30:00','2026-10-11 03:14:33'),('01a128f4-7c02-71d7-a6ab-d5a7d5caf61b','LAP-20261010-0005','01a128f4-6efb-70b0-a93a-81b9921ef834','01a128f4-735e-73c3-a5f5-c125d3f1db55','kerusakan_fisik','Panel busa peredam akustik pada dinding sisi kanan studio terlepas dan berlubang. Suara dari luar gedung bocor masuk saat rekaman podcast mahasiswa. Fasilitas perlu ditutup sementara untuk renovasi panel.','https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?w=800&auto=format&fit=crop&q=80','diproses',NULL,NULL,'01a128f4-714c-70b6-b81f-e3784f869a66','2026-10-10 07:00:00',NULL,NULL,NULL,NULL,'2026-10-10 06:15:00','2026-10-11 03:14:33'),('01a128f4-7c16-72eb-a21e-ef199d9b91d5','LAP-20261008-0006','01a128f4-7005-7219-97d4-5d4c12c2692d','01a128f4-723b-72f4-a836-76b26dfb33ed','kerusakan_elektronik','Unit pendingin udara (AC) split sisi utara ruangan lab tidak dingin dan meneteskan air ke lantai dekat workstation nomor 08, berisiko terhadap kelistrikan PC.',NULL,'selesai','Unit AC telah diservis oleh teknisi rekanan sarpras. Dilakukan pembersihan filter udara, perbaikan jalur pembuangan kondensasi air, dan pengisian freon R410A. Suhu ruangan kini stabil di 20°C.',NULL,'01a128f4-7100-7299-822a-fef2b5defad4','2026-10-08 07:00:00','01a128f4-70ea-73f1-9145-a9227ebec969','2026-10-09 09:30:00',NULL,NULL,'2026-10-08 06:30:00','2026-10-11 03:14:33'),('01a128f4-7c47-7300-9f74-f5623eb346f6','LAP-20261009-0007','01a128f4-6f4d-7203-93aa-661162418f1a','01a128f4-7301-7065-9cb1-f970f1f79a99','kerusakan_elektronik','Proyektor tidak mau menampilkan gambar saat disambungkan ke laptop saya menggunakan port USB-C ke HDMI.',NULL,'ditolak',NULL,'Setelah dilakukan pemeriksaan teknis, port HDMI dan unit proyektor laser berfungsi normal. Masalah disebabkan oleh dongle/adapter konverter milik pribadi pelapor yang tidak kompatibel.','01a128f4-70ea-73f1-9145-a9227ebec969','2026-10-09 03:00:00',NULL,NULL,'01a128f4-70ea-73f1-9145-a9227ebec969','2026-10-09 04:30:00','2026-10-09 02:15:00','2026-10-11 03:14:33'),('01a128f4-7c7f-718c-a8e3-d40bbdf562a7','LAP-20261010-0008','01a128f4-6fa2-72d1-b053-cf5ed58f816a','01a128f4-72d6-7216-9165-3360cc1b8e1d','keamanan','Dua buah bola basket latihan hilang dari keranjang penyimpanan di pinggir lapangan setelah sesi sparing kemarin sore.',NULL,'ditolak',NULL,'Sistem reservasi dan laporan hanya mengelola fasilitas fisik tetap dan inventaris terpasang kampus. Perlengkapan habis pakai seperti bola latihan merupakan tanggung jawab inventaris internal UKM Basket.','01a128f4-7136-717f-a9d1-0d5960145d56','2026-10-10 02:30:00',NULL,NULL,'01a128f4-7136-717f-a9d1-0d5960145d56','2026-10-10 04:00:00','2026-10-10 01:45:00','2026-10-11 03:14:33');
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reservation_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `facility_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `purpose` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('menunggu','disetujui','berlangsung','selesai','ditolak','dibatalkan_oleh_pengguna','dibatalkan_oleh_petugas','kedaluwarsa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  `cancelled_by_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiration_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_by_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservations_reservation_number_unique` (`reservation_number`),
  KEY `reservations_cancelled_by_id_foreign` (`cancelled_by_id`),
  KEY `reservations_approved_by_id_foreign` (`approved_by_id`),
  KEY `reservations_rejected_by_id_foreign` (`rejected_by_id`),
  KEY `idx_reservations_conflict_check` (`facility_id`,`date`,`status`,`start_time`,`end_time`),
  KEY `idx_reservations_duplicate_check` (`user_id`,`facility_id`,`date`,`start_time`,`end_time`,`status`),
  KEY `idx_reservations_user_history` (`user_id`,`status`,`created_at`),
  KEY `idx_reservations_queue_order` (`status`,`created_at`),
  KEY `idx_reservations_lazy_start` (`status`,`date`,`start_time`),
  KEY `idx_reservations_lazy_end` (`status`,`date`,`end_time`),
  CONSTRAINT `reservations_approved_by_id_foreign` FOREIGN KEY (`approved_by_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `reservations_cancelled_by_id_foreign` FOREIGN KEY (`cancelled_by_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `reservations_facility_id_foreign` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `reservations_rejected_by_id_foreign` FOREIGN KEY (`rejected_by_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `chk_reservations_end_after_start` CHECK ((`end_time` > `start_time`)),
  CONSTRAINT `chk_reservations_half_hour_slots` CHECK (((minute(`start_time`) in (0,30)) and (second(`start_time`) = 0) and (minute(`end_time`) in (0,30)) and (second(`end_time`) = 0))),
  CONSTRAINT `chk_reservations_operational_hours` CHECK (((`start_time` >= '07:00:00') and (`start_time` < '20:00:00') and (`end_time` > '07:00:00') and (`end_time` <= '20:00:00'))),
  CONSTRAINT `chk_reservations_purpose_not_blank` CHECK ((char_length(trim(`purpose`)) > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES ('01a128f4-73b5-7152-b3c8-3158de420d53','RSV-20260902-0001','01a128f4-6fe5-72c1-a45e-8363a281b875','01a128f4-7210-73b3-939b-2daadca9a99b','2026-09-02','08:00:00','11:00:00','Praktikum Komputasi Jaringan Lanjut & Simulasi Routing Cisco','selesai',NULL,NULL,NULL,NULL,'01a128f4-7100-7299-822a-fef2b5defad4','2026-09-01 02:30:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-73da-71ca-96b6-0fefe67e679c','RSV-20260904-0002','01a128f4-6d97-711c-8880-d0cb41e84853','01a128f4-719a-700d-bb10-58e7acf98771','2026-09-04','13:00:00','15:30:00','Rapat Kerja Himpunan Mahasiswa Informatika membahas Pengenalan Prodi','selesai',NULL,NULL,NULL,NULL,'01a128f4-7116-7251-ac96-ed52a670663c','2026-09-03 07:00:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-73f9-737d-afe1-71505396976c','RSV-20260907-0003','01a128f4-7005-7219-97d4-5d4c12c2692d','01a128f4-723b-72f4-a836-76b26dfb33ed','2026-09-07','09:00:00','12:00:00','Sesi Kolaboratif Bootcamp Machine Learning Mahasiswa Tingkat Akhir','selesai',NULL,NULL,NULL,NULL,'01a128f4-7100-7299-822a-fef2b5defad4','2026-09-06 03:15:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-740f-7202-a6eb-b4c9210f77cd','RSV-20260909-0004','01a128f4-7090-709c-84a2-ba86918b4c24','01a128f4-726d-7241-9feb-b670eb2a6768','2026-09-09','08:00:00','12:00:00','Sosialisasi Sistem Informasi Akademik Terpadu Semester Ganjil 2026','selesai',NULL,NULL,NULL,NULL,'01a128f4-70ea-73f1-9145-a9227ebec969','2026-09-07 04:00:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-7423-7248-b28e-b9bd074ff889','RSV-20260911-0005','01a128f4-6ee5-72a6-b99c-782eaead499d','01a128f4-72ab-724d-b3ce-02083b272eac','2026-09-11','16:00:00','18:00:00','Seleksi dan Latihan Rutin Tim Futsal Mahasiswa Piala Rektor','selesai',NULL,NULL,NULL,NULL,'01a128f4-7136-717f-a9d1-0d5960145d56','2026-09-10 08:00:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-7443-73ce-b8d5-4ca1b8e106ff','RSV-20260914-0006','01a128f4-701b-73f2-8ea6-b56cd94575d4','01a128f4-71d3-722d-976e-72a4352cc282','2026-09-14','10:00:00','12:30:00','Kuliah Pengganti Matematika Diskrit Paralel Kelas B','selesai',NULL,NULL,NULL,NULL,'01a128f4-7116-7251-ac96-ed52a670663c','2026-09-13 02:00:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-746e-7280-9010-2d89a15bcf4c','RSV-20260916-0007','01a128f4-6ecf-7098-961b-f5a0604c11cb','01a128f4-719a-700d-bb10-58e7acf98771','2026-09-16','14:00:00','16:30:00','Konsolidasi Divisi Aspirasi BEM Fakultas bersama Delegasi Angkatan','selesai',NULL,NULL,NULL,NULL,'01a128f4-7116-7251-ac96-ed52a670663c','2026-09-15 06:00:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-749a-7344-bdf0-5e0251bb0ecb','RSV-20260918-0008','01a128f4-6fa2-72d1-b053-cf5ed58f816a','01a128f4-72d6-7216-9165-3360cc1b8e1d','2026-09-18','15:30:00','17:30:00','Sparing Basket Antar Angkatan Persiapan Pekan Olahraga Mahasiswa','selesai',NULL,NULL,NULL,NULL,'01a128f4-7136-717f-a9d1-0d5960145d56','2026-09-17 07:00:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-74c2-7065-b35b-355aeee10d72','RSV-20260921-0009','01a128f4-7031-7292-bd26-c398c65cce6a','01a128f4-723b-72f4-a836-76b26dfb33ed','2026-09-21','08:00:00','11:00:00','Evaluasi Proyek Tengah Semester Rekayasa Kebutuhan Perangkat Lunak','selesai',NULL,NULL,NULL,NULL,'01a128f4-7100-7299-822a-fef2b5defad4','2026-09-20 09:00:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-74d8-7342-851d-03660f3ba3d4','RSV-20260923-0010','01a128f4-705d-70e1-a4da-b71dcdacad6f','01a128f4-7301-7065-9cb1-f970f1f79a99','2026-09-23','09:00:00','12:00:00','Presentasi Rencana Anggaran Kegiatan Kemahasiswaan Triwulan IV','selesai',NULL,NULL,NULL,NULL,'01a128f4-70ea-73f1-9145-a9227ebec969','2026-09-22 01:30:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-74f4-71af-b920-08b07a9335ab','RSV-20260924-0011','01a128f4-6efb-70b0-a93a-81b9921ef834','01a128f4-7334-70e2-905c-5bfd90b34ee2','2026-09-24','13:00:00','16:00:00','Latihan Tata Suara dan Sound Check Musik Akustik Kampus','selesai',NULL,NULL,NULL,NULL,'01a128f4-714c-70b6-b81f-e3784f869a66','2026-09-23 04:00:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-7515-7053-bd84-63cccd721486','RSV-20260925-0012','01a128f4-7046-7015-8d1a-07414631b1a0','01a128f4-719a-700d-bb10-58e7acf98771','2026-09-25','08:30:00','11:30:00','Seminar Proposal Skripsi Gelombang I Program Studi Informatika','selesai',NULL,NULL,NULL,NULL,'01a128f4-7116-7251-ac96-ed52a670663c','2026-09-24 07:00:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-752b-7301-ad98-736e2af9c9f0','RSV-20260928-0013','01a128f4-6f19-7155-a7cd-cdb8a9964b96','01a128f4-7210-73b3-939b-2daadca9a99b','2026-09-28','13:30:00','16:30:00','Pelatihan Dasar Pengenalan Linux Server untuk Mahasiswa Baru','selesai',NULL,NULL,NULL,NULL,'01a128f4-7100-7299-822a-fef2b5defad4','2026-09-27 03:00:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-7541-73b7-af84-711f483b7e72','RSV-20260929-0014','01a128f4-70cc-712b-9d07-314bcd9677ac','01a128f4-726d-7241-9feb-b670eb2a6768','2026-09-29','09:00:00','13:00:00','Rapat Pleno Pimpinan dan Sosialisasi Kebijakan Sarpras 2026','selesai',NULL,NULL,NULL,NULL,'01a128f4-70ea-73f1-9145-a9227ebec969','2026-09-28 01:30:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-7557-7029-8ded-900996435698','RSV-20260930-0015','01a128f4-6f30-71f3-b35f-546e14843469','01a128f4-71d3-722d-976e-72a4352cc282','2026-09-30','15:00:00','17:30:00','Workshop Desain Antarmuka Pengguna untuk Peserta Lomba Inovasi','selesai',NULL,NULL,NULL,NULL,'01a128f4-7116-7251-ac96-ed52a670663c','2026-09-29 04:30:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-757f-7387-b98a-eaa9606dad1c','RSV-20261001-0016','01a128f4-6fe5-72c1-a45e-8363a281b875','01a128f4-7210-73b3-939b-2daadca9a99b','2026-10-01','08:00:00','11:00:00','Praktikum Pengujian Keamanan Jaringan Menggunakan Wireshark','selesai',NULL,NULL,NULL,NULL,'01a128f4-7100-7299-822a-fef2b5defad4','2026-09-30 07:00:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-759e-70d2-a429-2e617eb8623e','RSV-20261002-0017','01a128f4-6ee5-72a6-b99c-782eaead499d','01a128f4-72ab-724d-b3ce-02083b272eac','2026-10-02','16:00:00','18:30:00','Latihan Bersama Futsal Antar Angkatan Teknik Informatika','selesai',NULL,NULL,NULL,NULL,'01a128f4-7136-717f-a9d1-0d5960145d56','2026-10-01 03:00:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-75b4-708b-a4fe-a2f5fb029f13','RSV-20261003-0018','01a128f4-6d97-711c-8880-d0cb41e84853','01a128f4-719a-700d-bb10-58e7acf98771','2026-10-03','09:00:00','12:00:00','Pembekalan Peserta Program Magang Industri Bersertifikat (MBKM)','selesai',NULL,NULL,NULL,NULL,'01a128f4-7116-7251-ac96-ed52a670663c','2026-10-02 01:30:00',NULL,NULL,NULL,'2026-10-11 03:14:31','2026-10-11 03:14:31'),('01a128f4-75ca-70be-8c72-ad3c38339a73','RSV-20261005-0019','01a128f4-7005-7219-97d4-5d4c12c2692d','01a128f4-723b-72f4-a836-76b26dfb33ed','2026-10-05','10:00:00','13:00:00','Pendampingan Proyek Akhir Pengembangan Sistem Berbasis AI','selesai',NULL,NULL,NULL,NULL,'01a128f4-7100-7299-822a-fef2b5defad4','2026-10-04 04:00:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-75ea-7094-9060-128a2cacf2ab','RSV-20261006-0020','01a128f4-7090-709c-84a2-ba86918b4c24','01a128f4-726d-7241-9feb-b670eb2a6768','2026-10-06','08:00:00','12:00:00','Sosialisasi Hibah Penelitian dan Pengabdian Masyarakat LLDIKTI','selesai',NULL,NULL,NULL,NULL,'01a128f4-70ea-73f1-9145-a9227ebec969','2026-10-05 02:00:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-7600-73b0-b3b0-cdf2c9de8ece','RSV-20261006-0021','01a128f4-6fa2-72d1-b053-cf5ed58f816a','01a128f4-72d6-7216-9165-3360cc1b8e1d','2026-10-06','16:00:00','18:00:00','Latihan Taktik dan Shooting UKM Basket Menghadapi Turnamen','selesai',NULL,NULL,NULL,NULL,'01a128f4-7136-717f-a9d1-0d5960145d56','2026-10-05 06:00:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-761e-7089-ac93-cf8fd7947a5e','RSV-20261007-0022','01a128f4-701b-73f2-8ea6-b56cd94575d4','01a128f4-71d3-722d-976e-72a4352cc282','2026-10-07','13:00:00','15:30:00','Ujian Kuis Tengah Semester Pemrograman Berorientasi Objek','selesai',NULL,NULL,NULL,NULL,'01a128f4-7116-7251-ac96-ed52a670663c','2026-10-06 03:30:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-7633-7187-a889-566f04535340','RSV-20261008-0023','01a128f4-705d-70e1-a4da-b71dcdacad6f','01a128f4-7301-7065-9cb1-f970f1f79a99','2026-10-08','09:00:00','11:30:00','Rapat Koordinasi Beasiswa Mahasiswa Berprestasi dan KIP-Kuliah','selesai',NULL,NULL,NULL,NULL,'01a128f4-70ea-73f1-9145-a9227ebec969','2026-10-07 07:00:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-7652-737d-94d3-c6d48e8c9bf1','RSV-20261008-0024','01a128f4-6efb-70b0-a93a-81b9921ef834','01a128f4-7334-70e2-905c-5bfd90b34ee2','2026-10-08','14:00:00','17:00:00','Rekaman Audio Teater Musikal UKM Seni Kampus','selesai',NULL,NULL,NULL,NULL,'01a128f4-714c-70b6-b81f-e3784f869a66','2026-10-07 08:30:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-7669-723e-b769-e11eec5ff55f','RSV-20261009-0025','01a128f4-6f30-71f3-b35f-546e14843469','01a128f4-719a-700d-bb10-58e7acf98771','2026-10-09','08:30:00','11:00:00','Simulasi Presentasi Lomba Esai Ilmiah Nasional Mahasiswa','selesai',NULL,NULL,NULL,NULL,'01a128f4-7116-7251-ac96-ed52a670663c','2026-10-08 02:00:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-767f-73eb-914b-9a92476c7849','RSV-20261005-0026','01a128f4-6f4d-7203-93aa-661162418f1a','01a128f4-71d3-722d-976e-72a4352cc282','2026-10-05','08:00:00','10:00:00','Belajar Bersama Persiapan Kuis Algoritma dan Pemrograman','kedaluwarsa',NULL,NULL,NULL,'Waktu mulai reservasi telah terlampaui sebelum disetujui petugas.',NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-7695-70ca-a8d8-490a8b806b6d','RSV-20261007-0027','01a128f4-6f82-7339-9f35-69644269c1a7','01a128f4-7210-73b3-939b-2daadca9a99b','2026-10-07','09:30:00','12:00:00','Eksperimen Mandiri Konfigurasi Web Server Apache di Debian','kedaluwarsa',NULL,NULL,NULL,'Waktu mulai reservasi telah terlampaui sebelum disetujui petugas.',NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-76b4-7316-9dcc-72a181ac1b42','RSV-20261008-0028','01a128f4-6fb8-72d3-b2ce-f204ae693632','01a128f4-72ab-724d-b3ce-02083b272eac','2026-10-08','13:00:00','15:00:00','Pertandingan Persahabatan Futsal Antar Kelas Semester 3','kedaluwarsa',NULL,NULL,NULL,'Waktu mulai reservasi telah terlampaui sebelum disetujui petugas.',NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-76c9-717a-a05c-ac53adeb1be0','RSV-20261013-0029','01a128f4-6fe5-72c1-a45e-8363a281b875','01a128f4-7210-73b3-939b-2daadca9a99b','2026-10-13','08:00:00','20:00:00','Praktikum Komputasi Jaringan & Sertifikasi Internasional Cisco','berlangsung',NULL,NULL,NULL,NULL,'01a128f4-7100-7299-822a-fef2b5defad4','2026-10-11 03:00:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-76e8-7219-ab86-4bb44ebca51e','RSV-20261013-0030','01a128f4-701b-73f2-8ea6-b56cd94575d4','01a128f4-719a-700d-bb10-58e7acf98771','2026-10-13','08:00:00','20:00:00','Kuliah Terbuka dan Sesi Tanya Jawab Terbimbing Bersama Dosen Tamu','berlangsung',NULL,NULL,NULL,NULL,'01a128f4-7116-7251-ac96-ed52a670663c','2026-10-11 04:30:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-76ff-7270-ac15-82bfd9b1d71f','RSV-20261013-0031','01a128f4-7090-709c-84a2-ba86918b4c24','01a128f4-726d-7241-9feb-b670eb2a6768','2026-10-13','08:00:00','20:00:00','Pameran Karya Ilmiah dan Rekayasa Teknologi Terapan Mahasiswa','berlangsung',NULL,NULL,NULL,NULL,'01a128f4-70ea-73f1-9145-a9227ebec969','2026-10-11 07:00:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-771a-7284-94e4-bb4188da4d80','RSV-20261013-0032','01a128f4-6ee5-72a6-b99c-782eaead499d','01a128f4-72ab-724d-b3ce-02083b272eac','2026-10-13','08:00:00','20:00:00','Pekan Olahraga Mahasiswa Tahunan Cabang Olahraga Futsal','berlangsung',NULL,NULL,NULL,NULL,'01a128f4-7136-717f-a9d1-0d5960145d56','2026-10-11 08:00:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-7742-734c-9c96-696cc2d75b10','RSV-20261013-0033','01a128f4-705d-70e1-a4da-b71dcdacad6f','01a128f4-7301-7065-9cb1-f970f1f79a99','2026-10-13','08:00:00','20:00:00','Dukungan Visualisasi Panggung Utama Pameran Teknologi Kampus','berlangsung',NULL,NULL,NULL,NULL,'01a128f4-70ea-73f1-9145-a9227ebec969','2026-10-11 09:00:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-7758-728d-bc5c-d05ffbeecc86','RSV-20261014-0034','01a128f4-6d97-711c-8880-d0cb41e84853','01a128f4-719a-700d-bb10-58e7acf98771','2026-10-14','11:30:00','13:30:00','Rapat Koordinasi Panitia Pemilihan Ketua Himpunan Mahasiswa','menunggu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-7775-729f-a474-53e47e1ec1de','RSV-20261014-0035','01a128f4-7005-7219-97d4-5d4c12c2692d','01a128f4-723b-72f4-a836-76b26dfb33ed','2026-10-14','13:00:00','16:00:00','Mentoring Teknis Tim Pengembang Aplikasi Web Sistem Kampus','menunggu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-778b-7349-aada-4596f85f7e6a','RSV-20261014-0036','01a128f4-6ecf-7098-961b-f5a0604c11cb','01a128f4-71d3-722d-976e-72a4352cc282','2026-10-14','16:00:00','18:00:00','Sosialisasi Hasil Rapat Dengar Pendapat Mahasiswa bersama Dekanat','menunggu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-77a2-7335-a68c-559d7e6e5dad','RSV-20261015-0037','01a128f4-6ee5-72a6-b99c-782eaead499d','01a128f4-72ab-724d-b3ce-02083b272eac','2026-10-15','16:30:00','18:30:00','Latihan Pemantapan Strategi Tim Futsal Mahasiswa Angkatan 2024','menunggu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-77b8-727d-a565-44248d666afb','RSV-20261015-0038','01a128f4-701b-73f2-8ea6-b56cd94575d4','01a128f4-719a-700d-bb10-58e7acf98771','2026-10-15','13:00:00','15:30:00','Sesi Tambahan Pendalaman Materi Kuliah Rekayasa Perangkat Lunak','menunggu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-77d8-715c-aa88-807e1b1f8385','RSV-20261015-0039','01a128f4-6efb-70b0-a93a-81b9921ef834','01a128f4-7334-70e2-905c-5bfd90b34ee2','2026-10-15','14:00:00','17:00:00','Persiapan Tata Suara Konser Amal Mahasiswa Dies Natalis Kampus','menunggu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-77ee-71e1-990c-aceb75dae120','RSV-20261016-0040','01a128f4-6f19-7155-a7cd-cdb8a9964b96','01a128f4-7210-73b3-939b-2daadca9a99b','2026-10-16','13:30:00','16:00:00','Workshop Git & GitHub Collaboration untuk Mahasiswa Tingkat Pertama','menunggu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-7802-7025-b360-f670a94b50ef','RSV-20261016-0041','01a128f4-7090-709c-84a2-ba86918b4c24','01a128f4-726d-7241-9feb-b670eb2a6768','2026-10-16','08:30:00','11:30:00','Rapat Pembagian Tugas Pengawas Ujian Tengah Semester Ganjil','menunggu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-7822-70ce-9183-00df04f21740','RSV-20261016-0042','01a128f4-6fa2-72d1-b053-cf5ed58f816a','01a128f4-72d6-7216-9165-3360cc1b8e1d','2026-10-16','08:00:00','10:30:00','Latihan Pagi Kebugaran Jasmani Atlet Basket Kampus','menunggu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-784f-71b5-bd5c-303432d54795','RSV-20261017-0043','01a128f4-7046-7015-8d1a-07414631b1a0','01a128f4-719a-700d-bb10-58e7acf98771','2026-10-17','09:00:00','12:00:00','Ujian Sidang Skripsi Terbuka Mahasiswa Tingkat Akhir','menunggu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-7893-7312-a8be-e5fb23a85bd3','RSV-20261017-0044','01a128f4-6f61-7305-922b-2c3c0fc87de5','01a128f4-723b-72f4-a836-76b26dfb33ed','2026-10-17','13:00:00','16:00:00','Pengerjaan Tugas Besar Mata Kuliah Arsitektur Perangkat Lunak','menunggu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-78b1-713a-b669-89d5fd39ee17','RSV-20261017-0045','01a128f4-705d-70e1-a4da-b71dcdacad6f','01a128f4-7301-7065-9cb1-f970f1f79a99','2026-10-17','13:30:00','16:00:00','Pelatihan Administrasi Persuratan Digital bagi Staf Tata Usaha','menunggu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-78c7-7224-9d56-ceb37818eca5','RSV-20261018-0046','01a128f4-6f30-71f3-b35f-546e14843469','01a128f4-71d3-722d-976e-72a4352cc282','2026-10-18','09:00:00','11:30:00','Simulasi Debat Kompetisi Mahasiswa Tingkat Nasional','menunggu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-78db-71ae-8d77-8c22e4960350','RSV-20261018-0047','01a128f4-7031-7292-bd26-c398c65cce6a','01a128f4-7210-73b3-939b-2daadca9a99b','2026-10-18','10:00:00','13:00:00','Uji Coba Infrastruktur Virtualisasi Server dan Cloud Computing','menunggu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-78fb-72aa-86ae-14e0254845f4','RSV-20261018-0048','01a128f4-70cc-712b-9d07-314bcd9677ac','01a128f4-726d-7241-9feb-b670eb2a6768','2026-10-18','13:00:00','17:00:00','Gladi Bersih Pelantikan Pengurus Organisasi Kemahasiswaan Kampus','menunggu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-7912-70bb-bcd4-cd6b7b76bd5c','RSV-20261014-0049','01a128f4-701b-73f2-8ea6-b56cd94575d4','01a128f4-719a-700d-bb10-58e7acf98771','2026-10-14','09:00:00','11:00:00','Kuliah Teori Pemrograman Lanjut Kelas Reguler A','disetujui',NULL,NULL,NULL,NULL,'01a128f4-7116-7251-ac96-ed52a670663c','2026-10-12 02:00:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-7927-71e8-b195-9d3ecfbf6aa0','RSV-20261014-0050','01a128f4-7046-7015-8d1a-07414631b1a0','01a128f4-71d3-722d-976e-72a4352cc282','2026-10-14','13:00:00','15:30:00','Seminar Diskusi Ilmiah Topik Tren Kecerdasan Buatan Generatif','disetujui',NULL,NULL,NULL,NULL,'01a128f4-7116-7251-ac96-ed52a670663c','2026-10-12 03:00:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-793e-7229-941d-031d8bd47243','RSV-20261015-0051','01a128f4-7005-7219-97d4-5d4c12c2692d','01a128f4-723b-72f4-a836-76b26dfb33ed','2026-10-15','08:30:00','11:30:00','Praktikum Desain Pengujian Perangkat Lunak (QA Automation)','disetujui',NULL,NULL,NULL,NULL,'01a128f4-7100-7299-822a-fef2b5defad4','2026-10-12 04:00:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-7954-71e8-83e1-00b5441b626b','RSV-20261015-0052','01a128f4-7090-709c-84a2-ba86918b4c24','01a128f4-726d-7241-9feb-b670eb2a6768','2026-10-15','14:00:00','17:00:00','Rapat Senat Akademik Terbuka Pembahasan Kurikulum Merdeka','disetujui',NULL,NULL,NULL,NULL,'01a128f4-70ea-73f1-9145-a9227ebec969','2026-10-12 04:30:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-7974-73ef-96b2-57e24c5cc0ca','RSV-20261016-0053','01a128f4-6fe5-72c1-a45e-8363a281b875','01a128f4-7210-73b3-939b-2daadca9a99b','2026-10-16','09:00:00','12:00:00','Ujian Sertifikasi Jaringan Komputer MikroTik MTCNA','disetujui',NULL,NULL,NULL,NULL,'01a128f4-7100-7299-822a-fef2b5defad4','2026-10-12 06:00:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-7988-701e-805b-84d317636905','RSV-20261016-0054','01a128f4-6fa2-72d1-b053-cf5ed58f816a','01a128f4-72d6-7216-9165-3360cc1b8e1d','2026-10-16','15:00:00','17:30:00','Pertandingan Basket Persahabatan Antar Jurusan','disetujui',NULL,NULL,NULL,NULL,'01a128f4-7136-717f-a9d1-0d5960145d56','2026-10-12 07:00:00',NULL,NULL,NULL,'2026-10-11 03:14:32','2026-10-11 03:14:32'),('01a128f4-79a8-73b7-9c4d-7da4f8f4944e','RSV-20261017-0055','01a128f4-6ee5-72a6-b99c-782eaead499d','01a128f4-72ab-724d-b3ce-02083b272eac','2026-10-17','08:00:00','11:00:00','Turnamen Futsal Cup Mahasiswa Baru Tahun Akademik 2026','disetujui',NULL,NULL,NULL,NULL,'01a128f4-7136-717f-a9d1-0d5960145d56','2026-10-12 08:00:00',NULL,NULL,NULL,'2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-79c7-72e4-acfe-c72da534a8df','RSV-20261019-0056','01a128f4-701b-73f2-8ea6-b56cd94575d4','01a128f4-719a-700d-bb10-58e7acf98771','2026-10-19','10:00:00','13:00:00','Kuliah Teori Pemrograman Lanjut Kelas Reguler B','disetujui',NULL,NULL,NULL,NULL,'01a128f4-7116-7251-ac96-ed52a670663c','2026-10-12 09:00:00',NULL,NULL,NULL,'2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-79e4-71ba-ad60-ea4d65ecdbba','RSV-20261006-0057','01a128f4-6f4d-7203-93aa-661162418f1a','01a128f4-726d-7241-9feb-b670eb2a6768','2026-10-06','09:00:00','12:00:00','Kegiatan Nobar Turnamen Game Online Mahasiswa Angkatan','ditolak','Pengajuan bentrok dengan agenda universitas: Sosialisasi Hibah Penelitian yang telah disetujui sebelumnya.',NULL,NULL,NULL,NULL,NULL,'01a128f4-70ea-73f1-9145-a9227ebec969','2026-10-05 03:00:00',NULL,'2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-79fa-710a-9e2a-cfecd551ffb6','RSV-20261007-0058','01a128f4-6f82-7339-9f35-69644269c1a7','01a128f4-723b-72f4-a836-76b26dfb33ed','2026-10-07','13:00:00','16:00:00','Pelatihan Desain Grafis Komersial untuk Lembaga Bimbingan Belajar Luar','ditolak','Fasilitas laboratorium tidak dapat digunakan untuk kegiatan berbayar/komersial pihak eksternal tanpa izin dekanat.',NULL,NULL,NULL,NULL,NULL,'01a128f4-7100-7299-822a-fef2b5defad4','2026-10-06 04:30:00',NULL,'2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-7a21-73e3-a8a0-56a859a95517','RSV-20261008-0059','01a128f4-6fce-702d-b1e2-4c2c7ccc091b','01a128f4-71d3-722d-976e-72a4352cc282','2026-10-08','14:00:00','17:00:00','Seminar Akbar Bersama Alumni dengan Jumlah Peserta 80 Orang','ditolak','Jumlah peserta (80 orang) melebihi batas kapasitas maksimal ruangan (30 orang). Disarankan mengajukan Aula Nusantara.',NULL,NULL,NULL,NULL,NULL,'01a128f4-7116-7251-ac96-ed52a670663c','2026-10-07 07:00:00',NULL,'2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-7a3d-70b6-b27e-6f92f4e9190e','RSV-20261009-0060','01a128f4-6fb8-72d3-b2ce-f204ae693632','01a128f4-72ab-724d-b3ce-02083b272eac','2026-10-09','17:00:00','19:30:00','Latihan Futsal Bebas Sore Hari Tanpa Surat Rekomendasi Pembina','ditolak','Pengajuan penggunaan lapangan di luar jam perkuliahan wajib menyertakan surat persetujuan dari Pembina UKM Olahraga.',NULL,NULL,NULL,NULL,NULL,'01a128f4-7136-717f-a9d1-0d5960145d56','2026-10-08 09:30:00',NULL,'2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-7a5e-73ac-9905-e89e2e6b1f65','RSV-20261010-0061','01a128f4-6f61-7305-922b-2c3c0fc87de5','01a128f4-7334-70e2-905c-5bfd90b34ee2','2026-10-10','10:00:00','14:00:00','Peminjaman Konsol Mixer untuk Kegiatan Konser di Luar Kampus','ditolak','Sesuai regulasi sarpras kampus, perangkat audio mixer menetap di ruang kendali Aula Nusantara dan tidak boleh dibawa keluar.',NULL,NULL,NULL,NULL,NULL,'01a128f4-714c-70b6-b81f-e3784f869a66','2026-10-09 04:00:00',NULL,'2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-7a74-716d-bd96-93b575022a64','RSV-20261011-0062','01a128f4-6d97-711c-8880-d0cb41e84853','01a128f4-719a-700d-bb10-58e7acf98771','2026-10-11','13:00:00','16:00:00','Rapat Umum Mahasiswa Persiapan Aksi Unjuk Rasa BEM Wilayah','ditolak','Agenda kegiatan belum mendapatkan izin dan tanda tangan pengesahan dari Wakil Dekan Bidang Kemahasiswaan.',NULL,NULL,NULL,NULL,NULL,'01a128f4-7116-7251-ac96-ed52a670663c','2026-10-10 07:00:00',NULL,'2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-7a95-70c3-81dc-cde672700814','RSV-20261012-0063','01a128f4-6f19-7155-a7cd-cdb8a9964b96','01a128f4-7210-73b3-939b-2daadca9a99b','2026-10-12','08:00:00','11:00:00','Uji Coba Script Penetration Testing di Jaringan Komputer Lokal','ditolak','Pengujian keamanan jaringan berpotensi mengganggu stabilitas server utama kampus yang sedang menjalankan proses KRS.',NULL,NULL,NULL,NULL,NULL,'01a128f4-7100-7299-822a-fef2b5defad4','2026-10-11 06:00:00',NULL,'2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-7aab-7243-98d5-8ea728702b78','RSV-20261014-0064','01a128f4-6ecf-7098-961b-f5a0604c11cb','01a128f4-719a-700d-bb10-58e7acf98771','2026-10-14','14:00:00','16:30:00','Rapat Internal Pengurus BEM Bahas Persiapan Pemilu Mahasiswa','dibatalkan_oleh_pengguna',NULL,'Rapat dibatalkan mandiri oleh pemohon karena narasumber dekanat berhalangan hadir pada jam tersebut.','01a128f4-6ecf-7098-961b-f5a0604c11cb',NULL,NULL,NULL,NULL,NULL,'2026-10-12 08:00:00','2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-7ac1-7208-a3f6-210b7451306a','RSV-20261015-0065','01a128f4-701b-73f2-8ea6-b56cd94575d4','01a128f4-71d3-722d-976e-72a4352cc282','2026-10-15','10:00:00','12:30:00','Kuliah Pengganti Matematika Diskrit Kelas B','dibatalkan_oleh_pengguna',NULL,'Dosen pengampu memutuskan mengubah sesi perkuliahan pengganti menjadi sesi webinar online melalui Zoom.','01a128f4-701b-73f2-8ea6-b56cd94575d4',NULL,NULL,NULL,NULL,NULL,'2026-10-12 09:30:00','2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-7ad6-70bc-bc6c-3a3acc184999','RSV-20261016-0066','01a128f4-6ee5-72a6-b99c-782eaead499d','01a128f4-72ab-724d-b3ce-02083b272eac','2026-10-16','16:00:00','18:00:00','Latihan Tanding Persahabatan Futsal Lawan Kampus Tetangga','dibatalkan_oleh_pengguna',NULL,'Pihak lawan membatalkan pertandingan karena bertepatan dengan jadwal praktikum wajib di universitas mereka.','01a128f4-6ee5-72a6-b99c-782eaead499d',NULL,NULL,NULL,NULL,NULL,'2026-10-12 10:00:00','2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-7afa-71b2-bdd3-772ee205c2c7','RSV-20261017-0067','01a128f4-705d-70e1-a4da-b71dcdacad6f','01a128f4-7301-7065-9cb1-f970f1f79a99','2026-10-17','09:00:00','11:30:00','Presentasi Evaluasi Kinerja Tenaga Kependidikan Triwulan III','dibatalkan_oleh_pengguna',NULL,'Peminjaman proyektor dibatalkan karena ruangan rapat dekanat sudah dilengkapi proyektor terpasang permanen.','01a128f4-705d-70e1-a4da-b71dcdacad6f',NULL,NULL,NULL,NULL,NULL,'2026-10-12 10:30:00','2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-7b2e-72ee-a138-43540e87e2a9','RSV-20261014-0068','01a128f4-6f30-71f3-b35f-546e14843469','01a128f4-719a-700d-bb10-58e7acf98771','2026-10-14','16:30:00','19:00:00','Latihan Presentasi dan Penjurian Internal Lomba Inovasi Bisnis','dibatalkan_oleh_petugas',NULL,'Pembatalan darurat oleh sarpras: Unit AC Ruang A.101 mengalami kebocoran air parah dan korsleting listrik mendadak.','01a128f4-7116-7251-ac96-ed52a670663c',NULL,'01a128f4-7116-7251-ac96-ed52a670663c','2026-10-11 02:00:00',NULL,NULL,'2026-10-12 07:00:00','2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-7b67-73dd-a3fd-1d1e3eecf5f6','RSV-20261015-0069','01a128f4-7046-7015-8d1a-07414631b1a0','01a128f4-726d-7241-9feb-b670eb2a6768','2026-10-15','08:30:00','12:00:00','Seminar Umum Pengenalan Peluang Karir Bidang Keamanan Siber','dibatalkan_oleh_petugas',NULL,'Pembatalan darurat oleh petugas: Aula dialihkan mendadak untuk Rapat Kerja Luar Biasa Pimpinan Universitas dan Kunjungan Asesor.','01a128f4-70ea-73f1-9145-a9227ebec969',NULL,'01a128f4-70ea-73f1-9145-a9227ebec969','2026-10-11 04:00:00',NULL,NULL,'2026-10-12 08:30:00','2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128f4-7b7d-72a9-bffe-cb93903032be','RSV-20261016-0070','01a128f4-6fa2-72d1-b053-cf5ed58f816a','01a128f4-72d6-7216-9165-3360cc1b8e1d','2026-10-16','13:00:00','15:00:00','Latihan Rutin Basket Srikandi Kampus Menjelang Kejuaraan','dibatalkan_oleh_petugas',NULL,'Pembatalan darurat oleh pengelola: Ring basket sisi utara mengalami patah hidrolik saat pemeriksaan rutin dan memerlukan perbaikan las.','01a128f4-7136-717f-a9d1-0d5960145d56',NULL,'01a128f4-7136-717f-a9d1-0d5960145d56','2026-10-11 06:00:00',NULL,NULL,'2026-10-12 09:00:00','2026-10-11 03:14:33','2026-10-11 03:14:33'),('01a128fc-514e-7149-a3bc-9022f466a797','RSV-20261011-0071','01a128f4-69fc-701a-966c-026f9ef5b13b','01a128f4-7210-73b3-939b-2daadca9a99b','2026-10-12','07:00:00','08:30:00','Nanio','disetujui',NULL,NULL,NULL,NULL,'01a128f4-682b-71c4-a83a-97ab1b690f14','2026-10-11 03:24:27',NULL,NULL,NULL,'2026-10-11 03:23:06','2026-10-11 03:24:27');
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
INSERT INTO `sessions` VALUES ('b8YeuWfuN8p2FaLDJemjN0wh94lwvcNFkaVHF1qC','01a128f4-6bd1-7341-91c9-e22d6fba99d5','172.18.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','eyJfdG9rZW4iOiIxNHNxRWJUQTNJMzB5RmEwWExreTdsdkUxT0NrVEJoWXZ5aHhBY2VrIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2ZhY2lsaXRpZXMiLCJyb3V0ZSI6ImZhY2lsaXRpZXMuaW5kZXgifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOiIwMWExMjhmNC02YmQxLTczNDEtOTFjOS1lMjJkNmZiYTk5ZDUifQ==',1791690487);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','petugas','pengguna') COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_category` enum('mahasiswa','dosen','staf') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`),
  CONSTRAINT `chk_users_category_matches_role` CHECK ((((`role` = _utf8mb4'pengguna') and (`user_category` is not null)) or ((`role` <> _utf8mb4'pengguna') and (`user_category` is null)))),
  CONSTRAINT `chk_users_name_not_blank` CHECK ((char_length(trim(`name`)) > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('01a128f4-6756-70d9-bbd4-2c298e0b9c87','Administrator PPK','admin@ppk.test','$2y$12$ze1hE3gVQVRUYmaUkWaMvuwM/vdF3vjVaxMQlXA63TfBTX3wS0zEO','admin',NULL,NULL,'2026-10-11 03:14:28','2026-10-11 03:21:33'),('01a128f4-682b-71c4-a83a-97ab1b690f14','Petugas Sarpras Kampus','petugas.demo@ppk.test','$2y$12$atfDxGqXXEIKgdALTsKP6.c/sCimygSCixt.qHkKp44nHDtOGAdVK','petugas',NULL,NULL,'2026-10-11 03:14:28','2026-10-11 03:21:33'),('01a128f4-691a-73ba-b59f-94717a2f12fe','Ahmad Mahasiswa','mahasiswa.demo@ppk.test','$2y$12$lnfBR5sUb5CiMRXg/fUWn.ThLMBNcTIr1yJFNKEtHLXptQXU9Dl7e','pengguna','mahasiswa',NULL,'2026-10-11 03:14:28','2026-10-11 03:21:34'),('01a128f4-69fc-701a-966c-026f9ef5b13b','Dr. Budi Santoso, M.Kom.','dosen.demo@ppk.test','$2y$12$LOS9I4.u2wnuVZxl.cN7ae0T9Jv54GTQvKfw3ltccv6SS23Ckpnge','pengguna','dosen',NULL,'2026-10-11 03:14:28','2026-10-11 03:21:34'),('01a128f4-6bd1-7341-91c9-e22d6fba99d5','Siti Aminah, S.AP.','staf.demo@ppk.test','$2y$12$91TZ9KKshLmXOZJiEUcrXeo666ZaxEW2dJKwGt7bQeAnClUlfaoPm','pengguna','staf',NULL,'2026-10-11 03:14:29','2026-10-11 03:21:35'),('01a128f4-6d97-711c-8880-d0cb41e84853','Dimas Arya Pratama','mhs.dimas@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','mahasiswa',NULL,'2026-10-11 03:14:29','2026-10-11 03:21:35'),('01a128f4-6ecf-7098-961b-f5a0604c11cb','Anindya Putri Rahayu','mhs.anindya@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','mahasiswa',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-6ee5-72a6-b99c-782eaead499d','Farhan Maulana','mhs.farhan@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','mahasiswa',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-6efb-70b0-a93a-81b9921ef834','Nadhira Zahra','mhs.nadhira@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','mahasiswa',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-6f19-7155-a7cd-cdb8a9964b96','Reza Kurniawan','mhs.reza@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','mahasiswa',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-6f30-71f3-b35f-546e14843469','Tiara Puspitasari','mhs.tiara@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','mahasiswa',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-6f4d-7203-93aa-661162418f1a','Kevin Sanjaya','mhs.kevin@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','mahasiswa',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-6f61-7305-922b-2c3c0fc87de5','Alifia Nurul Hasanah','mhs.alifia@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','mahasiswa',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-6f82-7339-9f35-69644269c1a7','Bagus Setiawan','mhs.bagus@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','mahasiswa',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-6fa2-72d1-b053-cf5ed58f816a','Cantika Dewi Lestari','mhs.cantika@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','mahasiswa',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-6fb8-72d3-b2ce-f204ae693632','Danu Wicaksono','mhs.danu@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','mahasiswa',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-6fce-702d-b1e2-4c2c7ccc091b','Fathir Muhammad','mhs.fathir@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','mahasiswa',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-6fe5-72c1-a45e-8363a281b875','Prof. Dr. Hendra Gunawan, M.T.','dosen.hendra@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','dosen',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-7005-7219-97d4-5d4c12c2692d','Dr. Ratna Kusuma Wardani, M.Cs.','dosen.ratna@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','dosen',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-701b-73f2-8ea6-b56cd94575d4','Ir. Irwan Prasetyo, M.Eng.','dosen.irwan@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','dosen',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-7031-7292-bd26-c398c65cce6a','Maya Indriani, S.Kom., M.T.','dosen.maya@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','dosen',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-7046-7015-8d1a-07414631b1a0','Dr. Yusuf Habibie, S.T., M.Kom.','dosen.yusuf@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','dosen',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-705d-70e1-a4da-b71dcdacad6f','Bambang Sudiro, S.Sos.','staf.bambang@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','staf',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-7090-709c-84a2-ba86918b4c24','Dewi Anggraeni, S.E.','staf.dewi@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','staf',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-70cc-712b-9d07-314bcd9677ac','Eko Prasetyawan, A.Md.','staf.eko@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','pengguna','staf',NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-70ea-73f1-9145-a9227ebec969','Agus Setiawan, S.T.','petugas.sarpras@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','petugas',NULL,NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-7100-7299-822a-fef2b5defad4','Rian Hidayat, A.Md.Kom.','petugas.lab@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','petugas',NULL,NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-7116-7251-ac96-ed52a670663c','Hendri Wijaya','petugas.gedung@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','petugas',NULL,NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-7136-717f-a9d1-0d5960145d56','Bayu Pratama','petugas.olahraga@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','petugas',NULL,NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-714c-70b6-b81f-e3784f869a66','Joko Susilo, A.Md.','petugas.audiovisual@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','petugas',NULL,NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-7162-70b6-9861-3bf1485e7e09','Ir. H. Suryono, M.T.','admin.sarpras@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','admin',NULL,NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35'),('01a128f4-7178-71f8-942a-ac0fb9e2ec50','Nanda Pratama, S.Kom.','admin.sistem@ppk.test','$2y$12$E7X5Bq/L/Aosaa7XS08YhOShG9IrY/XiLbCBESwY.JownjdSJsw6S','admin',NULL,NULL,'2026-10-11 03:14:30','2026-10-11 03:21:35');
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

-- Dump completed on 2026-10-11 11:07:02
