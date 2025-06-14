-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2025 at 02:53 AM
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

--
-- Dumping data for table `atividades`
--

INSERT INTO `atividades` (`id`, `tituloAtiv`, `questao1`, `descricao1`, `imagem1`, `questao2`, `descricao2`, `imagem2`, `questao3`, `descricao3`, `imagem3`, `questao4`, `descricao4`, `imagem4`, `questao5`, `descricao5`, `imagem5`, `questao6`, `descricao6`, `imagem6`, `questao7`, `descricao7`, `imagem7`, `questao8`, `descricao8`, `imagem8`, `questao9`, `descricao9`, `imagem9`, `questao10`, `descricao10`, `imagem10`, `curso_id`) VALUES
(29, 'TESTE', 'TESTE', 'TESTE', '', 'TESTE', 'TESTE', '', 'TESTE', 'TESTE', '', 'TESTE', 'TESTE', '', 'TESTE', 'TESTE', '', 'TESTE', 'TESTE', '', 'TESTE', 'TESTE', '', 'TESTE', 'TESTE', '', 'TESTE', 'TESTE', '', 'TESTE', 'TESTE', '', 1),
(30, 'TESTE2', 'TESTE2', 'TESTE2', '', 'TESTE2', 'TESTE2', '', 'TESTE2', 'TESTE2', '', 'TESTE2', 'TESTE2', '', 'TESTE2', 'TESTE2', '', 'TESTE2', 'TESTE2', '', 'TESTE2', 'TESTE2', '', 'TESTE2', 'TESTE2', '', 'TESTE2', 'TESTE2', '', 'TESTE2', 'TESTE2', '', 17);

-- --------------------------------------------------------

--
-- Table structure for table `aula`
--

CREATE TABLE `aula` (
  `id` int(11) NOT NULL,
  `videoAula` varchar(500) DEFAULT NULL,
  `curso_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aula`
--

INSERT INTO `aula` (`id`, `videoAula`, `curso_id`) VALUES
(1, '0', NULL),
(2, 'https://youtu.be/Fhy-5CtVkiM?si=6J0pJ5imfuHc6-Xl', 1),
(3, 'https://youtu.be/20WH5xB54Hc?si=OyqqvS1YGMLCVpBE', 17),
(4, 'https://youtu.be/0LB3FSfjvao?si=uUZKgzIYkv4td9NW', 20),
(6, 'https://youtu.be/gvkqT_Uoahw?si=u8xcj5LYa0XHoLEK', 19);

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

--
-- Dumping data for table `avaliacao`
--

INSERT INTO `avaliacao` (`id`, `curso`, `pergunta1`, `alternativaA1`, `alternativaB1`, `alternativaC1`, `alternativaD1`, `pergunta2`, `alternativaA2`, `alternativaB2`, `alternativaC2`, `alternativaD2`, `pergunta3`, `alternativaA3`, `alternativaB3`, `alternativaC3`, `alternativaD3`, `pergunta4`, `alternativaA4`, `alternativaB4`, `alternativaC4`, `alternativaD4`, `pergunta5`, `alternativaA5`, `alternativaB5`, `alternativaC5`, `alternativaD5`, `pergunta6`, `alternativaA6`, `alternativaB6`, `alternativaC6`, `alternativaD6`, `pergunta7`, `alternativaA7`, `alternativaB7`, `alternativaC7`, `alternativaD7`, `pergunta8`, `alternativaA8`, `alternativaB8`, `alternativaC8`, `alternativaD8`, `pergunta9`, `alternativaA9`, `alternativaB9`, `alternativaC9`, `alternativaD9`, `pergunta10`, `alternativaA10`, `alternativaB10`, `alternativaC10`, `alternativaD10`, `curso_id`, `resposta_correta1`, `resposta_correta2`, `resposta_correta3`, `resposta_correta4`, `resposta_correta5`, `resposta_correta6`, `resposta_correta7`, `resposta_correta8`, `resposta_correta9`, `resposta_correta10`) VALUES
(2, '', 'Qual é a função da tag <a> no HTML?', 'Exibir uma imagem   ', 'Criar um link', 'Inserir uma linha horizontal', 'Adicionar um botão', 'Qual propriedade CSS é usada para mudar a cor do texto?', 'background-color', 'color', 'text-align', 'font-style', 'O que a tag <img> faz?', 'Cria uma tabela', 'Adiciona um vídeo', 'Exibe uma imagem', 'Alinha o conteúdo ao centro', 'Qual é o atributo usado para definir o destino de um link em uma tag <a>?', 'href', 'target', 'link', 'src', 'Qual tag é usada para definir o título da página (que aparece na aba do navegador)?', '<meta>', '<h1>', '<header>', '<title>', 'Para alterar o plano de fundo de uma página, qual propriedade CSS é usada?', 'background-color', 'color', 'page-color', 'text-color', 'O que a propriedade margin controla em um elemento?', 'O espaço externo ao redor do elemento', 'A espessura da borda', 'O conteúdo interno', 'O espaçamento entre letras', 'Qual dessas propriedades CSS altera o tipo de fonte de um texto?', 'text-font', 'typeface', 'font-type', 'font-family', 'Qual atributo da tag <img> é usado para descrever a imagem para leitores de tela?', 'alt', 'label', ' title', 'desc', 'Como se aplica um estilo apenas a um elemento com id=\"banner\"?', '.banner {}', '#banner {}', '$banner {}', 'banner {}', 1, 'B', 'B', 'C', 'A', 'D', 'A', 'A', 'D', 'A', 'B'),
(3, '', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCDABCDABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 'ABCD', 17, 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A'),
(4, '', 'O que é o React Native?', 'Um framework para criar apps mobile usando JavaScript', 'Um framework para criar apps mobile usando PHP', 'Um framework para backend em Node.js', 'Um editor de texto', 'Qual componente é usado para exibir texto na tela?', '<Paragraph>', '<Text>', '<Div>', '<P>', 'Qual biblioteca é amplamente usada para navegação entre telas em React Native?', 'React DOM Navigator', 'React Native Pages', ' React Navigation Router', 'React Navigation', 'Para criar um botão que o usuário pode pressionar, usamos:', '<PressableButton>', '<ButtonClick>', '<TouchableOpacity>', '<TouchableOpa>', 'Qual comando roda o app no emulador?', 'expo run android', 'npx expo start', 'npm expo start', 'react-native android', 'O que é o StyleSheet.create() em React Native?', 'Um hook para estilizar componentes dinamicamente', 'Um método para criar páginas HTML', 'Um compilador de estilos externos', 'Uma forma de criar estilos em objetos JavaScript', 'Qual hook é usado para criar estado em componentes funcionais?', 'useState()', 'useFetch()', 'useStore()', 'useEffect()', ' Qual é o equivalente ao div do HTML no React Native?', '<Container>', '<Div>', '<View>', '<Box>', 'Qual extensão de arquivo é usada para componentes React Native?', '.rn', '.html', '.js', '.native', 'Qual comando instala uma biblioteca com o npm?', 'npm install nome-da-biblioteca', 'npm build nome-da-biblioteca', 'npm start nome-da-biblioteca', 'npm run nome-da-biblioteca', 19, 'A', 'B', 'D', 'C', 'B', 'D', 'A', 'C', 'C', 'A'),
(5, '', 'Qual é a extensão padrão de um arquivo Python?', '.pt', '.py**', '.pyt', '.pyth', 'Como se imprime algo na tela em Python?', 'echo(\"Olá\")', 'print(\"Olá\")', 'console.log(\"Olá\")', ' mostrar(\"Olá\")', 'Qual dessas é uma estrutura de repetição em Python?', 'repeat', 'repeat', 'for', 'do', 'Como se inicia um comentário de uma linha em Python?', '//', '<!--', '/*', '#', 'Qual função converte uma string em número inteiro?', 'string()', 'str()', 'float()', 'int()', 'Qual é o tipo de dado da variável: nome = \"Maria\"?', 'bool', 'int', 'list', 'str', 'Como se declara uma função em Python?', 'function minhaFuncao():', 'define minhaFuncao():', 'def minhaFuncao():', 'fun minhaFuncao():', 'Qual operador é usado para verificar igualdade em Python?', '!=', '=', '==', ':=', ' Qual estrutura é usada para tomar decisões (condições)?', 'when', 'if', 'choose', 'when', 'Qual das opções representa uma lista em Python?', '[1, 2, 3]', '{1, 2, 3}', '(1, 2, 3)', '<1, 2, 3>', 20, 'C', 'B', 'C', 'D', 'D', 'D', 'C', 'C', 'B', 'A');

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
(2, 'Marcos', 'adm@gmail.com', NULL, NULL, NULL, '$2y$10$AZE4s8oea7tBH7yENfdq9.iBIhW.895ruF3HbayZzwjHDfjhiP3.K', 'adm', NULL),
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
(24, 'Karina Pagnani', 'karinao@gmail.com', '11991045033', '0000-00-00', '', '$2b$10$sLlxJzx/IwACUrp9sjsveONpxAiuxLzwa35RK4D9aFhqPVpupqZKS', 'usuarioGeral', NULL),
(29, 'Jeff Silva', 'jeffteste@gmail.com', NULL, NULL, NULL, '$2y$10$3bSgyOT03XWMccOPkwtR4.7V4W4ThcLYXAQ29UueG5ZsMHY2ue8rG', 'usuarioGeral', NULL);

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
(1, 4, 'Desenvolvimento Web', 'Aprenda a criar sites e aplicações web completas com HTML5, CSS3, JavaScript, PHP, MySQL e mais.', '60h', '', 4.8, 1240, 'tech', '[\"#8b5cf6\",\"#a855f7\"]', 'fas fa-laptop-code', '2025-04-30 23:16:11'),
(19, 4, 'React Native', 'Faça aplicativos móveis completos para Android e iOS com React Native, JavaScript, Expo, APIs, banco de dados e muito mais.', '45h', 'intermediate', 5.0, 100, 'health', '[\"#8a2be2\",\"#0e0b11\"]', 'fas fa-laptop-code', '2025-05-30 19:08:26'),
(20, 4, 'Python', 'Aprenda a programar com Python do zero e desenvolva aplicações, automações, análise de dados, APIs, inteligência artificial e muito mais.', '70h', 'intermediate', 5.0, 50, 'tech', '[\"#8a2be2\",\"#0e0b11\"]', '0', '2025-06-13 20:14:09');

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
(5, 24, 19, '2025-05-30 19:09:15'),
(26, 29, 1, '2025-06-13 20:36:53'),
(27, 29, 20, '2025-06-13 20:43:24'),
(28, 29, 19, '2025-06-13 20:43:52');

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

--
-- Dumping data for table `newsletter`
--

INSERT INTO `newsletter` (`id`, `email`, `nome`, `inscrito_em`, `ativo`) VALUES
(1, 'jeff@gmail.com', 'Jefferson', '2025-06-06 12:35:04', 1),
(2, 'senaitchelo@gmail.com', 'Capa Azul', '2025-06-06 12:38:51', 1),
(3, 'ana2007@gmail.com', 'Ana luisa', '2025-06-06 12:41:13', 1),
(4, 'analusentz2007@gmail.com', 'ana', '2025-06-06 12:41:46', 1),
(5, 'jeffaosilva03@gmail.com', 'TESTE', '2025-06-06 12:45:02', 1),
(6, 'jeffop81@gmail.com', NULL, '2025-06-06 12:47:23', 1),
(9, 'jeffop8a01@gmail.com', 'kaka', '2025-06-06 13:24:55', 1),
(13, 'vitor.v.silva10@aluno.senai.br', 'vitor', '2025-06-06 13:55:21', 1),
(14, 'raelafaaa070@gmail.com', 'AAAAAAAAaf', '2025-06-06 13:57:48', 1),
(15, 'rafaelamatzak.senai@gmail.com', 'fgh', '2025-06-06 14:00:43', 1),
(16, 'je801@gmail.com', 'jeff', '2025-06-06 14:05:24', 1),
(17, 'w@gmail.com', 'Jefferson', '2025-06-06 14:08:21', 1),
(22, 'jeffop80s1@gmail.com', 'teste', '2025-06-06 14:46:36', 1),
(23, 'jeffop801@gmail.com', 'jeff', '2025-06-12 13:27:30', 1);

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

--
-- Dumping data for table `objetivo`
--

INSERT INTO `objetivo` (`id`, `objetivoCurso`, `conteudoCurso`, `curso_id`) VALUES
(0, 'Aprender os fundamentos da programação', 'Variáveis, loops, funções e estruturas de dados', 1);

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
-- Dumping data for table `respostas_avaliacao`
--

INSERT INTO `respostas_avaliacao` (`id`, `usuario_id`, `curso_id`, `resposta1`, `resposta2`, `resposta3`, `resposta4`, `resposta5`, `resposta6`, `resposta7`, `resposta8`, `resposta9`, `resposta10`, `nota`, `aprovado`, `data_realizacao`) VALUES
(1, 16, 1, 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 0.00, 0, '2025-06-13 14:18:26'),
(2, 16, 17, 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 0.00, 0, '2025-06-13 14:20:21'),
(3, 18, 17, 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 0.00, 0, '2025-06-13 14:29:34'),
(4, 18, 1, 'A', 'A', 'A', 'A', 'B', 'A', 'D', 'B', 'B', 'D', 0.00, 0, '2025-06-13 14:30:49'),
(5, 17, 17, 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 0.00, 0, '2025-06-13 14:34:07'),
(6, 17, 1, 'B', 'A', 'A', 'D', 'C', 'D', 'B', 'A', 'D', 'D', 0.00, 0, '2025-06-13 14:35:38'),
(7, 23, 17, 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 0.00, 0, '2025-06-13 14:52:52'),
(8, 23, 1, 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 0.00, 0, '2025-06-13 14:53:58'),
(9, 28, 1, 'A', 'B', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 9.00, 1, '2025-06-13 16:03:00'),
(10, 29, 1, 'B', 'B', 'C', 'A', 'D', 'A', 'A', 'D', 'A', 'B', 10.00, 1, '2025-06-13 23:37:34'),
(11, 29, 19, 'A', 'B', 'D', 'C', 'B', 'D', 'A', 'C', 'C', 'A', 10.00, 1, '2025-06-14 00:32:23');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `aula`
--
ALTER TABLE `aula`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `avaliacao`
--
ALTER TABLE `avaliacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cadastro`
--
ALTER TABLE `cadastro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `configuracao_avaliacao`
--
ALTER TABLE `configuracao_avaliacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contate_nos`
--
ALTER TABLE `contate_nos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `conteudos`
--
ALTER TABLE `conteudos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `inscricoes`
--
ALTER TABLE `inscricoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `respostas_avaliacao`
--
ALTER TABLE `respostas_avaliacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
