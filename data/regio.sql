-- Table structure for table `regio`
--

DROP TABLE IF EXISTS `regio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `regio` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nev` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET ascii NOT NULL,
  `order` int(10) unsigned NOT NULL,
  `parent` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  KEY `order` (`order`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regio`
--

LOCK TABLES `regio` WRITE;
/*!40000 ALTER TABLE `regio` DISABLE KEYS */;
INSERT INTO `regio` VALUES (1,'Országos','orszagos',1,0),(2,'Közép-Magyarország','kozep-magyarorszag',2,0),(3,'Pest megye','pest-megye',1,2),(4,'Budapest','budapest',2,2),(5,'Dél-Alföld','del-alfold',3,0),(6,'Bács-Kiskun megye','bacs-kiskun-megye',1,5),(7,'Békés megye','bekes-megye',2,5),(8,'Csongrád megye','csongrad-megye',3,5),(9,'Észak-Alföld','eszak-alfold',4,0),(10,'Hajdú-Bihar megye','hajdu-bihar-megye',1,9),(11,'Jász-Nagykun-Szolnok megye','jasz-nagykun-szolnok-megye',2,9),(12,'Szabolcs-Szatmár-Bereg megye','szabolcs-szatmar-bereg-megye',3,9),(13,'Észak-Magyarország','eszak-magyarorszag',5,0),(14,'Borsod-Abaúj-Zemplén megye','borsod-abauj-zemplen-megye',1,13),(15,'Heves megye','heves-megye',2,13),(16,'Nógrád megye','nograd-megye',3,13),(17,'Közép-Dunántúl','kozep-dunantul',6,0),(18,'Komárom-Esztergom megye','komarom-esztergom-megye',1,17),(19,'Fejér megye','fejer-megye',2,17),(20,'Veszprém megye','veszprem-megye',3,17),(21,'Nyugat-Dunántúl','nyugat-dunantul',7,0),(22,'Győr-Moson-Sopron megye','gyor-moson-sopron-megye',1,21),(23,'Vas megye','vas-megye',2,21),(24,'Zala megye','zala-megye',3,21),(25,'Dél-Dunántúl','del-dunantul',8,0),(26,'Baranya megye','baranya-megye',1,25),(27,'Somogy megye','somogy-megye',2,25),(28,'Tolna megye','tolna-megye',3,25),(29,'Külföld','kulfold',9,0);
/*!40000 ALTER TABLE `regio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rovat`
