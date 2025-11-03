-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 03/11/2025 às 12:54
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `escola_alunos`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
--

CREATE TABLE `alunos` (
  `id` int(6) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `serie` varchar(20) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `alunos`
--

INSERT INTO `alunos` (`id`, `nome`, `email`, `telefone`, `data_nascimento`, `endereco`, `serie`, `data_criacao`, `data_atualizacao`) VALUES
(1, 'João Silva', 'joao@email.com', '(11) 9999-8888', '2005-03-15', 'Rua A, 123 - São Paulo', '1º Ano', '2025-11-03 11:39:10', '2025-11-03 11:39:10'),
(2, 'Maria Santos', 'maria@email.com', '(11) 7777-6666', '2004-07-22', 'Av. B, 456 - São Paulo', '2º Ano', '2025-11-03 11:39:10', '2025-11-03 11:39:10'),
(3, 'Pedro Oliveira', 'pedro@email.com', '(11) 5555-4444', '2003-11-30', 'Rua C, 789 - São Paulo', '3º Ano', '2025-11-03 11:39:10', '2025-11-03 11:39:10');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
