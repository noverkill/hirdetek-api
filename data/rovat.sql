-- Table structure for table `rovat`
--

DROP TABLE IF EXISTS `rovat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rovat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nev` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET ascii NOT NULL,
  `order` int(10) unsigned NOT NULL,
  `parent` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  KEY `order` (`order`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rovat`
--

LOCK TABLES `rovat` WRITE;
/*!40000 ALTER TABLE `rovat` DISABLE KEYS */;
INSERT INTO `rovat` VALUES (1,'Általános kereskedelem','altalanos-kereskedelem',1,0),(2,'Bútor','butor',1,1),(3,'Egézség','egezseg',2,1),(4,'Gép, szerszám','gep-szerszam',3,1),(5,'Gyermekholmik','gyermekholmik',4,1),(6,'Hangszer','hangszer',5,1),(7,'Audió','audio',6,1),(8,'Lakberendezés','lakberendezes',7,1),(9,'Háztartási eszközök','haztartasi-eszkozok',8,1),(10,'Sporteszközök','sporteszkozok',9,1),(11,'Lakásfelszerelés','lakasfelszereles',10,1),(12,'Hardver &amp; Szoftver','hardver-szoftver',11,0),(13,'Ingatlan','ingatlan',3,0),(14,'Albérletet keres','alberletet-keres',1,13),(15,'Albérletet kínál','alberletet-kinal',2,13),(16,'Építkezés','epitkezes',3,13),(17,'Ingatlant keres','ingatlant-keres',4,13),(18,'Ingatlant kínál','ingatlant-kinal',5,13),(19,'Iroda, üzlethelyiség','iroda-uzlethelyiseg',6,13),(20,'Kiadó lakás','kiado-lakas',7,13),(21,'Közvetíto irodák','kozvetito-irodak',8,13),(22,'Lakáscsere','lakascsere',9,13),(23,'Telek','telek',10,13),(24,'Garázs','garazs',11,13),(25,'Ingatlan (Budapest)','ingatlan-budapest',4,0),(26,'Albérletet kínál','alberletet-kinal',1,25),(27,'Ingatlant keres','ingatlant-keres',2,25),(28,'Albérletet keres','alberletet-keres',3,25),(29,'Ingatlant kínál','ingatlant-kinal',4,25),(30,'Építkezés','epitkezes',5,25),(31,'Kiadó lakás','kiado-lakas',6,25),(32,'Iroda, üzlethelyiség','iroda-uzlethelyiseg',7,25),(33,'Közvetítő irodák','kozvetito-irodak',8,25),(34,'Garázs','garazs',9,25),(35,'Lakáscsere','lakascsere',10,25),(36,'Telek','telek',11,25),(37,'Internet szolgáltatások','internet-szolgaltatasok',9,0),(38,'Weblap','weblap',1,37),(39,'Foglalkoztatás','foglalkoztatas',2,0),(40,'Állást kínál','allast-kinal',1,39),(41,'Munkát kínál','munkat-kinal',2,39),(42,'Állást kínál - Alföld','allast-kinal-alfold',3,39),(43,'Állást kínál - Dunántúl','allast-kinal-dunantul',4,39),(44,'Állást keres','allast-keres',5,39),(45,'Állást kínál - Budapest és környéke','allast-kinal-budapest-es-kornyeke',6,39),(46,'Állást keres - Budapest és környéke','allast-keres-budapest-es-kornyeke',7,39),(47,'Állást kínál - Észak Magyarország','allast-kinal-eszak-magyarorszag',8,39),(48,'Kikapcsolódás','kikapcsolodas',7,0),(49,'Szálláshelyek','szallashelyek',1,48),(50,'Ismerkedés','ismerkedes',8,0),(51,'Hölgy keres Urat','holgy-keres-urat',1,50),(52,'Jármű','jarmu',6,0),(53,'Alkatrész','alkatresz',1,52),(54,'Autófelszerelés','autofelszereles',2,52),(55,'Autófelszerelés - Gumi, Felni','autofelszereles-gumi-felni',3,52),(56,'Szolgáltatás','szolgaltatas',5,0),(57,'Pénzügyi szolgáltatás','penzugyi-szolgaltatas',1,56),(58,'Oktatás','oktatas',2,56),(59,'Üzleti partnerkereső','uzleti-partnerkereso',3,56),(60,'Szervíz, javítás','szerviz-javitas',4,56),(61,'Befektetés','befektetes',5,56),(62,'Mobiltelefonok','mobiltelefonok',10,0);
/*!40000 ALTER TABLE `rovat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
