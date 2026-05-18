SET FOREIGN_KEY_CHECKS=0;\r\n-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 20, 2026 at 08:10 PM
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
-- Database: `u987557742_servicont1`
--

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;\n\nCREATE TABLE `groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `groups`
--

INSERT IGNORE INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Super Administrator'),
(2, 'admin', 'Administrador'),
(3, 'Asesor', 'Asesor de CrÃ©tidos'),
(4, 'promotor', 'Promotor de Ventas'),
(5, 'Promotor', 'Promotor - Acceso a procesos comerciales');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;\n\nCREATE TABLE `login_attempts` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_account`
--

DROP TABLE IF EXISTS `tb_account`;\n\nCREATE TABLE `tb_account` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `naturaleza` enum('deudora','acreedora') DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_account`
--

INSERT IGNORE INTO `tb_account` (`id`, `code`, `name`, `type`, `naturaleza`, `parent_id`, `created_at`) VALUES
(1, '1', 'ACTIVOS', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(2, '11', 'ACTIVO CIRCULANTE', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(3, '1101', 'Caja', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(4, '1102', 'Bancos', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(5, '1103', 'Inversiones Temporales', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(6, '1104', 'Clientes', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(7, '1105', 'Documentos por Cobrar', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(8, '1106', 'Deudores Diversos', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(9, '1107', 'IVA Acreditable', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(10, '1108', 'Almac?n', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(11, '1109', 'Anticipo a Proveedores', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(12, '12', 'ACTIVO FIJO', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(13, '1201', 'Terrenos', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(14, '1202', 'Edificios', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(15, '1203', 'Mobiliario y Equipo', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(16, '1204', 'Equipo de Transporte', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(17, '1205', 'Equipo de Computo', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(18, '1206', 'Maquinaria y Equipo', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(19, '1207', 'Depreciacion Acumulada', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(20, '13', 'ACTIVO DIFERIDO', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(21, '1301', 'Gastos de Instalacion', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(22, '1302', 'Gastos de Organizacion', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(23, '1303', 'Papeleria y Utiles', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(24, '1304', 'Publicidad y Propaganda', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(25, '1305', 'Rentas Pagadas por Anticipado', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(26, '1306', 'Seguros Pagados por Anticipado', 'activo', 'deudora', NULL, '2026-01-13 16:24:37'),
(27, '2', 'PASIVOS', 'pasivo', 'acreedora', NULL, '2026-01-13 16:24:37'),
(28, '21', 'PASIVO A CORTO PLAZO', 'pasivo', 'acreedora', NULL, '2026-01-13 16:24:37'),
(29, '2101', 'Proveedores', 'pasivo', 'acreedora', NULL, '2026-01-13 16:24:37'),
(30, '2102', 'Documentos por Pagar', 'pasivo', 'acreedora', NULL, '2026-01-13 16:24:37'),
(31, '2103', 'Acreedores Diversos', 'pasivo', 'acreedora', NULL, '2026-01-13 16:24:37'),
(32, '2104', 'IVA por Pagar', 'pasivo', 'acreedora', NULL, '2026-01-13 16:24:37'),
(33, '2105', 'Impuestos por Pagar', 'pasivo', 'acreedora', NULL, '2026-01-13 16:24:37'),
(34, '2106', 'Anticipos de Clientes', 'pasivo', 'acreedora', NULL, '2026-01-13 16:24:37'),
(35, '2107', 'Prestamos Bancarios a Corto Plazo', 'pasivo', 'acreedora', NULL, '2026-01-13 16:24:37'),
(36, '22', 'PASIVO A LARGO PLAZO', 'pasivo', 'acreedora', NULL, '2026-01-13 16:24:37'),
(37, '2201', 'Hipotecas por Pagar', 'pasivo', 'acreedora', NULL, '2026-01-13 16:24:37'),
(38, '2202', 'Documentos por Pagar a Largo Plazo', 'pasivo', 'acreedora', NULL, '2026-01-13 16:24:37'),
(39, '2203', 'Prestamos Bancarios a Largo Plazo', 'pasivo', 'acreedora', NULL, '2026-01-13 16:24:37'),
(40, '2204', 'Creditos Diferidos', 'pasivo', 'acreedora', NULL, '2026-01-13 16:24:37'),
(41, '3', 'CAPITAL CONTABLE', 'patrimonio', 'acreedora', NULL, '2026-01-13 16:24:37'),
(42, '3101', 'Capital Social', 'patrimonio', 'acreedora', NULL, '2026-01-13 16:24:37'),
(43, '3102', 'Aportaciones Adicionales', 'patrimonio', 'acreedora', NULL, '2026-01-13 16:24:37'),
(44, '3103', 'Reserva Legal', 'patrimonio', 'acreedora', NULL, '2026-01-13 16:24:37'),
(45, '3104', 'Utilidades Retenidas', 'patrimonio', 'acreedora', NULL, '2026-01-13 16:24:37'),
(46, '3105', 'Utilidad del Ejercicio', 'patrimonio', 'acreedora', NULL, '2026-01-13 16:24:37'),
(47, '4', 'INGRESOS', 'ingreso', 'acreedora', NULL, '2026-01-13 16:24:37'),
(48, '4101', 'Ventas', 'ingreso', 'acreedora', NULL, '2026-01-13 16:24:37'),
(49, '4102', 'Productos Financieros', 'ingreso', 'acreedora', NULL, '2026-01-13 16:24:37'),
(50, '4103', 'Otros Ingresos', 'ingreso', 'acreedora', NULL, '2026-01-13 16:24:37'),
(51, '4104', 'Devoluciones sobre Ventas', 'ingreso', 'acreedora', NULL, '2026-01-13 16:24:37'),
(52, '4105', 'Descuentos sobre Ventas', 'ingreso', 'acreedora', NULL, '2026-01-13 16:24:37'),
(53, '5', 'COSTOS', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37'),
(54, '5101', 'Costo de Ventas', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37'),
(55, '5102', 'Compras', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37'),
(56, '5103', 'Gastos sobre Compras', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37'),
(57, '5104', 'Devoluciones sobre Compras', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37'),
(58, '5105', 'Descuentos sobre Compras', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37'),
(59, '6', 'GASTOS', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37'),
(60, '6101', 'Gastos de Administraci?n', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37'),
(61, '6102', 'Gastos de Venta', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37'),
(62, '6103', 'Gastos Financieros', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37'),
(63, '6104', 'Gastos Por Sueldos, Salarios Y Compesaciones', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37'),
(64, '6105', 'Servicios Profesionales, Tecnicos Y Otros Oficios', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37'),
(65, '6106', 'Gasto Por Depreciacion', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37'),
(66, '6107', 'Depreciacion', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37'),
(67, '6108', 'Amortizacion', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37'),
(68, '6109', 'Mantenimiento', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37'),
(69, '6110', 'Combustibles', 'gasto', 'deudora', NULL, '2026-01-13 16:24:37');

-- --------------------------------------------------------

--
-- Table structure for table `tb_account_mapping`
--

DROP TABLE IF EXISTS `tb_account_mapping`;\n\nCREATE TABLE `tb_account_mapping` (
  `id` int(11) NOT NULL,
  `mapping_key` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `debit_account_id` int(11) NOT NULL,
  `credit_account_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_account_mapping`
--

INSERT IGNORE INTO `tb_account_mapping` (`id`, `mapping_key`, `description`, `debit_account_id`, `credit_account_id`, `created_at`) VALUES
(4, 'loan_disbursement', 'Desembolso de crÃ©dito', 2, 1, '2025-12-02 12:02:28'),
(5, 'loan_payment_principal', 'Pago principal de crÃ©dito', 1, 2, '2025-12-02 12:02:28'),
(6, 'loan_payment_interest', 'Pago interÃ©s de crÃ©dito', 1, 5, '2025-12-02 12:02:28');

-- --------------------------------------------------------

--
-- Table structure for table `tb_asesores`
--

DROP TABLE IF EXISTS `tb_asesores`;\n\nCREATE TABLE `tb_asesores` (
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

INSERT IGNORE INTO `tb_asesores` (`idasesor`, `nombres`, `telefono`, `direccion`, `fechaRegistro`, `estado`) VALUES
(1, 'Ruta Prueba', '5555 5555', 'Managua', '2024-02-06 12:07:42', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_bancos`
--

DROP TABLE IF EXISTS `tb_bancos`;\n\nCREATE TABLE `tb_bancos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) DEFAULT NULL,
  `estado` int(11) DEFAULT 1,
  `tb_bancoscol` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_caja`
--

DROP TABLE IF EXISTS `tb_caja`;\n\nCREATE TABLE `tb_caja` (
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

DROP TABLE IF EXISTS `tb_caja_movimiento`;\n\nCREATE TABLE `tb_caja_movimiento` (
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
-- Table structure for table `tb_centro_costo`
--

DROP TABLE IF EXISTS `tb_centro_costo`;\n\nCREATE TABLE `tb_centro_costo` (
  `id` int(11) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_centro_costo`
--

INSERT IGNORE INTO `tb_centro_costo` (`id`, `codigo`, `nombre`, `descripcion`, `activo`, `created_at`) VALUES
(1, '001', 'Gerencia', 'Centro de costo de Gerencia', 1, '2026-01-12 21:40:03'),
(2, '002', 'Administracion', 'Centro de costo de Administracion', 1, '2026-01-12 21:40:03'),
(3, '003', 'Finanzas', 'Centro de costo de Finanzas', 1, '2026-01-12 21:40:03'),
(4, '004', 'Credito', 'Centro de costo de Credito', 1, '2026-01-12 21:40:03'),
(5, '005', 'Cobranza', 'Centro de costo de Cobranza', 1, '2026-01-12 21:40:03');

-- --------------------------------------------------------

--
-- Table structure for table `tb_clientes`
--

DROP TABLE IF EXISTS `tb_clientes`;\n\nCREATE TABLE `tb_clientes` (
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
  `ventas_promedio_mensual` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_clientes`
--

INSERT IGNORE INTO `tb_clientes` (`idcliente`, `apellidos`, `nombres`, `direccion`, `telefono`, `email`, `tipo_doc`, `numero_doc`, `comentarios`, `estado`, `rechazado`, `fechaActualizacion`, `fecha_nacimiento`, `edad`, `estado_civil`, `nombre_conyuge`, `dni_conyuge`, `ocupacion_conyuge`, `telefono_conyuge`, `numero_dependientes`, `condicion_vivienda`, `tiempo_residir_anios`, `tiempo_residir_meses`, `nombre_empresa`, `direccion_empresa`, `telefono_empresa`, `cargo_puesto`, `tiempo_empleo_anios`, `tiempo_empleo_meses`, `tipo_contrato`, `ingreso_mensual_neto`, `deducciones`, `nombre_negocio`, `actividad_economica`, `telefono_negocio`, `tiempo_operacion_anios`, `tiempo_operacion_meses`, `ventas_buenos_amount`, `ventas_malos_amount`, `ventas_promedio_mensual`) VALUES
(2, 'Carrillo', 'Erick Antonio Ramirez', 'Bo batahola sur detras de sitel 1c arriba 1/2c al al sur', '76534038', NULL, 3, '0012702981004X', NULL, 0, 0, '2025-12-30 15:58:05', '1998-02-27', 27, 'Soltero', NULL, NULL, NULL, '76534038', NULL, 'Propia', 2, 2, 'Ernst &amp; Young', 'Managua', NULL, 'Staff I BI', 1, 1, 'Permanente', 360000.00, '1200', 'Serviconta', 'Servicios Profesionales', NULL, 1, 1, 1200.00, 300.00, 8400.00),
(3, 'VÃ¡squez', 'AlemÃ¡n Denis Ramon', 'Bo.Jonathan Gonzalez. Bancentro 22 c 1/2 al sur', '81112991', NULL, 3, '0010806670057W', NULL, 0, 0, '2026-01-07 15:58:03', '1967-06-08', 58, 'Soltero', NULL, NULL, NULL, NULL, 1, 'Propia', 58, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulperia Algeria', 'Comercio', '81112991', 14, NULL, 6500.00, 4200.00, 154400.00),
(4, 'osorio', 'juan mario vega', 'Bo batahola sur q', '88888888', NULL, 3, '0010101010000X', NULL, 0, 0, '2026-01-07 14:39:29', '2005-02-01', 20, 'Soltero', NULL, NULL, NULL, NULL, NULL, 'Propia', 10, NULL, 'EY', 'Rotonda Centroamerica', '99999999', 'Staff', 2, NULL, 'Permanente', 10000.00, '100', 'Negocio', 'Negocio', '9999999', 2, NULL, 1500.00, 500.00, 30000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tb_clientes_rechazados`
--

DROP TABLE IF EXISTS `tb_clientes_rechazados`;\n\nCREATE TABLE `tb_clientes_rechazados` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_contratos`
--

DROP TABLE IF EXISTS `tb_contratos`;\n\nCREATE TABLE `tb_contratos` (
  `idcontrato` int(11) NOT NULL,
  `idprestamo` int(11) NOT NULL,
  `template_id` int(11) NOT NULL DEFAULT 0,
  `contenido` longtext DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tb_contratos`
--

INSERT IGNORE INTO `tb_contratos` (`idcontrato`, `idprestamo`, `template_id`, `contenido`, `created_by`, `created_at`) VALUES
(1, 1, 4, '<!doctype html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\" />\r\n  <style>\r\n    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#222; }\r\n    .center { text-align:center; }\r\n    .small { font-size:11px; color:#444; }\r\n    .sig { margin-top:40px; }\r\n    .line { border-bottom:1px solid #000; width:240px; display:inline-block; }\r\n  </style>\r\n  <title>CONTRATO PRIVADO DE MUTUO (COMISION AMORTIZADA) SIN FIADOR</title>\r\n</head>\r\n<body>\r\n  <div class=\"center\">\r\n    <h2>CONTRATO PRIVADO DE MUTUO (COMISIÃ“N AMORTIZADA) SIN FIADOR</h2>\r\n    <div class=\"small\">Documento generado por Servicredit</div>\r\n  </div>\r\n  \r\n  <div style=\"background:#111;color:#fff;padding:10px;margin-top:12px;border-radius:4px;\">\r\n    <div style=\"text-align:center;font-weight:700;\">CONTRATO PRIVADO DE MUTUO</div>\r\n    <div style=\"text-align:center;color:#ff6666;margin-top:6px;\">NÂ° Cliente <span style=\"font-weight:700;color:#ff6666\">{{cliente_numero}}</span></div>\r\n  </div>\r\n\r\n  <p><strong>Nosotros:</strong> {{acreedor_fullname}}, mayor de edad, {{acreedor_estado_civil}}, {{acreedor_profesion}}, de este domicilio, identificada con cÃ©dula de identidad {{acreedor_doc}}; quien actÃºa en nombre y representaciÃ³n de la entidad jurÃ­dica <strong></strong>, conocida comercialmente como \"{{empresa_comercial}}\", a quien en lo sucesivo se le denominarÃ¡ <strong>\"EL ACREEDOR\"</strong>.</p>\r\n\r\n  <p><strong>Y:</strong> {{deudor_fullname}}, mayor de edad, {{deudor_estado_civil}}, {{deudor_profesion}}, identificada con cÃ©dula de identidad {{deudor_doc}}, con domicilio en {{deudor_direccion}}, a quien en lo sucesivo se le denominarÃ¡ <strong>\"EL DEUDOR\"</strong>.</p>\r\n\r\n  <p><strong>ANTECEDENTES:</strong></p>\r\n  <p>Con fecha de solicitud: <strong></strong>, el solicitante presentÃ³ la solicitud NÂ° <strong>9</strong> y se aprobÃ³ el prÃ©stamo NÂ° <strong>1</strong>. A continuaciÃ³n se consignan las declaraciones y antecedentes que constan en el expediente.</p>\r\n\r\n  <p><strong>OBJETO:</strong> El Acreedor otorga un prÃ©stamo al Deudor por la suma de <strong>$500.00</strong> dÃ³lares estadounidenses, con plazo de <strong>14</strong> meses y pagos de cuota segÃºn el plan de pagos adjunto.</p>\r\n\r\n  <p><strong>DESTINO DEL CRÃ‰DITO:</strong>  </p>\r\n\r\n  <p><strong>DECLARACIONES DEL DEUDOR:</strong></p>\r\n  <p></p>\r\n\r\n  <p>Las partes manifiestan que la informaciÃ³n contenida en la solicitud inicial y en el formato de Uso de CrÃ©dito fue proporcionada y verificada conforme a los documentos que forman parte del expediente.</p>\r\n\r\n  <h4>CLÃUSULA PRIMERA: OTORGAMIENTO DE CRÃ‰DITO Y DESTINO</h4>\r\n  <p>En este acto EL ACREEDOR otorga el presente crÃ©dito a EL DEUDOR por la Cantidad de <strong>$500.00</strong> ({{monto_credito_letras}}), que segÃºn el tipo de cambio oficial del cordoba con respecto al dÃ³lar autorizado por el Banco Central de Nicaragua para este dÃ­a a TREINTA Y SEIS CÃ“RDOBAS CON 6243/100 por dÃ³lar (USD x $1.00), equivalentes a <strong>{{monto_equivalente_usd}}</strong> (${{monto_credito_usd}}), el cual es destinado a prÃ©stamo para capital de trabajo.</p>\r\n\r\n  <h4>CLÃUSULA SEGUNDA: TASA DE INTERÃ‰S CORRIENTE Y MORATORIA</h4>\r\n  <p>EL DEUDOR reconoce a favor de EL ACREEDOR una Tasa de interÃ©s corriente del <strong>{{tasa_interes_corriente}}</strong>% anual sobre el saldo de principal desde la fecha de desembolso hasta el total de su cancelaciÃ³n y ademÃ¡s reconocerÃ¡ una Tasa de InterÃ©s Moratorio equivalente al <strong>{{tasa_moratoria}}</strong>% anual sobre las sumas adeudadas en mora.</p>\r\n\r\n  <h4>CLÃUSULA TERCERA: COMISIONES, GASTOS Y CARGOS CONEXOS</h4>\r\n  <p>a) ComisiÃ³n por desembolso: EL DEUDOR reconoce que pagarÃ¡ el <strong>{{comision_desembolso}}</strong>% sobre el monto del prÃ©stamo, en concepto de comisiÃ³n por desembolso, la cual serÃ¡ incluida y amortizada en las cuotas acordadas. AdemÃ¡s, el DEUDOR serÃ¡ responsable por los gastos de gestiÃ³n, notariales y administrativos necesarios para la ejecuciÃ³n del desembolso.</p>\r\n\r\n  <h4>CLÃUSULA CUARTA: PERIODO DE VIGENCIA, PLAZO Y MONTO DE LAS CUOTAS</h4>\r\n  <p>Este contrato tendrÃ¡ un plazo de <strong>14</strong> meses contados desde <strong>{{fecha_desembolso}}</strong>, venciendo dicho plazo el dÃ­a <strong>2026-07-09</strong>, salvo que se aplique la clÃ¡usula de vencimiento anticipado por incumplimiento.</p>\r\n\r\n  <h4>CLÃUSULA QUINTA: PLAN DE PAGOS</h4>\r\n  <p>El pago de las cuotas se realizarÃ¡ de acuerdo al plan de pagos adjunto que forma parte integrante de este contrato. La primera cuota vencerÃ¡ el dÃ­a <strong>2025-12-26</strong> y las siguientes conforme a la frecuencia pactada: <strong>{{frecuencia}}</strong>.</p>\r\n\r\n  <h4>CLÃUSULA SEXTA: INCUMPLIMIENTO</h4>\r\n  <p>En caso de incumplimiento en el pago de cualquiera de las cuotas, EL ACREEDOR podrÃ¡ exigir la totalidad del saldo vencido y devengado y aplicar los intereses moratorios establecidos en la ClÃ¡usula Segunda, asÃ­ como iniciar las gestiones de cobranza y acciones legales correspondientes.</p>\r\n\r\n  <h4>DETALLE ADICIONAL DEL PRÃ‰STAMO</h4>\r\n  <p>EL DEUDOR se obliga a pagar a EL ACREEDOR la cantidad de <strong>{{monto_principal}}</strong> ({{monto_principal_letras}}) en concepto de PRINCIPAL, y <strong>{{interes_total}}</strong> en concepto de INTERESES CORRIENTES, mÃ¡s <strong>{{comision_total}}</strong> en concepto de COMISIÃ“N POR DESEMBOLSO, para un total de <strong>{{total_conceptos}}</strong>.</p>\r\n\r\n  <p>El cronograma constarÃ¡ de <strong>14</strong> cuotas, de las cuales <strong>14</strong> serÃ¡n cuotas ordinarias de <strong>$44.26</strong> y una Ãºltima cuota de <strong>$44.26</strong>. Por ejemplo, se acuerda un monto por cuota de <strong>$44.26</strong> para las cuotas corrientes.</p>\r\n\r\n  <p>La primera cuota se realizarÃ¡ el dÃ­a <strong>2025-12-26</strong> y la Ãºltima cuota vencerÃ¡ el dÃ­a <strong>2026-07-09</strong>. En caso de que alguna fecha caiga en dÃ­a inhÃ¡bil, el pago se efectuarÃ¡ el dÃ­a hÃ¡bil siguiente salvo disposiciÃ³n en contrario.</p>\r\n\r\n  <h4>CLÃUSULA SÃ‰PTIMA: LUGAR, FORMA Y MEDIOS DE PAGO</h4>\r\n  <p>EL DEUDOR podrÃ¡ realizar los pagos de las cuotas de la presente obligaciÃ³n a EL ACREEDOR, en las siguientes formas:</p>\r\n  <ol type=\"a\">\r\n    <li>En las oficinas de CREDIBLAMEN;</li>\r\n    <li>Directamente a los gestores de cobros debidamente autorizados e identificados y designados por EL ACREEDOR;</li>\r\n    <li>TambiÃ©n podrÃ¡ realizar depÃ³sitos en las cuentas bancarias habilitadas por CREDIBLAMEN.</li>\r\n  </ol>\r\n\r\n  <h4>CLÃUSULA SEXTA: MANTENIMIENTO DE VALOR Y MONEDA DE REFERENCIA</h4>\r\n  <p>Conforme a lo establecido en el marco regulatorio, todas las variaciones de la Moneda Nacional (Devaluaciones) con respecto a la moneda de referencia serÃ¡n asumidas por EL DEUDOR; por ende, es entendido que el riesgo cambiario ha sido expresamente aceptado y asumido contractualmente por EL DEUDOR. El mantenimiento de valor se calcularÃ¡ sobre el saldo de principal a la fecha de corte neto.</p>\r\n\r\n  <h4>CLÃUSULA SÃ‰PTIMA (IMPUTACIÃ“N DE PAGO):</h4>\r\n  <p>EL DEUDOR reconoce que los pagos que realice se imputarÃ¡n en el siguiente orden: 1) Costos y gastos de recuperaciÃ³n extrajudicial o judicial; 2) Intereses moratorios que pudieran existir; 3) Gastos, costos y cargos conexos que pudieran proceder conforme a lo estipulado en este contrato; 4) Comisiones que pudieren proceder conforme a lo estipulado en este contrato; 5) Intereses corrientes adeudados; y 6) AmortizaciÃ³n al principal.</p>\r\n\r\n  <h4>CLÃUSULA OCTAVA: OBLIGACIONES DEL DEUDOR</h4>\r\n  <p>Al realizar los pagos en tiempo, modo y condiciones convenidas en el presente contrato, el DEUDOR se obliga a: a) No hacer uso diferente del dinero al que se ha estipulado en la clÃ¡usula segundo del presente contrato; b) Suministrar informaciones reales de su situaciÃ³n econÃ³mica y social antes, en el momento y despuÃ©s de otorgado el crÃ©dito; c) Comunicar, por escrito y en forma oportuna, a EL ACREEDOR cualquier cambio de su domicilio; d) Aceptar como vÃ¡lida cualquier notificaciÃ³n judicial o extrajudicial que se haga en la Ãºltima direcciÃ³n de su domicilio; e) Autorizar que EL ACREEDOR, a travÃ©s de sus representantes o funcionarios, supervise por medio de sus actuaciones el cumplimiento de las obligaciones asumidas.</p>\r\n\r\n  <h4>CLÃUSULA NOVENA: DERECHOS DEL ACREEDOR</h4>\r\n  <p>Al recibir, sin discriminaciÃ³n alguna, servicios de calidad y un trato respetuoso, EL ACREEDOR tendrÃ¡, entre otros, los derechos de: a) Exigir el pago oportuno de las cuotas; b) Aplicar intereses moratorios y cargos por mora; c) Ejecutar las garantÃ­as aportadas por el DEUDOR en caso de incumplimiento; d) Solicitar y recibir la informaciÃ³n necesaria para la administraciÃ³n y cobranza del crÃ©dito.</p>\r\n\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA: DERECHOS Y OBLIGACIONES DEL ACREEDOR</h4>\r\n  <p>EL ACREEDOR, ademÃ¡s de los derechos generales establecidos en este contrato y en la ley aplicable, tendrÃ¡ las siguientes facultades y obligaciones, sin perjuicio de otras que la normativa vigente reconozca:</p>\r\n  <ol type=\"a\">\r\n    <li>A ser atendido por EL DEUDOR y a recibir respuesta oportuna, fundamentada, comprensible e integral sobre los mismos cuando aplique.</li>\r\n    <li>A ser atendido en la sucursal de EL ACREEDOR donde suscribiÃ³ el presente contrato para realizar cualquier consulta sobre el mismo.</li>\r\n    <li>A recibir un ejemplar del presente contrato con sus respectivos anexos, incluyendo el Resumen Informativo y el plan de pago suscrito en la presente obligaciÃ³n.</li>\r\n    <li>A ser informado con la debida antelaciÃ³n sobre cualquier modificaciÃ³n que se pretenda introducir en las condiciones contractuales que le afecten; salvo disposiciÃ³n legal distinta, la comunicaciÃ³n se realizarÃ¡ con sesenta (60) dÃ­as calendario de anticipaciÃ³n; cuando las modificaciones se refieran a variaciones de tasas de interÃ©s, comisiones y/o costos, el plazo mÃ­nimo serÃ¡ de treinta (30) dÃ­as calendario.</li>\r\n    <li>A realizar el pago de forma anticipada, total o parcial, sin que por ello se imponga una penalidad que reduzca el derecho del DEUDOR a pagar anticipadamente; en tal caso se deberÃ¡n reducir los intereses generados a la fecha del pago.</li>\r\n    <li>A que EL ACREEDOR realice las gestiones de cobranza estrictamente respetando la tranquilidad familiar y laboral, la honorabilidad e integridad moral del DEUDOR, y a ser notificado en horarios razonables (no antes de las 8:00 ni despuÃ©s de las 19:00, salvo acuerdo expreso distinto).</li>\r\n    <li>Derecho de rescindir el contrato en caso de que EL ACREEDOR no cumpla con el desembolso del monto aprobado en el plazo establecido en la presente operaciÃ³n.</li>\r\n    <li>Los derechos previstos en la Ley No. 842 (Ley de ProtecciÃ³n de los Derechos de las Personas Consumidoras y Usuarias) y en la normativa aplicable, que prevalecerÃ¡n en caso de conflicto con las estipulaciones de este contrato.</li>\r\n  </ol>\r\n\r\n  <p>Adicionalmente, EL ACREEDOR se obliga a cumplir con las siguientes obligaciones y garantÃ­as de trato al DEUDOR:</p>\r\n  <ol type=\"a\">\r\n    <li>Respetar los tÃ©rminos y condiciones del contrato y brindar una respuesta oportuna, fundada, comprensible e integral a las consultas del DEUDOR.</li>\r\n    <li>Informar previamente al DEUDOR sobre las condiciones del crÃ©dito y cualquiera modificaciÃ³n que pudiera afectarle.</li>\r\n    <li>Brindar atenciÃ³n de calidad y facilitar el acceso al lugar de reclamo por parte del DEUDOR, proveyendo facilidades para que pueda formular el mismo y contar con un servicio de atenciÃ³n al usuario.</li>\r\n    <li>No exigir a las personas reclamantes la presentaciÃ³n de documentos o informaciÃ³n que no se encuentren en nuestro poder o que no guarden relaciÃ³n directa con la materia reclamada.</li>\r\n    <li>No exigir al DEUDOR la participaciÃ³n de un abogado para reclamos ordinarios; y no aplicar mÃ©todos o usos de cobro extrajudiciales que afecten el honor o la imagen del DEUDOR, ni que resulten intimidatorios.</li>\r\n    <li>Respetar las tareas de cobranza extrajudicial, de modo que las gestiones por parte de instituciones, abogados, gestores de cobranzas y servicios automatizados se realicen en horarios razonables (no antes de las 8:00 ni despuÃ©s de las 19:00, salvo acuerdo en contrario), y con respeto a la tranquilidad familiar y laboral del DEUDOR.</li>\r\n    <li>Proteger los datos personales del DEUDOR conforme a la normativa aplicable y a las polÃ­ticas de privacidad vigentes.</li>\r\n    <li>Entregar al DEUDOR copia del contrato en el momento de la firma y suministrar copia de todos los documentos que Ã©ste solicite con la debida antelaciÃ³n para la celebraciÃ³n del contrato y para responder todas las consultas que tenga.</li>\r\n    <li>Entregar, en un plazo no mayor de quince (15) dÃ­as hÃ¡biles, todos los documentos en los cuales se formalizÃ³ el crÃ©dito, debidamente firmados por las partes cuando asÃ­ proceda (Cancelaciones de Contratos, Liberaciones de Hipotecas o Prendas y Cesiones de garantÃ­a, si aplica).</li>\r\n    <li>Informar en la central de riesgo y a las autoridades correspondientes conforme a leyes del paÃ­s y Ãºnicamente cuando el DEUDOR incumpla el pago del crÃ©dito en la fecha establecida y de conformidad con la normativa aplicable.</li>\r\n    <li>Informar al DEUDOR, en forma previa a su aplicaciÃ³n, si existiese alguna modificaciÃ³n al contrato, siempre que la posibilidad de dicha modificaciÃ³n haya sido prevista expresamente en el presente contrato.</li>\r\n  </ol>\r\n\r\n  <p>Otras obligaciones y prerrogativas del ACREEDOR quedarÃ¡n sujetas a lo dispuesto en la legislaciÃ³n vigente y a las buenas prÃ¡cticas de protecciÃ³n al consumidor.</p>\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA PRIMERA: PERMUTA O CESIÃ“N DE CRÃ‰DITOS</h4>\r\n  <p>EL ACREEDOR podrÃ¡ permutar o ceder el crÃ©dito y sus garantÃ­as, sin necesidad de autorizaciÃ³n de parte de EL DEUDOR, bastando simplemente con la notificaciÃ³n que EL ACREEDOR cederÃ¡ a otro acreedor el presente crÃ©dito, de modo que el receptor del crÃ©dito deberÃ¡ respetar las condiciones originalmente pactadas en el contrato.</p>\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA SEGUNDA: VENCIMIENTO ANTICIPADO</h4>\r\n  <p>El presente contrato podrÃ¡ ser declarado como vencido, por parte de EL ACREEDOR, cuando EL DEUDOR incumpla con el pago de una o mÃ¡s de las cuotas del crÃ©dito objeto del presente contrato, o bien cuando EL DEUDOR incumpla cualesquiera de las obligaciones asumidas en razÃ³n del presente Contrato. No obstante, al plazo prefijado y la forma de pago convenida, y sin perjuicio de otras causales establecidas en este contrato, EL ACREEDOR podrÃ¡ dar por vencido anticipadamente el prÃ©stamo otorgado, resolviÃ©ndose este contrato de pleno derecho y EL ACREEDOR harÃ¡ exigible a EL DEUDOR, el pago inmediato de todo lo adeudado; con todos sus accesorios, sin necesidad de requerimiento judicial o extrajudicial, en los siguientes casos:</p>\r\n  <ol type=\"a\">\r\n    <li>Si el DEUDOR o una persona, sin o con sus instrucciones, impide a CREDIBLAMEN constatar el estado o inspeccionar los bienes constituidos en garantÃ­a a favor de CREDIBLAMEN;</li>\r\n    <li>Si se proporcionaron datos o informaciones falsas a CREDIBLAMEN sobre el DEUDOR;</li>\r\n    <li>En caso de que el DEUDOR, ya sea por presentaciÃ³n de declaratoria o por situaciÃ³n inscrita, impida o solicite su incapacidad para cumplir oportunamente con el pago de sus obligaciones corrientes o bien si el DEUDOR incurre en el deterioro de su situaciÃ³n econÃ³mica que pusiera en peligro el cumplimiento de sus obligaciones crediticias;</li>\r\n    <li>Por caso fortuito o fuerza mayor que impida que EL DEUDOR cumpliese con sus obligaciones crediticias;</li>\r\n    <li>Si el deudor faltase a las obligaciones establecidas en la ley; y</li>\r\n    <li>Si EL DEUDOR no entrega cualquier otra obligaciÃ³n que el deudor en favor de CREDIBLAMEN u otro acreedor tenga pendiente segÃºn lo establecido en el presente contrato.</li>\r\n  </ol>\r\n\r\n  <p><strong>CLAUSULAS PRINCIPALES:</strong></p>\r\n  <ol>\r\n    <li>El Deudor se obliga a pagar el capital y los intereses conforme al plan de pagos.</li>\r\n    <li>La comisiÃ³n de desembolso serÃ¡ amortizada en las cuotas segÃºn lo acordado.</li>\r\n    <li>El incumplimiento generarÃ¡ intereses moratorios y demÃ¡s acciones legales correspondientes.</li>\r\n  </ol>\r\n\r\n  <div class=\"sig\">\r\n    <div>\r\n      <div class=\"line\"></div>\r\n      <div>Firma del Deudor</div>\r\n    </div>\r\n    <div style=\"height:16px;\"></div>\r\n    <div>\r\n      <div class=\"line\"></div>\r\n      <div>Firma del Acreedor / Representante</div>\r\n    </div>\r\n  </div>\r\n\r\n  <div class=\"small\" style=\"margin-top:18px;\">Generado el: 17/12/2025</div>\r\n</body>\r\n</html>\r\n', 15, '2025-12-17 12:04:49'),
(2, 1, 4, '<!doctype html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\" />\r\n  <style>\r\n    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#222; }\r\n    .center { text-align:center; }\r\n    .small { font-size:11px; color:#444; }\r\n    .sig { margin-top:40px; }\r\n    .line { border-bottom:1px solid #000; width:240px; display:inline-block; }\r\n  </style>\r\n  <title>CONTRATO PRIVADO DE MUTUO (COMISION AMORTIZADA) SIN FIADOR</title>\r\n</head>\r\n<body>\r\n  <div class=\"center\">\r\n    <h2>CONTRATO PRIVADO DE MUTUO (COMISIÃ“N AMORTIZADA) SIN FIADOR</h2>\r\n    <div class=\"small\">Documento generado por Servicredit</div>\r\n  </div>\r\n  \r\n  <div style=\"background:#111;color:#fff;padding:10px;margin-top:12px;border-radius:4px;\">\r\n    <div style=\"text-align:center;font-weight:700;\">CONTRATO PRIVADO DE MUTUO</div>\r\n    <div style=\"text-align:center;color:#ff6666;margin-top:6px;\">NÂ° Cliente <span style=\"font-weight:700;color:#ff6666\">{{cliente_numero}}</span></div>\r\n  </div>\r\n\r\n  <p><strong>Nosotros:</strong> {{acreedor_fullname}}, mayor de edad, {{acreedor_estado_civil}}, {{acreedor_profesion}}, de este domicilio, identificada con cÃ©dula de identidad {{acreedor_doc}}; quien actÃºa en nombre y representaciÃ³n de la entidad jurÃ­dica <strong></strong>, conocida comercialmente como \"{{empresa_comercial}}\", a quien en lo sucesivo se le denominarÃ¡ <strong>\"EL ACREEDOR\"</strong>.</p>\r\n\r\n  <p><strong>Y:</strong> {{deudor_fullname}}, mayor de edad, {{deudor_estado_civil}}, {{deudor_profesion}}, identificada con cÃ©dula de identidad {{deudor_doc}}, con domicilio en {{deudor_direccion}}, a quien en lo sucesivo se le denominarÃ¡ <strong>\"EL DEUDOR\"</strong>.</p>\r\n\r\n  <p><strong>ANTECEDENTES:</strong></p>\r\n  <p>Con fecha de solicitud: <strong></strong>, el solicitante presentÃ³ la solicitud NÂ° <strong>9</strong> y se aprobÃ³ el prÃ©stamo NÂ° <strong>1</strong>. A continuaciÃ³n se consignan las declaraciones y antecedentes que constan en el expediente.</p>\r\n\r\n  <p><strong>OBJETO:</strong> El Acreedor otorga un prÃ©stamo al Deudor por la suma de <strong>$500.00</strong> dÃ³lares estadounidenses, con plazo de <strong>14</strong> meses y pagos de cuota segÃºn el plan de pagos adjunto.</p>\r\n\r\n  <p><strong>DESTINO DEL CRÃ‰DITO:</strong>  </p>\r\n\r\n  <p><strong>DECLARACIONES DEL DEUDOR:</strong></p>\r\n  <p></p>\r\n\r\n  <p>Las partes manifiestan que la informaciÃ³n contenida en la solicitud inicial y en el formato de Uso de CrÃ©dito fue proporcionada y verificada conforme a los documentos que forman parte del expediente.</p>\r\n\r\n  <h4>CLÃUSULA PRIMERA: OTORGAMIENTO DE CRÃ‰DITO Y DESTINO</h4>\r\n  <p>En este acto EL ACREEDOR otorga el presente crÃ©dito a EL DEUDOR por la Cantidad de <strong>$500.00</strong> ({{monto_credito_letras}}), que segÃºn el tipo de cambio oficial del cordoba con respecto al dÃ³lar autorizado por el Banco Central de Nicaragua para este dÃ­a a TREINTA Y SEIS CÃ“RDOBAS CON 6243/100 por dÃ³lar (USD x $1.00), equivalentes a <strong>{{monto_equivalente_usd}}</strong> (${{monto_credito_usd}}), el cual es destinado a prÃ©stamo para capital de trabajo.</p>\r\n\r\n  <h4>CLÃUSULA SEGUNDA: TASA DE INTERÃ‰S CORRIENTE Y MORATORIA</h4>\r\n  <p>EL DEUDOR reconoce a favor de EL ACREEDOR una Tasa de interÃ©s corriente del <strong>{{tasa_interes_corriente}}</strong>% anual sobre el saldo de principal desde la fecha de desembolso hasta el total de su cancelaciÃ³n y ademÃ¡s reconocerÃ¡ una Tasa de InterÃ©s Moratorio equivalente al <strong>{{tasa_moratoria}}</strong>% anual sobre las sumas adeudadas en mora.</p>\r\n\r\n  <h4>CLÃUSULA TERCERA: COMISIONES, GASTOS Y CARGOS CONEXOS</h4>\r\n  <p>a) ComisiÃ³n por desembolso: EL DEUDOR reconoce que pagarÃ¡ el <strong>{{comision_desembolso}}</strong>% sobre el monto del prÃ©stamo, en concepto de comisiÃ³n por desembolso, la cual serÃ¡ incluida y amortizada en las cuotas acordadas. AdemÃ¡s, el DEUDOR serÃ¡ responsable por los gastos de gestiÃ³n, notariales y administrativos necesarios para la ejecuciÃ³n del desembolso.</p>\r\n\r\n  <h4>CLÃUSULA CUARTA: PERIODO DE VIGENCIA, PLAZO Y MONTO DE LAS CUOTAS</h4>\r\n  <p>Este contrato tendrÃ¡ un plazo de <strong>14</strong> meses contados desde <strong>{{fecha_desembolso}}</strong>, venciendo dicho plazo el dÃ­a <strong>2026-07-09</strong>, salvo que se aplique la clÃ¡usula de vencimiento anticipado por incumplimiento.</p>\r\n\r\n  <h4>CLÃUSULA QUINTA: PLAN DE PAGOS</h4>\r\n  <p>El pago de las cuotas se realizarÃ¡ de acuerdo al plan de pagos adjunto que forma parte integrante de este contrato. La primera cuota vencerÃ¡ el dÃ­a <strong>2025-12-26</strong> y las siguientes conforme a la frecuencia pactada: <strong>{{frecuencia}}</strong>.</p>\r\n\r\n  <h4>CLÃUSULA SEXTA: INCUMPLIMIENTO</h4>\r\n  <p>En caso de incumplimiento en el pago de cualquiera de las cuotas, EL ACREEDOR podrÃ¡ exigir la totalidad del saldo vencido y devengado y aplicar los intereses moratorios establecidos en la ClÃ¡usula Segunda, asÃ­ como iniciar las gestiones de cobranza y acciones legales correspondientes.</p>\r\n\r\n  <h4>DETALLE ADICIONAL DEL PRÃ‰STAMO</h4>\r\n  <p>EL DEUDOR se obliga a pagar a EL ACREEDOR la cantidad de <strong>{{monto_principal}}</strong> ({{monto_principal_letras}}) en concepto de PRINCIPAL, y <strong>{{interes_total}}</strong> en concepto de INTERESES CORRIENTES, mÃ¡s <strong>{{comision_total}}</strong> en concepto de COMISIÃ“N POR DESEMBOLSO, para un total de <strong>{{total_conceptos}}</strong>.</p>\r\n\r\n  <p>El cronograma constarÃ¡ de <strong>14</strong> cuotas, de las cuales <strong>14</strong> serÃ¡n cuotas ordinarias de <strong>$44.26</strong> y una Ãºltima cuota de <strong>$44.26</strong>. Por ejemplo, se acuerda un monto por cuota de <strong>$44.26</strong> para las cuotas corrientes.</p>\r\n\r\n  <p>La primera cuota se realizarÃ¡ el dÃ­a <strong>2025-12-26</strong> y la Ãºltima cuota vencerÃ¡ el dÃ­a <strong>2026-07-09</strong>. En caso de que alguna fecha caiga en dÃ­a inhÃ¡bil, el pago se efectuarÃ¡ el dÃ­a hÃ¡bil siguiente salvo disposiciÃ³n en contrario.</p>\r\n\r\n  <h4>CLÃUSULA SÃ‰PTIMA: LUGAR, FORMA Y MEDIOS DE PAGO</h4>\r\n  <p>EL DEUDOR podrÃ¡ realizar los pagos de las cuotas de la presente obligaciÃ³n a EL ACREEDOR, en las siguientes formas:</p>\r\n  <ol type=\"a\">\r\n    <li>En las oficinas de CREDIBLAMEN;</li>\r\n    <li>Directamente a los gestores de cobros debidamente autorizados e identificados y designados por EL ACREEDOR;</li>\r\n    <li>TambiÃ©n podrÃ¡ realizar depÃ³sitos en las cuentas bancarias habilitadas por CREDIBLAMEN.</li>\r\n  </ol>\r\n\r\n  <h4>CLÃUSULA SEXTA: MANTENIMIENTO DE VALOR Y MONEDA DE REFERENCIA</h4>\r\n  <p>Conforme a lo establecido en el marco regulatorio, todas las variaciones de la Moneda Nacional (Devaluaciones) con respecto a la moneda de referencia serÃ¡n asumidas por EL DEUDOR; por ende, es entendido que el riesgo cambiario ha sido expresamente aceptado y asumido contractualmente por EL DEUDOR. El mantenimiento de valor se calcularÃ¡ sobre el saldo de principal a la fecha de corte neto.</p>\r\n\r\n  <h4>CLÃUSULA SÃ‰PTIMA (IMPUTACIÃ“N DE PAGO):</h4>\r\n  <p>EL DEUDOR reconoce que los pagos que realice se imputarÃ¡n en el siguiente orden: 1) Costos y gastos de recuperaciÃ³n extrajudicial o judicial; 2) Intereses moratorios que pudieran existir; 3) Gastos, costos y cargos conexos que pudieran proceder conforme a lo estipulado en este contrato; 4) Comisiones que pudieren proceder conforme a lo estipulado en este contrato; 5) Intereses corrientes adeudados; y 6) AmortizaciÃ³n al principal.</p>\r\n\r\n  <h4>CLÃUSULA OCTAVA: OBLIGACIONES DEL DEUDOR</h4>\r\n  <p>Al realizar los pagos en tiempo, modo y condiciones convenidas en el presente contrato, el DEUDOR se obliga a: a) No hacer uso diferente del dinero al que se ha estipulado en la clÃ¡usula segundo del presente contrato; b) Suministrar informaciones reales de su situaciÃ³n econÃ³mica y social antes, en el momento y despuÃ©s de otorgado el crÃ©dito; c) Comunicar, por escrito y en forma oportuna, a EL ACREEDOR cualquier cambio de su domicilio; d) Aceptar como vÃ¡lida cualquier notificaciÃ³n judicial o extrajudicial que se haga en la Ãºltima direcciÃ³n de su domicilio; e) Autorizar que EL ACREEDOR, a travÃ©s de sus representantes o funcionarios, supervise por medio de sus actuaciones el cumplimiento de las obligaciones asumidas.</p>\r\n\r\n  <h4>CLÃUSULA NOVENA: DERECHOS DEL ACREEDOR</h4>\r\n  <p>Al recibir, sin discriminaciÃ³n alguna, servicios de calidad y un trato respetuoso, EL ACREEDOR tendrÃ¡, entre otros, los derechos de: a) Exigir el pago oportuno de las cuotas; b) Aplicar intereses moratorios y cargos por mora; c) Ejecutar las garantÃ­as aportadas por el DEUDOR en caso de incumplimiento; d) Solicitar y recibir la informaciÃ³n necesaria para la administraciÃ³n y cobranza del crÃ©dito.</p>\r\n\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA: DERECHOS Y OBLIGACIONES DEL ACREEDOR</h4>\r\n  <p>EL ACREEDOR, ademÃ¡s de los derechos generales establecidos en este contrato y en la ley aplicable, tendrÃ¡ las siguientes facultades y obligaciones, sin perjuicio de otras que la normativa vigente reconozca:</p>\r\n  <ol type=\"a\">\r\n    <li>A ser atendido por EL DEUDOR y a recibir respuesta oportuna, fundamentada, comprensible e integral sobre los mismos cuando aplique.</li>\r\n    <li>A ser atendido en la sucursal de EL ACREEDOR donde suscribiÃ³ el presente contrato para realizar cualquier consulta sobre el mismo.</li>\r\n    <li>A recibir un ejemplar del presente contrato con sus respectivos anexos, incluyendo el Resumen Informativo y el plan de pago suscrito en la presente obligaciÃ³n.</li>\r\n    <li>A ser informado con la debida antelaciÃ³n sobre cualquier modificaciÃ³n que se pretenda introducir en las condiciones contractuales que le afecten; salvo disposiciÃ³n legal distinta, la comunicaciÃ³n se realizarÃ¡ con sesenta (60) dÃ­as calendario de anticipaciÃ³n; cuando las modificaciones se refieran a variaciones de tasas de interÃ©s, comisiones y/o costos, el plazo mÃ­nimo serÃ¡ de treinta (30) dÃ­as calendario.</li>\r\n    <li>A realizar el pago de forma anticipada, total o parcial, sin que por ello se imponga una penalidad que reduzca el derecho del DEUDOR a pagar anticipadamente; en tal caso se deberÃ¡n reducir los intereses generados a la fecha del pago.</li>\r\n    <li>A que EL ACREEDOR realice las gestiones de cobranza estrictamente respetando la tranquilidad familiar y laboral, la honorabilidad e integridad moral del DEUDOR, y a ser notificado en horarios razonables (no antes de las 8:00 ni despuÃ©s de las 19:00, salvo acuerdo expreso distinto).</li>\r\n    <li>Derecho de rescindir el contrato en caso de que EL ACREEDOR no cumpla con el desembolso del monto aprobado en el plazo establecido en la presente operaciÃ³n.</li>\r\n    <li>Los derechos previstos en la Ley No. 842 (Ley de ProtecciÃ³n de los Derechos de las Personas Consumidoras y Usuarias) y en la normativa aplicable, que prevalecerÃ¡n en caso de conflicto con las estipulaciones de este contrato.</li>\r\n  </ol>\r\n\r\n  <p>Adicionalmente, EL ACREEDOR se obliga a cumplir con las siguientes obligaciones y garantÃ­as de trato al DEUDOR:</p>\r\n  <ol type=\"a\">\r\n    <li>Respetar los tÃ©rminos y condiciones del contrato y brindar una respuesta oportuna, fundada, comprensible e integral a las consultas del DEUDOR.</li>\r\n    <li>Informar previamente al DEUDOR sobre las condiciones del crÃ©dito y cualquiera modificaciÃ³n que pudiera afectarle.</li>\r\n    <li>Brindar atenciÃ³n de calidad y facilitar el acceso al lugar de reclamo por parte del DEUDOR, proveyendo facilidades para que pueda formular el mismo y contar con un servicio de atenciÃ³n al usuario.</li>\r\n    <li>No exigir a las personas reclamantes la presentaciÃ³n de documentos o informaciÃ³n que no se encuentren en nuestro poder o que no guarden relaciÃ³n directa con la materia reclamada.</li>\r\n    <li>No exigir al DEUDOR la participaciÃ³n de un abogado para reclamos ordinarios; y no aplicar mÃ©todos o usos de cobro extrajudiciales que afecten el honor o la imagen del DEUDOR, ni que resulten intimidatorios.</li>\r\n    <li>Respetar las tareas de cobranza extrajudicial, de modo que las gestiones por parte de instituciones, abogados, gestores de cobranzas y servicios automatizados se realicen en horarios razonables (no antes de las 8:00 ni despuÃ©s de las 19:00, salvo acuerdo en contrario), y con respeto a la tranquilidad familiar y laboral del DEUDOR.</li>\r\n    <li>Proteger los datos personales del DEUDOR conforme a la normativa aplicable y a las polÃ­ticas de privacidad vigentes.</li>\r\n    <li>Entregar al DEUDOR copia del contrato en el momento de la firma y suministrar copia de todos los documentos que Ã©ste solicite con la debida antelaciÃ³n para la celebraciÃ³n del contrato y para responder todas las consultas que tenga.</li>\r\n    <li>Entregar, en un plazo no mayor de quince (15) dÃ­as hÃ¡biles, todos los documentos en los cuales se formalizÃ³ el crÃ©dito, debidamente firmados por las partes cuando asÃ­ proceda (Cancelaciones de Contratos, Liberaciones de Hipotecas o Prendas y Cesiones de garantÃ­a, si aplica).</li>\r\n    <li>Informar en la central de riesgo y a las autoridades correspondientes conforme a leyes del paÃ­s y Ãºnicamente cuando el DEUDOR incumpla el pago del crÃ©dito en la fecha establecida y de conformidad con la normativa aplicable.</li>\r\n    <li>Informar al DEUDOR, en forma previa a su aplicaciÃ³n, si existiese alguna modificaciÃ³n al contrato, siempre que la posibilidad de dicha modificaciÃ³n haya sido prevista expresamente en el presente contrato.</li>\r\n  </ol>\r\n\r\n  <p>Otras obligaciones y prerrogativas del ACREEDOR quedarÃ¡n sujetas a lo dispuesto en la legislaciÃ³n vigente y a las buenas prÃ¡cticas de protecciÃ³n al consumidor.</p>\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA PRIMERA: PERMUTA O CESIÃ“N DE CRÃ‰DITOS</h4>\r\n  <p>EL ACREEDOR podrÃ¡ permutar o ceder el crÃ©dito y sus garantÃ­as, sin necesidad de autorizaciÃ³n de parte de EL DEUDOR, bastando simplemente con la notificaciÃ³n que EL ACREEDOR cederÃ¡ a otro acreedor el presente crÃ©dito, de modo que el receptor del crÃ©dito deberÃ¡ respetar las condiciones originalmente pactadas en el contrato.</p>\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA SEGUNDA: VENCIMIENTO ANTICIPADO</h4>\r\n  <p>El presente contrato podrÃ¡ ser declarado como vencido, por parte de EL ACREEDOR, cuando EL DEUDOR incumpla con el pago de una o mÃ¡s de las cuotas del crÃ©dito objeto del presente contrato, o bien cuando EL DEUDOR incumpla cualesquiera de las obligaciones asumidas en razÃ³n del presente Contrato. No obstante, al plazo prefijado y la forma de pago convenida, y sin perjuicio de otras causales establecidas en este contrato, EL ACREEDOR podrÃ¡ dar por vencido anticipadamente el prÃ©stamo otorgado, resolviÃ©ndose este contrato de pleno derecho y EL ACREEDOR harÃ¡ exigible a EL DEUDOR, el pago inmediato de todo lo adeudado; con todos sus accesorios, sin necesidad de requerimiento judicial o extrajudicial, en los siguientes casos:</p>\r\n  <ol type=\"a\">\r\n    <li>Si el DEUDOR o una persona, sin o con sus instrucciones, impide a CREDIBLAMEN constatar el estado o inspeccionar los bienes constituidos en garantÃ­a a favor de CREDIBLAMEN;</li>\r\n    <li>Si se proporcionaron datos o informaciones falsas a CREDIBLAMEN sobre el DEUDOR;</li>\r\n    <li>En caso de que el DEUDOR, ya sea por presentaciÃ³n de declaratoria o por situaciÃ³n inscrita, impida o solicite su incapacidad para cumplir oportunamente con el pago de sus obligaciones corrientes o bien si el DEUDOR incurre en el deterioro de su situaciÃ³n econÃ³mica que pusiera en peligro el cumplimiento de sus obligaciones crediticias;</li>\r\n    <li>Por caso fortuito o fuerza mayor que impida que EL DEUDOR cumpliese con sus obligaciones crediticias;</li>\r\n    <li>Si el deudor faltase a las obligaciones establecidas en la ley; y</li>\r\n    <li>Si EL DEUDOR no entrega cualquier otra obligaciÃ³n que el deudor en favor de CREDIBLAMEN u otro acreedor tenga pendiente segÃºn lo establecido en el presente contrato.</li>\r\n  </ol>\r\n\r\n  <p><strong>CLAUSULAS PRINCIPALES:</strong></p>\r\n  <ol>\r\n    <li>El Deudor se obliga a pagar el capital y los intereses conforme al plan de pagos.</li>\r\n    <li>La comisiÃ³n de desembolso serÃ¡ amortizada en las cuotas segÃºn lo acordado.</li>\r\n    <li>El incumplimiento generarÃ¡ intereses moratorios y demÃ¡s acciones legales correspondientes.</li>\r\n  </ol>\r\n\r\n  <div class=\"sig\">\r\n    <div>\r\n      <div class=\"line\"></div>\r\n      <div>Firma del Deudor</div>\r\n    </div>\r\n    <div style=\"height:16px;\"></div>\r\n    <div>\r\n      <div class=\"line\"></div>\r\n      <div>Firma del Acreedor / Representante</div>\r\n    </div>\r\n  </div>\r\n\r\n  <div class=\"small\" style=\"margin-top:18px;\">Generado el: 17/12/2025</div>\r\n</body>\r\n</html>\r\n', 15, '2025-12-17 12:08:04');
INSERT IGNORE INTO `tb_contratos` (`idcontrato`, `idprestamo`, `template_id`, `contenido`, `created_by`, `created_at`) VALUES
(3, 12, 4, '<!doctype html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\" />\r\n  <style>\r\n    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#222; }\r\n    .center { text-align:center; }\r\n    .small { font-size:11px; color:#444; }\r\n    .sig { margin-top:40px; }\r\n    .line { border-bottom:1px solid #000; width:240px; display:inline-block; }\r\n  </style>\r\n  <title>CONTRATO PRIVADO DE MUTUO (COMISION AMORTIZADA) SIN FIADOR</title>\r\n</head>\r\n<body>\r\n  <div class=\"center\">\r\n    <h2>CONTRATO PRIVADO DE MUTUO (COMISIÃ“N AMORTIZADA) SIN FIADOR</h2>\r\n    <div class=\"small\">Documento generado por Servicredit</div>\r\n  </div>\r\n  \r\n  <div style=\"background:#111;color:#fff;padding:10px;margin-top:12px;border-radius:4px;\">\r\n    <div style=\"text-align:center;font-weight:700;\">CONTRATO PRIVADO DE MUTUO</div>\r\n    <div style=\"text-align:center;color:#ff6666;margin-top:6px;\">NÂ° Cliente <span style=\"font-weight:700;color:#ff6666\"></span></div>\r\n  </div>\r\n\r\n  <p><strong>Nosotros:</strong> Emilia Del Socorro Mendieta, mayor de edad, casada, SociÃ³loga, de este domicilio, identificada con cÃ©dula de identidad nicaragÃ¼ense nÃºmero: cero, cero, uno, guion, uno, cuatro, cero, uno, seis, nueve, cero, cero, cuatro, seis, E (001-140169-0046E); quien actÃºa en nombre y representaciÃ³n de la entidad jurÃ­dica <strong>CREDI BLAMEN, SOCIEDAD ANÃ“NIMA</strong>, conocida comercialmente como â€œCREDIBLAMEN, S.A.â€, a quien en lo sucesivo se le denominarÃ¡ <strong>\"EL ACREEDOR\"</strong>.</p>\r\n\r\n  <p><strong>Y:</strong> Antonio Ramirez Carrillo Erick, mayor de edad, {{deudor_estado_civil}}, {{deudor_profesion}}, identificada con cÃ©dula de identidad 0012702981004X, con domicilio en aaa, a quien en lo sucesivo se le denominarÃ¡ <strong>\"EL DEUDOR\"</strong>.</p>\r\n\r\n  <p><strong>ANTECEDENTES:</strong></p>\r\n  <p>Con fecha de solicitud: <strong>2025-12-12 15:29:00</strong>, el solicitante presentÃ³ la solicitud NÂ° <strong>13</strong> y se aprobÃ³ el prÃ©stamo NÂ° <strong>12</strong>. A continuaciÃ³n se consignan las declaraciones y antecedentes que constan en el expediente.</p>\r\n\r\n  <p><strong>OBJETO:</strong> El Acreedor otorga un prÃ©stamo al Deudor por la suma de <strong>$400.00</strong> dÃ³lares estadounidenses, con plazo de <strong>12</strong> meses y pagos de cuota segÃºn el plan de pagos adjunto.</p>\r\n\r\n  <p><strong>DESTINO DEL CRÃ‰DITO:</strong> Consumo Completo</p>\r\n\r\n  <p><strong>DECLARACIONES DEL DEUDOR:</strong></p>\r\n  <p>podjdjak</p>\r\n\r\n  <p>Las partes manifiestan que la informaciÃ³n contenida en la solicitud inicial y en el formato de Uso de CrÃ©dito fue proporcionada y verificada conforme a los documentos que forman parte del expediente.</p>\r\n\r\n  <h4>CLÃUSULA PRIMERA: OTORGAMIENTO DE CRÃ‰DITO Y DESTINO</h4>\r\n  <p>En este acto EL ACREEDOR otorga el presente crÃ©dito a EL DEUDOR por la Cantidad de <strong>$400.00</strong> ({{monto_credito_letras}}), que segÃºn el tipo de cambio oficial del cordoba con respecto al dÃ³lar autorizado por el Banco Central de Nicaragua para este dÃ­a a TREINTA Y SEIS CÃ“RDOBAS CON 6243/100 por dÃ³lar (USD x $1.00), equivalentes a <strong>{{monto_equivalente_usd}}</strong> (${{monto_credito_usd}}), el cual es destinado a prÃ©stamo para capital de trabajo.</p>\r\n\r\n  <h4>CLÃUSULA SEGUNDA: TASA DE INTERÃ‰S CORRIENTE Y MORATORIA</h4>\r\n  <p>EL DEUDOR reconoce a favor de EL ACREEDOR una Tasa de interÃ©s corriente del <strong>{{tasa_interes_corriente}}</strong>% anual sobre el saldo de principal desde la fecha de desembolso hasta el total de su cancelaciÃ³n y ademÃ¡s reconocerÃ¡ una Tasa de InterÃ©s Moratorio equivalente al <strong>{{tasa_moratoria}}</strong>% anual sobre las sumas adeudadas en mora.</p>\r\n\r\n  <h4>CLÃUSULA TERCERA: COMISIONES, GASTOS Y CARGOS CONEXOS</h4>\r\n  <p>a) ComisiÃ³n por desembolso: EL DEUDOR reconoce que pagarÃ¡ el <strong>{{comision_desembolso}}</strong>% sobre el monto del prÃ©stamo, en concepto de comisiÃ³n por desembolso, la cual serÃ¡ incluida y amortizada en las cuotas acordadas. AdemÃ¡s, el DEUDOR serÃ¡ responsable por los gastos de gestiÃ³n, notariales y administrativos necesarios para la ejecuciÃ³n del desembolso.</p>\r\n\r\n  <h4>CLÃUSULA CUARTA: PERIODO DE VIGENCIA, PLAZO Y MONTO DE LAS CUOTAS</h4>\r\n  <p>Este contrato tendrÃ¡ un plazo de <strong>12</strong> meses contados desde <strong>{{fecha_desembolso}}</strong>, venciendo dicho plazo el dÃ­a <strong>2026-11-26</strong>, salvo que se aplique la clÃ¡usula de vencimiento anticipado por incumplimiento.</p>\r\n\r\n  <h4>CLÃUSULA QUINTA: PLAN DE PAGOS</h4>\r\n  <p>El pago de las cuotas se realizarÃ¡ de acuerdo al plan de pagos adjunto que forma parte integrante de este contrato. La primera cuota vencerÃ¡ el dÃ­a <strong>2025-12-26</strong> y las siguientes conforme a la frecuencia pactada: <strong>{{frecuencia}}</strong>.</p>\r\n\r\n  <h4>CLÃUSULA SEXTA: INCUMPLIMIENTO</h4>\r\n  <p>En caso de incumplimiento en el pago de cualquiera de las cuotas, EL ACREEDOR podrÃ¡ exigir la totalidad del saldo vencido y devengado y aplicar los intereses moratorios establecidos en la ClÃ¡usula Segunda, asÃ­ como iniciar las gestiones de cobranza y acciones legales correspondientes.</p>\r\n\r\n  <h4>DETALLE ADICIONAL DEL PRÃ‰STAMO</h4>\r\n  <p>EL DEUDOR se obliga a pagar a EL ACREEDOR la cantidad de <strong>{{monto_principal}}</strong> ({{monto_principal_letras}}) en concepto de PRINCIPAL, y <strong>{{interes_total}}</strong> en concepto de INTERESES CORRIENTES, mÃ¡s <strong>{{comision_total}}</strong> en concepto de COMISIÃ“N POR DESEMBOLSO, para un total de <strong>{{total_conceptos}}</strong>.</p>\r\n\r\n  <p>El cronograma constarÃ¡ de <strong>12</strong> cuotas, de las cuales <strong>12</strong> serÃ¡n cuotas ordinarias de <strong>$55.41</strong> y una Ãºltima cuota de <strong>$55.45</strong>. Por ejemplo, se acuerda un monto por cuota de <strong>$55.41</strong> para las cuotas corrientes.</p>\r\n\r\n  <p>La primera cuota se realizarÃ¡ el dÃ­a <strong>2025-12-26</strong> y la Ãºltima cuota vencerÃ¡ el dÃ­a <strong>2026-11-26</strong>. En caso de que alguna fecha caiga en dÃ­a inhÃ¡bil, el pago se efectuarÃ¡ el dÃ­a hÃ¡bil siguiente salvo disposiciÃ³n en contrario.</p>\r\n\r\n  <h4>CLÃUSULA SÃ‰PTIMA: LUGAR, FORMA Y MEDIOS DE PAGO</h4>\r\n  <p>EL DEUDOR podrÃ¡ realizar los pagos de las cuotas de la presente obligaciÃ³n a EL ACREEDOR, en las siguientes formas:</p>\r\n  <ol type=\"a\">\r\n    <li>En las oficinas de CREDIBLAMEN;</li>\r\n    <li>Directamente a los gestores de cobros debidamente autorizados e identificados y designados por EL ACREEDOR;</li>\r\n    <li>TambiÃ©n podrÃ¡ realizar depÃ³sitos en las cuentas bancarias habilitadas por CREDIBLAMEN.</li>\r\n  </ol>\r\n\r\n  <h4>CLÃUSULA SEXTA: MANTENIMIENTO DE VALOR Y MONEDA DE REFERENCIA</h4>\r\n  <p>Conforme a lo establecido en el marco regulatorio, todas las variaciones de la Moneda Nacional (Devaluaciones) con respecto a la moneda de referencia serÃ¡n asumidas por EL DEUDOR; por ende, es entendido que el riesgo cambiario ha sido expresamente aceptado y asumido contractualmente por EL DEUDOR. El mantenimiento de valor se calcularÃ¡ sobre el saldo de principal a la fecha de corte neto.</p>\r\n\r\n  <h4>CLÃUSULA SÃ‰PTIMA (IMPUTACIÃ“N DE PAGO):</h4>\r\n  <p>EL DEUDOR reconoce que los pagos que realice se imputarÃ¡n en el siguiente orden: 1) Costos y gastos de recuperaciÃ³n extrajudicial o judicial; 2) Intereses moratorios que pudieran existir; 3) Gastos, costos y cargos conexos que pudieran proceder conforme a lo estipulado en este contrato; 4) Comisiones que pudieren proceder conforme a lo estipulado en este contrato; 5) Intereses corrientes adeudados; y 6) AmortizaciÃ³n al principal.</p>\r\n\r\n  <h4>CLÃUSULA OCTAVA: OBLIGACIONES DEL DEUDOR</h4>\r\n  <p>Al realizar los pagos en tiempo, modo y condiciones convenidas en el presente contrato, el DEUDOR se obliga a: a) No hacer uso diferente del dinero al que se ha estipulado en la clÃ¡usula segundo del presente contrato; b) Suministrar informaciones reales de su situaciÃ³n econÃ³mica y social antes, en el momento y despuÃ©s de otorgado el crÃ©dito; c) Comunicar, por escrito y en forma oportuna, a EL ACREEDOR cualquier cambio de su domicilio; d) Aceptar como vÃ¡lida cualquier notificaciÃ³n judicial o extrajudicial que se haga en la Ãºltima direcciÃ³n de su domicilio; e) Autorizar que EL ACREEDOR, a travÃ©s de sus representantes o funcionarios, supervise por medio de sus actuaciones el cumplimiento de las obligaciones asumidas.</p>\r\n\r\n  <h4>CLÃUSULA NOVENA: DERECHOS DEL ACREEDOR</h4>\r\n  <p>Al recibir, sin discriminaciÃ³n alguna, servicios de calidad y un trato respetuoso, EL ACREEDOR tendrÃ¡, entre otros, los derechos de: a) Exigir el pago oportuno de las cuotas; b) Aplicar intereses moratorios y cargos por mora; c) Ejecutar las garantÃ­as aportadas por el DEUDOR en caso de incumplimiento; d) Solicitar y recibir la informaciÃ³n necesaria para la administraciÃ³n y cobranza del crÃ©dito.</p>\r\n\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA: DERECHOS Y OBLIGACIONES DEL ACREEDOR</h4>\r\n  <p>EL ACREEDOR, ademÃ¡s de los derechos generales establecidos en este contrato y en la ley aplicable, tendrÃ¡ las siguientes facultades y obligaciones, sin perjuicio de otras que la normativa vigente reconozca:</p>\r\n  <ol type=\"a\">\r\n    <li>A ser atendido por EL DEUDOR y a recibir respuesta oportuna, fundamentada, comprensible e integral sobre los mismos cuando aplique.</li>\r\n    <li>A ser atendido en la sucursal de EL ACREEDOR donde suscribiÃ³ el presente contrato para realizar cualquier consulta sobre el mismo.</li>\r\n    <li>A recibir un ejemplar del presente contrato con sus respectivos anexos, incluyendo el Resumen Informativo y el plan de pago suscrito en la presente obligaciÃ³n.</li>\r\n    <li>A ser informado con la debida antelaciÃ³n sobre cualquier modificaciÃ³n que se pretenda introducir en las condiciones contractuales que le afecten; salvo disposiciÃ³n legal distinta, la comunicaciÃ³n se realizarÃ¡ con sesenta (60) dÃ­as calendario de anticipaciÃ³n; cuando las modificaciones se refieran a variaciones de tasas de interÃ©s, comisiones y/o costos, el plazo mÃ­nimo serÃ¡ de treinta (30) dÃ­as calendario.</li>\r\n    <li>A realizar el pago de forma anticipada, total o parcial, sin que por ello se imponga una penalidad que reduzca el derecho del DEUDOR a pagar anticipadamente; en tal caso se deberÃ¡n reducir los intereses generados a la fecha del pago.</li>\r\n    <li>A que EL ACREEDOR realice las gestiones de cobranza estrictamente respetando la tranquilidad familiar y laboral, la honorabilidad e integridad moral del DEUDOR, y a ser notificado en horarios razonables (no antes de las 8:00 ni despuÃ©s de las 19:00, salvo acuerdo expreso distinto).</li>\r\n    <li>Derecho de rescindir el contrato en caso de que EL ACREEDOR no cumpla con el desembolso del monto aprobado en el plazo establecido en la presente operaciÃ³n.</li>\r\n    <li>Los derechos previstos en la Ley No. 842 (Ley de ProtecciÃ³n de los Derechos de las Personas Consumidoras y Usuarias) y en la normativa aplicable, que prevalecerÃ¡n en caso de conflicto con las estipulaciones de este contrato.</li>\r\n  </ol>\r\n\r\n  <p>Adicionalmente, EL ACREEDOR se obliga a cumplir con las siguientes obligaciones y garantÃ­as de trato al DEUDOR:</p>\r\n  <ol type=\"a\">\r\n    <li>Respetar los tÃ©rminos y condiciones del contrato y brindar una respuesta oportuna, fundada, comprensible e integral a las consultas del DEUDOR.</li>\r\n    <li>Informar previamente al DEUDOR sobre las condiciones del crÃ©dito y cualquiera modificaciÃ³n que pudiera afectarle.</li>\r\n    <li>Brindar atenciÃ³n de calidad y facilitar el acceso al lugar de reclamo por parte del DEUDOR, proveyendo facilidades para que pueda formular el mismo y contar con un servicio de atenciÃ³n al usuario.</li>\r\n    <li>No exigir a las personas reclamantes la presentaciÃ³n de documentos o informaciÃ³n que no se encuentren en nuestro poder o que no guarden relaciÃ³n directa con la materia reclamada.</li>\r\n    <li>No exigir al DEUDOR la participaciÃ³n de un abogado para reclamos ordinarios; y no aplicar mÃ©todos o usos de cobro extrajudiciales que afecten el honor o la imagen del DEUDOR, ni que resulten intimidatorios.</li>\r\n    <li>Respetar las tareas de cobranza extrajudicial, de modo que las gestiones por parte de instituciones, abogados, gestores de cobranzas y servicios automatizados se realicen en horarios razonables (no antes de las 8:00 ni despuÃ©s de las 19:00, salvo acuerdo en contrario), y con respeto a la tranquilidad familiar y laboral del DEUDOR.</li>\r\n    <li>Proteger los datos personales del DEUDOR conforme a la normativa aplicable y a las polÃ­ticas de privacidad vigentes.</li>\r\n    <li>Entregar al DEUDOR copia del contrato en el momento de la firma y suministrar copia de todos los documentos que Ã©ste solicite con la debida antelaciÃ³n para la celebraciÃ³n del contrato y para responder todas las consultas que tenga.</li>\r\n    <li>Entregar, en un plazo no mayor de quince (15) dÃ­as hÃ¡biles, todos los documentos en los cuales se formalizÃ³ el crÃ©dito, debidamente firmados por las partes cuando asÃ­ proceda (Cancelaciones de Contratos, Liberaciones de Hipotecas o Prendas y Cesiones de garantÃ­a, si aplica).</li>\r\n    <li>Informar en la central de riesgo y a las autoridades correspondientes conforme a leyes del paÃ­s y Ãºnicamente cuando el DEUDOR incumpla el pago del crÃ©dito en la fecha establecida y de conformidad con la normativa aplicable.</li>\r\n    <li>Informar al DEUDOR, en forma previa a su aplicaciÃ³n, si existiese alguna modificaciÃ³n al contrato, siempre que la posibilidad de dicha modificaciÃ³n haya sido prevista expresamente en el presente contrato.</li>\r\n  </ol>\r\n\r\n  <p>Otras obligaciones y prerrogativas del ACREEDOR quedarÃ¡n sujetas a lo dispuesto en la legislaciÃ³n vigente y a las buenas prÃ¡cticas de protecciÃ³n al consumidor.</p>\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA PRIMERA: PERMUTA O CESIÃ“N DE CRÃ‰DITOS</h4>\r\n  <p>EL ACREEDOR podrÃ¡ permutar o ceder el crÃ©dito y sus garantÃ­as, sin necesidad de autorizaciÃ³n de parte de EL DEUDOR, bastando simplemente con la notificaciÃ³n que EL ACREEDOR cederÃ¡ a otro acreedor el presente crÃ©dito, de modo que el receptor del crÃ©dito deberÃ¡ respetar las condiciones originalmente pactadas en el contrato.</p>\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA SEGUNDA: VENCIMIENTO ANTICIPADO</h4>\r\n  <p>El presente contrato podrÃ¡ ser declarado como vencido, por parte de EL ACREEDOR, cuando EL DEUDOR incumpla con el pago de una o mÃ¡s de las cuotas del crÃ©dito objeto del presente contrato, o bien cuando EL DEUDOR incumpla cualesquiera de las obligaciones asumidas en razÃ³n del presente Contrato. No obstante, al plazo prefijado y la forma de pago convenida, y sin perjuicio de otras causales establecidas en este contrato, EL ACREEDOR podrÃ¡ dar por vencido anticipadamente el prÃ©stamo otorgado, resolviÃ©ndose este contrato de pleno derecho y EL ACREEDOR harÃ¡ exigible a EL DEUDOR, el pago inmediato de todo lo adeudado; con todos sus accesorios, sin necesidad de requerimiento judicial o extrajudicial, en los siguientes casos:</p>\r\n  <ol type=\"a\">\r\n    <li>Si el DEUDOR o una persona, sin o con sus instrucciones, impide a CREDIBLAMEN constatar el estado o inspeccionar los bienes constituidos en garantÃ­a a favor de CREDIBLAMEN;</li>\r\n    <li>Si se proporcionaron datos o informaciones falsas a CREDIBLAMEN sobre el DEUDOR;</li>\r\n    <li>En caso de que el DEUDOR, ya sea por presentaciÃ³n de declaratoria o por situaciÃ³n inscrita, impida o solicite su incapacidad para cumplir oportunamente con el pago de sus obligaciones corrientes o bien si el DEUDOR incurre en el deterioro de su situaciÃ³n econÃ³mica que pusiera en peligro el cumplimiento de sus obligaciones crediticias;</li>\r\n    <li>Por caso fortuito o fuerza mayor que impida que EL DEUDOR cumpliese con sus obligaciones crediticias;</li>\r\n    <li>Si el deudor faltase a las obligaciones establecidas en la ley; y</li>\r\n    <li>Si EL DEUDOR no entrega cualquier otra obligaciÃ³n que el deudor en favor de CREDIBLAMEN u otro acreedor tenga pendiente segÃºn lo establecido en el presente contrato.</li>\r\n  </ol>\r\n\r\n  <p><strong>CLAUSULAS PRINCIPALES:</strong></p>\r\n  <ol>\r\n    <li>El Deudor se obliga a pagar el capital y los intereses conforme al plan de pagos.</li>\r\n    <li>La comisiÃ³n de desembolso serÃ¡ amortizada en las cuotas segÃºn lo acordado.</li>\r\n    <li>El incumplimiento generarÃ¡ intereses moratorios y demÃ¡s acciones legales correspondientes.</li>\r\n  </ol>\r\n\r\n  <div class=\"sig\">\r\n    <div>\r\n      <div class=\"line\"></div>\r\n      <div>Firma del Deudor</div>\r\n    </div>\r\n    <div style=\"height:16px;\"></div>\r\n    <div>\r\n      <div class=\"line\"></div>\r\n      <div>Firma del Acreedor / Representante</div>\r\n    </div>\r\n  </div>\r\n\r\n  <div class=\"small\" style=\"margin-top:18px;\">Generado el: 23/12/2025</div>\r\n</body>\r\n</html>\r\n', 15, '2025-12-23 09:48:33'),
(4, 3, 0, '', 15, '2026-01-07 17:40:48'),
(5, 4, 0, '', 15, '2026-01-08 16:36:25'),
(6, 5, 0, '', 15, '2026-01-09 16:49:54');

-- --------------------------------------------------------

--
-- Table structure for table `tb_creditos`
--

DROP TABLE IF EXISTS `tb_creditos`;\n\nCREATE TABLE `tb_creditos` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_credito_detalle`
--

DROP TABLE IF EXISTS `tb_credito_detalle`;\n\nCREATE TABLE `tb_credito_detalle` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_departamentos`
--

DROP TABLE IF EXISTS `tb_departamentos`;\n\nCREATE TABLE `tb_departamentos` (
  `id` varchar(2) NOT NULL,
  `nombre` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_detalle_simulacion`
--

DROP TABLE IF EXISTS `tb_detalle_simulacion`;\n\nCREATE TABLE `tb_detalle_simulacion` (
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

DROP TABLE IF EXISTS `tb_distritos`;\n\nCREATE TABLE `tb_distritos` (
  `id` varchar(6) NOT NULL,
  `idprovincia` varchar(4) DEFAULT NULL,
  `iddepartamento` varchar(2) DEFAULT NULL,
  `nombre` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_feriados`
--

DROP TABLE IF EXISTS `tb_feriados`;\n\nCREATE TABLE `tb_feriados` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_feriados`
--

INSERT IGNORE INTO `tb_feriados` (`id`, `fecha`, `motivo`, `activo`, `created_at`) VALUES
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
-- Table structure for table `tb_garantias`
--

DROP TABLE IF EXISTS `tb_garantias`;\n\nCREATE TABLE `tb_garantias` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_garantias`
--

INSERT IGNORE INTO `tb_garantias` (`id`, `solicitud_id`, `nombre`, `cantidad`, `marca`, `modelo`, `n_serie`, `costo`, `tiempo_vida`, `foto__bak`, `foto1`, `foto2`, `foto3`, `foto4`, `foto5`, `created_at`, `updated_at`) VALUES
(1, 3, 'na', 1, 'na', 'na', 'na', 3.00, '2', NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-30 22:54:20', '2025-12-30 22:57:38'),
(2, 3, 'nann', 1, 'na', 'na', 'na', 3.00, '2', NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-07 16:15:13', NULL),
(3, 5, 'na', 1, 'na', 'na', 'na', 100.00, '3', NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-07 20:48:13', NULL),
(4, 6, 'Vitrina', 1, 'NA', 'NA', 'NA', 100.00, '2', NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-07 22:07:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_garantias_fotos`
--

DROP TABLE IF EXISTS `tb_garantias_fotos`;\n\nCREATE TABLE `tb_garantias_fotos` (
  `id` int(11) NOT NULL,
  `garantia_id` int(11) DEFAULT NULL,
  `solicitud_id` int(11) DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `grupo` varchar(100) DEFAULT NULL,
  `row_index` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_garantias_fotos`
--

INSERT IGNORE INTO `tb_garantias_fotos` (`id`, `garantia_id`, `solicitud_id`, `filename`, `grupo`, `row_index`, `created_at`) VALUES
(1, 3, 5, 'uploads/garantias/solicitud_5/63819a6541424cd776a179a8128f271f.jpg', NULL, 0, '2026-01-07 15:57:16'),
(2, 4, 6, 'uploads/garantias/solicitud_6/13035c4536b0ea11477dbfa3916e711e.jpg', NULL, 0, '2026-01-07 17:07:37');

-- --------------------------------------------------------

--
-- Table structure for table `tb_garantias_verificaciones`
--

DROP TABLE IF EXISTS `tb_garantias_verificaciones`;\n\nCREATE TABLE `tb_garantias_verificaciones` (
  `id` int(11) NOT NULL,
  `garantia_id` int(11) DEFAULT NULL,
  `solicitud_id` int(11) DEFAULT NULL,
  `verificador_usuario` varchar(150) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `foto1` varchar(255) DEFAULT NULL,
  `foto2` varchar(255) DEFAULT NULL,
  `foto3` varchar(255) DEFAULT NULL,
  `foto4` varchar(255) DEFAULT NULL,
  `foto5` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_journal`
--

DROP TABLE IF EXISTS `tb_journal`;\n\nCREATE TABLE `tb_journal` (
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
  `posted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_journal`
--

INSERT IGNORE INTO `tb_journal` (`id`, `date`, `description`, `total_debit`, `total_credit`, `created_at`, `created_by`, `source_type`, `source_id`, `voided`, `voided_by`, `voided_at`, `period_month`, `period_year`, `entry_type`, `centro_costo_id`, `posted`, `posted_by`, `posted_at`) VALUES
(1, '2025-12-31', 'Asiento de Apertura - Saldos Iniciales', 352158676.33, 352158676.42, '2025-12-31 16:27:19', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, 15, '2026-01-13 17:45:12'),
(2, '2026-01-13', 'Asiento Prueba', 10.00, 10.00, '2026-01-13 17:42:09', 15, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'CD', NULL, 1, 15, '2026-01-13 17:45:16');

-- --------------------------------------------------------

--
-- Table structure for table `tb_journal_entry`
--

DROP TABLE IF EXISTS `tb_journal_entry`;\n\nCREATE TABLE `tb_journal_entry` (
  `id` int(11) NOT NULL,
  `journal_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `debit` decimal(14,2) DEFAULT 0.00,
  `credit` decimal(14,2) DEFAULT 0.00,
  `description` varchar(512) DEFAULT NULL,
  `centro_costo_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_journal_entry`
--

INSERT IGNORE INTO `tb_journal_entry` (`id`, `journal_id`, `account_id`, `debit`, `credit`, `description`, `centro_costo_id`) VALUES
(39, 1, 3, 35869.00, 0.00, 'Saldo Inicial - Caja', 1),
(40, 1, 7, 5885801.39, 0.00, 'Saldo Inicial - Documentos por Cobrar', 1),
(41, 1, 10, 24168264.79, 0.00, 'Saldo Inicial - Almac?n', 1),
(42, 1, 11, 1200.00, 0.00, 'Saldo Inicial - Anticipo a Proveedores', 1),
(43, 1, 16, 589000.00, 0.00, 'Saldo Inicial - Equipo de Transporte', 1),
(44, 1, 17, 71000.00, 0.00, 'Saldo Inicial - Equipo de C?mputo', 1),
(45, 1, 18, 158600.00, 0.00, 'Saldo Inicial - Maquinaria y Equipo', 1),
(46, 1, 19, 0.00, 54862.00, 'Saldo Inicial - Depreciaci?n Acumulada', 1),
(47, 1, 25, 20000.00, 0.00, 'Saldo Inicial - Rentas Pagadas por Anticipado', 1),
(48, 1, 26, 100000.00, 0.00, 'Saldo Inicial - Seguros Pagados por Anticipado', 1),
(49, 1, 30, 0.00, 3955128.10, 'Saldo Inicial - Documentos por Pagar', 1),
(50, 1, 42, 0.00, 6220347.42, 'Saldo Inicial - Capital Social', 1),
(51, 1, 48, 0.00, 341928338.90, 'Saldo Inicial - Ventas', 1),
(52, 1, 54, 314574071.70, 0.00, 'Saldo Inicial - Costo de Ventas', 1),
(53, 1, 60, 3920514.23, 0.00, 'Saldo Inicial - Gastos de Administraci?n', 1),
(54, 1, 61, 30508.00, 0.00, 'Saldo Inicial - Gastos de Venta', 1),
(55, 1, 63, 2307264.84, 0.00, 'Saldo Inicial - Gastos Por Sueldos, Salarios Y Compesaciones', 1),
(56, 1, 64, 241720.38, 0.00, 'Saldo Inicial - Servicios Profesionales, Tecnicos Y Otros Oficios', 1),
(57, 1, 65, 54862.00, 0.00, 'Saldo Inicial - Gasto Por Depreciaci?n', 1),
(58, 2, 3, 10.00, 0.00, 'Asiento Prueba', 1),
(59, 2, 3, 0.00, 10.00, 'Asiento Prueba', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_ledger`
--

DROP TABLE IF EXISTS `tb_ledger`;\n\nCREATE TABLE `tb_ledger` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `period` varchar(20) DEFAULT NULL,
  `debit` decimal(14,2) DEFAULT 0.00,
  `credit` decimal(14,2) DEFAULT 0.00,
  `balance` decimal(14,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_ledger`
--

INSERT IGNORE INTO `tb_ledger` (`id`, `account_id`, `period`, `debit`, `credit`, `balance`) VALUES
(1, 2, '2026-01', 100.00, 0.00, 100.00),
(2, 73, '2026-01', 0.00, 100.00, -100.00),
(3, 3, '2026-01', 20.00, 20.00, -10.00),
(7, 2, '2025-08', 0.00, 0.00, -4720.93),
(8, 73, '2025-08', 0.00, 0.00, 306528.89),
(9, 4, '2025-08', 0.00, 0.00, 0.00),
(10, 74, '2025-08', 0.00, 0.00, -198181.48),
(11, 75, '2025-08', 0.00, 0.00, -13912.71),
(12, 76, '2025-08', 0.00, 0.00, 762.52),
(13, 78, '2025-08', 0.00, 0.00, 3502.93),
(14, 80, '2025-08', 0.00, 0.00, -9522.32),
(15, 81, '2025-08', 0.00, 0.00, -17752.71),
(16, 12, '2025-08', 0.00, 0.00, 1815.92),
(17, 13, '2025-08', 0.00, 0.00, -11098.35),
(18, 14, '2025-08', 0.00, 0.00, -20851.28),
(19, 16, '2025-08', 0.00, 0.00, -5784.04),
(20, 19, '2025-08', 0.00, 0.00, 314.76),
(21, 84, '2025-08', 0.00, 0.00, 0.00),
(22, 85, '2025-08', 0.00, 0.00, 441.76),
(23, 23, '2025-08', 0.00, 0.00, 0.00),
(24, 87, '2025-08', 0.00, 0.00, -308.14),
(25, 24, '2025-08', 0.00, 0.00, 4728.58),
(26, 88, '2025-08', 0.00, 0.00, 0.00),
(27, 89, '2025-08', 0.00, 0.00, 248.22),
(28, 90, '2025-08', 0.00, 0.00, 16217.59),
(29, 27, '2025-08', 0.00, 0.00, 13424.95),
(30, 91, '2025-08', 0.00, 0.00, 16217.59),
(31, 28, '2025-08', 0.00, 0.00, 0.00),
(32, 93, '2025-08', 0.00, 0.00, 966.78),
(33, 94, '2025-08', 0.00, 0.00, 89.92),
(34, 95, '2025-08', 0.00, 0.00, 2136.30),
(35, 34, '2025-08', 0.00, 0.00, -85264.70),
(36, 35, '2025-08', 0.00, 0.00, 493393.08),
(37, 97, '2025-08', 0.00, 0.00, 4184.45),
(38, 36, '2025-08', 0.00, 0.00, 14222.14),
(39, 100, '2025-08', 0.00, 0.00, -762.52),
(40, 101, '2025-08', 0.00, 0.00, -659.24),
(41, 41, '2025-08', 0.00, 0.00, -247317.31),
(42, 44, '2025-08', 0.00, 0.00, -10487.09),
(43, 46, '2025-08', 0.00, 0.00, -2860.00),
(44, 47, '2025-08', 0.00, 0.00, -16217.57),
(45, 48, '2025-08', 0.00, 0.00, -16217.57),
(46, 102, '2025-08', 0.00, 0.00, -16217.57),
(47, 50, '2025-08', 0.00, 0.00, -2136.30),
(48, 103, '2025-08', 0.00, 0.00, -9795.92),
(49, 51, '2025-08', 0.00, 0.00, -56028.42),
(50, 52, '2025-08', 0.00, 0.00, -5211.93),
(51, 104, '2025-08', 0.00, 0.00, -1636.00),
(52, 107, '2025-08', 0.00, 0.00, -3662.43),
(53, 54, '2025-08', 0.00, 0.00, -68815.76),
(54, 108, '2025-08', 0.00, 0.00, -9138.64),
(55, 111, '2025-08', 0.00, 0.00, -15047.47),
(56, 56, '2025-08', 0.00, 0.00, -5764.66),
(57, 57, '2025-08', 0.00, 0.00, -6011.39),
(58, 112, '2025-08', 0.00, 0.00, -1840.56),
(59, 113, '2025-08', 0.00, 0.00, -2527.08),
(60, 58, '2025-08', 0.00, 0.00, -1661.56),
(61, 114, '2025-08', 0.00, 0.00, -291.98),
(62, 59, '2025-08', 0.00, 0.00, -1640.68),
(63, 60, '2025-08', 0.00, 0.00, -34063.53),
(64, 115, '2025-08', 0.00, 0.00, -2321.42),
(65, 62, '2025-08', 0.00, 0.00, -3317.70),
(66, 63, '2025-08', 0.00, 0.00, -5826.04),
(67, 64, '2025-08', 0.00, 0.00, -26030.84),
(68, 116, '2025-08', 0.00, 0.00, -1732.96),
(69, 65, '2025-08', 0.00, 0.00, -13936.08),
(70, 68, '2025-08', 0.00, 0.00, -2168.00),
(71, 70, '2025-08', 0.00, 0.00, -1948.93),
(72, 119, '2025-08', 0.00, 0.00, -3769.27),
(73, 120, '2025-08', 0.00, 0.00, 85264.70),
(74, 2, '2025-07', 0.00, 0.00, 46340.47),
(75, 73, '2025-07', 0.00, 0.00, -416718.21),
(76, 4, '2025-07', 0.00, 0.00, 0.00),
(77, 74, '2025-07', 0.00, 0.00, 303889.21),
(78, 75, '2025-07', 0.00, 0.00, 17061.19),
(79, 76, '2025-07', 0.00, 0.00, 8721.59),
(80, 78, '2025-07', 0.00, 0.00, -7005.85),
(81, 80, '2025-07', 0.00, 0.00, -10987.29),
(82, 81, '2025-07', 0.00, 0.00, 7414.29),
(83, 12, '2025-07', 0.00, 0.00, -9118.00),
(84, 13, '2025-07', 0.00, 0.00, -2730.34),
(85, 14, '2025-07', 0.00, 0.00, 1962.61),
(86, 16, '2025-07', 0.00, 0.00, -5342.28),
(87, 19, '2025-07', 0.00, 0.00, -1840.90),
(88, 84, '2025-07', 0.00, 0.00, 0.02),
(89, 85, '2025-07', 0.00, 0.00, -117.47),
(90, 23, '2025-07', 0.00, 0.00, 0.00),
(91, 87, '2025-07', 0.00, 0.00, -117.47),
(92, 24, '2025-07', 0.00, 0.00, -1195.22),
(93, 88, '2025-07', 0.00, 0.00, 0.01),
(94, 89, '2025-07', 0.00, 0.00, -4532.05),
(95, 90, '2025-07', 0.00, 0.00, 15843.01),
(96, 27, '2025-07', 0.00, 0.00, 12029.58),
(97, 91, '2025-07', 0.00, 0.00, 15843.01),
(98, 93, '2025-07', 0.00, 0.00, -5654.15),
(99, 94, '2025-07', 0.00, 0.00, -525.98),
(100, 95, '2025-07', 0.00, 0.00, 2136.30),
(101, 34, '2025-07', 0.00, 0.00, 34643.96),
(102, 35, '2025-07', 0.00, 0.00, 530453.47),
(103, 97, '2025-07', 0.00, 0.00, 7895.93),
(104, 36, '2025-07', 0.00, 0.00, 25091.67),
(105, 100, '2025-07', 0.00, 0.00, -8721.59),
(106, 101, '2025-07', 0.00, 0.00, -659.24),
(107, 41, '2025-07', 0.00, 0.00, -239638.69),
(108, 44, '2025-07', 0.00, 0.00, -12648.34),
(109, 46, '2025-07', 0.00, 0.00, -3600.00),
(110, 47, '2025-07', 0.00, 0.00, -15843.01),
(111, 48, '2025-07', 0.00, 0.00, -15843.01),
(112, 102, '2025-07', 0.00, 0.00, -15843.01),
(113, 50, '2025-07', 0.00, 0.00, -2136.30),
(114, 103, '2025-07', 0.00, 0.00, -9795.92),
(115, 51, '2025-07', 0.00, 0.00, -55061.62),
(116, 52, '2025-07', 0.00, 0.00, -5121.97),
(117, 104, '2025-07', 0.00, 0.00, -482.00),
(118, 107, '2025-07', 0.00, 0.00, -3662.43),
(119, 54, '2025-07', 0.00, 0.00, -32191.46),
(120, 108, '2025-07', 0.00, 0.00, -936.10),
(121, 111, '2025-07', 0.00, 0.00, -14671.05),
(122, 56, '2025-07', 0.00, 0.00, -3845.55),
(123, 57, '2025-07', 0.00, 0.00, -5596.90),
(124, 113, '2025-07', 0.00, 0.00, -2527.08),
(125, 58, '2025-07', 0.00, 0.00, -5539.59),
(126, 114, '2025-07', 0.00, 0.00, -497.54),
(127, 59, '2025-07', 0.00, 0.00, -1638.53),
(128, 60, '2025-07', 0.00, 0.00, -34063.53),
(129, 115, '2025-07', 0.00, 0.00, -2013.08),
(130, 62, '2025-07', 0.00, 0.00, -2325.79),
(131, 63, '2025-07', 0.00, 0.00, -6092.28),
(132, 64, '2025-07', 0.00, 0.00, -9783.85),
(133, 116, '2025-07', 0.00, 0.00, -2636.54),
(134, 65, '2025-07', 0.00, 0.00, -9245.92),
(135, 68, '2025-07', 0.00, 0.00, -420.00),
(136, 70, '2025-07', 0.00, 0.00, -2212.27),
(137, 119, '2025-07', 0.00, 0.00, -3502.92),
(138, 120, '2025-07', 0.00, 0.00, -34643.96),
(139, 2, '2025-06', 0.00, 0.00, 25745.39),
(140, 73, '2025-06', 0.00, 0.00, -252440.62),
(141, 4, '2025-06', 0.00, 0.00, 0.00),
(142, 74, '2025-06', 0.00, 0.00, 165316.87),
(143, 75, '2025-06', 0.00, 0.00, 18853.64),
(144, 76, '2025-06', 0.00, 0.00, 4117.32),
(145, 77, '2025-06', 0.00, 0.00, -11619.06),
(146, 78, '2025-06', 0.00, 0.00, 3502.93),
(147, 81, '2025-06', 0.00, 0.00, 4514.00),
(148, 12, '2025-06', 0.00, 0.00, 2192.00),
(149, 13, '2025-06', 0.00, 0.00, 2030.82),
(150, 14, '2025-06', 0.00, 0.00, 1962.61),
(151, 16, '2025-06', 0.00, 0.00, -5459.75),
(152, 83, '2025-06', 0.00, 0.00, -24342.53),
(153, 19, '2025-06', 0.00, 0.00, 2290.29),
(154, 84, '2025-06', 0.00, 0.00, 0.00),
(155, 20, '2025-06', 0.00, 0.00, -5424.00),
(156, 85, '2025-06', 0.00, 0.00, 273.96),
(157, 23, '2025-06', 0.00, 0.00, 0.00),
(158, 87, '2025-06', 0.00, 0.00, 273.96),
(159, 24, '2025-06', 0.00, 0.00, 93.85),
(160, 88, '2025-06', 0.00, 0.00, -0.01),
(161, 89, '2025-06', 0.00, 0.00, 5425.09),
(162, 90, '2025-06', 0.00, 0.00, 19099.39),
(163, 27, '2025-06', 0.00, 0.00, -33617.60),
(164, 91, '2025-06', 0.00, 0.00, 12439.33),
(165, 92, '2025-06', 0.00, 0.00, 0.00),
(166, 93, '2025-06', 0.00, 0.00, 7034.51),
(167, 94, '2025-06', 0.00, 0.00, 654.37),
(168, 95, '2025-06', 0.00, 0.00, 2136.30),
(169, 34, '2025-06', 0.00, 0.00, 54946.96),
(170, 35, '2025-06', 0.00, 0.00, 538283.31),
(171, 97, '2025-06', 0.00, 0.00, 2297.16),
(172, 36, '2025-06', 0.00, 0.00, 43748.76),
(173, 100, '2025-06', 0.00, 0.00, -4117.32),
(174, 101, '2025-06', 0.00, 0.00, -659.24),
(175, 41, '2025-06', 0.00, 0.00, -223629.57),
(176, 44, '2025-06', 0.00, 0.00, -16009.01),
(177, 46, '2025-06', 0.00, 0.00, -2460.00),
(178, 47, '2025-06', 0.00, 0.00, -15352.85),
(179, 48, '2025-06', 0.00, 0.00, -9975.71),
(180, 102, '2025-06', 0.00, 0.00, -15352.85),
(181, 50, '2025-06', 0.00, 0.00, -3666.10),
(182, 103, '2025-06', 0.00, 0.00, -9795.92),
(183, 51, '2025-06', 0.00, 0.00, -60715.81),
(184, 52, '2025-06', 0.00, 0.00, -5647.98),
(185, 104, '2025-06', 0.00, 0.00, -1249.50),
(186, 107, '2025-06', 0.00, 0.00, -3662.43),
(187, 54, '2025-06', 0.00, 0.00, -43911.24),
(188, 108, '2025-06', 0.00, 0.00, -936.10),
(189, 111, '2025-06', 0.00, 0.00, -10373.38),
(190, 56, '2025-06', 0.00, 0.00, -3845.55),
(191, 57, '2025-06', 0.00, 0.00, -5596.90),
(192, 112, '2025-06', 0.00, 0.00, -1020.94),
(193, 113, '2025-06', 0.00, 0.00, -2527.08),
(194, 58, '2025-06', 0.00, 0.00, -6226.13),
(195, 114, '2025-06', 0.00, 0.00, -885.82),
(196, 59, '2025-06', 0.00, 0.00, -1638.53),
(197, 60, '2025-06', 0.00, 0.00, -34063.52),
(198, 115, '2025-06', 0.00, 0.00, -2013.08),
(199, 62, '2025-06', 0.00, 0.00, -2325.79),
(200, 63, '2025-06', 0.00, 0.00, -6209.75),
(201, 64, '2025-06', 0.00, 0.00, -14954.03),
(202, 116, '2025-06', 0.00, 0.00, -1846.54),
(203, 65, '2025-06', 0.00, 0.00, -7910.57),
(204, 121, '2025-06', 0.00, 0.00, -6317.69),
(205, 68, '2025-06', 0.00, 0.00, -200.00),
(206, 70, '2025-06', 0.00, 0.00, -782.42),
(207, 119, '2025-06', 0.00, 0.00, -3502.93),
(208, 120, '2025-06', 0.00, 0.00, -54946.96),
(209, 1, '2025-05', 0.00, 0.00, 115107.61),
(210, 2, '2025-05', 0.00, 0.00, -103069.20),
(211, 3, '2025-05', 0.00, 0.00, 2364584.94),
(212, 73, '2025-05', 0.00, 0.00, -2483284.59),
(213, 4, '2025-05', 0.00, 0.00, 0.00),
(214, 5, '2025-05', 0.00, 0.00, 11571278.84),
(215, 74, '2025-05', 0.00, 0.00, -11492757.85),
(216, 6, '2025-05', 0.00, 0.00, 39443.89),
(217, 75, '2025-05', 0.00, 0.00, -43090.95),
(218, 7, '2025-05', 0.00, 0.00, -885250.47),
(219, 76, '2025-05', 0.00, 0.00, 856618.49),
(220, 8, '2025-05', 0.00, 0.00, 21876.31),
(221, 77, '2025-05', 0.00, 0.00, -21876.31),
(222, 10, '2025-05', 0.00, 0.00, 237775.56),
(223, 78, '2025-05', 0.00, 0.00, -3502.92),
(224, 79, '2025-05', 0.00, 0.00, -10987.29),
(225, 80, '2025-05', 0.00, 0.00, -219782.42),
(226, 11, '2025-05', 0.00, 0.00, 68870.69),
(227, 81, '2025-05', 0.00, 0.00, -66658.53),
(228, 12, '2025-05', 0.00, 0.00, 17620.97),
(229, 13, '2025-05', 0.00, 0.00, -34491.13),
(230, 14, '2025-05', 0.00, 0.00, 19170.57),
(231, 16, '2025-05', 0.00, 0.00, -10718.07),
(232, 17, '2025-05', 0.00, 0.00, 385.00),
(233, 82, '2025-05', 0.00, 0.00, -385.00),
(234, 83, '2025-05', 0.00, 0.00, 24505.66),
(235, 19, '2025-05', 0.00, 0.00, -1882.13),
(236, 84, '2025-05', 0.00, 0.00, -0.28),
(237, 20, '2025-05', 0.00, 0.00, -1661.97),
(238, 21, '2025-05', 0.00, 0.00, -5976.72),
(239, 22, '2025-05', 0.00, 0.00, 626.03),
(240, 85, '2025-05', 0.00, 0.00, 16651.26),
(241, 23, '2025-05', 0.00, 0.00, -0.07),
(242, 86, '2025-05', 0.00, 0.00, 0.07),
(243, 87, '2025-05', 0.00, 0.00, -972.52),
(244, 24, '2025-05', 0.00, 0.00, -3555.61),
(245, 88, '2025-05', 0.00, 0.00, 4368.51),
(246, 25, '2025-05', 0.00, 0.00, -24013.20),
(247, 89, '2025-05', 0.00, 0.00, 21138.61),
(248, 26, '2025-05', 0.00, 0.00, -5488.75),
(249, 90, '2025-05', 0.00, 0.00, 74090.53),
(250, 27, '2025-05', 0.00, 0.00, 9567.29),
(251, 91, '2025-05', 0.00, 0.00, 286068.70),
(252, 92, '2025-05', 0.00, 0.00, 0.00),
(253, 29, '2025-05', 0.00, 0.00, -46448.74),
(254, 93, '2025-05', 0.00, 0.00, 41212.37),
(255, 94, '2025-05', 0.00, 0.00, 6072.81),
(256, 95, '2025-05', 0.00, 0.00, 9717.74),
(257, 30, '2025-05', 0.00, 0.00, -58782.31),
(258, 31, '2025-05', 0.00, 0.00, -3751413.64),
(259, 96, '2025-05', 0.00, 0.00, 3479308.50),
(260, 34, '2025-05', 0.00, 0.00, -10010.26),
(261, 35, '2025-05', 0.00, 0.00, 562877.95),
(262, 97, '2025-05', 0.00, 0.00, 9511.80),
(263, 98, '2025-05', 0.00, 0.00, 206360.64),
(264, 36, '2025-05', 0.00, 0.00, -182668.18),
(265, 38, '2025-05', 0.00, 0.00, 3296.19),
(266, 39, '2025-05', 0.00, 0.00, 62505.59),
(267, 99, '2025-05', 0.00, 0.00, -62505.59),
(268, 100, '2025-05', 0.00, 0.00, 0.00),
(269, 101, '2025-05', 0.00, 0.00, -3955.43),
(270, 41, '2025-05', 0.00, 0.00, -394979.69),
(271, 42, '2025-05', 0.00, 0.00, 122533.09),
(272, 43, '2025-05', 0.00, 0.00, 154187.94),
(273, 44, '2025-05', 0.00, 0.00, -75563.25),
(274, 45, '2025-05', 0.00, 0.00, 11874.26),
(275, 46, '2025-05', 0.00, 0.00, -4500.00),
(276, 47, '2025-05', 0.00, 0.00, -15308.21),
(277, 48, '2025-05', 0.00, 0.00, -15308.21),
(278, 102, '2025-05', 0.00, 0.00, -76171.25),
(279, 49, '2025-05', 0.00, 0.00, 60863.04),
(280, 50, '2025-05', 0.00, 0.00, 77367.23),
(281, 103, '2025-05', 0.00, 0.00, -44081.64),
(282, 51, '2025-05', 0.00, 0.00, -53681.27),
(283, 52, '2025-05', 0.00, 0.00, 926.39),
(284, 104, '2025-05', 0.00, 0.00, -2607.00),
(285, 105, '2025-05', 0.00, 0.00, -18734.50),
(286, 106, '2025-05', 0.00, 0.00, -14832.84),
(287, 53, '2025-05', 0.00, 0.00, -3851.71),
(288, 107, '2025-05', 0.00, 0.00, -18312.14),
(289, 54, '2025-05', 0.00, 0.00, -153972.48),
(290, 108, '2025-05', 0.00, 0.00, -5299.73),
(291, 109, '2025-05', 0.00, 0.00, -18190.19),
(292, 110, '2025-05', 0.00, 0.00, -421.18),
(293, 111, '2025-05', 0.00, 0.00, -59776.79),
(294, 55, '2025-05', 0.00, 0.00, 48841.85),
(295, 56, '2025-05', 0.00, 0.00, 14344.64),
(296, 57, '2025-05', 0.00, 0.00, -5596.90),
(297, 112, '2025-05', 0.00, 0.00, -2760.84),
(298, 113, '2025-05', 0.00, 0.00, -15162.48),
(299, 114, '2025-05', 0.00, 0.00, -6461.81),
(300, 59, '2025-05', 0.00, 0.00, -1531.34),
(301, 60, '2025-05', 0.00, 0.00, -34063.53),
(302, 115, '2025-05', 0.00, 0.00, -13428.43),
(303, 61, '2025-05', 0.00, 0.00, 8851.64),
(304, 62, '2025-05', 0.00, 0.00, -2325.79),
(305, 63, '2025-05', 0.00, 0.00, -5935.79),
(306, 64, '2025-05', 0.00, 0.00, -14694.73),
(307, 116, '2025-05', 0.00, 0.00, -15698.96),
(308, 65, '2025-05', 0.00, 0.00, 10943.77),
(309, 66, '2025-05', 0.00, 0.00, -11441.45),
(310, 67, '2025-05', 0.00, 0.00, -5920.00),
(311, 117, '2025-05', 0.00, 0.00, -60692.17),
(312, 118, '2025-05', 0.00, 0.00, -49007.52),
(313, 68, '2025-05', 0.00, 0.00, 138825.07),
(314, 69, '2025-05', 0.00, 0.00, 6363.63),
(315, 70, '2025-05', 0.00, 0.00, -23527.37),
(316, 119, '2025-05', 0.00, 0.00, -17514.62),
(317, 120, '2025-05', 0.00, 0.00, -120385.65),
(318, 71, '2025-05', 0.00, 0.00, 130395.91),
(319, 1, '2025-04', 0.00, 0.00, -27032.76),
(320, 2, '2025-04', 0.00, 0.00, -0.16),
(321, 3, '2025-04', 0.00, 0.00, -634910.14),
(322, 4, '2025-04', 0.00, 0.00, 0.00),
(323, 5, '2025-04', 0.00, 0.00, 637938.56),
(324, 6, '2025-04', 0.00, 0.00, 5692.46),
(325, 7, '2025-04', 0.00, 0.00, -154192.26),
(326, 8, '2025-04', 0.00, 0.00, 0.18),
(327, 9, '2025-04', 0.00, 0.00, -5.05),
(328, 10, '2025-04', 0.00, 0.00, 8640.66),
(329, 11, '2025-04', 0.00, 0.00, 140929.28),
(330, 12, '2025-04', 0.00, 0.00, -19018.84),
(331, 13, '2025-04', 0.00, 0.00, 668.39),
(332, 14, '2025-04', 0.00, 0.00, 2472.66),
(333, 15, '2025-04', 0.00, 0.00, -1468.21),
(334, 16, '2025-04', 0.00, 0.00, -5532.28),
(335, 19, '2025-04', 0.00, 0.00, 2110.73),
(336, 20, '2025-04', 0.00, 0.00, -58812.99),
(337, 21, '2025-04', 0.00, 0.00, 283.85),
(338, 22, '2025-04', 0.00, 0.00, 283.85),
(339, 23, '2025-04', 0.00, 0.00, 0.00),
(340, 24, '2025-04', 0.00, 0.00, -178.85),
(341, 25, '2025-04', 0.00, 0.00, 978.92),
(342, 27, '2025-04', 0.00, 0.00, -3958.47),
(343, 28, '2025-04', 0.00, 0.00, 1.45),
(344, 29, '2025-04', 0.00, 0.00, -68.98),
(345, 30, '2025-04', 0.00, 0.00, 17030.06),
(346, 31, '2025-04', 0.00, 0.00, 34532.53),
(347, 34, '2025-04', 0.00, 0.00, 53615.43),
(348, 35, '2025-04', 0.00, 0.00, 548590.12),
(349, 36, '2025-04', 0.00, 0.00, 88203.21),
(350, 37, '2025-04', 0.00, 0.00, 114.99),
(351, 38, '2025-04', 0.00, 0.00, -659.24),
(352, 41, '2025-04', 0.00, 0.00, -182553.84),
(353, 42, '2025-04', 0.00, 0.00, -24641.46),
(354, 43, '2025-04', 0.00, 0.00, -35952.15),
(355, 44, '2025-04', 0.00, 0.00, -6353.58),
(356, 45, '2025-04', 0.00, 0.00, -8394.76),
(357, 46, '2025-04', 0.00, 0.00, -2420.00),
(358, 47, '2025-04', 0.00, 0.00, -15593.80),
(359, 48, '2025-04', 0.00, 0.00, -15593.80),
(360, 49, '2025-04', 0.00, 0.00, -15593.80),
(361, 50, '2025-04', 0.00, 0.00, -22588.03),
(362, 51, '2025-04', 0.00, 0.00, -54418.39),
(363, 52, '2025-04', 0.00, 0.00, -10982.18),
(364, 53, '2025-04', 0.00, 0.00, -2136.30),
(365, 54, '2025-04', 0.00, 0.00, -2527.08),
(366, 55, '2025-04', 0.00, 0.00, -9325.00),
(367, 56, '2025-04', 0.00, 0.00, -3845.55),
(368, 57, '2025-04', 0.00, 0.00, -5596.90),
(369, 58, '2025-04', 0.00, 0.00, -3000.00),
(370, 59, '2025-04', 0.00, 0.00, -1639.01),
(371, 60, '2025-04', 0.00, 0.00, -34063.53),
(372, 61, '2025-04', 0.00, 0.00, -2013.08),
(373, 62, '2025-04', 0.00, 0.00, -2325.79),
(374, 63, '2025-04', 0.00, 0.00, -6324.28),
(375, 64, '2025-04', 0.00, 0.00, -15005.05),
(376, 65, '2025-04', 0.00, 0.00, -2042.71),
(377, 66, '2025-04', 0.00, 0.00, -10861.67),
(378, 68, '2025-04', 0.00, 0.00, -83843.24),
(379, 69, '2025-04', 0.00, 0.00, -1674.35),
(380, 70, '2025-04', 0.00, 0.00, -1324.34),
(381, 71, '2025-04', 0.00, 0.00, -53615.43),
(382, 1, '2025-03', 0.00, 0.00, -88074.85),
(383, 2, '2025-03', 0.00, 0.00, 0.15),
(384, 3, '2025-03', 0.00, 0.00, -1729674.80),
(385, 4, '2025-03', 0.00, 0.00, -7630.06),
(386, 5, '2025-03', 0.00, 0.00, -12209217.40),
(387, 6, '2025-03', 0.00, 0.00, -45136.35),
(388, 7, '2025-03', 0.00, 0.00, 1039442.73),
(389, 8, '2025-03', 0.00, 0.00, -21876.49),
(390, 9, '2025-03', 0.00, 0.00, 5.05),
(391, 10, '2025-03', 0.00, 0.00, -246416.22),
(392, 11, '2025-03', 0.00, 0.00, -209799.97),
(393, 12, '2025-03', 0.00, 0.00, -83383.49),
(394, 13, '2025-03', 0.00, 0.00, -9415.49),
(395, 14, '2025-03', 0.00, 0.00, -67225.72),
(396, 15, '2025-03', 0.00, 0.00, 1468.21),
(397, 16, '2025-03', 0.00, 0.00, -28134.42),
(398, 17, '2025-03', 0.00, 0.00, -385.00),
(399, 18, '2025-03', 0.00, 0.00, -25824.48),
(400, 19, '2025-03', 0.00, 0.00, -14952.29),
(401, 20, '2025-03', 0.00, 0.00, -69341.84),
(402, 21, '2025-03', 0.00, 0.00, -11181.62),
(403, 22, '2025-03', 0.00, 0.00, 909.88),
(404, 23, '2025-03', 0.00, 0.00, -749.87),
(405, 24, '2025-03', 0.00, 0.00, -19141.02),
(406, 25, '2025-03', 0.00, 0.00, -23034.28),
(407, 26, '2025-03', 0.00, 0.00, -5488.75),
(408, 27, '2025-03', 0.00, 0.00, -194122.86),
(409, 28, '2025-03', 0.00, 0.00, 1.45),
(410, 29, '2025-03', 0.00, 0.00, -46517.72),
(411, 30, '2025-03', 0.00, 0.00, -41752.25),
(412, 31, '2025-03', 0.00, 0.00, -3716881.11),
(413, 32, '2025-03', 0.00, 0.00, -4500000.00),
(414, 33, '2025-03', 0.00, 0.00, -4026425.24),
(415, 34, '2025-03', 0.00, 0.00, -1062601.11),
(416, 72, '2025-03', 0.00, 0.00, 27462557.23),
(419, 1, '2026-01', 0.00, 0.00, -100.00),
(841, 3, '2025-01', -35869.00, 0.00, -35869.00),
(842, 7, '2025-01', -5885801.39, 0.00, -5885801.39),
(843, 10, '2025-01', -24168264.79, 0.00, -24168264.79),
(844, 11, '2025-01', -1200.00, 0.00, -1200.00),
(845, 16, '2025-01', -589000.00, 0.00, -589000.00),
(846, 17, '2025-01', -71000.00, 0.00, -71000.00),
(847, 18, '2025-01', -158600.00, 0.00, -158600.00),
(848, 19, '2025-01', 0.00, -54862.00, 54862.00),
(849, 25, '2025-01', -20000.00, 0.00, -20000.00),
(850, 26, '2025-01', -100000.00, 0.00, -100000.00),
(851, 30, '2025-01', 0.00, -3955128.10, 3955128.10),
(852, 42, '2025-01', 0.00, -6220347.42, 6220347.42),
(853, 48, '2025-01', 0.00, -341928338.90, 341928338.90),
(854, 54, '2025-01', -314574071.70, 0.00, -314574071.70),
(855, 60, '2025-01', -3920514.23, 0.00, -3920514.23),
(856, 61, '2025-01', -30508.00, 0.00, -30508.00),
(857, 63, '2025-01', -2307264.84, 0.00, -2307264.84),
(858, 64, '2025-01', -241720.38, 0.00, -241720.38),
(859, 65, '2025-01', -54862.00, 0.00, -54862.00),
(860, 3, '2025-12', 35869.00, 0.00, 35869.00),
(861, 7, '2025-12', 5885801.39, 0.00, 5885801.39),
(862, 10, '2025-12', 24168264.79, 0.00, 24168264.79),
(863, 11, '2025-12', 1200.00, 0.00, 1200.00),
(864, 16, '2025-12', 589000.00, 0.00, 589000.00),
(865, 17, '2025-12', 71000.00, 0.00, 71000.00),
(866, 18, '2025-12', 158600.00, 0.00, 158600.00),
(867, 19, '2025-12', 0.00, 54862.00, -54862.00),
(868, 25, '2025-12', 20000.00, 0.00, 20000.00),
(869, 26, '2025-12', 100000.00, 0.00, 100000.00),
(870, 30, '2025-12', 0.00, 3955128.10, -3955128.10),
(871, 42, '2025-12', 0.00, 6220347.42, -6220347.42),
(872, 48, '2025-12', 0.00, 341928338.90, -341928338.90),
(873, 54, '2025-12', 314574071.70, 0.00, 314574071.70),
(874, 60, '2025-12', 3920514.23, 0.00, 3920514.23),
(875, 61, '2025-12', 30508.00, 0.00, 30508.00),
(876, 63, '2025-12', 2307264.84, 0.00, 2307264.84),
(877, 64, '2025-12', 241720.38, 0.00, 241720.38),
(878, 65, '2025-12', 54862.00, 0.00, 54862.00);

-- --------------------------------------------------------

--
-- Table structure for table `tb_monedas`
--

DROP TABLE IF EXISTS `tb_monedas`;\n\nCREATE TABLE `tb_monedas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) DEFAULT NULL,
  `simbolo` varchar(6) DEFAULT NULL,
  `estado` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_monedas`
--

INSERT IGNORE INTO `tb_monedas` (`id`, `nombre`, `simbolo`, `estado`) VALUES
(1, 'CORDOBAS', 'C$', 1),
(3, 'DOLARES', '$', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_pagos`
--

DROP TABLE IF EXISTS `tb_pagos`;\n\nCREATE TABLE `tb_pagos` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_pagos_detalle`
--

DROP TABLE IF EXISTS `tb_pagos_detalle`;\n\nCREATE TABLE `tb_pagos_detalle` (
  `pdid` int(11) NOT NULL,
  `idpago` int(11) DEFAULT NULL,
  `idcuota` int(11) DEFAULT NULL,
  `monto_pagado` decimal(18,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_perfil_integral_cliente`
--

DROP TABLE IF EXISTS `tb_perfil_integral_cliente`;\n\nCREATE TABLE `tb_perfil_integral_cliente` (
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
  `actividad_esperada_observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_perfil_integral_cliente`
--

INSERT IGNORE INTO `tb_perfil_integral_cliente` (`id`, `solicitud_id`, `nombre`, `primer_apellido`, `segundo_apellido`, `tipo_documento`, `numero_documento`, `fecha_nacimiento`, `telefono`, `celular`, `email`, `direccion`, `ciudad`, `estado_civil`, `ocupacion`, `empresa`, `ingreso_mensual`, `antiguedad_laboral`, `otros`, `created_at`, `updated_at`, `fecha_perfil`, `nivel_riesgo`, `tipo_ddc`, `en_su_propio_pais`, `es_funcionario_publico`, `cargo_funcionario`, `pais_emision_documento`, `categoria_otro`, `zona_cobertura`, `sitio_web_centro_trabajo`, `ingreso_mensual_usd`, `ingreso_mensual_cordobas`, `conyuge_profesion`, `conyuge_ocupacion_actual`, `conyuge_nombre_centro_trabajo`, `conyuge_direccion_centro_trabajo`, `conyuge_email_centro_trabajo`, `conyuge_sitio_web`, `conyuge_telefono_centro_trabajo`, `conyuge_fax_centro_trabajo`, `conyuge_apartado_postal`, `conyuge_ingreso_usd`, `conyuge_ingreso_cordobas`, `documento_legal_1_pais_emision`, `documento_legal_2_pais_emision`, `actividad_esperada_json`, `segundo_nombre`, `sexo`, `n_dependientes`, `nombre_conocido`, `pais_nacimiento`, `categoria_empleo`, `origen_fondos`, `proposito_relacion`, `actividad_esperada`, `conyuge_primer_nombre`, `conyuge_segundo_nombre`, `conyuge_primer_apellido`, `conyuge_segundo_apellido`, `conyuge_direccion`, `conyuge_telefono_domicilio`, `conyuge_celular`, `conyuge_email_personal`, `doc1_tipo`, `doc1_numero`, `doc1_registro`, `doc1_fecha_emision`, `doc1_vencimiento`, `doc2_tipo`, `doc2_numero`, `doc2_registro`, `doc2_fecha_emision`, `doc2_vencimiento`, `tipo_relacion`, `tipo_relacion_otro`, `origen_otros`, `numero_registro`, `fecha_emision_documento`, `fecha_vencimiento_documento`, `documento_legal_1_numero`, `documento_legal_1_fecha_emision`, `documento_legal_1_fecha_vencimiento`, `documento_legal_2_numero`, `documento_legal_2_fecha_emision`, `documento_legal_2_fecha_vencimiento`, `matriz_score`, `matriz_answers`, `actividad_esperada_observaciones`) VALUES
(1, 4, 'Denis', 'VÃ¡squez', 'AlemÃ¡n', NULL, '0010806670057W', '1967-06-08', '81112991', '81112991', NULL, 'Bo.Jonathan Gonzalez. Bancentro 22 c 1/2 al sur', 'Bo.Jonathan Gonzalez. Bancentro 22 c 1/2 al sur', 'Soltero', NULL, NULL, NULL, NULL, NULL, '2026-01-07 17:24:09', '2026-01-07 17:29:27', '2026-01-06', 'Alto', NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[{\"numero_transacciones\":\"\",\"monto_promedio\":\"\",\"periodo\":\"\"}]', 'Ramon', NULL, '1', NULL, NULL, 'Negocio propio', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Bo.Jonathan Gonzalez. Bancentro 22 c 1/2 al sur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '525', '[\"tipo_natural\",\"propietario\",\"agricultura_ganaderia\",\"edad_21_39\",\"pep_si\",\"frecuente_si\",\"zona_managua\",\"valor_usd_2000_5000\"]', NULL),
(2, 5, 'juan mario vega', 'osorio', NULL, NULL, '0010101010000X', '2005-02-01', '88888888', '88888888', NULL, 'Bo batahola sur q', NULL, 'Soltero', 'Staff', 'EY', 13500.00, '2', NULL, '2026-01-07 20:47:25', '2026-01-07 20:47:34', NULL, 'Alto', 'DDC-I', 0, 0, NULL, NULL, NULL, NULL, NULL, 272.93, 10000.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[{\"numero_transacciones\":\"12\",\"monto_promedio\":\"114.29\",\"periodo\":\"12 meses\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '500', '[\"tipo_natural\",\"propietario\",\"agricultura_ganaderia\",\"garantia_hipotecaria\",\"edad_21_39\",\"pep_si\",\"frecuente_si\",\"zona_managua\",\"valor_usd_500_1000\"]', NULL),
(3, 6, 'AlemÃ¡n Denis Ramon', 'VÃ¡squez', NULL, 'Cedula Identidad', '0010806670057W', '1967-06-08', '81112991', '81112991', NULL, 'Bo.Jonathan Gonzalez. Bancentro 22 c 1/2 al sur', NULL, 'Soltero', NULL, NULL, 6200.00, NULL, NULL, '2026-01-07 22:16:09', '2026-01-07 22:19:47', NULL, 'Medio', 'DDC-S', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[{\"numero_transacciones\":\"20\",\"monto_promedio\":\"35.36\",\"periodo\":\"10 meses\"}]', NULL, NULL, '1', NULL, NULL, 'Negocio propio', '[\"Ahorro\"]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[\"Otorgamiento de microcr\\u00e9ditos a personas naturales y jur\\u00eddicas\"]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '425', '[\"tipo_natural\",\"negocio_propio\",\"comercio_servicios\",\"garantia_inmobiliaria\",\"edad_mayor_56\",\"pep_no\",\"frecuente_si\",\"zona_managua\",\"valor_usd_100_500\"]', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_prestamos`
--

DROP TABLE IF EXISTS `tb_prestamos`;\n\nCREATE TABLE `tb_prestamos` (
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
  `promotor` varchar(255) DEFAULT NULL,
  `tipo_cuota` varchar(50) DEFAULT NULL,
  `fecha_desembolso` date DEFAULT NULL,
  `primer_dia_pago` date DEFAULT NULL,
  `saldo_inicial` decimal(14,2) DEFAULT NULL,
  `pdf_printed_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tb_prestamos`
--

INSERT IGNORE INTO `tb_prestamos` (`idprestamo`, `idsolicitud`, `monto_credito`, `monto_desembolsado`, `interes_credito`, `comision_desembolso`, `numero_coutas`, `forma_pago`, `fecha_credito`, `estado`, `created_at`, `interes_corriente_anual`, `interes_moratorio`, `idasesor`, `promotor`, `tipo_cuota`, `fecha_desembolso`, `primer_dia_pago`, `saldo_inicial`, `pdf_printed_count`) VALUES
(1, 6, 500.00, 465.00, 0.060000, 0.0700, 20, 1, '2026-02-02', 0, '2026-01-07 17:36:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(2, 6, 500.00, 465.00, 0.060000, 0.0700, 20, 1, '2026-02-02', 0, '2026-01-07 17:38:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(3, 6, 500.00, 465.00, 0.060000, 0.0700, 20, 1, '2026-02-02', 0, '2026-01-07 17:40:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(4, 6, 500.00, 465.00, 0.060000, 0.0700, 20, 1, '2026-02-02', 0, '2026-01-08 16:36:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2),
(5, 6, 500.00, 465.00, 0.060000, 0.0700, 20, 1, '2026-02-02', 0, '2026-01-09 16:49:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_prestamo_cuotas`
--

DROP TABLE IF EXISTS `tb_prestamo_cuotas`;\n\nCREATE TABLE `tb_prestamo_cuotas` (
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
  `comision` decimal(12,4) NOT NULL DEFAULT 0.0000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tb_prestamo_cuotas`
--

INSERT IGNORE INTO `tb_prestamo_cuotas` (`idcuota`, `idprestamo`, `numero`, `fecha_vencimiento`, `dias`, `principal`, `interes`, `cuota`, `saldo`, `created_at`, `comision`) VALUES
(1, 2, 1, '2026-02-02', 0, 18.61, 15.00, 35.36, 481.39, '2026-01-07 17:38:33', 1.7500),
(2, 2, 2, '2026-02-17', 0, 19.17, 14.44, 35.36, 462.22, '2026-01-07 17:38:33', 1.7500),
(3, 2, 3, '2026-03-02', 0, 19.74, 13.87, 35.36, 442.48, '2026-01-07 17:38:33', 1.7500),
(4, 2, 4, '2026-03-17', 0, 20.33, 13.27, 35.35, 422.15, '2026-01-07 17:38:33', 1.7500),
(5, 2, 5, '2026-04-02', 0, 20.94, 12.66, 35.35, 401.21, '2026-01-07 17:38:33', 1.7500),
(6, 2, 6, '2026-04-21', 0, 21.57, 12.04, 35.36, 379.64, '2026-01-07 17:38:33', 1.7500),
(7, 2, 7, '2026-05-02', 0, 22.22, 11.39, 35.36, 357.42, '2026-01-07 17:38:33', 1.7500),
(8, 2, 8, '2026-05-18', 0, 22.89, 10.72, 35.36, 334.53, '2026-01-07 17:38:33', 1.7500),
(9, 2, 9, '2026-06-02', 0, 23.57, 10.04, 35.36, 310.96, '2026-01-07 17:38:33', 1.7500),
(10, 2, 10, '2026-06-17', 0, 24.28, 9.33, 35.36, 286.68, '2026-01-07 17:38:33', 1.7500),
(11, 2, 11, '2026-07-02', 0, 25.01, 8.60, 35.36, 261.67, '2026-01-07 17:38:33', 1.7500),
(12, 2, 12, '2026-07-17', 0, 25.76, 7.85, 35.36, 235.91, '2026-01-07 17:38:33', 1.7500),
(13, 2, 13, '2026-08-03', 0, 26.53, 7.08, 35.36, 209.38, '2026-01-07 17:38:33', 1.7500),
(14, 2, 14, '2026-08-17', 0, 27.33, 6.28, 35.36, 182.05, '2026-01-07 17:38:33', 1.7500),
(15, 2, 15, '2026-09-02', 0, 28.15, 5.46, 35.36, 153.90, '2026-01-07 17:38:33', 1.7500),
(16, 2, 16, '2026-09-17', 0, 28.99, 4.62, 35.36, 124.91, '2026-01-07 17:38:33', 1.7500),
(17, 2, 17, '2026-10-02', 0, 29.86, 3.75, 35.36, 95.05, '2026-01-07 17:38:33', 1.7500),
(18, 2, 18, '2026-10-17', 0, 30.76, 2.85, 35.36, 64.29, '2026-01-07 17:38:33', 1.7500),
(19, 2, 19, '2026-11-02', 0, 31.68, 1.93, 35.36, 32.61, '2026-01-07 17:38:33', 1.7500),
(20, 2, 20, '2026-11-17', 0, 32.61, 0.98, 35.34, 0.00, '2026-01-07 17:38:33', 1.7500),
(21, 3, 1, '2026-02-02', 0, 18.61, 15.00, 35.36, 481.39, '2026-01-07 17:40:48', 1.7500),
(22, 3, 2, '2026-02-17', 0, 19.17, 14.44, 35.36, 462.22, '2026-01-07 17:40:48', 1.7500),
(23, 3, 3, '2026-03-02', 0, 19.74, 13.87, 35.36, 442.48, '2026-01-07 17:40:48', 1.7500),
(24, 3, 4, '2026-03-17', 0, 20.33, 13.27, 35.35, 422.15, '2026-01-07 17:40:48', 1.7500),
(25, 3, 5, '2026-04-02', 0, 20.94, 12.66, 35.35, 401.21, '2026-01-07 17:40:48', 1.7500),
(26, 3, 6, '2026-04-21', 0, 21.57, 12.04, 35.36, 379.64, '2026-01-07 17:40:48', 1.7500),
(27, 3, 7, '2026-05-02', 0, 22.22, 11.39, 35.36, 357.42, '2026-01-07 17:40:48', 1.7500),
(28, 3, 8, '2026-05-18', 0, 22.89, 10.72, 35.36, 334.53, '2026-01-07 17:40:48', 1.7500),
(29, 3, 9, '2026-06-02', 0, 23.57, 10.04, 35.36, 310.96, '2026-01-07 17:40:48', 1.7500),
(30, 3, 10, '2026-06-17', 0, 24.28, 9.33, 35.36, 286.68, '2026-01-07 17:40:48', 1.7500),
(31, 3, 11, '2026-07-02', 0, 25.01, 8.60, 35.36, 261.67, '2026-01-07 17:40:48', 1.7500),
(32, 3, 12, '2026-07-17', 0, 25.76, 7.85, 35.36, 235.91, '2026-01-07 17:40:48', 1.7500),
(33, 3, 13, '2026-08-03', 0, 26.53, 7.08, 35.36, 209.38, '2026-01-07 17:40:48', 1.7500),
(34, 3, 14, '2026-08-17', 0, 27.33, 6.28, 35.36, 182.05, '2026-01-07 17:40:48', 1.7500),
(35, 3, 15, '2026-09-02', 0, 28.15, 5.46, 35.36, 153.90, '2026-01-07 17:40:48', 1.7500),
(36, 3, 16, '2026-09-17', 0, 28.99, 4.62, 35.36, 124.91, '2026-01-07 17:40:48', 1.7500),
(37, 3, 17, '2026-10-02', 0, 29.86, 3.75, 35.36, 95.05, '2026-01-07 17:40:48', 1.7500),
(38, 3, 18, '2026-10-17', 0, 30.76, 2.85, 35.36, 64.29, '2026-01-07 17:40:48', 1.7500),
(39, 3, 19, '2026-11-02', 0, 31.68, 1.93, 35.36, 32.61, '2026-01-07 17:40:48', 1.7500),
(40, 3, 20, '2026-11-17', 0, 32.61, 0.98, 35.34, 0.00, '2026-01-07 17:40:48', 1.7500),
(41, 4, 1, '2026-02-02', 0, 18.61, 15.00, 35.36, 481.39, '2026-01-08 16:36:25', 1.7500),
(42, 4, 2, '2026-02-17', 15, 19.17, 14.44, 35.36, 462.22, '2026-01-08 16:36:25', 1.7500),
(43, 4, 3, '2026-03-02', 13, 19.74, 13.87, 35.36, 442.48, '2026-01-08 16:36:25', 1.7500),
(44, 4, 4, '2026-03-17', 15, 20.34, 13.27, 35.36, 422.14, '2026-01-08 16:36:25', 1.7500),
(45, 4, 5, '2026-04-02', 16, 20.95, 12.66, 35.36, 401.19, '2026-01-08 16:36:25', 1.7500),
(46, 4, 6, '2026-04-21', 19, 21.57, 12.04, 35.36, 379.62, '2026-01-08 16:36:25', 1.7500),
(47, 4, 7, '2026-05-02', 11, 22.22, 11.39, 35.36, 357.40, '2026-01-08 16:36:25', 1.7500),
(48, 4, 8, '2026-05-18', 16, 22.89, 10.72, 35.36, 334.51, '2026-01-08 16:36:25', 1.7500),
(49, 4, 9, '2026-06-02', 15, 23.57, 10.04, 35.36, 310.94, '2026-01-08 16:36:25', 1.7500),
(50, 4, 10, '2026-06-17', 15, 24.28, 9.33, 35.36, 286.66, '2026-01-08 16:36:25', 1.7500),
(51, 4, 11, '2026-07-02', 15, 25.01, 8.60, 35.36, 261.65, '2026-01-08 16:36:25', 1.7500),
(52, 4, 12, '2026-07-17', 15, 25.76, 7.85, 35.36, 235.89, '2026-01-08 16:36:25', 1.7500),
(53, 4, 13, '2026-08-03', 17, 26.53, 7.08, 35.36, 209.36, '2026-01-08 16:36:25', 1.7500),
(54, 4, 14, '2026-08-17', 14, 27.33, 6.28, 35.36, 182.03, '2026-01-08 16:36:25', 1.7500),
(55, 4, 15, '2026-09-02', 16, 28.15, 5.46, 35.36, 153.88, '2026-01-08 16:36:25', 1.7500),
(56, 4, 16, '2026-09-17', 15, 28.99, 4.62, 35.36, 124.89, '2026-01-08 16:36:25', 1.7500),
(57, 4, 17, '2026-10-02', 15, 29.86, 3.75, 35.36, 95.03, '2026-01-08 16:36:25', 1.7500),
(58, 4, 18, '2026-10-17', 15, 30.76, 2.85, 35.36, 64.27, '2026-01-08 16:36:25', 1.7500),
(59, 4, 19, '2026-11-02', 16, 31.68, 1.93, 35.36, 32.59, '2026-01-08 16:36:25', 1.7500),
(60, 4, 20, '2026-11-17', 15, 32.59, 0.98, 35.32, 0.00, '2026-01-08 16:36:25', 1.7500),
(61, 5, 1, '2026-02-02', 0, 18.61, 15.00, 35.36, 481.39, '2026-01-09 16:49:54', 1.7500),
(62, 5, 2, '2026-02-17', 15, 19.17, 14.44, 35.36, 462.22, '2026-01-09 16:49:54', 1.7500),
(63, 5, 3, '2026-03-02', 13, 19.74, 13.87, 35.36, 442.48, '2026-01-09 16:49:54', 1.7500),
(64, 5, 4, '2026-03-17', 15, 20.33, 13.27, 35.35, 422.15, '2026-01-09 16:49:54', 1.7500),
(65, 5, 5, '2026-04-02', 16, 20.94, 12.66, 35.35, 401.21, '2026-01-09 16:49:54', 1.7500),
(66, 5, 6, '2026-04-21', 19, 21.57, 12.04, 35.36, 379.64, '2026-01-09 16:49:54', 1.7500),
(67, 5, 7, '2026-05-02', 11, 22.22, 11.39, 35.36, 357.42, '2026-01-09 16:49:54', 1.7500),
(68, 5, 8, '2026-05-18', 16, 22.89, 10.72, 35.36, 334.53, '2026-01-09 16:49:54', 1.7500),
(69, 5, 9, '2026-06-02', 15, 23.57, 10.04, 35.36, 310.96, '2026-01-09 16:49:54', 1.7500),
(70, 5, 10, '2026-06-17', 15, 24.28, 9.33, 35.36, 286.68, '2026-01-09 16:49:54', 1.7500),
(71, 5, 11, '2026-07-02', 15, 25.01, 8.60, 35.36, 261.67, '2026-01-09 16:49:54', 1.7500),
(72, 5, 12, '2026-07-17', 15, 25.76, 7.85, 35.36, 235.91, '2026-01-09 16:49:54', 1.7500),
(73, 5, 13, '2026-08-03', 17, 26.53, 7.08, 35.36, 209.38, '2026-01-09 16:49:54', 1.7500),
(74, 5, 14, '2026-08-17', 14, 27.33, 6.28, 35.36, 182.05, '2026-01-09 16:49:54', 1.7500),
(75, 5, 15, '2026-09-02', 16, 28.15, 5.46, 35.36, 153.90, '2026-01-09 16:49:54', 1.7500),
(76, 5, 16, '2026-09-17', 15, 28.99, 4.62, 35.36, 124.91, '2026-01-09 16:49:54', 1.7500),
(77, 5, 17, '2026-10-02', 15, 29.86, 3.75, 35.36, 95.05, '2026-01-09 16:49:54', 1.7500),
(78, 5, 18, '2026-10-17', 15, 30.76, 2.85, 35.36, 64.29, '2026-01-09 16:49:54', 1.7500),
(79, 5, 19, '2026-11-02', 16, 31.68, 1.93, 35.36, 32.61, '2026-01-09 16:49:54', 1.7500),
(80, 5, 20, '2026-11-17', 15, 32.61, 0.98, 35.34, 0.00, '2026-01-09 16:49:54', 1.7500);

-- --------------------------------------------------------

--
-- Table structure for table `tb_provincias`
--

DROP TABLE IF EXISTS `tb_provincias`;\n\nCREATE TABLE `tb_provincias` (
  `id` varchar(4) NOT NULL,
  `nombre` varchar(45) DEFAULT NULL,
  `iddepartamento` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_reports`
--

DROP TABLE IF EXISTS `tb_reports`;\n\nCREATE TABLE `tb_reports` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_reports`
--

INSERT IGNORE INTO `tb_reports` (`job_id`, `type`, `print_url`, `file_path`, `status`, `created_by`, `created_at`, `started_at`, `finished_at`, `error_text`, `file_hash`) VALUES
('balanza_696146d4a7b4c3.65717712', 'balanza_pdf', 'http://localhost/Servicredit/contabilidad/balanza_print?start_date=2026-01-01&end_date=2026-01-31', 'uploads/reports/balanza_696146d4a7b4c3.65717712.pdf', 'pending', NULL, '2026-01-09 13:20:04', NULL, NULL, NULL, NULL),
('balanza_696146daf218d2.89885734', 'balanza_pdf', 'http://localhost/Servicredit/contabilidad/balanza_print?start_date=2026-01-01&end_date=2026-01-31', 'uploads/reports/balanza_696146daf218d2.89885734.pdf', 'pending', NULL, '2026-01-09 13:20:10', NULL, NULL, NULL, NULL),
('resultados_69307046716126.30564178', 'resultados_pdf', 'http://localhost/servicredit/contabilidad/resultados_print?start_date=2025-01-01&end_date=2025-12-01', 'uploads/reports/resultados_69307046716126.30564178.pdf', 'pending', NULL, '2025-12-03 12:15:50', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_series_recibos`
--

DROP TABLE IF EXISTS `tb_series_recibos`;\n\nCREATE TABLE `tb_series_recibos` (
  `idserie` int(11) NOT NULL,
  `codigo` varchar(10) NOT NULL COMMENT 'CÃ³digo de la serie, p.ej. A, B, C o "A1"',
  `nombre` varchar(100) DEFAULT NULL COMMENT 'Nombre legible de la serie, p.ej. "Serie A"',
  `consecutivo` int(11) NOT NULL DEFAULT 0 COMMENT 'Consecutivo actual (prÃ³ximo a emitir)',
  `ultimo_emitido` int(11) DEFAULT NULL COMMENT 'NÃºmero del Ãºltimo recibo emitido',
  `created_on` int(11) DEFAULT unix_timestamp(),
  `updated_on` int(11) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 activo, 0 inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `tb_series_recibos`
--

INSERT IGNORE INTO `tb_series_recibos` (`idserie`, `codigo`, `nombre`, `consecutivo`, `ultimo_emitido`, `created_on`, `updated_on`, `estado`) VALUES
(1, 'A', 'Serie A', 0, NULL, 1767622352, NULL, 1),
(2, 'B', 'Serie B', 0, NULL, 1767622352, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_simulacion`
--

DROP TABLE IF EXISTS `tb_simulacion`;\n\nCREATE TABLE `tb_simulacion` (
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

DROP TABLE IF EXISTS `tb_sistema`;\n\nCREATE TABLE `tb_sistema` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_sistema`
--

INSERT IGNORE INTO `tb_sistema` (`id`, `razon_social`, `email`, `web`, `direccion`, `telefonos`, `mensaje_ticket`, `idmoneda`, `fechaActualizacion`, `logotipo`) VALUES
(1, 'CREDIBLAMEN SYSTEM', 'info@crediblamen.group', 'www.crediblamen.group', 'Managua, Nicaragua', '0000-0000', 'Prestamos Rapidos y Faciles.', 1, '2025-12-30 12:48:11', '6302417859.png');

-- --------------------------------------------------------

--
-- Table structure for table `tb_solicitudes`
--

DROP TABLE IF EXISTS `tb_solicitudes`;\n\nCREATE TABLE `tb_solicitudes` (
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
  `estado_civil` enum('Soltero','Casado','Viudo') DEFAULT NULL,
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
  `frecuencia` enum('Diaria','Semanal','Quincenal','Mensual') DEFAULT NULL,
  `tasa_interes` decimal(6,2) DEFAULT NULL,
  `cuota_estim_estimada` decimal(14,2) DEFAULT NULL,
  `garantia` varchar(255) DEFAULT NULL,
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
  `gastos_transporte` decimal(14,2) DEFAULT NULL COMMENT 'Gastos de transporte mensuales'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_solicitudes`
--

INSERT IGNORE INTO `tb_solicitudes` (`idsolicitud`, `apellidos`, `nombres`, `direccion`, `telefono`, `email`, `tipo_doc`, `numero_doc`, `comentarios`, `estado`, `fechaActualizacion`, `negocio_propio`, `negocio_antiguedad`, `matricula_permiso`, `cedula_vigente`, `ingreso_promedio_alto`, `ingreso_promedio_bajo`, `otros_ingresos`, `otros_ingresos_docs`, `ahorros`, `inventario_disponible`, `cuentas_por_cobrar`, `porcentaje_recuperacion`, `gastos_fijos`, `gastos_operativos`, `margen_comercial`, `datos_personales`, `datos_conyuge`, `recibo_servicios`, `investigacion_vecinos`, `referencias_personales`, `barrio`, `municipio`, `tipo_credito`, `tipo_solicitud`, `estado_civil`, `uso_credito`, `analista`, `estado_aprobacion`, `fecha_solicitud`, `fuente_ingresos`, `telefono_trabajo`, `dni_conyuge`, `salario_conyuge`, `observaciones`, `giro_negocio`, `monto_solicitado`, `plazo_meses`, `frecuencia`, `tasa_interes`, `cuota_estim_estimada`, `garantia`, `otros_ingresos_detalle`, `ventas_promedio_diarios`, `ventas_promedio_mensual`, `detalle_inventario`, `cuentas_por_cobrar_amount`, `caja_amount`, `banco_amount`, `pago_alquiler`, `pago_trabajadores`, `energia`, `agua`, `internet`, `promotor`, `fecha_recepcion`, `ventas_dias_buenos`, `ventas_dias_malos`, `nombre_conyuge`, `ocupacion_conyuge`, `telefono_conyuge`, `numero_dependientes`, `fecha_nacimiento`, `edad`, `sexo`, `nombre_empresa`, `direccion_empresa`, `telefono_empresa`, `cargo_puesto`, `ingreso_mensual_neto`, `nombre_negocio`, `actividad_economica`, `ubicacion_negocio`, `telefono_negocio`, `numero_empleados`, `otros_gastos`, `es_nuevo`, `es_renovacion`, `tiempo_residir_anios`, `tiempo_residir_meses`, `condicion_vivienda`, `tiempo_empleo_anios`, `tiempo_empleo_meses`, `tipo_contrato`, `deducciones`, `tiempo_operacion_anios`, `tiempo_operacion_meses`, `propiedad_negocio`, `tipo_documento`, `ready_for_approval`, `rechazado`, `propuesta_tipos`, `ventas_dias_buenos_mask`, `ventas_dias_malos_mask`, `nombre_completo`, `comision_desembolso`, `edit_comment`, `rubro_credito`, `otros_ingresos_1_amount`, `otros_ingresos_1_margin`, `otros_ingresos_1_detalle`, `otros_ingresos_2_amount`, `otros_ingresos_2_margin`, `otros_ingresos_2_detalle`, `otros_ingresos_3_amount`, `otros_ingresos_3_margin`, `otros_ingresos_3_detalle`, `ventas_buenos_amount`, `ventas_malos_amount`, `declaro_verificacion`, `firma_solicitante`, `fecha_firma`, `energia_electrica`, `agua_potable`, `internet_telefonia`, `ddc_investigacion_campo`, `nombre_promotor`, `fecha_recepcion_solicitud`, `observaciones_promotor`, `destino_credito`, `idcliente`, `cuentas_por_cobrar_evidencia`, `gastos_personales`, `gastos_transporte`) VALUES
(3, 'Carrillo', 'Erick Antonio Ramirez', NULL, NULL, NULL, 3, '0012702981004X', NULL, NULL, '2026-01-07 12:08:09', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, 25.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CrÃ©ditos Personales', NULL, 'Soltero', NULL, NULL, 'aprobado', NULL, NULL, NULL, NULL, 0.00, NULL, 'Pizzeria casera', 500.00, 12, 'Mensual', 0.06, 71.43, 'Hipotecaria', NULL, NULL, 8400.00, NULL, 1200.00, 300.00, 1500.00, 1000.00, 1200.00, NULL, NULL, NULL, NULL, NULL, 1200, 300, NULL, NULL, '76534038', NULL, '1998-02-27', 27, NULL, 'Ernst & Young', 'Managua', NULL, 'Staff I BI', 360000.00, 'Serviconta', 'Servicios Profesionales', 'Bo batahola sur detras de sitel 1c arriba 1/2c al al sur ', NULL, 2, '100', 1, 0, 2, 2, NULL, 1, 1, NULL, 1200.00, 1, 1, NULL, NULL, 0, 0, '[\"9\"]', NULL, NULL, 'Erick Antonio Ramirez Carrillo', 0.0700, NULL, 'GanaderÃ­a', 1000.00, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1200.00, 300.00, NULL, NULL, '2025-12-30', 20.00, 220.00, 1200.00, NULL, 'Ruta Prueba', '2025-12-30', NULL, 'Consumo', 2, NULL, NULL, NULL),
(4, 'AlemÃ¡n', 'Denis Ramon VÃ¡squez', 'Bo.Jonathan Gonzalez. Bancentro 22 c 1/2 al sur', '81112991', NULL, 3, '0010806670057W', NULL, NULL, '2026-01-07 14:28:46', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-01-06 08:33:00', NULL, NULL, NULL, 0.00, NULL, 'Pulpero', 500.00, 12, 'Quincenal', 0.06, 35.36, 'Prendaria', NULL, NULL, 76400.00, 'Arroz,Azucar,aceite, frijoles,ace, cloro,jabon,huevo,queso,leche agria,papel, coca cola,agua, raptor,medicamentos naturales, maggi.', 4500.00, 6300.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6500, 4200, NULL, NULL, NULL, 1, '1967-06-08', 58, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulperia Algeria', NULL, 'Bo.Jonathan Gonzalez. Bancentro 22 c 1/2 al sur', '81112991', NULL, '2150', 0, 1, 58, NULL, 'Propia', NULL, NULL, NULL, NULL, 14, NULL, NULL, NULL, 0, 0, '[\"2\"]', 15, 112, 'Denis Ramon VÃ¡squez AlemÃ¡n', 0.0700, 'Modificacion', 'Comercio', 1500.00, 5.00, 'Seguros amÃ©rica', 1900.00, 50.00, 'Foto copiadora e impresiones', NULL, NULL, NULL, 6500.00, 4200.00, 1, NULL, '2026-01-06', 2636.57, 157.81, NULL, 'Se verifico con vecinos y se no se detecto ningÃºn comportamiento inusual ', 'Ruta Prueba', '2026-01-06', NULL, NULL, 3, NULL, NULL, NULL),
(5, 'osorio', 'juan mario vega', 'Bo batahola sur q', '88888888', NULL, 3, '0010101010000X', NULL, NULL, '2026-01-07 15:12:32', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, 60.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Personales', NULL, 'Soltero', NULL, NULL, 'aprobado', '2026-01-07 14:31:00', NULL, NULL, NULL, 0.00, NULL, 'Negocio', 800.00, 8, 'Mensual', 0.06, 135.83, 'Hipotecaria', NULL, NULL, 30000.00, 'Camisas, impresiones, golocinas', 100.00, 1000.00, 4000.00, 100.00, 120.00, NULL, NULL, NULL, NULL, NULL, 1500, 500, NULL, NULL, NULL, NULL, '2005-02-01', 20, NULL, 'EY', 'Rotonda Centroamerica', '99999999', 'Staff', 10000.00, 'Negocio', 'Negocio', 'Bo batahola sur q', '9999999', 2, NULL, 1, 0, 10, NULL, 'Propia', 2, NULL, 'Permanente', 100.00, 2, NULL, NULL, NULL, 0, 0, '[\"9\"]', 15, 112, 'juan mario vega osorio', 0.0700, 'Guardar y enviar', 'Personales (Asalariados)', 1000.00, 15.00, 'Venta de Muebles', 1200.00, 30.00, 'Venta de cosmeticos', 1300.00, 60.00, 'Venta de camisas', 1500.00, 500.00, 1, NULL, '2026-01-07', 1000.00, 80.00, 100.00, NULL, NULL, NULL, NULL, 'Consumo', 4, NULL, NULL, NULL),
(6, 'VÃ¡squez', 'AlemÃ¡n Denis Ramon', 'Bo.Jonathan Gonzalez. Bancentro 22 c 1/2 al sur', '81112991', NULL, 3, '0010806670057W', NULL, NULL, '2026-01-07 16:26:14', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Soltero', NULL, NULL, 'aprobado', '2025-12-27 15:47:00', NULL, NULL, NULL, 0.00, NULL, 'Pulpero', 500.00, 10, 'Quincenal', 0.06, 35.36, 'Prendaria', NULL, NULL, 154400.00, 'Arroz, Azucar, Aceite, Clor, ETC', 4500.00, 6300.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6500, 4200, NULL, NULL, NULL, 1, '1967-06-08', 58, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulperia Algeria', 'Comercio', 'Bo.Jonathan Gonzalez. Bancentro 22 c 1/2 al sur', '81112991', NULL, NULL, 0, 1, 58, NULL, 'Propia', NULL, NULL, NULL, NULL, 14, NULL, NULL, NULL, 0, 0, '[\"2\"]', 15, 112, 'AlemÃ¡n Denis Ramon VÃ¡squez', 0.0700, NULL, 'Comercio', 1500.00, 5.00, 'Venta de Seguros America', 1900.00, 50.00, 'Fotocopia, Impresiones y Articulos Escolares', 2800.00, 10.00, 'Venta de loto y bisuteria', 6500.00, 4200.00, 1, NULL, NULL, 2636.57, 157.81, NULL, NULL, NULL, '2025-12-27', NULL, 'Inversión', 3, 'cuentas_cobrar_1767823070_fe31be5e.jpg', 1700.00, 450.00);

-- --------------------------------------------------------

--
-- Table structure for table `tb_solicitudes_comments`
--

DROP TABLE IF EXISTS `tb_solicitudes_comments`;\n\nCREATE TABLE `tb_solicitudes_comments` (
  `idcomment` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(150) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_solicitudes_comments`
--

INSERT IGNORE INTO `tb_solicitudes_comments` (`idcomment`, `idsolicitud`, `user_id`, `username`, `action`, `comment`, `created_at`) VALUES
(1, 4, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Modificacion', '2026-01-06 12:30:03'),
(2, 5, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'VGuardar', '2026-01-07 14:58:06'),
(3, 5, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Guardar', '2026-01-07 15:01:13'),
(4, 5, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Guardar', '2026-01-07 15:01:45'),
(5, 5, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Guardar', '2026-01-07 15:04:30'),
(6, 5, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'guardar', '2026-01-07 15:09:18'),
(7, 5, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Guardar y enviar', '2026-01-07 15:12:00');

-- --------------------------------------------------------

--
-- Table structure for table `tb_solicitudes_notes`
--

DROP TABLE IF EXISTS `tb_solicitudes_notes`;\n\nCREATE TABLE `tb_solicitudes_notes` (
  `idnote` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(150) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_solicitud_aprobaciones`
--

DROP TABLE IF EXISTS `tb_solicitud_aprobaciones`;\n\nCREATE TABLE `tb_solicitud_aprobaciones` (
  `idaprobacion` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `role` varchar(80) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(120) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `aprobado_por` varchar(50) DEFAULT NULL,
  `propuesta_overrides` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_solicitud_aprobaciones`
--

INSERT IGNORE INTO `tb_solicitud_aprobaciones` (`idaprobacion`, `idsolicitud`, `role`, `user_id`, `username`, `comment`, `created_at`, `aprobado_por`, `propuesta_overrides`) VALUES
(1, 3, 'Validación', 15, 'ADMINISTRADOR ADMINISTRADOR', '[Aprobado] apro', '2026-01-07 12:08:09', 'Comite Interno', '[{\"id\":9,\"monto\":\"500.00\",\"tasa\":\"6\",\"plazo\":\"12\",\"comision\":\"7\",\"comments\":{}}]'),
(2, 4, 'Validación', 15, 'ADMINISTRADOR ADMINISTRADOR', '[Aprobado] apro', '2026-01-07 12:09:04', 'Comite Interno', '[{\"id\":2,\"monto\":\"500.00\",\"tasa\":\"6\",\"plazo\":\"12\",\"comision\":\"7\",\"comments\":{}}]'),
(3, 5, 'Validación', 15, 'ADMINISTRADOR ADMINISTRADOR', '[Aprobado] Aprobado [foto:solicitudes/5/1767820352_e94b495f3063.jpg]', '2026-01-07 15:12:32', 'Comite Interno', '[{\"id\":9,\"monto\":\"800.00\",\"tasa\":\"6\",\"plazo\":\"8\",\"comision\":\"7\",\"comments\":{}}]'),
(4, 6, 'Validación', 15, 'ADMINISTRADOR ADMINISTRADOR', '[Aprobado] Aprobacion por parte de gerencia. [foto:solicitudes/6/1767824774_0e80aa09ced3.jpg]', '2026-01-07 16:26:14', 'Junta Directiva', '[{\"id\":2,\"monto\":\"500.00\",\"tasa\":\"6\",\"plazo\":\"10\",\"comision\":\"7\",\"comments\":{}}]');

-- --------------------------------------------------------

--
-- Table structure for table `tb_solicitud_faf`
--

DROP TABLE IF EXISTS `tb_solicitud_faf`;\n\nCREATE TABLE `tb_solicitud_faf` (
  `idfaf` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL COMMENT 'asalariado|comerciante',
  `data` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_solicitud_photos`
--

DROP TABLE IF EXISTS `tb_solicitud_photos`;\n\nCREATE TABLE `tb_solicitud_photos` (
  `idphoto` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `mime` varchar(50) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `grupo` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_solicitud_photos`
--

INSERT IGNORE INTO `tb_solicitud_photos` (`idphoto`, `idsolicitud`, `filename`, `mime`, `size`, `created_at`, `grupo`) VALUES
(2, 4, 'solicitudes/4/otros_ingresos_1/1767712512_a3dbf3bc.png', NULL, NULL, '2026-01-06 10:15:12', 'otros_ingresos_1'),
(3, 5, 'solicitudes/5/otros_ingresos_1/1767818365_1b516249.jpg', NULL, NULL, '2026-01-07 15:39:25', 'otros_ingresos_1'),
(4, 5, 'solicitudes/5/otros_ingresos_2/1767818365_aa4a1f59.jpg', NULL, NULL, '2026-01-07 15:39:25', 'otros_ingresos_2'),
(5, 5, 'solicitudes/5/otros_ingresos_3/1767818365_ac6cd2c6.jpg', NULL, NULL, '2026-01-07 15:39:25', 'otros_ingresos_3'),
(6, 5, 'solicitudes/5/1767820352_e94b495f3063.jpg', 'image/jpeg', 300328, '2026-01-07 15:12:32', NULL),
(7, 6, 'solicitudes/6/cedula_front/1767823070_001a40b3.jpg', NULL, NULL, '2026-01-07 16:57:50', 'cedula_front'),
(8, 6, 'solicitudes/6/cedula_back/1767823070_58ec4086.jpg', NULL, NULL, '2026-01-07 16:57:50', 'cedula_back'),
(9, 6, 'solicitudes/6/fachada/1767823070_685f4f55.jpg', NULL, NULL, '2026-01-07 16:57:50', 'fachada'),
(10, 6, 'solicitudes/6/inventario/1767823070_ee9ebc9c.jpg', NULL, NULL, '2026-01-07 16:57:50', 'inventario'),
(11, 6, 'solicitudes/6/otros_ingresos_1/1767823070_d1680285.jpg', NULL, NULL, '2026-01-07 16:57:50', 'otros_ingresos_1'),
(12, 6, 'solicitudes/6/otros_ingresos_2/1767823070_c8681713.jpg', NULL, NULL, '2026-01-07 16:57:50', 'otros_ingresos_2'),
(13, 6, 'solicitudes/6/otros_ingresos_3/1767823070_54b40976.jpg', NULL, NULL, '2026-01-07 16:57:50', 'otros_ingresos_3'),
(14, 6, 'solicitudes/6/1767824774_0e80aa09ced3.jpg', 'image/jpeg', 300328, '2026-01-07 16:26:14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_solicitud_propuestas`
--

DROP TABLE IF EXISTS `tb_solicitud_propuestas`;\n\nCREATE TABLE `tb_solicitud_propuestas` (
  `idpropuesta` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `idtipo_producto` int(11) NOT NULL,
  `monto` decimal(15,2) DEFAULT NULL,
  `tasa` decimal(10,4) DEFAULT NULL,
  `comision_desembolso` decimal(10,4) DEFAULT NULL,
  `plazo_min` int(11) DEFAULT NULL,
  `plazo_max` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_solicitud_propuestas`
--

INSERT IGNORE INTO `tb_solicitud_propuestas` (`idpropuesta`, `idsolicitud`, `idtipo_producto`, `monto`, `tasa`, `comision_desembolso`, `plazo_min`, `plazo_max`, `created_at`) VALUES
(1, 5, 9, 800.00, 6.0000, 7.0000, 6, 8, '2026-01-07 15:12:32'),
(2, 6, 2, 500.00, 6.0000, 7.0000, 6, 10, '2026-01-07 16:26:14');

-- --------------------------------------------------------

--
-- Table structure for table `tb_solicitud_propuestas_history`
--

DROP TABLE IF EXISTS `tb_solicitud_propuestas_history`;\n\nCREATE TABLE `tb_solicitud_propuestas_history` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_solicitud_referencias`
--

DROP TABLE IF EXISTS `tb_solicitud_referencias`;\n\nCREATE TABLE `tb_solicitud_referencias` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_solicitud_referencias`
--

INSERT IGNORE INTO `tb_solicitud_referencias` (`idreferencia`, `idsolicitud`, `referencia_num`, `nombre`, `cedula`, `direccion`, `telefono`, `tipo_referencia`, `desde_conoce_cliente`, `relacion_economica`, `opinion`, `comentarios`, `created_at`, `tipo_personal_relacion`) VALUES
(1, 5, 1, 'Juan Diego Luna', '0010101010000X', 'Bo batahola sur ', '99999999', 'Comercial', '3', 1, 'Excelente', 'Es un amigo de infancia', '2026-01-07 14:42:38', ''),
(2, 5, 2, '', '', '', '', '', '', NULL, '', '', '2026-01-07 14:46:43', ''),
(3, 6, 1, 'Chrsitain Benito Lainez Aguilar', '', 'Terminal 172 2c al oeste ', '58706262', 'Comercial', '2 meses', 0, 'Excelente', 'Atendido por disribuidora Dismab, con excelente referencia de acuerdo al comentario de su vendedor. ', '2026-01-07 16:14:26', ''),
(4, 6, 2, 'Ligia del socorro sandoval', '0012705600002K', 'Bo jonathan gonzalez supermercado pricesmatr 3 /2 c al este ', '87930531', 'Personal', '20 años', 0, 'Excelente', 'Es un buen hombre, dueño del negocio. ', '2026-01-07 16:14:26', 'Vecino');

-- --------------------------------------------------------

--
-- Table structure for table `tb_solicitud_referencias_fotos`
--

DROP TABLE IF EXISTS `tb_solicitud_referencias_fotos`;\n\nCREATE TABLE `tb_solicitud_referencias_fotos` (
  `idfoto` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `idreferencia` int(11) DEFAULT NULL,
  `referencia_num` tinyint(4) DEFAULT NULL,
  `tipo` varchar(10) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `tb_solicitud_referencias_fotos`
--

INSERT IGNORE INTO `tb_solicitud_referencias_fotos` (`idfoto`, `idsolicitud`, `idreferencia`, `referencia_num`, `tipo`, `filename`, `created_at`) VALUES
(1, 5, 1, 1, 'front', 'uploads/solicitudes/solicitud_5/referencias/referencia_1/1767818803_4ab9df51_front.jpg', '2026-01-07 14:46:43'),
(2, 5, 1, 1, 'back', 'uploads/solicitudes/solicitud_5/referencias/referencia_1/1767818803_7df145fb_back.jpg', '2026-01-07 14:46:43'),
(3, 6, 3, 1, 'front', 'uploads/solicitudes/solicitud_6/referencias/referencia_1/1767824066_c4516981_front.jpg', '2026-01-07 16:14:26'),
(4, 6, 3, 1, 'back', 'uploads/solicitudes/solicitud_6/referencias/referencia_1/1767824066_95d2d95e_back.jpg', '2026-01-07 16:14:26'),
(5, 6, 4, 2, 'front', 'uploads/solicitudes/solicitud_6/referencias/referencia_2/1767824066_04fcd26d_front.jpg', '2026-01-07 16:14:26'),
(6, 6, 4, 2, 'back', 'uploads/solicitudes/solicitud_6/referencias/referencia_2/1767824066_74927372_back.jpg', '2026-01-07 16:14:26');

-- --------------------------------------------------------

--
-- Table structure for table `tb_solicitud_uso_credito`
--

DROP TABLE IF EXISTS `tb_solicitud_uso_credito`;\n\nCREATE TABLE `tb_solicitud_uso_credito` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_solicitud_uso_credito`
--

INSERT IGNORE INTO `tb_solicitud_uso_credito` (`iduso`, `idsolicitud`, `descripcion`, `fuente_ingreso`, `monto_estimado_mes`, `declaracion_nombre`, `declaracion_firma`, `declaracion_fecha`, `evaluador_credito`, `fecha_evaluacion`, `created_at`, `monto_solicitado`, `plazo_solicitado`, `destino_prestamo`, `destino_detalle`) VALUES
(1, 5, 'Camisas, impresiones, golocinas', 'Negocio', 10000.00, '', 'osorio juan mario vega', '2026-01-08', '', '2026-01-07', '2026-01-07 14:40:11', 800.00, 12, 'Consumo', 'CONSUMO DE VACACIONES'),
(2, 6, 'Arroz, Azucar, Aceite, Clor, ETC', 'Pulpero', 154400.00, '', 'VÃ¡squez AlemÃ¡n Denis Ramon', '2026-01-07', '', '2026-01-07', '2026-01-07 16:10:37', 500.00, 10, 'Inversión', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_tasa_cambio`
--

DROP TABLE IF EXISTS `tb_tasa_cambio`;\n\nCREATE TABLE `tb_tasa_cambio` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `tasa_cambio` decimal(10,4) NOT NULL,
  `tasa_venta` decimal(10,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_tasa_cambio`
--

INSERT IGNORE INTO `tb_tasa_cambio` (`id`, `fecha`, `tasa_cambio`, `tasa_venta`, `created_at`, `updated_at`) VALUES
(1, '2026-01-06', 36.5000, 37.0000, '2026-01-06 18:43:10', '2026-01-09 22:15:28');

-- --------------------------------------------------------

--
-- Table structure for table `tb_tipo_productos`
--

DROP TABLE IF EXISTS `tb_tipo_productos`;\n\nCREATE TABLE `tb_tipo_productos` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_tipo_productos`
--

INSERT IGNORE INTO `tb_tipo_productos` (`id`, `nombre`, `porcentaje`, `estado`, `created_at`, `monto_min`, `monto_max`, `tasa_mensual`, `comision_desembolso`, `plazo_min`, `plazo_max`, `clasificacion`) VALUES
(1, 'Micronegocio 300-499', 0.0800, 1, '2025-12-10 14:38:16', 300.00, 499.00, 0.0800, 0.0700, 6, 12, 'Negocios'),
(2, 'Micronegocio 500-999', 0.0600, 1, '2025-12-10 14:38:16', 500.00, 999.00, 0.0600, 0.0700, 6, 12, 'Negocios'),
(3, 'PequeÃ±o negocio 1 1000-1499', 0.0600, 1, '2025-12-10 14:38:16', 1000.00, 1499.00, 0.0600, 0.0700, 10, 18, 'Negocios'),
(4, 'PequeÃ±o Negocio 2 1500-4999', 0.0600, 1, '2025-12-10 14:38:16', 1500.00, 4999.00, 0.0600, 0.0700, 12, 24, 'Negocios'),
(5, 'PequeÃ±o Negocio 3 5000-9999', 0.0500, 1, '2025-12-10 14:38:16', 5000.00, 9999.00, 0.0500, 0.0500, 12, 36, 'Negocios'),
(6, 'PequeÃ±a Industria 10000-19999', 0.0400, 1, '2025-12-10 14:38:16', 10000.00, 19999.00, 0.0400, 0.0500, 24, 48, 'Negocios'),
(7, 'PequeÃ±a Industria 20000-25000', 0.0400, 1, '2025-12-10 14:38:16', 20000.00, 25000.00, 0.0400, 0.0500, 24, 48, 'Negocios'),
(8, 'Personal 300-499', 0.0800, 1, '2025-12-10 15:00:04', 300.00, 499.00, 0.0800, 0.0700, 4, 8, 'Personal'),
(9, 'Personal 500-999', 0.0600, 1, '2025-12-10 15:00:04', 500.00, 999.00, 0.0600, 0.0700, 6, 12, 'Personal'),
(10, 'Personal 1000-1499', 0.0600, 1, '2025-12-10 15:00:04', 1000.00, 1499.00, 0.0600, 0.0700, 10, 18, 'Personal'),
(11, 'Compra de lote 3000-4999', 0.0600, 1, '2025-12-10 15:01:58', 3000.00, 4999.00, 0.0600, 0.0700, 12, 36, 'Viviendo o Hipotecario'),
(12, 'Compra de lote 5000-7999', 0.0500, 1, '2025-12-10 15:01:58', 5000.00, 7999.00, 0.0500, 0.0500, 24, 36, 'Viviendo o Hipotecario'),
(13, 'Compra de lote 8000-10000', 0.0500, 1, '2025-12-10 15:01:58', 8000.00, 10000.00, 0.0500, 0.0500, 24, 36, 'Viviendo o Hipotecario'),
(14, 'Mejora de vivienda 300-499', 0.0600, 1, '2025-12-10 15:01:58', 300.00, 499.00, 0.0600, 0.0700, 6, 12, 'Viviendo o Hipotecario'),
(15, 'Mejora de vivienda 500-999', 0.0600, 1, '2025-12-10 15:01:58', 500.00, 999.00, 0.0600, 0.0700, 6, 12, 'Viviendo o Hipotecario'),
(16, 'Mejora de vivienda 1000-1499', 0.0600, 1, '2025-12-10 15:01:58', 1000.00, 1499.00, 0.0600, 0.0700, 8, 24, 'Viviendo o Hipotecario'),
(17, 'Mejora de vivienda 1500-3000', 0.0600, 1, '2025-12-10 15:01:58', 1500.00, 3000.00, 0.0600, 0.0700, 12, 24, 'Viviendo o Hipotecario'),
(18, 'Vehiculo usado 2000-2999', 0.0600, 1, '2025-12-10 15:11:47', 2000.00, 2999.00, 0.0600, 0.0700, 8, 18, 'Vehiculos Usados'),
(19, 'Vehiculo usado 3000-4999', 0.0600, 1, '2025-12-10 15:11:47', 3000.00, 4999.00, 0.0600, 0.0700, 12, 24, 'Vehiculos Usados'),
(20, 'Vehiculo usado 5000-9999', 0.0500, 1, '2025-12-10 15:11:47', 5000.00, 9999.00, 0.0500, 0.0500, 12, 36, 'Vehiculos Usados');

-- --------------------------------------------------------

--
-- Table structure for table `teso_accounts`
--

DROP TABLE IF EXISTS `teso_accounts`;\n\nCREATE TABLE `teso_accounts` (
  `id` int(11) NOT NULL,
  `code` varchar(64) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(128) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `currency_symbol` varchar(8) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_accounts_import`
--

DROP TABLE IF EXISTS `tmp_accounts_import`;\n\nCREATE TABLE `tmp_accounts_import` (
  `code` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code_muc` varchar(64) DEFAULT NULL,
  `name_muc` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_accounts_import_excel`
--

DROP TABLE IF EXISTS `tmp_accounts_import_excel`;\n\nCREATE TABLE `tmp_accounts_import_excel` (
  `CUENTACREDIBLAMEN` varchar(128) DEFAULT NULL,
  `NOMBRECUENTA` varchar(512) DEFAULT NULL,
  `CUENTAMUC` varchar(128) DEFAULT NULL,
  `NOMBREDECUENTAMUC` varchar(512) DEFAULT NULL,
  `COMENTARIOS` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;\n\nCREATE TABLE `users` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT IGNORE INTO `users` (`id`, `ip_address`, `username`, `password`, `email`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`, `perfil`, `idserie_recibo`) VALUES
(15, '208.96.130.158', 'ADMINISTRADOR', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'administrador@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705526891, 1768355626, 1, 'ADMINISTRADOR', 'ADMINISTRADOR', NULL, NULL, 1, NULL),
(19, '::1', 'erickprueba', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'erickprueba@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767042965, 1767043838, 1, 'erickprueba', 'erickprueba', NULL, NULL, 4, NULL),
(20, '::1', 'Carlos Mayeel Pineda', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'cpineda@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043445, 1767044221, 1, 'Carlos Mayeel Pineda', 'cpineda', NULL, NULL, 4, NULL),
(21, '::1', 'Roman Lainez', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'Rlainez@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043488, 1767572418, 1, 'Roman Lainez', 'Rlainez', NULL, NULL, 4, NULL),
(25, '::1', 'Roman Lainez', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'Rlainez@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043488, 1767822064, 1, 'Roman Lainez', 'Rlainez', NULL, NULL, 4, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_backup`
--

DROP TABLE IF EXISTS `users_backup`;\n\nCREATE TABLE `users_backup` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activation_selector` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activation_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forgotten_password_selector` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forgotten_password_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
  `remember_selector` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `perfil` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users_backup`
--

INSERT IGNORE INTO `users_backup` (`id`, `ip_address`, `username`, `password`, `email`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`, `perfil`) VALUES
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
-- Table structure for table `users_backup_20260104_182005`
--

DROP TABLE IF EXISTS `users_backup_20260104_182005`;\n\nCREATE TABLE `users_backup_20260104_182005` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activation_selector` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activation_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forgotten_password_selector` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forgotten_password_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
  `remember_selector` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `perfil` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users_backup_20260104_182005`
--

INSERT IGNORE INTO `users_backup_20260104_182005` (`id`, `ip_address`, `username`, `password`, `email`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`, `perfil`) VALUES
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
-- Table structure for table `users_groups`
--

DROP TABLE IF EXISTS `users_groups`;\n\nCREATE TABLE `users_groups` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `group_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `users_groups`
--

INSERT IGNORE INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
(28, 15, 1),
(32, 19, 4),
(33, 20, 4),
(34, 21, 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `groups`
--


--
-- Indexes for table `login_attempts`
--


--
-- Indexes for table `tb_account`
--


--
-- Indexes for table `tb_account_mapping`
--


--
-- Indexes for table `tb_asesores`
--


--
-- Indexes for table `tb_bancos`
--


--
-- Indexes for table `tb_caja`
--


--
-- Indexes for table `tb_caja_movimiento`
--


--
-- Indexes for table `tb_centro_costo`
--


--
-- Indexes for table `tb_clientes`
--


--
-- Indexes for table `tb_clientes_rechazados`
--


--
-- Indexes for table `tb_contratos`
--


--
-- Indexes for table `tb_creditos`
--


--
-- Indexes for table `tb_credito_detalle`
--


--
-- Indexes for table `tb_departamentos`
--


--
-- Indexes for table `tb_detalle_simulacion`
--


--
-- Indexes for table `tb_distritos`
--


--
-- Indexes for table `tb_feriados`
--


--
-- Indexes for table `tb_garantias`
--


--
-- Indexes for table `tb_garantias_fotos`
--


--
-- Indexes for table `tb_garantias_verificaciones`
--


--
-- Indexes for table `tb_journal`
--


--
-- Indexes for table `tb_journal_entry`
--


--
-- Indexes for table `tb_ledger`
--


--
-- Indexes for table `tb_monedas`
--


--
-- Indexes for table `tb_pagos`
--


--
-- Indexes for table `tb_pagos_detalle`
--


--
-- Indexes for table `tb_perfil_integral_cliente`
--


--
-- Indexes for table `tb_prestamos`
--


--
-- Indexes for table `tb_prestamo_cuotas`
--


--
-- Indexes for table `tb_provincias`
--


--
-- Indexes for table `tb_reports`
--


--
-- Indexes for table `tb_series_recibos`
--


--
-- Indexes for table `tb_simulacion`
--


--
-- Indexes for table `tb_sistema`
--


--
-- Indexes for table `tb_solicitudes`
--


--
-- Indexes for table `tb_solicitudes_comments`
--


--
-- Indexes for table `tb_solicitudes_notes`
--


--
-- Indexes for table `tb_solicitud_aprobaciones`
--


--
-- Indexes for table `tb_solicitud_photos`
--


--
-- Indexes for table `tb_solicitud_propuestas`
--


--
-- Indexes for table `tb_solicitud_referencias`
--


--
-- Indexes for table `tb_solicitud_referencias_fotos`
--


--
-- Indexes for table `tb_solicitud_uso_credito`
--


--
-- Indexes for table `tb_tasa_cambio`
--


--
-- Indexes for table `users`
--


--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_account`
--
ALTER TABLE `tb_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `tb_centro_costo`
--
ALTER TABLE `tb_centro_costo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_clientes`
--
ALTER TABLE `tb_clientes`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_contratos`
--
ALTER TABLE `tb_contratos`
  MODIFY `idcontrato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_garantias`
--
ALTER TABLE `tb_garantias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_garantias_fotos`
--
ALTER TABLE `tb_garantias_fotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_journal`
--
ALTER TABLE `tb_journal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_journal_entry`
--
ALTER TABLE `tb_journal_entry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `tb_ledger`
--
ALTER TABLE `tb_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=881;

--
-- AUTO_INCREMENT for table `tb_perfil_integral_cliente`
--
ALTER TABLE `tb_perfil_integral_cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_prestamos`
--
ALTER TABLE `tb_prestamos`
  MODIFY `idprestamo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_prestamo_cuotas`
--
ALTER TABLE `tb_prestamo_cuotas`
  MODIFY `idcuota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `tb_series_recibos`
--
ALTER TABLE `tb_series_recibos`
  MODIFY `idserie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_solicitudes`
--
ALTER TABLE `tb_solicitudes`
  MODIFY `idsolicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_solicitudes_comments`
--
ALTER TABLE `tb_solicitudes_comments`
  MODIFY `idcomment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tb_solicitud_aprobaciones`
--
ALTER TABLE `tb_solicitud_aprobaciones`
  MODIFY `idaprobacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_solicitud_photos`
--
ALTER TABLE `tb_solicitud_photos`
  MODIFY `idphoto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tb_solicitud_propuestas`
--
ALTER TABLE `tb_solicitud_propuestas`
  MODIFY `idpropuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_solicitud_referencias`
--
ALTER TABLE `tb_solicitud_referencias`
  MODIFY `idreferencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_solicitud_referencias_fotos`
--
ALTER TABLE `tb_solicitud_referencias_fotos`
  MODIFY `idfoto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_solicitud_uso_credito`
--
ALTER TABLE `tb_solicitud_uso_credito`
  MODIFY `iduso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_tasa_cambio`
--
ALTER TABLE `tb_tasa_cambio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users`
--

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
\r\nSET FOREIGN_KEY_CHECKS=1;\r\n