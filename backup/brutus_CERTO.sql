-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16/05/2025 às 13:19
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `brutus`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `cod_categoria` int(11) NOT NULL,
  `nome` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categoria`
--

INSERT INTO `categoria` (`cod_categoria`, `nome`) VALUES
(1, 'Hamburguer'),
(2, 'Kids'),
(3, 'Combos'),
(4, 'Acompanhamento'),
(5, 'Bebidas'),
(6, 'Sobremesa');

-- --------------------------------------------------------

--
-- Estrutura para tabela `endereco`
--

CREATE TABLE `endereco` (
  `cod_endereco` int(11) NOT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `rua` varchar(50) DEFAULT NULL,
  `bairro` varchar(20) DEFAULT NULL,
  `numero` int(6) DEFAULT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `cidade` varchar(15) DEFAULT NULL,
  `fk_Usuario_codigo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `endereco`
--

INSERT INTO `endereco` (`cod_endereco`, `cep`, `rua`, `bairro`, `numero`, `complemento`, `cidade`, `fk_Usuario_codigo`) VALUES
(5, '19907310', 'Rua Marechal Deodoro', 'Vila Sá', 310, '', 'Ourinhos', 6),
(6, '19907-310', 'Rua Marechal Deodoro', 'Vila Sá', 310, '', 'Ourinhos', 7),
(7, '11111-111', 'teste', 'teste', 0, '', 'Ourinhos', 8),
(8, '18990-138', 'Rua José Claudio Evangelista', 'Jardim Antonio Berna', 4, '', 'Ourinhos', 9);

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens`
--

CREATE TABLE `itens` (
  `cod_item` int(11) NOT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `descricao` varchar(200) DEFAULT NULL,
  `preco` varchar(10) DEFAULT NULL,
  `imagem` varchar(200) NOT NULL,
  `fk_Categoria_cod_categoria` int(11) DEFAULT NULL,
  `fk_Pedidos_cod_pedido` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `itens`
--

INSERT INTO `itens` (`cod_item`, `nome`, `descricao`, `preco`, `imagem`, `fk_Categoria_cod_categoria`, `fk_Pedidos_cod_pedido`) VALUES
(2, 'Burguer BBQ', 'Carne, cheddar, cebola caramelizada, bacon crocante e molho barbecue artesanal, servido no pão.', '32,00', 'burguerbbq.jpg', 1, NULL),
(6, 'Burguer do Chef', 'Carne de Angus, queijo gorgonzola, rúcula, cebola caramelizada e molho de mostarda, pão.', '34,00', 'burguerchef.png', 1, NULL),
(7, 'Brutu Bacon Especial', 'Blend de carnes premium, queijo cheddar derretido, fatias de bacon crocante, cebola caramelizada, alface, tomate,molho barbecue artesanal. ', '39,90', '674f68e2c0014.png', 1, NULL),
(8, 'Mini Brutu Cheese', 'Pão de brioche, hambúrguer artesanal de carne bovina, queijo cheddar derretido, ketchup.', '19,90', '674f6ab0e32ed.jpeg', 2, NULL),
(9, 'Kids Brutu', ' Mini Brutu Cheese (mini hambúrguer artesanal, queijo cheddar, ketchup, em pão brioche)', '19,90', '674f6bac0090e.jpg', 2, NULL),
(10, 'Combo Classico', '1 Brutu Cheese (hambúrguer artesanal com carne bovina, queijo cheddar derretido, alface, tomate e molho especial).\r\n1 porção de batatas rústicas com páprica.\r\n1 refrigerante ou suco à escolha.', '39,90', '674f6c223b77f.jpg', 3, NULL),
(11, ' Double Brutu Bacon', '1 Double Brutu Bacon (dois hambúrgueres artesanais, bacon crocante, queijo prato, cebola caramelizada e molho barbecue).\r\n1 porção de onion rings.\r\n1 milkshake (chocolate ou morango).', '59,90', '674f6c80b1e0d.jpg', 3, NULL),
(12, 'Combo kids', 'Mini Hambúrguer: Pão artesanal, hambúrguer de carne bovina, queijo derretido e ketchup caseiro.\r\nAcompanhamento: Porção pequena de batatas smiles ou batatinhas rústicas.\r\nBebida: Suco natural (laranja', '28,90', '674fc4f97f847.jpg', 2, NULL),
(13, 'Combo Kids 2', 'Mini Hambúrguer de Frango: Hambúrguer de frango grelhado, queijo prato e alface.\r\nAcompanhamento: Bolinhas de queijo.\r\nBebida: Água saborizada ou refrigerante natural (sem gás).\r\nBrinde: Máscaras de p', '29,90', '674fc5c8cf004.jpg', 2, NULL),
(14, 'Combo Veggie', 'Pão integral com gergelim, hambúrguer de grão-de-bico com cenoura e especiarias, queijo vegano, alface, tomate e cebola roxa, molho de tahine com limão\r\nAcompanhamento: Chips de batata-doce.\r\nBebida: ', '33,90', '674fc697bf89a.jpg', 3, NULL),
(15, 'Combo supreme', 'Pão brioche com gergelim, hambúrguer de carne bovina 180g, queijo provolone, cebola caramelizada, bacon crocante, molho barbecue caseiro\r\nAcompanhamento: Batatas fritas com cheddar e bacon.\r\nBebida: C', '49,90', '674fc6ffe77c1.jpg', 3, NULL),
(16, 'Batata Rusticas', 'Batatas cortadas em pedaços grandes, temperadas com alecrim, tomilho, alho assado e sal grosso.', '12,90', '674fc8571cdef.jpg', 4, NULL),
(17, 'Onion Rings', 'Cebola envoltas em uma massa temperada com páprica, alho em pó e ervas.', '10,90', '674fc8d87b418.jpg', 4, NULL),
(18, 'Nuggets', 'Pedaços de frango marinados, empanados com farinha panko e temperos', '11,90', '674fc9d4b1869.jpg', 4, NULL),
(19, 'Chips de Batata Doce', 'Lâminas finas de batata-doce assadas, temperadas com sal rosa e uma pitada de pimenta-do-reino', '10,90', '674fca1b6a619.jpg', 4, NULL),
(20, 'Refrigerante', 'Guarana, Kuat, Fanta laranja, Fanta uva, Coca cola e sprite', '7,00', '674fcc0617fa1.png', 5, NULL),
(21, 'Suco', 'Copo 350ml de suco de laranja, limão ou maracujá', '6,00', '674fcceadffd5.png', 5, NULL),
(22, 'Cerveja Artesanal', 'IPA, Pale Ale ou Lager', '10,90', '674fcd27094ef.jpg', 5, NULL),
(23, 'Milkshake', 'Chocolate belga, baunilha ou morango com pedaços de fruta, com chantilly e calda artesanal.', '15,90', '674fcf4a2ecbf.jpg', 6, NULL),
(24, 'Mini Churrus', 'Porção de mini churros frescos e crocantes, polvilhados com açúcar e canela, acompanhados de doce de leite cremoso ou ganache de chocolate.', '15,90', '674fcfc2c73aa.jpg', 6, NULL),
(25, 'Brownie com Sorvete', 'Brownie de chocolate meio amargo, servido quente, acompanhado de uma bola de sorvete de creme e calda de chocolate artesanal.', '15.9', '674fd059bda9c.jpg', 1, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `codigo` int(11) NOT NULL,
  `cod_item` int(11) NOT NULL,
  `cod_pedido` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `itens_pedido`
--

INSERT INTO `itens_pedido` (`codigo`, `cod_item`, `cod_pedido`, `quantidade`) VALUES
(1, 6, 8, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `codigo` int(11) NOT NULL,
  `tipos_pagamentos` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pagamentos`
--

INSERT INTO `pagamentos` (`codigo`, `tipos_pagamentos`) VALUES
(1, 'Dinheiro'),
(2, 'Cartão'),
(3, 'Pix');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `cod_pedido` int(11) NOT NULL,
  `datahora_pedido` datetime DEFAULT NULL,
  `total_pedidos` double DEFAULT NULL,
  `cod_status_pedidos` int(11) DEFAULT NULL,
  `fk_Usuario_codigo` int(11) DEFAULT NULL,
  `tipo_pagamento` int(11) NOT NULL,
  `quant_itens` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`cod_pedido`, `datahora_pedido`, `total_pedidos`, `cod_status_pedidos`, `fk_Usuario_codigo`, `tipo_pagamento`, `quant_itens`) VALUES
(6, NULL, 6800, 1, 6, 0, 2),
(7, NULL, 6800, 1, 6, 0, 2),
(8, NULL, 6800, 1, 6, 0, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `status_pedidos`
--

CREATE TABLE `status_pedidos` (
  `cod_status_pedidos` int(11) NOT NULL,
  `status_pedidos` varchar(20) DEFAULT NULL,
  `hora_atualizacao` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `status_pedidos`
--

INSERT INTO `status_pedidos` (`cod_status_pedidos`, `status_pedidos`, `hora_atualizacao`) VALUES
(1, 'PREPARANDO', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_usuario`
--

CREATE TABLE `tipos_usuario` (
  `codigo` int(11) NOT NULL,
  `tipos_usuario` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tipos_usuario`
--

INSERT INTO `tipos_usuario` (`codigo`, `tipos_usuario`) VALUES
(1, 'administrador'),
(2, 'cliente');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `codigo` int(11) NOT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `telefone` varchar(14) DEFAULT NULL,
  `senha` varchar(50) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `fk_tipos_usuario_codigo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`codigo`, `nome`, `email`, `telefone`, `senha`, `cpf`, `fk_tipos_usuario_codigo`) VALUES
(6, 'Caroline Gabriel', 'carolinemariagabriel@gmail.com', '(12) 12233-333', '81dc9bdb52d04dc20036dbd8313ed055', '25555555555', 2),
(7, 'Caroline Gabriel', 'tailacrs@gmail.com', '(12) 12233-333', '202cb962ac59075b964b07152d234b70', '255.555.555-55', 2),
(8, 'Administrador', 'admin@gmail.com', '(11) 11111-111', '81dc9bdb52d04dc20036dbd8313ed055', '111.111.111-11', 1),
(9, 'taila', 'tailacristtina@gmail.com', '(14) 99899-996', '81dc9bdb52d04dc20036dbd8313ed055', '493.616.548-45', 2);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`cod_categoria`);

--
-- Índices de tabela `endereco`
--
ALTER TABLE `endereco`
  ADD PRIMARY KEY (`cod_endereco`),
  ADD KEY `FK_Endereco_2` (`fk_Usuario_codigo`),
  ADD KEY `FK_Endereco_2_novo` (`fk_Usuario_codigo`);

--
-- Índices de tabela `itens`
--
ALTER TABLE `itens`
  ADD PRIMARY KEY (`cod_item`),
  ADD KEY `FK_Itens_2` (`fk_Categoria_cod_categoria`),
  ADD KEY `FK_Itens_3` (`fk_Pedidos_cod_pedido`);

--
-- Índices de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`cod_pedido`),
  ADD KEY `FK_Pedidos_2` (`cod_status_pedidos`),
  ADD KEY `FK_Pedidos_3` (`fk_Usuario_codigo`);

--
-- Índices de tabela `status_pedidos`
--
ALTER TABLE `status_pedidos`
  ADD PRIMARY KEY (`cod_status_pedidos`);

--
-- Índices de tabela `tipos_usuario`
--
ALTER TABLE `tipos_usuario`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `FK_Usuario_2` (`fk_tipos_usuario_codigo`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `cod_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `endereco`
--
ALTER TABLE `endereco`
  MODIFY `cod_endereco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `itens`
--
ALTER TABLE `itens`
  MODIFY `cod_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `cod_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `status_pedidos`
--
ALTER TABLE `status_pedidos`
  MODIFY `cod_status_pedidos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tipos_usuario`
--
ALTER TABLE `tipos_usuario`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `endereco`
--
ALTER TABLE `endereco`
  ADD CONSTRAINT `FK_Endereco_2` FOREIGN KEY (`fk_Usuario_codigo`) REFERENCES `usuario` (`codigo`);

--
-- Restrições para tabelas `itens`
--
ALTER TABLE `itens`
  ADD CONSTRAINT `FK_Itens_2` FOREIGN KEY (`fk_Categoria_cod_categoria`) REFERENCES `categoria` (`cod_categoria`),
  ADD CONSTRAINT `FK_Itens_3` FOREIGN KEY (`fk_Pedidos_cod_pedido`) REFERENCES `pedidos` (`cod_pedido`);

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `FK_Pedidos_2` FOREIGN KEY (`cod_status_pedidos`) REFERENCES `status_pedidos` (`cod_status_pedidos`),
  ADD CONSTRAINT `FK_Pedidos_3` FOREIGN KEY (`fk_Usuario_codigo`) REFERENCES `usuario` (`codigo`);

--
-- Restrições para tabelas `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `FK_Usuario_2` FOREIGN KEY (`fk_tipos_usuario_codigo`) REFERENCES `tipos_usuario` (`codigo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
