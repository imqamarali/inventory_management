-- MySQL dump 10.13  Distrib 8.4.7, for Win64 (x86_64)
--
-- Host: localhost    Database: inventory_system
-- ------------------------------------------------------
-- Server version	8.4.7

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
-- Table structure for table `activitylogs`
--

DROP TABLE IF EXISTS `activitylogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activitylogs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `activity` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `activitytype` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uid` int DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `additional_data` text COLLATE utf8mb4_unicode_ci,
  `date` date DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_date` (`date`),
  KEY `idx_datetime` (`datetime`),
  KEY `idx_module` (`module`),
  KEY `idx_activitytype` (`activitytype`)
) ENGINE=InnoDB AUTO_INCREMENT=385 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activitylogs`
--

LOCK TABLES `activitylogs` WRITE;
/*!40000 ALTER TABLE `activitylogs` DISABLE KEYS */;
INSERT INTO `activitylogs` VALUES (1,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 13:50:00'),(2,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 13:52:26'),(3,'Updated brand: Toyota Genuine','update','1','Products',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"brand_update\",\"brand_code\":\"TOY\"}','2026-07-22','2026-07-22 08:55:30'),(4,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 14:06:18'),(5,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 14:22:52'),(6,'Viewed stock adjustments list','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"stock_adjustments_view\",\"filters\":{\"warehouse\":\"\",\"adjustment_type\":\"\"},\"page\":1,\"total_records\":0}','2026-07-22','2026-07-22 09:23:33'),(7,'Updated warehouse: Main Warehouse','update','1','inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"warehouse_name\":\"Main Warehouse\",\"warehouse_code\":\"WH001\",\"address\":\"I-9 Industrial Area Islamabad\",\"city\":\"Islamabad\",\"province\":\"Islamabad Capital Territory\",\"country\":\"Pakistan\",\"contact_person\":\"Muhammad Asif\",\"phone\":\"+92-51-111-0001\",\"email\":\"mainwarehouse@company.com\",\"remarks\":\"Central inventory warehouse!\",\"is_active\":1,\"updated_at\":\"2026-07-22 09:23:43\",\"updated_by\":1}','2026-07-22','2026-07-22 14:23:43'),(8,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-22','2026-07-22 09:24:04'),(9,'Viewed sales orders list','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":3}','2026-07-22','2026-07-22 09:24:05'),(10,'Updated sales order status to: Confirmed','update','3','Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_order_status_update\",\"new_status\":\"Confirmed\"}','2026-07-22','2026-07-22 09:24:09'),(11,'Viewed sales orders list','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":3}','2026-07-22','2026-07-22 09:24:10'),(12,'Viewed inventory dashboard','view',NULL,'Inventory',666,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.22209.3 Chrome/148.0.7778.271 Electron/42.5.1 Safari/537.36 MSIX','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 14:53:13'),(13,'Viewed inventory dashboard','view',NULL,'Inventory',666,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.22209.3 Chrome/148.0.7778.271 Electron/42.5.1 Safari/537.36 MSIX','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 15:14:30'),(14,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 15:24:35'),(15,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-22','2026-07-22 10:26:04'),(16,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 15:29:02'),(17,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-22','2026-07-22 10:39:30'),(18,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 15:39:59'),(19,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 15:51:51'),(20,'Viewed inventory dashboard','view',NULL,'Inventory',665,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 15:52:42'),(21,'Viewed inventory dashboard','view',NULL,'Inventory',665,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 15:52:49'),(22,'Accessed Sales Dashboard','view',NULL,'Sales',665,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-22','2026-07-22 10:52:52'),(23,'Accessed Sales Dashboard','view',NULL,'Sales',665,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-22','2026-07-22 10:52:53'),(24,'Viewed sales orders list','view',NULL,'Sales',665,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":3}','2026-07-22','2026-07-22 10:52:54'),(25,'Viewed inventory dashboard','view',NULL,'Inventory',665,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 15:52:59'),(26,'Viewed inventory dashboard','view',NULL,'Inventory',665,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 15:55:07'),(27,'Viewed inventory dashboard','view',NULL,'Inventory',665,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 15:55:09'),(28,'Viewed inventory dashboard','view',NULL,'Inventory',665,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:02:41'),(29,'Accessed Sales Dashboard','view',NULL,'Sales',665,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-22','2026-07-22 11:02:43'),(30,'Viewed inventory dashboard','view',NULL,'Inventory',665,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:02:44'),(31,'Viewed inventory dashboard','view',NULL,'Inventory',665,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:03:25'),(32,'Viewed inventory dashboard','view',NULL,'Inventory',665,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:03:33'),(33,'Viewed inventory dashboard','view',NULL,'Inventory',665,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:03:51'),(34,'Viewed inventory dashboard','view',NULL,'Inventory',665,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:05:26'),(35,'Viewed inventory dashboard','view',NULL,'Inventory',665,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:07:14'),(36,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:08:00'),(37,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:09:01'),(38,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:09:02'),(39,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:11:32'),(40,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:12:05'),(41,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:12:58'),(42,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:13:48'),(43,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:14:08'),(44,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:14:22'),(45,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:14:30'),(46,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:14:38'),(47,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:15:25'),(48,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:15:29'),(49,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:15:46'),(50,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:16:26'),(51,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 16:20:15'),(52,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 17:35:24'),(53,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 17:49:03'),(54,'Updated system contract: Pakistan Professional Subscription - Monthly','update','1','system',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"contract_name\":\"Pakistan Professional Subscription - Monthly\",\"contract_type\":\"monthly\",\"contractor_name\":\"Inventory Management Solutions (Pvt) Ltd\",\"contractor_cnic\":\"12345-6789012-3\",\"contractor_phone\":\"+92-318-5657457\",\"contractor_email\":\"support@inventorysystem.pk\",\"contractor_address\":\"Tech Park, Sector I-8\\/2, Islamabad, Pakistan 44000\",\"installation_date\":\"2026-07-22\",\"contract_start_date\":\"2026-07-22\",\"contract_end_date\":\"\",\"monthly_charges\":5000,\"yearly_charges\":0,\"monthly_due_date\":1,\"maximum_extension_days\":15,\"system_status\":\"active\",\"contract_description\":\"SYSTEM SUBSCRIPTION CONTRACT\\r\\n\\r\\nType: Monthly Subscription Agreement\\r\\nValid From: 2026-07-22\\r\\n\\r\\nThis contract outlines the terms and conditions for the use of the Inventory Management System (IMS) on a monthly subscription basis. The subscription charges cover system access, data storage, technical support, and regular maintenance updates as per the agreed schedule.\\r\\n\\r\\nMonthly Subscription Fee: PKR 5,000 (Five Thousand Rupees Only)\\r\\nPayment Due Date: 1st of every month\\r\\nGrace Period: 15 days (until 15th of the month)\\r\\n\\r\\nThe system will remain fully operational during normal business hours (9:00 AM to 6:00 PM PST) with scheduled maintenance windows on alternate Sundays from 11:00 PM to 1:00 AM.\",\"policy_description\":\"PAYMENT POLICY & TERMS\\r\\n\\r\\n1. PAYMENT TERMS\\r\\n   \\u2022 Monthly subscription charges are due on or before the 1st day of each month\\r\\n   \\u2022 Payment currency: Pakistani Rupees (PKR)\\r\\n   \\u2022 Standard billing cycle: 30 days per invoice\\r\\n\\r\\n2. ACCEPTED PAYMENT METHODS\\r\\n   \\u2022 Bank Transfer\\/Online Fund Transfer (within Pakistan)\\r\\n   \\u2022 Cheque (crossed cheque to be sent to registered office)\\r\\n   \\u2022 Bank Draft\\r\\n   \\u2022 Cash deposit to designated account\\r\\n   \\u2022 Jazz Cash \\/ Easypaisa (for amounts below PKR 50,000)\\r\\n   \\u2022 HBL PayWave \\/ UBL Omni (for online payments)\\r\\n\\r\\n3. GRACE PERIOD\\r\\n   \\u2022 15 days extension period is provided beyond the due date (until 15th of month)\\r\\n   \\u2022 No system suspension during grace period\\r\\n   \\u2022 Late payment may incur 2% additional charges after grace period expires\\r\\n\\r\\n4. SERVICE SUSPENSION POLICY\\r\\n   \\u2022 If payment is not received within the grace period (after 15th of month):\\r\\n     - System access will be restricted for all regular users\\r\\n     - Only Super Administrator will retain full system access\\r\\n     - All other users will be locked out until payment is verified\\r\\n     - Dashboard will display pending payment notifications\\r\\n\\r\\n5. PAYMENT VERIFICATION PROCESS\\r\\n   \\u2022 Users can upload payment proof documents through the system\\r\\n   \\u2022 Accepted proof formats: Bank confirmation, transaction receipt, cheque image, payment slip\\r\\n   \\u2022 Proof is verified by Super Administrator within 24 business hours\\r\\n   \\u2022 Once verified, system access is immediately restored for all users\\r\\n\\r\\n6. INVOICE GENERATION\\r\\n   \\u2022 Monthly invoices are automatically generated by the system\\r\\n   \\u2022 Invoices can be downloaded in PDF format from Payment Management dashboard\\r\\n   \\u2022 Each invoice includes GST (if applicable) calculated at 17% on the base amount\\r\\n   \\u2022 Invoices are valid receipts for accounting and audit purposes\\r\\n\\r\\n7. REFUND POLICY\\r\\n   \\u2022 No refunds for partial months - subscription charges are for full calendar month\\r\\n   \\u2022 Unused days at the end of subscription are non-refundable\\r\\n   \\u2022 System termination requires written notice 30 days in advance\\r\\n   \\u2022 Advance payments are adjustable against future invoices\\r\\n\\r\\n8. RENEWAL & DISCONTINUATION\\r\\n   \\u2022 Automatic monthly renewal unless cancelled in writing\\r\\n   \\u2022 Cancellation must be submitted by the 20th of the current billing month\\r\\n   \\u2022 Data will be retained for 30 days after subscription expiry\\r\\n   \\u2022 Extended data retention beyond 30 days may incur storage charges\\r\\n\\r\\n9. SUPPORT & MAINTENANCE\\r\\n   \\u2022 Technical support available during business hours (9 AM - 6 PM, Monday-Friday)\\r\\n   \\u2022 Non-critical issues: 2-3 business days response time\\r\\n   \\u2022 Critical issues: Same business day response (within 4 hours)\\r\\n   \\u2022 System updates and patches deployed with advance notice\\r\\n   \\u2022 Scheduled maintenance: Alternate Sundays 11 PM - 1 AM (no system access)\\r\\n\\r\\n10. COMPLIANCE & REGULATIONS\\r\\n    \\u2022 System operates in compliance with Data Protection Act, 2018\\r\\n    \\u2022 User data is stored on secure servers with AES-256 encryption\\r\\n    \\u2022 Regular backups maintained for data recovery purposes\\r\\n    \\u2022 PTA compliance for financial transaction logging maintained\\r\\n    \\u2022 GST registration and compliance as per FBR regulations\\r\\n\\r\\n11. DISPUTE RESOLUTION\\r\\n    \\u2022 All disputes shall be subject to jurisdiction of courts in Islamabad\\r\\n    \\u2022 First resolution attempt through amicable negotiation\\r\\n    \\u2022 Escalation to legal proceedings only if negotiation fails\\r\\n    \\u2022 Both parties agree to attempt mediation before litigation\",\"contractor_info\":\"SYSTEM PROVIDER & SUPPORT\\r\\n\\r\\nProvider: Inventory Management System Team\\r\\nRegistration: Government of Pakistan - Business Registration\\r\\n\\r\\nPRIMARY CONTACT\\r\\nEmail: support@inventorysystem.pk\\r\\nPhone: +92-300-1234567\\r\\nWhatsApp: +92-300-1234567\\r\\nOffice Hours: 9:00 AM - 6:00 PM (Monday-Friday, PST)\\r\\n\\r\\nMAILING ADDRESS\\r\\nInventory Management Solutions (Pvt) Ltd\\r\\nTech Park, Sector I-8\\/2\\r\\nIslamabad, Pakistan 44000\\r\\n\\r\\nPAYMENT DETAILS\\r\\nBank Account Name: Inventory Management Solutions (Pvt) Ltd\\r\\nBank Name: Habib Bank Limited (HBL)\\r\\nAccount Number: XX-XXXX-XXXX-XXXX\\r\\nIBAN: PK36HBLC0001234567890123\\r\\nBranch: Blue Area, Islamabad\\r\\n\\r\\nFor Jazz Cash \\/ Easypaisa:\\r\\nMobile Number: 03001234567 (Registered as Inventory Systems)\\r\\n\\r\\nAll payments should include invoice number or contract reference in the remarks\\/memo field for quick reconciliation.\",\"full_description\":\"INVENTORY MANAGEMENT SYSTEM - PROFESSIONAL SUBSCRIPTION AGREEMENT\\r\\n\\r\\nThis is a comprehensive service agreement for the use of the Inventory Management System, a cloud-based inventory tracking and management solution designed for businesses operating in Pakistan. The system provides real-time inventory monitoring, automated invoicing, multi-warehouse management, and detailed financial reporting capabilities.\\r\\n\\r\\nThe subscription is billed on a monthly basis at PKR 5,000 per month with a mandatory 15-day grace period for payment processing. All invoices include applicable GST at current rates and are issued automatically on the first day of each billing cycle.\\r\\n\\r\\nThe system adheres to Pakistani regulatory standards including GST compliance (FBR), data protection laws, and PTA regulations for financial transactions. Regular automatic backups ensure business continuity, and 24-hour support is available for critical issues.\\r\\n\\r\\nUsers have full access to all features including unlimited invoice creation, multi-user accounts, real-time reporting, and API integrations. Payment proofs can be uploaded directly through the system for quick verification by administrators, ensuring minimal system downtime.\\r\\n\\r\\nThis agreement remains valid for the duration of the subscription and auto-renews monthly unless cancelled with 30 days written notice. All terms and conditions are subject to Pakistani law and the jurisdiction of Islamabad courts.\\r\\n\\r\\nFor inquiries or support, contact the Support Team through the contact details provided in the system\'s Help section.\",\"updated_at\":\"2026-07-22 12:50:21\",\"updated_by\":1}','2026-07-22','2026-07-22 17:50:21'),(55,'Updated system contract: Pakistan Professional Subscription - Monthly','update','1','system',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"contract_name\":\"Pakistan Professional Subscription - Monthly\",\"contract_type\":\"monthly\",\"contractor_name\":\"Inventory Management Solutions (Pvt) Ltd\",\"contractor_cnic\":\"12345-6789012-3\",\"contractor_phone\":\"+92-318-5657457\",\"contractor_email\":\"support@inventorysystem.pk\",\"contractor_address\":\"Tech Park, Sector I-8\\/2, Islamabad, Pakistan 44000\",\"installation_date\":\"2026-07-22\",\"contract_start_date\":\"2026-07-22\",\"contract_end_date\":\"\",\"monthly_charges\":5000,\"yearly_charges\":0,\"monthly_due_date\":1,\"maximum_extension_days\":15,\"system_status\":\"active\",\"contract_description\":\"SYSTEM SUBSCRIPTION CONTRACT\\r\\n\\r\\nType: Monthly Subscription Agreement\\r\\nValid From: 2026-07-22\\r\\n\\r\\nThis contract outlines the terms and conditions for the use of the Inventory Management System (IMS) on a monthly subscription basis. The subscription charges cover system access, data storage, technical support, and regular maintenance updates as per the agreed schedule.\\r\\n\\r\\nMonthly Subscription Fee: PKR 5,000 (Five Thousand Rupees Only)\\r\\nPayment Due Date: 1st of every month\\r\\nGrace Period: 15 days (until 15th of the month)\\r\\n\\r\\nThe system will remain fully operational during normal business hours (9:00 AM to 6:00 PM PST) with scheduled maintenance windows on alternate Sundays from 11:00 PM to 1:00 AM.\",\"policy_description\":\"PAYMENT POLICY & TERMS\\r\\n\\r\\n1. PAYMENT TERMS\\r\\n   \\u2022 Monthly subscription charges are due on or before the 1st day of each month\\r\\n   \\u2022 Payment currency: Pakistani Rupees (PKR)\\r\\n   \\u2022 Standard billing cycle: 30 days per invoice\\r\\n\\r\\n2. ACCEPTED PAYMENT METHODS\\r\\n   \\u2022 Bank Transfer\\/Online Fund Transfer (within Pakistan)\\r\\n   \\u2022 Cheque (crossed cheque to be sent to registered office)\\r\\n   \\u2022 Bank Draft\\r\\n   \\u2022 Cash deposit to designated account\\r\\n   \\u2022 Jazz Cash \\/ Easypaisa (for amounts below PKR 50,000)\\r\\n   \\u2022 HBL PayWave \\/ UBL Omni (for online payments)\\r\\n\\r\\n3. GRACE PERIOD\\r\\n   \\u2022 15 days extension period is provided beyond the due date (until 15th of month)\\r\\n   \\u2022 No system suspension during grace period\\r\\n   \\u2022 Late payment may incur 2% additional charges after grace period expires\\r\\n\\r\\n4. SERVICE SUSPENSION POLICY\\r\\n   \\u2022 If payment is not received within the grace period (after 15th of month):\\r\\n     - System access will be restricted for all regular users\\r\\n     - Only Super Administrator will retain full system access\\r\\n     - All other users will be locked out until payment is verified\\r\\n     - Dashboard will display pending payment notifications\\r\\n\\r\\n5. PAYMENT VERIFICATION PROCESS\\r\\n   \\u2022 Users can upload payment proof documents through the system\\r\\n   \\u2022 Accepted proof formats: Bank confirmation, transaction receipt, cheque image, payment slip\\r\\n   \\u2022 Proof is verified by Super Administrator within 24 business hours\\r\\n   \\u2022 Once verified, system access is immediately restored for all users\\r\\n\\r\\n6. INVOICE GENERATION\\r\\n   \\u2022 Monthly invoices are automatically generated by the system\\r\\n   \\u2022 Invoices can be downloaded in PDF format from Payment Management dashboard\\r\\n   \\u2022 Each invoice includes GST (if applicable) calculated at 17% on the base amount\\r\\n   \\u2022 Invoices are valid receipts for accounting and audit purposes\\r\\n\\r\\n7. REFUND POLICY\\r\\n   \\u2022 No refunds for partial months - subscription charges are for full calendar month\\r\\n   \\u2022 Unused days at the end of subscription are non-refundable\\r\\n   \\u2022 System termination requires written notice 30 days in advance\\r\\n   \\u2022 Advance payments are adjustable against future invoices\\r\\n\\r\\n8. RENEWAL & DISCONTINUATION\\r\\n   \\u2022 Automatic monthly renewal unless cancelled in writing\\r\\n   \\u2022 Cancellation must be submitted by the 20th of the current billing month\\r\\n   \\u2022 Data will be retained for 30 days after subscription expiry\\r\\n   \\u2022 Extended data retention beyond 30 days may incur storage charges\\r\\n\\r\\n9. SUPPORT & MAINTENANCE\\r\\n   \\u2022 Technical support available during business hours (9 AM - 6 PM, Monday-Friday)\\r\\n   \\u2022 Non-critical issues: 2-3 business days response time\\r\\n   \\u2022 Critical issues: Same business day response (within 4 hours)\\r\\n   \\u2022 System updates and patches deployed with advance notice\\r\\n   \\u2022 Scheduled maintenance: Alternate Sundays 11 PM - 1 AM (no system access)\\r\\n\\r\\n10. COMPLIANCE & REGULATIONS\\r\\n    \\u2022 System operates in compliance with Data Protection Act, 2018\\r\\n    \\u2022 User data is stored on secure servers with AES-256 encryption\\r\\n    \\u2022 Regular backups maintained for data recovery purposes\\r\\n    \\u2022 PTA compliance for financial transaction logging maintained\\r\\n    \\u2022 GST registration and compliance as per FBR regulations\\r\\n\\r\\n11. DISPUTE RESOLUTION\\r\\n    \\u2022 All disputes shall be subject to jurisdiction of courts in Islamabad\\r\\n    \\u2022 First resolution attempt through amicable negotiation\\r\\n    \\u2022 Escalation to legal proceedings only if negotiation fails\\r\\n    \\u2022 Both parties agree to attempt mediation before litigation\",\"contractor_info\":\"SYSTEM PROVIDER & SUPPORT\\r\\n\\r\\nProvider: Inventory Management System Team\\r\\nRegistration: Government of Pakistan - Business Registration\\r\\n\\r\\nPRIMARY CONTACT\\r\\nEmail: support@inventorysystem.pk\\r\\nPhone: +92-300-1234567\\r\\nWhatsApp: +92-300-1234567\\r\\nOffice Hours: 9:00 AM - 6:00 PM (Monday-Friday, PST)\\r\\n\\r\\nMAILING ADDRESS\\r\\nInventory Management Solutions (Pvt) Ltd\\r\\nTech Park, Sector I-8\\/2\\r\\nIslamabad, Pakistan 44000\\r\\n\\r\\nPAYMENT DETAILS\\r\\nBank Account Name: Inventory Management Solutions (Pvt) Ltd\\r\\nBank Name: Habib Bank Limited (HBL)\\r\\nAccount Number: XX-XXXX-XXXX-XXXX\\r\\nIBAN: PK36HBLC0001234567890123\\r\\nBranch: Blue Area, Islamabad\\r\\n\\r\\nFor Jazz Cash \\/ Easypaisa:\\r\\nMobile Number: 03001234567 (Registered as Inventory Systems)\\r\\n\\r\\nAll payments should include invoice number or contract reference in the remarks\\/memo field for quick reconciliation.\",\"full_description\":\"INVENTORY MANAGEMENT SYSTEM - PROFESSIONAL SUBSCRIPTION AGREEMENT\\r\\n\\r\\nThis is a comprehensive service agreement for the use of the Inventory Management System, a cloud-based inventory tracking and management solution designed for businesses operating in Pakistan. The system provides real-time inventory monitoring, automated invoicing, multi-warehouse management, and detailed financial reporting capabilities.\\r\\n\\r\\nThe subscription is billed on a monthly basis at PKR 5,000 per month with a mandatory 15-day grace period for payment processing. All invoices include applicable GST at current rates and are issued automatically on the first day of each billing cycle.\\r\\n\\r\\nThe system adheres to Pakistani regulatory standards including GST compliance (FBR), data protection laws, and PTA regulations for financial transactions. Regular automatic backups ensure business continuity, and 24-hour support is available for critical issues.\\r\\n\\r\\nUsers have full access to all features including unlimited invoice creation, multi-user accounts, real-time reporting, and API integrations. Payment proofs can be uploaded directly through the system for quick verification by administrators, ensuring minimal system downtime.\\r\\n\\r\\nThis agreement remains valid for the duration of the subscription and auto-renews monthly unless cancelled with 30 days written notice. All terms and conditions are subject to Pakistani law and the jurisdiction of Islamabad courts.\\r\\n\\r\\nFor inquiries or support, contact the Support Team through the contact details provided in the system\'s Help section.\",\"updated_at\":\"2026-07-22 12:50:22\",\"updated_by\":1}','2026-07-22','2026-07-22 17:50:22'),(56,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.24012.1 Chrome/148.0.7778.280 Electron/42.7.0 Safari/537.36 MSIX','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 17:57:44'),(57,'Updated system contract: Test Contract - System Plan','update','1','system',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.24012.1 Chrome/148.0.7778.280 Electron/42.7.0 Safari/537.36 MSIX','{\"contract_name\":\"Test Contract - System Plan\",\"contract_type\":\"monthly\",\"contractor_name\":\"Inventory Management Solutions (Pvt) Ltd\",\"contractor_cnic\":\"12345-6789012-3\",\"contractor_phone\":\"+92-318-5657457\",\"contractor_email\":\"support@inventorysystem.pk\",\"contractor_address\":\"Tech Park, Sector I-8\\/2, Islamabad, Pakistan 44000\",\"installation_date\":\"2026-07-22\",\"contract_start_date\":\"2026-07-22\",\"contract_end_date\":\"\",\"monthly_charges\":5000,\"yearly_charges\":0,\"monthly_due_date\":1,\"maximum_extension_days\":15,\"system_status\":\"active\",\"contract_description\":\"SYSTEM SUBSCRIPTION CONTRACT\\r\\n\\r\\nType: Monthly Subscription Agreement\\r\\nValid From: 2026-07-22\\r\\n\\r\\nThis contract outlines the terms and conditions for the use of the Inventory Management System (IMS) on a monthly subscription basis. The subscription charges cover system access, data storage, technical support, and regular maintenance updates as per the agreed schedule.\\r\\n\\r\\nMonthly Subscription Fee: PKR 5,000 (Five Thousand Rupees Only)\\r\\nPayment Due Date: 1st of every month\\r\\nGrace Period: 15 days (until 15th of the month)\\r\\n\\r\\nThe system will remain fully operational during normal business hours (9:00 AM to 6:00 PM PST) with scheduled maintenance windows on alternate Sundays from 11:00 PM to 1:00 AM.\",\"policy_description\":\"PAYMENT POLICY & TERMS\\r\\n\\r\\n1. PAYMENT TERMS\\r\\n   \\u2022 Monthly subscription charges are due on or before the 1st day of each month\\r\\n   \\u2022 Payment currency: Pakistani Rupees (PKR)\\r\\n   \\u2022 Standard billing cycle: 30 days per invoice\\r\\n\\r\\n2. ACCEPTED PAYMENT METHODS\\r\\n   \\u2022 Bank Transfer\\/Online Fund Transfer (within Pakistan)\\r\\n   \\u2022 Cheque (crossed cheque to be sent to registered office)\\r\\n   \\u2022 Bank Draft\\r\\n   \\u2022 Cash deposit to designated account\\r\\n   \\u2022 Jazz Cash \\/ Easypaisa (for amounts below PKR 50,000)\\r\\n   \\u2022 HBL PayWave \\/ UBL Omni (for online payments)\\r\\n\\r\\n3. GRACE PERIOD\\r\\n   \\u2022 15 days extension period is provided beyond the due date (until 15th of month)\\r\\n   \\u2022 No system suspension during grace period\\r\\n   \\u2022 Late payment may incur 2% additional charges after grace period expires\\r\\n\\r\\n4. SERVICE SUSPENSION POLICY\\r\\n   \\u2022 If payment is not received within the grace period (after 15th of month):\\r\\n     - System access will be restricted for all regular users\\r\\n     - Only Super Administrator will retain full system access\\r\\n     - All other users will be locked out until payment is verified\\r\\n     - Dashboard will display pending payment notifications\\r\\n\\r\\n5. PAYMENT VERIFICATION PROCESS\\r\\n   \\u2022 Users can upload payment proof documents through the system\\r\\n   \\u2022 Accepted proof formats: Bank confirmation, transaction receipt, cheque image, payment slip\\r\\n   \\u2022 Proof is verified by Super Administrator within 24 business hours\\r\\n   \\u2022 Once verified, system access is immediately restored for all users\\r\\n\\r\\n6. INVOICE GENERATION\\r\\n   \\u2022 Monthly invoices are automatically generated by the system\\r\\n   \\u2022 Invoices can be downloaded in PDF format from Payment Management dashboard\\r\\n   \\u2022 Each invoice includes GST (if applicable) calculated at 17% on the base amount\\r\\n   \\u2022 Invoices are valid receipts for accounting and audit purposes\\r\\n\\r\\n7. REFUND POLICY\\r\\n   \\u2022 No refunds for partial months - subscription charges are for full calendar month\\r\\n   \\u2022 Unused days at the end of subscription are non-refundable\\r\\n   \\u2022 System termination requires written notice 30 days in advance\\r\\n   \\u2022 Advance payments are adjustable against future invoices\\r\\n\\r\\n8. RENEWAL & DISCONTINUATION\\r\\n   \\u2022 Automatic monthly renewal unless cancelled in writing\\r\\n   \\u2022 Cancellation must be submitted by the 20th of the current billing month\\r\\n   \\u2022 Data will be retained for 30 days after subscription expiry\\r\\n   \\u2022 Extended data retention beyond 30 days may incur storage charges\\r\\n\\r\\n9. SUPPORT & MAINTENANCE\\r\\n   \\u2022 Technical support available during business hours (9 AM - 6 PM, Monday-Friday)\\r\\n   \\u2022 Non-critical issues: 2-3 business days response time\\r\\n   \\u2022 Critical issues: Same business day response (within 4 hours)\\r\\n   \\u2022 System updates and patches deployed with advance notice\\r\\n   \\u2022 Scheduled maintenance: Alternate Sundays 11 PM - 1 AM (no system access)\\r\\n\\r\\n10. COMPLIANCE & REGULATIONS\\r\\n    \\u2022 System operates in compliance with Data Protection Act, 2018\\r\\n    \\u2022 User data is stored on secure servers with AES-256 encryption\\r\\n    \\u2022 Regular backups maintained for data recovery purposes\\r\\n    \\u2022 PTA compliance for financial transaction logging maintained\\r\\n    \\u2022 GST registration and compliance as per FBR regulations\\r\\n\\r\\n11. DISPUTE RESOLUTION\\r\\n    \\u2022 All disputes shall be subject to jurisdiction of courts in Islamabad\\r\\n    \\u2022 First resolution attempt through amicable negotiation\\r\\n    \\u2022 Escalation to legal proceedings only if negotiation fails\\r\\n    \\u2022 Both parties agree to attempt mediation before litigation\",\"contractor_info\":\"SYSTEM PROVIDER & SUPPORT\\r\\n\\r\\nProvider: Inventory Management System Team\\r\\nRegistration: Government of Pakistan - Business Registration\\r\\n\\r\\nPRIMARY CONTACT\\r\\nEmail: support@inventorysystem.pk\\r\\nPhone: +92-300-1234567\\r\\nWhatsApp: +92-300-1234567\\r\\nOffice Hours: 9:00 AM - 6:00 PM (Monday-Friday, PST)\\r\\n\\r\\nMAILING ADDRESS\\r\\nInventory Management Solutions (Pvt) Ltd\\r\\nTech Park, Sector I-8\\/2\\r\\nIslamabad, Pakistan 44000\\r\\n\\r\\nPAYMENT DETAILS\\r\\nBank Account Name: Inventory Management Solutions (Pvt) Ltd\\r\\nBank Name: Habib Bank Limited (HBL)\\r\\nAccount Number: XX-XXXX-XXXX-XXXX\\r\\nIBAN: PK36HBLC0001234567890123\\r\\nBranch: Blue Area, Islamabad\\r\\n\\r\\nFor Jazz Cash \\/ Easypaisa:\\r\\nMobile Number: 03001234567 (Registered as Inventory Systems)\\r\\n\\r\\nAll payments should include invoice number or contract reference in the remarks\\/memo field for quick reconciliation.\",\"full_description\":\"INVENTORY MANAGEMENT SYSTEM - PROFESSIONAL SUBSCRIPTION AGREEMENT\\r\\n\\r\\nThis is a comprehensive service agreement for the use of the Inventory Management System, a cloud-based inventory tracking and management solution designed for businesses operating in Pakistan. The system provides real-time inventory monitoring, automated invoicing, multi-warehouse management, and detailed financial reporting capabilities.\\r\\n\\r\\nThe subscription is billed on a monthly basis at PKR 5,000 per month with a mandatory 15-day grace period for payment processing. All invoices include applicable GST at current rates and are issued automatically on the first day of each billing cycle.\\r\\n\\r\\nThe system adheres to Pakistani regulatory standards including GST compliance (FBR), data protection laws, and PTA regulations for financial transactions. Regular automatic backups ensure business continuity, and 24-hour support is available for critical issues.\\r\\n\\r\\nUsers have full access to all features including unlimited invoice creation, multi-user accounts, real-time reporting, and API integrations. Payment proofs can be uploaded directly through the system for quick verification by administrators, ensuring minimal system downtime.\\r\\n\\r\\nThis agreement remains valid for the duration of the subscription and auto-renews monthly unless cancelled with 30 days written notice. All terms and conditions are subject to Pakistani law and the jurisdiction of Islamabad courts.\\r\\n\\r\\nFor inquiries or support, contact the Support Team through the contact details provided in the system\'s Help section.\",\"updated_at\":\"2026-07-22 12:59:07\",\"updated_by\":1}','2026-07-22','2026-07-22 17:59:07'),(58,'Updated system contract: Updated Pakistan Professional System Plan - 2026','update','1','system',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.24012.1 Chrome/148.0.7778.280 Electron/42.7.0 Safari/537.36 MSIX','{\"contract_name\":\"Updated Pakistan Professional System Plan - 2026\",\"contract_type\":\"monthly\",\"contractor_name\":\"Inventory Management Solutions (Pvt) Ltd\",\"contractor_cnic\":\"12345-6789012-3\",\"contractor_phone\":\"+92-318-5657457\",\"contractor_email\":\"support@inventorysystem.pk\",\"contractor_address\":\"Tech Park, Sector I-8\\/2, Islamabad, Pakistan 44000\",\"installation_date\":\"2026-07-22\",\"contract_start_date\":\"2026-07-22\",\"contract_end_date\":\"\",\"monthly_charges\":5000,\"yearly_charges\":0,\"monthly_due_date\":1,\"maximum_extension_days\":15,\"system_status\":\"active\",\"contract_description\":\"SYSTEM SUBSCRIPTION CONTRACT\\r\\n\\r\\nType: Monthly Subscription Agreement\\r\\nValid From: 2026-07-22\\r\\n\\r\\nThis contract outlines the terms and conditions for the use of the Inventory Management System (IMS) on a monthly subscription basis. The subscription charges cover system access, data storage, technical support, and regular maintenance updates as per the agreed schedule.\\r\\n\\r\\nMonthly Subscription Fee: PKR 5,000 (Five Thousand Rupees Only)\\r\\nPayment Due Date: 1st of every month\\r\\nGrace Period: 15 days (until 15th of the month)\\r\\n\\r\\nThe system will remain fully operational during normal business hours (9:00 AM to 6:00 PM PST) with scheduled maintenance windows on alternate Sundays from 11:00 PM to 1:00 AM.\",\"policy_description\":\"PAYMENT POLICY & TERMS\\r\\n\\r\\n1. PAYMENT TERMS\\r\\n   \\u2022 Monthly subscription charges are due on or before the 1st day of each month\\r\\n   \\u2022 Payment currency: Pakistani Rupees (PKR)\\r\\n   \\u2022 Standard billing cycle: 30 days per invoice\\r\\n\\r\\n2. ACCEPTED PAYMENT METHODS\\r\\n   \\u2022 Bank Transfer\\/Online Fund Transfer (within Pakistan)\\r\\n   \\u2022 Cheque (crossed cheque to be sent to registered office)\\r\\n   \\u2022 Bank Draft\\r\\n   \\u2022 Cash deposit to designated account\\r\\n   \\u2022 Jazz Cash \\/ Easypaisa (for amounts below PKR 50,000)\\r\\n   \\u2022 HBL PayWave \\/ UBL Omni (for online payments)\\r\\n\\r\\n3. GRACE PERIOD\\r\\n   \\u2022 15 days extension period is provided beyond the due date (until 15th of month)\\r\\n   \\u2022 No system suspension during grace period\\r\\n   \\u2022 Late payment may incur 2% additional charges after grace period expires\\r\\n\\r\\n4. SERVICE SUSPENSION POLICY\\r\\n   \\u2022 If payment is not received within the grace period (after 15th of month):\\r\\n     - System access will be restricted for all regular users\\r\\n     - Only Super Administrator will retain full system access\\r\\n     - All other users will be locked out until payment is verified\\r\\n     - Dashboard will display pending payment notifications\\r\\n\\r\\n5. PAYMENT VERIFICATION PROCESS\\r\\n   \\u2022 Users can upload payment proof documents through the system\\r\\n   \\u2022 Accepted proof formats: Bank confirmation, transaction receipt, cheque image, payment slip\\r\\n   \\u2022 Proof is verified by Super Administrator within 24 business hours\\r\\n   \\u2022 Once verified, system access is immediately restored for all users\\r\\n\\r\\n6. INVOICE GENERATION\\r\\n   \\u2022 Monthly invoices are automatically generated by the system\\r\\n   \\u2022 Invoices can be downloaded in PDF format from Payment Management dashboard\\r\\n   \\u2022 Each invoice includes GST (if applicable) calculated at 17% on the base amount\\r\\n   \\u2022 Invoices are valid receipts for accounting and audit purposes\\r\\n\\r\\n7. REFUND POLICY\\r\\n   \\u2022 No refunds for partial months - subscription charges are for full calendar month\\r\\n   \\u2022 Unused days at the end of subscription are non-refundable\\r\\n   \\u2022 System termination requires written notice 30 days in advance\\r\\n   \\u2022 Advance payments are adjustable against future invoices\\r\\n\\r\\n8. RENEWAL & DISCONTINUATION\\r\\n   \\u2022 Automatic monthly renewal unless cancelled in writing\\r\\n   \\u2022 Cancellation must be submitted by the 20th of the current billing month\\r\\n   \\u2022 Data will be retained for 30 days after subscription expiry\\r\\n   \\u2022 Extended data retention beyond 30 days may incur storage charges\\r\\n\\r\\n9. SUPPORT & MAINTENANCE\\r\\n   \\u2022 Technical support available during business hours (9 AM - 6 PM, Monday-Friday)\\r\\n   \\u2022 Non-critical issues: 2-3 business days response time\\r\\n   \\u2022 Critical issues: Same business day response (within 4 hours)\\r\\n   \\u2022 System updates and patches deployed with advance notice\\r\\n   \\u2022 Scheduled maintenance: Alternate Sundays 11 PM - 1 AM (no system access)\\r\\n\\r\\n10. COMPLIANCE & REGULATIONS\\r\\n    \\u2022 System operates in compliance with Data Protection Act, 2018\\r\\n    \\u2022 User data is stored on secure servers with AES-256 encryption\\r\\n    \\u2022 Regular backups maintained for data recovery purposes\\r\\n    \\u2022 PTA compliance for financial transaction logging maintained\\r\\n    \\u2022 GST registration and compliance as per FBR regulations\\r\\n\\r\\n11. DISPUTE RESOLUTION\\r\\n    \\u2022 All disputes shall be subject to jurisdiction of courts in Islamabad\\r\\n    \\u2022 First resolution attempt through amicable negotiation\\r\\n    \\u2022 Escalation to legal proceedings only if negotiation fails\\r\\n    \\u2022 Both parties agree to attempt mediation before litigation\",\"contractor_info\":\"SYSTEM PROVIDER & SUPPORT\\r\\n\\r\\nProvider: Inventory Management System Team\\r\\nRegistration: Government of Pakistan - Business Registration\\r\\n\\r\\nPRIMARY CONTACT\\r\\nEmail: support@inventorysystem.pk\\r\\nPhone: +92-300-1234567\\r\\nWhatsApp: +92-300-1234567\\r\\nOffice Hours: 9:00 AM - 6:00 PM (Monday-Friday, PST)\\r\\n\\r\\nMAILING ADDRESS\\r\\nInventory Management Solutions (Pvt) Ltd\\r\\nTech Park, Sector I-8\\/2\\r\\nIslamabad, Pakistan 44000\\r\\n\\r\\nPAYMENT DETAILS\\r\\nBank Account Name: Inventory Management Solutions (Pvt) Ltd\\r\\nBank Name: Habib Bank Limited (HBL)\\r\\nAccount Number: XX-XXXX-XXXX-XXXX\\r\\nIBAN: PK36HBLC0001234567890123\\r\\nBranch: Blue Area, Islamabad\\r\\n\\r\\nFor Jazz Cash \\/ Easypaisa:\\r\\nMobile Number: 03001234567 (Registered as Inventory Systems)\\r\\n\\r\\nAll payments should include invoice number or contract reference in the remarks\\/memo field for quick reconciliation.\",\"full_description\":\"INVENTORY MANAGEMENT SYSTEM - PROFESSIONAL SUBSCRIPTION AGREEMENT\\r\\n\\r\\nThis is a comprehensive service agreement for the use of the Inventory Management System, a cloud-based inventory tracking and management solution designed for businesses operating in Pakistan. The system provides real-time inventory monitoring, automated invoicing, multi-warehouse management, and detailed financial reporting capabilities.\\r\\n\\r\\nThe subscription is billed on a monthly basis at PKR 5,000 per month with a mandatory 15-day grace period for payment processing. All invoices include applicable GST at current rates and are issued automatically on the first day of each billing cycle.\\r\\n\\r\\nThe system adheres to Pakistani regulatory standards including GST compliance (FBR), data protection laws, and PTA regulations for financial transactions. Regular automatic backups ensure business continuity, and 24-hour support is available for critical issues.\\r\\n\\r\\nUsers have full access to all features including unlimited invoice creation, multi-user accounts, real-time reporting, and API integrations. Payment proofs can be uploaded directly through the system for quick verification by administrators, ensuring minimal system downtime.\\r\\n\\r\\nThis agreement remains valid for the duration of the subscription and auto-renews monthly unless cancelled with 30 days written notice. All terms and conditions are subject to Pakistani law and the jurisdiction of Islamabad courts.\\r\\n\\r\\nFor inquiries or support, contact the Support Team through the contact details provided in the system\'s Help section.\",\"updated_at\":\"2026-07-22 13:00:38\",\"updated_by\":1}','2026-07-22','2026-07-22 18:00:38'),(59,'Updated system contract: Updated Pakistan Professional System Plan - 2026','update','1','system',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"contract_name\":\"Updated Pakistan Professional System Plan - 2026\",\"contract_type\":\"monthly\",\"contractor_name\":\"Inventory Management Solutions (Pvt) Ltd\",\"contractor_cnic\":\"12345-6789012-4\",\"contractor_phone\":\"+92-318-5657457\",\"contractor_email\":\"support@inventorysystem.pk\",\"contractor_address\":\"Tech Park, Sector I-8\\/2, Islamabad, Pakistan 44000\",\"installation_date\":\"2026-07-22\",\"contract_start_date\":\"2026-07-22\",\"contract_end_date\":\"\",\"monthly_charges\":5000,\"yearly_charges\":0,\"monthly_due_date\":1,\"maximum_extension_days\":15,\"system_status\":\"active\",\"contract_description\":\"SYSTEM SUBSCRIPTION CONTRACT\\r\\n\\r\\nType: Monthly Subscription Agreement\\r\\nValid From: 2026-07-22\\r\\n\\r\\nThis contract outlines the terms and conditions for the use of the Inventory Management System (IMS) on a monthly subscription basis. The subscription charges cover system access, data storage, technical support, and regular maintenance updates as per the agreed schedule.\\r\\n\\r\\nMonthly Subscription Fee: PKR 5,000 (Five Thousand Rupees Only)\\r\\nPayment Due Date: 1st of every month\\r\\nGrace Period: 15 days (until 15th of the month)\\r\\n\\r\\nThe system will remain fully operational during normal business hours (9:00 AM to 6:00 PM PST) with scheduled maintenance windows on alternate Sundays from 11:00 PM to 1:00 AM.\",\"policy_description\":\"PAYMENT POLICY & TERMS\\r\\n\\r\\n1. PAYMENT TERMS\\r\\n   \\u2022 Monthly subscription charges are due on or before the 1st day of each month\\r\\n   \\u2022 Payment currency: Pakistani Rupees (PKR)\\r\\n   \\u2022 Standard billing cycle: 30 days per invoice\\r\\n\\r\\n2. ACCEPTED PAYMENT METHODS\\r\\n   \\u2022 Bank Transfer\\/Online Fund Transfer (within Pakistan)\\r\\n   \\u2022 Cheque (crossed cheque to be sent to registered office)\\r\\n   \\u2022 Bank Draft\\r\\n   \\u2022 Cash deposit to designated account\\r\\n   \\u2022 Jazz Cash \\/ Easypaisa (for amounts below PKR 50,000)\\r\\n   \\u2022 HBL PayWave \\/ UBL Omni (for online payments)\\r\\n\\r\\n3. GRACE PERIOD\\r\\n   \\u2022 15 days extension period is provided beyond the due date (until 15th of month)\\r\\n   \\u2022 No system suspension during grace period\\r\\n   \\u2022 Late payment may incur 2% additional charges after grace period expires\\r\\n\\r\\n4. SERVICE SUSPENSION POLICY\\r\\n   \\u2022 If payment is not received within the grace period (after 15th of month):\\r\\n     - System access will be restricted for all regular users\\r\\n     - Only Super Administrator will retain full system access\\r\\n     - All other users will be locked out until payment is verified\\r\\n     - Dashboard will display pending payment notifications\\r\\n\\r\\n5. PAYMENT VERIFICATION PROCESS\\r\\n   \\u2022 Users can upload payment proof documents through the system\\r\\n   \\u2022 Accepted proof formats: Bank confirmation, transaction receipt, cheque image, payment slip\\r\\n   \\u2022 Proof is verified by Super Administrator within 24 business hours\\r\\n   \\u2022 Once verified, system access is immediately restored for all users\\r\\n\\r\\n6. INVOICE GENERATION\\r\\n   \\u2022 Monthly invoices are automatically generated by the system\\r\\n   \\u2022 Invoices can be downloaded in PDF format from Payment Management dashboard\\r\\n   \\u2022 Each invoice includes GST (if applicable) calculated at 17% on the base amount\\r\\n   \\u2022 Invoices are valid receipts for accounting and audit purposes\\r\\n\\r\\n7. REFUND POLICY\\r\\n   \\u2022 No refunds for partial months - subscription charges are for full calendar month\\r\\n   \\u2022 Unused days at the end of subscription are non-refundable\\r\\n   \\u2022 System termination requires written notice 30 days in advance\\r\\n   \\u2022 Advance payments are adjustable against future invoices\\r\\n\\r\\n8. RENEWAL & DISCONTINUATION\\r\\n   \\u2022 Automatic monthly renewal unless cancelled in writing\\r\\n   \\u2022 Cancellation must be submitted by the 20th of the current billing month\\r\\n   \\u2022 Data will be retained for 30 days after subscription expiry\\r\\n   \\u2022 Extended data retention beyond 30 days may incur storage charges\\r\\n\\r\\n9. SUPPORT & MAINTENANCE\\r\\n   \\u2022 Technical support available during business hours (9 AM - 6 PM, Monday-Friday)\\r\\n   \\u2022 Non-critical issues: 2-3 business days response time\\r\\n   \\u2022 Critical issues: Same business day response (within 4 hours)\\r\\n   \\u2022 System updates and patches deployed with advance notice\\r\\n   \\u2022 Scheduled maintenance: Alternate Sundays 11 PM - 1 AM (no system access)\\r\\n\\r\\n10. COMPLIANCE & REGULATIONS\\r\\n    \\u2022 System operates in compliance with Data Protection Act, 2018\\r\\n    \\u2022 User data is stored on secure servers with AES-256 encryption\\r\\n    \\u2022 Regular backups maintained for data recovery purposes\\r\\n    \\u2022 PTA compliance for financial transaction logging maintained\\r\\n    \\u2022 GST registration and compliance as per FBR regulations\\r\\n\\r\\n11. DISPUTE RESOLUTION\\r\\n    \\u2022 All disputes shall be subject to jurisdiction of courts in Islamabad\\r\\n    \\u2022 First resolution attempt through amicable negotiation\\r\\n    \\u2022 Escalation to legal proceedings only if negotiation fails\\r\\n    \\u2022 Both parties agree to attempt mediation before litigation\",\"contractor_info\":\"SYSTEM PROVIDER & SUPPORT\\r\\n\\r\\nProvider: Inventory Management System Team\\r\\nRegistration: Government of Pakistan - Business Registration\\r\\n\\r\\nPRIMARY CONTACT\\r\\nEmail: support@inventorysystem.pk\\r\\nPhone: +92-300-1234567\\r\\nWhatsApp: +92-300-1234567\\r\\nOffice Hours: 9:00 AM - 6:00 PM (Monday-Friday, PST)\\r\\n\\r\\nMAILING ADDRESS\\r\\nInventory Management Solutions (Pvt) Ltd\\r\\nTech Park, Sector I-8\\/2\\r\\nIslamabad, Pakistan 44000\\r\\n\\r\\nPAYMENT DETAILS\\r\\nBank Account Name: Inventory Management Solutions (Pvt) Ltd\\r\\nBank Name: Habib Bank Limited (HBL)\\r\\nAccount Number: XX-XXXX-XXXX-XXXX\\r\\nIBAN: PK36HBLC0001234567890123\\r\\nBranch: Blue Area, Islamabad\\r\\n\\r\\nFor Jazz Cash \\/ Easypaisa:\\r\\nMobile Number: 03001234567 (Registered as Inventory Systems)\\r\\n\\r\\nAll payments should include invoice number or contract reference in the remarks\\/memo field for quick reconciliation.\",\"full_description\":\"INVENTORY MANAGEMENT SYSTEM - PROFESSIONAL SUBSCRIPTION AGREEMENT\\r\\n\\r\\nThis is a comprehensive service agreement for the use of the Inventory Management System, a cloud-based inventory tracking and management solution designed for businesses operating in Pakistan. The system provides real-time inventory monitoring, automated invoicing, multi-warehouse management, and detailed financial reporting capabilities.\\r\\n\\r\\nThe subscription is billed on a monthly basis at PKR 5,000 per month with a mandatory 15-day grace period for payment processing. All invoices include applicable GST at current rates and are issued automatically on the first day of each billing cycle.\\r\\n\\r\\nThe system adheres to Pakistani regulatory standards including GST compliance (FBR), data protection laws, and PTA regulations for financial transactions. Regular automatic backups ensure business continuity, and 24-hour support is available for critical issues.\\r\\n\\r\\nUsers have full access to all features including unlimited invoice creation, multi-user accounts, real-time reporting, and API integrations. Payment proofs can be uploaded directly through the system for quick verification by administrators, ensuring minimal system downtime.\\r\\n\\r\\nThis agreement remains valid for the duration of the subscription and auto-renews monthly unless cancelled with 30 days written notice. All terms and conditions are subject to Pakistani law and the jurisdiction of Islamabad courts.\\r\\n\\r\\nFor inquiries or support, contact the Support Team through the contact details provided in the system\'s Help section.\",\"updated_at\":\"2026-07-22 13:03:17\",\"updated_by\":1}','2026-07-22','2026-07-22 18:03:17'),(60,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.24012.1 Chrome/148.0.7778.280 Electron/42.7.0 Safari/537.36 MSIX','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 18:08:17'),(61,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 18:08:22'),(62,'Truncated all invoice and payment data','delete','0','system',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"action\":\"truncate_all_data\"}','2026-07-22','2026-07-22 18:09:02'),(63,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 18:11:20'),(64,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.24012.1 Chrome/148.0.7778.280 Electron/42.7.0 Safari/537.36 MSIX','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 18:11:36'),(65,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-22','2026-07-22 13:13:14'),(66,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.24012.1 Chrome/148.0.7778.280 Electron/42.7.0 Safari/537.36 MSIX','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 18:19:00'),(67,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 19:25:39'),(68,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 19:26:06'),(69,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 19:26:23'),(70,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 19:30:16'),(71,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 19:31:35'),(72,'Reset password for user ID: 667','update','667','user',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"action\":\"password_reset\"}','2026-07-22','2026-07-22 19:39:36'),(73,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 19:39:42'),(74,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 19:39:55'),(75,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 19:51:19'),(76,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 19:52:07'),(77,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 19:52:11'),(78,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 19:55:54'),(79,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 19:56:01'),(80,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 19:56:44'),(81,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 19:57:50'),(82,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 19:57:51'),(83,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 19:57:52'),(84,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:04:36'),(85,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:04:44'),(86,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:04:49'),(87,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:05:25'),(88,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:13:20'),(89,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:13:55'),(90,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:14:03'),(91,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:14:20'),(92,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:17:01'),(93,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:41:03'),(94,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:41:14'),(95,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:41:39'),(96,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:41:47'),(97,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:41:48'),(98,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:42:04'),(99,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:42:06'),(100,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:42:12'),(101,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:42:53'),(102,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 21:50:33'),(103,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 22:01:24'),(104,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 22:02:12'),(105,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 22:02:14'),(106,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 22:02:27'),(107,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 22:02:30'),(108,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 22:02:35'),(109,'Accessed Purchase Dashboard','view',NULL,'Purchase',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"type\":\"purchase_dashboard_view\"}','2026-07-22','2026-07-22 17:07:01'),(110,'Viewed purchase orders list','view',NULL,'Purchase',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"type\":\"purchase_orders_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":2}','2026-07-22','2026-07-22 17:07:01'),(111,'Viewed purchase orders list','view',NULL,'Purchase',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"type\":\"purchase_orders_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":2}','2026-07-22','2026-07-22 17:07:10'),(112,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-22','2026-07-22 17:24:48'),(113,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-22','2026-07-22 17:25:05'),(114,'Submitted payment for invoice #2','create','2','payment',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"action\":\"payment_submitted\",\"comments\":\"Amount Paid\"}','2026-07-22','2026-07-22 23:32:54'),(115,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-22','2026-07-22 23:33:47'),(116,'Submitted payment for invoice #2','create','2','payment',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"action\":\"payment_submitted\",\"comments\":\"wq\"}','2026-07-22','2026-07-22 23:34:16'),(117,'Submitted payment for invoice #2','create','2','payment',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"action\":\"payment_submitted\",\"comments\":\"test\"}','2026-07-22','2026-07-22 23:38:27'),(118,'Submitted payment for invoice #2','create','2','payment',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"action\":\"payment_submitted\",\"comments\":\"Done\"}','2026-07-22','2026-07-22 23:43:18'),(119,'Submitted payment for invoice #2','create','2','payment',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"action\":\"payment_submitted\",\"comments\":\"test\"}','2026-07-23','2026-07-23 00:01:33'),(120,'Approved payment for invoice #2','update','2','payment',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"action\":\"payment_approved\",\"admin_comments\":\"Done & Verified\"}','2026-07-23','2026-07-23 00:06:49'),(121,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 10:58:03'),(122,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:07:22'),(123,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:07:27'),(124,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.24012.1 Chrome/148.0.7778.280 Electron/42.7.0 Safari/537.36 MSIX','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:08:29'),(125,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.24012.1 Chrome/148.0.7778.280 Electron/42.7.0 Safari/537.36 MSIX','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:08:54'),(126,'Truncated all invoice and payment data','delete','0','system',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"action\":\"truncate_all_data\"}','2026-07-23','2026-07-23 11:19:02'),(127,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:19:24'),(128,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:21:06'),(129,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:23:59'),(130,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:24:03'),(131,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:24:15'),(132,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.24012.1 Chrome/148.0.7778.280 Electron/42.7.0 Safari/537.36 MSIX','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:24:23'),(133,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:24:33'),(134,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.24012.1 Chrome/148.0.7778.280 Electron/42.7.0 Safari/537.36 MSIX','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:26:49'),(135,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.24012.1 Chrome/148.0.7778.280 Electron/42.7.0 Safari/537.36 MSIX','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:30:26'),(136,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:30:35'),(137,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:30:41'),(138,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.24012.1 Chrome/148.0.7778.280 Electron/42.7.0 Safari/537.36 MSIX','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:31:06'),(139,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.24012.1 Chrome/148.0.7778.280 Electron/42.7.0 Safari/537.36 MSIX','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:31:35'),(140,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:31:37'),(141,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.24012.1 Chrome/148.0.7778.280 Electron/42.7.0 Safari/537.36 MSIX','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:31:46'),(142,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.24012.1 Chrome/148.0.7778.280 Electron/42.7.0 Safari/537.36 MSIX','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:31:52'),(143,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:32:19'),(144,'Reset password for user ID: 1','update','1','user',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"action\":\"password_reset\"}','2026-07-23','2026-07-23 11:32:48'),(145,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:32:53'),(146,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:32:55'),(147,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:33:06'),(148,'Truncated all invoice and payment data','delete','0','system',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"action\":\"truncate_all_data\"}','2026-07-23','2026-07-23 11:33:16'),(149,'Updated system contract: Default System Contract','update','2','system',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"contract_name\":\"Default System Contract\",\"contract_type\":\"monthly\",\"contractor_name\":\"Company Administrator\",\"contractor_cnic\":\"00000-0000000-0\",\"contractor_phone\":\"+1-555-0000\",\"contractor_email\":\"admin@example.com\",\"contractor_address\":\"Your Company Address\",\"installation_date\":\"2026-07-22\",\"contract_start_date\":\"2026-07-22\",\"contract_end_date\":\"\",\"monthly_charges\":5000,\"yearly_charges\":60000,\"monthly_due_date\":1,\"maximum_extension_days\":15,\"system_status\":\"active\",\"contract_description\":\"This is the default system contract for the inventory management system.\\r\\n\\r\\nContract Type: Monthly Subscription\\r\\nMonthly Charges: Applicable as per the agreement.\\r\\nPayment Terms: Due on the specified date each month.\\r\\nExtension Policy: 15 days grace period allowed beyond due date.\\r\\n\",\"policy_description\":\"PAYMENT POLICY\\r\\n\\r\\n1. Payment Terms: Monthly subscription charges are due on or before the specified due date.\\r\\n2. Payment Methods: Bank transfer, online payment, or check.\\r\\n3. Grace Period: A maximum of 15 days extension is provided beyond the due date.\\r\\n4. Service Suspension: If payment is not received within the extended period, system access will be restricted to Super Admin only.\\r\\n5. Proof of Payment: Upload proof of payment through the system for verification.\\r\\n6. Invoice Generation: Monthly invoices are automatically generated and can be downloaded from the system.\\r\\n7. Refund Policy: No refunds for partial months. Cancellation requires 30 days notice.\\r\\n\",\"contractor_info\":\"System Administrator\\r\\nEmail: admin@example.com\\r\\nPhone: +1-555-0000\\r\\nAddress: Your Company Address\\r\\n\",\"full_description\":\"This is the system contract governing the use of the inventory management system. All users must comply with the payment terms and conditions outlined herein.\",\"updated_at\":\"2026-07-23 06:33:42\",\"updated_by\":1}','2026-07-23','2026-07-23 11:33:42'),(150,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:33:59'),(151,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:34:03'),(152,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:34:08'),(153,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:36:45'),(154,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:43:53'),(155,'Accessed Purchase Dashboard','view',NULL,'Purchase',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-23','2026-07-23 06:44:02'),(156,'Accessed Purchase Dashboard','view',NULL,'Purchase',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-23','2026-07-23 06:46:25'),(157,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:46:27'),(158,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:46:40'),(159,'Accessed Purchase Dashboard','view',NULL,'Purchase',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-23','2026-07-23 06:49:29'),(160,'Viewed purchase orders list','view',NULL,'Purchase',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_orders_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":2}','2026-07-23','2026-07-23 06:49:31'),(161,'Viewed goods receiving list','view',NULL,'Purchase',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"goods_receiving_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":2}','2026-07-23','2026-07-23 06:49:33'),(162,'Accessed Purchase Dashboard','view',NULL,'Purchase',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-23','2026-07-23 06:49:34'),(163,'Accessed Sales Dashboard','view',NULL,'Sales',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-23','2026-07-23 06:49:37'),(164,'Viewed sales orders list','view',NULL,'Sales',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":300}','2026-07-23','2026-07-23 06:49:39'),(165,'Viewed sales invoices list','view',NULL,'Sales',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_invoices_view\",\"filters\":{\"customer\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":300}','2026-07-23','2026-07-23 06:49:42'),(166,'Viewed pending sales orders','view',NULL,'Sales',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"pending_orders_view\",\"page\":1,\"total_records\":0}','2026-07-23','2026-07-23 06:49:45'),(167,'Accessed Sales Dashboard','view',NULL,'Sales',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-23','2026-07-23 06:49:57'),(168,'Accessed Purchase Dashboard','view',NULL,'Purchase',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-23','2026-07-23 06:49:58'),(169,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:50:34'),(170,'Viewed inventory dashboard','view',NULL,'Inventory',667,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:56:51'),(171,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:59:12'),(172,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:59:17'),(173,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 11:59:20'),(174,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-23','2026-07-23 06:59:46'),(175,'Viewed sales orders list','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":300}','2026-07-23','2026-07-23 06:59:48'),(176,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-23','2026-07-23 06:59:57'),(177,'Truncate Sales Records - Deleted all sale records including sales orders, invoices, payment history, POS sales, returns, and transactions','Truncate',NULL,'Sales',1,'::1',NULL,NULL,'2026-07-23','2026-07-23 07:00:04'),(178,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-23','2026-07-23 07:00:05'),(179,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-23','2026-07-23 07:00:06'),(180,'Viewed sales orders list','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":0}','2026-07-23','2026-07-23 07:00:09'),(181,'Viewed sales invoices list','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_invoices_view\",\"filters\":{\"customer\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":0}','2026-07-23','2026-07-23 07:00:10'),(182,'Viewed pending sales orders','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"pending_orders_view\",\"page\":1,\"total_records\":0}','2026-07-23','2026-07-23 07:00:10'),(183,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-23','2026-07-23 07:00:13'),(184,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 12:00:28'),(185,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 19:01:52'),(186,'Truncated all invoice and payment data','delete','0','system',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"action\":\"truncate_all_data\"}','2026-07-23','2026-07-23 19:03:13'),(187,'Truncated all invoice and payment data','delete','0','system',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"action\":\"truncate_all_data\"}','2026-07-23','2026-07-23 19:04:04'),(188,'Updated system contract: Contract 1','update','1','system',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"contract_name\":\"Contract 1\",\"contract_type\":\"yearly\",\"contractor_name\":\"Contractor 1\",\"contractor_cnic\":\"\",\"contractor_phone\":\"03021854710\",\"contractor_email\":\"contractor1@example.com\",\"contractor_address\":\"\",\"installation_date\":\"\",\"contract_start_date\":\"2025-01-01\",\"contract_end_date\":\"2026-01-01\",\"monthly_charges\":5000,\"yearly_charges\":60000,\"monthly_due_date\":1,\"maximum_extension_days\":15,\"system_status\":\"active\",\"contract_description\":\"\",\"policy_description\":\"\",\"contractor_info\":\"\",\"full_description\":\"\",\"updated_at\":\"2026-07-23 14:04:20\",\"updated_by\":1}','2026-07-23','2026-07-23 19:04:20'),(189,'Truncated all invoice and payment data','delete','0','system',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"action\":\"truncate_all_data\"}','2026-07-23','2026-07-23 19:04:39'),(190,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 19:22:50'),(191,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 19:23:49'),(192,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 19:23:55'),(193,'Created new user: inventory_admin','create','2','user',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"username\":\"inventory_admin\",\"email\":\"inventoryadmin@gmail.com\"}','2026-07-23','2026-07-23 19:25:21'),(194,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-23','2026-07-23 14:25:41'),(195,'Updated system contract: Auto Parts Supply Agreement','update','1','system',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"contract_name\":\"Auto Parts Supply Agreement\",\"contract_type\":\"monthly\",\"contractor_name\":\"Pak Auto Parts Traders\",\"contractor_cnic\":\"37405-1234567-1\",\"contractor_phone\":\"+923185657457\",\"contractor_email\":\"sales@pakautoparts.com\",\"contractor_address\":\"Shop #12, Auto Market, Main Murree Road Bharakaho, Islamabad, Pakistan\",\"installation_date\":\"2026-08-01\",\"contract_start_date\":\"2026-08-01\",\"contract_end_date\":\"2027-07-31\",\"monthly_charges\":5000,\"yearly_charges\":60000,\"monthly_due_date\":5,\"maximum_extension_days\":10,\"system_status\":\"active\",\"contract_description\":\"Annual agreement for the supply of genuine and aftermarket automobile spare parts including engine, suspension, brake, electrical, and body components.\",\"policy_description\":\"The supplier shall provide quality auto parts with manufacturer warranty where applicable. Defective items may be returned within 7 days. Payment is due within 10 days of invoice. Delivery shall be completed within 48 hours for in-stock items.\",\"contractor_info\":\"Primary Contact: Muhammad Usman (Sales Manager), Phone: +92-321-5551234, Email: sales@pakautoparts.com\",\"full_description\":\"This contract covers the regular supply of automobile spare parts for Japanese, Korean, and local vehicles. Items include oil filters, air filters, brake pads, clutch plates, spark plugs, engine oil, suspension parts, batteries, belts, radiators, headlights, and other genuine or approved aftermarket components. Pricing will remain fixed during the contract period unless mutually revised in writing. The supplier agrees to maintain adequate stock levels and provide prompt replacement of defective products.\",\"updated_at\":\"2026-07-23 14:29:43\",\"updated_by\":1}','2026-07-23','2026-07-23 19:29:43'),(196,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-23','2026-07-23 14:37:55'),(197,'Viewed purchase orders list','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_orders_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":2}','2026-07-23','2026-07-23 14:37:55'),(198,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-23','2026-07-23 14:38:08'),(199,'Viewed sales invoices list','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_invoices_view\",\"filters\":{\"customer\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":0}','2026-07-23','2026-07-23 14:38:09'),(200,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-23','2026-07-23 14:38:13'),(201,'Viewed purchase invoices list','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_invoices_view\",\"filters\":{\"supplier\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":150}','2026-07-23','2026-07-23 14:38:13'),(202,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 19:50:34'),(203,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 19:50:47'),(204,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 19:50:48'),(205,'Submitted payment for invoice #1','create','1','payment',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"action\":\"payment_submitted\",\"comments\":\"Easypaisa Payment\"}','2026-07-23','2026-07-23 19:51:17'),(206,'Approved payment for invoice #1','update','1','payment',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"action\":\"payment_approved\",\"admin_comments\":\"Payment Verified and Recieved\"}','2026-07-23','2026-07-23 19:51:53'),(207,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 19:51:59'),(208,'Truncated all invoice and payment data','delete','0','system',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"action\":\"truncate_all_data\"}','2026-07-23','2026-07-23 20:05:31'),(209,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 20:09:26'),(210,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 20:12:58'),(211,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 21:55:14'),(212,'Submitted payment for invoice #2','create','2','payment',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"action\":\"payment_submitted\",\"comments\":\"Paid Via Easypaisa!\"}','2026-07-23','2026-07-23 21:56:05'),(213,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 21:56:16'),(214,'Approved payment for invoice #2','update','2','payment',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"action\":\"payment_approved\",\"admin_comments\":\"Invalid Transaction Id, fix it\"}','2026-07-23','2026-07-23 22:03:13'),(215,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 22:11:40'),(216,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 22:23:10'),(217,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 22:33:24'),(218,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 22:48:56'),(219,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 22:49:22'),(220,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 23:24:01'),(221,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 23:43:35'),(222,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-23','2026-07-23 23:49:35'),(223,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"type\":\"purchase_dashboard_view\"}','2026-07-23','2026-07-23 18:57:07'),(224,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"type\":\"sales_dashboard_view\"}','2026-07-23','2026-07-23 18:57:19'),(225,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"type\":\"purchase_dashboard_view\"}','2026-07-23','2026-07-23 19:00:35'),(226,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"type\":\"purchase_dashboard_view\"}','2026-07-23','2026-07-23 19:05:04'),(227,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"type\":\"sales_dashboard_view\"}','2026-07-23','2026-07-23 19:05:09'),(228,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-23','2026-07-23 19:05:16'),(229,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-23','2026-07-23 19:10:16'),(230,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-24','2026-07-24 09:35:38'),(231,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-24','2026-07-24 04:35:43'),(232,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-24','2026-07-24 10:09:07'),(233,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-24','2026-07-24 10:09:56'),(234,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-24','2026-07-24 05:10:03'),(235,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"module\":\"dashboard\"}','2026-07-24','2026-07-24 15:31:53'),(236,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"type\":\"sales_dashboard_view\"}','2026-07-24','2026-07-24 10:32:10'),(237,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','{\"type\":\"purchase_dashboard_view\"}','2026-07-24','2026-07-24 10:32:12'),(238,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-24','2026-07-24 21:16:36'),(239,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-24','2026-07-24 16:16:50'),(240,'Viewed purchase orders list','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_orders_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":2}','2026-07-24','2026-07-24 16:16:54'),(241,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-24','2026-07-24 21:16:57'),(242,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-24','2026-07-24 16:17:40'),(243,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-24','2026-07-24 16:17:42'),(244,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-24','2026-07-24 16:18:02'),(245,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-24','2026-07-24 22:39:24'),(246,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-24','2026-07-24 17:40:08'),(247,'Viewed goods receiving list','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"goods_receiving_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":2}','2026-07-24','2026-07-24 17:40:09'),(248,'Viewed purchase invoices list','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_invoices_view\",\"filters\":{\"supplier\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":150}','2026-07-24','2026-07-24 17:40:22'),(249,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-24','2026-07-24 17:40:46'),(250,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-24','2026-07-24 17:40:46'),(251,'Viewed sales orders list','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":0}','2026-07-24','2026-07-24 17:40:48'),(252,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-24','2026-07-24 22:41:24'),(253,'Truncated all invoice and payment data','delete','0','system',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"action\":\"truncate_all_data\"}','2026-07-24','2026-07-24 22:42:37'),(254,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-24','2026-07-24 22:42:52'),(255,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-24','2026-07-24 22:43:16'),(256,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-24','2026-07-24 17:43:38'),(257,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-24','2026-07-24 17:43:43'),(258,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-24','2026-07-24 22:44:22'),(259,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-24','2026-07-24 22:45:18'),(260,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-24','2026-07-24 22:48:35'),(261,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-24','2026-07-24 22:49:08'),(262,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 08:52:58'),(263,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 03:53:06'),(264,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 03:53:17'),(265,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 08:53:29'),(266,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 03:53:32'),(267,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 03:58:10'),(268,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 08:59:02'),(269,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 08:59:09'),(270,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 08:59:18'),(271,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 08:59:44'),(272,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 09:00:34'),(273,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 09:01:37'),(274,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 09:01:44'),(275,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 09:06:14'),(276,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:06:18'),(277,'Truncate Purchase Records - Deleted all purchase records including purchase orders, invoices, payment history, goods receiving, returns, and related items','Truncate',NULL,'Purchase',1,'::1',NULL,NULL,'2026-07-25','2026-07-25 04:06:24'),(278,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:06:25'),(279,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 04:06:29'),(280,'Truncate Sales Records - Deleted all sale records including sales orders, invoices, payment history, POS sales, returns, and transactions','Truncate',NULL,'Sales',1,'::1',NULL,NULL,'2026-07-25','2026-07-25 04:06:36'),(281,'Accessed Sales Dashboard','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 04:06:37'),(282,'Viewed sales orders list','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":0}','2026-07-25','2026-07-25 04:06:39'),(283,'Viewed sales invoices list','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_invoices_view\",\"filters\":{\"customer\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":0}','2026-07-25','2026-07-25 04:06:40'),(284,'Viewed pending sales orders','view',NULL,'Sales',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"pending_orders_view\",\"page\":1,\"total_records\":0}','2026-07-25','2026-07-25 04:06:40'),(285,'Accessed Purchase Dashboard','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:06:46'),(286,'Viewed purchase orders list','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_orders_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":0}','2026-07-25','2026-07-25 04:06:47'),(287,'Viewed goods receiving list','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"goods_receiving_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":0}','2026-07-25','2026-07-25 04:06:48'),(288,'Viewed purchase invoices list','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_invoices_view\",\"filters\":{\"supplier\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":0}','2026-07-25','2026-07-25 04:06:48'),(289,'Viewed pending purchase orders','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"pending_purchase_orders_view\",\"page\":1,\"total_records\":0}','2026-07-25','2026-07-25 04:06:49'),(290,'Viewed approved purchase orders','view',NULL,'Purchase',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"approved_purchase_orders_view\",\"page\":1,\"total_records\":0}','2026-07-25','2026-07-25 04:06:50'),(291,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 09:09:42'),(292,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 04:10:07'),(293,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 09:10:10'),(294,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:10:37'),(295,'Viewed purchase orders list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_orders_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":0}','2026-07-25','2026-07-25 04:10:37'),(296,'Created purchase order: PO-20260725042939481','create','1','Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_order_create\",\"supplier_id\":\"21\",\"grand_total\":\"52000.00\",\"po_status\":\"Draft\"}','2026-07-25','2026-07-25 04:29:39'),(297,'Viewed purchase orders list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_orders_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:29:40'),(298,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 09:29:43'),(299,'Viewed goods receiving list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"goods_receiving_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:29:55'),(300,'Viewed purchase invoices list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_invoices_view\",\"filters\":{\"supplier\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:30:01'),(301,'Viewed purchase invoices list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_invoices_view\",\"filters\":{\"supplier\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:30:30'),(302,'Viewed goods receiving list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"goods_receiving_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:30:31'),(303,'Viewed goods receiving list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"goods_receiving_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:30:50'),(304,'Viewed purchase orders list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_orders_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:30:51'),(305,'Viewed purchase invoices list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_invoices_view\",\"filters\":{\"supplier\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:30:52'),(306,'Viewed goods receiving list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"goods_receiving_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:30:53'),(307,'Viewed purchase orders list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_orders_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:30:53'),(308,'Updated purchase order status to: Approved','update','1','Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_order_status_update\",\"new_status\":\"Approved\"}','2026-07-25','2026-07-25 04:31:12'),(309,'Viewed purchase orders list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_orders_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:31:12'),(310,'Viewed purchase invoices list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_invoices_view\",\"filters\":{\"supplier\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:31:13'),(311,'Viewed pending purchase orders','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"pending_purchase_orders_view\",\"page\":1,\"total_records\":0}','2026-07-25','2026-07-25 04:31:15'),(312,'Viewed approved purchase orders','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"approved_purchase_orders_view\",\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:31:16'),(313,'Completed purchase order: PO#1','update','1','Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_order_complete\"}','2026-07-25','2026-07-25 04:31:18'),(314,'Viewed approved purchase orders','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"approved_purchase_orders_view\",\"page\":1,\"total_records\":0}','2026-07-25','2026-07-25 04:31:19'),(315,'Viewed pending purchase orders','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"pending_purchase_orders_view\",\"page\":1,\"total_records\":0}','2026-07-25','2026-07-25 04:31:21'),(316,'Viewed purchase invoices list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_invoices_view\",\"filters\":{\"supplier\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:31:21'),(317,'Viewed goods receiving list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"goods_receiving_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:31:22'),(318,'Viewed purchase orders list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_orders_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:31:23'),(319,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 09:31:24'),(320,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 09:31:28'),(321,'Viewed goods receiving list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"goods_receiving_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:36:32'),(322,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:36:33'),(323,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 04:36:44'),(324,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":0}','2026-07-25','2026-07-25 04:36:47'),(325,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:38:47'),(326,'Viewed sales invoices list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_invoices_view\",\"filters\":{\"customer\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:38:49'),(327,'Viewed sales invoices list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_invoices_view\",\"filters\":{\"customer\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:39:18'),(328,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:39:19'),(329,'Viewed pending sales orders','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"pending_orders_view\",\"page\":1,\"total_records\":0}','2026-07-25','2026-07-25 04:39:29'),(330,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:39:33'),(331,'Updated sales order status to: Confirmed','update','1','Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_order_status_update\",\"new_status\":\"Confirmed\"}','2026-07-25','2026-07-25 04:39:40'),(332,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:39:41'),(333,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 04:41:28'),(334,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:41:29'),(335,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:41:37'),(336,'Viewed sales invoices list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_invoices_view\",\"filters\":{\"customer\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:41:41'),(337,'Viewed pending sales orders','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"pending_orders_view\",\"page\":1,\"total_records\":0}','2026-07-25','2026-07-25 04:41:42'),(338,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 04:41:43'),(339,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 09:41:55'),(340,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 09:42:29'),(341,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:42:43'),(342,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:53:38'),(343,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:53:41'),(344,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:53:44'),(345,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:53:44'),(346,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:53:45'),(347,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:53:46'),(348,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:53:46'),(349,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:53:47'),(350,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:53:47'),(351,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:53:48'),(352,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:53:48'),(353,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:53:48'),(354,'Accessed Purchase Dashboard','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_dashboard_view\"}','2026-07-25','2026-07-25 04:53:48'),(355,'Viewed purchase orders list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"purchase_orders_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:53:50'),(356,'Viewed goods receiving list','view',NULL,'Purchase',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"goods_receiving_view\",\"filters\":{\"supplier\":\"\",\"warehouse\":\"\",\"status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:53:54'),(357,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 04:53:57'),(358,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:53:58'),(359,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 04:55:59'),(360,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 04:56:22'),(361,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 04:57:29'),(362,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:57:30'),(363,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 04:57:57'),(364,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 04:57:58'),(365,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 05:08:32'),(366,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 05:08:32'),(367,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 05:08:37'),(368,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 05:08:37'),(369,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 05:10:21'),(370,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 05:10:24'),(371,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 05:10:46'),(372,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 05:10:49'),(373,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 05:12:27'),(374,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 05:12:31'),(375,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 05:12:32'),(376,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 05:13:07'),(377,'Viewed sales orders list','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_orders_view\",\"filters\":{\"customer\":\"\",\"warehouse\":\"\",\"status\":\"\",\"payment_status\":\"\"},\"page\":1,\"total_records\":1}','2026-07-25','2026-07-25 05:13:11'),(378,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 05:13:13'),(379,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 05:13:29'),(380,'Accessed Sales Dashboard','view',NULL,'Sales',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"type\":\"sales_dashboard_view\"}','2026-07-25','2026-07-25 05:20:39'),(381,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 10:20:57'),(382,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 10:48:51'),(383,'Viewed inventory dashboard','view',NULL,'Inventory',2,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 10:50:35'),(384,'Viewed inventory dashboard','view',NULL,'Inventory',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','{\"module\":\"dashboard\"}','2026-07-25','2026-07-25 10:50:42');
/*!40000 ALTER TABLE `activitylogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `backup_test`
--

DROP TABLE IF EXISTS `backup_test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `backup_test` (
  `id` int NOT NULL,
  `test_data` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup_test`
--

LOCK TABLES `backup_test` WRITE;
/*!40000 ALTER TABLE `backup_test` DISABLE KEYS */;
INSERT INTO `backup_test` VALUES (999,'MODIFIED DATA');
/*!40000 ALTER TABLE `backup_test` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `front_cms_settings`
--

DROP TABLE IF EXISTS `front_cms_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `front_cms_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sidebar_option` enum('News','Complain') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'News',
  `language_id` int DEFAULT NULL,
  `rtl_mode` tinyint(1) DEFAULT '0',
  `logo_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `favicon_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `footer_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `cookie_consent` tinyint(1) DEFAULT '0',
  `google_analytics_script` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `whatsapp_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `facebook_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `twitter_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `youtube_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `google_plus_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `linkedin_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `instagram_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pinterest_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `school_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `language_id` (`language_id`),
  KEY `fk_front_cms_settings_school` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `front_cms_settings`
--

LOCK TABLES `front_cms_settings` WRITE;
/*!40000 ALTER TABLE `front_cms_settings` DISABLE KEYS */;
INSERT INTO `front_cms_settings` VALUES (1,'News',38,1,'path/to/logo.png','path/to/favicon.png','Super School 2024-25 ',1,'<script async src=\"https://www.googletagmanager.com/gtag/js?id=GA_TRACKING_ID\"></script>©','https://www.whatsapp.com/a','https://www.facebook.com/a','https://twitter.com/a','https://www.youtube.com/a','https://plus.google.com/a','https://www.linkedin.com/a','https://www.instagram.com/a','https://in.pinterest.com/a','2024-10-18 12:38:32','2025-03-13 12:51:24',1);
/*!40000 ALTER TABLE `front_cms_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_accounts`
--

DROP TABLE IF EXISTS `inventory_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_accounts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT NULL,
  `account_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `account_name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `account_type` enum('Asset','Liability','Equity','Income','Expense') COLLATE utf8mb4_general_ci NOT NULL,
  `opening_balance` decimal(15,2) DEFAULT '0.00',
  `current_balance` decimal(15,2) DEFAULT '0.00',
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_code` (`account_code`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `inventory_accounts_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `inventory_accounts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_accounts`
--

LOCK TABLES `inventory_accounts` WRITE;
/*!40000 ALTER TABLE `inventory_accounts` DISABLE KEYS */;
INSERT INTO `inventory_accounts` VALUES (1,NULL,'1000','Cash','Asset',0.00,76500.00,NULL,'2026-07-14 19:01:00','2026-07-25 09:39:17',NULL,1,1,0),(2,NULL,'1010','Bank','Asset',0.00,0.00,NULL,'2026-07-14 19:01:00','2026-07-21 15:48:23',NULL,1,1,0),(3,NULL,'1100','Inventory','Asset',0.00,0.00,NULL,'2026-07-14 19:01:00','2026-07-21 15:48:23',NULL,1,1,0),(4,NULL,'2000','Accounts Payable','Liability',0.00,44000.00,NULL,'2026-07-14 19:01:00','2026-07-22 10:10:06',NULL,1,1,0),(5,NULL,'2100','Accounts Receivable','Asset',0.00,0.00,NULL,'2026-07-14 19:01:00','2026-07-21 15:48:23',NULL,1,1,0),(6,NULL,'3000','Capital','Equity',0.00,0.00,NULL,'2026-07-14 19:01:00','2026-07-21 15:48:23',NULL,1,1,0),(7,NULL,'4000','Parts Sales','Income',0.00,0.00,NULL,'2026-07-14 19:01:00','2026-07-21 15:48:23',NULL,1,1,0),(8,NULL,'4010','Accessories Sales','Income',0.00,0.00,NULL,'2026-07-14 19:01:00','2026-07-21 15:48:23',NULL,1,1,0),(9,NULL,'4020','Oil Sales','Income',0.00,0.00,NULL,'2026-07-14 19:01:00','2026-07-21 15:48:23',NULL,1,1,0),(10,NULL,'5000','Cost of Goods Sold','Expense',0.00,0.00,NULL,'2026-07-14 19:01:00','2026-07-21 15:48:23',NULL,1,1,0),(11,NULL,'5100','Purchases','Expense',0.00,139000.00,NULL,'2026-07-14 19:01:00','2026-07-22 10:10:06',NULL,1,1,0),(12,NULL,'5200','Operating Expenses','Expense',0.00,0.00,NULL,'2026-07-14 19:01:00','2026-07-21 15:48:23',NULL,1,1,0),(14,NULL,'1200','Accounts Receivable','Asset',0.00,-111000.00,NULL,'2026-07-21 00:04:32','2026-07-25 09:39:17',NULL,1,1,0);
/*!40000 ALTER TABLE `inventory_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_brands`
--

DROP TABLE IF EXISTS `inventory_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `brand_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_brands`
--

LOCK TABLES `inventory_brands` WRITE;
/*!40000 ALTER TABLE `inventory_brands` DISABLE KEYS */;
INSERT INTO `inventory_brands` VALUES (1,'Samsung',NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(2,'LG',NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(3,'Sony',NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(4,'Apple',NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(5,'Dell',NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(6,'HP',NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(7,'Asus',NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(8,'Lenovo',NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(9,'IKEA',NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(10,'Premier',NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(11,'Standard',NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(12,'Elite',NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(13,'Pro',NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0);
/*!40000 ALTER TABLE `inventory_brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_categories`
--

DROP TABLE IF EXISTS `inventory_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT NULL,
  `category_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `category_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_code` (`category_code`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_categories`
--

LOCK TABLES `inventory_categories` WRITE;
/*!40000 ALTER TABLE `inventory_categories` DISABLE KEYS */;
INSERT INTO `inventory_categories` VALUES (1,NULL,'Electronics',NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(2,NULL,'Furniture',NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(3,NULL,'Supplies',NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(4,NULL,'Equipment',NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(5,NULL,'Appliances',NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(6,NULL,'Hardware',NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(7,NULL,'Software',NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(8,NULL,'Accessories',NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(9,NULL,'Tools',NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(10,NULL,'Materials',NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0);
/*!40000 ALTER TABLE `inventory_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_customer_contacts`
--

DROP TABLE IF EXISTS `inventory_customer_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_customer_contacts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `contact_name` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `designation` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobile` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `inventory_customer_contacts_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `inventory_customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_customer_contacts`
--

LOCK TABLES `inventory_customer_contacts` WRITE;
/*!40000 ALTER TABLE `inventory_customer_contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_customer_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_customers`
--

DROP TABLE IF EXISTS `inventory_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `customer_type` enum('Individual','Company') COLLATE utf8mb4_general_ci DEFAULT 'Individual',
  `company_name` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobile` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tax_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `credit_limit` decimal(15,2) DEFAULT '0.00',
  `opening_balance` decimal(15,2) DEFAULT '0.00',
  `current_balance` decimal(15,2) DEFAULT '0.00',
  `payment_terms` int DEFAULT '0',
  `address` text COLLATE utf8mb4_general_ci,
  `city` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_code` (`customer_code`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_customers`
--

LOCK TABLES `inventory_customers` WRITE;
/*!40000 ALTER TABLE `inventory_customers` DISABLE KEYS */;
INSERT INTO `inventory_customers` VALUES (1,'CUST-00001','Individual','Company 1','Customer','Name 1','customer1@example.com','042-2979798','03014878187',NULL,0.00,0.00,0.00,0,'Address 1','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(2,'CUST-00002','','Company 2','Customer','Name 2','customer2@example.com','042-9453018','03095947166',NULL,0.00,0.00,0.00,0,'Address 2','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(3,'CUST-00003','Individual','Company 3','Customer','Name 3','customer3@example.com','042-4907259','03013124755',NULL,0.00,0.00,0.00,0,'Address 3','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(4,'CUST-00004','','Company 4','Customer','Name 4','customer4@example.com','042-2817522','03094586004',NULL,0.00,0.00,0.00,0,'Address 4','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(5,'CUST-00005','Individual','Company 5','Customer','Name 5','customer5@example.com','042-3150299','03042988045',NULL,0.00,0.00,0.00,0,'Address 5','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(6,'CUST-00006','','Company 6','Customer','Name 6','customer6@example.com','042-2034137','03096961309',NULL,0.00,0.00,0.00,0,'Address 6','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(7,'CUST-00007','Individual','Company 7','Customer','Name 7','customer7@example.com','042-4424247','03020069034',NULL,0.00,0.00,0.00,0,'Address 7','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(8,'CUST-00008','','Company 8','Customer','Name 8','customer8@example.com','042-3909756','03012315400',NULL,0.00,0.00,0.00,0,'Address 8','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(9,'CUST-00009','Individual','Company 9','Customer','Name 9','customer9@example.com','042-9374559','03064190432',NULL,0.00,0.00,0.00,0,'Address 9','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(10,'CUST-00010','','Company 10','Customer','Name 10','customer10@example.com','042-9412786','03064058708',NULL,0.00,0.00,0.00,0,'Address 10','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(11,'CUST-00011','Individual','Company 11','Customer','Name 11','customer11@example.com','042-8998930','03081940046',NULL,0.00,0.00,0.00,0,'Address 11','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(12,'CUST-00012','','Company 12','Customer','Name 12','customer12@example.com','042-5446738','03082287338',NULL,0.00,0.00,0.00,0,'Address 12','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(13,'CUST-00013','Individual','Company 13','Customer','Name 13','customer13@example.com','042-2067775','03044767786',NULL,0.00,0.00,0.00,0,'Address 13','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(14,'CUST-00014','','Company 14','Customer','Name 14','customer14@example.com','042-5982423','03043103573',NULL,0.00,0.00,0.00,0,'Address 14','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(15,'CUST-00015','Individual','Company 15','Customer','Name 15','customer15@example.com','042-4867864','03098362627',NULL,0.00,0.00,0.00,0,'Address 15','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(16,'CUST-00016','','Company 16','Customer','Name 16','customer16@example.com','042-4877138','03075241700',NULL,0.00,0.00,0.00,0,'Address 16','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(17,'CUST-00017','Individual','Company 17','Customer','Name 17','customer17@example.com','042-3812259','03088832365',NULL,0.00,0.00,0.00,0,'Address 17','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(18,'CUST-00018','','Company 18','Customer','Name 18','customer18@example.com','042-9967110','03042776527',NULL,0.00,0.00,0.00,0,'Address 18','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(19,'CUST-00019','Individual','Company 19','Customer','Name 19','customer19@example.com','042-9631187','03070235859',NULL,0.00,0.00,0.00,0,'Address 19','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(20,'CUST-00020','','Company 20','Customer','Name 20','customer20@example.com','042-5833613','03074267475',NULL,0.00,0.00,0.00,0,'Address 20','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(21,'CUST-00021','Individual','Company 21','Customer','Name 21','customer21@example.com','042-5075633','03071870873',NULL,0.00,0.00,0.00,0,'Address 21','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(22,'CUST-00022','','Company 22','Customer','Name 22','customer22@example.com','042-6577950','03067345514',NULL,0.00,0.00,0.00,0,'Address 22','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(23,'CUST-00023','Individual','Company 23','Customer','Name 23','customer23@example.com','042-9480151','03015892070',NULL,0.00,0.00,0.00,0,'Address 23','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(24,'CUST-00024','','Company 24','Customer','Name 24','customer24@example.com','042-6288657','03033495811',NULL,0.00,0.00,0.00,0,'Address 24','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(25,'CUST-00025','Individual','Company 25','Customer','Name 25','customer25@example.com','042-6598460','03064428576',NULL,0.00,0.00,0.00,0,'Address 25','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(26,'CUST-00026','','Company 26','Customer','Name 26','customer26@example.com','042-8459198','03076421425',NULL,0.00,0.00,0.00,0,'Address 26','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(27,'CUST-00027','Individual','Company 27','Customer','Name 27','customer27@example.com','042-2483244','03029224481',NULL,0.00,0.00,0.00,0,'Address 27','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(28,'CUST-00028','','Company 28','Customer','Name 28','customer28@example.com','042-1886925','03086026505',NULL,0.00,0.00,0.00,0,'Address 28','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(29,'CUST-00029','Individual','Company 29','Customer','Name 29','customer29@example.com','042-9768563','03055516914',NULL,0.00,0.00,0.00,0,'Address 29','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(30,'CUST-00030','','Company 30','Customer','Name 30','customer30@example.com','042-8310044','03047362848',NULL,0.00,0.00,0.00,0,'Address 30','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(31,'CUST-00031','Individual','Company 31','Customer','Name 31','customer31@example.com','042-2992900','03082089294',NULL,0.00,0.00,0.00,0,'Address 31','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(32,'CUST-00032','','Company 32','Customer','Name 32','customer32@example.com','042-5273136','03035559478',NULL,0.00,0.00,0.00,0,'Address 32','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(33,'CUST-00033','Individual','Company 33','Customer','Name 33','customer33@example.com','042-1764474','03064954933',NULL,0.00,0.00,0.00,0,'Address 33','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(34,'CUST-00034','','Company 34','Customer','Name 34','customer34@example.com','042-5012270','03070165526',NULL,0.00,0.00,0.00,0,'Address 34','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(35,'CUST-00035','Individual','Company 35','Customer','Name 35','customer35@example.com','042-6353493','03092658134',NULL,0.00,0.00,0.00,0,'Address 35','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(36,'CUST-00036','','Company 36','Customer','Name 36','customer36@example.com','042-2746529','03035692859',NULL,0.00,0.00,0.00,0,'Address 36','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(37,'CUST-00037','Individual','Company 37','Customer','Name 37','customer37@example.com','042-5906962','03042223189',NULL,0.00,0.00,0.00,0,'Address 37','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(38,'CUST-00038','','Company 38','Customer','Name 38','customer38@example.com','042-7618532','03011219457',NULL,0.00,0.00,0.00,0,'Address 38','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(39,'CUST-00039','Individual','Company 39','Customer','Name 39','customer39@example.com','042-3566530','03021551477',NULL,0.00,0.00,0.00,0,'Address 39','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(40,'CUST-00040','','Company 40','Customer','Name 40','customer40@example.com','042-8820454','03032162026',NULL,0.00,0.00,0.00,0,'Address 40','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(41,'CUST-00041','Individual','Company 41','Customer','Name 41','customer41@example.com','042-6747947','03018974124',NULL,0.00,0.00,0.00,0,'Address 41','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(42,'CUST-00042','','Company 42','Customer','Name 42','customer42@example.com','042-2411920','03068808946',NULL,0.00,0.00,0.00,0,'Address 42','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(43,'CUST-00043','Individual','Company 43','Customer','Name 43','customer43@example.com','042-6148815','03067398180',NULL,0.00,0.00,0.00,0,'Address 43','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(44,'CUST-00044','','Company 44','Customer','Name 44','customer44@example.com','042-1613444','03029859597',NULL,0.00,0.00,0.00,0,'Address 44','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(45,'CUST-00045','Individual','Company 45','Customer','Name 45','customer45@example.com','042-1805322','03062408552',NULL,0.00,0.00,0.00,0,'Address 45','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(46,'CUST-00046','','Company 46','Customer','Name 46','customer46@example.com','042-9453799','03061867089',NULL,0.00,0.00,0.00,0,'Address 46','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(47,'CUST-00047','Individual','Company 47','Customer','Name 47','customer47@example.com','042-3553135','03085519591',NULL,0.00,0.00,0.00,0,'Address 47','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(48,'CUST-00048','','Company 48','Customer','Name 48','customer48@example.com','042-8377442','03052706480',NULL,0.00,0.00,0.00,0,'Address 48','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(49,'CUST-00049','Individual','Company 49','Customer','Name 49','customer49@example.com','042-8349707','03071940055',NULL,0.00,0.00,0.00,0,'Address 49','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(50,'CUST-00050','','Company 50','Customer','Name 50','customer50@example.com','042-2232874','03065095632',NULL,0.00,0.00,0.00,0,'Address 50','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(51,'CUST-00051','Individual','Company 51','Customer','Name 51','customer51@example.com','042-4739096','03052673059',NULL,0.00,0.00,0.00,0,'Address 51','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(52,'CUST-00052','','Company 52','Customer','Name 52','customer52@example.com','042-1030201','03083029195',NULL,0.00,0.00,0.00,0,'Address 52','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(53,'CUST-00053','Individual','Company 53','Customer','Name 53','customer53@example.com','042-5451895','03048884346',NULL,0.00,0.00,0.00,0,'Address 53','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(54,'CUST-00054','','Company 54','Customer','Name 54','customer54@example.com','042-5016714','03039896519',NULL,0.00,0.00,0.00,0,'Address 54','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(55,'CUST-00055','Individual','Company 55','Customer','Name 55','customer55@example.com','042-8575841','03077514646',NULL,0.00,0.00,0.00,0,'Address 55','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(56,'CUST-00056','','Company 56','Customer','Name 56','customer56@example.com','042-2973575','03092481890',NULL,0.00,0.00,0.00,0,'Address 56','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(57,'CUST-00057','Individual','Company 57','Customer','Name 57','customer57@example.com','042-2203731','03035922134',NULL,0.00,0.00,0.00,0,'Address 57','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(58,'CUST-00058','','Company 58','Customer','Name 58','customer58@example.com','042-1021875','03020458795',NULL,0.00,0.00,0.00,0,'Address 58','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(59,'CUST-00059','Individual','Company 59','Customer','Name 59','customer59@example.com','042-2353511','03019279907',NULL,0.00,0.00,0.00,0,'Address 59','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(60,'CUST-00060','','Company 60','Customer','Name 60','customer60@example.com','042-2867234','03049538196',NULL,0.00,0.00,0.00,0,'Address 60','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(61,'CUST-00061','Individual','Company 61','Customer','Name 61','customer61@example.com','042-8174253','03078183935',NULL,0.00,0.00,0.00,0,'Address 61','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(62,'CUST-00062','','Company 62','Customer','Name 62','customer62@example.com','042-5942993','03083539182',NULL,0.00,0.00,0.00,0,'Address 62','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(63,'CUST-00063','Individual','Company 63','Customer','Name 63','customer63@example.com','042-2074179','03033802155',NULL,0.00,0.00,0.00,0,'Address 63','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(64,'CUST-00064','','Company 64','Customer','Name 64','customer64@example.com','042-8194282','03032868607',NULL,0.00,0.00,0.00,0,'Address 64','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(65,'CUST-00065','Individual','Company 65','Customer','Name 65','customer65@example.com','042-1771041','03026156649',NULL,0.00,0.00,0.00,0,'Address 65','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(66,'CUST-00066','','Company 66','Customer','Name 66','customer66@example.com','042-9944918','03041051148',NULL,0.00,0.00,0.00,0,'Address 66','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(67,'CUST-00067','Individual','Company 67','Customer','Name 67','customer67@example.com','042-9114743','03077898639',NULL,0.00,0.00,0.00,0,'Address 67','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(68,'CUST-00068','','Company 68','Customer','Name 68','customer68@example.com','042-3298599','03059242329',NULL,0.00,0.00,0.00,0,'Address 68','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(69,'CUST-00069','Individual','Company 69','Customer','Name 69','customer69@example.com','042-4825075','03042254665',NULL,0.00,0.00,0.00,0,'Address 69','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(70,'CUST-00070','','Company 70','Customer','Name 70','customer70@example.com','042-2912685','03035315940',NULL,0.00,0.00,0.00,0,'Address 70','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(71,'CUST-00071','Individual','Company 71','Customer','Name 71','customer71@example.com','042-1879491','03058922605',NULL,0.00,0.00,0.00,0,'Address 71','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(72,'CUST-00072','','Company 72','Customer','Name 72','customer72@example.com','042-9598978','03022415588',NULL,0.00,0.00,0.00,0,'Address 72','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(73,'CUST-00073','Individual','Company 73','Customer','Name 73','customer73@example.com','042-7836845','03049265053',NULL,0.00,0.00,0.00,0,'Address 73','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(74,'CUST-00074','','Company 74','Customer','Name 74','customer74@example.com','042-3845250','03075591750',NULL,0.00,0.00,0.00,0,'Address 74','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(75,'CUST-00075','Individual','Company 75','Customer','Name 75','customer75@example.com','042-5026563','03064377465',NULL,0.00,0.00,0.00,0,'Address 75','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(76,'CUST-00076','','Company 76','Customer','Name 76','customer76@example.com','042-8838304','03068003895',NULL,0.00,0.00,0.00,0,'Address 76','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(77,'CUST-00077','Individual','Company 77','Customer','Name 77','customer77@example.com','042-3026764','03043799106',NULL,0.00,0.00,0.00,0,'Address 77','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(78,'CUST-00078','','Company 78','Customer','Name 78','customer78@example.com','042-8232151','03017180796',NULL,0.00,0.00,0.00,0,'Address 78','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(79,'CUST-00079','Individual','Company 79','Customer','Name 79','customer79@example.com','042-2821394','03098251040',NULL,0.00,0.00,0.00,0,'Address 79','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(80,'CUST-00080','','Company 80','Customer','Name 80','customer80@example.com','042-5492761','03020886029',NULL,0.00,0.00,0.00,0,'Address 80','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(81,'CUST-00081','Individual','Company 81','Customer','Name 81','customer81@example.com','042-9751613','03016400306',NULL,0.00,0.00,0.00,0,'Address 81','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(82,'CUST-00082','','Company 82','Customer','Name 82','customer82@example.com','042-7601393','03030251854',NULL,0.00,0.00,0.00,0,'Address 82','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(83,'CUST-00083','Individual','Company 83','Customer','Name 83','customer83@example.com','042-7915530','03049345200',NULL,0.00,0.00,0.00,0,'Address 83','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(84,'CUST-00084','','Company 84','Customer','Name 84','customer84@example.com','042-2285754','03099822151',NULL,0.00,0.00,0.00,0,'Address 84','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(85,'CUST-00085','Individual','Company 85','Customer','Name 85','customer85@example.com','042-8932536','03039404741',NULL,0.00,0.00,0.00,0,'Address 85','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(86,'CUST-00086','','Company 86','Customer','Name 86','customer86@example.com','042-6432477','03043115092',NULL,0.00,0.00,0.00,0,'Address 86','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(87,'CUST-00087','Individual','Company 87','Customer','Name 87','customer87@example.com','042-6277884','03075995755',NULL,0.00,0.00,0.00,0,'Address 87','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(88,'CUST-00088','','Company 88','Customer','Name 88','customer88@example.com','042-3643146','03091651617',NULL,0.00,0.00,0.00,0,'Address 88','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(89,'CUST-00089','Individual','Company 89','Customer','Name 89','customer89@example.com','042-3640787','03040273676',NULL,0.00,0.00,0.00,0,'Address 89','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(90,'CUST-00090','','Company 90','Customer','Name 90','customer90@example.com','042-4530183','03036660702',NULL,0.00,0.00,0.00,0,'Address 90','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(91,'CUST-00091','Individual','Company 91','Customer','Name 91','customer91@example.com','042-7958078','03018296828',NULL,0.00,0.00,0.00,0,'Address 91','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(92,'CUST-00092','','Company 92','Customer','Name 92','customer92@example.com','042-4695484','03075221146',NULL,0.00,0.00,0.00,0,'Address 92','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(93,'CUST-00093','Individual','Company 93','Customer','Name 93','customer93@example.com','042-3909416','03096066322',NULL,0.00,0.00,0.00,0,'Address 93','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(94,'CUST-00094','','Company 94','Customer','Name 94','customer94@example.com','042-9671382','03031715241',NULL,0.00,0.00,0.00,0,'Address 94','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(95,'CUST-00095','Individual','Company 95','Customer','Name 95','customer95@example.com','042-5509769','03033201768',NULL,0.00,0.00,0.00,0,'Address 95','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(96,'CUST-00096','','Company 96','Customer','Name 96','customer96@example.com','042-9329621','03041606756',NULL,0.00,0.00,0.00,0,'Address 96','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(97,'CUST-00097','Individual','Company 97','Customer','Name 97','customer97@example.com','042-8822392','03044201846',NULL,0.00,0.00,0.00,0,'Address 97','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(98,'CUST-00098','','Company 98','Customer','Name 98','customer98@example.com','042-5656464','03021401204',NULL,0.00,0.00,0.00,0,'Address 98','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(99,'CUST-00099','Individual','Company 99','Customer','Name 99','customer99@example.com','042-8352927','03075261302',NULL,0.00,0.00,0.00,0,'Address 99','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(100,'CUST-00100','','Company 100','Customer','Name 100','customer100@example.com','042-6471003','03082578126',NULL,0.00,0.00,0.00,0,'Address 100','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0);
/*!40000 ALTER TABLE `inventory_customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_email_config`
--

DROP TABLE IF EXISTS `inventory_email_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_email_config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `smtp_host` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_port` int DEFAULT '587',
  `smtp_username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_password` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `encryption` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'tls',
  `is_enabled` tinyint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_email_config`
--

LOCK TABLES `inventory_email_config` WRITE;
/*!40000 ALTER TABLE `inventory_email_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_email_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_events`
--

DROP TABLE IF EXISTS `inventory_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '#3fb50f',
  `event_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `start_datetime` (`start_datetime`),
  KEY `event_type` (`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_events`
--

LOCK TABLES `inventory_events` WRITE;
/*!40000 ALTER TABLE `inventory_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_goods_receiving`
--

DROP TABLE IF EXISTS `inventory_goods_receiving`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_goods_receiving` (
  `id` int NOT NULL AUTO_INCREMENT,
  `grn_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `purchase_order_id` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `warehouse_id` int DEFAULT NULL,
  `receiving_date` date DEFAULT NULL,
  `reference_no` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `invoice_no` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Pending','Completed','Cancelled') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_goods_receiving`
--

LOCK TABLES `inventory_goods_receiving` WRITE;
/*!40000 ALTER TABLE `inventory_goods_receiving` DISABLE KEYS */;
INSERT INTO `inventory_goods_receiving` VALUES (1,'GRN-2026-00001',1,21,1,'2026-07-30','GR-20260725043049-116399','INV-2026-00001','','Both Good Reveived.','2026-07-25 04:29:39','2026-07-25 04:31:12',1,0);
/*!40000 ALTER TABLE `inventory_goods_receiving` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_goods_receiving_items`
--

DROP TABLE IF EXISTS `inventory_goods_receiving_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_goods_receiving_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `goods_receiving_id` int NOT NULL,
  `purchase_order_item_id` int DEFAULT NULL,
  `product_id` int NOT NULL,
  `unit_id` int DEFAULT NULL,
  `ordered_quantity` decimal(18,2) DEFAULT '0.00',
  `received_quantity` decimal(18,2) DEFAULT '0.00',
  `accepted_quantity` decimal(18,2) DEFAULT '0.00',
  `rejected_quantity` decimal(18,2) DEFAULT '0.00',
  `unit_cost` decimal(18,2) DEFAULT '0.00',
  `total_amount` decimal(18,2) DEFAULT '0.00',
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `goods_receiving_id` (`goods_receiving_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_goods_receiving_items`
--

LOCK TABLES `inventory_goods_receiving_items` WRITE;
/*!40000 ALTER TABLE `inventory_goods_receiving_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_goods_receiving_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_logs`
--

DROP TABLE IF EXISTS `inventory_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_logs` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `module` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `table_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `record_id` bigint DEFAULT NULL,
  `old_data` longtext COLLATE utf8mb4_general_ci,
  `new_data` longtext COLLATE utf8mb4_general_ci,
  `ip_address` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `module` (`module`),
  KEY `table_name` (`table_name`),
  KEY `record_id` (`record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_logs`
--

LOCK TABLES `inventory_logs` WRITE;
/*!40000 ALTER TABLE `inventory_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_notifications`
--

DROP TABLE IF EXISTS `inventory_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci,
  `notification_type` enum('Info','Success','Warning','Error') COLLATE utf8mb4_general_ci DEFAULT 'Info',
  `is_read` tinyint DEFAULT '0',
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_notifications`
--

LOCK TABLES `inventory_notifications` WRITE;
/*!40000 ALTER TABLE `inventory_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_payments`
--

DROP TABLE IF EXISTS `inventory_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `payment_no` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_type` enum('Receive','Pay') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reference_type` enum('Customer','Supplier') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `payment_method` enum('Cash','Bank','Cheque','Online') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `account_id` int DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_no` (`payment_no`),
  KEY `account_id` (`account_id`),
  CONSTRAINT `inventory_payments_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `inventory_accounts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_payments`
--

LOCK TABLES `inventory_payments` WRITE;
/*!40000 ALTER TABLE `inventory_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_pos_items`
--

DROP TABLE IF EXISTS `inventory_pos_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_pos_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pos_transaction_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` decimal(15,2) DEFAULT NULL,
  `unit_price` decimal(15,2) DEFAULT NULL,
  `discount_amount` decimal(15,2) DEFAULT '0.00',
  `tax_amount` decimal(15,2) DEFAULT '0.00',
  `total_amount` decimal(15,2) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pos_transaction_id` (`pos_transaction_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `inventory_pos_items_ibfk_1` FOREIGN KEY (`pos_transaction_id`) REFERENCES `inventory_pos_transactions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_pos_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `inventory_products` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_pos_items`
--

LOCK TABLES `inventory_pos_items` WRITE;
/*!40000 ALTER TABLE `inventory_pos_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_pos_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_pos_payment_history`
--

DROP TABLE IF EXISTS `inventory_pos_payment_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_pos_payment_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pos_sales_id` int NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Cash',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pos_sales_id` (`pos_sales_id`),
  CONSTRAINT `inventory_pos_payment_history_ibfk_1` FOREIGN KEY (`pos_sales_id`) REFERENCES `inventory_pos_sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_pos_payment_history`
--

LOCK TABLES `inventory_pos_payment_history` WRITE;
/*!40000 ALTER TABLE `inventory_pos_payment_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_pos_payment_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_pos_sales`
--

DROP TABLE IF EXISTS `inventory_pos_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_pos_sales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pos_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `warehouse_id` int NOT NULL,
  `sale_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `items` json DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT '0.00',
  `discount_amount` decimal(15,2) DEFAULT '0.00',
  `tax_amount` decimal(15,2) DEFAULT '0.00',
  `grand_total` decimal(15,2) DEFAULT '0.00',
  `paid_amount` decimal(15,2) DEFAULT '0.00',
  `remaining_balance` decimal(15,2) DEFAULT '0.00',
  `change_amount` decimal(15,2) DEFAULT '0.00',
  `payment_method` enum('Cash','Card','Bank','Online') COLLATE utf8mb4_unicode_ci DEFAULT 'Cash',
  `payment_status` enum('Unpaid','Partial','Paid') COLLATE utf8mb4_unicode_ci DEFAULT 'Unpaid',
  `status` enum('Completed','Cancelled','Refunded') COLLATE utf8mb4_unicode_ci DEFAULT 'Completed',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pos_no` (`pos_no`),
  KEY `customer_id` (`customer_id`),
  KEY `warehouse_id` (`warehouse_id`),
  CONSTRAINT `inventory_pos_sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `inventory_customers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `inventory_pos_sales_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `inventory_warehouses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_pos_sales`
--

LOCK TABLES `inventory_pos_sales` WRITE;
/*!40000 ALTER TABLE `inventory_pos_sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_pos_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_pos_transactions`
--

DROP TABLE IF EXISTS `inventory_pos_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_pos_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaction_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `warehouse_id` int DEFAULT NULL,
  `transaction_date` datetime DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT '0.00',
  `discount_amount` decimal(15,2) DEFAULT '0.00',
  `tax_amount` decimal(15,2) DEFAULT '0.00',
  `grand_total` decimal(15,2) DEFAULT '0.00',
  `paid_amount` decimal(15,2) DEFAULT '0.00',
  `remaining_balance` decimal(15,2) DEFAULT '0.00',
  `payment_status` enum('Unpaid','Paid','Partial') COLLATE utf8mb4_unicode_ci DEFAULT 'Unpaid',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_no` (`transaction_no`),
  KEY `customer_id` (`customer_id`),
  KEY `warehouse_id` (`warehouse_id`),
  CONSTRAINT `inventory_pos_transactions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `inventory_customers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `inventory_pos_transactions_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `inventory_warehouses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_pos_transactions`
--

LOCK TABLES `inventory_pos_transactions` WRITE;
/*!40000 ALTER TABLE `inventory_pos_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_pos_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_products`
--

DROP TABLE IF EXISTS `inventory_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `brand_id` int DEFAULT NULL,
  `model_id` int DEFAULT NULL,
  `unit_id` int DEFAULT NULL,
  `product_name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `sku` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `barcode` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `purchase_price` decimal(15,2) DEFAULT '0.00',
  `selling_price` decimal(15,2) DEFAULT '0.00',
  `minimum_stock` decimal(15,2) DEFAULT '0.00',
  `maximum_stock` decimal(15,2) DEFAULT '0.00',
  `reorder_level` decimal(15,2) DEFAULT '0.00',
  `product_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `length` decimal(10,2) DEFAULT NULL,
  `width` decimal(10,2) DEFAULT NULL,
  `height` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  KEY `category_id` (`category_id`),
  KEY `brand_id` (`brand_id`),
  KEY `unit_id` (`unit_id`),
  CONSTRAINT `inventory_products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `inventory_categories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `inventory_products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `inventory_brands` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `inventory_products_ibfk_3` FOREIGN KEY (`unit_id`) REFERENCES `inventory_units` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=217 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_products`
--

LOCK TABLES `inventory_products` WRITE;
/*!40000 ALTER TABLE `inventory_products` DISABLE KEYS */;
INSERT INTO `inventory_products` VALUES (1,1,1,NULL,NULL,'LED Monitor 24\" - Variant 1','SKU-000001',NULL,'Product: LED Monitor 24\" - Variant 1',39166.00,10353.00,9.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(2,2,2,NULL,NULL,'LED Monitor 27\" - Variant 1','SKU-000002',NULL,'Product: LED Monitor 27\" - Variant 1',27922.00,18665.00,11.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(3,3,3,NULL,NULL,'LED Monitor 32\" - Variant 1','SKU-000003',NULL,'Product: LED Monitor 32\" - Variant 1',3341.00,36992.00,12.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(4,4,4,NULL,NULL,'Keyboard Wireless - Variant 1','SKU-000004',NULL,'Product: Keyboard Wireless - Variant 1',30416.00,32459.00,20.00,0.00,11.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(5,5,5,NULL,NULL,'Keyboard Mechanical - Variant 1','SKU-000005',NULL,'Product: Keyboard Mechanical - Variant 1',24783.00,45293.00,13.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(6,6,6,NULL,NULL,'Keyboard USB - Variant 1','SKU-000006',NULL,'Product: Keyboard USB - Variant 1',47846.00,38526.00,12.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(7,7,7,NULL,NULL,'Mouse Optical - Variant 1','SKU-000007',NULL,'Product: Mouse Optical - Variant 1',11704.00,35727.00,10.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(8,8,8,NULL,NULL,'Mouse Wireless - Variant 1','SKU-000008',NULL,'Product: Mouse Wireless - Variant 1',29386.00,52960.00,18.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(9,9,9,NULL,NULL,'Mouse Gaming - Variant 1','SKU-000009',NULL,'Product: Mouse Gaming - Variant 1',22488.00,9723.00,5.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(10,10,10,NULL,NULL,'USB Cable 1m - Variant 1','SKU-000010',NULL,'Product: USB Cable 1m - Variant 1',29820.00,46165.00,20.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(11,1,11,NULL,NULL,'USB Cable 2m - Variant 1','SKU-000011',NULL,'Product: USB Cable 2m - Variant 1',14012.00,14887.00,9.00,0.00,15.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(12,2,12,NULL,NULL,'USB Cable 5m - Variant 1','SKU-000012',NULL,'Product: USB Cable 5m - Variant 1',45507.00,46727.00,17.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(13,3,13,NULL,NULL,'HDMI Cable - Variant 1','SKU-000013',NULL,'Product: HDMI Cable - Variant 1',1756.00,28414.00,7.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(14,4,1,NULL,NULL,'Network Cable - Variant 1','SKU-000014',NULL,'Product: Network Cable - Variant 1',21834.00,43787.00,12.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(15,5,2,NULL,NULL,'Power Cable - Variant 1','SKU-000015',NULL,'Product: Power Cable - Variant 1',32849.00,25128.00,13.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(16,6,3,NULL,NULL,'Office Chair - Variant 1','SKU-000016',NULL,'Product: Office Chair - Variant 1',29737.00,14256.00,17.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(17,7,4,NULL,NULL,'Standing Desk - Variant 1','SKU-000017',NULL,'Product: Standing Desk - Variant 1',29702.00,28260.00,17.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(18,8,5,NULL,NULL,'Computer Table - Variant 1','SKU-000018',NULL,'Product: Computer Table - Variant 1',21706.00,19411.00,8.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(19,9,6,NULL,NULL,'Desk Lamp - Variant 1','SKU-000019',NULL,'Product: Desk Lamp - Variant 1',48883.00,50349.00,8.00,0.00,11.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(20,10,7,NULL,NULL,'Table Fan - Variant 1','SKU-000020',NULL,'Product: Table Fan - Variant 1',44128.00,19540.00,5.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(21,1,8,NULL,NULL,'Water Dispenser - Variant 1','SKU-000021',NULL,'Product: Water Dispenser - Variant 1',15512.00,47054.00,18.00,0.00,8.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(22,2,9,NULL,NULL,'Printer Laser - Variant 1','SKU-000022',NULL,'Product: Printer Laser - Variant 1',47665.00,36621.00,19.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(23,3,10,NULL,NULL,'Printer Inkjet - Variant 1','SKU-000023',NULL,'Product: Printer Inkjet - Variant 1',25626.00,42523.00,14.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(24,4,11,NULL,NULL,'Scanner - Variant 1','SKU-000024',NULL,'Product: Scanner - Variant 1',38850.00,8028.00,13.00,0.00,11.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(25,1,1,NULL,NULL,'LED Monitor 24\" - Variant 2','SKU-001001',NULL,'Product: LED Monitor 24\" - Variant 2',10193.00,59574.00,11.00,0.00,11.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(26,2,2,NULL,NULL,'LED Monitor 27\" - Variant 2','SKU-001002',NULL,'Product: LED Monitor 27\" - Variant 2',5000.00,52758.00,9.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(27,3,3,NULL,NULL,'LED Monitor 32\" - Variant 2','SKU-001003',NULL,'Product: LED Monitor 32\" - Variant 2',4939.00,24349.00,14.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(28,4,4,NULL,NULL,'Keyboard Wireless - Variant 2','SKU-001004',NULL,'Product: Keyboard Wireless - Variant 2',21755.00,45479.00,7.00,0.00,8.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(29,5,5,NULL,NULL,'Keyboard Mechanical - Variant 2','SKU-001005',NULL,'Product: Keyboard Mechanical - Variant 2',41095.00,36979.00,13.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(30,6,6,NULL,NULL,'Keyboard USB - Variant 2','SKU-001006',NULL,'Product: Keyboard USB - Variant 2',45299.00,14863.00,5.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(31,7,7,NULL,NULL,'Mouse Optical - Variant 2','SKU-001007',NULL,'Product: Mouse Optical - Variant 2',22455.00,42954.00,11.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(32,8,8,NULL,NULL,'Mouse Wireless - Variant 2','SKU-001008',NULL,'Product: Mouse Wireless - Variant 2',32875.00,31192.00,9.00,0.00,8.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(33,9,9,NULL,NULL,'Mouse Gaming - Variant 2','SKU-001009',NULL,'Product: Mouse Gaming - Variant 2',40399.00,43294.00,6.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(34,10,10,NULL,NULL,'USB Cable 1m - Variant 2','SKU-001010',NULL,'Product: USB Cable 1m - Variant 2',31011.00,8970.00,12.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(35,1,11,NULL,NULL,'USB Cable 2m - Variant 2','SKU-001011',NULL,'Product: USB Cable 2m - Variant 2',34990.00,42267.00,10.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(36,2,12,NULL,NULL,'USB Cable 5m - Variant 2','SKU-001012',NULL,'Product: USB Cable 5m - Variant 2',25887.00,43110.00,5.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(37,3,13,NULL,NULL,'HDMI Cable - Variant 2','SKU-001013',NULL,'Product: HDMI Cable - Variant 2',31284.00,55685.00,6.00,0.00,8.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(38,4,1,NULL,NULL,'Network Cable - Variant 2','SKU-001014',NULL,'Product: Network Cable - Variant 2',34434.00,12771.00,16.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(39,5,2,NULL,NULL,'Power Cable - Variant 2','SKU-001015',NULL,'Product: Power Cable - Variant 2',2368.00,41924.00,16.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(40,6,3,NULL,NULL,'Office Chair - Variant 2','SKU-001016',NULL,'Product: Office Chair - Variant 2',29181.00,23991.00,17.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(41,7,4,NULL,NULL,'Standing Desk - Variant 2','SKU-001017',NULL,'Product: Standing Desk - Variant 2',28645.00,14670.00,17.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(42,8,5,NULL,NULL,'Computer Table - Variant 2','SKU-001018',NULL,'Product: Computer Table - Variant 2',2320.00,39552.00,9.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(43,9,6,NULL,NULL,'Desk Lamp - Variant 2','SKU-001019',NULL,'Product: Desk Lamp - Variant 2',3309.00,42221.00,13.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(44,10,7,NULL,NULL,'Table Fan - Variant 2','SKU-001020',NULL,'Product: Table Fan - Variant 2',9230.00,51023.00,10.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(45,1,8,NULL,NULL,'Water Dispenser - Variant 2','SKU-001021',NULL,'Product: Water Dispenser - Variant 2',10852.00,2976.00,6.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(46,2,9,NULL,NULL,'Printer Laser - Variant 2','SKU-001022',NULL,'Product: Printer Laser - Variant 2',43244.00,34016.00,13.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(47,3,10,NULL,NULL,'Printer Inkjet - Variant 2','SKU-001023',NULL,'Product: Printer Inkjet - Variant 2',1904.00,40162.00,18.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(48,4,11,NULL,NULL,'Scanner - Variant 2','SKU-001024',NULL,'Product: Scanner - Variant 2',20854.00,20949.00,6.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(49,1,1,NULL,NULL,'LED Monitor 24\" - Variant 3','SKU-002001',NULL,'Product: LED Monitor 24\" - Variant 3',37171.00,33493.00,14.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(50,2,2,NULL,NULL,'LED Monitor 27\" - Variant 3','SKU-002002',NULL,'Product: LED Monitor 27\" - Variant 3',37183.00,3841.00,18.00,0.00,15.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(51,3,3,NULL,NULL,'LED Monitor 32\" - Variant 3','SKU-002003',NULL,'Product: LED Monitor 32\" - Variant 3',40094.00,10862.00,20.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(52,4,4,NULL,NULL,'Keyboard Wireless - Variant 3','SKU-002004',NULL,'Product: Keyboard Wireless - Variant 3',9339.00,44670.00,8.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(53,5,5,NULL,NULL,'Keyboard Mechanical - Variant 3','SKU-002005',NULL,'Product: Keyboard Mechanical - Variant 3',4893.00,30779.00,17.00,0.00,8.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(54,6,6,NULL,NULL,'Keyboard USB - Variant 3','SKU-002006',NULL,'Product: Keyboard USB - Variant 3',49793.00,32496.00,11.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(55,7,7,NULL,NULL,'Mouse Optical - Variant 3','SKU-002007',NULL,'Product: Mouse Optical - Variant 3',40717.00,14157.00,17.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(56,8,8,NULL,NULL,'Mouse Wireless - Variant 3','SKU-002008',NULL,'Product: Mouse Wireless - Variant 3',44205.00,55008.00,18.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(57,9,9,NULL,NULL,'Mouse Gaming - Variant 3','SKU-002009',NULL,'Product: Mouse Gaming - Variant 3',23436.00,38674.00,13.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(58,10,10,NULL,NULL,'USB Cable 1m - Variant 3','SKU-002010',NULL,'Product: USB Cable 1m - Variant 3',14582.00,13850.00,13.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(59,1,11,NULL,NULL,'USB Cable 2m - Variant 3','SKU-002011',NULL,'Product: USB Cable 2m - Variant 3',37234.00,51370.00,13.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(60,2,12,NULL,NULL,'USB Cable 5m - Variant 3','SKU-002012',NULL,'Product: USB Cable 5m - Variant 3',32906.00,55433.00,7.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(61,3,13,NULL,NULL,'HDMI Cable - Variant 3','SKU-002013',NULL,'Product: HDMI Cable - Variant 3',31496.00,27083.00,17.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(62,4,1,NULL,NULL,'Network Cable - Variant 3','SKU-002014',NULL,'Product: Network Cable - Variant 3',18291.00,9797.00,13.00,0.00,11.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(63,5,2,NULL,NULL,'Power Cable - Variant 3','SKU-002015',NULL,'Product: Power Cable - Variant 3',23051.00,40053.00,17.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(64,6,3,NULL,NULL,'Office Chair - Variant 3','SKU-002016',NULL,'Product: Office Chair - Variant 3',4709.00,30151.00,11.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(65,7,4,NULL,NULL,'Standing Desk - Variant 3','SKU-002017',NULL,'Product: Standing Desk - Variant 3',24448.00,43430.00,19.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(66,8,5,NULL,NULL,'Computer Table - Variant 3','SKU-002018',NULL,'Product: Computer Table - Variant 3',15048.00,19461.00,12.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(67,9,6,NULL,NULL,'Desk Lamp - Variant 3','SKU-002019',NULL,'Product: Desk Lamp - Variant 3',45630.00,59555.00,5.00,0.00,15.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(68,10,7,NULL,NULL,'Table Fan - Variant 3','SKU-002020',NULL,'Product: Table Fan - Variant 3',28882.00,44137.00,5.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(69,1,8,NULL,NULL,'Water Dispenser - Variant 3','SKU-002021',NULL,'Product: Water Dispenser - Variant 3',11388.00,30375.00,18.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(70,2,9,NULL,NULL,'Printer Laser - Variant 3','SKU-002022',NULL,'Product: Printer Laser - Variant 3',5979.00,33192.00,10.00,0.00,11.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(71,3,10,NULL,NULL,'Printer Inkjet - Variant 3','SKU-002023',NULL,'Product: Printer Inkjet - Variant 3',11741.00,23935.00,15.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(72,4,11,NULL,NULL,'Scanner - Variant 3','SKU-002024',NULL,'Product: Scanner - Variant 3',28413.00,49303.00,13.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(73,1,1,NULL,NULL,'LED Monitor 24\" - Variant 4','SKU-003001',NULL,'Product: LED Monitor 24\" - Variant 4',15830.00,4125.00,10.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(74,2,2,NULL,NULL,'LED Monitor 27\" - Variant 4','SKU-003002',NULL,'Product: LED Monitor 27\" - Variant 4',35261.00,26192.00,10.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(75,3,3,NULL,NULL,'LED Monitor 32\" - Variant 4','SKU-003003',NULL,'Product: LED Monitor 32\" - Variant 4',2088.00,32939.00,17.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(76,4,4,NULL,NULL,'Keyboard Wireless - Variant 4','SKU-003004',NULL,'Product: Keyboard Wireless - Variant 4',36683.00,10664.00,5.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(77,5,5,NULL,NULL,'Keyboard Mechanical - Variant 4','SKU-003005',NULL,'Product: Keyboard Mechanical - Variant 4',20524.00,30646.00,17.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(78,6,6,NULL,NULL,'Keyboard USB - Variant 4','SKU-003006',NULL,'Product: Keyboard USB - Variant 4',49434.00,29572.00,12.00,0.00,15.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(79,7,7,NULL,NULL,'Mouse Optical - Variant 4','SKU-003007',NULL,'Product: Mouse Optical - Variant 4',23719.00,32188.00,5.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(80,8,8,NULL,NULL,'Mouse Wireless - Variant 4','SKU-003008',NULL,'Product: Mouse Wireless - Variant 4',25772.00,51815.00,7.00,0.00,11.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(81,9,9,NULL,NULL,'Mouse Gaming - Variant 4','SKU-003009',NULL,'Product: Mouse Gaming - Variant 4',17996.00,48889.00,17.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(82,10,10,NULL,NULL,'USB Cable 1m - Variant 4','SKU-003010',NULL,'Product: USB Cable 1m - Variant 4',9548.00,2362.00,8.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(83,1,11,NULL,NULL,'USB Cable 2m - Variant 4','SKU-003011',NULL,'Product: USB Cable 2m - Variant 4',23824.00,36327.00,5.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(84,2,12,NULL,NULL,'USB Cable 5m - Variant 4','SKU-003012',NULL,'Product: USB Cable 5m - Variant 4',41155.00,32099.00,18.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(85,3,13,NULL,NULL,'HDMI Cable - Variant 4','SKU-003013',NULL,'Product: HDMI Cable - Variant 4',12292.00,55064.00,19.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(86,4,1,NULL,NULL,'Network Cable - Variant 4','SKU-003014',NULL,'Product: Network Cable - Variant 4',23961.00,35072.00,12.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(87,5,2,NULL,NULL,'Power Cable - Variant 4','SKU-003015',NULL,'Product: Power Cable - Variant 4',16991.00,46545.00,10.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(88,6,3,NULL,NULL,'Office Chair - Variant 4','SKU-003016',NULL,'Product: Office Chair - Variant 4',35114.00,47501.00,14.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(89,7,4,NULL,NULL,'Standing Desk - Variant 4','SKU-003017',NULL,'Product: Standing Desk - Variant 4',38640.00,51679.00,8.00,0.00,8.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(90,8,5,NULL,NULL,'Computer Table - Variant 4','SKU-003018',NULL,'Product: Computer Table - Variant 4',45358.00,39886.00,19.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(91,9,6,NULL,NULL,'Desk Lamp - Variant 4','SKU-003019',NULL,'Product: Desk Lamp - Variant 4',38080.00,40105.00,10.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(92,10,7,NULL,NULL,'Table Fan - Variant 4','SKU-003020',NULL,'Product: Table Fan - Variant 4',14982.00,22524.00,19.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(93,1,8,NULL,NULL,'Water Dispenser - Variant 4','SKU-003021',NULL,'Product: Water Dispenser - Variant 4',13908.00,40752.00,20.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(94,2,9,NULL,NULL,'Printer Laser - Variant 4','SKU-003022',NULL,'Product: Printer Laser - Variant 4',31552.00,55973.00,12.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(95,3,10,NULL,NULL,'Printer Inkjet - Variant 4','SKU-003023',NULL,'Product: Printer Inkjet - Variant 4',25637.00,21426.00,18.00,0.00,8.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(96,4,11,NULL,NULL,'Scanner - Variant 4','SKU-003024',NULL,'Product: Scanner - Variant 4',30441.00,2356.00,15.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(97,1,1,NULL,NULL,'LED Monitor 24\" - Variant 5','SKU-004001',NULL,'Product: LED Monitor 24\" - Variant 5',7021.00,21612.00,19.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(98,2,2,NULL,NULL,'LED Monitor 27\" - Variant 5','SKU-004002',NULL,'Product: LED Monitor 27\" - Variant 5',38078.00,59484.00,11.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(99,3,3,NULL,NULL,'LED Monitor 32\" - Variant 5','SKU-004003',NULL,'Product: LED Monitor 32\" - Variant 5',12608.00,42107.00,13.00,0.00,15.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(100,4,4,NULL,NULL,'Keyboard Wireless - Variant 5','SKU-004004',NULL,'Product: Keyboard Wireless - Variant 5',18992.00,50705.00,12.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(101,5,5,NULL,NULL,'Keyboard Mechanical - Variant 5','SKU-004005',NULL,'Product: Keyboard Mechanical - Variant 5',20321.00,30528.00,5.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(102,6,6,NULL,NULL,'Keyboard USB - Variant 5','SKU-004006',NULL,'Product: Keyboard USB - Variant 5',35865.00,5801.00,5.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(103,7,7,NULL,NULL,'Mouse Optical - Variant 5','SKU-004007',NULL,'Product: Mouse Optical - Variant 5',15801.00,36998.00,18.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(104,8,8,NULL,NULL,'Mouse Wireless - Variant 5','SKU-004008',NULL,'Product: Mouse Wireless - Variant 5',19373.00,28321.00,18.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(105,9,9,NULL,NULL,'Mouse Gaming - Variant 5','SKU-004009',NULL,'Product: Mouse Gaming - Variant 5',31897.00,28979.00,11.00,0.00,15.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(106,10,10,NULL,NULL,'USB Cable 1m - Variant 5','SKU-004010',NULL,'Product: USB Cable 1m - Variant 5',15865.00,14287.00,5.00,0.00,15.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(107,1,11,NULL,NULL,'USB Cable 2m - Variant 5','SKU-004011',NULL,'Product: USB Cable 2m - Variant 5',29006.00,13554.00,15.00,0.00,15.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(108,2,12,NULL,NULL,'USB Cable 5m - Variant 5','SKU-004012',NULL,'Product: USB Cable 5m - Variant 5',38439.00,26760.00,19.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(109,3,13,NULL,NULL,'HDMI Cable - Variant 5','SKU-004013',NULL,'Product: HDMI Cable - Variant 5',33132.00,19173.00,16.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(110,4,1,NULL,NULL,'Network Cable - Variant 5','SKU-004014',NULL,'Product: Network Cable - Variant 5',48999.00,26357.00,6.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(111,5,2,NULL,NULL,'Power Cable - Variant 5','SKU-004015',NULL,'Product: Power Cable - Variant 5',26198.00,43286.00,6.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(112,6,3,NULL,NULL,'Office Chair - Variant 5','SKU-004016',NULL,'Product: Office Chair - Variant 5',9226.00,55735.00,15.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(113,7,4,NULL,NULL,'Standing Desk - Variant 5','SKU-004017',NULL,'Product: Standing Desk - Variant 5',3996.00,21080.00,14.00,0.00,15.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(114,8,5,NULL,NULL,'Computer Table - Variant 5','SKU-004018',NULL,'Product: Computer Table - Variant 5',42168.00,8191.00,18.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(115,9,6,NULL,NULL,'Desk Lamp - Variant 5','SKU-004019',NULL,'Product: Desk Lamp - Variant 5',39850.00,15722.00,12.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(116,10,7,NULL,NULL,'Table Fan - Variant 5','SKU-004020',NULL,'Product: Table Fan - Variant 5',32724.00,8318.00,18.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(117,1,8,NULL,NULL,'Water Dispenser - Variant 5','SKU-004021',NULL,'Product: Water Dispenser - Variant 5',14738.00,5409.00,8.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(118,2,9,NULL,NULL,'Printer Laser - Variant 5','SKU-004022',NULL,'Product: Printer Laser - Variant 5',40350.00,42769.00,19.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(119,3,10,NULL,NULL,'Printer Inkjet - Variant 5','SKU-004023',NULL,'Product: Printer Inkjet - Variant 5',15228.00,43196.00,18.00,0.00,11.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(120,4,11,NULL,NULL,'Scanner - Variant 5','SKU-004024',NULL,'Product: Scanner - Variant 5',29605.00,56974.00,17.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(121,1,1,NULL,NULL,'LED Monitor 24\" - Variant 6','SKU-005001',NULL,'Product: LED Monitor 24\" - Variant 6',15899.00,7844.00,14.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(122,2,2,NULL,NULL,'LED Monitor 27\" - Variant 6','SKU-005002',NULL,'Product: LED Monitor 27\" - Variant 6',18887.00,17073.00,6.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(123,3,3,NULL,NULL,'LED Monitor 32\" - Variant 6','SKU-005003',NULL,'Product: LED Monitor 32\" - Variant 6',11532.00,28505.00,18.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(124,4,4,NULL,NULL,'Keyboard Wireless - Variant 6','SKU-005004',NULL,'Product: Keyboard Wireless - Variant 6',20069.00,52045.00,13.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(125,5,5,NULL,NULL,'Keyboard Mechanical - Variant 6','SKU-005005',NULL,'Product: Keyboard Mechanical - Variant 6',7434.00,27325.00,19.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(126,6,6,NULL,NULL,'Keyboard USB - Variant 6','SKU-005006',NULL,'Product: Keyboard USB - Variant 6',44818.00,8325.00,10.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(127,7,7,NULL,NULL,'Mouse Optical - Variant 6','SKU-005007',NULL,'Product: Mouse Optical - Variant 6',36592.00,58874.00,16.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(128,8,8,NULL,NULL,'Mouse Wireless - Variant 6','SKU-005008',NULL,'Product: Mouse Wireless - Variant 6',8798.00,37953.00,7.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(129,9,9,NULL,NULL,'Mouse Gaming - Variant 6','SKU-005009',NULL,'Product: Mouse Gaming - Variant 6',12427.00,4043.00,17.00,0.00,8.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(130,10,10,NULL,NULL,'USB Cable 1m - Variant 6','SKU-005010',NULL,'Product: USB Cable 1m - Variant 6',19350.00,27180.00,16.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(131,1,11,NULL,NULL,'USB Cable 2m - Variant 6','SKU-005011',NULL,'Product: USB Cable 2m - Variant 6',28808.00,14814.00,20.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(132,2,12,NULL,NULL,'USB Cable 5m - Variant 6','SKU-005012',NULL,'Product: USB Cable 5m - Variant 6',6172.00,20602.00,6.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(133,3,13,NULL,NULL,'HDMI Cable - Variant 6','SKU-005013',NULL,'Product: HDMI Cable - Variant 6',46994.00,46467.00,13.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(134,4,1,NULL,NULL,'Network Cable - Variant 6','SKU-005014',NULL,'Product: Network Cable - Variant 6',46600.00,36316.00,6.00,0.00,8.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(135,5,2,NULL,NULL,'Power Cable - Variant 6','SKU-005015',NULL,'Product: Power Cable - Variant 6',30146.00,22403.00,11.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(136,6,3,NULL,NULL,'Office Chair - Variant 6','SKU-005016',NULL,'Product: Office Chair - Variant 6',34045.00,38393.00,15.00,0.00,15.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(137,7,4,NULL,NULL,'Standing Desk - Variant 6','SKU-005017',NULL,'Product: Standing Desk - Variant 6',30109.00,59736.00,17.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(138,8,5,NULL,NULL,'Computer Table - Variant 6','SKU-005018',NULL,'Product: Computer Table - Variant 6',18921.00,30450.00,10.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(139,9,6,NULL,NULL,'Desk Lamp - Variant 6','SKU-005019',NULL,'Product: Desk Lamp - Variant 6',38828.00,12007.00,15.00,0.00,11.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(140,10,7,NULL,NULL,'Table Fan - Variant 6','SKU-005020',NULL,'Product: Table Fan - Variant 6',42999.00,28326.00,10.00,0.00,11.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(141,1,8,NULL,NULL,'Water Dispenser - Variant 6','SKU-005021',NULL,'Product: Water Dispenser - Variant 6',2820.00,7741.00,6.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(142,2,9,NULL,NULL,'Printer Laser - Variant 6','SKU-005022',NULL,'Product: Printer Laser - Variant 6',34384.00,3833.00,13.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(143,3,10,NULL,NULL,'Printer Inkjet - Variant 6','SKU-005023',NULL,'Product: Printer Inkjet - Variant 6',39359.00,56960.00,19.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(144,4,11,NULL,NULL,'Scanner - Variant 6','SKU-005024',NULL,'Product: Scanner - Variant 6',26465.00,55891.00,20.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(145,1,1,NULL,NULL,'LED Monitor 24\" - Variant 7','SKU-006001',NULL,'Product: LED Monitor 24\" - Variant 7',19362.00,2052.00,19.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(146,2,2,NULL,NULL,'LED Monitor 27\" - Variant 7','SKU-006002',NULL,'Product: LED Monitor 27\" - Variant 7',8397.00,28810.00,17.00,0.00,15.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(147,3,3,NULL,NULL,'LED Monitor 32\" - Variant 7','SKU-006003',NULL,'Product: LED Monitor 32\" - Variant 7',32189.00,29915.00,6.00,0.00,8.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(148,4,4,NULL,NULL,'Keyboard Wireless - Variant 7','SKU-006004',NULL,'Product: Keyboard Wireless - Variant 7',26811.00,37277.00,19.00,0.00,15.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(149,5,5,NULL,NULL,'Keyboard Mechanical - Variant 7','SKU-006005',NULL,'Product: Keyboard Mechanical - Variant 7',8936.00,58323.00,11.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(150,6,6,NULL,NULL,'Keyboard USB - Variant 7','SKU-006006',NULL,'Product: Keyboard USB - Variant 7',17189.00,50958.00,17.00,0.00,11.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(151,7,7,NULL,NULL,'Mouse Optical - Variant 7','SKU-006007',NULL,'Product: Mouse Optical - Variant 7',18900.00,47939.00,17.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(152,8,8,NULL,NULL,'Mouse Wireless - Variant 7','SKU-006008',NULL,'Product: Mouse Wireless - Variant 7',25674.00,5643.00,20.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(153,9,9,NULL,NULL,'Mouse Gaming - Variant 7','SKU-006009',NULL,'Product: Mouse Gaming - Variant 7',9738.00,11920.00,7.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(154,10,10,NULL,NULL,'USB Cable 1m - Variant 7','SKU-006010',NULL,'Product: USB Cable 1m - Variant 7',24986.00,13726.00,18.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(155,1,11,NULL,NULL,'USB Cable 2m - Variant 7','SKU-006011',NULL,'Product: USB Cable 2m - Variant 7',6085.00,19921.00,17.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(156,2,12,NULL,NULL,'USB Cable 5m - Variant 7','SKU-006012',NULL,'Product: USB Cable 5m - Variant 7',41711.00,41289.00,8.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(157,3,13,NULL,NULL,'HDMI Cable - Variant 7','SKU-006013',NULL,'Product: HDMI Cable - Variant 7',32276.00,17239.00,5.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(158,4,1,NULL,NULL,'Network Cable - Variant 7','SKU-006014',NULL,'Product: Network Cable - Variant 7',47467.00,35378.00,7.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(159,5,2,NULL,NULL,'Power Cable - Variant 7','SKU-006015',NULL,'Product: Power Cable - Variant 7',42945.00,30652.00,7.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(160,6,3,NULL,NULL,'Office Chair - Variant 7','SKU-006016',NULL,'Product: Office Chair - Variant 7',9469.00,48661.00,14.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(161,7,4,NULL,NULL,'Standing Desk - Variant 7','SKU-006017',NULL,'Product: Standing Desk - Variant 7',24485.00,15624.00,14.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(162,8,5,NULL,NULL,'Computer Table - Variant 7','SKU-006018',NULL,'Product: Computer Table - Variant 7',13811.00,22685.00,12.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(163,9,6,NULL,NULL,'Desk Lamp - Variant 7','SKU-006019',NULL,'Product: Desk Lamp - Variant 7',9930.00,2447.00,11.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(164,10,7,NULL,NULL,'Table Fan - Variant 7','SKU-006020',NULL,'Product: Table Fan - Variant 7',16726.00,56514.00,16.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(165,1,8,NULL,NULL,'Water Dispenser - Variant 7','SKU-006021',NULL,'Product: Water Dispenser - Variant 7',35871.00,15006.00,16.00,0.00,8.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(166,2,9,NULL,NULL,'Printer Laser - Variant 7','SKU-006022',NULL,'Product: Printer Laser - Variant 7',45783.00,10768.00,6.00,0.00,8.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(167,3,10,NULL,NULL,'Printer Inkjet - Variant 7','SKU-006023',NULL,'Product: Printer Inkjet - Variant 7',7010.00,8252.00,7.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(168,4,11,NULL,NULL,'Scanner - Variant 7','SKU-006024',NULL,'Product: Scanner - Variant 7',9947.00,59012.00,15.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(169,1,1,NULL,NULL,'LED Monitor 24\" - Variant 8','SKU-007001',NULL,'Product: LED Monitor 24\" - Variant 8',45718.00,23677.00,10.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(170,2,2,NULL,NULL,'LED Monitor 27\" - Variant 8','SKU-007002',NULL,'Product: LED Monitor 27\" - Variant 8',15670.00,11513.00,16.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(171,3,3,NULL,NULL,'LED Monitor 32\" - Variant 8','SKU-007003',NULL,'Product: LED Monitor 32\" - Variant 8',45906.00,30088.00,15.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(172,4,4,NULL,NULL,'Keyboard Wireless - Variant 8','SKU-007004',NULL,'Product: Keyboard Wireless - Variant 8',39194.00,27758.00,17.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(173,5,5,NULL,NULL,'Keyboard Mechanical - Variant 8','SKU-007005',NULL,'Product: Keyboard Mechanical - Variant 8',2808.00,24324.00,15.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(174,6,6,NULL,NULL,'Keyboard USB - Variant 8','SKU-007006',NULL,'Product: Keyboard USB - Variant 8',31035.00,31738.00,10.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(175,7,7,NULL,NULL,'Mouse Optical - Variant 8','SKU-007007',NULL,'Product: Mouse Optical - Variant 8',33435.00,52632.00,13.00,0.00,15.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(176,8,8,NULL,NULL,'Mouse Wireless - Variant 8','SKU-007008',NULL,'Product: Mouse Wireless - Variant 8',4918.00,18643.00,6.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(177,9,9,NULL,NULL,'Mouse Gaming - Variant 8','SKU-007009',NULL,'Product: Mouse Gaming - Variant 8',11437.00,16373.00,20.00,0.00,8.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(178,10,10,NULL,NULL,'USB Cable 1m - Variant 8','SKU-007010',NULL,'Product: USB Cable 1m - Variant 8',45239.00,41958.00,10.00,0.00,15.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(179,1,11,NULL,NULL,'USB Cable 2m - Variant 8','SKU-007011',NULL,'Product: USB Cable 2m - Variant 8',33753.00,30249.00,10.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(180,2,12,NULL,NULL,'USB Cable 5m - Variant 8','SKU-007012',NULL,'Product: USB Cable 5m - Variant 8',21934.00,16063.00,7.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(181,3,13,NULL,NULL,'HDMI Cable - Variant 8','SKU-007013',NULL,'Product: HDMI Cable - Variant 8',40279.00,58505.00,11.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(182,4,1,NULL,NULL,'Network Cable - Variant 8','SKU-007014',NULL,'Product: Network Cable - Variant 8',37494.00,24486.00,10.00,0.00,9.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(183,5,2,NULL,NULL,'Power Cable - Variant 8','SKU-007015',NULL,'Product: Power Cable - Variant 8',23365.00,20133.00,7.00,0.00,11.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(184,6,3,NULL,NULL,'Office Chair - Variant 8','SKU-007016',NULL,'Product: Office Chair - Variant 8',24810.00,8882.00,5.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(185,7,4,NULL,NULL,'Standing Desk - Variant 8','SKU-007017',NULL,'Product: Standing Desk - Variant 8',21273.00,11191.00,8.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(186,8,5,NULL,NULL,'Computer Table - Variant 8','SKU-007018',NULL,'Product: Computer Table - Variant 8',20434.00,46476.00,9.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(187,9,6,NULL,NULL,'Desk Lamp - Variant 8','SKU-007019',NULL,'Product: Desk Lamp - Variant 8',34676.00,44175.00,5.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(188,10,7,NULL,NULL,'Table Fan - Variant 8','SKU-007020',NULL,'Product: Table Fan - Variant 8',21831.00,47848.00,9.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(189,1,8,NULL,NULL,'Water Dispenser - Variant 8','SKU-007021',NULL,'Product: Water Dispenser - Variant 8',44161.00,12553.00,7.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(190,2,9,NULL,NULL,'Printer Laser - Variant 8','SKU-007022',NULL,'Product: Printer Laser - Variant 8',6216.00,43493.00,8.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(191,3,10,NULL,NULL,'Printer Inkjet - Variant 8','SKU-007023',NULL,'Product: Printer Inkjet - Variant 8',42335.00,50542.00,15.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(192,4,11,NULL,NULL,'Scanner - Variant 8','SKU-007024',NULL,'Product: Scanner - Variant 8',32389.00,11323.00,12.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(193,1,1,NULL,NULL,'LED Monitor 24\" - Variant 9','SKU-008001',NULL,'Product: LED Monitor 24\" - Variant 9',31842.00,5159.00,12.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(194,2,2,NULL,NULL,'LED Monitor 27\" - Variant 9','SKU-008002',NULL,'Product: LED Monitor 27\" - Variant 9',46739.00,43594.00,19.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(195,3,3,NULL,NULL,'LED Monitor 32\" - Variant 9','SKU-008003',NULL,'Product: LED Monitor 32\" - Variant 9',39758.00,31885.00,19.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(196,4,4,NULL,NULL,'Keyboard Wireless - Variant 9','SKU-008004',NULL,'Product: Keyboard Wireless - Variant 9',13459.00,55260.00,16.00,0.00,11.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(197,5,5,NULL,NULL,'Keyboard Mechanical - Variant 9','SKU-008005',NULL,'Product: Keyboard Mechanical - Variant 9',34938.00,20261.00,11.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(198,6,6,NULL,NULL,'Keyboard USB - Variant 9','SKU-008006',NULL,'Product: Keyboard USB - Variant 9',30531.00,14780.00,8.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(199,7,7,NULL,NULL,'Mouse Optical - Variant 9','SKU-008007',NULL,'Product: Mouse Optical - Variant 9',23959.00,13569.00,17.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(200,8,8,NULL,NULL,'Mouse Wireless - Variant 9','SKU-008008',NULL,'Product: Mouse Wireless - Variant 9',11531.00,31576.00,15.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(201,9,9,NULL,NULL,'Mouse Gaming - Variant 9','SKU-008009',NULL,'Product: Mouse Gaming - Variant 9',15787.00,22514.00,17.00,0.00,8.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(202,10,10,NULL,NULL,'USB Cable 1m - Variant 9','SKU-008010',NULL,'Product: USB Cable 1m - Variant 9',4227.00,24350.00,5.00,0.00,15.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(203,1,11,NULL,NULL,'USB Cable 2m - Variant 9','SKU-008011',NULL,'Product: USB Cable 2m - Variant 9',9550.00,20498.00,20.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(204,2,12,NULL,NULL,'USB Cable 5m - Variant 9','SKU-008012',NULL,'Product: USB Cable 5m - Variant 9',25256.00,49295.00,18.00,0.00,11.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(205,3,13,NULL,NULL,'HDMI Cable - Variant 9','SKU-008013',NULL,'Product: HDMI Cable - Variant 9',13449.00,56264.00,7.00,0.00,6.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(206,4,1,NULL,NULL,'Network Cable - Variant 9','SKU-008014',NULL,'Product: Network Cable - Variant 9',9414.00,32658.00,7.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(207,5,2,NULL,NULL,'Power Cable - Variant 9','SKU-008015',NULL,'Product: Power Cable - Variant 9',44898.00,43191.00,20.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(208,6,3,NULL,NULL,'Office Chair - Variant 9','SKU-008016',NULL,'Product: Office Chair - Variant 9',1726.00,9437.00,20.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(209,7,4,NULL,NULL,'Standing Desk - Variant 9','SKU-008017',NULL,'Product: Standing Desk - Variant 9',15660.00,32753.00,6.00,0.00,10.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(210,8,5,NULL,NULL,'Computer Table - Variant 9','SKU-008018',NULL,'Product: Computer Table - Variant 9',43662.00,50945.00,10.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(211,9,6,NULL,NULL,'Desk Lamp - Variant 9','SKU-008019',NULL,'Product: Desk Lamp - Variant 9',42820.00,7355.00,15.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(212,10,7,NULL,NULL,'Table Fan - Variant 9','SKU-008020',NULL,'Product: Table Fan - Variant 9',3661.00,15415.00,12.00,0.00,7.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(213,1,8,NULL,NULL,'Water Dispenser - Variant 9','SKU-008021',NULL,'Product: Water Dispenser - Variant 9',8208.00,39537.00,13.00,0.00,12.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(214,2,9,NULL,NULL,'Printer Laser - Variant 9','SKU-008022',NULL,'Product: Printer Laser - Variant 9',22695.00,17579.00,19.00,0.00,14.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(215,3,10,NULL,NULL,'Printer Inkjet - Variant 9','SKU-008023',NULL,'Product: Printer Inkjet - Variant 9',48965.00,27687.00,8.00,0.00,13.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(216,4,11,NULL,NULL,'Scanner - Variant 9','SKU-008024',NULL,'Product: Scanner - Variant 9',32451.00,4906.00,11.00,0.00,5.00,NULL,NULL,NULL,NULL,NULL,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0);
/*!40000 ALTER TABLE `inventory_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_purchase_invoice_items`
--

DROP TABLE IF EXISTS `inventory_purchase_invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_purchase_invoice_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_invoice_id` int NOT NULL,
  `product_id` int NOT NULL,
  `unit_id` int DEFAULT NULL,
  `quantity` decimal(18,2) DEFAULT '0.00',
  `unit_price` decimal(18,2) DEFAULT '0.00',
  `discount_amount` decimal(18,2) DEFAULT '0.00',
  `tax_amount` decimal(18,2) DEFAULT '0.00',
  `total_amount` decimal(18,2) DEFAULT '0.00',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_invoice_id` (`purchase_invoice_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_purchase_invoice_items`
--

LOCK TABLES `inventory_purchase_invoice_items` WRITE;
/*!40000 ALTER TABLE `inventory_purchase_invoice_items` DISABLE KEYS */;
INSERT INTO `inventory_purchase_invoice_items` VALUES (1,1,21,NULL,2.00,15512.00,48.00,24.00,31000.00,'2026-07-25 04:29:39','2026-07-25 04:29:39'),(2,1,45,NULL,2.00,10852.00,1000.00,296.00,21000.00,'2026-07-25 04:29:39','2026-07-25 04:29:39');
/*!40000 ALTER TABLE `inventory_purchase_invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_purchase_invoice_payments`
--

DROP TABLE IF EXISTS `inventory_purchase_invoice_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_purchase_invoice_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_invoice_id` int NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL,
  `payment_date` date NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_invoice_id` (`purchase_invoice_id`),
  CONSTRAINT `inventory_purchase_invoice_payments_ibfk_1` FOREIGN KEY (`purchase_invoice_id`) REFERENCES `inventory_purchase_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_purchase_invoice_payments`
--

LOCK TABLES `inventory_purchase_invoice_payments` WRITE;
/*!40000 ALTER TABLE `inventory_purchase_invoice_payments` DISABLE KEYS */;
INSERT INTO `inventory_purchase_invoice_payments` VALUES (1,1,52000.00,'2026-07-25','Full Amount Paid','2026-07-25 09:30:29',2);
/*!40000 ALTER TABLE `inventory_purchase_invoice_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_purchase_invoices`
--

DROP TABLE IF EXISTS `inventory_purchase_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_purchase_invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_order_id` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `account_id` int DEFAULT NULL,
  `invoice_no` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `invoice_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `subtotal` decimal(18,2) DEFAULT '0.00',
  `discount_amount` decimal(18,2) DEFAULT '0.00',
  `tax_amount` decimal(18,2) DEFAULT '0.00',
  `grand_total` decimal(18,2) DEFAULT '0.00',
  `paid_amount` decimal(18,2) DEFAULT '0.00',
  `remaining_balance` decimal(15,2) DEFAULT '0.00',
  `balance_amount` decimal(18,2) DEFAULT '0.00',
  `status` enum('Pending','Partial','Paid','Cancelled') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `status` (`status`),
  KEY `account_id` (`account_id`),
  CONSTRAINT `fk_purchase_inv_account` FOREIGN KEY (`account_id`) REFERENCES `inventory_accounts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_purchase_invoices`
--

LOCK TABLES `inventory_purchase_invoices` WRITE;
/*!40000 ALTER TABLE `inventory_purchase_invoices` DISABLE KEYS */;
INSERT INTO `inventory_purchase_invoices` VALUES (1,1,21,11,'PINV-2026-00001','2026-07-25','2026-08-29',52000.00,1048.00,320.00,52000.00,52000.00,0.00,0.00,'Paid','Full Amount Paid','2026-07-25 04:29:39','2026-07-25 04:30:29',1,0);
/*!40000 ALTER TABLE `inventory_purchase_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_purchase_order_items`
--

DROP TABLE IF EXISTS `inventory_purchase_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_purchase_order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` decimal(15,2) DEFAULT '0.00',
  `received_quantity` decimal(15,2) DEFAULT '0.00',
  `remaining_quantity` decimal(15,2) DEFAULT '0.00',
  `unit_price` decimal(15,2) DEFAULT '0.00',
  `discount_amount` decimal(15,2) DEFAULT '0.00',
  `tax_amount` decimal(15,2) DEFAULT '0.00',
  `line_total` decimal(15,2) DEFAULT '0.00',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `inventory_purchase_order_items_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `inventory_purchase_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inventory_purchase_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `inventory_products` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_purchase_order_items`
--

LOCK TABLES `inventory_purchase_order_items` WRITE;
/*!40000 ALTER TABLE `inventory_purchase_order_items` DISABLE KEYS */;
INSERT INTO `inventory_purchase_order_items` VALUES (1,1,21,2.00,0.00,2.00,15512.00,48.00,24.00,31000.00,'Water Dispenser V1','2026-07-25 04:29:39','2026-07-25 09:29:39',NULL,NULL,1,0),(2,1,45,2.00,0.00,2.00,10852.00,1000.00,296.00,21000.00,'Water Dispenser V2','2026-07-25 04:29:39','2026-07-25 09:29:39',NULL,NULL,1,0);
/*!40000 ALTER TABLE `inventory_purchase_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_purchase_orders`
--

DROP TABLE IF EXISTS `inventory_purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_purchase_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `po_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_id` int NOT NULL,
  `warehouse_id` int NOT NULL,
  `order_date` date DEFAULT NULL,
  `expected_date` date DEFAULT NULL,
  `payment_terms` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Draft','Approved','Partially Received','Completed','Cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'Draft',
  `subtotal` decimal(15,2) DEFAULT '0.00',
  `discount` decimal(15,2) DEFAULT '0.00',
  `tax` decimal(15,2) DEFAULT '0.00',
  `freight` decimal(15,2) DEFAULT '0.00',
  `other_charges` decimal(15,2) DEFAULT '0.00',
  `grand_total` decimal(15,2) DEFAULT '0.00',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `po_number` (`po_number`),
  KEY `supplier_id` (`supplier_id`),
  KEY `warehouse_id` (`warehouse_id`),
  CONSTRAINT `inventory_purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `inventory_suppliers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `inventory_purchase_orders_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `inventory_warehouses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_purchase_orders`
--

LOCK TABLES `inventory_purchase_orders` WRITE;
/*!40000 ALTER TABLE `inventory_purchase_orders` DISABLE KEYS */;
INSERT INTO `inventory_purchase_orders` VALUES (1,'PO-20260725042939446',21,1,'2026-07-25','2026-07-30','Cash in Hand','Completed',52000.00,1048.00,320.00,0.00,0.00,52000.00,'2 Water Dispenser V1 & 2 Water Dispenser V2','2026-07-25 04:29:39','2026-07-25 04:31:18',NULL,NULL,1,0);
/*!40000 ALTER TABLE `inventory_purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_purchase_return_items`
--

DROP TABLE IF EXISTS `inventory_purchase_return_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_purchase_return_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_return_id` int NOT NULL,
  `purchase_invoice_item_id` int DEFAULT NULL,
  `product_id` int NOT NULL,
  `unit_id` int DEFAULT NULL,
  `quantity` decimal(18,2) DEFAULT '0.00',
  `unit_price` decimal(18,2) DEFAULT '0.00',
  `total_amount` decimal(18,2) DEFAULT '0.00',
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_return_id` (`purchase_return_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_purchase_return_items`
--

LOCK TABLES `inventory_purchase_return_items` WRITE;
/*!40000 ALTER TABLE `inventory_purchase_return_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_purchase_return_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_purchase_returns`
--

DROP TABLE IF EXISTS `inventory_purchase_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_purchase_returns` (
  `id` int NOT NULL AUTO_INCREMENT,
  `return_no` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `purchase_invoice_id` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `reason` text COLLATE utf8mb4_general_ci,
  `subtotal` decimal(18,2) DEFAULT '0.00',
  `tax_amount` decimal(18,2) DEFAULT '0.00',
  `grand_total` decimal(18,2) DEFAULT '0.00',
  `status` enum('Pending','Approved','Completed','Cancelled') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `purchase_invoice_id` (`purchase_invoice_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_purchase_returns`
--

LOCK TABLES `inventory_purchase_returns` WRITE;
/*!40000 ALTER TABLE `inventory_purchase_returns` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_purchase_returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_reports`
--

DROP TABLE IF EXISTS `inventory_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `report_name` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `report_type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `generated_by` int DEFAULT NULL,
  `filters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `file_path` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  CONSTRAINT `inventory_reports_chk_1` CHECK (json_valid(`filters`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_reports`
--

LOCK TABLES `inventory_reports` WRITE;
/*!40000 ALTER TABLE `inventory_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_sale_invoice_items`
--

DROP TABLE IF EXISTS `inventory_sale_invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_sale_invoice_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sales_invoice_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` decimal(15,2) DEFAULT '0.00',
  `unit_price` decimal(15,2) DEFAULT '0.00',
  `discount` decimal(15,2) DEFAULT '0.00',
  `tax` decimal(15,2) DEFAULT '0.00',
  `total` decimal(15,2) DEFAULT '0.00',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sales_invoice_id` (`sales_invoice_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `inventory_sale_invoice_items_ibfk_1` FOREIGN KEY (`sales_invoice_id`) REFERENCES `inventory_sales_invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inventory_sale_invoice_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `inventory_products` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_sale_invoice_items`
--

LOCK TABLES `inventory_sale_invoice_items` WRITE;
/*!40000 ALTER TABLE `inventory_sale_invoice_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_sale_invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_sale_invoice_payments`
--

DROP TABLE IF EXISTS `inventory_sale_invoice_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_sale_invoice_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sale_invoice_id` int NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL,
  `payment_date` date NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_invoice_id` (`sale_invoice_id`),
  CONSTRAINT `inventory_sale_invoice_payments_ibfk_1` FOREIGN KEY (`sale_invoice_id`) REFERENCES `inventory_sales_invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_sale_invoice_payments`
--

LOCK TABLES `inventory_sale_invoice_payments` WRITE;
/*!40000 ALTER TABLE `inventory_sale_invoice_payments` DISABLE KEYS */;
INSERT INTO `inventory_sale_invoice_payments` VALUES (1,1,50000.00,'2026-07-25','Initial Payment - Sales Order','2026-07-25 04:38:45',2),(2,1,50000.00,'2026-07-25','Partial Payment - Invoice Update','2026-07-25 04:39:17',2);
/*!40000 ALTER TABLE `inventory_sale_invoice_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_sales_invoice_items`
--

DROP TABLE IF EXISTS `inventory_sales_invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_sales_invoice_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sales_invoice_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` decimal(15,2) DEFAULT '0.00',
  `unit_price` decimal(15,2) DEFAULT '0.00',
  `discount` decimal(15,2) DEFAULT '0.00',
  `tax` decimal(15,2) DEFAULT '0.00',
  `total` decimal(15,2) DEFAULT '0.00',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sales_invoice_id` (`sales_invoice_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `inventory_sales_invoice_items_ibfk_1` FOREIGN KEY (`sales_invoice_id`) REFERENCES `inventory_sales_invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inventory_sales_invoice_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `inventory_products` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_sales_invoice_items`
--

LOCK TABLES `inventory_sales_invoice_items` WRITE;
/*!40000 ALTER TABLE `inventory_sales_invoice_items` DISABLE KEYS */;
INSERT INTO `inventory_sales_invoice_items` VALUES (1,1,21,2.00,47054.00,45.00,15.00,94078.00,'2026-07-25 04:38:45','2026-07-25 04:38:45',0),(2,1,45,2.00,2976.00,45.00,15.00,5922.00,'2026-07-25 04:38:45','2026-07-25 04:38:45',0);
/*!40000 ALTER TABLE `inventory_sales_invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_sales_invoices`
--

DROP TABLE IF EXISTS `inventory_sales_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_sales_invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sales_order_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `warehouse_id` int NOT NULL,
  `account_id` int DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT '0.00',
  `discount` decimal(15,2) DEFAULT '0.00',
  `tax` decimal(15,2) DEFAULT '0.00',
  `shipping` decimal(15,2) DEFAULT '0.00',
  `grand_total` decimal(15,2) DEFAULT '0.00',
  `paid_amount` decimal(15,2) DEFAULT '0.00',
  `remaining_balance` decimal(15,2) DEFAULT '0.00',
  `status` enum('Draft','Issued','Paid','Partially Paid','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_no`),
  KEY `sales_order_id` (`sales_order_id`),
  KEY `customer_id` (`customer_id`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `account_id` (`account_id`),
  CONSTRAINT `fk_sales_inv_account` FOREIGN KEY (`account_id`) REFERENCES `inventory_accounts` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `inventory_sales_invoices_ibfk_1` FOREIGN KEY (`sales_order_id`) REFERENCES `inventory_sales_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inventory_sales_invoices_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `inventory_customers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `inventory_sales_invoices_ibfk_3` FOREIGN KEY (`warehouse_id`) REFERENCES `inventory_warehouses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_sales_invoices`
--

LOCK TABLES `inventory_sales_invoices` WRITE;
/*!40000 ALTER TABLE `inventory_sales_invoices` DISABLE KEYS */;
INSERT INTO `inventory_sales_invoices` VALUES (1,'INV-20260725043845-437',1,1,1,7,'2026-07-25','2026-08-24',100060.00,90.00,30.00,0.00,100000.00,100000.00,0.00,'Paid','4 Water Dispenser V1 & V2  respectively','2026-07-25 04:38:45','2026-07-25 04:39:17',2,NULL,0);
/*!40000 ALTER TABLE `inventory_sales_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_sales_order_items`
--

DROP TABLE IF EXISTS `inventory_sales_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_sales_order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sales_order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` decimal(15,2) DEFAULT '0.00',
  `delivered_quantity` decimal(15,2) DEFAULT '0.00',
  `remaining_quantity` decimal(15,2) DEFAULT '0.00',
  `unit_price` decimal(15,2) DEFAULT '0.00',
  `discount` decimal(15,2) DEFAULT '0.00',
  `tax` decimal(15,2) DEFAULT '0.00',
  `total` decimal(15,2) DEFAULT '0.00',
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sales_order_id` (`sales_order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `inventory_sales_order_items_ibfk_1` FOREIGN KEY (`sales_order_id`) REFERENCES `inventory_sales_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inventory_sales_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `inventory_products` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_sales_order_items`
--

LOCK TABLES `inventory_sales_order_items` WRITE;
/*!40000 ALTER TABLE `inventory_sales_order_items` DISABLE KEYS */;
INSERT INTO `inventory_sales_order_items` VALUES (1,1,21,2.00,0.00,0.00,47054.00,45.00,15.00,94078.00,NULL,'2026-07-25 04:38:45','2026-07-25 09:41:36',2,NULL,1,1),(2,1,45,2.00,0.00,0.00,2976.00,45.00,15.00,5922.00,NULL,'2026-07-25 04:38:45','2026-07-25 09:41:36',2,NULL,1,1),(3,1,21,2.00,0.00,0.00,47054.00,45.00,15.00,94078.00,NULL,'2026-07-25 04:41:36','2026-07-25 09:41:36',2,NULL,1,0),(4,1,45,2.00,0.00,0.00,2976.00,45.00,15.00,5922.00,NULL,'2026-07-25 04:41:36','2026-07-25 09:41:36',2,NULL,1,0);
/*!40000 ALTER TABLE `inventory_sales_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_sales_orders`
--

DROP TABLE IF EXISTS `inventory_sales_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_sales_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `customer_id` int NOT NULL,
  `warehouse_id` int NOT NULL,
  `order_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `order_status` enum('Draft','Confirmed','Packed','Dispatched','Delivered','Cancelled') COLLATE utf8mb4_general_ci DEFAULT 'Draft',
  `payment_status` enum('Pending','Partial','Paid') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `subtotal` decimal(15,2) DEFAULT '0.00',
  `discount` decimal(15,2) DEFAULT '0.00',
  `tax` decimal(15,2) DEFAULT '0.00',
  `shipping` decimal(15,2) DEFAULT '0.00',
  `grand_total` decimal(15,2) DEFAULT '0.00',
  `paid_amount` decimal(15,2) DEFAULT '0.00',
  `remaining_balance` decimal(15,2) DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `customer_id` (`customer_id`),
  KEY `warehouse_id` (`warehouse_id`),
  CONSTRAINT `inventory_sales_orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `inventory_customers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `inventory_sales_orders_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `inventory_warehouses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_sales_orders`
--

LOCK TABLES `inventory_sales_orders` WRITE;
/*!40000 ALTER TABLE `inventory_sales_orders` DISABLE KEYS */;
INSERT INTO `inventory_sales_orders` VALUES (1,'SO-20260725043845-508',1,1,'2026-07-25','2026-07-25','Confirmed','Paid',100060.00,90.00,30.00,0.00,100000.00,100000.00,0.00,'4 Water Dispenser V1 & V2  respectively','2026-07-25 04:38:45','2026-07-25 04:41:36',2,2,1,0);
/*!40000 ALTER TABLE `inventory_sales_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_sales_returns`
--

DROP TABLE IF EXISTS `inventory_sales_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_sales_returns` (
  `id` int NOT NULL AUTO_INCREMENT,
  `return_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sales_invoice_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `return_date` date DEFAULT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT '0.00',
  `tax_amount` decimal(15,2) DEFAULT '0.00',
  `grand_total` decimal(15,2) DEFAULT '0.00',
  `status` enum('Pending','Approved','Completed','Cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `return_no` (`return_no`),
  KEY `sales_invoice_id` (`sales_invoice_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `inventory_sales_returns_ibfk_1` FOREIGN KEY (`sales_invoice_id`) REFERENCES `inventory_sales_invoices` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `inventory_sales_returns_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `inventory_customers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_sales_returns`
--

LOCK TABLES `inventory_sales_returns` WRITE;
/*!40000 ALTER TABLE `inventory_sales_returns` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_sales_returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_settings`
--

DROP TABLE IF EXISTS `inventory_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` longtext COLLATE utf8mb4_unicode_ci,
  `setting_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'text',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `setting_key_2` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_settings`
--

LOCK TABLES `inventory_settings` WRITE;
/*!40000 ALTER TABLE `inventory_settings` DISABLE KEYS */;
INSERT INTO `inventory_settings` VALUES (1,'app_name','Inventory Management System','text','Application name','2026-07-19 10:13:10','2026-07-22 11:33:06',1,1,0),(2,'app_version','1.0.0','text','Application version','2026-07-19 10:13:10','2026-07-22 11:33:06',1,1,0),(3,'support_email','support@example.com','email','Support email address','2026-07-19 10:13:10','2026-07-22 11:33:06',1,1,0),(4,'support_phone','+92 318 5657457','text','Support phone number','2026-07-19 10:13:10','2026-07-22 11:33:06',1,1,0),(5,'company_name','Inventory Managenent System','text','Company legal name','2026-07-19 10:13:10','2026-07-19 11:11:53',1,1,0),(6,'company_address','123 Business Street, City, Country','text','Company address','2026-07-19 10:13:10','2026-07-19 11:11:53',1,1,0),(7,'company_phone','+1-555-0000','text','Company phone','2026-07-19 10:13:10','2026-07-19 10:13:10',1,1,0),(8,'company_email','info@example.com','email','Company email','2026-07-19 10:13:10','2026-07-19 10:13:10',1,1,0),(9,'company_website','https://example.com','text','Company website','2026-07-19 10:13:10','2026-07-19 11:11:53',1,1,0),(10,'tax_number','TAX123456789','text','Tax ID or VAT number','2026-07-19 10:13:10','2026-07-19 11:11:53',1,1,0),(11,'currency','INR','text','Default currency','2026-07-19 10:13:10','2026-07-23 14:50:09',1,1,0),(12,'currency_symbol','RS.','text','Currency symbol','2026-07-19 10:13:10','2026-07-23 14:50:09',1,1,0),(13,'fiscal_year_start','01','text','Fiscal year start month','2026-07-19 10:13:10','2026-07-23 14:50:09',1,1,0),(14,'company_logo','','text','Company logo path','2026-07-19 10:13:10','2026-07-19 10:13:10',1,1,0),(15,'theme','light','text','UI theme (light/dark/auto)','2026-07-19 10:13:10','2026-07-22 11:28:45',1,1,0),(16,'items_per_page','25','number','Default pagination size','2026-07-19 10:13:10','2026-07-22 11:28:45',1,1,0),(17,'enable_sidebar','1','boolean','Show sidebar by default','2026-07-19 10:13:10','2026-07-22 11:28:45',1,1,0),(18,'show_tooltips','1','boolean','Show help tooltips','2026-07-19 10:13:10','2026-07-22 11:28:45',1,1,0),(19,'language','en','text','Default language','2026-07-19 10:13:10','2026-07-22 11:32:42',1,1,0),(20,'timezone','UTC','text','System timezone','2026-07-19 10:13:10','2026-07-22 11:32:42',1,1,0),(21,'date_format','Y-m-d','text','Date format','2026-07-19 10:13:10','2026-07-22 11:32:42',1,1,0),(22,'time_format','H:i:s','text','Time format','2026-07-19 10:13:10','2026-07-22 11:32:42',1,1,0),(23,'maintenance_mode','0','boolean','Enable maintenance mode','2026-07-19 10:13:10','2026-07-19 10:13:10',1,1,0),(24,'debug_mode','0','boolean','Enable debug mode','2026-07-19 10:13:10','2026-07-19 10:13:10',1,1,0),(25,'enable_audit_log','1','boolean','Enable audit logging','2026-07-19 10:13:10','2026-07-22 11:33:06',1,1,0),(26,'email_smtp_host','smtp.gmail.com','text','SMTP server host','2026-07-19 10:13:10','2026-07-23 17:58:24',1,1,0),(27,'email_smtp_port','587','number','SMTP server port','2026-07-19 10:13:10','2026-07-23 17:58:24',1,1,0),(28,'email_smtp_username','qamaralizaine786@gmail.com','text','SMTP username','2026-07-19 10:13:10','2026-07-23 17:58:24',1,1,0),(29,'email_smtp_password','emoehxfuyxqpkscw','text','SMTP password','2026-07-19 10:13:10','2026-07-23 17:58:24',1,1,0),(30,'email_from_address','qamaralizaine786@gmail.com','email','Email from address','2026-07-19 10:13:10','2026-07-23 17:58:24',1,1,0),(31,'email_from_name','Inventory System','text','Email from name','2026-07-19 10:13:10','2026-07-23 17:58:24',1,1,0),(32,'email_encryption','ssl','text','Email encryption (tls/ssl/none)','2026-07-19 10:13:10','2026-07-23 17:58:24',1,1,0),(33,'email_smtp_enabled','1','boolean','Enable SMTP email','2026-07-19 10:13:10','2026-07-23 17:58:24',1,1,0),(34,'sms_api_provider','','text','SMS API provider','2026-07-19 10:13:10','2026-07-19 10:13:10',1,1,0),(35,'sms_api_key','','text','SMS API key','2026-07-19 10:13:10','2026-07-19 10:13:10',1,1,0),(36,'sms_api_secret','','text','SMS API secret','2026-07-19 10:13:10','2026-07-19 10:13:10',1,1,0),(37,'sms_sender_id','Inventory','text','SMS sender ID','2026-07-19 10:13:10','2026-07-19 10:13:10',1,1,0),(38,'sms_enabled','0','boolean','Enable SMS notifications','2026-07-19 10:13:10','2026-07-19 10:13:10',1,1,0),(39,'default_sales_account','7','text',NULL,'2026-07-20 12:31:27','2026-07-20 17:31:27',1,NULL,0),(40,'default_purchase_account','11','text',NULL,'2026-07-20 12:31:27','2026-07-20 17:31:27',1,NULL,0),(41,'default_expense_account','12','text',NULL,'2026-07-20 12:31:27','2026-07-20 17:31:27',1,NULL,0),(42,'default_refund_account','4','text',NULL,'2026-07-20 12:31:27','2026-07-20 17:31:27',1,NULL,0),(43,'default_cash_account','1','text',NULL,'2026-07-20 21:45:24','2026-07-20 21:45:24',NULL,NULL,0),(44,'navbar_color','#438eb9','text',NULL,'2026-07-22 11:12:54','2026-07-22 16:15:23',1,NULL,0);
/*!40000 ALTER TABLE `inventory_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_sms_config`
--

DROP TABLE IF EXISTS `inventory_sms_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_sms_config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `api_provider` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_key` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_secret` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sender_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_enabled` tinyint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_sms_config`
--

LOCK TABLES `inventory_sms_config` WRITE;
/*!40000 ALTER TABLE `inventory_sms_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_sms_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_stock`
--

DROP TABLE IF EXISTS `inventory_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_stock` (
  `id` int NOT NULL AUTO_INCREMENT,
  `warehouse_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` decimal(15,2) DEFAULT '0.00',
  `reserved_quantity` decimal(15,2) DEFAULT '0.00',
  `available_quantity` decimal(15,2) DEFAULT '0.00',
  `average_cost` decimal(15,2) DEFAULT '0.00',
  `last_purchase_price` decimal(15,2) DEFAULT '0.00',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`,`warehouse_id`),
  KEY `product_id_2` (`product_id`),
  KEY `warehouse_id` (`warehouse_id`),
  CONSTRAINT `inventory_stock_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `inventory_products` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `inventory_stock_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `inventory_warehouses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=217 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_stock`
--

LOCK TABLES `inventory_stock` WRITE;
/*!40000 ALTER TABLE `inventory_stock` DISABLE KEYS */;
INSERT INTO `inventory_stock` VALUES (1,1,1,332.00,0.00,332.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(2,1,2,82.00,0.00,82.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(3,1,3,414.00,0.00,414.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(4,1,4,92.00,0.00,92.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(5,1,5,231.00,0.00,231.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(6,1,6,89.00,0.00,89.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(7,1,7,104.00,0.00,104.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(8,1,8,79.00,0.00,79.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(9,1,9,328.00,0.00,328.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(10,1,10,189.00,0.00,189.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(11,1,11,283.00,0.00,283.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(12,1,12,97.00,0.00,97.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(13,1,13,376.00,0.00,376.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(14,1,14,266.00,0.00,266.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(15,1,15,385.00,0.00,385.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(16,1,16,66.00,0.00,66.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(17,1,17,432.00,0.00,432.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(18,1,18,200.00,0.00,200.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(19,1,19,274.00,0.00,274.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(20,1,20,491.00,0.00,491.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(21,1,21,230.00,0.00,226.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-25 04:41:36',1,NULL,1,0),(22,1,22,437.00,0.00,437.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(23,1,23,303.00,0.00,303.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(24,1,24,481.00,0.00,481.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(25,1,25,272.00,0.00,272.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(26,1,26,223.00,0.00,223.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(27,1,27,299.00,0.00,299.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(28,1,28,113.00,0.00,113.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(29,1,29,349.00,0.00,349.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(30,1,30,222.00,0.00,222.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(31,1,31,340.00,0.00,340.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(32,1,32,325.00,0.00,325.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(33,1,33,145.00,0.00,145.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(34,1,34,50.00,0.00,50.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(35,1,35,140.00,0.00,140.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(36,1,36,482.00,0.00,482.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(37,1,37,289.00,0.00,289.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(38,1,38,314.00,0.00,314.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(39,1,39,59.00,0.00,59.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(40,1,40,138.00,0.00,138.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(41,1,41,433.00,0.00,433.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(42,1,42,118.00,0.00,118.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(43,1,43,76.00,0.00,76.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(44,1,44,346.00,0.00,346.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(45,1,45,343.00,0.00,339.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-25 04:41:36',1,NULL,1,0),(46,1,46,379.00,0.00,379.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(47,1,47,164.00,0.00,164.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(48,1,48,299.00,0.00,299.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(49,1,49,191.00,0.00,191.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(50,1,50,70.00,0.00,70.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(51,1,51,488.00,0.00,488.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(52,1,52,467.00,0.00,467.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(53,1,53,119.00,0.00,119.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(54,1,54,450.00,0.00,450.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(55,1,55,137.00,0.00,137.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(56,1,56,227.00,0.00,227.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(57,1,57,389.00,0.00,389.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(58,1,58,441.00,0.00,441.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(59,1,59,190.00,0.00,190.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(60,1,60,356.00,0.00,356.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(61,1,61,268.00,0.00,268.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(62,1,62,165.00,0.00,165.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(63,1,63,202.00,0.00,202.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(64,1,64,390.00,0.00,390.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(65,1,65,361.00,0.00,361.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(66,1,66,480.00,0.00,480.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(67,1,67,376.00,0.00,376.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(68,1,68,275.00,0.00,275.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(69,1,69,323.00,0.00,323.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(70,1,70,251.00,0.00,251.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(71,1,71,403.00,0.00,403.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(72,1,72,73.00,0.00,73.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(73,1,73,464.00,0.00,464.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(74,1,74,333.00,0.00,333.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(75,1,75,220.00,0.00,220.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(76,1,76,384.00,0.00,384.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(77,1,77,375.00,0.00,375.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(78,1,78,432.00,0.00,432.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(79,1,79,191.00,0.00,191.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(80,1,80,230.00,0.00,230.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(81,1,81,226.00,0.00,226.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(82,1,82,114.00,0.00,114.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(83,1,83,263.00,0.00,263.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(84,1,84,115.00,0.00,115.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(85,1,85,164.00,0.00,164.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(86,1,86,57.00,0.00,57.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(87,1,87,408.00,0.00,408.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(88,1,88,217.00,0.00,217.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(89,1,89,487.00,0.00,487.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(90,1,90,226.00,0.00,226.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(91,1,91,302.00,0.00,302.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(92,1,92,274.00,0.00,274.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(93,1,93,494.00,0.00,494.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(94,1,94,353.00,0.00,353.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(95,1,95,245.00,0.00,245.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(96,1,96,104.00,0.00,104.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(97,1,97,181.00,0.00,181.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(98,1,98,429.00,0.00,429.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(99,1,99,309.00,0.00,309.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(100,1,100,85.00,0.00,85.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(101,1,101,341.00,0.00,341.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(102,1,102,205.00,0.00,205.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(103,1,103,151.00,0.00,151.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(104,1,104,377.00,0.00,377.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(105,1,105,166.00,0.00,166.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(106,1,106,157.00,0.00,157.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(107,1,107,470.00,0.00,470.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(108,1,108,226.00,0.00,226.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(109,1,109,349.00,0.00,349.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(110,1,110,373.00,0.00,373.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(111,1,111,376.00,0.00,376.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(112,1,112,102.00,0.00,102.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(113,1,113,192.00,0.00,192.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(114,1,114,407.00,0.00,407.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(115,1,115,479.00,0.00,479.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(116,1,116,228.00,0.00,228.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(117,1,117,197.00,0.00,197.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(118,1,118,108.00,0.00,108.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(119,1,119,298.00,0.00,298.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(120,1,120,336.00,0.00,336.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(121,1,121,326.00,0.00,326.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(122,1,122,218.00,0.00,218.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(123,1,123,53.00,0.00,53.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(124,1,124,205.00,0.00,205.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(125,1,125,372.00,0.00,372.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(126,1,126,91.00,0.00,91.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(127,1,127,447.00,0.00,447.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(128,1,128,258.00,0.00,258.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(129,1,129,122.00,0.00,122.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(130,1,130,388.00,0.00,388.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(131,1,131,472.00,0.00,472.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(132,1,132,251.00,0.00,251.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(133,1,133,391.00,0.00,391.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(134,1,134,230.00,0.00,230.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(135,1,135,261.00,0.00,261.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(136,1,136,356.00,0.00,356.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(137,1,137,465.00,0.00,465.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(138,1,138,387.00,0.00,387.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(139,1,139,114.00,0.00,114.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(140,1,140,464.00,0.00,464.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(141,1,141,159.00,0.00,159.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(142,1,142,353.00,0.00,353.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(143,1,143,152.00,0.00,152.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(144,1,144,120.00,0.00,120.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(145,1,145,344.00,0.00,344.00,0.00,0.00,'2026-07-23 11:45:49','2026-07-23 11:45:49',1,NULL,1,0),(146,1,146,477.00,0.00,477.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(147,1,147,210.00,0.00,210.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(148,1,148,137.00,0.00,137.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(149,1,149,252.00,0.00,252.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(150,1,150,461.00,0.00,461.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(151,1,151,136.00,0.00,136.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(152,1,152,130.00,0.00,130.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(153,1,153,277.00,0.00,277.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(154,1,154,216.00,0.00,216.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(155,1,155,423.00,0.00,423.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(156,1,156,320.00,0.00,320.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(157,1,157,140.00,0.00,140.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(158,1,158,350.00,0.00,350.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(159,1,159,242.00,0.00,242.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(160,1,160,426.00,0.00,426.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(161,1,161,66.00,0.00,66.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(162,1,162,295.00,0.00,295.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(163,1,163,103.00,0.00,103.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(164,1,164,153.00,0.00,153.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(165,1,165,159.00,0.00,159.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(166,1,166,105.00,0.00,105.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(167,1,167,419.00,0.00,419.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(168,1,168,325.00,0.00,325.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(169,1,169,341.00,0.00,341.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(170,1,170,350.00,0.00,350.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(171,1,171,476.00,0.00,476.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(172,1,172,301.00,0.00,301.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(173,1,173,135.00,0.00,135.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(174,1,174,62.00,0.00,62.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(175,1,175,131.00,0.00,131.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(176,1,176,411.00,0.00,411.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(177,1,177,333.00,0.00,333.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(178,1,178,107.00,0.00,107.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(179,1,179,204.00,0.00,204.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(180,1,180,494.00,0.00,494.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(181,1,181,55.00,0.00,55.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(182,1,182,340.00,0.00,340.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(183,1,183,158.00,0.00,158.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(184,1,184,434.00,0.00,434.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(185,1,185,220.00,0.00,220.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(186,1,186,307.00,0.00,307.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(187,1,187,228.00,0.00,228.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(188,1,188,367.00,0.00,367.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(189,1,189,420.00,0.00,420.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(190,1,190,191.00,0.00,191.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(191,1,191,270.00,0.00,270.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(192,1,192,497.00,0.00,497.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(193,1,193,108.00,0.00,108.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(194,1,194,252.00,0.00,252.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(195,1,195,327.00,0.00,327.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(196,1,196,67.00,0.00,67.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(197,1,197,151.00,0.00,151.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(198,1,198,292.00,0.00,292.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(199,1,199,367.00,0.00,367.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(200,1,200,58.00,0.00,58.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(201,1,201,206.00,0.00,206.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(202,1,202,487.00,0.00,487.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(203,1,203,179.00,0.00,179.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(204,1,204,402.00,0.00,402.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(205,1,205,328.00,0.00,328.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(206,1,206,462.00,0.00,462.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(207,1,207,497.00,0.00,497.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(208,1,208,435.00,0.00,435.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(209,1,209,234.00,0.00,234.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(210,1,210,379.00,0.00,379.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(211,1,211,177.00,0.00,177.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(212,1,212,350.00,0.00,350.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(213,1,213,152.00,0.00,152.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(214,1,214,207.00,0.00,207.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(215,1,215,245.00,0.00,245.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0),(216,1,216,266.00,0.00,266.00,0.00,0.00,'2026-07-23 11:45:50','2026-07-23 11:45:50',1,NULL,1,0);
/*!40000 ALTER TABLE `inventory_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_stock_adjustment_items`
--

DROP TABLE IF EXISTS `inventory_stock_adjustment_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_stock_adjustment_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `adjustment_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` decimal(15,2) DEFAULT NULL,
  `unit_cost` decimal(15,2) DEFAULT NULL,
  `total_cost` decimal(15,2) DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `adjustment_id` (`adjustment_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `inventory_stock_adjustment_items_ibfk_1` FOREIGN KEY (`adjustment_id`) REFERENCES `inventory_stock_adjustments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_stock_adjustment_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `inventory_products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_stock_adjustment_items`
--

LOCK TABLES `inventory_stock_adjustment_items` WRITE;
/*!40000 ALTER TABLE `inventory_stock_adjustment_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_stock_adjustment_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_stock_adjustments`
--

DROP TABLE IF EXISTS `inventory_stock_adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_stock_adjustments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `adjustment_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `warehouse_id` int NOT NULL,
  `adjustment_date` date DEFAULT NULL,
  `adjustment_type` enum('Increase','Decrease') COLLATE utf8mb4_general_ci NOT NULL,
  `reason` enum('Damage','Expired','Lost','Correction','Other') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `adjustment_no` (`adjustment_no`),
  KEY `warehouse_id` (`warehouse_id`),
  CONSTRAINT `inventory_stock_adjustments_ibfk_1` FOREIGN KEY (`warehouse_id`) REFERENCES `inventory_warehouses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_stock_adjustments`
--

LOCK TABLES `inventory_stock_adjustments` WRITE;
/*!40000 ALTER TABLE `inventory_stock_adjustments` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_stock_adjustments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_stock_audit_items`
--

DROP TABLE IF EXISTS `inventory_stock_audit_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_stock_audit_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `audit_id` int NOT NULL,
  `product_id` int NOT NULL,
  `system_quantity` decimal(15,2) DEFAULT NULL,
  `physical_quantity` decimal(15,2) DEFAULT NULL,
  `variance` decimal(15,2) DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `audit_id` (`audit_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `inventory_stock_audit_items_ibfk_1` FOREIGN KEY (`audit_id`) REFERENCES `inventory_stock_audits` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_stock_audit_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `inventory_products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_stock_audit_items`
--

LOCK TABLES `inventory_stock_audit_items` WRITE;
/*!40000 ALTER TABLE `inventory_stock_audit_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_stock_audit_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_stock_audits`
--

DROP TABLE IF EXISTS `inventory_stock_audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_stock_audits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `audit_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `warehouse_id` int DEFAULT NULL,
  `audit_date` date DEFAULT NULL,
  `status` enum('Open','Completed') COLLATE utf8mb4_general_ci DEFAULT 'Open',
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `audit_no` (`audit_no`),
  KEY `warehouse_id` (`warehouse_id`),
  CONSTRAINT `inventory_stock_audits_ibfk_1` FOREIGN KEY (`warehouse_id`) REFERENCES `inventory_warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_stock_audits`
--

LOCK TABLES `inventory_stock_audits` WRITE;
/*!40000 ALTER TABLE `inventory_stock_audits` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_stock_audits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_stock_movements`
--

DROP TABLE IF EXISTS `inventory_stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_stock_movements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `movement_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `warehouse_id` int NOT NULL,
  `product_id` int NOT NULL,
  `reference_type` enum('Purchase','Sale','Transfer In','Transfer Out','Adjustment','Return Purchase','Return Sale','Opening Stock','Stock Audit') COLLATE utf8mb4_general_ci NOT NULL,
  `reference_id` int DEFAULT NULL,
  `movement_type` enum('IN','OUT') COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `unit_cost` decimal(15,2) DEFAULT '0.00',
  `total_cost` decimal(15,2) DEFAULT '0.00',
  `remarks` text COLLATE utf8mb4_general_ci,
  `movement_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `movement_no` (`movement_no`),
  KEY `product_id` (`product_id`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `reference_id` (`reference_id`),
  CONSTRAINT `inventory_stock_movements_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `inventory_products` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `inventory_stock_movements_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `inventory_warehouses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_stock_movements`
--

LOCK TABLES `inventory_stock_movements` WRITE;
/*!40000 ALTER TABLE `inventory_stock_movements` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_stock_transfer_items`
--

DROP TABLE IF EXISTS `inventory_stock_transfer_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_stock_transfer_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transfer_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` decimal(15,2) DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `transfer_id` (`transfer_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `inventory_stock_transfer_items_ibfk_1` FOREIGN KEY (`transfer_id`) REFERENCES `inventory_stock_transfers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_stock_transfer_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `inventory_products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_stock_transfer_items`
--

LOCK TABLES `inventory_stock_transfer_items` WRITE;
/*!40000 ALTER TABLE `inventory_stock_transfer_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_stock_transfer_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_stock_transfers`
--

DROP TABLE IF EXISTS `inventory_stock_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_stock_transfers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transfer_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `from_warehouse` int NOT NULL,
  `to_warehouse` int NOT NULL,
  `transfer_date` date DEFAULT NULL,
  `status` enum('Pending','In Transit','Completed','Cancelled') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `transfer_no` (`transfer_no`),
  KEY `from_warehouse` (`from_warehouse`),
  KEY `to_warehouse` (`to_warehouse`),
  CONSTRAINT `inventory_stock_transfers_ibfk_1` FOREIGN KEY (`from_warehouse`) REFERENCES `inventory_warehouses` (`id`),
  CONSTRAINT `inventory_stock_transfers_ibfk_2` FOREIGN KEY (`to_warehouse`) REFERENCES `inventory_warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_stock_transfers`
--

LOCK TABLES `inventory_stock_transfers` WRITE;
/*!40000 ALTER TABLE `inventory_stock_transfers` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_stock_transfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_supplier_contacts`
--

DROP TABLE IF EXISTS `inventory_supplier_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_supplier_contacts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_id` int NOT NULL,
  `contact_name` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `designation` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobile` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `inventory_supplier_contacts_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `inventory_suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_supplier_contacts`
--

LOCK TABLES `inventory_supplier_contacts` WRITE;
/*!40000 ALTER TABLE `inventory_supplier_contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_supplier_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_supplier_documents`
--

DROP TABLE IF EXISTS `inventory_supplier_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_supplier_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_id` int NOT NULL,
  `document_type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `document_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `document_file` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `inventory_supplier_documents_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `inventory_suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_supplier_documents`
--

LOCK TABLES `inventory_supplier_documents` WRITE;
/*!40000 ALTER TABLE `inventory_supplier_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_supplier_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_suppliers`
--

DROP TABLE IF EXISTS `inventory_suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_suppliers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company_name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `contact_person` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobile` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tax_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_terms` int DEFAULT '30',
  `credit_limit` decimal(15,2) DEFAULT '0.00',
  `opening_balance` decimal(15,2) DEFAULT '0.00',
  `current_balance` decimal(15,2) DEFAULT '0.00',
  `address` text COLLATE utf8mb4_general_ci,
  `city` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_code` (`supplier_code`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_suppliers`
--

LOCK TABLES `inventory_suppliers` WRITE;
/*!40000 ALTER TABLE `inventory_suppliers` DISABLE KEYS */;
INSERT INTO `inventory_suppliers` VALUES (1,'SUP-0001','Global Supplies Co','Contact 1','supplier1@example.com','042-7024612','03084001247',NULL,NULL,30,0.00,0.00,0.00,'Address 1','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0),(2,'SUP-0002','Tech Imports Ltd','Contact 2','supplier2@example.com','042-7357478','03028119227',NULL,NULL,30,0.00,0.00,0.00,'Address 2','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(3,'SUP-0003','Premier Distributors','Contact 3','supplier3@example.com','042-2398584','03031595860',NULL,NULL,30,0.00,0.00,0.00,'Address 3','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(4,'SUP-0004','Quality Products Inc','Contact 4','supplier4@example.com','042-2250294','03012585753',NULL,NULL,30,0.00,0.00,0.00,'Address 4','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(5,'SUP-0005','Metro Electronics','Contact 5','supplier5@example.com','042-4892206','03069720034',NULL,NULL,30,0.00,0.00,0.00,'Address 5','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(6,'SUP-0006','Elite Supplies','Contact 6','supplier6@example.com','042-8642598','03064512567',NULL,NULL,30,0.00,0.00,0.00,'Address 6','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(7,'SUP-0007','Standard Goods LLC','Contact 7','supplier7@example.com','042-5193589','03061418403',NULL,NULL,30,0.00,0.00,0.00,'Address 7','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(8,'SUP-0008','Interstate Trading','Contact 8','supplier8@example.com','042-4593891','03052989891',NULL,NULL,30,0.00,0.00,0.00,'Address 8','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(9,'SUP-0009','Pinnacle Distributors','Contact 9','supplier9@example.com','042-9846691','03022091825',NULL,NULL,30,0.00,0.00,0.00,'Address 9','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(10,'SUP-0010','Crown Suppliers','Contact 10','supplier10@example.com','042-5343575','03025471818',NULL,NULL,30,0.00,0.00,0.00,'Address 10','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(11,'SUP-0011','Expert Suppliers','Contact 11','supplier11@example.com','042-3089832','03055958558',NULL,NULL,30,0.00,0.00,0.00,'Address 11','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(12,'SUP-0012','Trusted Goods Co','Contact 12','supplier12@example.com','042-7837173','03078965984',NULL,NULL,30,0.00,0.00,0.00,'Address 12','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(13,'SUP-0013','Summit Trading','Contact 13','supplier13@example.com','042-5462573','03047330556',NULL,NULL,30,0.00,0.00,0.00,'Address 13','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(14,'SUP-0014','Valley Distributors','Contact 14','supplier14@example.com','042-9688712','03061567013',NULL,NULL,30,0.00,0.00,0.00,'Address 14','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(15,'SUP-0015','Rainbow Supplies','Contact 15','supplier15@example.com','042-6314817','03069451792',NULL,NULL,30,0.00,0.00,0.00,'Address 15','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(16,'SUP-0016','Phoenix Imports','Contact 16','supplier16@example.com','042-7324253','03095083241',NULL,NULL,30,0.00,0.00,0.00,'Address 16','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(17,'SUP-0017','Classic Goods','Contact 17','supplier17@example.com','042-5799937','03038764071',NULL,NULL,30,0.00,0.00,0.00,'Address 17','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(18,'SUP-0018','Venture Supplies','Contact 18','supplier18@example.com','042-9358974','03032565633',NULL,NULL,30,0.00,0.00,0.00,'Address 18','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(19,'SUP-0019','Unity Trading','Contact 19','supplier19@example.com','042-6042808','03013926434',NULL,NULL,30,0.00,0.00,0.00,'Address 19','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(20,'SUP-0020','Zenith Distributors','Contact 20','supplier20@example.com','042-4548220','03028572573',NULL,NULL,30,0.00,0.00,0.00,'Address 20','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(21,'SUP-0021','Alpha Electronics','Contact 21','supplier21@example.com','042-2527347','03025666232',NULL,NULL,30,0.00,0.00,0.00,'Address 21','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(22,'SUP-0022','Beta Supplies','Contact 22','supplier22@example.com','042-5978530','03098754366',NULL,NULL,30,0.00,0.00,0.00,'Address 22','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(23,'SUP-0023','Gamma Imports','Contact 23','supplier23@example.com','042-9133115','03032832450',NULL,NULL,30,0.00,0.00,0.00,'Address 23','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(24,'SUP-0024','Delta Trading','Contact 24','supplier24@example.com','042-9304467','03015507843',NULL,NULL,30,0.00,0.00,0.00,'Address 24','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(25,'SUP-0025','Epsilon Goods','Contact 25','supplier25@example.com','042-7920230','03052593111',NULL,NULL,30,0.00,0.00,0.00,'Address 25','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(26,'SUP-0026','Zeta Distributors','Contact 26','supplier26@example.com','042-4739418','03067923346',NULL,NULL,30,0.00,0.00,0.00,'Address 26','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(27,'SUP-0027','Eta Supplies','Contact 27','supplier27@example.com','042-7300413','03039317862',NULL,NULL,30,0.00,0.00,0.00,'Address 27','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(28,'SUP-0028','Theta Trading','Contact 28','supplier28@example.com','042-1628825','03016005207',NULL,NULL,30,0.00,0.00,0.00,'Address 28','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(29,'SUP-0029','Iota Electronics','Contact 29','supplier29@example.com','042-3359433','03061304177',NULL,NULL,30,0.00,0.00,0.00,'Address 29','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(30,'SUP-0030','Kappa Imports','Contact 30','supplier30@example.com','042-8051155','03093918380',NULL,NULL,30,0.00,0.00,0.00,'Address 30','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(31,'SUP-0031','Lambda Goods','Contact 31','supplier31@example.com','042-4359617','03074127970',NULL,NULL,30,0.00,0.00,0.00,'Address 31','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(32,'SUP-0032','Mu Supplies','Contact 32','supplier32@example.com','042-7684644','03022781018',NULL,NULL,30,0.00,0.00,0.00,'Address 32','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(33,'SUP-0033','Nu Trading','Contact 33','supplier33@example.com','042-4521523','03071619318',NULL,NULL,30,0.00,0.00,0.00,'Address 33','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(34,'SUP-0034','Xi Distributors','Contact 34','supplier34@example.com','042-4251819','03043425472',NULL,NULL,30,0.00,0.00,0.00,'Address 34','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(35,'SUP-0035','Omicron Imports','Contact 35','supplier35@example.com','042-1911338','03065276479',NULL,NULL,30,0.00,0.00,0.00,'Address 35','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(36,'SUP-0036','Pi Supplies','Contact 36','supplier36@example.com','042-3088128','03023624384',NULL,NULL,30,0.00,0.00,0.00,'Address 36','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(37,'SUP-0037','Rho Trading','Contact 37','supplier37@example.com','042-4672201','03049699218',NULL,NULL,30,0.00,0.00,0.00,'Address 37','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(38,'SUP-0038','Sigma Goods','Contact 38','supplier38@example.com','042-4003386','03034955661',NULL,NULL,30,0.00,0.00,0.00,'Address 38','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(39,'SUP-0039','Tau Electronics','Contact 39','supplier39@example.com','042-8809093','03058110061',NULL,NULL,30,0.00,0.00,0.00,'Address 39','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(40,'SUP-0040','Upsilon Imports','Contact 40','supplier40@example.com','042-5860105','03050243170',NULL,NULL,30,0.00,0.00,0.00,'Address 40','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(41,'SUP-0041','Phi Supplies','Contact 41','supplier41@example.com','042-4583140','03058720890',NULL,NULL,30,0.00,0.00,0.00,'Address 41','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(42,'SUP-0042','Chi Trading','Contact 42','supplier42@example.com','042-2767316','03080460513',NULL,NULL,30,0.00,0.00,0.00,'Address 42','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(43,'SUP-0043','Psi Distributors','Contact 43','supplier43@example.com','042-3043480','03080163985',NULL,NULL,30,0.00,0.00,0.00,'Address 43','Lahore','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(44,'SUP-0044','Omega Goods','Contact 44','supplier44@example.com','042-4109265','03074515707',NULL,NULL,30,0.00,0.00,0.00,'Address 44','Peshawar','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(45,'SUP-0045','Asia Traders','Contact 45','supplier45@example.com','042-7393262','03081092285',NULL,NULL,30,0.00,0.00,0.00,'Address 45','Quetta','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(46,'SUP-0046','Pak Supplies','Contact 46','supplier46@example.com','042-5021734','03063559480',NULL,NULL,30,0.00,0.00,0.00,'Address 46','Multan','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(47,'SUP-0047','Global Trade','Contact 47','supplier47@example.com','042-1558947','03042583575',NULL,NULL,30,0.00,0.00,0.00,'Address 47','Faisalabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(48,'SUP-0048','Continental','Contact 48','supplier48@example.com','042-5421771','03083365749',NULL,NULL,30,0.00,0.00,0.00,'Address 48','Rawalpindi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(49,'SUP-0049','Universal Supplies','Contact 49','supplier49@example.com','042-9843757','03096628544',NULL,NULL,30,0.00,0.00,0.00,'Address 49','Islamabad','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0),(50,'SUP-0050','Prime Goods','Contact 50','supplier50@example.com','042-1454040','03075843806',NULL,NULL,30,0.00,0.00,0.00,'Address 50','Karachi','Punjab','Pakistan',NULL,NULL,'2026-07-23 11:45:48','2026-07-23 11:45:48',1,NULL,1,0);
/*!40000 ALTER TABLE `inventory_suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_tax_rates`
--

DROP TABLE IF EXISTS `inventory_tax_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_tax_rates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tax_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_percentage` decimal(5,2) DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_default` tinyint DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_default` (`is_default`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_tax_rates`
--

LOCK TABLES `inventory_tax_rates` WRITE;
/*!40000 ALTER TABLE `inventory_tax_rates` DISABLE KEYS */;
INSERT INTO `inventory_tax_rates` VALUES (1,'No Tax',0.00,NULL,1,'2026-07-19 10:13:10','2026-07-19 10:13:10',1,1,1,0),(2,'Standard VAT',15.00,NULL,0,'2026-07-19 10:13:10','2026-07-19 10:13:10',1,1,1,0),(3,'Reduced VAT',7.00,NULL,0,'2026-07-19 10:13:10','2026-07-19 10:13:10',1,1,1,0),(4,'Sales Tax',8.00,NULL,0,'2026-07-19 10:13:10','2026-07-19 10:13:10',1,1,1,0),(5,'Service Tax',5.00,NULL,0,'2026-07-19 10:13:10','2026-07-19 10:13:10',1,1,1,0),(6,'GST',10.00,NULL,0,'2026-07-19 10:13:10','2026-07-19 10:13:10',1,1,1,0);
/*!40000 ALTER TABLE `inventory_tax_rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_transactions`
--

DROP TABLE IF EXISTS `inventory_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaction_no` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_date` date DEFAULT NULL,
  `reference_type` enum('Purchase','Sale','Payment','Receipt','Expense','Adjustment') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `account_id` int NOT NULL,
  `transaction_type` enum('Debit','Credit') COLLATE utf8mb4_general_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_no` (`transaction_no`),
  KEY `account_id` (`account_id`),
  CONSTRAINT `inventory_transactions_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `inventory_accounts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_transactions`
--

LOCK TABLES `inventory_transactions` WRITE;
/*!40000 ALTER TABLE `inventory_transactions` DISABLE KEYS */;
INSERT INTO `inventory_transactions` VALUES (1,'PAYMENT-INV-20260721113251-956-DR','2026-07-21','Sale',2,1,'Debit',30000.00,'Sale Payment Received - Invoice: INV-20260721113251-956','2026-07-21 11:42:12','2026-07-21 16:42:12',1,NULL,1,0),(2,'PAYMENT-INV-20260721113251-956-CR','2026-07-21','Sale',2,14,'Credit',30000.00,'Sale Payment - Reduce AR - Invoice: INV-20260721113251-956','2026-07-21 11:42:12','2026-07-21 16:42:12',1,NULL,1,0),(3,'PURCH-PINV-2026-00001-DR','2026-07-21','Purchase',1,11,'Debit',95000.00,'Purchase recorded - Invoice: PINV-2026-00001','2026-07-21 17:26:50','2026-07-21 22:26:50',1,NULL,1,0),(4,'PURCH-PINV-2026-00001-CR','2026-07-21','Purchase',1,4,'Credit',95000.00,'Accounts Payable - Invoice: PINV-2026-00001','2026-07-21 17:26:50','2026-07-21 22:26:50',1,NULL,1,0),(5,'PAYMENT-INV-20260721183022-239-DR','2026-07-21','Sale',2,1,'Debit',4500.00,'Sale Payment Received - Invoice: INV-20260721183022-239','2026-07-21 18:31:08','2026-07-21 23:31:08',1,NULL,1,0),(6,'PAYMENT-INV-20260721183022-239-CR','2026-07-21','Sale',2,14,'Credit',4500.00,'Sale Payment - Reduce AR - Invoice: INV-20260721183022-239','2026-07-21 18:31:08','2026-07-21 23:31:08',1,NULL,1,0),(7,'PURCH-PINV-2026-00002-DR','2026-07-22','Purchase',2,11,'Debit',44000.00,'Purchase recorded - Invoice: PINV-2026-00002','2026-07-22 05:10:06','2026-07-22 10:10:06',1,NULL,1,0),(8,'PURCH-PINV-2026-00002-CR','2026-07-22','Purchase',2,4,'Credit',44000.00,'Accounts Payable - Invoice: PINV-2026-00002','2026-07-22 05:10:06','2026-07-22 10:10:06',1,NULL,1,0),(9,'PAYMENT-INV-20260722055342-706-DR','2026-07-22','Sale',3,1,'Debit',26500.00,'Sale Payment Received - Invoice: INV-20260722055342-706','2026-07-22 05:54:41','2026-07-22 10:54:41',1,NULL,1,0),(10,'PAYMENT-INV-20260722055342-706-CR','2026-07-22','Sale',3,14,'Credit',26500.00,'Sale Payment - Reduce AR - Invoice: INV-20260722055342-706','2026-07-22 05:54:41','2026-07-22 10:54:41',1,NULL,1,0),(12,'PAYMENT-INV-20260725043845-437-DR','2026-07-25','Sale',1,1,'Debit',50000.00,'Sale Payment Received - Invoice: INV-20260725043845-437','2026-07-25 04:39:17','2026-07-25 09:39:17',2,NULL,1,0),(13,'PAYMENT-INV-20260725043845-437-CR','2026-07-25','Sale',1,14,'Credit',50000.00,'Sale Payment - Reduce AR - Invoice: INV-20260725043845-437','2026-07-25 04:39:17','2026-07-25 09:39:17',2,NULL,1,0);
/*!40000 ALTER TABLE `inventory_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_units`
--

DROP TABLE IF EXISTS `inventory_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_units` (
  `id` int NOT NULL AUTO_INCREMENT,
  `unit_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `short_name` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_units`
--

LOCK TABLES `inventory_units` WRITE;
/*!40000 ALTER TABLE `inventory_units` DISABLE KEYS */;
INSERT INTO `inventory_units` VALUES (1,'Piece','PCS','Single item!','2026-07-14 22:12:49','2026-07-14 20:18:11',NULL,1,1,0),(2,'Set','SET','Complete set','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(3,'Pair','PAIR','Two items','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(4,'Box','BOX','Box packing','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(5,'Bottle','BOT','Liquid bottle','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(6,'Liter','LTR','Liquid measurement','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(7,'Milliliter','ML','Small liquid measurement','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(8,'Kilogram','KG','Weight measurement','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(9,'Gram','GM','Small weight measurement','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(10,'Roll','ROLL','Roll packing','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(11,'Pack','PACK','Package packing','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(12,'Carton','CTN','Carton packing','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0);
/*!40000 ALTER TABLE `inventory_units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_vehicle_makes`
--

DROP TABLE IF EXISTS `inventory_vehicle_makes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_vehicle_makes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `make_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `make_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `make_code` (`make_code`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_vehicle_makes`
--

LOCK TABLES `inventory_vehicle_makes` WRITE;
/*!40000 ALTER TABLE `inventory_vehicle_makes` DISABLE KEYS */;
INSERT INTO `inventory_vehicle_makes` VALUES (1,'Toyota','TOY','Japan','https://www.toyota.com','Japanese automobile manufacturer','2026-07-14 22:12:49','2026-07-14 20:26:53',NULL,1,1,0),(2,'Honda','HON','Japan','https://www.honda.com','Japanese automobile manufacturer','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(3,'Suzuki','SUZ','Japan','https://www.suzuki.co.jp','Japanese automobile manufacturer','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(4,'Hyundai','HYU','South Korea','https://www.hyundai.com','Korean automobile manufacturer','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(5,'Kia','KIA','South Korea','https://www.kia.com','Korean automobile manufacturer','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(6,'Nissan','NIS','Japan','https://www.nissan-global.com','Japanese automobile manufacturer','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(7,'Mitsubishi','MIT','Japan','https://www.mitsubishi-motors.com','Japanese automobile manufacturer','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(8,'BMW','BMW','Germany','https://www.bmw.com','German automobile manufacturer','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(9,'Mercedes Benz','MER','Germany','https://www.mercedes-benz.com','German automobile manufacturer','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(10,'Ford','FOR','USA','https://www.ford.com','American automobile manufacturer','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(11,'Volkswagen','VW','Germany','https://www.volkswagen.com','German automobile manufacturer','2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(12,'Audi','AUD','Germany','https://www.audi.com','German automobile manufacturer','2026-07-14 22:12:49','2026-07-14 23:27:16',NULL,1,1,0);
/*!40000 ALTER TABLE `inventory_vehicle_makes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_vehicle_models`
--

DROP TABLE IF EXISTS `inventory_vehicle_models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_vehicle_models` (
  `id` int NOT NULL AUTO_INCREMENT,
  `make_id` int NOT NULL,
  `model_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `model_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `model_year` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `engine_type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `engine_capacity` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fuel_type` enum('Petrol','Diesel','Hybrid','Electric','CNG') COLLATE utf8mb4_general_ci DEFAULT 'Petrol',
  `transmission` enum('Manual','Automatic','CVT') COLLATE utf8mb4_general_ci DEFAULT 'Manual',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `make_id` (`make_id`),
  CONSTRAINT `inventory_vehicle_models_ibfk_1` FOREIGN KEY (`make_id`) REFERENCES `inventory_vehicle_makes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_vehicle_models`
--

LOCK TABLES `inventory_vehicle_models` WRITE;
/*!40000 ALTER TABLE `inventory_vehicle_models` DISABLE KEYS */;
INSERT INTO `inventory_vehicle_models` VALUES (1,1,'Corolla','COR','2008-2025','4 Cylinder','1300cc','Petrol','Automatic','','2026-07-14 22:12:49','2026-07-14 20:32:13',NULL,1,1,0),(2,1,'Yaris','YAR','2020-2025','4 Cylinder','1500cc','Petrol','CVT',NULL,'2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(3,1,'Hilux','HIL','2015-2025','Diesel','2800cc','Diesel','Automatic',NULL,'2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(4,2,'Civic','CIV','2016-2025','Turbo','1500cc','Petrol','CVT',NULL,'2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(5,2,'City','CITY','2009-2025','4 Cylinder','1500cc','Petrol','CVT',NULL,'2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(6,3,'Alto','ALT','2019-2025','3 Cylinder','660cc','Petrol','Automatic',NULL,'2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(7,3,'Swift','SWF','2010-2025','4 Cylinder','1200cc','Petrol','Automatic',NULL,'2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(8,4,'Tucson','TUC','2020-2025','4 Cylinder','2000cc','Petrol','Automatic',NULL,'2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(9,5,'Sportage','SPT','2020-2025','4 Cylinder','2000cc','Petrol','Automatic',NULL,'2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(10,6,'Sunny','SUN','2012-2025','4 Cylinder','1500cc','Petrol','Automatic',NULL,'2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(11,7,'Pajero','PAJ','2010-2020','Diesel','3200cc','Diesel','Automatic',NULL,'2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(12,8,'3 Series','BMW3','2015-2025','Turbo','2000cc','Petrol','Automatic',NULL,'2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(13,9,'C Class','C200','2015-2025','Turbo','2000cc','Petrol','Automatic',NULL,'2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0),(14,10,'Ranger','RNG','2015-2025','Diesel','3000cc','Diesel','Automatic',NULL,'2026-07-14 22:12:49','2026-07-14 22:12:49',NULL,NULL,1,0);
/*!40000 ALTER TABLE `inventory_vehicle_models` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_warehouses`
--

DROP TABLE IF EXISTS `inventory_warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_warehouses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `warehouse_name` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `warehouse_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `city` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contact_person` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_warehouses`
--

LOCK TABLES `inventory_warehouses` WRITE;
/*!40000 ALTER TABLE `inventory_warehouses` DISABLE KEYS */;
INSERT INTO `inventory_warehouses` VALUES (1,'Main Warehouse','WH-001','Main Street 123','Islamabad','Federal','Pakistan','Ahmed Khan','03001234567','warehouse@example.com',NULL,'2026-07-23 11:45:47','2026-07-23 11:45:47',1,NULL,1,0);
/*!40000 ALTER TABLE `inventory_warehouses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `languages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `school_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `fk_languages_school` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=186 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (38,'en','English',1);
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_auth_settings`
--

DROP TABLE IF EXISTS `login_auth_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_auth_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `two_factor_enabled` tinyint DEFAULT '0',
  `auth_method` enum('email','sms','both') COLLATE utf8mb4_unicode_ci DEFAULT 'email',
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `login_auth_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `system_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_auth_settings`
--

LOCK TABLES `login_auth_settings` WRITE;
/*!40000 ALTER TABLE `login_auth_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_auth_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `link` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `active` tinyint NOT NULL DEFAULT '0',
  `order_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `school_id` int DEFAULT NULL,
  `type` int NOT NULL DEFAULT '1' COMMENT '1. Sidebar\r\n2. Navbar',
  PRIMARY KEY (`id`),
  KEY `fk_modules_school` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modules`
--

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` VALUES (97,'Dashboard','fa fa-dashboard','Inventory Dashboard','inventory/dashboard',1,1,'2025-03-14 23:22:01',1,1),(98,'User Management','fa fa-users','Manage users, roles and permissions','inventory/users',0,2,'2025-03-14 23:22:01',1,1),(99,'Products','fa fa-cubes','Manage products, categories and brands','inventory/products',1,3,'2025-03-14 23:22:01',1,1),(100,'Suppliers','fa fa-truck','Manage suppliers and vendors','inventory/suppliers',1,5,'2025-03-14 23:22:01',1,1),(101,'Customers','fa fa-users','Manage customers','inventory/customers',1,5,'2025-03-14 23:22:01',1,1),(102,'Purchase','fa fa-shopping-cart','Manage purchase orders and receiving','inventory/purchases',1,6,'2025-03-14 23:22:01',1,1),(103,'Sales','fa fa-line-chart','Manage sales orders and invoices','inventory/sales',1,7,'2025-03-14 23:22:01',1,1),(104,'Inventory','fa fa-archive','Manage inventory and stock movements','inventory/inventory',1,4,'2025-03-14 23:22:01',1,1),(106,'Warehouse','fa fa-building','Manage warehouses and locations','inventory/warehouses',1,1,'2025-03-14 23:22:01',1,1),(107,'Stock Audit','fa fa-check-square-o','Physical stock count and audit','inventory/stock-audit',0,10,'2025-03-14 23:22:01',1,1),(108,'Finance & Payments','fa fa-money','Manage payments and financial records','inventory/finance',1,11,'2025-03-14 23:22:01',1,1),(109,'Reports','fa fa-bar-chart','Inventory and business reports','inventory/reports',1,12,'2025-03-14 23:22:01',1,1),(110,'Approval Workflow','fa fa-check-circle','Approval management','inventory/approvals',0,13,'2025-03-14 23:22:01',1,1),(111,'Notifications','fa fa-bell','Alerts and notifications','inventory/notifications',0,14,'2025-03-14 23:22:01',1,1),(112,'Settings','fa fa-cogs','System configuration and settings','settings/settings',1,15,'2025-03-14 23:22:01',1,1),(113,'Activity Logs','fa fa-history','System activity and audit logs','inventory/activitylogs',0,16,'2025-03-14 23:22:01',1,1),(114,'Backup & Restore','fa fa-database','Database backup and restore','system/backup',1,17,'2025-03-14 23:22:01',1,1);
/*!40000 ALTER TABLE `modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otp_verification`
--

DROP TABLE IF EXISTS `otp_verification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `otp_verification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `otp_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_method` enum('email','sms') COLLATE utf8mb4_unicode_ci DEFAULT 'email',
  `delivered_to` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_verified` tinyint DEFAULT '0',
  `attempts` int DEFAULT '0',
  `max_attempts` int DEFAULT '3',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL,
  `verified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_created` (`user_id`,`created_at`),
  CONSTRAINT `otp_verification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `system_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otp_verification`
--

LOCK TABLES `otp_verification` WRITE;
/*!40000 ALTER TABLE `otp_verification` DISABLE KEYS */;
INSERT INTO `otp_verification` VALUES (9,2,'309872','email','qamaralizaine786@gmail.com',0,0,3,'2026-07-23 23:47:43','2026-07-23 23:57:43',NULL);
/*!40000 ALTER TABLE `otp_verification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `module_id` int DEFAULT NULL,
  `feature_id` int DEFAULT NULL,
  `role_id` int NOT NULL,
  `is_active` tinyint NOT NULL DEFAULT '1',
  `can_view` tinyint(1) DEFAULT '0',
  `can_add` tinyint(1) DEFAULT '0',
  `can_edit` tinyint(1) DEFAULT '0',
  `can_delete` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `school_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`),
  KEY `feature_id` (`feature_id`),
  KEY `role_id` (`role_id`),
  KEY `fk_permissions_school` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25814 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (25779,97,1,2,1,1,1,1,1,'2026-07-25 04:09:07',1),(25780,99,1,2,1,1,1,1,1,'2026-07-25 04:09:07',1),(25781,100,1,2,1,1,1,1,1,'2026-07-25 04:09:07',1),(25782,101,1,2,1,1,1,1,1,'2026-07-25 04:09:07',1),(25783,102,1,2,1,1,1,1,1,'2026-07-25 04:09:07',1),(25784,103,1,2,1,1,1,1,1,'2026-07-25 04:09:07',1),(25785,104,1,2,1,1,1,1,1,'2026-07-25 04:09:07',1),(25786,106,1,2,1,1,1,1,1,'2026-07-25 04:09:07',1),(25787,108,1,2,1,1,1,1,1,'2026-07-25 04:09:07',1),(25788,109,1,2,1,1,1,1,1,'2026-07-25 04:09:07',1),(25800,97,1,3,1,1,1,1,1,'2026-07-25 04:09:29',1),(25801,103,1,3,1,1,1,1,1,'2026-07-25 04:09:29',1),(25802,97,1,1,1,1,1,1,1,'2026-07-25 05:51:26',1),(25803,99,1,1,1,1,1,1,1,'2026-07-25 05:51:26',1),(25804,100,1,1,1,1,1,1,1,'2026-07-25 05:51:26',1),(25805,101,1,1,1,1,1,1,1,'2026-07-25 05:51:26',1),(25806,102,1,1,1,1,1,1,1,'2026-07-25 05:51:26',1),(25807,103,1,1,1,1,1,1,1,'2026-07-25 05:51:26',1),(25808,104,1,1,1,1,1,1,1,'2026-07-25 05:51:26',1),(25809,106,1,1,1,1,1,1,1,'2026-07-25 05:51:26',1),(25810,108,1,1,1,1,1,1,1,'2026-07-25 05:51:26',1),(25811,109,1,1,1,1,1,1,1,'2026-07-25 05:51:26',1),(25812,112,1,1,1,1,1,1,1,'2026-07-25 05:51:26',1),(25813,114,1,1,1,1,1,1,1,'2026-07-25 05:51:26',1);
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `active` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `school_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `fk_roles_school` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Admin','Manage All Modules and actions',1,'2026-07-22 13:01:43',NULL),(2,'Inventory Super Admin','Manages roles and all other administrative tasks!',1,'2024-10-19 07:54:30',1),(3,'Cashier Role',NULL,1,'2026-07-22 05:50:57',NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school`
--

DROP TABLE IF EXISTS `school`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school` (
  `school_id` int NOT NULL AUTO_INCREMENT,
  `school_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_of_establishment` date NOT NULL,
  `principal_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `school_type` enum('Public','Private','Charter') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `number_of_students` int DEFAULT '0',
  `accreditation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `motto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `navbar_color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '#0f4c29',
  `active` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`school_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school`
--

LOCK TABLES `school` WRITE;
/*!40000 ALTER TABLE `school` DISABLE KEYS */;
INSERT INTO `school` VALUES (1,'Inventory Management System','inventorysystem@gmail.com','00000000000000','Islamabad Pakistan','https://www.lmssystem.com','2023-01-01','Qamar Ali','Private',1000,'Not Verified','All Systems Operational','images/school/1763493530_Management_System.png','#438eb9',1,'2025-03-22 11:08:18','2026-07-13 23:35:08');
/*!40000 ALTER TABLE `school` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_contracts`
--

DROP TABLE IF EXISTS `system_contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_contracts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contract_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contract_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contract_type` enum('monthly','yearly') COLLATE utf8mb4_unicode_ci DEFAULT 'monthly',
  `contractor_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contractor_cnic` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contractor_phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contractor_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contractor_address` text COLLATE utf8mb4_unicode_ci,
  `installation_date` date DEFAULT NULL,
  `contract_start_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `monthly_charges` decimal(15,2) DEFAULT '0.00',
  `yearly_charges` decimal(15,2) DEFAULT '0.00',
  `monthly_due_date` int DEFAULT '1',
  `maximum_extension_days` int DEFAULT '15',
  `system_status` enum('active','inactive','suspended','expired') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `contract_description` longtext COLLATE utf8mb4_unicode_ci,
  `policy_description` longtext COLLATE utf8mb4_unicode_ci,
  `contractor_info` longtext COLLATE utf8mb4_unicode_ci,
  `full_description` longtext COLLATE utf8mb4_unicode_ci,
  `attachment_file` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_contracts`
--

LOCK TABLES `system_contracts` WRITE;
/*!40000 ALTER TABLE `system_contracts` DISABLE KEYS */;
INSERT INTO `system_contracts` VALUES (1,'APC-2026-0001','Auto Parts Supply Agreement','monthly','Pak Auto Parts Traders','37405-1234567-1','+923185657457','sales@pakautoparts.com','Shop #12, Auto Market, Main Murree Road Bharakaho, Islamabad, Pakistan','2026-08-01','2026-08-01','2027-07-31',5000.00,60000.00,5,10,'active','Annual agreement for the supply of genuine and aftermarket automobile spare parts including engine, suspension, brake, electrical, and body components.','The supplier shall provide quality auto parts with manufacturer warranty where applicable. Defective items may be returned within 7 days. Payment is due within 10 days of invoice. Delivery shall be completed within 48 hours for in-stock items.','Primary Contact: Muhammad Usman (Sales Manager), Phone: +92-321-5551234, Email: sales@pakautoparts.com','This contract covers the regular supply of automobile spare parts for Japanese, Korean, and local vehicles. Items include oil filters, air filters, brake pads, clutch plates, spark plugs, engine oil, suspension parts, batteries, belts, radiators, headlights, and other genuine or approved aftermarket components. Pricing will remain fixed during the contract period unless mutually revised in writing. The supplier agrees to maintain adequate stock levels and provide prompt replacement of defective products.','contracts/APC-2026-0001.pdf','2026-07-23 19:28:03','2026-07-23 14:29:43',1,1,1,0);
/*!40000 ALTER TABLE `system_contracts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_invoices`
--

DROP TABLE IF EXISTS `system_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contract_id` int NOT NULL,
  `invoice_month` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_year` int DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `extended_due_date` date DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `invoice_status` enum('draft','sent','pending','partial','paid','overdue','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `payment_status` enum('unpaid','partial','pending_approval','paid') COLLATE utf8mb4_unicode_ci DEFAULT 'unpaid',
  `paid_amount` decimal(15,2) DEFAULT '0.00',
  `remaining_amount` decimal(15,2) DEFAULT '0.00',
  `invoice_file` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `contract_id` (`contract_id`),
  CONSTRAINT `system_invoices_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `system_contracts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_invoices`
--

LOCK TABLES `system_invoices` WRITE;
/*!40000 ALTER TABLE `system_invoices` DISABLE KEYS */;
INSERT INTO `system_invoices` VALUES (1,'INV-20260723143545-8554',1,'2026-07',2026,'2026-07-23','2026-08-05','2026-08-15',5000.00,'Monthly subscription','sent','paid',0.00,5000.00,NULL,'2026-07-23 14:35:45','2026-07-23 15:05:31',1,1,1,1),(2,'INV-20260723150537-4734',1,'2026-07',2026,'2026-07-23','2026-08-05','2026-08-15',5000.00,'Monthly subscription','sent','paid',0.00,5000.00,NULL,'2026-07-23 15:05:37','2026-07-24 17:42:37',1,1,1,1),(3,'INV-20260724174252-5574',1,'2026-07',2026,'2026-07-24','2026-08-01','2026-08-11',5000.00,NULL,'sent','unpaid',0.00,0.00,NULL,'2026-07-24 22:42:52','2026-07-24 22:42:52',1,NULL,1,0);
/*!40000 ALTER TABLE `system_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_payment_proofs`
--

DROP TABLE IF EXISTS `system_payment_proofs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_payment_proofs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL,
  `proof_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `proof_date` date DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_file` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `verification_status` enum('pending','verified','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `verified_by` int DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `comments` longtext COLLATE utf8mb4_unicode_ci,
  `admin_comments` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `proof_number` (`proof_number`),
  KEY `invoice_id` (`invoice_id`),
  KEY `proof_number_2` (`proof_number`),
  KEY `verification_status` (`verification_status`),
  CONSTRAINT `system_payment_proofs_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `system_invoices` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_payment_proofs`
--

LOCK TABLES `system_payment_proofs` WRITE;
/*!40000 ALTER TABLE `system_payment_proofs` DISABLE KEYS */;
INSERT INTO `system_payment_proofs` VALUES (1,1,'PROOF-20260723145117-8542','2026-07-23',NULL,NULL,NULL,NULL,'uploads/payment_proofs/1784818277_6a622a6519b21_profile_1_1761204296_68f9d84811f10.png','profile_1_1761204296_68f9d84811f10.png',NULL,NULL,'verified',1,'2026-07-23 14:51:53',NULL,'Easypaisa Payment',NULL,'2026-07-23 19:51:17','2026-07-23 15:05:31',1,1,1,1),(2,2,'PROOF-20260723165605-1657','2026-07-23',NULL,NULL,NULL,'TR-12683249821583','uploads/payment_proofs/1784825765_6a6247a5333d1_29650_images.png','29650_images.png',NULL,NULL,'verified',1,'2026-07-23 17:03:13',NULL,'Paid Via Easypaisa!',NULL,'2026-07-23 21:56:05','2026-07-24 17:42:37',2,1,1,1);
/*!40000 ALTER TABLE `system_payment_proofs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_payments`
--

DROP TABLE IF EXISTS `system_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `payment_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_id` int NOT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `reference_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `payment_status` enum('pending','completed','failed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `is_deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_number` (`payment_number`),
  KEY `invoice_id` (`invoice_id`),
  KEY `payment_number_2` (`payment_number`),
  KEY `payment_status` (`payment_status`),
  CONSTRAINT `system_payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `system_invoices` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_payments`
--

LOCK TABLES `system_payments` WRITE;
/*!40000 ALTER TABLE `system_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_users`
--

DROP TABLE IF EXISTS `system_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_general_ci DEFAULT 'active',
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1',
  `is_deleted` tinyint(1) DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `failed_login_attempts` int DEFAULT '0',
  `profile_picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'assets/images/avatars/default-avatar.jpg',
  `address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `whatsapp` varchar(256) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `facebook` varchar(256) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pinterest` varchar(256) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `instagram` varchar(256) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `about` text COLLATE utf8mb4_general_ci,
  `school_id` int DEFAULT NULL,
  `referance` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_role` (`role_id`),
  KEY `fk_system_users_school` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_users`
--

LOCK TABLES `system_users` WRITE;
/*!40000 ALTER TABLE `system_users` DISABLE KEYS */;
INSERT INTO `system_users` VALUES (1,'superadmin','$2y$10$QhqttUePrywOckLgsP.rTON3VtMLj9F7laOSnxO2WafaqST6ud5hy','qamarali@gmail.com','Qamar','Ali','active','0346-7607204',1,'2024-10-25 15:44:52','2026-07-25 05:50:42',1,0,'2026-07-25 05:50:42',0,'uploads/profile_pictures/1784818242_7d0ebb3f18.png','Street 15, Gulberg, Lahore','Pakistan','Islamabad','2007-07-27','Male','0346-7607204','https://facebook.com/qamarAli','https://linkedin.com/in/alikhan','https://instagram.com/ali.khan','Super Admin. Manages System Operations.',1,NULL,NULL,NULL),(2,'inventory_admin','$2y$10$YpoAPRT.GBfLWcyW1P7MF.hEMpk3x81qbfGi43U9hbAVQnK.LBi9O','inventoryadmin@gmail.com','Qamar Ali','Inventory Admin','active','03185657457',2,'2026-07-23 09:25:21','2026-07-25 04:09:42',1,0,'2026-07-25 04:09:42',0,'uploads/profile_pictures/1784827731_3c1637f0a1.png','Islamabad Capital Territory, Pakistan',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `system_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-25 11:01:43
