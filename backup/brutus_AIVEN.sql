-- Estrutura e catálogo inicial. Não inclui clientes, senhas, endereços ou pedidos.
-- Brutus: importacao em banco vazio no Aiven / MySQL 8.4
-- Inclui estrutura atualizada; chaves primarias definidas no CREATE TABLE.
-- Executar apenas em um banco vazio. Nao repetir a importacao sobre dados existentes.

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria` (
  `cod_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`cod_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

set autocommit=0;
INSERT INTO `categoria` VALUES
(1,'Hamburguer'),
(2,'Kids'),
(3,'Combos'),
(4,'Acompanhamento'),
(5,'Bebidas'),
(6,'Sobremesa');
commit;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `endereco` (
  `cod_endereco` int(11) NOT NULL AUTO_INCREMENT,
  `cep` varchar(9) DEFAULT NULL,
  `rua` varchar(120) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `numero` int(6) DEFAULT NULL,
  `complemento` varchar(120) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `fk_Usuario_codigo` int(11) DEFAULT NULL,
  `identificacao` varchar(50) NOT NULL DEFAULT 'Entrega',
  `principal` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`cod_endereco`),
  KEY `FK_Endereco_2` (`fk_Usuario_codigo`),
  KEY `FK_Endereco_2_novo` (`fk_Usuario_codigo`),
  CONSTRAINT `FK_Endereco_2` FOREIGN KEY (`fk_Usuario_codigo`) REFERENCES `usuario` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

set autocommit=0;
commit;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `hist_status_ped` (
  `cod_hist` int(11) NOT NULL AUTO_INCREMENT,
  `cod_pedido` int(11) NOT NULL,
  `cod_status` int(11) NOT NULL,
  `data_hora` datetime NOT NULL,
  PRIMARY KEY (`cod_hist`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

set autocommit=0;
commit;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `itens` (
  `cod_item` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) DEFAULT NULL,
  `descricao` varchar(200) DEFAULT NULL,
  `preco` varchar(10) DEFAULT NULL,
  `imagem` varchar(200) NOT NULL,
  `fk_Categoria_cod_categoria` int(11) DEFAULT NULL,
  `fk_Pedidos_cod_pedido` int(11) DEFAULT NULL,
  PRIMARY KEY (`cod_item`),
  KEY `FK_Itens_2` (`fk_Categoria_cod_categoria`),
  KEY `FK_Itens_3` (`fk_Pedidos_cod_pedido`),
  CONSTRAINT `FK_Itens_2` FOREIGN KEY (`fk_Categoria_cod_categoria`) REFERENCES `categoria` (`cod_categoria`),
  CONSTRAINT `FK_Itens_3` FOREIGN KEY (`fk_Pedidos_cod_pedido`) REFERENCES `pedidos` (`cod_pedido`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

set autocommit=0;
INSERT INTO `itens` VALUES
(2,'Burguer BBQ','Carne, cheddar, cebola caramelizada, bacon crocante e molho barbecue artesanal, servido no pão.','32,00','burguerbbq.jpg',1,NULL),
(6,'Burguer do Chef','Carne de Angus, queijo gorgonzola, rúcula, cebola caramelizada e molho de mostarda, pão.','34,00','burguerchef.png',1,NULL),
(7,'Brutu Bacon Especial','Blend de carnes premium, queijo cheddar derretido, fatias de bacon crocante, cebola caramelizada, alface, tomate,molho barbecue artesanal. ','39,90','674f68e2c0014.png',1,NULL),
(8,'Mini Brutu Cheese','Pão de brioche, hambúrguer artesanal de carne bovina, queijo cheddar derretido, ketchup.','19,90','674f6ab0e32ed.jpeg',2,NULL),
(9,'Kids Brutu',' Mini Brutu Cheese (mini hambúrguer artesanal, queijo cheddar, ketchup, em pão brioche)','19,90','674f6bac0090e.jpg',2,NULL),
(10,'Combo Classico','1 Brutu Cheese (hambúrguer artesanal com carne bovina, queijo cheddar derretido, alface, tomate e molho especial).\r\n1 porção de batatas rústicas com páprica.\r\n1 refrigerante ou suco à escolha.','39,90','674f6c223b77f.jpg',3,NULL),
(11,' Double Brutu Bacon','1 Double Brutu Bacon (dois hambúrgueres artesanais, bacon crocante, queijo prato, cebola caramelizada e molho barbecue).\r\n1 porção de onion rings.\r\n1 milkshake (chocolate ou morango).','59,90','674f6c80b1e0d.jpg',3,NULL),
(12,'Combo kids','Mini Hambúrguer: Pão artesanal, hambúrguer de carne bovina, queijo derretido e ketchup caseiro.\r\nAcompanhamento: Porção pequena de batatas smiles ou batatinhas rústicas.\r\nBebida: Suco natural (laranja','28,90','674fc4f97f847.jpg',2,NULL),
(13,'Combo Kids 2','Mini Hambúrguer de Frango: Hambúrguer de frango grelhado, queijo prato e alface.\r\nAcompanhamento: Bolinhas de queijo.\r\nBebida: Água saborizada ou refrigerante natural (sem gás).\r\nBrinde: Máscaras de p','29,90','674fc5c8cf004.jpg',2,NULL),
(14,'Combo Veggie','Pão integral com gergelim, hambúrguer de grão-de-bico com cenoura e especiarias, queijo vegano, alface, tomate e cebola roxa, molho de tahine com limão\r\nAcompanhamento: Chips de batata-doce.\r\nBebida: ','33,90','674fc697bf89a.jpg',3,NULL),
(15,'Combo supreme','Pão brioche com gergelim, hambúrguer de carne bovina 180g, queijo provolone, cebola caramelizada, bacon crocante, molho barbecue caseiro\r\nAcompanhamento: Batatas fritas com cheddar e bacon.\r\nBebida: C','49,90','674fc6ffe77c1.jpg',3,NULL),
(16,'Batata Rusticas','Batatas cortadas em pedaços grandes, temperadas com alecrim, tomilho, alho assado e sal grosso.','12,90','674fc8571cdef.jpg',4,NULL),
(17,'Onion Rings','Cebola envoltas em uma massa temperada com páprica, alho em pó e ervas.','10,90','674fc8d87b418.jpg',4,NULL),
(18,'Nuggets','Pedaços de frango marinados, empanados com farinha panko e temperos','11,90','674fc9d4b1869.jpg',4,NULL),
(19,'Chips de Batata Doce','Lâminas finas de batata-doce assadas, temperadas com sal rosa e uma pitada de pimenta-do-reino','10,90','674fca1b6a619.jpg',4,NULL),
(20,'Refrigerante','Guarana, Kuat, Fanta laranja, Fanta uva, Coca cola e sprite','7,00','674fcc0617fa1.png',5,NULL),
(21,'Suco','Copo 350ml de suco de laranja, limão ou maracujá','6,00','674fcceadffd5.png',5,NULL),
(22,'Cerveja Artesanal','IPA, Pale Ale ou Lager','10,90','674fcd27094ef.jpg',5,NULL),
(23,'Milkshake','Chocolate belga, baunilha ou morango com pedaços de fruta, com chantilly e calda artesanal.','15,90','674fcf4a2ecbf.jpg',6,NULL),
(24,'Mini Churrus','Porção de mini churros frescos e crocantes, polvilhados com açúcar e canela, acompanhados de doce de leite cremoso ou ganache de chocolate.','15,90','674fcfc2c73aa.jpg',6,NULL),
(25,'Brownie com Sorvete','Brownie de chocolate meio amargo, servido quente, acompanhado de uma bola de sorvete de creme e calda de chocolate artesanal.','15.9','674fd059bda9c.jpg',1,NULL);
commit;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `itens_pedido` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_item` int(11) NOT NULL,
  `codigo_pedido` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `preco_unitario` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

set autocommit=0;
commit;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagamentos` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `tipos_pagamentos` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

set autocommit=0;
commit;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedidos` (
  `cod_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `datahora_pedido` datetime DEFAULT NULL,
  `total_pedidos` double DEFAULT NULL,
  `cod_status_pedidos` int(11) DEFAULT NULL,
  `fk_Usuario_codigo` int(11) DEFAULT NULL,
  `tipo_pagamento` varchar(20) NOT NULL,
  `quant_itens` int(11) NOT NULL,
  `cod_endereco` int(11) DEFAULT NULL,
  PRIMARY KEY (`cod_pedido`),
  KEY `FK_Pedidos_2` (`cod_status_pedidos`),
  KEY `FK_Pedidos_3` (`fk_Usuario_codigo`),
  CONSTRAINT `FK_Pedidos_2` FOREIGN KEY (`cod_status_pedidos`) REFERENCES `status_pedidos` (`cod_status_pedidos`),
  CONSTRAINT `FK_Pedidos_3` FOREIGN KEY (`fk_Usuario_codigo`) REFERENCES `usuario` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

set autocommit=0;
commit;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `status_pedidos` (
  `cod_status_pedidos` int(11) NOT NULL AUTO_INCREMENT,
  `status_pedidos` varchar(20) DEFAULT NULL,
  `hora_atualizacao` time DEFAULT NULL,
  PRIMARY KEY (`cod_status_pedidos`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

set autocommit=0;
INSERT INTO `status_pedidos` VALUES
(1,'PREPARANDO',NULL);
commit;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_usuario` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `tipos_usuario` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

set autocommit=0;
INSERT INTO `tipos_usuario` VALUES
(1,'administrador'),
(2,'cliente');
commit;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(254) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `fk_tipos_usuario_codigo` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`),
  KEY `FK_Usuario_2` (`fk_tipos_usuario_codigo`),
  CONSTRAINT `FK_Usuario_2` FOREIGN KEY (`fk_tipos_usuario_codigo`) REFERENCES `tipos_usuario` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

set autocommit=0;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
