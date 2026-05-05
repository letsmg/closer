-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 02/03/2026 às 20:35
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
-- Banco de dados: `closer`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `bloqueios`
--

CREATE TABLE `bloqueios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perfil_id` bigint(20) UNSIGNED NOT NULL,
  `perfil_bloqueado_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cidades`
--

CREATE TABLE `cidades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `estado_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pais_code` varchar(3) NOT NULL DEFAULT 'BR',
  `geoname_id` bigint(20) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `cidades`
--

INSERT INTO `cidades` (`id`, `nome`, `display_name`, `estado_id`, `pais_code`, `geoname_id`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(1, 'São Paulo', 'São Paulo - SP (BR)', 25, 'BR', 3448439, -23.5475000, -46.6361100, '2026-02-26 16:05:33', '2026-02-26 16:05:33'),
(2, 'Rio de Janeiro', 'Rio de Janeiro - RJ (BR)', 19, 'BR', 3451190, -22.9064200, -43.1822300, '2026-02-26 16:05:33', '2026-02-26 16:05:33'),
(3, 'Monte Belo', 'Monte Belo - MG (BR)', 13, 'BR', 3456855, -21.3263900, -46.3675000, '2026-02-26 16:05:34', '2026-02-26 16:05:34');

-- --------------------------------------------------------

--
-- Estrutura para tabela `contratos_promocionais`
--

CREATE TABLE `contratos_promocionais` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nome_oferta` varchar(255) NOT NULL,
  `data_aceite` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_inicio` timestamp NULL DEFAULT NULL,
  `data_termino` timestamp NULL DEFAULT NULL,
  `cumprindo_regras` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `denuncias`
--

CREATE TABLE `denuncias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `denunciante_id` bigint(20) UNSIGNED NOT NULL,
  `denunciado_id` bigint(20) UNSIGNED NOT NULL,
  `motivo` enum('importunacao','desrespeito','perfil_falso','outro') NOT NULL,
  `descricao` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `emails_bloqueados`
--

CREATE TABLE `emails_bloqueados` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `hash_email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estados`
--

CREATE TABLE `estados` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `uf` varchar(5) NOT NULL,
  `pais_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `estados`
--

INSERT INTO `estados` (`id`, `nome`, `uf`, `pais_id`, `created_at`, `updated_at`) VALUES
(1, 'Acre', 'AC', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(2, 'Alagoas', 'AL', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(3, 'Amapá', 'AP', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(4, 'Amazonas', 'AM', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(5, 'Bahia', 'BA', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(6, 'Ceará', 'CE', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(7, 'Distrito Federal', 'DF', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(8, 'Espírito Santo', 'ES', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(9, 'Goiás', 'GO', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(10, 'Maranhão', 'MA', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(11, 'Mato Grosso', 'MT', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(12, 'Mato Grosso do Sul', 'MS', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(13, 'Minas Gerais', 'MG', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(14, 'Pará', 'PA', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(15, 'Paraíba', 'PB', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(16, 'Paraná', 'PR', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(17, 'Pernambuco', 'PE', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(18, 'Piauí', 'PI', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(19, 'Rio de Janeiro', 'RJ', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(20, 'Rio Grande do Norte', 'RN', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(21, 'Rio Grande do Sul', 'RS', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(22, 'Rondônia', 'RO', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(23, 'Roraima', 'RR', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(24, 'Santa Catarina', 'SC', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(25, 'São Paulo', 'SP', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(26, 'Sergipe', 'SE', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32'),
(27, 'Tocantins', 'TO', 1, '2026-02-26 16:05:32', '2026-02-26 16:05:32');

-- --------------------------------------------------------

--
-- Estrutura para tabela `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fotos_perfil`
--

CREATE TABLE `fotos_perfil` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `path` varchar(255) NOT NULL,
  `is_principal` tinyint(1) NOT NULL DEFAULT 0,
  `ordem` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `fotos_perfil`
--

INSERT INTO `fotos_perfil` (`id`, `user_id`, `path`, `is_principal`, `ordem`, `created_at`, `updated_at`) VALUES
(1, 1, 'fotos/user_1/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:38', '2026-02-26 16:05:38'),
(2, 1, 'fotos/user_1/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:38', '2026-02-26 16:05:38'),
(3, 1, 'fotos/user_1/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:39', '2026-02-26 16:05:39'),
(4, 2, 'fotos/user_2/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:39', '2026-02-26 16:05:39'),
(5, 2, 'fotos/user_2/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:39', '2026-02-26 16:05:39'),
(6, 2, 'fotos/user_2/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:39', '2026-02-26 16:05:39'),
(7, 3, 'fotos/user_3/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:40', '2026-02-26 16:05:40'),
(8, 3, 'fotos/user_3/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:40', '2026-02-26 16:05:40'),
(9, 3, 'fotos/user_3/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:40', '2026-02-26 16:05:40'),
(10, 4, 'fotos/user_4/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:40', '2026-02-26 16:05:40'),
(11, 4, 'fotos/user_4/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:40', '2026-02-26 16:05:40'),
(12, 4, 'fotos/user_4/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:41', '2026-02-26 16:05:41'),
(13, 5, 'fotos/user_5/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:41', '2026-02-26 16:05:41'),
(14, 5, 'fotos/user_5/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:41', '2026-02-26 16:05:41'),
(15, 5, 'fotos/user_5/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:41', '2026-02-26 16:05:41'),
(16, 6, 'fotos/user_6/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:41', '2026-02-26 16:05:41'),
(17, 6, 'fotos/user_6/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:42', '2026-02-26 16:05:42'),
(18, 6, 'fotos/user_6/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:42', '2026-02-26 16:05:42'),
(19, 7, 'fotos/user_7/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:42', '2026-02-26 16:05:42'),
(20, 7, 'fotos/user_7/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:42', '2026-02-26 16:05:42'),
(21, 7, 'fotos/user_7/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:43', '2026-02-26 16:05:43'),
(22, 8, 'fotos/user_8/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:43', '2026-02-26 16:05:43'),
(23, 8, 'fotos/user_8/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:43', '2026-02-26 16:05:43'),
(24, 8, 'fotos/user_8/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:43', '2026-02-26 16:05:43'),
(25, 9, 'fotos/user_9/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:43', '2026-02-26 16:05:43'),
(26, 9, 'fotos/user_9/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:44', '2026-02-26 16:05:44'),
(27, 9, 'fotos/user_9/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:44', '2026-02-26 16:05:44'),
(28, 10, 'fotos/user_10/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:44', '2026-02-26 16:05:44'),
(29, 10, 'fotos/user_10/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:44', '2026-02-26 16:05:44'),
(30, 10, 'fotos/user_10/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:44', '2026-02-26 16:05:44'),
(31, 11, 'fotos/user_11/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:45', '2026-02-26 16:05:45'),
(32, 11, 'fotos/user_11/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:45', '2026-02-26 16:05:45'),
(33, 11, 'fotos/user_11/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:45', '2026-02-26 16:05:45'),
(34, 12, 'fotos/user_12/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:45', '2026-02-26 16:05:45'),
(35, 12, 'fotos/user_12/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:46', '2026-02-26 16:05:46'),
(36, 12, 'fotos/user_12/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:46', '2026-02-26 16:05:46'),
(37, 13, 'fotos/user_13/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:46', '2026-02-26 16:05:46'),
(38, 13, 'fotos/user_13/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:46', '2026-02-26 16:05:46'),
(39, 13, 'fotos/user_13/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:46', '2026-02-26 16:05:46'),
(40, 14, 'fotos/user_14/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:47', '2026-02-26 16:05:47'),
(41, 14, 'fotos/user_14/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:47', '2026-02-26 16:05:47'),
(42, 14, 'fotos/user_14/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:47', '2026-02-26 16:05:47'),
(43, 15, 'fotos/user_15/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:47', '2026-02-26 16:05:47'),
(44, 15, 'fotos/user_15/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:48', '2026-02-26 16:05:48'),
(45, 15, 'fotos/user_15/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:48', '2026-02-26 16:05:48'),
(46, 16, 'fotos/user_16/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:48', '2026-02-26 16:05:48'),
(47, 16, 'fotos/user_16/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:48', '2026-02-26 16:05:48'),
(48, 16, 'fotos/user_16/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:49', '2026-02-26 16:05:49'),
(49, 17, 'fotos/user_17/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:49', '2026-02-26 16:05:49'),
(50, 17, 'fotos/user_17/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:49', '2026-02-26 16:05:49'),
(51, 17, 'fotos/user_17/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:49', '2026-02-26 16:05:49'),
(52, 18, 'fotos/user_18/seed_foto_1.jpg', 1, 1, '2026-02-26 16:05:49', '2026-02-26 16:05:49'),
(53, 18, 'fotos/user_18/seed_foto_2.jpg', 0, 2, '2026-02-26 16:05:50', '2026-02-26 16:05:50'),
(54, 18, 'fotos/user_18/seed_foto_3.jpg', 0, 3, '2026-02-26 16:05:50', '2026-02-26 16:05:50');

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_acessos`
--

CREATE TABLE `historico_acessos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `ip` varchar(45) NOT NULL,
  `dispositivo` varchar(255) DEFAULT NULL,
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `hobbies`
--

CREATE TABLE `hobbies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `categoria` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `hobbies`
--

INSERT INTO `hobbies` (`id`, `nome`, `categoria`, `created_at`, `updated_at`) VALUES
(1, 'Animes', 'Geek', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(2, 'Séries', 'Geek', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(3, 'Filmes', 'Geek', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(4, 'Quadrinhos', 'Geek', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(5, 'Cosplay', 'Geek', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(6, 'Tecnologia', 'Geek', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(7, 'Astronomia', 'Ciência', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(8, 'Biologia', 'Ciência', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(9, 'Química', 'Ciência', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(10, 'Física', 'Ciência', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(11, 'Matemática', 'Ciência', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(12, 'Futebol', 'Esporte', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(13, 'Academia', 'Esporte', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(14, 'Corrida', 'Esporte', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(15, 'Ciclismo', 'Esporte', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(16, 'Natação', 'Esporte', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(17, 'Yoga', 'Esporte', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(18, 'Crossfit', 'Esporte', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(19, 'Surf', 'Esporte', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(20, 'Cinema', 'Cultura', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(21, 'Teatro', 'Cultura', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(22, 'Museus', 'Cultura', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(23, 'Leitura', 'Cultura', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(24, 'Escrita', 'Cultura', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(25, 'Tocar Violão', 'Música', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(26, 'Cantar', 'Música', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(27, 'Shows', 'Música', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(28, 'Produção Musical', 'Música', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(29, 'Programação', 'Tecnologia', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(30, 'Games', 'Tecnologia', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(31, 'E-sports', 'Tecnologia', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(32, 'Criptomoedas', 'Tecnologia', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(33, 'IA', 'Tecnologia', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(34, 'Viagens', 'Social', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(35, 'Culinária', 'Social', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(36, 'Barzinho', 'Social', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(37, 'Café', 'Social', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(38, 'Voluntariado', 'Social', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(39, 'Trilhas', 'Outdoor', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(40, 'Camping', 'Outdoor', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(41, 'Escalada', 'Outdoor', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(42, 'Fotografia', 'Outdoor', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(43, 'Observação de Estrelas', 'Outdoor', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(44, 'Moda', 'Lifestyle', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(45, 'Maquiagem', 'Lifestyle', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(46, 'Pets', 'Lifestyle', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(47, 'Meditação', 'Lifestyle', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(48, 'Astrologia', 'Lifestyle', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(49, 'Investimentos', 'Lifestyle', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(50, 'Empreendedorismo', 'Lifestyle', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(51, 'Podcasts', 'Extra', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(52, 'Stand-up', 'Extra', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(53, 'Dança', 'Extra', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(54, 'Artesanato', 'Extra', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(55, 'Desenho', 'Extra', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(56, 'Pintura', 'Extra', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(57, 'TikTok', 'Extra', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(58, 'YouTube', 'Extra', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(59, 'Board Games', 'Extra', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(60, 'Carros', 'Extra', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(61, 'Motociclismo', 'Extra', '2026-02-26 16:05:34', '2026-02-26 16:05:34');

-- --------------------------------------------------------

--
-- Estrutura para tabela `idiomas`
--

CREATE TABLE `idiomas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(5) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `idiomas`
--

INSERT INTO `idiomas` (`id`, `codigo`, `nome`, `created_at`, `updated_at`) VALUES
(1, 'pt', 'Português', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(2, 'en', 'Inglês', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(3, 'es', 'Espanhol', '2026-02-26 16:05:34', '2026-02-26 16:05:34');

-- --------------------------------------------------------

--
-- Estrutura para tabela `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `likes`
--

CREATE TABLE `likes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `liked_user_id` bigint(20) UNSIGNED NOT NULL,
  `is_like` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `localidades_ocultas`
--

CREATE TABLE `localidades_ocultas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nome_localidade` varchar(255) NOT NULL,
  `tipo` enum('cidade','estado') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_match_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `conteudo` text NOT NULL,
  `lida` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_18_132146_create_personal_access_tokens_table', 1),
(5, '2026_02_18_133547_create_user_matches_table', 1),
(6, '2026_02_18_185100_create_emails_bloqueados_table', 1),
(7, '2026_02_18_185335_create_shorts_table', 1),
(8, '2026_02_18_194156_create_paises_table', 1),
(9, '2026_02_18_194221_create_estado_table', 1),
(10, '2026_02_18_194229_create_cidades_table', 1),
(11, '2026_02_18_200956_create_historico_acessos', 1),
(12, '2026_02_19_132924_create_perfis_table', 1),
(13, '2026_02_19_135627_create_fotos_perfil_table', 1),
(14, '2026_02_19_140142_create_perfis_preferencias_table', 1),
(15, '2026_02_19_180954_create_mensagens_table', 1),
(16, '2026_02_19_181907_create_bloqueios_table', 1),
(17, '2026_02_19_181926_create_denuncias_table', 1),
(18, '2026_02_19_205544_create_contratos_promocionais_table', 1),
(19, '2026_02_21_112926_create_idiomas_table', 1),
(20, '2026_02_21_112945_create_perfil_idiomas_table', 1),
(21, '2026_02_21_113002_create_perfil_idiomas_preferencias_table', 1),
(22, '2026_02_21_121838_create_hobbies_table', 1),
(23, '2026_02_21_121905_create_perfil_hobbies_table', 1),
(24, '2026_02_21_181359_create_perfil_hobbies_preferencias_table', 1),
(25, '2026_02_23_133408_create_likes_table', 1),
(26, '2026_02_23_135714_create_segundas_chances_table', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `paises`
--

CREATE TABLE `paises` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `sigla` varchar(5) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `paises`
--

INSERT INTO `paises` (`id`, `code`, `sigla`, `created_at`, `updated_at`) VALUES
(1, 'BR', 'BRA', '2026-02-26 16:05:32', '2026-02-26 16:05:32');

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfil_hobbies`
--

CREATE TABLE `perfil_hobbies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perfil_id` bigint(20) UNSIGNED NOT NULL,
  `hobby_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `perfil_hobbies`
--

INSERT INTO `perfil_hobbies` (`id`, `perfil_id`, `hobby_id`, `created_at`, `updated_at`) VALUES
(1, 1, 7, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(2, 1, 12, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(3, 1, 23, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(4, 2, 10, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(5, 2, 23, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(6, 2, 55, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(7, 3, 17, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(8, 3, 33, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(9, 3, 55, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(10, 4, 23, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(11, 4, 39, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(12, 4, 55, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(13, 5, 43, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(14, 5, 47, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(15, 5, 56, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(16, 6, 26, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(17, 6, 43, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(18, 6, 50, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(19, 7, 8, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(20, 7, 10, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(21, 7, 49, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(22, 8, 8, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(23, 8, 20, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(24, 8, 48, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(25, 9, 36, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(26, 9, 38, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(27, 9, 52, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(28, 10, 8, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(29, 10, 12, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(30, 10, 57, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(31, 11, 2, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(32, 11, 19, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(33, 11, 28, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(34, 12, 5, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(35, 12, 20, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(36, 12, 57, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(37, 13, 13, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(38, 13, 25, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(39, 13, 52, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(40, 14, 17, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(41, 14, 19, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(42, 14, 52, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(43, 15, 5, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(44, 15, 35, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(45, 15, 56, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(46, 16, 2, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(47, 16, 33, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(48, 16, 49, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(49, 17, 26, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(50, 17, 42, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(51, 17, 52, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(52, 18, 22, '2026-02-26 16:05:38', '2026-02-26 16:05:38'),
(53, 18, 33, '2026-02-26 16:05:38', '2026-02-26 16:05:38'),
(54, 18, 55, '2026-02-26 16:05:38', '2026-02-26 16:05:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfil_hobbies_preferencias`
--

CREATE TABLE `perfil_hobbies_preferencias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perfil_id` bigint(20) UNSIGNED NOT NULL,
  `hobby_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `perfil_hobbies_preferencias`
--

INSERT INTO `perfil_hobbies_preferencias` (`id`, `perfil_id`, `hobby_id`, `created_at`, `updated_at`) VALUES
(1, 1, 24, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(2, 1, 52, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(3, 2, 16, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(4, 2, 32, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(5, 3, 30, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(6, 3, 59, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(7, 4, 40, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(8, 4, 58, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(9, 5, 3, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(10, 5, 17, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(11, 6, 11, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(12, 6, 51, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(13, 7, 9, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(14, 7, 56, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(15, 8, 2, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(16, 8, 28, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(17, 9, 30, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(18, 9, 48, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(19, 10, 55, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(20, 10, 61, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(21, 11, 22, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(22, 11, 54, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(23, 12, 2, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(24, 12, 13, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(25, 13, 12, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(26, 13, 44, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(27, 14, 24, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(28, 14, 44, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(29, 15, 48, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(30, 15, 61, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(31, 16, 12, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(32, 16, 18, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(33, 17, 20, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(34, 17, 60, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(35, 18, 25, '2026-02-26 16:05:38', '2026-02-26 16:05:38'),
(36, 18, 40, '2026-02-26 16:05:38', '2026-02-26 16:05:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfil_idiomas`
--

CREATE TABLE `perfil_idiomas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perfil_id` bigint(20) UNSIGNED NOT NULL,
  `idioma_id` bigint(20) UNSIGNED NOT NULL,
  `nivel` enum('nativo','fluente','intermediario','basico') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `perfil_idiomas`
--

INSERT INTO `perfil_idiomas` (`id`, `perfil_id`, `idioma_id`, `nivel`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'intermediario', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(2, 1, 3, 'intermediario', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(3, 2, 1, 'intermediario', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(4, 2, 3, 'intermediario', '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(5, 3, 1, 'intermediario', '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(6, 3, 3, 'intermediario', '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(7, 4, 1, 'intermediario', '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(8, 4, 3, 'intermediario', '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(9, 5, 1, 'intermediario', '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(10, 5, 3, 'intermediario', '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(11, 6, 1, 'intermediario', '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(12, 6, 3, 'intermediario', '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(13, 7, 1, 'intermediario', '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(14, 7, 2, 'intermediario', '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(15, 8, 1, 'intermediario', '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(16, 8, 2, 'intermediario', '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(17, 9, 1, 'intermediario', '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(18, 9, 2, 'intermediario', '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(19, 10, 1, 'intermediario', '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(20, 10, 2, 'intermediario', '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(21, 11, 2, 'intermediario', '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(22, 11, 3, 'intermediario', '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(23, 12, 2, 'intermediario', '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(24, 12, 3, 'intermediario', '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(25, 13, 1, 'intermediario', '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(26, 13, 3, 'intermediario', '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(27, 14, 1, 'intermediario', '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(28, 14, 2, 'intermediario', '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(29, 15, 1, 'intermediario', '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(30, 15, 2, 'intermediario', '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(31, 16, 1, 'intermediario', '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(32, 16, 3, 'intermediario', '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(33, 17, 1, 'intermediario', '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(34, 17, 2, 'intermediario', '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(35, 18, 1, 'intermediario', '2026-02-26 16:05:38', '2026-02-26 16:05:38'),
(36, 18, 3, 'intermediario', '2026-02-26 16:05:38', '2026-02-26 16:05:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfil_idiomas_preferencias`
--

CREATE TABLE `perfil_idiomas_preferencias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perfil_id` bigint(20) UNSIGNED NOT NULL,
  `idioma_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `perfil_idiomas_preferencias`
--

INSERT INTO `perfil_idiomas_preferencias` (`id`, `perfil_id`, `idioma_id`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(2, 2, 1, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(3, 3, 1, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(4, 4, 1, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(5, 5, 1, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(6, 6, 1, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(7, 7, 3, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(8, 8, 3, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(9, 9, 2, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(10, 10, 1, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(11, 11, 1, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(12, 12, 1, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(13, 13, 3, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(14, 14, 2, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(15, 15, 3, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(16, 16, 1, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(17, 17, 2, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(18, 18, 1, '2026-02-26 16:05:38', '2026-02-26 16:05:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfis`
--

CREATE TABLE `perfis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `apelido` varchar(255) DEFAULT NULL,
  `data_nascimento` date NOT NULL,
  `sexo` enum('masculino','feminino','nao_binario','outro') NOT NULL,
  `identidade_genero` varchar(255) NOT NULL,
  `orientacao_sexual` varchar(255) NOT NULL,
  `objetivo` enum('serio','casual','amizade','networking','todos') NOT NULL,
  `profissao` varchar(255) DEFAULT NULL,
  `biografia` text DEFAULT NULL,
  `fumante` tinyint(1) NOT NULL,
  `bebida` tinyint(1) NOT NULL,
  `estado_civil` enum('solteiro','casado','divorciado','viuvo','relacionamento_aberto') DEFAULT NULL,
  `pais_id` bigint(20) UNSIGNED DEFAULT NULL,
  `estado_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cidade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visibilidade` enum('publico','somente_match','invisivel') NOT NULL DEFAULT 'publico',
  `nivel_suspeito` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0: baixo, 1: medio, 2: alto',
  `pontos_likes` int(11) NOT NULL DEFAULT 0,
  `pontos_ghosting` int(11) NOT NULL DEFAULT 0,
  `pontos_bloqueio` int(11) NOT NULL DEFAULT 0,
  `pontos_viajante` int(11) NOT NULL DEFAULT 0,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `perfis`
--

INSERT INTO `perfis` (`id`, `user_id`, `apelido`, `data_nascimento`, `sexo`, `identidade_genero`, `orientacao_sexual`, `objetivo`, `profissao`, `biografia`, `fumante`, `bebida`, `estado_civil`, `pais_id`, `estado_id`, `cidade_id`, `visibilidade`, `nivel_suspeito`, `pontos_likes`, `pontos_ghosting`, `pontos_bloqueio`, `pontos_viajante`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(1, 1, 'Apelido Plus 1', '1999-02-26', 'feminino', 'Mulher Cis', 'Hetero', 'serio', 'Profissão Plus 1', 'Biografia do usuário Plus 1. Gosta de viajar e conhecer pessoas novas.', 1, 0, 'solteiro', 1, 25, 1, 'publico', 0, 0, 0, 0, 0, -23.5475000, -46.6361100, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(2, 2, 'Apelido Plus 2', '1990-02-26', 'masculino', 'Homem Cis', 'Hetero', 'serio', 'Profissão Plus 2', 'Biografia do usuário Plus 2. Gosta de viajar e conhecer pessoas novas.', 1, 1, 'solteiro', 1, 25, 1, 'somente_match', 0, 0, 0, 0, 0, -23.5475000, -46.6361100, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(3, 3, 'Apelido Premium 3', '1999-02-26', 'feminino', 'Mulher Cis', 'Hetero', 'serio', 'Profissão Premium 3', 'Biografia do usuário Premium 3. Gosta de viajar e conhecer pessoas novas.', 0, 0, 'solteiro', 1, 25, 1, 'invisivel', 0, 0, 0, 0, 0, -23.5475000, -46.6361100, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(4, 4, 'Apelido Free 4', '1993-02-26', 'feminino', 'Mulher Cis', 'Hetero', 'serio', 'Profissão Free 4', 'Biografia do usuário Free 4. Gosta de viajar e conhecer pessoas novas.', 0, 0, 'solteiro', 1, 25, 1, 'publico', 0, 0, 0, 0, 0, -23.5475000, -46.6361100, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(5, 5, 'Apelido Free 5', '2002-02-26', 'masculino', 'Homem Cis', 'Hetero', 'serio', 'Profissão Free 5', 'Biografia do usuário Free 5. Gosta de viajar e conhecer pessoas novas.', 0, 0, 'solteiro', 1, 25, 1, 'invisivel', 0, 0, 0, 0, 0, -23.5475000, -46.6361100, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(6, 6, 'Apelido Free 6', '2000-02-26', 'feminino', 'Mulher Cis', 'Hetero', 'serio', 'Profissão Free 6', 'Biografia do usuário Free 6. Gosta de viajar e conhecer pessoas novas.', 0, 1, 'solteiro', 1, 25, 1, 'invisivel', 0, 0, 0, 0, 0, -23.5475000, -46.6361100, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(7, 7, 'Apelido Premium 1', '1999-02-26', 'masculino', 'Homem Cis', 'Hetero', 'serio', 'Profissão Premium 1', 'Biografia do usuário Premium 1. Gosta de viajar e conhecer pessoas novas.', 1, 0, 'solteiro', 1, 19, 2, 'invisivel', 0, 0, 0, 0, 0, -22.9064200, -43.1822300, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(8, 8, 'Apelido Plus 2', '2007-02-26', 'feminino', 'Mulher Cis', 'Hetero', 'serio', 'Profissão Plus 2', 'Biografia do usuário Plus 2. Gosta de viajar e conhecer pessoas novas.', 0, 0, 'solteiro', 1, 19, 2, 'invisivel', 0, 0, 0, 0, 0, -22.9064200, -43.1822300, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(9, 9, 'Apelido Plus 3', '1992-02-26', 'masculino', 'Homem Cis', 'Hetero', 'serio', 'Profissão Plus 3', 'Biografia do usuário Plus 3. Gosta de viajar e conhecer pessoas novas.', 0, 1, 'solteiro', 1, 19, 2, 'somente_match', 0, 0, 0, 0, 0, -22.9064200, -43.1822300, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(10, 10, 'Apelido Free 4', '1987-02-26', 'feminino', 'Mulher Cis', 'Hetero', 'serio', 'Profissão Free 4', 'Biografia do usuário Free 4. Gosta de viajar e conhecer pessoas novas.', 1, 1, 'solteiro', 1, 19, 2, 'somente_match', 0, 0, 0, 0, 0, -22.9064200, -43.1822300, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(11, 11, 'Apelido Premium 5', '1988-02-26', 'masculino', 'Homem Cis', 'Hetero', 'serio', 'Profissão Premium 5', 'Biografia do usuário Premium 5. Gosta de viajar e conhecer pessoas novas.', 0, 0, 'solteiro', 1, 19, 2, 'invisivel', 0, 0, 0, 0, 0, -22.9064200, -43.1822300, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(12, 12, 'Apelido Free 6', '1995-02-26', 'masculino', 'Homem Cis', 'Hetero', 'serio', 'Profissão Free 6', 'Biografia do usuário Free 6. Gosta de viajar e conhecer pessoas novas.', 1, 1, 'solteiro', 1, 19, 2, 'somente_match', 0, 0, 0, 0, 0, -22.9064200, -43.1822300, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(13, 13, 'Apelido Premium 1', '1994-02-26', 'feminino', 'Mulher Cis', 'Hetero', 'serio', 'Profissão Premium 1', 'Biografia do usuário Premium 1. Gosta de viajar e conhecer pessoas novas.', 0, 1, 'solteiro', 1, 13, 3, 'invisivel', 0, 0, 0, 0, 0, -21.3263900, -46.3675000, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(14, 14, 'Apelido Free 2', '1988-02-26', 'feminino', 'Mulher Cis', 'Hetero', 'serio', 'Profissão Free 2', 'Biografia do usuário Free 2. Gosta de viajar e conhecer pessoas novas.', 0, 1, 'solteiro', 1, 13, 3, 'invisivel', 0, 0, 0, 0, 0, -21.3263900, -46.3675000, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(15, 15, 'Apelido Plus 3', '1990-02-26', 'masculino', 'Homem Cis', 'Hetero', 'serio', 'Profissão Plus 3', 'Biografia do usuário Plus 3. Gosta de viajar e conhecer pessoas novas.', 1, 1, 'solteiro', 1, 13, 3, 'publico', 0, 0, 0, 0, 0, -21.3263900, -46.3675000, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(16, 16, 'Apelido Premium 4', '2003-02-26', 'feminino', 'Mulher Cis', 'Hetero', 'serio', 'Profissão Premium 4', 'Biografia do usuário Premium 4. Gosta de viajar e conhecer pessoas novas.', 0, 0, 'solteiro', 1, 13, 3, 'publico', 0, 0, 0, 0, 0, -21.3263900, -46.3675000, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(17, 17, 'Apelido Plus 5', '1999-02-26', 'masculino', 'Homem Cis', 'Hetero', 'serio', 'Profissão Plus 5', 'Biografia do usuário Plus 5. Gosta de viajar e conhecer pessoas novas.', 0, 0, 'solteiro', 1, 13, 3, 'invisivel', 0, 0, 0, 0, 0, -21.3263900, -46.3675000, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(18, 18, 'Apelido Free 6', '2003-02-26', 'feminino', 'Mulher Cis', 'Hetero', 'serio', 'Profissão Free 6', 'Biografia do usuário Free 6. Gosta de viajar e conhecer pessoas novas.', 0, 0, 'solteiro', 1, 13, 3, 'somente_match', 0, 0, 0, 0, 0, -21.3263900, -46.3675000, '2026-02-26 16:05:38', '2026-02-26 16:05:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfis_preferencias`
--

CREATE TABLE `perfis_preferencias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perfil_id` bigint(20) UNSIGNED NOT NULL,
  `sexo` varchar(255) DEFAULT NULL,
  `identidade_genero` varchar(255) DEFAULT NULL,
  `orientacao_sexual` varchar(255) DEFAULT NULL,
  `objetivo` enum('serio','casual','amizade','networking','todos') DEFAULT NULL,
  `fumante` tinyint(1) DEFAULT NULL,
  `bebida` tinyint(1) DEFAULT NULL,
  `estado_civil` varchar(255) DEFAULT NULL,
  `pais_id` bigint(20) UNSIGNED DEFAULT NULL,
  `estado_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cidade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `raio_busca_km` int(11) NOT NULL DEFAULT 50,
  `idade_minima` tinyint(3) UNSIGNED DEFAULT NULL,
  `idade_maxima` tinyint(3) UNSIGNED DEFAULT NULL,
  `visibilidade` enum('publico','invisivel','somente_match') NOT NULL DEFAULT 'publico',
  `permitir_busca_global` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `perfis_preferencias`
--

INSERT INTO `perfis_preferencias` (`id`, `perfil_id`, `sexo`, `identidade_genero`, `orientacao_sexual`, `objetivo`, `fumante`, `bebida`, `estado_civil`, `pais_id`, `estado_id`, `cidade_id`, `raio_busca_km`, `idade_minima`, `idade_maxima`, `visibilidade`, `permitir_busca_global`, `created_at`, `updated_at`) VALUES
(1, 1, 'masculino', NULL, 'Assexual', 'serio', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(2, 2, 'feminino', NULL, 'Hetero', 'serio', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(3, 3, 'masculino', NULL, 'Assexual', 'casual', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(4, 4, 'masculino', NULL, 'Hetero', 'todos', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(5, 5, 'feminino', NULL, 'Homosexual', 'todos', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(6, 6, 'masculino', NULL, 'Assexual', 'networking', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(7, 7, 'feminino', NULL, 'Assexual', 'networking', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(8, 8, 'masculino', NULL, 'Assexual', 'todos', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(9, 9, 'feminino', NULL, 'Homosexual', 'casual', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(10, 10, 'masculino', NULL, 'Assexual', 'serio', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(11, 11, 'feminino', NULL, 'Hetero', 'todos', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(12, 12, 'feminino', NULL, 'Bissexual', 'amizade', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(13, 13, 'masculino', NULL, 'Hetero', 'amizade', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(14, 14, 'masculino', NULL, 'Assexual', 'casual', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(15, 15, 'feminino', NULL, 'Homosexual', 'amizade', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(16, 16, 'masculino', NULL, 'Hetero', 'networking', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(17, 17, 'feminino', NULL, 'Assexual', 'serio', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(18, 18, 'masculino', NULL, 'Hetero', 'casual', NULL, NULL, NULL, NULL, NULL, NULL, 50, 18, 45, 'publico', 0, '2026-02-26 16:05:38', '2026-02-26 16:05:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `segundas_chances`
--

CREATE TABLE `segundas_chances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perfil_id` bigint(20) UNSIGNED NOT NULL,
  `like_id` bigint(20) UNSIGNED NOT NULL,
  `usado_em` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `shorts`
--

CREATE TABLE `shorts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tipo` enum('q','r') NOT NULL,
  `conteudo` text NOT NULL,
  `posicao` tinyint(3) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `shorts`
--

INSERT INTO `shorts` (`id`, `user_id`, `tipo`, `conteudo`, `posicao`, `created_at`, `updated_at`) VALUES
(1, 1, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:38', '2026-02-26 16:05:38'),
(2, 1, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:38', '2026-02-26 16:05:38'),
(3, 1, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:38', '2026-02-26 16:05:38'),
(4, 1, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:38', '2026-02-26 16:05:38'),
(5, 2, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:39', '2026-02-26 16:05:39'),
(6, 2, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:39', '2026-02-26 16:05:39'),
(7, 2, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:39', '2026-02-26 16:05:39'),
(8, 2, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:39', '2026-02-26 16:05:39'),
(9, 3, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:39', '2026-02-26 16:05:39'),
(10, 3, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:39', '2026-02-26 16:05:39'),
(11, 3, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:39', '2026-02-26 16:05:39'),
(12, 3, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:39', '2026-02-26 16:05:39'),
(13, 4, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:40', '2026-02-26 16:05:40'),
(14, 4, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:40', '2026-02-26 16:05:40'),
(15, 4, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:40', '2026-02-26 16:05:40'),
(16, 4, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:40', '2026-02-26 16:05:40'),
(17, 5, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:41', '2026-02-26 16:05:41'),
(18, 5, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:41', '2026-02-26 16:05:41'),
(19, 5, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:41', '2026-02-26 16:05:41'),
(20, 5, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:41', '2026-02-26 16:05:41'),
(21, 6, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:41', '2026-02-26 16:05:41'),
(22, 6, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:41', '2026-02-26 16:05:41'),
(23, 6, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:41', '2026-02-26 16:05:41'),
(24, 6, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:41', '2026-02-26 16:05:41'),
(25, 7, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:42', '2026-02-26 16:05:42'),
(26, 7, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:42', '2026-02-26 16:05:42'),
(27, 7, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:42', '2026-02-26 16:05:42'),
(28, 7, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:42', '2026-02-26 16:05:42'),
(29, 8, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:43', '2026-02-26 16:05:43'),
(30, 8, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:43', '2026-02-26 16:05:43'),
(31, 8, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:43', '2026-02-26 16:05:43'),
(32, 8, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:43', '2026-02-26 16:05:43'),
(33, 9, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:43', '2026-02-26 16:05:43'),
(34, 9, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:43', '2026-02-26 16:05:43'),
(35, 9, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:43', '2026-02-26 16:05:43'),
(36, 9, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:43', '2026-02-26 16:05:43'),
(37, 10, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:44', '2026-02-26 16:05:44'),
(38, 10, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:44', '2026-02-26 16:05:44'),
(39, 10, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:44', '2026-02-26 16:05:44'),
(40, 10, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:44', '2026-02-26 16:05:44'),
(41, 11, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:44', '2026-02-26 16:05:44'),
(42, 11, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:45', '2026-02-26 16:05:45'),
(43, 11, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:45', '2026-02-26 16:05:45'),
(44, 11, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:45', '2026-02-26 16:05:45'),
(45, 12, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:45', '2026-02-26 16:05:45'),
(46, 12, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:45', '2026-02-26 16:05:45'),
(47, 12, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:45', '2026-02-26 16:05:45'),
(48, 12, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:45', '2026-02-26 16:05:45'),
(49, 13, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:46', '2026-02-26 16:05:46'),
(50, 13, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:46', '2026-02-26 16:05:46'),
(51, 13, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:46', '2026-02-26 16:05:46'),
(52, 13, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:46', '2026-02-26 16:05:46'),
(53, 14, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:46', '2026-02-26 16:05:46'),
(54, 14, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:46', '2026-02-26 16:05:46'),
(55, 14, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:46', '2026-02-26 16:05:46'),
(56, 14, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:46', '2026-02-26 16:05:46'),
(57, 15, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:47', '2026-02-26 16:05:47'),
(58, 15, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:47', '2026-02-26 16:05:47'),
(59, 15, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:47', '2026-02-26 16:05:47'),
(60, 15, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:47', '2026-02-26 16:05:47'),
(61, 16, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:48', '2026-02-26 16:05:48'),
(62, 16, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:48', '2026-02-26 16:05:48'),
(63, 16, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:48', '2026-02-26 16:05:48'),
(64, 16, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:48', '2026-02-26 16:05:48'),
(65, 17, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:49', '2026-02-26 16:05:49'),
(66, 17, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:49', '2026-02-26 16:05:49'),
(67, 17, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:49', '2026-02-26 16:05:49'),
(68, 17, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:49', '2026-02-26 16:05:49'),
(69, 18, 'q', 'Qual sua série favorita?', 1, '2026-02-26 16:05:49', '2026-02-26 16:05:49'),
(70, 18, 'r', 'Adoro Breaking Bad!', 1, '2026-02-26 16:05:49', '2026-02-26 16:05:49'),
(71, 18, 'q', 'Café ou Chá?', 2, '2026-02-26 16:05:49', '2026-02-26 16:05:49'),
(72, 18, 'r', 'Prefiro um café forte!', 2, '2026-02-26 16:05:49', '2026-02-26 16:05:49');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `nivel_acesso` tinyint(4) NOT NULL DEFAULT 0,
  `assinatura_id` varchar(255) DEFAULT NULL,
  `premium_expira_em` timestamp NULL DEFAULT NULL,
  `last_seen` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `ativo`, `nivel_acesso`, `assinatura_id`, `premium_expira_em`, `last_seen`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Usuário Plus 1', 'plus1_641@saopaulo.com', NULL, '$2y$12$VoabQu8CPveWLBhFF4BdYu5jjEiXHBl7.h.u5D7NGxgDOKve8nD.O', 1, 1, NULL, NULL, NULL, NULL, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(2, 'Usuário Plus 2', 'plus2_632@saopaulo.com', NULL, '$2y$12$VUBUKZx3SNs58DWqkPMWHeB1V.wZz30MKd3TD7QzpCKwYUkNR.cy6', 1, 1, NULL, NULL, NULL, NULL, '2026-02-26 16:05:34', '2026-02-26 16:05:34'),
(3, 'Usuário Premium 3', 'premium3_528@saopaulo.com', NULL, '$2y$12$hWveDXbpXa09umC.Ks2TMOcKF3PVv6r/GISg4NhVpD1PoRyJUOSEe', 1, 2, NULL, NULL, NULL, NULL, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(4, 'Usuário Free 4', 'free4_613@saopaulo.com', NULL, '$2y$12$Jqe5xYsWm/.RP/YQki2Ehu611sbpjYbHoCrZ91jnYRUHjGd.0ufjO', 1, 0, NULL, NULL, NULL, NULL, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(5, 'Usuário Free 5', 'free5_171@saopaulo.com', NULL, '$2y$12$it6jLZiVOxqW4uKzJyi27ez6eBr.xkye8COwN8iTw86Wzs8USuTh6', 1, 0, NULL, NULL, NULL, NULL, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(6, 'Usuário Free 6', 'free6_473@saopaulo.com', NULL, '$2y$12$ak.C7Vza7gb771upfA4/A.kVmwzEFbvFr6vZ4cKPJu8OXI1B9MRii', 1, 0, NULL, NULL, NULL, NULL, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(7, 'Usuário Premium 1', 'premium1_182@riodejaneiro.com', NULL, '$2y$12$bdwQSZ3IXKXq28cm0keV2eo6BTJo8XCcS6n2q5aIa.0qaWm5D9OK2', 1, 2, NULL, NULL, NULL, NULL, '2026-02-26 16:05:35', '2026-02-26 16:05:35'),
(8, 'Usuário Plus 2', 'plus2_469@riodejaneiro.com', NULL, '$2y$12$Lh4c0veg9.5R5eZKXTeZDuPwYBYc5oADkDc3QzDVq0BB4MRgJeV3G', 1, 1, NULL, NULL, NULL, NULL, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(9, 'Usuário Plus 3', 'plus3_539@riodejaneiro.com', NULL, '$2y$12$MC9C8JZE5JgWfLMPSqcwH.Zgg4ocxu3FmCpIxqC67o0B1gn5GTQIe', 1, 1, NULL, NULL, NULL, NULL, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(10, 'Usuário Free 4', 'free4_458@riodejaneiro.com', NULL, '$2y$12$K4XA9ZluDHPyTpMvW15bU.29QNYipUtzv2IJMo8pT2WGyVb66KM1q', 1, 0, NULL, NULL, NULL, NULL, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(11, 'Usuário Premium 5', 'premium5_741@riodejaneiro.com', NULL, '$2y$12$BS1cJhaXEa1dpzI.Kishue1D.sj0Pl3I0EKDK2x6g9OWBlY1z0mgq', 1, 2, NULL, NULL, NULL, NULL, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(12, 'Usuário Free 6', 'free6_791@riodejaneiro.com', NULL, '$2y$12$zuvJaBKvNXekSW3H1l6ezuyXln3CrceZzVzH5SRNU4ra8gcHb2oA.', 1, 0, NULL, NULL, NULL, NULL, '2026-02-26 16:05:36', '2026-02-26 16:05:36'),
(13, 'Usuário Premium 1', 'premium1_927@montebelo.com', NULL, '$2y$12$9Ujs4zxfpZ6hjBGIcXydR.DsJkCpf3bLPcJY2jeV1dZi0d77DEEpy', 1, 2, NULL, NULL, NULL, NULL, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(14, 'Usuário Free 2', 'free2_267@montebelo.com', NULL, '$2y$12$m5OQWv1jaMk3VtOKAxAqOugXoRMHbyijtxtmFhY5Ez3B.V1eE/9S6', 1, 0, NULL, NULL, NULL, NULL, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(15, 'Usuário Plus 3', 'plus3_765@montebelo.com', NULL, '$2y$12$bh2aJ2UOCZVDOYiPLq3DoOtW3wWNi4njMWdM7jLv.urDEXxtfQrxi', 1, 1, NULL, NULL, NULL, NULL, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(16, 'Usuário Premium 4', 'premium4_354@montebelo.com', NULL, '$2y$12$0BF5B.TyPv/HnFqVBsz0W.OCdj2gCnYjlpd/f.s6s0UFecpIJnB4a', 1, 2, NULL, NULL, NULL, NULL, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(17, 'Usuário Plus 5', 'plus5_485@montebelo.com', NULL, '$2y$12$9dD11edfFV0s4ai1uhmGf.Rx8sr3OQIJuDLgmuqC6SNHqBxtFYulu', 1, 1, NULL, NULL, NULL, NULL, '2026-02-26 16:05:37', '2026-02-26 16:05:37'),
(18, 'Usuário Free 6', 'free6_208@montebelo.com', NULL, '$2y$12$v10jpv/qQTlEg8Ju0mxxl.rSORpLc4p1N.FT5BiMeV5aAh1ReO9Cy', 1, 0, NULL, NULL, NULL, NULL, '2026-02-26 16:05:38', '2026-02-26 16:05:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_matches`
--

CREATE TABLE `user_matches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_one_id` bigint(20) UNSIGNED NOT NULL,
  `user_two_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `bloqueios`
--
ALTER TABLE `bloqueios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bloqueios_perfil_id_perfil_bloqueado_id_unique` (`perfil_id`,`perfil_bloqueado_id`),
  ADD KEY `bloqueios_perfil_bloqueado_id_foreign` (`perfil_bloqueado_id`);

--
-- Índices de tabela `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Índices de tabela `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Índices de tabela `cidades`
--
ALTER TABLE `cidades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cidades_geoname_id_unique` (`geoname_id`),
  ADD KEY `cidades_estado_id_index` (`estado_id`),
  ADD KEY `cidades_pais_code_index` (`pais_code`),
  ADD KEY `cidades_latitude_longitude_index` (`latitude`,`longitude`);

--
-- Índices de tabela `contratos_promocionais`
--
ALTER TABLE `contratos_promocionais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contratos_promocionais_user_id_foreign` (`user_id`);

--
-- Índices de tabela `denuncias`
--
ALTER TABLE `denuncias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `denuncias_denunciante_id_foreign` (`denunciante_id`),
  ADD KEY `denuncias_denunciado_id_foreign` (`denunciado_id`);

--
-- Índices de tabela `emails_bloqueados`
--
ALTER TABLE `emails_bloqueados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emails_bloqueados_user_id_foreign` (`user_id`),
  ADD KEY `emails_bloqueados_hash_email_index` (`hash_email`);

--
-- Índices de tabela `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estados_pais_id_foreign` (`pais_id`);

--
-- Índices de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Índices de tabela `fotos_perfil`
--
ALTER TABLE `fotos_perfil`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fotos_perfil_user_id_foreign` (`user_id`);

--
-- Índices de tabela `historico_acessos`
--
ALTER TABLE `historico_acessos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `historico_acessos_user_id_foreign` (`user_id`);

--
-- Índices de tabela `hobbies`
--
ALTER TABLE `hobbies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hobbies_nome_unique` (`nome`);

--
-- Índices de tabela `idiomas`
--
ALTER TABLE `idiomas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idiomas_codigo_unique` (`codigo`);

--
-- Índices de tabela `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Índices de tabela `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `likes_user_id_liked_user_id_unique` (`user_id`,`liked_user_id`),
  ADD KEY `likes_liked_user_id_foreign` (`liked_user_id`);

--
-- Índices de tabela `localidades_ocultas`
--
ALTER TABLE `localidades_ocultas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `localidades_ocultas_user_id_foreign` (`user_id`);

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mensagens_user_match_id_foreign` (`user_match_id`),
  ADD KEY `mensagens_sender_id_foreign` (`sender_id`);

--
-- Índices de tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `paises`
--
ALTER TABLE `paises`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `paises_code_unique` (`code`);

--
-- Índices de tabela `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Índices de tabela `perfil_hobbies`
--
ALTER TABLE `perfil_hobbies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `perfil_hobbies_perfil_id_hobby_id_unique` (`perfil_id`,`hobby_id`),
  ADD KEY `perfil_hobbies_hobby_id_foreign` (`hobby_id`);

--
-- Índices de tabela `perfil_hobbies_preferencias`
--
ALTER TABLE `perfil_hobbies_preferencias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `perfil_hobby_pref_unique` (`perfil_id`,`hobby_id`),
  ADD KEY `perfil_hobbies_preferencias_hobby_id_foreign` (`hobby_id`);

--
-- Índices de tabela `perfil_idiomas`
--
ALTER TABLE `perfil_idiomas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `perfil_idiomas_perfil_id_idioma_id_unique` (`perfil_id`,`idioma_id`),
  ADD KEY `perfil_idiomas_idioma_id_foreign` (`idioma_id`);

--
-- Índices de tabela `perfil_idiomas_preferencias`
--
ALTER TABLE `perfil_idiomas_preferencias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `perfil_idiomas_preferencias_perfil_id_idioma_id_unique` (`perfil_id`,`idioma_id`),
  ADD KEY `perfil_idiomas_preferencias_idioma_id_foreign` (`idioma_id`);

--
-- Índices de tabela `perfis`
--
ALTER TABLE `perfis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `perfis_user_id_foreign` (`user_id`),
  ADD KEY `perfis_pais_id_foreign` (`pais_id`),
  ADD KEY `perfis_estado_id_foreign` (`estado_id`),
  ADD KEY `perfis_sexo_index` (`sexo`),
  ADD KEY `perfis_estado_civil_index` (`estado_civil`),
  ADD KEY `perfis_cidade_id_index` (`cidade_id`),
  ADD KEY `perfis_visibilidade_index` (`visibilidade`),
  ADD KEY `perfis_latitude_index` (`latitude`),
  ADD KEY `perfis_longitude_index` (`longitude`);

--
-- Índices de tabela `perfis_preferencias`
--
ALTER TABLE `perfis_preferencias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `perfis_preferencias_perfil_id_unique` (`perfil_id`),
  ADD KEY `perfis_preferencias_pais_id_foreign` (`pais_id`),
  ADD KEY `perfis_preferencias_estado_id_foreign` (`estado_id`),
  ADD KEY `perfis_preferencias_cidade_id_foreign` (`cidade_id`);

--
-- Índices de tabela `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Índices de tabela `segundas_chances`
--
ALTER TABLE `segundas_chances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `segundas_chances_perfil_id_foreign` (`perfil_id`),
  ADD KEY `segundas_chances_like_id_foreign` (`like_id`);

--
-- Índices de tabela `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Índices de tabela `shorts`
--
ALTER TABLE `shorts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shorts_user_id_foreign` (`user_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Índices de tabela `user_matches`
--
ALTER TABLE `user_matches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_matches_user_one_id_user_two_id_unique` (`user_one_id`,`user_two_id`),
  ADD KEY `user_matches_user_two_id_foreign` (`user_two_id`),
  ADD KEY `user_matches_user_one_id_user_two_id_index` (`user_one_id`,`user_two_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `bloqueios`
--
ALTER TABLE `bloqueios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cidades`
--
ALTER TABLE `cidades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `contratos_promocionais`
--
ALTER TABLE `contratos_promocionais`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `denuncias`
--
ALTER TABLE `denuncias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `emails_bloqueados`
--
ALTER TABLE `emails_bloqueados`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estados`
--
ALTER TABLE `estados`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fotos_perfil`
--
ALTER TABLE `fotos_perfil`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de tabela `historico_acessos`
--
ALTER TABLE `historico_acessos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `hobbies`
--
ALTER TABLE `hobbies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT de tabela `idiomas`
--
ALTER TABLE `idiomas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `likes`
--
ALTER TABLE `likes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `localidades_ocultas`
--
ALTER TABLE `localidades_ocultas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `paises`
--
ALTER TABLE `paises`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `perfil_hobbies`
--
ALTER TABLE `perfil_hobbies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de tabela `perfil_hobbies_preferencias`
--
ALTER TABLE `perfil_hobbies_preferencias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de tabela `perfil_idiomas`
--
ALTER TABLE `perfil_idiomas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de tabela `perfil_idiomas_preferencias`
--
ALTER TABLE `perfil_idiomas_preferencias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `perfis`
--
ALTER TABLE `perfis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `perfis_preferencias`
--
ALTER TABLE `perfis_preferencias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `segundas_chances`
--
ALTER TABLE `segundas_chances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `shorts`
--
ALTER TABLE `shorts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `user_matches`
--
ALTER TABLE `user_matches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `bloqueios`
--
ALTER TABLE `bloqueios`
  ADD CONSTRAINT `bloqueios_perfil_bloqueado_id_foreign` FOREIGN KEY (`perfil_bloqueado_id`) REFERENCES `perfis` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bloqueios_perfil_id_foreign` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `contratos_promocionais`
--
ALTER TABLE `contratos_promocionais`
  ADD CONSTRAINT `contratos_promocionais_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `denuncias`
--
ALTER TABLE `denuncias`
  ADD CONSTRAINT `denuncias_denunciado_id_foreign` FOREIGN KEY (`denunciado_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `denuncias_denunciante_id_foreign` FOREIGN KEY (`denunciante_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `emails_bloqueados`
--
ALTER TABLE `emails_bloqueados`
  ADD CONSTRAINT `emails_bloqueados_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `estados`
--
ALTER TABLE `estados`
  ADD CONSTRAINT `estados_pais_id_foreign` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `fotos_perfil`
--
ALTER TABLE `fotos_perfil`
  ADD CONSTRAINT `fotos_perfil_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `historico_acessos`
--
ALTER TABLE `historico_acessos`
  ADD CONSTRAINT `historico_acessos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_liked_user_id_foreign` FOREIGN KEY (`liked_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `localidades_ocultas`
--
ALTER TABLE `localidades_ocultas`
  ADD CONSTRAINT `localidades_ocultas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `mensagens`
--
ALTER TABLE `mensagens`
  ADD CONSTRAINT `mensagens_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensagens_user_match_id_foreign` FOREIGN KEY (`user_match_id`) REFERENCES `user_matches` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `perfil_hobbies`
--
ALTER TABLE `perfil_hobbies`
  ADD CONSTRAINT `perfil_hobbies_hobby_id_foreign` FOREIGN KEY (`hobby_id`) REFERENCES `hobbies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `perfil_hobbies_perfil_id_foreign` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `perfil_hobbies_preferencias`
--
ALTER TABLE `perfil_hobbies_preferencias`
  ADD CONSTRAINT `perfil_hobbies_preferencias_hobby_id_foreign` FOREIGN KEY (`hobby_id`) REFERENCES `hobbies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `perfil_hobbies_preferencias_perfil_id_foreign` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `perfil_idiomas`
--
ALTER TABLE `perfil_idiomas`
  ADD CONSTRAINT `perfil_idiomas_idioma_id_foreign` FOREIGN KEY (`idioma_id`) REFERENCES `idiomas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `perfil_idiomas_perfil_id_foreign` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `perfil_idiomas_preferencias`
--
ALTER TABLE `perfil_idiomas_preferencias`
  ADD CONSTRAINT `perfil_idiomas_preferencias_idioma_id_foreign` FOREIGN KEY (`idioma_id`) REFERENCES `idiomas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `perfil_idiomas_preferencias_perfil_id_foreign` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `perfis`
--
ALTER TABLE `perfis`
  ADD CONSTRAINT `perfis_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `perfis_estado_id_foreign` FOREIGN KEY (`estado_id`) REFERENCES `estados` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `perfis_pais_id_foreign` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `perfis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `perfis_preferencias`
--
ALTER TABLE `perfis_preferencias`
  ADD CONSTRAINT `perfis_preferencias_cidade_id_foreign` FOREIGN KEY (`cidade_id`) REFERENCES `cidades` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `perfis_preferencias_estado_id_foreign` FOREIGN KEY (`estado_id`) REFERENCES `estados` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `perfis_preferencias_pais_id_foreign` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `perfis_preferencias_perfil_id_foreign` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `segundas_chances`
--
ALTER TABLE `segundas_chances`
  ADD CONSTRAINT `segundas_chances_like_id_foreign` FOREIGN KEY (`like_id`) REFERENCES `likes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `segundas_chances_perfil_id_foreign` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `shorts`
--
ALTER TABLE `shorts`
  ADD CONSTRAINT `shorts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `user_matches`
--
ALTER TABLE `user_matches`
  ADD CONSTRAINT `user_matches_user_one_id_foreign` FOREIGN KEY (`user_one_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_matches_user_two_id_foreign` FOREIGN KEY (`user_two_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
