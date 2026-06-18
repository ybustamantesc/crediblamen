-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-06-2026 a las 23:36:21
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u987557742_crediblamensis`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `analisis_financiero`
--

CREATE TABLE `analisis_financiero` (
  `id` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `tipo` enum('asalariado','negociante') NOT NULL,
  `respuestas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`respuestas`)),
  `fecha` datetime DEFAULT current_timestamp(),
  `usuario` varchar(100) DEFAULT NULL,
  `gasto_salario_ayudante` decimal(12,2) DEFAULT 0.00,
  `gasto_transporte` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `groups`
--

CREATE TABLE `groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Super Administrator'),
(2, 'admin', 'Administrador'),
(3, 'Asesor', 'Asesor de CrÃ©tidos'),
(4, 'promotor', 'Promotor de Ventas'),
(5, 'Promotor', 'Promotor - Acceso a procesos comerciales');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `import_log`
--

CREATE TABLE `import_log` (
  `id` int(11) NOT NULL,
  `csv_file` varchar(255) NOT NULL,
  `stg_rows` int(11) DEFAULT 0,
  `stg_prestamos` int(11) DEFAULT 0,
  `imported_prestamos` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `import_log`
--

INSERT INTO `import_log` (`id`, `csv_file`, `stg_rows`, `stg_prestamos`, `imported_prestamos`, `created_at`) VALUES
(1, 'CargaCredito.csv', 757, 19, 19, '2026-02-20 05:43:37'),
(2, 'CargaCredito.csv', 757, 19, 19, '2026-02-20 05:44:45'),
(3, 'CargaCredito.csv', 760, 14, 14, '2026-02-20 05:45:50'),
(4, 'c:/xampp/htdocs/Conta/temp/CargaCredito3.csv', 760, 14, 14, '2026-02-20 05:53:58'),
(5, 'c:/xampp/htdocs/Conta/temp/CargaCredito3.csv', 760, 14, 14, '2026-02-20 05:56:10'),
(6, 'c:/xampp/htdocs/Conta/temp/CargaCredito3.csv', 760, 14, 36, '2026-02-20 06:04:20'),
(7, 'c:/xampp/htdocs/Conta/temp/CargaCredito3.csv', 1024, 43, 43, '2026-02-20 06:07:18'),
(8, 'c:/xampp/htdocs/Conta/temp/CargaCredito3.csv', 1024, 43, 43, '2026-02-20 06:13:42'),
(9, 'c:/xampp/htdocs/Conta/temp/CargaCredito3.csv', 1024, 43, 43, '2026-02-20 06:34:52'),
(10, 'c:/xampp/htdocs/Conta/temp/CargaCredito3.csv', 1024, 43, 43, '2026-02-20 06:44:32'),
(11, 'c:/xampp/htdocs/Conta/temp/CargaCredito.csv', 760, 36, 36, '2026-02-20 06:47:15'),
(12, 'c:/xampp/htdocs/Conta/temp/CargaCredito1.csv', 757, 29, 29, '2026-02-20 06:48:37'),
(13, 'c:/xampp/htdocs/Conta/temp/CargaCredito2.csv', 760, 36, 36, '2026-02-20 06:49:22'),
(14, 'c:/xampp/htdocs/Conta/temp/CargaCredito4.csv', 1993, 88, 86, '2026-02-20 06:52:30'),
(15, 'c:/xampp/htdocs/Conta/temp/CargaCredito5.csv', 2350, 106, 106, '2026-02-20 06:55:01'),
(16, 'c:/xampp/htdocs/Conta/temp/CargaCredito6.csv', 2712, 118, 118, '2026-02-20 06:56:32'),
(17, 'c:/xampp/htdocs/Conta/temp/CargaCredito7.csv', 2830, 145, 145, '2026-02-20 06:58:38'),
(18, 'c:/xampp/htdocs/Conta/temp/CargaCredito8.csv', 3031, 158, 158, '2026-02-20 07:01:50'),
(19, 'c:/xampp/htdocs/Conta/temp/CargaCredito9.csv', 3310, 159, 159, '2026-02-20 07:11:50'),
(20, 'c:/xampp/htdocs/Conta/temp/CargaCredito10.csv', 3723, 176, 176, '2026-02-20 07:15:23'),
(21, 'c:/xampp/htdocs/Conta/temp/CargaCredito11.csv', 3869, 201, 201, '2026-02-20 07:19:56'),
(22, 'c:/xampp/htdocs/Conta/temp/CargaCredito12p.csv', 3378, 201, 201, '2026-02-20 07:24:41'),
(23, 'c:/xampp/htdocs/Conta/temp/CargaCredito13p.csv', 2549, 148, 148, '2026-02-20 07:28:28'),
(24, 'c:/xampp/htdocs/Conta/temp/CargaCredito13p1.csv', 2549, 148, 148, '2026-02-20 07:31:55'),
(25, 'c:/xampp/htdocs/Conta/temp/CargaCredito14p.csv', 2549, 148, 148, '2026-02-20 07:35:31'),
(26, 'c:/xampp/htdocs/Conta/temp/CargaCredito13p1.csv', 3319, 205, 204, '2026-02-20 07:44:51'),
(27, 'c:/xampp/htdocs/Conta/temp/CargaCredito14p.csv', 3626, 227, 227, '2026-02-20 08:16:33'),
(28, 'c:/xampp/htdocs/Conta/temp/CargaCredito16p.csv', 3432, 211, 211, '2026-02-20 08:29:46'),
(29, 'c:/xampp/htdocs/Conta/temp/CargaCredito17p.csv', 3492, 220, 220, '2026-02-20 08:44:07'),
(30, 'c:/xampp/htdocs/Conta/temp/CargaCredito18p.csv', 470, 25, 25, '2026-02-20 08:56:18'),
(31, 'c:/xampp/htdocs/Conta/temp/CargaCredito19p2.csv', 5380, 153, 153, '2026-02-20 09:10:45'),
(32, 'c:/xampp/htdocs/Conta/temp/CargaCredito.csv', 3825, 0, 0, '2026-03-02 08:27:37'),
(33, 'c:/xampp/htdocs/Conta/temp/CargaCredito.csv', 3825, 0, 0, '2026-03-02 08:32:59'),
(34, 'c:/xampp/htdocs/Conta/temp/CargaCredito.csv', 3825, 0, 0, '2026-03-02 08:33:38'),
(35, 'c:/xampp/htdocs/Conta/temp/CargaCredito.csv', 7650, 0, 0, '2026-03-02 08:42:19'),
(36, 'c:/xampp/htdocs/Conta/temp/CargaCredito.csv', 3825, 0, 0, '2026-03-02 08:53:29'),
(37, 'c:/xampp/htdocs/Conta/temp/CargaCredito.csv', 7650, 0, 0, '2026-03-02 08:57:17'),
(38, 'c:/xampp/htdocs/Conta/temp/CargaCredito.csv', 11475, 0, 0, '2026-03-02 09:01:38'),
(39, 'c:/xampp/htdocs/Conta/temp/CargaCredito.csv', 15300, 0, 0, '2026-03-02 09:02:57'),
(40, 'c:/xampp/htdocs/Conta/temp/CargaCredito.csv', 19125, 0, 0, '2026-03-02 09:03:49'),
(41, 'c:/xampp/htdocs/Conta/temp/CargaCredito.csv', 22950, 0, 0, '2026-03-02 09:04:49'),
(42, 'c:/xampp/htdocs/Conta/temp/CargaCredito.csv', 26775, 0, 0, '2026-03-02 09:05:30'),
(43, 'c:/xampp/htdocs/Conta/temp/CargaCredito.csv', 30600, 0, 0, '2026-03-02 09:06:59'),
(44, 'c:/xampp/htdocs/Conta/temp/CargaCredito.csv', 30600, 0, 0, '2026-03-02 09:08:31'),
(45, 'c:/xampp/htdocs/Conta/temp/CargaCredito.csv', 30600, 0, 0, '2026-03-02 09:09:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stg_carga_credito`
--

CREATE TABLE `stg_carga_credito` (
  `fecha_desembolso_raw` varchar(50) DEFAULT NULL,
  `num_exp_raw` varchar(50) DEFAULT NULL,
  `estado_civil` varchar(50) DEFAULT NULL,
  `codigo_busqueda2` varchar(255) DEFAULT NULL,
  `vendedor` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `num_prestamo_raw` varchar(50) DEFAULT NULL,
  `sexo` varchar(10) DEFAULT NULL,
  `anio_piriosidad` varchar(20) DEFAULT NULL,
  `primer_seg_nombre` varchar(255) DEFAULT NULL,
  `nombre_cliente2` varchar(255) DEFAULT NULL,
  `primer_nombre` varchar(100) DEFAULT NULL,
  `segundo_nombre` varchar(100) DEFAULT NULL,
  `primer_apellido` varchar(100) DEFAULT NULL,
  `segundo_apellido` varchar(100) DEFAULT NULL,
  `ruta2` varchar(100) DEFAULT NULL,
  `piriosidad_mes` varchar(50) DEFAULT NULL,
  `dia` varchar(50) DEFAULT NULL,
  `periosidad_pagos` varchar(100) DEFAULT NULL,
  `cuota_no_raw` varchar(50) DEFAULT NULL,
  `dias_raw` varchar(50) DEFAULT NULL,
  `monto_credito_saldo_raw` varchar(50) DEFAULT NULL,
  `principal_raw` varchar(50) DEFAULT NULL,
  `interes_devengado_raw` varchar(50) DEFAULT NULL,
  `comision_desembolso_raw` varchar(50) DEFAULT NULL,
  `monto_cuota_raw` varchar(50) DEFAULT NULL,
  `fecha_raw` varchar(50) DEFAULT NULL,
  `recibo_no` varchar(100) DEFAULT NULL,
  `monto_usd_raw` varchar(50) DEFAULT NULL,
  `principal_usd_raw` varchar(50) DEFAULT NULL,
  `interes_usd_raw` varchar(50) DEFAULT NULL,
  `saldo_usd_raw` varchar(50) DEFAULT NULL,
  `comision_desembolso2_raw` varchar(50) DEFAULT NULL,
  `mora_usd_raw` varchar(50) DEFAULT NULL,
  `dias_mora_raw` varchar(50) DEFAULT NULL,
  `dias_mora2_raw` varchar(50) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `serie` varchar(50) DEFAULT NULL,
  `consecutivo` varchar(50) DEFAULT NULL,
  `suma_principal_interes_mora_raw` varchar(50) DEFAULT NULL,
  `resultado` varchar(50) DEFAULT NULL,
  `mes_desembolso` varchar(50) DEFAULT NULL,
  `rango` varchar(100) DEFAULT NULL,
  `rango_mora` varchar(100) DEFAULT NULL,
  `mes_pagado` varchar(50) DEFAULT NULL,
  `anio_pagado` varchar(10) DEFAULT NULL,
  `agrupacion_credito` varchar(100) DEFAULT NULL,
  `rango2` varchar(100) DEFAULT NULL,
  `c` varchar(20) DEFAULT NULL,
  `nivel` varchar(20) DEFAULT NULL,
  `interes_raw` varchar(50) DEFAULT NULL,
  `frecuencia_pago` varchar(50) DEFAULT NULL,
  `id_modalidad_credito` varchar(50) DEFAULT NULL,
  `id_sector_economico` varchar(50) DEFAULT NULL,
  `id_municipio` varchar(100) DEFAULT NULL,
  `id_sector_economico2` varchar(50) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `nombre_columna` varchar(255) DEFAULT NULL,
  `cedula_cliente` varchar(255) DEFAULT NULL,
  `cedula_promotor` varchar(255) DEFAULT NULL,
  `id_tipo_zona` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_account`
--

CREATE TABLE `tb_account` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `naturaleza` enum('deudora','acreedora') DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `agrupador_estado` varchar(100) DEFAULT NULL,
  `report_is` varchar(80) DEFAULT NULL COMMENT 'Key for Estado de Resultado mapping',
  `report_bs` varchar(80) DEFAULT NULL COMMENT 'Key for Estado de Situación Financiera mapping',
  `is_mayor` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_account`
--

INSERT INTO `tb_account` (`id`, `code`, `name`, `type`, `naturaleza`, `parent_id`, `created_at`, `agrupador_estado`, `report_is`, `report_bs`, `is_mayor`) VALUES
(1, '11010101201', 'CAJA PRINCIPAL C$', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', 'Fondos disponibles', NULL, 'Fondos disponibles', 1),
(2, '11010101301', 'CAJA PRINCIPAL U$', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', 'Fondos disponibles', NULL, 'Fondos disponibles', 1),
(3, '11020101301', 'Depósitos en Cuenta Corriente con intereses ME (LA FISE)', 'Activo', 'deudora', 1, '2026-05-26 13:57:59', 'Fondos disponibles', NULL, 'Fondos disponibles', 0),
(4, '11020102301', 'Depósito en Cuenta Corriente Sin Interes ME (LA FISE)', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', 'Fondos disponibles', NULL, 'Fondos disponibles', 0),
(5, '11040101201', 'Caja Chica', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', 'Fondos disponibles', NULL, 'Fondos disponibles', 0),
(6, '14010101101', 'Cartera de Créditos vigentes (Préstamos Microcreditos) MN con MV', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', 'Cartera de créditos, neto de provisiones por incobrabilidad', NULL, 'Cartera de créditos, neto de provisiones por incobrabilidad', 0),
(7, '14010101301', 'Cartera sobre saldos créditos vigentes (Prestamos Microcreditos) U$', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', 'Cartera de créditos, neto de provisiones por incobrabilidad', NULL, 'Cartera de créditos, neto de provisiones por incobrabilidad', 0),
(8, '14010201301', 'Cartera sobre saldos créditos vigentes (Prestamos Personales) U$', 'Activo', 'deudora', 1, '2026-05-26 13:57:59', NULL, NULL, 'Cartera de créditos, neto de provisiones por incobrabilidad', 0),
(9, '14010301301', 'Cartera sobre saldos créditos vigentes (Prestamos Hipotecarios) U$', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', NULL, NULL, NULL, 0),
(10, '14030101301', 'Cartera de Créditos Reestructurado (Préstamos Microcreditos) MN con MV', 'activo', 'deudora', NULL, '2026-05-26 13:57:59', NULL, NULL, 'Otros activos, neto', 0),
(11, '14040101301', 'Cartera de Créditos vencidos (Préstamos Microcreditos) U$', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', NULL, NULL, NULL, 0),
(12, '14040201301', 'Cartera sobre saldos créditos vencidos (Prestamos Personales) U$', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', NULL, NULL, NULL, 0),
(13, '14040301301', 'Cartera sobre saldos créditos vencidos (Prestamos Hipotecarios) U$', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', NULL, NULL, NULL, 0),
(14, '14050101301', 'Cartera de Créditos cobro judicial (Préstamos Microcreditos) U$', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', NULL, NULL, NULL, 0),
(15, '14060101101', 'Intereses y Comisiones por Cobrar de Créditos Vigentes (Microcreditos) MN con MV', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', NULL, NULL, NULL, 0),
(16, '14060101301', 'Intereses y Comisiones por Cobrar Créditos Vigentes ( Microcréditos) U$', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', NULL, NULL, NULL, 0),
(17, '14060102301', 'Intereses y Comisiones por Cobrar Creditos Vigentes (Personales) U$', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', NULL, NULL, NULL, 0),
(18, '14060103301', 'Interes y Comisiones por Cobrar Creditos Vigentes (Hipotecarios) U$', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', NULL, NULL, NULL, 0),
(19, '14060301301', 'Intereses y Comisiones por Cobrar Creditos Reestructurado  (Microcreditos) U$', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', NULL, NULL, NULL, 0),
(20, '14060401301', 'Intereses y Comisiones por Cobrar Creditos Vencidos (Microcreditos)U$', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', NULL, NULL, NULL, 0),
(21, '14080101101', 'Provisión para Incobrabilidad Cartera Vigente (Microcreditos) MN con MV', 'Activo', 'deudora', NULL, '2026-05-26 13:57:59', NULL, NULL, NULL, 0),
(22, '14080101301', 'Provisión para Incobrabilidad Cartera Vigente (Microcreditos) U$', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(23, '15010101101', 'Bienes recibidos en Pago y adjudicados (Bienes Muebles e Inmuebles)', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(24, '15010901201', 'Otros Bienes Recibido en Recuperación de Créditos', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(25, '15010101301', 'Bienes recibidos en Pago y adjudicados (Bienes Muebles e Inmuebles)', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(26, '16020101101', 'Anticipos proveedores MNL', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(27, '16020101201', 'Anticipos proveedores (Aporte CONAMI)', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(28, '16020101202', 'Anticipos proveedores (EFRAIN BLANDON)', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(29, '16020101203', 'Anticipos proveedores (Varios)', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(30, '16020601101', 'Cuentas por Cobrar al Personal', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(31, '16020601201', 'Cuentas por Cobrar al Personal', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(32, '16020901201', 'Otras partidas pendientes de cobro', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(33, '18030101201', 'MOBILIARIOS', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(34, '18030201201', 'EQUIPOS', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(35, '18040101201', 'Equipos de computación', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(36, '18050101201', 'Vehiculos', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(37, '18090102201', 'Depreciacion Acumulada de Mobiliario y Equipo', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(38, '18090103201', 'Depreciacion Acumulada de Equipos de Computacion', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(39, '19010301201', 'Impuesto Pagados por Anticipado', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(40, '19010601201', 'Mantenimientos Pagados Por Anticipado', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(41, '19110101201', 'Gastos de Organización e Instalación', 'Activo', 'deudora', NULL, '2026-05-26 13:58:00', NULL, NULL, NULL, 0),
(42, '19110201201', 'Mejoras a Propiedades Tomadas en Alquiler', 'Activo', 'deudora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(43, '19110801201', 'Licencias y permisos', 'Activo', 'deudora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(44, '19110801202', 'Membresias', 'Activo', 'deudora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(45, '19110801203', 'Afiliaciones y suscripciones', 'Activo', 'deudora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(46, '19110801204', 'Licencia de Software', 'Activo', 'deudora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(47, '19110901201', 'Amortizacion acumulada de gastos de Organizacion e Instalacion', 'Activo', 'deudora', NULL, '2026-05-26 13:58:01', NULL, NULL, 'Otros activos, neto', 0),
(48, '19110909201', 'Amortizacion acumulada de gastos de Licencias y permisos', 'Activo', 'deudora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(49, '19110909202', 'Amortizacion acumulada de gastos de Membresias', 'Activo', 'deudora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(50, '19110909203', 'Amortizacion acumulada de gastos de Afiliaciones y suscirpciones', 'Activo', 'deudora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(51, '19110909204', 'Amortizacion acumulada de licencia de software', 'Activo', 'deudora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(52, '22029901301', 'OTRAS OBLIGACIONES A PLAZO MAYORES A UN AÑO (MEYRA LINA BLANDON)', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(53, '26010401201', 'Aportaciones Laborales Retenidas por Pagar (INSS Laboral)', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(54, '26010501201', 'Remuneraciones por Pagar', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(55, '26019901201', 'Cuentas por Pagar Diversas', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(56, '26040101201', 'Impuesto sobre la renta por pagar', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(57, '26040201101', 'IMPUESTOS MÍNIMO DEFINITIVO', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(58, '26040201201', 'IMPUESTOS MÍNIMO DEFINITIVO', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(59, '26040301201', 'Impuestos municipales por pagar (Basura)', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(60, '26040301202', 'Impuestos municipales por pagar (Matricula)', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(61, '26040301203', 'Impuestos municipales por pagar (S/I)', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(62, '26050101201', 'Impuesto S/Renta en la Fuente (2% Y 10%)', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(63, '26050101202', 'Impuesto S/Renta en la Fuente (No Domiciliado 15% )', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(64, '26050301101', 'Impuesto S/Renta sobre Salarios', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(65, '26050301201', 'Impuesto S/Renta sobre Salarios', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(66, '26050401201', 'Impuestos municipales', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(67, '26050901201', 'OTROS IMPUESTOS', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(68, '27010201201', 'Provisiones para Prestaciones Laborales (Aguinaldos)', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:01', NULL, NULL, NULL, 0),
(69, '27010201202', 'Provisiones para Prestaciones Laborales (Vacaciones)', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(70, '27010201203', 'Provisiones para Prestaciones Laborales (Indemnización)', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(71, '27010401101', 'Provisiones para Otros Gastos', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(72, '27010401202', 'Provisiones para Otros Gastos (Internet)', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(73, '27010701101', 'Aportaciones Patronales Por Pagar', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(74, '27010701201', 'Aportaciones Patronales por Pagar (INSS Patronal)', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(75, '27010701202', 'Aportaciones Patronales por Pagar (INATEC)', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(76, '27010901201', 'Otras Provisiones (Canasta Navideña)', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(77, '27030101201', 'Reserva para Retiro', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(78, '27030201101', 'RESERVAS POR PRIMAS DE ANTIGÜEDAD', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(79, '29290103201', 'Otras operaciones pendientes de imputacion', 'Pasivo', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(80, '31010101201', 'Capital Sucrito Ordinario', 'patrimonio', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, 'Capital social / Aportes', 0),
(81, '32010301201', 'Capital Adicional', 'patrimonio', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, 'Capital adicional / Aporte adicional', 0),
(82, '32320103031', 'Donaciones pendientes de capitalizacion', 'Patrimonio', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, 'Capital adicional / Aporte adicional', 0),
(83, '38010101201', 'Utilidadades Acumuladas con Acuerdo de Distribución', 'Patrimonio', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(84, '39010101201', 'Utilidades del ejercicio', 'Patrimonio', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(85, '41060101201', 'Intereses y comisiones Créditos Vigentes (Préstamos Microcréditos)', 'ingreso', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, 'Cartera de créditos', NULL, 0),
(86, '41060101202', 'Interés moratorios Vigentes (Prestamos Microcreditos)', 'Ingreso', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(87, '41060101203', 'Ingresos por Comisiones  de Desembolsos  (Préstamos Microcréditos)', 'ingreso', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, 'Otros Ingresos Financieros', NULL, 0),
(88, '41060201201', 'Intereses y comisiones Créditos Vigentes (Préstamos Personales)', 'Ingreso', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(89, '41060201202', 'Interés moratorios Vigentes (Prestamos Personales)', 'Ingreso', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(90, '41080101201', 'Intereses y comisiones Créditos Vigentes (Préstamos Microcréditos Reestructurados)', 'Ingreso', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(91, '41080101202', 'Interés moratorios Vencidos (Prestamos Microcreditos)', 'Ingreso', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(92, '41090101201', 'Intereses y comisiones Créditos Vencidos (Préstamos Microcréditos)', 'ingreso', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, 'Cartera de créditos', NULL, 0),
(93, '41090201201', 'Intereses y comisiones Créditos Vencidos (Préstamos Personales)', 'Ingreso', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(94, '41090201202', 'Interés moratorios Vencidos (Prestamos Personales)', 'Ingreso', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(95, '41100101201', 'Intereses y comisiones Créditos Cobro Judicial (Préstamos Microcréditos)', 'Ingreso', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(96, '42030101201', 'Disminucion de provision para cartera de creditos', 'Ingreso', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(97, '43020501201', 'OTROS INGRESOS GENERADOS POR OTROS ACTIVOS', 'Ingreso', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(98, '43430301101', 'Ingresos Operativos varios', 'Ingreso', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(99, '45450101101', 'Ingresos por efectos cambiarios (Fondos disponibles)', 'ingreso', 'acreedora', NULL, '2026-05-26 13:58:02', NULL, 'Diferencia Cambiaria', NULL, 0),
(100, '51040501201', 'CARGOS POR PRESTAMOS DE OTRAS INSTITUCIONES DEL PAIS', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(101, '51040801301', 'Cargos por Préstamos con Otras Instituciones Financieras del Pais Mayor a un año', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:02', NULL, NULL, NULL, 0),
(102, '51040901201', 'CARGOS POR OTRAS OBLIGACIONES POR FINANCIAMIENTOS', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, NULL, NULL, 0),
(103, '52010101201', 'Constitución de Provisión por Cartera de Créditos', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, NULL, NULL, 0),
(104, '52020201201', 'Saneamiento sobre intereses y comisiones de cartera de credito', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, NULL, NULL, 0),
(105, '53010102201', 'Comisiones por Giros y Transferencias', 'gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, 'Diferencia Cambiaria (gastos)', NULL, 0),
(106, '53020401201', 'Amortización de Software', 'gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, 'Otros gastos financieros', NULL, 0),
(107, '54010101201', 'Sueldos de Personal Permanente', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, NULL, NULL, 0),
(108, '54010102301', 'Sueldos de Personal Contratado', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, NULL, NULL, 0),
(109, '54010103301', 'Sobresueldos', 'gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, 'Deuda subordinada y obligaciones convertibles en acciones', NULL, 0),
(110, '54010104201', 'Comisiones', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, NULL, NULL, 0),
(111, '54010106201', 'Tiempo Extraordinario', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, NULL, NULL, 0),
(112, '54010107201', 'Viaticos', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, NULL, NULL, 0),
(113, '54010108201', 'Aguinaldos', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, NULL, NULL, 0),
(114, '54010109201', 'Vacaciones', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, NULL, NULL, 0),
(115, '54010110201', 'Indemnización', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, NULL, NULL, 0),
(116, '54010111201', 'Jubilaciones y/o Retiros', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, NULL, NULL, 0),
(117, '54010113201', 'Bonificaciones e Incentivos (Celebraciones)', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, NULL, NULL, 0),
(118, '54010113202', 'Bonificaciones e Incentivos (Convenio)', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, NULL, NULL, 0),
(119, '54010115201', 'Seguro Social Aporte Patronal', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, NULL, NULL, 0),
(120, '54010116201', 'Aporte al Instituto Nacional Tecnológico (INATEC)', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:03', NULL, NULL, NULL, 0),
(121, '54010117201', 'Gastos de cafeteria', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(122, '54010119201', 'Uniformes', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(123, '54010121201', 'Capacitación', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(124, '54010199201', 'Otros Gastos de Personal', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(125, '54020301201', 'Servicios de Información', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(126, '54029901201', 'Honorarios profesionales', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(127, '54029901202', 'Otros Servicios Contratados (Servicio de GPS )', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(128, '54030101201', 'Pasajes y Traslados', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(129, '54030201201', 'FLETES', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(130, '54030401201', 'Combustible, Lubricantes y Otros', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(131, '54030401301', 'Combustible, Lubricantes y Otros', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(132, '54030901201', 'Depreciacion de Vehiculos', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(133, '54031001201', 'Telefonos, Telefax, fax (Celulares)', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(134, '54031001202', 'Telefonos, Telefax, fax', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(135, '54039901201', 'Otros Gastos de Transporte y Comunicación (Internet)', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(136, '54040301203', 'Mantenimiento y reparación de equipo de oficina y computo', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(137, '54040401201', 'Agua', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(138, '54040401202', 'Energía eléctrica', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(139, '54040501201', 'Alquileres de Inmuebles', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(140, '54040801201', 'Depreciación  de Equipos y Mobiliario', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(141, '54040801301', 'Depreciación  de Equipos y Mobiliario', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(142, '54040901201', 'Depreciación de Equipos de cómputo', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(143, '54050101201', 'Impuestos, Multas y Tasas Municipales', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(144, '54050401201', 'Amortizacion de gastos de organización e instalacion', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(145, '54050601201', 'Amortizaciones de otros cargos diferidos', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(146, '54050701201', 'Papeleria, utiles y otros materiales de Limpieza', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(147, '54050701202', 'Papeleria, utiles y otros materiales (Materiales de oficina)', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(148, '54050701301', 'Papeleria, utiles y otros materiales de Limpieza', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(149, '54050801201', 'Gastos Legales', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(150, '54050901201', 'Suscripciones y afiliaciones', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(151, '54051001201', 'Propaganda, Publicidad y Promociones', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(152, '54059901201', 'Otros Gastos Generales (Beneficios a Clientes por Pronto pago)', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(153, '54059901202', 'Otros Gastos Generales (Gastos no deducibles)', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(154, '54059901203', 'Otros Gastos Generales', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(155, '54059901301', 'Otros Gastos Generales', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(156, '55303091101', 'Gastos operativos varios', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(157, '55550101101', 'Gastos por Efectos Cambiarios (Fondos Disponibles)', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(158, '55114110901', 'Otros gastos financieros diversos', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(159, '56010101201', 'Aportes al Presupuesto de la CONAMI', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(160, '62010101201', 'Gasto por impuesto sobre la renta', 'gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, 'Impuesto a la renta', NULL, 0),
(161, '63010101201', 'Resultado Neto del Ejercicio', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(162, '63010201201', 'Resultado Neto del Ejercicio', 'Gasto', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(163, '82040217201', 'Créditos Saneados Año 2025', 'Orden', 'deudora', NULL, '2026-05-26 13:58:04', NULL, NULL, NULL, 0),
(164, '82050201201', 'INTERESES Y COMISIONES EN SUSPENSO POR CREDITOS', 'Orden', 'deudora', NULL, '2026-05-26 13:58:05', NULL, NULL, NULL, 0),
(165, '86040101201', 'CONTRACUENTA DE CUENTAS SANEADAS', 'Orden', 'deudora', NULL, '2026-05-26 13:58:05', NULL, NULL, NULL, 0),
(166, '86050101201', 'CONTRACUENTA DE INGRESOS EN SUSPENSO', 'Orden', 'deudora', NULL, '2026-05-26 13:58:05', NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_account_mapping`
--

CREATE TABLE `tb_account_mapping` (
  `id` int(11) NOT NULL,
  `mapping_key` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `debit_account_id` int(11) NOT NULL,
  `credit_account_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_analisis_financiero_asalariado`
--

CREATE TABLE `tb_analisis_financiero_asalariado` (
  `id` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `ingreso_sueldo_neto` decimal(14,2) DEFAULT 0.00,
  `ingreso_comisiones` decimal(14,2) DEFAULT 0.00,
  `ingreso_bonificaciones` decimal(14,2) DEFAULT 0.00,
  `ingreso_remesas` decimal(14,2) DEFAULT 0.00,
  `ingreso_otros` decimal(14,2) DEFAULT 0.00,
  `total_ingresos` decimal(14,2) DEFAULT 0.00,
  `sueldo` decimal(14,2) DEFAULT 0.00,
  `inss` decimal(14,2) DEFAULT 0.00,
  `ir` decimal(14,2) DEFAULT 0.00,
  `sueldo_neto_calc` decimal(14,2) DEFAULT 0.00,
  `gastos_alimentacion` decimal(14,2) DEFAULT 0.00,
  `gastos_servicios` decimal(14,2) DEFAULT 0.00,
  `gastos_vestuario` decimal(14,2) DEFAULT 0.00,
  `gastos_educativos` decimal(14,2) DEFAULT 0.00,
  `gastos_transporte` decimal(14,2) DEFAULT 0.00,
  `gastos_alquiler` decimal(14,2) DEFAULT 0.00,
  `pago_empleado_viatico` decimal(14,2) DEFAULT 0.00,
  `entretenimiento` decimal(14,2) DEFAULT 0.00,
  `otros_gastos` decimal(14,2) DEFAULT 0.00,
  `total_gastos_familiares` decimal(14,2) DEFAULT 0.00,
  `cuotas_prestamos` decimal(14,2) DEFAULT 0.00,
  `pension_alimenticia` decimal(14,2) DEFAULT 0.00,
  `otras_obligaciones` decimal(14,2) DEFAULT 0.00,
  `total_otras_obligaciones` decimal(14,2) DEFAULT 0.00,
  `total_egresos` decimal(14,2) DEFAULT 0.00,
  `flujo_neto_mensual` decimal(14,2) DEFAULT 0.00,
  `cuota_periodica` decimal(14,2) DEFAULT 0.00,
  `canasta_basica` decimal(14,2) DEFAULT 0.00,
  `cantidad_promedio` int(11) DEFAULT 0,
  `monto_por_persona` decimal(14,2) DEFAULT 0.00,
  `personas_dependientes` int(11) DEFAULT 0,
  `gastos_alimentacion_canasta` decimal(14,2) DEFAULT 0.00,
  `transporte_urbano` decimal(14,2) DEFAULT 0.00,
  `transporte_individual` decimal(14,2) DEFAULT 0.00,
  `transporte_interurbano` decimal(14,2) DEFAULT 0.00,
  `recorrido_laboral` decimal(14,2) DEFAULT 0.00,
  `vehiculo_particular` decimal(14,2) DEFAULT 0.00,
  `alquiler` decimal(14,2) DEFAULT 0.00,
  `casa_propia` decimal(14,2) DEFAULT 0.00,
  `cobertura_deuda` decimal(14,4) DEFAULT 0.0000,
  `cobertura_garantia` decimal(14,4) DEFAULT 0.0000,
  `tc_acumulado` decimal(14,2) DEFAULT 0.00,
  `p_entretenimiento` decimal(14,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `efectivo_caja` decimal(14,2) DEFAULT 0.00,
  `dinero_banco` decimal(14,2) DEFAULT 0.00,
  `total_disponible` decimal(14,2) DEFAULT 0.00,
  `cuentas_cobrar` decimal(14,2) DEFAULT 0.00,
  `inventario_mercaderia` decimal(14,2) DEFAULT 0.00,
  `productos_proceso` decimal(14,2) DEFAULT 0.00,
  `productos_terminados` decimal(14,2) DEFAULT 0.00,
  `total_inventarios` decimal(14,2) DEFAULT 0.00,
  `bienes_muebles` decimal(14,2) DEFAULT 0.00,
  `propiedades` decimal(14,2) DEFAULT 0.00,
  `otros_activos` decimal(14,2) DEFAULT 0.00,
  `total_activos_fijos` decimal(14,2) DEFAULT 0.00,
  `total_activos` decimal(14,2) DEFAULT 0.00,
  `cuentas_pagar_proveedores` decimal(14,2) DEFAULT 0.00,
  `cuentas_pagar_credito` decimal(14,2) DEFAULT 0.00,
  `pasivo_no_corriente` decimal(14,2) DEFAULT 0.00,
  `total_pasivo` decimal(14,2) DEFAULT 0.00,
  `total_patrimonio` decimal(14,2) DEFAULT 0.00,
  `total_pasivo_patrimonio` decimal(14,2) DEFAULT 0.00,
  `ventas_contado` decimal(14,2) DEFAULT 0.00,
  `ventas_credito` decimal(14,2) DEFAULT 0.00,
  `ventas_totales` decimal(14,2) DEFAULT 0.00,
  `costos_venta` decimal(14,2) DEFAULT 0.00,
  `margen_bruto` decimal(14,2) DEFAULT 0.00,
  `gastos_generales` decimal(14,2) DEFAULT 0.00,
  `utilidad_operativa` decimal(14,2) DEFAULT 0.00,
  `fcm_ventas_contado` decimal(14,2) DEFAULT 0.00,
  `fcm_recuperacion_credito` decimal(14,2) DEFAULT 0.00,
  `fcm_compras_contado` decimal(14,2) DEFAULT 0.00,
  `fcm_gastos_generales` decimal(14,2) DEFAULT 0.00,
  `flujo_negocio` decimal(14,2) DEFAULT 0.00,
  `fcm_otros_ingresos` decimal(14,2) DEFAULT 0.00,
  `fcm_gastos_consumo` decimal(14,2) DEFAULT 0.00,
  `fcm_otros_gastos` decimal(14,2) DEFAULT 0.00,
  `flujo_neto_disponible` decimal(14,2) DEFAULT 0.00,
  `gasto_local_alquiler` decimal(14,2) DEFAULT 0.00,
  `gasto_energia` decimal(14,2) DEFAULT 0.00,
  `gasto_agua` decimal(14,2) DEFAULT 0.00,
  `gasto_internet` decimal(14,2) DEFAULT 0.00,
  `gasto_seguridad` decimal(14,2) DEFAULT 0.00,
  `gasto_limpieza` decimal(14,2) DEFAULT 0.00,
  `gasto_personal` decimal(14,2) DEFAULT 0.00,
  `total_gastos_fijos` decimal(14,2) DEFAULT 0.00,
  `olp_fecha` varchar(255) DEFAULT NULL,
  `olp_cuota` varchar(255) DEFAULT NULL,
  `olp_instituciones` varchar(255) DEFAULT NULL,
  `olp_saldo` varchar(255) DEFAULT NULL,
  `subtotal_olp_saldo` varchar(255) DEFAULT NULL,
  `ocp_fecha` varchar(255) DEFAULT NULL,
  `ocp_cuota` varchar(255) DEFAULT NULL,
  `ocp_instituciones` varchar(255) DEFAULT NULL,
  `ocp_saldo` varchar(255) DEFAULT NULL,
  `subtotal_ocp_saldo` varchar(255) DEFAULT NULL,
  `costo_salario_ayudante` decimal(14,2) DEFAULT 0.00,
  `costo_transporte` decimal(14,2) DEFAULT 0.00,
  `costo_total_operacion` decimal(14,2) DEFAULT 0.00,
  `asal_olp_fecha` varchar(255) DEFAULT NULL,
  `asal_olp_cuota` varchar(255) DEFAULT NULL,
  `asal_olp_instituciones` varchar(255) DEFAULT NULL,
  `asal_olp_saldo` varchar(255) DEFAULT NULL,
  `asal_subtotal_olp_saldo` varchar(255) DEFAULT NULL,
  `indicador_endeudamiento` decimal(14,4) DEFAULT 0.0000,
  `capital_trabajo_neto` decimal(14,2) DEFAULT 0.00,
  `porcentaje_margen` decimal(5,2) DEFAULT 0.00,
  `fcm_valor_canasta_basica` decimal(15,2) DEFAULT 0.00,
  `fcm_cant_personas_dep` int(11) DEFAULT 0,
  `total_deuda_acreditar` decimal(14,2) DEFAULT 0.00,
  `porcentaje_deuda_total` decimal(14,6) DEFAULT 0.000000,
  `disponible_ab` decimal(14,2) DEFAULT 0.00,
  `cuentas_por_cobrar` decimal(14,2) DEFAULT 0.00,
  `inventarios_abc` decimal(14,2) DEFAULT 0.00,
  `cuentas_pagar_corto_plazo` decimal(14,2) DEFAULT 0.00,
  `cuota_periodica_estim` decimal(14,2) DEFAULT 0.00,
  `flujo_ventas_contado` decimal(14,2) DEFAULT 0.00,
  `flujo_recuperacion_credito` decimal(14,2) DEFAULT 0.00,
  `flujo_compras_contado` decimal(14,2) DEFAULT 0.00,
  `flujo_gastos_generales` decimal(14,2) DEFAULT 0.00,
  `flujo_otros_ingresos_fam` decimal(14,2) DEFAULT 0.00,
  `flujo_gastos_consumo_fam` decimal(14,2) DEFAULT 0.00,
  `flujo_otros_gastos` decimal(14,2) DEFAULT 0.00,
  `gasto_personal_basico` decimal(14,2) DEFAULT 0.00,
  `oblig_largo_plazo1_fecha` date DEFAULT NULL,
  `oblig_largo_plazo1_cuota` decimal(14,2) DEFAULT 0.00,
  `oblig_largo_plazo1_inst` varchar(100) DEFAULT NULL,
  `oblig_largo_plazo1_saldo` decimal(14,2) DEFAULT 0.00,
  `oblig_largo_plazo2_fecha` date DEFAULT NULL,
  `oblig_largo_plazo2_cuota` decimal(14,2) DEFAULT 0.00,
  `oblig_largo_plazo2_inst` varchar(100) DEFAULT NULL,
  `oblig_largo_plazo2_saldo` decimal(14,2) DEFAULT 0.00,
  `oblig_largo_plazo3_fecha` date DEFAULT NULL,
  `oblig_largo_plazo3_cuota` decimal(14,2) DEFAULT 0.00,
  `oblig_largo_plazo3_inst` varchar(100) DEFAULT NULL,
  `oblig_largo_plazo3_saldo` decimal(14,2) DEFAULT 0.00,
  `subtotal_oblig_largo_plazo` decimal(14,2) DEFAULT 0.00,
  `oblig_corto_plazo1_fecha` date DEFAULT NULL,
  `oblig_corto_plazo1_cuota` decimal(14,2) DEFAULT 0.00,
  `oblig_corto_plazo1_inst` varchar(100) DEFAULT NULL,
  `oblig_corto_plazo1_saldo` decimal(14,2) DEFAULT 0.00,
  `oblig_corto_plazo2_fecha` date DEFAULT NULL,
  `oblig_corto_plazo2_cuota` decimal(14,2) DEFAULT 0.00,
  `oblig_corto_plazo2_inst` varchar(100) DEFAULT NULL,
  `oblig_corto_plazo2_saldo` decimal(14,2) DEFAULT 0.00,
  `oblig_corto_plazo3_fecha` date DEFAULT NULL,
  `oblig_corto_plazo3_cuota` decimal(14,2) DEFAULT 0.00,
  `oblig_corto_plazo3_inst` varchar(100) DEFAULT NULL,
  `oblig_corto_plazo3_saldo` decimal(14,2) DEFAULT 0.00,
  `subtotal_oblig_corto_plazo` decimal(14,2) DEFAULT 0.00,
  `nivel_endeudamiento` decimal(14,4) DEFAULT 0.0000,
  `total_transporte` decimal(14,2) DEFAULT 0.00,
  `total_gastos_vivienda` decimal(14,2) DEFAULT 0.00,
  `tipo_credito` varchar(150) DEFAULT NULL,
  `monto_financiar` decimal(14,2) DEFAULT NULL,
  `plazo_credito` int(11) DEFAULT NULL,
  `num_cuotas` int(11) DEFAULT NULL,
  `monto_cuota` decimal(14,2) DEFAULT NULL,
  `fecha_pago` varchar(50) DEFAULT NULL,
  `frecuencia_pago` varchar(150) DEFAULT NULL,
  `forma_pago` varchar(150) DEFAULT NULL,
  `garantia` varchar(150) DEFAULT NULL,
  `fundamentacion` varchar(200) DEFAULT NULL,
  `tasa_interes` decimal(6,2) DEFAULT NULL,
  `comision_desembolso` decimal(8,4) DEFAULT NULL,
  `comentario` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_analisis_financiero_asalariado`
--

INSERT INTO `tb_analisis_financiero_asalariado` (`id`, `idsolicitud`, `ingreso_sueldo_neto`, `ingreso_comisiones`, `ingreso_bonificaciones`, `ingreso_remesas`, `ingreso_otros`, `total_ingresos`, `sueldo`, `inss`, `ir`, `sueldo_neto_calc`, `gastos_alimentacion`, `gastos_servicios`, `gastos_vestuario`, `gastos_educativos`, `gastos_transporte`, `gastos_alquiler`, `pago_empleado_viatico`, `entretenimiento`, `otros_gastos`, `total_gastos_familiares`, `cuotas_prestamos`, `pension_alimenticia`, `otras_obligaciones`, `total_otras_obligaciones`, `total_egresos`, `flujo_neto_mensual`, `cuota_periodica`, `canasta_basica`, `cantidad_promedio`, `monto_por_persona`, `personas_dependientes`, `gastos_alimentacion_canasta`, `transporte_urbano`, `transporte_individual`, `transporte_interurbano`, `recorrido_laboral`, `vehiculo_particular`, `alquiler`, `casa_propia`, `cobertura_deuda`, `cobertura_garantia`, `tc_acumulado`, `p_entretenimiento`, `created_at`, `updated_at`, `efectivo_caja`, `dinero_banco`, `total_disponible`, `cuentas_cobrar`, `inventario_mercaderia`, `productos_proceso`, `productos_terminados`, `total_inventarios`, `bienes_muebles`, `propiedades`, `otros_activos`, `total_activos_fijos`, `total_activos`, `cuentas_pagar_proveedores`, `cuentas_pagar_credito`, `pasivo_no_corriente`, `total_pasivo`, `total_patrimonio`, `total_pasivo_patrimonio`, `ventas_contado`, `ventas_credito`, `ventas_totales`, `costos_venta`, `margen_bruto`, `gastos_generales`, `utilidad_operativa`, `fcm_ventas_contado`, `fcm_recuperacion_credito`, `fcm_compras_contado`, `fcm_gastos_generales`, `flujo_negocio`, `fcm_otros_ingresos`, `fcm_gastos_consumo`, `fcm_otros_gastos`, `flujo_neto_disponible`, `gasto_local_alquiler`, `gasto_energia`, `gasto_agua`, `gasto_internet`, `gasto_seguridad`, `gasto_limpieza`, `gasto_personal`, `total_gastos_fijos`, `olp_fecha`, `olp_cuota`, `olp_instituciones`, `olp_saldo`, `subtotal_olp_saldo`, `ocp_fecha`, `ocp_cuota`, `ocp_instituciones`, `ocp_saldo`, `subtotal_ocp_saldo`, `costo_salario_ayudante`, `costo_transporte`, `costo_total_operacion`, `asal_olp_fecha`, `asal_olp_cuota`, `asal_olp_instituciones`, `asal_olp_saldo`, `asal_subtotal_olp_saldo`, `indicador_endeudamiento`, `capital_trabajo_neto`, `porcentaje_margen`, `fcm_valor_canasta_basica`, `fcm_cant_personas_dep`, `total_deuda_acreditar`, `porcentaje_deuda_total`, `disponible_ab`, `cuentas_por_cobrar`, `inventarios_abc`, `cuentas_pagar_corto_plazo`, `cuota_periodica_estim`, `flujo_ventas_contado`, `flujo_recuperacion_credito`, `flujo_compras_contado`, `flujo_gastos_generales`, `flujo_otros_ingresos_fam`, `flujo_gastos_consumo_fam`, `flujo_otros_gastos`, `gasto_personal_basico`, `oblig_largo_plazo1_fecha`, `oblig_largo_plazo1_cuota`, `oblig_largo_plazo1_inst`, `oblig_largo_plazo1_saldo`, `oblig_largo_plazo2_fecha`, `oblig_largo_plazo2_cuota`, `oblig_largo_plazo2_inst`, `oblig_largo_plazo2_saldo`, `oblig_largo_plazo3_fecha`, `oblig_largo_plazo3_cuota`, `oblig_largo_plazo3_inst`, `oblig_largo_plazo3_saldo`, `subtotal_oblig_largo_plazo`, `oblig_corto_plazo1_fecha`, `oblig_corto_plazo1_cuota`, `oblig_corto_plazo1_inst`, `oblig_corto_plazo1_saldo`, `oblig_corto_plazo2_fecha`, `oblig_corto_plazo2_cuota`, `oblig_corto_plazo2_inst`, `oblig_corto_plazo2_saldo`, `oblig_corto_plazo3_fecha`, `oblig_corto_plazo3_cuota`, `oblig_corto_plazo3_inst`, `oblig_corto_plazo3_saldo`, `subtotal_oblig_corto_plazo`, `nivel_endeudamiento`, `total_transporte`, `total_gastos_vivienda`, `tipo_credito`, `monto_financiar`, `plazo_credito`, `num_cuotas`, `monto_cuota`, `fecha_pago`, `frecuencia_pago`, `forma_pago`, `garantia`, `fundamentacion`, `tasa_interes`, `comision_desembolso`, `comentario`) VALUES
(3, 1, '1.00', '2.00', '3.00', '400.00', '400.00', '806.00', '0.00', '0.00', '0.00', '0.00', '6.00', '7.00', '8.00', '9.00', '10.00', '11.00', '12.00', '13.00', '14.00', '90.00', '15.00', '16.00', '17.00', '48.00', '138.00', '668.00', '18.00', '19.00', 20, '0.95', 21, '19.95', '22.00', '23.00', '24.00', '25.00', '26.00', '27.00', '28.00', '37.1111', '58.4500', '29.00', '30.00', '2026-06-04 22:10:35', '2026-06-05 08:59:17', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0.00', '0.00', '0.00', NULL, NULL, NULL, NULL, NULL, '0.0000', '0.00', '0.00', '0.00', 0, '31.00', '0.098000', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', '0.0000', '120.00', '55.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 16, '16963.33', '1200.00', '1300.00', '500.00', '500.00', '20463.33', '20000.00', '1400.00', '1636.67', '0.00', '1.00', '350.00', '200.00', '100.00', '100.00', '40.00', '15.00', '6.00', '3.00', '1428.00', '100.00', '130.00', '10.00', '240.00', '1668.00', '18795.33', '5498.77', '10.00', 20, '0.50', 2, '1.00', '20.00', '20.00', '20.00', '20.00', '20.00', '20.00', '20.00', '0.2926', '4.1100', '12.00', '10.00', '2026-06-10 08:32:24', '2026-06-15 14:18:16', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '613.00', '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0.00', '0.00', '0.00', NULL, NULL, NULL, NULL, NULL, '0.0000', '0.00', '0.00', '0.00', 0, '13.00', '0.012400', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', '0.0000', '100.00', '40.00', NULL, '1200.00', 12, 24, '75.07', NULL, 'quincenal', NULL, NULL, NULL, '0.06', '0.0700', 'Comentario a Yolanda María'),
(5, 17, '13107.50', '1500.00', '5000.00', '10000.00', '4600.00', '34207.50', '15000.00', '1050.00', '842.50', '0.00', '21249.74', '250.00', '100.00', '100.00', '40.00', '65.00', '100.00', '0.00', '100.00', '22004.74', '1.00', '2.00', '3.00', '6.00', '22010.74', '12196.76', '2291.22', '21249.74', 1, '21249.74', 1, '21249.74', '30.00', '10.00', '0.00', '0.00', '0.00', '15.00', '50.00', '0.1879', '27.4000', '0.00', '0.00', '2026-06-10 15:44:46', '2026-06-15 10:21:46', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0.00', '0.00', '0.00', NULL, NULL, NULL, NULL, NULL, '0.0000', '0.00', '0.00', '0.00', 0, '0.00', '0.000200', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', '0.0000', '40.00', '65.00', NULL, '500.00', 12, 24, '31.28', NULL, 'quincenal', NULL, NULL, NULL, '0.06', '0.0700', 'Mi comentario Sol María asalariada'),
(6, 7, '20683.33', '1500.00', '5000.00', '0.00', '0.00', '27183.33', '25000.00', '1750.00', '2566.67', '0.00', '8499.90', '50.00', '0.00', '0.00', '200.00', '100.00', '0.00', '0.00', '0.00', '8849.90', '0.00', '0.00', '0.00', '0.00', '8849.90', '18333.43', '3027.36', '21249.74', 5, '4249.95', 2, '8499.90', '100.00', '100.00', '0.00', '0.00', '0.00', '100.00', '0.00', '0.1651', '0.0000', '0.00', '0.00', '2026-06-11 13:54:28', '2026-06-12 11:52:11', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0.00', '0.00', '0.00', NULL, NULL, NULL, NULL, NULL, '0.0000', '0.00', '0.00', '0.00', 0, '0.00', '0.000000', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', '0.0000', '200.00', '100.00', 'Inversión', '800.00', 16, 32, '41.33', NULL, 'quincenal', NULL, 'Mobiliaria', NULL, '0.06', '0.0700', NULL),
(7, 14, '14688.50', '5000.00', '0.00', '6000.00', '606.00', '26294.50', '17000.00', '1190.00', '1121.50', '0.00', '4249.95', '0.00', '0.00', '0.00', '20.00', '0.00', '0.00', '0.00', '0.00', '4269.95', '0.00', '0.00', '0.00', '0.00', '4269.95', '22024.55', '3025.17', '21249.74', 5, '4249.95', 1, '4249.95', '20.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.1374', '25.8000', '0.00', '0.00', '2026-06-12 13:22:35', '2026-06-12 13:22:35', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0.00', '0.00', '0.00', NULL, NULL, NULL, NULL, NULL, '0.0000', '0.00', '0.00', '0.00', 0, '0.00', '0.000000', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', '0.0000', '20.00', '0.00', 'Inversión', '1000.00', 24, 48, '41.30', NULL, 'quincenal', NULL, 'Sin garantía', NULL, '0.06', '0.0700', NULL),
(8, 3, '20683.33', '0.00', '9600.00', '0.00', '1440.00', '31723.33', '25000.00', '1750.00', '2566.67', '0.00', '10624.87', '60.00', '0.00', '500.00', '250.00', '0.00', '0.00', '1500.00', '400.00', '13584.87', '0.00', '0.00', '0.00', '0.00', '13584.87', '18138.46', '6049.97', '21249.74', 2, '10624.87', 1, '10624.87', '150.00', '100.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.3335', '0.0000', '0.00', '0.00', '2026-06-15 09:36:32', '2026-06-15 09:36:32', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '250.00', '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0.00', '0.00', '0.00', NULL, NULL, NULL, NULL, NULL, '0.0000', '0.00', '0.00', '0.00', 0, '0.00', '0.000000', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', '0.0000', '250.00', '0.00', 'Inversión', '2000.00', 24, 48, '82.60', NULL, 'quincenal', NULL, NULL, NULL, '0.06', '0.0700', NULL),
(9, 8, '34920.00', '0.00', '0.00', '0.00', '400.00', '35320.00', '45000.00', '3150.00', '6930.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '35320.00', '4230.11', '21249.74', 0, '0.00', 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.1198', '0.0000', '0.00', '0.00', '2026-06-15 09:41:15', '2026-06-15 09:41:15', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0.00', '0.00', '0.00', NULL, NULL, NULL, NULL, NULL, '0.0000', '0.00', '0.00', '0.00', 0, '0.00', '0.000000', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', '0.0000', '0.00', '0.00', NULL, '1200.00', 18, 36, '57.75', NULL, 'quincenal', NULL, 'Hipotecaria', NULL, '0.06', '0.0700', NULL),
(10, 18, '20683.33', '5000.00', '4000.00', '3000.00', '3300.00', '35983.33', '25000.00', '1750.00', '2566.67', '0.00', '7083.25', '300.00', '150.00', '150.00', '150.00', '4000.00', '150.00', '150.00', '100.00', '13733.25', '50.00', '5000.00', '0.00', '5050.00', '18783.25', '17200.08', '2722.65', '21249.74', 3, '7083.25', 1, '7083.25', '10.00', '20.00', '30.00', '40.00', '50.00', '1500.00', '2500.00', '0.1583', '0.0000', '0.00', '0.00', '2026-06-15 14:49:32', '2026-06-15 15:34:26', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1500.00', '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0.00', '0.00', '0.00', NULL, NULL, NULL, NULL, NULL, '0.0000', '0.00', '0.00', '0.00', 0, '0.00', '0.000000', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', '0.0000', '150.00', '4000.00', 'Consumo', '900.00', 24, 48, '37.17', NULL, 'quincenal', NULL, 'Hipotecaria', NULL, '0.06', '0.0700', 'Un comentario primero Asalariada Josefina');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_analisis_financiero_comerciante`
--

CREATE TABLE `tb_analisis_financiero_comerciante` (
  `id` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `canasta_basica` decimal(14,2) DEFAULT 0.00,
  `cantidad_promedio` int(11) DEFAULT 0,
  `monto_por_persona` decimal(14,2) DEFAULT 0.00,
  `personas_dependientes` int(11) DEFAULT 0,
  `gastos_alimentacion_canasta` decimal(14,2) DEFAULT 0.00,
  `efectivo_caja` decimal(14,2) DEFAULT 0.00,
  `dinero_banco` decimal(14,2) DEFAULT 0.00,
  `disponible_ab` decimal(14,2) DEFAULT 0.00,
  `cuentas_por_cobrar` decimal(14,2) DEFAULT 0.00,
  `inventario_mercaderia` decimal(14,2) DEFAULT 0.00,
  `productos_proceso` decimal(14,2) DEFAULT 0.00,
  `productos_terminados` decimal(14,2) DEFAULT 0.00,
  `inventarios_abc` decimal(14,2) DEFAULT 0.00,
  `bienes_muebles` decimal(14,2) DEFAULT 0.00,
  `propiedades` decimal(14,2) DEFAULT 0.00,
  `otros_activos` decimal(14,2) DEFAULT 0.00,
  `total_activos_fijos` decimal(14,2) DEFAULT 0.00,
  `total_activos` decimal(14,2) DEFAULT 0.00,
  `cuentas_pagar_proveedores` decimal(14,2) DEFAULT 0.00,
  `cuentas_pagar_corto_plazo` decimal(14,2) DEFAULT 0.00,
  `pasivo_no_corriente` decimal(14,2) DEFAULT 0.00,
  `total_pasivo` decimal(14,2) DEFAULT 0.00,
  `total_patrimonio` decimal(14,2) DEFAULT 0.00,
  `total_pasivo_patrimonio` decimal(14,2) DEFAULT 0.00,
  `ventas_contado` decimal(14,2) DEFAULT 0.00,
  `ventas_credito` decimal(14,2) DEFAULT 0.00,
  `ventas_totales` decimal(14,2) DEFAULT 0.00,
  `costos_venta` decimal(14,2) DEFAULT 0.00,
  `margen_bruto` decimal(14,2) DEFAULT 0.00,
  `gastos_generales` decimal(14,2) DEFAULT 0.00,
  `utilidad_operativa` decimal(14,2) DEFAULT 0.00,
  `cuota_periodica_estim` decimal(14,2) DEFAULT 0.00,
  `flujo_ventas_contado` decimal(14,2) DEFAULT 0.00,
  `flujo_recuperacion_credito` decimal(14,2) DEFAULT 0.00,
  `flujo_compras_contado` decimal(14,2) DEFAULT 0.00,
  `flujo_gastos_generales` decimal(14,2) DEFAULT 0.00,
  `flujo_negocio` decimal(14,2) DEFAULT 0.00,
  `flujo_otros_ingresos_fam` decimal(14,2) DEFAULT 0.00,
  `flujo_gastos_consumo_fam` decimal(14,2) DEFAULT 0.00,
  `flujo_otros_gastos` decimal(14,2) DEFAULT 0.00,
  `flujo_neto_disponible` decimal(14,2) DEFAULT 0.00,
  `gasto_local_alquiler` decimal(14,2) DEFAULT 0.00,
  `gasto_energia` decimal(14,2) DEFAULT 0.00,
  `gasto_agua` decimal(14,2) DEFAULT 0.00,
  `gasto_internet` decimal(14,2) DEFAULT 0.00,
  `gasto_seguridad` decimal(14,2) DEFAULT 0.00,
  `gasto_limpieza` decimal(14,2) DEFAULT 0.00,
  `gasto_personal_basico` decimal(14,2) DEFAULT 0.00,
  `total_gastos_fijos` decimal(14,2) DEFAULT 0.00,
  `oblig_largo_plazo1_fecha` date DEFAULT NULL,
  `oblig_largo_plazo1_cuota` decimal(14,2) DEFAULT 0.00,
  `oblig_largo_plazo1_inst` varchar(100) DEFAULT NULL,
  `oblig_largo_plazo1_saldo` decimal(14,2) DEFAULT 0.00,
  `oblig_largo_plazo2_fecha` date DEFAULT NULL,
  `oblig_largo_plazo2_cuota` decimal(14,2) DEFAULT 0.00,
  `oblig_largo_plazo2_inst` varchar(100) DEFAULT NULL,
  `oblig_largo_plazo2_saldo` decimal(14,2) DEFAULT 0.00,
  `oblig_largo_plazo3_fecha` date DEFAULT NULL,
  `oblig_largo_plazo3_cuota` decimal(14,2) DEFAULT 0.00,
  `oblig_largo_plazo3_inst` varchar(100) DEFAULT NULL,
  `oblig_largo_plazo3_saldo` decimal(14,2) DEFAULT 0.00,
  `subtotal_oblig_largo_plazo` decimal(14,2) DEFAULT 0.00,
  `oblig_corto_plazo1_fecha` date DEFAULT NULL,
  `oblig_corto_plazo1_cuota` decimal(14,2) DEFAULT 0.00,
  `oblig_corto_plazo1_inst` varchar(100) DEFAULT NULL,
  `oblig_corto_plazo1_saldo` decimal(14,2) DEFAULT 0.00,
  `oblig_corto_plazo2_fecha` date DEFAULT NULL,
  `oblig_corto_plazo2_cuota` decimal(14,2) DEFAULT 0.00,
  `oblig_corto_plazo2_inst` varchar(100) DEFAULT NULL,
  `oblig_corto_plazo2_saldo` decimal(14,2) DEFAULT 0.00,
  `oblig_corto_plazo3_fecha` date DEFAULT NULL,
  `oblig_corto_plazo3_cuota` decimal(14,2) DEFAULT 0.00,
  `oblig_corto_plazo3_inst` varchar(100) DEFAULT NULL,
  `oblig_corto_plazo3_saldo` decimal(14,2) DEFAULT 0.00,
  `subtotal_oblig_corto_plazo` decimal(14,2) DEFAULT 0.00,
  `costo_salario_ayudante` decimal(14,2) DEFAULT 0.00,
  `costo_transporte` decimal(14,2) DEFAULT 0.00,
  `costo_total_operacion` decimal(14,2) DEFAULT 0.00,
  `nivel_endeudamiento` decimal(14,4) DEFAULT 0.0000,
  `capital_trabajo_neto` decimal(14,2) DEFAULT 0.00,
  `cobertura_deuda` decimal(14,4) DEFAULT 0.0000,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ingreso_sueldo_neto` decimal(14,2) DEFAULT 0.00,
  `ingreso_comisiones` decimal(14,2) DEFAULT 0.00,
  `ingreso_bonificaciones` decimal(14,2) DEFAULT 0.00,
  `ingreso_remesas` decimal(14,2) DEFAULT 0.00,
  `ingreso_otros` decimal(14,2) DEFAULT 0.00,
  `total_ingresos` decimal(14,2) DEFAULT 0.00,
  `sueldo` decimal(14,2) DEFAULT 0.00,
  `inss` decimal(14,2) DEFAULT 0.00,
  `ir` decimal(14,2) DEFAULT 0.00,
  `sueldo_neto_calc` decimal(14,2) DEFAULT 0.00,
  `gastos_alimentacion` decimal(14,2) DEFAULT 0.00,
  `gastos_servicios` decimal(14,2) DEFAULT 0.00,
  `gastos_vestuario` decimal(14,2) DEFAULT 0.00,
  `gastos_educativos` decimal(14,2) DEFAULT 0.00,
  `gastos_transporte` decimal(14,2) DEFAULT 0.00,
  `gastos_alquiler` decimal(14,2) DEFAULT 0.00,
  `pago_empleado_viatico` decimal(14,2) DEFAULT 0.00,
  `entretenimiento` decimal(14,2) DEFAULT 0.00,
  `otros_gastos` decimal(14,2) DEFAULT 0.00,
  `total_gastos_familiares` decimal(14,2) DEFAULT 0.00,
  `cuotas_prestamos` decimal(14,2) DEFAULT 0.00,
  `pension_alimenticia` decimal(14,2) DEFAULT 0.00,
  `otras_obligaciones` decimal(14,2) DEFAULT 0.00,
  `total_otras_obligaciones` decimal(14,2) DEFAULT 0.00,
  `total_egresos` decimal(14,2) DEFAULT 0.00,
  `flujo_neto_mensual` decimal(14,2) DEFAULT 0.00,
  `cuota_periodica` decimal(14,2) DEFAULT 0.00,
  `transporte_urbano` decimal(14,2) DEFAULT 0.00,
  `transporte_individual` decimal(14,2) DEFAULT 0.00,
  `transporte_interurbano` decimal(14,2) DEFAULT 0.00,
  `recorrido_laboral` decimal(14,2) DEFAULT 0.00,
  `vehiculo_particular` decimal(14,2) DEFAULT 0.00,
  `total_transporte` decimal(14,2) DEFAULT 0.00,
  `alquiler` decimal(14,2) DEFAULT 0.00,
  `casa_propia` decimal(14,2) DEFAULT 0.00,
  `total_gastos_vivienda` decimal(14,2) DEFAULT 0.00,
  `cobertura_garantia` decimal(14,2) DEFAULT 0.00,
  `p_entretenimiento` decimal(14,2) DEFAULT 0.00,
  `total_deuda_acreditar` decimal(14,2) DEFAULT 0.00,
  `porcentaje_deuda_total` decimal(14,6) DEFAULT 0.000000,
  `porcentaje_margen` decimal(5,2) DEFAULT NULL,
  `tc_acumulado` decimal(14,2) DEFAULT 0.00,
  `total_disponible` decimal(14,2) DEFAULT 0.00,
  `cuentas_cobrar` decimal(14,2) DEFAULT 0.00,
  `total_inventarios` decimal(14,2) DEFAULT 0.00,
  `cuentas_pagar_credito` decimal(14,2) DEFAULT 0.00,
  `fcm_ventas_contado` decimal(14,2) DEFAULT 0.00,
  `fcm_recuperacion_credito` decimal(14,2) DEFAULT 0.00,
  `fcm_compras_contado` decimal(14,2) DEFAULT 0.00,
  `fcm_gastos_generales` decimal(14,2) DEFAULT 0.00,
  `fcm_otros_ingresos` decimal(14,2) DEFAULT 0.00,
  `fcm_gastos_consumo` decimal(14,2) DEFAULT 0.00,
  `fcm_otros_gastos` decimal(14,2) DEFAULT 0.00,
  `gasto_personal` decimal(14,2) DEFAULT 0.00,
  `olp_fecha` varchar(255) DEFAULT NULL,
  `olp_cuota` varchar(255) DEFAULT NULL,
  `olp_instituciones` varchar(255) DEFAULT NULL,
  `olp_saldo` varchar(255) DEFAULT NULL,
  `subtotal_olp_saldo` varchar(255) DEFAULT NULL,
  `ocp_fecha` varchar(255) DEFAULT NULL,
  `ocp_cuota` varchar(255) DEFAULT NULL,
  `ocp_instituciones` varchar(255) DEFAULT NULL,
  `ocp_saldo` varchar(255) DEFAULT NULL,
  `subtotal_ocp_saldo` varchar(255) DEFAULT NULL,
  `asal_olp_fecha` varchar(255) DEFAULT NULL,
  `asal_olp_cuota` varchar(255) DEFAULT NULL,
  `asal_olp_instituciones` varchar(255) DEFAULT NULL,
  `asal_olp_saldo` varchar(255) DEFAULT NULL,
  `asal_subtotal_olp_saldo` varchar(255) DEFAULT NULL,
  `indicador_endeudamiento` decimal(14,4) DEFAULT 0.0000,
  `fcm_valor_canasta_basica` decimal(15,2) DEFAULT 0.00,
  `fcm_cant_personas_dep` int(11) DEFAULT 0,
  `tipo_credito` varchar(150) DEFAULT NULL,
  `monto_financiar` decimal(14,2) DEFAULT NULL,
  `plazo_credito` int(11) DEFAULT NULL,
  `num_cuotas` int(11) DEFAULT NULL,
  `monto_cuota` decimal(14,2) DEFAULT NULL,
  `fecha_pago` varchar(50) DEFAULT NULL,
  `frecuencia_pago` varchar(150) DEFAULT NULL,
  `forma_pago` varchar(150) DEFAULT NULL,
  `garantia` varchar(150) DEFAULT NULL,
  `fundamentacion` varchar(200) DEFAULT NULL,
  `tasa_interes` decimal(6,2) DEFAULT NULL,
  `comision_desembolso` decimal(8,4) DEFAULT NULL,
  `comentario` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_analisis_financiero_comerciante`
--

INSERT INTO `tb_analisis_financiero_comerciante` (`id`, `idsolicitud`, `canasta_basica`, `cantidad_promedio`, `monto_por_persona`, `personas_dependientes`, `gastos_alimentacion_canasta`, `efectivo_caja`, `dinero_banco`, `disponible_ab`, `cuentas_por_cobrar`, `inventario_mercaderia`, `productos_proceso`, `productos_terminados`, `inventarios_abc`, `bienes_muebles`, `propiedades`, `otros_activos`, `total_activos_fijos`, `total_activos`, `cuentas_pagar_proveedores`, `cuentas_pagar_corto_plazo`, `pasivo_no_corriente`, `total_pasivo`, `total_patrimonio`, `total_pasivo_patrimonio`, `ventas_contado`, `ventas_credito`, `ventas_totales`, `costos_venta`, `margen_bruto`, `gastos_generales`, `utilidad_operativa`, `cuota_periodica_estim`, `flujo_ventas_contado`, `flujo_recuperacion_credito`, `flujo_compras_contado`, `flujo_gastos_generales`, `flujo_negocio`, `flujo_otros_ingresos_fam`, `flujo_gastos_consumo_fam`, `flujo_otros_gastos`, `flujo_neto_disponible`, `gasto_local_alquiler`, `gasto_energia`, `gasto_agua`, `gasto_internet`, `gasto_seguridad`, `gasto_limpieza`, `gasto_personal_basico`, `total_gastos_fijos`, `oblig_largo_plazo1_fecha`, `oblig_largo_plazo1_cuota`, `oblig_largo_plazo1_inst`, `oblig_largo_plazo1_saldo`, `oblig_largo_plazo2_fecha`, `oblig_largo_plazo2_cuota`, `oblig_largo_plazo2_inst`, `oblig_largo_plazo2_saldo`, `oblig_largo_plazo3_fecha`, `oblig_largo_plazo3_cuota`, `oblig_largo_plazo3_inst`, `oblig_largo_plazo3_saldo`, `subtotal_oblig_largo_plazo`, `oblig_corto_plazo1_fecha`, `oblig_corto_plazo1_cuota`, `oblig_corto_plazo1_inst`, `oblig_corto_plazo1_saldo`, `oblig_corto_plazo2_fecha`, `oblig_corto_plazo2_cuota`, `oblig_corto_plazo2_inst`, `oblig_corto_plazo2_saldo`, `oblig_corto_plazo3_fecha`, `oblig_corto_plazo3_cuota`, `oblig_corto_plazo3_inst`, `oblig_corto_plazo3_saldo`, `subtotal_oblig_corto_plazo`, `costo_salario_ayudante`, `costo_transporte`, `costo_total_operacion`, `nivel_endeudamiento`, `capital_trabajo_neto`, `cobertura_deuda`, `created_at`, `updated_at`, `ingreso_sueldo_neto`, `ingreso_comisiones`, `ingreso_bonificaciones`, `ingreso_remesas`, `ingreso_otros`, `total_ingresos`, `sueldo`, `inss`, `ir`, `sueldo_neto_calc`, `gastos_alimentacion`, `gastos_servicios`, `gastos_vestuario`, `gastos_educativos`, `gastos_transporte`, `gastos_alquiler`, `pago_empleado_viatico`, `entretenimiento`, `otros_gastos`, `total_gastos_familiares`, `cuotas_prestamos`, `pension_alimenticia`, `otras_obligaciones`, `total_otras_obligaciones`, `total_egresos`, `flujo_neto_mensual`, `cuota_periodica`, `transporte_urbano`, `transporte_individual`, `transporte_interurbano`, `recorrido_laboral`, `vehiculo_particular`, `total_transporte`, `alquiler`, `casa_propia`, `total_gastos_vivienda`, `cobertura_garantia`, `p_entretenimiento`, `total_deuda_acreditar`, `porcentaje_deuda_total`, `porcentaje_margen`, `tc_acumulado`, `total_disponible`, `cuentas_cobrar`, `total_inventarios`, `cuentas_pagar_credito`, `fcm_ventas_contado`, `fcm_recuperacion_credito`, `fcm_compras_contado`, `fcm_gastos_generales`, `fcm_otros_ingresos`, `fcm_gastos_consumo`, `fcm_otros_gastos`, `gasto_personal`, `olp_fecha`, `olp_cuota`, `olp_instituciones`, `olp_saldo`, `subtotal_olp_saldo`, `ocp_fecha`, `ocp_cuota`, `ocp_instituciones`, `ocp_saldo`, `subtotal_ocp_saldo`, `asal_olp_fecha`, `asal_olp_cuota`, `asal_olp_instituciones`, `asal_olp_saldo`, `asal_subtotal_olp_saldo`, `indicador_endeudamiento`, `fcm_valor_canasta_basica`, `fcm_cant_personas_dep`, `tipo_credito`, `monto_financiar`, `plazo_credito`, `num_cuotas`, `monto_cuota`, `fecha_pago`, `frecuencia_pago`, `forma_pago`, `garantia`, `fundamentacion`, `tasa_interes`, `comision_desembolso`, `comentario`) VALUES
(1, 1, '0.00', 0, '0.00', 0, '0.00', '5000.00', '10000.00', '0.00', '0.00', '150.00', '150.00', '150.00', '0.00', '150.00', '200.00', '200.00', '550.00', '17400.00', '500.00', '0.00', '50.00', '560.00', '16840.00', '17400.00', '8000.00', '9000.00', '17000.00', '50.00', '16950.00', '50.00', '16900.00', '15.00', '150.00', '250.00', '350.00', '10.00', '190.00', '450.00', '200.00', '10.00', '430.00', '12.00', '34.00', '56.00', '78.00', '910.00', '11.00', '115.00', '1651.00', '2026-06-04', '10.00', 'Lar1', '25.00', '2026-06-04', '20.00', 'Lar2', '20.00', '2026-06-04', '30.00', 'Lar3', '30.00', '75.00', '2026-06-04', '10.00', 'In1', '10.00', '2026-06-04', '20.00', 'In2', '10.00', '2026-06-05', '30.00', 'In3', '30.00', '50.00', '420.00', '15.00', '435.00', '3.2000', '16340.00', '3.5000', '2026-06-04 12:12:15', '2026-06-05 09:24:39', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '15.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '58.45', '0.00', '0.00', '0.000000', '5.00', '0.00', '15000.00', '1400.00', '450.00', '10.00', '150.00', '250.00', '200.00', '10.00', '450.00', '200.00', '10.00', '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0.0000', '120.00', 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 2, '0.00', 0, '0.00', 0, '0.00', '15000.00', '20000.00', '0.00', '0.00', '1500.00', '5.00', '5.00', '0.00', '100.00', '900.00', '1500.00', '2500.00', '45010.00', '3600.00', '0.00', '1000.00', '7100.00', '37910.00', '45010.00', '4500.00', '5600.00', '10100.00', '3000.00', '7100.00', '3000.00', '4100.00', '3525.09', '0.00', '0.00', '0.00', '0.00', '5800.00', '0.00', '0.00', '0.00', '7100.00', '15.00', '150.00', '20.00', '250.00', '20.00', '25.00', '400.00', '6630.00', '2026-06-01', '100.00', 'Com1', '50.00', '2026-06-02', '200.00', 'Com2', '100.00', '2026-06-03', '300.00', 'Com3', '150.00', '300.00', '2026-06-04', '400.00', 'Cor1', '200.00', '2026-06-05', '500.00', 'Cor2', '250.00', '2026-06-06', '600.00', 'Co3', '300.00', '750.00', '5600.00', '150.00', '5750.00', '15.8000', '36410.00', '49.6000', '2026-06-08 09:08:52', '2026-06-08 09:17:00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '3525.09', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '41.10', '0.00', '0.00', '0.000000', '6.00', '0.00', '35000.00', '6000.00', '1510.00', '2500.00', '8000.00', '4000.00', '5700.00', '500.00', '6000.00', '200.00', '4500.00', '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0.0000', '600.00', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 16, '0.00', 0, '0.00', 0, '0.00', '15200.00', '20000.00', '0.00', '0.00', '1000.00', '3.00', '1.00', '0.00', '120.00', '1500.00', '3200.00', '4820.00', '42224.00', '1200.00', '0.00', '1.00', '1351.00', '40873.00', '42224.00', '32000.00', '15000.00', '47000.00', '15000.00', '32000.00', '3000.00', '29000.00', '8247.79', '0.00', '0.00', '0.00', '0.00', '42500.00', '0.00', '0.00', '0.00', '42270.00', '120.00', '60.00', '70.00', '100.00', '1.00', '2.00', '600.00', '1203.00', '2026-06-10', '10.00', 'Prob1', '5.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '5.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', '150.00', '100.00', '250.00', '263.4000', '36054.00', '19.5000', '2026-06-10 08:26:52', '2026-06-15 14:16:19', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '8247.79', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '4.11', '0.00', '0.00', '0.000000', '2.00', '0.00', '35200.00', '1200.00', '1004.00', '150.00', '32000.00', '1500.00', '1500.00', '1200.00', '1200.00', '400.00', '1030.00', '0.00', NULL, NULL, NULL, NULL, '5', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, '0.0000', '1200.00', 2, 'Consumo', '1800.00', 12, 24, '112.60', NULL, 'quincenal', NULL, NULL, NULL, '0.06', '0.0700', NULL),
(4, 17, '0.00', 0, '0.00', 0, '0.00', '16000.00', '68000.00', '0.00', '0.00', '1500.00', '30.00', '20.00', '0.00', '1500.00', '2500.00', '3500.00', '7500.00', '96540.00', '6000.00', '0.00', '200.00', '8200.00', '88340.00', '96540.00', '170000.00', '26000.00', '196000.00', '3000.00', '193000.00', '1700.00', '191300.00', '3525.46', '0.00', '0.00', '0.00', '0.00', '190800.00', '0.00', '0.00', '0.00', '187066.67', '10.00', '20.00', '30.00', '40.00', '50.00', '60.00', '70.00', '450.00', '2026-06-10', '10.00', 'Institución1', '5.00', '2026-06-11', '20.00', 'Institución2', '10.00', '2026-06-12', '40.00', 'Institución3', '20.00', '35.00', '2026-06-14', '50.00', 'Institución4', '25.00', '2026-06-15', '100.00', 'Institución5', '50.00', '2026-06-16', '200.00', 'Institución6', '100.00', '175.00', '80.00', '90.00', '170.00', '65.4000', '81040.00', '1.9000', '2026-06-10 11:27:18', '2026-06-12 15:49:20', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '3525.46', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '27.40', '0.00', '0.00', '0.000000', '2.00', '0.00', '84000.00', '3490.00', '1550.00', '2000.00', '170000.00', '26000.00', '3500.00', '1700.00', '1600.00', '333.33', '5000.00', '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0.0000', '2000.00', 1, 'Consumo', '1000.00', 18, 36, '48.13', NULL, 'quincenal', NULL, NULL, NULL, '0.06', '0.0700', NULL),
(5, 15, '0.00', 0, '0.00', 0, '0.00', '150.00', '250.00', '0.00', '0.00', '2500.00', '20.00', '30.00', '0.00', '5000.00', '2000.00', '1200.00', '8200.00', '16150.00', '1000.00', '0.00', '500.00', '2500.00', '13650.00', '16150.00', '64000.00', '1000.00', '65000.00', '1200.00', '63800.00', '1000.00', '62800.00', '8812.54', '0.00', '0.00', '0.00', '0.00', '64000.00', '0.00', '0.00', '0.00', '75166.67', '125.00', '22.00', '33.00', '44.00', '55.00', '66.00', '77.00', '609.00', '2026-06-15', '10.00', 'In1', '20.00', '2026-06-16', '15.00', 'In2', '20.00', '2026-06-15', '20.00', 'In3', '20.00', '60.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', '88.00', '99.00', '187.00', '355.6000', '5950.00', '11.7000', '2026-06-10 11:57:41', '2026-06-15 14:39:44', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '8812.54', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '21.00', '0.00', '0.00', '0.000000', '1.00', '0.00', '400.00', '5000.00', '2550.00', '1000.00', '64000.00', '1000.00', '0.00', '1000.00', '12000.00', '833.33', '0.00', '0.00', NULL, NULL, NULL, NULL, '60', NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, '0.0000', '5000.00', 1, 'Consumo', '2500.00', 18, 36, '120.31', NULL, 'quincenal', NULL, NULL, NULL, '0.06', '0.0700', NULL),
(6, 7, '0.00', 0, '0.00', 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '23000.00', '0.00', '23000.00', '0.00', '23000.00', '0.00', '23000.00', '2419.40', '0.00', '0.00', '0.00', '0.00', '23000.00', '0.00', '0.00', '0.00', '23000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.0000', '0.00', '10.5000', '2026-06-12 11:25:50', '2026-06-12 11:51:35', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2419.40', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.000000', NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '23000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0.0000', '0.00', 0, 'Inversión', '1000.00', 24, 48, '33.03', NULL, 'quincenal', NULL, NULL, NULL, '0.06', '0.0700', NULL),
(7, 18, '0.00', 0, '0.00', 0, '0.00', '30000.00', '12000.00', '0.00', '0.00', '1500.00', '0.00', '0.00', '0.00', '2000.00', '0.00', '0.00', '2000.00', '47000.00', '2000.00', '0.00', '0.00', '4000.00', '43000.00', '47000.00', '204000.00', '1300.00', '205300.00', '14000.00', '191300.00', '0.00', '191300.00', '4537.75', '0.00', '0.00', '0.00', '0.00', '205300.00', '0.00', '0.00', '0.00', '203200.00', '1600.00', '100.00', '100.00', '100.00', '0.00', '0.00', '1500.00', '3700.00', '2026-06-12', '1200.00', 'In1', '500.00', '2026-06-12', '1000.00', 'In2', '500.00', '2026-06-15', '1200.00', 'In3', '250.00', '1250.00', '2026-06-16', '1200.00', 'c1', '500.00', '2026-06-17', '200.00', 'C2', '20.00', '2026-06-18', '120.00', 'C23', '20.00', '540.00', '100.00', '200.00', '300.00', '125.4000', '41000.00', '2.2000', '2026-06-12 12:35:16', '2026-06-15 14:10:48', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '4537.75', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.000000', '2.00', '0.00', '42000.00', '1500.00', '1500.00', '2000.00', '204000.00', '1300.00', '0.00', '0.00', '5000.00', '7000.00', '100.00', '0.00', NULL, NULL, NULL, NULL, '1250', NULL, NULL, NULL, NULL, '540', NULL, NULL, NULL, NULL, NULL, '0.0000', '21000.00', 2, 'Consumo', '1500.00', 24, 48, '61.95', NULL, 'quincenal', 'Efectivo', NULL, NULL, '0.06', '0.0700', 'Un comentario para Josefina Benavides'),
(8, 3, '0.00', 0, '0.00', 0, '0.00', '25000.00', '6000.00', '0.00', '0.00', '1500.00', '650.00', '150.00', '0.00', '1000.00', '2000.00', '3000.00', '6000.00', '49100.00', '4000.00', '0.00', '500.00', '9000.00', '40100.00', '49100.00', '42000.00', '25000.00', '67000.00', '15000.00', '52000.00', '1500.00', '50500.00', '6050.33', '0.00', '0.00', '0.00', '0.00', '65500.00', '0.00', '0.00', '0.00', '66016.75', '0.00', '10.00', '20.00', '30.00', '0.00', '0.00', '250.00', '310.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '167.5000', '34600.00', '9.2000', '2026-06-15 09:16:04', '2026-06-15 09:16:04', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '6050.33', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.000000', NULL, '0.00', '31000.00', '9800.00', '2300.00', '4500.00', '42000.00', '25000.00', '0.00', '1500.00', '8000.00', '7083.25', '400.00', '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0.0000', '21249.74', 2, 'Inversión', '2000.00', 24, 48, '82.60', NULL, 'quincenal', 'USD', NULL, NULL, '0.06', '0.0700', NULL),
(9, 4, '0.00', 0, '0.00', 0, '0.00', '500.00', '200.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1700.00', '0.00', '0.00', '0.00', '0.00', '1700.00', '1700.00', '25000.00', '0.00', '25000.00', '0.00', '25000.00', '0.00', '25000.00', '2820.07', '0.00', '0.00', '0.00', '0.00', '25000.00', '0.00', '0.00', '0.00', '25000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '2585.2000', '1700.00', '11.3000', '2026-06-15 09:40:53', '2026-06-15 09:40:53', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2820.07', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.000000', NULL, '0.00', '700.00', '1000.00', '0.00', '0.00', '25000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0.0000', '21249.74', 0, 'Consumo', '800.00', 18, 36, '38.50', NULL, 'quincenal', NULL, NULL, NULL, '0.06', '0.0700', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_asesores`
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
-- Volcado de datos para la tabla `tb_asesores`
--

INSERT INTO `tb_asesores` (`idasesor`, `nombres`, `telefono`, `direccion`, `fechaRegistro`, `estado`) VALUES
(0, 'Bmolina', '00001', 'Managua, Nicaragua', '2026-03-07 19:38:09', 1),
(1, 'Oficina', '00002', 'Managua', '2024-02-06 12:07:42', 1),
(2, 'Cpineda', '00003', 'Managua, Nicaragua', '2026-03-07 19:39:34', 1),
(3, 'RLainez', '00004', 'Managua, Nicaragua', '2026-03-07 19:40:18', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_bancos`
--

CREATE TABLE `tb_bancos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) DEFAULT NULL,
  `estado` int(11) DEFAULT 1,
  `tb_bancoscol` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_caja`
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
-- Estructura de tabla para la tabla `tb_caja_movimiento`
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
-- Estructura de tabla para la tabla `tb_centro_costo`
--

CREATE TABLE `tb_centro_costo` (
  `id` int(11) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_centro_costo`
--

INSERT INTO `tb_centro_costo` (`id`, `codigo`, `nombre`, `descripcion`, `activo`, `created_at`) VALUES
(1, '001', 'Gerencia', 'Centro de costo de Gerencia', 1, '2026-01-12 21:40:03'),
(2, '002', 'Administracion', 'Centro de costo de Administracion', 1, '2026-01-12 21:40:03'),
(3, '003', 'Finanzas', 'Centro de costo de Finanzas', 1, '2026-01-12 21:40:03'),
(4, '004', 'Credito', 'Centro de costo de Credito', 1, '2026-01-12 21:40:03'),
(5, '005', 'Cobranza', 'Centro de costo de Cobranza', 1, '2026-01-12 21:40:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_cierres_caja`
--

CREATE TABLE `tb_cierres_caja` (
  `id` int(11) NOT NULL,
  `consecutivo` int(11) NOT NULL,
  `fecha_cierre` datetime DEFAULT current_timestamp(),
  `idusuario` int(11) DEFAULT NULL,
  `monto_total` decimal(18,2) DEFAULT NULL,
  `cantidad_pagos` int(11) DEFAULT 0,
  `estado` varchar(50) DEFAULT 'abierto',
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tb_cierres_caja`
--

INSERT INTO `tb_cierres_caja` (`id`, `consecutivo`, `fecha_cierre`, `idusuario`, `monto_total`, `cantidad_pagos`, `estado`, `observaciones`, `created_at`, `updated_at`) VALUES
(3, 3, '2026-05-13 13:46:54', 15, '123.90', 1, 'cerrado', 'Cierre de caja #3 generado automáticamente', '2026-05-13 18:46:54', '2026-05-13 18:46:54'),
(4, 4, '2026-05-13 15:05:04', 15, '123.90', 1, 'cerrado', 'Cierre de caja #4 generado automáticamente', '2026-05-13 20:05:04', '2026-05-13 20:05:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_cierre_arqueos`
--

CREATE TABLE `tb_cierre_arqueos` (
  `id` int(11) NOT NULL,
  `idcierre_caja` int(11) NOT NULL,
  `monto_cierre_usd` decimal(18,2) NOT NULL DEFAULT 0.00,
  `monto_cierre_nio` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_billetaje_usd` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_billetaje_nio` decimal(18,2) NOT NULL DEFAULT 0.00,
  `diferencia_usd` decimal(18,2) NOT NULL DEFAULT 0.00,
  `diferencia_nio` decimal(18,2) NOT NULL DEFAULT 0.00,
  `comentario_diferencia` text DEFAULT NULL,
  `idbanco` int(11) DEFAULT NULL,
  `estado_deposito` varchar(20) NOT NULL DEFAULT 'pendiente',
  `monto_depositado_total` decimal(18,2) DEFAULT NULL,
  `referencia_minuta` varchar(120) DEFAULT NULL,
  `fecha_deposito` date DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `deposito_movimiento_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_cierre_arqueos`
--

INSERT INTO `tb_cierre_arqueos` (`id`, `idcierre_caja`, `monto_cierre_usd`, `monto_cierre_nio`, `total_billetaje_usd`, `total_billetaje_nio`, `diferencia_usd`, `diferencia_nio`, `comentario_diferencia`, `idbanco`, `estado_deposito`, `monto_depositado_total`, `referencia_minuta`, `fecha_deposito`, `idusuario`, `deposito_movimiento_id`, `created_at`, `updated_at`) VALUES
(1, 3, '123.90', '0.00', '124.00', '0.00', '0.10', '0.00', 'Deposito', 1, 'depositado', '124.00', '09090', '2026-05-13', 15, 8, '2026-05-13 14:15:53', '2026-05-13 14:19:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_cierre_arqueos_series`
--

CREATE TABLE `tb_cierre_arqueos_series` (
  `id` int(11) NOT NULL,
  `idcierre_caja` int(11) NOT NULL,
  `serie_codigo` varchar(20) NOT NULL,
  `monto_cierre_usd` decimal(18,2) NOT NULL DEFAULT 0.00,
  `monto_cierre_nio` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_billetaje_usd` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_billetaje_nio` decimal(18,2) NOT NULL DEFAULT 0.00,
  `diferencia_usd` decimal(18,2) NOT NULL DEFAULT 0.00,
  `diferencia_nio` decimal(18,2) NOT NULL DEFAULT 0.00,
  `comentario_diferencia` text DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `edit_autorizado_por` varchar(150) DEFAULT NULL,
  `edit_comentario` text DEFAULT NULL,
  `edit_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_cierre_arqueo_detalle`
--

CREATE TABLE `tb_cierre_arqueo_detalle` (
  `id` int(11) NOT NULL,
  `idarqueo` int(11) NOT NULL,
  `moneda` varchar(10) NOT NULL,
  `denominacion` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `monto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_cierre_arqueo_detalle`
--

INSERT INTO `tb_cierre_arqueo_detalle` (`id`, `idarqueo`, `moneda`, `denominacion`, `cantidad`, `monto`, `created_at`) VALUES
(10, 1, 'USD', '1.00', 4, '4.00', '2026-05-13 14:19:16'),
(11, 1, 'USD', '5.00', 4, '20.00', '2026-05-13 14:19:16'),
(12, 1, 'USD', '10.00', 10, '100.00', '2026-05-13 14:19:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_cierre_arqueo_serie_detalle`
--

CREATE TABLE `tb_cierre_arqueo_serie_detalle` (
  `id` int(11) NOT NULL,
  `idarqueo_serie` int(11) NOT NULL,
  `moneda` varchar(10) NOT NULL,
  `denominacion` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `monto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_cierre_depositos_pendientes`
--

CREATE TABLE `tb_cierre_depositos_pendientes` (
  `id` int(11) NOT NULL,
  `idcierre_caja` int(11) NOT NULL,
  `moneda_origen` varchar(10) NOT NULL,
  `monto_arqueo` decimal(18,2) NOT NULL DEFAULT 0.00,
  `estado` varchar(20) NOT NULL DEFAULT 'pendiente',
  `moneda_destino` varchar(10) DEFAULT NULL,
  `tasa_cambio` decimal(18,6) DEFAULT NULL,
  `monto_depositado` decimal(18,2) DEFAULT NULL,
  `monto_integrado` decimal(18,2) DEFAULT NULL,
  `idcuenta_banco` int(11) DEFAULT NULL,
  `referencia_minuta` varchar(120) DEFAULT NULL,
  `fecha_deposito` date DEFAULT NULL,
  `movimiento_id` int(11) DEFAULT NULL,
  `enviado_por` int(11) DEFAULT NULL,
  `integrado_por` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `tc_tipo_aplicado` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_clientes`
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
  `rechazado` tinyint(1) NOT NULL DEFAULT 0,
  `fechaActualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `fecha_nacimiento` date DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `estado_civil` varchar(60) DEFAULT NULL,
  `nombre_conyuge` varchar(255) DEFAULT NULL,
  `dni_conyuge` varchar(100) DEFAULT NULL,
  `ocupacion_conyuge` varchar(255) DEFAULT NULL,
  `telefono_conyuge` varchar(100) DEFAULT NULL,
  `numero_dependientes` int(11) DEFAULT NULL,
  `condicion_vivienda` varchar(60) DEFAULT NULL,
  `tiempo_residir_anios` int(11) DEFAULT NULL,
  `tiempo_residir_meses` int(11) DEFAULT NULL,
  `nombre_empresa` varchar(255) DEFAULT NULL,
  `direccion_empresa` varchar(255) DEFAULT NULL,
  `telefono_empresa` varchar(100) DEFAULT NULL,
  `cargo_puesto` varchar(150) DEFAULT NULL,
  `tiempo_empleo_anios` int(11) DEFAULT NULL,
  `tiempo_empleo_meses` int(11) DEFAULT NULL,
  `tipo_contrato` varchar(255) DEFAULT NULL,
  `ingreso_mensual_neto` decimal(15,2) DEFAULT NULL,
  `deducciones` varchar(255) DEFAULT NULL,
  `nombre_negocio` varchar(255) DEFAULT NULL,
  `actividad_economica` varchar(255) DEFAULT NULL,
  `telefono_negocio` varchar(100) DEFAULT NULL,
  `tiempo_operacion_anios` int(11) DEFAULT NULL,
  `tiempo_operacion_meses` int(11) DEFAULT NULL,
  `ventas_buenos_amount` decimal(15,2) DEFAULT NULL,
  `ventas_malos_amount` decimal(15,2) DEFAULT NULL,
  `ventas_promedio_mensual` decimal(15,2) DEFAULT NULL,
  `cedula_cliente` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_clientes`
--

INSERT INTO `tb_clientes` (`idcliente`, `apellidos`, `nombres`, `direccion`, `telefono`, `email`, `tipo_doc`, `numero_doc`, `comentarios`, `estado`, `rechazado`, `fechaActualizacion`, `fecha_nacimiento`, `edad`, `estado_civil`, `nombre_conyuge`, `dni_conyuge`, `ocupacion_conyuge`, `telefono_conyuge`, `numero_dependientes`, `condicion_vivienda`, `tiempo_residir_anios`, `tiempo_residir_meses`, `nombre_empresa`, `direccion_empresa`, `telefono_empresa`, `cargo_puesto`, `tiempo_empleo_anios`, `tiempo_empleo_meses`, `tipo_contrato`, `ingreso_mensual_neto`, `deducciones`, `nombre_negocio`, `actividad_economica`, `telefono_negocio`, `tiempo_operacion_anios`, `tiempo_operacion_meses`, `ventas_buenos_amount`, `ventas_malos_amount`, `ventas_promedio_mensual`, `cedula_cliente`) VALUES
(2, 'FLORES', 'DAYANA TÁMARA BLANDON', 'Bo.costarrica Rotonda Bello horizonte 1 cd oeste 4 cd norte ', '83582199', NULL, 3, '0011009940037C', NULL, NULL, 0, NULL, '1994-09-10', 31, 'Union libre', 'Juan Daniel Gómez Murillo ', '0010910011004N', NULL, '86335531', 1, 'Propia', 32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería Dayana ', 'Pulpería ', '78122671', 8, 5, '3800.00', '2400.00', '4800.00', NULL),
(3, 'Zamora', 'Ana Bellis Gutierrez', 'BO. Pantasma Entrada Principal Centro Comercial 3C. E. 1/2C.N', '57271671', NULL, 0, '0010801890029P', NULL, 1, 0, '2026-03-04 22:01:01', '1989-01-08', 37, 'casada', 'Luddin Sequeira Hernandez', NULL, 'Tecnico en Enderezado Y Pintura', '86294115', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Alemán', 'Aidan Joshua Jiménez', 'Residencial Villa Kelly k13 carretera a Masaya 3ra calle casa número 27', '83624422', NULL, 3, '0010407061032L', NULL, NULL, 0, NULL, '2006-07-04', 19, 'Soltero', NULL, NULL, NULL, NULL, 0, 'Familiar', 8, NULL, 'Ibex', 'Rotonda periodista, 400mt al sur ', NULL, 'Atención al cliente ', 1, NULL, 'Permanente', '17000.00', '1500', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Jara', 'Sérgio Antonio Hurtado', 'San Rafael del sur bo.pinol iglesia católica 4 1/2cd  al Sur ', '84996037', NULL, 3, '0021005610004M', NULL, NULL, 0, NULL, '1961-05-10', 64, 'Union libre', NULL, NULL, NULL, NULL, NULL, 'Propia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Distribuidora Sergio ', 'Venta de abarrotes ', '84996037', 18, NULL, '32000.00', '21400.00', '811600.00', NULL),
(6, 'alemán', 'Elena Auxiliadora reyes', 'Bo.19 de julio hotel Nicaragua 3c. S', '87895937', NULL, 3, '6111109620001x', NULL, NULL, 0, NULL, '1962-09-11', 63, 'Casado', 'Efrain artola arroliga ', '0012705600057U', 'Pensionado ', '78209417', 0, 'Propia', 42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería Elena reyes ', 'Comercio ', '87895937', 10, NULL, '6300.00', '4100.00', '167000.00', NULL),
(7, 'Rodríguez', 'Carmen Del Socorro Herrera', 'Recd altos de motastepe dela rotonda 1 1/2 al sur ', '86406122', NULL, 3, '6012706720003M', NULL, NULL, 0, NULL, '1972-06-27', 53, 'Soltero', NULL, NULL, NULL, NULL, NULL, 'Propia', 20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería las gemelas ', 'Venta de abarrotes ', NULL, NULL, NULL, '2800.00', '1900.00', '3800.00', NULL),
(8, 'Aguirre', 'Emeris Del Carmen Cruz', 'Semáforos de la asamblea nacional 1c al oeste 1/2c al norte 1/2 cuadra al oeste', '77961554', NULL, 3, '5611210680009N', NULL, NULL, 0, NULL, '1968-10-12', 57, 'Casado', NULL, NULL, NULL, NULL, 0, 'Propia', 36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Kioskito cristal', 'Negocio ', '77961554', 32, NULL, '12000.00', '5000.00', '40000.00', NULL),
(9, 'González', 'Rebeca Del Socorro Castillo', 'Bo.Nueva Nicaragua Cementerio milagro de Dios 3cd este1 cd norte ', '87610624', NULL, 3, '0012209850093N', NULL, NULL, 0, NULL, '1985-09-22', 40, 'Union libre', NULL, NULL, NULL, NULL, 1, 'Propia', 14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Variedades Castillo ', 'Variedades ', NULL, 4, NULL, '3200.00', '2100.00', '4200.00', NULL),
(10, 'ROSTRAN', 'BISMARK FRANCISCO RIVAS', 'B. LAURELES SUR DE LA DISTRIBUIDORA GRANDE 3 1/2 AL SUR ', '86687724', NULL, 3, '0012501760049T', NULL, NULL, 0, NULL, '1976-01-25', 50, 'Casado', 'TANIA LISBETH TELLEZ', '0072203880000L', 'Comerciante', '77785417', 1, 'Propia', 32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Super freno la sabana ', 'Negocio ', '86687724', 28, NULL, '50000.00', '15000.00', '90000.00', NULL),
(11, 'Ferrufino', 'Eduardo Antonio', 'Parque los cocos 150 M arriba mano derecha Residencial Bruselas casa A5', '75390140', NULL, 3, '6073010740003N', NULL, NULL, 0, NULL, '1974-10-30', 51, 'Soltero', NULL, NULL, NULL, NULL, 1, 'Familiar', 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Autolavado Hernández ', 'Negocio ', '75390140', 6, NULL, '58000.00', '15000.00', '450000.00', NULL),
(12, 'OBANDO', 'BELLY ALEXANDRA MEJIA', 'Bo.la esperanza plaza Julio Martinez 1 cd oeste 3 1/2sur 1 cd este', '89760607', NULL, 3, '0012205740000V', NULL, NULL, 0, NULL, '1974-05-22', 51, 'Union libre', NULL, NULL, NULL, NULL, NULL, 'Propia', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería belli', 'Venta de abarrotes ', NULL, 3, NULL, '3200.00', '2100.00', '4200.00', NULL),
(13, 'Hernández', 'Gema Cristina Alvarado', 'Bo. Sierra maestra ddf la surtidora Gastón 3c abajo 25v al sur ', '78497489', NULL, 3, '0011907031016S', NULL, NULL, 0, NULL, '2003-07-19', 22, 'Soltero', NULL, NULL, NULL, NULL, 1, 'Familiar', 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Distribuidora de cepillos', 'Negocio ', '78497489', 11, NULL, '8000.00', '1800.00', '30000.00', NULL),
(14, 'MARTINEZ', 'JADITH GABRIELA VALLEJOS', 'Bo.boer ddefue depósito vehicular 4cd este ', '88038173', NULL, 3, '4410609940003J', NULL, NULL, 0, NULL, '1994-09-06', 31, 'Soltero', NULL, NULL, NULL, NULL, NULL, 'Propia', 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería la chelita', 'Venta de abarrotes ', NULL, 3, NULL, '3600.00', '2400.00', NULL, NULL),
(15, 'ayerdis', 'Kenia Nohemí flores', 'Bo. Francisco Salazar semáforo mil metros 6c. 2ce 1/2cn.', '86182299', NULL, 3, '0010208971023j', NULL, NULL, 0, NULL, '1997-08-02', 28, 'Casado', 'Marcos natanael cuadra Vásquez ', '0012311981064n', '0', '58552435', 1, 'Propia', 27, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulperia Kenia ', 'Comercio ', '83182299', 4, NULL, '5900.00', '4000.00', '158000.00', NULL),
(16, 'CARRILLO', 'ERICK RAMIREZ', 'asasddassd', '90909090', NULL, 3, '0012702981004X', NULL, 0, 0, '2026-05-13 09:20:15', '1999-01-01', 27, 'Soltero', 'SDASADSDADA', 'asas', 'cacsaas', '21312', 2, 'Propia', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulperia Tita ', 'Comerciante', '58015297', 1, 1, '7600.00', '500.00', '157000.00', NULL),
(17, 'PALACIOS', 'EDEL JOSE VEGA', 'Resd San Sebastián, casa número 252', '81366667', NULL, 3, '0010702820040T', NULL, NULL, 0, NULL, '1982-02-07', 44, 'Casado', 'SANDRA DANELIA SEQUEIRA CASTILLO', '4412311770020U', 'Estilista', '84901646', 0, 'Propia', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Elisha beauti salón ', 'Negocio ', '57020705', 15, NULL, '25000.00', '9000.00', '100000.00', NULL),
(18, 'CABRERA', 'CAUDIZ YAHOSKA NARVÁEZ', 'Barrio San Isidro de bola segundo porto del parque de ferias 1 1/2 al sur ', '75575593', NULL, 3, '0010410830048S', NULL, NULL, 0, NULL, '1983-10-04', 42, 'Soltero', NULL, NULL, NULL, NULL, 1, 'Familiar', 20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Variedades ya', 'Negocio ', '75575593', 2, NULL, '4500.00', '1800.00', '20000.00', NULL),
(19, 'AGUILAR', 'JONATHAN JESUS LAINEZ', 'Comarca los brasiles. Matadero casi que 2c oeste 2c norte', '84822212', NULL, 3, '2810509021012V', NULL, 0, 0, '2026-03-13 15:12:05', '2002-09-05', 23, 'Soltero', NULL, NULL, NULL, NULL, NULL, 'Familiar', 24, NULL, 'Ferrocon los brasiles', 'Km 15 carretera nueva a león ', '57800428', 'Conductor', 1, 8, 'Permanente', '11500.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'SOZA', 'ILEANA VICTORIA GUILLÉN', 'Villa fraternidad terminal ruta 119 1/2 al sur', '87772868', NULL, 3, '6161504810009R', NULL, NULL, 0, NULL, '1981-04-15', 44, 'Soltero', NULL, NULL, NULL, NULL, 1, 'Familiar', 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Sala de belleza Myzpa', 'Negocio ', '87772868', 10, NULL, '7000.00', '2500.00', '38000.00', NULL),
(21, 'OVIEDO', 'FELIX DAVID QUIROZ', 'K10 carretera vieja león 300 vrs al sur entrada del mini super ', '86290587', NULL, 3, '0010603830035M', NULL, NULL, 0, NULL, '1983-03-06', 43, 'Soltero', NULL, NULL, NULL, NULL, 1, 'Familiar', 20, NULL, 'Sanlasa', 'Semáforos de montoyo 5 al lago 1/2 abajo', '84325222', 'Técnico de telecomunicaciones ', 3, NULL, 'Permanente', '14000.00', '1000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'HERNÁNDEZ', 'MICHAEL ALEXANDER FIGUEROA', 'Bo. Santa rosa puente de desnivel carrera norte 10 vrs sur', '83837045', NULL, 3, '0012011840018V', NULL, NULL, 0, NULL, '1984-11-20', 41, 'Union libre', 'LILLIAM DEL SOCORRO MONTENEGRO URBINA ', '0030909950000P', NULL, '75290009', NULL, 'Propia', 41, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Tienda express ', 'Variedades ', NULL, 8, NULL, '5800.00', '3400.00', NULL, NULL),
(23, 'URBINA', 'SANDRA ELISA ALFARO', 'Plasma los cabros módulo 2 contigo a enacal', '85878058', NULL, 3, '0012012780003T', NULL, NULL, 0, NULL, '1978-12-20', 47, 'Union libre', NULL, NULL, NULL, NULL, NULL, 'Alquilada', 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Distribuidora Alfaro ', 'Venta de abarrotes ', NULL, 4, NULL, '8200.00', '4300.00', NULL, NULL),
(24, 'CUADRA', 'KARLA VANESSA PÉREZ', 'Bo.venezuela clínica Don Bosco 7 cd este 1/2 sur', '86702870', NULL, 3, '0012109740071W', NULL, NULL, 0, NULL, '1974-09-21', 51, NULL, NULL, NULL, NULL, NULL, NULL, 'Propia', 32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería Karla ', 'Venta de abarrotes ', NULL, NULL, NULL, '6200.00', '4108.00', NULL, NULL),
(25, 'FLORES', 'MERCEDES ELVIRA CUARESMA', 'Zonas 8 cuidad sandino costando Sur colegio Bella cruz ', '89613601', NULL, 3, '0012307680050x', NULL, NULL, 0, NULL, '1968-07-23', 57, 'Soltero', NULL, NULL, NULL, NULL, NULL, 'Propia', 50, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería Mercedes ', 'Venta de abarrotes ', '89613601', NULL, NULL, '3100.00', '1800.00', NULL, NULL),
(26, 'CARRIÓN', 'NANCY VERÓNICA ROMERO', 'Monumentos el calvario 1/2 cuadra al sur sabana grande ', '89264586', NULL, 3, '0010604850023U', NULL, NULL, 0, NULL, '1985-04-06', 40, 'Casado', 'SERGIO JOSÉ ZAMORA GONZALES ', '0011001850024K', 'Comerciante ', '86405746', 1, 'Propia', 40, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Delicias zarkeys', 'Negocio ', '89264586', 9, NULL, '35000.00', '23000.00', '750000.00', NULL),
(27, 'HERNÁNDEZ', 'FRANCIS ARICELA ARIAS', 'Bo. Frawley kmt8 1/2carrt  sur contigo carnic', '81023862', NULL, 3, '0012208031027S', NULL, NULL, 0, NULL, '2003-08-22', 22, 'Union libre', NULL, NULL, NULL, NULL, NULL, 'Propia', 21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Comedor FRANCIS', 'Cedor', NULL, NULL, NULL, '2600.00', '1900.00', '3800.00', NULL),
(28, 'CRUZ', 'SANTO ELIODORO VARGAS', 'Antiguo Cine ideal 3 cd oeste ', '828806760000N', NULL, 3, '4432104760000N', NULL, NULL, 0, NULL, '1976-04-21', 49, 'Union libre', NULL, NULL, NULL, NULL, NULL, 'Familiar', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Calzado Elio ', 'Venta de calzado ', NULL, 8, NULL, '3700.00', '2100.00', '4200.00', NULL),
(29, 'LOPEZ', 'JOSE MARIA MORALES', 'Centro de Managua', '78787878', NULL, 3, '0114545457777K', NULL, 0, 0, '2026-05-19 08:26:57', '1975-06-24', 50, 'Soltero', NULL, NULL, NULL, NULL, NULL, 'Propia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Mi negocio', 'Negociar', '22225555', NULL, NULL, '1000.00', '500.00', '17000.00', NULL),
(30, 'MENA', 'MARIA JOSE LOPEZ', 'Centro de la ciudad', '15151515', NULL, 3, '1001515157887L', NULL, 0, 0, '2026-05-19 08:37:55', NULL, 30, 'Soltero', NULL, NULL, NULL, NULL, NULL, 'Familiar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Mis empresas', 'Empresarial', NULL, 10, NULL, '2000.00', '1000.00', '42000.00', NULL),
(31, 'HERNANDEZ', 'LUZ CELESTE PEREZ', 'Mi casa', '88888888', NULL, 3, '4445555558888L', NULL, 0, 0, '2026-05-19 10:23:45', NULL, 40, 'Casado', 'JOSE JOSE LOPEZ PEREZ', '4447777771111M', 'Empresario', '55555555', NULL, 'Familiar', 4, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Mis negocios familiares', 'Negocios', '25656565', 2, 1, '1500.00', '500.00', '25000.00', NULL),
(32, 'MORALES', 'MARIA MARIA MORALES', 'Casa mi casa', '78787878', NULL, 3, '1115555552222S', NULL, 0, 0, '2026-05-19 10:49:55', NULL, 50, 'Soltero', NULL, NULL, NULL, NULL, NULL, 'Familiar', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Mi negocio muy bueno', 'Negocios', '22222222', 1, 1, '100.00', '50.00', '2300.00', NULL),
(33, 'MORALES SUAREZ', 'CARMEN MARIA', 'Direccion domiciliar', '78787878', NULL, 3, '1445252524444D', NULL, 0, 0, '2026-05-19 11:33:57', NULL, 60, 'Soltero', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Mi negocio', 'Negocios', '45454545', NULL, NULL, '100.00', '50.00', '2300.00', NULL),
(34, 'HERNANDEZ', 'MARIO JOSE FERNANDEZ', 'Mi casa', '45454545', NULL, 3, '4545555552222D', NULL, NULL, 0, NULL, NULL, 50, 'Soltero', NULL, '0110505057777L', NULL, NULL, NULL, 'Propia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Mi negocio de negocios', 'Negocios', NULL, NULL, NULL, '1500.00', '500.00', '23000.00', NULL),
(35, 'CRUZ LOPEZ', 'MORA AZUL', 'Mi direccion', '78787878', NULL, 3, '4554411441515L', NULL, 0, 0, '2026-05-19 12:41:49', NULL, 40, 'Soltero', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Mi gran negocio', 'Negocios', NULL, NULL, NULL, '2000.00', '150.00', '40900.00', NULL),
(36, 'JOHNSON OTHER', 'CHRIS JOHN', 'Direccion de lago', '78788787', NULL, 3, '4557878789999J', NULL, 0, 0, '2026-05-19 15:10:03', NULL, 46, 'Soltero', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Mi negocio de pesca', 'Pescar', NULL, NULL, NULL, '4500.00', '1500.00', '87000.00', NULL),
(37, 'TRES CUATRO', 'UNO DOS', 'una direccion', '45454545', NULL, 3, '7778888884444D', NULL, 0, 0, '2026-05-19 15:20:46', NULL, 45, 'Soltero', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1500.00', '200.00', '400.00', NULL),
(38, 'SOLIS CRUZ', 'JONATHAN JOHN', 'Mi direccion', '77771111', NULL, 3, '4445555557878D', NULL, 0, 0, '2026-05-19 15:29:47', NULL, 45, 'Soltero', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Mi buen negocio', 'Cosas', '88888888', NULL, NULL, '1500.00', '200.00', '26000.00', NULL),
(39, 'HERNANDEZ MORALES', 'MARIA KAMILA', 'Dirección de casa de María.', '78787878', NULL, 3, '0011515150000D', NULL, 0, 0, '2026-05-20 08:17:23', '1974-06-20', 51, 'Casado', 'JOSE MARIO LOPEZ FERNANDEZ', '1221515154444D', 'Trabajador', '15151515', 0, 'Familiar', 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ventas de Cosas', 'Vender', '22222225', 2, 1, '4000.00', '1500.00', '79000.00', NULL),
(40, 'GUTIERREZ PEREZ', 'SOL MARIA', 'Casa de Sol y Juan', '25632563', NULL, 0, '1552525259999F', NULL, 0, 0, '2026-05-21 10:33:23', '1994-01-17', 32, 'Casado', 'JUAN JOSE LOPEZ MENDEZ', '4447878781444S', 'Vendedor', '78985456', 0, 'Propia', 8, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ropa en Venta SyJ', 'Vender ropa a gente', '22225555', 4, 2, '5000.00', '1500.00', '81000.00', NULL),
(41, 'LOPEZ BLANDON', 'LUIS GABRIEL', 'Casa del ferretero', '78547854', NULL, 3, '1114444445252D', NULL, 0, 0, '2026-05-21 11:23:27', '1998-07-21', 27, 'Soltero', NULL, NULL, NULL, NULL, NULL, 'Propia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Cosas de Metal', 'Vender herramientas', NULL, NULL, NULL, '8000.00', '1200.00', '108000.00', NULL),
(42, 'FLORES DEL AIRE', 'FLORA MARIA', 'Casa donde viven Flor y Luis', '74589687', NULL, 3, '5552525254444S', NULL, 0, 0, '2026-05-25 09:53:52', '1987-01-13', 39, 'Casado', 'LUIS JUAN GONZALEZ DAVILA', '7471111114545D', 'Trabajador de construcción', '44554455', 0, 'Propia', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Fritanga muy buena', 'Vender comida', '22665522', 1, 1, '4500.00', '1000.00', '64000.00', NULL),
(43, 'NUÑEZ PEREZ', 'YOLANDA MARIA', 'Casa de Yolanda y persona', '88889999', NULL, 0, '0010505056666D', 'Comentario de prueba', 1, 0, '2026-06-05 12:03:13', '2000-06-03', 26, 'Union libre', 'PERSONA CONYUGUE UNO', '0050101014444D', 'Venta de juegos', '63366336', 1, 'Propia', 3, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Juguetes BBB', 'Venta de cosas', '26263636', 1, 1, '6500.00', '2000.00', '106000.00', NULL),
(44, 'JOHNSON OTHER', 'SOL MARIA', 'Casa donde viven Flor y Luis', '74589687', NULL, 3, '0011515150000v', NULL, 0, 0, '2026-06-09 08:07:23', '1995-01-09', 31, 'Union libre', NULL, NULL, NULL, NULL, NULL, 'Propia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ventas de Cosas', NULL, NULL, NULL, NULL, '10000.00', '1000.00', '170000.00', NULL),
(45, 'BENAVIDES', 'JOSEFINA', 'Dirección', '78899878', NULL, 3, '0010202024444L', NULL, 0, 0, '2026-06-12 12:29:42', '1997-05-12', 29, 'Soltero', NULL, NULL, NULL, NULL, NULL, 'Propia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Medicinas en Venta', 'Vender', NULL, NULL, NULL, '10000.00', '6000.00', '204000.00', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_clientes_rechazados`
--

CREATE TABLE `tb_clientes_rechazados` (
  `id` int(11) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `nombres` varchar(80) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `tipo_doc` tinyint(2) DEFAULT NULL,
  `numero_doc` varchar(60) DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `rechazo_motivo` varchar(255) DEFAULT NULL,
  `restaurado_comentario` varchar(255) DEFAULT NULL,
  `restaurado_por` int(11) DEFAULT NULL,
  `restaurado_en` datetime DEFAULT NULL,
  `rechazado_por` int(11) DEFAULT NULL,
  `rechazado_en` datetime DEFAULT current_timestamp(),
  `idcliente_original` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_contratos`
--

CREATE TABLE `tb_contratos` (
  `idcontrato` int(11) NOT NULL,
  `idprestamo` int(11) NOT NULL,
  `template_id` int(11) NOT NULL DEFAULT 0,
  `contenido` longtext DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tb_contratos`
--

INSERT INTO `tb_contratos` (`idcontrato`, `idprestamo`, `template_id`, `contenido`, `created_by`, `created_at`) VALUES
(1, 1, 0, '', 15, '2026-05-13 10:23:11'),
(2, 2, 0, '', 15, '2026-06-08 10:36:24'),
(3, 3, 0, '', 15, '2026-06-08 10:59:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_creditos`
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_credito_detalle`
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_departamentos`
--

CREATE TABLE `tb_departamentos` (
  `id` varchar(2) NOT NULL,
  `nombre` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_detalle_simulacion`
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
-- Estructura de tabla para la tabla `tb_distritos`
--

CREATE TABLE `tb_distritos` (
  `id` varchar(6) NOT NULL,
  `idprovincia` varchar(4) DEFAULT NULL,
  `iddepartamento` varchar(2) DEFAULT NULL,
  `nombre` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_feriados`
--

CREATE TABLE `tb_feriados` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_feriados`
--

INSERT INTO `tb_feriados` (`id`, `fecha`, `motivo`, `activo`, `created_at`) VALUES
(1, '2026-01-01', 'AÃ±o Nuevo', 1, '2025-12-12 11:48:44'),
(2, '2026-04-17', 'Jueves Santo (Semana Santa)', 1, '2025-12-12 11:48:44'),
(3, '2026-04-18', 'Viernes Santo (Semana Santa)', 1, '2025-12-12 11:48:44'),
(4, '2026-04-20', 'Domingo de ResurrecciÃ³n (Semana Santa)', 1, '2025-12-12 11:48:44'),
(5, '2026-05-01', 'DÃ­a del Trabajo', 1, '2025-12-12 11:48:44'),
(6, '2026-07-19', 'DÃ­a de la RevoluciÃ³n', 1, '2025-12-12 11:48:44'),
(7, '2026-08-01', 'TÃ©rmino festividad (ejemplo)', 1, '2025-12-12 11:48:44'),
(8, '2026-09-14', 'Batalla de San Jacinto', 1, '2025-12-12 11:48:44'),
(9, '2026-09-15', 'DÃ­a de la Independencia', 1, '2025-12-12 11:48:44'),
(10, '2026-12-08', 'DÃ­a de la Inmaculada ConcepciÃ³n', 1, '2025-12-12 11:48:44'),
(11, '2026-12-25', 'Navidad', 1, '2025-12-12 11:48:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_garantias`
--

CREATE TABLE `tb_garantias` (
  `id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `cantidad` int(11) DEFAULT 1,
  `marca` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `n_serie` varchar(255) DEFAULT NULL,
  `costo` decimal(14,2) DEFAULT NULL,
  `tiempo_vida` varchar(100) DEFAULT NULL,
  `foto__bak` varchar(255) DEFAULT NULL,
  `foto1` varchar(255) DEFAULT NULL,
  `foto2` varchar(255) DEFAULT NULL,
  `foto3` varchar(255) DEFAULT NULL,
  `foto4` varchar(255) DEFAULT NULL,
  `foto5` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_garantias`
--

INSERT INTO `tb_garantias` (`id`, `solicitud_id`, `nombre`, `cantidad`, `marca`, `modelo`, `n_serie`, `costo`, `tiempo_vida`, `foto__bak`, `foto1`, `foto2`, `foto3`, `foto4`, `foto5`, `created_at`, `updated_at`) VALUES
(1, 1, 'garantia', 1, 'marca', 'modelo', 'serie', '32000.00', '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-13 15:20:50', NULL),
(2, 12, 'Cámara cara', 2, 'Azul', '1234A', '001', '500.00', 'Bueno', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-20 18:03:09', '2026-05-20 18:12:43'),
(3, 12, 'Foto de edificio', 1, 'Rojo', 'MD1', '0012', '15.00', 'Bien', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-20 18:07:56', NULL),
(4, 11, 'Cosa', 1, 'Verde', 'Su', 'dfsa', '100.00', 'Ok', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-20 18:56:08', NULL),
(6, 15, 'Busito', 1, 'Azul', 'BUS3K', '152ABC', '10000.00', 'Bueno', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-25 21:06:31', NULL),
(13, 15, 'Algo', 1, '', '', '', '1500.00', '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-25 21:27:25', NULL),
(14, 14, 'Un parque', 1, 'Verde', 'Parque', '001', '15000.00', 'Cuidado', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-26 16:09:45', NULL),
(17, 14, 'Cosillas', 2, 'fdsafa', 'gdfsgds', 'sdfas', '4272.00', '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-26 17:49:21', NULL),
(18, 16, 'Cámara profesional', 1, 'Negro', '1234A', '2', '4500.00', 'Cuidado', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-05 18:28:17', NULL),
(19, 2, 'Algo caro', 1, 'Verde', 'Caro', '123', '15000.00', 'Ok', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 15:16:35', NULL),
(20, 17, 'Cámara cara', 1, 'Algo', 'Parque', 'G6', '15000.00', 'Bueno', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-09 14:20:06', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_garantias_fotos`
--

CREATE TABLE `tb_garantias_fotos` (
  `id` int(11) NOT NULL,
  `garantia_id` int(11) DEFAULT NULL,
  `solicitud_id` int(11) DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `grupo` varchar(100) DEFAULT NULL,
  `row_index` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_garantias_fotos`
--

INSERT INTO `tb_garantias_fotos` (`id`, `garantia_id`, `solicitud_id`, `filename`, `grupo`, `row_index`, `created_at`) VALUES
(1, 2, 12, 'uploads/garantias/solicitud_12/18620e6c74b4f0e0404be2751932a250.png', NULL, 0, '2026-05-20 13:03:09'),
(2, 2, 12, 'uploads/garantias/solicitud_12/adc682f9799e7092349efc7bfc046f46.png', NULL, 0, '2026-05-20 13:03:53'),
(3, 3, 12, 'uploads/garantias/solicitud_12/e4f50669bc4471721bd7baa7228f0dce.jpg', NULL, 1, '2026-05-20 13:07:56'),
(8, 6, 15, 'uploads/garantias/solicitud_15/22b10357b04a236d356feaadacb4133f.jpg', NULL, 0, '2026-05-25 16:07:03'),
(9, 4, 11, 'uploads/garantias/solicitud_11/b9682185576fe3dfaaec14cec36e1497.jpg', NULL, 0, '2026-05-26 09:31:52'),
(10, 14, 14, 'uploads/garantias/solicitud_14/4717a7f85cad98f97274bd2d85500d43.jpg', NULL, 0, '2026-05-26 11:09:45'),
(12, 18, 16, 'uploads/garantias/solicitud_16/b7981d89a6407e65db7fe7835ea0bf02.png', NULL, 0, '2026-06-05 13:28:17'),
(13, 19, 2, 'uploads/garantias/solicitud_2/80664ececd7c850e6d32a46a760dbfc7.jpg', NULL, 0, '2026-06-08 10:16:35'),
(16, 20, 17, 'uploads/garantias/solicitud_17/df88cdc4f086fd1676773513d81695f8.png', NULL, 0, '2026-06-09 09:20:07'),
(17, 20, 17, 'uploads/garantias/solicitud_17/32987e7b2b2177a86b42b812b779c76c.jpg', NULL, 0, '2026-06-09 09:21:37'),
(18, 20, 17, 'uploads/garantias/solicitud_17/f90b059ecbbd788265a27323f0e161d2.png', NULL, 0, '2026-06-09 09:21:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_garantias_verificaciones`
--

CREATE TABLE `tb_garantias_verificaciones` (
  `id` int(11) NOT NULL,
  `garantia_id` int(11) DEFAULT NULL,
  `nombre_garantia` varchar(255) DEFAULT NULL,
  `estado_aprobacion` varchar(50) DEFAULT 'No aprobado',
  `solicitud_id` int(11) DEFAULT NULL,
  `verificador_usuario` varchar(150) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `foto1` varchar(255) DEFAULT NULL,
  `foto2` varchar(255) DEFAULT NULL,
  `foto3` varchar(255) DEFAULT NULL,
  `foto4` varchar(255) DEFAULT NULL,
  `foto5` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_garantias_verificaciones`
--

INSERT INTO `tb_garantias_verificaciones` (`id`, `garantia_id`, `nombre_garantia`, `estado_aprobacion`, `solicitud_id`, `verificador_usuario`, `comentario`, `foto1`, `foto2`, `foto3`, `foto4`, `foto5`, `created_at`) VALUES
(1, 9999, NULL, 'No aprobado', 9999, 'TEST_USER', 'test insert', NULL, NULL, NULL, NULL, NULL, '2026-05-20 18:49:54'),
(2, 2, 'Cámara cara', 'Aprobado', 12, 'ADMINISTRADOR', 'Mi verificación', NULL, NULL, NULL, NULL, NULL, '2026-05-20 18:50:32'),
(3, 4, NULL, 'No aprobado', 11, 'ADMINISTRADOR', 'Crear verificación', 'uploads/garantias/solicitud_11/verificaciones/47ecb251c4dfa316604e26f7fa02581f.jpg', NULL, NULL, NULL, NULL, '2026-05-20 21:12:30'),
(11, NULL, NULL, 'No aprobado', 14, 'ADMINISTRADOR', NULL, 'uploads/garantias/solicitud_14/verificaciones/23a10aa85bb60f8529f85ab0459a2085.jpg', 'uploads/garantias/solicitud_14/verificaciones/ba532e3569a9dde3f2e0facfaa1968da.png', 'uploads/garantias/solicitud_14/verificaciones/0c0909c4d7b3348a9acdeadc699f5088.jpg', NULL, NULL, '2026-05-26 16:25:21'),
(12, 15, 'Una foto de persona', 'No aprobado', 14, 'ADMINISTRADOR', 'Verifico persona', NULL, NULL, NULL, NULL, NULL, '2026-05-26 16:25:21'),
(13, 14, 'Un parque', 'Aprobado', 14, 'ADMINISTRADOR', 'Verifico otra vez solo parque y pongo aprobado', NULL, NULL, NULL, NULL, NULL, '2026-05-26 16:25:21'),
(14, NULL, NULL, 'No aprobado', 1, 'ADMINISTRADOR', NULL, 'uploads/garantias/solicitud_1/verificaciones/7ba6866d0e10a92fc01eaceef688d3e6.jpg', NULL, NULL, NULL, NULL, '2026-05-26 17:11:23'),
(15, 1, 'garantia', 'Aprobado', 1, 'ADMINISTRADOR', 'Primera garantía', NULL, NULL, NULL, NULL, NULL, '2026-05-26 17:11:24'),
(16, NULL, NULL, 'No aprobado', 12, 'ADMINISTRADOR', NULL, 'uploads/garantias/solicitud_12/verificaciones/8845bf84518b3964b27736cf546d1cd6.jpg', 'uploads/garantias/solicitud_12/verificaciones/ef564533af7720a9207b906d9787912c.jpg', NULL, NULL, NULL, '2026-05-26 17:13:35'),
(17, 3, 'Foto de edificio', 'Aprobado', 12, 'ADMINISTRADOR', 'Edificio', NULL, NULL, NULL, NULL, NULL, '2026-05-26 17:13:35'),
(18, NULL, NULL, 'No aprobado', 15, 'ADMINISTRADOR', NULL, 'uploads/garantias/solicitud_15/verificaciones/6699fd5f421d70bb419aeeff6728d53c.jpg', NULL, NULL, NULL, NULL, '2026-05-26 17:33:27'),
(19, 13, 'Algo', 'Aprobado', 15, 'ADMINISTRADOR', 'Algo verf', NULL, NULL, NULL, NULL, NULL, '2026-05-26 17:33:27'),
(20, 12, 'Cosas', 'No aprobado', 15, 'ADMINISTRADOR', 'Cosas verf', NULL, NULL, NULL, NULL, NULL, '2026-05-26 17:33:27'),
(21, 6, 'Busito', 'Aprobado', 15, 'ADMINISTRADOR', 'busito verf', NULL, NULL, NULL, NULL, NULL, '2026-05-26 17:33:27'),
(22, 17, 'Cosillas', 'No aprobado', 14, 'ADMINISTRADOR', 'Están en mal estado', NULL, NULL, NULL, NULL, NULL, '2026-05-26 17:50:03'),
(24, NULL, NULL, 'No aprobado', 16, 'ADMINISTRADOR', NULL, 'uploads/garantias/solicitud_16/verificaciones/1cfebcad672af5125068eb199d5c4386.jpg', NULL, NULL, NULL, NULL, '2026-06-05 18:28:56'),
(25, 18, 'Cámara profesional', 'Aprobado', 16, 'ADMINISTRADOR', 'Verifico estado bueno', NULL, NULL, NULL, NULL, NULL, '2026-06-05 18:28:56'),
(26, NULL, NULL, 'No aprobado', 2, 'ADMINISTRADOR', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 16:26:45'),
(27, 19, 'Algo caro', 'Aprobado', 2, 'ADMINISTRADOR', 'Aprobado hoy sin foto', NULL, NULL, NULL, NULL, NULL, '2026-06-08 16:26:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_journal`
--

CREATE TABLE `tb_journal` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `total_debit` decimal(14,2) DEFAULT 0.00,
  `total_credit` decimal(14,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `source_type` varchar(50) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `voided` tinyint(1) NOT NULL DEFAULT 0,
  `voided_by` int(11) DEFAULT NULL,
  `voided_at` datetime DEFAULT NULL,
  `period_month` varchar(2) DEFAULT NULL,
  `period_year` int(11) DEFAULT NULL,
  `entry_type` varchar(50) DEFAULT NULL,
  `centro_costo_id` int(11) DEFAULT NULL,
  `posted` tinyint(1) NOT NULL DEFAULT 0,
  `posted_by` int(11) DEFAULT NULL,
  `posted_at` datetime DEFAULT NULL,
  `document_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_journal`
--

INSERT INTO `tb_journal` (`id`, `date`, `description`, `total_debit`, `total_credit`, `created_at`, `created_by`, `source_type`, `source_id`, `voided`, `voided_by`, `voided_at`, `period_month`, `period_year`, `entry_type`, `centro_costo_id`, `posted`, `posted_by`, `posted_at`, `document_type`) VALUES
(24, '2026-06-04', 'Asiento de prueba', '6500.00', '6500.00', '2026-06-04 09:55:17', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'CD', NULL, 1, 15, '2026-06-04 09:55:30', 'CD'),
(25, '2026-06-10', 'Una descrip.', '1500.00', '1500.00', '2026-06-09 14:58:05', NULL, 'teso_movimiento', 13, 0, NULL, NULL, NULL, NULL, 'CT', NULL, 0, NULL, NULL, 'CT'),
(26, '2026-06-12', 'Descripción', '1000.00', '1000.00', '2026-06-12 14:53:05', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'CE', NULL, 1, 15, '2026-06-12 14:54:00', 'CE'),
(27, '2026-07-07', 'desc', '2000.00', '2000.00', '2026-06-12 15:00:30', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'CT', NULL, 1, 15, '2026-06-12 15:00:52', 'CT'),
(28, '2026-06-12', 'Desc', '1000.00', '1000.00', '2026-06-12 15:40:06', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'CE', NULL, 1, 15, '2026-06-12 15:41:01', 'CE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_journal_entry`
--

CREATE TABLE `tb_journal_entry` (
  `id` int(11) NOT NULL,
  `journal_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `debit` decimal(14,2) DEFAULT 0.00,
  `credit` decimal(14,2) DEFAULT 0.00,
  `description` varchar(512) DEFAULT NULL,
  `centro_costo_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_journal_entry`
--

INSERT INTO `tb_journal_entry` (`id`, `journal_id`, `account_id`, `debit`, `credit`, `description`, `centro_costo_id`) VALUES
(105, 24, 3, '6500.00', '0.00', 'Asiento de prueba', 2),
(106, 24, 4, '0.00', '6500.00', 'Asiento de prueba', 2),
(107, 25, 6, '1500.00', '0.00', 'det', 2),
(108, 25, 28, '0.00', '1500.00', 'Una descrip.', 2),
(109, 26, 9, '1000.00', '0.00', 'Descripción', 1),
(110, 26, 6, '0.00', '1000.00', 'Descripción', 3),
(111, 27, 8, '0.00', '2000.00', 'desc', 1),
(112, 27, 14, '2000.00', '0.00', 'desc', 3),
(113, 28, 7, '1000.00', '0.00', 'Desc', 1),
(114, 28, 12, '0.00', '1000.00', 'Desc', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_ledger`
--

CREATE TABLE `tb_ledger` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `period` varchar(20) DEFAULT NULL,
  `debit` decimal(14,2) DEFAULT 0.00,
  `credit` decimal(14,2) DEFAULT 0.00,
  `balance` decimal(14,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_ledger`
--

INSERT INTO `tb_ledger` (`id`, `account_id`, `period`, `debit`, `credit`, `balance`) VALUES
(1, 126, '2026-05', '0.00', '0.00', '100000.00'),
(3, 6, '2026-05', '0.00', '0.00', '15000.00'),
(5, 7, '2026-05', '0.00', '0.00', '4000.00'),
(7, 8, '2026-05', '0.00', '0.00', '5950.00'),
(9, 42, '2026-05', '0.00', '0.00', '150.00'),
(19, 77, '2026-06', '0.00', '0.00', '11000.00'),
(21, 126, '2026-06', '12610.00', '12610.00', '5000.00'),
(23, 6, '2026-06', '100.00', '1000.00', '-1900.00'),
(62, 87, '2026-06', '0.00', '100.00', '-100.00'),
(63, 6, '2026-07', '800.00', '0.00', '800.00'),
(64, 98, '2026-07', '0.00', '800.00', '-800.00'),
(65, 6, '2026-08', '0.00', '1000.00', '-1000.00'),
(66, 32, '2026-08', '1000.00', '0.00', '1000.00'),
(67, 106, '2026-06', '4800.00', '0.00', '4800.00'),
(68, 156, '2026-06', '0.00', '4800.00', '-4800.00'),
(69, 105, '2026-06', '3000.00', '0.00', '3000.00'),
(70, 109, '2026-06', '0.00', '3000.00', '-3000.00'),
(71, 8, '2026-06', '6000.00', '0.00', '6000.00'),
(72, 85, '2026-06', '0.00', '6000.00', '-6000.00'),
(73, 82, '2026-06', '6500.00', '0.00', '6500.00'),
(74, 3, '2026-06', '6500.00', '6500.00', '6500.00'),
(75, 81, '2026-06', '7800.00', '0.00', '7800.00'),
(76, 47, '2026-06', '0.00', '7800.00', '-7800.00'),
(78, 4, '2026-06', '0.00', '6500.00', '-6500.00'),
(79, 9, '2026-06', '1000.00', '0.00', '1000.00'),
(81, 8, '2026-07', '0.00', '2000.00', '-2000.00'),
(82, 14, '2026-07', '2000.00', '0.00', '2000.00'),
(83, 7, '2026-06', '1000.00', '0.00', '1000.00'),
(84, 12, '2026-06', '0.00', '1000.00', '-1000.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_monedas`
--

CREATE TABLE `tb_monedas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) DEFAULT NULL,
  `simbolo` varchar(6) DEFAULT NULL,
  `estado` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_monedas`
--

INSERT INTO `tb_monedas` (`id`, `nombre`, `simbolo`, `estado`) VALUES
(1, 'CORDOBAS', 'C$', 1),
(3, 'DOLARES', '$', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_pagos`
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
-- Estructura de tabla para la tabla `tb_pagos_detalle`
--

CREATE TABLE `tb_pagos_detalle` (
  `pdid` int(11) NOT NULL,
  `idpago` int(11) DEFAULT NULL,
  `idcuota` int(11) DEFAULT NULL,
  `monto_pagado` decimal(18,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_perfil_integral_cliente`
--

CREATE TABLE `tb_perfil_integral_cliente` (
  `id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `primer_apellido` varchar(255) DEFAULT NULL,
  `segundo_apellido` varchar(255) DEFAULT NULL,
  `tipo_documento` varchar(50) DEFAULT NULL,
  `numero_documento` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `celular` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `estado_civil` varchar(50) DEFAULT NULL,
  `ocupacion` varchar(150) DEFAULT NULL,
  `empresa` varchar(255) DEFAULT NULL,
  `ingreso_mensual` decimal(14,2) DEFAULT NULL,
  `antiguedad_laboral` varchar(50) DEFAULT NULL,
  `otros` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `fecha_perfil` date DEFAULT NULL,
  `nivel_riesgo` varchar(50) DEFAULT NULL,
  `tipo_ddc` varchar(100) DEFAULT NULL,
  `en_su_propio_pais` tinyint(1) DEFAULT 0,
  `es_funcionario_publico` tinyint(1) DEFAULT 0,
  `cargo_funcionario` varchar(255) DEFAULT NULL,
  `pais_emision_documento` varchar(150) DEFAULT NULL,
  `categoria_otro` varchar(255) DEFAULT NULL,
  `zona_cobertura` varchar(50) DEFAULT NULL,
  `sitio_web_centro_trabajo` varchar(255) DEFAULT NULL,
  `ingreso_mensual_usd` decimal(14,2) DEFAULT NULL,
  `ingreso_mensual_cordobas` decimal(14,2) DEFAULT NULL,
  `conyuge_profesion` varchar(150) DEFAULT NULL,
  `conyuge_ocupacion_actual` varchar(150) DEFAULT NULL,
  `conyuge_nombre_centro_trabajo` varchar(255) DEFAULT NULL,
  `conyuge_direccion_centro_trabajo` varchar(255) DEFAULT NULL,
  `conyuge_email_centro_trabajo` varchar(150) DEFAULT NULL,
  `conyuge_sitio_web` varchar(255) DEFAULT NULL,
  `conyuge_telefono_centro_trabajo` varchar(50) DEFAULT NULL,
  `conyuge_fax_centro_trabajo` varchar(50) DEFAULT NULL,
  `conyuge_apartado_postal` varchar(100) DEFAULT NULL,
  `conyuge_ingreso_usd` decimal(14,2) DEFAULT NULL,
  `conyuge_ingreso_cordobas` decimal(14,2) DEFAULT NULL,
  `documento_legal_1_pais_emision` varchar(100) DEFAULT NULL,
  `documento_legal_2_pais_emision` varchar(100) DEFAULT NULL,
  `actividad_esperada_json` text DEFAULT NULL,
  `segundo_nombre` text DEFAULT NULL,
  `sexo` text DEFAULT NULL,
  `n_dependientes` text DEFAULT NULL,
  `nombre_conocido` text DEFAULT NULL,
  `pais_nacimiento` text DEFAULT NULL,
  `categoria_empleo` text DEFAULT NULL,
  `origen_fondos` text DEFAULT NULL,
  `proposito_relacion` text DEFAULT NULL,
  `actividad_esperada` text DEFAULT NULL,
  `conyuge_primer_nombre` varchar(150) DEFAULT NULL,
  `conyuge_segundo_nombre` varchar(150) DEFAULT NULL,
  `conyuge_primer_apellido` varchar(150) DEFAULT NULL,
  `conyuge_segundo_apellido` varchar(150) DEFAULT NULL,
  `conyuge_direccion` varchar(255) DEFAULT NULL,
  `conyuge_telefono_domicilio` varchar(50) DEFAULT NULL,
  `conyuge_celular` varchar(50) DEFAULT NULL,
  `conyuge_email_personal` varchar(255) DEFAULT NULL,
  `doc1_tipo` varchar(150) DEFAULT NULL,
  `doc1_numero` varchar(150) DEFAULT NULL,
  `doc1_registro` varchar(150) DEFAULT NULL,
  `doc1_fecha_emision` date DEFAULT NULL,
  `doc1_vencimiento` date DEFAULT NULL,
  `doc2_tipo` varchar(150) DEFAULT NULL,
  `doc2_numero` varchar(150) DEFAULT NULL,
  `doc2_registro` varchar(150) DEFAULT NULL,
  `doc2_fecha_emision` date DEFAULT NULL,
  `doc2_vencimiento` date DEFAULT NULL,
  `tipo_relacion` text DEFAULT NULL,
  `tipo_relacion_otro` varchar(255) DEFAULT NULL,
  `origen_otros` varchar(255) DEFAULT NULL,
  `numero_registro` varchar(100) DEFAULT NULL,
  `fecha_emision_documento` date DEFAULT NULL,
  `fecha_vencimiento_documento` date DEFAULT NULL,
  `documento_legal_1_numero` varchar(100) DEFAULT NULL,
  `documento_legal_1_fecha_emision` date DEFAULT NULL,
  `documento_legal_1_fecha_vencimiento` date DEFAULT NULL,
  `documento_legal_2_numero` varchar(100) DEFAULT NULL,
  `documento_legal_2_fecha_emision` date DEFAULT NULL,
  `documento_legal_2_fecha_vencimiento` date DEFAULT NULL,
  `matriz_score` text DEFAULT NULL,
  `matriz_answers` text DEFAULT NULL,
  `actividad_esperada_observaciones` text DEFAULT NULL,
  `nombre_conyuge` text DEFAULT NULL,
  `fax_centro_trabajo` varchar(150) DEFAULT NULL,
  `email_centro_trabajo` varchar(150) DEFAULT NULL,
  `doc1_municipio_emision_documento` varchar(150) DEFAULT NULL,
  `doc2_municipio_emision_documento` varchar(150) DEFAULT NULL,
  `profesion` varchar(150) DEFAULT NULL,
  `apartado_postal` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_perfil_integral_cliente`
--

INSERT INTO `tb_perfil_integral_cliente` (`id`, `solicitud_id`, `nombre`, `primer_apellido`, `segundo_apellido`, `tipo_documento`, `numero_documento`, `fecha_nacimiento`, `telefono`, `celular`, `email`, `direccion`, `ciudad`, `estado_civil`, `ocupacion`, `empresa`, `ingreso_mensual`, `antiguedad_laboral`, `otros`, `created_at`, `updated_at`, `fecha_perfil`, `nivel_riesgo`, `tipo_ddc`, `en_su_propio_pais`, `es_funcionario_publico`, `cargo_funcionario`, `pais_emision_documento`, `categoria_otro`, `zona_cobertura`, `sitio_web_centro_trabajo`, `ingreso_mensual_usd`, `ingreso_mensual_cordobas`, `conyuge_profesion`, `conyuge_ocupacion_actual`, `conyuge_nombre_centro_trabajo`, `conyuge_direccion_centro_trabajo`, `conyuge_email_centro_trabajo`, `conyuge_sitio_web`, `conyuge_telefono_centro_trabajo`, `conyuge_fax_centro_trabajo`, `conyuge_apartado_postal`, `conyuge_ingreso_usd`, `conyuge_ingreso_cordobas`, `documento_legal_1_pais_emision`, `documento_legal_2_pais_emision`, `actividad_esperada_json`, `segundo_nombre`, `sexo`, `n_dependientes`, `nombre_conocido`, `pais_nacimiento`, `categoria_empleo`, `origen_fondos`, `proposito_relacion`, `actividad_esperada`, `conyuge_primer_nombre`, `conyuge_segundo_nombre`, `conyuge_primer_apellido`, `conyuge_segundo_apellido`, `conyuge_direccion`, `conyuge_telefono_domicilio`, `conyuge_celular`, `conyuge_email_personal`, `doc1_tipo`, `doc1_numero`, `doc1_registro`, `doc1_fecha_emision`, `doc1_vencimiento`, `doc2_tipo`, `doc2_numero`, `doc2_registro`, `doc2_fecha_emision`, `doc2_vencimiento`, `tipo_relacion`, `tipo_relacion_otro`, `origen_otros`, `numero_registro`, `fecha_emision_documento`, `fecha_vencimiento_documento`, `documento_legal_1_numero`, `documento_legal_1_fecha_emision`, `documento_legal_1_fecha_vencimiento`, `documento_legal_2_numero`, `documento_legal_2_fecha_emision`, `documento_legal_2_fecha_vencimiento`, `matriz_score`, `matriz_answers`, `actividad_esperada_observaciones`, `nombre_conyuge`, `fax_centro_trabajo`, `email_centro_trabajo`, `doc1_municipio_emision_documento`, `doc2_municipio_emision_documento`, `profesion`, `apartado_postal`) VALUES
(1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-19 14:53:29', NULL, NULL, 'Medio', NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '450', '[\"empleado\",\"construccion\",\"garantia_hipotecaria\",\"edad_21_39\",\"pep_si\",\"frecuente_no\",\"zona_managua\",\"valor_usd_500_1000\"]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 12, 'MARIA KAMILA DEL SOCORRO', 'HERNANDEZ MORALES', NULL, 'Cedula Identidad', '0011515150000D', '1974-06-20', '78787878', '78787878', 'mariadelsocorro@ejemplo.com', 'Dirección de casa de María.', 'Managua', 'Casado', 'Dueña', 'Mi empresa', '79600.00', '4 años', 'Nada que añadir', '2026-05-20 16:37:12', '2026-05-20 21:44:32', '2026-05-20', 'Alto', 'DDC-I', 1, 1, 'Propietaria', 'Nicaragua', 'Una categoría', 'Nacional', NULL, '2156.11', '79000.00', 'Nada', 'Hacer nada', 'Mi trabajito', 'El centro de managua', 'trabajoconyuge@correo.com', 'trabajo.com', '56565656', '123456789', '111111', '500.00', '15000.00', NULL, NULL, '[{\"numero_transacciones\":\"24\",\"monto_promedio\":\"165.19\",\"periodo\":\"24 meses\"}]', NULL, 'F', '0', 'Maria', 'Nicaragua', 'Empleado', '[\"Ahorro\",\"Transferencia de fondos\",\"salarios\",\"Negocios\"]', 'Tener dinero', NULL, NULL, NULL, NULL, NULL, 'Misma de María', '14111411', '78565874', 'joseconyugue@ejemplo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[\"Compra y venta de bienes inmobiliarios\",\"Administraci\\u00f3n de dinero, valores u otros activos\"]', 'Nada de relación de negocios', 'Nada de origen', NULL, '2026-05-12', '2026-06-03', NULL, NULL, NULL, NULL, NULL, NULL, '675', '[\"tipo_juridica\",\"propietario\",\"comercio_servicios\",\"garantia_hipotecaria\",\"edad_40_55\",\"pep_si\",\"frecuente_si\",\"zona_managua\",\"valor_usd_1500_2000\"]', 'Nada que observar -1', 'JOSE MARIO LOPEZ FERNANDEZ D.', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 15, 'FLORA MARIA', 'FLORES DEL AIRE', NULL, NULL, '5552525254444S', '1987-01-13', '74589687', '74589687', NULL, 'Casa donde viven Flor y Luis', NULL, 'Casado', NULL, NULL, '64660.00', NULL, 'Nada que observar', '2026-05-25 19:06:29', '2026-05-25 19:42:08', '2026-05-25', 'Medio', 'DDC-S', 0, 0, NULL, NULL, NULL, NULL, NULL, '1746.72', '64000.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[{\"numero_transacciones\":\"24\",\"monto_promedio\":\"123.90\",\"periodo\":\"24 meses\"}]', NULL, 'F', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '550', '[\"tipo_juridica\",\"propietario\",\"profesionales\",\"garantia_prendaria\",\"edad_40_55\",\"pep_no\",\"frecuente_no\",\"zona_chinandega\",\"valor_usd_1000_1500\"]', NULL, 'LUIS JUAN GONZALEZ DAVILA', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 16, 'YOLANDA MARIA', 'NUÑEZ PEREZ', NULL, 'Cedula Identidad', '0010505056666D', '2000-06-03', '88889999', '88889999', 'yolprueba@unemail.com', 'Casa de Yolanda y persona', 'Managua', 'Union libre', 'Dueña', 'Empresarial', '106600.00', '2 meses', 'Observo promotorialmente', '2026-06-05 18:21:28', '2026-06-08 19:16:31', NULL, 'Alto', 'DDC-I', 1, 0, 'Sin cargo', 'Nicaragua', 'Categoría Ok', 'Nacional', 'www.juguetes.com', '2893.01', '106000.00', 'Vendedor', 'Vendedor', 'Tienda de más juegos', 'Una casa en el centro', 'nohay@unemail.com', 'www.vengocosas.com', '78877887', '001001', '1551', '200.00', '6000.00', 'Nicaragua', 'Nicaragua', '[{\"numero_transacciones\":\"20\",\"monto_promedio\":\"272.05\",\"periodo\":\"20 meses\"}]', NULL, 'F', '1', 'Yolanda', 'Nicaragua', 'Negocio propio', '[\"Pr\\u00e9stamo\",\"Ahorro\",\"Salarios\"]', 'Quiero dinero', NULL, NULL, NULL, NULL, NULL, 'Vive con solicitante', '22525252', '45454544', 'personaconyugue@unemail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[\"Otro\"]', 'Indico otro', NULL, 'ABC123', '2023-08-06', '2027-06-05', 'doc1', '2026-06-08', '2026-12-01', 'doc2', '2026-06-08', '2027-04-14', '550', '[\"tipo_natural\",\"propietario\",\"garantia_hipotecaria\",\"edad_21_39\",\"pep_si\",\"frecuente_recomendado\",\"zona_managua\",\"valor_usd_2000_5000\"]', NULL, 'PERSONA CONYUGUE UNO', 'mifax123', 'emailtrabajoclint@email.com', 'Managua', 'Tipitapa', 'Administradora', '123456');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_period_lock`
--

CREATE TABLE `tb_period_lock` (
  `id` int(11) NOT NULL,
  `year` int(4) NOT NULL,
  `month` int(2) NOT NULL,
  `closed_by` int(11) DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `tb_period_lock`
--

INSERT INTO `tb_period_lock` (`id`, `year`, `month`, `closed_by`, `closed_at`, `notes`) VALUES
(2, 2026, 5, 15, '2026-06-01 11:19:39', 'Cerrando mayo el primer día de Junio');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_prestamos`
--

CREATE TABLE `tb_prestamos` (
  `idprestamo` int(11) NOT NULL,
  `idsolicitud` int(11) DEFAULT NULL,
  `monto_credito` decimal(14,2) DEFAULT 0.00,
  `monto_desembolsado` decimal(14,2) DEFAULT 0.00,
  `interes_credito` decimal(12,6) DEFAULT 0.000000,
  `comision_desembolso` decimal(8,4) DEFAULT 0.0000,
  `numero_coutas` int(11) DEFAULT 0,
  `forma_pago` tinyint(4) DEFAULT 0,
  `fecha_credito` date DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `interes_corriente_anual` decimal(12,6) DEFAULT NULL,
  `interes_moratorio` decimal(12,6) DEFAULT NULL,
  `idasesor` int(11) DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `promotor` text DEFAULT NULL,
  `tipo_cuota` varchar(50) DEFAULT NULL,
  `fecha_desembolso` date DEFAULT NULL,
  `primer_dia_pago` date DEFAULT NULL,
  `saldo_inicial` decimal(14,2) DEFAULT NULL,
  `pdf_printed_count` int(11) DEFAULT 0,
  `agrupacion_credito` text DEFAULT NULL,
  `id_modalidad_credito` varchar(50) DEFAULT NULL,
  `id_sector_economico` varchar(50) DEFAULT NULL,
  `id_municipio` varchar(100) DEFAULT NULL,
  `id_sector_economico2` varchar(50) DEFAULT NULL,
  `rango_mora` text DEFAULT NULL,
  `nivel` varchar(20) DEFAULT NULL,
  `total_saldo` decimal(14,2) DEFAULT NULL,
  `codigo_busqueda2` varchar(255) DEFAULT NULL,
  `sexo` varchar(10) DEFAULT NULL,
  `anio_piriosidad` varchar(20) DEFAULT NULL,
  `primer_seg_nombre` text DEFAULT NULL,
  `ruta2` text DEFAULT NULL,
  `piriosidad_mes` varchar(50) DEFAULT NULL,
  `dia` varchar(50) DEFAULT NULL,
  `periosidad_pagos` varchar(100) DEFAULT NULL,
  `cuota_no_raw` varchar(50) DEFAULT NULL,
  `dias_raw` varchar(50) DEFAULT NULL,
  `interes_devengado_raw` varchar(50) DEFAULT NULL,
  `monto_cuota_raw` varchar(50) DEFAULT NULL,
  `fecha_raw` varchar(50) DEFAULT NULL,
  `recibo_no` text DEFAULT NULL,
  `monto_usd_raw` varchar(50) DEFAULT NULL,
  `principal_usd_raw` varchar(50) DEFAULT NULL,
  `interes_usd_raw` varchar(50) DEFAULT NULL,
  `saldo_usd_raw` varchar(50) DEFAULT NULL,
  `comision_desembolso2_raw` varchar(50) DEFAULT NULL,
  `mora_usd_raw` varchar(50) DEFAULT NULL,
  `dias_mora_raw` varchar(50) DEFAULT NULL,
  `dias_mora2_raw` varchar(50) DEFAULT NULL,
  `tipo` text DEFAULT NULL,
  `serie` text DEFAULT NULL,
  `consecutivo` varchar(50) DEFAULT NULL,
  `suma_principal_interes_mora_raw` varchar(50) DEFAULT NULL,
  `resultado` text DEFAULT NULL,
  `mes_desembolso` varchar(50) DEFAULT NULL,
  `rango` text DEFAULT NULL,
  `mes_pagado` varchar(50) DEFAULT NULL,
  `anio_pagado` varchar(10) DEFAULT NULL,
  `rango2` varchar(100) DEFAULT NULL,
  `c` varchar(20) DEFAULT NULL,
  `interes_raw` varchar(50) DEFAULT NULL,
  `frecuencia_pago` text DEFAULT NULL,
  `categoria` text DEFAULT NULL,
  `cedula_cliente` varchar(50) DEFAULT NULL,
  `cedula_promotor` varchar(50) DEFAULT NULL,
  `id_tipo_zona` varchar(50) DEFAULT NULL,
  `nombre_cliente2` text DEFAULT NULL,
  `primer_nombre` text DEFAULT NULL,
  `segundo_nombre` text DEFAULT NULL,
  `primer_apellido` text DEFAULT NULL,
  `segundo_apellido` text DEFAULT NULL,
  `num_prestamo_raw` varchar(50) DEFAULT NULL,
  `monto_credito_saldo_raw` varchar(50) DEFAULT NULL,
  `principal_raw` varchar(50) DEFAULT NULL,
  `comision_desembolso_raw` varchar(50) DEFAULT NULL,
  `fecha_desembolso_raw` varchar(50) DEFAULT NULL,
  `num_exp_raw` varchar(50) DEFAULT NULL,
  `desembolsado` tinyint(1) NOT NULL DEFAULT 0,
  `obs_desembolso` text DEFAULT NULL,
  `usuario_desembolso` varchar(100) DEFAULT NULL,
  `fecha_desembolso_real` datetime DEFAULT NULL,
  `emitido` tinyint(1) NOT NULL DEFAULT 0,
  `id_cheque` int(11) DEFAULT NULL,
  `costos_legales` decimal(10,2) DEFAULT 0.00,
  `seguros` decimal(10,2) DEFAULT 0.00,
  `comisiones` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tb_prestamos`
--

INSERT INTO `tb_prestamos` (`idprestamo`, `idsolicitud`, `monto_credito`, `monto_desembolsado`, `interes_credito`, `comision_desembolso`, `numero_coutas`, `forma_pago`, `fecha_credito`, `estado`, `created_at`, `interes_corriente_anual`, `interes_moratorio`, `idasesor`, `idusuario`, `promotor`, `tipo_cuota`, `fecha_desembolso`, `primer_dia_pago`, `saldo_inicial`, `pdf_printed_count`, `agrupacion_credito`, `id_modalidad_credito`, `id_sector_economico`, `id_municipio`, `id_sector_economico2`, `rango_mora`, `nivel`, `total_saldo`, `codigo_busqueda2`, `sexo`, `anio_piriosidad`, `primer_seg_nombre`, `ruta2`, `piriosidad_mes`, `dia`, `periosidad_pagos`, `cuota_no_raw`, `dias_raw`, `interes_devengado_raw`, `monto_cuota_raw`, `fecha_raw`, `recibo_no`, `monto_usd_raw`, `principal_usd_raw`, `interes_usd_raw`, `saldo_usd_raw`, `comision_desembolso2_raw`, `mora_usd_raw`, `dias_mora_raw`, `dias_mora2_raw`, `tipo`, `serie`, `consecutivo`, `suma_principal_interes_mora_raw`, `resultado`, `mes_desembolso`, `rango`, `mes_pagado`, `anio_pagado`, `rango2`, `c`, `interes_raw`, `frecuencia_pago`, `categoria`, `cedula_cliente`, `cedula_promotor`, `id_tipo_zona`, `nombre_cliente2`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `num_prestamo_raw`, `monto_credito_saldo_raw`, `principal_raw`, `comision_desembolso_raw`, `fecha_desembolso_raw`, `num_exp_raw`, `desembolsado`, `obs_desembolso`, `usuario_desembolso`, `fecha_desembolso_real`, `emitido`, `id_cheque`, `costos_legales`, `seguros`, `comisiones`) VALUES
(1, 1, '1500.00', '1395.00', '0.060000', '0.0700', 24, 3, '2026-06-15', 0, '2026-05-13 10:23:11', NULL, NULL, NULL, 15, NULL, NULL, '2026-05-15', '2026-06-15', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '18397.40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '0.00', '0.00', '0.00'),
(2, 2, '1000.00', '930.00', '0.060000', '0.0700', 540, 0, '2026-07-09', 0, '2026-06-08 10:36:24', NULL, NULL, NULL, 15, NULL, NULL, '2026-06-09', '2026-07-09', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '0.00', '0.00', '0.00'),
(3, 3, '2000.00', '1860.00', '0.060000', '0.0700', 24, 3, '2026-07-15', 0, '2026-06-08 10:59:17', NULL, NULL, NULL, 15, NULL, NULL, '2026-06-15', '2026-07-15', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '0.00', '0.00', '0.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_prestamo_cuotas`
--

CREATE TABLE `tb_prestamo_cuotas` (
  `idcuota` int(11) NOT NULL,
  `idprestamo` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `dias` int(11) DEFAULT 0,
  `principal` decimal(14,2) DEFAULT 0.00,
  `interes` decimal(14,2) DEFAULT 0.00,
  `cuota` decimal(14,2) DEFAULT 0.00,
  `saldo` decimal(14,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `comision` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `dias_mora_raw` int(11) DEFAULT NULL,
  `dias_mora_manual` int(11) DEFAULT NULL,
  `monto_mora` decimal(14,2) DEFAULT NULL,
  `cuota_no_raw` varchar(50) DEFAULT NULL,
  `fecha_raw` varchar(50) DEFAULT NULL,
  `dias_raw` varchar(50) DEFAULT NULL,
  `principal_raw` varchar(50) DEFAULT NULL,
  `interes_devengado_raw` varchar(50) DEFAULT NULL,
  `monto_cuota_raw` varchar(50) DEFAULT NULL,
  `saldo_usd_raw` varchar(50) DEFAULT NULL,
  `monto_credito_saldo_raw` varchar(50) DEFAULT NULL,
  `comision_desembolso_raw` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tb_prestamo_cuotas`
--

INSERT INTO `tb_prestamo_cuotas` (`idcuota`, `idprestamo`, `numero`, `fecha_vencimiento`, `dias`, `principal`, `interes`, `cuota`, `saldo`, `created_at`, `comision`, `dias_mora_raw`, `dias_mora_manual`, `monto_mora`, `cuota_no_raw`, `fecha_raw`, `dias_raw`, `principal_raw`, `interes_devengado_raw`, `monto_cuota_raw`, `saldo_usd_raw`, `monto_credito_saldo_raw`, `comision_desembolso_raw`) VALUES
(1, 1, 1, '2026-06-15', 0, '29.52', '90.00', '123.90', '0.00', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, 2, '2026-07-15', 30, '31.29', '88.23', '123.90', '0.00', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1, 3, '2026-08-15', 31, '33.17', '86.35', '123.90', '123.90', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 1, 4, '2026-09-16', 32, '35.16', '84.36', '123.90', '1370.86', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 1, 5, '2026-10-15', 29, '37.27', '82.25', '123.90', '1333.59', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 1, 6, '2026-11-16', 32, '39.50', '80.02', '123.90', '1294.09', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 1, 7, '2026-12-15', 29, '41.87', '77.65', '123.90', '1252.22', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 1, 8, '2027-01-15', 31, '44.39', '75.13', '123.90', '1207.83', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 1, 9, '2027-02-15', 31, '47.05', '72.47', '123.90', '1160.78', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 1, 10, '2027-03-15', 28, '49.87', '69.65', '123.90', '1110.91', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 1, 11, '2027-04-15', 31, '52.86', '66.65', '123.89', '1058.05', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 1, 12, '2027-05-15', 30, '56.04', '63.48', '123.90', '1002.01', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 1, 13, '2027-06-15', 31, '59.40', '60.12', '123.90', '942.61', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 1, 14, '2027-07-15', 30, '62.96', '56.56', '123.90', '879.65', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 1, 15, '2027-08-16', 32, '66.74', '52.78', '123.90', '812.91', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 1, 16, '2027-09-15', 30, '70.74', '48.77', '123.89', '742.17', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 1, 17, '2027-10-15', 30, '74.99', '44.53', '123.90', '667.18', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 1, 18, '2027-11-15', 31, '79.49', '40.03', '123.90', '587.69', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 1, 19, '2027-12-15', 30, '84.26', '35.26', '123.90', '503.43', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 1, 20, '2028-01-15', 31, '89.31', '30.21', '123.90', '414.12', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 1, 21, '2028-02-15', 31, '94.67', '24.85', '123.90', '319.45', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 1, 22, '2028-03-15', 29, '100.35', '19.17', '123.90', '219.10', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 1, 23, '2028-04-15', 31, '106.37', '13.15', '123.90', '112.73', '2026-05-13 10:23:11', '4.3800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 1, 24, '2028-05-15', 30, '112.73', '6.76', '123.75', '0.00', '2026-05-13 10:23:11', '4.2600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 3, 1, '2026-07-15', 0, '39.36', '120.00', '165.19', '165.19', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 3, 2, '2026-08-15', 31, '41.72', '117.64', '165.19', '1918.92', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 3, 3, '2026-09-16', 32, '44.22', '115.14', '165.19', '1874.70', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 3, 4, '2026-10-15', 29, '46.88', '112.48', '165.19', '1827.82', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 3, 5, '2026-11-16', 32, '49.69', '109.67', '165.19', '1778.13', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 3, 6, '2026-12-15', 29, '52.67', '106.69', '165.19', '1725.46', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 3, 7, '2027-01-15', 31, '55.83', '103.53', '165.19', '1669.63', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 3, 8, '2027-02-15', 31, '59.18', '100.18', '165.19', '1610.45', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 3, 9, '2027-03-15', 28, '62.73', '96.63', '165.19', '1547.72', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 3, 10, '2027-04-15', 31, '66.49', '92.86', '165.18', '1481.23', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 3, 11, '2027-05-15', 30, '70.48', '88.87', '165.18', '1410.75', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 3, 12, '2027-06-15', 31, '74.71', '84.65', '165.19', '1336.04', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 3, 13, '2027-07-15', 30, '79.20', '80.16', '165.19', '1256.84', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 3, 14, '2027-08-16', 32, '83.95', '75.41', '165.19', '1172.89', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 3, 15, '2027-09-15', 30, '88.98', '70.37', '165.18', '1083.91', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 3, 16, '2027-10-15', 30, '94.32', '65.03', '165.18', '989.59', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 3, 17, '2027-11-15', 31, '99.98', '59.38', '165.19', '889.61', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 3, 18, '2027-12-15', 30, '105.98', '53.38', '165.19', '783.63', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 3, 19, '2028-01-15', 31, '112.34', '47.02', '165.19', '671.29', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 3, 20, '2028-02-15', 31, '119.08', '40.28', '165.19', '552.21', '2026-06-08 10:59:17', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 3, 21, '2028-03-15', 29, '126.23', '33.13', '165.19', '425.98', '2026-06-08 10:59:18', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 3, 22, '2028-04-15', 31, '133.80', '25.56', '165.19', '292.18', '2026-06-08 10:59:18', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 3, 23, '2028-05-15', 30, '141.83', '17.53', '165.19', '150.35', '2026-06-08 10:59:18', '5.8300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 3, 24, '2028-06-15', 31, '150.35', '9.02', '165.28', '0.00', '2026-06-08 10:59:18', '5.9100', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_prestamo_pagos`
--

CREATE TABLE `tb_prestamo_pagos` (
  `id` int(11) NOT NULL,
  `idprestamo` int(11) NOT NULL,
  `idcuota` int(11) NOT NULL,
  `monto_pagado` decimal(18,2) NOT NULL DEFAULT 0.00,
  `fecha_pago` date NOT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `idserie` int(11) DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `dias_mora_raw` int(11) DEFAULT NULL,
  `rango_mora` varchar(100) DEFAULT NULL,
  `nivel` varchar(20) DEFAULT NULL,
  `idcliente` int(11) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `dato_adicional` varchar(100) DEFAULT NULL,
  `monto_principal_pagado` decimal(18,2) DEFAULT NULL,
  `monto_interes_corriente_pagado` decimal(18,2) DEFAULT NULL,
  `monto_interes_mora_pagado` decimal(18,2) DEFAULT NULL,
  `monto_interes_pagado` decimal(18,2) DEFAULT NULL,
  `monto_usd_recibido` decimal(18,2) DEFAULT NULL,
  `monto_nio_recibido` decimal(18,2) DEFAULT NULL,
  `tc_venta_aplicada` decimal(10,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_prestamo_pagos`
--

INSERT INTO `tb_prestamo_pagos` (`id`, `idprestamo`, `idcuota`, `monto_pagado`, `fecha_pago`, `referencia`, `idserie`, `idusuario`, `dias_mora_raw`, `rango_mora`, `nivel`, `idcliente`, `metodo_pago`, `dato_adicional`, `monto_principal_pagado`, `monto_interes_corriente_pagado`, `monto_interes_mora_pagado`, `monto_interes_pagado`, `monto_usd_recibido`, `monto_nio_recibido`, `tc_venta_aplicada`) VALUES
(1, 1, 1, '123.90', '2026-05-13', 'A0000000004', 1, 15, NULL, NULL, NULL, 16, 'efectivo', 'Serie A PRuebe', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, 2, '123.90', '2026-05-13', 'A0000000005', 1, 15, NULL, NULL, NULL, 16, 'efectivo', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_provincias`
--

CREATE TABLE `tb_provincias` (
  `id` varchar(4) NOT NULL,
  `nombre` varchar(45) DEFAULT NULL,
  `iddepartamento` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_reports`
--

CREATE TABLE `tb_reports` (
  `job_id` varchar(64) NOT NULL,
  `type` varchar(50) NOT NULL,
  `print_url` text DEFAULT NULL,
  `file_path` text DEFAULT NULL,
  `status` enum('pending','processing','done','error') NOT NULL DEFAULT 'pending',
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `started_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `error_text` text DEFAULT NULL,
  `file_hash` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_reports`
--

INSERT INTO `tb_reports` (`job_id`, `type`, `print_url`, `file_path`, `status`, `created_by`, `created_at`, `started_at`, `finished_at`, `error_text`, `file_hash`) VALUES
('balanza_696146d4a7b4c3.65717712', 'balanza_pdf', 'http://localhost/Servicredit/contabilidad/balanza_print?start_date=2026-01-01&end_date=2026-01-31', 'uploads/reports/balanza_696146d4a7b4c3.65717712.pdf', 'pending', NULL, '2026-01-09 13:20:04', NULL, NULL, NULL, NULL),
('balanza_696146daf218d2.89885734', 'balanza_pdf', 'http://localhost/Servicredit/contabilidad/balanza_print?start_date=2026-01-01&end_date=2026-01-31', 'uploads/reports/balanza_696146daf218d2.89885734.pdf', 'pending', NULL, '2026-01-09 13:20:10', NULL, NULL, NULL, NULL),
('resultados_69307046716126.30564178', 'resultados_pdf', 'http://localhost/servicredit/contabilidad/resultados_print?start_date=2025-01-01&end_date=2025-12-01', 'uploads/reports/resultados_69307046716126.30564178.pdf', 'pending', NULL, '2025-12-03 12:15:50', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_series_recibos`
--

CREATE TABLE `tb_series_recibos` (
  `idserie` int(11) NOT NULL,
  `codigo` varchar(10) NOT NULL COMMENT 'CÃ³digo de la serie, p.ej. A, B, C o "A1"',
  `nombre` varchar(100) DEFAULT NULL COMMENT 'Nombre legible de la serie, p.ej. "Serie A"',
  `consecutivo` int(11) NOT NULL DEFAULT 0 COMMENT 'Consecutivo actual (prÃ³ximo a emitir)',
  `ultimo_emitido` int(11) DEFAULT NULL COMMENT 'NÃºmero del Ãºltimo recibo emitido',
  `created_on` int(11) DEFAULT unix_timestamp(),
  `updated_on` int(11) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 activo, 0 inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `tb_series_recibos`
--

INSERT INTO `tb_series_recibos` (`idserie`, `codigo`, `nombre`, `consecutivo`, `ultimo_emitido`, `created_on`, `updated_on`, `estado`) VALUES
(1, 'A', 'Serie A', 7, 7, 1767622352, 1778869264, 1),
(2, 'B', 'Serie B', 1, NULL, 1767622352, 1778696419, 1),
(3, 'C', 'Serie C', 1, NULL, 1778696446, NULL, 1),
(4, 'D', 'Serie D', 1, NULL, 1778696455, NULL, 1),
(5, 'E', 'Serie E', 1, NULL, 1778696462, NULL, 1),
(6, 'F', 'Serie F', 1, NULL, 1778696471, NULL, 1),
(7, 'G', 'Serie G', 1, NULL, 1778696479, NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_simulacion`
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
-- Estructura de tabla para la tabla `tb_sistema`
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
  `logotipo` varchar(15) DEFAULT NULL,
  `firma` varchar(255) DEFAULT NULL,
  `firma_financiero` varchar(255) DEFAULT NULL,
  `firma_contador` varchar(255) DEFAULT NULL,
  `firma_gerente` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_sistema`
--

INSERT INTO `tb_sistema` (`id`, `razon_social`, `email`, `web`, `direccion`, `telefonos`, `mensaje_ticket`, `idmoneda`, `fechaActualizacion`, `logotipo`, `firma`, `firma_financiero`, `firma_contador`, `firma_gerente`) VALUES
(1, 'CREDIBLAMEN SYSTEM', 'info@crediblamen.group', 'www.crediblamen.group', 'Managua, Nicaragua', '0000-0000', 'Prestamos Rapidos y Faciles.', 1, '2026-05-29 09:11:35', '6302417859.png', 'firma_1780065262.jpg', 'firma_financiero_1780067489.jpg', 'firma_contador_1780067472.jpg', 'firma_gerente_1780067495.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_solicitudes`
--

CREATE TABLE `tb_solicitudes` (
  `idsolicitud` int(11) NOT NULL,
  `apellidos` varchar(50) DEFAULT NULL,
  `nombres` varchar(100) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `telefono` varchar(27) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tipo_doc` int(11) NOT NULL,
  `numero_doc` varchar(50) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `estado` int(11) DEFAULT NULL,
  `fechaActualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `negocio_propio` tinyint(1) DEFAULT 0 COMMENT '1=Si,0=No',
  `negocio_antiguedad` int(11) DEFAULT NULL COMMENT 'AntigÃ¼edad en meses',
  `matricula_permiso` varchar(150) DEFAULT NULL COMMENT 'NÃºmero o referencia de matrÃ­cula/permiso',
  `cedula_vigente` tinyint(1) DEFAULT 0 COMMENT '1=SÃ­, 0=No',
  `ingreso_promedio_alto` decimal(18,2) DEFAULT NULL COMMENT 'Ingreso promedio alto mensual del negocio',
  `ingreso_promedio_bajo` decimal(18,2) DEFAULT NULL COMMENT 'Ingreso promedio bajo mensual del negocio',
  `otros_ingresos` tinyint(1) DEFAULT 0 COMMENT '1=SÃ­,0=No',
  `otros_ingresos_docs` varchar(255) DEFAULT NULL COMMENT 'Referencia o ruta de soporte de otros ingresos',
  `ahorros` tinyint(1) DEFAULT 0 COMMENT '1=SÃ­,0=No',
  `inventario_disponible` tinyint(1) DEFAULT 0 COMMENT '1=Tiene inventario disponible,0=No',
  `cuentas_por_cobrar` tinyint(1) DEFAULT 0 COMMENT '1=SÃ­,0=No',
  `ventas_al_credito` decimal(15,2) DEFAULT NULL,
  `porcentaje_recuperacion` decimal(5,2) DEFAULT NULL COMMENT 'Porcentaje que recupera mensualmente de cuentas por cobrar',
  `gastos_fijos` decimal(18,2) DEFAULT NULL COMMENT 'Gastos fijos mensuales',
  `gastos_operativos` decimal(18,2) DEFAULT NULL COMMENT 'Gastos operativos mensuales',
  `margen_comercial` decimal(5,2) DEFAULT NULL,
  `datos_personales` text DEFAULT NULL,
  `datos_conyuge` text DEFAULT NULL,
  `recibo_servicios` varchar(255) DEFAULT NULL,
  `investigacion_vecinos` text DEFAULT NULL,
  `referencias_personales` text DEFAULT NULL,
  `barrio` varchar(150) DEFAULT NULL,
  `municipio` varchar(150) DEFAULT NULL,
  `tipo_credito` varchar(100) DEFAULT NULL,
  `tipo_solicitud` varchar(100) DEFAULT NULL,
  `estado_civil` varchar(20) DEFAULT NULL,
  `uso_credito` varchar(255) DEFAULT NULL,
  `analista` int(11) DEFAULT NULL,
  `estado_aprobacion` varchar(50) DEFAULT 'pendiente',
  `fecha_solicitud` datetime DEFAULT NULL,
  `fuente_ingresos` varchar(255) DEFAULT NULL,
  `telefono_trabajo` varchar(50) DEFAULT NULL,
  `dni_conyuge` varchar(50) DEFAULT NULL,
  `salario_conyuge` decimal(12,2) DEFAULT 0.00,
  `observaciones` text DEFAULT NULL,
  `giro_negocio` varchar(255) DEFAULT NULL,
  `monto_solicitado` decimal(14,2) DEFAULT NULL,
  `plazo_meses` int(11) DEFAULT NULL,
  `frecuencia` varchar(255) DEFAULT NULL,
  `tasa_interes` decimal(6,2) DEFAULT NULL,
  `cuota_estim_estimada` decimal(14,2) DEFAULT NULL,
  `cuota_estim_estimada_quincenal` decimal(12,2) DEFAULT NULL,
  `garantia` varchar(255) DEFAULT NULL,
  `es_rural` tinyint(1) DEFAULT 0,
  `otros_ingresos_detalle` text DEFAULT NULL,
  `ventas_promedio_diarios` decimal(14,2) DEFAULT NULL,
  `ventas_promedio_mensual` decimal(14,2) DEFAULT NULL,
  `detalle_inventario` text DEFAULT NULL,
  `cuentas_por_cobrar_amount` decimal(14,2) DEFAULT NULL,
  `caja_amount` decimal(14,2) DEFAULT NULL,
  `banco_amount` decimal(14,2) DEFAULT NULL,
  `pago_alquiler` decimal(14,2) DEFAULT NULL,
  `pago_trabajadores` decimal(14,2) DEFAULT NULL,
  `energia` decimal(14,2) DEFAULT NULL,
  `agua` decimal(14,2) DEFAULT NULL,
  `internet` decimal(14,2) DEFAULT NULL,
  `promotor` varchar(100) DEFAULT NULL,
  `fecha_recepcion` date DEFAULT NULL,
  `ventas_dias_buenos` int(11) DEFAULT NULL,
  `ventas_dias_malos` int(11) DEFAULT NULL,
  `nombre_conyuge` varchar(255) DEFAULT NULL,
  `ocupacion_conyuge` varchar(255) DEFAULT NULL,
  `ingresos_conyuge` decimal(15,2) DEFAULT NULL,
  `telefono_conyuge` varchar(100) DEFAULT NULL,
  `numero_dependientes` int(11) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `sexo` varchar(20) DEFAULT NULL,
  `nombre_empresa` varchar(255) DEFAULT NULL,
  `direccion_empresa` varchar(255) DEFAULT NULL,
  `telefono_empresa` varchar(100) DEFAULT NULL,
  `cargo_puesto` varchar(150) DEFAULT NULL,
  `ingreso_mensual_neto` decimal(15,2) DEFAULT NULL,
  `nombre_negocio` varchar(255) DEFAULT NULL,
  `actividad_economica` varchar(255) DEFAULT NULL,
  `ubicacion_negocio` varchar(255) DEFAULT NULL,
  `telefono_negocio` varchar(100) DEFAULT NULL,
  `numero_empleados` int(11) DEFAULT NULL,
  `otros_gastos` text DEFAULT NULL,
  `es_nuevo` tinyint(1) DEFAULT NULL,
  `es_renovacion` tinyint(1) DEFAULT NULL,
  `tiempo_residir_anios` int(11) DEFAULT NULL,
  `tiempo_residir_meses` int(11) DEFAULT NULL,
  `condicion_vivienda` varchar(100) DEFAULT NULL,
  `tiempo_empleo_anios` int(11) DEFAULT NULL,
  `tiempo_empleo_meses` int(11) DEFAULT NULL,
  `tipo_contrato` varchar(100) DEFAULT NULL,
  `deducciones` decimal(15,2) DEFAULT NULL,
  `tiempo_operacion_anios` int(11) DEFAULT NULL,
  `tiempo_operacion_meses` int(11) DEFAULT NULL,
  `propiedad_negocio` varchar(100) DEFAULT NULL,
  `tipo_documento` enum('Cedula','Pasaporte') DEFAULT NULL,
  `ready_for_approval` tinyint(1) DEFAULT 0,
  `rechazado` tinyint(1) DEFAULT 0,
  `propuesta_tipos` text DEFAULT NULL,
  `ventas_dias_buenos_mask` int(11) DEFAULT NULL,
  `ventas_dias_malos_mask` int(11) DEFAULT NULL,
  `nombre_completo` varchar(255) DEFAULT NULL,
  `comision_desembolso` decimal(8,4) DEFAULT NULL,
  `edit_comment` text DEFAULT NULL,
  `rubro_credito` varchar(255) DEFAULT NULL,
  `otros_ingresos_1_amount` decimal(14,2) DEFAULT NULL,
  `otros_ingresos_1_margin` decimal(7,2) DEFAULT NULL,
  `otros_ingresos_1_detalle` text DEFAULT NULL,
  `otros_ingresos_2_amount` decimal(14,2) DEFAULT NULL,
  `otros_ingresos_2_margin` decimal(7,2) DEFAULT NULL,
  `otros_ingresos_2_detalle` text DEFAULT NULL,
  `otros_ingresos_3_amount` decimal(14,2) DEFAULT NULL,
  `otros_ingresos_3_margin` decimal(7,2) DEFAULT NULL,
  `otros_ingresos_3_detalle` text DEFAULT NULL,
  `ventas_buenos_amount` decimal(14,2) DEFAULT NULL,
  `ventas_malos_amount` decimal(14,2) DEFAULT NULL,
  `declaro_verificacion` tinyint(1) DEFAULT 0,
  `firma_solicitante` varchar(255) DEFAULT NULL,
  `fecha_firma` date DEFAULT NULL,
  `energia_electrica` decimal(12,2) DEFAULT NULL,
  `agua_potable` decimal(12,2) DEFAULT NULL,
  `internet_telefonia` decimal(12,2) DEFAULT NULL,
  `ddc_investigacion_campo` varchar(255) DEFAULT NULL,
  `nombre_promotor` varchar(255) DEFAULT NULL,
  `fecha_recepcion_solicitud` date DEFAULT NULL,
  `observaciones_promotor` text DEFAULT NULL,
  `destino_credito` varchar(100) DEFAULT NULL,
  `idcliente` int(11) DEFAULT NULL,
  `cuentas_por_cobrar_evidencia` varchar(255) DEFAULT NULL COMMENT 'Ruta de la foto de evidencia de cuentas por cobrar',
  `gastos_personales` decimal(14,2) DEFAULT NULL COMMENT 'Gastos personales mensuales',
  `gastos_transporte` decimal(14,2) DEFAULT NULL COMMENT 'Gastos de transporte mensuales',
  `idasesor` int(11) DEFAULT NULL,
  `monto_total_inventario` decimal(14,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_solicitudes`
--

INSERT INTO `tb_solicitudes` (`idsolicitud`, `apellidos`, `nombres`, `direccion`, `telefono`, `email`, `tipo_doc`, `numero_doc`, `comentarios`, `estado`, `fechaActualizacion`, `negocio_propio`, `negocio_antiguedad`, `matricula_permiso`, `cedula_vigente`, `ingreso_promedio_alto`, `ingreso_promedio_bajo`, `otros_ingresos`, `otros_ingresos_docs`, `ahorros`, `inventario_disponible`, `cuentas_por_cobrar`, `ventas_al_credito`, `porcentaje_recuperacion`, `gastos_fijos`, `gastos_operativos`, `margen_comercial`, `datos_personales`, `datos_conyuge`, `recibo_servicios`, `investigacion_vecinos`, `referencias_personales`, `barrio`, `municipio`, `tipo_credito`, `tipo_solicitud`, `estado_civil`, `uso_credito`, `analista`, `estado_aprobacion`, `fecha_solicitud`, `fuente_ingresos`, `telefono_trabajo`, `dni_conyuge`, `salario_conyuge`, `observaciones`, `giro_negocio`, `monto_solicitado`, `plazo_meses`, `frecuencia`, `tasa_interes`, `cuota_estim_estimada`, `cuota_estim_estimada_quincenal`, `garantia`, `es_rural`, `otros_ingresos_detalle`, `ventas_promedio_diarios`, `ventas_promedio_mensual`, `detalle_inventario`, `cuentas_por_cobrar_amount`, `caja_amount`, `banco_amount`, `pago_alquiler`, `pago_trabajadores`, `energia`, `agua`, `internet`, `promotor`, `fecha_recepcion`, `ventas_dias_buenos`, `ventas_dias_malos`, `nombre_conyuge`, `ocupacion_conyuge`, `ingresos_conyuge`, `telefono_conyuge`, `numero_dependientes`, `fecha_nacimiento`, `edad`, `sexo`, `nombre_empresa`, `direccion_empresa`, `telefono_empresa`, `cargo_puesto`, `ingreso_mensual_neto`, `nombre_negocio`, `actividad_economica`, `ubicacion_negocio`, `telefono_negocio`, `numero_empleados`, `otros_gastos`, `es_nuevo`, `es_renovacion`, `tiempo_residir_anios`, `tiempo_residir_meses`, `condicion_vivienda`, `tiempo_empleo_anios`, `tiempo_empleo_meses`, `tipo_contrato`, `deducciones`, `tiempo_operacion_anios`, `tiempo_operacion_meses`, `propiedad_negocio`, `tipo_documento`, `ready_for_approval`, `rechazado`, `propuesta_tipos`, `ventas_dias_buenos_mask`, `ventas_dias_malos_mask`, `nombre_completo`, `comision_desembolso`, `edit_comment`, `rubro_credito`, `otros_ingresos_1_amount`, `otros_ingresos_1_margin`, `otros_ingresos_1_detalle`, `otros_ingresos_2_amount`, `otros_ingresos_2_margin`, `otros_ingresos_2_detalle`, `otros_ingresos_3_amount`, `otros_ingresos_3_margin`, `otros_ingresos_3_detalle`, `ventas_buenos_amount`, `ventas_malos_amount`, `declaro_verificacion`, `firma_solicitante`, `fecha_firma`, `energia_electrica`, `agua_potable`, `internet_telefonia`, `ddc_investigacion_campo`, `nombre_promotor`, `fecha_recepcion_solicitud`, `observaciones_promotor`, `destino_credito`, `idcliente`, `cuentas_por_cobrar_evidencia`, `gastos_personales`, `gastos_transporte`, `idasesor`, `monto_total_inventario`) VALUES
(1, 'CARRILLO RAMIREZ', 'ERICK', NULL, '90909090', NULL, 3, '0012702981004X', NULL, NULL, '2026-05-19 18:26:26', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, '34.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Personales', NULL, 'Soltero', NULL, NULL, 'aprobado', '2026-05-11 18:26:00', NULL, NULL, NULL, '0.00', NULL, 'Pulperia', '1500.00', 24, 'Mensual', '0.06', '123.90', '61.95', 'Hipotecaria', 1, NULL, NULL, '157000.00', 'Detalle', '1233.00', '23.00', '21.00', '223.00', '33.00', NULL, NULL, NULL, NULL, NULL, 7600, 500, 'SDASADSDADA', 'cacsaas', NULL, '21312', NULL, '1999-01-01', 27, 'M', NULL, NULL, NULL, NULL, NULL, 'Pulperia Tita ', 'Comerciante', NULL, '58015297', 1, '213', 1, 0, NULL, NULL, 'Propia', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, 0, 0, '[\"4\"]', 31, 96, NULL, '0.0700', 'agregando fecha de solicitud', 'Ganadería', '1200.00', '30.00', 'Venta', '1222.00', '30.00', 'Venta', '1240.00', '30.00', 'Venta', '7600.00', '500.00', 1, NULL, '2026-05-19', '31.00', '2.00', '3.00', 'Comentarios', NULL, '2026-05-01', 'promotor', 'Consumo', 16, NULL, '2312.00', '123.00', 0, NULL),
(2, 'LOPEZ', 'JOSE MARIA MORALES', 'Centro de Managua', '78787878', NULL, 3, '0114545457777K', NULL, NULL, '2026-06-08 09:34:36', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Personales', NULL, 'Soltero', NULL, NULL, 'aprobado', '2026-05-18 08:15:00', NULL, NULL, NULL, '0.00', NULL, 'Pruebas de Negocios', '1000.00', 18, 'Mensual', '0.06', '96.25', '48.13', NULL, 0, NULL, NULL, '17000.00', NULL, '155.00', '100.00', '100.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1000, 500, NULL, NULL, NULL, NULL, NULL, '1975-06-24', 50, 'M', NULL, NULL, NULL, NULL, NULL, 'Mi negocio', 'Negociar', 'Centro de Managua', '22225555', NULL, NULL, 1, 0, NULL, NULL, 'Propia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '[\"3\"]', 19, 12, 'JOSE MARIA MORALES LOPEZ', '0.0700', NULL, 'Industria', '100.00', NULL, NULL, '200.00', NULL, NULL, '100.00', NULL, NULL, '1000.00', '500.00', 1, NULL, '2026-05-19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Capital de trabajo', 29, 'cuentas_cobrar_1779200798_bab4cd7c.jpg', NULL, NULL, NULL, NULL),
(3, 'MENA', 'MARIA JOSE LOPEZ', 'Centro de la ciudad', NULL, NULL, 3, '1001515157887L', NULL, NULL, '2026-06-11 09:16:15', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Hipotecarios', NULL, 'Soltero', NULL, NULL, 'aprobado', '2026-05-18 08:36:00', NULL, NULL, NULL, '0.00', NULL, 'Mi nueva empresa', '2000.00', 24, 'Mensual', '0.06', '165.19', '82.60', NULL, 0, NULL, NULL, '42000.00', NULL, '1000.00', '100.00', '100.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2000, 1000, NULL, NULL, NULL, NULL, NULL, NULL, 30, 'F', NULL, NULL, NULL, NULL, NULL, 'Mis empresas', 'Empresarial', 'Centro de la ciudad', NULL, NULL, '400', 1, 0, NULL, NULL, 'Familiar', NULL, NULL, NULL, NULL, 10, NULL, NULL, NULL, 0, 0, '[\"4\"]', 57, 6, NULL, '0.0700', 'Agregando gastos', 'Construcción', '40.00', NULL, NULL, NULL, NULL, NULL, '1400.00', NULL, NULL, '2000.00', '1000.00', NULL, NULL, NULL, '10.00', '20.00', '30.00', NULL, NULL, NULL, NULL, 'Inversión', 30, NULL, '250.00', NULL, NULL, NULL),
(4, 'HERNANDEZ', 'LUZ CELESTE PEREZ', 'Mi casa', '88888888', NULL, 3, '4445555558888L', NULL, NULL, '2026-05-19 14:50:09', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Casado', NULL, NULL, 'pendiente', '2026-05-19 10:20:00', NULL, NULL, '4447777771111M', '0.00', NULL, 'Empresa Negocial', '1200.00', 18, 'Mensual', '0.06', '115.50', '57.75', NULL, 0, NULL, NULL, '25000.00', 'Mi inventario', '1000.00', '500.00', '200.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1500, 500, 'JOSE JOSE LOPEZ PEREZ', 'Empresario', NULL, '55555555', NULL, NULL, 40, 'F', NULL, NULL, NULL, NULL, NULL, 'Mis negocios familiares', 'Negocios', 'Mi casa', '25656565', NULL, NULL, 1, 0, 4, 2, 'Familiar', NULL, NULL, NULL, NULL, 2, 1, NULL, NULL, 0, 0, '[\"3\"]', 7, 56, NULL, '0.0700', 'garantia mob', 'Agricultura', '100.00', NULL, NULL, '200.00', NULL, NULL, '300.00', NULL, NULL, '1500.00', '500.00', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Consumo', 31, NULL, NULL, NULL, NULL, NULL),
(5, 'MORALES', 'MARIA MARIA MORALES', 'Casa', NULL, NULL, 3, '1115555552222S', NULL, NULL, '2026-05-19 11:31:01', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-05-19 10:44:00', NULL, NULL, NULL, '0.00', NULL, 'Una empresa mas', '1000.00', 18, 'Mensual', '0.06', '96.25', '48.13', 'Hipotecaria', 0, NULL, NULL, '2300.00', NULL, '100.00', '50.00', '50.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 100, 50, NULL, NULL, NULL, NULL, NULL, NULL, 50, 'F', NULL, NULL, NULL, NULL, NULL, 'Mi negocio muy bueno', 'Negocios', 'Negocio el lugar', '22222222', NULL, NULL, 1, 0, NULL, NULL, 'Familiar', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, 0, 0, '[\"3\"]', 31, 32, 'MARIA MARIA MORALES MORALES', '0.0700', 'Añadiendo foto azul a fachada', NULL, '100.00', NULL, NULL, '200.00', NULL, NULL, '300.00', NULL, NULL, '100.00', '50.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 32, NULL, NULL, NULL, NULL, NULL),
(6, 'MORALES', 'CARMEN MARIA SUAREZ', NULL, NULL, NULL, 3, '1445252524444D', NULL, NULL, '2026-05-19 11:44:29', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Personales', NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-05-19 11:32:00', NULL, NULL, NULL, '0.00', NULL, 'Negocio de Fotos', '2000.00', 24, 'Mensual', '0.06', '165.19', '82.60', NULL, 0, NULL, NULL, '2300.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 100, 50, NULL, NULL, NULL, NULL, NULL, NULL, 60, 'F', NULL, NULL, NULL, NULL, NULL, 'Mi negocio', 'Negocios', 'La ubicación del negocio', '45454545', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '[\"4\"]', 31, 32, 'CARMEN MARIA SUAREZ MORALES', '0.0700', 'Subiendo cedula frontal', 'Vivienda (Mejora, Ampliación, Remodelación, Otros)', '100.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100.00', '50.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Consumo', 33, NULL, NULL, NULL, NULL, NULL),
(7, 'HERNANDEZ', 'MARIO JOSE FERNANDEZ', 'Mi casa', '45454545', NULL, 3, '4545555552222D', NULL, NULL, '2026-06-11 14:40:05', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Soltero', NULL, NULL, 'pendiente', NULL, NULL, NULL, '0110505057777L', '0.00', NULL, 'Mi nuevo negocio', '4500.00', 24, 'Mensual', '0.06', '371.69', '185.85', 'Mobiliaria', 0, NULL, NULL, '23000.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1500, 500, NULL, NULL, NULL, NULL, NULL, NULL, 50, 'M', NULL, NULL, NULL, NULL, NULL, 'Mi negocio de negocios', 'Negocios', 'Centro de la ciudad', NULL, NULL, NULL, 1, 0, NULL, NULL, 'Propia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '[\"19\"]', 7, 24, NULL, '0.0700', 'Garant. Mob', 'Turismo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1500.00', '500.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Inversión', 34, NULL, NULL, NULL, NULL, NULL),
(8, 'CRUZ LOPEZ', 'MORA AZUL', NULL, NULL, NULL, 3, '4554411441515L', NULL, NULL, '2026-05-19 15:18:43', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Soltero', NULL, NULL, 'pendiente', NULL, NULL, NULL, NULL, '0.00', NULL, 'Un gran nefocio', '1200.00', 18, 'Mensual', '0.06', '115.50', '57.75', 'Hipotecaria', 0, NULL, NULL, '40900.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2000, 150, NULL, NULL, NULL, NULL, NULL, NULL, 40, 'F', NULL, NULL, NULL, NULL, NULL, 'Mi gran negocio', 'Negocios', 'La ubicación del negocio', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '[\"3\"]', 31, 32, NULL, '0.0700', 'NUEVAS FOTOS', NULL, '100.00', NULL, NULL, NULL, NULL, NULL, '300.00', NULL, NULL, '2000.00', '150.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 35, NULL, NULL, NULL, NULL, NULL),
(9, 'JOHNSON OTHER', 'CHRIS JOHN', NULL, NULL, NULL, 3, '4557878789999J', NULL, NULL, '2026-06-11 19:45:49', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Hipotecarios', NULL, 'Soltero', NULL, NULL, 'aprobado', '2026-04-28 15:07:00', NULL, NULL, NULL, '0.00', NULL, 'Mis grandes negocios', '1500.00', 24, 'Mensual', '0.06', '123.90', '61.95', NULL, 0, NULL, NULL, '87000.00', NULL, '1500.00', '200.00', '200.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4500, 1500, NULL, NULL, NULL, NULL, NULL, NULL, 46, 'M', NULL, NULL, NULL, NULL, NULL, 'Mi negocio de pesca', 'Pescar', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '[\"4\"]', 51, 12, NULL, '0.0700', 'mob', 'Tarjetas de Crédito', '100.00', NULL, NULL, '200.00', NULL, NULL, '300.00', NULL, NULL, '4500.00', '1500.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Consumo', 36, NULL, NULL, NULL, NULL, NULL),
(10, 'TRES CUATRO', 'UNO DOS', NULL, NULL, NULL, 3, '7778888884444D', NULL, NULL, '2026-05-19 15:21:23', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-05-19 15:19:00', NULL, NULL, NULL, '0.00', NULL, 'Mi nueva empresa', '1500.00', 24, 'Mensual', '0.06', '123.90', '61.95', 'Hipotecaria', 0, NULL, NULL, '400.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1500, 200, NULL, NULL, NULL, NULL, NULL, NULL, 45, 'F', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '[\"4\"]', NULL, NULL, NULL, '0.0700', 'fachada foto', NULL, '100.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1500.00', '200.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 37, NULL, NULL, NULL, NULL, NULL),
(11, 'SOLIS CRUZ', 'JONATHAN JOHN', NULL, '77771111', NULL, 3, '4445555557878D', NULL, NULL, '2026-05-19 18:34:57', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Hipotecarios', NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-05-19 15:26:00', NULL, NULL, NULL, '0.00', NULL, 'Mi nueva solicitud de negocio', '1500.00', 24, 'Mensual', '0.06', '123.90', '61.95', 'Hipotecaria', 0, NULL, NULL, '26000.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1500, 200, NULL, NULL, NULL, NULL, NULL, NULL, 45, 'M', NULL, NULL, NULL, NULL, NULL, 'Mi buen negocio', 'Cosas', 'Direccion', '88888888', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '[\"4\"]', 51, 12, NULL, '0.0700', 'fecha de firma', 'Hipotecario', '100.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1500.00', '200.00', NULL, NULL, '2026-05-04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Consumo', 38, NULL, NULL, NULL, NULL, NULL),
(12, 'HERNANDEZ MORALES', 'MARIA KAMILA DEL SOCORRO', 'Dirección de casa de María.', '78787878', NULL, 3, '0011515150000D', NULL, NULL, '2026-05-21 10:00:34', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, '800.00', NULL, NULL, NULL, '10.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Hipotecarios', NULL, 'Casado', NULL, NULL, 'pendiente', '2026-05-20 08:09:00', NULL, NULL, '1221515154444D', '0.00', NULL, 'Mi negocio de venta', '2000.00', 24, 'Mensual', '0.06', '165.19', '82.60', 'Mobiliaria', 0, NULL, NULL, '79000.00', NULL, '1500.00', '100.00', '100.00', '0.00', '1500.00', NULL, NULL, NULL, NULL, NULL, 4000, 1500, 'JOSE MARIO LOPEZ FERNANDEZ D.', 'Trabajador', '1500.00', '15151515', 0, '1974-06-20', 51, 'F', NULL, NULL, NULL, NULL, NULL, 'Ventas de Cosas', 'Vender', 'Centro de la ciudad de Managua', '22222225', 2, '200', 1, 0, 1, 2, 'Familiar', NULL, NULL, NULL, NULL, 2, 1, NULL, NULL, 0, 0, '[\"4\"]', 57, 6, NULL, '0.0700', 'Añadiendo dato de ventas al crédito y foto evidencia', 'Hipotecario', '100.00', '10.00', 'Mi primer ingreso', '200.00', '20.00', 'Mi segundo ingreso', '300.00', '30.00', 'Mi tercer ingreso', '4000.00', '1500.00', 1, NULL, '2026-05-20', '200.00', '20.00', '200.00', NULL, NULL, '2026-05-19', NULL, 'Inversión', 39, 'evidencia_1779379234_024f782d.jpg', '500.00', '200.00', 0, NULL),
(13, 'GUTIERREZ PEREZ', 'SOL MARIA', 'Casa de Sol y Juan', '25632563', NULL, 3, '1552525259999F', NULL, NULL, '2026-05-21 10:41:42', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, '1000.00', NULL, NULL, NULL, '2.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Casado', NULL, NULL, 'pendiente', '2026-05-21 10:26:00', NULL, NULL, '4447878781444S', '0.00', NULL, 'Venta de Ropa', '10000.00', 48, 'Mensual', '0.04', '482.23', '241.12', 'Mobiliaria', 0, NULL, NULL, '81000.00', 'Mi inventario es ropa para todas las edades', '4000.00', '2000.00', '1000.00', '2000.00', '300.00', NULL, NULL, NULL, NULL, NULL, 5000, 1500, 'JUAN JOSE LOPEZ MENDEZ', 'Vendedor', '1500.00', '78985456', 0, '1994-01-17', 32, 'F', NULL, NULL, NULL, NULL, NULL, 'Ropa en Venta SyJ', 'Vender ropa a gente', 'Centro de Bello Horizonte', '22225555', 5, '500', 1, 0, 8, 4, 'Propia', NULL, NULL, NULL, NULL, 4, 2, NULL, NULL, 0, 0, '[\"6\"]', 52, 11, NULL, '0.0500', 'Añadiendo datos al inicio', 'Comercio', '150.00', '15.00', 'Mi primer ingreso', '250.00', '25.00', 'Mi segundo ingreso', '350.00', '35.00', 'Mi tercer ingreso', '5000.00', '1500.00', 1, NULL, '2026-05-21', '200.00', '300.00', '400.00', NULL, NULL, '2026-05-21', 'Sin observaciones del promotor por hoy', 'Inversión', 40, NULL, '4500.00', '1000.00', 0, NULL),
(14, 'LOPEZ BLANDON', 'LUIS GABRIEL', 'Casa del ferretero', '78547854', NULL, 3, '1114444445252D', NULL, NULL, '2026-05-21 11:23:24', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, '1400.00', NULL, NULL, NULL, '16.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Personales', NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-05-21 11:18:00', NULL, NULL, NULL, '0.00', NULL, 'Ferretería', '2500.00', 24, 'Mensual', '0.06', '206.49', '103.25', 'Sin garantía', 1, NULL, NULL, '108000.00', 'Mi detalle son las herramientas', '15000.00', '1200.00', '3000.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8000, 1200, NULL, NULL, NULL, NULL, NULL, '1998-07-21', 27, 'M', NULL, NULL, NULL, NULL, NULL, 'Cosas de Metal', 'Vender herramientas', 'Centroamérica', NULL, NULL, NULL, 1, 0, NULL, NULL, 'Propia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '[\"4\"]', 19, 12, NULL, '0.0700', NULL, 'Industria', '101.00', '10.00', 'Ingres ferr1', '202.00', '20.00', 'Ingres ferr2', '303.00', '3.00', 'Ingreso ferr3', '8000.00', '1200.00', 1, NULL, '2026-05-20', NULL, NULL, NULL, NULL, 'ADMINISTRADOR ADMINISTRADOR', '2026-05-21', 'Nada que observar', 'Inversión', 41, 'images _1_.jpg', NULL, NULL, 0, NULL),
(15, 'FLORES DEL AIRE', 'FLORA MARIA', 'Casa donde viven Flor y Luis', '74589687', NULL, 3, '5552525254444S', NULL, NULL, '2026-06-09 08:04:26', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, '1000.00', NULL, NULL, NULL, '1.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Hipotecarios', NULL, 'Casado', NULL, NULL, 'pendiente', '2026-05-25 09:47:00', NULL, NULL, '7471111114545D', '0.00', NULL, 'Venta de Comida', '1500.00', 24, 'Mensual', '0.06', '123.90', '61.95', 'Mobiliaria', 0, NULL, NULL, '64000.00', 'Cosas de comida', '4000.00', '150.00', '250.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4500, 1000, 'LUIS JUAN GONZALEZ DAVILA', 'Trabajador de construcción', '12000.00', '44554455', 0, '1987-01-13', 39, 'F', NULL, NULL, NULL, NULL, NULL, 'Fritanga muy buena', 'Vender comida', 'Centro del barrio', '22665522', NULL, NULL, 1, 0, 3, 1, 'Propia', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, 0, 0, '[\"4\"]', 112, 12, NULL, '0.0700', 'Agregando fotos faltantes', 'Comercio', '110.00', '10.00', 'Flor primer ingreso', '220.00', '20.00', 'Flor segundo ingreso', '330.00', '30.00', 'Flor tercer ingreso', '4500.00', '1000.00', 1, NULL, '2026-05-25', NULL, NULL, NULL, NULL, NULL, '2026-05-25', 'Nada que observar', 'Consumo', 42, NULL, NULL, NULL, 0, NULL),
(16, 'NUÑEZ PEREZ', 'YOLANDA MARIA', 'Casa de Yolanda y persona', '88889999', NULL, 3, '0010505056666D', NULL, NULL, '2026-06-08 14:06:03', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, '4500.00', NULL, NULL, NULL, '2.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Personales', NULL, 'Union libre', NULL, NULL, 'pendiente', '2026-06-05 11:51:00', NULL, NULL, '0050101014444D', '0.00', NULL, 'Venta de juguetes', '3000.00', 20, 'Mensual', '0.06', '272.05', '136.03', 'Mobiliaria', 0, NULL, NULL, '106000.00', 'Sin detalle particular', '1500.00', '2500.00', '3500.00', '150.00', '7000.00', NULL, NULL, NULL, NULL, NULL, 6500, 2000, 'PERSONA CONYUGUE UNO', 'Venta de juegos', '16000.00', '63366336', 1, '2000-06-03', 26, 'F', NULL, NULL, NULL, NULL, NULL, 'Juguetes BBB', 'Venta de cosas', 'Una tienda en el centro', '26263636', 2, '150', 1, 0, 3, 2, 'Propia', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, 0, 0, '[\"4\"]', 56, 7, NULL, '0.0700', 'Se agregó una foto', 'Comercio', '100.00', '1.00', 'Primer ingreso', '200.00', '2.00', 'Segundo ingreso', '300.00', '3.00', 'Tercer ingreso', '6500.00', '2000.00', 1, NULL, '2026-06-05', '150.00', '55.00', '200.00', NULL, NULL, '2026-06-04', 'Observo promotorialmente', 'Consumo', 43, NULL, '2000.00', '500.00', 0, '940.00'),
(17, 'JOHNSON OTHER', 'SOL MARIA', NULL, NULL, NULL, 3, '0011515150000v', NULL, NULL, '2026-06-09 08:10:45', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, '2.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Hipotecarios', NULL, 'Union libre', NULL, NULL, 'pendiente', '2026-06-09 08:06:00', NULL, NULL, NULL, '0.00', NULL, 'Empresa', '1500.00', 18, 'Mensual', '0.06', '144.36', '72.18', 'Hipotecaria', 0, NULL, NULL, '170000.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10000, 1000, 'JOSE MARIO LOPEZ FERNANDEZ D.', NULL, NULL, NULL, NULL, '1995-01-09', 31, 'F', NULL, NULL, NULL, NULL, NULL, 'Ventas de Cosas', NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 'Propia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '[\"4\"]', 57, 6, NULL, '0.0700', 'Agregando flores', 'Industria', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '10000.00', '1000.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Consumo', 44, NULL, NULL, NULL, NULL, NULL),
(18, 'BENAVIDES', 'JOSEFINA', NULL, '78899878', NULL, 3, '0010202024444L', NULL, NULL, '2026-06-12 12:29:32', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, '1200.00', NULL, NULL, NULL, '2.00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Personales', NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-06-12 12:06:00', NULL, NULL, NULL, '0.00', NULL, 'Venta de medicina', '1500.00', 24, 'Mensual', '0.06', '123.90', '61.95', 'Hipotecaria', 0, NULL, NULL, '204000.00', NULL, '1500.00', '30000.00', '12000.00', '1600.00', '100.00', NULL, NULL, NULL, NULL, NULL, 10000, 6000, NULL, NULL, NULL, NULL, NULL, '1997-05-12', 29, 'F', NULL, NULL, NULL, NULL, NULL, 'Medicinas en Venta', 'Vender', NULL, NULL, NULL, '100', 1, 0, NULL, NULL, 'Propia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '[\"4\"]', 56, 7, NULL, '0.0700', NULL, 'Comercio', '100.00', NULL, NULL, '200.00', NULL, NULL, '3000.00', NULL, NULL, '10000.00', '6000.00', NULL, NULL, NULL, '100.00', '100.00', '100.00', NULL, 'ADMINISTRADOR ADMINISTRADOR', NULL, NULL, 'Consumo', 45, NULL, '1500.00', '200.00', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_solicitudes_comments`
--

CREATE TABLE `tb_solicitudes_comments` (
  `idcomment` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(150) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_solicitudes_comments`
--

INSERT INTO `tb_solicitudes_comments` (`idcomment`, `idsolicitud`, `user_id`, `username`, `action`, `comment`, `created_at`) VALUES
(1, 3, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Añadiendo foto', '2026-05-19 09:08:22'),
(2, 3, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Añadiendo más fotos', '2026-05-19 09:09:26'),
(3, 3, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Foto de fachada', '2026-05-19 09:14:23'),
(4, 3, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Añadiendo foto evidencia', '2026-05-19 09:16:14'),
(5, 3, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Otra foto evidencia', '2026-05-19 09:17:22'),
(6, 3, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Evidencias', '2026-05-19 09:18:11'),
(7, 3, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Fotos de ingresos y evidencia', '2026-05-19 09:20:38'),
(8, 3, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Fotos de evidencia', '2026-05-19 09:30:36'),
(9, 3, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Mas fotos de ingresos', '2026-05-19 09:38:38'),
(10, 3, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'eliminando foto', '2026-05-19 09:40:14'),
(11, 3, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'mas fotos', '2026-05-19 09:40:44'),
(12, 5, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Eliminando una foto', '2026-05-19 10:51:52'),
(13, 5, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Agregando misma foto', '2026-05-19 10:53:55'),
(14, 5, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Borrando una foto duplicada', '2026-05-19 10:58:57'),
(15, 5, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Añadiendo foto cámara', '2026-05-19 10:59:56'),
(16, 5, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Añadiendo cámara a otros ingresos', '2026-05-19 11:07:19'),
(17, 5, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Subiendo una camara', '2026-05-19 11:12:39'),
(18, 5, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'añadiendo flores', '2026-05-19 11:15:08'),
(19, 5, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Añadiendo foto azul a fachada', '2026-05-19 11:31:01'),
(20, 6, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Foto cedula de flores', '2026-05-19 11:34:51'),
(21, 6, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Subiendo foto de personas en cedula', '2026-05-19 11:41:45'),
(22, 6, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Subiendo foto azul en fachada', '2026-05-19 11:42:22'),
(23, 6, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Subiendo misma foto en fachada', '2026-05-19 11:42:59'),
(24, 6, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Subiendo cedula frontal', '2026-05-19 11:44:29'),
(25, 1, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Apellidos cambiaron de orden', '2026-05-19 14:43:11'),
(26, 4, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'garantia mob', '2026-05-19 14:50:09'),
(27, 8, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'NUEVAS FOTOS', '2026-05-19 15:18:43'),
(28, 10, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'fachada foto', '2026-05-19 15:21:23'),
(29, 11, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'nueva foto de evidencia', '2026-05-19 15:32:16'),
(30, 9, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'garat mob agregando', '2026-05-19 18:02:07'),
(31, 9, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'otra garantia', '2026-05-19 18:07:14'),
(32, 9, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'garant hipo', '2026-05-19 18:07:35'),
(33, 9, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'mob', '2026-05-19 18:14:51'),
(34, 1, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'fecha firma', '2026-05-19 18:23:12'),
(35, 1, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'agregando fecha de solicitud', '2026-05-19 18:26:26'),
(36, 11, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'fecha de firma', '2026-05-19 18:34:57'),
(37, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Cambiando nombres de persona y conyugue', '2026-05-20 10:34:37'),
(38, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Destino conami agregado', '2026-05-20 12:26:10'),
(39, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Agregando promotor', '2026-05-20 12:27:09'),
(40, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Garantia mob', '2026-05-20 13:53:17'),
(41, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Sin garantía', '2026-05-20 13:56:21'),
(42, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Cambiando a garant. hip', '2026-05-20 13:59:12'),
(43, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Cambio a g.m', '2026-05-20 13:59:35'),
(44, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Cambiando a S.G', '2026-05-20 14:02:02'),
(45, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Ingresos del conyuge y garantia mob', '2026-05-20 14:33:05'),
(46, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Añadiendo 1500 de ingresos al conyuge', '2026-05-20 14:39:52'),
(47, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Añadiendo %margen en los ingresos', '2026-05-20 14:45:02'),
(48, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Guardando ventas al crédito', '2026-05-20 15:34:37'),
(49, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Nuevos pdfs y fotos', '2026-05-21 08:24:03'),
(50, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Consentimiento de filtrado prueba1', '2026-05-21 08:29:57'),
(51, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Agregando docu1', '2026-05-21 08:43:44'),
(52, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Probando eliminaciones', '2026-05-21 09:34:09'),
(53, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'delete_file', 'El archivo/foto de nombre \'1779373400_6853053f66fd.pdf\' fue eliminado por \'ADMINISTRADOR ADMINISTRADOR\'', '2026-05-21 09:34:09'),
(54, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'delete_file', 'El archivo/foto de nombre \'Docu1.pdf\' fue eliminado por \'ADMINISTRADOR ADMINISTRADOR\'', '2026-05-21 09:34:10'),
(55, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'delete_file', 'El archivo/foto de nombre \'1779373410_5155b2ee6407.pdf\' fue eliminado por \'ADMINISTRADOR ADMINISTRADOR\'', '2026-05-21 09:34:10'),
(56, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Pruebas', '2026-05-21 09:35:47'),
(57, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'delete_file', 'El archivo/foto de nombre \'1779376189_Docu3.pdf\' fue eliminado por \'ADMINISTRADOR ADMINISTRADOR\'', '2026-05-21 09:35:47'),
(58, 12, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Añadiendo dato de ventas al crédito y foto evidencia', '2026-05-21 10:00:34'),
(59, 13, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Agregando docu', '2026-05-21 10:34:23'),
(60, 13, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Añadiendo datos al inicio', '2026-05-21 10:41:42'),
(61, 15, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Añadiendo destino', '2026-05-25 11:26:43'),
(62, 16, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Borrando una foto en otros ingresos', '2026-06-05 12:09:03'),
(63, 16, 15, 'ADMINISTRADOR ADMINISTRADOR', 'delete_file', 'El archivo/foto de nombre \'6cgqAOkb_400x400.jpg\' fue eliminado por \'ADMINISTRADOR ADMINISTRADOR\'', '2026-06-05 12:09:04'),
(64, 16, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Agregando monto de inventario', '2026-06-05 12:11:42'),
(65, 16, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Editando monto inventario', '2026-06-05 12:15:17'),
(66, 16, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Se agregó una foto', '2026-06-08 14:06:03'),
(67, 15, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Agregando fotos faltantes', '2026-06-09 08:04:26'),
(68, 17, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Más documentos agregados', '2026-06-09 08:08:28'),
(69, 17, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Agregando flores', '2026-06-09 08:10:45'),
(70, 3, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Agregando gastos', '2026-06-11 09:16:16'),
(71, 7, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Garant. Mob', '2026-06-11 14:40:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_solicitudes_notes`
--

CREATE TABLE `tb_solicitudes_notes` (
  `idnote` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(150) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_solicitud_aprobaciones`
--

CREATE TABLE `tb_solicitud_aprobaciones` (
  `idaprobacion` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `role` varchar(80) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(120) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `aprobado_por` varchar(50) DEFAULT NULL,
  `propuesta_overrides` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_solicitud_aprobaciones`
--

INSERT INTO `tb_solicitud_aprobaciones` (`idaprobacion`, `idsolicitud`, `role`, `user_id`, `username`, `comment`, `created_at`, `aprobado_por`, `propuesta_overrides`) VALUES
(1, 1, 'Validación', 15, 'ADMINISTRADOR ADMINISTRADOR', '[Aprobado] Aprobacion', '2026-05-13 09:22:14', 'Comite Interno', '[{\"id\":4,\"monto\":\"1500.00\",\"tasa\":\"6\",\"plazo\":\"24\",\"comision\":\"7\",\"comments\":{}}]'),
(2, 2, 'Validación', 15, 'ADMINISTRADOR ADMINISTRADOR', '[Aprobado] Aprobando en junio 8 [foto:solicitudes/2/1780932876_40ec99b6e713.jpg]', '2026-06-08 09:34:36', 'Comite Interno', '[{\"id\":3,\"monto\":\"1000.00\",\"tasa\":\"6\",\"plazo\":\"18\",\"comision\":\"7\",\"comments\":{}}]'),
(3, 3, 'Validación', 15, 'ADMINISTRADOR ADMINISTRADOR', '[Aprobado] Aprob de Junta 8/6 [foto:solicitudes/3/1780933796_c01158eab661.jpg]', '2026-06-08 09:49:56', 'Junta Directiva', '[{\"id\":4,\"monto\":\"2000.00\",\"tasa\":\"6\",\"plazo\":\"24\",\"comision\":\"7\",\"comments\":{}}]'),
(4, 9, 'Validación', 15, 'ADMINISTRADOR ADMINISTRADOR', '[Aprobado] Aprobado. Ejm', '2026-06-11 19:45:49', 'Comite Interno', '[{\"id\":4,\"monto\":\"1500.00\",\"tasa\":\"6\",\"plazo\":\"24\",\"comision\":\"7\",\"comments\":{}}]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_solicitud_faf`
--

CREATE TABLE `tb_solicitud_faf` (
  `idfaf` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL COMMENT 'asalariado|comerciante',
  `data` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_solicitud_photos`
--

CREATE TABLE `tb_solicitud_photos` (
  `idphoto` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `mime` varchar(50) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `grupo` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_solicitud_photos`
--

INSERT INTO `tb_solicitud_photos` (`idphoto`, `idsolicitud`, `filename`, `mime`, `size`, `created_at`, `grupo`) VALUES
(1, 2, 'solicitudes/2/cedula_front/1779200798_2a48d9fa.jpg', NULL, NULL, '2026-05-19 09:26:38', 'cedula_front'),
(2, 2, 'solicitudes/2/cedula_back/1779200798_c6bcdc28.png', NULL, NULL, '2026-05-19 09:26:38', 'cedula_back'),
(3, 2, 'solicitudes/2/fachada/1779200798_d24b3489.jpg', NULL, NULL, '2026-05-19 09:26:39', 'fachada'),
(4, 2, 'solicitudes/2/otros_ingresos_1/1779200799_c53f38ed.jpg', NULL, NULL, '2026-05-19 09:26:39', 'otros_ingresos_1'),
(5, 2, 'solicitudes/2/otros_ingresos_2/1779200799_9162e612.jpg', NULL, NULL, '2026-05-19 09:26:39', 'otros_ingresos_2'),
(6, 2, 'solicitudes/2/otros_ingresos_3/1779200799_ad14251e.jpg', NULL, NULL, '2026-05-19 09:26:39', 'otros_ingresos_3'),
(7, 2, 'solicitudes/2/cuentas_cobrar_1779200798_bab4cd7c.jpg', NULL, NULL, '2026-05-19 10:07:22', 'inventario'),
(11, 3, 'solicitudes/3/fachada/1779203656_eebe3fe2727d.jpg', 'image/jpeg', 81325, '2026-05-19 09:14:16', NULL),
(12, 3, 'solicitudes/3/otros_ingresos_1/1779203999_4166a753e49a.jpg', 'image/jpeg', 14298, '2026-05-19 09:19:59', NULL),
(14, 3, 'solicitudes/3/otros_ingresos_3/1779204015_d0d239b36f2a.jpg', 'image/jpeg', 8814, '2026-05-19 09:20:15', NULL),
(16, 3, 'solicitudes/3/evidencia/evidencia_1779204636_2eac3386.jpg', NULL, NULL, '2026-05-19 10:30:37', 'evidencia'),
(18, 3, 'solicitudes/3/otros_ingresos_2/1779205108_fc7b4c05b61e.jpg', 'image/jpeg', 8953, '2026-05-19 09:38:28', NULL),
(20, 3, 'solicitudes/3/otros_ingresos_1/1779205237_2437828ebb62.jpg', 'image/jpeg', 8953, '2026-05-19 09:40:37', NULL),
(21, 3, 'solicitudes/3/otros_ingresos_1/1779205245_6435fa36.jpg', NULL, NULL, '2026-05-19 10:40:45', 'otros_ingresos_1'),
(22, 4, 'solicitudes/4/evidencia/evidencia_1779207819_25d65eab.jpg', NULL, NULL, '2026-05-19 11:23:40', 'evidencia'),
(23, 4, 'solicitudes/4/cedula_front/1779207820_15d38ed5.jpg', NULL, NULL, '2026-05-19 11:23:40', 'cedula_front'),
(24, 4, 'solicitudes/4/cedula_back/1779207820_9807b5c8.jpg', NULL, NULL, '2026-05-19 11:23:40', 'cedula_back'),
(25, 4, 'solicitudes/4/fachada/1779207820_a3f88c79.jpg', NULL, NULL, '2026-05-19 11:23:40', 'fachada'),
(26, 4, 'solicitudes/4/otros_ingresos_1/1779207820_46944e42.jpg', NULL, NULL, '2026-05-19 11:23:40', 'otros_ingresos_1'),
(27, 4, 'solicitudes/4/otros_ingresos_2/1779207821_3cb0fccf.jpg', NULL, NULL, '2026-05-19 11:23:41', 'otros_ingresos_2'),
(28, 4, 'solicitudes/4/otros_ingresos_3/1779207821_850b0277.jpg', NULL, NULL, '2026-05-19 11:23:41', 'otros_ingresos_3'),
(29, 5, 'solicitudes/5/evidencia/evidencia_1779209364_0827d791.png', NULL, NULL, '2026-05-19 11:49:24', 'evidencia'),
(30, 5, 'solicitudes/5/cedula_front/1779209364_d8c7313d.jpg', NULL, NULL, '2026-05-19 11:49:24', 'cedula_front'),
(31, 5, 'solicitudes/5/cedula_back/1779209364_20ca455b.jpg', NULL, NULL, '2026-05-19 11:49:24', 'cedula_back'),
(33, 5, 'solicitudes/5/fachada/1779209364_902db4d0.png', NULL, NULL, '2026-05-19 11:49:24', 'fachada'),
(34, 5, 'solicitudes/5/otros_ingresos_1/1779209364_db0bc8e0.jpg', NULL, NULL, '2026-05-19 11:49:24', 'otros_ingresos_1'),
(35, 5, 'solicitudes/5/otros_ingresos_2/1779209364_94de23df.jpg', NULL, NULL, '2026-05-19 11:49:24', 'otros_ingresos_2'),
(36, 5, 'solicitudes/5/otros_ingresos_3/1779209364_ba666453.jpg', NULL, NULL, '2026-05-19 11:49:24', 'otros_ingresos_3'),
(43, 5, 'solicitudes/5/otros_ingresos_2/1779210750_dfb89cd8c5b5.png', 'image/png', 37844, '2026-05-19 11:12:30', 'otros_ingresos_2'),
(45, 5, 'solicitudes/5/otros_ingresos_3/1779210900_d0f59efa5cfa.jpg', 'image/jpeg', 13658, '2026-05-19 11:15:00', 'otros_ingresos_3'),
(47, 5, 'solicitudes/5/fachada/1779211851_cfe1be2971b2.jpg', 'image/jpeg', 8669, '2026-05-19 11:30:51', 'fachada'),
(48, 5, 'solicitudes/5/fachada/1779211861_483ce40d.jpg', NULL, NULL, '2026-05-19 12:31:01', 'fachada'),
(49, 6, 'solicitudes/6/fachada/1779212020_90dad353.jpg', NULL, NULL, '2026-05-19 12:33:40', 'fachada'),
(50, 6, 'solicitudes/6/otros_ingresos_1/1779212020_29dbcb28.jpg', NULL, NULL, '2026-05-19 12:33:40', 'otros_ingresos_1'),
(51, 6, 'solicitudes/6/otros_ingresos_1/1779212020_3e533a5e.jpg', NULL, NULL, '2026-05-19 12:33:40', 'otros_ingresos_1'),
(54, 6, 'solicitudes/6/cedula_back/1779212496_8c4e27c67d7f.png', 'image/png', 6745, '2026-05-19 11:41:36', 'cedula_back'),
(57, 6, 'solicitudes/6/cedula_front/1779212661_cf5df324aae4.jpg', 'image/jpeg', 9894, '2026-05-19 11:44:21', 'cedula_front'),
(58, 9, 'solicitudes/9/evidencia/evidencia_1779224989_8ff4e996.png', NULL, NULL, '2026-05-19 16:09:49', 'evidencia'),
(59, 8, 'solicitudes/8/otros_ingresos_1/1779225361_cc00ad73f300.jpg', 'image/jpeg', 7770, '2026-05-19 15:16:01', 'otros_ingresos_1'),
(60, 8, 'solicitudes/8/otros_ingresos_2/1779225489_5419c1ea6a07.png', 'image/png', 87869, '2026-05-19 15:18:09', 'otros_ingresos_2'),
(61, 8, 'solicitudes/8/otros_ingresos_3/1779225498_8e25a231fc72.jpg', 'image/jpeg', 13658, '2026-05-19 15:18:18', 'otros_ingresos_3'),
(62, 10, 'solicitudes/10/otros_ingresos_1/1779225637_0e908566.jpg', NULL, NULL, '2026-05-19 16:20:37', 'otros_ingresos_1'),
(63, 10, 'solicitudes/10/otros_ingresos_2/1779225637_249df85c.jpg', NULL, NULL, '2026-05-19 16:20:37', 'otros_ingresos_2'),
(64, 10, 'solicitudes/10/otros_ingresos_3/1779225637_3b8dc89e.jpg', NULL, NULL, '2026-05-19 16:20:37', 'otros_ingresos_3'),
(65, 10, 'solicitudes/10/fachada/1779225675_760056bfb287.png', 'image/png', 37844, '2026-05-19 15:21:15', 'fachada'),
(67, 11, 'solicitudes/11/cedula_front/1779226178_0ab1a12d.jpg', NULL, NULL, '2026-05-19 16:29:38', 'cedula_front'),
(68, 11, 'solicitudes/11/cedula_back/1779226178_ad9f7821.jpg', NULL, NULL, '2026-05-19 16:29:38', 'cedula_back'),
(69, 11, 'solicitudes/11/fachada/1779226178_8398def5.png', NULL, NULL, '2026-05-19 16:29:38', 'fachada'),
(70, 11, 'solicitudes/11/otros_ingresos_1/1779226178_a20b7da8.jpg', NULL, NULL, '2026-05-19 16:29:38', 'otros_ingresos_1'),
(71, 11, 'solicitudes/11/otros_ingresos_2/1779226178_f3f2bb26.jpg', NULL, NULL, '2026-05-19 16:29:38', 'otros_ingresos_2'),
(72, 11, 'solicitudes/11/otros_ingresos_3/1779226179_3a49ac5c.jpg', NULL, NULL, '2026-05-19 16:29:39', 'otros_ingresos_3'),
(73, 11, 'solicitudes/11/evidencia/evidencia_1779226336_c2d1a1c1.jpg', NULL, NULL, '2026-05-19 16:32:16', 'evidencia'),
(75, 12, 'solicitudes/12/cedula_front/1779286634_81bae8c8.jpg', NULL, NULL, '2026-05-20 09:17:14', 'cedula_front'),
(76, 12, 'solicitudes/12/cedula_back/1779286634_d6dfbf76.jpg', NULL, NULL, '2026-05-20 09:17:14', 'cedula_back'),
(77, 12, 'solicitudes/12/fachada/1779286634_9c81d26a.png', NULL, NULL, '2026-05-20 09:17:14', 'fachada'),
(78, 12, 'solicitudes/12/otros_ingresos_1/1779286634_aa7a4ea0.jpg', NULL, NULL, '2026-05-20 09:17:14', 'otros_ingresos_1'),
(79, 12, 'solicitudes/12/otros_ingresos_2/1779286634_abfe38a6.jpg', NULL, NULL, '2026-05-20 09:17:14', 'otros_ingresos_2'),
(80, 12, 'solicitudes/12/otros_ingresos_2/1779286634_51abfdee.jpg', NULL, NULL, '2026-05-20 09:17:14', 'otros_ingresos_2'),
(81, 12, 'solicitudes/12/otros_ingresos_3/1779286634_9dc70d6a.png', NULL, NULL, '2026-05-20 09:17:14', 'otros_ingresos_3'),
(84, 12, 'solicitudes/12/1779373417_328ab6e3d512.jpg', 'image/jpeg', 7304, '2026-05-21 08:23:37', 'fotos_adicionales'),
(85, 12, 'solicitudes/12/1779373783_ffc3f392fd16.jpg', 'image/jpeg', 81325, '2026-05-21 08:29:43', 'consentimiento_filtrado'),
(90, 12, 'solicitudes/12/docs_generales/Docu3.pdf', NULL, NULL, '2026-05-21 09:44:03', 'docs_generales'),
(93, 12, 'solicitudes/12/docs_generales/formato_garantia_1 _3_.pdf', NULL, NULL, '2026-05-21 09:50:12', 'docs_generales'),
(96, 12, 'solicitudes/12/docs_generales/form anterior.jpeg', NULL, NULL, '2026-05-21 10:06:27', 'docs_generales'),
(98, 12, 'solicitudes/12/docs_generales/images.jpg', NULL, NULL, '2026-05-21 10:09:57', 'docs_generales'),
(99, 12, 'solicitudes/12/docs_legales/Docu4.pdf', NULL, NULL, '2026-05-21 10:35:24', 'docs_legales'),
(100, 12, 'solicitudes/12/evidencia/evidencia_1779379234_024f782d.jpg', NULL, NULL, '2026-05-21 11:00:34', 'evidencia'),
(101, 13, 'solicitudes/13/evidencia/evidencia_1779381151_7ad4369f.jpg', NULL, NULL, '2026-05-21 11:32:31', 'evidencia'),
(102, 13, 'solicitudes/13/cedula_front/1779381151_6b6c5d55.jpg', NULL, NULL, '2026-05-21 11:32:31', 'cedula_front'),
(103, 13, 'solicitudes/13/cedula_back/1779381151_9c2ece76.jpg', NULL, NULL, '2026-05-21 11:32:31', 'cedula_back'),
(104, 13, 'solicitudes/13/consentimiento_filtrado/1779381151_717790ea.jpg', NULL, NULL, '2026-05-21 11:32:31', 'consentimiento_filtrado'),
(105, 13, 'solicitudes/13/fachada/1779381151_bac6ecea.jpg', NULL, NULL, '2026-05-21 11:32:31', 'fachada'),
(106, 13, 'solicitudes/13/otros_ingresos_1/1779381151_80c5498a.jpg', NULL, NULL, '2026-05-21 11:32:31', 'otros_ingresos_1'),
(107, 13, 'solicitudes/13/otros_ingresos_2/1779381151_be39efe0.png', NULL, NULL, '2026-05-21 11:32:31', 'otros_ingresos_2'),
(108, 13, 'solicitudes/13/otros_ingresos_3/1779381151_844fcb81.jpg', NULL, NULL, '2026-05-21 11:32:31', 'otros_ingresos_3'),
(109, 13, 'solicitudes/13/docs_generales/1779381152_0893c5a8.pdf', NULL, NULL, '2026-05-21 11:32:32', 'docs_generales'),
(110, 13, 'solicitudes/13/docs_legales/1779381152_04dfe53b.pdf', NULL, NULL, '2026-05-21 11:32:32', 'docs_legales'),
(111, 13, 'solicitudes/13/fotos_adicionales/1779381152_adad15da.jpg', NULL, NULL, '2026-05-21 11:32:32', 'fotos_adicionales'),
(112, 13, 'solicitudes/13/formato_garantia_1 _1_.pdf', 'application/pdf', 911476, '2026-05-21 10:34:12', 'docs_generales'),
(113, 13, 'solicitudes/13/docs_generales/Docu4.pdf', NULL, NULL, '2026-05-21 11:37:34', 'docs_generales'),
(114, 14, 'solicitudes/14/evidencia/images _1_.jpg', NULL, NULL, '2026-05-21 12:23:24', 'evidencia'),
(115, 14, 'solicitudes/14/cedula_front/images _3_.jpg', NULL, NULL, '2026-05-21 12:23:24', 'cedula_front'),
(116, 14, 'solicitudes/14/cedula_back/images _4_.jpg', NULL, NULL, '2026-05-21 12:23:24', 'cedula_back'),
(117, 14, 'solicitudes/14/consentimiento_filtrado/images _6_.jpg', NULL, NULL, '2026-05-21 12:23:24', 'consentimiento_filtrado'),
(118, 14, 'solicitudes/14/fachada/images _2_.jpg', NULL, NULL, '2026-05-21 12:23:24', 'fachada'),
(119, 14, 'solicitudes/14/otros_ingresos_1/6cgqAOkb_400x400.jpg', NULL, NULL, '2026-05-21 12:23:24', 'otros_ingresos_1'),
(120, 14, 'solicitudes/14/otros_ingresos_1/360_F_341448248_AJ0S225TTqp2uFBqQmvHRh5Ah5FP09T1.jpg', NULL, NULL, '2026-05-21 12:23:24', 'otros_ingresos_1'),
(121, 14, 'solicitudes/14/otros_ingresos_2/adicionales.jpg', NULL, NULL, '2026-05-21 12:23:24', 'otros_ingresos_2'),
(122, 14, 'solicitudes/14/otros_ingresos_3/foto-generica.jpg', NULL, NULL, '2026-05-21 12:23:24', 'otros_ingresos_3'),
(123, 14, 'solicitudes/14/docs_generales/Docu1.pdf', NULL, NULL, '2026-05-21 12:23:24', 'docs_generales'),
(124, 14, 'solicitudes/14/docs_legales/Docu3.pdf', NULL, NULL, '2026-05-21 12:23:24', 'docs_legales'),
(125, 14, 'solicitudes/14/fotos_adicionales/images _5_.jpg', NULL, NULL, '2026-05-21 12:23:24', 'fotos_adicionales'),
(126, 15, 'solicitudes/15/evidencia/images _1_.jpg', NULL, NULL, '2026-05-25 10:53:39', 'evidencia'),
(127, 15, 'solicitudes/15/cedula_front/images _3_.jpg', NULL, NULL, '2026-05-25 10:53:39', 'cedula_front'),
(128, 15, 'solicitudes/15/cedula_back/images _4_.jpg', NULL, NULL, '2026-05-25 10:53:39', 'cedula_back'),
(129, 15, 'solicitudes/15/consentimiento_filtrado/images _8_.jpg', NULL, NULL, '2026-05-25 10:53:39', 'consentimiento_filtrado'),
(130, 15, 'solicitudes/15/fachada/images _2_.jpg', NULL, NULL, '2026-05-25 10:53:39', 'fachada'),
(131, 15, 'solicitudes/15/otros_ingresos_1/6cgqAOkb_400x400.jpg', NULL, NULL, '2026-05-25 10:53:39', 'otros_ingresos_1'),
(132, 15, 'solicitudes/15/otros_ingresos_2/360_F_341448248_AJ0S225TTqp2uFBqQmvHRh5Ah5FP09T1.jpg', NULL, NULL, '2026-05-25 10:53:39', 'otros_ingresos_2'),
(133, 15, 'solicitudes/15/otros_ingresos_3/foto-generica.jpg', NULL, NULL, '2026-05-25 10:53:39', 'otros_ingresos_3'),
(134, 15, 'solicitudes/15/docs_generales/Docu1.pdf', NULL, NULL, '2026-05-25 10:53:39', 'docs_generales'),
(135, 15, 'solicitudes/15/docs_legales/Docu2.pdf', NULL, NULL, '2026-05-25 10:53:39', 'docs_legales'),
(136, 15, 'solicitudes/15/fotos_adicionales/images _7_.jpg', NULL, NULL, '2026-05-25 10:53:39', 'fotos_adicionales'),
(137, 16, 'solicitudes/16/evidencia/images _1_.jpg', NULL, NULL, '2026-06-05 13:01:53', 'evidencia'),
(139, 16, 'solicitudes/16/cedula_back/images _3_.jpg', NULL, NULL, '2026-06-05 13:01:53', 'cedula_back'),
(140, 16, 'solicitudes/16/consentimiento_filtrado/images _6_.jpg', NULL, NULL, '2026-06-05 13:01:54', 'consentimiento_filtrado'),
(141, 16, 'solicitudes/16/fachada/foto-generica.jpg', NULL, NULL, '2026-06-05 13:01:54', 'fachada'),
(142, 16, 'solicitudes/16/fachada/foto-perfil-generica.jpg', NULL, NULL, '2026-06-05 13:01:54', 'fachada'),
(143, 16, 'solicitudes/16/otros_ingresos_1/360_F_341448248_AJ0S225TTqp2uFBqQmvHRh5Ah5FP09T1.jpg', NULL, NULL, '2026-06-05 13:01:54', 'otros_ingresos_1'),
(145, 16, 'solicitudes/16/otros_ingresos_2/adicionales.jpg', NULL, NULL, '2026-06-05 13:01:54', 'otros_ingresos_2'),
(146, 16, 'solicitudes/16/otros_ingresos_3/form anterior.jpeg', NULL, NULL, '2026-06-05 13:01:54', 'otros_ingresos_3'),
(147, 16, 'solicitudes/16/docs_generales/Docu1.pdf', NULL, NULL, '2026-06-05 13:01:54', 'docs_generales'),
(148, 16, 'solicitudes/16/docs_legales/Docu2.pdf', NULL, NULL, '2026-06-05 13:01:54', 'docs_legales'),
(149, 16, 'solicitudes/16/docs_legales/Docu3.pdf', NULL, NULL, '2026-06-05 13:01:54', 'docs_legales'),
(150, 16, 'solicitudes/16/fotos_adicionales/images _4_.jpg', NULL, NULL, '2026-06-05 13:01:54', 'fotos_adicionales'),
(152, 2, 'solicitudes/2/1780932876_40ec99b6e713.jpg', 'image/jpeg', 14298, '2026-06-08 09:34:36', NULL),
(153, 3, 'solicitudes/3/1780933796_c01158eab661.jpg', 'image/jpeg', 13658, '2026-06-08 09:49:56', NULL),
(154, 16, 'solicitudes/16/cedula_front/pngtree-generic-manual-focus-photo-camera-lens-isolated-png-image_11511754.png', 'image/png', 37844, '2026-06-08 14:05:52', 'cedula_front'),
(155, 15, 'solicitudes/15/png-transparent-social-media-icons-generic-drug-email-silhouette-head-black-and-white-neck.png', 'image/png', 6355, '2026-06-09 08:04:16', 'fotos_adicionales'),
(156, 15, 'solicitudes/15/istockphoto-1142192548-612x612.jpg', 'image/jpeg', 13885, '2026-06-09 08:04:16', 'fotos_adicionales'),
(157, 17, 'solicitudes/17/fotos_adicionales/6cgqAOkb_400x400.jpg', NULL, NULL, '2026-06-09 09:07:07', 'fotos_adicionales'),
(158, 17, 'solicitudes/17/fotos_adicionales/360_F_341448248_AJ0S225TTqp2uFBqQmvHRh5Ah5FP09T1.jpg', NULL, NULL, '2026-06-09 09:07:07', 'fotos_adicionales'),
(159, 17, 'solicitudes/17/fotos_adicionales/adicionales.jpg', NULL, NULL, '2026-06-09 09:07:07', 'fotos_adicionales'),
(160, 17, 'solicitudes/17/fotos_adicionales/form anterior.jpeg', NULL, NULL, '2026-06-09 09:07:07', 'fotos_adicionales'),
(161, 17, 'solicitudes/17/Docu1.pdf', 'application/pdf', 912237, '2026-06-09 08:08:01', 'docs_generales'),
(163, 17, 'solicitudes/17/images _2_.jpg', 'image/jpeg', 13658, '2026-06-09 08:10:34', 'fotos_adicionales');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_solicitud_propuestas`
--

CREATE TABLE `tb_solicitud_propuestas` (
  `idpropuesta` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `idtipo_producto` int(11) NOT NULL,
  `monto` decimal(15,2) DEFAULT NULL,
  `tasa` decimal(10,4) DEFAULT NULL,
  `comision_desembolso` decimal(10,4) DEFAULT NULL,
  `plazo_min` int(11) DEFAULT NULL,
  `plazo_max` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_solicitud_propuestas`
--

INSERT INTO `tb_solicitud_propuestas` (`idpropuesta`, `idsolicitud`, `idtipo_producto`, `monto`, `tasa`, `comision_desembolso`, `plazo_min`, `plazo_max`, `created_at`) VALUES
(1, 1, 4, '1500.00', '6.0000', '7.0000', 12, 24, '2026-05-13 09:22:14'),
(2, 2, 3, '1000.00', '6.0000', '7.0000', 10, 18, '2026-06-08 09:34:37'),
(3, 3, 4, '2000.00', '6.0000', '7.0000', 12, 24, '2026-06-08 09:49:56'),
(4, 9, 4, '1500.00', '6.0000', '7.0000', 12, 24, '2026-06-11 19:45:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_solicitud_propuestas_history`
--

CREATE TABLE `tb_solicitud_propuestas_history` (
  `idhistory` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `idtipo_producto` int(11) DEFAULT NULL,
  `field_name` varchar(80) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(120) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_solicitud_referencias`
--

CREATE TABLE `tb_solicitud_referencias` (
  `idreferencia` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `referencia_num` tinyint(4) NOT NULL DEFAULT 1,
  `nombre` varchar(255) DEFAULT NULL,
  `cedula` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `telefono` varchar(100) DEFAULT NULL,
  `tipo_referencia` varchar(80) DEFAULT NULL,
  `desde_conoce_cliente` varchar(255) DEFAULT NULL,
  `relacion_economica` tinyint(4) DEFAULT NULL,
  `opinion` varchar(255) DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `tipo_personal_relacion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_solicitud_referencias`
--

INSERT INTO `tb_solicitud_referencias` (`idreferencia`, `idsolicitud`, `referencia_num`, `nombre`, `cedula`, `direccion`, `telefono`, `tipo_referencia`, `desde_conoce_cliente`, `relacion_economica`, `opinion`, `comentarios`, `created_at`, `tipo_personal_relacion`) VALUES
(1, 1, 1, 'Heysel Massiel Uyoa ', '0013011940043Q', 'direccion', '333333', 'Personal', '2', 1, 'Excelente', 'bueno', '2026-05-13 09:21:51', 'Compañero de Trabajo'),
(2, 1, 2, 'Distribuidora Orozco La Bendicion', '', '', '', 'Comercial', '2', 1, 'Excelente', 'bueno', '2026-05-13 09:21:51', ''),
(3, 2, 1, 'Referencia Persona Apellido1 Apellido2', '0014545458779L', 'Casa', '88996655', 'Personal', '9 años', 1, 'Excelente', 'Muy buena persona', '2026-05-19 08:51:38', 'Amigo'),
(4, 2, 2, 'Otra Referencia Yosoy Yo', '4557878784444K', 'Otra casa', '77777777', 'Comercial', '7 años', 1, 'Excelente', '', '2026-05-19 08:51:39', ''),
(5, 12, 1, 'Referencia Primera', '4444ID', 'Casa de ref 1', '78965656', 'Personal', '2 años', 1, 'Excelente', 'Nada ref 1', '2026-05-20 09:54:07', 'Amigo'),
(6, 12, 2, 'Referencia Segunda', '5555ID', 'Casa ref 2', '+50698997451', 'Comercial', '1 año', 1, 'Excelente', 'Nada mas ref 2', '2026-05-20 09:54:08', ''),
(7, 15, 1, 'Referencia Primera de Flor', '1118888881515D', 'Dirección Flor', '96965656', 'Comercial', 'Hace mucho', 0, 'Excelente', 'Nada', '2026-05-25 13:11:07', ''),
(8, 15, 2, 'Referencia Segunda', '4448484844444D', 'Una dirección', '78877887', 'Personal', '2 años', 1, 'Excelente', 'Nada junio', '2026-05-25 13:11:07', 'Amigo'),
(9, 16, 1, 'Mar Refi', '1231212121221J', 'La casa de refi1', '78879889', 'Personal', 'Desde que nací', 1, 'Buena', 'Adiciono que estoy bien', '2026-06-05 12:19:41', 'Amigo'),
(10, 16, 2, 'Refi2', '1233232324554K', 'Dirección de refi2', '123243231', 'Comercial', '3 años', 1, 'Buena', 'Nada que comentar', '2026-06-05 12:19:41', ''),
(11, 17, 1, 'Referencia Primera', '0014545458779L', 'Casa de referencia 1', '77777777', 'Personal', '2 años', 1, 'Buena', 'Es buena paga', '2026-06-09 08:17:08', 'Vecino'),
(12, 17, 2, 'Referencia Segunda', '4557878784444K', 'Casa de segunda referencia', '77777777', 'Comercial', '3 años', 1, 'Buena', '', '2026-06-09 08:17:10', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_solicitud_referencias_fotos`
--

CREATE TABLE `tb_solicitud_referencias_fotos` (
  `idfoto` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `idreferencia` int(11) DEFAULT NULL,
  `referencia_num` tinyint(4) DEFAULT NULL,
  `tipo` varchar(10) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `tb_solicitud_referencias_fotos`
--

INSERT INTO `tb_solicitud_referencias_fotos` (`idfoto`, `idsolicitud`, `idreferencia`, `referencia_num`, `tipo`, `filename`, `created_at`) VALUES
(1, 2, 3, 1, 'front', 'uploads/solicitudes/solicitud_2/referencias/referencia_1/1779202299_280c57ce_front.jpg', '2026-05-19 08:51:39'),
(2, 2, 3, 1, 'back', 'uploads/solicitudes/solicitud_2/referencias/referencia_1/1779202299_bf20e287_back.jpg', '2026-05-19 08:51:39'),
(3, 2, 4, 2, 'front', 'uploads/solicitudes/solicitud_2/referencias/referencia_2/1779202299_8f29557f_front.jpg', '2026-05-19 08:51:39'),
(4, 2, 4, 2, 'back', 'uploads/solicitudes/solicitud_2/referencias/referencia_2/1779202299_a2bc3c00_back.jpg', '2026-05-19 08:51:39'),
(5, 12, 5, 1, 'front', 'uploads/solicitudes/solicitud_12/referencias/referencia_1/1779292448_e5232f86_front.jpg', '2026-05-20 09:54:08'),
(6, 12, 5, 1, 'back', 'uploads/solicitudes/solicitud_12/referencias/referencia_1/1779292448_31c9e4ca_back.jpg', '2026-05-20 09:54:08'),
(7, 12, 6, 2, 'front', 'uploads/solicitudes/solicitud_12/referencias/referencia_2/1779292448_f369205b_front.jpg', '2026-05-20 09:54:08'),
(8, 12, 6, 2, 'back', 'uploads/solicitudes/solicitud_12/referencias/referencia_2/1779292448_1203fa20_back.png', '2026-05-20 09:54:08'),
(9, 12, 5, 1, 'back', 'uploads/solicitudes/solicitud_12/referencias/referencia_1/1779292811_5bac0b5d_back.jpg', '2026-05-20 10:00:11'),
(10, 15, 7, 1, 'front', 'uploads/solicitudes/solicitud_15/referencias/referencia_1/1779736341_bea6e3ad_front.jpg', '2026-05-25 13:12:21'),
(11, 15, 7, 1, 'back', 'uploads/solicitudes/solicitud_15/referencias/referencia_1/1779736341_c26f8423_back.jpg', '2026-05-25 13:12:21'),
(12, 15, 7, 1, 'front', 'uploads/solicitudes/solicitud_15/referencias/referencia_1/1779736413_ae97920b_front.jpg', '2026-05-25 13:13:33'),
(13, 15, 7, 1, 'back', 'uploads/solicitudes/solicitud_15/referencias/referencia_1/1779736413_6b51a5d7_back.jpg', '2026-05-25 13:13:33'),
(14, 15, 8, 2, 'front', 'uploads/solicitudes/solicitud_15/referencias/referencia_2/1779736413_8767885e_front.png', '2026-05-25 13:13:33'),
(15, 15, 8, 2, 'back', 'uploads/solicitudes/solicitud_15/referencias/referencia_2/1779736413_b0b34388_back.png', '2026-05-25 13:13:33'),
(16, 16, 9, 1, 'front', 'uploads/solicitudes/solicitud_16/referencias/referencia_1/1780683581_772c80f6_front.jpg', '2026-06-05 12:19:41'),
(17, 16, 10, 2, 'front', 'uploads/solicitudes/solicitud_16/referencias/referencia_2/1780683581_39917861_front.jpg', '2026-06-05 12:19:41'),
(18, 16, 9, 1, 'back', 'uploads/solicitudes/solicitud_16/referencias/referencia_1/1780687691_daa57c50_back.jpg', '2026-06-05 13:28:11'),
(19, 17, 11, 1, 'front', 'uploads/solicitudes/solicitud_17/referencias/referencia_1/1781014629_e1c6281a_front.jpg', '2026-06-09 08:17:09'),
(20, 17, 11, 1, 'back', 'uploads/solicitudes/solicitud_17/referencias/referencia_1/1781014630_37ef61c5_back.jpg', '2026-06-09 08:17:10'),
(21, 17, 12, 2, 'front', 'uploads/solicitudes/solicitud_17/referencias/referencia_2/1781014630_3d132214_front.jpg', '2026-06-09 08:17:10'),
(22, 17, 12, 2, 'back', 'uploads/solicitudes/solicitud_17/referencias/referencia_2/1781014630_405da713_back.jpg', '2026-06-09 08:17:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_solicitud_uso_credito`
--

CREATE TABLE `tb_solicitud_uso_credito` (
  `iduso` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fuente_ingreso` text DEFAULT NULL,
  `monto_estimado_mes` decimal(15,2) DEFAULT NULL,
  `declaracion_nombre` varchar(255) DEFAULT NULL,
  `declaracion_firma` varchar(255) DEFAULT NULL,
  `declaracion_fecha` date DEFAULT NULL,
  `evaluador_credito` varchar(150) DEFAULT NULL,
  `fecha_evaluacion` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `monto_solicitado` decimal(15,2) DEFAULT NULL,
  `plazo_solicitado` int(11) DEFAULT NULL,
  `destino_prestamo` varchar(100) DEFAULT NULL,
  `destino_detalle` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_solicitud_uso_credito`
--

INSERT INTO `tb_solicitud_uso_credito` (`iduso`, `idsolicitud`, `descripcion`, `fuente_ingreso`, `monto_estimado_mes`, `declaracion_nombre`, `declaracion_firma`, `declaracion_fecha`, `evaluador_credito`, `fecha_evaluacion`, `created_at`, `monto_solicitado`, `plazo_solicitado`, `destino_prestamo`, `destino_detalle`) VALUES
(1, 1, 'Detalle', 'Pulperia', '157000.00', 'ERICK CARRILLO RAMIREZ', 'ERICK CARRILLO RAMIREZ', '2026-05-19', 'EV_CR_1', '2026-05-18', '2026-05-13 09:21:09', '1500.00', 24, 'Consumo', 'Mi préstamo será para algo'),
(2, 10, '', 'Mi nueva empresa', '400.00', 'UNO DOS TRES CUATRO', 'UNO DOS TRES CUATRO', NULL, '', '2026-04-29', '2026-05-19 20:31:04', '1500.00', 24, NULL, NULL),
(3, 11, '', 'Mi nueva solicitud de negocio', '26000.00', '', 'ERICK CARRILLO RAMIREZ', '2026-05-04', '', '2026-05-20', '2026-05-19 20:31:53', '1500.00', 24, 'Consumo', NULL),
(4, 3, '', 'Mi nueva empresa', '42000.00', 'MARIA JOSE LOPEZ MENA', 'MARIA JOSE LOPEZ MENA', '2026-05-19', '', NULL, '2026-05-19 20:53:08', '2000.00', 24, 'Inversión', NULL),
(5, 2, '', 'Pruebas de Negocios', '17000.00', 'JOSE MARIA MORALES LOPEZ', 'JOSE MARIA MORALES LOPEZ', '2026-05-19', '', '2026-05-18', '2026-05-19 21:04:36', '1000.00', 18, 'Capital de trabajo', NULL),
(6, 12, '', 'Mi negocio de venta', '79000.00', 'MARIA KAMILA DEL SOCORRO HERNANDEZ MORALES', 'MARIA KAMILA DEL SOCORRO HERNANDEZ MORALES', '2026-05-20', 'Firma Prueba', '2025-11-16', '2026-05-20 08:18:23', '2000.00', 24, NULL, NULL),
(7, 15, 'Cosas de comida', 'Venta de Comida', '64000.00', 'FLORA MARIA FLORES DEL AIRE', 'FLORA MARIA FLORES DEL AIRE', '2026-05-25', '', '2026-05-24', '2026-05-25 13:04:06', '1500.00', 24, 'Consumo', 'Uso de Junio'),
(8, 16, 'Sin detalle particular', 'Venta de juguetes', '106000.00', 'YOLANDA MARIA NUÑEZ PEREZ', 'YOLANDA MARIA NUÑEZ PEREZ', '2026-06-05', '', '2026-06-08', '2026-06-05 12:15:53', '3000.00', 20, 'Consumo', 'Guardando el uso de crédito en junio cinco'),
(9, 17, 'Dinero de inversión', 'Empresa', '170000.00', 'SOL MARIA JOHNSON OTHER', 'SOL MARIA JOHNSON OTHER', '2026-06-08', '', '2026-06-09', '2026-06-09 08:13:01', '1500.00', 18, 'Consumo', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_tasa_cambio`
--

CREATE TABLE `tb_tasa_cambio` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `tasa_cambio` decimal(10,4) NOT NULL,
  `tasa_venta` decimal(10,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_tasa_cambio`
--

INSERT INTO `tb_tasa_cambio` (`id`, `fecha`, `tasa_cambio`, `tasa_venta`, `created_at`, `updated_at`) VALUES
(1, '2026-02-26', '36.6200', '37.0000', '2026-01-06 18:43:10', '2026-02-26 15:47:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_tipo_productos`
--

CREATE TABLE `tb_tipo_productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `porcentaje` decimal(7,4) NOT NULL DEFAULT 0.0000,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `monto_min` decimal(12,2) DEFAULT NULL,
  `monto_max` decimal(12,2) DEFAULT NULL,
  `tasa_mensual` decimal(7,4) DEFAULT NULL,
  `comision_desembolso` decimal(7,4) DEFAULT NULL,
  `plazo_min` int(11) DEFAULT NULL,
  `plazo_max` int(11) DEFAULT NULL,
  `clasificacion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_tipo_productos`
--

INSERT INTO `tb_tipo_productos` (`id`, `nombre`, `porcentaje`, `estado`, `created_at`, `monto_min`, `monto_max`, `tasa_mensual`, `comision_desembolso`, `plazo_min`, `plazo_max`, `clasificacion`) VALUES
(1, 'Micronegocio 300-499', '0.0800', 1, '2025-12-10 14:38:16', '300.00', '499.00', '0.0800', '0.0700', 6, 12, 'Negocios'),
(2, 'Micronegocio 500-999', '0.0600', 1, '2025-12-10 14:38:16', '500.00', '999.00', '0.0600', '0.0700', 6, 12, 'Negocios'),
(3, 'Pequeño negocio 1 1000-1499', '0.0600', 1, '2025-12-10 14:38:16', '1000.00', '1499.00', '0.0600', '0.0700', 10, 18, 'Negocios'),
(4, 'Pequeño Negocio 2 1500-4999', '0.0600', 1, '2025-12-10 14:38:16', '1500.00', '4999.00', '0.0600', '0.0700', 12, 24, 'Negocios'),
(5, 'Pequeño Negocio 3 5000-9999', '0.0500', 1, '2025-12-10 14:38:16', '5000.00', '9999.00', '0.0500', '0.0500', 12, 36, 'Negocios'),
(6, 'Pequeña Industria 10000-19999', '0.0400', 1, '2025-12-10 14:38:16', '10000.00', '19999.00', '0.0400', '0.0500', 24, 48, 'Negocios'),
(7, 'Pequeña Industria 20000-25000', '0.0400', 1, '2025-12-10 14:38:16', '20000.00', '25000.00', '0.0400', '0.0500', 24, 48, 'Negocios'),
(8, 'Personal 300-499', '0.0800', 1, '2025-12-10 15:00:04', '300.00', '499.00', '0.0800', '0.0700', 4, 8, 'Personal'),
(9, 'Personal 500-999', '0.0600', 1, '2025-12-10 15:00:04', '500.00', '999.00', '0.0600', '0.0700', 6, 12, 'Personal'),
(10, 'Personal 1000-1499', '0.0600', 1, '2025-12-10 15:00:04', '1000.00', '1499.00', '0.0600', '0.0700', 10, 18, 'Personal'),
(11, 'Compra de lote 3000-4999', '0.0600', 1, '2025-12-10 15:01:58', '3000.00', '4999.00', '0.0600', '0.0700', 12, 36, 'Viviendo o Hipotecario'),
(12, 'Compra de lote 5000-7999', '0.0500', 1, '2025-12-10 15:01:58', '5000.00', '7999.00', '0.0500', '0.0500', 24, 36, 'Viviendo o Hipotecario'),
(13, 'Compra de lote 8000-10000', '0.0500', 1, '2025-12-10 15:01:58', '8000.00', '10000.00', '0.0500', '0.0500', 24, 36, 'Viviendo o Hipotecario'),
(14, 'Mejora de vivienda 300-499', '0.0600', 1, '2025-12-10 15:01:58', '300.00', '499.00', '0.0600', '0.0700', 6, 12, 'Viviendo o Hipotecario'),
(15, 'Mejora de vivienda 500-999', '0.0600', 1, '2025-12-10 15:01:58', '500.00', '999.00', '0.0600', '0.0700', 6, 12, 'Viviendo o Hipotecario'),
(16, 'Mejora de vivienda 1000-1499', '0.0600', 1, '2025-12-10 15:01:58', '1000.00', '1499.00', '0.0600', '0.0700', 8, 24, 'Viviendo o Hipotecario'),
(17, 'Mejora de vivienda 1500-3000', '0.0600', 1, '2025-12-10 15:01:58', '1500.00', '3000.00', '0.0600', '0.0700', 12, 24, 'Viviendo o Hipotecario'),
(18, 'Vehiculo usado 2000-2999', '0.0600', 1, '2025-12-10 15:11:47', '2000.00', '2999.00', '0.0600', '0.0700', 8, 18, 'Vehiculos Usados'),
(19, 'Vehiculo usado 3000-4999', '0.0600', 1, '2025-12-10 15:11:47', '3000.00', '4999.00', '0.0600', '0.0700', 12, 24, 'Vehiculos Usados'),
(20, 'Vehiculo usado 5000-9999', '0.0500', 1, '2025-12-10 15:11:47', '5000.00', '9999.00', '0.0500', '0.0500', 12, 36, 'Vehiculos Usados'),
(1, 'Micronegocio 300-499', '0.0800', 1, '2025-12-10 14:38:16', '300.00', '499.00', '0.0800', '0.0700', 6, 12, 'Negocios'),
(2, 'Micronegocio 500-999', '0.0600', 1, '2025-12-10 14:38:16', '500.00', '999.00', '0.0600', '0.0700', 6, 12, 'Negocios'),
(3, 'Pequeño negocio 1 1000-1499', '0.0600', 1, '2025-12-10 14:38:16', '1000.00', '1499.00', '0.0600', '0.0700', 10, 18, 'Negocios'),
(4, 'Pequeño Negocio 2 1500-4999', '0.0600', 1, '2025-12-10 14:38:16', '1500.00', '4999.00', '0.0600', '0.0700', 12, 24, 'Negocios'),
(5, 'Pequeño Negocio 3 5000-9999', '0.0500', 1, '2025-12-10 14:38:16', '5000.00', '9999.00', '0.0500', '0.0500', 12, 36, 'Negocios'),
(6, 'Pequeña Industria 10000-19999', '0.0400', 1, '2025-12-10 14:38:16', '10000.00', '19999.00', '0.0400', '0.0500', 24, 48, 'Negocios'),
(7, 'Pequeña Industria 20000-25000', '0.0400', 1, '2025-12-10 14:38:16', '20000.00', '25000.00', '0.0400', '0.0500', 24, 48, 'Negocios'),
(8, 'Personal 300-499', '0.0800', 1, '2025-12-10 15:00:04', '300.00', '499.00', '0.0800', '0.0700', 4, 8, 'Personal'),
(9, 'Personal 500-999', '0.0600', 1, '2025-12-10 15:00:04', '500.00', '999.00', '0.0600', '0.0700', 6, 12, 'Personal'),
(10, 'Personal 1000-1499', '0.0600', 1, '2025-12-10 15:00:04', '1000.00', '1499.00', '0.0600', '0.0700', 10, 18, 'Personal'),
(11, 'Compra de lote 3000-4999', '0.0600', 1, '2025-12-10 15:01:58', '3000.00', '4999.00', '0.0600', '0.0700', 12, 36, 'Viviendo o Hipotecario'),
(12, 'Compra de lote 5000-7999', '0.0500', 1, '2025-12-10 15:01:58', '5000.00', '7999.00', '0.0500', '0.0500', 24, 36, 'Viviendo o Hipotecario'),
(13, 'Compra de lote 8000-10000', '0.0500', 1, '2025-12-10 15:01:58', '8000.00', '10000.00', '0.0500', '0.0500', 24, 36, 'Viviendo o Hipotecario'),
(14, 'Mejora de vivienda 300-499', '0.0600', 1, '2025-12-10 15:01:58', '300.00', '499.00', '0.0600', '0.0700', 6, 12, 'Viviendo o Hipotecario'),
(15, 'Mejora de vivienda 500-999', '0.0600', 1, '2025-12-10 15:01:58', '500.00', '999.00', '0.0600', '0.0700', 6, 12, 'Viviendo o Hipotecario'),
(16, 'Mejora de vivienda 1000-1499', '0.0600', 1, '2025-12-10 15:01:58', '1000.00', '1499.00', '0.0600', '0.0700', 8, 24, 'Viviendo o Hipotecario'),
(17, 'Mejora de vivienda 1500-3000', '0.0600', 1, '2025-12-10 15:01:58', '1500.00', '3000.00', '0.0600', '0.0700', 12, 24, 'Viviendo o Hipotecario'),
(18, 'Vehiculo usado 2000-2999', '0.0600', 1, '2025-12-10 15:11:47', '2000.00', '2999.00', '0.0600', '0.0700', 8, 18, 'Vehiculos Usados'),
(19, 'Vehiculo usado 3000-4999', '0.0600', 1, '2025-12-10 15:11:47', '3000.00', '4999.00', '0.0600', '0.0700', 12, 24, 'Vehiculos Usados'),
(20, 'Vehiculo usado 5000-9999', '0.0500', 1, '2025-12-10 15:11:47', '5000.00', '9999.00', '0.0500', '0.0500', 12, 36, 'Vehiculos Usados');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `teso_accounts`
--

CREATE TABLE `teso_accounts` (
  `id` int(11) NOT NULL,
  `code` varchar(64) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(128) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `currency_symbol` varchar(8) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_apertura` date DEFAULT NULL,
  `clabe` varchar(30) DEFAULT NULL,
  `sig_cheque` int(11) DEFAULT NULL,
  `dia_corte` int(11) DEFAULT NULL,
  `ultimo_dia_mes` tinyint(1) DEFAULT 0,
  `formato` varchar(50) DEFAULT NULL,
  `cuenta_contable` varchar(50) DEFAULT NULL,
  `nombre_banco` varchar(100) DEFAULT NULL,
  `clave_banco` varchar(20) DEFAULT NULL,
  `sucursal` varchar(100) DEFAULT NULL,
  `funcionario` varchar(100) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `plaza` varchar(50) DEFAULT NULL,
  `logo_banco` varchar(255) DEFAULT NULL,
  `rfc` varchar(20) DEFAULT NULL,
  `banco_extranjero` tinyint(1) DEFAULT 0,
  `saldo_conciliado` decimal(18,2) DEFAULT 0.00,
  `total_cargos` decimal(18,2) DEFAULT 0.00,
  `total_abonos` decimal(18,2) DEFAULT 0.00,
  `cargos_transito` decimal(18,2) DEFAULT 0.00,
  `abonos_transito` decimal(18,2) DEFAULT 0.00,
  `montos_transito` decimal(18,2) DEFAULT 0.00,
  `saldos_sin_transito` decimal(18,2) DEFAULT 0.00,
  `saldo_inicial` decimal(18,2) DEFAULT 0.00,
  `saldo_actual` decimal(18,2) DEFAULT 0.00,
  `naturaleza` varchar(20) DEFAULT NULL,
  `level` int(11) DEFAULT 1,
  `report_is` varchar(100) DEFAULT NULL,
  `report_bs` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `teso_accounts`
--

INSERT INTO `teso_accounts` (`id`, `code`, `name`, `type`, `bank_name`, `account_number`, `currency`, `currency_symbol`, `estado`, `created_at`, `fecha_apertura`, `clabe`, `sig_cheque`, `dia_corte`, `ultimo_dia_mes`, `formato`, `cuenta_contable`, `nombre_banco`, `clave_banco`, `sucursal`, `funcionario`, `telefono`, `plaza`, `logo_banco`, `rfc`, `banco_extranjero`, `saldo_conciliado`, `total_cargos`, `total_abonos`, `cargos_transito`, `abonos_transito`, `montos_transito`, `saldos_sin_transito`, `saldo_inicial`, `saldo_actual`, `naturaleza`, `level`, `report_is`, `report_bs`) VALUES
(1, '0001', 'Lafise Dolares', 'banco', 'Lafise', '106202630', 'USD', '$', 1, '2026-03-04 09:01:03', '2026-06-02', NULL, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '0.00', '0.00', '124.00', '0.00', '0.00', '0.00', '0.00', '0.00', '124.00', NULL, 1, NULL, NULL),
(2, '0002', 'Nombre', 'caja', 'BAC', 'Caja', '', 'C$', 1, '2026-05-05 09:21:30', NULL, NULL, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, 1, NULL, NULL),
(3, '0003', 'Laf Cord', 'banco', 'Lafise', '0002', 'NIO', '$', 1, '2026-06-09 11:06:51', '2026-06-09', '123', 0, 8, 1, 'Form', 'Cont', 'Cuenta', '321', 'Este', 'Sí', '77887788', NULL, NULL, NULL, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `teso_cajas`
--

CREATE TABLE `teso_cajas` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `responsable` int(11) DEFAULT NULL,
  `moneda` varchar(10) NOT NULL DEFAULT 'PEN',
  `saldo_inicial` decimal(18,2) NOT NULL DEFAULT 0.00,
  `saldo_actual` decimal(18,2) NOT NULL DEFAULT 0.00,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `teso_cuentas`
--

CREATE TABLE `teso_cuentas` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('banco','caja','otros') NOT NULL DEFAULT 'banco',
  `moneda` varchar(10) NOT NULL DEFAULT 'PEN',
  `saldo_inicial` decimal(18,2) NOT NULL DEFAULT 0.00,
  `saldo_actual` decimal(18,2) NOT NULL DEFAULT 0.00,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `teso_flujo`
--

CREATE TABLE `teso_flujo` (
  `id` bigint(20) NOT NULL,
  `fecha` date NOT NULL,
  `cuenta_id` int(11) NOT NULL,
  `concepto` varchar(200) NOT NULL,
  `tipo` enum('ingreso','egreso') NOT NULL,
  `proyectado` decimal(18,2) NOT NULL DEFAULT 0.00,
  `realizado` decimal(18,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `teso_flujo`
--

INSERT INTO `teso_flujo` (`id`, `fecha`, `cuenta_id`, `concepto`, `tipo`, `proyectado`, `realizado`, `created_at`, `updated_at`) VALUES
(1, '2026-05-04', 1, 'Desembolso préstamo #1 (Solicitud 1)', 'egreso', '1488.00', NULL, '2026-05-04 12:44:45', NULL),
(2, '2026-05-04', 1, 'Desembolso préstamo #2 (Solicitud 2)', 'egreso', '930.00', NULL, '2026-05-04 18:06:28', NULL),
(3, '2026-05-08', 1, 'Desembolso préstamo #3 (Solicitud 10)', 'egreso', '744.00', NULL, '2026-05-05 11:15:23', NULL),
(4, '2026-05-08', 1, 'Desembolso préstamo #4 (Solicitud 11)', 'egreso', '930.00', NULL, '2026-05-08 12:49:32', NULL),
(5, '2026-05-08', 1, 'Desembolso préstamo #5 (Solicitud 12)', 'egreso', '1116.00', NULL, '2026-05-08 13:34:32', NULL),
(6, '2026-05-08', 1, 'Desembolso préstamo #6 (Solicitud 13)', 'egreso', '1395.00', NULL, '2026-05-08 15:49:50', NULL),
(7, '2026-05-08', 1, 'Desembolso préstamo #7 (Solicitud 14)', 'egreso', '9500.00', NULL, '2026-05-08 16:37:43', NULL),
(8, '2026-05-08', 1, 'Desembolso préstamo #8 (Solicitud 15)', 'egreso', '1116.00', NULL, '2026-05-08 18:00:41', NULL),
(9, '2026-05-15', 1, 'Desembolso préstamo #1 (Solicitud 1)', 'egreso', '1395.00', NULL, '2026-05-13 10:23:11', NULL),
(10, '2026-05-13', 1, 'Aplicación pago provisional #1 préstamo 1 cuota 1', 'ingreso', '0.00', '123.90', '2026-05-13 12:01:28', NULL),
(11, '2026-05-13', 1, 'Aplicación pago provisional #2 préstamo 1 cuota 2', 'ingreso', '0.00', '123.90', '2026-05-13 12:46:17', NULL),
(12, '2026-05-13', 1, 'Aplicación pago provisional #1 préstamo 1 cuota 1', 'ingreso', '0.00', '123.90', '2026-05-13 13:29:23', NULL),
(13, '2026-05-13', 1, 'Aplicación pago provisional #1 préstamo 1 cuota 1', 'ingreso', '0.00', '123.90', '2026-05-13 13:42:42', NULL),
(14, '2026-05-13', 1, 'Aplicación pago provisional #1 préstamo 1 cuota 1', 'ingreso', '0.00', '123.90', '2026-05-13 13:46:54', NULL),
(15, '2026-05-13', 1, 'Aplicación pago provisional #2 préstamo 1 cuota 2', 'ingreso', '0.00', '123.90', '2026-05-13 15:05:03', NULL),
(16, '2026-06-09', 1, 'Desembolso préstamo #2 (Solicitud 2)', 'egreso', '930.00', NULL, '2026-06-08 10:36:24', NULL),
(17, '2026-06-15', 1, 'Desembolso préstamo #3 (Solicitud 3)', 'egreso', '1860.00', NULL, '2026-06-08 10:59:18', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `teso_movimientos`
--

CREATE TABLE `teso_movimientos` (
  `id` int(11) NOT NULL,
  `tipo_movimiento` enum('transferencia','efectivo','cheque','traslado','otros') NOT NULL,
  `concepto` varchar(255) DEFAULT NULL,
  `forma_pago` varchar(50) DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `fecha_aplicacion` date DEFAULT NULL,
  `primer_dia_pago` datetime DEFAULT NULL,
  `beneficiario` varchar(255) DEFAULT NULL,
  `referencia1` varchar(100) DEFAULT NULL,
  `referencia2` varchar(100) DEFAULT NULL,
  `monto_total` decimal(18,2) DEFAULT 0.00,
  `iva_total` decimal(18,2) DEFAULT 0.00,
  `departamento` varchar(100) DEFAULT NULL,
  `centro_costos` varchar(100) DEFAULT NULL,
  `proyecto` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `cuenta_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `cuenta_destino` int(11) DEFAULT NULL,
  `tipo_transferencia` varchar(50) DEFAULT NULL,
  `numero_cheque` varchar(50) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'activo',
  `motivo_anulacion` text DEFAULT NULL,
  `fecha_anulacion` datetime DEFAULT NULL,
  `contabilizado` tinyint(1) NOT NULL DEFAULT 0,
  `tipo` varchar(50) DEFAULT NULL,
  `creado_por` varchar(255) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `teso_movimientos`
--

INSERT INTO `teso_movimientos` (`id`, `tipo_movimiento`, `concepto`, `forma_pago`, `fecha_registro`, `fecha_aplicacion`, `primer_dia_pago`, `beneficiario`, `referencia1`, `referencia2`, `monto_total`, `iva_total`, `departamento`, `centro_costos`, `proyecto`, `descripcion`, `cuenta_id`, `created_at`, `cuenta_destino`, `tipo_transferencia`, `numero_cheque`, `estado`, `motivo_anulacion`, `fecha_anulacion`, `contabilizado`, `tipo`, `creado_por`, `fecha_creacion`) VALUES
(1, 'cheque', 'Desembolso Plan #1 - DAYANA TÁMARA BLANDON FLORES', 'CHEQUE', '2026-05-08', '2026-05-04', NULL, 'DAYANA TÁMARA BLANDON FLORES', 'PLAN#1', '', '1470.00', '0.00', NULL, NULL, NULL, 'Desembolso con costos: Legales=1, Seguros=1, Comisiones=1', 1, '2026-05-08 12:07:07', NULL, 'cargo', NULL, '1', NULL, NULL, 0, NULL, '15', '2026-05-08 13:07:07'),
(2, 'cheque', 'Cliente: Rebeca Del Socorro Castillo González | Monto crédito: 1200.00 | Tasa: 6.00% | Comisión: 7.00% | Plazo: 12', 'CHEQUE', '2026-05-08', '2026-05-08', NULL, 'Rebeca Del Socorro Castillo González', '', '', '1200.00', '0.00', NULL, NULL, NULL, 'Inicio crédito: 2026-05-08 | Monto cuota: 150.13 | Costos: Legales=0.00, Seguros=0.00, Comisiones=0.00 | Renovación: Principal=0.00, Interés Corriente=0.00, Interés Mora=0.00', 1, '2026-05-08 12:35:43', NULL, 'cargo', '1', '1', NULL, NULL, 0, NULL, '15', '2026-05-08 13:35:43'),
(3, 'cheque', 'Cliente: BISMARK FRANCISCO RIVAS ROSTRAN | Monto crédito: 1500.00 | Tasa: 6.00% | Comisión: 7.00% | Plazo: 12', 'CHEQUE', '2026-05-08', '2026-05-08', NULL, 'BISMARK FRANCISCO RIVAS ROSTRAN', '', 'p=6&fd=2026-05-08&pp=2026-06-07&cl=0.00&sg=0.00&cm=0.00', '1500.00', '0.00', NULL, NULL, NULL, 'Inicio crédito: 2026-05-08 | Monto de cuota: 187.67', 1, '2026-05-08 15:25:10', NULL, 'cargo', '2', 'activo', NULL, NULL, 0, 'desembolso_preview', '15', '2026-05-08 16:25:10'),
(4, 'cheque', 'Cliente: Eduardo Antonio Ferrufino | Monto crédito: 10000.00 | Tasa: 5.00% | Comisión: 5.00% | Plazo: 36', 'CHEQUE', '2026-05-08', '2026-05-08', NULL, 'Eduardo Antonio Ferrufino', 'Incluye: costos legales, seguros, comisiones', 'p=7&fd=2026-05-08&pp=2026-06-07&cl=100.00&sg=200.00&cm=50.00', '9650.00', '0.00', NULL, NULL, NULL, 'Inicio crédito: 2026-05-08 | Monto de cuota: 618.23', 1, '2026-05-08 15:39:47', NULL, 'cargo', '3', 'anulado', 'Anulacion por mala ejecucion', '2026-05-08 16:43:19', 0, 'desembolso_preview', '15', '2026-05-08 16:39:47'),
(5, 'cheque', 'Cliente: Eduardo Antonio Ferrufino | Monto crédito: 10000.00 | Tasa: 5.00% | Comisión: 5.00% | Plazo: 36', 'CHEQUE', '2026-05-08', '2026-05-08', NULL, 'Eduardo Antonio Ferrufino', '', 'p=7&fd=2026-05-08&pp=2026-06-07&cl=0.00&sg=0.00&cm=0.00', '8717.00', '0.00', NULL, NULL, NULL, 'Inicio crédito: 2026-05-08 | Monto de cuota: 618.23', 1, '2026-05-08 15:44:05', NULL, 'cargo', '4', 'activo', NULL, NULL, 0, 'desembolso_preview', '15', '2026-05-08 16:44:05'),
(6, 'cheque', 'Cliente: BELLY ALEXANDRA MEJIA OBANDO | Monto crédito: 1200.00 | Tasa: 6.00% | Comisión: 7.00% | Plazo: 10', 'CHEQUE', '2026-05-08', '2026-05-08', NULL, 'BELLY ALEXANDRA MEJIA OBANDO', '', 'p=8&fd=2026-05-08&pp=2026-06-07&cl=0.00&sg=0.00&cm=0.00&rn=0.00&rp=0.00&rc=0.00&rm=0.00', '1200.00', '0.00', NULL, NULL, NULL, 'Inicio crédito: 2026-05-08 | Monto de cuota: 171.44', 1, '2026-05-08 17:03:42', NULL, 'cargo', '5', 'anulado', 'Mal registrado', '2026-05-08 18:05:05', 0, 'desembolso_preview', '15', '2026-05-08 18:03:42'),
(7, 'cheque', 'Cliente: BELLY ALEXANDRA MEJIA OBANDO | Monto crédito: 1200.00 | Tasa: 6.00% | Comisión: 7.00% | Plazo: 10', 'CHEQUE', '2026-05-08', '2026-05-08', NULL, 'BELLY ALEXANDRA MEJIA OBANDO', 'Incluye: costos legales', 'p=8&fd=2026-05-08&pp=2026-06-07&cl=200.00&sg=0.00&cm=0.00&rn=0.00&rp=0.00&rc=0.00&rm=0.00', '1000.00', '0.00', NULL, NULL, NULL, 'Inicio crédito: 2026-05-08 | Monto de cuota: 171.44', 1, '2026-05-08 17:05:42', NULL, 'cargo', '6', 'activo', NULL, NULL, 0, 'desembolso_preview', '15', '2026-05-08 18:05:42'),
(8, 'transferencia', 'Deposito de arqueo cierre #3', 'DEPOSITO', '2026-05-13', '2026-05-13', NULL, 'TESORERIA', '09090', 'cierre_id=3', '124.00', '0.00', NULL, NULL, NULL, 'Deposito de arqueo - cierre de caja #3', 1, '2026-05-13 14:19:16', NULL, 'abono', NULL, 'activo', NULL, NULL, 0, NULL, 'user_15', NULL),
(9, 'transferencia', NULL, 'TRANSFERENCIA', '2026-06-09', '2026-06-09', NULL, 'Empresa', 'Ref1', 'Ref2', '1500.00', '20.00', '', '', '', 'Desc', 3, '2026-06-09 12:46:35', NULL, 'cargo', NULL, 'activo', NULL, NULL, 0, NULL, NULL, NULL),
(10, 'transferencia', NULL, 'EFECTIVO', '2026-06-09', '2026-06-09', NULL, 'Empresa2', 'R1', 'R2', '500.00', '5.00', '', '', '', 'Desc2', 2, '2026-06-09 12:51:42', NULL, 'abono', NULL, 'activo', NULL, NULL, 0, NULL, NULL, NULL),
(11, 'cheque', 'Concepto de cheque primero', 'CHEQUE', '2026-06-09', '2026-06-09', NULL, 'PERSONAS', 'red', 'redd', '350.00', '0.00', '', '', '', 'Desc', 3, '2026-06-09 12:53:02', NULL, 'cargo', '1', 'activo', NULL, NULL, 0, NULL, NULL, NULL),
(12, 'transferencia', NULL, 'TRANSFERENCIA', '2026-06-09', '2026-06-09', NULL, 'Empresa3', 'Ref3', 'Ref4', '5000.00', '21.00', '', '', '', 'DeSC', 1, '2026-06-09 13:00:02', NULL, 'abono', NULL, 'activo', NULL, NULL, 0, NULL, NULL, NULL),
(13, 'cheque', 'Cheque segundo', 'CHEQUE', '2026-06-10', '2026-06-09', NULL, 'Persona con Apellido', 'wqeq', 'qweq', '7000.00', '0.00', '', '', '', 'Una descrip.', 3, '2026-06-09 14:51:17', NULL, 'cargo', '2', 'activo', NULL, NULL, 1, NULL, NULL, NULL),
(14, 'transferencia', NULL, 'EFECTIVO', '2026-06-09', '2026-06-09', NULL, 'Empresa3', 'Efe', 'Effe', '2500.00', '20.00', '', '', '', 'Efect', 2, '2026-06-09 14:56:53', NULL, 'abono', NULL, 'activo', NULL, NULL, 0, NULL, NULL, NULL),
(15, 'cheque', 'Cheque de gran número', 'CHEQUE', '2026-06-09', '2026-06-09', NULL, 'Una persona que lo merece', 'Ref1', 'Ref2', '15000.50', '15.00', '', '', '', 'Un cheque extenso', 3, '2026-06-09 15:04:20', NULL, 'cargo', '3', 'activo', NULL, NULL, 0, NULL, NULL, NULL),
(16, 'transferencia', NULL, 'TRANSFERENCIA', '2026-06-09', '2026-06-09', NULL, 'Administración', 'Referencia primera', 'Referencia segunda', '1900.00', '15.00', '', '', '', 'Describiendo', 3, '2026-06-09 15:19:27', NULL, 'abono', NULL, 'activo', NULL, NULL, 0, NULL, 'user_15', NULL),
(17, 'transferencia', NULL, 'TRANSFERENCIA', '2026-06-09', '2026-06-09', NULL, 'YOLANDA BUSTAMANTE HERNÁNDEZ DEL SOCORRO MARTÍNEZ', 'asdf', 'fdas', '6700.00', '25.00', '', '', '', 'Sí', 3, '2026-06-09 15:53:10', NULL, 'cargo', NULL, 'activo', NULL, NULL, 0, NULL, 'user_15', '2026-06-09 15:53:10'),
(18, 'transferencia', NULL, 'TRANSFERENCIA', '2026-06-09', '2026-06-09', NULL, 'Empresa', 'fafas', 'dsafa', '123321.00', '0.00', '', '', '', 'asdfghjkl', 1, '2026-06-09 15:54:03', NULL, 'abono', NULL, 'activo', NULL, NULL, 0, NULL, 'user_15', '2026-06-09 15:54:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `teso_pagos`
--

CREATE TABLE `teso_pagos` (
  `id` bigint(20) NOT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `cuenta_id` int(11) DEFAULT NULL,
  `monto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `fecha` date DEFAULT NULL,
  `fecha_programada` date DEFAULT NULL,
  `estado` varchar(50) NOT NULL DEFAULT 'programado',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `monto_recibido` decimal(18,2) DEFAULT NULL,
  `fecha_recepcion` date DEFAULT NULL,
  `serie_codigo` varchar(20) DEFAULT NULL,
  `recepcion_validada` tinyint(1) NOT NULL DEFAULT 0,
  `recepcion_guardada_at` datetime DEFAULT NULL,
  `idprestamo` int(11) DEFAULT NULL,
  `idcuota` int(11) DEFAULT NULL,
  `idcliente` int(11) DEFAULT NULL,
  `idcierre_caja` int(11) DEFAULT NULL,
  `beneficiario` varchar(255) DEFAULT NULL,
  `concepto` varchar(255) DEFAULT NULL,
  `medio_pago` varchar(50) DEFAULT NULL,
  `documento_numero` varchar(100) DEFAULT NULL,
  `moneda` varchar(10) DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `dato_adicional` text DEFAULT NULL,
  `idserie` int(11) DEFAULT NULL,
  `documento_tipo` varchar(50) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `fecha_aprobacion` datetime DEFAULT NULL,
  `aprobado_por` int(11) DEFAULT NULL,
  `motivo_rechazo` varchar(255) DEFAULT NULL,
  `tc_compra` decimal(10,4) DEFAULT NULL,
  `tc_venta` decimal(10,4) DEFAULT NULL,
  `tc_aplicada` decimal(10,4) DEFAULT NULL,
  `monto_usd_aplicado` decimal(18,2) DEFAULT NULL,
  `monto_usd` decimal(18,2) DEFAULT NULL,
  `monto_nio` decimal(18,2) DEFAULT NULL,
  `monto_total_usd` decimal(18,2) DEFAULT NULL,
  `recibo_revisado` tinyint(1) NOT NULL DEFAULT 0,
  `recibo_revisado_por` int(11) DEFAULT NULL,
  `recibo_revisado_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `teso_pagos`
--

INSERT INTO `teso_pagos` (`id`, `proveedor_id`, `cuenta_id`, `monto`, `fecha`, `fecha_programada`, `estado`, `created_at`, `monto_recibido`, `fecha_recepcion`, `serie_codigo`, `recepcion_validada`, `recepcion_guardada_at`, `idprestamo`, `idcuota`, `idcliente`, `idcierre_caja`, `beneficiario`, `concepto`, `medio_pago`, `documento_numero`, `moneda`, `idusuario`, `dato_adicional`, `idserie`, `documento_tipo`, `usuario_id`, `updated_at`, `fecha_aprobacion`, `aprobado_por`, `motivo_rechazo`, `tc_compra`, `tc_venta`, `tc_aplicada`, `monto_usd_aplicado`, `monto_usd`, `monto_nio`, `monto_total_usd`, `recibo_revisado`, `recibo_revisado_por`, `recibo_revisado_at`) VALUES
(1, NULL, 1, '123.90', '2026-05-13', '2026-05-13', 'aplicado_pendiente_arqueo', '2026-05-13 13:46:34', '123.90', '2026-05-13', 'A', 1, NULL, 1, 1, 16, 3, 'CARRILLO ERICK RAMIREZ', 'Pago provisional préstamo #1 cuota #1', 'efectivo', 'A0000000004', 'USD', NULL, 'Serie A PRuebe', 1, 'RECIBO', 15, '2026-05-13 13:46:54', '2026-05-13 13:46:54', 15, NULL, '36.6200', '37.0000', NULL, '123.90', NULL, NULL, NULL, 0, NULL, NULL),
(2, NULL, 1, '123.90', '2026-05-13', '2026-05-13', 'aplicado_pendiente_arqueo', '2026-05-13 14:42:45', '123.90', '2026-05-13', 'A', 1, NULL, 1, 2, 16, 4, 'CARRILLO ERICK RAMIREZ', 'Pago provisional préstamo #1 cuota #2', 'efectivo', 'A0000000005', 'USD', NULL, '', 1, 'RECIBO', 15, '2026-05-13 15:05:03', '2026-05-13 15:05:03', 15, NULL, '36.6200', '37.0000', NULL, '123.90', NULL, NULL, NULL, 0, NULL, NULL),
(3, NULL, 1, '123.90', '2026-05-13', '2026-05-13', 'registrado', '2026-05-13 15:17:36', NULL, NULL, 'A', 0, NULL, 1, 3, 16, NULL, 'CARRILLO ERICK RAMIREZ', 'Pago provisional préstamo #1 cuota #3', 'efectivo', 'A0000000006', 'USD', NULL, '', 1, 'RECIBO', 15, NULL, NULL, NULL, NULL, '36.6200', '37.0000', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(4, NULL, 1, '123.90', '2026-05-15', '2026-05-15', 'registrado', '2026-05-15 13:21:04', NULL, NULL, 'A', 0, NULL, 1, 3, 16, NULL, 'CARRILLO ERICK RAMIREZ', 'Pago provisional préstamo #1 cuota #3', 'efectivo', 'A0000000007', 'USD', NULL, 'USD: 123.90 | NIO: 0.00 | TC Venta: 37.0000', 1, 'RECIBO', 15, '2026-05-15 15:07:51', NULL, NULL, NULL, '36.6200', '37.0000', NULL, NULL, '123.90', '0.00', '123.90', 1, 10, '2026-05-15 15:07:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tmp_accounts_import`
--

CREATE TABLE `tmp_accounts_import` (
  `code` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code_muc` varchar(64) DEFAULT NULL,
  `name_muc` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tmp_accounts_import_excel`
--

CREATE TABLE `tmp_accounts_import_excel` (
  `CUENTACREDIBLAMEN` varchar(128) DEFAULT NULL,
  `NOMBRECUENTA` varchar(512) DEFAULT NULL,
  `CUENTAMUC` varchar(128) DEFAULT NULL,
  `NOMBREDECUENTAMUC` varchar(512) DEFAULT NULL,
  `COMENTARIOS` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
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
  `perfil` int(11) DEFAULT NULL,
  `idserie_recibo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `email`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`, `perfil`, `idserie_recibo`) VALUES
(15, '208.96.130.158', 'ADMINISTRADOR', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'administrador@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705526891, 1781532989, 1, 'ADMINISTRADOR', 'ADMINISTRADOR', NULL, NULL, 1, NULL),
(19, '::1', 'erickprueba', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'erickprueba@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767042965, 1767043838, 1, 'erickprueba', 'erickprueba', NULL, NULL, 4, NULL),
(20, '::1', 'Carlos Mayeel Pineda', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'cpineda@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043445, 1774706653, 1, 'Carlos Mayeel Pineda', 'cpineda', NULL, NULL, 4, NULL),
(21, '::1', 'Roman Lainez', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'Rlainez@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043488, 1773413019, 1, 'Roman Lainez', 'Rlainez', NULL, NULL, 4, NULL),
(25, '::1', 'Roman Lainez', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'bmolina@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043488, 1774829521, 1, 'Roman Lainez', 'Rlainez', NULL, NULL, 4, NULL),
(26, '::1', 'Diana', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'ventas@crediblamen.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705526891, 1773957454, 1, 'Diana', 'Diana', NULL, NULL, 1, NULL),
(15, '208.96.130.158', 'ADMINISTRADOR', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'administrador@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705526891, 1781532989, 1, 'ADMINISTRADOR', 'ADMINISTRADOR', NULL, NULL, 1, NULL),
(19, '::1', 'erickprueba', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'erickprueba@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767042965, 1767043838, 1, 'erickprueba', 'erickprueba', NULL, NULL, 4, NULL),
(20, '::1', 'Carlos Mayeel Pineda', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'cpineda@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043445, 1774706653, 1, 'Carlos Mayeel Pineda', 'cpineda', NULL, NULL, 4, NULL),
(21, '::1', 'Roman Lainez', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'Rlainez@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043488, 1773413019, 1, 'Roman Lainez', 'Rlainez', NULL, NULL, 4, NULL),
(25, '::1', 'Roman Lainez', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'bmolina@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043488, 1774829521, 1, 'Roman Lainez', 'Rlainez', NULL, NULL, 4, NULL),
(26, '::1', 'Diana', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'ventas@crediblamen.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705526891, 1773957454, 1, 'Diana', 'Diana', NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_backup`
--

CREATE TABLE `users_backup` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `username` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(254) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `activation_selector` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `activation_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `forgotten_password_selector` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `forgotten_password_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
  `remember_selector` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `remember_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `last_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `company` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `perfil` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users_backup`
--

INSERT INTO `users_backup` (`id`, `ip_address`, `username`, `password`, `email`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`, `perfil`) VALUES
(10, '190.237.61.146', 'admin@admin.com', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'admin@admin.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1688783075, 1725491172, 1, 'Joselito â¸', 'larson', NULL, NULL, 1),
(13, '190.87.165.213', 'Wilmar', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'wilmar@wilmar.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1698294973, 1698295002, 0, 'Wilmar', 'wilmarcito', NULL, NULL, 2),
(14, '208.96.130.158', 'ERICK', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'erick@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705522625, 1754417474, 1, 'ERICK', 'ERICK', NULL, NULL, 3),
(15, '208.96.130.158', 'ADMINISTRADOR', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'administrador@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705526891, 1767371660, 1, 'ADMINISTRADOR', 'ADMINISTRADOR', NULL, NULL, 1),
(16, '152.231.34.211', 'Admin', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'Admin@prestamos.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705783195, 1754417533, 1, 'Admin', 'Admin', NULL, NULL, 1),
(17, '152.231.34.211', 'admin', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'admin1@prestamos.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705783255, 1705783268, 1, 'admin', 'admin', NULL, NULL, 2),
(18, '152.231.35.196', 'erick', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'erick1@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1707498697, 1763650059, 1, 'erick', 'erick', NULL, NULL, 3),
(19, '::1', 'erickprueba', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'erickprueba@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767042965, 1767043838, 1, 'erickprueba', 'erickprueba', NULL, NULL, 4),
(20, '::1', 'Carlos Mayeel Pineda', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'cpineda@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043445, 1767044221, 1, 'Carlos Mayeel Pineda', 'cpineda', NULL, NULL, 4),
(21, '::1', 'Roman Lainez', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'Rlainez@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043488, 1767572418, 1, 'Roman Lainez', 'Rlainez', NULL, NULL, 4),
(10, '190.237.61.146', 'admin@admin.com', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'admin@admin.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1688783075, 1725491172, 1, 'Joselito â¸', 'larson', NULL, NULL, 1),
(13, '190.87.165.213', 'Wilmar', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'wilmar@wilmar.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1698294973, 1698295002, 0, 'Wilmar', 'wilmarcito', NULL, NULL, 2),
(14, '208.96.130.158', 'ERICK', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'erick@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705522625, 1754417474, 1, 'ERICK', 'ERICK', NULL, NULL, 3),
(15, '208.96.130.158', 'ADMINISTRADOR', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'administrador@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705526891, 1767371660, 1, 'ADMINISTRADOR', 'ADMINISTRADOR', NULL, NULL, 1),
(16, '152.231.34.211', 'Admin', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'Admin@prestamos.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705783195, 1754417533, 1, 'Admin', 'Admin', NULL, NULL, 1),
(17, '152.231.34.211', 'admin', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'admin1@prestamos.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705783255, 1705783268, 1, 'admin', 'admin', NULL, NULL, 2),
(18, '152.231.35.196', 'erick', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'erick1@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1707498697, 1763650059, 1, 'erick', 'erick', NULL, NULL, 3),
(19, '::1', 'erickprueba', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'erickprueba@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767042965, 1767043838, 1, 'erickprueba', 'erickprueba', NULL, NULL, 4),
(20, '::1', 'Carlos Mayeel Pineda', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'cpineda@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043445, 1767044221, 1, 'Carlos Mayeel Pineda', 'cpineda', NULL, NULL, 4),
(21, '::1', 'Roman Lainez', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'Rlainez@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043488, 1767572418, 1, 'Roman Lainez', 'Rlainez', NULL, NULL, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_backup_20260104_182005`
--

CREATE TABLE `users_backup_20260104_182005` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `username` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(254) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `activation_selector` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `activation_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `forgotten_password_selector` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `forgotten_password_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
  `remember_selector` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `remember_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `last_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `company` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `perfil` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users_backup_20260104_182005`
--

INSERT INTO `users_backup_20260104_182005` (`id`, `ip_address`, `username`, `password`, `email`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`, `perfil`) VALUES
(10, '190.237.61.146', 'admin@admin.com', '$2y$10$ycVVFxeyaqLgH6l3t9C6QuSujNHLK6LHiaf6HUiYE0JuUMCchpfVC', 'admin@admin.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1688783075, 1725491172, 1, 'Joselito â¸', 'larson', NULL, NULL, 1),
(13, '190.87.165.213', 'Wilmar', '$2y$10$/0Bt1FNddlsZwFLBxLPAk.muDlnkLNWZvF.83tiulmli9r1qJMbAG', 'wilmar@wilmar.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1698294973, 1698295002, 0, 'Wilmar', 'wilmarcito', NULL, NULL, 2),
(14, '208.96.130.158', 'ERICK', '$2y$10$NquvggjmjpTdxdVxm1gwVOQcUpO41jL6ErAM32tVP.EGAsYt7j3pK', 'erick@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705522625, 1754417474, 1, 'ERICK', 'ERICK', NULL, NULL, 3),
(15, '208.96.130.158', 'ADMINISTRADOR', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'administrador@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705526891, 1767371660, 1, 'ADMINISTRADOR', 'ADMINISTRADOR', NULL, NULL, 1),
(16, '152.231.34.211', 'Admin', '$2y$10$P5nqQJA/JLD9uyCQh0fB2uG0AmFoBXfbz0zEQke9SNONIYlS4samm', 'Admin@prestamos.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705783195, 1754417533, 1, 'Admin', 'Admin', NULL, NULL, 1),
(17, '152.231.34.211', 'admin', '$2y$10$aCbqGqMuzU2oLSCoJ7/I7OytQOq4JUEEsxC9LbMn9k9Y35CBFyG9W', 'admin1@prestamos.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705783255, 1705783268, 1, 'admin', 'admin', NULL, NULL, 2),
(18, '152.231.35.196', 'erick', '$2y$10$P0TmofzkxqCD23EVEnNuGeiyhYU0zjkfxxDp0iNcbQTAGDJKzBxr6', 'erick1@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1707498697, 1763650059, 1, 'erick', 'erick', NULL, NULL, 3),
(19, '::1', 'erickprueba', '$2y$10$aRGCi2UZK6vsseZUBP1VweiuEgRN6PqciJ5dMykujM5LrRsQK6T3C', 'erickprueba@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767042965, 1767043838, 1, 'erickprueba', 'erickprueba', NULL, NULL, 4),
(20, '::1', 'Carlos Mayeel Pineda', '$2y$10$jiGUL.5NWXeu2zPz6qrN5OCXDmaZKFCaoZ3BQ/fg8XsRfowVqcxBa', 'cpineda@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043445, 1767044221, 1, 'Carlos Mayeel Pineda', 'cpineda', NULL, NULL, 4),
(21, '::1', 'Roman Lainez', '$2y$10$v0eXtI1/KOlr5d2g1.F9kepj7UpnmY7BcZvkRXzRwM.akxxpQor76', 'Rlainez@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043488, 1767043674, 1, 'Roman Lainez', 'Rlainez', NULL, NULL, 4),
(10, '190.237.61.146', 'admin@admin.com', '$2y$10$ycVVFxeyaqLgH6l3t9C6QuSujNHLK6LHiaf6HUiYE0JuUMCchpfVC', 'admin@admin.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1688783075, 1725491172, 1, 'Joselito â¸', 'larson', NULL, NULL, 1),
(13, '190.87.165.213', 'Wilmar', '$2y$10$/0Bt1FNddlsZwFLBxLPAk.muDlnkLNWZvF.83tiulmli9r1qJMbAG', 'wilmar@wilmar.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1698294973, 1698295002, 0, 'Wilmar', 'wilmarcito', NULL, NULL, 2),
(14, '208.96.130.158', 'ERICK', '$2y$10$NquvggjmjpTdxdVxm1gwVOQcUpO41jL6ErAM32tVP.EGAsYt7j3pK', 'erick@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705522625, 1754417474, 1, 'ERICK', 'ERICK', NULL, NULL, 3),
(15, '208.96.130.158', 'ADMINISTRADOR', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'administrador@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705526891, 1767371660, 1, 'ADMINISTRADOR', 'ADMINISTRADOR', NULL, NULL, 1),
(16, '152.231.34.211', 'Admin', '$2y$10$P5nqQJA/JLD9uyCQh0fB2uG0AmFoBXfbz0zEQke9SNONIYlS4samm', 'Admin@prestamos.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705783195, 1754417533, 1, 'Admin', 'Admin', NULL, NULL, 1),
(17, '152.231.34.211', 'admin', '$2y$10$aCbqGqMuzU2oLSCoJ7/I7OytQOq4JUEEsxC9LbMn9k9Y35CBFyG9W', 'admin1@prestamos.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705783255, 1705783268, 1, 'admin', 'admin', NULL, NULL, 2),
(18, '152.231.35.196', 'erick', '$2y$10$P0TmofzkxqCD23EVEnNuGeiyhYU0zjkfxxDp0iNcbQTAGDJKzBxr6', 'erick1@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1707498697, 1763650059, 1, 'erick', 'erick', NULL, NULL, 3),
(19, '::1', 'erickprueba', '$2y$10$aRGCi2UZK6vsseZUBP1VweiuEgRN6PqciJ5dMykujM5LrRsQK6T3C', 'erickprueba@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767042965, 1767043838, 1, 'erickprueba', 'erickprueba', NULL, NULL, 4),
(20, '::1', 'Carlos Mayeel Pineda', '$2y$10$jiGUL.5NWXeu2zPz6qrN5OCXDmaZKFCaoZ3BQ/fg8XsRfowVqcxBa', 'cpineda@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043445, 1767044221, 1, 'Carlos Mayeel Pineda', 'cpineda', NULL, NULL, 4),
(21, '::1', 'Roman Lainez', '$2y$10$v0eXtI1/KOlr5d2g1.F9kepj7UpnmY7BcZvkRXzRwM.akxxpQor76', 'Rlainez@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043488, 1767043674, 1, 'Roman Lainez', 'Rlainez', NULL, NULL, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_groups`
--

CREATE TABLE `users_groups` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `group_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `users_groups`
--

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
(28, 15, 1),
(32, 19, 4),
(33, 20, 4),
(34, 21, 4),
(0, 26, 1),
(28, 15, 1),
(32, 19, 4),
(33, 20, 4),
(34, 21, 4),
(0, 26, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `analisis_financiero`
--
ALTER TABLE `analisis_financiero`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `import_log`
--
ALTER TABLE `import_log`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `stg_carga_credito`
--
ALTER TABLE `stg_carga_credito`
  ADD KEY `idx_stg_num_prestamo` (`num_prestamo_raw`),
  ADD KEY `idx_stg_cuota_no` (`cuota_no_raw`);

--
-- Indices de la tabla `tb_account`
--
ALTER TABLE `tb_account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indices de la tabla `tb_account_mapping`
--
ALTER TABLE `tb_account_mapping`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mapping_key` (`mapping_key`),
  ADD KEY `debit_idx` (`debit_account_id`),
  ADD KEY `credit_idx` (`credit_account_id`);

--
-- Indices de la tabla `tb_analisis_financiero_asalariado`
--
ALTER TABLE `tb_analisis_financiero_asalariado`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tb_analisis_financiero_comerciante`
--
ALTER TABLE `tb_analisis_financiero_comerciante`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tb_asesores`
--
ALTER TABLE `tb_asesores`
  ADD PRIMARY KEY (`idasesor`);

--
-- Indices de la tabla `tb_bancos`
--
ALTER TABLE `tb_bancos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tb_caja`
--
ALTER TABLE `tb_caja`
  ADD PRIMARY KEY (`idcaja`);

--
-- Indices de la tabla `tb_caja_movimiento`
--
ALTER TABLE `tb_caja_movimiento`
  ADD PRIMARY KEY (`idcm`);

--
-- Indices de la tabla `tb_centro_costo`
--
ALTER TABLE `tb_centro_costo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `tb_cierres_caja`
--
ALTER TABLE `tb_cierres_caja`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `consecutivo` (`consecutivo`);

--
-- Indices de la tabla `tb_cierre_arqueos`
--
ALTER TABLE `tb_cierre_arqueos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cierre_arqueo_idcierre` (`idcierre_caja`);

--
-- Indices de la tabla `tb_cierre_arqueo_detalle`
--
ALTER TABLE `tb_cierre_arqueo_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_arqueo` (`idarqueo`);

--
-- Indices de la tabla `tb_clientes`
--
ALTER TABLE `tb_clientes`
  ADD PRIMARY KEY (`idcliente`),
  ADD KEY `idx_numero_doc` (`numero_doc`(32));

--
-- Indices de la tabla `tb_clientes_rechazados`
--
ALTER TABLE `tb_clientes_rechazados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `numero_doc_idx` (`numero_doc`),
  ADD KEY `rechazado_por_idx` (`rechazado_por`);

--
-- Indices de la tabla `tb_contratos`
--
ALTER TABLE `tb_contratos`
  ADD PRIMARY KEY (`idcontrato`),
  ADD KEY `idprestamo` (`idprestamo`);

--
-- Indices de la tabla `tb_creditos`
--
ALTER TABLE `tb_creditos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tb_credito_detalle`
--
ALTER TABLE `tb_credito_detalle`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tb_departamentos`
--
ALTER TABLE `tb_departamentos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tb_detalle_simulacion`
--
ALTER TABLE `tb_detalle_simulacion`
  ADD PRIMARY KEY (`iddetallesim`);

--
-- Indices de la tabla `tb_distritos`
--
ALTER TABLE `tb_distritos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tb_feriados`
--
ALTER TABLE `tb_feriados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_fecha` (`fecha`);

--
-- Indices de la tabla `tb_garantias`
--
ALTER TABLE `tb_garantias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `solicitud_id` (`solicitud_id`);

--
-- Indices de la tabla `tb_garantias_fotos`
--
ALTER TABLE `tb_garantias_fotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `solicitud_id` (`solicitud_id`),
  ADD KEY `garantia_id` (`garantia_id`);

--
-- Indices de la tabla `tb_garantias_verificaciones`
--
ALTER TABLE `tb_garantias_verificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `garantia_id` (`garantia_id`),
  ADD KEY `solicitud_id` (`solicitud_id`);

--
-- Indices de la tabla `tb_journal`
--
ALTER TABLE `tb_journal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_centro_costo` (`centro_costo_id`);

--
-- Indices de la tabla `tb_journal_entry`
--
ALTER TABLE `tb_journal_entry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_idx` (`journal_id`),
  ADD KEY `account_idx` (`account_id`),
  ADD KEY `idx_centro_costo` (`centro_costo_id`);

--
-- Indices de la tabla `tb_ledger`
--
ALTER TABLE `tb_ledger`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_period` (`account_id`,`period`),
  ADD KEY `account_idx` (`account_id`);

--
-- Indices de la tabla `tb_monedas`
--
ALTER TABLE `tb_monedas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tb_pagos`
--
ALTER TABLE `tb_pagos`
  ADD PRIMARY KEY (`idpago`);

--
-- Indices de la tabla `tb_pagos_detalle`
--
ALTER TABLE `tb_pagos_detalle`
  ADD PRIMARY KEY (`pdid`);

--
-- Indices de la tabla `tb_perfil_integral_cliente`
--
ALTER TABLE `tb_perfil_integral_cliente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `solicitud_id` (`solicitud_id`);

--
-- Indices de la tabla `tb_period_lock`
--
ALTER TABLE `tb_period_lock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `year_month` (`year`,`month`);

--
-- Indices de la tabla `tb_prestamos`
--
ALTER TABLE `tb_prestamos`
  ADD PRIMARY KEY (`idprestamo`);

--
-- Indices de la tabla `tb_prestamo_cuotas`
--
ALTER TABLE `tb_prestamo_cuotas`
  ADD PRIMARY KEY (`idcuota`),
  ADD KEY `idprestamo` (`idprestamo`);

--
-- Indices de la tabla `tb_prestamo_pagos`
--
ALTER TABLE `tb_prestamo_pagos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tb_provincias`
--
ALTER TABLE `tb_provincias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tb_reports`
--
ALTER TABLE `tb_reports`
  ADD PRIMARY KEY (`job_id`);

--
-- Indices de la tabla `tb_series_recibos`
--
ALTER TABLE `tb_series_recibos`
  ADD PRIMARY KEY (`idserie`),
  ADD UNIQUE KEY `uc_codigo` (`codigo`);

--
-- Indices de la tabla `tb_simulacion`
--
ALTER TABLE `tb_simulacion`
  ADD PRIMARY KEY (`idsimulacion`);

--
-- Indices de la tabla `tb_sistema`
--
ALTER TABLE `tb_sistema`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tb_solicitudes`
--
ALTER TABLE `tb_solicitudes`
  ADD PRIMARY KEY (`idsolicitud`),
  ADD KEY `idx_ventas_dias_buenos` (`ventas_dias_buenos`),
  ADD KEY `idx_ventas_dias_malos` (`ventas_dias_malos`),
  ADD KEY `idx_ventas_dias_buenos_mask` (`ventas_dias_buenos_mask`),
  ADD KEY `idx_ventas_dias_malos_mask` (`ventas_dias_malos_mask`),
  ADD KEY `idx_numero_doc` (`numero_doc`),
  ADD KEY `idx_ventas_dias_masks` (`ventas_dias_buenos_mask`,`ventas_dias_malos_mask`),
  ADD KEY `idx_solicitudes_idcliente` (`idcliente`);

--
-- Indices de la tabla `tb_solicitudes_comments`
--
ALTER TABLE `tb_solicitudes_comments`
  ADD PRIMARY KEY (`idcomment`),
  ADD KEY `idsolicitud` (`idsolicitud`);

--
-- Indices de la tabla `tb_solicitudes_notes`
--
ALTER TABLE `tb_solicitudes_notes`
  ADD PRIMARY KEY (`idnote`),
  ADD KEY `idsolicitud` (`idsolicitud`);

--
-- Indices de la tabla `tb_solicitud_aprobaciones`
--
ALTER TABLE `tb_solicitud_aprobaciones`
  ADD PRIMARY KEY (`idaprobacion`),
  ADD KEY `idsolicitud` (`idsolicitud`);

--
-- Indices de la tabla `tb_solicitud_photos`
--
ALTER TABLE `tb_solicitud_photos`
  ADD PRIMARY KEY (`idphoto`);

--
-- Indices de la tabla `tb_solicitud_propuestas`
--
ALTER TABLE `tb_solicitud_propuestas`
  ADD PRIMARY KEY (`idpropuesta`);

--
-- Indices de la tabla `tb_solicitud_referencias`
--
ALTER TABLE `tb_solicitud_referencias`
  ADD PRIMARY KEY (`idreferencia`);

--
-- Indices de la tabla `tb_solicitud_referencias_fotos`
--
ALTER TABLE `tb_solicitud_referencias_fotos`
  ADD PRIMARY KEY (`idfoto`);

--
-- Indices de la tabla `tb_solicitud_uso_credito`
--
ALTER TABLE `tb_solicitud_uso_credito`
  ADD PRIMARY KEY (`iduso`);

--
-- Indices de la tabla `tb_tasa_cambio`
--
ALTER TABLE `tb_tasa_cambio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_fecha` (`fecha`);

--
-- Indices de la tabla `teso_accounts`
--
ALTER TABLE `teso_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_teso_accounts_code` (`code`),
  ADD KEY `idx_teso_accounts_type` (`type`);

--
-- Indices de la tabla `teso_cajas`
--
ALTER TABLE `teso_cajas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `teso_cuentas`
--
ALTER TABLE `teso_cuentas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `idx_teso_cuentas_tipo` (`tipo`),
  ADD KEY `idx_teso_cuentas_activo` (`activo`);

--
-- Indices de la tabla `teso_flujo`
--
ALTER TABLE `teso_flujo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `teso_movimientos`
--
ALTER TABLE `teso_movimientos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `teso_pagos`
--
ALTER TABLE `teso_pagos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `analisis_financiero`
--
ALTER TABLE `analisis_financiero`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `import_log`
--
ALTER TABLE `import_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tb_account`
--
ALTER TABLE `tb_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT de la tabla `tb_analisis_financiero_asalariado`
--
ALTER TABLE `tb_analisis_financiero_asalariado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tb_analisis_financiero_comerciante`
--
ALTER TABLE `tb_analisis_financiero_comerciante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tb_centro_costo`
--
ALTER TABLE `tb_centro_costo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tb_cierres_caja`
--
ALTER TABLE `tb_cierres_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tb_cierre_arqueos`
--
ALTER TABLE `tb_cierre_arqueos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tb_cierre_arqueo_detalle`
--
ALTER TABLE `tb_cierre_arqueo_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tb_clientes`
--
ALTER TABLE `tb_clientes`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `tb_contratos`
--
ALTER TABLE `tb_contratos`
  MODIFY `idcontrato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_garantias`
--
ALTER TABLE `tb_garantias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `tb_garantias_fotos`
--
ALTER TABLE `tb_garantias_fotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `tb_garantias_verificaciones`
--
ALTER TABLE `tb_garantias_verificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `tb_journal`
--
ALTER TABLE `tb_journal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `tb_journal_entry`
--
ALTER TABLE `tb_journal_entry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT de la tabla `tb_ledger`
--
ALTER TABLE `tb_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT de la tabla `tb_perfil_integral_cliente`
--
ALTER TABLE `tb_perfil_integral_cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tb_period_lock`
--
ALTER TABLE `tb_period_lock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tb_prestamos`
--
ALTER TABLE `tb_prestamos`
  MODIFY `idprestamo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_prestamo_cuotas`
--
ALTER TABLE `tb_prestamo_cuotas`
  MODIFY `idcuota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `tb_prestamo_pagos`
--
ALTER TABLE `tb_prestamo_pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tb_series_recibos`
--
ALTER TABLE `tb_series_recibos`
  MODIFY `idserie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tb_solicitudes`
--
ALTER TABLE `tb_solicitudes`
  MODIFY `idsolicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `tb_solicitudes_comments`
--
ALTER TABLE `tb_solicitudes_comments`
  MODIFY `idcomment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_aprobaciones`
--
ALTER TABLE `tb_solicitud_aprobaciones`
  MODIFY `idaprobacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_photos`
--
ALTER TABLE `tb_solicitud_photos`
  MODIFY `idphoto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_propuestas`
--
ALTER TABLE `tb_solicitud_propuestas`
  MODIFY `idpropuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_referencias`
--
ALTER TABLE `tb_solicitud_referencias`
  MODIFY `idreferencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_referencias_fotos`
--
ALTER TABLE `tb_solicitud_referencias_fotos`
  MODIFY `idfoto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_uso_credito`
--
ALTER TABLE `tb_solicitud_uso_credito`
  MODIFY `iduso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tb_tasa_cambio`
--
ALTER TABLE `tb_tasa_cambio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `teso_accounts`
--
ALTER TABLE `teso_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `teso_cajas`
--
ALTER TABLE `teso_cajas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `teso_cuentas`
--
ALTER TABLE `teso_cuentas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `teso_flujo`
--
ALTER TABLE `teso_flujo`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `teso_movimientos`
--
ALTER TABLE `teso_movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `teso_pagos`
--
ALTER TABLE `teso_pagos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
