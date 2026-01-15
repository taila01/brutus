-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04/06/2025 às 23:03
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
  `fk_Usuario_codigo` int(11) DEFAULT NULL,
  `identificacao` varchar(50) NOT NULL,
  `principal` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `endereco`
--

INSERT INTO `endereco` (`cod_endereco`, `cep`, `rua`, `bairro`, `numero`, `complemento`, `cidade`, `fk_Usuario_codigo`, `identificacao`, `principal`) VALUES
(16, '19907-001', 'Rua José Claudio Evangelista', 'Jardim Antonio Berna', 2555, '', NULL, 13, 'Trabalho', 0),
(17, '19907-310', 'Rua Marechal Deodoro', 'Vila Sá', 2555, '23', 'Ourinhos', 14, '', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `hist_status_ped`
--

CREATE TABLE `hist_status_ped` (
  `cod_hist` int(11) NOT NULL,
  `cod_pedido` int(11) NOT NULL,
  `cod_status` int(11) NOT NULL,
  `data_hora` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `hist_status_ped`
--

INSERT INTO `hist_status_ped` (`cod_hist`, `cod_pedido`, `cod_status`, `data_hora`) VALUES
(4, 19, 1, '2025-06-04 01:58:01');

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
(30, 'Burguer BBQ', 'Carne, cheddar, cebola caramelizada, bacon crocante e molho barbecue artesanal, servido no pão.', '32', '683dc0f002fdc.jpg', 1, NULL),
(31, 'Burguer do Chef', 'Carne de Angus, queijo gorgonzola, rúcula, cebola caramelizada e molho de mostarda, pão.', '34', '683dc13c83624.png', 1, NULL),
(32, 'Brutu Bacon Especial', 'Blend de carnes premium, queijo cheddar derretido, fatias de bacon crocante, cebola caramelizada, alface, tomate,molho barbecue artesanal.', '39.9', '683dc19e9e66f.png', 1, NULL),
(33, 'Mini Brutu Cheese', 'Pão de brioche, hambúrguer artesanal de carne bovina, queijo cheddar derretido, ketchup.', '19.9', '683dc244d71bf.jpeg', 2, NULL),
(34, 'Kids Brutu', 'Mini hambúrguer artesanal, queijo cheddar, ketchup, em pão brioche.', '19.9', '683dc29674610.jpg', 2, NULL),
(35, 'Combo Classico', '1 Brutu Cheese (hambúrguer artesanal com carne bovina, queijo cheddar derretido, alface, tomate e molho especial). 1 porção de batatas rústicas com páprica. 1 refrigerante ou suco à escolha.', '39.9', '683dc2eb7eba4.jpg', 3, NULL),
(36, 'Double Brutu Bacon', '1 Double Brutu Bacon (dois hambúrgueres artesanais, bacon crocante, queijo prato, cebola caramelizada e molho barbecue). 1 porção de onion rings. 1 milkshake (chocolate ou morango).', '59.9', '683dc3265398a.jpg', 3, NULL),
(37, 'Combo kids', 'Mini Hambúrguer: Pão artesanal, hambúrguer de carne bovina, queijo derretido e ketchup caseiro. Acompanhamento: Porção pequena de batatas smiles ou batatinhas rústicas. Bebida: Suco natural de laranja', '28.9', '683dc37e361d7.jpg', 2, NULL),
(38, 'Combo Kids 2', 'Mini Hambúrguer de Frango: Hambúrguer de frango grelhado, queijo prato e alface. Acompanhamento: Bolinhas de queijo. Bebida: Água saborizada ou refrigerante natural (sem gás). Brinde: Máscaras de pers', '29.9', '683dc43388796.jpg', 2, NULL),
(39, 'Combo Veggie', 'Pão integral com gergelim, hambúrguer de grão-de-bico com cenoura e especiarias, queijo vegano, alface, tomate e cebola roxa, molho de tahine com limão\\r\\nAcompanhamento: Chips de batata-doce.\\r\\nBebi', '33.9', '683dc4967d868.jpg', 3, NULL),
(40, 'Combo supreme', 'Pão brioche com gergelim, hambúrguer de carne bovina 180g, queijo provolone, cebola caramelizada, bacon crocante, molho barbecue caseiro. Acompanhamento: Batatas fritas com cheddar e bacon. Bebida: Ch', '49.9', '683dc4ed0b32e.jpg', 3, NULL),
(41, 'Batata Rusticas', 'Batatas cortadas em pedaços grandes, temperadas com alecrim, tomilho, alho assado e sal grosso.', '12.9', '683dc51791fd2.jpg', 4, NULL),
(42, 'Onion Rings', 'Cebola envoltas em uma massa temperada com páprica, alho em pó e ervas.', '10.9', '683dc540e9e64.jpg', 4, NULL),
(43, 'Nuggets', 'Pedaços de frango marinados, empanados com farinha panko e temperos.', '11.9', '683dc57cdc776.jpg', 4, NULL),
(44, 'Chips de Batata Doce', 'Lâminas finas de batata-doce assadas, temperadas com sal rosa e uma pitada de pimenta-do-reino', '10.9', '683dc5ada6b6c.jpg', 4, NULL),
(45, 'Refrigerante', 'Guarana, Kuat, Fanta laranja, Fanta uva, Coca cola e sprite.', '7', '683dc5d17165b.png', 5, NULL),
(46, 'Suco', 'Copo 350ml de suco de laranja, limão ou maracujá.', '6', '683dc5f54b946.png', 5, NULL),
(47, 'Cerveja Artesanal', 'IPA, Pale Ale ou Lager', '10.9', '683dc61b739cc.jpg', 5, NULL),
(48, 'Milkshake', 'Chocolate belga, baunilha ou morango com pedaços de fruta, com chantilly e calda artesanal.', '15.9', '683dc640cca31.jpg', 6, NULL),
(49, 'Mini Churrus', 'Porção de mini churros frescos e crocantes, polvilhados com açúcar e canela, acompanhados de doce de leite cremoso ou ganache de chocolate.', '15.9', '683dc66d53acb.jpg', 6, NULL),
(50, 'Brownie com Sorvete', 'Brownie de chocolate meio amargo, servido quente, acompanhado de uma bola de sorvete de creme e calda de chocolate artesanal.', '15.9', '683dc6983dbbc.jpg', 6, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `codigo` int(11) NOT NULL,
  `cod_item` int(11) NOT NULL,
  `codigo_pedido` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `itens_pedido`
--

INSERT INTO `itens_pedido` (`codigo`, `cod_item`, `codigo_pedido`, `quantidade`) VALUES
(10, 34, 19, 1);

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
  `fk_Usuario_codigo` int(11) DEFAULT NULL,
  `tipo_pagamento` varchar(20) NOT NULL,
  `quant_itens` int(11) NOT NULL,
  `cod_endereco` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`cod_pedido`, `datahora_pedido`, `total_pedidos`, `fk_Usuario_codigo`, `tipo_pagamento`, `quant_itens`, `cod_endereco`) VALUES
(14, NULL, 48.8, 13, 'cartao', 2, 15),
(15, NULL, 48.8, 13, 'cartao', 2, 15),
(16, NULL, 0, 13, 'cartao', 0, 15),
(17, NULL, 0, 13, 'cartao', 0, 15),
(18, NULL, 34, 13, 'cartao', 1, 15),
(19, NULL, 19.9, 14, 'dinheiro', 1, 17);

-- --------------------------------------------------------

--
-- Estrutura para tabela `status_pedidos`
--

CREATE TABLE `status_pedidos` (
  `cod_status_pedidos` int(11) NOT NULL,
  `status_pedidos` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `status_pedidos`
--

INSERT INTO `status_pedidos` (`cod_status_pedidos`, `status_pedidos`) VALUES
(1, 'PREPARANDO'),
(2, 'PRONTO PARA RETIRADA'),
(3, 'CONCLUÍDO');

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
(1, 'Administrador', 'admin@gmail.com', '(11) 11111-111', '81dc9bdb52d04dc20036dbd8313ed055', '111.111.111-11', 1),
(13, 'Caroline Gabriel', 'caroline@gmail.com', '(14) 99856-471', '827ccb0eea8a706c4c34a16891f84e7b', '50628671857', 2),
(14, 'Taila Cristina', 'taila@gmail.com', '(14) 99856-471', '81dc9bdb52d04dc20036dbd8313ed055', '12345678958', 2);

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
-- Índices de tabela `hist_status_ped`
--
ALTER TABLE `hist_status_ped`
  ADD PRIMARY KEY (`cod_hist`),
  ADD KEY `cod_pedido_idx` (`cod_pedido`),
  ADD KEY `cod_status_pedidos__idx` (`cod_status`);

--
-- Índices de tabela `itens`
--
ALTER TABLE `itens`
  ADD PRIMARY KEY (`cod_item`),
  ADD KEY `FK_Itens_2` (`fk_Categoria_cod_categoria`);

--
-- Índices de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `cod_pedido_idx` (`codigo_pedido`),
  ADD KEY `idx_codigo_pedido` (`codigo_pedido`);

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
  MODIFY `cod_endereco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `hist_status_ped`
--
ALTER TABLE `hist_status_ped`
  MODIFY `cod_hist` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `itens`
--
ALTER TABLE `itens`
  MODIFY `cod_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `cod_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `status_pedidos`
--
ALTER TABLE `status_pedidos`
  MODIFY `cod_status_pedidos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `tipos_usuario`
--
ALTER TABLE `tipos_usuario`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `endereco`
--
ALTER TABLE `endereco`
  ADD CONSTRAINT `FK_Endereco_2` FOREIGN KEY (`fk_Usuario_codigo`) REFERENCES `usuario` (`codigo`);

--
-- Restrições para tabelas `hist_status_ped`
--
ALTER TABLE `hist_status_ped`
  ADD CONSTRAINT `cod_pedido` FOREIGN KEY (`cod_pedido`) REFERENCES `pedidos` (`cod_pedido`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `cod_status_pedidos_` FOREIGN KEY (`cod_status`) REFERENCES `status_pedidos` (`cod_status_pedidos`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `itens`
--
ALTER TABLE `itens`
  ADD CONSTRAINT `FK_Itens_2` FOREIGN KEY (`fk_Categoria_cod_categoria`) REFERENCES `categoria` (`cod_categoria`),
  ADD CONSTRAINT `FK_Itens_3` FOREIGN KEY (`fk_Pedidos_cod_pedido`) REFERENCES `pedidos` (`cod_pedido`);

--
-- Restrições para tabelas `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `fk_itens_pedido_cod_pedido` FOREIGN KEY (`codigo_pedido`) REFERENCES `pedidos` (`cod_pedido`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
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
