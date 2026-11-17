-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: tahfidz_absensi_test
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB-log

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
-- Table structure for table `absensis`
--

DROP TABLE IF EXISTS `absensis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `absensis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kelompok_id` bigint(20) unsigned NOT NULL,
  `siswa_id` bigint(20) unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_mulai` time DEFAULT NULL,
  `waktu_selesai` time DEFAULT NULL,
  `status_kehadiran` enum('Hadir','Izin','Sakit','Alfa','Libur') NOT NULL,
  `guru_pengisi_id` bigint(20) unsigned NOT NULL,
  `alfa_berturut_turut` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `override_admin` tinyint(1) NOT NULL DEFAULT 0,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_absensi` (`kelompok_id`,`siswa_id`,`tanggal`),
  KEY `absensis_admin_id_foreign` (`admin_id`),
  KEY `idx_kelompok_tanggal` (`kelompok_id`,`tanggal`),
  KEY `idx_siswa_tanggal` (`siswa_id`,`tanggal`),
  KEY `idx_guru_pengisi` (`guru_pengisi_id`),
  KEY `idx_status` (`status_kehadiran`),
  KEY `absensis_siswa_id_tanggal_index` (`siswa_id`,`tanggal`),
  KEY `absensis_kelompok_tanggal_index` (`kelompok_id`,`tanggal`),
  KEY `idx_absensis_kelompok_tanggal` (`kelompok_id`,`tanggal`),
  KEY `idx_absensis_siswa_tanggal` (`siswa_id`,`tanggal`),
  KEY `idx_absensis_status` (`status_kehadiran`),
  CONSTRAINT `absensis_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `absensis_guru_pengisi_id_foreign` FOREIGN KEY (`guru_pengisi_id`) REFERENCES `gurus` (`id`),
  CONSTRAINT `absensis_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompoks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `absensis_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `absensis`
--

LOCK TABLES `absensis` WRITE;
/*!40000 ALTER TABLE `absensis` DISABLE KEYS */;
INSERT INTO `absensis` (`id`, `kelompok_id`, `siswa_id`, `tanggal`, `waktu_mulai`, `waktu_selesai`, `status_kehadiran`, `guru_pengisi_id`, `alfa_berturut_turut`, `override_admin`, `admin_id`, `created_at`, `updated_at`) VALUES (1,1,2,'2024-07-01','11:17:00','13:17:00','Hadir',6,0,1,1,'2026-08-23 04:18:17','2026-08-23 04:18:18'),(2,1,3,'2024-07-01','11:17:00','13:17:00','Hadir',6,0,1,1,'2026-08-23 04:22:15','2026-08-23 04:22:15'),(3,1,2,'2024-07-02','11:22:00','13:22:00','Hadir',6,0,1,1,'2026-08-23 04:22:39','2026-08-23 04:22:39'),(4,1,2,'2024-07-03','11:22:00','13:22:00','Hadir',6,0,1,1,'2026-08-23 04:22:59','2026-08-23 04:22:59'),(5,1,2,'2024-07-04','11:32:00','13:32:00','Hadir',6,0,0,NULL,'2026-08-23 04:32:45','2026-08-23 04:32:45'),(6,1,2,'2024-07-05','11:32:00','13:32:00','Hadir',6,0,0,NULL,'2026-08-23 04:34:29','2026-08-23 04:34:29'),(7,1,2,'2024-07-06','11:36:00','13:36:00','Hadir',6,0,0,NULL,'2026-08-23 04:37:03','2026-08-23 04:37:03'),(8,1,2,'2024-07-08','11:37:00','13:37:00','Hadir',6,0,0,NULL,'2026-08-23 04:37:59','2026-08-23 04:37:59'),(9,1,2,'2024-07-09','11:38:00','13:38:00','Hadir',6,0,0,NULL,'2026-08-23 04:38:16','2026-08-23 04:38:16'),(10,1,2,'2024-07-10','11:38:00','13:38:00','Hadir',6,0,0,NULL,'2026-08-23 04:38:35','2026-08-23 04:38:35'),(11,1,3,'2024-07-02','11:22:00','13:22:00','Hadir',6,0,0,NULL,'2026-08-23 04:39:07','2026-08-23 04:39:08'),(12,1,3,'2024-07-03','11:22:00','13:22:00','Hadir',6,0,0,NULL,'2026-08-23 04:39:35','2026-08-23 04:39:35'),(13,1,3,'2024-07-04','11:32:00','13:32:00','Hadir',6,0,0,NULL,'2026-08-23 04:39:54','2026-08-23 04:39:54'),(14,1,3,'2024-07-05','11:32:00','13:32:00','Hadir',6,0,0,NULL,'2026-08-23 04:40:17','2026-08-23 04:40:17'),(15,1,3,'2024-07-06','11:36:00','13:36:00','Hadir',6,0,0,NULL,'2026-08-23 04:42:47','2026-08-23 04:42:47'),(16,1,3,'2024-07-08','11:37:00','13:37:00','Hadir',6,0,0,NULL,'2026-08-23 04:44:02','2026-08-23 04:44:02'),(17,1,3,'2024-07-09','11:38:00','13:38:00','Hadir',6,0,0,NULL,'2026-08-23 04:45:38','2026-08-23 04:45:38'),(18,1,3,'2024-07-10','11:38:00','13:38:00','Hadir',6,0,0,NULL,'2026-08-23 04:46:56','2026-08-23 04:46:56'),(19,1,2,'2024-07-11','11:47:00','13:47:00','Hadir',6,0,0,NULL,'2026-08-23 04:47:21','2026-08-23 04:47:21'),(20,1,2,'2024-07-12','11:47:00','13:47:00','Hadir',6,0,0,NULL,'2026-08-23 04:47:38','2026-08-23 04:47:38'),(21,1,2,'2024-07-13','11:47:00','13:47:00','Hadir',6,0,0,NULL,'2026-08-23 04:47:55','2026-08-23 04:47:55'),(22,1,2,'2024-07-15','11:47:00','13:47:00','Hadir',6,0,0,NULL,'2026-08-23 04:48:33','2026-08-23 04:48:33'),(23,1,2,'2024-07-16','11:48:00','13:48:00','Hadir',6,0,0,NULL,'2026-08-23 04:48:52','2026-08-23 04:48:52'),(24,1,2,'2024-07-17','11:48:00','13:48:00','Hadir',6,0,0,NULL,'2026-08-23 04:51:02','2026-08-23 04:51:02'),(25,1,2,'2024-07-18','11:51:00','13:51:00','Hadir',6,0,0,NULL,'2026-08-23 04:51:44','2026-08-23 04:51:44'),(26,1,2,'2024-07-19','11:52:00','13:52:00','Hadir',6,0,0,NULL,'2026-08-23 04:52:43','2026-08-23 04:52:43'),(27,1,2,'2024-07-20','11:52:00','13:52:00','Hadir',6,0,0,NULL,'2026-08-23 04:53:22','2026-08-23 04:53:22'),(28,1,2,'2024-07-22','11:53:00','13:53:00','Hadir',6,0,0,NULL,'2026-08-23 04:53:46','2026-08-23 04:53:46'),(29,1,3,'2024-07-11','11:47:00','13:47:00','Sakit',6,0,0,NULL,'2026-08-23 04:53:58','2026-08-23 04:53:58'),(30,1,3,'2024-07-12','11:47:00','13:47:00','Sakit',6,0,0,NULL,'2026-08-23 04:54:13','2026-08-23 04:54:13'),(31,1,3,'2024-07-13','11:47:00','13:47:00','Hadir',6,0,0,NULL,'2026-08-23 04:54:36','2026-08-23 04:54:36'),(32,1,3,'2024-07-15','11:47:00','13:47:00','Hadir',6,0,0,NULL,'2026-08-23 04:54:58','2026-08-23 04:54:58'),(33,1,3,'2024-07-16','11:48:00','13:48:00','Hadir',6,0,0,NULL,'2026-08-23 04:55:17','2026-08-23 04:55:17'),(34,1,3,'2024-07-17','11:48:00','13:48:00','Izin',6,0,0,NULL,'2026-08-23 04:55:34','2026-08-23 04:55:34'),(35,1,2,'2024-07-23','11:55:00','13:55:00','Sakit',6,0,0,NULL,'2026-08-23 04:55:53','2026-08-23 04:55:53'),(36,1,3,'2024-07-18','11:51:00','13:51:00','Hadir',6,0,0,NULL,'2026-08-23 04:56:11','2026-08-23 04:56:11'),(37,1,3,'2024-07-19','11:52:00','13:52:00','Hadir',6,0,0,NULL,'2026-08-23 04:56:28','2026-08-23 04:56:28'),(38,1,3,'2024-07-20','11:52:00','13:52:00','Hadir',6,0,0,NULL,'2026-08-23 04:56:57','2026-08-23 04:56:57'),(39,1,3,'2024-07-22','11:53:00','13:53:00','Hadir',6,0,0,NULL,'2026-08-23 04:57:18','2026-08-23 04:57:18'),(40,1,3,'2024-07-23','11:55:00','13:55:00','Hadir',6,0,0,NULL,'2026-08-23 04:57:37','2026-08-23 04:57:37'),(41,1,3,'2024-07-24','11:57:00','13:57:00','Hadir',6,0,0,NULL,'2026-08-23 04:58:06','2026-08-23 04:58:07'),(42,1,2,'2024-07-24','11:57:00','13:57:00','Hadir',6,0,0,NULL,'2026-08-23 04:58:26','2026-08-23 04:58:26'),(44,1,2,'2024-07-26','11:59:00','13:59:00','Hadir',6,0,0,NULL,'2026-08-23 05:01:45','2026-08-23 05:01:45'),(46,1,3,'2024-07-26','11:59:00','13:59:00','Hadir',6,0,0,NULL,'2026-08-23 05:04:08','2026-08-23 05:04:08'),(47,1,2,'2024-07-25','12:07:00','14:07:00','Libur',6,0,0,NULL,'2026-08-23 05:07:30','2026-08-23 05:07:30'),(48,1,2,'2024-07-27','12:15:00','14:15:00','Hadir',6,0,0,NULL,'2026-08-23 05:15:58','2026-08-23 05:15:58'),(49,1,3,'2024-07-27','12:15:00','14:15:00','Hadir',6,0,0,NULL,'2026-08-23 05:16:12','2026-08-23 05:16:12'),(50,1,2,'2024-07-29','12:16:00','14:16:00','Hadir',6,0,0,NULL,'2026-08-23 05:16:24','2026-08-23 05:16:24'),(51,1,2,'2024-07-30','12:16:00','14:16:00','Hadir',6,0,0,NULL,'2026-08-23 05:16:43','2026-08-23 05:16:43'),(52,1,2,'2024-07-31','12:16:00','14:16:00','Hadir',6,0,0,NULL,'2026-08-23 05:16:56','2026-08-23 05:16:56'),(53,1,3,'2024-07-29','12:16:00','14:16:00','Hadir',6,0,0,NULL,'2026-08-23 05:17:09','2026-08-23 05:17:09'),(54,1,3,'2024-07-30','12:16:00','14:16:00','Hadir',6,0,0,NULL,'2026-08-23 05:17:28','2026-08-23 05:17:28'),(55,1,3,'2024-07-31','12:16:00','14:16:00','Hadir',6,0,0,NULL,'2026-08-23 05:18:12','2026-08-23 05:18:12'),(91,1,2,'2024-08-01','12:22:00','14:22:00','Hadir',6,0,0,NULL,'2026-08-23 05:23:04','2026-08-23 05:23:04'),(92,1,3,'2024-08-01','12:22:00','14:22:00','Hadir',6,0,0,NULL,'2026-08-23 05:25:36','2026-08-23 05:25:37'),(93,1,2,'2024-08-02','12:25:00','14:25:00','Hadir',6,0,0,NULL,'2026-08-23 05:25:51','2026-08-23 05:25:51'),(94,1,3,'2024-08-02','12:25:00','14:25:00','Hadir',6,0,0,NULL,'2026-08-23 05:30:02','2026-08-23 05:30:02'),(95,1,2,'2024-08-03','12:30:00','14:30:00','Hadir',6,0,0,NULL,'2026-08-23 05:30:14','2026-08-23 05:30:14'),(96,1,3,'2024-08-03','12:30:00','14:30:00','Hadir',6,0,0,NULL,'2026-08-23 05:30:28','2026-08-23 05:30:28'),(97,1,2,'2024-08-05','12:30:00','14:30:00','Hadir',6,0,0,NULL,'2026-08-23 05:30:40','2026-08-23 05:30:40'),(98,1,3,'2024-08-05','12:30:00','14:30:00','Hadir',6,0,0,NULL,'2026-08-23 05:31:04','2026-08-23 05:31:04'),(99,1,2,'2024-08-06','12:31:00','14:31:00','Hadir',6,0,0,NULL,'2026-08-23 05:31:18','2026-08-23 05:31:18'),(100,1,3,'2024-08-06','12:31:00','14:31:00','Hadir',6,0,0,NULL,'2026-08-23 05:31:31','2026-08-23 05:31:31'),(101,1,2,'2024-08-07','12:31:00','14:31:00','Hadir',6,0,0,NULL,'2026-08-23 05:31:43','2026-08-23 05:31:43'),(102,1,3,'2024-08-07','12:31:00','14:31:00','Hadir',6,0,0,NULL,'2026-08-23 05:34:44','2026-08-23 05:34:44'),(103,1,2,'2024-08-08','12:34:00','14:34:00','Hadir',6,0,0,NULL,'2026-08-23 05:36:58','2026-08-23 05:36:58'),(104,1,3,'2024-08-08','12:34:00','14:34:00','Hadir',6,0,0,NULL,'2026-08-23 05:37:25','2026-08-23 05:37:25'),(105,1,2,'2024-08-09','12:37:00','14:37:00','Hadir',6,0,0,NULL,'2026-08-23 05:37:38','2026-08-23 05:37:38'),(106,1,3,'2024-08-09','12:37:00','14:37:00','Hadir',6,0,0,NULL,'2026-08-23 05:37:51','2026-08-23 05:37:51'),(107,1,2,'2024-08-10','12:37:00','14:37:00','Hadir',6,0,0,NULL,'2026-08-23 05:38:04','2026-08-23 05:38:04'),(108,1,3,'2024-08-10','12:37:00','14:37:00','Hadir',6,0,0,NULL,'2026-08-23 05:38:18','2026-08-23 05:38:18'),(109,1,2,'2024-08-12','12:38:00','14:38:00','Hadir',6,0,0,NULL,'2026-08-23 05:38:30','2026-08-23 05:38:30'),(110,1,2,'2024-08-13','12:38:00','14:38:00','Alfa',6,1,0,NULL,'2026-08-23 05:38:37','2026-08-23 05:38:37'),(111,1,3,'2024-08-12','12:38:00','14:38:00','Hadir',6,0,0,NULL,'2026-08-23 05:38:50','2026-08-23 05:38:50'),(112,1,3,'2024-08-13','12:38:00','14:38:00','Hadir',6,0,0,NULL,'2026-08-23 05:39:02','2026-08-23 05:39:02'),(113,1,3,'2024-08-14','12:39:00','14:39:00','Hadir',6,0,0,NULL,'2026-08-23 05:39:18','2026-08-23 05:39:18'),(114,1,3,'2024-08-15','12:39:00','14:39:00','Hadir',6,0,0,NULL,'2026-08-23 05:39:31','2026-08-23 05:39:31'),(115,1,3,'2024-08-16','12:39:00','14:39:00','Hadir',6,0,0,NULL,'2026-08-23 05:39:47','2026-08-23 05:39:48'),(116,1,3,'2024-08-17','12:39:00','14:39:00','Hadir',6,0,0,NULL,'2026-08-23 05:40:04','2026-08-23 05:40:04'),(117,1,2,'2024-08-14','12:39:00','14:39:00','Hadir',6,0,0,NULL,'2026-08-23 05:40:18','2026-08-23 05:40:18'),(118,1,2,'2024-08-15','12:39:00','14:39:00','Hadir',6,0,0,NULL,'2026-08-23 05:40:31','2026-08-23 05:40:31'),(120,2,2,'2024-08-16','14:57:00','16:57:00','Hadir',1,0,0,NULL,'2026-08-23 07:59:14','2026-08-23 07:59:14'),(121,2,2,'2024-08-17','14:59:00','16:59:00','Hadir',1,0,0,NULL,'2026-08-23 08:00:03','2026-08-23 08:00:03'),(122,2,2,'2024-08-19','15:00:00','17:00:00','Hadir',1,0,0,NULL,'2026-08-23 08:00:24','2026-08-23 08:00:24'),(123,2,2,'2024-08-20','15:00:00','17:00:00','Hadir',1,0,0,NULL,'2026-08-23 08:00:56','2026-08-23 08:00:57'),(124,2,2,'2024-08-21','15:01:00','17:01:00','Hadir',1,0,0,NULL,'2026-08-23 08:01:46','2026-08-23 08:01:46'),(125,2,2,'2024-08-22','15:01:00','17:01:00','Hadir',1,0,0,NULL,'2026-08-23 08:02:38','2026-08-23 08:02:38'),(126,2,2,'2024-08-23','15:02:00','17:02:00','Hadir',1,0,0,NULL,'2026-08-23 08:03:08','2026-08-23 08:03:08'),(127,2,2,'2024-08-24','15:03:00','17:03:00','Hadir',1,0,0,NULL,'2026-08-23 08:03:43','2026-08-23 08:03:43'),(128,2,2,'2024-08-26','15:03:00','17:03:00','Hadir',1,0,0,NULL,'2026-08-23 08:04:11','2026-08-23 08:04:11'),(129,2,2,'2024-08-27','15:04:00','17:04:00','Hadir',1,0,0,NULL,'2026-08-23 08:04:43','2026-08-23 08:04:43'),(130,2,2,'2024-08-28','15:04:00','17:04:00','Hadir',1,0,0,NULL,'2026-08-23 08:05:11','2026-08-23 08:05:11'),(131,2,3,'2024-08-19','15:00:00','17:00:00','Hadir',1,0,0,NULL,'2026-08-23 08:05:37','2026-08-23 08:05:37'),(132,2,3,'2024-08-20','15:00:00','17:00:00','Hadir',1,0,0,NULL,'2026-08-23 08:06:02','2026-08-23 08:06:02'),(133,2,3,'2024-08-21','15:01:00','17:01:00','Hadir',1,0,0,NULL,'2026-08-23 08:06:20','2026-08-23 08:06:20'),(134,2,3,'2024-08-22','15:01:00','17:01:00','Hadir',1,0,0,NULL,'2026-08-23 08:06:42','2026-08-23 08:06:42'),(135,2,3,'2024-08-23','15:02:00','17:02:00','Hadir',1,0,0,NULL,'2026-08-23 08:07:01','2026-08-23 08:07:01'),(136,2,3,'2024-08-24','15:03:00','17:03:00','Hadir',1,0,0,NULL,'2026-08-23 08:07:28','2026-08-23 08:07:28'),(137,2,3,'2024-08-26','15:03:00','17:03:00','Hadir',1,0,0,NULL,'2026-08-23 08:07:50','2026-08-23 08:07:50'),(138,2,3,'2024-08-27','15:04:00','17:04:00','Hadir',1,0,0,NULL,'2026-08-23 08:09:38','2026-08-23 08:09:38'),(139,2,3,'2024-08-28','15:04:00','17:04:00','Hadir',1,0,0,NULL,'2026-08-23 08:10:02','2026-08-23 08:10:02'),(140,2,3,'2024-08-29','15:10:00','17:10:00','Hadir',1,0,0,NULL,'2026-08-23 08:10:19','2026-08-23 08:10:19'),(141,2,3,'2024-08-30','15:10:00','17:10:00','Hadir',1,0,0,NULL,'2026-08-23 08:10:37','2026-08-23 08:10:37'),(142,3,2,'2024-08-29','15:21:00','17:21:00','Hadir',7,0,0,NULL,'2026-08-23 08:21:34','2026-08-23 08:21:35'),(143,3,2,'2024-08-30','15:21:00','17:21:00','Hadir',7,0,0,NULL,'2026-08-23 08:22:03','2026-08-23 08:22:03'),(144,3,2,'2024-08-31','15:23:00','17:23:00','Hadir',7,0,0,NULL,'2026-08-23 08:23:44','2026-08-23 08:23:44'),(145,3,3,'2024-08-31','15:23:00','17:23:00','Hadir',7,0,0,NULL,'2026-08-23 08:24:44','2026-08-23 08:24:44'),(146,3,2,'2024-09-02','15:27:00','17:27:00','Hadir',7,0,0,NULL,'2026-08-23 08:27:37','2026-08-23 08:27:37'),(147,3,2,'2024-09-03','15:30:00','17:30:00','Hadir',7,0,0,NULL,'2026-08-23 08:30:31','2026-08-23 08:30:31'),(148,3,2,'2024-09-04','15:31:00','17:31:00','Hadir',7,0,0,NULL,'2026-08-23 08:31:11','2026-08-23 08:31:11'),(149,3,2,'2024-09-05','15:31:00','17:31:00','Hadir',7,0,0,NULL,'2026-08-23 08:31:24','2026-08-23 08:31:24'),(150,3,2,'2024-09-06','15:31:00','17:31:00','Hadir',7,0,0,NULL,'2026-08-23 08:31:38','2026-08-23 08:31:38'),(151,3,2,'2024-09-07','15:31:00','17:31:00','Hadir',7,0,0,NULL,'2026-08-23 08:33:22','2026-08-23 08:33:22'),(152,3,3,'2024-09-02','15:27:00','17:27:00','Hadir',7,0,0,NULL,'2026-08-23 08:34:04','2026-08-23 08:34:04'),(153,3,3,'2024-09-03','15:30:00','17:30:00','Hadir',7,0,0,NULL,'2026-08-23 08:34:18','2026-08-23 08:34:18'),(154,3,3,'2024-09-04','15:31:00','17:31:00','Hadir',7,0,0,NULL,'2026-08-23 08:34:43','2026-08-23 08:34:43'),(155,3,3,'2024-09-05','15:31:00','17:31:00','Hadir',7,0,0,NULL,'2026-08-23 08:34:58','2026-08-23 08:34:58'),(156,3,3,'2024-09-06','15:31:00','17:31:00','Hadir',7,0,0,NULL,'2026-08-23 08:35:12','2026-08-23 08:35:12'),(157,3,3,'2024-09-07','15:31:00','17:31:00','Hadir',7,0,0,NULL,'2026-08-23 08:35:27','2026-08-23 08:35:27'),(158,3,3,'2024-09-09','15:35:00','17:35:00','Hadir',7,0,0,NULL,'2026-08-23 08:35:42','2026-08-23 08:35:42'),(159,3,3,'2024-09-10','15:35:00','17:35:00','Hadir',7,0,0,NULL,'2026-08-23 08:35:56','2026-08-23 08:35:56');
/*!40000 ALTER TABLE `absensis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) unsigned DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`),
  KEY `activity_log_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES (1,'auth','Login: 1',NULL,NULL,NULL,'App\\Models\\Admin',1,'{\"guard\":\"admin\",\"ip_address\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/151.0.0.0 Safari\\/537.36\"}',NULL,'2026-08-22 16:27:16','2026-08-22 16:27:16'),(2,'default','Snapshot created before batas_tingkat change',NULL,NULL,NULL,NULL,NULL,'[]',NULL,'2026-08-22 18:03:25','2026-08-22 18:03:25'),(3,'auth','Login: 1',NULL,NULL,NULL,'App\\Models\\Admin',1,'{\"guard\":\"admin\",\"ip_address\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/151.0.0.0 Safari\\/537.36\"}',NULL,'2026-08-23 01:43:08','2026-08-23 01:43:08'),(4,'kelompok','Kelompok [Kelompok A] dibentuk dengan guru pembimbing awal.','App\\Models\\Kelompok',NULL,1,'App\\Models\\Admin',1,'[]',NULL,'2026-08-23 02:28:04','2026-08-23 02:28:04'),(5,'kelompok','Kelompok [Kelompok B] dibentuk dengan guru pembimbing awal.','App\\Models\\Kelompok',NULL,2,'App\\Models\\Admin',1,'[]',NULL,'2026-08-23 02:40:28','2026-08-23 02:40:28'),(6,'kelompok','Kelompok [Kelompok C] dibentuk dengan guru pembimbing awal.','App\\Models\\Kelompok',NULL,3,'App\\Models\\Admin',1,'[]',NULL,'2026-08-23 02:41:02','2026-08-23 02:41:02'),(7,'default','Snapshot created before target_kelulusan change',NULL,NULL,NULL,'App\\Models\\Admin',1,'[]',NULL,'2026-08-23 02:42:09','2026-08-23 02:42:09'),(8,'default','Snapshot created before batas_tingkat change',NULL,NULL,NULL,'App\\Models\\Admin',1,'[]',NULL,'2026-08-23 02:42:09','2026-08-23 02:42:09'),(9,'default','Baseline progres hafalan disimpan dari placement test','App\\Models\\ProgresHafalan',NULL,2,'App\\Models\\Admin',1,'{\"baseline_juzs\":[],\"rekomendasi_tingkat\":null}',NULL,'2026-08-23 02:51:18','2026-08-23 02:51:18'),(10,'default','Baseline progres hafalan disimpan dari placement test','App\\Models\\ProgresHafalan',NULL,3,'App\\Models\\Admin',1,'{\"baseline_juzs\":[],\"rekomendasi_tingkat\":null}',NULL,'2026-08-23 02:54:57','2026-08-23 02:54:57'),(11,'auth','Login: 6',NULL,NULL,NULL,'App\\Models\\Guru',6,'{\"guard\":\"guru\",\"ip_address\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/151.0.0.0 Safari\\/537.36\"}',NULL,'2026-08-23 04:26:31','2026-08-23 04:26:31'),(12,'auth','Logout: 6',NULL,NULL,NULL,'App\\Models\\Guru',6,'{\"guard\":\"guru\"}',NULL,'2026-08-23 04:27:36','2026-08-23 04:27:36'),(13,'auth','Login: 6',NULL,NULL,NULL,'App\\Models\\Guru',6,'{\"guard\":\"guru\",\"ip_address\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/151.0.0.0 Safari\\/537.36\"}',NULL,'2026-08-23 04:29:05','2026-08-23 04:29:05'),(20,'default','Usulan kenaikan tingkat dibuat','App\\Models\\UsulanKenaikanTingkat','created',7,NULL,NULL,'{\"attributes\":{\"siswa_id\":2,\"guru_pengusul_id\":6,\"tingkat_asal\":\"Dasar\",\"tingkat_tujuan\":\"Menengah\",\"kelompok_asal_id\":1,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2026-08-23T06:03:02.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 06:03:02','2026-08-23 06:03:02'),(21,'default','Usulan kenaikan tingkat dibuat','App\\Models\\UsulanKenaikanTingkat','created',8,NULL,NULL,'{\"attributes\":{\"siswa_id\":3,\"guru_pengusul_id\":6,\"tingkat_asal\":\"Dasar\",\"tingkat_tujuan\":\"Menengah\",\"kelompok_asal_id\":1,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2026-08-23T06:03:15.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 06:03:15','2026-08-23 06:03:15'),(22,'default','Usulan kenaikan tingkat dihapus','App\\Models\\UsulanKenaikanTingkat','deleted',7,NULL,NULL,'{\"old\":{\"siswa_id\":2,\"guru_pengusul_id\":6,\"tingkat_asal\":\"Dasar\",\"tingkat_tujuan\":\"Menengah\",\"kelompok_asal_id\":1,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2026-08-23T06:03:02.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 06:04:53','2026-08-23 06:04:53'),(23,'default','Usulan kenaikan tingkat dihapus','App\\Models\\UsulanKenaikanTingkat','deleted',8,NULL,NULL,'{\"old\":{\"siswa_id\":3,\"guru_pengusul_id\":6,\"tingkat_asal\":\"Dasar\",\"tingkat_tujuan\":\"Menengah\",\"kelompok_asal_id\":1,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2026-08-23T06:03:15.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 06:04:58','2026-08-23 06:04:58'),(24,'auth','Login: 3',NULL,NULL,NULL,'App\\Models\\Siswa',3,'{\"guard\":\"siswa\",\"ip_address\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/151.0.0.0 Safari\\/537.36\"}',NULL,'2026-08-23 06:15:36','2026-08-23 06:15:36'),(25,'auth','Logout: 3',NULL,NULL,NULL,'App\\Models\\Siswa',3,'{\"guard\":\"siswa\"}',NULL,'2026-08-23 06:16:12','2026-08-23 06:16:12'),(26,'auth','Login: 3',NULL,NULL,NULL,'App\\Models\\Siswa',3,'{\"guard\":\"siswa\",\"ip_address\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/151.0.0.0 Safari\\/537.36\"}',NULL,'2026-08-23 06:16:37','2026-08-23 06:16:37'),(27,'default','Usulan kenaikan tingkat dibuat','App\\Models\\UsulanKenaikanTingkat','created',9,NULL,NULL,'{\"attributes\":{\"siswa_id\":2,\"guru_pengusul_id\":6,\"tingkat_asal\":\"Dasar\",\"tingkat_tujuan\":\"Menengah\",\"kelompok_asal_id\":1,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2026-08-23T06:20:28.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 06:20:28','2026-08-23 06:20:28'),(28,'default','Usulan kenaikan tingkat dihapus','App\\Models\\UsulanKenaikanTingkat','deleted',9,NULL,NULL,'{\"old\":{\"siswa_id\":2,\"guru_pengusul_id\":6,\"tingkat_asal\":\"Dasar\",\"tingkat_tujuan\":\"Menengah\",\"kelompok_asal_id\":1,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2026-08-23T06:20:28.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 06:27:00','2026-08-23 06:27:00'),(29,'default','Usulan kenaikan tingkat dibuat','App\\Models\\UsulanKenaikanTingkat','created',10,NULL,NULL,'{\"attributes\":{\"siswa_id\":2,\"guru_pengusul_id\":6,\"tingkat_asal\":\"Dasar\",\"tingkat_tujuan\":\"Menengah\",\"kelompok_asal_id\":1,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2026-08-23T06:27:18.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 06:27:18','2026-08-23 06:27:18'),(30,'default','Usulan kenaikan tingkat dibuat','App\\Models\\UsulanKenaikanTingkat','created',11,NULL,NULL,'{\"attributes\":{\"siswa_id\":3,\"guru_pengusul_id\":6,\"tingkat_asal\":\"Dasar\",\"tingkat_tujuan\":\"Menengah\",\"kelompok_asal_id\":1,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2026-08-23T06:28:35.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 06:28:35','2026-08-23 06:28:35'),(31,'default','Usulan kenaikan tingkat dihapus','App\\Models\\UsulanKenaikanTingkat','deleted',10,NULL,NULL,'{\"old\":{\"siswa_id\":2,\"guru_pengusul_id\":6,\"tingkat_asal\":\"Dasar\",\"tingkat_tujuan\":\"Menengah\",\"kelompok_asal_id\":1,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2026-08-23T06:27:18.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 06:32:32','2026-08-23 06:32:32'),(32,'default','Usulan kenaikan tingkat dihapus','App\\Models\\UsulanKenaikanTingkat','deleted',11,NULL,NULL,'{\"old\":{\"siswa_id\":3,\"guru_pengusul_id\":6,\"tingkat_asal\":\"Dasar\",\"tingkat_tujuan\":\"Menengah\",\"kelompok_asal_id\":1,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2026-08-23T06:28:35.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 06:32:35','2026-08-23 06:32:35'),(33,'default','Usulan kenaikan tingkat dibuat','App\\Models\\UsulanKenaikanTingkat','created',12,NULL,NULL,'{\"attributes\":{\"siswa_id\":2,\"guru_pengusul_id\":6,\"tingkat_asal\":\"Dasar\",\"tingkat_tujuan\":\"Menengah\",\"kelompok_asal_id\":1,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2024-08-14T17:00:00.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 06:33:45','2026-08-23 06:33:45'),(34,'default','Usulan kenaikan tingkat dibuat','App\\Models\\UsulanKenaikanTingkat','created',13,NULL,NULL,'{\"attributes\":{\"siswa_id\":3,\"guru_pengusul_id\":6,\"tingkat_asal\":\"Dasar\",\"tingkat_tujuan\":\"Menengah\",\"kelompok_asal_id\":1,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2024-08-16T17:00:00.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 06:55:34','2026-08-23 06:55:34'),(35,'default','Usulan kenaikan tingkat dihapus','App\\Models\\UsulanKenaikanTingkat','deleted',13,NULL,NULL,'{\"old\":{\"siswa_id\":3,\"guru_pengusul_id\":6,\"tingkat_asal\":\"Dasar\",\"tingkat_tujuan\":\"Menengah\",\"kelompok_asal_id\":1,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2024-08-16T17:00:00.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 06:56:44','2026-08-23 06:56:44'),(36,'default','Usulan kenaikan tingkat dihapus','App\\Models\\UsulanKenaikanTingkat','deleted',12,NULL,NULL,'{\"old\":{\"siswa_id\":2,\"guru_pengusul_id\":6,\"tingkat_asal\":\"Dasar\",\"tingkat_tujuan\":\"Menengah\",\"kelompok_asal_id\":1,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2024-08-14T17:00:00.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 06:56:48','2026-08-23 06:56:48'),(37,'default','Usulan kenaikan tingkat dibuat','App\\Models\\UsulanKenaikanTingkat','created',14,NULL,NULL,'{\"attributes\":{\"siswa_id\":2,\"guru_pengusul_id\":6,\"tingkat_asal\":\"Dasar\",\"tingkat_tujuan\":\"Menengah\",\"kelompok_asal_id\":1,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2024-08-14T17:00:00.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 07:01:32','2026-08-23 07:01:32'),(38,'default','Usulan kenaikan tingkat diperbarui','App\\Models\\UsulanKenaikanTingkat','updated',14,'App\\Models\\Admin',1,'{\"attributes\":{\"status_usulan\":\"Disetujui\",\"admin_verifikator_id\":1,\"tanggal_keputusan\":\"2024-07-14T17:00:00.000000Z\"},\"old\":{\"status_usulan\":\"Diajukan\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null}}',NULL,'2026-08-23 07:14:54','2026-08-23 07:14:54'),(39,'default','Usulan kenaikan tingkat disetujui','App\\Models\\UsulanKenaikanTingkat',NULL,14,'App\\Models\\Admin',1,'{\"siswa_nama\":\"ACHMAD SYAQIMAN\",\"status_lama\":\"Diajukan\",\"status_baru\":\"Disetujui\",\"tanggal_keputusan\":\"2024-07-14T17:00:00.000000Z\",\"catatan_keputusan\":null,\"causer_type\":\"Admin\",\"causer_id\":1}',NULL,'2026-08-23 07:14:54','2026-08-23 07:14:54'),(40,'default','Usulan kenaikan tingkat diperbarui','App\\Models\\UsulanKenaikanTingkat','updated',14,'App\\Models\\Admin',1,'{\"attributes\":{\"status_usulan\":\"Diajukan\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null},\"old\":{\"status_usulan\":\"Disetujui\",\"admin_verifikator_id\":1,\"tanggal_keputusan\":\"2024-07-14T17:00:00.000000Z\"}}',NULL,'2026-08-23 07:15:22','2026-08-23 07:15:22'),(41,'default','Persetujuan usulan kenaikan tingkat dibatalkan','App\\Models\\UsulanKenaikanTingkat',NULL,14,'App\\Models\\Admin',1,'{\"siswa_nama\":\"ACHMAD SYAQIMAN\",\"status_lama\":\"Disetujui\",\"status_baru\":\"Diajukan\",\"alasan_pembatalan\":\"Kesalahan Input\",\"old_admin_verifikator_id\":1,\"old_tanggal_keputusan\":\"2024-07-14T17:00:00.000000Z\",\"old_catatan_keputusan\":null,\"causer_type\":\"Admin\",\"causer_id\":1}',NULL,'2026-08-23 07:15:22','2026-08-23 07:15:22'),(42,'default','Usulan kenaikan tingkat diperbarui','App\\Models\\UsulanKenaikanTingkat','updated',14,'App\\Models\\Admin',1,'{\"attributes\":{\"status_usulan\":\"Disetujui\",\"admin_verifikator_id\":1,\"tanggal_keputusan\":\"2024-08-14T17:00:00.000000Z\"},\"old\":{\"status_usulan\":\"Diajukan\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null}}',NULL,'2026-08-23 07:15:53','2026-08-23 07:15:53'),(43,'default','Usulan kenaikan tingkat disetujui','App\\Models\\UsulanKenaikanTingkat',NULL,14,'App\\Models\\Admin',1,'{\"siswa_nama\":\"ACHMAD SYAQIMAN\",\"status_lama\":\"Diajukan\",\"status_baru\":\"Disetujui\",\"tanggal_keputusan\":\"2024-08-14T17:00:00.000000Z\",\"catatan_keputusan\":null,\"causer_type\":\"Admin\",\"causer_id\":1}',NULL,'2026-08-23 07:15:53','2026-08-23 07:15:53'),(44,'default','Usulan kenaikan tingkat diperbarui','App\\Models\\UsulanKenaikanTingkat','updated',14,'App\\Models\\Admin',1,'{\"attributes\":{\"kelompok_tujuan_id\":2,\"status_usulan\":\"Ditempatkan\",\"tanggal_penempatan\":\"2024-08-15T17:00:00.000000Z\"},\"old\":{\"kelompok_tujuan_id\":null,\"status_usulan\":\"Disetujui\",\"tanggal_penempatan\":null}}',NULL,'2026-08-23 07:25:40','2026-08-23 07:25:40'),(45,'default','Siswa ditempatkan ke kelompok baru','App\\Models\\UsulanKenaikanTingkat',NULL,14,'App\\Models\\Admin',1,'{\"siswa_nama\":\"ACHMAD SYAQIMAN\",\"status_lama\":\"Disetujui\",\"status_baru\":\"Ditempatkan\",\"kelompok_dari\":\"Kelompok A\",\"kelompok_ke\":\"Kelompok B\",\"tanggal_penempatan\":\"2024-08-15T17:00:00.000000Z\",\"causer_type\":\"Admin\",\"causer_id\":1}',NULL,'2026-08-23 07:25:41','2026-08-23 07:25:41'),(46,'default','Usulan kenaikan tingkat dibuat','App\\Models\\UsulanKenaikanTingkat','created',15,NULL,NULL,'{\"attributes\":{\"siswa_id\":3,\"guru_pengusul_id\":6,\"tingkat_asal\":\"Dasar\",\"tingkat_tujuan\":\"Menengah\",\"kelompok_asal_id\":1,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2024-08-16T17:00:00.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 07:26:22','2026-08-23 07:26:22'),(47,'default','Usulan kenaikan tingkat diperbarui','App\\Models\\UsulanKenaikanTingkat','updated',15,'App\\Models\\Admin',1,'{\"attributes\":{\"status_usulan\":\"Disetujui\",\"admin_verifikator_id\":1,\"tanggal_keputusan\":\"2024-08-16T17:00:00.000000Z\"},\"old\":{\"status_usulan\":\"Diajukan\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null}}',NULL,'2026-08-23 07:26:52','2026-08-23 07:26:52'),(48,'default','Usulan kenaikan tingkat disetujui','App\\Models\\UsulanKenaikanTingkat',NULL,15,'App\\Models\\Admin',1,'{\"siswa_nama\":\"Akmal Alfitrah Budiman\",\"status_lama\":\"Diajukan\",\"status_baru\":\"Disetujui\",\"tanggal_keputusan\":\"2024-08-16T17:00:00.000000Z\",\"catatan_keputusan\":null,\"causer_type\":\"Admin\",\"causer_id\":1}',NULL,'2026-08-23 07:26:52','2026-08-23 07:26:52'),(49,'default','Usulan kenaikan tingkat diperbarui','App\\Models\\UsulanKenaikanTingkat','updated',15,'App\\Models\\Admin',1,'{\"attributes\":{\"kelompok_tujuan_id\":2,\"status_usulan\":\"Ditempatkan\",\"tanggal_penempatan\":\"2024-08-18T17:00:00.000000Z\"},\"old\":{\"kelompok_tujuan_id\":null,\"status_usulan\":\"Disetujui\",\"tanggal_penempatan\":null}}',NULL,'2026-08-23 07:27:25','2026-08-23 07:27:25'),(50,'default','Siswa ditempatkan ke kelompok baru','App\\Models\\UsulanKenaikanTingkat',NULL,15,'App\\Models\\Admin',1,'{\"siswa_nama\":\"Akmal Alfitrah Budiman\",\"status_lama\":\"Disetujui\",\"status_baru\":\"Ditempatkan\",\"kelompok_dari\":\"Kelompok A\",\"kelompok_ke\":\"Kelompok B\",\"tanggal_penempatan\":\"2024-08-18T17:00:00.000000Z\",\"causer_type\":\"Admin\",\"causer_id\":1}',NULL,'2026-08-23 07:27:26','2026-08-23 07:27:26'),(51,'auth','Logout: 6',NULL,NULL,NULL,'App\\Models\\Guru',6,'{\"guard\":\"guru\"}',NULL,'2026-08-23 07:27:56','2026-08-23 07:27:56'),(52,'auth','Login: 1',NULL,NULL,NULL,'App\\Models\\Guru',1,'{\"guard\":\"guru\",\"ip_address\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/151.0.0.0 Safari\\/537.36\"}',NULL,'2026-08-23 07:28:55','2026-08-23 07:28:55'),(53,'auth','Logout: 1',NULL,NULL,NULL,'App\\Models\\Guru',1,'{\"guard\":\"guru\"}',NULL,'2026-08-23 07:29:31','2026-08-23 07:29:31'),(54,'auth','Login: 1',NULL,NULL,NULL,'App\\Models\\Guru',1,'{\"guard\":\"guru\",\"ip_address\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/151.0.0.0 Safari\\/537.36\"}',NULL,'2026-08-23 07:29:56','2026-08-23 07:29:56'),(55,'auth','Logout: 3',NULL,NULL,NULL,'App\\Models\\Siswa',3,'{\"guard\":\"siswa\"}',NULL,'2026-08-23 07:32:13','2026-08-23 07:32:13'),(56,'auth','Login: 3',NULL,NULL,NULL,'App\\Models\\Siswa',3,'{\"guard\":\"siswa\",\"ip_address\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/151.0.0.0 Safari\\/537.36\"}',NULL,'2026-08-23 07:32:49','2026-08-23 07:32:49'),(57,'default','Usulan kenaikan tingkat dibuat','App\\Models\\UsulanKenaikanTingkat','created',16,NULL,NULL,'{\"attributes\":{\"siswa_id\":2,\"guru_pengusul_id\":1,\"tingkat_asal\":\"Menengah\",\"tingkat_tujuan\":\"Atas\",\"kelompok_asal_id\":2,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2024-08-27T17:00:00.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 08:11:02','2026-08-23 08:11:02'),(58,'default','Usulan kenaikan tingkat dibuat','App\\Models\\UsulanKenaikanTingkat','created',17,NULL,NULL,'{\"attributes\":{\"siswa_id\":3,\"guru_pengusul_id\":1,\"tingkat_asal\":\"Menengah\",\"tingkat_tujuan\":\"Atas\",\"kelompok_asal_id\":2,\"kelompok_tujuan_id\":null,\"status_usulan\":\"Diajukan\",\"alasan\":\"Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.\",\"tanggal_usul\":\"2024-08-29T17:00:00.000000Z\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null,\"catatan_keputusan\":null,\"tanggal_penempatan\":null}}',NULL,'2026-08-23 08:11:14','2026-08-23 08:11:14'),(59,'default','Usulan kenaikan tingkat diperbarui','App\\Models\\UsulanKenaikanTingkat','updated',17,'App\\Models\\Admin',1,'{\"attributes\":{\"status_usulan\":\"Disetujui\",\"admin_verifikator_id\":1,\"tanggal_keputusan\":\"2024-08-29T17:00:00.000000Z\"},\"old\":{\"status_usulan\":\"Diajukan\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null}}',NULL,'2026-08-23 08:13:15','2026-08-23 08:13:15'),(60,'default','Usulan kenaikan tingkat disetujui','App\\Models\\UsulanKenaikanTingkat',NULL,17,'App\\Models\\Admin',1,'{\"siswa_nama\":\"Akmal Alfitrah Budiman\",\"status_lama\":\"Diajukan\",\"status_baru\":\"Disetujui\",\"tanggal_keputusan\":\"2024-08-29T17:00:00.000000Z\",\"catatan_keputusan\":null,\"causer_type\":\"Admin\",\"causer_id\":1}',NULL,'2026-08-23 08:13:15','2026-08-23 08:13:15'),(61,'default','Usulan kenaikan tingkat diperbarui','App\\Models\\UsulanKenaikanTingkat','updated',17,'App\\Models\\Admin',1,'{\"attributes\":{\"kelompok_tujuan_id\":3,\"status_usulan\":\"Ditempatkan\",\"tanggal_penempatan\":\"2024-08-30T17:00:00.000000Z\"},\"old\":{\"kelompok_tujuan_id\":null,\"status_usulan\":\"Disetujui\",\"tanggal_penempatan\":null}}',NULL,'2026-08-23 08:13:40','2026-08-23 08:13:40'),(62,'default','Siswa ditempatkan ke kelompok baru','App\\Models\\UsulanKenaikanTingkat',NULL,17,'App\\Models\\Admin',1,'{\"siswa_nama\":\"Akmal Alfitrah Budiman\",\"status_lama\":\"Disetujui\",\"status_baru\":\"Ditempatkan\",\"kelompok_dari\":\"Kelompok B\",\"kelompok_ke\":\"Kelompok C\",\"tanggal_penempatan\":\"2024-08-30T17:00:00.000000Z\",\"causer_type\":\"Admin\",\"causer_id\":1}',NULL,'2026-08-23 08:13:40','2026-08-23 08:13:40'),(63,'default','Usulan kenaikan tingkat diperbarui','App\\Models\\UsulanKenaikanTingkat','updated',16,'App\\Models\\Admin',1,'{\"attributes\":{\"status_usulan\":\"Disetujui\",\"admin_verifikator_id\":1,\"tanggal_keputusan\":\"2024-08-27T17:00:00.000000Z\"},\"old\":{\"status_usulan\":\"Diajukan\",\"admin_verifikator_id\":null,\"tanggal_keputusan\":null}}',NULL,'2026-08-23 08:14:06','2026-08-23 08:14:06'),(64,'default','Usulan kenaikan tingkat disetujui','App\\Models\\UsulanKenaikanTingkat',NULL,16,'App\\Models\\Admin',1,'{\"siswa_nama\":\"ACHMAD SYAQIMAN\",\"status_lama\":\"Diajukan\",\"status_baru\":\"Disetujui\",\"tanggal_keputusan\":\"2024-08-27T17:00:00.000000Z\",\"catatan_keputusan\":null,\"causer_type\":\"Admin\",\"causer_id\":1}',NULL,'2026-08-23 08:14:06','2026-08-23 08:14:06'),(65,'default','Usulan kenaikan tingkat diperbarui','App\\Models\\UsulanKenaikanTingkat','updated',16,'App\\Models\\Admin',1,'{\"attributes\":{\"kelompok_tujuan_id\":3,\"status_usulan\":\"Ditempatkan\",\"tanggal_penempatan\":\"2024-08-28T17:00:00.000000Z\"},\"old\":{\"kelompok_tujuan_id\":null,\"status_usulan\":\"Disetujui\",\"tanggal_penempatan\":null}}',NULL,'2026-08-23 08:14:30','2026-08-23 08:14:30'),(66,'default','Siswa ditempatkan ke kelompok baru','App\\Models\\UsulanKenaikanTingkat',NULL,16,'App\\Models\\Admin',1,'{\"siswa_nama\":\"ACHMAD SYAQIMAN\",\"status_lama\":\"Disetujui\",\"status_baru\":\"Ditempatkan\",\"kelompok_dari\":\"Kelompok B\",\"kelompok_ke\":\"Kelompok C\",\"tanggal_penempatan\":\"2024-08-28T17:00:00.000000Z\",\"causer_type\":\"Admin\",\"causer_id\":1}',NULL,'2026-08-23 08:14:30','2026-08-23 08:14:30'),(67,'auth','Logout: 1',NULL,NULL,NULL,'App\\Models\\Guru',1,'{\"guard\":\"guru\"}',NULL,'2026-08-23 08:16:06','2026-08-23 08:16:06'),(68,'auth','Login: 7',NULL,NULL,NULL,'App\\Models\\Guru',7,'{\"guard\":\"guru\",\"ip_address\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/151.0.0.0 Safari\\/537.36\"}',NULL,'2026-08-23 08:19:02','2026-08-23 08:19:02'),(69,'auth','Logout: 7',NULL,NULL,NULL,'App\\Models\\Guru',7,'{\"guard\":\"guru\"}',NULL,'2026-08-23 08:19:41','2026-08-23 08:19:41'),(70,'auth','Login: 7',NULL,NULL,NULL,'App\\Models\\Guru',7,'{\"guard\":\"guru\",\"ip_address\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/151.0.0.0 Safari\\/537.36\"}',NULL,'2026-08-23 08:20:02','2026-08-23 08:20:02'),(71,'default','Kelulusan program tahfidz disetujui','App\\Models\\KelulusanProgram',NULL,2,'App\\Models\\Admin',1,'{\"keputusan\":\"Disetujui\",\"causer_type\":\"Admin\",\"causer_id\":1}',NULL,'2026-08-23 08:36:37','2026-08-23 08:36:37'),(72,'default','Kelulusan program tahfidz disetujui','App\\Models\\KelulusanProgram',NULL,1,'App\\Models\\Admin',1,'{\"keputusan\":\"Disetujui\",\"causer_type\":\"Admin\",\"causer_id\":1}',NULL,'2026-08-23 08:36:51','2026-08-23 08:36:51'),(73,'auth','Logout: 3',NULL,NULL,NULL,'App\\Models\\Siswa',3,'{\"guard\":\"siswa\"}',NULL,'2026-08-23 10:00:06','2026-08-23 10:00:06'),(74,'auth','Logout: 7',NULL,NULL,NULL,'App\\Models\\Guru',7,'{\"guard\":\"guru\"}',NULL,'2026-08-23 10:00:54','2026-08-23 10:00:54'),(75,'auth','Login: 3',NULL,NULL,NULL,'App\\Models\\Siswa',3,'{\"guard\":\"siswa\",\"ip_address\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/151.0.0.0 Safari\\/537.36\"}',NULL,'2026-08-23 10:01:40','2026-08-23 10:01:40'),(76,'auth','Login: 6',NULL,NULL,NULL,'App\\Models\\Guru',6,'{\"guard\":\"guru\",\"ip_address\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/151.0.0.0 Safari\\/537.36\"}',NULL,'2026-08-23 10:09:58','2026-08-23 10:09:58'),(77,'default','Keputusan kelulusan program tahfidz dibatalkan','App\\Models\\KelulusanProgram',NULL,2,'App\\Models\\Admin',1,'{\"keputusan\":\"Dibatalkan\",\"causer_type\":\"Admin\",\"causer_id\":1}',NULL,'2026-08-23 10:17:06','2026-08-23 10:17:06'),(78,'default','Keputusan kelulusan program tahfidz dibatalkan','App\\Models\\KelulusanProgram',NULL,1,'App\\Models\\Admin',1,'{\"keputusan\":\"Dibatalkan\",\"causer_type\":\"Admin\",\"causer_id\":1}',NULL,'2026-08-23 10:17:21','2026-08-23 10:17:21'),(79,'default','Kelulusan program tahfidz disetujui','App\\Models\\KelulusanProgram',NULL,2,'App\\Models\\Admin',1,'{\"keputusan\":\"Disetujui\",\"causer_type\":\"Admin\",\"causer_id\":1}',NULL,'2026-08-23 10:38:20','2026-08-23 10:38:20'),(80,'default','Kelulusan program tahfidz disetujui','App\\Models\\KelulusanProgram',NULL,1,'App\\Models\\Admin',1,'{\"keputusan\":\"Disetujui\",\"causer_type\":\"Admin\",\"causer_id\":1}',NULL,'2026-08-23 10:38:59','2026-08-23 10:38:59');
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `guru_id` bigint(20) unsigned DEFAULT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_username_unique` (`email`),
  KEY `admins_guru_id_foreign` (`guru_id`),
  KEY `admins_status_aktif_index` (`status_aktif`),
  CONSTRAINT `admins_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` (`id`, `nama`, `email`, `password`, `guru_id`, `status_aktif`, `created_at`, `updated_at`) VALUES (1,'Muhammad Bayu','admin.tahfidz@albahjah.com','$2y$12$mAEMF144JHjAtzYPaOVk9ehgNtBehTXIKxcfclT7XiRiSPeuNR80e',NULL,1,'2026-08-22 16:26:56','2026-08-23 03:46:18'),(2,'Admin 2','admin2@albahjah.com','$2y$12$Yd..SZXWzbjRkCb01Cp7nOOl3e/7KYyPgHKix6YK4N8Qe6PLGeZ6K',NULL,1,'2026-08-22 16:26:56','2026-08-22 16:26:56'),(3,'Admin Non-Aktif','admin_nonaktif@albahjah.com','$2y$12$2Bm4Wz0weW0CtsPix3ove.MYmpQQWjncVxCzi26tcHNRspiDZLhfq',NULL,0,'2026-08-22 16:26:57','2026-08-22 16:26:57');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
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
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
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
-- Table structure for table `capaian_juzs`
--

DROP TABLE IF EXISTS `capaian_juzs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `capaian_juzs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `progres_hafalan_id` bigint(20) unsigned NOT NULL,
  `juz_id` bigint(20) unsigned NOT NULL,
  `ayat_tercakup` int(11) NOT NULL DEFAULT 0 COMMENT 'Number of unique ayat covered',
  `total_ayat` int(11) NOT NULL COMMENT 'From Referensi Quran (denormalized for performance)',
  `persentase` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT '(ayat_tercakup / total_ayat) * 100',
  `status_tuntas` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'True when persentase = 100%',
  `tanggal_tuntas` date DEFAULT NULL COMMENT 'Date when juz became 100% complete',
  `detail_ayat_tercakup` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Array of [surah_id, ayat_mulai, ayat_selesai]' CHECK (json_valid(`detail_ayat_tercakup`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `capaian_juzs_unique` (`progres_hafalan_id`,`juz_id`),
  KEY `capaian_juzs_juz_id_foreign` (`juz_id`),
  KEY `capaian_juzs_status_tuntas_index` (`status_tuntas`),
  KEY `capaian_juzs_persentase_index` (`persentase`),
  CONSTRAINT `capaian_juzs_juz_id_foreign` FOREIGN KEY (`juz_id`) REFERENCES `juzs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `capaian_juzs_progres_hafalan_id_foreign` FOREIGN KEY (`progres_hafalan_id`) REFERENCES `progres_hafalans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `capaian_juzs`
--

LOCK TABLES `capaian_juzs` WRITE;
/*!40000 ALTER TABLE `capaian_juzs` DISABLE KEYS */;
INSERT INTO `capaian_juzs` (`id`, `progres_hafalan_id`, `juz_id`, `ayat_tercakup`, `total_ayat`, `persentase`, `status_tuntas`, `tanggal_tuntas`, `detail_ayat_tercakup`, `created_at`, `updated_at`) VALUES (31,2,1,0,148,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(32,2,2,0,111,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(33,2,3,0,126,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(34,2,4,0,131,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(35,2,5,0,124,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(36,2,6,0,111,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(37,2,7,0,148,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(38,2,8,0,142,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(39,2,9,0,159,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(40,2,10,0,127,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(41,2,11,0,151,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(42,2,12,0,170,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(43,2,13,0,154,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(44,2,14,0,227,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(45,2,15,0,185,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(46,2,16,0,269,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(47,2,17,0,190,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(48,2,18,0,202,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(49,2,19,0,339,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(50,2,20,0,171,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(51,2,21,0,178,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(52,2,22,0,169,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(53,2,23,0,357,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(54,2,24,0,175,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(55,2,25,0,246,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(56,2,26,0,195,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(57,2,27,0,399,0.00,0,NULL,'[]','2026-08-23 04:18:18','2026-08-23 04:18:18'),(58,2,28,137,137,100.00,1,'2024-09-07','[{\"surah_id\":58,\"ayat_mulai\":1,\"ayat_selesai\":22,\"tanggal\":\"2024-08-29\"},{\"surah_id\":59,\"ayat_mulai\":1,\"ayat_selesai\":24,\"tanggal\":\"2024-08-30\"},{\"surah_id\":60,\"ayat_mulai\":1,\"ayat_selesai\":13,\"tanggal\":\"2024-08-31\"},{\"surah_id\":61,\"ayat_mulai\":1,\"ayat_selesai\":14,\"tanggal\":\"2024-09-02\"},{\"surah_id\":62,\"ayat_mulai\":1,\"ayat_selesai\":11,\"tanggal\":\"2024-09-03\"},{\"surah_id\":63,\"ayat_mulai\":1,\"ayat_selesai\":11,\"tanggal\":\"2024-09-04\"},{\"surah_id\":64,\"ayat_mulai\":1,\"ayat_selesai\":18,\"tanggal\":\"2024-09-05\"},{\"surah_id\":65,\"ayat_mulai\":1,\"ayat_selesai\":12,\"tanggal\":\"2024-09-06\"},{\"surah_id\":66,\"ayat_mulai\":1,\"ayat_selesai\":12,\"tanggal\":\"2024-09-07\"}]','2026-08-23 04:18:18','2026-08-23 08:33:22'),(59,2,29,431,431,100.00,1,'2024-08-28','[{\"surah_id\":67,\"ayat_mulai\":1,\"ayat_selesai\":30,\"tanggal\":\"2024-08-16\"},{\"surah_id\":68,\"ayat_mulai\":1,\"ayat_selesai\":52,\"tanggal\":\"2024-08-17\"},{\"surah_id\":69,\"ayat_mulai\":1,\"ayat_selesai\":52,\"tanggal\":\"2024-08-19\"},{\"surah_id\":70,\"ayat_mulai\":1,\"ayat_selesai\":44,\"tanggal\":\"2024-08-20\"},{\"surah_id\":71,\"ayat_mulai\":1,\"ayat_selesai\":28,\"tanggal\":\"2024-08-21\"},{\"surah_id\":72,\"ayat_mulai\":1,\"ayat_selesai\":28,\"tanggal\":\"2024-08-22\"},{\"surah_id\":73,\"ayat_mulai\":1,\"ayat_selesai\":20,\"tanggal\":\"2024-08-23\"},{\"surah_id\":74,\"ayat_mulai\":1,\"ayat_selesai\":56,\"tanggal\":\"2024-08-24\"},{\"surah_id\":75,\"ayat_mulai\":1,\"ayat_selesai\":40,\"tanggal\":\"2024-08-26\"},{\"surah_id\":76,\"ayat_mulai\":1,\"ayat_selesai\":31,\"tanggal\":\"2024-08-27\"},{\"surah_id\":77,\"ayat_mulai\":1,\"ayat_selesai\":50,\"tanggal\":\"2024-08-28\"}]','2026-08-23 04:18:18','2026-08-23 08:05:12'),(60,2,30,564,564,100.00,1,'2024-08-15','[{\"surah_id\":78,\"ayat_mulai\":1,\"ayat_selesai\":40,\"tanggal\":\"2024-07-01\"},{\"surah_id\":79,\"ayat_mulai\":1,\"ayat_selesai\":46,\"tanggal\":\"2024-07-02\"},{\"surah_id\":80,\"ayat_mulai\":1,\"ayat_selesai\":42,\"tanggal\":\"2024-07-03\"},{\"surah_id\":81,\"ayat_mulai\":1,\"ayat_selesai\":29,\"tanggal\":\"2024-07-04\"},{\"surah_id\":82,\"ayat_mulai\":1,\"ayat_selesai\":19,\"tanggal\":\"2024-07-05\"},{\"surah_id\":83,\"ayat_mulai\":1,\"ayat_selesai\":36,\"tanggal\":\"2024-07-06\"},{\"surah_id\":84,\"ayat_mulai\":1,\"ayat_selesai\":25,\"tanggal\":\"2024-07-08\"},{\"surah_id\":85,\"ayat_mulai\":1,\"ayat_selesai\":22,\"tanggal\":\"2024-07-09\"},{\"surah_id\":86,\"ayat_mulai\":1,\"ayat_selesai\":17,\"tanggal\":\"2024-07-10\"},{\"surah_id\":87,\"ayat_mulai\":1,\"ayat_selesai\":19,\"tanggal\":\"2024-07-11\"},{\"surah_id\":88,\"ayat_mulai\":1,\"ayat_selesai\":26,\"tanggal\":\"2024-07-12\"},{\"surah_id\":89,\"ayat_mulai\":1,\"ayat_selesai\":30,\"tanggal\":\"2024-07-13\"},{\"surah_id\":90,\"ayat_mulai\":1,\"ayat_selesai\":20,\"tanggal\":\"2024-07-15\"},{\"surah_id\":91,\"ayat_mulai\":1,\"ayat_selesai\":15,\"tanggal\":\"2024-07-16\"},{\"surah_id\":92,\"ayat_mulai\":1,\"ayat_selesai\":21,\"tanggal\":\"2024-07-17\"},{\"surah_id\":93,\"ayat_mulai\":1,\"ayat_selesai\":11,\"tanggal\":\"2024-07-18\"},{\"surah_id\":94,\"ayat_mulai\":1,\"ayat_selesai\":8,\"tanggal\":\"2024-07-19\"},{\"surah_id\":95,\"ayat_mulai\":1,\"ayat_selesai\":8,\"tanggal\":\"2024-07-20\"},{\"surah_id\":96,\"ayat_mulai\":1,\"ayat_selesai\":19,\"tanggal\":\"2024-07-22\"},{\"surah_id\":97,\"ayat_mulai\":1,\"ayat_selesai\":5,\"tanggal\":\"2024-07-24\"},{\"surah_id\":98,\"ayat_mulai\":1,\"ayat_selesai\":8,\"tanggal\":\"2024-07-26\"},{\"surah_id\":99,\"ayat_mulai\":1,\"ayat_selesai\":8,\"tanggal\":\"2024-07-27\"},{\"surah_id\":100,\"ayat_mulai\":1,\"ayat_selesai\":11,\"tanggal\":\"2024-07-29\"},{\"surah_id\":101,\"ayat_mulai\":1,\"ayat_selesai\":11,\"tanggal\":\"2024-07-30\"},{\"surah_id\":102,\"ayat_mulai\":1,\"ayat_selesai\":8,\"tanggal\":\"2024-07-31\"},{\"surah_id\":103,\"ayat_mulai\":1,\"ayat_selesai\":3,\"tanggal\":\"2024-08-01\"},{\"surah_id\":104,\"ayat_mulai\":1,\"ayat_selesai\":9,\"tanggal\":\"2024-08-02\"},{\"surah_id\":105,\"ayat_mulai\":1,\"ayat_selesai\":5,\"tanggal\":\"2024-08-03\"},{\"surah_id\":106,\"ayat_mulai\":1,\"ayat_selesai\":4,\"tanggal\":\"2024-08-05\"},{\"surah_id\":107,\"ayat_mulai\":1,\"ayat_selesai\":7,\"tanggal\":\"2024-08-06\"},{\"surah_id\":108,\"ayat_mulai\":1,\"ayat_selesai\":3,\"tanggal\":\"2024-08-07\"},{\"surah_id\":109,\"ayat_mulai\":1,\"ayat_selesai\":6,\"tanggal\":\"2024-08-08\"},{\"surah_id\":110,\"ayat_mulai\":1,\"ayat_selesai\":3,\"tanggal\":\"2024-08-09\"},{\"surah_id\":111,\"ayat_mulai\":1,\"ayat_selesai\":5,\"tanggal\":\"2024-08-10\"},{\"surah_id\":112,\"ayat_mulai\":1,\"ayat_selesai\":4,\"tanggal\":\"2024-08-12\"},{\"surah_id\":113,\"ayat_mulai\":1,\"ayat_selesai\":5,\"tanggal\":\"2024-08-14\"},{\"surah_id\":114,\"ayat_mulai\":1,\"ayat_selesai\":6,\"tanggal\":\"2024-08-15\"}]','2026-08-23 04:18:18','2026-08-23 05:40:31'),(61,3,1,0,148,0.00,0,NULL,'[]','2026-08-23 04:22:15','2026-08-23 04:22:15'),(62,3,2,0,111,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(63,3,3,0,126,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(64,3,4,0,131,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(65,3,5,0,124,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(66,3,6,0,111,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(67,3,7,0,148,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(68,3,8,0,142,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(69,3,9,0,159,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(70,3,10,0,127,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(71,3,11,0,151,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(72,3,12,0,170,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(73,3,13,0,154,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(74,3,14,0,227,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(75,3,15,0,185,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(76,3,16,0,269,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(77,3,17,0,190,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(78,3,18,0,202,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(79,3,19,0,339,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(80,3,20,0,171,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(81,3,21,0,178,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(82,3,22,0,169,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(83,3,23,0,357,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(84,3,24,0,175,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(85,3,25,0,246,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(86,3,26,0,195,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(87,3,27,0,399,0.00,0,NULL,'[]','2026-08-23 04:22:16','2026-08-23 04:22:16'),(88,3,28,137,137,100.00,1,'2024-09-10','[{\"surah_id\":58,\"ayat_mulai\":1,\"ayat_selesai\":22,\"tanggal\":\"2024-08-31\"},{\"surah_id\":59,\"ayat_mulai\":1,\"ayat_selesai\":24,\"tanggal\":\"2024-09-02\"},{\"surah_id\":60,\"ayat_mulai\":1,\"ayat_selesai\":13,\"tanggal\":\"2024-09-03\"},{\"surah_id\":61,\"ayat_mulai\":1,\"ayat_selesai\":14,\"tanggal\":\"2024-09-04\"},{\"surah_id\":62,\"ayat_mulai\":1,\"ayat_selesai\":11,\"tanggal\":\"2024-09-05\"},{\"surah_id\":63,\"ayat_mulai\":1,\"ayat_selesai\":11,\"tanggal\":\"2024-09-06\"},{\"surah_id\":64,\"ayat_mulai\":1,\"ayat_selesai\":18,\"tanggal\":\"2024-09-07\"},{\"surah_id\":65,\"ayat_mulai\":1,\"ayat_selesai\":12,\"tanggal\":\"2024-09-09\"},{\"surah_id\":66,\"ayat_mulai\":1,\"ayat_selesai\":12,\"tanggal\":\"2024-09-10\"}]','2026-08-23 04:22:16','2026-08-23 08:35:57'),(89,3,29,431,431,100.00,1,'2024-08-30','[{\"surah_id\":67,\"ayat_mulai\":1,\"ayat_selesai\":30,\"tanggal\":\"2024-08-19\"},{\"surah_id\":68,\"ayat_mulai\":1,\"ayat_selesai\":52,\"tanggal\":\"2024-08-20\"},{\"surah_id\":69,\"ayat_mulai\":1,\"ayat_selesai\":52,\"tanggal\":\"2024-08-21\"},{\"surah_id\":70,\"ayat_mulai\":1,\"ayat_selesai\":44,\"tanggal\":\"2024-08-22\"},{\"surah_id\":71,\"ayat_mulai\":1,\"ayat_selesai\":28,\"tanggal\":\"2024-08-23\"},{\"surah_id\":72,\"ayat_mulai\":1,\"ayat_selesai\":28,\"tanggal\":\"2024-08-24\"},{\"surah_id\":73,\"ayat_mulai\":1,\"ayat_selesai\":20,\"tanggal\":\"2024-08-26\"},{\"surah_id\":74,\"ayat_mulai\":1,\"ayat_selesai\":56,\"tanggal\":\"2024-08-27\"},{\"surah_id\":75,\"ayat_mulai\":1,\"ayat_selesai\":40,\"tanggal\":\"2024-08-28\"},{\"surah_id\":76,\"ayat_mulai\":1,\"ayat_selesai\":31,\"tanggal\":\"2024-08-29\"},{\"surah_id\":77,\"ayat_mulai\":1,\"ayat_selesai\":50,\"tanggal\":\"2024-08-30\"}]','2026-08-23 04:22:16','2026-08-23 08:10:37'),(90,3,30,564,564,100.00,1,'2024-08-17','[{\"surah_id\":78,\"ayat_mulai\":1,\"ayat_selesai\":40,\"tanggal\":\"2024-07-01\"},{\"surah_id\":79,\"ayat_mulai\":1,\"ayat_selesai\":46,\"tanggal\":\"2024-07-02\"},{\"surah_id\":80,\"ayat_mulai\":1,\"ayat_selesai\":42,\"tanggal\":\"2024-07-03\"},{\"surah_id\":81,\"ayat_mulai\":1,\"ayat_selesai\":29,\"tanggal\":\"2024-07-04\"},{\"surah_id\":82,\"ayat_mulai\":1,\"ayat_selesai\":19,\"tanggal\":\"2024-07-05\"},{\"surah_id\":83,\"ayat_mulai\":1,\"ayat_selesai\":36,\"tanggal\":\"2024-07-06\"},{\"surah_id\":84,\"ayat_mulai\":1,\"ayat_selesai\":25,\"tanggal\":\"2024-07-08\"},{\"surah_id\":85,\"ayat_mulai\":1,\"ayat_selesai\":22,\"tanggal\":\"2024-07-09\"},{\"surah_id\":86,\"ayat_mulai\":1,\"ayat_selesai\":17,\"tanggal\":\"2024-07-10\"},{\"surah_id\":87,\"ayat_mulai\":1,\"ayat_selesai\":19,\"tanggal\":\"2024-07-13\"},{\"surah_id\":88,\"ayat_mulai\":1,\"ayat_selesai\":26,\"tanggal\":\"2024-07-15\"},{\"surah_id\":89,\"ayat_mulai\":1,\"ayat_selesai\":30,\"tanggal\":\"2024-07-16\"},{\"surah_id\":90,\"ayat_mulai\":1,\"ayat_selesai\":20,\"tanggal\":\"2024-07-18\"},{\"surah_id\":91,\"ayat_mulai\":1,\"ayat_selesai\":15,\"tanggal\":\"2024-07-19\"},{\"surah_id\":92,\"ayat_mulai\":1,\"ayat_selesai\":21,\"tanggal\":\"2024-07-20\"},{\"surah_id\":93,\"ayat_mulai\":1,\"ayat_selesai\":11,\"tanggal\":\"2024-07-22\"},{\"surah_id\":94,\"ayat_mulai\":1,\"ayat_selesai\":8,\"tanggal\":\"2024-07-23\"},{\"surah_id\":95,\"ayat_mulai\":1,\"ayat_selesai\":8,\"tanggal\":\"2024-07-26\"},{\"surah_id\":96,\"ayat_mulai\":1,\"ayat_selesai\":19,\"tanggal\":\"2024-07-27\"},{\"surah_id\":97,\"ayat_mulai\":1,\"ayat_selesai\":5,\"tanggal\":\"2024-07-29\"},{\"surah_id\":98,\"ayat_mulai\":1,\"ayat_selesai\":8,\"tanggal\":\"2024-07-30\"},{\"surah_id\":99,\"ayat_mulai\":1,\"ayat_selesai\":8,\"tanggal\":\"2024-07-31\"},{\"surah_id\":100,\"ayat_mulai\":1,\"ayat_selesai\":11,\"tanggal\":\"2024-08-01\"},{\"surah_id\":101,\"ayat_mulai\":1,\"ayat_selesai\":11,\"tanggal\":\"2024-08-02\"},{\"surah_id\":102,\"ayat_mulai\":1,\"ayat_selesai\":8,\"tanggal\":\"2024-08-03\"},{\"surah_id\":103,\"ayat_mulai\":1,\"ayat_selesai\":3,\"tanggal\":\"2024-08-05\"},{\"surah_id\":104,\"ayat_mulai\":1,\"ayat_selesai\":9,\"tanggal\":\"2024-08-06\"},{\"surah_id\":105,\"ayat_mulai\":1,\"ayat_selesai\":5,\"tanggal\":\"2024-08-07\"},{\"surah_id\":106,\"ayat_mulai\":1,\"ayat_selesai\":4,\"tanggal\":\"2024-08-08\"},{\"surah_id\":107,\"ayat_mulai\":1,\"ayat_selesai\":7,\"tanggal\":\"2024-08-09\"},{\"surah_id\":108,\"ayat_mulai\":1,\"ayat_selesai\":3,\"tanggal\":\"2024-08-10\"},{\"surah_id\":109,\"ayat_mulai\":1,\"ayat_selesai\":6,\"tanggal\":\"2024-08-12\"},{\"surah_id\":110,\"ayat_mulai\":1,\"ayat_selesai\":3,\"tanggal\":\"2024-08-13\"},{\"surah_id\":111,\"ayat_mulai\":1,\"ayat_selesai\":5,\"tanggal\":\"2024-08-14\"},{\"surah_id\":112,\"ayat_mulai\":1,\"ayat_selesai\":4,\"tanggal\":\"2024-08-15\"},{\"surah_id\":113,\"ayat_mulai\":1,\"ayat_selesai\":5,\"tanggal\":\"2024-08-16\"},{\"surah_id\":114,\"ayat_mulai\":1,\"ayat_selesai\":6,\"tanggal\":\"2024-08-17\"}]','2026-08-23 04:22:16','2026-08-23 05:40:04');
/*!40000 ALTER TABLE `capaian_juzs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
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
-- Table structure for table `feature_toggles`
--

DROP TABLE IF EXISTS `feature_toggles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feature_toggles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feature_toggles_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feature_toggles`
--

LOCK TABLES `feature_toggles` WRITE;
/*!40000 ALTER TABLE `feature_toggles` DISABLE KEYS */;
INSERT INTO `feature_toggles` (`id`, `key`, `name`, `description`, `is_enabled`, `created_at`, `updated_at`) VALUES (1,'tes_penempatan','Tes Penempatan Siswa','Mengaktifkan fitur tes penempatan bagi siswa baru atau pindahan.',0,'2026-08-22 16:26:47','2026-08-23 03:45:58');
/*!40000 ALTER TABLE `feature_toggles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guru_tugas_tambahans`
--

DROP TABLE IF EXISTS `guru_tugas_tambahans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guru_tugas_tambahans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `guru_id` bigint(20) unsigned NOT NULL,
  `jenis_tugas` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guru_tugas_tambahans_guru_id_jenis_tugas_unique` (`guru_id`,`jenis_tugas`),
  CONSTRAINT `guru_tugas_tambahans_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guru_tugas_tambahans`
--

LOCK TABLES `guru_tugas_tambahans` WRITE;
/*!40000 ALTER TABLE `guru_tugas_tambahans` DISABLE KEYS */;
/*!40000 ALTER TABLE `guru_tugas_tambahans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gurus`
--

DROP TABLE IF EXISTS `gurus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gurus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `niy` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `password_text` varchar(255) DEFAULT NULL,
  `keterangan_akun` varchar(255) DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `tempat_lahir` varchar(255) NOT NULL,
  `jabatan` enum('Guru Pembimbing Tahfidz','Guru Mapel','Kepala Sekolah','Tendik') NOT NULL,
  `no_telepon` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tanggal_mulai_kerja` date DEFAULT NULL COMMENT 'Tanggal guru mulai bekerja di sekolah',
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `akun_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `jenis_keluar` enum('Pensiun','Mutasi','Berhenti','Meninggal') DEFAULT NULL COMMENT 'Jenis keluarnya guru dari sekolah',
  `tanggal_keluar` date DEFAULT NULL COMMENT 'Tanggal guru keluar dari sekolah',
  `keterangan_keluar` text DEFAULT NULL COMMENT 'Catatan keluar',
  `diproses_oleh` bigint(20) unsigned DEFAULT NULL COMMENT 'Admin yang memproses registrasi keluar',
  `reset_oleh` bigint(20) unsigned DEFAULT NULL,
  `reset_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gurus_niy_unique` (`niy`),
  KEY `gurus_reset_oleh_foreign` (`reset_oleh`),
  KEY `gurus_status_aktif_index` (`status_aktif`),
  KEY `gurus_jabatan_index` (`jabatan`),
  KEY `gurus_diproses_oleh_foreign` (`diproses_oleh`),
  KEY `gurus_jenis_keluar_index` (`jenis_keluar`),
  CONSTRAINT `gurus_diproses_oleh_foreign` FOREIGN KEY (`diproses_oleh`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `gurus_reset_oleh_foreign` FOREIGN KEY (`reset_oleh`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gurus`
--

LOCK TABLES `gurus` WRITE;
/*!40000 ALTER TABLE `gurus` DISABLE KEYS */;
INSERT INTO `gurus` (`id`, `niy`, `nama`, `tanggal_lahir`, `password`, `password_text`, `keterangan_akun`, `jenis_kelamin`, `tempat_lahir`, `jabatan`, `no_telepon`, `email`, `tanggal_mulai_kerja`, `status_aktif`, `akun_aktif`, `jenis_keluar`, `tanggal_keluar`, `keterangan_keluar`, `diproses_oleh`, `reset_oleh`, `reset_pada`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,'0909786876786876','Jaelani,S.Pd','1960-08-01','$2y$12$pipMTCxxr6/gSqt.GDdB6u62D50AZb4PBbTuuBnca/J1at7UzmUja',NULL,'Password sudah diganti','Laki-laki','Cianjur','Guru Pembimbing Tahfidz','0980980980980','jaelani@gmail.com','2024-01-01',1,1,'Mutasi','2026-08-23','gjhgjhgjhgjhghgjh',1,1,'2026-08-23 03:45:14','2026-08-23 02:00:52','2026-08-23 07:29:31',NULL),(2,'8970987097907097','Muhammad Yasa,S.Pd','1972-10-11','$2y$12$CKchONFrezjkCataxybjFOxL8x26YQWVIu4uZjNcDR.uQfo8Mlm6y',',R9!mmnA','Password belum diganti','Laki-laki','Cianjur','Guru Pembimbing Tahfidz','0989079878979','muhyasa@gmail.com','2024-01-01',1,1,NULL,NULL,NULL,NULL,1,'2026-08-23 03:45:16','2026-08-23 02:01:53','2026-08-23 03:45:16',NULL),(3,'8698789787987987','Sulaeman Farouk,S.Pd','1977-12-01','$2y$12$5FkOjkYedYtakm1PB1x2wORT2rq80TEnQINq83xkyTsUnhqcsJi5y','OZ2[Wx9S','Password belum diganti','Laki-laki','Cianjur','Guru Pembimbing Tahfidz','9789709789798','sulaemanfarouk@gmail.com','2024-01-01',1,1,NULL,NULL,NULL,NULL,1,'2026-08-23 03:45:16','2026-08-23 02:03:21','2026-08-23 03:45:16',NULL),(4,'6986986986986986','Muhammad Firdaus,M.Pd','1980-06-13','$2y$12$cQW0UPaLbLqsodq9SdR8D.dy.SN0scVBVNykS/2hmAtTx6ln7hmbi','YT3wkv<#','Password belum diganti','Laki-laki','Cianjur','Guru Pembimbing Tahfidz','0832454646431','muhammadfirdaus@gmail.com','2024-01-01',1,1,NULL,NULL,NULL,NULL,1,'2026-08-23 03:45:15','2026-08-23 02:09:41','2026-08-23 03:45:15',NULL),(5,'3453543797645343','Tabib Rokhani,S.Pd.I','1982-05-05','$2y$12$Co1KykkdQ4MDtB0Ha7UnxOyrFG1C0oh.H5SvXPG31wyuikdpXYika','}\\Biz2jc','Password belum diganti','Laki-laki','Cianjur','Guru Pembimbing Tahfidz','0897079878769','mangtabib@gmail.com','2024-01-01',1,1,NULL,NULL,NULL,NULL,1,'2026-08-23 03:45:16','2026-08-23 02:14:00','2026-08-23 03:45:16',NULL),(6,'9889696986896765','Abdul Rohman,S.Pd','1986-04-04','$2y$12$.q9GMt5waaaZpAAKhsuPA.XOp8TQoyVgMobFsbmNbhvrlzKPUwwUu',NULL,'Password sudah diganti','Laki-laki','Cianjur','Guru Pembimbing Tahfidz','0970978786786','abdulrohman@gmail.com','2024-01-01',1,1,NULL,NULL,NULL,NULL,1,'2026-08-23 03:45:14','2026-08-23 02:15:38','2026-08-23 04:27:35',NULL),(7,'9070787898798786','May May Hidayat,S.Pd','1969-07-28','$2y$12$/0Nfiehg/j5Rt0JEbsX63uqJKaZK/sZxaFPcRkL24RcCsYGj4Vcey',NULL,'Password sudah diganti','Laki-laki','Cianjur','Guru Pembimbing Tahfidz','0980989786756','mayhidayat@gmail.com','2024-01-01',1,1,NULL,NULL,NULL,NULL,1,'2026-08-23 08:18:48','2026-08-23 02:21:15','2026-08-23 08:19:41',NULL);
/*!40000 ALTER TABLE `gurus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hasil_tes_penempatans`
--

DROP TABLE IF EXISTS `hasil_tes_penempatans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hasil_tes_penempatans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tes_penempatan_id` bigint(20) unsigned NOT NULL,
  `juz_id` bigint(20) unsigned NOT NULL,
  `surah_id` bigint(20) unsigned NOT NULL,
  `ayat_mulai` smallint(5) unsigned NOT NULL,
  `ayat_selesai` smallint(5) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hasil_tes_penempatans_juz_id_foreign` (`juz_id`),
  KEY `hasil_tes_penempatans_surah_id_foreign` (`surah_id`),
  KEY `hasil_tes_penempatans_tes_penempatan_id_index` (`tes_penempatan_id`),
  CONSTRAINT `hasil_tes_penempatans_juz_id_foreign` FOREIGN KEY (`juz_id`) REFERENCES `juzs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hasil_tes_penempatans_surah_id_foreign` FOREIGN KEY (`surah_id`) REFERENCES `surahs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hasil_tes_penempatans_tes_penempatan_id_foreign` FOREIGN KEY (`tes_penempatan_id`) REFERENCES `tes_penempatans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hasil_tes_penempatans`
--

LOCK TABLES `hasil_tes_penempatans` WRITE;
/*!40000 ALTER TABLE `hasil_tes_penempatans` DISABLE KEYS */;
/*!40000 ALTER TABLE `hasil_tes_penempatans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES (1,'default','{\"uuid\":\"9942132f-3f2b-41d6-84dc-8306983ce583\",\"displayName\":\"App\\\\Notifications\\\\UsulanKenaikanTingkatDecisionNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\Guru\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:59:\\\"App\\\\Notifications\\\\UsulanKenaikanTingkatDecisionNotification\\\":3:{s:6:\\\"usulan\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\UsulanKenaikanTingkat\\\";s:2:\\\"id\\\";i:14;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"siswa\\\";i:1;s:12:\\\"guruPengusul\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:8:\\\"decision\\\";s:8:\\\"approved\\\";s:2:\\\"id\\\";s:36:\\\"499d361b-67d2-4f71-9a1f-4eb417a7c03b\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1787469297,\"delay\":null}',0,NULL,1787469297,1787469297),(2,'default','{\"uuid\":\"83670de1-a248-4f3c-b4d6-6ad326804165\",\"displayName\":\"App\\\\Notifications\\\\UsulanKenaikanTingkatDecisionNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\Guru\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:59:\\\"App\\\\Notifications\\\\UsulanKenaikanTingkatDecisionNotification\\\":3:{s:6:\\\"usulan\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\UsulanKenaikanTingkat\\\";s:2:\\\"id\\\";i:14;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"siswa\\\";i:1;s:12:\\\"guruPengusul\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:8:\\\"decision\\\";s:9:\\\"cancelled\\\";s:2:\\\"id\\\";s:36:\\\"759649a6-fa9d-4872-a93a-f6e300630c7b\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1787469322,\"delay\":null}',0,NULL,1787469322,1787469322),(3,'default','{\"uuid\":\"402df036-7e6d-4b02-8216-a1c9dff64aeb\",\"displayName\":\"App\\\\Notifications\\\\UsulanKenaikanTingkatDecisionNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\Guru\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:59:\\\"App\\\\Notifications\\\\UsulanKenaikanTingkatDecisionNotification\\\":3:{s:6:\\\"usulan\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\UsulanKenaikanTingkat\\\";s:2:\\\"id\\\";i:14;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"siswa\\\";i:1;s:12:\\\"guruPengusul\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:8:\\\"decision\\\";s:8:\\\"approved\\\";s:2:\\\"id\\\";s:36:\\\"3c3091e6-607e-428d-8590-80ed45e917a8\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1787469353,\"delay\":null}',0,NULL,1787469353,1787469353),(4,'default','{\"uuid\":\"df5fa846-f90d-401a-b777-4c6c2ffb9258\",\"displayName\":\"App\\\\Notifications\\\\KenaikanTingkatPlacementNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Siswa\\\";s:2:\\\"id\\\";a:1:{i:0;i:2;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:54:\\\"App\\\\Notifications\\\\KenaikanTingkatPlacementNotification\\\":2:{s:6:\\\"usulan\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\UsulanKenaikanTingkat\\\";s:2:\\\"id\\\";i:14;s:9:\\\"relations\\\";a:3:{i:0;s:5:\\\"siswa\\\";i:1;s:14:\\\"kelompokTujuan\\\";i:2;s:29:\\\"kelompokTujuan.guruPembimbing\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"1ecb24b7-6109-4498-b2c5-50226f973ffe\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1787469941,\"delay\":null}',0,NULL,1787469941,1787469941),(5,'default','{\"uuid\":\"434a9901-0561-44a5-81bb-d4d9f1580d40\",\"displayName\":\"App\\\\Notifications\\\\UsulanKenaikanTingkatDecisionNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\Guru\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:59:\\\"App\\\\Notifications\\\\UsulanKenaikanTingkatDecisionNotification\\\":3:{s:6:\\\"usulan\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\UsulanKenaikanTingkat\\\";s:2:\\\"id\\\";i:15;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"siswa\\\";i:1;s:12:\\\"guruPengusul\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:8:\\\"decision\\\";s:8:\\\"approved\\\";s:2:\\\"id\\\";s:36:\\\"fe81078a-2fb4-46d6-96cc-7632e7e1e575\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1787470013,\"delay\":null}',0,NULL,1787470013,1787470013),(6,'default','{\"uuid\":\"453e493b-14cb-4515-b4cc-dcffb0720951\",\"displayName\":\"App\\\\Notifications\\\\KenaikanTingkatPlacementNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Siswa\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:54:\\\"App\\\\Notifications\\\\KenaikanTingkatPlacementNotification\\\":2:{s:6:\\\"usulan\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\UsulanKenaikanTingkat\\\";s:2:\\\"id\\\";i:15;s:9:\\\"relations\\\";a:3:{i:0;s:5:\\\"siswa\\\";i:1;s:14:\\\"kelompokTujuan\\\";i:2;s:29:\\\"kelompokTujuan.guruPembimbing\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"20082f18-e991-41c1-b4f0-7d379f91928f\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1787470046,\"delay\":null}',0,NULL,1787470046,1787470046),(7,'default','{\"uuid\":\"afd51e52-7e0b-4bba-b97c-3f9ba2f7731f\",\"displayName\":\"App\\\\Notifications\\\\UsulanKenaikanTingkatDecisionNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\Guru\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:59:\\\"App\\\\Notifications\\\\UsulanKenaikanTingkatDecisionNotification\\\":3:{s:6:\\\"usulan\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\UsulanKenaikanTingkat\\\";s:2:\\\"id\\\";i:17;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"siswa\\\";i:1;s:12:\\\"guruPengusul\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:8:\\\"decision\\\";s:8:\\\"approved\\\";s:2:\\\"id\\\";s:36:\\\"f9ed0099-adf6-49f2-865f-b3fb454a3d91\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1787472798,\"delay\":null}',0,NULL,1787472798,1787472798),(8,'default','{\"uuid\":\"12027e9a-e739-4837-814b-ab41ad2b27f5\",\"displayName\":\"App\\\\Notifications\\\\KenaikanTingkatPlacementNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Siswa\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:54:\\\"App\\\\Notifications\\\\KenaikanTingkatPlacementNotification\\\":2:{s:6:\\\"usulan\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\UsulanKenaikanTingkat\\\";s:2:\\\"id\\\";i:17;s:9:\\\"relations\\\";a:3:{i:0;s:5:\\\"siswa\\\";i:1;s:14:\\\"kelompokTujuan\\\";i:2;s:29:\\\"kelompokTujuan.guruPembimbing\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"001caa74-bbb9-4804-b822-328c074b3db5\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1787472820,\"delay\":null}',0,NULL,1787472820,1787472820),(9,'default','{\"uuid\":\"d5fbdc5b-418b-430c-a045-9da14f6035a3\",\"displayName\":\"App\\\\Notifications\\\\UsulanKenaikanTingkatDecisionNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\Guru\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:59:\\\"App\\\\Notifications\\\\UsulanKenaikanTingkatDecisionNotification\\\":3:{s:6:\\\"usulan\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\UsulanKenaikanTingkat\\\";s:2:\\\"id\\\";i:16;s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"siswa\\\";i:1;s:12:\\\"guruPengusul\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:8:\\\"decision\\\";s:8:\\\"approved\\\";s:2:\\\"id\\\";s:36:\\\"0f956b2f-af11-4fdc-9bba-ef822ec32533\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1787472846,\"delay\":null}',0,NULL,1787472846,1787472846),(10,'default','{\"uuid\":\"6a1c6abb-ba19-49d2-b22e-bc929e11674a\",\"displayName\":\"App\\\\Notifications\\\\KenaikanTingkatPlacementNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Siswa\\\";s:2:\\\"id\\\";a:1:{i:0;i:2;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:54:\\\"App\\\\Notifications\\\\KenaikanTingkatPlacementNotification\\\":2:{s:6:\\\"usulan\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:32:\\\"App\\\\Models\\\\UsulanKenaikanTingkat\\\";s:2:\\\"id\\\";i:16;s:9:\\\"relations\\\";a:3:{i:0;s:5:\\\"siswa\\\";i:1;s:14:\\\"kelompokTujuan\\\";i:2;s:29:\\\"kelompokTujuan.guruPembimbing\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"1399abdf-9786-47c3-b3b4-57fb483ff670\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1787472870,\"delay\":null}',0,NULL,1787472870,1787472870);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `juz_surah`
--

DROP TABLE IF EXISTS `juz_surah`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `juz_surah` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `juz_id` bigint(20) unsigned NOT NULL,
  `surah_id` bigint(20) unsigned NOT NULL,
  `ayat_mulai` smallint(5) unsigned NOT NULL COMMENT 'Ayat awal surah dalam juz ini',
  `ayat_selesai` smallint(5) unsigned NOT NULL COMMENT 'Ayat akhir surah dalam juz ini',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_juz_id` (`juz_id`),
  KEY `idx_surah_id` (`surah_id`),
  CONSTRAINT `juz_surah_juz_id_foreign` FOREIGN KEY (`juz_id`) REFERENCES `juzs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `juz_surah_surah_id_foreign` FOREIGN KEY (`surah_id`) REFERENCES `surahs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `juz_surah`
--

LOCK TABLES `juz_surah` WRITE;
/*!40000 ALTER TABLE `juz_surah` DISABLE KEYS */;
INSERT INTO `juz_surah` (`id`, `juz_id`, `surah_id`, `ayat_mulai`, `ayat_selesai`, `created_at`, `updated_at`) VALUES (1,1,1,1,7,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(2,1,2,1,141,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(3,2,2,142,252,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(4,3,2,253,286,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(5,3,3,1,92,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(6,4,3,93,200,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(7,4,4,1,23,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(8,5,4,24,147,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(9,6,4,148,176,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(10,6,5,1,81,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(11,7,5,82,120,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(12,7,6,1,110,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(13,8,6,111,165,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(14,8,7,1,87,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(15,9,7,88,206,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(16,9,8,1,40,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(17,10,8,41,75,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(18,10,9,1,92,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(19,11,9,93,129,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(20,11,10,1,109,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(21,11,11,1,5,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(22,12,11,6,123,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(23,12,12,1,52,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(24,13,12,53,111,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(25,13,13,1,43,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(26,13,14,1,52,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(27,14,15,1,99,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(28,14,16,1,128,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(29,15,17,1,111,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(30,15,18,1,74,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(31,16,18,75,110,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(32,16,19,1,98,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(33,16,20,1,135,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(34,17,21,1,112,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(35,17,22,1,78,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(36,18,23,1,118,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(37,18,24,1,64,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(38,18,25,1,20,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(39,19,25,21,77,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(40,19,26,1,227,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(41,19,27,1,55,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(42,20,27,56,93,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(43,20,28,1,88,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(44,20,29,1,45,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(45,21,29,46,69,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(46,21,30,1,60,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(47,21,31,1,34,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(48,21,32,1,30,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(49,21,33,1,30,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(50,22,33,31,73,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(51,22,34,1,54,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(52,22,35,1,45,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(53,22,36,1,27,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(54,23,36,28,83,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(55,23,37,1,182,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(56,23,38,1,88,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(57,23,39,1,31,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(58,24,39,32,75,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(59,24,40,1,85,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(60,24,41,1,46,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(61,25,41,47,54,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(62,25,42,1,53,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(63,25,43,1,89,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(64,25,44,1,59,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(65,25,45,1,37,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(66,26,46,1,35,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(67,26,47,1,38,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(68,26,48,1,29,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(69,26,49,1,18,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(70,26,50,1,45,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(71,26,51,1,30,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(72,27,51,31,60,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(73,27,52,1,49,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(74,27,53,1,62,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(75,27,54,1,55,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(76,27,55,1,78,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(77,27,56,1,96,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(78,27,57,1,29,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(79,28,58,1,22,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(80,28,59,1,24,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(81,28,60,1,13,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(82,28,61,1,14,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(83,28,62,1,11,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(84,28,63,1,11,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(85,28,64,1,18,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(86,28,65,1,12,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(87,28,66,1,12,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(88,29,67,1,30,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(89,29,68,1,52,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(90,29,69,1,52,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(91,29,70,1,44,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(92,29,71,1,28,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(93,29,72,1,28,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(94,29,73,1,20,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(95,29,74,1,56,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(96,29,75,1,40,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(97,29,76,1,31,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(98,29,77,1,50,'2026-08-22 16:27:00','2026-08-22 16:27:00'),(99,30,78,1,40,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(100,30,79,1,46,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(101,30,80,1,42,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(102,30,81,1,29,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(103,30,82,1,19,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(104,30,83,1,36,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(105,30,84,1,25,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(106,30,85,1,22,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(107,30,86,1,17,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(108,30,87,1,19,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(109,30,88,1,26,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(110,30,89,1,30,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(111,30,90,1,20,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(112,30,91,1,15,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(113,30,92,1,21,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(114,30,93,1,11,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(115,30,94,1,8,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(116,30,95,1,8,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(117,30,96,1,19,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(118,30,97,1,5,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(119,30,98,1,8,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(120,30,99,1,8,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(121,30,100,1,11,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(122,30,101,1,11,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(123,30,102,1,8,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(124,30,103,1,3,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(125,30,104,1,9,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(126,30,105,1,5,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(127,30,106,1,4,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(128,30,107,1,7,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(129,30,108,1,3,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(130,30,109,1,6,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(131,30,110,1,3,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(132,30,111,1,5,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(133,30,112,1,4,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(134,30,113,1,5,'2026-08-22 16:27:01','2026-08-22 16:27:01'),(135,30,114,1,6,'2026-08-22 16:27:01','2026-08-22 16:27:01');
/*!40000 ALTER TABLE `juz_surah` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `juzs`
--

DROP TABLE IF EXISTS `juzs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `juzs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `juz_ke` tinyint(3) unsigned NOT NULL COMMENT 'Nomor juz 1-30',
  `urutan_hafalan` tinyint(3) unsigned NOT NULL COMMENT 'Urutan hafalan yang dapat dikustomisasi',
  `jumlah_ayat` smallint(5) unsigned NOT NULL COMMENT 'Total ayat dalam juz ini',
  `awal_surah` tinyint(3) unsigned DEFAULT NULL COMMENT 'Surah awal juz',
  `awal_ayat` tinyint(3) unsigned DEFAULT NULL COMMENT 'Ayat awal juz',
  `akhir_surah` tinyint(3) unsigned DEFAULT NULL COMMENT 'Surah akhir juz',
  `akhir_ayat` tinyint(3) unsigned DEFAULT NULL COMMENT 'Ayat akhir juz',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `juzs_juz_ke_unique` (`juz_ke`),
  KEY `idx_juz_ke` (`juz_ke`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `juzs`
--

LOCK TABLES `juzs` WRITE;
/*!40000 ALTER TABLE `juzs` DISABLE KEYS */;
INSERT INTO `juzs` (`id`, `juz_ke`, `urutan_hafalan`, `jumlah_ayat`, `awal_surah`, `awal_ayat`, `akhir_surah`, `akhir_ayat`, `created_at`, `updated_at`) VALUES (1,1,1,148,1,1,2,141,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(2,2,2,111,2,142,2,252,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(3,3,3,126,2,253,3,92,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(4,4,4,131,3,93,4,23,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(5,5,5,124,4,24,4,147,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(6,6,6,111,4,148,5,82,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(7,7,7,148,5,83,6,110,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(8,8,8,142,6,111,7,87,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(9,9,9,159,7,88,8,40,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(10,10,10,127,8,41,9,92,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(11,11,11,151,9,93,11,5,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(12,12,12,170,11,6,12,52,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(13,13,13,154,12,53,14,52,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(14,14,14,227,15,1,16,128,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(15,15,15,185,17,1,18,74,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(16,16,16,269,18,75,20,135,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(17,17,17,190,21,1,22,78,'2026-08-22 16:26:57','2026-08-22 16:26:57'),(18,18,18,202,23,1,25,20,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(19,19,19,339,25,21,27,55,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(20,20,20,171,27,56,29,45,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(21,21,21,178,29,46,33,30,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(22,22,22,169,33,31,36,27,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(23,23,23,357,36,28,39,31,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(24,24,24,175,39,32,41,46,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(25,25,25,246,41,47,45,37,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(26,26,26,195,46,1,51,30,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(27,27,27,399,51,31,57,29,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(28,28,28,137,58,1,66,12,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(29,29,29,431,67,1,77,50,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(30,30,30,564,78,1,114,6,'2026-08-22 16:26:58','2026-08-22 16:26:58');
/*!40000 ALTER TABLE `juzs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kelas`
--

DROP TABLE IF EXISTS `kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kelas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tingkat` enum('7','8','9') NOT NULL,
  `nama_kelas` varchar(10) NOT NULL,
  `ruang_kelas` varchar(255) DEFAULT NULL,
  `jumlah_siswa` int(11) DEFAULT NULL COMMENT 'Kapasitas maksimal siswa',
  `nama_wali_kelas` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kelas_tingkat_index` (`tingkat`),
  KEY `kelas_nama_kelas_index` (`nama_kelas`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kelas`
--

LOCK TABLES `kelas` WRITE;
/*!40000 ALTER TABLE `kelas` DISABLE KEYS */;
INSERT INTO `kelas` (`id`, `tingkat`, `nama_kelas`, `ruang_kelas`, `jumlah_siswa`, `nama_wali_kelas`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,'7','7A','Ruang Kelas 7A',NULL,'Ali Sadikin','2026-08-22 17:59:51','2026-08-23 01:54:33',NULL),(2,'7','7B','Ruang Kelas 7B',NULL,'Mawardi,S.Pd','2026-08-23 01:55:01','2026-08-23 01:55:01',NULL),(3,'8','8A','Ruang Kelas 8A',NULL,'Ido Sutarman','2026-08-23 01:55:32','2026-08-23 01:55:32',NULL),(4,'8','8B','Ruang Kelas 8B',NULL,'Ano Kardiono,S.Pd','2026-08-23 01:56:00','2026-08-23 01:56:00',NULL),(5,'9','9A','Ruang Kelas 9A',NULL,'Apriansyah Rusdawan,S.Pd','2026-08-23 01:56:40','2026-08-23 01:56:40',NULL),(6,'9','9B','Ruang Kelsa 9B',NULL,'Sularman,S.Pd','2026-08-23 01:57:18','2026-08-23 01:57:18',NULL);
/*!40000 ALTER TABLE `kelas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kelompoks`
--

DROP TABLE IF EXISTS `kelompoks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kelompoks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `jenis` enum('Banin','Banat') NOT NULL,
  `tingkat` enum('Dasar','Menengah','Atas') NOT NULL,
  `guru_pembimbing_id` bigint(20) unsigned NOT NULL,
  `tanggal_penempatan_guru` date DEFAULT NULL,
  `tahun_ajaran_id` bigint(20) unsigned DEFAULT NULL,
  `kapasitas_min` int(11) DEFAULT NULL,
  `kapasitas_maks` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kelompoks_nama_tahun_ajaran_id_unique` (`nama`,`tahun_ajaran_id`),
  KEY `idx_kelompoks_tahun_ajaran` (`tahun_ajaran_id`),
  KEY `idx_kelompoks_guru` (`guru_pembimbing_id`),
  CONSTRAINT `kelompoks_guru_pembimbing_id_foreign` FOREIGN KEY (`guru_pembimbing_id`) REFERENCES `gurus` (`id`),
  CONSTRAINT `kelompoks_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajarans` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kelompoks`
--

LOCK TABLES `kelompoks` WRITE;
/*!40000 ALTER TABLE `kelompoks` DISABLE KEYS */;
INSERT INTO `kelompoks` (`id`, `nama`, `jenis`, `tingkat`, `guru_pembimbing_id`, `tanggal_penempatan_guru`, `tahun_ajaran_id`, `kapasitas_min`, `kapasitas_maks`, `created_at`, `updated_at`) VALUES (1,'Kelompok A','Banin','Dasar',6,'2024-07-01',1,NULL,NULL,'2026-08-23 02:28:04','2026-08-23 03:31:55'),(2,'Kelompok B','Banin','Menengah',1,'2024-07-01',1,NULL,NULL,'2026-08-23 02:40:28','2026-08-23 03:35:38'),(3,'Kelompok C','Banin','Atas',7,'2024-07-01',1,NULL,NULL,'2026-08-23 02:41:02','2026-08-23 03:33:16');
/*!40000 ALTER TABLE `kelompoks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kelulusan_programs`
--

DROP TABLE IF EXISTS `kelulusan_programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kelulusan_programs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint(20) unsigned NOT NULL,
  `guru_pengusul_id` bigint(20) unsigned DEFAULT NULL,
  `kelompok_id` bigint(20) unsigned NOT NULL,
  `tanggal_tuntas` date NOT NULL COMMENT 'Tanggal selesai target hafalan',
  `tanggal_usul` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_kelulusan` enum('Diajukan','Disetujui','Ditolak','Dibatalkan') NOT NULL DEFAULT 'Diajukan',
  `catatan` text DEFAULT NULL,
  `disetujui_oleh` bigint(20) unsigned DEFAULT NULL,
  `tanggal_keputusan` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kelulusan_programs_guru_pengusul_id_foreign` (`guru_pengusul_id`),
  KEY `kelulusan_programs_kelompok_id_foreign` (`kelompok_id`),
  KEY `kelulusan_programs_disetujui_oleh_foreign` (`disetujui_oleh`),
  KEY `kelulusan_programs_siswa_id_index` (`siswa_id`),
  KEY `kelulusan_programs_status_kelulusan_index` (`status_kelulusan`),
  KEY `kelulusan_programs_tanggal_usul_index` (`tanggal_usul`),
  CONSTRAINT `kelulusan_programs_disetujui_oleh_foreign` FOREIGN KEY (`disetujui_oleh`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `kelulusan_programs_guru_pengusul_id_foreign` FOREIGN KEY (`guru_pengusul_id`) REFERENCES `gurus` (`id`) ON DELETE SET NULL,
  CONSTRAINT `kelulusan_programs_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompoks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kelulusan_programs_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kelulusan_programs`
--

LOCK TABLES `kelulusan_programs` WRITE;
/*!40000 ALTER TABLE `kelulusan_programs` DISABLE KEYS */;
INSERT INTO `kelulusan_programs` (`id`, `siswa_id`, `guru_pengusul_id`, `kelompok_id`, `tanggal_tuntas`, `tanggal_usul`, `status_kelulusan`, `catatan`, `disetujui_oleh`, `tanggal_keputusan`, `created_at`, `updated_at`) VALUES (1,2,7,3,'2024-09-08','2024-09-06 17:00:00','Disetujui','',1,'2026-08-23 10:38:59','2026-08-23 08:36:12','2026-08-23 10:38:59'),(2,3,7,3,'2024-09-11','2024-09-09 17:00:00','Disetujui','',1,'2026-08-23 10:38:20','2026-08-23 08:36:17','2026-08-23 10:38:20');
/*!40000 ALTER TABLE `kelulusan_programs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_01_10_000001_create_tahun_ajarans_table',1),(5,'2024_01_10_000002_create_semesters_table',1),(6,'2024_01_10_000009_create_referensi_qurans_table',1),(7,'2026_07_25_071448_create_admins_table',1),(8,'2026_07_25_071506_create_gurus_table',1),(9,'2026_07_25_071507_create_guru_tugas_tambahans_table',1),(10,'2026_07_25_071508_create_kelompoks_table',1),(11,'2026_07_25_071509_create_riwayat_guru_pembimbing_kelompoks_table',1),(12,'2026_07_25_071510_add_foreign_key_admins_guru_id',1),(13,'2026_07_25_071524_create_siswas_table',1),(14,'2026_07_25_071525_create_orang_tua_walis_table',1),(15,'2026_07_25_071526_add_kelompok_id_to_siswas_table',1),(16,'2026_07_25_071530_add_status_aktif_indexes',1),(17,'2026_07_25_071600_create_absensis_table',1),(18,'2026_07_30_031542_create_juzs_table',1),(19,'2026_07_30_032524_create_surahs_table',1),(20,'2026_07_30_032555_create_juz_surah_table',1),(21,'2026_07_30_033132_drop_setorans_table_temporarily',1),(22,'2026_07_30_033209_create_setorans_table_with_normalized_structure',1),(23,'2026_07_30_121151_create_progres_hafalans_table',1),(24,'2026_07_30_122157_create_capaian_juzs_table',1),(25,'2026_07_30_122949_create_transkrip_capaians_table',1),(26,'2026_07_31_061620_create_parameter_konfigurasis_table',1),(27,'2026_07_31_222058_create_usulan_kenaikan_tingkats_table',1),(28,'2026_08_01_044545_create_riwayat_kelompoks_table',1),(29,'2026_08_01_052109_create_notifications_table',1),(30,'2026_08_01_150921_create_activity_log_table',1),(31,'2026_08_01_150922_add_event_column_to_activity_log_table',1),(32,'2026_08_01_150923_add_batch_uuid_column_to_activity_log_table',1),(33,'2026_08_02_000001_add_laporan_performance_indexes',1),(34,'2026_08_02_100001_create_tes_penempatans_table',1),(35,'2026_08_02_100002_create_hasil_tes_penempatans_table',1),(36,'2026_08_02_100003_add_perlu_tes_penempatan_to_siswas_table',1),(37,'2026_08_02_100004_create_feature_toggles_table',1),(38,'2026_08_02_100005_create_riwayat_guru_pembimbings_table',1),(39,'2026_08_02_100006_add_indexes_to_activity_log_table',1),(40,'2026_08_02_164013_add_exit_tracking_fields_to_siswas_table',1),(41,'2026_08_03_000001_create_sekolahs_table',1),(42,'2026_08_03_042049_create_kelas_table',1),(43,'2026_08_03_042615_add_kelas_id_to_siswas_table',1),(44,'2026_08_03_083525_modify_kelas_table_structure',1),(45,'2026_08_03_223712_make_sekolah_fields_nullable',1),(46,'2026_08_04_002323_remove_foto_nik_agama_from_siswas_table',1),(47,'2026_08_04_002543_remove_foto_nik_agama_from_siswas_table',1),(48,'2026_08_04_002616_remove_nik_from_orang_tua_walis_table',1),(49,'2026_08_04_012547_remove_kewarganegaraan_from_siswas_and_contact_from_orang_tua_walis_table',1),(50,'2026_08_04_021750_remove_fields_from_gurus_table',1),(51,'2026_08_04_025752_simplify_guru_siswa_and_orang_tua_structure',1),(52,'2026_08_04_045206_add_ruang_kelas_and_jumlah_siswa_to_kelas_table',1),(53,'2026_08_04_053107_remove_status_masuk_fields_from_siswas_table',1),(54,'2026_08_04_085820_add_status_masuk_fields_back_to_siswas_table',1),(55,'2026_08_05_083416_update_kelompoks_table_remove_capacity_and_fix_tahun_ajaran',1),(56,'2026_08_06_100000_remove_pendidikan_from_gurus_table',1),(57,'2026_08_07_000000_add_punya_hafalan_sebelumnya_to_siswas_table',1),(58,'2026_08_07_000001_add_exit_tracking_fields_to_gurus_table',1),(59,'2026_08_07_161237_add_tanggal_mulai_kerja_to_gurus_table',1),(60,'2026_08_08_071700_add_password_columns_to_gurus_and_siswas_table',1),(61,'2026_08_08_111404_update_kelas_nama_format',1),(62,'2026_08_08_112609_update_kelas_nama_format',1),(63,'2026_08_08_150000_add_akun_aktif_and_keterangan_akun_to_gurus_siswas',1),(64,'2026_08_09_020000_add_waktu_sesi_to_absensis_table',1),(65,'2026_08_09_030000_add_target_hafalan_per_tingkat_to_parameter_konfigurasis',1),(66,'2026_08_09_155100_add_libur_to_status_kehadiran_in_absensis_table',1),(67,'2026_08_10_000001_rename_terdaftar_to_belum_ditempatkan_in_siswas',1),(68,'2026_08_11_000001_update_admins_table',1),(69,'2026_08_11_100000_add_guard_to_sessions_table',1),(70,'2026_08_11_214114_refactor_progres_hafalans_remove_tahun_ajaran',1),(71,'2026_08_11_214115_refactor_transkrip_capaians_remove_tahun_ajaran',1),(72,'2026_08_11_214116_add_status_akademik_to_siswas',1),(73,'2026_08_12_080948_make_kelompok_ke_id_nullable_in_riwayat_kelompoks_table',1),(74,'2026_08_12_121446_update_jenis_setoran_enum_to_ulang',1),(75,'2026_08_13_180557_create_kelulusan_programs_table',1),(76,'2026_08_15_015723_add_tahun_ajaran_and_tanggal_penempatan_to_siswas_table',1),(77,'2026_08_21_040157_create_rollover_audit_logs_table',1),(78,'2026_08_22_000000_add_performance_indexes',1),(79,'2026_08_22_033551_cleanup_unused_fields_from_tables',1),(80,'2026_08_22_034904_drop_jadwal_fields_from_kelompoks_table',1),(81,'2026_08_23_101853_add_tanggal_penempatan_guru_to_kelompoks_table',2),(82,'2026_08_23_172424_add_dibatalkan_to_status_kelulusan_enum',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parameter_konfigurasis`
--

DROP TABLE IF EXISTS `parameter_konfigurasis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parameter_konfigurasis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `target_kelulusan` int(11) NOT NULL DEFAULT 5,
  `batas_tingkat` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`batas_tingkat`)),
  `kapasitas_kelompok_min` int(11) NOT NULL DEFAULT 6,
  `kapasitas_kelompok_max` int(11) NOT NULL DEFAULT 12,
  `formula_predikat` varchar(255) NOT NULL DEFAULT 'min',
  `ambang_alfa_berturut` int(11) NOT NULL DEFAULT 3,
  `hari_efektif_tahfidz` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hari_efektif_tahfidz`)),
  `target_hafalan_per_sesi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`target_hafalan_per_sesi`)),
  `komponen_penilaian` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`komponen_penilaian`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parameter_konfigurasis`
--

LOCK TABLES `parameter_konfigurasis` WRITE;
/*!40000 ALTER TABLE `parameter_konfigurasis` DISABLE KEYS */;
INSERT INTO `parameter_konfigurasis` (`id`, `target_kelulusan`, `batas_tingkat`, `kapasitas_kelompok_min`, `kapasitas_kelompok_max`, `formula_predikat`, `ambang_alfa_berturut`, `hari_efektif_tahfidz`, `target_hafalan_per_sesi`, `komponen_penilaian`, `created_at`, `updated_at`) VALUES (1,3,'{\"Dasar\":[30],\"Menengah\":[29],\"Atas\":[28]}',5,12,'min',3,'[\"Senin\",\"Selasa\",\"Rabu\",\"Kamis\",\"Jumat\"]',NULL,'[\"skor_tajwid\",\"skor_kelancaran\",\"skor_makhraj\",\"skor_fashahah\"]','2026-08-22 18:01:15','2026-08-23 02:42:09');
/*!40000 ALTER TABLE `parameter_konfigurasis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
-- Table structure for table `progres_hafalans`
--

DROP TABLE IF EXISTS `progres_hafalans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `progres_hafalans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint(20) unsigned NOT NULL,
  `baseline_juzs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of juz IDs from placement test' CHECK (json_valid(`baseline_juzs`)),
  `baseline_surahs` text DEFAULT NULL COMMENT 'Descriptive text of covered surahs',
  `catatan_kualitas_baseline` text DEFAULT NULL,
  `rekomendasi_tingkat` varchar(255) DEFAULT NULL,
  `total_juz_tuntas` int(11) NOT NULL DEFAULT 0 COMMENT 'Count of completed juz',
  `juz_berjalan` int(11) DEFAULT NULL COMMENT 'Current incomplete juz',
  `juz_berjalan_percentage` decimal(5,2) DEFAULT NULL COMMENT 'Percentage 0.00-100.00',
  `progress_toward_target` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT '% toward target_kelulusan',
  `target_juz` int(11) NOT NULL DEFAULT 5 COMMENT 'Target minimal juz untuk dinyatakan lulus program tahfidz',
  `status_tahfidz` enum('Belum Lulus','Lulus') NOT NULL DEFAULT 'Belum Lulus' COMMENT 'Status kelulusan program tahfidz (independen dari akademik)',
  `tanggal_lulus_tahfidz` date DEFAULT NULL COMMENT 'Tanggal pertama kali siswa mencapai target minimal juz',
  `baseline_modified_by` bigint(20) unsigned DEFAULT NULL,
  `baseline_modified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `progres_hafalans_siswa_unique` (`siswa_id`),
  KEY `progres_hafalans_baseline_modified_by_foreign` (`baseline_modified_by`),
  KEY `progres_hafalans_total_juz_tuntas_index` (`total_juz_tuntas`),
  KEY `progres_hafalans_progress_toward_target_index` (`progress_toward_target`),
  KEY `progres_hafalans_siswa_tahun_index` (`siswa_id`),
  KEY `idx_progres_juz_tuntas` (`total_juz_tuntas`),
  KEY `idx_progres_target` (`target_juz`),
  CONSTRAINT `progres_hafalans_baseline_modified_by_foreign` FOREIGN KEY (`baseline_modified_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `progres_hafalans_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `progres_hafalans`
--

LOCK TABLES `progres_hafalans` WRITE;
/*!40000 ALTER TABLE `progres_hafalans` DISABLE KEYS */;
INSERT INTO `progres_hafalans` (`id`, `siswa_id`, `baseline_juzs`, `baseline_surahs`, `catatan_kualitas_baseline`, `rekomendasi_tingkat`, `total_juz_tuntas`, `juz_berjalan`, `juz_berjalan_percentage`, `progress_toward_target`, `target_juz`, `status_tahfidz`, `tanggal_lulus_tahfidz`, `baseline_modified_by`, `baseline_modified_at`, `created_at`, `updated_at`) VALUES (2,2,'[]','Belum ada hafalan (baseline 0 juz)',NULL,NULL,3,NULL,NULL,60.00,5,'Lulus','2024-09-08',NULL,NULL,'2026-08-23 02:51:18','2026-08-23 10:38:59'),(3,3,'[]','Belum ada hafalan (baseline 0 juz)',NULL,NULL,3,NULL,NULL,60.00,5,'Lulus','2024-09-11',NULL,NULL,'2026-08-23 02:54:57','2026-08-23 10:38:20');
/*!40000 ALTER TABLE `progres_hafalans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referensi_qurans`
--

DROP TABLE IF EXISTS `referensi_qurans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `referensi_qurans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `juz_ke` int(11) NOT NULL,
  `urutan_hafalan` int(11) NOT NULL,
  `awal_surah` varchar(255) NOT NULL,
  `awal_ayat` int(11) NOT NULL,
  `akhir_surah` varchar(255) NOT NULL,
  `akhir_ayat` int(11) NOT NULL,
  `jumlah_ayat` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `referensi_qurans_juz_ke_unique` (`juz_ke`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referensi_qurans`
--

LOCK TABLES `referensi_qurans` WRITE;
/*!40000 ALTER TABLE `referensi_qurans` DISABLE KEYS */;
INSERT INTO `referensi_qurans` (`id`, `juz_ke`, `urutan_hafalan`, `awal_surah`, `awal_ayat`, `akhir_surah`, `akhir_ayat`, `jumlah_ayat`) VALUES (1,1,1,'1',1,'2',141,148),(2,2,2,'2',142,'2',252,111),(3,3,3,'2',253,'3',92,126),(4,4,4,'3',93,'4',23,131),(5,5,5,'4',24,'4',147,124),(6,6,6,'4',148,'5',82,111),(7,7,7,'5',83,'6',110,148),(8,8,8,'6',111,'7',87,142),(9,9,9,'7',88,'8',40,159),(10,10,10,'8',41,'9',92,127),(11,11,11,'9',93,'11',5,151),(12,12,12,'11',6,'12',52,170),(13,13,13,'12',53,'14',52,154),(14,14,14,'15',1,'16',128,227),(15,15,15,'17',1,'18',74,185),(16,16,16,'18',75,'20',135,269),(17,17,17,'21',1,'22',78,190),(18,18,18,'23',1,'25',20,202),(19,19,19,'25',21,'27',55,339),(20,20,20,'27',56,'29',45,171),(21,21,21,'29',46,'33',30,178),(22,22,22,'33',31,'36',27,169),(23,23,23,'36',28,'39',31,357),(24,24,24,'39',32,'41',46,175),(25,25,25,'41',47,'45',37,246),(26,26,26,'46',1,'51',30,195),(27,27,27,'51',31,'57',29,399),(28,28,28,'58',1,'66',12,137),(29,29,29,'67',1,'77',50,431),(30,30,30,'78',1,'114',6,564);
/*!40000 ALTER TABLE `referensi_qurans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `riwayat_guru_pembimbing_kelompoks`
--

DROP TABLE IF EXISTS `riwayat_guru_pembimbing_kelompoks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `riwayat_guru_pembimbing_kelompoks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kelompok_id` bigint(20) unsigned NOT NULL,
  `guru_dari_id` bigint(20) unsigned NOT NULL,
  `guru_ke_id` bigint(20) unsigned NOT NULL,
  `tanggal_efektif` date NOT NULL,
  `alasan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `riwayat_guru_pembimbing_kelompoks_kelompok_id_foreign` (`kelompok_id`),
  KEY `riwayat_guru_pembimbing_kelompoks_guru_dari_id_foreign` (`guru_dari_id`),
  KEY `riwayat_guru_pembimbing_kelompoks_guru_ke_id_foreign` (`guru_ke_id`),
  CONSTRAINT `riwayat_guru_pembimbing_kelompoks_guru_dari_id_foreign` FOREIGN KEY (`guru_dari_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `riwayat_guru_pembimbing_kelompoks_guru_ke_id_foreign` FOREIGN KEY (`guru_ke_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `riwayat_guru_pembimbing_kelompoks_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompoks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `riwayat_guru_pembimbing_kelompoks`
--

LOCK TABLES `riwayat_guru_pembimbing_kelompoks` WRITE;
/*!40000 ALTER TABLE `riwayat_guru_pembimbing_kelompoks` DISABLE KEYS */;
/*!40000 ALTER TABLE `riwayat_guru_pembimbing_kelompoks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `riwayat_guru_pembimbings`
--

DROP TABLE IF EXISTS `riwayat_guru_pembimbings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `riwayat_guru_pembimbings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kelompok_id` bigint(20) unsigned NOT NULL,
  `guru_dari_id` bigint(20) unsigned DEFAULT NULL,
  `guru_ke_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal_efektif` date NOT NULL,
  `alasan` text DEFAULT NULL,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `riwayat_guru_pembimbings_guru_dari_id_foreign` (`guru_dari_id`),
  KEY `riwayat_guru_pembimbings_guru_ke_id_foreign` (`guru_ke_id`),
  KEY `riwayat_guru_pembimbings_admin_id_foreign` (`admin_id`),
  KEY `riwayat_guru_pembimbings_kelompok_id_tanggal_efektif_index` (`kelompok_id`,`tanggal_efektif`),
  CONSTRAINT `riwayat_guru_pembimbings_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `riwayat_guru_pembimbings_guru_dari_id_foreign` FOREIGN KEY (`guru_dari_id`) REFERENCES `gurus` (`id`) ON DELETE SET NULL,
  CONSTRAINT `riwayat_guru_pembimbings_guru_ke_id_foreign` FOREIGN KEY (`guru_ke_id`) REFERENCES `gurus` (`id`) ON DELETE SET NULL,
  CONSTRAINT `riwayat_guru_pembimbings_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompoks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `riwayat_guru_pembimbings`
--

LOCK TABLES `riwayat_guru_pembimbings` WRITE;
/*!40000 ALTER TABLE `riwayat_guru_pembimbings` DISABLE KEYS */;
INSERT INTO `riwayat_guru_pembimbings` (`id`, `kelompok_id`, `guru_dari_id`, `guru_ke_id`, `tanggal_efektif`, `alasan`, `admin_id`, `created_at`, `updated_at`) VALUES (1,1,NULL,6,'2026-08-23','Penugasan awal saat kelompok dibentuk.',1,'2026-08-23 02:28:04','2026-08-23 02:28:04'),(2,2,NULL,1,'2026-08-23','Penugasan awal saat kelompok dibentuk.',1,'2026-08-23 02:40:28','2026-08-23 02:40:28'),(3,3,NULL,7,'2026-08-23','Penugasan awal saat kelompok dibentuk.',1,'2026-08-23 02:41:02','2026-08-23 02:41:02');
/*!40000 ALTER TABLE `riwayat_guru_pembimbings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `riwayat_kelompoks`
--

DROP TABLE IF EXISTS `riwayat_kelompoks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `riwayat_kelompoks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint(20) unsigned NOT NULL,
  `kelompok_dari_id` bigint(20) unsigned DEFAULT NULL,
  `kelompok_ke_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal` datetime NOT NULL,
  `alasan` varchar(255) NOT NULL,
  `disetujui_oleh` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `riwayat_kelompoks_disetujui_oleh_foreign` (`disetujui_oleh`),
  KEY `riwayat_kelompoks_siswa_id_index` (`siswa_id`),
  KEY `riwayat_kelompoks_kelompok_dari_id_index` (`kelompok_dari_id`),
  KEY `riwayat_kelompoks_kelompok_ke_id_index` (`kelompok_ke_id`),
  KEY `riwayat_kelompoks_tanggal_index` (`tanggal`),
  CONSTRAINT `riwayat_kelompoks_disetujui_oleh_foreign` FOREIGN KEY (`disetujui_oleh`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `riwayat_kelompoks_kelompok_dari_id_foreign` FOREIGN KEY (`kelompok_dari_id`) REFERENCES `kelompoks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `riwayat_kelompoks_kelompok_ke_id_foreign` FOREIGN KEY (`kelompok_ke_id`) REFERENCES `kelompoks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `riwayat_kelompoks_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `riwayat_kelompoks`
--

LOCK TABLES `riwayat_kelompoks` WRITE;
/*!40000 ALTER TABLE `riwayat_kelompoks` DISABLE KEYS */;
INSERT INTO `riwayat_kelompoks` (`id`, `siswa_id`, `kelompok_dari_id`, `kelompok_ke_id`, `tanggal`, `alasan`, `disetujui_oleh`, `created_at`, `updated_at`) VALUES (1,3,NULL,1,'2024-07-01 00:00:00','Penempatan Bulk',1,'2026-08-23 03:49:16','2026-08-23 03:49:16'),(3,2,NULL,1,'2024-07-01 00:00:00','Penempatan Bulk',1,'2026-08-23 04:00:16','2026-08-23 04:00:16'),(18,2,1,NULL,'2024-08-15 00:00:00','Lulus dari kelompok dan menunggu penempatan ke tingkat lebih tinggi',NULL,'2026-08-23 07:01:32','2026-08-23 07:01:32'),(19,2,NULL,2,'2024-08-16 00:00:00','Kenaikan Tingkat',1,'2026-08-23 07:25:40','2026-08-23 07:25:40'),(20,3,1,NULL,'2024-08-17 00:00:00','Lulus dari kelompok dan menunggu penempatan ke tingkat lebih tinggi',NULL,'2026-08-23 07:26:22','2026-08-23 07:26:22'),(21,3,NULL,2,'2024-08-19 00:00:00','Kenaikan Tingkat',1,'2026-08-23 07:27:26','2026-08-23 07:27:26'),(22,2,2,NULL,'2024-08-28 00:00:00','Lulus dari kelompok dan menunggu penempatan ke tingkat lebih tinggi',NULL,'2026-08-23 08:11:02','2026-08-23 08:11:02'),(23,3,2,NULL,'2024-08-30 00:00:00','Lulus dari kelompok dan menunggu penempatan ke tingkat lebih tinggi',NULL,'2026-08-23 08:11:14','2026-08-23 08:11:14'),(24,3,NULL,3,'2024-08-31 00:00:00','Kenaikan Tingkat',1,'2026-08-23 08:13:40','2026-08-23 08:13:40'),(25,2,NULL,3,'2024-08-29 00:00:00','Kenaikan Tingkat',1,'2026-08-23 08:14:30','2026-08-23 08:14:30'),(26,2,3,NULL,'2024-09-07 00:00:00','LULUS dari Program Tahfidz - Menyelesaikan seluruh target hafalan tingkat ATAS',NULL,'2026-08-23 08:36:12','2026-08-23 08:36:12'),(27,3,3,NULL,'2024-09-10 00:00:00','LULUS dari Program Tahfidz - Menyelesaikan seluruh target hafalan tingkat ATAS',NULL,'2026-08-23 08:36:17','2026-08-23 08:36:17');
/*!40000 ALTER TABLE `riwayat_kelompoks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rollover_audit_logs`
--

DROP TABLE IF EXISTS `rollover_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rollover_audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `initiated_by` bigint(20) unsigned NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `source_tahun_ajaran_id` bigint(20) unsigned NOT NULL,
  `target_tahun_ajaran_id` bigint(20) unsigned NOT NULL,
  `kelas_7_to_8_count` int(11) NOT NULL DEFAULT 0,
  `kelas_8_to_9_count` int(11) NOT NULL DEFAULT 0,
  `graduated_count` int(11) NOT NULL DEFAULT 0,
  `kelompok_cleared_count` int(11) NOT NULL DEFAULT 0,
  `status` enum('in_progress','completed','failed','rolled_back') NOT NULL DEFAULT 'in_progress',
  `error_message` text DEFAULT NULL,
  `error_trace` text DEFAULT NULL,
  `rolled_back_by` bigint(20) unsigned DEFAULT NULL,
  `rolled_back_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rollover_audit_logs_initiated_by_foreign` (`initiated_by`),
  KEY `rollover_audit_logs_target_tahun_ajaran_id_foreign` (`target_tahun_ajaran_id`),
  KEY `rollover_audit_logs_rolled_back_by_foreign` (`rolled_back_by`),
  KEY `rollover_audit_logs_status_index` (`status`),
  KEY `rollover_audit_logs_started_at_index` (`started_at`),
  KEY `idx_rollover_tahun_ajaran` (`source_tahun_ajaran_id`,`target_tahun_ajaran_id`),
  CONSTRAINT `rollover_audit_logs_initiated_by_foreign` FOREIGN KEY (`initiated_by`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rollover_audit_logs_rolled_back_by_foreign` FOREIGN KEY (`rolled_back_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rollover_audit_logs_source_tahun_ajaran_id_foreign` FOREIGN KEY (`source_tahun_ajaran_id`) REFERENCES `tahun_ajarans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rollover_audit_logs_target_tahun_ajaran_id_foreign` FOREIGN KEY (`target_tahun_ajaran_id`) REFERENCES `tahun_ajarans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rollover_audit_logs`
--

LOCK TABLES `rollover_audit_logs` WRITE;
/*!40000 ALTER TABLE `rollover_audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `rollover_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sekolahs`
--

DROP TABLE IF EXISTS `sekolahs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sekolahs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_sekolah` varchar(255) NOT NULL,
  `npsn` varchar(8) NOT NULL,
  `status_sekolah` enum('Negeri','Swasta') NOT NULL,
  `akreditasi` varchar(255) NOT NULL,
  `naungan` enum('Kemenag','Kemendikbud','Lainnya') DEFAULT NULL,
  `no_sk_pendirian` varchar(255) DEFAULT NULL,
  `tanggal_sk_pendirian` date DEFAULT NULL,
  `no_sk_operasional` varchar(255) DEFAULT NULL,
  `tanggal_sk_operasional` date DEFAULT NULL,
  `nama_kepala_sekolah` varchar(255) DEFAULT NULL,
  `nip_kepala_sekolah` varchar(255) DEFAULT NULL,
  `no_telepon` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `alamat_jalan` varchar(255) NOT NULL,
  `alamat_dusun` varchar(255) DEFAULT NULL,
  `alamat_rt` varchar(3) DEFAULT NULL,
  `alamat_rw` varchar(3) DEFAULT NULL,
  `alamat_desa_kelurahan` varchar(255) NOT NULL,
  `alamat_kecamatan` varchar(255) NOT NULL,
  `alamat_kabupaten_kota` varchar(255) NOT NULL,
  `alamat_provinsi` varchar(255) NOT NULL,
  `alamat_kode_pos` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sekolahs`
--

LOCK TABLES `sekolahs` WRITE;
/*!40000 ALTER TABLE `sekolahs` DISABLE KEYS */;
INSERT INTO `sekolahs` (`id`, `nama_sekolah`, `npsn`, `status_sekolah`, `akreditasi`, `naungan`, `no_sk_pendirian`, `tanggal_sk_pendirian`, `no_sk_operasional`, `tanggal_sk_operasional`, `nama_kepala_sekolah`, `nip_kepala_sekolah`, `no_telepon`, `email`, `website`, `alamat_jalan`, `alamat_dusun`, `alamat_rt`, `alamat_rw`, `alamat_desa_kelurahan`, `alamat_kecamatan`, `alamat_kabupaten_kota`, `alamat_provinsi`, `alamat_kode_pos`, `created_at`, `updated_at`, `logo`) VALUES (1,'SMPIQu Al-Bahjah Cianjur','20257290','Swasta','B',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'081224203479','smpiqu.cianjur@albahjah.or.id','https://smpiqualbahjahcjr.sch.id','Jl. Raya Puncak KM7','Cirumput','001','005','Cijedil','Cugenang','Cianjur','Jawa Barat','43252','2026-08-23 01:53:59','2026-08-23 03:45:56','branding/L8HPyHO6DNYrVADllwS1pztXgqtEFRvxHexCBKH7.png');
/*!40000 ALTER TABLE `sekolahs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `semesters`
--

DROP TABLE IF EXISTS `semesters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `semesters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tahun_ajaran_id` bigint(20) unsigned NOT NULL,
  `jenis` enum('Ganjil','Genap') NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `semesters_tahun_ajaran_id_jenis_unique` (`tahun_ajaran_id`,`jenis`),
  CONSTRAINT `semesters_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajarans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `semesters`
--

LOCK TABLES `semesters` WRITE;
/*!40000 ALTER TABLE `semesters` DISABLE KEYS */;
INSERT INTO `semesters` (`id`, `tahun_ajaran_id`, `jenis`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `updated_at`) VALUES (1,1,'Ganjil','2024-07-01','2024-12-31',1,'2026-08-22 17:27:04','2026-08-22 17:44:53'),(2,1,'Genap','2025-01-01','2025-06-30',0,'2026-08-22 17:27:04','2026-08-22 17:44:53'),(3,2,'Ganjil','2025-07-01','2025-12-31',0,'2026-08-22 17:27:18','2026-08-22 17:44:53'),(4,2,'Genap','2026-01-01','2026-06-30',0,'2026-08-22 17:27:18','2026-08-22 17:44:53'),(5,3,'Ganjil','2026-07-01','2026-12-31',0,'2026-08-22 17:27:27','2026-08-22 17:44:53'),(6,3,'Genap','2027-01-01','2027-06-30',0,'2026-08-22 17:27:27','2026-08-22 17:44:53');
/*!40000 ALTER TABLE `semesters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `guard` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_guard_index` (`guard`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `setorans`
--

DROP TABLE IF EXISTS `setorans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `setorans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `absensi_id` bigint(20) unsigned NOT NULL,
  `jenis_setoran` enum('Hafalan Baru','Ulang') NOT NULL,
  `juz_id` bigint(20) unsigned NOT NULL,
  `surah_id` bigint(20) unsigned NOT NULL,
  `ayat_mulai` smallint(5) unsigned NOT NULL,
  `ayat_selesai` smallint(5) unsigned NOT NULL,
  `skor_tajwid` enum('Lancar','Kurang Lancar','Tidak Lancar') NOT NULL,
  `skor_kelancaran` enum('Lancar','Kurang Lancar','Tidak Lancar') NOT NULL,
  `skor_makhraj` enum('Lancar','Kurang Lancar','Tidak Lancar') NOT NULL,
  `skor_fashahah` enum('Lancar','Kurang Lancar','Tidak Lancar') NOT NULL,
  `predikat` enum('Lancar','Kurang Lancar','Tidak Lancar') NOT NULL,
  `keputusan` enum('Lanjut','Ulang') NOT NULL,
  `catatan` text DEFAULT NULL,
  `saran_ayat_mulai` smallint(5) unsigned DEFAULT NULL,
  `saran_ayat_selesai` smallint(5) unsigned DEFAULT NULL,
  `saran_surah_id` bigint(20) unsigned DEFAULT NULL,
  `saran_juz_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `setorans_saran_surah_id_foreign` (`saran_surah_id`),
  KEY `setorans_saran_juz_id_foreign` (`saran_juz_id`),
  KEY `idx_absensi` (`absensi_id`),
  KEY `idx_jenis_setoran` (`jenis_setoran`),
  KEY `idx_predikat` (`predikat`),
  KEY `idx_keputusan` (`keputusan`),
  KEY `idx_juz` (`juz_id`),
  KEY `setorans_absensi_predikat_index` (`absensi_id`,`predikat`),
  KEY `idx_setorans_absensi_keputusan` (`absensi_id`,`keputusan`),
  KEY `idx_setorans_juz` (`juz_id`),
  KEY `idx_setorans_surah` (`surah_id`),
  CONSTRAINT `setorans_absensi_id_foreign` FOREIGN KEY (`absensi_id`) REFERENCES `absensis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `setorans_juz_id_foreign` FOREIGN KEY (`juz_id`) REFERENCES `juzs` (`id`),
  CONSTRAINT `setorans_saran_juz_id_foreign` FOREIGN KEY (`saran_juz_id`) REFERENCES `juzs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `setorans_saran_surah_id_foreign` FOREIGN KEY (`saran_surah_id`) REFERENCES `surahs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `setorans_surah_id_foreign` FOREIGN KEY (`surah_id`) REFERENCES `surahs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=173 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `setorans`
--

LOCK TABLES `setorans` WRITE;
/*!40000 ALTER TABLE `setorans` DISABLE KEYS */;
INSERT INTO `setorans` (`id`, `absensi_id`, `jenis_setoran`, `juz_id`, `surah_id`, `ayat_mulai`, `ayat_selesai`, `skor_tajwid`, `skor_kelancaran`, `skor_makhraj`, `skor_fashahah`, `predikat`, `keputusan`, `catatan`, `saran_ayat_mulai`, `saran_ayat_selesai`, `saran_surah_id`, `saran_juz_id`, `created_at`, `updated_at`) VALUES (2,1,'Hafalan Baru',30,78,1,40,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,79,30,'2026-08-23 04:22:15','2026-08-23 04:22:15'),(3,2,'Hafalan Baru',30,78,1,40,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,79,30,'2026-08-23 04:22:15','2026-08-23 04:22:15'),(12,3,'Hafalan Baru',30,79,1,46,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,80,30,'2026-08-23 04:39:08','2026-08-23 04:39:08'),(13,11,'Hafalan Baru',30,79,1,46,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,80,30,'2026-08-23 04:39:08','2026-08-23 04:39:08'),(14,4,'Hafalan Baru',30,80,1,42,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,81,30,'2026-08-23 04:39:35','2026-08-23 04:39:35'),(15,12,'Hafalan Baru',30,80,1,42,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,81,30,'2026-08-23 04:39:36','2026-08-23 04:39:36'),(16,5,'Hafalan Baru',30,81,1,29,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,82,30,'2026-08-23 04:39:54','2026-08-23 04:39:54'),(17,13,'Hafalan Baru',30,81,1,29,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,82,30,'2026-08-23 04:39:54','2026-08-23 04:39:54'),(18,6,'Hafalan Baru',30,82,1,19,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,83,30,'2026-08-23 04:40:17','2026-08-23 04:40:17'),(19,14,'Hafalan Baru',30,82,1,19,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,83,30,'2026-08-23 04:40:18','2026-08-23 04:40:18'),(20,7,'Hafalan Baru',30,83,1,36,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,84,30,'2026-08-23 04:42:47','2026-08-23 04:42:47'),(21,15,'Hafalan Baru',30,83,1,36,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,84,30,'2026-08-23 04:42:47','2026-08-23 04:42:47'),(22,8,'Hafalan Baru',30,84,1,25,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,85,30,'2026-08-23 04:44:02','2026-08-23 04:44:02'),(23,16,'Hafalan Baru',30,84,1,25,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,85,30,'2026-08-23 04:44:02','2026-08-23 04:44:02'),(24,9,'Hafalan Baru',30,85,1,22,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,86,30,'2026-08-23 04:45:38','2026-08-23 04:45:38'),(25,17,'Hafalan Baru',30,85,1,22,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,86,30,'2026-08-23 04:45:39','2026-08-23 04:45:39'),(26,10,'Hafalan Baru',30,86,1,17,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,87,30,'2026-08-23 04:46:56','2026-08-23 04:46:56'),(27,18,'Hafalan Baru',30,86,1,17,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,87,30,'2026-08-23 04:46:57','2026-08-23 04:46:57'),(38,19,'Hafalan Baru',30,87,1,19,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,88,30,'2026-08-23 04:53:58','2026-08-23 04:53:58'),(39,20,'Hafalan Baru',30,88,1,26,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,89,30,'2026-08-23 04:54:13','2026-08-23 04:54:13'),(40,21,'Hafalan Baru',30,89,1,30,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,90,30,'2026-08-23 04:54:36','2026-08-23 04:54:36'),(41,31,'Hafalan Baru',30,87,1,19,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,88,30,'2026-08-23 04:54:36','2026-08-23 04:54:36'),(42,22,'Hafalan Baru',30,90,1,20,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,91,30,'2026-08-23 04:54:58','2026-08-23 04:54:58'),(43,32,'Hafalan Baru',30,88,1,26,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,89,30,'2026-08-23 04:54:59','2026-08-23 04:54:59'),(44,23,'Hafalan Baru',30,91,1,15,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,92,30,'2026-08-23 04:55:17','2026-08-23 04:55:17'),(45,33,'Hafalan Baru',30,89,1,30,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,90,30,'2026-08-23 04:55:18','2026-08-23 04:55:18'),(46,24,'Hafalan Baru',30,92,1,21,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,93,30,'2026-08-23 04:55:34','2026-08-23 04:55:34'),(47,25,'Hafalan Baru',30,93,1,11,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,94,30,'2026-08-23 04:56:11','2026-08-23 04:56:11'),(48,36,'Hafalan Baru',30,90,1,20,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,91,30,'2026-08-23 04:56:12','2026-08-23 04:56:12'),(49,26,'Hafalan Baru',30,94,1,8,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,95,30,'2026-08-23 04:56:28','2026-08-23 04:56:28'),(50,37,'Hafalan Baru',30,91,1,15,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,92,30,'2026-08-23 04:56:29','2026-08-23 04:56:29'),(51,27,'Hafalan Baru',30,95,1,8,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,96,30,'2026-08-23 04:56:57','2026-08-23 04:56:57'),(52,38,'Hafalan Baru',30,92,1,21,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,93,30,'2026-08-23 04:56:58','2026-08-23 04:56:58'),(53,28,'Hafalan Baru',30,96,1,19,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,97,30,'2026-08-23 04:57:18','2026-08-23 04:57:18'),(54,39,'Hafalan Baru',30,93,1,11,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,94,30,'2026-08-23 04:57:18','2026-08-23 04:57:18'),(55,40,'Hafalan Baru',30,94,1,8,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,95,30,'2026-08-23 04:57:37','2026-08-23 04:57:37'),(57,41,'Hafalan Baru',30,95,1,8,'Lancar','Kurang Lancar','Lancar','Lancar','Kurang Lancar','Ulang',NULL,1,8,95,30,'2026-08-23 04:58:26','2026-08-23 04:58:26'),(58,42,'Hafalan Baru',30,97,1,5,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,98,30,'2026-08-23 04:58:26','2026-08-23 04:58:26'),(62,44,'Hafalan Baru',30,98,1,8,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,99,30,'2026-08-23 05:04:08','2026-08-23 05:04:08'),(63,46,'Ulang',30,95,1,8,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,96,30,'2026-08-23 05:04:08','2026-08-23 05:04:08'),(65,48,'Hafalan Baru',30,99,1,8,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,100,30,'2026-08-23 05:16:12','2026-08-23 05:16:12'),(66,49,'Hafalan Baru',30,96,1,19,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,97,30,'2026-08-23 05:16:12','2026-08-23 05:16:12'),(70,50,'Hafalan Baru',30,100,1,11,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,101,30,'2026-08-23 05:17:09','2026-08-23 05:17:09'),(71,53,'Hafalan Baru',30,97,1,5,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,98,30,'2026-08-23 05:17:09','2026-08-23 05:17:09'),(72,51,'Hafalan Baru',30,101,1,11,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,102,30,'2026-08-23 05:17:28','2026-08-23 05:17:28'),(73,54,'Hafalan Baru',30,98,1,8,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,99,30,'2026-08-23 05:17:28','2026-08-23 05:17:28'),(74,52,'Hafalan Baru',30,102,1,8,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,103,30,'2026-08-23 05:18:12','2026-08-23 05:18:12'),(75,55,'Hafalan Baru',30,99,1,8,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,100,30,'2026-08-23 05:18:12','2026-08-23 05:18:12'),(78,91,'Hafalan Baru',30,103,1,3,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,104,30,'2026-08-23 05:25:37','2026-08-23 05:25:37'),(79,92,'Hafalan Baru',30,100,1,11,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,101,30,'2026-08-23 05:25:37','2026-08-23 05:25:37'),(81,93,'Hafalan Baru',30,104,1,9,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,105,30,'2026-08-23 05:30:02','2026-08-23 05:30:02'),(82,94,'Hafalan Baru',30,101,1,11,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,102,30,'2026-08-23 05:30:02','2026-08-23 05:30:02'),(84,95,'Hafalan Baru',30,105,1,5,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,106,30,'2026-08-23 05:30:28','2026-08-23 05:30:28'),(85,96,'Hafalan Baru',30,102,1,8,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,103,30,'2026-08-23 05:30:28','2026-08-23 05:30:28'),(87,97,'Hafalan Baru',30,106,1,4,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,107,30,'2026-08-23 05:31:04','2026-08-23 05:31:04'),(88,98,'Hafalan Baru',30,103,1,3,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,104,30,'2026-08-23 05:31:04','2026-08-23 05:31:04'),(90,99,'Hafalan Baru',30,107,1,7,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,108,30,'2026-08-23 05:31:31','2026-08-23 05:31:31'),(91,100,'Hafalan Baru',30,104,1,9,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,105,30,'2026-08-23 05:31:31','2026-08-23 05:31:31'),(93,101,'Hafalan Baru',30,108,1,3,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,109,30,'2026-08-23 05:34:44','2026-08-23 05:34:44'),(94,102,'Hafalan Baru',30,105,1,5,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,106,30,'2026-08-23 05:34:45','2026-08-23 05:34:45'),(96,103,'Hafalan Baru',30,109,1,6,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,110,30,'2026-08-23 05:37:25','2026-08-23 05:37:25'),(97,104,'Hafalan Baru',30,106,1,4,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,107,30,'2026-08-23 05:37:26','2026-08-23 05:37:26'),(99,105,'Hafalan Baru',30,110,1,3,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,111,30,'2026-08-23 05:37:51','2026-08-23 05:37:51'),(100,106,'Hafalan Baru',30,107,1,7,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,108,30,'2026-08-23 05:37:51','2026-08-23 05:37:51'),(102,107,'Hafalan Baru',30,111,1,5,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,112,30,'2026-08-23 05:38:18','2026-08-23 05:38:18'),(103,108,'Hafalan Baru',30,108,1,3,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,109,30,'2026-08-23 05:38:18','2026-08-23 05:38:18'),(105,109,'Hafalan Baru',30,112,1,4,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,113,30,'2026-08-23 05:38:50','2026-08-23 05:38:50'),(106,111,'Hafalan Baru',30,109,1,6,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,110,30,'2026-08-23 05:38:50','2026-08-23 05:38:50'),(107,112,'Hafalan Baru',30,110,1,3,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,111,30,'2026-08-23 05:39:02','2026-08-23 05:39:02'),(110,115,'Hafalan Baru',30,113,1,5,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,114,30,'2026-08-23 05:39:48','2026-08-23 05:39:48'),(111,116,'Hafalan Baru',30,114,1,6,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,NULL,NULL,NULL,NULL,'2026-08-23 05:40:04','2026-08-23 05:40:04'),(112,113,'Hafalan Baru',30,111,1,5,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,112,30,'2026-08-23 05:40:18','2026-08-23 05:40:18'),(113,117,'Hafalan Baru',30,113,1,5,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,114,30,'2026-08-23 05:40:18','2026-08-23 05:40:18'),(114,114,'Hafalan Baru',30,112,1,4,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,113,30,'2026-08-23 05:40:31','2026-08-23 05:40:31'),(115,118,'Hafalan Baru',30,114,1,6,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,NULL,NULL,NULL,NULL,'2026-08-23 05:40:31','2026-08-23 05:40:31'),(117,120,'Hafalan Baru',29,67,1,30,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,68,29,'2026-08-23 07:59:14','2026-08-23 07:59:14'),(118,121,'Hafalan Baru',29,68,1,52,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,69,29,'2026-08-23 08:00:03','2026-08-23 08:00:03'),(128,122,'Hafalan Baru',29,69,1,52,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,70,29,'2026-08-23 08:05:37','2026-08-23 08:05:37'),(129,131,'Hafalan Baru',29,67,1,30,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,68,29,'2026-08-23 08:05:38','2026-08-23 08:05:38'),(130,123,'Hafalan Baru',29,70,1,44,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,71,29,'2026-08-23 08:06:02','2026-08-23 08:06:02'),(131,132,'Hafalan Baru',29,68,1,52,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,69,29,'2026-08-23 08:06:02','2026-08-23 08:06:02'),(132,124,'Hafalan Baru',29,71,1,28,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,72,29,'2026-08-23 08:06:20','2026-08-23 08:06:20'),(133,133,'Hafalan Baru',29,69,1,52,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,70,29,'2026-08-23 08:06:21','2026-08-23 08:06:21'),(134,125,'Hafalan Baru',29,72,1,28,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,73,29,'2026-08-23 08:06:42','2026-08-23 08:06:42'),(135,134,'Hafalan Baru',29,70,1,44,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,71,29,'2026-08-23 08:06:42','2026-08-23 08:06:42'),(136,126,'Hafalan Baru',29,73,1,20,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,74,29,'2026-08-23 08:07:01','2026-08-23 08:07:01'),(137,135,'Hafalan Baru',29,71,1,28,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,72,29,'2026-08-23 08:07:01','2026-08-23 08:07:01'),(138,127,'Hafalan Baru',29,74,1,56,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,75,29,'2026-08-23 08:07:28','2026-08-23 08:07:28'),(139,136,'Hafalan Baru',29,72,1,28,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,73,29,'2026-08-23 08:07:28','2026-08-23 08:07:28'),(140,128,'Hafalan Baru',29,75,1,40,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,76,29,'2026-08-23 08:07:50','2026-08-23 08:07:50'),(141,137,'Hafalan Baru',29,73,1,20,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,74,29,'2026-08-23 08:07:51','2026-08-23 08:07:51'),(142,129,'Hafalan Baru',29,76,1,31,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,77,29,'2026-08-23 08:09:38','2026-08-23 08:09:38'),(143,138,'Hafalan Baru',29,74,1,56,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,75,29,'2026-08-23 08:09:39','2026-08-23 08:09:39'),(144,130,'Hafalan Baru',29,77,1,50,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,78,30,'2026-08-23 08:10:02','2026-08-23 08:10:02'),(145,139,'Hafalan Baru',29,75,1,40,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,76,29,'2026-08-23 08:10:02','2026-08-23 08:10:02'),(146,140,'Hafalan Baru',29,76,1,31,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,77,29,'2026-08-23 08:10:19','2026-08-23 08:10:19'),(147,141,'Hafalan Baru',29,77,1,50,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,78,30,'2026-08-23 08:10:37','2026-08-23 08:10:37'),(148,142,'Hafalan Baru',28,58,1,22,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,59,28,'2026-08-23 08:21:35','2026-08-23 08:21:35'),(149,143,'Hafalan Baru',28,59,1,24,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,60,28,'2026-08-23 08:22:03','2026-08-23 08:22:03'),(151,144,'Hafalan Baru',28,60,1,13,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,61,28,'2026-08-23 08:24:44','2026-08-23 08:24:44'),(152,145,'Hafalan Baru',28,58,1,22,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,59,28,'2026-08-23 08:24:45','2026-08-23 08:24:45'),(159,146,'Hafalan Baru',28,61,1,14,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,62,28,'2026-08-23 08:34:04','2026-08-23 08:34:04'),(160,152,'Hafalan Baru',28,59,1,24,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,60,28,'2026-08-23 08:34:04','2026-08-23 08:34:04'),(161,147,'Hafalan Baru',28,62,1,11,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,63,28,'2026-08-23 08:34:18','2026-08-23 08:34:18'),(162,153,'Hafalan Baru',28,60,1,13,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,61,28,'2026-08-23 08:34:18','2026-08-23 08:34:18'),(163,148,'Hafalan Baru',28,63,1,11,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,64,28,'2026-08-23 08:34:43','2026-08-23 08:34:43'),(164,154,'Hafalan Baru',28,61,1,14,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,62,28,'2026-08-23 08:34:44','2026-08-23 08:34:44'),(165,149,'Hafalan Baru',28,64,1,18,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,65,28,'2026-08-23 08:34:58','2026-08-23 08:34:58'),(166,155,'Hafalan Baru',28,62,1,11,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,63,28,'2026-08-23 08:34:58','2026-08-23 08:34:58'),(167,150,'Hafalan Baru',28,65,1,12,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,66,28,'2026-08-23 08:35:12','2026-08-23 08:35:12'),(168,156,'Hafalan Baru',28,63,1,11,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,64,28,'2026-08-23 08:35:12','2026-08-23 08:35:12'),(169,151,'Hafalan Baru',28,66,1,12,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,67,29,'2026-08-23 08:35:27','2026-08-23 08:35:27'),(170,157,'Hafalan Baru',28,64,1,18,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,65,28,'2026-08-23 08:35:27','2026-08-23 08:35:27'),(171,158,'Hafalan Baru',28,65,1,12,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,66,28,'2026-08-23 08:35:42','2026-08-23 08:35:42'),(172,159,'Hafalan Baru',28,66,1,12,'Lancar','Lancar','Lancar','Lancar','Lancar','Lanjut',NULL,1,3,67,29,'2026-08-23 08:35:57','2026-08-23 08:35:57');
/*!40000 ALTER TABLE `setorans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siswas`
--

DROP TABLE IF EXISTS `siswas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `siswas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nis` varchar(255) NOT NULL,
  `nisn` varchar(255) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `password_text` varchar(255) DEFAULT NULL,
  `keterangan_akun` varchar(255) DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `tempat_lahir` varchar(255) NOT NULL,
  `nama_ayah` varchar(255) DEFAULT NULL,
  `nama_ibu` varchar(255) DEFAULT NULL,
  `status_akademik` enum('Aktif','Lulus SMP','Pindah Sekolah','Keluar') NOT NULL DEFAULT 'Aktif',
  `status_tahfidz` enum('Sedang Berjalan','Lulus Program Tahfidz') NOT NULL DEFAULT 'Sedang Berjalan',
  `tanggal_lulus_akademik` date DEFAULT NULL COMMENT 'Tanggal kelulusan akademik (tamat SMP kelas 9)',
  `status_penempatan` enum('Belum Ditempatkan','Ditempatkan') NOT NULL DEFAULT 'Belum Ditempatkan',
  `status_masuk` varchar(255) DEFAULT NULL,
  `punya_hafalan_sebelumnya` tinyint(1) NOT NULL DEFAULT 0,
  `tanggal_masuk` date DEFAULT NULL,
  `asal_sekolah` varchar(255) DEFAULT NULL,
  `alamat_sekolah_asal` text DEFAULT NULL,
  `perlu_tes_penempatan` tinyint(1) NOT NULL DEFAULT 0,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `akun_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `jenis_keluar` enum('Lulus SMP','Pindah Sekolah','Meninggal','Keluar') DEFAULT NULL COMMENT 'Jenis keberangkatan siswa dari sekolah',
  `tanggal_keluar` date DEFAULT NULL COMMENT 'Tanggal siswa keluar dari sekolah',
  `tujuan_sekolah` varchar(255) DEFAULT NULL COMMENT 'Nama sekolah tujuan (wajib untuk jenis_keluar = Pindah Sekolah)',
  `keterangan_keluar` text DEFAULT NULL COMMENT 'Catatan keluar (wajib untuk jenis_keluar = Meninggal atau Keluar)',
  `diproses_oleh` bigint(20) unsigned DEFAULT NULL COMMENT 'Admin yang memproses registrasi keluar',
  `kelompok_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal_penempatan` date DEFAULT NULL COMMENT 'Tanggal siswa ditempatkan ke kelompok tahfidz',
  `kelas_id` bigint(20) unsigned DEFAULT NULL,
  `tahun_ajaran_id` bigint(20) unsigned DEFAULT NULL,
  `reset_oleh` bigint(20) unsigned DEFAULT NULL,
  `reset_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `siswas_nis_unique` (`nis`),
  UNIQUE KEY `siswas_nisn_unique` (`nisn`),
  KEY `siswas_reset_oleh_foreign` (`reset_oleh`),
  KEY `siswas_status_aktif_index` (`status_aktif`),
  KEY `siswas_status_akademik_index` (`status_akademik`),
  KEY `siswas_status_penempatan_index` (`status_penempatan`),
  KEY `siswas_kelompok_status_index` (`kelompok_id`,`status_akademik`),
  KEY `siswas_diproses_oleh_foreign` (`diproses_oleh`),
  KEY `siswas_jenis_keluar_index` (`jenis_keluar`),
  KEY `siswas_kelas_id_index` (`kelas_id`),
  KEY `siswas_tahun_ajaran_id_index` (`tahun_ajaran_id`),
  KEY `siswas_tanggal_penempatan_index` (`tanggal_penempatan`),
  KEY `idx_siswas_kelompok_status` (`kelompok_id`,`status_akademik`),
  KEY `idx_siswas_penempatan` (`status_penempatan`),
  KEY `idx_siswas_tahun_ajaran` (`tahun_ajaran_id`),
  CONSTRAINT `siswas_diproses_oleh_foreign` FOREIGN KEY (`diproses_oleh`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `siswas_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `siswas_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompoks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `siswas_reset_oleh_foreign` FOREIGN KEY (`reset_oleh`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `siswas_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajarans` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siswas`
--

LOCK TABLES `siswas` WRITE;
/*!40000 ALTER TABLE `siswas` DISABLE KEYS */;
INSERT INTO `siswas` (`id`, `nis`, `nisn`, `nama`, `tanggal_lahir`, `password`, `password_text`, `keterangan_akun`, `jenis_kelamin`, `tempat_lahir`, `nama_ayah`, `nama_ibu`, `status_akademik`, `status_tahfidz`, `tanggal_lulus_akademik`, `status_penempatan`, `status_masuk`, `punya_hafalan_sebelumnya`, `tanggal_masuk`, `asal_sekolah`, `alamat_sekolah_asal`, `perlu_tes_penempatan`, `status_aktif`, `akun_aktif`, `jenis_keluar`, `tanggal_keluar`, `tujuan_sekolah`, `keterangan_keluar`, `diproses_oleh`, `kelompok_id`, `tanggal_penempatan`, `kelas_id`, `tahun_ajaran_id`, `reset_oleh`, `reset_pada`, `created_at`, `updated_at`, `deleted_at`) VALUES (2,'078789798','010234660','ACHMAD SYAQIMAN','2012-01-01','$2y$12$2voL70HSfolmqy4/q2mEVOyTX.ExDXwCluR5CQcbYuDc2tM91mjYG','Rf50e7C)','Password belum diganti','Laki-laki','Cianjur','Odip','Imas','Aktif','Lulus Program Tahfidz',NULL,'Belum Ditempatkan','Baru',0,'2024-07-01','SDN Ciranjanggirang 2',NULL,0,1,1,NULL,NULL,NULL,NULL,NULL,3,'2024-08-29',1,1,1,'2026-08-23 03:45:27','2026-08-23 02:51:18','2026-08-23 10:38:59',NULL),(3,'969869868','011200239','Akmal Alfitrah Budiman','2011-05-25','$2y$12$XWECTjaBSOX/R67qEq2IQe5L9J7Dunotl.PxSocEEseQRKpiYXfGu',NULL,'Password sudah diganti','Laki-laki','Cianjur','Surahman','Siti','Aktif','Lulus Program Tahfidz',NULL,'Belum Ditempatkan','Baru',0,'2024-07-01','SDN Ciranjanggirang 2',NULL,0,1,1,NULL,NULL,NULL,NULL,NULL,3,'2024-08-31',2,1,1,'2026-08-23 06:15:17','2026-08-23 02:54:57','2026-08-23 10:38:20',NULL);
/*!40000 ALTER TABLE `siswas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `surahs`
--

DROP TABLE IF EXISTS `surahs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `surahs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nomor_surah` tinyint(3) unsigned NOT NULL COMMENT 'Nomor surah 1-114',
  `nama_latin` varchar(50) NOT NULL COMMENT 'Nama surah dalam huruf latin',
  `nama_arab` varchar(50) NOT NULL COMMENT 'Nama surah dalam huruf arab',
  `jumlah_ayat` smallint(5) unsigned NOT NULL COMMENT 'Total ayat dalam surah',
  `tempat_turun` enum('Makkiyah','Madaniyah') NOT NULL COMMENT 'Tempat turunnya surah',
  `urutan_mushaf` tinyint(3) unsigned NOT NULL COMMENT 'Urutan surah dalam mushaf (1-114)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `surahs_nomor_surah_unique` (`nomor_surah`),
  KEY `idx_nomor_surah` (`nomor_surah`),
  KEY `idx_urutan_mushaf` (`urutan_mushaf`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surahs`
--

LOCK TABLES `surahs` WRITE;
/*!40000 ALTER TABLE `surahs` DISABLE KEYS */;
INSERT INTO `surahs` (`id`, `nomor_surah`, `nama_latin`, `nama_arab`, `jumlah_ayat`, `tempat_turun`, `urutan_mushaf`, `created_at`, `updated_at`) VALUES (1,1,'Al-Fatihah','الفاتحة',7,'Makkiyah',1,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(2,2,'Al-Baqarah','البقرة',286,'Madaniyah',2,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(3,3,'Ali Imran','آل عمران',200,'Madaniyah',3,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(4,4,'An-Nisa','النساء',176,'Madaniyah',4,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(5,5,'Al-Maidah','المائدة',120,'Madaniyah',5,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(6,6,'Al-Anam','الأنعام',165,'Makkiyah',6,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(7,7,'Al-Araf','الأعراف',206,'Makkiyah',7,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(8,8,'Al-Anfal','الأنفال',75,'Madaniyah',8,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(9,9,'At-Tawbah','التوبة',129,'Madaniyah',9,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(10,10,'Yunus','يونس',109,'Makkiyah',10,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(11,11,'Hud','هود',123,'Makkiyah',11,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(12,12,'Yusuf','يوسف',111,'Makkiyah',12,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(13,13,'Ar-Rad','الرعد',43,'Madaniyah',13,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(14,14,'Ibrahim','إبراهيم',52,'Makkiyah',14,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(15,15,'Al-Hijr','الحجر',99,'Makkiyah',15,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(16,16,'An-Nahl','النحل',128,'Makkiyah',16,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(17,17,'Al-Isra','الإسراء',111,'Makkiyah',17,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(18,18,'Al-Kahf','الكهف',110,'Makkiyah',18,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(19,19,'Maryam','مريم',98,'Makkiyah',19,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(20,20,'Ta-Ha','طه',135,'Makkiyah',20,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(21,21,'Al-Anbiya','الأنبياء',112,'Makkiyah',21,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(22,22,'Al-Hajj','الحج',78,'Madaniyah',22,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(23,23,'Al-Muminun','المؤمنون',118,'Makkiyah',23,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(24,24,'An-Nur','النور',64,'Madaniyah',24,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(25,25,'Al-Furqan','الفرقان',77,'Makkiyah',25,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(26,26,'Asy-Syuara','الشعراء',227,'Makkiyah',26,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(27,27,'An-Naml','النمل',93,'Makkiyah',27,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(28,28,'Al-Qasas','القصص',88,'Makkiyah',28,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(29,29,'Al-Ankabut','العنكبوت',69,'Makkiyah',29,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(30,30,'Ar-Rum','الروم',60,'Makkiyah',30,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(31,31,'Luqman','لقمان',34,'Makkiyah',31,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(32,32,'As-Sajdah','السجدة',30,'Makkiyah',32,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(33,33,'Al-Ahzab','الأحزاب',73,'Madaniyah',33,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(34,34,'Saba','سبأ',54,'Makkiyah',34,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(35,35,'Fatir','فاطر',45,'Makkiyah',35,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(36,36,'Ya-Sin','يس',83,'Makkiyah',36,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(37,37,'As-Saffat','الصافات',182,'Makkiyah',37,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(38,38,'Sad','ص',88,'Makkiyah',38,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(39,39,'Az-Zumar','الزمر',75,'Makkiyah',39,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(40,40,'Gafir','غافر',85,'Makkiyah',40,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(41,41,'Fussilat','فصلت',54,'Makkiyah',41,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(42,42,'Asy-Syura','الشورى',53,'Makkiyah',42,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(43,43,'Az-Zukhruf','الزخرف',89,'Makkiyah',43,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(44,44,'Ad-Dukhan','الدخان',59,'Makkiyah',44,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(45,45,'Al-Jasiyah','الجاثية',37,'Makkiyah',45,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(46,46,'Al-Ahqaf','الأحقاف',35,'Makkiyah',46,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(47,47,'Muhammad','محمد',38,'Madaniyah',47,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(48,48,'Al-Fath','الفتح',29,'Madaniyah',48,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(49,49,'Al-Hujurat','الحجرات',18,'Madaniyah',49,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(50,50,'Qaf','ق',45,'Makkiyah',50,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(51,51,'Az-Zariyat','الذاريات',60,'Makkiyah',51,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(52,52,'At-Tur','الطور',49,'Makkiyah',52,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(53,53,'An-Najm','النجم',62,'Makkiyah',53,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(54,54,'Al-Qamar','القمر',55,'Makkiyah',54,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(55,55,'Ar-Rahman','الرحمن',78,'Madaniyah',55,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(56,56,'Al-Waqiah','الواقعة',96,'Makkiyah',56,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(57,57,'Al-Hadid','الحديد',29,'Madaniyah',57,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(58,58,'Al-Mujadilah','المجادلة',22,'Madaniyah',58,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(59,59,'Al-Hasyr','الحشر',24,'Madaniyah',59,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(60,60,'Al-Mumtahanah','الممتحنة',13,'Madaniyah',60,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(61,61,'As-Saf','الصف',14,'Madaniyah',61,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(62,62,'Al-Jumuah','الجمعة',11,'Madaniyah',62,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(63,63,'Al-Munafiqun','المنافقون',11,'Madaniyah',63,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(64,64,'At-Tagabun','التغابن',18,'Madaniyah',64,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(65,65,'At-Talaq','الطلاق',12,'Madaniyah',65,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(66,66,'At-Tahrim','التحريم',12,'Madaniyah',66,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(67,67,'Al-Mulk','الملك',30,'Makkiyah',67,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(68,68,'Al-Qalam','القلم',52,'Makkiyah',68,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(69,69,'Al-Haqqah','الحاقة',52,'Makkiyah',69,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(70,70,'Al-Maarij','المعارج',44,'Makkiyah',70,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(71,71,'Nuh','نوح',28,'Makkiyah',71,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(72,72,'Al-Jinn','الجن',28,'Makkiyah',72,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(73,73,'Al-Muzzammil','المزمل',20,'Makkiyah',73,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(74,74,'Al-Muddassir','المدثر',56,'Makkiyah',74,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(75,75,'Al-Qiyamah','القيامة',40,'Makkiyah',75,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(76,76,'Al-Insan','الإنسان',31,'Madaniyah',76,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(77,77,'Al-Mursalat','المرسلات',50,'Makkiyah',77,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(78,78,'An-Naba','النبأ',40,'Makkiyah',78,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(79,79,'An-Naziat','النازعات',46,'Makkiyah',79,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(80,80,'Abasa','عبس',42,'Makkiyah',80,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(81,81,'At-Takwir','التكوير',29,'Makkiyah',81,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(82,82,'Al-Infitar','الانفطار',19,'Makkiyah',82,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(83,83,'Al-Mutaffifin','المطففين',36,'Makkiyah',83,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(84,84,'Al-Insyiqaq','الانشقاق',25,'Makkiyah',84,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(85,85,'Al-Buruj','البروج',22,'Makkiyah',85,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(86,86,'At-Tariq','الطارق',17,'Makkiyah',86,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(87,87,'Al-Ala','الأعلى',19,'Makkiyah',87,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(88,88,'Al-Gasyiyah','الغاشية',26,'Makkiyah',88,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(89,89,'Al-Fajr','الفجر',30,'Makkiyah',89,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(90,90,'Al-Balad','البلد',20,'Makkiyah',90,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(91,91,'Asy-Syams','الشمس',15,'Makkiyah',91,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(92,92,'Al-Lail','الليل',21,'Makkiyah',92,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(93,93,'Ad-Duha','الضحى',11,'Makkiyah',93,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(94,94,'Al-Insyirah','الشرح',8,'Makkiyah',94,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(95,95,'At-Tin','التين',8,'Makkiyah',95,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(96,96,'Al-Alaq','العلق',19,'Makkiyah',96,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(97,97,'Al-Qadr','القدر',5,'Makkiyah',97,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(98,98,'Al-Bayyinah','البينة',8,'Madaniyah',98,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(99,99,'Az-Zalzalah','الزلزلة',8,'Madaniyah',99,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(100,100,'Al-Adiyat','العاديات',11,'Makkiyah',100,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(101,101,'Al-Qariah','القارعة',11,'Makkiyah',101,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(102,102,'At-Takasur','التكاثر',8,'Makkiyah',102,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(103,103,'Al-Asr','العصر',3,'Makkiyah',103,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(104,104,'Al-Humazah','الهمزة',9,'Makkiyah',104,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(105,105,'Al-Fil','الفيل',5,'Makkiyah',105,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(106,106,'Quraisy','قريش',4,'Makkiyah',106,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(107,107,'Al-Maun','الماعون',7,'Makkiyah',107,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(108,108,'Al-Kausar','الكوثر',3,'Makkiyah',108,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(109,109,'Al-Kafirun','الكافرون',6,'Makkiyah',109,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(110,110,'An-Nasr','النصر',3,'Madaniyah',110,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(111,111,'Al-Masad','المسد',5,'Makkiyah',111,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(112,112,'Al-Ikhlas','الإخلاص',4,'Makkiyah',112,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(113,113,'Al-Falaq','الفلق',5,'Makkiyah',113,'2026-08-22 16:26:58','2026-08-22 16:26:58'),(114,114,'An-Nas','الناس',6,'Makkiyah',114,'2026-08-22 16:26:58','2026-08-22 16:26:58');
/*!40000 ALTER TABLE `surahs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tahun_ajarans`
--

DROP TABLE IF EXISTS `tahun_ajarans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tahun_ajarans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(20) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tahun_ajarans_label_unique` (`label`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tahun_ajarans`
--

LOCK TABLES `tahun_ajarans` WRITE;
/*!40000 ALTER TABLE `tahun_ajarans` DISABLE KEYS */;
INSERT INTO `tahun_ajarans` (`id`, `label`, `tanggal_mulai`, `tanggal_selesai`, `status`, `created_at`, `updated_at`) VALUES (1,'2024/2025','2024-07-01','2025-06-30',1,'2026-08-22 17:27:04','2026-08-22 17:44:53'),(2,'2025/2026','2025-07-01','2026-06-30',0,'2026-08-22 17:27:18','2026-08-22 17:27:18'),(3,'2026/2027','2026-07-01','2027-06-30',0,'2026-08-22 17:27:27','2026-08-22 17:44:53');
/*!40000 ALTER TABLE `tahun_ajarans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tes_penempatans`
--

DROP TABLE IF EXISTS `tes_penempatans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tes_penempatans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint(20) unsigned NOT NULL,
  `guru_penguji_id` bigint(20) unsigned NOT NULL,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal_tes` date NOT NULL,
  `waktu_tes` time DEFAULT NULL,
  `status` enum('Terjadwal','Selesai','Dibatalkan') NOT NULL DEFAULT 'Terjadwal',
  `rekomendasi_tingkat` enum('Dasar','Menengah','Atas') DEFAULT NULL,
  `catatan_kualitas` text DEFAULT NULL,
  `alasan_pembatalan` text DEFAULT NULL,
  `final_kelompok_id` bigint(20) unsigned DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `ditempatkan_at` timestamp NULL DEFAULT NULL,
  `penempatan_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tes_penempatans_admin_id_foreign` (`admin_id`),
  KEY `tes_penempatans_final_kelompok_id_foreign` (`final_kelompok_id`),
  KEY `tes_penempatans_penempatan_by_foreign` (`penempatan_by`),
  KEY `tes_penempatans_siswa_id_status_index` (`siswa_id`,`status`),
  KEY `tes_penempatans_guru_penguji_id_status_index` (`guru_penguji_id`,`status`),
  KEY `tes_penempatans_tanggal_tes_index` (`tanggal_tes`),
  CONSTRAINT `tes_penempatans_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tes_penempatans_final_kelompok_id_foreign` FOREIGN KEY (`final_kelompok_id`) REFERENCES `kelompoks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tes_penempatans_guru_penguji_id_foreign` FOREIGN KEY (`guru_penguji_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tes_penempatans_penempatan_by_foreign` FOREIGN KEY (`penempatan_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tes_penempatans_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tes_penempatans`
--

LOCK TABLES `tes_penempatans` WRITE;
/*!40000 ALTER TABLE `tes_penempatans` DISABLE KEYS */;
/*!40000 ALTER TABLE `tes_penempatans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transkrip_capaians`
--

DROP TABLE IF EXISTS `transkrip_capaians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transkrip_capaians` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint(20) unsigned NOT NULL,
  `tanggal_generate` date NOT NULL,
  `reason` varchar(255) NOT NULL,
  `generated_by` bigint(20) unsigned DEFAULT NULL,
  `total_juz_tuntas_snapshot` int(11) NOT NULL,
  `detail_capaian_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`detail_capaian_snapshot`)),
  `target_kelulusan_at_snapshot` int(11) NOT NULL,
  `batas_tingkat_at_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`batas_tingkat_at_snapshot`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transkrip_capaians_generated_by_foreign` (`generated_by`),
  KEY `transkrip_capaians_siswa_id_index` (`siswa_id`),
  KEY `transkrip_capaians_tanggal_generate_index` (`tanggal_generate`),
  CONSTRAINT `transkrip_capaians_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `admins` (`id`),
  CONSTRAINT `transkrip_capaians_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transkrip_capaians`
--

LOCK TABLES `transkrip_capaians` WRITE;
/*!40000 ALTER TABLE `transkrip_capaians` DISABLE KEYS */;
/*!40000 ALTER TABLE `transkrip_capaians` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usulan_kenaikan_tingkats`
--

DROP TABLE IF EXISTS `usulan_kenaikan_tingkats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usulan_kenaikan_tingkats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint(20) unsigned NOT NULL,
  `guru_pengusul_id` bigint(20) unsigned NOT NULL,
  `tingkat_asal` enum('Dasar','Menengah','Atas') NOT NULL,
  `tingkat_tujuan` enum('Dasar','Menengah','Atas') NOT NULL,
  `kelompok_asal_id` bigint(20) unsigned DEFAULT NULL,
  `kelompok_tujuan_id` bigint(20) unsigned DEFAULT NULL,
  `status_usulan` enum('Diajukan','Disetujui','Ditempatkan','Ditolak') NOT NULL DEFAULT 'Diajukan',
  `alasan` text NOT NULL,
  `tanggal_usul` datetime NOT NULL,
  `admin_verifikator_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal_keputusan` datetime DEFAULT NULL,
  `catatan_keputusan` text DEFAULT NULL,
  `tanggal_penempatan` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usulan_kenaikan_tingkats_kelompok_asal_id_foreign` (`kelompok_asal_id`),
  KEY `usulan_kenaikan_tingkats_kelompok_tujuan_id_foreign` (`kelompok_tujuan_id`),
  KEY `usulan_kenaikan_tingkats_admin_verifikator_id_foreign` (`admin_verifikator_id`),
  KEY `usulan_kenaikan_tingkats_siswa_id_index` (`siswa_id`),
  KEY `usulan_kenaikan_tingkats_guru_pengusul_id_index` (`guru_pengusul_id`),
  KEY `usulan_kenaikan_tingkats_status_usulan_index` (`status_usulan`),
  KEY `usulan_kenaikan_tingkats_tingkat_tujuan_index` (`tingkat_tujuan`),
  KEY `usulan_status_tanggal_index` (`status_usulan`,`tanggal_usul`),
  KEY `idx_usulan_status` (`status_usulan`),
  KEY `idx_usulan_siswa` (`siswa_id`),
  CONSTRAINT `usulan_kenaikan_tingkats_admin_verifikator_id_foreign` FOREIGN KEY (`admin_verifikator_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  CONSTRAINT `usulan_kenaikan_tingkats_guru_pengusul_id_foreign` FOREIGN KEY (`guru_pengusul_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `usulan_kenaikan_tingkats_kelompok_asal_id_foreign` FOREIGN KEY (`kelompok_asal_id`) REFERENCES `kelompoks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `usulan_kenaikan_tingkats_kelompok_tujuan_id_foreign` FOREIGN KEY (`kelompok_tujuan_id`) REFERENCES `kelompoks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `usulan_kenaikan_tingkats_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usulan_kenaikan_tingkats`
--

LOCK TABLES `usulan_kenaikan_tingkats` WRITE;
/*!40000 ALTER TABLE `usulan_kenaikan_tingkats` DISABLE KEYS */;
INSERT INTO `usulan_kenaikan_tingkats` (`id`, `siswa_id`, `guru_pengusul_id`, `tingkat_asal`, `tingkat_tujuan`, `kelompok_asal_id`, `kelompok_tujuan_id`, `status_usulan`, `alasan`, `tanggal_usul`, `admin_verifikator_id`, `tanggal_keputusan`, `catatan_keputusan`, `tanggal_penempatan`, `created_at`, `updated_at`) VALUES (14,2,6,'Dasar','Menengah',1,2,'Ditempatkan','Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.','2024-08-15 00:00:00',1,'2024-08-15 00:00:00',NULL,'2024-08-16 00:00:00','2026-08-23 07:01:32','2026-08-23 07:25:40'),(15,3,6,'Dasar','Menengah',1,2,'Ditempatkan','Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.','2024-08-17 00:00:00',1,'2024-08-17 00:00:00',NULL,'2024-08-19 00:00:00','2026-08-23 07:26:22','2026-08-23 07:27:25'),(16,2,1,'Menengah','Atas',2,3,'Ditempatkan','Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.','2024-08-28 00:00:00',1,'2024-08-28 00:00:00',NULL,'2024-08-29 00:00:00','2026-08-23 08:11:02','2026-08-23 08:14:30'),(17,3,1,'Menengah','Atas',2,3,'Ditempatkan','Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.','2024-08-30 00:00:00',1,'2024-08-30 00:00:00',NULL,'2024-08-31 00:00:00','2026-08-23 08:11:14','2026-08-23 08:13:39');
/*!40000 ALTER TABLE `usulan_kenaikan_tingkats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'tahfidz_absensi_test'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-23 18:48:44
