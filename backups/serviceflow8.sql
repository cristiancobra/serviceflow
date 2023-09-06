-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Tempo de geração: 05/09/2023 às 00:48
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
-- Estrutura para tabela `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(6, 1, NULL, '2023-05-31 21:00:00', '2023-05-31 22:36:00', 5760, '2023-08-28 15:32:26', '2023-08-28 15:32:26'),
(8, 10, 'resumo', '2023-05-13 00:00:00', '2023-05-16 03:00:00', 270000, '2023-08-28 19:07:52', '2023-08-28 19:07:52'),
(9, 12, 'estimado', '2023-05-26 00:00:00', '2023-05-26 15:00:00', 54000, '2023-08-28 19:24:01', '2023-08-28 19:24:01'),
(10, 12, NULL, '2023-05-29 10:15:00', '2023-05-29 12:02:00', 6420, '2023-08-28 20:39:22', '2023-08-28 20:39:22'),
(11, 12, NULL, '2023-05-29 15:19:00', '2023-05-29 16:20:00', 3660, '2023-08-28 20:53:10', '2023-08-28 20:53:10'),
(12, 12, NULL, '2023-05-29 17:45:00', '2023-05-29 19:51:00', 7560, '2023-08-29 22:13:12', '2023-08-29 22:13:12'),
(13, 12, NULL, '2023-05-30 10:20:00', '2023-05-30 11:46:00', 5160, '2023-08-30 22:16:55', '2023-08-30 22:16:55'),
(14, 12, NULL, '2023-05-30 07:20:00', '2023-05-30 08:46:00', 5160, '2023-08-30 22:18:26', '2023-08-30 22:18:26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `leads`
--

CREATE TABLE `leads` (
  `id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cel_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_social_media` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_complement` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `neighborhood` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_date` date DEFAULT NULL,
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_contact_channel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason_for_initial_contact` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `comments` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
(3, 1, 1, 'Wendy Palo', 'wendypalo@gmail.com', '16992057388', NULL, NULL, NULL, NULL, 'Professor Eneas Camargo, 344', NULL, 'Vila Monteiro (Gleba I)', 'são carlos', 'SP', 'Brasil', '13560-301', '2023-07-26', 'contato pessoal', 'whatsapp', 'ISBN para livro Guia Para Abrir Relacionamento', NULL, '2023-08-13 01:27:00', '2023-08-13 01:27:51', NULL),
(4, 1, 1, 'Cassiana Boeno', NULL, '16 992919894', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-05-01', 'contato pessoal', 'whatsapp', 'Correções no wordpress Minha Cozinha', NULL, '2023-08-30 21:48:11', '2023-08-30 21:48:11', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2022_06_10_150443_create_projects_table', 1),
(6, '2022_07_15_182738_create_tasks_table', 1),
(7, '2023_06_04_004444_create_leads_table', 1),
(8, '2023_07_14_232832_create_services_table', 1),
(10, '2023_08_17_224607_create_journeys_table', 2),
(12, '2023_08_28_142407_add_duration_column_in_tasks_table', 3),
(14, '2023_08_28_142407_add_duration_time_column_in_tasks_table', 4),
(15, '2023_08_30_201520_add_duration_days_column_in_tasks_table', 4),
(17, '2023_09_01_205231_change_labour_hours_to_int_in_services_table', 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `projects`
--

CREATE TABLE `projects` (
  `id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `contact_id` bigint UNSIGNED DEFAULT NULL,
  `company_id` bigint UNSIGNED DEFAULT NULL,
  `goal_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_start` datetime DEFAULT NULL,
  `date_due` datetime DEFAULT NULL,
  `date_conclusion` datetime DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `services`
--

CREATE TABLE `services` (
  `id` bigint UNSIGNED NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `labor_hours` int NOT NULL,
  `labor_hourly_rate` decimal(8,2) NOT NULL,
  `labor_hourly_rate_total` decimal(8,2) NOT NULL,
  `profit_percentage` decimal(5,2) NOT NULL,
  `profit` decimal(8,2) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `observations` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `services`
--

INSERT INTO `services` (`id`, `category`, `name`, `labor_hours`, `labor_hourly_rate`, `labor_hourly_rate_total`, `profit_percentage`, `profit`, `price`, `observations`, `created_at`, `updated_at`, `deleted_at`) VALUES
(12, NULL, 'novo', 3600, 50.00, 50.00, 0.00, 5.56, 55.56, NULL, '2023-09-02 03:24:17', '2023-09-02 03:24:17', NULL),
(13, NULL, 'tete', 3600, 0.00, 0.00, 10.00, 0.00, 0.00, NULL, '2023-09-02 03:27:42', '2023-09-02 03:27:42', NULL),
(14, NULL, 'tete', 3600, 50.00, 50.00, 10.00, 5.56, 55.56, NULL, '2023-09-02 03:29:23', '2023-09-02 03:29:23', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `contact_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_id` bigint UNSIGNED DEFAULT NULL,
  `goal_id` bigint UNSIGNED DEFAULT NULL,
  `project_id` bigint UNSIGNED DEFAULT NULL,
  `stage_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `points` decimal(4,1) DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date_due` date DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_conclusion` date DEFAULT NULL,
  `duration_days` int DEFAULT NULL,
  `duration_time` int DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tasks`
--

INSERT INTO `tasks` (`id`, `account_id`, `user_id`, `contact_id`, `company_id`, `goal_id`, `project_id`, `stage_id`, `name`, `department`, `type`, `points`, `description`, `date_due`, `date_start`, `date_conclusion`, `duration_days`, `duration_time`, `status`, `priority`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '1', 1, NULL, 1, 1, 'criar site Cobra Sites e Sistemas', NULL, NULL, NULL, 'cria site portifolio', '2021-11-19', '2021-11-15', NULL, NULL, 54420, 'doing', 'medium', '2023-08-13 01:43:06', '2023-08-28 15:32:26', NULL),
(2, 1, 1, '1', 1, NULL, 1, 1, 'hospedagem Cobra Sites e Sistemas', NULL, NULL, NULL, 'configurar servidor para o site portifólio', '2021-11-19', '2021-11-15', NULL, NULL, NULL, 'wait', 'low', '2023-08-13 01:46:58', '2023-08-13 01:46:58', NULL),
(10, 1, 1, '1', 1, NULL, 1, 1, 'Gestao de usuarios  e login', NULL, NULL, NULL, 'Funcionalidade de usuarios no ERP do clinimap', '2023-05-19', '2023-04-13', NULL, NULL, 270000, 'done', 'high', '2023-08-28 18:36:40', '2023-08-28 19:07:52', NULL),
(12, 1, 1, '1', 1, NULL, 1, 1, 'Agendamento de pacientes', NULL, NULL, NULL, 'Funcionalidade agendamento no ERP do clinimap', '2023-06-22', '2023-05-22', NULL, NULL, 81960, 'done', 'high', '2023-08-28 18:51:52', '2023-08-30 22:18:26', NULL),
(13, 1, 1, '1', 1, NULL, 1, 1, 'Planilha de gestão administrativo-financeira', NULL, NULL, NULL, 'planilha para gerenciar todos os processos da empresa', '2023-07-01', '2023-06-01', NULL, NULL, NULL, 'to-do', 'high', '2023-08-28 18:58:21', '2023-08-28 18:58:21', NULL),
(14, 1, 1, '1', 1, NULL, 1, 1, 'Venda Yuri site SPFC Interior', NULL, NULL, NULL, NULL, '2023-05-26', '2023-05-26', NULL, NULL, NULL, 'done', 'high', '2023-08-28 18:59:38', '2023-08-28 18:59:38', NULL),
(15, 1, 1, '1', 1, NULL, 1, 1, 'parceria com miguel', NULL, NULL, NULL, 'acordo com o Miguel para oferecer serviços de criação de site para seus clientes', '2023-06-02', '2023-06-01', NULL, NULL, NULL, 'done', 'medium', '2023-08-28 19:03:19', '2023-08-28 19:03:19', NULL),
(16, 1, 1, '1', 1, NULL, 1, 1, 'abrir conta PJ com cartao de credito', NULL, NULL, NULL, NULL, '2023-06-02', '2023-06-02', NULL, NULL, NULL, 'done', 'medium', '2023-08-28 20:50:19', '2023-08-28 20:50:19', NULL),
(17, 1, 1, '1', 1, NULL, 1, 1, 'Criação de sistema ERP - modulo CRM básico', NULL, NULL, NULL, 'Criar ERP para realizar a gestão da empresa. Estrutura básica com os modulos contatos, tarefas, jornadas, oportunidades e serviços', '2023-12-31', '2023-06-02', NULL, NULL, NULL, 'doing', 'medium', '2023-08-30 20:50:59', '2023-08-30 20:50:59', NULL),
(18, 1, 1, '1', 1, NULL, 1, 1, 'Comprar domínio SPFCINTERIOR.com.br', NULL, NULL, NULL, NULL, '2023-06-06', '2023-06-06', '2023-06-06', 1, NULL, 'done', 'medium', '2023-08-30 21:01:19', '2023-08-30 21:01:19', NULL),
(19, 1, 1, '1', 1, NULL, 1, 1, 'criar droplet SPFC na digital ocean', NULL, NULL, NULL, 'configurar servidor na digital ocean', '2023-06-07', '2023-06-06', '2023-06-06', 1, NULL, 'done', 'high', '2023-08-30 21:10:04', '2023-08-30 21:10:04', NULL),
(20, 1, 1, '1', 1, NULL, 1, 1, 'criar site estilo blog SPFC INTERIOR', NULL, NULL, NULL, NULL, '2023-08-30', '2023-06-27', NULL, NULL, NULL, 'doing', 'low', '2023-08-30 21:18:40', '2023-08-30 21:18:40', NULL),
(22, 1, 1, '1', 1, NULL, 1, 1, 'postagens redes sociais junho', NULL, NULL, NULL, 'total das postagens de junho', '2023-06-30', '2023-06-01', '2023-06-30', 30, NULL, 'done', 'medium', '2023-08-30 21:25:40', '2023-08-30 21:25:40', NULL),
(23, 1, 1, '1', 1, NULL, 1, 1, 'Emitir nota gestao de logins e agendamentos 26 e 27', NULL, NULL, NULL, 'emitir nota para Clinimap', NULL, '2023-06-07', '2023-06-07', 1, NULL, 'done', 'medium', '2023-08-30 21:38:48', '2023-08-30 21:38:48', NULL),
(24, 1, 1, '1', 1, NULL, 1, 1, 'captação editais geral', NULL, NULL, NULL, NULL, NULL, '2023-06-19', '2023-06-19', 1, NULL, 'done', 'low', '2023-08-30 21:42:01', '2023-08-30 21:42:01', NULL),
(25, 1, 1, '1', 1, NULL, 1, 1, 'Venda wordpress cassiana', NULL, NULL, NULL, NULL, NULL, '2023-05-01', '2023-07-03', 64, NULL, 'done', 'medium', '2023-08-30 21:50:26', '2023-08-30 21:50:26', NULL),
(26, 1, 1, '1', 1, NULL, 1, 1, 'Venda ISBN Guia Abrir Relacionamento', NULL, NULL, NULL, NULL, NULL, '2023-06-23', '2023-06-24', 2, NULL, 'done', 'low', '2023-09-04 20:01:02', '2023-09-04 20:01:02', NULL),
(27, 1, 1, '1', 1, NULL, 1, 1, 'captação, respondendo linkedin', NULL, NULL, NULL, NULL, NULL, '2023-06-23', '2023-06-23', 1, NULL, 'done', 'low', '2023-09-04 20:05:34', '2023-09-04 20:05:34', NULL),
(28, 1, 1, '1', 1, NULL, 1, 1, 'CLINIMAP: Atendimento com anamnese, procedimentos e receitas', NULL, NULL, NULL, 'criar funcionalidades Atendimento com anamnese, procedimentos e receitas', NULL, '2023-06-22', '2023-06-29', 8, NULL, 'done', NULL, '2023-09-04 22:09:40', '2023-09-04 22:09:40', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Índices de tabela `journeys`
--
ALTER TABLE `journeys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journeys_task_id_foreign` (`task_id`);

--
-- Índices de tabela `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Índices de tabela `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Índices de tabela `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `journeys`
--
ALTER TABLE `journeys`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `leads`
--
ALTER TABLE `leads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

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
