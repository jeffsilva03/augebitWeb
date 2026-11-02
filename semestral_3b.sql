-- Estrutura pública para desenvolvimento; sem dados de usuários, contatos ou senhas.
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
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
-- Table structure for table `atividades`
--

CREATE TABLE `atividades` (
  `id` int(11) NOT NULL,
  `tituloAtiv` varchar(255) NOT NULL,
  `questao1` varchar(500) NOT NULL,
  `descricao1` varchar(500) NOT NULL,
  `imagem1` varchar(225) NOT NULL,
  `questao2` varchar(255) NOT NULL,
  `descricao2` varchar(500) NOT NULL,
  `imagem2` varchar(500) NOT NULL,
  `questao3` varchar(500) NOT NULL,
  `descricao3` varchar(500) NOT NULL,
  `imagem3` varchar(500) NOT NULL,
  `questao4` varchar(500) NOT NULL,
  `descricao4` varchar(500) NOT NULL,
  `imagem4` varchar(500) NOT NULL,
  `questao5` varchar(500) NOT NULL,
  `descricao5` varchar(500) NOT NULL,
  `imagem5` varchar(500) NOT NULL,
  `questao6` varchar(500) NOT NULL,
  `descricao6` varchar(500) NOT NULL,
  `imagem6` varchar(500) NOT NULL,
  `questao7` varchar(500) NOT NULL,
  `descricao7` varchar(500) NOT NULL,
  `imagem7` varchar(500) NOT NULL,
  `questao8` varchar(500) NOT NULL,
  `descricao8` varchar(500) NOT NULL,
  `imagem8` varchar(500) NOT NULL,
  `questao9` varchar(500) NOT NULL,
  `descricao9` varchar(500) NOT NULL,
  `imagem9` varchar(500) NOT NULL,
  `questao10` varchar(500) NOT NULL,
  `descricao10` varchar(500) NOT NULL,
  `imagem10` varchar(500) NOT NULL,
  `curso_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `aula`
--

CREATE TABLE `aula` (
  `id` int(11) NOT NULL,
  `videoAula` varchar(500) DEFAULT NULL,
  `curso_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `avaliacao`
--

CREATE TABLE `avaliacao` (
  `id` int(11) NOT NULL,
  `curso` varchar(255) NOT NULL,
  `pergunta1` varchar(255) NOT NULL,
  `alternativaA1` varchar(500) NOT NULL,
  `alternativaB1` varchar(255) NOT NULL,
  `alternativaC1` varchar(255) NOT NULL,
  `alternativaD1` varchar(255) NOT NULL,
  `pergunta2` varchar(255) NOT NULL,
  `alternativaA2` varchar(255) NOT NULL,
  `alternativaB2` varchar(255) NOT NULL,
  `alternativaC2` varchar(255) NOT NULL,
  `alternativaD2` varchar(255) NOT NULL,
  `pergunta3` varchar(255) NOT NULL,
  `alternativaA3` varchar(255) NOT NULL,
  `alternativaB3` varchar(255) NOT NULL,
  `alternativaC3` varchar(255) NOT NULL,
  `alternativaD3` varchar(255) NOT NULL,
  `pergunta4` varchar(255) NOT NULL,
  `alternativaA4` varchar(255) NOT NULL,
  `alternativaB4` varchar(255) NOT NULL,
  `alternativaC4` varchar(255) NOT NULL,
  `alternativaD4` varchar(255) NOT NULL,
  `pergunta5` varchar(255) NOT NULL,
  `alternativaA5` varchar(255) NOT NULL,
  `alternativaB5` varchar(255) NOT NULL,
  `alternativaC5` varchar(255) NOT NULL,
  `alternativaD5` varchar(255) NOT NULL,
  `pergunta6` varchar(255) NOT NULL,
  `alternativaA6` varchar(255) NOT NULL,
  `alternativaB6` varchar(255) NOT NULL,
  `alternativaC6` varchar(255) NOT NULL,
  `alternativaD6` varchar(255) NOT NULL,
  `pergunta7` varchar(255) NOT NULL,
  `alternativaA7` varchar(255) NOT NULL,
  `alternativaB7` varchar(255) NOT NULL,
  `alternativaC7` varchar(255) NOT NULL,
  `alternativaD7` varchar(255) NOT NULL,
  `pergunta8` varchar(255) NOT NULL,
  `alternativaA8` varchar(255) NOT NULL,
  `alternativaB8` varchar(255) NOT NULL,
  `alternativaC8` varchar(255) NOT NULL,
  `alternativaD8` varchar(255) NOT NULL,
  `pergunta9` varchar(255) NOT NULL,
  `alternativaA9` varchar(255) NOT NULL,
  `alternativaB9` varchar(255) NOT NULL,
  `alternativaC9` varchar(255) NOT NULL,
  `alternativaD9` varchar(255) NOT NULL,
  `pergunta10` varchar(255) NOT NULL,
  `alternativaA10` varchar(255) NOT NULL,
  `alternativaB10` varchar(255) NOT NULL,
  `alternativaC10` varchar(255) NOT NULL,
  `alternativaD10` varchar(255) NOT NULL,
  `curso_id` int(11) DEFAULT NULL,
  `resposta_correta1` char(1) DEFAULT NULL,
  `resposta_correta2` char(1) DEFAULT NULL,
  `resposta_correta3` char(1) DEFAULT NULL,
  `resposta_correta4` char(1) DEFAULT NULL,
  `resposta_correta5` char(1) DEFAULT NULL,
  `resposta_correta6` char(1) DEFAULT NULL,
  `resposta_correta7` char(1) DEFAULT NULL,
  `resposta_correta8` char(1) DEFAULT NULL,
  `resposta_correta9` char(1) DEFAULT NULL,
  `resposta_correta10` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


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


-- --------------------------------------------------------

--
-- Table structure for table `configuracao_avaliacao`
--

CREATE TABLE `configuracao_avaliacao` (
  `id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `nota_minima` decimal(5,2) DEFAULT 70.00,
  `max_tentativas` int(11) DEFAULT 3,
  `tempo_limite` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `inscrito_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `objetivo`
--

CREATE TABLE `objetivo` (
  `id` int(11) NOT NULL,
  `objetivoCurso` text DEFAULT NULL,
  `conteudoCurso` text DEFAULT NULL,
  `curso_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `respostas_avaliacao`
--

CREATE TABLE `respostas_avaliacao` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `resposta1` varchar(1) DEFAULT NULL,
  `resposta2` varchar(1) DEFAULT NULL,
  `resposta3` varchar(1) DEFAULT NULL,
  `resposta4` varchar(1) DEFAULT NULL,
  `resposta5` varchar(1) DEFAULT NULL,
  `resposta6` varchar(1) DEFAULT NULL,
  `resposta7` varchar(1) DEFAULT NULL,
  `resposta8` varchar(1) DEFAULT NULL,
  `resposta9` varchar(1) DEFAULT NULL,
  `resposta10` varchar(1) DEFAULT NULL,
  `nota` decimal(4,2) DEFAULT NULL,
  `aprovado` tinyint(1) DEFAULT 0,
  `data_realizacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `atividades`
--
ALTER TABLE `atividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_atividades_curso_id` (`curso_id`);

--
-- Indexes for table `aula`
--
ALTER TABLE `aula`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_aula_curso_id` (`curso_id`);

--
-- Indexes for table `avaliacao`
--
ALTER TABLE `avaliacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_avaliacao_curso_id` (`curso_id`);

--
-- Indexes for table `cadastro`
--
ALTER TABLE `cadastro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `configuracao_avaliacao`
--
ALTER TABLE `configuracao_avaliacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `curso_id` (`curso_id`);

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
-- Indexes for table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `objetivo`
--
ALTER TABLE `objetivo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_objetivo_curso_id` (`curso_id`);

--
-- Indexes for table `respostas_avaliacao`
--
ALTER TABLE `respostas_avaliacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario_curso` (`usuario_id`,`curso_id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_curso_id` (`curso_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `atividades`
--
ALTER TABLE `atividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `aula`
--
ALTER TABLE `aula`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `avaliacao`
--
ALTER TABLE `avaliacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `cadastro`
--
ALTER TABLE `cadastro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `configuracao_avaliacao`
--
ALTER TABLE `configuracao_avaliacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contate_nos`
--
ALTER TABLE `contate_nos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `conteudos`
--
ALTER TABLE `conteudos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `inscricoes`
--
ALTER TABLE `inscricoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `respostas_avaliacao`
--
ALTER TABLE `respostas_avaliacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

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
