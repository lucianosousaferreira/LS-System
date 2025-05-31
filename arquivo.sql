-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: oficina
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'c','85991404281','br222 km06,9154 tabapuazinho caucaia Ce'),(2,'Luciano','85991404281','br 222km06, 9154 - tabapua caucaia ce');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ordens_servico`
--

DROP TABLE IF EXISTS `ordens_servico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ordens_servico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) DEFAULT NULL,
  `veiculo_id` int(11) DEFAULT NULL,
  `data_abertura` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `valor_total` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `veiculo_id` (`veiculo_id`),
  CONSTRAINT `ordens_servico_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `ordens_servico_ibfk_2` FOREIGN KEY (`veiculo_id`) REFERENCES `veiculos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ordens_servico`
--

LOCK TABLES `ordens_servico` WRITE;
/*!40000 ALTER TABLE `ordens_servico` DISABLE KEYS */;
/*!40000 ALTER TABLE `ordens_servico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `os_itens`
--

DROP TABLE IF EXISTS `os_itens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `os_itens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `os_id` int(11) DEFAULT NULL,
  `produto_servico_id` int(11) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `os_id` (`os_id`),
  KEY `produto_servico_id` (`produto_servico_id`),
  CONSTRAINT `os_itens_ibfk_1` FOREIGN KEY (`os_id`) REFERENCES `ordens_servico` (`id`),
  CONSTRAINT `os_itens_ibfk_2` FOREIGN KEY (`produto_servico_id`) REFERENCES `produtos_servicos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `os_itens`
--

LOCK TABLES `os_itens` WRITE;
/*!40000 ALTER TABLE `os_itens` DISABLE KEYS */;
/*!40000 ALTER TABLE `os_itens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produtos_servicos`
--

DROP TABLE IF EXISTS `produtos_servicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produtos_servicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos_servicos`
--

LOCK TABLES `produtos_servicos` WRITE;
/*!40000 ALTER TABLE `produtos_servicos` DISABLE KEYS */;
/*!40000 ALTER TABLE `produtos_servicos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_clientes`
--

DROP TABLE IF EXISTS `tb_clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `cpf_cnpj` varchar(20) NOT NULL,
  `contato` varchar(30) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_clientes`
--

LOCK TABLES `tb_clientes` WRITE;
/*!40000 ALTER TABLE `tb_clientes` DISABLE KEYS */;
INSERT INTO `tb_clientes` VALUES (15,'LUCIANO S FERREIRA','4998744000130','85991404281','BR 222 KM06,9154','lucianosousaferreira27@gmail.com'),(16,'CLIENTE2','89077705691','85999000000','R.EXEMPLO1,123 BAIRRO UF','CLIENTE2@GMAIL.COM'),(17,'CLIENTE 3','99999999999','85988880000','RUA 3,123 BAIRRO UF','CLIENTE3@GMAIL.COM'),(18,'CLIENTE 4','99999999998','85988879993','RUA 4,234 BAIRRO UF','CLIENTE4@GMAIL.COM'),(19,'CLIENTE 5','99999999997','85988879986','RUA 5, 456 BAIRRO UF','CLIENTE5@GMAIL.COM'),(20,'CLIENTE 6','99999999996','85988879979','RUA 6 , 789 BAIRRO UF','CLIENTE6@GMAIL.COM'),(21,'CLIENTE 7','99999999995','85988879972','RUA 7 , 890 BAIRRO UF','CLIENTE7@GMAIL.COM'),(22,'CLIENTE 8','99999999994','85988879965','RUA 8 , 999 BAIRRO UF','CLIENTE8@GMAIL.COM'),(23,'CLIENTE 9','99999999993','85988879958','RUA 9 , 888 BAIRRO UF','CLIENTE9@GMAIL.COM'),(24,'CLIENTE 10','99999999992','85988879951','RUA 10 , 777 BAIRRO UF','CLIENTE10@GMAIL.COM'),(25,'CLIENTE 11','99999999991','85988879944','RUA 11 , 655 BAIRRO UF','CLIENTE11@GMAIL.COM'),(26,'CLIENTE 12','99999999990','85988879937','RUA 12, 444 BAIRRO UF','CLIENTE12@GMAIL.COM'),(27,'CLODOMILDO','','85982024066','BR 222 COND MISTER HULL','');
/*!40000 ALTER TABLE `tb_clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_fornecedores`
--

DROP TABLE IF EXISTS `tb_fornecedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_fornecedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_fornecedores`
--

LOCK TABLES `tb_fornecedores` WRITE;
/*!40000 ALTER TABLE `tb_fornecedores` DISABLE KEYS */;
INSERT INTO `tb_fornecedores` VALUES (6,'AUTOFUSO'),(4,'BEZERRA OLIVEIRA'),(7,'CAMPINA GRANDE'),(10,'DPA DISTRIBUIDORA'),(9,'ESCRITORIO DO OLEO'),(8,'GERARDO BASTOS'),(11,'INACIO'),(3,'NOSSO ESTOQUE'),(5,'PACAEMBU'),(1,'PADRE CICERO ATACADO'),(2,'PADRE CICERO VAREJO');
/*!40000 ALTER TABLE `tb_fornecedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_itens_os`
--

DROP TABLE IF EXISTS `tb_itens_os`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_itens_os` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ordem_servico_id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `tipo` enum('produto','serviço') NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `total` decimal(10,2) GENERATED ALWAYS AS (`preco` * `quantidade`) STORED,
  PRIMARY KEY (`id`),
  KEY `ordem_servico_id` (`ordem_servico_id`),
  CONSTRAINT `tb_itens_os_ibfk_1` FOREIGN KEY (`ordem_servico_id`) REFERENCES `tb_ordens_servico` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_itens_os`
--

LOCK TABLES `tb_itens_os` WRITE;
/*!40000 ALTER TABLE `tb_itens_os` DISABLE KEYS */;
INSERT INTO `tb_itens_os` VALUES (102,30,'REVISAO COMPLETA DO MTR CAM GRANDE','serviço',1000.00,1,1000.00),(103,31,'REVISAO COMPLETA DO MTR CAM GRANDE','serviço',1000.00,1,1000.00),(105,32,'DESMONTAGEM E MONTAGEM DO MTR OM366','serviço',3500.00,1,3500.00);
/*!40000 ALTER TABLE `tb_itens_os` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_itens_venda`
--

DROP TABLE IF EXISTS `tb_itens_venda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_itens_venda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `venda_id` int(11) DEFAULT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_itens_venda`
--

LOCK TABLES `tb_itens_venda` WRITE;
/*!40000 ALTER TABLE `tb_itens_venda` DISABLE KEYS */;
INSERT INTO `tb_itens_venda` VALUES (1,1,15,'SILICONE ALTATEMPERATURA 55G',28.00,1,28.00),(2,1,18,'GRAXA ROLAMENTO AZUL 1KG',68.00,1,68.00),(3,1,17,'COLA P JUNTA',16.00,1,16.00),(4,4,17,'COLA P JUNTA',16.00,4,64.00),(5,4,19,'OLEO DE FREIO DOT4',27.00,2,54.00),(6,5,15,'SILICONE ALTATEMPERATURA 55G',28.00,2,56.00),(7,6,39,'OLEO HID ATF',30.00,1,30.00),(8,6,15,'SILICONE ALTATEMPERATURA 55G',28.00,1,28.00),(9,7,39,'OLEO HID ATF',30.00,1,30.00),(10,7,15,'SILICONE ALTATEMPERATURA 55G',28.00,1,28.00),(11,8,40,'RET CUB ROD TS MB 710',35.80,1,35.80),(12,8,15,'SILICONE ALTATEMPERATURA 55G',28.00,1,28.00),(13,8,18,'GRAXA ROLAMENTO AZUL 1KG',68.00,1,68.00),(14,9,40,'RET CUB ROD TS MB 710',35.80,1,35.80),(15,9,15,'SILICONE ALTATEMPERATURA 55G',28.00,1,28.00),(16,9,18,'GRAXA ROLAMENTO AZUL 1KG',68.00,1,68.00);
/*!40000 ALTER TABLE `tb_itens_venda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_marca`
--

DROP TABLE IF EXISTS `tb_marca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_marca` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_marca`
--

LOCK TABLES `tb_marca` WRITE;
/*!40000 ALTER TABLE `tb_marca` DISABLE KEYS */;
INSERT INTO `tb_marca` VALUES (8,'DAF'),(6,'Ford'),(19,'Foton'),(10,'Freightliner'),(23,'HHHH'),(17,'Hino'),(21,'HITECH'),(14,'Hyundai'),(9,'International'),(18,'Isuzu'),(7,'Iveco'),(20,'JAC Motors'),(25,'KARTER'),(11,'Kenworth'),(13,'Mack'),(5,'MAN'),(1,'Mercedes-Benz'),(12,'Peterbilt'),(15,'Renault Trucks'),(24,'SABO'),(3,'Scania'),(22,'SCHADEK'),(16,'Tata Motors'),(26,'TECFIL'),(4,'Volkswagen'),(2,'Volvo'),(27,'WEGA');
/*!40000 ALTER TABLE `tb_marca` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_ordens_servico`
--

DROP TABLE IF EXISTS `tb_ordens_servico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_ordens_servico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_os` varchar(20) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `veiculo_id` int(11) NOT NULL,
  `data_abertura` datetime DEFAULT current_timestamp(),
  `total` float DEFAULT NULL,
  `data_entrada` date DEFAULT NULL,
  `data_saida` date DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `relato_problemas` text DEFAULT NULL,
  `laudo_servico` text DEFAULT NULL,
  `desconto` decimal(10,2) DEFAULT 0.00,
  `forma_pagamento` varchar(100) DEFAULT NULL,
  `tecnico_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_ordens_servico`
--

LOCK TABLES `tb_ordens_servico` WRITE;
/*!40000 ALTER TABLE `tb_ordens_servico` DISABLE KEYS */;
INSERT INTO `tb_ordens_servico` VALUES (30,'1',27,21,'2025-05-30 19:41:42',950,'2025-05-30',NULL,'Aberto','D','X',5.00,NULL,NULL),(31,'2',27,21,'2025-05-30 19:42:57',950,'2025-05-30',NULL,'Aberto','D','D',5.00,NULL,NULL),(32,'3',27,21,'2025-05-30 20:59:13',3150,'2025-05-30','0000-00-00','Aberto','s','0',10.00,'Pix',3);
/*!40000 ALTER TABLE `tb_ordens_servico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_produtos_servicos`
--

DROP TABLE IF EXISTS `tb_produtos_servicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_produtos_servicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(20) NOT NULL,
  `descricao` text NOT NULL,
  `referencia_produto` varchar(100) DEFAULT NULL,
  `preco_compra` decimal(10,2) DEFAULT NULL,
  `preco_venda` decimal(10,2) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `imagem` varchar(255) DEFAULT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `fornecedor` varchar(100) DEFAULT NULL,
  `codigo_produto` varchar(50) DEFAULT NULL,
  `estoque` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_produtos_servicos`
--

LOCK TABLES `tb_produtos_servicos` WRITE;
/*!40000 ALTER TABLE `tb_produtos_servicos` DISABLE KEYS */;
INSERT INTO `tb_produtos_servicos` VALUES (15,'Produto','SILICONE ALTATEMPERATURA 55G','501',17.80,28.00,0.00,'2025-05-13 01:41:14','produto_68262341bf9aa3.69702405.jpg','LOCTITE','','004915',10),(17,'Produto','COLA P JUNTA','COLA3M',8.60,16.00,0.00,'2025-05-15 16:29:22','produto_68261ba41d8011.35952049.jpg','3M','PADRE CICERO VAREJO','382935',7),(18,'Produto','GRAXA ROLAMENTO AZUL 1KG','GXMP2',39.00,68.00,0.00,'2025-05-15 17:26:55','produto_682625b2745dc1.15807353.jpg','BLUTER','ESCRITORIO DO OLEO','899930',8),(19,'Produto','OLEO DE FREIO DOT4','DOT4',17.91,27.00,0.00,'2025-05-15 17:48:59','produto_6826290b3d04d1.13493647.jpg','HITECH','ESCRITORIO DO OLEO','350845',11),(20,'Serviço','TROCAR RETENTOR DA RODA  CAM 3/4','',1.00,80.00,0.00,'2025-05-15 17:58:55',NULL,'','','054437',10),(21,'Serviço','LUFRIFICACAO COMPLETA','',1.00,70.00,0.00,'2025-05-15 18:01:24',NULL,'','','219649',10),(22,'Serviço','TROCAR LONA DE FREIO CAM GRANDE','',1.00,150.00,0.00,'2025-05-15 18:02:12',NULL,'','','934936',10),(23,'Serviço','TROCAR PAR AMORT CABINE','',1.00,100.00,0.00,'2025-05-15 18:02:39',NULL,'','','015734',10),(24,'Serviço','REVISAO SIMPLES','',1.00,200.00,0.00,'2025-05-15 18:03:24',NULL,'','','273863',10),(25,'Serviço','REVISAO COMPLETA','',1.00,300.00,0.00,'2025-05-15 18:03:51',NULL,'','','322115',10),(26,'Serviço','TROCAR OLEO + FILTRO LUB','',1.00,100.00,0.00,'2025-05-15 18:04:12',NULL,'','','788983',10),(27,'Serviço','TROCAR OLEO CX CAMBIO','',1.00,70.00,0.00,'2025-05-15 18:05:03',NULL,'','','978573',10),(28,'Serviço','TROCAR OLEO DIFERENCIAL','',1.00,60.00,0.00,'2025-05-15 18:05:39',NULL,'','','525915',10),(29,'Serviço','REVISAO COMPLETA DO MTR CAM GRANDE','',1.00,1000.00,0.00,'2025-05-15 18:06:31',NULL,'','','693858',10),(30,'Serviço','DESMONTAGEM E MONTAGEM DO MTR OM366','',1.00,3500.00,0.00,'2025-05-15 18:07:08',NULL,'','','891545',10),(31,'Serviço','DESMONTAGEM E MONTAGEM DO MTR CUMMINS ISB 6 CIL','',1.00,4500.00,0.00,'2025-05-15 18:07:46',NULL,'','','376153',10),(32,'Serviço','DESMONTAGEM E MONTAGEM DO MTR CUMMINS 4 CIL','',1.00,4000.00,0.00,'2025-05-15 18:08:12',NULL,'','','206127',10),(33,'Serviço','DESMONTAGEM E MONTAGEM DO MTR MWM X10 X12 6 CIL','',1.00,4500.00,0.00,'2025-05-15 18:08:49',NULL,'','','902180',10),(34,'Serviço','DESMONTAGEM E MONTAGEM DO MTR MWM 229 3 CIL 4 CIL','',1.00,3000.00,0.00,'2025-05-15 18:09:19',NULL,'','','892517',10),(36,'Serviço','TROCAR BOMBA DE AGUA','',1.00,200.00,0.00,'2025-05-24 00:15:00',NULL,'','','399174',0),(37,'Produto','RET CUB RODA TS 3 EIXO','00310B',22.24,32.00,0.00,'2025-05-27 18:42:18','produto_68360857b938d4.75696927.jpg','SABO','PADRE CICERO ATACADO','518298',3),(38,'Produto','RET CUB ROD TS VW FORD ROCKUEL','02713BRY',55.07,82.75,0.00,'2025-05-27 22:55:30','produto_683642e2b53105.52273525.jpg','SABO','PADRE CICERO ATACADO','790525',0),(39,'Produto','OLEO HID ATF','ATF',20.00,30.00,0.00,'2025-05-28 17:29:59','produto_68374817406842.45337022.jpg','KARTER','ESCRITORIO DO OLEO','747957',2),(40,'Produto','RET CUB ROD TS MB 710','02690BRG',23.79,35.80,0.00,'2025-05-30 11:30:58','produto_683996f2dd6a08.93854533.jpg','SABO','PADRE CICERO ATACADO','895292',3),(41,'Produto','RET CUBO RD DIANT MB 1620','01549BRA',35.11,59.00,0.00,'2025-05-30 11:34:51','produto_683997dbde3876.42414099.jpg','SABO','PADRE CICERO ATACADO','407867',2),(42,'Produto','RET CUB ROD TS MB FR A AR','01884BRAG',31.98,49.50,0.00,'2025-05-30 11:38:32','produto_683998b8e6dfd4.99805914.jpg','SABO','PADRE CICERO ATACADO','339627',2),(43,'Produto','FILTRO COMB D20','PC2/155',16.00,28.00,0.00,'2025-05-30 16:51:50','produto_6839e2263e5035.65948543.jpg','TECFIL','PADRE CICERO VAREJO','204628',2),(44,'Produto','RET VOLANTE MTR MAXION PERKINS','02583BRG',173.16,256.00,0.00,'2025-05-30 17:02:55','produto_6839e4bf52fd73.83349589.jpg','SABO','PADRE CICERO ATACADO','725852',1),(45,'Produto','FILTRO LUBRIF HILUX 2.8 96/','WO350',22.42,38.00,0.00,'2025-05-30 17:26:01','produto_6839ea29893e66.22765321.jpg','WEGA','PADRE CICERO ATACADO','889157',1);
/*!40000 ALTER TABLE `tb_produtos_servicos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_tecnicos`
--

DROP TABLE IF EXISTS `tb_tecnicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_tecnicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `especialidade` varchar(100) DEFAULT NULL,
  `contato` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_tecnicos`
--

LOCK TABLES `tb_tecnicos` WRITE;
/*!40000 ALTER TABLE `tb_tecnicos` DISABLE KEYS */;
INSERT INTO `tb_tecnicos` VALUES (1,'FC DIASSIS','MECANICO','','','2025-05-16 15:01:51'),(2,'JOSE MARCIANO','MECANICO','','','2025-05-16 15:02:26'),(3,'LUCIANO','CONSULTOR DE AUTOPEÇAS','','','2025-05-16 15:03:49'),(4,'RICARDO','AUX MECANICO','','','2025-05-16 15:04:25');
/*!40000 ALTER TABLE `tb_tecnicos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_usuarios`
--

DROP TABLE IF EXISTS `tb_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_usuarios`
--

LOCK TABLES `tb_usuarios` WRITE;
/*!40000 ALTER TABLE `tb_usuarios` DISABLE KEYS */;
INSERT INTO `tb_usuarios` VALUES (1,'Administrador','admin','$2y$10$Wl3CP9w8TbbWwI/VMTnTHexYDDt7KlgmN3G44aGu1UXCBm10PCUNa','2025-05-10 15:42:29'),(3,'Administrador1','luciano','123456','2025-05-10 15:45:23'),(4,'Administrador2','admin1','$2y$10$UneasqGeWYlIdjTxOnvplelf0UpN1a804D1DZjo291qp4H/TT9JGW','2025-05-10 18:04:06');
/*!40000 ALTER TABLE `tb_usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_veiculos`
--

DROP TABLE IF EXISTS `tb_veiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_veiculos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_nome` varchar(100) NOT NULL,
  `placa` varchar(10) NOT NULL,
  `marca` varchar(50) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `ano` varchar(10) DEFAULT NULL,
  `cor` varchar(30) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `cliente_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `placa` (`placa`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_veiculos`
--

LOCK TABLES `tb_veiculos` WRITE;
/*!40000 ALTER TABLE `tb_veiculos` DISABLE KEYS */;
INSERT INTO `tb_veiculos` VALUES (19,'CLIENTE2','AAA1111','MERCEDES-BENZ','710','2003','BRANCA','2025-05-23 23:05:16',16),(20,'CLIENTE 3','BBB7799','MERCEDES-BENZ','710','2006','VERMELHA','2025-05-23 23:05:59',17),(21,'CLODOMILDO','DTC3C50','VOLVO','VM 260','2007','BRANCO','2025-05-28 17:37:45',27);
/*!40000 ALTER TABLE `tb_veiculos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_vendas`
--

DROP TABLE IF EXISTS `tb_vendas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_vendas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_venda` varchar(20) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `desconto` decimal(5,2) DEFAULT NULL,
  `forma_pagamento` varchar(50) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `data_venda` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_vendas`
--

LOCK TABLES `tb_vendas` WRITE;
/*!40000 ALTER TABLE `tb_vendas` DISABLE KEYS */;
INSERT INTO `tb_vendas` VALUES (1,'V990633',21,5.00,'PIX',106.40,'2025-05-25 13:28:38'),(2,NULL,NULL,NULL,NULL,NULL,'2025-05-25 13:33:33'),(3,NULL,NULL,NULL,NULL,NULL,'2025-05-25 13:33:37'),(4,'V774470',19,5.00,'PIX',112.10,'2025-05-25 13:34:11'),(5,'V128954',21,5.00,'PIX',53.20,'2025-05-25 13:36:19'),(6,'V976850',17,5.00,'Dinheiro',55.10,'2025-05-28 14:30:55'),(7,'V976850',17,5.00,'Dinheiro',55.10,'2025-05-28 14:30:58'),(8,'V862578',27,5.00,'Outros',125.21,'2025-05-30 16:56:47'),(9,'V862578',27,5.00,'Outros',125.21,'2025-05-30 16:56:50');
/*!40000 ALTER TABLE `tb_vendas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `veiculos`
--

DROP TABLE IF EXISTS `veiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `veiculos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) DEFAULT NULL,
  `placa` varchar(10) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `ano` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `veiculos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `veiculos`
--

LOCK TABLES `veiculos` WRITE;
/*!40000 ALTER TABLE `veiculos` DISABLE KEYS */;
/*!40000 ALTER TABLE `veiculos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-31 15:34:17
