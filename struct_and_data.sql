-- MySQL dump 10.13  Distrib 5.7.18, for Linux (x86_64)
--
-- Host: localhost    Database: minicms_mvc
-- ------------------------------------------------------
-- Server version	5.7.18-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `creation_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,5,6,'test comment\r\n1',0),(2,2,2,'comment 2',1492440914),(4,11,6,'comment writer on sub 6\r\n',1492546884),(5,11,8,'comenter on sub-6',1492546964),(7,2,8,'commenter sub 1',1492620477);
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medias`
--

DROP TABLE IF EXISTS `medias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `medias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `creation_datetime` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medias`
--

LOCK TABLES `medias` WRITE;
/*!40000 ALTER TABLE `medias` DISABLE KEYS */;
INSERT INTO `medias` VALUES (17,'media-jpg','test800x600-media-jpg-2016-12-09.jpg','2016-12-09 00:00:00',2),(18,'media-pdf','test800x600-media-pdf-2016-12-09.pdf','2016-12-09 00:00:00',2),(19,'media-png','test800x600-media-png-2016-12-09.png','2016-12-09 00:00:00',2),(20,'media-zip','test800x600-media-zip-2016-12-09.zip','2016-12-09 00:00:00',6),(22,'media-jpeg','test800x600-media-jpeg-2016-12-09.jpeg','2016-12-09 00:00:00',6);
/*!40000 ALTER TABLE `medias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `in_use` tinyint(4) NOT NULL,
  `structure` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `parent_page_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `creation_datetime` datetime NOT NULL,
  `published` tinyint(4) NOT NULL,
  `allow_comments` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (2,'sub-1','sub 1','sub 1\r\n\r\n[manifesto media-jpg Vestibulum vestibulum, est viverra sagittis imperdiet, metus turpis finibus tellus, at aliquam est arcu id dui. Proin felis felis, ultrices nec turpis nec, lacinia tristique libero. <br>\r\nCurabitur ornare euismod pretium. Pellentesque commodo accumsan mi. Nunc gravida laoreet ligula, ac porta elit blandit quis. Nulla lorem urna, maximus eget interdum in, imperdiet et turpis. Mauris sed lectus vehicula nisl porttitor fringilla. Proin suscipit varius libero. Nam iaculis purus tempor orci vulputate aliquam. Nulla vitae justo faucibus, rhoncus justo tincidunt, ullamcorper turpis. Maecenas et lacus dignissim, condimentum est eu, ullamcorper sem. Nulla accumsan pulvinar diam, id viverra elit placerat sed. Aenean nec vulputate ligula, sed molestie risus.]',5,6,'2017-06-01 00:00:00',1,1),(5,'parent-1','parent 1','voici la liste de nos <strong>produits</strong>.\r\n<br>\r\n[img media-pnfgg 200]\r\n<br>\r\n[img media-jpg blabla]\r\n<br>\r\n[img media-jpeg bli bli]\r\n<br>\r\n[img media-jpg]\r\n<br>\r\n[img media-jpg title=\"blabla\" alt=\"blibli\" height=\"100px\" width=\"300px\"]',NULL,6,'2016-12-05 00:00:00',1,1),(6,'carousel','Carousel','sub 2 <br>\r\n[carousel media-jpg media-jpeg media-png]',5,6,'2016-12-05 00:00:00',1,0),(7,'parent-2','parent 2','parent 2',NULL,2,'2016-12-10 00:00:00',1,0),(8,'sub-3','sub 3','test',7,2,'2016-12-10 00:00:00',1,0),(9,'sub-4','Sub 4','sub 4',7,2,'2016-12-10 00:00:00',0,0),(10,'parent-darft','parent darft','parent darft',NULL,2,'2016-12-10 00:00:00',1,0),(11,'sub-6','sub 6','sub 6',10,2,'2016-12-10 00:00:00',1,1),(12,'parent-4','parent 4','parent 4',NULL,6,'2016-12-10 00:00:00',1,0);
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `creation_datetime` datetime NOT NULL,
  `published` tinyint(4) NOT NULL,
  `allow_comments` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_token` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_token` varchar(255) DEFAULT NULL,
  `password_change_time` int(11) unsigned DEFAULT NULL,
  `role` varchar(255) NOT NULL,
  `creation_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'Admin','florentpoujol@fastmail.com','','$2y$10$zfbhDR0GZWF5J10p8KToveJFMwWoXnxtaLNcfS/qXfqc/yBvE/HS6','48292c5d87bed7bb3329268b0eb2c57e01217ca2',1495357523,'admin','2016-11-27 00:00:00'),(6,'Writer','florent.poujol@gmail.com','','$2y$10$FG9UGDAGugNl.ruXUEThX.zbGjefL8dJkSTr1zcOj.RX8xHUTeMHu','',0,'writer','2016-11-30 00:00:00'),(8,'commenter','poujol.florent@wanadoo.fr','','$2y$10$vsYCo5XMrJ/NmmLj8JqJw.ts1J0ogE/gRYXXtB1Fc632EAysqrWKy','',0,'commenter','2017-04-17 00:00:00'),(9,'florent','florent.poujol+1@gmail.com','','$2y$10$IpS6wpk.fvAf6bvPiZV2PeGz1AnTxQE08dMYtzTBTnU8hzcA88kwS',NULL,NULL,'commenter','2017-04-28 00:00:00');
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

-- Dump completed on 2017-06-17 13:42:19
