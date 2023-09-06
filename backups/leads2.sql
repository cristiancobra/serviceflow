-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Tempo de geração: 13/08/2023 às 01:28
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
-- Estrutura para tabela `leads`
--

CREATE TABLE `leads` (
  `id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cel_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_social_media` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_complement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `neighborhood` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_date` date DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_contact_channel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason_for_initial_contact` text COLLATE utf8mb4_unicode_ci,
  `comments` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `leads`
--

INSERT INTO `leads` (`id`, `account_id`, `user_id`, `name`, `email`, `cel_phone`, `linkedin`, `facebook`, `instagram`, `other_social_media`, `address`, `address_complement`, `neighborhood`, `city`, `state`, `country`, `zip_code`, `contact_date`, `source`, `source_contact_channel`, `reason_for_initial_contact`, `comments`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'Yuri Lavandoski Amato', 'amatople@gmail.com', '16981285359', NULL, NULL, NULL, NULL, 'Rua José fortuna, 275', NULL, 'Portal do Sol', 'são carlos', 'SP', 'Brasil', '13569450', '2023-05-26', 'contato pessoal', 'whatsapp', 'criação de site', NULL, '2023-08-13 01:12:16', '2023-08-13 01:12:16', NULL),
(2, 1, 1, 'Miguel Rodrigues dos Reis', 'contato@solucao360.com.br', '3591186709', 'https://www.linkedin.com/in/rodriguesmiguel/', 'https://www.facebook.com/migreis', NULL, NULL, 'Rua dos Contabilistas, 171', 'Sala 1', 'Belo Horizonte', 'passos', 'MG', 'brasil', '37900114', '2022-04-12', 'contato pessoal', 'whatsapp', 'programacao PHP', NULL, '2023-08-13 01:15:19', '2023-08-13 01:15:19', NULL),
(3, 1, 1, 'Wendy Palo', 'wendypalo@gmail.com', '16992057388', NULL, NULL, NULL, NULL, 'Professor Eneas Camargo, 344', NULL, 'Vila Monteiro (Gleba I)', 'são carlos', 'SP', 'Brasil', '13560-301', '2023-07-26', 'contato pessoal', 'whatsapp', 'ISBN para livro Guia Para Abrir Relacionamento', NULL, '2023-08-13 01:27:00', '2023-08-13 01:27:51', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `leads`
--
ALTER TABLE `leads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
