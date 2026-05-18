-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 20, 2025 at 09:59 PM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u987557742_servicredit`
--

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Super Administrator'),
(2, 'admin', 'Administrador'),
(3, 'Asesor', 'Asesor de Crétidos');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_asesores`
--

CREATE TABLE `tb_asesores` (
  `idasesor` int(11) NOT NULL,
  `nombres` varchar(50) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `fechaRegistro` datetime NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_asesores`
--

INSERT INTO `tb_asesores` (`idasesor`, `nombres`, `telefono`, `direccion`, `fechaRegistro`, `estado`) VALUES
(1, 'Cobrador', '5555 5555', 'Managua', '2024-02-06 12:07:42', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_bancos`
--

CREATE TABLE `tb_bancos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) DEFAULT NULL,
  `estado` int(11) DEFAULT 1,
  `tb_bancoscol` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_caja`
--

CREATE TABLE `tb_caja` (
  `idcaja` int(11) NOT NULL,
  `fecha_apertura` datetime NOT NULL,
  `monto_apertura` decimal(18,2) NOT NULL DEFAULT 0.00,
  `monto_cierre` decimal(18,2) NOT NULL DEFAULT 0.00,
  `fecha_cierre` datetime DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_caja_movimiento`
--

CREATE TABLE `tb_caja_movimiento` (
  `idcm` int(11) NOT NULL,
  `idcaja` int(11) NOT NULL,
  `tipo_movimiento` int(11) NOT NULL,
  `monto_movimiento` decimal(18,2) NOT NULL DEFAULT 0.00,
  `descripcion_movimiento` varchar(100) NOT NULL,
  `fecha_movimiento` datetime NOT NULL,
  `forma_pago` varchar(20) DEFAULT NULL,
  `tipo_doc` varchar(20) DEFAULT NULL,
  `numero_doc` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_clientes`
--

CREATE TABLE `tb_clientes` (
  `idcliente` int(11) NOT NULL,
  `apellidos` varchar(50) DEFAULT NULL,
  `nombres` varchar(100) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `telefono` varchar(27) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tipo_doc` int(11) NOT NULL,
  `numero_doc` varchar(50) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `estado` int(11) DEFAULT NULL,
  `fechaActualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_clientes`
--

INSERT INTO `tb_clientes` (`idcliente`, `apellidos`, `nombres`, `direccion`, `telefono`, `email`, `tipo_doc`, `numero_doc`, `comentarios`, `estado`, `fechaActualizacion`) VALUES
(691, 'GONZALES', 'JASON FERNANDDO', 'MANAGUA', '+505 7794 8321', NULL, 0, '0010106920064D', 'CLIENTE COLABORADOR', 1, NULL),
(692, 'UMAÑA CRUZ', 'YELBA MARIA', 'MANAGUA, BARRIO EDGAR LAND 2 CUADRAS AL LAGO 2 ABAJO 75 VRAS AL LAGO CASA ESQUINERA', '+505 7823 8775', NULL, 0, '001-041204-1008D', 'NUEVO CLIENTE', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_creditos`
--

CREATE TABLE `tb_creditos` (
  `id` int(11) NOT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `idcliente` int(11) DEFAULT NULL,
  `idasesor` int(11) NOT NULL,
  `fecha_credito` date DEFAULT NULL,
  `monto_credito` decimal(18,2) DEFAULT 0.00,
  `interes_credito` decimal(18,2) DEFAULT 0.00,
  `numero_coutas` int(11) DEFAULT NULL,
  `monto_capital` decimal(18,2) NOT NULL DEFAULT 0.00,
  `monto_interes` decimal(18,2) NOT NULL DEFAULT 0.00,
  `monto_couta` decimal(18,2) DEFAULT 0.00,
  `total_interes` decimal(18,2) DEFAULT 0.00,
  `descuento` decimal(18,2) DEFAULT 0.00,
  `total_pagar` decimal(18,2) DEFAULT 0.00,
  `forma_pago` varchar(45) DEFAULT NULL,
  `total_saldo` decimal(18,2) NOT NULL DEFAULT 0.00,
  `estado` int(11) DEFAULT 1,
  `comentarios` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_creditos`
--

INSERT INTO `tb_creditos` (`id`, `idusuario`, `idcliente`, `idasesor`, `fecha_credito`, `monto_credito`, `interes_credito`, `numero_coutas`, `monto_capital`, `monto_interes`, `monto_couta`, `total_interes`, `descuento`, `total_pagar`, `forma_pago`, `total_saldo`, `estado`, `comentarios`) VALUES
(512, 15, 691, 1, '2025-08-16', 14500.00, 1.00, 15, 966.67, 9.67, 976.33, 145.00, 0.00, 14645.00, '2', 14645.00, 1, ''),
(513, 15, 692, 1, '2025-08-19', 12000.00, 20.00, 24, 500.00, 100.00, 600.00, 2400.00, 0.00, 14400.00, '2', 14400.00, 1, 'CUOTAS QUINCENALES');

-- --------------------------------------------------------

--
-- Table structure for table `tb_credito_detalle`
--

CREATE TABLE `tb_credito_detalle` (
  `id` int(11) NOT NULL,
  `idcredito` int(11) DEFAULT NULL,
  `fecha_couta` date DEFAULT NULL,
  `numero_couta` int(11) DEFAULT NULL,
  `monto_capital` decimal(18,2) NOT NULL DEFAULT 0.00,
  `monto_interes` decimal(18,2) NOT NULL DEFAULT 0.00,
  `monto_couta` decimal(18,2) DEFAULT NULL,
  `fecha_pago` datetime DEFAULT NULL,
  `monto_pagado` decimal(18,2) DEFAULT NULL,
  `monto_pendiente` decimal(18,2) DEFAULT NULL,
  `mora` decimal(18,2) DEFAULT 0.00,
  `estado_couta` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_credito_detalle`
--

INSERT INTO `tb_credito_detalle` (`id`, `idcredito`, `fecha_couta`, `numero_couta`, `monto_capital`, `monto_interes`, `monto_couta`, `fecha_pago`, `monto_pagado`, `monto_pendiente`, `mora`, `estado_couta`) VALUES
(5052, 512, '2025-08-30', 1, 966.67, 9.67, 976.33, NULL, NULL, 976.33, 0.00, 1),
(5053, 512, '2025-09-13', 2, 966.67, 9.67, 976.33, NULL, NULL, 976.33, 0.00, 1),
(5054, 512, '2025-09-27', 3, 966.67, 9.67, 976.33, NULL, NULL, 976.33, 0.00, 1),
(5055, 512, '2025-10-11', 4, 966.67, 9.67, 976.33, NULL, NULL, 976.33, 0.00, 1),
(5056, 512, '2025-10-25', 5, 966.67, 9.67, 976.33, NULL, NULL, 976.33, 0.00, 1),
(5057, 512, '2025-11-08', 6, 966.67, 9.67, 976.33, NULL, NULL, 976.33, 0.00, 1),
(5058, 512, '2025-11-22', 7, 966.67, 9.67, 976.33, NULL, NULL, 976.33, 0.00, 1),
(5059, 512, '2025-12-06', 8, 966.67, 9.67, 976.33, NULL, NULL, 976.33, 0.00, 1),
(5060, 512, '2025-12-20', 9, 966.67, 9.67, 976.33, NULL, NULL, 976.33, 0.00, 1),
(5061, 512, '2026-01-03', 10, 966.67, 9.67, 976.33, NULL, NULL, 976.33, 0.00, 1),
(5062, 512, '2026-01-17', 11, 966.67, 9.67, 976.33, NULL, NULL, 976.33, 0.00, 1),
(5063, 512, '2026-01-31', 12, 966.67, 9.67, 976.33, NULL, NULL, 976.33, 0.00, 1),
(5064, 512, '2026-02-14', 13, 966.67, 9.67, 976.33, NULL, NULL, 976.33, 0.00, 1),
(5065, 512, '2026-02-28', 14, 966.67, 9.67, 976.33, NULL, NULL, 976.33, 0.00, 1),
(5066, 512, '2026-03-14', 15, 966.67, 9.67, 976.33, NULL, NULL, 976.33, 0.00, 1),
(5067, 513, '2025-09-02', 1, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5068, 513, '2025-09-16', 2, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5069, 513, '2025-09-30', 3, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5070, 513, '2025-10-14', 4, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5071, 513, '2025-10-28', 5, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5072, 513, '2025-11-11', 6, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5073, 513, '2025-11-25', 7, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5074, 513, '2025-12-09', 8, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5075, 513, '2025-12-23', 9, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5076, 513, '2026-01-06', 10, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5077, 513, '2026-01-20', 11, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5078, 513, '2026-02-03', 12, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5079, 513, '2026-02-17', 13, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5080, 513, '2026-03-03', 14, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5081, 513, '2026-03-17', 15, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5082, 513, '2026-03-31', 16, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5083, 513, '2026-04-14', 17, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5084, 513, '2026-04-28', 18, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5085, 513, '2026-05-12', 19, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5086, 513, '2026-05-26', 20, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5087, 513, '2026-06-09', 21, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5088, 513, '2026-06-23', 22, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5089, 513, '2026-07-07', 23, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1),
(5090, 513, '2026-07-21', 24, 500.00, 100.00, 600.00, NULL, NULL, 600.00, 0.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_departamentos`
--

CREATE TABLE `tb_departamentos` (
  `id` varchar(2) NOT NULL,
  `nombre` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_detalle_simulacion`
--

CREATE TABLE `tb_detalle_simulacion` (
  `iddetallesim` int(11) NOT NULL,
  `idsimulacion` int(11) NOT NULL,
  `numero_cuota` int(11) NOT NULL,
  `fecha_cuota` date NOT NULL,
  `monto_capital` decimal(18,2) NOT NULL,
  `monto_interes` decimal(18,2) NOT NULL,
  `monto_cuota` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_distritos`
--

CREATE TABLE `tb_distritos` (
  `id` varchar(6) NOT NULL,
  `idprovincia` varchar(4) DEFAULT NULL,
  `iddepartamento` varchar(2) DEFAULT NULL,
  `nombre` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_monedas`
--

CREATE TABLE `tb_monedas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) DEFAULT NULL,
  `simbolo` varchar(6) DEFAULT NULL,
  `estado` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_monedas`
--

INSERT INTO `tb_monedas` (`id`, `nombre`, `simbolo`, `estado`) VALUES
(1, 'CORDOBAS', 'C$', 1),
(3, 'DOLARES', '$', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_pagos`
--

CREATE TABLE `tb_pagos` (
  `idpago` int(11) NOT NULL,
  `fecha_pago` datetime DEFAULT NULL,
  `idcliente` int(11) DEFAULT NULL,
  `idcredito` int(11) DEFAULT NULL,
  `idcuota` int(11) DEFAULT NULL,
  `monto_pago` decimal(18,2) DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `descuento_pago` decimal(18,2) DEFAULT 0.00,
  `forma_pago` varchar(20) DEFAULT NULL,
  `tipo_doc` varchar(20) DEFAULT NULL,
  `numero_doc` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_pagos_detalle`
--

CREATE TABLE `tb_pagos_detalle` (
  `pdid` int(11) NOT NULL,
  `idpago` int(11) DEFAULT NULL,
  `idcuota` int(11) DEFAULT NULL,
  `monto_pagado` decimal(18,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_provincias`
--

CREATE TABLE `tb_provincias` (
  `id` varchar(4) NOT NULL,
  `nombre` varchar(45) DEFAULT NULL,
  `iddepartamento` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_simulacion`
--

CREATE TABLE `tb_simulacion` (
  `idsimulacion` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `idasesor` int(11) NOT NULL,
  `fecha_credito` datetime NOT NULL,
  `monto_credito` decimal(18,2) NOT NULL DEFAULT 0.00,
  `interes_credito` decimal(18,2) DEFAULT 0.00,
  `numero_cuotas` int(11) NOT NULL,
  `monto_capital` decimal(18,2) NOT NULL DEFAULT 0.00,
  `monto_interes` decimal(18,2) NOT NULL DEFAULT 0.00,
  `monto_cuota` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_interes` decimal(18,2) NOT NULL DEFAULT 0.00,
  `fecha_simulacion` datetime NOT NULL,
  `total_pagar` decimal(18,2) NOT NULL DEFAULT 0.00,
  `forma_pago` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_sistema`
--

CREATE TABLE `tb_sistema` (
  `id` int(11) NOT NULL,
  `razon_social` varchar(145) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `web` varchar(200) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `telefonos` varchar(45) DEFAULT NULL,
  `mensaje_ticket` varchar(300) DEFAULT NULL,
  `idmoneda` int(11) DEFAULT NULL,
  `fechaActualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `logotipo` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_sistema`
--

INSERT INTO `tb_sistema` (`id`, `razon_social`, `email`, `web`, `direccion`, `telefonos`, `mensaje_ticket`, `idmoneda`, `fechaActualizacion`, `logotipo`) VALUES
(1, 'SERVICREDIT', 'info@serviconta.online', 'www.serviconta.online', 'Managua, Nicaragua', '84364391', 'Préstamos rápidos y flexibles para los emprendedores.', 1, '2025-08-15 20:57:07', '6302417859.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(254) NOT NULL,
  `activation_selector` varchar(255) DEFAULT NULL,
  `activation_code` varchar(255) DEFAULT NULL,
  `forgotten_password_selector` varchar(255) DEFAULT NULL,
  `forgotten_password_code` varchar(255) DEFAULT NULL,
  `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
  `remember_selector` varchar(255) DEFAULT NULL,
  `remember_code` varchar(255) DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `perfil` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `email`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`, `perfil`) VALUES
(10, '190.237.61.146', 'admin@admin.com', '$2y$10$ycVVFxeyaqLgH6l3t9C6QuSujNHLK6LHiaf6HUiYE0JuUMCchpfVC', 'admin@admin.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1688783075, 1725491172, 1, 'Joselito ⁸', 'larson', NULL, NULL, 1),
(13, '190.87.165.213', 'Wilmar', '$2y$10$/0Bt1FNddlsZwFLBxLPAk.muDlnkLNWZvF.83tiulmli9r1qJMbAG', 'wilmar@wilmar.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1698294973, 1698295002, 0, 'Wilmar', 'wilmarcito', NULL, NULL, 2),
(14, '208.96.130.158', 'ERICK', '$2y$10$NquvggjmjpTdxdVxm1gwVOQcUpO41jL6ErAM32tVP.EGAsYt7j3pK', 'erick@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705522625, 1754417474, 1, 'ERICK', 'ERICK', NULL, NULL, 3),
(15, '208.96.130.158', 'ADMINISTRADOR', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'administrador@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705526891, 1763650024, 1, 'ADMINISTRADOR', 'ADMINISTRADOR', NULL, NULL, 1),
(16, '152.231.34.211', 'Admin', '$2y$10$P5nqQJA/JLD9uyCQh0fB2uG0AmFoBXfbz0zEQke9SNONIYlS4samm', 'Admin@prestamos.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705783195, 1754417533, 1, 'Admin', 'Admin', NULL, NULL, 1),
(17, '152.231.34.211', 'admin', '$2y$10$aCbqGqMuzU2oLSCoJ7/I7OytQOq4JUEEsxC9LbMn9k9Y35CBFyG9W', 'admin1@prestamos.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705783255, 1705783268, 1, 'admin', 'admin', NULL, NULL, 2),
(18, '152.231.35.196', 'erick', '$2y$10$P0TmofzkxqCD23EVEnNuGeiyhYU0zjkfxxDp0iNcbQTAGDJKzBxr6', 'erick1@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1707498697, 1763650059, 1, 'erick', 'erick', NULL, NULL, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

CREATE TABLE `users_groups` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `group_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `users_groups`
--

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
(22, 10, 1),
(25, 13, 2),
(27, 14, 3),
(28, 15, 1),
(29, 16, 1),
(30, 17, 2),
(31, 18, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_asesores`
--
ALTER TABLE `tb_asesores`
  ADD PRIMARY KEY (`idasesor`);

--
-- Indexes for table `tb_bancos`
--
ALTER TABLE `tb_bancos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_caja`
--
ALTER TABLE `tb_caja`
  ADD PRIMARY KEY (`idcaja`);

--
-- Indexes for table `tb_caja_movimiento`
--
ALTER TABLE `tb_caja_movimiento`
  ADD PRIMARY KEY (`idcm`);

--
-- Indexes for table `tb_clientes`
--
ALTER TABLE `tb_clientes`
  ADD PRIMARY KEY (`idcliente`);

--
-- Indexes for table `tb_creditos`
--
ALTER TABLE `tb_creditos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_credito_detalle`
--
ALTER TABLE `tb_credito_detalle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_departamentos`
--
ALTER TABLE `tb_departamentos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_detalle_simulacion`
--
ALTER TABLE `tb_detalle_simulacion`
  ADD PRIMARY KEY (`iddetallesim`);

--
-- Indexes for table `tb_distritos`
--
ALTER TABLE `tb_distritos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_monedas`
--
ALTER TABLE `tb_monedas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_pagos`
--
ALTER TABLE `tb_pagos`
  ADD PRIMARY KEY (`idpago`);

--
-- Indexes for table `tb_pagos_detalle`
--
ALTER TABLE `tb_pagos_detalle`
  ADD PRIMARY KEY (`pdid`);

--
-- Indexes for table `tb_provincias`
--
ALTER TABLE `tb_provincias`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_simulacion`
--
ALTER TABLE `tb_simulacion`
  ADD PRIMARY KEY (`idsimulacion`);

--
-- Indexes for table `tb_sistema`
--
ALTER TABLE `tb_sistema`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uc_email` (`email`),
  ADD UNIQUE KEY `uc_activation_selector` (`activation_selector`),
  ADD UNIQUE KEY `uc_forgotten_password_selector` (`forgotten_password_selector`),
  ADD UNIQUE KEY `uc_remember_selector` (`remember_selector`);

--
-- Indexes for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  ADD KEY `fk_users_groups_users1_idx` (`user_id`),
  ADD KEY `fk_users_groups_groups1_idx` (`group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `tb_asesores`
--
ALTER TABLE `tb_asesores`
  MODIFY `idasesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_bancos`
--
ALTER TABLE `tb_bancos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_caja`
--
ALTER TABLE `tb_caja`
  MODIFY `idcaja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `tb_caja_movimiento`
--
ALTER TABLE `tb_caja_movimiento`
  MODIFY `idcm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1452;

--
-- AUTO_INCREMENT for table `tb_clientes`
--
ALTER TABLE `tb_clientes`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=693;

--
-- AUTO_INCREMENT for table `tb_creditos`
--
ALTER TABLE `tb_creditos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=514;

--
-- AUTO_INCREMENT for table `tb_credito_detalle`
--
ALTER TABLE `tb_credito_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5091;

--
-- AUTO_INCREMENT for table `tb_detalle_simulacion`
--
ALTER TABLE `tb_detalle_simulacion`
  MODIFY `iddetallesim` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tb_monedas`
--
ALTER TABLE `tb_monedas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_pagos`
--
ALTER TABLE `tb_pagos`
  MODIFY `idpago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1452;

--
-- AUTO_INCREMENT for table `tb_pagos_detalle`
--
ALTER TABLE `tb_pagos_detalle`
  MODIFY `pdid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1542;

--
-- AUTO_INCREMENT for table `tb_simulacion`
--
ALTER TABLE `tb_simulacion`
  MODIFY `idsimulacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_sistema`
--
ALTER TABLE `tb_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users_groups`
--
ALTER TABLE `users_groups`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
