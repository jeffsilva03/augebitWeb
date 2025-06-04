-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2025 at 12:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `semestral_3b`
--

-- --------------------------------------------------------

--
-- Table structure for table `cadastro`
--

CREATE TABLE `cadastro` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('usuario','usuarioGeral','instrutor','adm') NOT NULL DEFAULT 'usuarioGeral',
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `cadastro`
--

INSERT INTO `cadastro` (`id`, `nome`, `email`, `telefone`, `data_nascimento`, `bio`, `senha`, `perfil`, `avatar`) VALUES
(2, 'Jeff', 'jeffop801@gmail.com', NULL, NULL, NULL, '$2y$10$AZE4s8oea7tBH7yENfdq9.iBIhW.895ruF3HbayZzwjHDfjhiP3.K', 'adm', NULL),
(4, 'Marcos', 'instrutor@gmail.com', NULL, NULL, NULL, '$2y$10$JyGG.otg3m2pbXIrYIySDO0MyoLuxaFgiuaVuB.8..KbQxy/S0zI2', 'instrutor', NULL),
(12, 'mine', 'sousa@gmail.com', NULL, NULL, NULL, '$2b$10$1jD6n.ST6nsmLjbz7.k/6u1i0QdybTG6PsVYk1Y/DR.29ebKspS7m', 'usuarioGeral', NULL),
(13, 'caua', 'caua@gmail.com', '111111111', '0000-00-00', 'vd', '$2b$10$BK2SjHDDe1/X0pz3weQFQ.U2lXCvYDtzfV5d7pfLD2va0Zhi7.Lgi', 'usuarioGeral', NULL),
(14, 'VAS', 'vas@gmail.com', NULL, NULL, NULL, '$2y$10$fDel63gWM3k7suY5YA7nP.BtyCkZRi6efGnA67cRMDjXSVc933WL6', 'usuario', NULL),
(15, 'rafaela', 'rafa@gmail.com', NULL, NULL, NULL, '$2b$10$pWX2q.0Q7FXQnsHQQl1in.cKRA9JytLqbKMoaI02iLlK2yBthqf0G', 'usuarioGeral', NULL),
(16, 'AAAAAAAA', 'kakapagnani@gmail.com', '119661116163', '0000-00-00', '', '$2y$10$F/EcYrnIXk6Xc.MkscgrnedRBypjTBNAf1kr3GAsY2.4WpNN9vvXm', 'usuario', NULL),
(17, 'jefferson', 'jj@gmail.com', NULL, NULL, NULL, '$2b$10$IRq1rZwfp7VJFEtp6.Lf4O7ZAPWq5iRPofTBhvdInnETSHLtC.uv6', 'usuarioGeral', NULL),
(18, 'jeff', 'tz@gmail.com', NULL, NULL, NULL, '$2b$10$kwuVBo57fJBeLxxGXJ89eubXv2fe30JBa4g784Bjr3MOZ3KMW.ylS', 'usuarioGeral', NULL),
(20, 'PHP', 'php@gmail.com', NULL, NULL, NULL, '$2y$10$5IuxzfhanRYF4wCbc.R1aOz4VTTY49BKEPlO6Xvnam6.Q74bcXFXa', 'usuarioGeral', NULL),
(21, 'TESTEEEEEE', 'testeapp2@gmail.com', '11966116163', '0000-00-00', 'Eu jogo free fire', '$2b$10$.hPDj64wd5BgUVHequD02.qrVnQEtE4KneEAJXhXN0BnChTckvUDy', 'usuarioGeral', NULL),
(23, 'testee', 'testeapp23@gmail.com', '1199999999', NULL, 'w', '$2b$10$35Jr8CfQ3N/khekWFJ6qk.I1PK0Dp2ftzkFz0l7UYpH2IcOpTxb2K', 'usuarioGeral', NULL),
(24, 'Karina Pagnani', 'karinao@gmail.com', '11991045033', '0000-00-00', '', '$2b$10$sLlxJzx/IwACUrp9sjsveONpxAiuxLzwa35RK4D9aFhqPVpupqZKS', 'usuarioGeral', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contate_nos`
--

CREATE TABLE `contate_nos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mensagem` text NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contate_nos`
--

INSERT INTO `contate_nos` (`id`, `nome`, `email`, `mensagem`, `criado_em`) VALUES
(19, 'Jeff', 'jeffop801@gmail.com', 'TESTE', '2025-05-08 14:43:04'),
(20, 'yago dias', 'yagod@gmail.com', 'Bom dia, o sol já nasceu lá na fazendinha', '2025-05-09 12:34:05'),
(21, 'TESTE', 'TESTETESTETESTE@GMAIL.COM', 'TESTETESTE', '2025-05-09 19:25:44'),
(22, 'aaaaaaa', 'aaaaa', 'aaaaaaaaa', '2025-05-23 13:55:43'),
(23, 'd', 's', 's', '2025-05-23 13:56:43'),
(24, 'testehj', 'testehj@gmail.com', 'testehj', '2025-05-29 11:38:40');

-- --------------------------------------------------------

--
-- Table structure for table `conteudos`
--

CREATE TABLE `conteudos` (
  `id` int(11) NOT NULL,
  `modulo_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `corpo` text DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `instrutor_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `duration` varchar(10) DEFAULT NULL,
  `level` varchar(50) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `students` int(11) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `gradient` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gradient`)),
  `icon` varchar(100) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `cursos`
--

INSERT INTO `cursos` (`id`, `instrutor_id`, `titulo`, `descricao`, `duration`, `level`, `rating`, `students`, `category`, `gradient`, `icon`, `criado_em`) VALUES
(1, 4, 'CURSO TESTE', 'TESTE DE CURSO', '40h', 'Avançado', 4.8, 1240, 'tech', '[\"#8B5CF6\",\"#A855F7\"]', 'phone-portrait-outline', '2025-04-30 23:16:11'),
(19, 4, 'Flutter', 'FlutterFlutterFlutter', '2H', 'intermediate', 0.0, 1, 'health', '[\"#8a2be2\",\"#0e0b11\"]', '0', '2025-05-30 19:08:26');

-- --------------------------------------------------------

--
-- Table structure for table `inscricoes`
--

CREATE TABLE `inscricoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `inscrito_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `inscricoes`
--

INSERT INTO `inscricoes` (`id`, `usuario_id`, `curso_id`, `inscrito_em`) VALUES
(2, 4, 1, '2025-05-08 11:52:50'),
(4, 16, 1, '2025-05-16 15:18:06'),
(5, 24, 19, '2025-05-30 19:09:15');

-- --------------------------------------------------------

--
-- Table structure for table `modulos`
--

CREATE TABLE `modulos` (
  `id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cadastro`
--
ALTER TABLE `cadastro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `contate_nos`
--
ALTER TABLE `contate_nos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conteudos`
--
ALTER TABLE `conteudos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `modulo_id` (`modulo_id`);

--
-- Indexes for table `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `instrutor_id` (`instrutor_id`);

--
-- Indexes for table `inscricoes`
--
ALTER TABLE `inscricoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Indexes for table `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cadastro`
--
ALTER TABLE `cadastro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `contate_nos`
--
ALTER TABLE `contate_nos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `conteudos`
--
ALTER TABLE `conteudos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `inscricoes`
--
ALTER TABLE `inscricoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `conteudos`
--
ALTER TABLE `conteudos`
  ADD CONSTRAINT `conteudos_ibfk_1` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `cursos_ibfk_1` FOREIGN KEY (`instrutor_id`) REFERENCES `cadastro` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inscricoes`
--
ALTER TABLE `inscricoes`
  ADD CONSTRAINT `inscricoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `cadastro` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscricoes_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `modulos`
--
ALTER TABLE `modulos`
  ADD CONSTRAINT `modulos_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
