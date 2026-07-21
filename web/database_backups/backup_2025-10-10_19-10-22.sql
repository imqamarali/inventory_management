/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: webixpkc_online_quran_academy
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-cll-lve

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
-- Table structure for table `admission_inquiry`
--

DROP TABLE IF EXISTS `admission_inquiry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admission_inquiry` (
  `id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `date` date NOT NULL,
  `follow_up_date` date NOT NULL,
  `assigned` int(11) DEFAULT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `source` varchar(50) NOT NULL,
  `class` int(11) DEFAULT NULL,
  `status` varchar(200) DEFAULT NULL,
  `no_of_child` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admission_inquiry`
--

LOCK TABLES `admission_inquiry` WRITE;
/*!40000 ALTER TABLE `admission_inquiry` DISABLE KEYS */;
INSERT INTO `admission_inquiry` (`id`, `session_id`, `name`, `phone`, `email`, `address`, `description`, `note`, `date`, `follow_up_date`, `assigned`, `reference`, `source`, `class`, `status`, `no_of_child`, `created_at`, `updated_at`, `school_id`) VALUES (391,1,'Olie Martin','0898976556',NULL,NULL,NULL,NULL,'2024-09-30','2024-10-02',NULL,NULL,'Online Front Site',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(392,1,'Jack','89065733345',NULL,NULL,NULL,NULL,'2024-09-16','2024-09-22',NULL,NULL,'Front Office',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(395,1,'Ottneil Baartman','890768643',NULL,NULL,NULL,NULL,'2024-10-10','2024-10-12',NULL,NULL,'Admission Campaign',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(397,1,'Cooper Connolly','89080678655',NULL,NULL,NULL,NULL,'2024-10-20','2024-10-22',NULL,NULL,'Advertisement',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(400,1,'Shashwat Rawat','879090572',NULL,NULL,NULL,NULL,'2024-10-30','2024-10-31',NULL,NULL,'Front Office',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(401,1,'Mukesh Kumar','7897895434',NULL,NULL,NULL,NULL,'2024-10-26','2024-10-28',NULL,NULL,'Advertisement',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(402,1,'Royston Dias','898957455',NULL,NULL,NULL,NULL,'2024-10-22','2024-10-26',NULL,NULL,'Front Office',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(404,1,'Akash Deep','8890678677',NULL,NULL,NULL,NULL,'2024-11-05','2024-11-07',NULL,NULL,'Google Ads',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(405,1,'Ottneil Baartman','8896567566',NULL,NULL,NULL,NULL,'2024-11-10','2024-11-12',NULL,NULL,'Admission Campaign',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(407,1,'Morgan Simmons','890007866',NULL,NULL,NULL,NULL,'2024-11-15','2024-11-18',NULL,NULL,'Front Office',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(408,1,'Olie Martin','8006767889',NULL,NULL,NULL,NULL,'2024-11-20','2024-11-25',NULL,NULL,'Advertisement',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(411,1,'Rachin sinha','8009077889',NULL,NULL,NULL,NULL,'2024-11-18','2024-11-22',NULL,NULL,'Online Front Site',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(415,1,'Stanley Wood','808906867',NULL,NULL,NULL,NULL,'2024-12-16','2024-12-20',NULL,NULL,'Admission Campaign',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(416,1,'James','809806786',NULL,NULL,NULL,NULL,'2024-12-20','2024-12-26',NULL,NULL,'Front Office',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(417,1,'Jeffrey T. Rafter','8790567530',NULL,NULL,NULL,NULL,'2024-12-25','2024-12-31',NULL,NULL,'Online Front Site',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(418,1,'Charlie Barrett','099756754',NULL,NULL,NULL,NULL,'2024-12-26','2024-12-28',NULL,NULL,'Admission Campaign',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(421,1,'David Wilson','987867856',NULL,NULL,NULL,NULL,'2024-12-22','2024-12-28',NULL,NULL,'Advertisement',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(424,1,'Darren K. Hubbard','9067876845',NULL,NULL,NULL,NULL,'2025-01-10','2025-01-12',NULL,NULL,'Google Ads',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(426,1,'Sweta Sharma','9078768564',NULL,NULL,NULL,NULL,'2025-01-20','2025-01-22',NULL,NULL,'Front Office',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(428,1,'Alister','0988678465',NULL,NULL,NULL,NULL,'2025-01-30','2025-01-31',NULL,NULL,'Admission Campaign',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(429,1,'Preeti Mehra','9065756554',NULL,NULL,NULL,NULL,'2025-01-16','2025-01-18',NULL,NULL,'Advertisement',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(431,1,'David','889056744',NULL,NULL,NULL,NULL,'2025-01-28','2025-01-31',NULL,NULL,'Front Office',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(434,1,'Bella McCallum','788067565',NULL,NULL,NULL,NULL,'2025-02-05','2025-02-08',NULL,NULL,'Google Ads',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(435,1,'Darren K. Hubbard','808875674',NULL,NULL,NULL,NULL,'2025-02-10','2025-02-12',NULL,NULL,'Front Office',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(437,1,'Darren Karren','890807867',NULL,NULL,NULL,NULL,'2025-02-15','2025-02-18',NULL,NULL,'Google Ads',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1),
(438,1,'Morgan Simmons','789806786',NULL,NULL,NULL,NULL,'2025-02-20','2025-02-22',NULL,NULL,'Online Front Site',NULL,'Active',NULL,'2025-02-09 17:10:27','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `admission_inquiry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bank_details`
--

DROP TABLE IF EXISTS `bank_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_details` (
  `bank_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `account_title` varchar(255) NOT NULL,
  `account_no` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `fsc_code` varchar(50) DEFAULT NULL,
  `bank_branch` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_details`
--

LOCK TABLES `bank_details` WRITE;
/*!40000 ALTER TABLE `bank_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `bank_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blood_groups`
--

DROP TABLE IF EXISTS `blood_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blood_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(3) NOT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blood_groups`
--

LOCK TABLES `blood_groups` WRITE;
/*!40000 ALTER TABLE `blood_groups` DISABLE KEYS */;
INSERT INTO `blood_groups` (`id`, `name`, `school_id`) VALUES (1,'A+',1),
(2,'A-',1),
(3,'B+',1),
(4,'B-',1),
(5,'AB+',1),
(6,'AB-',1),
(7,'O+',1),
(8,'O-',1);
/*!40000 ALTER TABLE `blood_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `book_number` varchar(50) NOT NULL,
  `isbn_number` varchar(50) NOT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `rack_number` varchar(50) DEFAULT NULL,
  `qty` int(11) DEFAULT 0,
  `available` int(11) DEFAULT 0,
  `book_price` decimal(10,2) DEFAULT NULL,
  `post_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
INSERT INTO `books` (`id`, `title`, `description`, `book_number`, `isbn_number`, `publisher`, `author`, `subject`, `rack_number`, `qty`, `available`, `book_price`, `post_date`, `created_at`, `updated_at`, `school_id`) VALUES (59,'Diversity in the Living World','No Description','7878456','WEE0-78976','D.L.Publisher','Harmreet Singh','Science','534512',100,94,100.00,'2025-02-15','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(60,'Data Hanling an Presentation','No Description','098079','DFDF900789','S. K. Publisher','Gourav Singh','Mathematics','456234',80,75,120.00,'2025-02-10','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(61,'NURTURING NATURE','No Description','455675','WEWE-0-7979','D.S Publisher','Garry','English','5345',100,95,90.00,'2025-02-05','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(62,'हार की जीत','No Description','907894','SDD095674','S.K Publisher','Mahesh Sinha','NCRT Hindi','4562354',100,94,80.00,'2025-02-01','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(63,'Kingdoms, Kings and an Early Republic','No Description','24344','OIKK-00999','S.K.Publisher','G.R.Singh','Social Studies','653',90,86,90.00,'2025-01-25','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(64,'Motion and Measurement of Distances','No Description','4634377','KO009098','D.S.Publisher','T.Mehta','Science','2311',80,74,95.00,'2025-01-20','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(65,'The Valley of Flowers','No Description','34533','EE89089778','S.D.Publisher','Q.L.Singh','EVS -II','3244',90,83,100.00,'2025-01-15','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(66,'सबसे अच्छा पेड़','No Description','546333','LPOO-00999','S.K.Publisher','K.S.Mehra','HINDI','53422',90,84,95.00,'2025-01-10','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(67,'The Story of Food','No Description','546433','DFDF89078','D.S.Publisher','G.S.Lokesh','English','67564',80,73,120.00,'2025-01-06','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(68,'Shapes and Designs','No Description','7434','WESD88809','S.K.Publisher','S.K,.mehta','Mathematics','4322',100,89,80.00,'2025-01-01','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(69,'The Valley of Flowers- III','No Description','55476','HDD45456','S.K.Publisher','Kalvin','EVS','695',100,92,120.00,'2024-12-25','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(70,'A Seed Tells farmer Story part- 5','No Description','3643','EER42343','D.s.Publisher','Harry Wood','Science','43543',100,95,80.00,'2024-12-20','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(71,'Social and Political Life -5','No Description','53422','GEERD3422','S.K.Publisher','Yash Thakur','Social Studies','3234',80,76,120.00,'2024-12-15','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(72,'नाव बनाओ नाव बनाओ','No Description','64564','WSS5464','S.K. Publisher','Suresh Mehra','Hindi','34234',100,96,80.00,'2024-12-10','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(73,'Looking Around II','No Description','453452','GDF3432','D.S.Publisher','Kalvin Martin','English','4353',100,95,100.00,'2024-12-05','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(74,'MATH-MAGIC 5','No Description','SDW3452','SDD23322','S.K.Publisher','Garry Wood','Mathematics','3445',50,45,80.00,'2024-12-01','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(75,'A Little Fish Story','No Description','SGD6785','EW7897','D.S. Publisher','Lokesh','English','QQ3422',100,92,100.00,'2024-11-25','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(76,'Reaching Grandmother’s House 3','No Description','SDA768','WEW67878','S.K.Publisher','John','EVS 2','SAA56674',100,98,80.00,'2024-11-20','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(77,'Surface areas and volumes -II','No Description','SAA2611','SEW344','D.K.Publisher','Vinay Mehta','Mathematics','67865',100,94,80.00,'2024-11-15','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(78,'Social and Political Life','No Description','GG78965','VSS345','S.K.Publisher','Harrish','Social Studies','5433',100,96,100.00,'2024-11-10','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(79,'A Seed Tells farmer Story','A Seed Tells farmer Story','DS674','AQW422','D.K.Publisher','Lukesh','English','4345',100,93,120.00,'2024-11-05','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(80,'Human Body Systems Chapter -X1','No Description','QQ43234','SSF6575','D.K.Publisher','David','Science','5674',80,74,120.00,'2024-11-01','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(81,'Carbon and its Compounds Chapter 5','No Description','5466753','lKJK069789','S.K.Publisher','J.S.Verma','EVS 2','54623',80,71,100.00,'2024-10-25','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(82,'The Sound of Music','No Description','7563','QDFF906784','D.S.Publisher','Hari Singh','Social Studies','3453',80,76,56.00,'2024-10-20','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(83,'दान का हिसाब','No Description','65735','FGD890784','D.K.Publisher','K.L.Mishra','Hindi','452234',100,99,80.00,'2024-10-15','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(84,'Long And Short Chapter X','No Description','DF890567','GDD90895','D.S.Publisher','G.K. Mehta','Mathematics','546345',85,79,95.00,'2024-10-10','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(85,'Human Body Systems Chapter -X','No Description','45632','WE899068','D.K.Publisher','U.S.Singh','Science','2144',100,93,85.00,'2024-10-05','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(86,'English Chapter 10 Animals','No Description','WTR90980','WEQ890890','S.k.Publisher','S.K. verma','NCRT English','2413',80,80,55.00,'2024-10-01','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(87,'Social and Political Life –V','No Description','78906','WER64633','D.S.Publisher','Yash Singh','Social Studies','522',80,74,60.00,'2024-09-25','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(88,'Reaching Grandmother’s House 2','No Description','4534','DRT78956','S.K.Publisher','Yash Sinha','EVS-2','5463',90,84,75.00,'2024-09-20','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(89,'The Valley of Flowers- II','No Description','806785','WED896576','Deepak Publisher','Robin','English','3124',100,97,80.00,'2024-09-16','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(90,'A Seed Tells farmer Story part-4','No Description','86784','RD7899057','S.K.Publisher','Rohit Sinha','Hindi','890675',80,75,65.00,'2024-09-10','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(91,'Human Body Systems','No Description','66533','GHT9-6784','Deepak Publisher','Tarun','Science','7844',100,97,120.00,'2024-09-05','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(92,'Periodic Classification of Elements II','No Description','6532','SD89068','S.K.Publisher','J.s.Sharma','Mathematics','5211',100,98,86.00,'2024-09-01','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(93,'A Little Fish Story','No Description','4563','GFG34532','D.K.Publisher','R.T Martin','English','3422',100,94,120.00,'2024-08-20','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(94,'Food And Fun','Food And Fun','3456','DA3422','D.K.publisher','J.K.Mehta','Social Studies','3453',95,92,100.00,'2024-08-25','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(95,'A Seed Tells farmer Story','A Seed Tells farmer Story','54356','345SDA','D.S.Publisher','G.Singh','EVS-2','2322',100,96,90.00,'2024-08-15','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(96,'Human Body Systems','Human Body Systems','56789`','ASl68795','L.S.Publisher','J.S. mehta','Science','5643',100,96,85.00,'2024-08-10','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(97,'Hindi the native language','No Description','5677456','DF34234','K.S.Verma','Hari Singh','Hindi','4221',80,77,100.00,'2024-08-05','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(98,'Surface areas and volumes','Surface areas and volumes','467878','AS54633','DS Publisher','D.S.Sharma','Mathematics','2322',100,98,100.00,'2024-08-01','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(99,'Experiments with Soil','No Description','5423','ERW47678','R.S. Publisher','K.L. Mehta','Science','FS734',90,83,95.00,'2024-07-25','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(100,'India and Contemporary World-2','No Description','SDS3652','SD0236','V.K. Publisher','R.Sharma','History','SDSD23',80,75,80.00,'2024-07-20','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(101,'Lab Manual for Mathematics-8','No Description','GHH4533','SD3422','D.S. Publisher','Vinay Sharma','Mathematics','46652',80,78,150.00,'2024-07-15','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(102,'Map and activity workbook-6','No Description','BF0231','FDS36521','D.S.Publisher','Naresh Mehta','Social Science','SAA3422',100,93,120.00,'2024-07-10','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(103,'रचनात्मक लेखन','No Description','WFF4522','TRW2311','Mahesh Publisher','Arjun Sinha','Hindi','D6744',80,76,110.00,'2024-07-05','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(104,'Fun With Writing Skills-1','Fun With Writing Skills-1','SD23111','VFF02331','Kirti Publisher','David Martin','English','SS12113',100,98,120.00,'2024-07-01','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(105,'Carbon and its Compounds','Carbon and its Compounds','FHG8906','567657','K.S. Publisher','U.L.Mishra','NCRT Science','67657',100,97,150.00,'2024-06-25','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(106,'The Boy Who Built a Secret Garden','No Description','00459','DF7898','S.K.Publisher','G.K.Singh','NCRT Novel','45532',50,47,120.00,'2024-06-20','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(107,'Reaching Grandmother’s House','No Description','66753','SD7875','J.S.publisher','Kiran Rao','EVS 2','4234',50,48,150.00,'2024-06-16','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(108,'Carts and Wheels','No Description','65733','SDS342','D.S.Publisher','H.D.Mehra','Mathematics','3424',100,99,120.00,'2024-06-10','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(109,'A Watering Rhyme','No Description','SFD67567','ASA23422','S.K.Publisher','G.K.Paul','NCRT English','0899',50,49,100.00,'2024-06-05','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(110,'The Lord of the Rings','No Description','5151','TCITR152','Little, Brown and Company','J. D. Salinger','Novel','6263',120,113,100.00,'2024-05-31','2025-02-08 18:36:42','2025-03-13 17:51:24',1),
(111,'Wuthering Heights','No Description','2485','WH651','Thomas Cautley Newby','Emily Bronte','Novel','4844',110,104,120.00,'2024-05-15','2025-02-08 18:36:42','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_teachers`
--

DROP TABLE IF EXISTS `class_teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_teachers` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `teacher_ids` varchar(256) DEFAULT NULL COMMENT 'having all comma saperated ids of teachers against class, sections',
  `session_id` int(11) NOT NULL,
  `description` varchar(256) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_teachers`
--

LOCK TABLES `class_teachers` WRITE;
/*!40000 ALTER TABLE `class_teachers` DISABLE KEYS */;
INSERT INTO `class_teachers` (`id`, `class_id`, `section_id`, `teacher_ids`, `session_id`, `description`, `created_at`, `updated_at`, `school_id`) VALUES (1,1,1,'1,5',1,'Assigning Info','2025-02-06 09:16:29','2025-03-13 17:51:24',1),
(2,2,1,'5',1,'Testing...','2025-02-16 09:49:40','2025-03-13 17:51:24',1),
(3,1,2,'1,5',1,'Assigning Info...','2025-02-16 09:50:04','2025-03-13 17:51:24',1),
(4,1,4,'5,6',1,'abc','2025-03-20 05:39:43','2025-03-20 05:39:43',NULL);
/*!40000 ALTER TABLE `class_teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `total_seats` int(11) DEFAULT NULL,
  `assets` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
INSERT INTO `classes` (`id`, `class_name`, `total_seats`, `assets`, `description`, `school_id`) VALUES (1,'Batch 10 PM - 11 PM',10,'1 Room 10 Pcs','Daily 10 to 11 PM Class',2);
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complaint_type`
--

DROP TABLE IF EXISTS `complaint_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `complaint_type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaint_type`
--

LOCK TABLES `complaint_type` WRITE;
/*!40000 ALTER TABLE `complaint_type` DISABLE KEYS */;
INSERT INTO `complaint_type` (`id`, `name`, `description`, `created_at`, `updated_at`, `school_id`) VALUES (1,'Academic Complaint','Complaints related to academics','2025-02-15 15:22:50','2025-03-13 17:51:24',1),
(2,'Fee Payment','Purpose related to fee paymentss','2025-02-15 15:22:50','2025-03-13 17:51:24',1),
(3,'General Inquiry','General inquiries about the institution!','2025-02-15 15:22:50','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `complaint_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `complaint_type` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `description` text DEFAULT NULL,
  `action_taken` text DEFAULT NULL,
  `assigned` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `session_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaints`
--

LOCK TABLES `complaints` WRITE;
/*!40000 ALTER TABLE `complaints` DISABLE KEYS */;
INSERT INTO `complaints` (`id`, `complaint_type`, `source_id`, `name`, `phone`, `date`, `description`, `action_taken`, `assigned`, `note`, `file_path`, `session_id`, `created_at`, `updated_at`, `school_id`) VALUES (1,1,1,'New test','03185657457','2025-02-16','Test Description','Not Taken Yet',' 1-3','Under Process',NULL,1,'2025-02-15 19:52:08','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `complaints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contract_types`
--

DROP TABLE IF EXISTS `contract_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `contract_types` (
  `contract_id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contract_types`
--

LOCK TABLES `contract_types` WRITE;
/*!40000 ALTER TABLE `contract_types` DISABLE KEYS */;
INSERT INTO `contract_types` (`contract_id`, `type_name`, `created_at`, `updated_at`, `school_id`) VALUES (1,'Permanent','2025-01-05 12:05:42','2025-03-13 17:51:24',1),
(2,'Probation','2025-01-05 12:05:42','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `contract_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` (`department_id`, `department_name`, `created_at`, `updated_at`, `school_id`) VALUES (1,'Academic','2025-01-05 12:03:46','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `designations`
--

DROP TABLE IF EXISTS `designations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `designations` (
  `designation_id` int(11) NOT NULL,
  `designation_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `designations`
--

LOCK TABLES `designations` WRITE;
/*!40000 ALTER TABLE `designations` DISABLE KEYS */;
INSERT INTO `designations` (`designation_id`, `designation_name`, `created_at`, `updated_at`, `school_id`) VALUES (1,'Faculty','2025-01-05 12:02:35','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `designations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disabled_staff`
--

DROP TABLE IF EXISTS `disabled_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `disabled_staff` (
  `disable_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `dated` date NOT NULL,
  `disable_reason` varchar(255) NOT NULL,
  `comments` text DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `active_comments` text DEFAULT NULL,
  `active_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disabled_staff`
--

LOCK TABLES `disabled_staff` WRITE;
/*!40000 ALTER TABLE `disabled_staff` DISABLE KEYS */;
/*!40000 ALTER TABLE `disabled_staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disabled_students`
--

DROP TABLE IF EXISTS `disabled_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `disabled_students` (
  `disable_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `dated` datetime DEFAULT current_timestamp(),
  `disable_reason` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `active_comments` text DEFAULT NULL,
  `active_time` datetime DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disabled_students`
--

LOCK TABLES `disabled_students` WRITE;
/*!40000 ALTER TABLE `disabled_students` DISABLE KEYS */;
/*!40000 ALTER TABLE `disabled_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `divisions`
--

DROP TABLE IF EXISTS `divisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `divisions` (
  `division_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `percentage_from` decimal(5,2) NOT NULL,
  `percentage_upto` decimal(5,2) NOT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `divisions`
--

LOCK TABLES `divisions` WRITE;
/*!40000 ALTER TABLE `divisions` DISABLE KEYS */;
INSERT INTO `divisions` (`division_id`, `name`, `percentage_from`, `percentage_upto`, `session_id`, `created_at`, `updated_at`, `school_id`) VALUES (5,'First',80.00,100.00,1,'2025-02-16 21:28:37','2025-03-13 22:51:24',1),
(6,'Second',60.00,80.00,1,'2025-02-16 21:29:55','2025-03-13 22:51:24',1),
(7,'Third',40.00,60.00,1,'2025-02-16 21:30:09','2025-03-13 22:51:24',1),
(8,'Fail',0.00,40.00,1,'2025-02-16 21:30:28','2025-03-13 22:51:24',1);
/*!40000 ALTER TABLE `divisions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documentations`
--

DROP TABLE IF EXISTS `documentations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentations` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file` varchar(255) NOT NULL,
  `school_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `doc_type` varchar(256) DEFAULT NULL,
  `applied_from` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentations`
--

LOCK TABLES `documentations` WRITE;
/*!40000 ALTER TABLE `documentations` DISABLE KEYS */;
/*!40000 ALTER TABLE `documentations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam`
--

DROP TABLE IF EXISTS `exam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam` (
  `exam_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `session_id` int(11) NOT NULL,
  `exam_group_id` int(11) NOT NULL,
  `subjects_included` int(11) DEFAULT NULL,
  `publish_exam` tinyint(1) DEFAULT NULL,
  `publish_result` tinyint(1) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam`
--

LOCK TABLES `exam` WRITE;
/*!40000 ALTER TABLE `exam` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_grades`
--

DROP TABLE IF EXISTS `exam_grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_grades` (
  `exam_grade_id` int(11) NOT NULL,
  `exam_type_id` int(11) NOT NULL DEFAULT 1,
  `grade_name` varchar(10) NOT NULL,
  `percent_from` decimal(5,2) NOT NULL,
  `percent_upto` decimal(5,2) NOT NULL,
  `grade_point` decimal(3,1) NOT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_grades`
--

LOCK TABLES `exam_grades` WRITE;
/*!40000 ALTER TABLE `exam_grades` DISABLE KEYS */;
INSERT INTO `exam_grades` (`exam_grade_id`, `exam_type_id`, `grade_name`, `percent_from`, `percent_upto`, `grade_point`, `session_id`, `created_at`, `updated_at`, `school_id`) VALUES (12,1,'A',70.00,80.00,1.1,1,'2024-12-22 17:28:53','2025-03-13 22:51:24',1),
(13,2,'A+',80.00,90.00,0.0,1,'2024-12-22 17:29:21','2025-03-13 22:51:24',1),
(14,2,'A',70.00,80.00,0.0,1,'2024-12-22 17:29:57','2025-03-13 22:51:24',1),
(15,1,'B+',60.00,70.00,0.0,1,'2024-12-22 17:30:32','2025-03-13 22:51:24',1),
(17,1,'C',50.00,60.00,1.2,1,'2025-02-02 16:18:41','2025-03-13 22:51:24',1);
/*!40000 ALTER TABLE `exam_grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_groups`
--

DROP TABLE IF EXISTS `exam_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_groups` (
  `exam_group_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `no_of_exams` int(11) DEFAULT NULL,
  `exam_type_id` int(11) NOT NULL DEFAULT 1,
  `session_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_groups`
--

LOCK TABLES `exam_groups` WRITE;
/*!40000 ALTER TABLE `exam_groups` DISABLE KEYS */;
INSERT INTO `exam_groups` (`exam_group_id`, `name`, `description`, `no_of_exams`, `exam_type_id`, `session_id`, `created_at`, `updated_at`, `school_id`) VALUES (1,'Class 4 (Pass / Fail)','',NULL,1,1,'2024-12-22 21:10:31','2025-03-13 22:51:24',1),
(2,'Class 4 (School Based Grading System)','SBGS stands for the  SCHOOL BASED GRADING SYATEM. It is a standard method of calculating a student’s average score obtained over a stipulated period i.e a semester or a term. ',NULL,2,1,'2024-12-22 21:10:51','2025-03-13 22:51:24',1),
(3,'Class 4 (College Based Grading System)','',NULL,3,1,'2024-12-22 21:11:03','2025-03-13 22:51:24',1),
(4,'Class 4 (GPA Grading System)','Grade Point Average (GPA) is the average of all the grades obtained by a student in his/ her academic studies.',NULL,4,1,'2024-12-22 21:11:27','2025-03-13 22:51:24',1),
(5,'Average Passing Exam','',NULL,5,1,'2024-12-22 21:11:55','2025-03-13 22:51:24',1),
(7,'Class 4 (Pass / Fail)','',NULL,6,1,'2025-03-14 21:40:31','2025-03-14 21:40:31',2),
(8,'All student','test',NULL,2,1,'2025-03-22 10:31:18','2025-03-22 10:31:18',1),
(9,'Mid Terms Session 2025','Exams',NULL,13,1,'2025-03-22 10:36:59','2025-03-22 10:37:36',1);
/*!40000 ALTER TABLE `exam_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_marks`
--

DROP TABLE IF EXISTS `exam_marks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_marks` (
  `exam_marks_id` int(11) NOT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `student_exam_id` int(11) DEFAULT NULL,
  `exam_subject_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `attendance` int(11) DEFAULT 1,
  `marks` decimal(10,2) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `status` varchar(256) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_marks`
--

LOCK TABLES `exam_marks` WRITE;
/*!40000 ALTER TABLE `exam_marks` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_marks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_remarks`
--

DROP TABLE IF EXISTS `exam_remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_remarks` (
  `exam_remarks_id` int(11) NOT NULL,
  `exam_group_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_remarks`
--

LOCK TABLES `exam_remarks` WRITE;
/*!40000 ALTER TABLE `exam_remarks` DISABLE KEYS */;
INSERT INTO `exam_remarks` (`exam_remarks_id`, `exam_group_id`, `exam_id`, `student_exam_id`, `student_id`, `remarks`, `created_at`, `updated_at`, `school_id`) VALUES (1,1,1,1,19,'Nicely Attempted','2025-01-03 00:07:09','2025-03-13 22:51:24',1),
(2,1,1,2,20,'Need lot of improvement','2025-01-03 00:07:09','2025-03-13 22:51:24',1),
(3,1,1,102,21,'Excellent','2025-01-03 00:07:09','2025-03-13 22:51:24',1),
(4,1,1,112,22,'Poor!','2025-01-03 00:07:09','2025-03-13 22:51:24',1),
(5,7,18,114,119,'Well Done','2025-03-14 22:44:29','2025-03-14 22:49:54',2),
(7,9,19,116,2,'Nicely Attempted!!!','2025-03-22 10:46:13','2025-03-22 10:46:13',1),
(8,9,19,116,2,'Nicely Attempted!!!','2025-03-22 10:46:35','2025-03-22 10:46:35',1);
/*!40000 ALTER TABLE `exam_remarks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_student`
--

DROP TABLE IF EXISTS `exam_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_student` (
  `student_exam_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_student`
--

LOCK TABLES `exam_student` WRITE;
/*!40000 ALTER TABLE `exam_student` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_subjects`
--

DROP TABLE IF EXISTS `exam_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_subjects` (
  `exam_subject_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `exam_date` date NOT NULL,
  `start_time` time NOT NULL,
  `duration` varchar(10) NOT NULL,
  `credit_hours` int(11) NOT NULL,
  `room_no` varchar(50) NOT NULL,
  `marks_max` int(11) NOT NULL,
  `marks_min` int(11) NOT NULL,
  `file_uploaded` varchar(256) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_subjects`
--

LOCK TABLES `exam_subjects` WRITE;
/*!40000 ALTER TABLE `exam_subjects` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_types`
--

DROP TABLE IF EXISTS `exam_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_types` (
  `exam_type_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_types`
--

LOCK TABLES `exam_types` WRITE;
/*!40000 ALTER TABLE `exam_types` DISABLE KEYS */;
INSERT INTO `exam_types` (`exam_type_id`, `name`, `description`, `created_at`, `updated_at`, `school_id`) VALUES (1,'basic_system','General Purpose (Pass/Fail)','2024-12-20 22:15:45','2025-03-13 22:51:24',1),
(2,'school_grade_system','School Based Grading System','2024-12-20 22:15:45','2025-03-13 22:51:24',1),
(3,'coll_grade_system','College Based Grading System','2024-12-20 22:15:45','2025-03-13 22:51:24',1),
(4,'gpa','GPA Grading System','2024-12-20 22:15:45','2025-03-13 22:51:24',1),
(5,'average_passing','Average Passing','2024-12-20 22:15:45','2025-03-13 22:51:24',1),
(6,'basic_system','General Purpose (Pass/Fail)','2024-12-20 22:15:45','2025-03-13 22:51:24',2),
(7,'school_grade_system','School Based Grading System','2024-12-20 22:15:45','2025-03-13 22:51:24',2),
(8,'coll_grade_system','College Based Grading System','2024-12-20 22:15:45','2025-03-13 22:51:24',2),
(9,'gpa','GPA Grading System','2024-12-20 22:15:45','2025-03-13 22:51:24',2),
(10,'average_passing','Average Passing','2024-12-20 22:15:45','2025-03-13 22:51:24',2),
(13,'Mid terms','Testing','2025-03-22 10:36:19','2025-03-22 10:36:19',1);
/*!40000 ALTER TABLE `exam_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_head`
--

DROP TABLE IF EXISTS `expense_head`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_head` (
  `expense_head_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_head`
--

LOCK TABLES `expense_head` WRITE;
/*!40000 ALTER TABLE `expense_head` DISABLE KEYS */;
INSERT INTO `expense_head` (`expense_head_id`, `name`, `description`, `created_at`, `updated_at`, `school_id`) VALUES (1,'Stationery Purchase','','2024-12-20 11:38:38','2025-03-13 17:51:24',1),
(2,'Electricity Bill','','2024-12-20 11:38:38','2025-03-13 17:51:24',1),
(3,'Telephone Bill','','2024-12-20 11:38:38','2025-03-13 17:51:24',1),
(4,'Miscellaneous','','2024-12-20 11:38:38','2025-03-13 17:51:24',1),
(5,'Flower','','2024-12-20 11:38:38','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `expense_head` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_list`
--

DROP TABLE IF EXISTS `expense_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_list` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `expense_date` date NOT NULL,
  `expense_head` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `session_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_list`
--

LOCK TABLES `expense_list` WRITE;
/*!40000 ALTER TABLE `expense_list` DISABLE KEYS */;
INSERT INTO `expense_list` (`id`, `name`, `description`, `invoice_no`, `expense_date`, `expense_head`, `amount`, `session_id`, `created_at`, `updated_at`, `school_id`) VALUES (61,'CBSE NEW BOOKS','NCRT Books are essential materials for students of all classes.','222','2024-12-31',1,180.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(62,'EDUCATIONAL TRIP','As informed before our school has organized Educational trip.','6656','2024-12-25',5,200.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(63,'D.S.Publication','Sales book is a book of original entry or a subsidiary book that is used to record the credit sales of the goods.','342','2024-12-20',1,150.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(64,'Online Exam Preparation','Online Exam Preparation','3453','2024-12-15',4,180.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(65,'JIO Broadband','Broadband is high speed internet connection enabled through different mediums.','24234','2024-12-10',3,220.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(66,'MP Power House','Electricity Bill means the invoice sent every month to customers or consumers stating in detail all components, charges.','5463','2024-12-05',2,150.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(67,'Extra curricular activates programs','Extra curricular activates programs.','3452','2024-12-01',5,150.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(68,'School Programme','The worker should wear disposable rubber boots, gloves (heavy duty), and a triple layer mask.','567445','2024-11-30',4,150.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(69,'Staff training','Staff training is a programme implemented by a manager or person of authority to provide specific staff members.','4533','2024-11-25',4,100.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(70,'Power house center','Electricity Bill means the invoice sent every month to customers or consumers stating in detail all components, charges.','3243','2024-11-20',2,100.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(71,'AIRTEL BROADBAND','Broadband is high speed internet connection enabled through different mediums.','3433','2024-11-15',3,150.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(72,'EDUCATIONAL TRIP','As informed before our school has organized Educational trip to NEHRU PLANETARIUM & NEHRU SCIENCE CENTRE.','5464','2024-11-10',5,200.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(73,'NCC CAMP','The worker should wear disposable rubber boots, gloves (heavy duty), and a triple layer mask.','7865','2024-11-05',4,150.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(74,'NCRT BOOKS','NCRT Books are essential materials for students of all classes.','564','2024-11-01',1,150.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(75,'Online Exam Preparation','Online Exam Preparation.','6434','2024-10-30',4,200.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(76,'MP Power Center','The new billing solution is faster and more accurate than its existing systems.','5323','2024-10-25',2,150.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(77,'School Events','Seminars are for small groups of students studying the same course. They are normally led by a tutor in a seminar room.','545','2024-10-20',4,150.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(78,'Deepak Books Publication','Sales book is a book of original entry or a subsidiary book that is used to record the credit sales of the goods.','76855','2024-10-15',1,150.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(79,'EDUCATIONAL TRIP','As informed before our school has organized Educational trip to NEHRU PLANETARIUM & NEHRU SCIENCE CENTRE on 5th October 2024.','56756','2024-10-05',5,200.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(80,'BSNL Broad Band','Broadband is high speed internet connection enabled through different mediums.','7684','2024-10-01',3,250.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(81,'Deepak Books Publication','Sales book is a book of original entry or a subsidiary book that is used to record the credit sales of the goods.','7785','2024-09-30',1,100.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(82,'School Events','The worker should wear disposable rubber boots, gloves (heavy duty), and a triple layer mask.','6754','2024-09-25',4,100.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(83,'Stock Flower','Stock is an odd name for a flower. It seems to be a reference to the “stocky” stems of perennial growth.','4532','2024-09-20',5,100.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(84,'School Seminars','Seminars are for small groups of students studying the same course. They are normally led by a tutor in a seminar room.','56432','2024-09-15',4,150.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(85,'MP Power House','Electricity Bill means the invoice sent every month to customers or consumers stating in detail all components, charges.','3432','2024-09-10',2,150.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(86,'Vidya Books Publication','Sales book is a book of original entry or a subsidiary book that is used to record the credit sales of the goods.','5463','2024-09-05',1,150.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(87,'Airtel Broadband Bill','Airtel Broadband Bill.','4532','2024-09-01',3,150.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(88,'Student Seminars','Seminars are for small groups of students studying the same course.','232','2024-08-30',5,100.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(89,'School Bus Rent','School Bus Rent.','4787','2024-08-25',4,120.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1),
(90,'Staff training Camp','Staff training is a programme implemented by a manager or person of authority to provide specific staff members with the necessary skills.','2355','2024-08-20',4,150.00,1,'2024-12-20 11:46:37','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `expense_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_discount`
--

DROP TABLE IF EXISTS `fee_discount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fee_discount` (
  `discount_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `discount_code` varchar(50) NOT NULL,
  `discount_type` enum('Percentage','Fix Amount') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `session_id` int(11) NOT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_discount`
--

LOCK TABLES `fee_discount` WRITE;
/*!40000 ALTER TABLE `fee_discount` DISABLE KEYS */;
INSERT INTO `fee_discount` (`discount_id`, `name`, `discount_code`, `discount_type`, `discount_value`, `session_id`, `school_id`) VALUES (1,'Sibling Discount','sibling-disc','Fix Amount',100.00,1,1),
(2,'Handicapped Discount','handicap-disc','Fix Amount',1000.00,1,1),
(3,'Class Topper Discount','cls-top-disc','Percentage',10.00,1,1);
/*!40000 ALTER TABLE `fee_discount` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_group`
--

DROP TABLE IF EXISTS `fee_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fee_group` (
  `fee_group_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_group`
--

LOCK TABLES `fee_group` WRITE;
/*!40000 ALTER TABLE `fee_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_master`
--

DROP TABLE IF EXISTS `fee_master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fee_master` (
  `fee_master_id` int(11) NOT NULL,
  `fee_group_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_master`
--

LOCK TABLES `fee_master` WRITE;
/*!40000 ALTER TABLE `fee_master` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee_master` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_master_codes`
--

DROP TABLE IF EXISTS `fee_master_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fee_master_codes` (
  `fee_master_codes_id` int(11) NOT NULL,
  `fee_master_id` int(11) NOT NULL,
  `fee_type_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `due_date` date DEFAULT NULL,
  `fine_type` enum('None','Percentage','Fixed Amount') DEFAULT NULL,
  `fine_value` decimal(10,2) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_master_codes`
--

LOCK TABLES `fee_master_codes` WRITE;
/*!40000 ALTER TABLE `fee_master_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee_master_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_payments`
--

DROP TABLE IF EXISTS `fee_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fee_payments` (
  `payment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `fee_master_codes_id` int(11) NOT NULL,
  `fee_structure_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_id` int(11) DEFAULT NULL,
  `discount_applied` decimal(10,2) DEFAULT 0.00,
  `fine_applied` decimal(10,2) DEFAULT 0.00,
  `balance_due` decimal(10,2) DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_payments`
--

LOCK TABLES `fee_payments` WRITE;
/*!40000 ALTER TABLE `fee_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_structure`
--

DROP TABLE IF EXISTS `fee_structure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fee_structure` (
  `fee_structure_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `fee_master_id` int(11) NOT NULL,
  `discount_type` enum('Percentage','Fixed Amount') NOT NULL,
  `value` decimal(10,2) DEFAULT NULL,
  `total_received` decimal(10,2) DEFAULT 0.00,
  `received_percentage` int(11) DEFAULT 0,
  `session_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_structure`
--

LOCK TABLES `fee_structure` WRITE;
/*!40000 ALTER TABLE `fee_structure` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee_structure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_type`
--

DROP TABLE IF EXISTS `fee_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fee_type` (
  `fee_type_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `fee_code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `sort` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_type`
--

LOCK TABLES `fee_type` WRITE;
/*!40000 ALTER TABLE `fee_type` DISABLE KEYS */;
INSERT INTO `fee_type` (`fee_type_id`, `name`, `fee_code`, `description`, `status`, `sort`, `school_id`) VALUES (1,'September Month Fees','sep-month-fees','',1,11,1),
(2,'October Month Fees','oct-month-fees',NULL,1,12,1),
(3,'November Month Fees','nov-month-fees',NULL,1,13,1),
(4,'monthly-fee-yazdan','monthlyfeeyazdan',NULL,1,27,1),
(5,'May Month Fees','may-month-fees',NULL,1,7,1),
(7,'Lumpsum fees','lumpsum-fees',NULL,1,2,1),
(8,'June Month Fees','jun-month-fees',NULL,1,8,1),
(9,'July Month Fees','jul-month-fees',NULL,1,9,1),
(10,'January Month Fees','jan-month-fees',NULL,1,3,1),
(11,'Fees','fees','',1,22,1),
(12,'February Month Fees','feb-month-fees',NULL,1,4,1),
(13,'March Month Fees','march-month-fees',NULL,1,5,1),
(14,'Exam Fees','exam-fees',NULL,1,21,1),
(16,'Topper Discount','discount123',NULL,1,24,1),
(17,'December Month Fees','dec-month-fees',NULL,1,14,1),
(18,'Certificate fee','Cert-Fee',NULL,1,23,1),
(19,'Caution Money Fees','caution-money-fees',NULL,1,25,1),
(20,'Bus-fees','Bus-fees',NULL,1,26,1),
(21,'August Month Fees','aug-month-fees',NULL,1,10,1),
(22,'April Month Fees','apr-month-fees',NULL,1,6,1),
(23,'Admission Fees','admission-fees',NULL,1,1,1),
(24,'6th Installment Fees','6-installment-fees',NULL,1,20,1),
(25,'5th Installment Fees','5-installment-fees',NULL,1,19,1),
(26,'4th Installment Fees','4-installment-fees',NULL,1,18,1),
(27,'3rd Installment Fees','3-installment-fees',NULL,1,17,1),
(28,'2nd Installment Fees','2-installment-fees',NULL,1,16,1),
(29,'1st Installment Fees','1-installment-fees',NULL,1,15,1);
/*!40000 ALTER TABLE `fee_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `front_cms_settings`
--

DROP TABLE IF EXISTS `front_cms_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `front_cms_settings` (
  `id` int(11) NOT NULL,
  `sidebar_option` enum('News','Complain') DEFAULT 'News',
  `language_id` int(11) DEFAULT NULL,
  `rtl_mode` tinyint(1) DEFAULT 0,
  `logo_path` varchar(255) DEFAULT NULL,
  `favicon_path` varchar(255) DEFAULT NULL,
  `footer_text` text DEFAULT NULL,
  `cookie_consent` tinyint(1) DEFAULT 0,
  `google_analytics_script` text DEFAULT NULL,
  `whatsapp_url` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `google_plus_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `pinterest_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `front_cms_settings`
--

LOCK TABLES `front_cms_settings` WRITE;
/*!40000 ALTER TABLE `front_cms_settings` DISABLE KEYS */;
INSERT INTO `front_cms_settings` (`id`, `sidebar_option`, `language_id`, `rtl_mode`, `logo_path`, `favicon_path`, `footer_text`, `cookie_consent`, `google_analytics_script`, `whatsapp_url`, `facebook_url`, `twitter_url`, `youtube_url`, `google_plus_url`, `linkedin_url`, `instagram_url`, `pinterest_url`, `created_at`, `updated_at`, `school_id`) VALUES (1,'News',38,1,'path/to/logo.png','path/to/favicon.png','Super School 2024-25 ',1,'<script async src=\"https://www.googletagmanager.com/gtag/js?id=GA_TRACKING_ID\"></script>©','https://www.whatsapp.com/a','https://www.facebook.com/a','https://twitter.com/a','https://www.youtube.com/a','https://plus.google.com/a','https://www.linkedin.com/a','https://www.instagram.com/a','https://in.pinterest.com/a','2024-10-18 17:38:32','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `front_cms_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guardians`
--

DROP TABLE IF EXISTS `guardians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `guardians` (
  `guardian_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `guardian_name` varchar(50) NOT NULL,
  `guardian_is` enum('Father','Mother','Other') NOT NULL,
  `relation` varchar(50) DEFAULT NULL,
  `guardian_phone` varchar(50) NOT NULL,
  `guardian_occupation` varchar(50) DEFAULT NULL,
  `guardian_email` varchar(100) DEFAULT NULL,
  `guardian_photo_path` varchar(255) DEFAULT NULL,
  `guardian_address` text DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guardians`
--

LOCK TABLES `guardians` WRITE;
/*!40000 ALTER TABLE `guardians` DISABLE KEYS */;
/*!40000 ALTER TABLE `guardians` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incidents`
--

DROP TABLE IF EXISTS `incidents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `incidents` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `point` int(11) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incidents`
--

LOCK TABLES `incidents` WRITE;
/*!40000 ALTER TABLE `incidents` DISABLE KEYS */;
/*!40000 ALTER TABLE `incidents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `income_head`
--

DROP TABLE IF EXISTS `income_head`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `income_head` (
  `income_head_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `income_head`
--

LOCK TABLES `income_head` WRITE;
/*!40000 ALTER TABLE `income_head` DISABLE KEYS */;
INSERT INTO `income_head` (`income_head_id`, `name`, `description`, `created_at`, `updated_at`, `school_id`) VALUES (1,'Donation',NULL,'2024-12-17 15:56:28','2025-03-13 17:51:24',1),
(2,'Rent',NULL,'2024-12-17 15:56:28','2025-03-13 17:51:24',1),
(3,'Miscellaneous',NULL,'2024-12-17 15:56:28','2025-03-13 17:51:24',1),
(4,'Book Sale',NULL,'2024-12-17 15:56:28','2025-03-13 17:51:24',1),
(5,'Uniform Sale',NULL,'2024-12-17 15:56:28','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `income_head` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `income_list`
--

DROP TABLE IF EXISTS `income_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `income_list` (
  `income_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `income_date` date NOT NULL,
  `income_head_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `file` varchar(256) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `income_list`
--

LOCK TABLES `income_list` WRITE;
/*!40000 ALTER TABLE `income_list` DISABLE KEYS */;
INSERT INTO `income_list` (`income_id`, `name`, `description`, `invoice_no`, `income_date`, `income_head_id`, `amount`, `file`, `session_id`, `created_at`, `updated_at`, `school_id`) VALUES (1,'CBSE Books','Distribution refers to the process and logistics of making your book available to the customer.','2342','2024-12-31',4,200.00,NULL,1,'2024-12-17 16:00:44','2025-03-13 17:51:24',1),
(2,'School Building Rent','Rental Flats in Velammal Matriculation West School - Searching 1','3523','2024-12-25',2,200.00,NULL,1,'2024-12-17 16:00:44','2025-03-13 17:51:24',1),
(3,'Sports Games','Extra curricular activates programs.','675','2024-12-20',3,200.00,NULL,1,'2024-12-17 16:00:44','2025-03-13 17:51:24',1),
(4,'New Books Publication','NCRT Books are essential materials for students of all classes.','2342','2024-12-15',4,150.00,NULL,1,'2024-12-17 16:00:44','2025-03-13 17:51:24',1),
(5,'Student Uniform','Dress codes are used to communicate to students.','546','2024-12-10',5,150.00,NULL,1,'2024-12-17 16:00:44','2025-03-13 17:51:24',1),
(6,'School Fees','Donation fee is fee which you have to give to make you eligible for student.','3234','2024-12-05',1,250.00,NULL,1,'2024-12-17 16:00:44','2025-03-13 17:51:24',1),
(7,'Online Class','Miscellaneous fees are charges that are separate from tuition that can be selected.','3534','2024-12-01',3,200.00,NULL,1,'2024-12-17 16:00:44','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `income_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` (`id`, `code`, `name`, `school_id`) VALUES (1,'aa','Afar',1),
(2,'ab','Abkhaz',1),
(3,'ae','Avestan',1),
(4,'af','Afrikaans',1),
(5,'ak','Akan',1),
(6,'am','Amharic',1),
(7,'an','Aragonese',1),
(8,'ar','Arabic',1),
(9,'as','Assamese',1),
(10,'av','Avar',1),
(11,'ay','Aymara',1),
(12,'az','Azerbaijani',1),
(13,'ba','Bashkir',1),
(14,'be','Belarusian',1),
(15,'bg','Bulgarian',1),
(16,'bh','Bihari',1),
(17,'bi','Bislama',1),
(18,'bm','Bambara',1),
(19,'bn','Bengali',1),
(20,'bo','Tibetan',1),
(21,'br','Breton',1),
(22,'bs','Bosnian',1),
(23,'ca','Catalan',1),
(24,'ce','Chechen',1),
(25,'ch','Chamorro',1),
(26,'co','Corsican',1),
(27,'cr','Cree',1),
(28,'cs','Czech',1),
(29,'cu','Church Slavonic',1),
(30,'cv','Chuvash',1),
(31,'cy','Welsh',1),
(32,'da','Danish',1),
(33,'de','German',1),
(34,'dv','Divehi',1),
(35,'dz','Dzongkha',1),
(36,'ee','Ewe',1),
(37,'el','Greek',1),
(38,'en','English',1),
(39,'eo','Esperanto',1),
(40,'es','Spanish',1),
(41,'et','Estonian',1),
(42,'eu','Basque',1),
(43,'fa','Persian',1),
(44,'ff','Fula',1),
(45,'fi','Finnish',1),
(46,'fj','Fijian',1),
(47,'fo','Faroese',1),
(48,'fr','French',1),
(49,'fy','Western Frisian',1),
(50,'ga','Irish',1),
(51,'gd','Scottish Gaelic',1),
(52,'gl','Galician',1),
(53,'gn','Guarani',1),
(54,'gu','Gujarati',1),
(55,'gv','Manx',1),
(56,'ha','Hausa',1),
(57,'he','Hebrew',1),
(58,'hi','Hindi',1),
(59,'ho','Hiri Motu',1),
(60,'hr','Croatian',1),
(61,'ht','Haitian',1),
(62,'hu','Hungarian',1),
(63,'hy','Armenian',1),
(64,'hz','Herero',1),
(65,'ia','Interlingua',1),
(66,'id','Indonesian',1),
(67,'ie','Interlingue',1),
(68,'ig','Igbo',1),
(69,'ii','Sichuan Yi',1),
(70,'ik','Inupiaq',1),
(71,'io','Ido',1),
(72,'is','Icelandic',1),
(73,'it','Italian',1),
(74,'iu','Inuktitut',1),
(75,'ja','Japanese',1),
(76,'jv','Javanese',1),
(77,'ka','Georgian',1),
(78,'kg','Kongo',1),
(79,'ki','Kikuyu',1),
(80,'kj','Kuanyama',1),
(81,'kk','Kazakh',1),
(82,'kl','Kalaallisut',1),
(83,'km','Khmer',1),
(84,'kn','Kannada',1),
(85,'ko','Korean',1),
(86,'kr','Kanuri',1),
(87,'ks','Kashmiri',1),
(88,'ku','Kurdish',1),
(89,'kv','Komi',1),
(90,'kw','Cornish',1),
(91,'ky','Kirghiz',1),
(92,'la','Latin',1),
(93,'lb','Luxembourgish',1),
(94,'lg','Ganda',1),
(95,'li','Limburgish',1),
(96,'ln','Lingala',1),
(97,'lo','Lao',1),
(98,'lt','Lithuanian',1),
(99,'lu','Luba-Katanga',1),
(100,'lv','Latvian',1),
(101,'mg','Malagasy',1),
(102,'mh','Marshallese',1),
(103,'mi','Māori',1),
(104,'mk','Macedonian',1),
(105,'ml','Malayalam',1),
(106,'mn','Mongolian',1),
(107,'mo','Moldovan',1),
(108,'mr','Marathi',1),
(109,'ms','Malay',1),
(110,'mt','Maltese',1),
(111,'my','Burmese',1),
(112,'na','Nauru',1),
(113,'nb','Norwegian Bokmål',1),
(114,'nd','North Ndebele',1),
(115,'ne','Nepali',1),
(116,'ng','Ndonga',1),
(117,'nl','Dutch',1),
(118,'nn','Norwegian Nynorsk',1),
(119,'no','Norwegian',1),
(120,'nr','South Ndebele',1),
(121,'nv','Navajo',1),
(122,'ny','Nyanja',1),
(123,'oc','Occitan',1),
(124,'oj','Ojibwe',1),
(125,'om','Oromo',1),
(126,'or','Odia',1),
(127,'os','Ossetian',1),
(128,'pa','Punjabi',1),
(129,'pi','Pali',1),
(130,'pl','Polish',1),
(131,'pm','Creole',1),
(132,'ps','Pashto',1),
(133,'pt','Portuguese',1),
(134,'qu','Quechua',1),
(135,'rm','Romansh',1),
(136,'rn','Rundi',1),
(137,'ro','Romanian',1),
(138,'ru','Russian',1),
(139,'rw','Kinyarwanda',1),
(140,'sa','Sanskrit',1),
(141,'sc','Sardinian',1),
(142,'sd','Sindhi',1),
(143,'se','Northern Sami',1),
(144,'sg','Sango',1),
(145,'si','Sinhala',1),
(146,'sk','Slovak',1),
(147,'sl','Slovenian',1),
(148,'sm','Samoan',1),
(149,'sn','Shona',1),
(150,'so','Somali',1),
(151,'sq','Albanian',1),
(152,'sr','Serbian',1),
(153,'ss','Swati',1),
(154,'st','Southern Sotho',1),
(155,'su','Sundanese',1),
(156,'sv','Swedish',1),
(157,'sw','Swahili',1),
(158,'ta','Tamil',1),
(159,'te','Telugu',1),
(160,'tg','Tajik',1),
(161,'th','Thai',1),
(162,'ti','Tigrinya',1),
(163,'tk','Turkmen',1),
(164,'tl','Tagalog',1),
(165,'tn','Tswana',1),
(166,'to','Tongan',1),
(167,'tr','Turkish',1),
(168,'ts','Tsonga',1),
(169,'tt','Tatar',1),
(170,'tw','Twi',1),
(171,'ty','Tahitian',1),
(172,'ug','Uighur',1),
(173,'uk','Ukrainian',1),
(174,'ur','Urdu',1),
(175,'uz','Uzbek',1),
(176,'ve','Tshivenda',1),
(177,'vi','Vietnamese',1),
(178,'vo','Volapük',1),
(179,'wa','Walloon',1),
(180,'wo','Wolof',1),
(181,'xh','Xhosa',1),
(182,'yi','Yiddish',1),
(183,'yo','Yoruba',1),
(184,'za','Zhuang',1),
(185,'zu','Zulu',1);
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_types`
--

DROP TABLE IF EXISTS `leave_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_types` (
  `leave_type_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `max_days` int(11) DEFAULT 0,
  `carry_forward` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `widget_color` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_types`
--

LOCK TABLES `leave_types` WRITE;
/*!40000 ALTER TABLE `leave_types` DISABLE KEYS */;
INSERT INTO `leave_types` (`leave_type_id`, `name`, `code`, `description`, `max_days`, `carry_forward`, `status`, `widget_color`, `created_at`, `updated_at`, `school_id`) VALUES (1,'Sick Leave','SL','Leave for illness or medical emergencies',12,0,1,'red3','2025-01-08 17:47:16','2025-03-13 17:51:24',1),
(2,'Casual Leave','CL','Leave for personal reasons or emergencies',10,0,1,'orange','2025-01-08 17:47:16','2025-03-13 17:51:24',1),
(3,'Annual Leave','AL','Planned leave for vacation or rest',20,0,1,'blue','2025-01-08 17:47:16','2025-03-13 17:51:24',1),
(4,'Maternity Leave','ML','Leave for maternity purposes',90,0,1,'green','2025-01-08 17:47:16','2025-03-13 17:51:24',1),
(5,'Paternity Leave','PL','Leave for paternity purposes',15,0,1,'grey','2025-01-08 17:47:16','2025-03-13 17:51:24',1),
(6,'Study Leave','STL','Leave for educational purposes or exams',30,0,1,'purple','2025-01-08 17:47:16','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `leave_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leaves`
--

DROP TABLE IF EXISTS `leaves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leaves` (
  `leave_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `applicant_type` enum('student','staff') NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','on hold') DEFAULT 'pending',
  `session_id` int(11) NOT NULL,
  `submitted_by` int(11) NOT NULL DEFAULT 1,
  `update_status` text DEFAULT NULL,
  `entertain_by` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaves`
--

LOCK TABLES `leaves` WRITE;
/*!40000 ALTER TABLE `leaves` DISABLE KEYS */;
/*!40000 ALTER TABLE `leaves` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lesson_list`
--

DROP TABLE IF EXISTS `lesson_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lesson_list` (
  `lesson_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `subject_group_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `comments` text DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson_list`
--

LOCK TABLES `lesson_list` WRITE;
/*!40000 ALTER TABLE `lesson_list` DISABLE KEYS */;
INSERT INTO `lesson_list` (`lesson_id`, `class_id`, `section_id`, `subject_group_id`, `subject_id`, `comments`, `session_id`, `school_id`, `created_at`, `updated_at`) VALUES (2,10,1,3,1,'Class 10 Chapters for now',1,1,'2025-03-16 08:42:06','2025-03-16 08:42:06'),
(4,10,1,3,3,'Initial Topics for current session',1,1,'2025-03-16 19:36:53','2025-03-16 19:36:53'),
(5,10,1,3,2,'test',1,1,'2025-03-20 05:42:19','2025-03-20 05:42:19');
/*!40000 ALTER TABLE `lesson_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lesson_topics`
--

DROP TABLE IF EXISTS `lesson_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lesson_topics` (
  `id` int(11) NOT NULL,
  `lesson_info` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `topic` text DEFAULT NULL,
  `status` enum('complete','inprogress') NOT NULL DEFAULT 'inprogress',
  `completion_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson_topics`
--

LOCK TABLES `lesson_topics` WRITE;
/*!40000 ALTER TABLE `lesson_topics` DISABLE KEYS */;
INSERT INTO `lesson_topics` (`id`, `lesson_info`, `lesson_id`, `topic`, `status`, `completion_date`, `created_at`, `updated_at`) VALUES (12,2,24,'Basic Definitions','inprogress',NULL,'2025-03-16 19:27:39','2025-03-16 19:27:39'),
(13,2,24,'Exercise 1.1','inprogress',NULL,'2025-03-16 19:27:39','2025-04-25 17:19:12'),
(14,2,24,'Exercise 1.2','inprogress',NULL,'2025-03-16 19:27:39','2025-04-25 17:19:15'),
(15,2,24,'Exercise 1.3','inprogress',NULL,'2025-03-16 19:27:39','2025-04-25 17:19:16'),
(30,4,32,'The Impact of Technology on Modern Education','inprogress',NULL,'2025-03-16 19:45:23','2025-03-16 19:45:23'),
(31,4,32,'Climate Change and Its Effects on Our Planet','inprogress',NULL,'2025-03-16 19:45:23','2025-03-16 19:45:23'),
(32,4,32,'The Importance of Mental Health Awareness in Youth','inprogress',NULL,'2025-03-16 19:45:23','2025-03-16 19:45:23'),
(33,4,32,'Social Media: A Blessing or a Curse?','inprogress',NULL,'2025-03-16 19:45:23','2025-03-16 19:45:23'),
(34,4,32,'The Role of Women in Shaping Society','inprogress',NULL,'2025-03-16 19:45:23','2025-03-16 19:45:23'),
(35,4,36,' Definition and Difference','inprogress',NULL,'2025-03-16 19:46:21','2025-03-16 19:56:45'),
(36,4,36,' Changes in Tense, Pronouns, and Time Expressions','inprogress',NULL,'2025-03-16 19:46:21','2025-03-16 19:56:45'),
(89,2,23,'Basic Definitions','complete','2025-04-25 17:19:17','2025-03-17 18:30:57','2025-04-25 17:19:17'),
(90,2,23,'Exercise 1.1','complete','2025-04-25 17:19:18','2025-03-17 18:30:57','2025-04-25 17:19:18'),
(91,2,23,'Exercise 1.3','complete','2025-04-25 17:19:19','2025-03-17 18:30:57','2025-04-25 17:19:19'),
(92,2,23,'Exercise 1.3','inprogress',NULL,'2025-03-17 18:30:57','2025-03-17 18:30:57'),
(93,5,37,'Definations','inprogress',NULL,'2025-03-20 05:43:56','2025-03-20 05:54:03'),
(94,5,37,'MCQs','complete','2025-03-20 05:44:30','2025-03-20 05:43:56','2025-03-20 05:44:30');
/*!40000 ALTER TABLE `lesson_topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lessons`
--

DROP TABLE IF EXISTS `lessons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lessons` (
  `id` int(11) NOT NULL,
  `lesson` text DEFAULT NULL,
  `lesson_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lessons`
--

LOCK TABLES `lessons` WRITE;
/*!40000 ALTER TABLE `lessons` DISABLE KEYS */;
INSERT INTO `lessons` (`id`, `lesson`, `lesson_id`, `created_at`, `updated_at`) VALUES (23,'Linera Equation',2,'2025-03-16 08:42:06','2025-03-16 08:42:06'),
(24,'Quadratic Equation',2,'2025-03-16 08:42:06','2025-03-16 08:42:06'),
(25,'Analytic geometry',2,'2025-03-16 08:42:06','2025-03-16 08:42:06'),
(26,'Statistics',2,'2025-03-16 08:42:06','2025-03-16 08:42:06'),
(27,'Probability',2,'2025-03-16 08:42:06','2025-03-16 08:42:06'),
(28,'Surface areas and Volumes',2,'2025-03-16 08:42:06','2025-03-16 08:42:06'),
(32,'English Essays',4,'2025-03-16 19:36:53','2025-03-16 19:36:53'),
(33,'Correct Use of Verb',4,'2025-03-16 19:36:53','2025-03-16 19:36:53'),
(34,'Preposition',4,'2025-03-16 19:36:53','2025-03-16 19:36:53'),
(35,'Pair of Words',4,'2025-03-16 19:36:53','2025-03-16 19:36:53'),
(36,'Direct / Indirect',4,'2025-03-16 19:36:53','2025-03-16 19:36:53'),
(37,'Chapter 1',5,'2025-03-20 05:42:19','2025-03-20 05:42:19'),
(38,'Chapter 2',5,'2025-03-20 05:42:19','2025-03-20 05:42:19');
/*!40000 ALTER TABLE `lessons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marital_status`
--

DROP TABLE IF EXISTS `marital_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `marital_status` (
  `status_id` int(11) NOT NULL,
  `status_name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marital_status`
--

LOCK TABLES `marital_status` WRITE;
/*!40000 ALTER TABLE `marital_status` DISABLE KEYS */;
INSERT INTO `marital_status` (`status_id`, `status_name`, `created_at`, `updated_at`, `school_id`) VALUES (1,'Single','2025-01-05 12:04:58','2025-03-13 17:51:24',1),
(2,'Married','2025-01-05 12:04:58','2025-03-13 17:51:24',1),
(3,'Widowed','2025-01-05 12:04:58','2025-03-13 17:51:24',1),
(4,'Separated','2025-01-05 12:04:58','2025-03-13 17:51:24',1),
(5,'Not Specified','2025-01-05 12:04:58','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `marital_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meeting_chat`
--

DROP TABLE IF EXISTS `meeting_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_chat` (
  `id` int(11) NOT NULL,
  `room_code` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `reply_to` int(11) DEFAULT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_chat`
--

LOCK TABLES `meeting_chat` WRITE;
/*!40000 ALTER TABLE `meeting_chat` DISABLE KEYS */;
INSERT INTO `meeting_chat` (`id`, `room_code`, `user_id`, `user_name`, `message`, `reply_to`, `timestamp`) VALUES (1,'0egH7D',1,'Ali Khan','Hello',NULL,'2025-10-03 10:31:33'),
(2,'0egH7D',8,'Ali Khan','I am here now',NULL,'2025-10-03 10:32:13'),
(3,'0egH7D',1,'Ali Khan','Good to see you here',2,'2025-10-03 10:32:34'),
(4,'0egH7D',1,'Ali Khan','Good',NULL,'2025-10-03 10:32:43'),
(5,'0egH7D',1,'Ali Khan','Hi',2,'2025-10-04 11:04:38'),
(6,'0egH7D',1,'Ali Khan','hi',2,'2025-10-04 11:12:34'),
(7,'0egH7D',1,'Ali Khan','ok',5,'2025-10-04 11:12:41'),
(8,'0egH7D',1,'Ali Khan','Hi how are you doing?',NULL,'2025-10-04 11:25:11'),
(9,'0egH7D',1,'Ali Khan','Test message from debug function',NULL,'2025-10-04 15:43:23'),
(10,'0egH7D',8,'Ali Khan','Test message from debug function',NULL,'2025-10-04 15:54:49'),
(11,'0egH7D',1,'Ali Khan','Test message from debug function',NULL,'2025-10-04 15:55:08'),
(12,'0egH7D',1,'Ali Khan','Test message from debug function',NULL,'2025-10-04 15:55:12'),
(13,'0egH7D',1,'Ali Khan','Hi',NULL,'2025-10-04 23:20:53'),
(14,'0egH7D',8,'Ali Khan','HIii',NULL,'2025-10-04 23:39:15'),
(15,'0egH7D',1,'Qamar Ali','Haii',NULL,'2025-10-04 23:39:34'),
(16,'0egH7D',1,'Qamar Ali','Hi',NULL,'2025-10-05 11:39:38'),
(17,'0egH7D',8,'Ali Khan','Hi',NULL,'2025-10-05 12:53:50'),
(18,'0egH7D',1,'Qamar Ali','What\'s Up',NULL,'2025-10-05 12:53:59'),
(19,'0egH7D',8,'Ali Khan','All Good!',NULL,'2025-10-05 12:54:11'),
(20,'0egH7D',1,'Qamar Ali','Hello',NULL,'2025-10-06 20:53:09'),
(21,'Ov8Qju',8,'Ali Khan','Hi',NULL,'2025-10-07 23:18:40'),
(22,'Ov8Qju',1,'Qamar Ali','Hellow',NULL,'2025-10-08 09:44:00'),
(23,'Ov8Qju',1,'Qamar Ali','OK',NULL,'2025-10-08 09:49:52'),
(24,'Ov8Qju',1,'Qamar Ali','Are you okay?',NULL,'2025-10-08 09:49:57'),
(25,'Ov8Qju',1,'Qamar Ali','Hmm',NULL,'2025-10-08 09:50:14'),
(26,'Ov8Qju',8,'Ali Khan','Hellow Man',NULL,'2025-10-08 09:51:02'),
(27,'Ov8Qju',1,'Qamar Ali','Hellow',NULL,'2025-10-08 09:51:25'),
(28,'Ov8Qju',8,'Ali Khan','Please make it working...',NULL,'2025-10-08 09:51:34'),
(29,'Ov8Qju',1,'Qamar Ali','Hwllo',NULL,'2025-10-08 11:08:45');
/*!40000 ALTER TABLE `meeting_chat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meeting_cron_logs`
--

DROP TABLE IF EXISTS `meeting_cron_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_cron_logs` (
  `id` int(11) NOT NULL,
  `task_type` enum('auto_start','create_recurring','send_reminders') NOT NULL,
  `executed_at` datetime DEFAULT current_timestamp(),
  `result` text DEFAULT NULL,
  `meetings_processed` int(11) DEFAULT 0,
  `success` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_cron_logs`
--

LOCK TABLES `meeting_cron_logs` WRITE;
/*!40000 ALTER TABLE `meeting_cron_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `meeting_cron_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meeting_logs`
--

DROP TABLE IF EXISTS `meeting_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_logs` (
  `id` int(11) DEFAULT NULL,
  `room_code` varchar(256) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `peer_id` varchar(255) DEFAULT NULL,
  `join_time` datetime DEFAULT NULL,
  `leave_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_logs`
--

LOCK TABLES `meeting_logs` WRITE;
/*!40000 ALTER TABLE `meeting_logs` DISABLE KEYS */;
INSERT INTO `meeting_logs` (`id`, `room_code`, `user_id`, `peer_id`, `join_time`, `leave_time`) VALUES (NULL,'0egH7D',1,'employee_1','2025-10-01 14:02:13','2025-10-01 14:03:00'),
(NULL,'0egH7D',1,'employee_1','2025-10-01 14:03:25','2025-10-01 14:05:44'),
(NULL,'0egH7D',1,'employee_1','2025-10-01 14:05:51','2025-10-01 14:06:26'),
(NULL,'0egH7D',1,'employee_1','2025-10-01 14:06:37','2025-10-01 14:09:03'),
(NULL,'0egH7D',8,'employee_8','2025-10-01 14:08:11','2025-10-01 14:09:14'),
(NULL,'0egH7D',1,'employee_1','2025-10-01 14:09:07','2025-10-01 14:10:26'),
(NULL,'0egH7D',8,'employee_8','2025-10-01 14:09:18','2025-10-01 14:10:19'),
(NULL,'0egH7D',1,'user_1_1759311443272','2025-10-01 14:37:26','2025-10-01 14:40:58'),
(NULL,'0egH7D',8,'user_8_1759311497897','2025-10-01 14:38:21','2025-10-01 14:41:12'),
(NULL,'0egH7D',1,'user_1_1759311664424','2025-10-01 14:41:06','2025-10-01 15:04:35'),
(NULL,'0egH7D',8,'user_8_1759311676647','2025-10-01 14:41:18','2025-10-01 15:03:41'),
(NULL,'0egH7D',6,'user_6_1759311872418','2025-10-01 14:44:39','2025-10-01 14:45:52'),
(NULL,'0egH7D',8,'user_8_1759313071564','2025-10-01 15:04:36','2025-10-01 15:05:13'),
(NULL,'0egH7D',1,'user_1_1759313088395','2025-10-01 15:04:51','2025-10-01 15:14:40'),
(NULL,'0egH7D',8,'user_8_1759313118450','2025-10-01 15:05:19','2025-10-01 15:09:05'),
(NULL,'0egH7D',1,'user_1_1759313687900','2025-10-01 15:14:51','2025-10-01 15:17:45'),
(NULL,'0egH7D',8,'user_8_1759313693628','2025-10-01 15:14:58','2025-10-01 15:18:08'),
(NULL,'0egH7D',8,'user_8_1759313931229','2025-10-01 15:18:53','2025-10-01 15:23:27'),
(NULL,'0egH7D',1,'user_1_1759313930264','2025-10-01 15:18:55','2025-10-01 15:23:45'),
(NULL,'0egH7D',1,'user_1_1759314344222','2025-10-01 15:25:50','2025-10-01 15:28:09'),
(NULL,'0egH7D',8,'user_8_1759314355969','2025-10-01 15:26:01','2025-10-01 15:28:06'),
(NULL,'0egH7D',8,'user_8_1759314729595','2025-10-01 15:32:15','2025-10-01 15:35:40'),
(NULL,'0egH7D',1,'user_1_1759314731901','2025-10-01 15:32:19','2025-10-01 15:34:22'),
(NULL,'0egH7D',1,'user_1_1759315508131','2025-10-01 15:45:09','2025-10-01 15:46:19'),
(NULL,'0egH7D',8,'user_8_1759315549786','2025-10-01 15:45:53','2025-10-01 15:46:27'),
(NULL,'0egH7D',1,'user_1_1759316490509','2025-10-01 16:01:33','2025-10-01 16:03:01'),
(NULL,'0egH7D',1,'user_1_1759318560755','2025-10-01 16:36:02','2025-10-01 16:37:12'),
(NULL,'0egH7D',8,'user_8_1759318605571','2025-10-01 16:36:49','2025-10-01 16:37:31'),
(NULL,'0egH7D',1,'user_1_1759399195335','2025-10-02 14:59:57','2025-10-02 15:01:30'),
(NULL,'0egH7D',8,'user_8_1759399210489','2025-10-02 15:00:12','2025-10-02 15:01:36'),
(NULL,'0egH7D',1,'user_1_1759399641699','2025-10-02 15:07:26','2025-10-02 15:08:27'),
(NULL,'0egH7D',1,'user_1_1759400296804','2025-10-02 15:18:20','2025-10-02 15:19:27'),
(NULL,'0egH7D',8,'user_8_1759467954624','2025-10-03 10:05:55','2025-10-03 10:07:06'),
(NULL,'0egH7D',1,'user_1_1759467956305','2025-10-03 10:05:58','2025-10-03 10:07:03'),
(NULL,'0egH7D',1,'user_1_1759468410591','2025-10-03 10:13:32','2025-10-03 10:14:16'),
(NULL,'0egH7D',1,'user_1_1759468485151','2025-10-03 10:14:46','2025-10-03 10:14:57'),
(NULL,'0egH7D',1,'user_1_1759469478530','2025-10-03 10:31:21','2025-10-03 10:33:57'),
(NULL,'0egH7D',8,'user_8_1759469505860','2025-10-03 10:31:55','2025-10-03 10:34:45'),
(NULL,'0egH7D',1,'user_1_1759557861566','2025-10-04 11:04:22','2025-10-04 11:12:02'),
(NULL,'0egH7D',1,'user_1_1759558330518','2025-10-04 11:12:11','2025-10-04 11:24:42'),
(NULL,'0egH7D',1,'user_1_1759559093822','2025-10-04 11:24:54','2025-10-04 11:36:18'),
(NULL,'0egH7D',1,'user_1_1759560425353','2025-10-04 11:47:09','2025-10-04 11:52:48'),
(NULL,'0egH7D',8,'user_8_1759560647456','2025-10-04 11:50:53','2025-10-04 11:52:59'),
(NULL,'0egH7D',1,'user_1_1759561030748','2025-10-04 11:57:16','2025-10-04 12:35:33'),
(NULL,'0egH7D',1,'user_1_1759564478327','2025-10-04 12:54:41','2025-10-04 13:10:27'),
(NULL,'0egH7D',1,'user_1_1759565434182','2025-10-04 13:10:51','2025-10-04 13:11:05'),
(NULL,'0egH7D',1,'user_1_1759565590591','2025-10-04 13:13:27','2025-10-04 13:13:47'),
(NULL,'0egH7D',1,'user_1_1759566220937','2025-10-04 13:23:56','2025-10-04 13:23:57'),
(NULL,'0egH7D',1,'user_1_1759566346377','2025-10-04 13:26:00','2025-10-04 13:26:10'),
(NULL,'0egH7D',1,'user_1_1759566671366','2025-10-04 13:31:16','2025-10-04 13:31:29'),
(NULL,'0egH7D',1,'user_1_1759567007332','2025-10-04 13:36:49','2025-10-04 13:47:03'),
(NULL,'0egH7D',1,'user_1_1759567631059','2025-10-04 13:47:29','2025-10-04 14:11:58'),
(NULL,'0egH7D',1,'user_1_1759569119601','2025-10-04 14:12:05','2025-10-04 14:14:57'),
(NULL,'0egH7D',1,'user_1_1759569450040','2025-10-04 14:17:36','2025-10-04 14:17:44'),
(NULL,'0egH7D',1,'user_1_1759569650263','2025-10-04 14:20:52','2025-10-04 14:21:08'),
(NULL,'0egH7D',1,'user_1_1759570136564','2025-10-04 14:29:08','2025-10-04 14:29:43'),
(NULL,'0egH7D',1,'user_1_1759570287155','2025-10-04 14:31:32','2025-10-04 14:31:52'),
(NULL,'0egH7D',1,'user_1_1759570922201','2025-10-04 14:42:04','2025-10-04 14:44:45'),
(NULL,'0egH7D',8,'user_8_1759570981628','2025-10-04 14:43:05','2025-10-04 14:43:16'),
(NULL,'0egH7D',1,'user_1_1759571373551','2025-10-04 14:49:35','2025-10-04 14:53:17'),
(NULL,'0egH7D',1,'user_1_1759572109809','2025-10-04 15:01:54','2025-10-04 15:07:13'),
(NULL,'0egH7D',8,'user_8_1759572343182','2025-10-04 15:06:24','2025-10-04 15:06:47'),
(NULL,'0egH7D',1,'user_1_1759574588141','2025-10-04 15:43:10','2025-10-04 15:44:43'),
(NULL,'0egH7D',8,'user_8_1759574643612','2025-10-04 15:44:17','2025-10-04 15:44:27'),
(NULL,'0egH7D',1,'user_1_1759575172463','2025-10-04 15:52:56','2025-10-04 15:55:38'),
(NULL,'0egH7D',8,'user_8_1759575205453','2025-10-04 15:53:49','2025-10-04 15:55:58'),
(NULL,'0egH7D',1,'user_1_1759575870620','2025-10-04 16:04:32','2025-10-04 18:34:44'),
(NULL,'0egH7D',1,'peer_ooenf2tp7_1','2025-10-04 23:37:41',NULL),
(NULL,'0egH7D',8,'peer_1sk6aacx2_8','2025-10-04 23:38:39',NULL),
(NULL,'0egH7D',1,'peer_mrjzej5hq_1','2025-10-05 01:06:37',NULL),
(NULL,'0egH7D',1,'peer_l2t30vnyr_1','2025-10-05 11:21:15',NULL),
(NULL,'0egH7D',1,'peer_4hr9jbri8_1','2025-10-05 11:27:38',NULL),
(NULL,'0egH7D',1,'peer_h28toh6tu_1','2025-10-05 11:30:11',NULL),
(NULL,'0egH7D',1,'peer_cdvtioqjt_1','2025-10-05 11:37:24',NULL),
(NULL,'0egH7D',1,'peer_g9ymrb2wx_1','2025-10-05 11:38:49',NULL),
(NULL,'0egH7D',8,'peer_w5ghic8mu_8','2025-10-05 11:40:06',NULL),
(NULL,'0egH7D',1,'peer_g4wnr60ac_1','2025-10-05 11:53:47',NULL),
(NULL,'0egH7D',8,'peer_ytnjhdy7i_8','2025-10-05 11:54:06',NULL),
(NULL,'0egH7D',1,'peer_87dq6wi01_1','2025-10-05 12:25:13',NULL),
(NULL,'0egH7D',8,'peer_739ok4ohy_8','2025-10-05 12:25:33',NULL),
(NULL,'0egH7D',1,'peer_pn95cfpz8_1','2025-10-05 12:40:11',NULL),
(NULL,'0egH7D',1,'peer_2eo8nuwg8_1','2025-10-05 12:40:38',NULL),
(NULL,'0egH7D',1,'peer_arulqr7xn_1','2025-10-05 12:53:20',NULL),
(NULL,'0egH7D',8,'peer_2nynprh0d_8','2025-10-05 12:53:35',NULL),
(NULL,'0egH7D',1,'peer_zzyovertl_1','2025-10-05 12:56:27',NULL),
(NULL,'0egH7D',8,'peer_qxrcwv3q7_8','2025-10-05 12:56:40',NULL),
(NULL,'0egH7D',1,'peer_058ouaotv_1','2025-10-05 13:08:50',NULL),
(NULL,'0egH7D',1,'peer_d7h2wo1nc_1','2025-10-05 13:10:11',NULL),
(NULL,'0egH7D',8,'peer_dwpfciqmr_8','2025-10-05 13:11:00',NULL),
(NULL,'0egH7D',1,'peer_xoxrte0ad_1','2025-10-05 13:47:58',NULL),
(NULL,'0egH7D',8,'peer_llp0y0nyy_8','2025-10-05 13:48:26',NULL),
(NULL,'0egH7D',1,'peer_tjkxed9mn_1','2025-10-05 13:54:00',NULL),
(NULL,'0egH7D',1,'peer_vp5ae15pw_1','2025-10-05 13:55:17',NULL),
(NULL,'0egH7D',8,'peer_c685eknj5_8','2025-10-05 13:55:36',NULL),
(NULL,'0egH7D',1,'peer_zga5mw2wp_1','2025-10-05 15:48:16',NULL),
(NULL,'0egH7D',1,'30688525-00cb-4a22-adae-775343eb219f','2025-10-05 15:51:10',NULL),
(NULL,'0egH7D',8,'0d27fa3c-3710-4de3-9424-f8f505cbea75','2025-10-05 15:51:21',NULL),
(NULL,'0egH7D',1,'peer_yqh98fyux_1','2025-10-05 15:51:53',NULL),
(NULL,'0egH7D',1,'peer_j9mpu2u6l_1','2025-10-05 15:56:34',NULL),
(NULL,'0egH7D',1,'peer_xrfne75qd_1','2025-10-05 16:14:14',NULL),
(NULL,'0egH7D',1,'peer_cgk6e1lhx_1','2025-10-05 16:30:42',NULL),
(NULL,'0egH7D',1,'peer_y6rbs7oyh_1','2025-10-05 19:27:29',NULL),
(NULL,'0egH7D',1,'peer_37eft7pw4_1','2025-10-05 19:28:42',NULL),
(NULL,'0egH7D',1,'peer_szpcr3a50_1','2025-10-05 19:30:23',NULL),
(NULL,'0egH7D',1,'peer_9te9m8sfh_1','2025-10-05 19:33:48',NULL),
(NULL,'0egH7D',1,'peer_ghcqeflzr_1','2025-10-05 19:37:01',NULL),
(NULL,'0egH7D',1,'peer_yi8dx2dw9_1','2025-10-05 19:37:41',NULL),
(NULL,'0egH7D',1,'peer_fawi8hq99_1','2025-10-05 19:50:15',NULL),
(NULL,'0egH7D',8,'peer_mioatksc0_8','2025-10-05 19:52:06',NULL),
(NULL,'0egH7D',1,'peer_8spwy202s_1','2025-10-05 20:07:56',NULL),
(NULL,'0egH7D',1,'peer_nwftdqoe0_1','2025-10-05 20:09:37',NULL),
(NULL,'0egH7D',1,'peer_n6xzufaba_1','2025-10-05 20:48:51',NULL),
(NULL,'0egH7D',1,'employee_1','2025-10-05 20:51:39','2025-10-05 20:53:58'),
(NULL,'0egH7D',1,'employee_1','2025-10-05 20:54:05','2025-10-05 20:55:21'),
(NULL,'0egH7D',1,'employee_1','2025-10-05 20:55:28','2025-10-05 20:56:38'),
(NULL,'0egH7D',8,'employee_8','2025-10-05 20:56:11','2025-10-05 20:57:40'),
(NULL,'0egH7D',1,'employee_1','2025-10-05 20:57:36','2025-10-05 21:00:57'),
(NULL,'0egH7D',8,'employee_8','2025-10-05 20:57:48','2025-10-05 20:58:43'),
(NULL,'0egH7D',8,'peer_x9lkt04ge_8','2025-10-05 20:58:55',NULL),
(NULL,'0egH7D',1,'peer_ngv1zfzk2_1','2025-10-06 20:52:40','2025-10-06 20:55:52'),
(NULL,'0egH7D',1,'peer_xx0gnvxsa_1','2025-10-06 21:32:14',NULL),
(NULL,'0egH7D',1,'peer_usz6bcmcm_1','2025-10-06 21:36:05',NULL),
(NULL,'0egH7D',1,'peer_kycx2acqw_1','2025-10-06 21:38:43',NULL),
(NULL,'0egH7D',1,'peer_y4xsr00ft_1','2025-10-06 21:51:02',NULL),
(NULL,'0egH7D',1,'peer_quhpfam2i_1','2025-10-06 22:02:53',NULL),
(NULL,'0egH7D',1,'peer_gbmzwquww_1','2025-10-06 22:10:31',NULL),
(NULL,'0egH7D',1,'peer_082pnhog6_1','2025-10-06 22:15:19',NULL),
(NULL,'0egH7D',1,'peer_u7bq0x9dw_1','2025-10-06 22:25:12',NULL),
(NULL,'0egH7D',1,'peer_ku04wai67_1','2025-10-06 22:41:40',NULL),
(NULL,'HDTlg',1,'peer_aaganhg5l_1','2025-10-07 00:01:49',NULL),
(NULL,'0egH7D',1,'peer_1lghv9imy_1','2025-10-07 00:21:01',NULL),
(NULL,'HDTlg',1,'peer_9yef8kzoh_1','2025-10-07 14:06:44',NULL),
(NULL,'Ov8Qju',1,'peer_rlwai4ite_1','2025-10-07 16:46:44',NULL),
(NULL,'Ov8Qju',1,'peer_q5gca9ldr_1','2025-10-07 16:49:54',NULL),
(NULL,'Ov8Qju',1,'peer_7u73g5fob_1','2025-10-07 16:59:30',NULL),
(NULL,'Ov8Qju',1,'peer_2l8krjdy7_1','2025-10-07 22:40:34',NULL),
(NULL,'Ov8Qju',1,'peer_ctnqso53r_1','2025-10-07 22:41:30',NULL),
(NULL,'Ov8Qju',1,'peer_3vg1xrsqy_1_1759862567008','2025-10-07 23:42:48','2025-10-07 23:43:47'),
(NULL,'Ov8Qju',8,'peer_f3sfs4smh_8_1759862580212','2025-10-07 23:43:11','2025-10-07 23:54:24'),
(NULL,'Ov8Qju',1,'peer_lf2q58egb_1_1759862635633','2025-10-07 23:43:59','2025-10-07 23:54:30'),
(NULL,'Ov8Qju',8,'peer_klz9t7kv0_8_1759863289100','2025-10-07 23:54:59',NULL),
(NULL,'Ov8Qju',1,'peer_xz1vo1lu3_1_1759863288202','2025-10-07 23:55:03','2025-10-08 00:18:28'),
(NULL,'Ov8Qju',1,'peer_1g4mbm0bd_1_1759864721076','2025-10-08 00:18:52','2025-10-08 00:31:06'),
(NULL,'Ov8Qju',8,'peer_95q2qf0o3_8_1759864739874','2025-10-08 00:19:11',NULL),
(NULL,'Ov8Qju',8,'peer_gv78du3d5_8_1759865471939','2025-10-08 00:31:15',NULL),
(NULL,'Ov8Qju',1,'peer_mo3kcbuvp_1_1759865472770','2025-10-08 00:31:21',NULL),
(NULL,'Ov8Qju',1,'peer_1_1759900686341','2025-10-08 10:18:08',NULL),
(NULL,'Ov8Qju',1,'peer_1_1759900716990','2025-10-08 10:18:38',NULL),
(NULL,'Ov8Qju',1,'peer_1_1759900877032','2025-10-08 10:21:18',NULL),
(NULL,'Ov8Qju',8,'peer_8_1759900896580','2025-10-08 10:21:37',NULL),
(NULL,'Ov8Qju',8,'peer_8_1759900909503','2025-10-08 10:21:50',NULL),
(NULL,'Ov8Qju',1,'peer_1_1759900911065','2025-10-08 10:21:52',NULL),
(NULL,'Ov8Qju',1,'peer_1_1759900965483','2025-10-08 10:22:46',NULL),
(NULL,'Ov8Qju',8,'peer_8_1759900969363','2025-10-08 10:22:50',NULL),
(NULL,'HDTlg',8,'peer_8_1759900979578','2025-10-08 10:23:00',NULL),
(NULL,'Ov8Qju',1,'peer_1_1759901643353','2025-10-08 10:34:08',NULL),
(NULL,'Ov8Qju',8,'peer_8_1759901659120','2025-10-08 10:34:23',NULL),
(NULL,'Ov8Qju',8,'peer_8_1759902166498','2025-10-08 10:42:48',NULL),
(NULL,'Ov8Qju',1,'peer_1_1759902175257','2025-10-08 10:42:57',NULL),
(NULL,'Ov8Qju',8,'peer_8_1759902961164','2025-10-08 10:56:04',NULL),
(NULL,'Ov8Qju',1,'peer_1_1759902962732','2025-10-08 10:56:07',NULL),
(NULL,'Ov8Qju',1,'peer_1_1759903553125','2025-10-08 11:05:57',NULL),
(NULL,'Ov8Qju',8,'peer_8_1759903569658','2025-10-08 11:06:14',NULL),
(NULL,'Ov8Qju',1,'peer_1_1759904814036','2025-10-08 11:26:58',NULL),
(NULL,'Ov8Qju',8,'peer_8_1759904927269','2025-10-08 11:28:50',NULL),
(NULL,'Ov8Qju',1,'peer_1_1759904942533','2025-10-08 11:29:05',NULL),
(NULL,'Ov8Qju',8,'peer_8_1759904985716','2025-10-08 11:29:50',NULL),
(NULL,'Ov8Qju',1,'peer_1_1759905484648','2025-10-08 11:38:07',NULL),
(NULL,'Ov8Qju',8,'peer_8_1759905491662','2025-10-08 11:38:19',NULL),
(NULL,'Ov8Qju',8,'peer_8_1759905547121','2025-10-08 11:39:10',NULL),
(NULL,'Ov8Qju',8,'peer_8_1759905562794','2025-10-08 11:39:26',NULL),
(NULL,'Ov8Qju',1,'peer_1_1759905575882','2025-10-08 11:39:40',NULL);
/*!40000 ALTER TABLE `meeting_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meeting_members`
--

DROP TABLE IF EXISTS `meeting_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_members` (
  `id` varchar(36) NOT NULL COMMENT 'UUID for member',
  `meeting_id` varchar(36) NOT NULL COMMENT 'UUID of meeting',
  `user_id` int(11) NOT NULL,
  `peer_id` varchar(256) DEFAULT NULL,
  `permissions_template_id` int(11) DEFAULT NULL,
  `can_speak` tinyint(1) DEFAULT NULL,
  `can_share_video` tinyint(1) DEFAULT NULL,
  `can_share_screen` tinyint(1) DEFAULT NULL,
  `is_allowed` int(11) NOT NULL DEFAULT 1,
  `invited_by` int(11) NOT NULL,
  `joined_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_members`
--

LOCK TABLES `meeting_members` WRITE;
/*!40000 ALTER TABLE `meeting_members` DISABLE KEYS */;
INSERT INTO `meeting_members` (`id`, `meeting_id`, `user_id`, `peer_id`, `permissions_template_id`, `can_speak`, `can_share_video`, `can_share_screen`, `is_allowed`, `invited_by`, `joined_at`) VALUES ('1','0',1,NULL,1,0,0,0,1,1,NULL),
('2','0',3,NULL,1,0,0,0,1,1,NULL),
('3','0',8,NULL,1,0,0,0,1,1,NULL),
('4','0',4,NULL,1,0,0,0,1,1,NULL),
('5','0',5,NULL,1,0,0,0,1,1,NULL),
('7','0',6,NULL,1,0,0,0,1,1,NULL),
('714','0',1,NULL,3,1,1,1,1,1,NULL),
('715','0',8,NULL,3,1,1,1,1,1,NULL),
('716','0',6,NULL,3,1,1,1,1,1,NULL),
('726','2',6,NULL,3,1,1,1,1,1,NULL),
('727','2',8,NULL,3,1,1,1,1,1,NULL),
('728','2',1,NULL,3,1,1,1,1,1,NULL),
('729','1',1,NULL,1,1,1,1,1,1,NULL),
('730','1',4,NULL,1,1,1,1,1,1,NULL),
('731','1',5,NULL,1,1,1,1,1,1,NULL),
('732','1',6,NULL,1,1,1,1,1,1,NULL),
('733','1',8,NULL,1,1,1,1,1,1,NULL),
('734','0',1,NULL,3,1,1,1,1,1,NULL),
('735','0',8,NULL,3,1,1,1,1,1,NULL),
('736','0',4,NULL,3,1,1,1,1,1,NULL),
('741','3',8,NULL,3,1,1,1,1,1,NULL),
('742','3',6,NULL,3,1,1,1,1,1,NULL),
('hpiH7ZgrufA0tXRr2X3-3qW3SMdbyqsTbU2j','PbB6RDv4Le_2s4HGQYnufxukkR_84S-lw0dg',100,NULL,3,1,1,1,1,1,NULL),
('KnbtDPYFbrD5As2Ehh1kVprp41R6OnkA7jV1','PbB6RDv4Le_2s4HGQYnufxukkR_84S-lw0dg',1,NULL,3,1,1,1,1,1,NULL),
('YkVX-DxJ0UX6ueQDq7gBliiMyma2VhYlLjS9','PbB6RDv4Le_2s4HGQYnufxukkR_84S-lw0dg',90,NULL,3,1,1,1,1,1,NULL);
/*!40000 ALTER TABLE `meeting_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meeting_notifications`
--

DROP TABLE IF EXISTS `meeting_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_notifications` (
  `id` varchar(36) NOT NULL,
  `meeting_id` varchar(36) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_type` enum('reminder','cancellation','reschedule','auto_start') NOT NULL,
  `message` text NOT NULL,
  `sent_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_notifications`
--

LOCK TABLES `meeting_notifications` WRITE;
/*!40000 ALTER TABLE `meeting_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `meeting_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meeting_participant_status`
--

DROP TABLE IF EXISTS `meeting_participant_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_participant_status` (
  `id` int(11) NOT NULL,
  `room_code` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `peer_id` varchar(100) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `video_enabled` tinyint(1) DEFAULT 1,
  `audio_enabled` tinyint(1) DEFAULT 1,
  `is_joined` tinyint(1) DEFAULT 0,
  `joined_at` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_participant_status`
--

LOCK TABLES `meeting_participant_status` WRITE;
/*!40000 ALTER TABLE `meeting_participant_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `meeting_participant_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meeting_participants_view`
--

DROP TABLE IF EXISTS `meeting_participants_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_participants_view` (
  `id` int(11) DEFAULT NULL,
  `room_code` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `peer_id` varchar(100) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `video_enabled` tinyint(1) DEFAULT NULL,
  `audio_enabled` tinyint(1) DEFAULT NULL,
  `is_joined` tinyint(1) DEFAULT NULL,
  `joined_at` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `meeting_id` varchar(36) DEFAULT NULL,
  `can_speak` tinyint(1) DEFAULT NULL,
  `can_share_video` tinyint(1) DEFAULT NULL,
  `can_share_screen` tinyint(1) DEFAULT NULL,
  `is_allowed` int(11) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_participants_view`
--

LOCK TABLES `meeting_participants_view` WRITE;
/*!40000 ALTER TABLE `meeting_participants_view` DISABLE KEYS */;
/*!40000 ALTER TABLE `meeting_participants_view` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meeting_peers`
--

DROP TABLE IF EXISTS `meeting_peers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_peers` (
  `id` int(11) NOT NULL,
  `room_code` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `video_enabled` tinyint(1) DEFAULT 1,
  `audio_enabled` tinyint(1) DEFAULT 1,
  `is_joined` tinyint(1) DEFAULT 1,
  `joined_at` datetime DEFAULT current_timestamp(),
  `last_activity` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `peer_id` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_peers`
--

LOCK TABLES `meeting_peers` WRITE;
/*!40000 ALTER TABLE `meeting_peers` DISABLE KEYS */;
INSERT INTO `meeting_peers` (`id`, `room_code`, `user_id`, `user_name`, `video_enabled`, `audio_enabled`, `is_joined`, `joined_at`, `last_activity`, `peer_id`, `created_at`) VALUES (119,'0egH7D',8,'Ali Khan',1,1,1,'2025-10-05 20:58:55','2025-10-05 20:58:55','peer_x9lkt04ge_8','2025-10-05 15:58:55'),
(131,'0egH7D',1,'Qamar Ali',1,1,1,'2025-10-07 00:21:01','2025-10-07 00:21:01','peer_1lghv9imy_1','2025-10-06 19:21:01'),
(132,'HDTlg',1,'Qamar Ali',1,1,1,'2025-10-07 14:06:44','2025-10-07 14:06:44','peer_9yef8kzoh_1','2025-10-07 09:06:44'),
(171,'Ov8Qju',8,'Ali Khan',1,1,1,'2025-10-08 11:39:26','2025-10-08 11:49:06','peer_8_1759905562794','2025-10-08 06:39:26');
/*!40000 ALTER TABLE `meeting_peers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meeting_recordings`
--

DROP TABLE IF EXISTS `meeting_recordings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_recordings` (
  `id` varchar(36) NOT NULL,
  `meeting_id` varchar(36) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'Duration in seconds',
  `recorded_by` int(11) NOT NULL,
  `recorded_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_recordings`
--

LOCK TABLES `meeting_recordings` WRITE;
/*!40000 ALTER TABLE `meeting_recordings` DISABLE KEYS */;
/*!40000 ALTER TABLE `meeting_recordings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meetings`
--

DROP TABLE IF EXISTS `meetings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `meetings` (
  `id` varchar(36) NOT NULL COMMENT 'UUID for meeting',
  `peer_id` varchar(256) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `host_id` int(11) NOT NULL,
  `room_code` varchar(20) NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `status` enum('scheduled','ongoing','ended','canceled') DEFAULT 'scheduled',
  `meeting_type` enum('single','daily','weekly') DEFAULT 'single',
  `auto_start` tinyint(1) DEFAULT 0,
  `recurrence_end` date DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancelled_by` int(11) DEFAULT NULL,
  `parent_meeting_id` varchar(36) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meetings`
--

LOCK TABLES `meetings` WRITE;
/*!40000 ALTER TABLE `meetings` DISABLE KEYS */;
INSERT INTO `meetings` (`id`, `peer_id`, `title`, `description`, `host_id`, `room_code`, `start_datetime`, `end_datetime`, `status`, `meeting_type`, `auto_start`, `recurrence_end`, `cancellation_reason`, `cancelled_at`, `cancelled_by`, `parent_meeting_id`, `created_at`, `updated_at`) VALUES ('PbB6RDv4Le_2s4HGQYnufxukkR_84S-lw0dg',NULL,'Class 9-10 Meeting','Daily Class 9-10 Meeting',1,'c8dZ-C','2025-10-10 21:00:00','2025-10-10 21:30:00','ongoing','single',0,NULL,'',NULL,NULL,NULL,'2025-10-10 20:28:27','2025-10-10 21:14:47');
/*!40000 ALTER TABLE `meetings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `link` varchar(255) NOT NULL,
  `icon` varchar(150) DEFAULT NULL,
  `position` enum('sidebar','navbar') DEFAULT 'sidebar',
  `order_position` int(11) DEFAULT 0,
  `is_active` int(11) DEFAULT 1,
  `view` int(11) DEFAULT 1,
  `delete` int(11) DEFAULT 1,
  `update` int(11) NOT NULL DEFAULT 1,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` (`id`, `parent_id`, `title`, `link`, `icon`, `position`, `order_position`, `is_active`, `view`, `delete`, `update`, `school_id`) VALUES (1,NULL,'Dashboard','index.php','fa fa-tachometer','sidebar',1,1,1,1,1,1),
(2,NULL,'UI & Elements','#','fa fa-desktop','sidebar',2,1,1,1,1,1),
(3,2,'Layouts','#','fa fa-caret-right','sidebar',1,1,1,1,1,1),
(4,3,'Top Menu','index.php?r=site/topmenu','fa fa-caret-right','sidebar',1,1,1,1,1,1),
(5,3,'Two Menus 1','index.php?r=site/topmenu1','fa fa-caret-right','sidebar',2,1,1,1,1,1),
(6,3,'Two Menus 2','index.php?r=site/topmenu2','fa fa-caret-right','sidebar',3,1,1,1,1,1),
(7,3,'Default Mobile Menu','index.php?r=site/mobilemenu1','fa fa-caret-right','sidebar',4,1,1,1,1,1),
(8,3,'Mobile Menu 2','index.php?r=site/mobilemenu2','fa fa-caret-right','sidebar',5,1,1,1,1,1),
(9,3,'Mobile Menu 3','index.php?r=site/mobilemenu3','fa fa-caret-right','sidebar',6,1,1,1,1,1),
(10,2,'Typography','index.php?r=site/typography','fa fa-caret-right','sidebar',2,1,1,1,1,1),
(11,2,'Elements','index.php?r=site/elements','fa fa-caret-right','sidebar',3,1,1,1,1,1),
(12,2,'Buttons & Icons','index.php?r=site/buttons','fa fa-caret-right','sidebar',4,1,1,1,1,1),
(13,2,'Content Sliders','index.php?r=site/content-slider','fa fa-caret-right','sidebar',5,1,1,1,1,1),
(14,2,'Treeview','index.php?r=site/treeview','fa fa-caret-right','sidebar',6,1,1,1,1,1),
(15,2,'jQuery UI','index.php?r=site/jquery-ui','fa fa-caret-right','sidebar',7,1,1,1,1,1),
(16,2,'Nestable Lists','index.php?r=site/nestable-list','fa fa-caret-right','sidebar',8,1,1,1,1,1),
(17,2,'Three Level Menu','#','fa fa-caret-right','sidebar',9,1,1,1,1,1),
(18,17,'Item #1','#','fa fa-leaf green','sidebar',1,1,1,1,1,1),
(19,17,'4th level','#','fa fa-pencil orange','sidebar',2,1,1,1,1,1),
(20,19,'Add Product','#','fa fa-plus purple','sidebar',1,1,1,1,1,1),
(21,19,'View Products','#','fa fa-eye pink','sidebar',2,1,1,1,1,1),
(22,NULL,'Tables','#','fa fa-list','sidebar',3,1,1,1,1,1),
(23,22,'Simple & Dynamic','index.php?r=site/tables','fa fa-caret-right','sidebar',1,1,1,1,1,1),
(24,22,'jqGrid plugin','index.php?r=site/jqgrid','fa fa-caret-right','sidebar',2,1,1,1,1,1),
(25,NULL,'Forms','#','fa fa-pencil-square-o','sidebar',4,1,1,1,1,1),
(26,25,'Form Elements','index.php?r=site/form-elements','fa fa-caret-right','sidebar',1,1,1,1,1,1),
(27,25,'Form Elements 2','index.php?r=site/form-elements2','fa fa-caret-right','sidebar',2,1,1,1,1,1),
(28,25,'Wizard & Validation','index.php?r=site/form-wizard','fa fa-caret-right','sidebar',3,1,1,1,1,1),
(29,25,'Wysiwyg & Markdown','index.php?r=site/wysiwyg','fa fa-caret-right','sidebar',4,1,1,1,1,1),
(30,25,'Dropzone File Upload','index.php?r=site/dropzone','fa fa-caret-right','sidebar',5,1,1,1,1,1),
(31,NULL,'Widgets','index.php?r=site/widgets','fa fa-list-alt','sidebar',5,1,1,1,1,1),
(32,NULL,'Calendar','index.php?r=site/calendar','fa fa-calendar','sidebar',6,1,1,1,1,1),
(33,NULL,'Gallery','index.php?r=site/gallery','fa fa-picture-o','sidebar',7,1,1,1,1,1),
(34,NULL,'More Pages','#','fa fa-tag','sidebar',8,1,1,1,1,1),
(35,34,'User Profile','index.php?r=site/profile','fa fa-caret-right','sidebar',1,1,1,1,1,1),
(36,34,'Inbox','index.php?r=site/inbox','fa fa-caret-right','sidebar',2,1,1,1,1,1),
(37,34,'Pricing Tables','index.php?r=site/pricing','fa fa-caret-right','sidebar',3,1,1,1,1,1),
(38,34,'Invoice','index.php?r=site/invoice','fa fa-caret-right','sidebar',4,1,1,1,1,1),
(39,34,'Timeline','index.php?r=site/timeline','fa fa-caret-right','sidebar',5,1,1,1,1,1),
(40,34,'Search Results','index.php?r=site/search','fa fa-caret-right','sidebar',6,1,1,1,1,1),
(41,34,'Email Templates','index.php?r=site/email','fa fa-caret-right','sidebar',7,1,1,1,1,1),
(42,34,'Login & Register','index.php?r=site/login','fa fa-caret-right','sidebar',8,1,1,1,1,1),
(43,NULL,'Other Pages','#','fa fa-file-o','sidebar',1,1,1,1,1,1),
(44,43,'FAQ','index.php?r=site/faq','fa fa-caret-right','sidebar',1,1,1,1,1,1),
(45,43,'Error 404','index.php?r=site/error404','fa fa-caret-right','sidebar',2,1,1,1,1,1),
(46,43,'Error 500','index.php?r=site/error500','fa fa-caret-right','sidebar',3,1,1,1,1,1),
(47,43,'Grid','index.php?r=site/grid','fa fa-caret-right','sidebar',4,1,1,1,1,1),
(48,43,'Blank Page','index.php?r=site/blank','fa fa-caret-right','sidebar',5,1,1,1,1,1),
(49,NULL,'Modules','index.php?r=modules/','fa fa-file-o','navbar',5,1,1,1,1,1),
(50,NULL,'Academics','index.php?r=academics/','ace-icon fa fa-folder fa-sharp-duotone fa-solid fa-graduation-cap','sidebar',NULL,1,1,1,1,1);
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `miscellaneous`
--

DROP TABLE IF EXISTS `miscellaneous`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `miscellaneous` (
  `miscellaneous_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `bank_account_number` varchar(50) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `national_id_number` varchar(50) DEFAULT NULL,
  `local_id_number` varchar(50) DEFAULT NULL,
  `rte` varchar(5) DEFAULT '0',
  `previous_school` varchar(255) DEFAULT NULL,
  `additional_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `miscellaneous`
--

LOCK TABLES `miscellaneous` WRITE;
/*!40000 ALTER TABLE `miscellaneous` DISABLE KEYS */;
/*!40000 ALTER TABLE `miscellaneous` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `link` varchar(100) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 0,
  `order_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `school_id` int(11) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT 1 COMMENT '1. Sidebar\r\n2. Navbar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modules`
--

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` (`id`, `name`, `icon`, `description`, `link`, `active`, `order_by`, `created_at`, `school_id`, `type`) VALUES (1,'Students','fa fa-users','Manage student records and information.','',1,1,'2024-10-19 09:28:18',1,1),
(2,'Fees Collection','fa fa-money','Manage fees collection and related activities.','',0,15,'2024-10-19 09:28:18',1,1),
(3,'Income','fa fa-bar-chart-o','Manage income records.','',0,7,'2024-10-19 09:28:18',1,1),
(4,'Expense','fa fa-credit-card','Manage expense records.','',0,6,'2024-10-19 09:28:18',1,1),
(5,'Attendance','fa fa-calendar-check-o','Track student attendance.','',0,12,'2024-10-19 09:28:18',1,1),
(6,'Examination','fa fa-file-text','Manage examinations and related activities.','',0,13,'2024-10-19 09:28:18',1,1),
(7,'Academics','fa-sharp-duotone fa-solid fa fa-graduation-cap','Manage academic resources and activities.','',0,10,'2024-10-19 09:28:18',1,1),
(15,'System Settings','fa fa-cogs','Manage system configurations and settings.','config/index',1,5,'2024-10-19 09:28:18',1,1),
(17,'Front Office','fa fa-building','Handle front office operations.','',0,1,'2024-10-19 09:28:18',1,1),
(18,'Human Resource','fa fa-users','Manage staff and HR operations.','',1,2,'2024-10-19 09:28:18',1,1),
(25,'Multi Class','fa fa-exchange','Handle multi-class operations.','',0,4,'2024-10-19 09:28:18',1,1),
(29,'Lesson Plan','fa fa-calendar','Manage lesson plans and syllabus status.','',0,17,'2024-10-19 09:28:18',1,1),
(30,'Online Classes','fa fa-bell-o','Manage live classes via Zoom.','onlineclass/index',1,4,'2024-10-19 09:28:18',1,1),
(33,'Behaviour Records','fa fa-solid fa-clipboard','Manage student behaviour records and incidents.','',0,8,'2024-10-19 09:28:18',1,1),
(35,'Multi Branch','fa fa-exchange','Manage multi-branch operations.','',0,3,'2024-10-19 09:28:18',1,1),
(97,'Dashboard','fa fa-television','Dashboard module','site/student',0,1,'2025-03-15 09:22:01',1,2),
(98,'My Profile','fa fa-user-plus ftlayer','Student profile details','student/profile',0,10,'2025-03-15 09:22:01',1,2),
(99,'Fees','fa fa-money ftlayer','Student fees information','student/fee',0,16,'2025-03-15 09:22:01',1,2),
(100,'Online Course','fa fa-file-video-o ftlayer','Access online courses','student/courses',0,9,'2025-03-15 09:22:01',1,2),
(101,'Zoom Classes','fa fa-video-camera ftlayer','Join Zoom classes','student/zoom',0,7,'2025-03-15 09:22:01',1,2),
(102,'Gmeet Classes','fa fa-video-camera ftlayer','Join Google Meet classes','student/meet',0,2,'2025-03-15 09:22:01',1,2),
(103,'Class Timetable','fa fa-calendar-plus-o ftlayer','View class timetable','student/timetable',0,8,'2025-03-15 09:22:01',1,2),
(104,'Syllabus','fa fa-list-ol ftlayer','Syllabus completion status','student/syllabus',0,13,'2025-03-15 09:22:01',1,2),
(105,'Homework','fa fa-flask ftlayer','Homework assignments','student/homework',0,12,'2025-03-15 09:22:01',1,2),
(106,'Online Exam','fa fa-rss ftlayer','Take online exams','student/onlineexam',0,6,'2025-03-15 09:22:01',1,2),
(107,'Apply Leave','fa fa-check-square ftlayer','Leave application form','student/apply_leave',0,15,'2025-03-15 09:22:01',1,2),
(108,'Visitor Book','fa fa-check-square ftlayer','Manage visitor log','student/visitors',0,5,'2025-03-15 09:22:01',1,2),
(109,'Download Center','fa fa-download ftlayer','Download study content','student/download',0,4,'2025-03-15 09:22:01',1,2),
(110,'My Attendance','fa fa-calendar-check-o ftlayer','View attendance','student/attendance',0,17,'2025-03-15 09:22:01',1,2),
(111,'Exams','fa fa-map-o ftlayer','Internal exam module','student/exams',0,14,'2025-03-15 09:22:01',1,2),
(112,'Notice Board','fa fa-envelope ftlayer','View notices and announcements','student/notification',0,3,'2025-03-15 09:22:01',1,2),
(113,'Teachers Reviews','fa fa-user-secret ftlayer','Teacher feedback and reviews','student/remarks',0,11,'2025-03-15 09:22:01',1,2),
(114,'Library Visit','fa fa-book ftlayer','Library books and issued items','student/library',0,18,'2025-03-15 09:22:01',1,2),
(115,'Documentations','fa fa-file-text','Manage documents of schools.','documentation/index',1,6,'2024-10-19 09:28:18',1,1),
(116,'System Reports','fa fa-line-chart','Manage overall reports of system.','',1,3,'2024-10-19 09:28:18',1,1),
(119,'Support Ticketing','fa fa-comment-o','Manage internal ticketing','support/index',0,9,'2024-10-19 09:28:18',1,1);
/*!40000 ALTER TABLE `modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modules_features`
--

DROP TABLE IF EXISTS `modules_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_features` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `link` varchar(100) DEFAULT NULL,
  `order_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `school_id` int(11) DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modules_features`
--

LOCK TABLES `modules_features` WRITE;
/*!40000 ALTER TABLE `modules_features` DISABLE KEYS */;
INSERT INTO `modules_features` (`id`, `module_id`, `icon`, `name`, `description`, `link`, `order_by`, `created_at`, `school_id`, `is_active`) VALUES (1,1,NULL,'Student','Manage student information.','students/index',NULL,'2024-10-19 10:21:23',1,1),
(2,1,NULL,'Import Student','Import student records.','students/import',NULL,'2024-10-19 10:21:23',1,0),
(3,1,NULL,'Student Categories','Manage categories of students.','students/categories',NULL,'2024-10-19 10:21:23',1,0),
(5,1,NULL,'Disabled Student','Disable student accounts.','students/disabled',NULL,'2024-10-19 10:21:23',1,1),
(8,2,NULL,'Fee Info','For General Overview about Fee','fee/index',NULL,'2024-10-19 10:21:23',1,1),
(9,2,NULL,'Fees Carry Forward','Carry forward fees from previous terms.','fee/caryforward',NULL,'2024-10-19 10:21:23',1,1),
(10,2,NULL,'Collect Fees','Manage fee collection.','fee/collectfee',NULL,'2024-10-19 10:21:23',1,1),
(11,2,NULL,'Fees Group','Manage groups of fees.','fee/group',NULL,'2024-10-19 10:21:23',1,1),
(12,2,NULL,'Fees Group Assign','Assign fees to groups.','fee/assigngroup',NULL,'2024-10-19 10:21:23',1,1),
(13,2,NULL,'Fees Type','Manage different types of fees.','fee/type',NULL,'2024-10-19 10:21:23',1,1),
(14,2,NULL,'Fees Discount','Manage fee discounts.','fee/discount',NULL,'2024-10-19 10:21:23',1,1),
(15,2,NULL,'Fees Discount Assign','Assign discounts to fees.','fee/nextdiscount',NULL,'2024-10-19 10:21:23',1,1),
(16,2,NULL,'Search Fees Payment','Search for fee payments.','fee/feepayment',NULL,'2024-10-19 10:21:23',1,1),
(17,2,NULL,'Search Due Fees','Search for due fees.','fee/duefee',NULL,'2024-10-19 10:21:23',1,1),
(18,2,NULL,'Fees Reminder','Send reminders for fee payments.','fee/reminder',NULL,'2024-10-19 10:21:23',1,1),
(19,2,NULL,'Offline Bank Payments','Manage offline bank payment records.','fee/offlinepayment',NULL,'2024-10-19 10:21:23',1,1),
(20,3,NULL,'Income','Manage income records.','income/income',NULL,'2024-10-19 10:21:23',1,1),
(21,3,NULL,'Income Head','Manage income categories.','income/head',NULL,'2024-10-19 10:21:23',1,1),
(22,3,NULL,'Search Income','Search for income records.','income/index',NULL,'2024-10-19 10:21:23',1,1),
(23,4,NULL,'Expense','Manage expense records.','expense/expense',NULL,'2024-10-19 10:21:23',1,1),
(24,4,NULL,'Expense Head','Manage expense categories.','expense/head',NULL,'2024-10-19 10:21:23',1,1),
(25,4,NULL,'Search Expense','Search for expense records.','expense/index',NULL,'2024-10-19 10:21:23',1,1),
(26,5,NULL,'Student Attendance','Manage attendance records.','humanresource/studentattendance',NULL,'2024-10-19 10:21:23',1,1),
(27,5,NULL,'Attendance By Date','View attendance by date.','humanresource/attendancereport',NULL,'2024-10-19 10:21:23',1,1),
(28,5,NULL,'Approve Leave','Approve leave requests.','humanresource/leaverequests',NULL,'2024-10-19 10:21:23',1,1),
(29,6,NULL,'Marks Grade','Manage marks and grades.','examination/grade',NULL,'2024-10-19 10:21:23',1,1),
(30,6,NULL,'Exam Group','Manage groups for exams.','examination/group',2,'2024-10-19 10:21:23',1,1),
(31,6,NULL,'Design Admit Card','Design and print admit cards.',NULL,NULL,'2024-10-19 10:21:23',1,1),
(32,6,NULL,'Print Admit Card','Print admit cards for students.',NULL,NULL,'2024-10-19 10:21:23',1,1),
(33,6,NULL,'Design Marksheet','Design and print marksheets.','examination/design',NULL,'2024-10-19 10:21:23',1,1),
(34,6,NULL,'Print Marksheet','Print marksheets for students.','examination/marksheet',NULL,'2024-10-19 10:21:23',1,1),
(35,6,NULL,'Exam Result','Publish exam results.','examination/result',NULL,'2024-10-19 10:21:23',1,1),
(36,6,NULL,'Marks Import','Import marks from external sources.','examination/import',NULL,'2024-10-19 10:21:23',1,1),
(37,6,NULL,'Exam','Manage examination schedules.','examination/',NULL,'2024-10-19 10:21:23',1,1),
(38,6,NULL,'Exam Publish','Publish exam details.','examination/publishexams',NULL,'2024-10-19 10:21:23',1,1),
(39,6,NULL,'Link Exam','Link exams with subjects.',NULL,NULL,'2024-10-19 10:21:23',1,1),
(40,6,NULL,'Assign / View student','Assign students to exams and view details.',NULL,NULL,'2024-10-19 10:21:23',1,1),
(41,6,NULL,'Exam Subject','Manage subjects for exams.',NULL,NULL,'2024-10-19 10:21:23',1,1),
(42,6,NULL,'Exam Marks','Manage marks for exams.','examination/addexammarks',NULL,'2024-10-19 10:21:23',1,1),
(43,6,NULL,'Marks Division','Divide marks into categories.','examination/division',NULL,'2024-10-19 10:21:23',1,1),
(44,6,NULL,'Exam Schedule','Manage exam schedules.','examination/schedule',NULL,'2024-10-19 10:21:23',1,1),
(45,6,NULL,'Generate Rank','Generate student ranks based on performance.',NULL,NULL,'2024-10-19 10:21:23',1,1),
(46,7,NULL,'Class Timetable','Manage class timetables.','academics/timetable',NULL,'2024-10-19 10:21:23',1,1),
(47,7,NULL,'Subjects','Manage subjects offered.','academics/index&flag=subjects',NULL,'2024-10-19 10:21:23',1,1),
(48,7,NULL,'Classes','Manage class details.','academics/index&flag=classes',NULL,'2024-10-19 10:21:23',1,1),
(49,7,NULL,'Sections','Manage sections within classes.','academics/index&flag=sections',NULL,'2024-10-19 10:21:23',1,1),
(50,7,NULL,'Promote Student','Promote students to the next class.','academics/promotestudents',NULL,'2024-10-19 10:21:23',1,1),
(51,7,NULL,'Assign Class Teacher','Assign teachers to classes.','academics/assignclassteacher',NULL,'2024-10-19 10:21:23',1,1),
(52,7,NULL,'Teachers Timetable','Manage teachers’ timetables.',NULL,NULL,'2024-10-19 10:21:23',1,1),
(53,7,NULL,'Subject Group','Manage groups of subjects.','academics/subjectgroup',NULL,'2024-10-19 10:21:23',1,1),
(161,17,NULL,'Admission Enquiry','Manage admission inquiries.','frontoffice/admissionenquiry',NULL,'2024-10-19 10:21:23',1,1),
(163,17,NULL,'Visitor Book','Manage visitor logs.','frontoffice/visitors',NULL,'2024-10-19 10:21:23',1,1),
(164,17,NULL,'Phone Call Log','Log phone calls for inquiries.','frontoffice/phonecalllogs',NULL,'2024-10-19 10:21:23',1,1),
(167,17,NULL,'Complaints','Manage complaints.','frontoffice/complaints',NULL,'2024-10-19 10:21:23',1,1),
(168,17,NULL,'Setup Front Office','Setup configurations for front office operations.','frontoffice/setup',NULL,'2024-10-19 10:21:23',1,1),
(169,18,NULL,'Staff','Manage staff records.','humanresource/index',NULL,'2024-10-19 10:21:23',1,1),
(170,18,NULL,'Disable Staff','Disable staff accounts.',NULL,NULL,'2024-10-19 10:21:23',1,1),
(171,18,NULL,'Staff Attendance','Manage staff attendance records.','humanresource/attendance',NULL,'2024-10-19 10:21:23',1,0),
(172,18,NULL,'Staff Payroll','Manage payroll for staff.','humanresource/payroll',NULL,'2024-10-19 10:21:23',1,1),
(173,18,NULL,'Approve Leave Request','Approve staff leave requests.','humanresource/leaverequests',NULL,'2024-10-19 10:21:23',1,0),
(174,18,NULL,'Apply Leave','Allow staff to apply for leave.',NULL,NULL,'2024-10-19 10:21:23',1,0),
(175,18,NULL,'Leave Types','Manage types of leave available.',NULL,NULL,'2024-10-19 10:21:23',1,0),
(176,18,NULL,'Department','Manage departments within the organization.',NULL,NULL,'2024-10-19 10:21:23',1,0),
(177,18,NULL,'Designation','Manage designations of staff.',NULL,NULL,'2024-10-19 10:21:23',1,0),
(180,18,NULL,'Teachers Rating','Manage ratings for teachers.',NULL,NULL,'2024-10-19 10:21:23',1,0),
(202,25,NULL,'Multi Class Setup','Set up multi-class structures.',NULL,NULL,'2024-10-19 10:21:23',1,1),
(203,25,NULL,'Class Allocation','Allocate students to multiple classes.',NULL,NULL,'2024-10-19 10:21:23',1,1),
(213,29,NULL,'Copy Old Lessons','copy old lesson plans for sessions','lessonplans/copy',1,'2024-10-19 10:21:23',1,1),
(214,29,NULL,'Syllabus Status','Track syllabus completion status.','lessonplans/status',2,'2024-10-19 10:21:23',1,1),
(215,30,NULL,'Schedule Class','Schedule live classes using Zoom.','onlineclass/index',NULL,'2024-10-19 10:21:23',1,0),
(216,30,NULL,'Class Room','Manage links for Zoom classes.','onlineclass/room',NULL,'2024-10-19 10:21:23',1,0),
(217,30,NULL,'Zoom Attendance','Manage attendance for Zoom classes.',NULL,NULL,'2024-10-19 10:21:23',1,0),
(225,33,NULL,'Assign Incident','Manage behaviour records for students.','behaviour/assignincident',NULL,'2024-10-19 10:21:23',1,1),
(226,33,NULL,'Incidents','Create reports for incidents.','behaviour/incident',NULL,'2024-10-19 10:21:23',1,1),
(230,35,NULL,'Branch Management','Manage different branches of the institution.',NULL,NULL,'2024-10-19 10:21:23',1,1),
(231,35,NULL,'Branch Staff','Manage staff across branches.',NULL,NULL,'2024-10-19 10:21:23',1,1),
(232,35,NULL,'Branch Resources','Manage resources allocated to branches.',NULL,NULL,'2024-10-19 10:21:23',1,1),
(238,2,NULL,'Fees Master','Manage fee types and structures.','fee/master',NULL,'2024-12-02 10:20:05',1,1),
(402,33,NULL,'Report','Reports for incidents.','behaviour/',NULL,'2024-10-19 10:21:23',1,1),
(403,29,NULL,'Lesson','Manage and Add Lesson for classes','lessonplans/lesson',3,'2024-10-19 10:21:23',1,1),
(404,29,NULL,'Topic','Manage and Add topics for classes and subjects','lessonplans/topic',4,'2024-10-19 10:21:23',1,1),
(405,6,NULL,'Exam Type','Manage type of exams.','examination/type',1,'2024-10-19 10:21:23',1,1),
(406,116,NULL,'Students','Manage student\'s reports.','reporting/index&406',1,'2024-10-19 10:21:23',1,1),
(407,116,NULL,'Attendance','Manage attendance reports.','reporting/index&407',2,'2024-10-19 10:21:23',1,0),
(408,116,NULL,'Examinations','Manage exams reports of system.','reporting/index&408',3,'2024-10-19 10:21:23',1,0),
(409,116,NULL,'Online Classes','Manage online classes reports of system.','reporting/index&409',4,'2024-10-19 10:21:23',1,1),
(410,116,NULL,'Human Resource','Manage HRM reports of system.','reporting/index&410',5,'2024-10-19 10:21:23',1,1),
(411,116,NULL,'User Logs','Manage exams reports of system.','reporting/index&411',6,'2024-10-19 10:21:23',1,0);
/*!40000 ALTER TABLE `modules_features` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_event`
--

DROP TABLE IF EXISTS `notification_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_event` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `sample_message` text DEFAULT NULL,
  `email` tinyint(4) NOT NULL DEFAULT 1,
  `sms` tinyint(4) NOT NULL DEFAULT 1,
  `push` tinyint(4) NOT NULL DEFAULT 1,
  `student` tinyint(4) NOT NULL DEFAULT 1,
  `guardian` tinyint(4) NOT NULL DEFAULT 1,
  `admin` tinyint(4) NOT NULL DEFAULT 1,
  `session_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_event`
--

LOCK TABLES `notification_event` WRITE;
/*!40000 ALTER TABLE `notification_event` DISABLE KEYS */;
INSERT INTO `notification_event` (`id`, `name`, `code`, `sample_message`, `email`, `sms`, `push`, `student`, `guardian`, `admin`, `session_id`, `school_id`) VALUES (1,'Student Admission','ADM001','Dear {{student_name}} your admission is confirm in Class: {{class}} Section: {{section}} for Session: {{current_session_name}} for more detail contact System Admin {{admission_no}}',1,1,1,1,1,1,1,1),
(2,'Exam Result','EXAM001','Dear {{student_name}} - {{exam_roll_no}}, your {{exam}} result has been published.',1,1,1,1,1,1,1,1),
(3,'Fee Submission','FEE001','Dear parents, we have received Fees Amount {{fee_amount}} for {{student_name}} by Your School Name {{class}} {{section}} {{fee_amount}}',1,1,1,1,1,1,1,1),
(4,'Absent Attendance','ABS001','Absent Notice: {{student_name}} was absent on {{date}} in period {{subject_name}} {{subject_code}} from Your School Name',1,1,1,1,1,1,1,1),
(5,'Homework','HOME001','New Homework has been created for {{student_name}} at {{homework_date}} for the class {{class}} {{section}}. kindly submit your homework before {{submit_date}}.',1,1,1,1,1,1,1,1),
(6,'Fees Reminder','REM001','Dear parents, please pay fee amount Rs.{{due_amount}} of {{fee_type}} before {{due_date}} for {{student_name}}',1,1,1,1,1,1,1,1),
(7,'Forgot Password','PASS001','Dear {{name}}, Recently a request was submitted to reset password for your account. {{resetPassLink}}.',1,1,1,1,1,1,1,1),
(8,'Online Examination Publish Exam','EXAMPUB001','A new exam {{exam_title}} has been created for duration: {{time_duration}} min, which will be available from: {{exam_from}} to {{exam_to}}.',1,1,1,1,1,1,1,1),
(9,'Online Examination Publish Result','EXAMRES001','Exam {{exam_title}} result has been declared which was conducted between {{exam_from}} to {{exam_to}}',1,1,1,1,1,1,1,1),
(10,'Zoom Live Classes','ZOOMCLASS001','Dear student, your live class {{title}} has been scheduled on {{date}} for the duration of {{duration}} minute',1,1,1,1,1,1,1,1),
(11,'Zoom Live Meetings','GMEETCLASS001','Dear student, your live class {{title}} has been scheduled on {{date}} for the duration of {{duration}} minute',1,1,1,1,1,1,1,1),
(12,'Gmeet Live Meeting',NULL,NULL,1,1,1,1,1,1,1,1),
(13,'Gmeet Live Classes',NULL,NULL,1,1,1,1,1,1,1,1),
(14,'Gmeet Live Meeting Start',NULL,NULL,1,1,1,1,1,1,1,1),
(15,'Gmeet Live Classes Start',NULL,NULL,1,1,1,1,1,1,1,1),
(16,'Zoom Live Classes Start',NULL,NULL,1,1,1,1,1,1,1,1),
(17,'Zoom Live Meetings Start',NULL,NULL,1,1,1,1,1,1,1,1),
(18,'Online Admission Form Submission',NULL,NULL,1,1,1,1,1,1,1,1),
(19,'Online Admission Fees Submission',NULL,NULL,1,1,1,1,1,1,1,1),
(20,'Online Course Publish',NULL,NULL,1,1,1,1,1,1,1,1),
(21,'Online Course Purchase',NULL,NULL,1,1,1,1,1,1,1,1),
(22,'Student Login Credential',NULL,NULL,1,1,1,1,1,1,1,1),
(23,'Staff Login Credential',NULL,NULL,1,1,1,1,1,1,1,1),
(24,'Fee Processing',NULL,NULL,1,1,1,1,1,1,1,1),
(25,'Online Admission Fees Processing',NULL,NULL,1,1,1,1,1,1,1,1),
(26,'Student Apply Leave',NULL,NULL,1,1,1,1,1,1,1,1),
(27,'Email PDF Exam Marksheet',NULL,NULL,1,1,1,1,1,1,1,1),
(28,'Online Course Purchase For Guest User',NULL,NULL,1,1,1,1,1,1,1,1),
(29,'Online Course Guest User Sign Up',NULL,NULL,1,1,1,1,1,1,1,1),
(30,'Behaviour Incident Assigned',NULL,NULL,1,1,1,1,1,1,1,1),
(31,'CBSE Exam Marksheet Pdf',NULL,NULL,1,1,1,1,1,1,1,1),
(32,'CBSE Exam Result',NULL,NULL,1,1,1,1,1,1,1,1);
/*!40000 ALTER TABLE `notification_event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meeting_id` char(36) DEFAULT NULL,
  `message` text NOT NULL,
  `send_at` datetime NOT NULL,
  `sent` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` (`id`, `user_id`, `meeting_id`, `message`, `send_at`, `sent`) VALUES (1,5,'7','You are invited to the meeting: Online Testing','2025-05-31 13:32:14',0),
(3,4,'1','You are invited to the meeting: Test Meeting','2025-10-07 00:55:09',0),
(7,100,'PbB6RDv4Le_2s4HGQYnufxukkR_84S-lw0dg','You are invited to the meeting: Class 9-10 Meeting','2025-10-10 20:28:27',0),
(12,3,'UaJepdN5dV8S6tJQ_Ivu_DRLp52P9CsMZNM1','You are invited to the meeting: Meeting 1','2025-05-07 23:34:46',0),
(94,5,'UaJepdN5dV8S6tJQ_Ivu_DRLp52P9CsMZNM1','You are invited to the meeting: Meeting 1','2025-05-07 23:34:46',0),
(95,8,'UaJepdN5dV8S6tJQ_Ivu_DRLp52P9CsMZNM1','You are invited to the meeting: Meeting 1','2025-05-07 23:34:46',0),
(96,6,'UaJepdN5dV8S6tJQ_Ivu_DRLp52P9CsMZNM1','You are invited to the meeting: Meeting 1','2025-05-07 23:34:46',0),
(97,1,'UaJepdN5dV8S6tJQ_Ivu_DRLp52P9CsMZNM1','You are invited to the meeting: Meeting 1','2025-05-07 23:34:46',0),
(98,4,'ab8W13yu0IOwktscao9OwrComuLRFUW11WVg','You are invited to the meeting: Test','2025-05-11 22:46:10',0),
(99,5,'ab8W13yu0IOwktscao9OwrComuLRFUW11WVg','You are invited to the meeting: Test','2025-05-11 22:46:10',0),
(100,6,'ab8W13yu0IOwktscao9OwrComuLRFUW11WVg','You are invited to the meeting: Test','2025-05-11 22:46:10',0),
(101,1,'7To2zEVadMoTBdV8KUSn2WjZu81zDuHU4VBx','You are invited to the meeting: Online Testing','2025-05-14 19:59:56',0),
(102,8,'7To2zEVadMoTBdV8KUSn2WjZu81zDuHU4VBx','You are invited to the meeting: Online Testing','2025-05-14 19:59:56',0),
(103,6,'7To2zEVadMoTBdV8KUSn2WjZu81zDuHU4VBx','You are invited to the meeting: Online Testing','2025-05-14 19:59:56',0),
(104,5,'7To2zEVadMoTBdV8KUSn2WjZu81zDuHU4VBx','You are invited to the meeting: Online Testing','2025-05-14 19:59:56',0),
(106,1,'7','You are invited to the meeting: Online Testing','2025-05-31 13:32:14',0),
(107,8,'7','You are invited to the meeting: Online Testing','2025-05-31 13:32:14',0),
(108,6,'7','You are invited to the meeting: Online Testing','2025-05-31 13:32:14',0),
(109,1,'wClJ3EA3II5DuVA55Bg0FFgpns-S2Dcjf0kR','You are invited to the meeting: testing','2025-10-01 13:22:04',0),
(110,1,'iNLuuUQsmLcaFtak860Ma22X6zZCPET36CYj','You are invited to the meeting: Class No 1','2025-10-01 14:00:28',0),
(111,3,'iNLuuUQsmLcaFtak860Ma22X6zZCPET36CYj','You are invited to the meeting: Class No 1','2025-10-01 14:00:28',0),
(112,8,'iNLuuUQsmLcaFtak860Ma22X6zZCPET36CYj','You are invited to the meeting: Class No 1','2025-10-01 14:00:28',0),
(113,4,'iNLuuUQsmLcaFtak860Ma22X6zZCPET36CYj','You are invited to the meeting: Class No 1','2025-10-01 14:00:28',0),
(114,5,'iNLuuUQsmLcaFtak860Ma22X6zZCPET36CYj','You are invited to the meeting: Class No 1','2025-10-01 14:00:28',0),
(115,6,'iNLuuUQsmLcaFtak860Ma22X6zZCPET36CYj','You are invited to the meeting: Class No 1','2025-10-01 14:00:28',0),
(120,1,'tObHPfdN4L0JjN6Jq3_I5ahqXDTVPgRcPmTX','You are invited to the meeting: Class No 1','2025-10-06 22:46:20',0),
(121,8,'tObHPfdN4L0JjN6Jq3_I5ahqXDTVPgRcPmTX','You are invited to the meeting: Class No 1','2025-10-06 22:46:20',0),
(122,6,'tObHPfdN4L0JjN6Jq3_I5ahqXDTVPgRcPmTX','You are invited to the meeting: Class No 1','2025-10-06 22:46:20',0),
(133,8,'2','You are invited to the meeting: Class No 1','2025-10-07 00:02:56',0),
(134,1,'2','You are invited to the meeting: Class No 1','2025-10-07 00:02:56',0),
(135,1,'1','You are invited to the meeting: Test Meeting','2025-10-07 00:55:09',0),
(136,5,'1','You are invited to the meeting: Test Meeting','2025-10-07 00:55:09',0),
(137,8,'1','You are invited to the meeting: Test Meeting','2025-10-07 00:55:09',0),
(138,1,'lcPjv4iydjTil05osqNvDXpwWxa-YTfqLaeb','You are invited to the meeting: Testing Meeting','2025-10-07 16:03:52',0),
(139,8,'lcPjv4iydjTil05osqNvDXpwWxa-YTfqLaeb','You are invited to the meeting: Testing Meeting','2025-10-07 16:03:52',0),
(140,4,'lcPjv4iydjTil05osqNvDXpwWxa-YTfqLaeb','You are invited to the meeting: Testing Meeting','2025-10-07 16:03:52',0),
(142,8,'3','You are invited to the meeting: Testing Meeting','2025-10-07 16:11:02',0),
(143,6,'3','You are invited to the meeting: Testing Meeting','2025-10-07 16:11:02',0),
(144,1,'PbB6RDv4Le_2s4HGQYnufxukkR_84S-lw0dg','You are invited to the meeting: Class 9-10 Meeting','2025-10-10 20:28:27',0),
(145,90,'PbB6RDv4Le_2s4HGQYnufxukkR_84S-lw0dg','You are invited to the meeting: Class 9-10 Meeting','2025-10-10 20:28:27',0);
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parents`
--

DROP TABLE IF EXISTS `parents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `parents` (
  `parent_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `father_name` varchar(50) DEFAULT NULL,
  `father_phone` varchar(50) DEFAULT NULL,
  `father_occupation` varchar(50) DEFAULT NULL,
  `father_photo_path` varchar(255) DEFAULT NULL,
  `mother_name` varchar(50) DEFAULT NULL,
  `mother_phone` varchar(50) DEFAULT NULL,
  `mother_occupation` varchar(50) DEFAULT NULL,
  `mother_photo_path` varchar(255) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parents`
--

LOCK TABLES `parents` WRITE;
/*!40000 ALTER TABLE `parents` DISABLE KEYS */;
INSERT INTO `parents` (`parent_id`, `student_id`, `father_name`, `father_phone`, `father_occupation`, `father_photo_path`, `mother_name`, `mother_phone`, `mother_occupation`, `mother_photo_path`, `school_id`) VALUES (1,1,'Mohammad Khan','03001234567','Engineer',NULL,'Shazia Khan','03002345678','Teacher',NULL,1),
(2,2,'Rashid Ali','03011234567','Doctor',NULL,'Nazia Ali','03012345678','Housewife',NULL,1),
(3,3,'Tariq Ahmed','03021234567','Businessman',NULL,'Sana Ahmed','03022345678','Lecturer',NULL,1),
(4,4,'Wasim Hassan','03031234567','Lawyer',NULL,'Hina Hassan','03032345678','Nurse',NULL,1),
(5,5,'Zahid Malik','03041234567','Accountant',NULL,'Farah Malik','03042345678','Doctor',NULL,1),
(6,6,'Asif Raza','03051234567','Architect',NULL,'Samina Raza','03052345678','Designer',NULL,1),
(7,7,'Nadeem Sheikh','03061234567','Banker',NULL,'Rabia Sheikh','03062345678','Teacher',NULL,1),
(8,8,'Khalid Iqbal','03071234567','Civil Servant',NULL,'Ayesha Iqbal','03072345678','Principal',NULL,1),
(9,9,'Shahid Yousaf','03081234567','Entrepreneur',NULL,'Saima Yousaf','03082345678','Pharmacist',NULL,1),
(10,10,'Javed Hussain','03091234567','Police Officer',NULL,'Zainab Hussain','03092345678','Lawyer',NULL,1),
(11,11,'Imtiaz Farooq','03101234567','IT Consultant',NULL,'Maria Farooq','03102345678','Software Engineer',NULL,1),
(12,12,'Aamir Saleem','03111234567','Journalist',NULL,'Kiran Saleem','03112345678','Editor',NULL,1),
(13,13,'Faizan Nawaz','03121234567','Pilot',NULL,'Nadia Nawaz','03122345678','Flight Attendant',NULL,1),
(14,14,'Arshad Tariq','03131234567','Manager',NULL,'Saira Tariq','03132345678','HR Officer',NULL,1),
(15,15,'Fahad Abbasi','03141234567','Scientist',NULL,'Lubna Abbasi','03142345678','Researcher',NULL,1),
(16,16,'Salman Jamil','03151234567','Dentist',NULL,'Huma Jamil','03152345678','Dietitian',NULL,1),
(17,17,'Rizwan Saeed','03161234567','Contractor',NULL,'Sidra Saeed','03162345678','Interior Designer',NULL,1),
(18,18,'Naveed Shakeel','03171234567','Farmer',NULL,'Uzma Shakeel','03172345678','Social Worker',NULL,1),
(19,19,'Adnan Asif','03181234567','Chef',NULL,'Bushra Asif','03182345678','Nutritionist',NULL,1),
(20,20,'Waqar Butt','03191234567','Sports Coach',NULL,'Tayyaba Butt','03192345678','Physiotherapist',NULL,1);
/*!40000 ALTER TABLE `parents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll`
--

DROP TABLE IF EXISTS `payroll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll` (
  `payroll_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `epfno` varchar(255) DEFAULT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `contract_type` int(11) NOT NULL DEFAULT 1,
  `work_shift` varchar(255) DEFAULT NULL,
  `work_location` text DEFAULT NULL,
  `leave_id` int(11) DEFAULT NULL,
  `medical_leaves` int(11) DEFAULT 0,
  `casual_leaves` int(11) DEFAULT 0,
  `maternity_leaves` int(11) DEFAULT 0,
  `sick_leaves` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll`
--

LOCK TABLES `payroll` WRITE;
/*!40000 ALTER TABLE `payroll` DISABLE KEYS */;
INSERT INTO `payroll` (`payroll_id`, `staff_id`, `epfno`, `basic_salary`, `contract_type`, `work_shift`, `work_location`, `leave_id`, `medical_leaves`, `casual_leaves`, `maternity_leaves`, `sick_leaves`, `created_at`, `updated_at`, `school_id`) VALUES (1,16,NULL,0.00,1,NULL,NULL,NULL,0,0,0,0,'2025-10-10 11:51:20','2025-10-10 14:51:20',1),
(2,31,NULL,3000.00,1,NULL,NULL,NULL,0,0,0,0,'2025-10-10 12:01:12','2025-10-10 15:01:12',1),
(3,30,NULL,30000.00,1,NULL,NULL,NULL,0,0,0,0,'2025-10-10 12:03:11','2025-10-10 15:03:11',1),
(4,22,NULL,0.00,1,NULL,NULL,NULL,0,0,0,0,'2025-10-10 15:13:02','2025-10-10 18:13:02',1),
(5,19,NULL,0.00,1,NULL,NULL,NULL,0,0,0,0,'2025-10-10 15:13:02','2025-10-10 18:13:02',1),
(6,23,NULL,0.00,1,NULL,NULL,NULL,0,0,0,0,'2025-10-10 15:13:02','2025-10-10 18:13:02',1),
(7,21,NULL,0.00,1,NULL,NULL,NULL,0,0,0,0,'2025-10-10 15:13:02','2025-10-10 18:13:02',1),
(8,25,NULL,0.00,1,NULL,NULL,NULL,0,0,0,0,'2025-10-10 15:13:02','2025-10-10 18:13:02',1),
(9,24,NULL,0.00,1,NULL,NULL,NULL,0,0,0,0,'2025-10-10 15:13:02','2025-10-10 18:13:02',1),
(10,17,NULL,0.00,1,NULL,NULL,NULL,0,0,0,0,'2025-10-10 15:13:02','2025-10-10 18:13:02',1),
(11,18,NULL,0.00,1,NULL,NULL,NULL,0,0,0,0,'2025-10-10 15:13:02','2025-10-10 18:13:02',1),
(12,27,NULL,0.00,1,NULL,NULL,NULL,0,0,0,0,'2025-10-10 15:13:02','2025-10-10 18:13:02',1),
(13,29,NULL,0.00,1,NULL,NULL,NULL,0,0,0,0,'2025-10-10 15:13:02','2025-10-10 18:13:02',1),
(14,20,NULL,0.00,1,NULL,NULL,NULL,0,0,0,0,'2025-10-10 15:13:02','2025-10-10 18:13:02',1),
(15,26,NULL,0.00,1,NULL,NULL,NULL,0,0,0,0,'2025-10-10 15:13:02','2025-10-10 18:13:02',1),
(16,28,NULL,0.00,1,NULL,NULL,NULL,0,0,0,0,'2025-10-10 15:13:02','2025-10-10 18:13:02',1);
/*!40000 ALTER TABLE `payroll` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_allowances`
--

DROP TABLE IF EXISTS `payroll_allowances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_allowances` (
  `allowance_id` int(11) NOT NULL,
  `payroll_id` int(11) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_allowances`
--

LOCK TABLES `payroll_allowances` WRITE;
/*!40000 ALTER TABLE `payroll_allowances` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_allowances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_deductions`
--

DROP TABLE IF EXISTS `payroll_deductions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_deductions` (
  `deduction_id` int(11) NOT NULL,
  `payroll_id` int(11) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_deductions`
--

LOCK TABLES `payroll_deductions` WRITE;
/*!40000 ALTER TABLE `payroll_deductions` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_deductions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `module_id` int(11) DEFAULT NULL,
  `feature_id` int(11) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  `can_view` tinyint(1) DEFAULT 0,
  `can_add` tinyint(1) DEFAULT 0,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` (`id`, `module_id`, `feature_id`, `role_id`, `is_active`, `can_view`, `can_add`, `can_edit`, `can_delete`, `created_at`, `school_id`) VALUES (4,1,1,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(5,1,1,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(6,1,1,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(10,1,2,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(11,1,2,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(12,1,2,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(16,1,3,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(17,1,3,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(18,1,3,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(28,1,5,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(29,1,5,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(30,1,5,3,1,0,0,0,0,'2024-10-19 11:49:04',1),
(46,2,8,4,1,0,0,0,0,'2024-10-19 11:49:04',1),
(47,2,8,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(48,2,8,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(52,2,9,4,1,0,0,0,0,'2024-10-19 11:49:04',1),
(53,2,9,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(54,2,9,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(58,2,10,4,1,0,0,0,0,'2024-10-19 11:49:04',1),
(59,2,10,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(60,2,10,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(64,2,11,4,1,0,0,0,0,'2024-10-19 11:49:04',1),
(65,2,11,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(66,2,11,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(70,2,12,4,1,0,0,0,0,'2024-10-19 11:49:04',1),
(71,2,12,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(72,2,12,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(76,2,13,4,1,0,0,0,0,'2024-10-19 11:49:04',1),
(77,2,13,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(78,2,13,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(82,2,14,4,1,0,0,0,0,'2024-10-19 11:49:04',1),
(83,2,14,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(84,2,14,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(88,2,15,4,1,0,0,0,0,'2024-10-19 11:49:04',1),
(89,2,15,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(90,2,15,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(94,2,16,4,1,0,0,0,0,'2024-10-19 11:49:04',1),
(95,2,16,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(96,2,16,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(100,2,17,4,1,0,0,0,0,'2024-10-19 11:49:04',1),
(101,2,17,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(102,2,17,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(106,2,18,4,1,0,0,0,0,'2024-10-19 11:49:04',1),
(107,2,18,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(108,2,18,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(112,2,19,4,1,0,0,0,0,'2024-10-19 11:49:04',1),
(113,2,19,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(114,2,19,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(118,3,20,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(119,3,20,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(120,3,20,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(124,3,21,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(125,3,21,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(126,3,21,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(130,3,22,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(131,3,22,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(132,3,22,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(136,4,23,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(137,4,23,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(138,4,23,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(142,4,24,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(143,4,24,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(144,4,24,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(148,4,25,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(149,4,25,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(150,4,25,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(154,5,26,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(155,5,26,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(156,5,26,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(160,5,27,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(161,5,27,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(162,5,27,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(166,5,28,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(167,5,28,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(168,5,28,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(172,6,29,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(173,6,29,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(174,6,29,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(178,6,30,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(179,6,30,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(180,6,30,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(184,6,31,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(185,6,31,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(186,6,31,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(190,6,32,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(191,6,32,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(192,6,32,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(196,6,33,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(197,6,33,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(198,6,33,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(202,6,34,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(203,6,34,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(204,6,34,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(208,6,35,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(209,6,35,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(210,6,35,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(214,6,36,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(215,6,36,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(216,6,36,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(220,6,37,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(221,6,37,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(222,6,37,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(226,6,38,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(227,6,38,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(228,6,38,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(232,6,39,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(233,6,39,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(234,6,39,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(238,6,40,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(239,6,40,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(240,6,40,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(244,6,41,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(245,6,41,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(246,6,41,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(250,6,42,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(251,6,42,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(252,6,42,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(256,6,43,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(257,6,43,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(258,6,43,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(262,6,44,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(263,6,44,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(264,6,44,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(268,6,45,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(269,6,45,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(270,6,45,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(274,7,46,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(275,7,46,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(276,7,46,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(280,7,47,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(281,7,47,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(282,7,47,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(286,7,48,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(287,7,48,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(288,7,48,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(292,7,49,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(293,7,49,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(294,7,49,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(298,7,50,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(299,7,50,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(300,7,50,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(304,7,51,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(305,7,51,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(306,7,51,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(310,7,52,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(311,7,52,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(312,7,52,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(316,7,53,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(317,7,53,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(318,7,53,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(964,17,161,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(965,17,161,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(966,17,161,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(976,17,163,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(977,17,163,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(978,17,163,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(982,17,164,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(983,17,164,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(984,17,164,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(1000,17,167,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1001,17,167,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1002,17,167,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(1006,17,168,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1007,17,168,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1008,17,168,3,1,0,1,1,1,'2024-10-19 11:49:04',1),
(1012,18,169,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1013,18,169,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1014,18,169,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1018,18,170,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1019,18,170,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1020,18,170,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1024,18,171,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1025,18,171,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1026,18,171,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1030,18,172,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1031,18,172,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1032,18,172,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1036,18,173,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1037,18,173,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1038,18,173,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1042,18,174,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1043,18,174,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1044,18,174,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1048,18,175,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1049,18,175,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1050,18,175,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1054,18,176,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1055,18,176,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(1056,18,176,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1060,18,177,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1061,18,177,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(1062,18,177,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1078,18,180,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1079,18,180,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(1080,18,180,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1210,25,202,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1211,25,202,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(1212,25,202,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1216,25,203,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1217,25,203,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(1218,25,203,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1276,29,213,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1277,29,213,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1278,29,213,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1282,29,214,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1283,29,214,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1284,29,214,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1288,30,NULL,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1289,30,NULL,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1290,30,NULL,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1294,30,216,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1295,30,216,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1296,30,216,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1300,30,217,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1301,30,217,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1302,30,217,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1348,33,225,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1349,33,225,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1350,33,225,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1354,33,226,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1355,33,226,1,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1356,33,226,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1378,35,230,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1379,35,230,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(1380,35,230,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1384,35,231,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1385,35,231,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(1386,35,231,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1390,35,232,4,1,1,0,0,0,'2024-10-19 11:49:04',1),
(1391,35,232,1,1,0,1,1,1,'2024-10-19 11:49:04',1),
(1392,35,232,3,1,1,1,1,1,'2024-10-19 11:49:04',1),
(1660,2,238,1,1,1,1,1,1,'2024-12-02 10:23:11',1),
(1664,33,402,4,1,1,0,0,0,'2024-10-19 06:49:04',1),
(1665,33,402,1,1,1,1,1,1,'2024-10-19 06:49:04',1),
(1666,33,402,3,1,1,1,1,1,'2024-10-19 06:49:04',1),
(1667,97,NULL,4,1,1,1,1,1,'2025-03-15 09:26:00',1),
(1668,98,NULL,4,1,1,1,1,1,'2025-03-15 09:26:00',1),
(1669,99,NULL,4,1,1,1,1,1,'2025-03-15 09:26:00',1),
(1670,100,NULL,4,1,1,1,1,1,'2025-03-15 09:26:00',1),
(1671,101,NULL,4,1,0,1,1,1,'2025-03-15 09:26:00',1),
(1672,102,NULL,4,1,0,1,1,1,'2025-03-15 09:26:00',1),
(1673,103,NULL,4,1,0,1,1,1,'2025-03-15 09:26:00',1),
(1674,104,NULL,4,1,1,1,1,1,'2025-03-15 09:26:00',1),
(1675,105,NULL,4,1,1,1,1,1,'2025-03-15 09:26:00',1),
(1676,106,NULL,4,1,0,1,1,1,'2025-03-15 09:26:00',1),
(1677,107,NULL,4,1,1,1,1,1,'2025-03-15 09:26:00',1),
(1678,108,NULL,4,1,0,1,1,1,'2025-03-15 09:26:00',1),
(1679,109,NULL,4,1,0,1,1,1,'2025-03-15 09:26:00',1),
(1680,110,NULL,4,1,1,1,1,1,'2025-03-15 09:26:00',1),
(1681,111,NULL,4,1,1,1,1,1,'2025-03-15 09:26:00',1),
(1682,112,NULL,4,1,0,1,1,1,'2025-03-15 09:26:00',1),
(1683,113,NULL,4,1,1,1,1,1,'2025-03-15 09:26:00',1),
(1684,114,NULL,4,1,0,1,1,1,'2025-03-15 09:26:00',1),
(1685,2,238,4,1,0,0,0,0,'2025-03-15 12:48:46',NULL),
(1686,2,238,3,1,0,0,0,0,'2025-03-15 12:52:09',NULL),
(1687,29,403,1,1,1,1,1,1,'2025-03-15 19:57:39',NULL),
(1688,29,404,1,1,1,1,1,1,'2025-03-15 19:57:39',NULL),
(1689,29,403,3,1,0,0,0,0,'2025-03-18 11:35:22',NULL),
(1690,29,404,3,1,0,0,0,0,'2025-03-18 11:35:22',NULL),
(1691,6,405,1,1,1,1,1,1,'2025-03-20 18:16:02',NULL),
(1715,115,NULL,1,1,1,1,1,1,'2025-03-20 19:57:13',NULL),
(1718,116,407,1,1,1,0,0,0,'2025-03-21 17:49:26',NULL),
(1719,116,408,1,1,1,0,0,0,'2025-03-21 17:49:26',NULL),
(1720,116,409,1,1,1,0,0,0,'2025-03-21 17:49:26',NULL),
(1721,116,410,1,1,1,0,0,0,'2025-03-21 17:49:26',NULL),
(1722,116,411,1,1,1,0,0,0,'2025-03-21 17:49:26',NULL),
(1723,6,405,3,1,0,0,0,0,'2025-03-22 17:57:07',NULL),
(1724,115,NULL,3,1,0,0,0,0,'2025-03-22 17:57:07',NULL),
(1725,116,406,3,1,1,0,0,0,'2025-03-22 17:57:07',NULL),
(1726,116,407,3,1,1,0,0,0,'2025-03-22 17:57:07',NULL),
(1727,116,408,3,1,0,0,0,0,'2025-03-22 17:57:07',NULL),
(1728,116,409,3,1,0,0,0,0,'2025-03-22 17:57:07',NULL),
(1729,116,410,3,1,0,0,0,0,'2025-03-22 17:57:07',NULL),
(1730,116,411,3,1,0,0,0,0,'2025-03-22 17:57:07',NULL),
(1731,116,406,1,1,1,0,0,0,'2025-03-22 18:29:44',NULL),
(1732,119,NULL,1,1,1,1,1,1,'2025-03-20 19:57:13',NULL),
(1733,15,NULL,1,1,1,1,1,1,'2025-03-20 19:57:13',NULL);
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions_template`
--

DROP TABLE IF EXISTS `permissions_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions_template` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `can_speak` tinyint(1) DEFAULT 0,
  `can_share_video` tinyint(1) DEFAULT 0,
  `can_share_screen` tinyint(1) DEFAULT 0,
  `description` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions_template`
--

LOCK TABLES `permissions_template` WRITE;
/*!40000 ALTER TABLE `permissions_template` DISABLE KEYS */;
INSERT INTO `permissions_template` (`id`, `name`, `can_speak`, `can_share_video`, `can_share_screen`, `description`) VALUES (1,'Viewer Only',0,0,0,'Can join the meeting but cannot speak or share screen/video'),
(2,'Presenter',1,0,1,'Can speak and share screen'),
(3,'Full Access',1,1,1,'Can speak, share video, and share screen'),
(4,'Audio Only',1,0,0,'Can speak but cannot share video or screen'),
(5,'Moderator',1,1,1,'Has full access and can mute others'),
(6,'Video Only',0,1,0,'Can share video but cannot speak or share screen');
/*!40000 ALTER TABLE `permissions_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phone_call_logs`
--

DROP TABLE IF EXISTS `phone_call_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `phone_call_logs` (
  `call_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `next_follow_up_date` date NOT NULL,
  `call_type` enum('Incoming','Outgoing','Missed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `session_id` int(11) DEFAULT 1,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phone_call_logs`
--

LOCK TABLES `phone_call_logs` WRITE;
/*!40000 ALTER TABLE `phone_call_logs` DISABLE KEYS */;
INSERT INTO `phone_call_logs` (`call_id`, `name`, `phone`, `date`, `next_follow_up_date`, `call_type`, `created_at`, `updated_at`, `session_id`, `school_id`) VALUES (11,'Ali Khan','03001234567','2025-02-15','2025-02-20','Outgoing','2025-02-15 14:01:51','2025-03-13 17:51:24',1,1),
(12,'Ayesha Ahmed','03211234567','2025-02-14','2025-02-18','Outgoing','2025-02-15 14:01:51','2025-03-13 17:51:24',1,1),
(13,'Usman Raza','03151234567','2025-02-13','2025-02-22','Missed','2025-02-15 14:01:51','2025-03-13 17:51:24',1,1),
(14,'Sana Malik','03451234567','2025-02-12','2025-02-25','Incoming','2025-02-15 14:01:51','2025-03-13 17:51:24',1,1);
/*!40000 ALTER TABLE `phone_call_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purpose`
--

DROP TABLE IF EXISTS `purpose`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `purpose` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purpose`
--

LOCK TABLES `purpose` WRITE;
/*!40000 ALTER TABLE `purpose` DISABLE KEYS */;
INSERT INTO `purpose` (`id`, `name`, `description`, `created_at`, `updated_at`, `school_id`) VALUES (1,'Admission Inquiry','Inquiries related to admissions','2025-02-15 15:22:50','2025-03-13 17:51:24',1),
(2,'Fee Payment','Purpose related to fee payments','2025-02-15 15:22:50','2025-03-13 17:51:24',1),
(3,'General Inquiry','General inquiries about the institution','2025-02-15 15:22:50','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `purpose` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reference`
--

DROP TABLE IF EXISTS `reference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reference` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reference`
--

LOCK TABLES `reference` WRITE;
/*!40000 ALTER TABLE `reference` DISABLE KEYS */;
INSERT INTO `reference` (`id`, `name`, `description`, `created_at`, `updated_at`, `school_id`) VALUES (1,'Friend','Referred by a friend','2025-02-15 15:22:50','2025-03-13 17:51:24',1),
(2,'Alumni','Referred by an alumni','2025-02-15 15:22:50','2025-03-13 17:51:24',1),
(3,'Advertisement','Referred by an advertisement','2025-02-15 15:22:50','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `reference` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `order_by` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `feature_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` (`id`, `name`, `icon`, `description`, `link`, `active`, `order_by`, `created_at`, `feature_id`) VALUES (1,'Student Report','fa fa-file-text-o',NULL,'reporting/student_report',1,0,'2025-03-22 17:02:22',406),
(2,'Class & Section Report','fa fa-file-text-o',NULL,'reporting/student_classsection',1,0,'2025-03-22 17:02:22',406),
(3,'Staff Payroll','fa fa-file-text-o',NULL,'reporting/payroll',1,0,'2025-03-22 17:02:22',410),
(4,'Student History','fa fa-file-text-o',NULL,'',0,0,'2025-03-22 17:02:22',406),
(5,'Student Login Credential','fa fa-file-text-o',NULL,'reporting/logindetails',1,0,'2025-03-22 17:02:22',406),
(6,'Parent Login Credential','fa fa-file-text-o',NULL,'',0,0,'2025-03-22 17:02:22',406),
(7,'Class Subject Report','fa fa-file-text-o',NULL,'reporting/student_classsubject',1,0,'2025-03-22 17:02:22',406),
(8,'Admission Report','fa fa-file-text-o',NULL,'',0,0,'2025-03-22 17:02:22',406),
(9,'Sibling Report','fa fa-file-text-o',NULL,'',0,0,'2025-03-22 17:02:22',406),
(10,'Student Profile','fa fa-file-text-o',NULL,NULL,0,0,'2025-03-22 17:02:22',406),
(11,'Student Gender Ratio Report','fa fa-file-text-o',NULL,'',0,0,'2025-03-22 17:02:22',406),
(12,'Student Teacher Ratio Report','fa fa-file-text-o',NULL,'',0,0,'2025-03-22 17:02:22',406),
(13,'Online Admission Report','fa fa-file-text-o',NULL,'',0,0,'2025-03-22 17:02:22',406),
(14,'Attendance Report','fa fa-file-text-o',NULL,'reporting/attendance_report',0,0,'2025-03-22 17:02:22',406),
(15,'Attendance Report','fa fa-file-text-o',NULL,'reporting/attendance_report',1,0,'2025-03-22 17:02:22',407),
(16,'Student Attendance Type Report','fa fa-file-text-o',NULL,'reporting/attendance_type_report',1,0,'2025-03-22 17:02:22',407),
(17,'Daily Attendance Report','fa fa-file-text-o',NULL,'reporting/daily_attendance_report',1,0,'2025-03-22 17:02:22',407),
(18,'Staff Attendance Report','fa fa-file-text-o',NULL,'reporting/staff_attendance_report',1,0,'2025-03-22 17:02:22',407),
(21,'Rank Report','fa fa-file-text-o',NULL,'reporting/rank',1,0,'2025-03-22 17:02:22',408),
(22,'All Classes Report','fa fa-file-text-o',NULL,'reporting/onlineclasses',1,0,'2025-03-22 17:02:22',409),
(23,'Subject Lesson Plan Report','fa fa-file-text-o',NULL,'',0,0,'2025-03-22 17:02:22',409),
(24,'Staff Report','fa fa-file-text-o',NULL,'reporting/staff_report',1,0,'2025-03-22 17:02:22',410),
(25,'All Users','fa fa-file-text-o',NULL,'',1,0,'2025-03-22 17:02:22',411),
(26,'Students','fa fa-file-text-o',NULL,'',1,0,'2025-03-22 17:02:22',411),
(27,'Staff','fa fa-file-text-o',NULL,'',1,0,'2025-03-22 17:02:22',411),
(28,'Parents','fa fa-file-text-o',NULL,'',0,0,'2025-03-22 17:02:22',411);
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_permissions`
--

DROP TABLE IF EXISTS `reports_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports_permissions` (
  `id` int(11) NOT NULL,
  `report_id` int(11) DEFAULT NULL,
  `feature_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `can_view` tinyint(1) DEFAULT 0,
  `can_add` tinyint(1) DEFAULT 0,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_permissions`
--

LOCK TABLES `reports_permissions` WRITE;
/*!40000 ALTER TABLE `reports_permissions` DISABLE KEYS */;
INSERT INTO `reports_permissions` (`id`, `report_id`, `feature_id`, `role_id`, `is_active`, `can_view`, `can_add`, `can_edit`, `can_delete`, `created_at`) VALUES (1,15,407,1,1,1,0,0,0,'2025-03-22 17:14:10'),
(2,16,407,1,1,1,0,0,0,'2025-03-22 17:14:10'),
(3,17,407,1,1,1,0,0,0,'2025-03-22 17:14:10'),
(4,18,407,1,1,1,0,0,0,'2025-03-22 17:14:10'),
(7,21,408,1,1,1,0,0,0,'2025-03-22 17:14:10'),
(8,22,409,1,1,1,0,0,0,'2025-03-22 17:14:10'),
(9,23,409,1,1,1,0,0,0,'2025-03-22 17:14:10'),
(10,24,410,1,1,1,0,0,0,'2025-03-22 17:14:10'),
(11,25,411,1,1,1,0,0,0,'2025-03-22 17:14:10'),
(12,26,411,1,1,1,0,0,0,'2025-03-22 17:14:10'),
(13,27,411,1,1,1,0,0,0,'2025-03-22 17:14:10'),
(14,28,411,1,1,1,0,0,0,'2025-03-22 17:14:10'),
(15,1,406,1,1,1,0,0,0,'2025-03-22 18:03:49'),
(16,2,406,1,1,1,0,0,0,'2025-03-22 18:03:49'),
(17,3,406,1,1,0,0,0,0,'2025-03-22 18:03:49'),
(18,4,406,1,1,0,0,0,0,'2025-03-22 18:03:49'),
(19,5,406,1,1,1,0,0,0,'2025-03-22 18:03:49'),
(20,6,406,1,1,1,0,0,0,'2025-03-22 18:03:49'),
(21,7,406,1,1,1,0,0,0,'2025-03-22 18:03:49'),
(22,8,406,1,1,0,0,0,0,'2025-03-22 18:03:49'),
(23,9,406,1,1,0,0,0,0,'2025-03-22 18:03:49'),
(24,10,406,1,1,1,0,0,0,'2025-03-22 18:03:49'),
(25,11,406,1,1,0,0,0,0,'2025-03-22 18:03:49'),
(26,12,406,1,1,0,0,0,0,'2025-03-22 18:03:49'),
(27,13,406,1,1,0,0,0,0,'2025-03-22 18:03:49'),
(28,14,406,1,1,1,0,0,0,'2025-03-22 18:03:49'),
(29,3,410,1,1,1,0,0,0,'2025-10-10 07:13:29');
/*!40000 ALTER TABLE `reports_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `name`, `description`, `active`, `created_at`, `school_id`) VALUES (1,'Super Admin','Manages roles and all other administrative tasks',1,'2024-10-19 07:54:30',1),
(3,'Teacher','Manages students, grades, and course content',1,'2024-10-19 07:54:30',1),
(4,'Student','Manage subjects and plans',1,'2024-10-19 07:54:30',1);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school`
--

DROP TABLE IF EXISTS `school`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `school` (
  `school_id` int(11) NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `address` text NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `date_of_establishment` date NOT NULL,
  `principal_name` varchar(100) DEFAULT NULL,
  `school_type` enum('Public','Private','Charter') NOT NULL,
  `number_of_students` int(11) DEFAULT 0,
  `accreditation` varchar(255) DEFAULT NULL,
  `motto` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school`
--

LOCK TABLES `school` WRITE;
/*!40000 ALTER TABLE `school` DISABLE KEYS */;
INSERT INTO `school` (`school_id`, `school_name`, `email`, `phone`, `address`, `url`, `date_of_establishment`, `principal_name`, `school_type`, `number_of_students`, `accreditation`, `motto`, `logo`, `active`, `created_at`, `updated_at`) VALUES (1,'Online Quran Academy','super.school@gmail.com','00000000000000','Islamabad Pakistan','https://www.als.com','2023-01-01','Noreen','Public',200,'Not Verified','Empowering Minds, Inspiring Futures','images/school/American Lycettuf DNK School Systems.png',1,'2025-03-22 16:08:18','2025-10-10 16:40:42'),
(2,'CISD','kore@mailinator.com','+1 (979) 896-1126','Saepe ad consequuntu','https://www.cubax.me.uk','1979-12-06','Quo velit soluta exc','Private',34,'Voluptates aut debit','Laboris veritatis vo','images/school/Kaden Ferguson.jpg',1,'2025-03-22 16:08:18','2025-03-22 16:08:18'),
(3,'National Excellence Institute','lgddgd@gmail.com','l','l','https://mail.google.com/mail/u/0/#inbox','2025-03-11','l','',1212,'l','l',NULL,1,'2025-03-22 16:08:18','2025-03-22 16:08:18');
/*!40000 ALTER TABLE `school` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_permissions`
--

DROP TABLE IF EXISTS `school_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 1,
  `can_add` tinyint(1) DEFAULT 0,
  `can_view` tinyint(1) DEFAULT 0,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_permissions`
--

LOCK TABLES `school_permissions` WRITE;
/*!40000 ALTER TABLE `school_permissions` DISABLE KEYS */;
INSERT INTO `school_permissions` (`id`, `role_id`, `school_id`, `active`, `can_add`, `can_view`, `can_edit`, `can_delete`, `created_at`, `updated_at`) VALUES (1,1,1,1,1,1,1,1,'2025-03-12 18:04:12','2025-03-15 19:28:28'),
(2,1,1,1,0,0,0,0,'2025-03-12 18:04:12','2025-03-13 17:51:24'),
(3,1,2,1,1,1,1,1,'2025-03-15 14:41:36','2025-03-15 19:28:29');
/*!40000 ALTER TABLE `school_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `section_name` varchar(10) NOT NULL,
  `description` text DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` (`id`, `section_name`, `description`, `school_id`) VALUES (1,'Beginners','This is section for beginners of Class 10 - 11 PM Daily',2);
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `session` (
  `session_id` int(11) NOT NULL,
  `session_name` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `view_permission` tinyint(1) DEFAULT 0,
  `edit_permission` tinyint(1) DEFAULT 0,
  `update_permission` tinyint(1) DEFAULT 0,
  `delete_permission` tinyint(1) DEFAULT 0,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session`
--

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
INSERT INTO `session` (`session_id`, `session_name`, `icon`, `date`, `is_active`, `view_permission`, `edit_permission`, `update_permission`, `delete_permission`, `school_id`) VALUES (1,'2024-2025','fa  fa-cogs','2024-10-21',1,1,1,1,1,1),
(3,'2025-2026','fa  fa-cogs','2025-01-01',0,1,1,1,1,1);
/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_events`
--

DROP TABLE IF EXISTS `session_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `session_events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `event_color` varchar(255) NOT NULL,
  `event_type` enum('Public','Private','All','Super Admin','Protected') NOT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_events`
--

LOCK TABLES `session_events` WRITE;
/*!40000 ALTER TABLE `session_events` DISABLE KEYS */;
INSERT INTO `session_events` (`id`, `title`, `description`, `start_datetime`, `end_datetime`, `location`, `event_color`, `event_type`, `session_id`, `created_at`, `updated_at`, `school_id`) VALUES (1,'Event Title','Event Description','2025-01-23 09:00:00','2025-01-23 11:00:00','Location Name','#FF5733','Public',1,'2025-01-21 17:03:34','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `session_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `setting_permissions`
--

DROP TABLE IF EXISTS `setting_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `setting_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `setting_id` int(11) NOT NULL,
  `can_add` int(11) NOT NULL DEFAULT 0,
  `can_view` tinyint(1) DEFAULT 0,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `setting_permissions`
--

LOCK TABLES `setting_permissions` WRITE;
/*!40000 ALTER TABLE `setting_permissions` DISABLE KEYS */;
INSERT INTO `setting_permissions` (`id`, `role_id`, `setting_id`, `can_add`, `can_view`, `can_edit`, `can_delete`, `created_at`, `updated_at`, `school_id`) VALUES (1,1,1,1,0,1,1,'2025-03-11 18:01:41','2025-10-07 08:44:18',1),
(2,1,7,1,1,1,1,'2025-03-11 18:01:41','2025-03-19 12:27:41',1),
(3,1,8,1,1,1,1,'2025-03-11 18:04:04','2025-03-19 12:27:42',1),
(7,1,9,1,0,1,1,'2025-03-13 15:59:46','2025-10-07 08:44:21',1),
(8,4,1,0,0,0,0,'2025-03-15 09:55:42','2025-03-15 09:55:42',NULL),
(9,4,7,0,0,0,0,'2025-03-15 09:55:42','2025-03-15 09:55:42',NULL),
(10,4,8,1,1,1,1,'2025-03-15 09:55:42','2025-03-15 09:56:02',NULL),
(11,4,9,0,0,0,0,'2025-03-15 09:55:42','2025-03-15 09:55:42',NULL),
(12,1,10,1,1,1,1,'2025-03-13 15:59:46','2025-10-07 08:44:21',1),
(13,1,11,1,1,1,1,'2025-03-13 15:59:46','2025-10-07 08:44:21',1);
/*!40000 ALTER TABLE `setting_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smtp_settings`
--

DROP TABLE IF EXISTS `smtp_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `smtp_settings` (
  `id` int(11) NOT NULL,
  `email_engine` varchar(255) NOT NULL,
  `smtp_server` varchar(255) NOT NULL,
  `smtp_port` int(11) NOT NULL,
  `smtp_username` varchar(255) NOT NULL,
  `smtp_password` varchar(255) NOT NULL,
  `smtp_security` varchar(10) NOT NULL,
  `smtp_auth` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` tinyint(1) DEFAULT 1,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smtp_settings`
--

LOCK TABLES `smtp_settings` WRITE;
/*!40000 ALTER TABLE `smtp_settings` DISABLE KEYS */;
INSERT INTO `smtp_settings` (`id`, `email_engine`, `smtp_server`, `smtp_port`, `smtp_username`, `smtp_password`, `smtp_security`, `smtp_auth`, `created_at`, `updated_at`, `active`, `school_id`) VALUES (1,'Gmail','smtp.gmail.com',587,'quantaaffix786@gmail.com','your_encrypted_password','TLS',1,'2024-10-18 16:56:51','2025-03-13 17:51:24',0,1);
/*!40000 ALTER TABLE `smtp_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_links`
--

DROP TABLE IF EXISTS `social_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_links` (
  `social_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `x_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_links`
--

LOCK TABLES `social_links` WRITE;
/*!40000 ALTER TABLE `social_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `social_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `source`
--

DROP TABLE IF EXISTS `source`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `source` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `source`
--

LOCK TABLES `source` WRITE;
/*!40000 ALTER TABLE `source` DISABLE KEYS */;
INSERT INTO `source` (`id`, `name`, `description`, `created_at`, `updated_at`, `school_id`) VALUES (1,'Website','Inquiries from the official website','2025-02-15 15:22:50','2025-03-13 17:51:24',1),
(2,'Social Media','Inquiries from social media platforms','2025-02-15 15:22:50','2025-03-13 17:51:24',1),
(3,'Referral','Inquiries from referrals','2025-02-15 15:22:50','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `source` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `designation_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `gender` enum('','Male','Female','Other') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `doj` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `emergency_phone` varchar(50) DEFAULT NULL,
  `religion` varchar(200) DEFAULT NULL,
  `caste` varchar(200) DEFAULT NULL,
  `blood_group` int(11) DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `marital_status` int(11) DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `pan` varchar(255) DEFAULT NULL,
  `qualification` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `basic_salary` double DEFAULT 0,
  `note` text DEFAULT NULL,
  `disable_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
INSERT INTO `staff` (`staff_id`, `role_id`, `designation_id`, `department_id`, `first_name`, `last_name`, `father_name`, `mother_name`, `email`, `gender`, `dob`, `doj`, `phone`, `emergency_phone`, `religion`, `caste`, `blood_group`, `medical_history`, `marital_status`, `current_address`, `permanent_address`, `pan`, `qualification`, `experience`, `basic_salary`, `note`, `disable_id`, `created_at`, `updated_at`, `school_id`) VALUES (16,3,1,1,'Sana','Khalid','Khalid Mahmood','','sana.khalid@school.com','Female','1990-05-15','2020-01-15','03201234567',NULL,'Islam',NULL,1,NULL,1,'House 25, Block A, DHA, Lahore','House 12, Model Town, Lahore',NULL,'Masters in Mathematics','5 years teaching experience',0,NULL,NULL,'2025-10-10 12:27:32','2025-10-10 14:51:20',1),
(17,3,1,1,'Kamran','Akram','Akram Ali',NULL,'kamran.akram@school.com','Male','1988-08-22','2019-03-10','03211234567',NULL,'Islam',NULL,2,NULL,2,'Flat 10, Johar Town, Lahore','Street 5, Satellite Town, Lahore',NULL,'Masters in Mathematics, B.Ed','7 years teaching experience',0,NULL,NULL,'2025-10-10 12:27:32','2025-10-10 12:37:29',1),
(18,3,1,1,'Lubna','Zaheer','Zaheer Ahmed',NULL,'lubna.zaheer@school.com','Female','1992-03-18','2021-08-01','03221234567',NULL,'Islam',NULL,1,NULL,1,'House 40, Garden Town, Lahore','House 18, Cantt, Lahore',NULL,'Masters in English Literature','4 years teaching experience',0,NULL,NULL,'2025-10-10 12:27:32','2025-10-10 12:27:32',1),
(19,3,1,1,'Asad','Rauf','Abdul Rauf',NULL,'asad.rauf@school.com','Male','1985-11-30','2017-02-20','03231234567',NULL,'Islam',NULL,3,NULL,2,'Apartment 5B, Gulberg, Lahore','House 22, Faisal Town, Lahore',NULL,'Masters in English Language, TEFL Certified','8 years teaching experience',0,NULL,NULL,'2025-10-10 12:27:32','2025-10-10 12:37:34',1),
(20,3,1,1,'Rabia','Siddique','Siddique Hussain',NULL,'rabia.siddique@school.com','Female','1991-07-25','2020-09-01','03241234567',NULL,'Islam',NULL,2,NULL,1,'House 15, Wapda Town, Lahore','Street 8, Township, Lahore',NULL,'Masters in Physics','5 years teaching experience',0,NULL,NULL,'2025-10-10 12:27:32','2025-10-10 12:37:43',1),
(21,3,1,1,'Farhan','Bashir','Bashir Ahmad',NULL,'farhan.bashir@school.com','Male','1989-12-10','2018-07-15','03251234567',NULL,'Islam',NULL,1,NULL,2,'House 30, Iqbal Town, Lahore','House 45, Shahdara, Lahore',NULL,'Masters in Chemistry, B.Ed','7 years teaching experience',0,NULL,NULL,'2025-10-10 12:27:32','2025-10-10 12:37:48',1),
(22,3,1,1,'Adeel','Haider','Haider Ali',NULL,'adeel.haider@school.com','Male','1993-03-30','2021-07-01','03341234567',NULL,'Islam',NULL,2,NULL,1,'House 9, Harbanspura, Lahore','House 4, Badami Bagh, Lahore',NULL,'Masters in Biology','4 years teaching experience',0,NULL,NULL,'2025-10-10 12:27:32','2025-10-10 12:37:48',1),
(23,3,1,1,'Ayesha','Noor','Noor Mohammad',NULL,'ayesha.noor@school.com','Female','1993-02-14','2021-01-10','03261234567',NULL,'Islam',NULL,1,NULL,1,'Flat 3, Bahria Town, Lahore','House 20, Allama Iqbal Town, Lahore',NULL,'Masters in Computer Science','4 years teaching experience',0,NULL,NULL,'2025-10-10 12:27:32','2025-10-10 12:27:32',1),
(24,3,1,1,'Hamza','Rizwan','Rizwan ul Haq',NULL,'hamza.rizwan@school.com','Male','1987-09-08','2016-08-20','03271234567',NULL,'Islam',NULL,3,NULL,2,'House 12, Phase 5, DHA, Lahore','House 8, Valencia Town, Lahore',NULL,'Masters in IT, Software Engineering','9 years teaching experience',0,NULL,NULL,'2025-10-10 12:27:32','2025-10-10 12:37:34',1),
(25,3,1,1,'Hafiz','Abdullah','Abdullah Shah',NULL,'hafiz.abdullah@school.com','Male','1986-04-05','2015-01-05','03281234567',NULL,'Islam',NULL,2,NULL,2,'House 5, Muslim Town, Lahore','House 17, Green Town, Lahore',NULL,'Masters in Islamic Studies, Hafiz-e-Quran','10 years teaching experience',0,NULL,NULL,'2025-10-10 12:27:32','2025-10-10 12:37:34',1),
(26,3,1,1,'Saima','Tariq','Tariq Hussain',NULL,'saima.tariq@school.com','Female','1994-06-20','2022-03-15','03291234567',NULL,'Islam',NULL,1,NULL,1,'Apartment 7A, Cavalry Ground, Lahore','House 25, Sabzazar, Lahore',NULL,'BFA, Masters in Fine Arts','3 years teaching experience',0,NULL,NULL,'2025-10-10 12:27:32','2025-10-10 12:37:48',1),
(27,3,1,1,'Mahjabeen','Ali','Ali Ahmed',NULL,'mahjabeen.ali@school.com','Female','1995-04-14','2023-01-15','03351234567',NULL,'Islam',NULL,1,NULL,1,'House 55, Gulshan Ravi, Lahore','House 28, Green Fort, Lahore',NULL,'Masters in Music','2 years teaching experience',0,NULL,NULL,'2025-10-10 12:27:32','2025-10-10 12:37:48',1),
(28,3,1,1,'Shahzad','Mirza','Mirza Baig',NULL,'shahzad.mirza@school.com','Male','1990-10-12','2019-06-01','03301234567',NULL,'Islam',NULL,2,NULL,2,'House 33, Canal Road, Lahore','House 9, Ravi Road, Lahore',NULL,'Masters in Physical Education, Sports Diploma','6 years coaching experience',0,NULL,NULL,'2025-10-10 12:27:32','2025-10-10 12:37:48',1),
(29,3,1,1,'Nadia','Masood','Masood Ahmed',NULL,'nadia.masood@school.com','Female','1988-01-18','2018-04-10','03311234567',NULL,'Islam',NULL,1,NULL,2,'House 22, Model Town Ext, Lahore','House 14, Shalimar Town, Lahore',NULL,'Masters in Urdu Literature','7 years teaching experience',0,NULL,NULL,'2025-10-10 12:27:32','2025-10-10 12:27:32',1),
(30,3,1,1,'Bilal','Qureshi','Qureshi Sahib','NA','bilal.qureshi@school.com','Male','1992-11-25','2020-02-15','03321234567',NULL,'Islam',NULL,3,NULL,1,'Flat 15, Johar Town, Lahore','House 30, Wahdat Colony, Lahore',NULL,'Masters in History & Political Science','5 years teaching experience',30000,NULL,NULL,'2025-10-10 12:27:32','2025-10-10 15:03:11',1),
(31,3,1,1,'Zoya','Anwar','Anwar Hussain','NA','zoya.anwar@school.com','Female','1991-08-08','2019-09-01','03331234567',NULL,'Islam',NULL,1,NULL,1,'House 18, Thokar Niaz Baig, Lahore','House 7, Kot Lakhpat, Lahore',NULL,'Masters in Library Science','6 years library management',3000,NULL,NULL,'2025-10-10 12:27:32','2025-10-10 15:01:12',1);
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_attendance`
--

DROP TABLE IF EXISTS `staff_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_attendance` (
  `attendance_id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `is_student` int(11) DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `attendance_type` enum('present','late','absent','half_day','holiday') NOT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_attendance`
--

LOCK TABLES `staff_attendance` WRITE;
/*!40000 ALTER TABLE `staff_attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff_attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_payroll`
--

DROP TABLE IF EXISTS `staff_payroll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_payroll` (
  `payroll_id` int(11) NOT NULL,
  `staff_payroll_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `month` varchar(20) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `basic_salary` decimal(10,2) DEFAULT NULL,
  `total_allowance` decimal(10,2) DEFAULT NULL,
  `total_deduction` decimal(10,2) DEFAULT NULL,
  `gross_salary` decimal(10,2) DEFAULT NULL,
  `tax` decimal(10,2) DEFAULT NULL,
  `net_salary` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_payroll`
--

LOCK TABLES `staff_payroll` WRITE;
/*!40000 ALTER TABLE `staff_payroll` DISABLE KEYS */;
INSERT INTO `staff_payroll` (`payroll_id`, `staff_payroll_id`, `staff_id`, `month`, `year`, `basic_salary`, `total_allowance`, `total_deduction`, `gross_salary`, `tax`, `net_salary`, `status`, `session_id`, `created_at`, `updated_at`, `school_id`) VALUES (1,3,30,'10',2025,30000.00,0.00,0.00,30000.00,NULL,30000.00,'Generated',1,'2025-10-10 10:12:20','2025-10-10 13:12:20',1),
(2,1,16,'10',2025,0.00,0.00,0.00,0.00,NULL,0.00,'Generated',1,'2025-10-10 10:12:20','2025-10-10 13:12:20',1),
(3,2,31,'10',2025,3000.00,0.00,0.00,3000.00,NULL,3000.00,'Generated',1,'2025-10-10 10:12:20','2025-10-10 13:12:20',1),
(4,4,22,'10',2025,0.00,0.00,0.00,0.00,NULL,0.00,'Generated',1,'2025-10-10 10:13:02','2025-10-10 13:13:02',1),
(5,5,19,'10',2025,0.00,0.00,0.00,0.00,NULL,0.00,'Generated',1,'2025-10-10 10:13:02','2025-10-10 13:13:02',1),
(6,6,23,'10',2025,0.00,0.00,0.00,0.00,NULL,0.00,'Generated',1,'2025-10-10 10:13:02','2025-10-10 13:13:02',1),
(7,7,21,'10',2025,0.00,0.00,0.00,0.00,NULL,0.00,'Generated',1,'2025-10-10 10:13:02','2025-10-10 13:13:02',1),
(8,8,25,'10',2025,0.00,0.00,0.00,0.00,NULL,0.00,'Generated',1,'2025-10-10 10:13:02','2025-10-10 13:13:02',1),
(9,9,24,'10',2025,0.00,0.00,0.00,0.00,NULL,0.00,'Generated',1,'2025-10-10 10:13:02','2025-10-10 13:13:02',1),
(10,10,17,'10',2025,0.00,0.00,0.00,0.00,NULL,0.00,'Generated',1,'2025-10-10 10:13:02','2025-10-10 13:13:02',1),
(11,11,18,'10',2025,0.00,0.00,0.00,0.00,NULL,0.00,'Generated',1,'2025-10-10 10:13:02','2025-10-10 13:13:02',1),
(12,12,27,'10',2025,0.00,0.00,0.00,0.00,NULL,0.00,'Generated',1,'2025-10-10 10:13:02','2025-10-10 13:13:02',1),
(13,13,29,'10',2025,0.00,0.00,0.00,0.00,NULL,0.00,'Generated',1,'2025-10-10 10:13:02','2025-10-10 13:13:02',1),
(14,14,20,'10',2025,0.00,0.00,0.00,0.00,NULL,0.00,'Generated',1,'2025-10-10 10:13:02','2025-10-10 13:13:02',1),
(15,15,26,'10',2025,0.00,0.00,0.00,0.00,NULL,0.00,'Generated',1,'2025-10-10 10:13:02','2025-10-10 13:13:02',1),
(16,16,28,'10',2025,0.00,0.00,0.00,0.00,NULL,0.00,'Generated',1,'2025-10-10 10:13:02','2025-10-10 13:13:02',1);
/*!40000 ALTER TABLE `staff_payroll` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_categories`
--

DROP TABLE IF EXISTS `student_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_categories`
--

LOCK TABLES `student_categories` WRITE;
/*!40000 ALTER TABLE `student_categories` DISABLE KEYS */;
INSERT INTO `student_categories` (`id`, `name`, `description`, `school_id`) VALUES (1,'General','For General Students!',1),
(2,'OBC','For Other Backward Class',1),
(3,'Special','For Special Students',1),
(4,'Special Chellenged','For Special Chellenging students',1);
/*!40000 ALTER TABLE `student_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_documents`
--

DROP TABLE IF EXISTS `student_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_documents` (
  `document_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `document_title` varchar(100) NOT NULL,
  `document_path` varchar(255) NOT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_documents`
--

LOCK TABLES `student_documents` WRITE;
/*!40000 ALTER TABLE `student_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_incidents`
--

DROP TABLE IF EXISTS `student_incidents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_incidents` (
  `std_incident_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_incidents`
--

LOCK TABLES `student_incidents` WRITE;
/*!40000 ALTER TABLE `student_incidents` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_incidents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_misc`
--

DROP TABLE IF EXISTS `student_misc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_misc` (
  `misc_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `bank_account_number` varchar(30) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `national_id_number` varchar(20) DEFAULT NULL,
  `local_id_number` varchar(20) DEFAULT NULL,
  `rte` enum('Yes','No') DEFAULT NULL,
  `previous_school_details` text DEFAULT NULL,
  `additional_note` text DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_misc`
--

LOCK TABLES `student_misc` WRITE;
/*!40000 ALTER TABLE `student_misc` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_misc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `admission_no` varchar(20) NOT NULL,
  `roll_no` varchar(20) DEFAULT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `dob` date NOT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `caste` varchar(50) DEFAULT NULL,
  `mobile_number` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `house_id` int(11) DEFAULT NULL,
  `height` varchar(10) DEFAULT NULL,
  `weight` varchar(10) DEFAULT NULL,
  `measurement_date` date DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `blood_group_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `disable_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` (`student_id`, `admission_no`, `roll_no`, `class_id`, `section_id`, `first_name`, `last_name`, `gender`, `dob`, `religion`, `caste`, `mobile_number`, `email`, `admission_date`, `photo_path`, `house_id`, `height`, `weight`, `measurement_date`, `medical_history`, `blood_group_id`, `category_id`, `session_id`, `disable_id`, `created_at`, `updated_at`, `school_id`) VALUES (1,'ADM2025001','001',1,1,'Ahmed','Khan','Male','2018-03-15','Islam',NULL,'03001234567','ahmed.khan@email.com','2025-01-10',NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:28',1),
(2,'ADM2025002','002',1,1,'Fatima','Ali','Female','2018-05-20','Islam',NULL,'03011234567','fatima.ali@email.com','2025-01-10',NULL,NULL,NULL,NULL,NULL,NULL,2,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:24:25',1),
(3,'ADM2025003','003',1,1,'Sara','Ahmed','Female','2018-07-12','Islam',NULL,'03021234567','sara.ahmed@email.com','2025-01-10',NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:24:25',1),
(4,'ADM2025004','004',1,1,'Ali','Hassan','Male','2018-04-25','Islam',NULL,'03031234567','ali.hassan@email.com','2025-01-10',NULL,NULL,NULL,NULL,NULL,NULL,3,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:24:25',1),
(5,'ADM2025005','001',1,1,'Zainab','Malik','Female','2018-06-10','Islam',NULL,'03041234567','zainab.malik@email.com','2025-01-12',NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:35',1),
(6,'ADM2025006','002',1,1,'Usman','Raza','Male','2018-08-18','Islam',NULL,'03051234567','usman.raza@email.com','2025-01-12',NULL,NULL,NULL,NULL,NULL,NULL,2,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:35',1),
(7,'ADM2025007','001',1,1,'Bilal','Sheikh','Male','2017-02-14','Islam',NULL,'03061234567','bilal.sheikh@email.com','2024-08-15',NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:28',1),
(8,'ADM2025008','002',1,1,'Ayesha','Iqbal','Female','2017-03-22','Islam',NULL,'03071234567','ayesha.iqbal@email.com','2024-08-15',NULL,NULL,NULL,NULL,NULL,NULL,3,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:28',1),
(9,'ADM2025009','003',1,1,'Hamza','Yousaf','Male','2017-05-30','Islam',NULL,'03081234567','hamza.yousaf@email.com','2024-08-15',NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:28',1),
(10,'ADM2025010','004',1,1,'Maryam','Hussain','Female','2017-01-17','Islam',NULL,'03091234567','maryam.hussain@email.com','2024-08-15',NULL,NULL,NULL,NULL,NULL,NULL,2,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:28',1),
(11,'ADM2025011','001',1,1,'Hassan','Farooq','Male','2016-04-08','Islam',NULL,'03101234567','hassan.farooq@email.com','2023-09-01',NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:28',1),
(12,'ADM2025012','002',1,1,'Amna','Saleem','Female','2016-06-19','Islam',NULL,'03111234567','amna.saleem@email.com','2023-09-01',NULL,NULL,NULL,NULL,NULL,NULL,2,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:28',1),
(13,'ADM2025013','003',1,1,'Faisal','Nawaz','Male','2016-08-25','Islam',NULL,'03121234567','faisal.nawaz@email.com','2023-09-01',NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:28',1),
(14,'ADM2025014','001',1,1,'Ibrahim','Tariq','Male','2015-03-12','Islam',NULL,'03131234567','ibrahim.tariq@email.com','2022-08-20',NULL,NULL,NULL,NULL,NULL,NULL,3,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:28',1),
(15,'ADM2025015','002',1,1,'Hira','Abbasi','Female','2015-07-28','Islam',NULL,'03141234567','hira.abbasi@email.com','2022-08-20',NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:28',1),
(16,'ADM2025016','003',1,1,'Imran','Jamil','Male','2015-09-05','Islam',NULL,'03151234567','imran.jamil@email.com','2022-08-20',NULL,NULL,NULL,NULL,NULL,NULL,2,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:28',1),
(17,'ADM2025017','004',1,1,'Khadija','Saeed','Female','2015-11-14','Islam',NULL,'03161234567','khadija.saeed@email.com','2022-08-20',NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:28',1),
(18,'ADM2025018','005',1,1,'Omar','Shakeel','Male','2015-12-22','Islam',NULL,'03171234567','omar.shakeel@email.com','2022-08-20',NULL,NULL,NULL,NULL,NULL,NULL,3,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:28',1),
(19,'ADM2025019','005',1,1,'Nida','Asif','Female','2016-10-05','Islam',NULL,'03181234567','nida.asif@email.com','2023-09-01',NULL,NULL,NULL,NULL,NULL,NULL,2,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:28',1),
(20,'ADM2025020','006',1,1,'Yasir','Butt','Male','2017-11-09','Islam',NULL,'03191234567','yasir.butt@email.com','2024-08-15',NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,NULL,'2025-10-10 07:24:25','2025-10-10 07:33:28',1);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject_groups`
--

DROP TABLE IF EXISTS `subject_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject_groups` (
  `subject_group_id` int(11) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`section_ids`)),
  `subject_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`subject_ids`)),
  `session_id` int(11) NOT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject_groups`
--

LOCK TABLES `subject_groups` WRITE;
/*!40000 ALTER TABLE `subject_groups` DISABLE KEYS */;
INSERT INTO `subject_groups` (`subject_group_id`, `group_name`, `class_id`, `section_ids`, `subject_ids`, `session_id`, `comments`, `created_at`, `school_id`) VALUES (1,'Name 1',2,'[\"1\",\"2\",\"4\"]','[\"4\",\"5\",\"6\"]',1,'Test11','2025-02-06 13:19:19',1),
(2,'Name 2',1,'[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\"]','[\"1\",\"3\",\"4\",\"9\"]',1,'Assigninig for testing...','2025-02-16 09:51:16',1),
(3,'Class 10',10,'[\"1\"]','[\"1\",\"2\",\"3\",\"4\"]',1,'Testing for second school','2025-03-14 17:35:36',1),
(4,'Class 5',5,'[\"1\"]','[\"1\",\"2\",\"22\"]',1,'Class 5','2025-03-22 10:41:52',1);
/*!40000 ALTER TABLE `subject_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `href` varchar(150) NOT NULL,
  `view` varchar(150) DEFAULT NULL,
  `active` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` (`id`, `name`, `icon`, `description`, `href`, `view`, `active`, `created_at`, `updated_at`, `school_id`) VALUES (1,'General Setting','green ace-icon fa fa-home bigger-120','Settings related to general application configuration','home','school_info.php',1,'2024-10-28 15:23:31','2025-03-13 17:51:24',1),
(7,'Roles Permissions','grey fa fa-users bigger-120','Settings for defining roles and permissions','roles','user_roles.php',1,'2024-10-28 15:23:31','2025-03-13 17:51:24',1),
(8,'Sort Modules','grey fa fa-desktop bigger-120','Settings for Arranging the position of Modules','sorting','sorting.php',1,'2024-10-28 15:23:31','2025-03-13 17:51:24',1),
(9,'Change School','fa fa-exchange','Settings for Changing School from one to other','change_school','change_school.php',1,'2024-10-28 15:23:31','2025-03-13 17:51:24',1),
(10,'Classes','fa fa-exchange','Manage the Classes for the System','classes','classes.php',1,'2024-10-28 15:23:31','2025-03-13 17:51:24',1),
(11,'Sections','fa fa-exchange','Manage the Sections for the Classes','section','sections.php',1,'2024-10-28 15:23:31','2025-03-13 17:51:24',1);
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_users`
--

DROP TABLE IF EXISTS `system_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `is_deleted` tinyint(1) DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `failed_login_attempts` int(11) DEFAULT 0,
  `profile_picture` varchar(255) DEFAULT 'assets/images/avatars/default-avatar.jpg',
  `address` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `city` varchar(150) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `whatsapp` varchar(256) DEFAULT NULL,
  `facebook` varchar(256) DEFAULT NULL,
  `pinterest` varchar(256) DEFAULT NULL,
  `instagram` varchar(256) DEFAULT NULL,
  `about` text DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `referance` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_users`
--

LOCK TABLES `system_users` WRITE;
/*!40000 ALTER TABLE `system_users` DISABLE KEYS */;
INSERT INTO `system_users` (`id`, `username`, `password`, `email`, `first_name`, `last_name`, `phone`, `role_id`, `created_at`, `updated_at`, `is_active`, `is_deleted`, `last_login`, `failed_login_attempts`, `profile_picture`, `address`, `country`, `city`, `date_of_birth`, `gender`, `whatsapp`, `facebook`, `pinterest`, `instagram`, `about`, `school_id`, `referance`) VALUES (1,'superadmin','superadmin321','qamarali@gmail.com','Qamar','Ali','0346-7607204',1,'2024-10-25 20:44:52','2025-10-10 18:49:44',1,0,NULL,0,'assets/images/avatars/default-profile.jpg','Street 15, Gulberg, Lahore','Pakistan','Islamabad','2007-07-27','Male','0346-7607204','https://facebook.com/qamarAli','https://linkedin.com/in/alikhan','https://instagram.com/ali.khan','Super Admin. Manages System Operations.',1,1),
(3,'superadmin','superadmin123','hmustafa@example.com','Hassan','Mustafa','03211223344',1,'2024-10-25 20:44:52','2025-10-10 18:49:51',1,0,NULL,0,NULL,'Islamabad, Pakistan','Pakistan','Islamabad','1985-03-12','Male',NULL,NULL,NULL,NULL,NULL,1,NULL),
(84,'sana.khalid@school.com','sana.khalid@school.com','sana.khalid@school.com','Sana','Khalid','03201234567',3,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'1990-05-15','Female',NULL,NULL,NULL,NULL,NULL,1,16),
(85,'kamran.akram@school.com','kamran.akram@school.com','kamran.akram@school.com','Kamran','Akram','03211234567',3,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'1988-08-22','Male',NULL,NULL,NULL,NULL,NULL,1,17),
(86,'lubna.zaheer@school.com','lubna.zaheer@school.com','lubna.zaheer@school.com','Lubna','Zaheer','03221234567',3,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'1992-03-18','Female',NULL,NULL,NULL,NULL,NULL,1,18),
(87,'asad.rauf@school.com','asad.rauf@school.com','asad.rauf@school.com','Asad','Rauf','03231234567',3,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'1985-11-30','Male',NULL,NULL,NULL,NULL,NULL,1,19),
(88,'rabia.siddique@school.com','rabia.siddique@school.com','rabia.siddique@school.com','Rabia','Siddique','03241234567',3,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'1991-07-25','Female',NULL,NULL,NULL,NULL,NULL,1,20),
(89,'farhan.bashir@school.com','farhan.bashir@school.com','farhan.bashir@school.com','Farhan','Bashir','03251234567',3,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'1989-12-10','Male',NULL,NULL,NULL,NULL,NULL,1,21),
(90,'adeel.haider@school.com','adeel.haider@school.com','adeel.haider@school.com','Adeel','Haider','03341234567',3,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'1993-03-30','Male',NULL,NULL,NULL,NULL,NULL,1,22),
(91,'ayesha.noor@school.com','ayesha.noor@school.com','ayesha.noor@school.com','Ayesha','Noor','03261234567',3,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'1993-02-14','Female',NULL,NULL,NULL,NULL,NULL,1,23),
(92,'hamza.rizwan@school.com','hamza.rizwan@school.com','hamza.rizwan@school.com','Hamza','Rizwan','03271234567',3,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'1987-09-08','Male',NULL,NULL,NULL,NULL,NULL,1,24),
(93,'hafiz.abdullah@school.com','hafiz.abdullah@school.com','hafiz.abdullah@school.com','Hafiz','Abdullah','03281234567',3,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'1986-04-05','Male',NULL,NULL,NULL,NULL,NULL,1,25),
(94,'saima.tariq@school.com','saima.tariq@school.com','saima.tariq@school.com','Saima','Tariq','03291234567',3,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'1994-06-20','Female',NULL,NULL,NULL,NULL,NULL,1,26),
(95,'mahjabeen.ali@school.com','mahjabeen.ali@school.com','mahjabeen.ali@school.com','Mahjabeen','Ali','03351234567',3,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'1995-04-14','Female',NULL,NULL,NULL,NULL,NULL,1,27),
(96,'shahzad.mirza@school.com','shahzad.mirza@school.com','shahzad.mirza@school.com','Shahzad','Mirza','03301234567',3,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'1990-10-12','Male',NULL,NULL,NULL,NULL,NULL,1,28),
(97,'nadia.masood@school.com','nadia.masood@school.com','nadia.masood@school.com','Nadia','Masood','03311234567',3,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'1988-01-18','Female',NULL,NULL,NULL,NULL,NULL,1,29),
(98,'bilal.qureshi@school.com','bilal.qureshi@school.com','bilal.qureshi@school.com','Bilal','Qureshi','03321234567',3,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'1992-11-25','Male',NULL,NULL,NULL,NULL,NULL,1,30),
(99,'zoya.anwar@school.com','zoya.anwar@school.com','zoya.anwar@school.com','Zoya','Anwar','03331234567',3,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'1991-08-08','Female',NULL,NULL,NULL,NULL,NULL,1,31),
(100,'ahmed.khan@email.com','ahmed.khan@email.com','ahmed.khan@email.com','Ahmed','Khan','03001234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2018-03-15','Male',NULL,NULL,NULL,NULL,NULL,1,1),
(101,'fatima.ali@email.com','fatima.ali@email.com','fatima.ali@email.com','Fatima','Ali','03011234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2018-05-20','Female',NULL,NULL,NULL,NULL,NULL,1,2),
(102,'sara.ahmed@email.com','sara.ahmed@email.com','sara.ahmed@email.com','Sara','Ahmed','03021234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2018-07-12','Female',NULL,NULL,NULL,NULL,NULL,1,3),
(103,'ali.hassan@email.com','ali.hassan@email.com','ali.hassan@email.com','Ali','Hassan','03031234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2018-04-25','Male',NULL,NULL,NULL,NULL,NULL,1,4),
(104,'zainab.malik@email.com','zainab.malik@email.com','zainab.malik@email.com','Zainab','Malik','03041234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2018-06-10','Female',NULL,NULL,NULL,NULL,NULL,1,5),
(105,'usman.raza@email.com','usman.raza@email.com','usman.raza@email.com','Usman','Raza','03051234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2018-08-18','Male',NULL,NULL,NULL,NULL,NULL,1,6),
(106,'bilal.sheikh@email.com','bilal.sheikh@email.com','bilal.sheikh@email.com','Bilal','Sheikh','03061234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2017-02-14','Male',NULL,NULL,NULL,NULL,NULL,1,7),
(107,'ayesha.iqbal@email.com','ayesha.iqbal@email.com','ayesha.iqbal@email.com','Ayesha','Iqbal','03071234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2017-03-22','Female',NULL,NULL,NULL,NULL,NULL,1,8),
(108,'hamza.yousaf@email.com','hamza.yousaf@email.com','hamza.yousaf@email.com','Hamza','Yousaf','03081234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2017-05-30','Male',NULL,NULL,NULL,NULL,NULL,1,9),
(109,'maryam.hussain@email.com','maryam.hussain@email.com','maryam.hussain@email.com','Maryam','Hussain','03091234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2017-01-17','Female',NULL,NULL,NULL,NULL,NULL,1,10),
(110,'hassan.farooq@email.com','hassan.farooq@email.com','hassan.farooq@email.com','Hassan','Farooq','03101234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2016-04-08','Male',NULL,NULL,NULL,NULL,NULL,1,11),
(111,'amna.saleem@email.com','amna.saleem@email.com','amna.saleem@email.com','Amna','Saleem','03111234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2016-06-19','Female',NULL,NULL,NULL,NULL,NULL,1,12),
(112,'faisal.nawaz@email.com','faisal.nawaz@email.com','faisal.nawaz@email.com','Faisal','Nawaz','03121234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2016-08-25','Male',NULL,NULL,NULL,NULL,NULL,1,13),
(113,'ibrahim.tariq@email.com','ibrahim.tariq@email.com','ibrahim.tariq@email.com','Ibrahim','Tariq','03131234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2015-03-12','Male',NULL,NULL,NULL,NULL,NULL,1,14),
(114,'hira.abbasi@email.com','hira.abbasi@email.com','hira.abbasi@email.com','Hira','Abbasi','03141234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2015-07-28','Female',NULL,NULL,NULL,NULL,NULL,1,15),
(115,'imran.jamil@email.com','imran.jamil@email.com','imran.jamil@email.com','Imran','Jamil','03151234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2015-09-05','Male',NULL,NULL,NULL,NULL,NULL,1,16),
(116,'khadija.saeed@email.com','khadija.saeed@email.com','khadija.saeed@email.com','Khadija','Saeed','03161234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2015-11-14','Female',NULL,NULL,NULL,NULL,NULL,1,17),
(117,'omar.shakeel@email.com','omar.shakeel@email.com','omar.shakeel@email.com','Omar','Shakeel','03171234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2015-12-22','Male',NULL,NULL,NULL,NULL,NULL,1,18),
(118,'nida.asif@email.com','nida.asif@email.com','nida.asif@email.com','Nida','Asif','03181234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2016-10-05','Female',NULL,NULL,NULL,NULL,NULL,1,19),
(119,'yasir.butt@email.com','yasir.butt@email.com','yasir.butt@email.com','Yasir','Butt','03191234567',4,'2025-10-10 09:26:41','2025-10-10 12:26:41',1,0,NULL,0,'assets/images/avatars/default-profile.jpg',NULL,NULL,NULL,'2017-11-09','Male',NULL,NULL,NULL,NULL,NULL,1,20);
/*!40000 ALTER TABLE `system_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_files`
--

DROP TABLE IF EXISTS `ticket_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_files` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_files`
--

LOCK TABLES `ticket_files` WRITE;
/*!40000 ALTER TABLE `ticket_files` DISABLE KEYS */;
INSERT INTO `ticket_files` (`id`, `ticket_id`, `file_name`, `file_path`, `uploaded_at`) VALUES (1,1,'155front.jpg','ticketing/6846cb142e37e_155front.jpg','2025-06-09 11:52:52'),
(2,1,'Screenshot 2025-10-09 at 10.08.53 PM.png','ticketing/68e7ec84d1531_Screenshot_2025-10-09_at_10.08.53_PM.png','2025-10-09 19:10:28');
/*!40000 ALTER TABLE `ticket_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_logs`
--

DROP TABLE IF EXISTS `ticket_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_logs` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `field_name` varchar(50) DEFAULT NULL,
  `old_value` varchar(255) DEFAULT NULL,
  `new_value` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tracks all changes made to tickets';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_logs`
--

LOCK TABLES `ticket_logs` WRITE;
/*!40000 ALTER TABLE `ticket_logs` DISABLE KEYS */;
INSERT INTO `ticket_logs` (`id`, `ticket_id`, `user_id`, `action`, `field_name`, `old_value`, `new_value`, `created_at`) VALUES (1,1,1,'updated','status','On hold','Pending','2025-10-09 19:45:33'),
(2,1,1,'updated','priority','High','Low','2025-10-09 19:47:56'),
(3,1,1,'updated','status','Pending','On hold','2025-10-09 19:57:39'),
(4,1,1,'updated','status','On hold','Solved','2025-10-09 19:57:45');
/*!40000 ALTER TABLE `ticket_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_replies`
--

DROP TABLE IF EXISTS `ticket_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_replies` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `file` text DEFAULT NULL,
  `replied_by` int(11) DEFAULT NULL,
  `replied_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_replies`
--

LOCK TABLES `ticket_replies` WRITE;
/*!40000 ALTER TABLE `ticket_replies` DISABLE KEYS */;
INSERT INTO `ticket_replies` (`id`, `ticket_id`, `message`, `file`, `replied_by`, `replied_at`) VALUES (1,1,'Please do it As Soon As Possible.',NULL,1,'2025-10-09 19:17:11');
/*!40000 ALTER TABLE `ticket_replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `requester_name` varchar(256) DEFAULT NULL,
  `requester_email` varchar(50) DEFAULT NULL,
  `file` text DEFAULT NULL,
  `category` enum('General','Technical','Academic','Fee Related') NOT NULL,
  `status` enum('Open','Pending','On hold','Solved','Closed') DEFAULT 'Open',
  `priority` enum('Low','Medium','High') DEFAULT 'Medium',
  `created_by` int(11) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` (`id`, `title`, `description`, `requester_name`, `requester_email`, `file`, `category`, `status`, `priority`, `created_by`, `assigned_to`, `created_at`, `updated_at`) VALUES (1,'Truncate System Request','Hello, IT Support team, we need to truncate our current system for deploying and making it live for our customer.\r\nThankyou.','Super Admin','superadmin@gmail.com',NULL,'Technical','Solved','Low',1,1,'2025-10-09 19:10:28','2025-10-09 19:57:45');
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `video_comments`
--

DROP TABLE IF EXISTS `video_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `video_comments` (
  `id` int(11) NOT NULL,
  `recording_id` varchar(46) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `timestamp_seconds` int(11) DEFAULT 0,
  `color_scheme` varchar(20) DEFAULT 'default',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `video_comments`
--

LOCK TABLES `video_comments` WRITE;
/*!40000 ALTER TABLE `video_comments` DISABLE KEYS */;
INSERT INTO `video_comments` (`id`, `recording_id`, `user_id`, `comment_text`, `timestamp_seconds`, `color_scheme`, `created_at`, `updated_at`) VALUES (1,'1',1,'Test comment from database',30,'success','2025-10-02 11:44:54','2025-10-02 11:44:54'),
(2,'0',1,'This is for testing',0,'success','2025-10-02 14:55:00','2025-10-02 14:55:00'),
(4,'TlYon-NoPVyZyM2009cJSDtSBIYgA8luBZVP',1,'This is testing',0,'danger','2025-10-03 04:33:04','2025-10-03 04:33:04'),
(5,'TlYon-NoPVyZyM2009cJSDtSBIYgA8luBZVP',1,'New Testing',0,'success','2025-10-03 04:33:19','2025-10-03 04:33:19'),
(6,'mqWSipzt6VXKxkrvavoORwwqENs64OI6760i',1,'Main Testing',0,'danger','2025-10-06 15:54:00','2025-10-06 15:54:00');
/*!40000 ALTER TABLE `video_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visitors`
--

DROP TABLE IF EXISTS `visitors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `visitors` (
  `visitor_id` int(11) NOT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `meeting_with` varchar(255) DEFAULT NULL,
  `visitor_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `id_card` varchar(20) DEFAULT NULL,
  `number_of_persons` int(11) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Active',
  `date` date DEFAULT NULL,
  `in_time` time DEFAULT NULL,
  `out_time` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `session_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitors`
--

LOCK TABLES `visitors` WRITE;
/*!40000 ALTER TABLE `visitors` DISABLE KEYS */;
INSERT INTO `visitors` (`visitor_id`, `purpose`, `meeting_with`, `visitor_name`, `phone`, `id_card`, `number_of_persons`, `status`, `date`, `in_time`, `out_time`, `created_at`, `updated_at`, `session_id`, `school_id`) VALUES (1,'Parent Teacher Meeting','18001','Ali Ahmed','03001234567','4210112345671',2,'Active','2025-02-10','10:00:00','11:00:00','2025-02-15 11:58:10','2025-03-13 17:51:24',1,1),
(2,'Marketing','9004','Fatima Khan','03111234567','4220112345672',3,'Active','2025-02-11','11:30:00','12:30:00','2025-02-15 11:58:10','2025-03-13 17:51:24',1,1),
(3,'Student Meeting','18002','Usman Malik','03221234567','4230112345673',1,'Active','2025-02-11','09:00:00','10:00:00','2025-02-15 11:58:10','2025-03-13 17:51:24',1,1),
(4,'Principal Meeting','9003','Ayesha Raza','03331234567','4240112345674',4,'Active','2025-02-12','12:00:00','13:00:00','2025-02-15 11:58:10','2025-03-13 17:51:24',1,1),
(5,'School Events','18003','Bilal Akhtar','03441234567','4250112345675',5,'Active','2025-02-13','14:00:00','15:00:00','2025-02-15 11:58:10','2025-03-13 17:51:24',1,1),
(6,'Staff Meeting','9005','Hina Shah','03551234567','4260112345676',2,'Active','2025-02-12','16:00:00','17:00:00','2025-02-15 11:58:10','2025-03-13 17:51:24',1,1),
(7,'Parent Teacher Meeting','18004','Kamran Ali','03661234567','4270112345677',3,'Active','2025-02-12','10:30:00','11:30:00','2025-02-15 11:58:10','2025-03-13 17:51:24',1,1),
(8,'Marketing','3-4','Sanaullah Khan','03771234567','4280112345678',1,'active','2025-02-13','12:00:00','13:00:00','2025-02-15 11:58:10','2025-03-13 17:51:24',1,1);
/*!40000 ALTER TABLE `visitors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webrtc_signals`
--

DROP TABLE IF EXISTS `webrtc_signals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `webrtc_signals` (
  `id` int(11) NOT NULL,
  `room_code` varchar(50) NOT NULL,
  `from_peer_id` varchar(100) NOT NULL,
  `to_peer_id` varchar(100) NOT NULL,
  `signal_type` enum('offer','answer','ice-candidate') NOT NULL,
  `signal_data` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores WebRTC signaling data for peer connections';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webrtc_signals`
--

LOCK TABLES `webrtc_signals` WRITE;
/*!40000 ALTER TABLE `webrtc_signals` DISABLE KEYS */;
/*!40000 ALTER TABLE `webrtc_signals` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-10 19:10:23
