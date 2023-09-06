-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Tempo de geração: 28/08/2023 às 18:05
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
-- Estrutura para tabela `journeys`
--

CREATE TABLE `journeys` (
  `id` bigint UNSIGNED NOT NULL,
  `task_id` bigint UNSIGNED NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `journeys`
--

INSERT INTO `journeys` (`id`, `task_id`, `details`, `start`, `end`, `duration`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, '2023-05-24 12:00:00', '2023-05-24 16:00:00', 14400, '2023-08-28 13:18:49', '2023-08-28 13:18:49'),
(2, 1, NULL, '2023-05-26 00:00:00', '2023-05-26 04:00:00', 14400, '2023-08-28 13:42:28', '2023-08-28 13:42:28'),
(3, 1, NULL, '2023-05-27 11:40:00', '2023-05-27 12:20:00', 2400, '2023-08-28 13:58:04', '2023-08-28 13:58:04'),
(4, 1, NULL, '2023-05-31 17:30:00', '2023-05-31 20:45:00', 11700, '2023-08-28 15:25:44', '2023-08-28 15:25:44'),
(5, 1, NULL, '2023-06-01 00:00:00', '2023-06-01 01:36:00', 5760, '2023-08-28 15:28:35', '2023-08-28 15:28:35'),
(6, 1, NULL, '2023-05-31 21:00:00', '2023-05-31 22:36:00', 5760, '2023-08-28 15:32:26', '2023-08-28 15:32:26');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `journeys`
--
ALTER TABLE `journeys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journeys_task_id_foreign` (`task_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `journeys`
--
ALTER TABLE `journeys`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `journeys`
--
ALTER TABLE `journeys`
  ADD CONSTRAINT `journeys_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
