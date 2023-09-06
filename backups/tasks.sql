-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Tempo de geração: 13/08/2023 às 01:48
-- Versão do servidor: 8.0.32
-- Versão do PHP: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `serviceflow`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `contact_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_id` bigint UNSIGNED DEFAULT NULL,
  `goal_id` bigint UNSIGNED DEFAULT NULL,
  `project_id` bigint UNSIGNED DEFAULT NULL,
  `stage_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `points` decimal(4,1) DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `date_due` date DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_conclusion` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tasks`
--

INSERT INTO `tasks` (`id`, `account_id`, `user_id`, `contact_id`, `company_id`, `goal_id`, `project_id`, `stage_id`, `name`, `department`, `type`, `points`, `description`, `date_due`, `date_start`, `date_conclusion`, `status`, `priority`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '1', 1, NULL, 1, 1, 'criar site Cobra Sites e Sistemas', NULL, NULL, NULL, 'cria site portifolio', '2021-11-19', '2021-11-15', NULL, 'fazendo', NULL, '2023-08-13 01:43:06', '2023-08-13 01:43:06', NULL),
(2, 1, 1, '1', 1, NULL, 1, 1, 'hospedagem Cobra Sites e Sistemas', NULL, NULL, NULL, 'configurar servidor para o site portifólio', '2021-11-19', '2021-11-15', NULL, 'aguardar', NULL, '2023-08-13 01:46:58', '2023-08-13 01:46:58', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
