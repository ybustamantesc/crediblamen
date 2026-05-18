-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 23-04-2026 a las 15:06:47
-- Versión del servidor: 11.8.6-MariaDB-log
-- Versión de PHP: 7.2.34

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

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
(1, '0001', 'Prueba', 'activo', 'acreedora', NULL, '2026-03-03 14:37:26', NULL, 'Disponibilidades', 'Provisiones por incobrabilidad', 0),
(2, '0002', 'Prueba', 'pasivo', 'deudora', NULL, '2026-03-03 14:37:46', NULL, 'Cartera de créditos', 'Obligaciones financieras', 0);

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
  `fcm_cant_personas_dep` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_analisis_financiero_asalariado`
--

INSERT INTO `tb_analisis_financiero_asalariado` (`id`, `idsolicitud`, `ingreso_sueldo_neto`, `ingreso_comisiones`, `ingreso_bonificaciones`, `ingreso_remesas`, `ingreso_otros`, `total_ingresos`, `sueldo`, `inss`, `ir`, `sueldo_neto_calc`, `gastos_alimentacion`, `gastos_servicios`, `gastos_vestuario`, `gastos_educativos`, `gastos_transporte`, `gastos_alquiler`, `pago_empleado_viatico`, `entretenimiento`, `otros_gastos`, `total_gastos_familiares`, `cuotas_prestamos`, `pension_alimenticia`, `otras_obligaciones`, `total_otras_obligaciones`, `total_egresos`, `flujo_neto_mensual`, `cuota_periodica`, `canasta_basica`, `cantidad_promedio`, `monto_por_persona`, `personas_dependientes`, `gastos_alimentacion_canasta`, `transporte_urbano`, `transporte_individual`, `transporte_interurbano`, `recorrido_laboral`, `vehiculo_particular`, `alquiler`, `casa_propia`, `cobertura_deuda`, `cobertura_garantia`, `tc_acumulado`, `p_entretenimiento`, `created_at`, `updated_at`, `efectivo_caja`, `dinero_banco`, `total_disponible`, `cuentas_cobrar`, `inventario_mercaderia`, `productos_proceso`, `productos_terminados`, `total_inventarios`, `bienes_muebles`, `propiedades`, `otros_activos`, `total_activos_fijos`, `total_activos`, `cuentas_pagar_proveedores`, `cuentas_pagar_credito`, `pasivo_no_corriente`, `total_pasivo`, `total_patrimonio`, `total_pasivo_patrimonio`, `ventas_contado`, `ventas_credito`, `ventas_totales`, `costos_venta`, `margen_bruto`, `gastos_generales`, `utilidad_operativa`, `fcm_ventas_contado`, `fcm_recuperacion_credito`, `fcm_compras_contado`, `fcm_gastos_generales`, `flujo_negocio`, `fcm_otros_ingresos`, `fcm_gastos_consumo`, `fcm_otros_gastos`, `flujo_neto_disponible`, `gasto_local_alquiler`, `gasto_energia`, `gasto_agua`, `gasto_internet`, `gasto_seguridad`, `gasto_limpieza`, `gasto_personal`, `total_gastos_fijos`, `olp_fecha`, `olp_cuota`, `olp_instituciones`, `olp_saldo`, `subtotal_olp_saldo`, `ocp_fecha`, `ocp_cuota`, `ocp_instituciones`, `ocp_saldo`, `subtotal_ocp_saldo`, `costo_salario_ayudante`, `costo_transporte`, `costo_total_operacion`, `asal_olp_fecha`, `asal_olp_cuota`, `asal_olp_instituciones`, `asal_olp_saldo`, `asal_subtotal_olp_saldo`, `indicador_endeudamiento`, `capital_trabajo_neto`, `porcentaje_margen`, `fcm_valor_canasta_basica`, `fcm_cant_personas_dep`) VALUES
(1, 9, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 8173.81, 21249.74, 1, 0.00, 0, 3541.62, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.2410, 0.0000, 0.00, 0.00, '2026-03-13 14:21:17', '2026-03-13 20:02:28', 6200.00, 0.00, 6200.00, 0.00, 80000.00, 0.00, 0.00, 80000.00, 0.00, 0.00, 0.00, 0.00, 86200.00, 0.00, 0.00, 0.00, 0.00, 86200.00, 86200.00, 167000.00, 0.00, 167000.00, 133600.00, 33400.00, 4793.07, 28606.93, 167000.00, 0.00, 133600.00, 4793.07, 28606.93, 8900.00, 3541.62, 0.00, 33965.31, 0.00, 1415.17, 477.90, 0.00, 0.00, 0.00, 2500.00, 4793.07, '[\"\",\"\",\"\",\"\"]', '[\"\",\"\",\"\",\"\"]', '[\"\",\"\",\"\",\"\"]', '[\"\",\"\",\"\",\"\"]', NULL, '[\"\",\"\",\"\"]', '[\"\",\"\",\"\"]', '[\"\",\"\",\"\"]', '[\"\",\"\",\"\"]', NULL, NULL, 400.00, NULL, '[\"\"]', '[\"\"]', '[\"\"]', '[\"\"]', NULL, 0.0000, 86200.00, 0.20, 21249.74, 1),
(2, 21, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.0000, 0.00, 0.00, '2026-03-13 16:56:19', '2026-03-13 16:56:19', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '[\"\",\"\",\"\",\"\"]', '[\"\",\"\",\"\",\"\"]', '[\"\",\"\",\"\",\"\"]', '[\"\",\"\",\"\",\"\"]', NULL, '[\"\",\"\",\"\"]', '[\"\",\"\",\"\"]', '[\"\",\"\",\"\"]', '[\"\",\"\",\"\"]', NULL, NULL, NULL, NULL, '[\"\"]', '[\"\"]', '[\"\"]', '[\"\"]', NULL, 0.0000, 0.00, NULL, 0.00, 0),
(3, 1, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.0000, 0.00, 0.00, '2026-03-13 18:15:56', '2026-03-13 18:15:56', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 167000.00, 0.00, 133600.00, 4793.07, NULL, 0.00, 0.00, 0.00, 28606.93, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '[\"\",\"\",\"\",\"\"]', '[\"\",\"\",\"\",\"\"]', '[\"\",\"\",\"\",\"\"]', '[\"\",\"\",\"\",\"\"]', NULL, '[\"\",\"\",\"\"]', '[\"\",\"\",\"\"]', '[\"\",\"\",\"\"]', '[\"\",\"\",\"\"]', NULL, NULL, NULL, NULL, '[\"\"]', '[\"\"]', '[\"\"]', '[\"\"]', NULL, 0.0000, 0.00, NULL, 0.00, 0),
(4, 15, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 3690.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.0520, 0.0000, 0.00, 0.00, '2026-03-13 21:50:42', '2026-03-13 22:24:36', 6500.00, 0.00, 6500.00, 0.00, 83700.00, 0.00, 0.00, 83700.00, 0.00, 0.00, 0.00, 0.00, 90200.00, 0.00, 0.00, 0.00, 0.00, 90200.00, 90200.00, 80600.00, 0.00, 80600.00, 64480.00, 16120.00, 8686.48, 7433.52, 80600.00, 0.00, 0.00, 8686.48, 71913.52, 5500.00, 7083.25, 0.00, 70330.27, 0.00, 486.48, 0.00, 0.00, 0.00, 0.00, 6400.00, 6886.48, '[\"\",\"\",\"\",\"\"]', '[\"\",\"\",\"\",\"\"]', '[\"\",\"\",\"\",\"\"]', '[\"\",\"\",\"\",\"\"]', NULL, '[\"\",\"\",\"\"]', '[\"\",\"\",\"\"]', '[\"\",\"\",\"\"]', '[\"\",\"\",\"\"]', NULL, NULL, 1800.00, NULL, '[\"\"]', '[\"\"]', '[\"\"]', '[\"\"]', NULL, 0.0000, 90200.00, 0.20, 21249.74, 2),
(5, 12, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2723.38, 0.00, 0, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.0560, 0.0000, 0.00, 0.00, '2026-03-14 00:04:50', '2026-03-14 00:10:59', 0.00, 0.00, 0.00, 0.00, 54000.00, 0.00, 0.00, 54000.00, 0.00, 0.00, 0.00, 0.00, 54000.00, 0.00, 0.00, 0.00, 0.00, 54000.00, 54000.00, 80600.00, 0.00, 80600.00, 64480.00, 16120.00, 9870.44, 6249.56, 80600.00, 0.00, 0.00, 9870.44, 70729.56, 2800.00, 7083.25, 17523.58, 48922.73, 0.00, 0.00, 270.44, 0.00, 0.00, 0.00, 4600.00, 6070.44, '[\"\",\"2025-07-18\",\"\",\"\"]', '[\"\",\"7938.30\",\"1405.06\",\"\"]', '[\"\",\"GMG\",\"PRESTAMO SUAREZ\",\"\"]', '[\"\",\"45166.74\",\"13655.70\",\"\"]', NULL, '[\"2025-11-20\",\"\",\"\"]', '[\"7938.30\",\"2599\",\"3750\"]', '[\"GMG\",\"CREDIFACIL\",\"CREDICITI\"]', '[\"45166.74\",\"8350.07\",\"13058.23\"]', NULL, NULL, NULL, NULL, '[\"\"]', '[\"\"]', '[\"\"]', '[\"\"]', NULL, 0.0000, 54000.00, 0.40, 21249.74, 2);

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
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_analisis_financiero_comerciante`
--

INSERT INTO `tb_analisis_financiero_comerciante` (`id`, `idsolicitud`, `canasta_basica`, `cantidad_promedio`, `monto_por_persona`, `personas_dependientes`, `gastos_alimentacion_canasta`, `efectivo_caja`, `dinero_banco`, `disponible_ab`, `cuentas_por_cobrar`, `inventario_mercaderia`, `productos_proceso`, `productos_terminados`, `inventarios_abc`, `bienes_muebles`, `propiedades`, `otros_activos`, `total_activos_fijos`, `total_activos`, `cuentas_pagar_proveedores`, `cuentas_pagar_corto_plazo`, `pasivo_no_corriente`, `total_pasivo`, `total_patrimonio`, `total_pasivo_patrimonio`, `ventas_contado`, `ventas_credito`, `ventas_totales`, `costos_venta`, `margen_bruto`, `gastos_generales`, `utilidad_operativa`, `cuota_periodica_estim`, `flujo_ventas_contado`, `flujo_recuperacion_credito`, `flujo_compras_contado`, `flujo_gastos_generales`, `flujo_negocio`, `flujo_otros_ingresos_fam`, `flujo_gastos_consumo_fam`, `flujo_otros_gastos`, `flujo_neto_disponible`, `gasto_local_alquiler`, `gasto_energia`, `gasto_agua`, `gasto_internet`, `gasto_seguridad`, `gasto_limpieza`, `gasto_personal_basico`, `total_gastos_fijos`, `oblig_largo_plazo1_fecha`, `oblig_largo_plazo1_cuota`, `oblig_largo_plazo1_inst`, `oblig_largo_plazo1_saldo`, `oblig_largo_plazo2_fecha`, `oblig_largo_plazo2_cuota`, `oblig_largo_plazo2_inst`, `oblig_largo_plazo2_saldo`, `oblig_largo_plazo3_fecha`, `oblig_largo_plazo3_cuota`, `oblig_largo_plazo3_inst`, `oblig_largo_plazo3_saldo`, `subtotal_oblig_largo_plazo`, `oblig_corto_plazo1_fecha`, `oblig_corto_plazo1_cuota`, `oblig_corto_plazo1_inst`, `oblig_corto_plazo1_saldo`, `oblig_corto_plazo2_fecha`, `oblig_corto_plazo2_cuota`, `oblig_corto_plazo2_inst`, `oblig_corto_plazo2_saldo`, `oblig_corto_plazo3_fecha`, `oblig_corto_plazo3_cuota`, `oblig_corto_plazo3_inst`, `oblig_corto_plazo3_saldo`, `subtotal_oblig_corto_plazo`, `costo_salario_ayudante`, `costo_transporte`, `costo_total_operacion`, `nivel_endeudamiento`, `capital_trabajo_neto`, `cobertura_deuda`, `created_at`, `updated_at`) VALUES
(1, 3, 0.00, 0, 0.00, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 0.00, NULL, 0.00, NULL, 0.00, NULL, 0.00, NULL, 0.00, NULL, 0.00, 0.00, NULL, 0.00, NULL, 0.00, NULL, 0.00, NULL, 0.00, NULL, 0.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.0000, 0.00, 0.0000, '2026-03-14 00:23:51', '2026-03-14 00:23:51');

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
(2, 'FLORES', 'DAYANA TÁMARA BLANDON', 'Bo.costarrica Rotonda Bello horizonte 1 cd oeste 4 cd norte ', '83582199', NULL, 3, '0011009940037C', NULL, NULL, 0, NULL, '1994-09-10', 31, 'Union libre', 'Juan Daniel Gómez Murillo ', '0010910011004N', NULL, '86335531', 1, 'Propia', 32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería Dayana ', 'Pulpería ', '78122671', 8, 5, 3800.00, 2400.00, 4800.00, NULL),
(3, 'Zamora', 'Ana Bellis Gutierrez', 'BO. Pantasma Entrada Principal Centro Comercial 3C. E. 1/2C.N', '57271671', NULL, 0, '0010801890029P', NULL, 1, 0, '2026-03-04 22:01:01', '1989-01-08', 37, 'casada', 'Luddin Sequeira Hernandez', NULL, 'Tecnico en Enderezado Y Pintura', '86294115', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Alemán', 'Aidan Joshua Jiménez', 'Residencial Villa Kelly k13 carretera a Masaya 3ra calle casa número 27', '83624422', NULL, 3, '0010407061032L', NULL, NULL, 0, NULL, '2006-07-04', 19, 'Soltero', NULL, NULL, NULL, NULL, 0, 'Familiar', 8, NULL, 'Ibex', 'Rotonda periodista, 400mt al sur ', NULL, 'Atención al cliente ', 1, NULL, 'Permanente', 17000.00, '1500', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Jara', 'Sérgio Antonio Hurtado', 'San Rafael del sur bo.pinol iglesia católica 4 1/2cd  al Sur ', '84996037', NULL, 3, '0021005610004M', NULL, NULL, 0, NULL, '1961-05-10', 64, 'Union libre', NULL, NULL, NULL, NULL, NULL, 'Propia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Distribuidora Sergio ', 'Venta de abarrotes ', '84996037', 18, NULL, 32000.00, 21400.00, 811600.00, NULL),
(6, 'alemán', 'Elena Auxiliadora reyes', 'Bo.19 de julio hotel Nicaragua 3c. S', '87895937', NULL, 3, '6111109620001x', NULL, NULL, 0, NULL, '1962-09-11', 63, 'Casado', 'Efrain artola arroliga ', '0012705600057U', 'Pensionado ', '78209417', 0, 'Propia', 42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería Elena reyes ', 'Comercio ', '87895937', 10, NULL, 6300.00, 4100.00, 167000.00, NULL),
(7, 'Rodríguez', 'Carmen Del Socorro Herrera', 'Recd altos de motastepe dela rotonda 1 1/2 al sur ', '86406122', NULL, 3, '6012706720003M', NULL, NULL, 0, NULL, '1972-06-27', 53, 'Soltero', NULL, NULL, NULL, NULL, NULL, 'Propia', 20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería las gemelas ', 'Venta de abarrotes ', NULL, NULL, NULL, 2800.00, 1900.00, 3800.00, NULL),
(8, 'Aguirre', 'Emeris Del Carmen Cruz', 'Semáforos de la asamblea nacional 1c al oeste 1/2c al norte 1/2 cuadra al oeste', '77961554', NULL, 3, '5611210680009N', NULL, NULL, 0, NULL, '1968-10-12', 57, 'Casado', NULL, NULL, NULL, NULL, 0, 'Propia', 36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Kioskito cristal', 'Negocio ', '77961554', 32, NULL, 12000.00, 5000.00, 40000.00, NULL),
(9, 'González', 'Rebeca Del Socorro Castillo', 'Bo.Nueva Nicaragua Cementerio milagro de Dios 3cd este1 cd norte ', '87610624', NULL, 3, '0012209850093N', NULL, NULL, 0, NULL, '1985-09-22', 40, 'Union libre', NULL, NULL, NULL, NULL, 1, 'Propia', 14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Variedades Castillo ', 'Variedades ', NULL, 4, NULL, 3200.00, 2100.00, 4200.00, NULL),
(10, 'ROSTRAN', 'BISMARK FRANCISCO RIVAS', 'B. LAURELES SUR DE LA DISTRIBUIDORA GRANDE 3 1/2 AL SUR ', '86687724', NULL, 3, '0012501760049T', NULL, NULL, 0, NULL, '1976-01-25', 50, 'Casado', 'TANIA LISBETH TELLEZ', '0072203880000L', 'Comerciante', '77785417', 1, 'Propia', 32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Super freno la sabana ', 'Negocio ', '86687724', 28, NULL, 50000.00, 15000.00, 90000.00, NULL),
(11, 'Ferrufino', 'Eduardo Antonio', 'Parque los cocos 150 M arriba mano derecha Residencial Bruselas casa A5', '75390140', NULL, 3, '6073010740003N', NULL, NULL, 0, NULL, '1974-10-30', 51, 'Soltero', NULL, NULL, NULL, NULL, 1, 'Familiar', 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Autolavado Hernández ', 'Negocio ', '75390140', 6, NULL, 58000.00, 15000.00, 450000.00, NULL),
(12, 'OBANDO', 'BELLY ALEXANDRA MEJIA', 'Bo.la esperanza plaza Julio Martinez 1 cd oeste 3 1/2sur 1 cd este', '89760607', NULL, 3, '0012205740000V', NULL, NULL, 0, NULL, '1974-05-22', 51, 'Union libre', NULL, NULL, NULL, NULL, NULL, 'Propia', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería belli', 'Venta de abarrotes ', NULL, 3, NULL, 3200.00, 2100.00, 4200.00, NULL),
(13, 'Hernández', 'Gema Cristina Alvarado', 'Bo. Sierra maestra ddf la surtidora Gastón 3c abajo 25v al sur ', '78497489', NULL, 3, '0011907031016S', NULL, NULL, 0, NULL, '2003-07-19', 22, 'Soltero', NULL, NULL, NULL, NULL, 1, 'Familiar', 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Distribuidora de cepillos', 'Negocio ', '78497489', 11, NULL, 8000.00, 1800.00, 30000.00, NULL),
(14, 'MARTINEZ', 'JADITH GABRIELA VALLEJOS', 'Bo.boer ddefue depósito vehicular 4cd este ', '88038173', NULL, 3, '4410609940003J', NULL, NULL, 0, NULL, '1994-09-06', 31, 'Soltero', NULL, NULL, NULL, NULL, NULL, 'Propia', 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería la chelita', 'Venta de abarrotes ', NULL, 3, NULL, 3600.00, 2400.00, NULL, NULL),
(15, 'ayerdis', 'Kenia Nohemí flores', 'Bo. Francisco Salazar semáforo mil metros 6c. 2ce 1/2cn.', '86182299', NULL, 3, '0010208971023j', NULL, NULL, 0, NULL, '1997-08-02', 28, 'Casado', 'Marcos natanael cuadra Vásquez ', '0012311981064n', '0', '58552435', 1, 'Propia', 27, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulperia Kenia ', 'Comercio ', '83182299', 4, NULL, 5900.00, 4000.00, 158000.00, NULL),
(16, 'Carrillo', 'Erick Ramirez', 'asasddassd', '2321312', NULL, 3, '0012702981004X', NULL, 0, 0, '2026-03-10 15:46:06', '2026-03-10', 0, 'Soltero', 'SDASADSDADA', 'asas', 'dasa', '221312', 2, 'Propia', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'dabdsab', 'casai', '12', 21212, 1, 12312.00, 2312.00, NULL, NULL),
(17, 'PALACIOS', 'EDEL JOSE VEGA', 'Resd San Sebastián, casa número 252', '81366667', NULL, 3, '0010702820040T', NULL, NULL, 0, NULL, '1982-02-07', 44, 'Casado', 'SANDRA DANELIA SEQUEIRA CASTILLO', '4412311770020U', 'Estilista', '84901646', 0, 'Propia', 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Elisha beauti salón ', 'Negocio ', '57020705', 15, NULL, 25000.00, 9000.00, 100000.00, NULL),
(18, 'CABRERA', 'CAUDIZ YAHOSKA NARVÁEZ', 'Barrio San Isidro de bola segundo porto del parque de ferias 1 1/2 al sur ', '75575593', NULL, 3, '0010410830048S', NULL, NULL, 0, NULL, '1983-10-04', 42, 'Soltero', NULL, NULL, NULL, NULL, 1, 'Familiar', 20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Variedades ya', 'Negocio ', '75575593', 2, NULL, 4500.00, 1800.00, 20000.00, NULL),
(19, 'AGUILAR', 'JONATHAN JESUS LAINEZ', 'Comarca los brasiles. Matadero casi que 2c oeste 2c norte', '84822212', NULL, 3, '2810509021012V', NULL, 0, 0, '2026-03-13 15:12:05', '2002-09-05', 23, 'Soltero', NULL, NULL, NULL, NULL, NULL, 'Familiar', 24, NULL, 'Ferrocon los brasiles', 'Km 15 carretera nueva a león ', '57800428', 'Conductor', 1, 8, 'Permanente', 11500.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'SOZA', 'ILEANA VICTORIA GUILLÉN', 'Villa fraternidad terminal ruta 119 1/2 al sur', '87772868', NULL, 3, '6161504810009R', NULL, NULL, 0, NULL, '1981-04-15', 44, 'Soltero', NULL, NULL, NULL, NULL, 1, 'Familiar', 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Sala de belleza Myzpa', 'Negocio ', '87772868', 10, NULL, 7000.00, 2500.00, 38000.00, NULL),
(21, 'OVIEDO', 'FELIX DAVID QUIROZ', 'K10 carretera vieja león 300 vrs al sur entrada del mini super ', '86290587', NULL, 3, '0010603830035M', NULL, NULL, 0, NULL, '1983-03-06', 43, 'Soltero', NULL, NULL, NULL, NULL, 1, 'Familiar', 20, NULL, 'Sanlasa', 'Semáforos de montoyo 5 al lago 1/2 abajo', '84325222', 'Técnico de telecomunicaciones ', 3, NULL, 'Permanente', 14000.00, '1000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'HERNÁNDEZ', 'MICHAEL ALEXANDER FIGUEROA', 'Bo. Santa rosa puente de desnivel carrera norte 10 vrs sur', '83837045', NULL, 3, '0012011840018V', NULL, NULL, 0, NULL, '1984-11-20', 41, 'Union libre', 'LILLIAM DEL SOCORRO MONTENEGRO URBINA ', '0030909950000P', NULL, '75290009', NULL, 'Propia', 41, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Tienda express ', 'Variedades ', NULL, 8, NULL, 5800.00, 3400.00, NULL, NULL),
(23, 'URBINA', 'SANDRA ELISA ALFARO', 'Plasma los cabros módulo 2 contigo a enacal', '85878058', NULL, 3, '0012012780003T', NULL, NULL, 0, NULL, '1978-12-20', 47, 'Union libre', NULL, NULL, NULL, NULL, NULL, 'Alquilada', 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Distribuidora Alfaro ', 'Venta de abarrotes ', NULL, 4, NULL, 8200.00, 4300.00, NULL, NULL),
(24, 'CUADRA', 'KARLA VANESSA PÉREZ', 'Bo.venezuela clínica Don Bosco 7 cd este 1/2 sur', '86702870', NULL, 3, '0012109740071W', NULL, NULL, 0, NULL, '1974-09-21', 51, NULL, NULL, NULL, NULL, NULL, NULL, 'Propia', 32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería Karla ', 'Venta de abarrotes ', NULL, NULL, NULL, 6200.00, 4108.00, NULL, NULL),
(25, 'FLORES', 'MERCEDES ELVIRA CUARESMA', 'Zonas 8 cuidad sandino costando Sur colegio Bella cruz ', '89613601', NULL, 3, '0012307680050x', NULL, NULL, 0, NULL, '1968-07-23', 57, 'Soltero', NULL, NULL, NULL, NULL, NULL, 'Propia', 50, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería Mercedes ', 'Venta de abarrotes ', '89613601', NULL, NULL, 3100.00, 1800.00, NULL, NULL),
(26, 'CARRIÓN', 'NANCY VERÓNICA ROMERO', 'Monumentos el calvario 1/2 cuadra al sur sabana grande ', '89264586', NULL, 3, '0010604850023U', NULL, NULL, 0, NULL, '1985-04-06', 40, 'Casado', 'SERGIO JOSÉ ZAMORA GONZALES ', '0011001850024K', 'Comerciante ', '86405746', 1, 'Propia', 40, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Delicias zarkeys', 'Negocio ', '89264586', 9, NULL, 35000.00, 23000.00, 750000.00, NULL),
(27, 'HERNÁNDEZ', 'FRANCIS ARICELA ARIAS', 'Bo. Frawley kmt8 1/2carrt  sur contigo carnic', '81023862', NULL, 3, '0012208031027S', NULL, NULL, 0, NULL, '2003-08-22', 22, 'Union libre', NULL, NULL, NULL, NULL, NULL, 'Propia', 21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Comedor FRANCIS', 'Cedor', NULL, NULL, NULL, 2600.00, 1900.00, 3800.00, NULL),
(28, 'CRUZ', 'SANTO ELIODORO VARGAS', 'Antiguo Cine ideal 3 cd oeste ', '828806760000N', NULL, 3, '4432104760000N', NULL, NULL, 0, NULL, '1976-04-21', 49, 'Union libre', NULL, NULL, NULL, NULL, NULL, 'Familiar', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Calzado Elio ', 'Venta de calzado ', NULL, 8, NULL, 3700.00, 2100.00, 4200.00, NULL);

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
(1, 1, 0, '', 15, '2026-03-03 09:43:20'),
(2, 2, 0, '', 15, '2026-03-03 10:35:51'),
(3, 3, 0, '', 15, '2026-03-03 15:03:36');

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
(9, 3, 'Televisor de 50p', 1, 'Samsung', '', '', 13000.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 16:53:33', NULL),
(10, 3, 'Juego de sofa', 1, '', '', '', 12000.00, '7', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 16:53:33', NULL),
(11, 3, 'Refrigeradora', 1, 'Lg', '', '', 8000.00, '6', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 16:53:33', NULL),
(12, 3, 'Comedor', 1, '', '', '', 10000.00, '5', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 16:53:33', NULL),
(13, 3, 'Computadora de mesa', 1, '', '', '', NULL, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 16:53:33', NULL),
(14, 1, 'Vitrina vertical', 1, '', '', '', 6000.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 17:06:37', NULL),
(15, 1, 'Vitrina orizontal', 1, '', '', '', 8000.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 17:06:37', NULL),
(16, 1, 'Tev plasma 42', 1, 'ElGI', '', '', 10000.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 17:06:37', NULL),
(17, 1, 'Refrigerador', 1, 'ElGi', '', '', 12000.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 17:06:37', NULL),
(18, 1, 'Lavadora', 1, 'Mabe', '', '', 11000.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 17:06:37', NULL),
(19, 1, 'Fricer', 1, 'Frigidare', '', '', 9000.00, '9', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 17:06:37', NULL),
(20, 1, 'Micro onda', 1, 'Duraban', '', '', 4200.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 17:06:37', NULL),
(21, 1, 'Bañó María', 1, '', '', '', 12300.00, '9', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 17:06:37', NULL),
(22, 9, 'Fricer gris doble puerta', 1, 'Premium levella', '', '', 25000.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 17:14:59', NULL),
(23, 9, 'Fricer', 1, 'Challenger', '', '', 4000.00, '5', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 17:14:59', NULL),
(24, 9, 'Vitrina horizontal', 1, '', '', '', 5000.00, '7', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 17:14:59', NULL),
(25, 9, 'Espejo patalion', 1, '', 'Rojo vino', '', 2000.00, '6', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 17:14:59', NULL),
(26, 9, 'Computadora', 1, 'Hp', 'Negro', '', 4000.00, '7', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-05 17:14:59', NULL),
(27, 10, 'Vitrina orizontal', 1, '', '', '', 6200.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-06 18:04:28', NULL),
(28, 10, 'Vitrina vertical', 2, '', '', '', 12800.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-06 18:04:28', NULL),
(29, 10, 'Fricer', 1, 'Frigidare', '', '', 10600.00, '9', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-06 18:04:28', NULL),
(30, 10, 'Tevplasma32pulgada', 1, 'RCA', '', '', NULL, '5600', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-06 18:04:28', NULL),
(31, 12, 'Tv plasma 36', 1, 'Top sony', '', '', 6200.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-06 20:49:22', NULL),
(32, 12, 'Fricer', 1, 'Tl Star', '', '', 10800.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-06 20:49:22', NULL),
(33, 12, 'Refrigerador', 1, 'Mabe', '', '', 11400.00, '9', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-06 20:49:22', NULL),
(34, 12, 'Vitrina caramelera', 1, '', '', '', 4200.00, '9', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-06 20:49:22', NULL),
(35, 12, 'Vitrina vertical', 1, '', '', '', 8200.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-06 20:49:22', NULL),
(36, 12, 'Fricer', 1, 'T Star', '', '', 80000.00, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-06 20:49:22', NULL),
(37, 13, 'Freezer', 1, '', '', '', 12000.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-07 00:49:30', NULL),
(38, 13, 'Máquina de torno', 1, '', '', '', 110000.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-07 00:49:30', NULL),
(39, 13, 'Freezer', 1, '', '', '', 12000.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-07 00:51:02', NULL),
(40, 13, 'Máquina de torno', 1, '', '', '', 110000.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-07 00:51:02', NULL),
(41, 2, 'Vitrinas horizontales y verticales', NULL, '', '', '', 2000.00, 'Buena', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-07 19:37:18', '2026-03-07 20:18:54'),
(42, 2, 'Estante metalico colo azul', NULL, 'Azul', '', '', 3500.00, 'Buena', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-07 19:37:18', '2026-03-07 20:18:54'),
(43, 2, 'Refrigerador vertical marca f', NULL, 'Gris', '', '', 3600.00, 'Buena', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-07 19:38:43', '2026-03-07 20:18:54'),
(44, 14, 'Vehículo', 1, 'Honda', 'Accor', '', 444840.00, '9', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-07 21:37:14', NULL),
(45, 14, 'Elevador de automóviles', 1, '', '', '', 203885.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-07 21:37:14', NULL),
(46, 10, 'Vitrina vertical', 2, '', '', '', 9500.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 13:59:47', NULL),
(47, 10, 'Plasma 32 pulgadas', 1, 'Elgi', '', '', 7500.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 13:59:47', NULL),
(48, 10, 'Vitrina orizontal', 1, '', '', '', 7400.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 13:59:47', NULL),
(49, 10, 'Fricer', 1, 'Frigidare', '', '', 10400.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 13:59:47', NULL),
(50, 12, 'Fricer', 1, 'Fogel rojo', '', '', NULL, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 14:46:12', NULL),
(51, 12, 'Vitrina vertical', 1, '', '', '', 4300.00, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 14:46:12', NULL),
(52, 12, 'Refrigerador', 1, 'Mabe', '', '', 11700.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 14:46:12', NULL),
(53, 12, 'Fricer', 1, 'Frigidare', '', '', 11400.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 14:46:12', NULL),
(54, 17, 'Juego de sofá', 1, '', '', '', 15200.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 15:00:46', NULL),
(55, 17, 'Plasma 42pulg', 1, 'Aiwa', '', '', 12300.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 15:00:46', NULL),
(56, 17, 'Fricer', 1, 'Frigidare', '', '', 11400.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 15:00:46', NULL),
(57, 17, 'Fricer', 1, 'Frigidare', '', '', 12400.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 15:00:46', NULL),
(58, 18, 'Refrigeradora', 1, 'Tornado', 'Gris', '', 5500.00, '7', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 15:04:19', NULL),
(59, 18, 'Smart tv 43 pulgadas', 1, 'Hisense', 'Negro', '', 3800.00, '9', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 15:04:19', NULL),
(60, 18, 'Fricer gris', 1, 'Norton', '', '', 8000.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 15:04:19', NULL),
(61, 18, 'Fricer gris', 1, 'Levella  premiun', '', '', 8000.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 15:04:19', NULL),
(62, 18, 'Estante metalico', 1, '', 'Crema', '', 7500.00, '7', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 15:04:19', NULL),
(63, 18, 'Vitrina panadera', 1, '', '', '', 800.00, '9', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 15:04:19', NULL),
(64, 18, 'Vitrina vertical', 1, '', '', '', 6000.00, '7', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 15:04:19', NULL),
(65, 18, 'Estante metalico', 1, '', '', '', 5600.00, '6', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 15:04:19', NULL),
(66, 15, 'Fricer', 1, 'Frigidare', '', '', 11500.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 15:39:33', NULL),
(67, 15, 'Fricer', 1, 'Frigidare', '', '', 14500.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 15:39:33', NULL),
(68, 15, 'Equipo', 1, '', '', '', 18200.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-10 15:39:33', NULL),
(69, 20, 'Módulo', 1, '', '', '', 930000.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-11 00:08:05', NULL),
(70, 21, 'Televisor', 1, 'Sony', '', '', 5000.00, '10', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 20:18:52', NULL),
(71, 21, 'Refrigeradora', 1, '', '', '', 8000.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 20:18:52', NULL),
(72, 21, 'Vitrina', 1, '', 'Vertical', '', 3500.00, '9', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 20:18:52', NULL),
(73, 21, 'Vitrina', 2, '', 'Horizontal', '', 5000.00, '9', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 20:18:52', NULL),
(74, 21, 'Frezzer', 1, '', '', '', 7000.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-13 20:18:52', NULL),
(75, 23, 'Sillas de sala de belleza', 3, '', '', '', 25000.00, '9', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 01:33:47', NULL),
(76, 23, 'Baño maria', 1, '', '', '', 12000.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 01:33:47', NULL),
(77, 23, 'Televisor', 1, 'Jvc', '', '', 6000.00, '9', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 01:33:47', NULL),
(78, 23, 'Freezer', 1, 'Premier', '', '', 8000.00, '7', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 01:33:47', NULL),
(79, 24, 'Lavadora', 1, 'LG', '', '', 8500.00, '7', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 02:14:41', NULL),
(80, 24, 'Televisores', 2, 'RCA', '', '', 9000.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 02:14:41', NULL),
(81, 24, 'Microondas', 1, 'Oster', '', '', 4000.00, '9', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 02:14:41', NULL),
(82, 24, 'Microondas', 1, 'Sankey', '', '', 5000.00, '9', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 02:14:41', NULL),
(83, 24, 'Teatro en casa', NULL, '', '', '', 2500.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 02:14:41', NULL),
(84, 24, 'Refrigeradora', 1, 'LG', '', '', 9000.00, '8', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-14 02:14:41', NULL);

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
(1, 42, 2, 'uploads/garantias/solicitud_2/8f031dda334e0cedcfe7609733416b15.jpeg', NULL, 1, '2026-03-07 14:37:18'),
(2, 43, 2, 'uploads/garantias/solicitud_2/3175d0cac1163aff779accc00e70f438.jpeg', NULL, 2, '2026-03-07 14:38:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_garantias_verificaciones`
--

CREATE TABLE `tb_garantias_verificaciones` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `posted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `actividad_esperada_observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_perfil_integral_cliente`
--

INSERT INTO `tb_perfil_integral_cliente` (`id`, `solicitud_id`, `nombre`, `primer_apellido`, `segundo_apellido`, `tipo_documento`, `numero_documento`, `fecha_nacimiento`, `telefono`, `celular`, `email`, `direccion`, `ciudad`, `estado_civil`, `ocupacion`, `empresa`, `ingreso_mensual`, `antiguedad_laboral`, `otros`, `created_at`, `updated_at`, `fecha_perfil`, `nivel_riesgo`, `tipo_ddc`, `en_su_propio_pais`, `es_funcionario_publico`, `cargo_funcionario`, `pais_emision_documento`, `categoria_otro`, `zona_cobertura`, `sitio_web_centro_trabajo`, `ingreso_mensual_usd`, `ingreso_mensual_cordobas`, `conyuge_profesion`, `conyuge_ocupacion_actual`, `conyuge_nombre_centro_trabajo`, `conyuge_direccion_centro_trabajo`, `conyuge_email_centro_trabajo`, `conyuge_sitio_web`, `conyuge_telefono_centro_trabajo`, `conyuge_fax_centro_trabajo`, `conyuge_apartado_postal`, `conyuge_ingreso_usd`, `conyuge_ingreso_cordobas`, `documento_legal_1_pais_emision`, `documento_legal_2_pais_emision`, `actividad_esperada_json`, `segundo_nombre`, `sexo`, `n_dependientes`, `nombre_conocido`, `pais_nacimiento`, `categoria_empleo`, `origen_fondos`, `proposito_relacion`, `actividad_esperada`, `conyuge_primer_nombre`, `conyuge_segundo_nombre`, `conyuge_primer_apellido`, `conyuge_segundo_apellido`, `conyuge_direccion`, `conyuge_telefono_domicilio`, `conyuge_celular`, `conyuge_email_personal`, `doc1_tipo`, `doc1_numero`, `doc1_registro`, `doc1_fecha_emision`, `doc1_vencimiento`, `doc2_tipo`, `doc2_numero`, `doc2_registro`, `doc2_fecha_emision`, `doc2_vencimiento`, `tipo_relacion`, `tipo_relacion_otro`, `origen_otros`, `numero_registro`, `fecha_emision_documento`, `fecha_vencimiento_documento`, `documento_legal_1_numero`, `documento_legal_1_fecha_emision`, `documento_legal_1_fecha_vencimiento`, `documento_legal_2_numero`, `documento_legal_2_fecha_emision`, `documento_legal_2_fecha_vencimiento`, `matriz_score`, `matriz_answers`, `actividad_esperada_observaciones`) VALUES
(1, 1, 'Erick Ramirez', 'Carrillo', NULL, NULL, '0012702981004X', '1997-02-27', '83582199', '83582199', NULL, 'Bo.costarrica Rotonda Bello horizonte 1 cd oeste 4 cd norte', NULL, 'Soltero', NULL, NULL, 13200.00, NULL, 'Sele visitó alos alrededor vecina Ingrid López comentó que la clienta tiene buen comportamiento', '2026-03-03 13:56:36', '2026-03-12 16:03:25', '2026-03-03', 'Alto', 'DDC-I', 0, 0, NULL, NULL, NULL, NULL, NULL, 131.00, 4800.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[{\"numero_transacciones\":\"12\",\"monto_promedio\":\"187.67\",\"periodo\":\"12 meses\"},{\"numero_transacciones\":\"\",\"monto_promedio\":\"\",\"periodo\":\"\"}]', NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '475', '[\"tipo_natural\",\"empleado\",\"estado\",\"garantia_hipotecaria\",\"edad_21_39\",\"pep_no\",\"frecuente_si\",\"zona_managua\",\"valor_usd_1500_2000\"]', NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

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
  `id_cheque` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `dato_adicional` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `tb_series_recibos`
--

INSERT INTO `tb_series_recibos` (`idserie`, `codigo`, `nombre`, `consecutivo`, `ultimo_emitido`, `created_on`, `updated_on`, `estado`) VALUES
(1, 'A', 'Serie A', 0, NULL, 1767622352, NULL, 1),
(2, 'B', 'Serie B', 0, NULL, 1767622352, NULL, 1);

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
  `logotipo` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_sistema`
--

INSERT INTO `tb_sistema` (`id`, `razon_social`, `email`, `web`, `direccion`, `telefonos`, `mensaje_ticket`, `idmoneda`, `fechaActualizacion`, `logotipo`) VALUES
(1, 'CREDIBLAMEN SYSTEM', 'info@crediblamen.group', 'www.crediblamen.group', 'Managua, Nicaragua', '0000-0000', 'Prestamos Rapidos y Faciles.', 1, '2025-12-30 12:48:11', '6302417859.png');

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
  `idasesor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_solicitudes`
--

INSERT INTO `tb_solicitudes` (`idsolicitud`, `apellidos`, `nombres`, `direccion`, `telefono`, `email`, `tipo_doc`, `numero_doc`, `comentarios`, `estado`, `fechaActualizacion`, `negocio_propio`, `negocio_antiguedad`, `matricula_permiso`, `cedula_vigente`, `ingreso_promedio_alto`, `ingreso_promedio_bajo`, `otros_ingresos`, `otros_ingresos_docs`, `ahorros`, `inventario_disponible`, `cuentas_por_cobrar`, `ventas_al_credito`, `porcentaje_recuperacion`, `gastos_fijos`, `gastos_operativos`, `margen_comercial`, `datos_personales`, `datos_conyuge`, `recibo_servicios`, `investigacion_vecinos`, `referencias_personales`, `barrio`, `municipio`, `tipo_credito`, `tipo_solicitud`, `estado_civil`, `uso_credito`, `analista`, `estado_aprobacion`, `fecha_solicitud`, `fuente_ingresos`, `telefono_trabajo`, `dni_conyuge`, `salario_conyuge`, `observaciones`, `giro_negocio`, `monto_solicitado`, `plazo_meses`, `frecuencia`, `tasa_interes`, `cuota_estim_estimada`, `cuota_estim_estimada_quincenal`, `garantia`, `otros_ingresos_detalle`, `ventas_promedio_diarios`, `ventas_promedio_mensual`, `detalle_inventario`, `cuentas_por_cobrar_amount`, `caja_amount`, `banco_amount`, `pago_alquiler`, `pago_trabajadores`, `energia`, `agua`, `internet`, `promotor`, `fecha_recepcion`, `ventas_dias_buenos`, `ventas_dias_malos`, `nombre_conyuge`, `ocupacion_conyuge`, `ingresos_conyuge`, `telefono_conyuge`, `numero_dependientes`, `fecha_nacimiento`, `edad`, `sexo`, `nombre_empresa`, `direccion_empresa`, `telefono_empresa`, `cargo_puesto`, `ingreso_mensual_neto`, `nombre_negocio`, `actividad_economica`, `ubicacion_negocio`, `telefono_negocio`, `numero_empleados`, `otros_gastos`, `es_nuevo`, `es_renovacion`, `tiempo_residir_anios`, `tiempo_residir_meses`, `condicion_vivienda`, `tiempo_empleo_anios`, `tiempo_empleo_meses`, `tipo_contrato`, `deducciones`, `tiempo_operacion_anios`, `tiempo_operacion_meses`, `propiedad_negocio`, `tipo_documento`, `ready_for_approval`, `rechazado`, `propuesta_tipos`, `ventas_dias_buenos_mask`, `ventas_dias_malos_mask`, `nombre_completo`, `comision_desembolso`, `edit_comment`, `rubro_credito`, `otros_ingresos_1_amount`, `otros_ingresos_1_margin`, `otros_ingresos_1_detalle`, `otros_ingresos_2_amount`, `otros_ingresos_2_margin`, `otros_ingresos_2_detalle`, `otros_ingresos_3_amount`, `otros_ingresos_3_margin`, `otros_ingresos_3_detalle`, `ventas_buenos_amount`, `ventas_malos_amount`, `declaro_verificacion`, `firma_solicitante`, `fecha_firma`, `energia_electrica`, `agua_potable`, `internet_telefonia`, `ddc_investigacion_campo`, `nombre_promotor`, `fecha_recepcion_solicitud`, `observaciones_promotor`, `destino_credito`, `idcliente`, `cuentas_por_cobrar_evidencia`, `gastos_personales`, `gastos_transporte`, `idasesor`) VALUES
(1, 'FLORES', 'DAYANA TÁMARA BLANDON', 'Bo.costarrica Rotonda Bello horizonte 1 cd oeste 4 cd norte ', '83582199', NULL, 3, '0011009940037C', NULL, NULL, '2026-03-13 16:55:45', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Union libre', NULL, NULL, 'pendiente', '2026-03-04 14:21:00', NULL, NULL, '0010910011004N', 0.00, NULL, 'Pulpería ', 1600.00, 12, 'Mensual', NULL, NULL, NULL, NULL, NULL, NULL, 4800.00, 'Arroz, café, pan, leche, gaseosas, medicamento, pollo, embutidos, detergente frijoles, aceite ', NULL, 9200.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3800, 2400, 'Juan Daniel Gómez Murillo ', NULL, NULL, '86335531', 1, '1994-09-10', 31, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería Dayana ', 'Pulpería ', 'Bo.costarrica Rotonda Bello horizonte 1 cd oeste 4 cd norte ', '78122671', NULL, '2300', 0, 1, 32, NULL, 'Propia', NULL, NULL, NULL, NULL, 8, 5, NULL, NULL, 0, 0, NULL, 53, 74, 'DAYANA TÁMARA BLANDON FLORES ', NULL, 'Modificaciones', 'Comercio', 8400.00, 60.00, 'Venta de fritanga', NULL, NULL, NULL, NULL, NULL, NULL, 3800.00, 2400.00, 1, NULL, NULL, 540.76, NULL, NULL, 'Sele visitó alos alrededor de la casa comentó vecina Ingrid Rocha que tiene 10 años de conocerla posee buen comportamiento de vesindario ', NULL, '2026-03-04', 'Sele visitó alos alrededor vecina Ingrid López comentó que la clienta tiene buen comportamiento ', 'Inversión', 2, NULL, 4600.00, 1200.00, 0),
(2, 'Zamora', 'Ana Bellis Gutierrez', 'Bo. Pantasma Entrada Principal Centro Comercial 3C. E. 1/2C. N.', '57271671', NULL, 3, '0010801890029P', NULL, NULL, '2026-03-13 20:56:02', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 25.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Casado', NULL, NULL, 'pendiente', '2026-02-26 14:23:00', NULL, NULL, '001-241089-0001M', 0.00, NULL, NULL, 1000.00, 10, 'Quincenal', 0.06, 70.72, NULL, NULL, NULL, NULL, 83200.00, 'Gaseosas, café, pan, arroz, azúcar, meneitos,caramelos, embutidos,etc', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3100, 2400, 'Luddin Sequeira Hernandez', 'Tecnico en Enderezado Y Pintura', NULL, '86294115', 2, '1989-01-08', 37, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería Ana', NULL, 'Bo. Pantasma Entrada Principal Centro Comercial 3C. E. 1/2C. N.', NULL, NULL, NULL, 0, 1, 34, NULL, 'Familiar', NULL, NULL, NULL, NULL, 6, NULL, NULL, NULL, 0, 0, '[\"3\"]', 120, 7, 'Ana Bellis Gutierrez Zamora', 0.0700, 'Se agregaron mas fotos', 'Comercio', 2100.00, 40.00, 'Clienta ofrece venta de desinfectantes', 1400.00, 20.00, 'clienta ofrece venta de carbón', 1200.00, 4.00, 'Venta de recargas', 3100.00, 2400.00, 1, NULL, '2026-02-26', 578.90, 0.00, 0.00, 'Se visito a los alrededores del negocio encontrándonos con la vecina Esperanza Collado y nos comenta que Ana Bellis tiene 37 años de conocerla es una buena vecina no ha presentado un mal comportamiento con los vecinos.', NULL, '2026-02-26', 'Ninguna', 'Capital de trabajo', 3, NULL, 5800.00, 1700.00, 0),
(3, 'Alemán', 'Aidan Joshua Jiménez', 'Residencial Villa Kelly k13 carretera a Masaya 3ra calle casa número 27', '83624422', NULL, 3, '0010407061032L', NULL, NULL, '2026-03-13 20:56:14', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Personales', NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-03-03 09:51:00', NULL, NULL, NULL, 0.00, NULL, 'Asalariado ', 800.00, 12, 'Quincenal', 0.06, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2006-07-04', 19, NULL, 'Ibex', 'Rotonda periodista, 400mt al sur ', NULL, 'Atención al cliente ', 17000.00, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 8, NULL, 'Familiar', 1, NULL, 'Permanente', 1500.00, NULL, NULL, NULL, NULL, 0, 0, '[\"9\"]', NULL, NULL, 'Aidan Joshua Jiménez Alemán ', 0.0700, 'Carta salarial', 'Personales (Asalariados)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Se investigó en la zona y vecinos indican que ya tiene varios años avisando en la propiedad indican que es un muchacho recto y enfocado ', NULL, '2026-03-05', 'Cliente se ve una persona honesta y muy recta.', 'Consumo', 4, NULL, NULL, NULL, 2),
(4, 'Jara', 'Sérgio Antonio Hurtado', 'San Rafael del sur bo.pinol iglesia católica 4 1/2cd  al Sur ', '84996037', NULL, 3, '0021005610004M', NULL, NULL, '2026-03-13 20:56:18', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Union libre', NULL, NULL, 'pendiente', '2026-03-02 00:15:00', NULL, NULL, NULL, 0.00, NULL, 'Distribuidora ', 5000.00, 12, 'Catorcenal', 0.05, 294.14, NULL, NULL, NULL, NULL, 811600.00, 'Arroz, aceite,panper, café, azúcar, gaseosa, medicamento, pollo, carne de Res,', NULL, 61800.00, NULL, NULL, 6200.00, NULL, NULL, NULL, NULL, NULL, 32000, 21400, NULL, NULL, NULL, NULL, NULL, '1961-05-10', 64, NULL, NULL, NULL, NULL, NULL, NULL, 'Distribuidora Sergio ', 'Venta de abarrotes ', 'Mercado municipal de San Rafael del sur ', '84996037', 1, NULL, 0, 1, NULL, NULL, 'Propia', NULL, NULL, NULL, NULL, 18, NULL, NULL, NULL, 0, 0, '[\"5\"]', 39, 88, 'Sérgio Antonio Hurtado Jara', 0.0500, NULL, 'Comercio', 11500.00, 40.00, 'Venta de pollo y carné ', 8400.00, 30.00, 'Venta de huevos ', 9400.00, 70.00, 'Venta de maiz', 32000.00, 21400.00, 1, NULL, NULL, 2506.00, NULL, NULL, NULL, NULL, '2026-03-02', NULL, 'Inversión', 5, NULL, 6200.00, 3100.00, 0),
(9, 'alemán', 'Elena Auxiliadora reyes', 'Bo.19 de julio hotel Nicaragua 3c. S', '87895937', NULL, 3, '6111109620001x', NULL, NULL, '2026-03-13 20:56:23', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Casado', NULL, NULL, 'pendiente', '2026-03-04 10:41:00', NULL, NULL, '0012705600057U', 0.00, NULL, 'Pulpería ', 2000.00, 14, 'Quincenal', 0.06, 111.59, NULL, NULL, NULL, NULL, 167000.00, 'Arroz azúcar aceite frijoles maruchan café gaseosa jugo power rapto aceite jabón queso crema huevo embutido ', 0.00, 6200.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, 6300, 4100, 'Efrain artola arroliga ', 'Pensionado ', NULL, '78209417', 0, '1962-09-11', 63, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería Elena reyes ', 'Comercio ', 'Bo.19 de julio hotel Nicaragua 3c. S', '87895937', 0, '8', 0, 1, 42, NULL, 'Propia', NULL, NULL, NULL, NULL, 10, NULL, NULL, NULL, 0, 0, '[\"4\"]', 103, 24, 'Elena Auxiliadora reyes alemán ', 0.0700, 'Sé cambió foto', 'Comercio', 800.00, 100.00, 'Clienta ofrece venta de frijoles cosidos ', 4000.00, 80.00, 'Clienta ofrece venta de perfumes ', 4100.00, 100.00, 'Clienta recibe ayuda de su esposo ', 6300.00, 4100.00, 1, NULL, NULL, 1415.17, 477.90, 0.00, 'Se pregunto Alós vecinos aledaños por doña Elena Auxiliadora donde nos dijeron que ella es la dueña del negocio desde hace 42 años en el barrio .', NULL, '2026-03-05', NULL, 'Inversión', 6, NULL, 2500.00, 500.00, 3),
(10, 'Rodríguez', 'Carmen Del Socorro Herrera', 'Recd altos de motastepe dela rotonda 1 1/2 al sur ', '86406122', NULL, 3, '6012706720003M', NULL, NULL, '2026-03-13 20:56:25', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-03-06 11:28:00', NULL, NULL, NULL, 0.00, NULL, 'Pulpería ', 800.00, 9, 'Quincenal', 0.06, 61.28, NULL, 'Hipotecaria', NULL, NULL, 3800.00, 'Arroz, azúcar, aceite, pan, huevos, medicamentos, gaseosa, galletas ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2800, 1900, NULL, NULL, NULL, NULL, NULL, '1972-06-27', 53, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería las gemelas ', 'Venta de abarrotes ', 'Recd altos de motastepe dela rotonda 1 1/2 al sur ', NULL, NULL, NULL, 0, 1, 20, NULL, 'Propia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '[\"2\"]', 113, 14, 'Carmen Del Socorro Herrera Rodríguez ', 0.0700, NULL, 'Comercio', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2800.00, 1900.00, 1, NULL, NULL, NULL, NULL, 1429.00, NULL, NULL, '2026-03-06', 'Sele visitó alos alrededor vecina aleyda traña comento que el cliente tiene buena referencia vecindaria ', 'Inversión', 7, NULL, 4600.00, 1800.00, 0),
(11, 'Aguirre', 'Emeris Del Carmen Cruz', 'Semáforos de la asamblea nacional 1c al oeste 1/2c al norte 1/2 cuadra al oeste', '77961554', NULL, 3, '5611210680009N', NULL, NULL, '2026-03-13 20:56:35', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 45.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Casado', NULL, NULL, 'pendiente', '2026-03-06 14:16:00', NULL, NULL, NULL, 0.00, NULL, 'Comida rápida ', 1000.00, 12, 'Mensual', 0.06, 125.11, 62.56, NULL, NULL, NULL, 40000.00, 'Gaseosas, jugos, agua fuente pura, meneitos, Maruchan, galletas reposterías, champú, desodorante, leche gelatina, mascarillas ,palitos, yuquitas', 0.00, 7000.00, NULL, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, 12000, 5000, NULL, NULL, NULL, NULL, 0, '1968-10-12', 57, NULL, NULL, NULL, NULL, NULL, NULL, 'Kioskito cristal', 'Negocio ', 'Entrada principal de emergencia de remesa, Bolonia ', '77961554', 1, '600', 1, 0, 36, NULL, 'Propia', NULL, NULL, NULL, NULL, 32, NULL, NULL, NULL, 0, 0, '[\"3\"]', 31, 96, 'Emeris Del Carmen Cruz Aguirre', 0.0700, 'el sistema ala hora de imprimir no me sale el calculo de la cuota', 'Comercio', 2000.00, 30.00, 'Clienta vende desayuno y almuerzos ', NULL, NULL, NULL, NULL, NULL, NULL, 12000.00, 5000.00, 1, NULL, '2026-03-06', 0.00, 0.00, 0.00, 'Se investigó en toda la zona y los mismos doctores indican que la señora tiene muchos años de tener el kiosco, que es una señora muy empática y amable ', NULL, '2026-03-06', 'Doña emeris es una señora muy recta y sentrada en su negocio, llena un orden muy bueno y es una persona muy amable ', 'Inversión', 8, NULL, 2000.00, 1500.00, 2),
(12, 'González', 'Rebeca Del Socorro Castillo', 'Bo.Nueva Nicaragua Cementerio milagro de Dios 3cd este1 cd norte ', '87610624', NULL, 3, '0012209850093N', NULL, NULL, '2026-03-13 20:58:03', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Union libre', NULL, NULL, 'pendiente', '2026-03-06 14:17:00', NULL, NULL, NULL, 0.00, NULL, 'Variedades ', 1200.00, 12, 'Quincenal', 0.06, 74.36, NULL, 'Hipotecaria', NULL, NULL, 4200.00, 'Arroz, azúcar,aceite,medicamento, pan gaseosa, detergente ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3200, 2100, NULL, NULL, NULL, NULL, 1, '1985-09-22', 40, NULL, NULL, NULL, NULL, NULL, NULL, 'Variedades Castillo ', 'Variedades ', 'Bo.Nueva Nicaragua Cementerio milagro de Dios 3cd este1 cd norte ', NULL, NULL, '4600', 0, 1, 14, NULL, 'Propia', NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL, 0, 0, '[\"3\"]', 85, 42, 'Rebeca Del Socorro Castillo González ', 0.0700, 'Sele ingresa fotos de el negocio', 'Comercio', 2800.00, NULL, 'Ofrece servicio de profesores de farmacia ', NULL, NULL, NULL, NULL, NULL, NULL, 3200.00, 2100.00, 1, NULL, NULL, NULL, 270.44, NULL, 'Sele visitó alos alrededor de la casa y vecino comento lesbia Martinez comento q tiene 13 años de conocer la', NULL, '2026-03-06', NULL, 'Inversión', 9, NULL, 3800.00, 1200.00, 0),
(13, 'ROSTRAN', 'BISMARK FRANCISCO RIVAS', 'B. LAURELES SUR DE LA DISTRIBUIDORA GRANDE 3 1/2 AL SUR ', '86687724', NULL, 3, '0012501760049T', NULL, NULL, '2026-03-14 16:25:21', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 65.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Casado', NULL, NULL, 'pendiente', '2026-03-06 18:18:00', NULL, NULL, '0072203880000L', 0.00, NULL, 'Taller de freno automotriz ', 1500.00, 12, 'Mensual', 0.06, 187.67, 93.84, NULL, NULL, NULL, 1150000.00, 'Cliente ofrece servicio de freno en sobremedida ', NULL, 15000.00, NULL, 0.00, 16000.00, NULL, NULL, NULL, NULL, NULL, 50000, 15000, 'TANIA LISBETH TELLEZ', 'Comerciante', NULL, '77785417', 1, '1976-01-25', 50, NULL, NULL, NULL, NULL, NULL, NULL, 'Super freno la sabana ', 'Negocio ', 'Restaurante el madroño 9 cuadras arriba, posta sabana grande ', '86687724', 2, NULL, 1, 0, 32, NULL, 'Propia', NULL, NULL, NULL, NULL, 28, NULL, NULL, NULL, 0, 0, '[\"4\"]', 115, 12, 'BISMARK FRANCISCO RIVAS ROSTRAN', 0.0700, 'fdghn', 'Servicios', 5000.00, 80.00, 'Cliente vende todo en accesorios para frenado, más aceites ', 1500.00, 45.00, 'Cliente vende gaseosas, jugos chiverias', NULL, NULL, NULL, 50000.00, 15000.00, 1, NULL, '2026-03-06', 1500.00, 0.00, 700.00, 'Se investigó en zonas cercanas e indican que ya tiene muchos años con el negocio de torno y de muy buen porte ', NULL, '2026-03-06', 'Al cliente le llegan bastante trabajo, muy ordenado con todas su facturas y se ve muy honesto', 'Inversión', 10, NULL, 1500.00, 1200.00, 0),
(14, 'Ferrufino', 'Eduardo Antonio', 'Parque los cocos 150 M arriba mano derecha Residencial Bruselas casa A5', '75390140', NULL, 3, '6073010740003N', NULL, NULL, '2026-03-13 20:58:11', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 75.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Hipotecarios', NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-03-07 14:36:00', NULL, NULL, NULL, 0.00, NULL, 'Auto lavado ', 10000.00, 36, 'Quincenal', 0.05, NULL, NULL, 'Hipotecaria', NULL, NULL, 450000.00, 'Clientes tiene negocio de autolavado donde ofrece servicios de 150 córdobas hasta los 350 dólares ', 0.00, 35000.00, NULL, 23310.00, 45000.00, NULL, NULL, NULL, NULL, NULL, 58000, 15000, NULL, NULL, NULL, NULL, 1, '1974-10-30', 51, NULL, NULL, NULL, NULL, NULL, NULL, 'Autolavado Hernández ', 'Negocio ', 'Entrada del motel Montezuma 150 M arriba mano derecha ', '75390140', 7, '450', 1, 0, 6, NULL, 'Familiar', NULL, NULL, NULL, NULL, 6, NULL, NULL, NULL, 0, 0, '[\"13\"]', 126, 1, 'Eduardo Antonio Ferrufino ', 0.0500, NULL, 'Servicios', 7000.00, 48.00, 'Clientes vende aceites, líquidos de frenos jabón líquido ', 15000.00, 50.00, 'Clientes vende gaseosas, cervezas Maruchanes, alitas, chiverias ', NULL, NULL, NULL, 58000.00, 15000.00, 1, NULL, '2026-03-07', 6000.00, 2500.00, 1640.00, 'Se hizo la investigación a los alrededores del negocio y los habitantes indican que la persona ya tiene 6 años con el negocio y con el pasar de los años van  mejorando el lugar y que siempre se mantiene lleno', NULL, '2026-03-07', 'En el tiempo que se hizo el llenado se mantuvo demasiado lleno alrededor de 18 carros con lavado full el cliente es muy serio y sentrado, cada cosa que vende o carro que se lava lo lleva apuntado, es muy ordenado ', 'Inversión', 11, NULL, 2000.00, 500.00, 2),
(15, 'OBANDO', 'BELLY ALEXANDRA MEJIA', 'Bo.la esperanza plaza Julio Martinez 1 cd oeste 3 1/2sur 1 cd este', '89760607', NULL, 3, '0012205740000V', NULL, NULL, '2026-03-13 21:51:58', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Union libre', NULL, NULL, 'pendiente', '2026-03-09 13:06:00', NULL, NULL, NULL, 0.00, NULL, 'Pulpería ', 1200.00, 10, 'Mensual', 0.06, 100.78, NULL, NULL, NULL, NULL, 80600.00, 'Arroz, azúcar, aceite medicamento, galletas, pan, gaseosa ', NULL, 6500.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3200, 2100, NULL, NULL, NULL, NULL, NULL, '1974-05-22', 51, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería belli', 'Venta de abarrotes ', 'Bo.la esperanza plaza Julio Martinez 1 cd oeste 3 1/2sur 1 cd este', NULL, NULL, '1200', 0, 1, 4, NULL, 'Propia', NULL, NULL, NULL, NULL, 3, NULL, NULL, NULL, 0, 0, '[\"3\"]', 53, 74, 'BELLY ALEXANDRA MEJIA OBANDO ', 0.0700, 'calculo de', 'Comercio', 2300.00, 40.00, 'Venta de frijoles cosido ', 3200.00, 60.00, 'Venta de refresco natural ', NULL, NULL, NULL, 3200.00, 2100.00, 1, NULL, NULL, 486.48, NULL, NULL, 'Sele visitó alos alrededor de la casa y vecino comento elisa Tinoco comenta q tiene 4 años de conocer cliente posee buen comportamiento ', NULL, '2026-03-09', NULL, 'Inversión', 12, NULL, 5200.00, 1800.00, 0),
(16, 'Hernández', 'Gema Cristina Alvarado', 'Bo. Sierra maestra ddf la surtidora Gastón 3c abajo 25v al sur ', '78497489', NULL, 3, '0011907031016S', NULL, NULL, '2026-03-16 15:41:03', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 60.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-03-09 13:16:00', NULL, NULL, NULL, 0.00, NULL, 'Cepillos de lavar ropa ', 300.00, 12, 'Mensual', 0.08, 41.56, 20.78, NULL, NULL, NULL, 178000.00, 'Cliente vende cepillos de lavar ropa al detalle y por mayor ', NULL, NULL, NULL, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, 8000, 1800, NULL, NULL, NULL, NULL, 1, '2003-07-19', 22, NULL, NULL, NULL, NULL, NULL, NULL, 'Distribuidora de cepillos', 'Negocio ', 'Bo. Sierra maestra ddf la surtidora Gastón 3c abajo 25v al sur ', '78497489', 0, NULL, 1, 0, 5, NULL, 'Familiar', NULL, NULL, NULL, NULL, 11, NULL, NULL, NULL, 0, 0, '[\"1\"]', 124, 3, 'Gema Cristina Alvarado Hernández ', 0.0700, 'ferggf', NULL, 6000.00, 70.00, 'Clientes, alquila cuarto en su vivienda tiene 6 habitaciones disponibles pero solo 2 están siendo rentadas por el momento ', NULL, NULL, NULL, NULL, NULL, NULL, 8000.00, 1800.00, 1, NULL, '2026-03-09', 652.00, NULL, 0.00, 'Los vecinos indican que la muchacha distribuye los cepillos de lavar ropa desde ya hace varios años y siempre se le vende', NULL, '2026-03-09', 'Se encontró a la muchacha trabajando haciendo los cepillos porque tenía varios encargos ', NULL, 13, NULL, 1200.00, 308.00, 2),
(17, 'MARTINEZ', 'JADITH GABRIELA VALLEJOS', 'Bo.boer ddefue depósito vehicular 4cd este ', '88038173', NULL, 3, '4410609940003J', NULL, NULL, '2026-03-18 21:37:18', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-03-09 15:00:00', NULL, NULL, NULL, 0.00, NULL, 'Pulpería ', 500.00, 6, 'Mensual', 0.06, 107.51, 53.76, NULL, NULL, NULL, 91200.00, 'Arroz, azúcar, aceite, medicamento, galletas, café, pan, huevos, gaseosa ', NULL, 9800.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3600, 2400, NULL, NULL, NULL, NULL, NULL, '1994-09-06', 31, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulpería la chelita', 'Venta de abarrotes ', 'Bo.boer ddefue depósito vehicular 4cd este ', NULL, NULL, '1600', 1, 0, 10, NULL, 'Propia', NULL, NULL, NULL, NULL, 3, NULL, NULL, NULL, 0, 0, '[\"2\"]', 101, 26, 'JADITH GABRIELA VALLEJOS MARTINEZ ', 0.0700, 'calcular cuota', 'Comercio', 7000.00, NULL, 'Recibe pensión x viudez ', 6800.00, 40.00, 'Venta de productos ferretería ', NULL, NULL, NULL, 3600.00, 2400.00, 1, NULL, NULL, 460.24, NULL, NULL, 'Sele visitó alos alrededor de la casa y vecina Johana valle comenta q tiene 10 años de conocer a la clienta y q tiene buen comportamiento vecindario', NULL, '2026-03-09', NULL, 'Inversión', 14, NULL, 6400.00, 1400.00, 0),
(18, 'ayerdis', 'Kenia Nohemí flores', 'Bo. Francisco Salazar semáforo mil metros 6c. 2ce 1/2cn.', '86182299', NULL, 3, '0010208971023j', NULL, NULL, '2026-03-13 14:45:19', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Casado', NULL, NULL, 'pendiente', '2026-03-10 07:33:00', NULL, NULL, '0012311981064n', 0.00, NULL, 'Pulpería ', 800.00, 10, 'Mensual', 0.06, 114.29, 57.15, NULL, NULL, NULL, 158000.00, 'Arroz azúcar aceite frijoles queso crema rapto leche gaseosa jugo power agua café maruchan papel higiénico ', 0.00, 5600.00, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, 5900, 4000, 'Marcos natanael cuadra Vásquez ', '0', NULL, '58552435', 1, '1997-08-02', 28, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulperia Kenia ', 'Comercio ', 'Bo. Francisco Salazar semáforo mil metros 6c. 2ce 1/2cn.', '83182299', 0, '0', 0, 1, 27, NULL, 'Propia', NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL, 0, 0, '[\"2\"]', 103, 24, 'Kenia Nohemí flores ayerdis', 0.0700, 'Se cambio', 'Comercio', 800.00, 100.00, 'Clienta ofrece venta de hielo ', NULL, NULL, NULL, NULL, NULL, NULL, 5900.00, 4000.00, 1, NULL, '2026-03-10', 716.08, 0.00, 0.00, 'Se pregunto Alós vecinos aledaños por doña Kenia donde me indican que ella es dueña del negocio de hace 4 años . recibo sale a nombre de su mamá  ', NULL, '2026-03-10', NULL, 'Inversión', 15, NULL, 2500.00, 500.00, 3),
(19, 'Carrillo', 'Erick Ramirez', 'asasddassd', '2321312', NULL, 3, '0012702981004X', NULL, NULL, '2026-03-13 20:58:30', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-03-10 09:42:00', NULL, NULL, 'asas', 0.00, NULL, 'Pulperia', 1500.00, 24, 'Mensual', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'fdasfad', 23.00, 221.00, 22.00, 231.00, 32.00, NULL, NULL, NULL, NULL, NULL, 12312, 2312, 'SDASADSDADA', 'dasa', NULL, '221312', 2, '2026-03-10', 0, NULL, NULL, NULL, NULL, NULL, NULL, 'dabdsab', 'casai', 'asjbakb', '12', 223, '223', 1, 0, 1, 1, 'Propia', NULL, NULL, NULL, NULL, 21212, 1, NULL, NULL, 0, 0, '[\"17\"]', NULL, NULL, 'Erick Ramirez Carrillo', NULL, NULL, NULL, 2312.00, 1.00, 'sdad', 312.00, 3.00, 'sdada', 23.00, 2.00, 'zcf', 12312.00, 2312.00, 1, NULL, NULL, 23.00, 332.00, 232.00, 'dafrf', NULL, NULL, 'dasd', NULL, 16, NULL, 23.00, 23.00, 2),
(20, 'PALACIOS', 'EDEL JOSE VEGA', 'Resd San Sebastián, casa número 252', '81366667', NULL, 3, '0010702820040T', NULL, NULL, '2026-03-13 20:30:24', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 55.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Hipotecarios', NULL, 'Casado', NULL, NULL, 'pendiente', '2026-03-10 17:45:00', NULL, NULL, '4412311770020U', 0.00, NULL, 'Salón de belleza ', 10000.00, 36, 'Mensual', 0.05, 618.23, 309.12, 'Hipotecaria', NULL, NULL, 100000.00, 'Clientes tiene todo tipo de productos para el cabello, para cara, lavado y planchado', 0.00, 18000.00, NULL, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, 25000, 9000, 'SANDRA DANELIA SEQUEIRA CASTILLO', 'Estilista', NULL, '84901646', 0, '1982-02-07', 44, NULL, NULL, NULL, NULL, NULL, NULL, 'Elisha beauti salón ', 'Negocio ', 'Plaza Barcelona, módulo 11 contigo a trasnica', '57020705', 0, NULL, 1, 0, 9, NULL, 'Propia', NULL, NULL, NULL, NULL, 15, NULL, NULL, NULL, 0, 0, '[\"13\"]', 124, 3, 'EDEL JOSE VEGA PALACIOS ', 0.0500, 'Modificacion', 'Hipotecario', 35000.00, 60.00, 'Cliente fabrica anillos, pulseras, cadenas ya sea de oro y plata ', 15000.00, 40.00, 'Cliente también tiene una venta de ropa, variedades ya sea de varón y mujer ', NULL, NULL, NULL, 25000.00, 9000.00, 1, NULL, '2026-03-10', 1800.00, 180.00, NULL, 'Se investigó en la plaza y vecinos cerca de si casa y dan buena referencia de la persona todos con cuerda con ser una persona muy enfocada en su trabajo, no tiene vicio y son cristianos ', NULL, '2026-03-10', 'Se observa al cliente muy sentrado respetuoso y sobre todo muy sincero tiene buen comportamiento ', 'Inversión', 17, NULL, 2500.00, 800.00, 0),
(21, 'CABRERA', 'CAUDIZ YAHOSKA NARVÁEZ', 'Barrio San Isidro de bola segundo porto del parque de ferias 1 1/2 al sur ', '75575593', NULL, 3, '0010410830048S', NULL, NULL, '2026-03-13 20:41:09', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-03-12 08:04:00', NULL, NULL, NULL, 0.00, NULL, 'Pulpería ', 900.00, 12, 'Mensual', 0.06, NULL, NULL, NULL, NULL, NULL, 20000.00, 'Azúcar, Maruchan, fósforos, meneitos, gaseosas, arroz, frijoles, aceites, todo de granos básicos.', 0.00, 6000.00, NULL, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, 4500, 1800, NULL, NULL, NULL, NULL, 1, '1983-10-04', 42, 'F', NULL, NULL, NULL, NULL, NULL, 'Variedades ya', 'Negocio ', 'Barrio San Isidro de bola segundo porto del parque de ferias 1 1/2 al sur ', '75575593', 0, '0', 1, 0, 20, NULL, 'Familiar', NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, 0, 0, '[\"2\"]', 124, 3, 'CAUDIZ YAHOSKA NARVÁEZ CABRERA', 0.0700, 'Guardar', 'Servicios', 3000.00, 50.00, 'Cliente tiene máquinas de traga monedas ', 2500.00, 45.00, 'Cliente vende aceites para carros y moto', 3500.00, 40.00, 'Clientes vende perfumes, útiles escolares, ropa interior, productos variados', 4500.00, 1800.00, 1, NULL, '2026-03-12', 304.00, 0.00, 0.00, 'Se hizo la investigación en campo los vecinos cercanos indican que ya tienen tiempo con la pulpería y nunca han tenido problema ya que son personas muy tranquilas', NULL, '2026-03-13', 'Se observa que doña caudiz es una persona muy tranquila y muy eficaz con su negocio tiene todo en orden y en el tiempo que estuve ahí se vio movimiento de clientes ', 'Inversión', 18, NULL, 1000.00, 0.00, 2),
(22, 'AGUILAR', 'JONATHAN JESUS LAINEZ', 'Comarca los brasiles. Matadero casi que 2c oeste 2c norte', '84822212', NULL, 3, '2810509021012V', NULL, NULL, '2026-03-13 20:40:07', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Personales', NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-03-13 09:03:00', NULL, NULL, NULL, 0.00, NULL, 'Asalariado', 300.00, 6, 'Mensual', 0.08, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2002-09-05', 23, 'M', 'Ferrocon los brasiles', 'Km 15 carretera nueva a león ', '57800428', 'Conductor', 11500.00, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 24, NULL, 'Familiar', 1, 8, 'Permanente', NULL, NULL, NULL, NULL, NULL, 0, 0, '[\"8\"]', NULL, NULL, 'JONATHAN JESUS LAINEZ AGUILAR', 0.0700, 'Guardar.', 'Personales (Asalariados)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2026-03-13', NULL, NULL, NULL, NULL, NULL, '2026-03-13', NULL, 'Construcción', 19, NULL, 3000.00, 500.00, 1),
(23, 'SOZA', 'ILEANA VICTORIA GUILLÉN', 'Villa fraternidad terminal ruta 119 1/2 al sur', '87772868', NULL, 3, '6161504810009R', NULL, NULL, '2026-03-14 01:23:55', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-03-13 19:03:00', NULL, NULL, NULL, 0.00, NULL, 'Salón de belleza', 1000.00, 12, 'Mensual', 0.06, 125.11, 62.56, NULL, NULL, NULL, 38000.00, 'Cliente ofrece todo tipo de productos para el cabello y todo tipo de cosméticos para el trabajo de estilismo', 0.00, 8800.00, NULL, 0.00, 12000.00, NULL, NULL, NULL, NULL, NULL, 7000, 2500, NULL, NULL, NULL, NULL, 1, '1981-04-15', 44, 'F', NULL, NULL, NULL, NULL, NULL, 'Sala de belleza Myzpa', 'Negocio ', 'Villa fraternidad terminal ruta 119 1/2 al sur', '87772868', 2, '0', 1, 0, 12, NULL, 'Familiar', NULL, NULL, NULL, NULL, 10, NULL, NULL, NULL, 0, 0, '[\"3\"]', 125, 2, 'ILEANA VICTORIA GUILLÉN SOZA ', 0.0700, NULL, 'Comercio', 32000.00, 50.00, 'Cliente también vende comidas todos los días excepto el día martes ', NULL, NULL, NULL, NULL, NULL, NULL, 7000.00, 2500.00, 1, NULL, '2026-03-13', 2000.00, 0.00, 600.00, 'Se hizo la investigación en campo y los vecinos indican que siempre se mantienen atendiendo el negocio que se destaca por ser una mujer emprendedora y sobre todo muy humilde ', NULL, '2026-03-13', 'Se observa que la cliente es muy activa en su negocio casi no para porque atiende sus 2 negocio a pesar que tiene personas que le ayuda, la zona es muy movida', 'Inversión', 20, NULL, 1200.00, 0.00, 0),
(24, 'OVIEDO', 'FELIX DAVID QUIROZ', 'K10 carretera vieja león 300 vrs al sur entrada del mini super ', '86290587', NULL, 3, '0010603830035M', NULL, NULL, '2026-03-14 02:04:26', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Créditos Personales', NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-03-13 19:37:00', NULL, NULL, NULL, 0.00, NULL, 'Asalariado ', 600.00, 12, 'Mensual', 0.06, 75.07, 37.53, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '1983-03-06', 43, 'M', 'Sanlasa', 'Semáforos de montoyo 5 al lago 1/2 abajo', '84325222', 'Técnico de telecomunicaciones ', 14000.00, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 20, NULL, 'Familiar', 3, NULL, 'Permanente', 1000.00, NULL, NULL, NULL, NULL, 0, 0, '[\"9\"]', NULL, NULL, 'FELIX DAVID QUIROZ OVIEDO ', 0.0700, NULL, 'Personales (Asalariados)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2026-03-13', 600.00, 300.00, 600.00, 'Se hizo la investigación y los vecinos indican que es un buen hombre y no se mete con nadie que hasta donde conocen nunca a quedado mal', NULL, '2026-03-13', 'Se observa que el cliente es muy honesto y trabajador, tiene enfoque en prosperar y salir adelante ', 'Inversión', 21, NULL, 1200.00, 700.00, 0),
(25, 'HERNÁNDEZ', 'MICHAEL ALEXANDER FIGUEROA', 'Bo. Santa rosa puente de desnivel carrera norte 10 vrs sur', '83837045', NULL, 3, '0012011840018V', NULL, NULL, '2026-03-17 15:14:08', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Union libre', NULL, NULL, 'pendiente', '2026-03-17 09:00:00', NULL, NULL, '0030909950000P', 0.00, NULL, 'Pulpería ', 1000.00, 12, 'Mensual', 0.06, 125.11, 62.56, NULL, NULL, NULL, NULL, 'Café,pan, huevo, arroz, azúcar, detergente, medicamento, gaseosa, galletas, aceite,', NULL, 8600.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5800, 3400, 'LILLIAM DEL SOCORRO MONTENEGRO URBINA ', NULL, NULL, '75290009', NULL, '1984-11-20', 41, 'M', NULL, NULL, NULL, NULL, NULL, 'Tienda express ', 'Variedades ', 'Bo. Santa rosa puente de desnivel carrera norte 10 vrs sur', NULL, NULL, '1800', 0, 1, 41, NULL, 'Propia', NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, 0, 0, '[\"3\"]', 57, 70, 'MICHAEL ALEXANDER FIGUEROA HERNÁNDEZ ', 0.0700, NULL, 'Comercio', 4800.00, 10.00, 'Venta de agua de botellón ', 2400.00, 4.00, 'Venta de recarga ', 3400.00, 70.00, 'Venta de perfumes y cosméticos ', 5800.00, 3400.00, 1, NULL, '2026-03-17', 3207.00, NULL, NULL, NULL, NULL, '2026-03-17', NULL, 'Inversión', 22, NULL, 4600.00, 800.00, 2),
(26, 'HERNÁNDEZ', 'MICHAEL ALEXANDER FIGUEROA', 'Bo. Santa rosa puente de desnivel carrera norte 10 vrs sur', '83837045', NULL, 3, '0012011840018V', NULL, NULL, '2026-03-21 14:48:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Union libre', NULL, NULL, 'pendiente', '2026-03-17 09:00:00', NULL, NULL, '0030909950000P', 0.00, NULL, 'Pulpería ', 1000.00, 12, 'Mensual', NULL, NULL, NULL, NULL, NULL, NULL, 140400.00, 'Café,pan, huevo, arroz, azúcar, detergente, medicamento, gaseosa, galletas, aceite,', NULL, 8600.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5800, 3400, 'LILLIAM DEL SOCORRO MONTENEGRO URBINA ', NULL, NULL, '75290009', NULL, '1984-11-20', 41, 'M', NULL, NULL, NULL, NULL, NULL, 'Tienda express ', 'Variedades ', 'Bo. Santa rosa puente de desnivel carrera norte 10 vrs sur', NULL, NULL, '1800', 0, 1, 41, NULL, 'Propia', NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, 0, 0, NULL, 57, 70, 'MICHAEL ALEXANDER FIGUEROA HERNÁNDEZ ', NULL, 'Modificacion', 'Comercio', 4800.00, 10.00, 'Venta de agua de botellón ', 2400.00, 4.00, 'Venta de recarga ', 3400.00, 70.00, 'Venta de perfumes y cosméticos ', 5800.00, 3400.00, 1, NULL, '2026-03-17', 3207.00, NULL, NULL, NULL, NULL, '2026-03-17', NULL, 'Inversión', 22, NULL, 4600.00, 800.00, 2),
(27, 'URBINA', 'SANDRA ELISA ALFARO', 'Plasma los cabros módulo 2 contigo a enacal', '85878058', NULL, 3, '0012012780003T', NULL, NULL, '2026-03-21 14:47:23', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Union libre', NULL, NULL, 'pendiente', '2026-03-17 09:24:00', NULL, NULL, NULL, 0.00, NULL, 'Distribuidora ', 1000.00, 12, 'Mensual', 0.06, NULL, NULL, NULL, NULL, NULL, 191400.00, 'Arroz, azúcar, detergente, medicamento, galletas, gaseosa, pollo, carne de res y cerdo, embutidos ', NULL, 10100.00, NULL, 3700.00, NULL, NULL, NULL, NULL, NULL, NULL, 8200, 4300, NULL, NULL, NULL, NULL, NULL, '1978-12-20', 47, 'F', NULL, NULL, NULL, NULL, NULL, 'Distribuidora Alfaro ', 'Venta de abarrotes ', 'Plasma los cabros módulo 2 contigo a enacal', NULL, NULL, '2300', 0, 1, 10, NULL, 'Alquilada', NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL, 0, 0, '[\"3\"]', 57, 70, 'SANDRA ELISA ALFARO URBINA ', 0.0700, 'Modificacion', 'Comercio', 12300.00, 70.00, 'Venta de comida ', 8300.00, 60.00, 'Venta de pollo y carne ', 3500.00, 40.00, 'Venta de ropa usada ', 8200.00, 4300.00, 1, NULL, '2026-03-17', NULL, 178.00, NULL, 'Sele ISO visita a los alrededores con Joel Salinas comenta q tiene 15 años de conocer a la clienta y q tiene buen comportamiento ', NULL, '2026-03-17', NULL, 'Inversión', 23, NULL, 4800.00, 1300.00, 2),
(28, 'CUADRA', 'KARLA VANESSA PÉREZ', 'Bo.venezuela clínica Don Bosco 7 cd este 1/2 sur', '86702870', NULL, 3, '0012109740071W', NULL, NULL, '2026-03-21 14:46:35', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, NULL, NULL, NULL, 'pendiente', '2026-03-21 08:29:00', NULL, NULL, NULL, 0.00, NULL, 'Pulpería ', 2300.00, 12, 'Mensual', 0.06, 328.60, 164.30, NULL, NULL, NULL, 165080.00, 'Arroz, azúcar, detergente, medicamento, gaseosa, galletas, aceite,pan, huevos ', NULL, 10400.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6200, 4108, NULL, NULL, NULL, NULL, NULL, '1974-09-21', 51, 'F', NULL, NULL, NULL, NULL, NULL, 'Pulpería Karla ', 'Venta de abarrotes ', 'Bo.venezuela clínica Don Bosco 7 cd este 1/2 sur', NULL, NULL, '1800', 0, 1, 32, NULL, 'Propia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '[\"4\"]', 117, 10, 'KARLA VANESSA PÉREZ CUADRA ', 0.0700, 'Modificacion', 'Comercio', 7100.00, 80.00, 'Venta de frijoles cocidos ', 2400.00, 60.00, 'Venta de carbón ', 4800.00, 70.00, 'Venta de ropa interior ', 6200.00, 4108.00, 1, NULL, '2026-03-21', 2408.00, NULL, NULL, 'Sele visitó alos alrededor de la casa y vecinoa Fátima Espinoza comenta q tiene 20 años de conocer al cliente y q posee buen comportamiento ', NULL, '2026-03-21', NULL, 'Inversión', 24, NULL, 4600.00, 1600.00, 2),
(29, 'FLORES', 'MERCEDES ELVIRA CUARESMA', 'Zonas 8 cuidad sandino costando Sur colegio Bella cruz ', '89613601', NULL, 3, '0012307680050x', NULL, NULL, '2026-03-27 14:37:24', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-03-27 08:08:00', NULL, NULL, NULL, 0.00, NULL, 'Pulpería ', 700.00, 6, 'Mensual', 0.06, 150.52, 75.26, NULL, NULL, NULL, NULL, 'Arroz, azúcar, detergente medicamento Café útiles escolares ', NULL, 61400.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3100, 1800, NULL, NULL, NULL, NULL, NULL, '1968-07-23', 57, 'F', NULL, NULL, NULL, NULL, NULL, 'Pulpería Mercedes ', 'Venta de abarrotes ', 'Zonas 8 cuidad sandino costando Sur colegio Bella cruz ', '89613601', NULL, '1800', 0, 1, 50, NULL, 'Propia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '[\"2\"]', 113, 14, 'MERCEDES ELVIRA CUARESMA FLORES ', 0.0700, 'Se edito la fórmula de cuota', 'Comercio', 1800.00, 80.00, 'Cliente ofrece servicio de fotocopias ', 3800.00, 70.00, 'Venta de nacatamales', 2800.00, 70.00, 'Venta de sandalias ', 3100.00, 1800.00, 1, NULL, NULL, NULL, 698.00, NULL, NULL, NULL, NULL, NULL, 'Consumo', 25, NULL, 2100.00, 3800.00, NULL),
(30, 'CARRIÓN', 'NANCY VERÓNICA ROMERO', 'Monumentos el calvario 1/2 cuadra al sur sabana grande ', '89264586', NULL, 3, '0010604850023U', NULL, NULL, '2026-03-28 05:05:24', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 65.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Casado', NULL, NULL, 'pendiente', '2026-03-27 22:55:00', NULL, NULL, '0011001850024K', 0.00, NULL, 'Comedor ', 2400.00, 12, 'Mensual', 0.06, 300.26, 150.13, NULL, NULL, NULL, 750000.00, 'Cliente ofrece todo tipo de comida, desayuno almuerzo y cenas', 0.00, 23000.00, 0.00, 0.00, 45000.00, NULL, NULL, NULL, NULL, NULL, 35000, 23000, 'SERGIO JOSÉ ZAMORA GONZALES ', 'Comerciante ', NULL, '86405746', 1, '1985-04-06', 40, 'F', NULL, NULL, NULL, NULL, NULL, 'Delicias zarkeys', 'Negocio ', 'Cruze sabana grande 5 cuadras al sur ', '89264586', 5, '0', 1, 0, 40, NULL, 'Propia', NULL, NULL, NULL, NULL, 9, NULL, NULL, NULL, 0, 0, '[\"4\"]', 123, 4, 'NANCY VERÓNICA ROMERO CARRIÓN', 0.0700, NULL, 'Comercio', 5000.00, 48.00, 'Cliente ofrece venta de eskimos ', NULL, NULL, NULL, NULL, NULL, NULL, 35000.00, 23000.00, 1, NULL, '2026-03-27', 5000.00, 0.00, 1800.00, 'Se hizo la investigación de campo en zonas aledañas y las personas indican que es un comedor muy movido e indican que en la noche se llena demasiado ', NULL, '2026-03-27', 'Excelente negocio hay demasiado movimiento y está en un lugar muy visibles sobre la pista, se observa mucho movimiento ', 'Inversión', 26, NULL, 2500.00, 800.00, 0),
(31, 'HERNÁNDEZ', 'FRANCIS ARICELA ARIAS', 'Bo. Frawley kmt8 1/2carrt  sur contigo carnic', '81023862', NULL, 3, '0012208031027S', NULL, NULL, '2026-03-28 14:26:16', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Union libre', NULL, NULL, 'pendiente', '2026-03-28 08:05:00', NULL, NULL, NULL, 0.00, NULL, 'Comedor ', 500.00, 6, 'Mensual', 0.06, NULL, NULL, NULL, NULL, NULL, 3800.00, 'Pollo, arroz, azúcar, gaseosa ', NULL, 5600.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2600, 1900, NULL, NULL, NULL, NULL, NULL, '2003-08-22', 22, 'F', NULL, NULL, NULL, NULL, NULL, 'Comedor FRANCIS', 'Cedor', 'Bo. Frawley kmt8 1/2carrt  sur contigo carnic', NULL, NULL, '1600', 1, 0, 21, NULL, 'Propia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '[\"2\"]', 15, 112, 'FRANCIS ARICELA ARIAS HERNÁNDEZ ', 0.0700, NULL, 'Comercio', 6800.00, NULL, 'Cliente recibe ayuda de su esposo ', 6800.00, NULL, NULL, NULL, NULL, NULL, 2600.00, 1900.00, 1, NULL, '2026-03-28', 719.00, NULL, NULL, 'Sele visitó alos alrededor con la vecina Carolina Mena comenta q la clienta no tiene problema con los vecinos ', NULL, '2026-03-28', NULL, 'Inversión', 27, NULL, 4800.00, 1400.00, 2),
(32, 'CRUZ', 'SANTO ELIODORO VARGAS', 'Antiguo Cine ideal 3 cd oeste ', '828806760000N', NULL, 3, '4432104760000N', NULL, NULL, '2026-03-28 14:47:43', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, 60.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Microcréditos', NULL, 'Union libre', NULL, NULL, 'pendiente', '2026-03-28 08:39:00', NULL, NULL, NULL, 0.00, NULL, NULL, 1000.00, 12, 'Mensual', 0.06, 125.11, 62.56, NULL, NULL, NULL, 4200.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3700, 2100, NULL, NULL, NULL, NULL, NULL, '1976-04-21', 49, 'M', NULL, NULL, NULL, NULL, NULL, 'Calzado Elio ', 'Venta de calzado ', 'Antiguo Cine ideal 3 cd oeste ', NULL, NULL, '1500', 0, 1, 4, NULL, 'Familiar', NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, 0, 0, '[\"3\"]', 120, 7, 'SANTO ELIODORO VARGAS CRUZ ', 0.0700, NULL, 'Comercio', 5800.00, 70.00, 'Cliente ofrece venta de accesorios para celular ', 4500.00, NULL, 'Cliente recibe ayuda de su esposa trabajadora docente ', NULL, NULL, NULL, 3700.00, 2100.00, 1, NULL, NULL, NULL, 303.40, NULL, NULL, NULL, '2026-03-28', 'Sele visitó alos alrededor de la casa y vecinoa Angi masías comenta q el cliente posee buen comportamiento ', 'Inversión', 28, NULL, 5200.00, 1608.00, 2);

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
(1, 2, 26, 'Diana Diana', 'edit', 'Documentación está incompleta ya que tengo que pedir las fotos individuales para continuar', '2026-03-04 22:13:59'),
(2, 9, 25, 'Roman Lainez Rlainez', 'edit', 'Agregue las imágenes de la fachada', '2026-03-05 17:26:42'),
(3, 9, 25, 'Roman Lainez Rlainez', 'edit', 'Cambie la foto', '2026-03-05 17:36:19'),
(4, 9, 25, 'Roman Lainez Rlainez', 'edit', 'Se agrego foto', '2026-03-05 17:40:32'),
(5, 9, 25, 'Roman Lainez Rlainez', 'edit', 'Sé cambió foto', '2026-03-05 17:42:59'),
(6, 9, 25, 'Roman Lainez Rlainez', 'edit', 'Sé cambió foto', '2026-03-05 17:44:03'),
(7, 12, 20, 'Carlos Mayeel Pineda cpineda', 'edit', 'Se agrego foto', '2026-03-07 13:57:33'),
(8, 2, 26, 'Diana Diana', 'edit', 'Se ingreso la documentación que estaba pendiente', '2026-03-07 19:27:32'),
(9, 2, 26, 'Diana Diana', 'edit', 'se agregaron los otros ingresos', '2026-03-07 19:47:47'),
(10, 2, 26, 'Diana Diana', 'edit', 'Se agregaron mas fotos', '2026-03-07 20:16:38'),
(11, 12, 20, 'Carlos Mayeel Pineda cpineda', 'edit', 'Modificaciones de imagenes', '2026-03-09 13:57:56'),
(12, 12, 20, 'Carlos Mayeel Pineda cpineda', 'edit', 'Modificaciones', '2026-03-09 13:58:35'),
(13, 12, 20, 'Carlos Mayeel Pineda cpineda', 'edit', 'Se modifico las fotos de archivos', '2026-03-09 14:38:52'),
(14, 18, 21, 'Roman Lainez Rlainez', 'edit', 'Sé agrego foto', '2026-03-10 13:57:11'),
(15, 18, 21, 'Roman Lainez Rlainez', 'edit', 'Sé agrego foto', '2026-03-10 13:57:18'),
(16, 18, 21, 'Roman Lainez Rlainez', 'edit', 'Se guardo foto', '2026-03-10 13:59:04'),
(17, 18, 21, 'Roman Lainez Rlainez', 'edit', 'Se agrego foto', '2026-03-10 14:00:01'),
(18, 18, 21, 'Roman Lainez Rlainez', 'edit', 'Se agrego foto', '2026-03-10 14:01:57'),
(19, 18, 21, 'Roman Lainez Rlainez', 'edit', 'Se agrego foto', '2026-03-10 14:01:59'),
(20, 18, 21, 'Roman Lainez Rlainez', 'edit', 'Se grego foto', '2026-03-10 14:03:14'),
(21, 18, 21, 'Roman Lainez Rlainez', 'edit', 'Se grego foto', '2026-03-10 14:03:16'),
(22, 18, 21, 'Roman Lainez Rlainez', 'edit', 'Se agrego información', '2026-03-10 14:05:34'),
(23, 12, 20, 'Carlos Mayeel Pineda cpineda', 'edit', 'Sele ingresa fotos de el negocio', '2026-03-10 14:49:41'),
(24, 17, 20, 'Carlos Mayeel Pineda cpineda', 'edit', 'Sele ingresa fotos de negocio', '2026-03-10 15:25:49'),
(25, 3, 25, 'Roman Lainez Rlainez', 'edit', 'Carta salarial', '2026-03-11 15:21:56'),
(26, 21, 25, 'Roman Lainez Rlainez', 'edit', 'Se puso el nombre de promotor', '2026-03-13 14:42:37'),
(27, 18, 21, 'Roman Lainez Rlainez', 'edit', 'Se cambio', '2026-03-13 14:45:19'),
(28, 18, 21, 'Roman Lainez Rlainez', 'edit', 'Se cambio', '2026-03-13 14:46:22'),
(29, 11, 25, 'Roman Lainez Rlainez', 'edit', 'Nombre de promotor', '2026-03-13 14:47:32'),
(30, 11, 26, 'Diana Diana', 'edit', 'el sistema ala hora de imprimir no me sale el calculo de la cuota', '2026-03-13 14:52:07'),
(31, 1, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Modificaciones', '2026-03-13 16:55:45'),
(32, 20, 26, 'Diana Diana', 'edit', 'ediccion de cuota', '2026-03-13 20:26:42'),
(33, 20, 26, 'Diana Diana', 'edit', 'dfcgb', '2026-03-13 20:27:43'),
(34, 20, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Modificacion', '2026-03-13 20:30:24'),
(35, 22, 26, 'Diana Diana', 'edit', 'Guardar.', '2026-03-13 20:40:07'),
(36, 21, 26, 'Diana Diana', 'edit', 'Guardar', '2026-03-13 20:40:36'),
(37, 15, 26, 'Diana Diana', 'edit', 'calculo de', '2026-03-13 21:51:58'),
(38, 13, 26, 'Diana Diana', 'edit', 'fdghn', '2026-03-14 16:25:21'),
(39, 16, 26, 'Diana Diana', 'edit', 'ferggf', '2026-03-16 15:41:03'),
(40, 17, 26, 'Diana Diana', 'edit', 'calcular cuota', '2026-03-18 21:37:18'),
(41, 28, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Modificacion', '2026-03-21 14:46:35'),
(42, 27, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Modificacion', '2026-03-21 14:47:23'),
(43, 26, 15, 'ADMINISTRADOR ADMINISTRADOR', 'edit', 'Modificacion', '2026-03-21 14:48:00'),
(44, 29, 20, 'Carlos Mayeel Pineda cpineda', 'edit', 'Se edito la fórmula de cuota', '2026-03-27 14:37:24');

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
(1, 1, 'solicitudes/1/cedula_front/1772658870_1e109233.jpg', NULL, NULL, '2026-03-04 16:14:30', 'cedula_front'),
(2, 1, 'solicitudes/1/cedula_back/1772658870_f4802d2b.jpg', NULL, NULL, '2026-03-04 16:14:30', 'cedula_back'),
(3, 3, 'solicitudes/3/cedula_front/1772726776_89211767.jpg', NULL, NULL, '2026-03-05 11:06:16', 'cedula_front'),
(4, 3, 'solicitudes/3/cedula_back/1772726776_0758f65e.jpg', NULL, NULL, '2026-03-05 11:06:16', 'cedula_back'),
(5, 4, 'solicitudes/4/cedula_front/1772728634_cac26a2c.jpg', NULL, NULL, '2026-03-05 11:37:14', 'cedula_front'),
(6, 4, 'solicitudes/4/cedula_back/1772728634_a5bd7de6.jpg', NULL, NULL, '2026-03-05 11:37:14', 'cedula_back'),
(7, 4, 'solicitudes/4/otros_ingresos_1/1772728634_705fac68.jpg', NULL, NULL, '2026-03-05 11:37:14', 'otros_ingresos_1'),
(8, 4, 'solicitudes/4/otros_ingresos_2/1772728634_12a61be7.jpg', NULL, NULL, '2026-03-05 11:37:14', 'otros_ingresos_2'),
(9, 4, 'solicitudes/4/otros_ingresos_3/1772728634_449825ed.jpg', NULL, NULL, '2026-03-05 11:37:14', 'otros_ingresos_3'),
(10, 5, 'solicitudes/5/cedula_front/1772728658_bdcc8a44.jpg', NULL, NULL, '2026-03-05 11:37:38', 'cedula_front'),
(11, 5, 'solicitudes/5/cedula_back/1772728658_86c18e21.jpg', NULL, NULL, '2026-03-05 11:37:38', 'cedula_back'),
(12, 5, 'solicitudes/5/otros_ingresos_1/1772728658_48052d17.jpg', NULL, NULL, '2026-03-05 11:37:38', 'otros_ingresos_1'),
(13, 5, 'solicitudes/5/otros_ingresos_2/1772728658_9278e0dc.jpg', NULL, NULL, '2026-03-05 11:37:38', 'otros_ingresos_2'),
(14, 5, 'solicitudes/5/otros_ingresos_3/1772728658_781a3f4b.jpg', NULL, NULL, '2026-03-05 11:37:38', 'otros_ingresos_3'),
(15, 6, 'solicitudes/6/cedula_front/1772728670_246ee09f.jpg', NULL, NULL, '2026-03-05 11:37:50', 'cedula_front'),
(16, 6, 'solicitudes/6/cedula_back/1772728670_cb4f9f3f.jpg', NULL, NULL, '2026-03-05 11:37:50', 'cedula_back'),
(17, 6, 'solicitudes/6/otros_ingresos_1/1772728670_4dedf0c9.jpg', NULL, NULL, '2026-03-05 11:37:50', 'otros_ingresos_1'),
(18, 6, 'solicitudes/6/otros_ingresos_2/1772728670_5a51becf.jpg', NULL, NULL, '2026-03-05 11:37:50', 'otros_ingresos_2'),
(19, 6, 'solicitudes/6/otros_ingresos_3/1772728670_645c654b.jpg', NULL, NULL, '2026-03-05 11:37:50', 'otros_ingresos_3'),
(20, 7, 'solicitudes/7/otros_ingresos_1/1772730102_9a645bd4.jpg', NULL, NULL, '2026-03-05 12:01:42', 'otros_ingresos_1'),
(21, 8, 'solicitudes/8/cedula_front/1772730132_6ecbaa72.jpg', NULL, NULL, '2026-03-05 12:02:12', 'cedula_front'),
(22, 8, 'solicitudes/8/cedula_back/1772730132_135f1925.jpg', NULL, NULL, '2026-03-05 12:02:12', 'cedula_back'),
(23, 8, 'solicitudes/8/otros_ingresos_1/1772730132_6e02c61f.jpg', NULL, NULL, '2026-03-05 12:02:12', 'otros_ingresos_1'),
(24, 8, 'solicitudes/8/otros_ingresos_2/1772730132_56ee84a4.jpg', NULL, NULL, '2026-03-05 12:02:12', 'otros_ingresos_2'),
(25, 8, 'solicitudes/8/otros_ingresos_3/1772730132_1f4bbbbe.jpg', NULL, NULL, '2026-03-05 12:02:12', 'otros_ingresos_3'),
(26, 8, 'solicitudes/8/otros_ingresos_3/1772730132_4d4ab836.jpg', NULL, NULL, '2026-03-05 12:02:12', 'otros_ingresos_3'),
(27, 9, 'solicitudes/9/cedula_front/1772730172_41cfdaa9.jpg', NULL, NULL, '2026-03-05 12:02:52', 'cedula_front'),
(28, 9, 'solicitudes/9/cedula_back/1772730172_73c08655.jpg', NULL, NULL, '2026-03-05 12:02:52', 'cedula_back'),
(29, 9, 'solicitudes/9/otros_ingresos_1/1772730172_582166a2.jpg', NULL, NULL, '2026-03-05 12:02:52', 'otros_ingresos_1'),
(30, 9, 'solicitudes/9/otros_ingresos_2/1772730172_788d5b44.jpg', NULL, NULL, '2026-03-05 12:02:52', 'otros_ingresos_2'),
(31, 9, 'solicitudes/9/otros_ingresos_3/1772730172_f4bca1f1.jpg', NULL, NULL, '2026-03-05 12:02:52', 'otros_ingresos_3'),
(32, 9, 'solicitudes/9/otros_ingresos_3/1772730172_6c9ac850.jpg', NULL, NULL, '2026-03-05 12:02:52', 'otros_ingresos_3'),
(33, 9, 'solicitudes/9/fachada/1772732158_ab5f356b579b.jpg', 'image/jpeg', 2968824, '2026-03-05 17:35:58', NULL),
(34, 9, 'solicitudes/9/fachada/1772732419_03418eb3c4f8.jpg', 'image/jpeg', 164007, '2026-03-05 17:40:19', NULL),
(36, 11, 'solicitudes/11/cedula_front/1772829303_a1c560b1.jpg', NULL, NULL, '2026-03-06 15:35:03', 'cedula_front'),
(37, 11, 'solicitudes/11/cedula_back/1772829303_ac8ebfc6.jpg', NULL, NULL, '2026-03-06 15:35:03', 'cedula_back'),
(38, 11, 'solicitudes/11/fachada/1772829771_f831ea87eadf.jpg', 'image/jpeg', 3563650, '2026-03-06 20:42:51', NULL),
(39, 11, 'solicitudes/11/docs_generales/1772829886_d6cf5ea5d0c8.jpg', 'image/jpeg', 3635589, '2026-03-06 20:44:46', NULL),
(40, 11, 'solicitudes/11/docs_generales/1772829889_c2e1c3315d0c.jpg', 'image/jpeg', 3729368, '2026-03-06 20:44:49', NULL),
(41, 11, 'solicitudes/11/docs_generales/1772829890_d7dbcc6ba904.jpg', 'image/jpeg', 3786773, '2026-03-06 20:44:50', NULL),
(42, 11, 'solicitudes/11/docs_generales/1772829890_e7f46740d029.jpg', 'image/jpeg', 3841070, '2026-03-06 20:44:50', NULL),
(43, 11, 'solicitudes/11/fotos_adicionales/1772830141_1a1a62a67712.jpg', 'image/jpeg', 919616, '2026-03-06 20:49:01', NULL),
(44, 11, 'solicitudes/11/fotos_adicionales/1772830141_9603658a104c.jpg', 'image/jpeg', 966029, '2026-03-06 20:49:01', NULL),
(45, 11, 'solicitudes/11/fotos_adicionales/1772830160_320890eec910.jpg', 'image/jpeg', 919616, '2026-03-06 20:49:20', NULL),
(46, 11, 'solicitudes/11/fotos_adicionales/1772830160_d1318f757740.jpg', 'image/jpeg', 966029, '2026-03-06 20:49:20', NULL),
(47, 13, 'solicitudes/13/cedula_front/1772843735_3ddbaa9d.jpg', NULL, NULL, '2026-03-06 19:35:35', 'cedula_front'),
(48, 13, 'solicitudes/13/cedula_back/1772843735_c9062ee7.jpg', NULL, NULL, '2026-03-06 19:35:35', 'cedula_back'),
(49, 13, 'solicitudes/13/fachada/1772843735_4580a9a0.jpg', NULL, NULL, '2026-03-06 19:35:35', 'fachada'),
(50, 13, 'solicitudes/13/otros_ingresos_1/1772843735_0a144c73.jpg', NULL, NULL, '2026-03-06 19:35:35', 'otros_ingresos_1'),
(51, 13, 'solicitudes/13/otros_ingresos_2/1772843735_9fab7a6a.jpg', NULL, NULL, '2026-03-06 19:35:35', 'otros_ingresos_2'),
(52, 13, 'solicitudes/13/otros_ingresos_2/1772843735_bd269fa8.jpg', NULL, NULL, '2026-03-06 19:35:35', 'otros_ingresos_2'),
(53, 2, 'solicitudes/2/cedula_back/1772910716_1784597ce624.jpeg', 'image/jpeg', 117265, '2026-03-07 19:11:56', NULL),
(54, 2, 'solicitudes/2/fachada/1772910727_965cf7ddd2a7.jpeg', 'image/jpeg', 184223, '2026-03-07 19:12:07', NULL),
(55, 2, 'solicitudes/2/docs_generales/1772914376_a339f6c3490f.jpeg', 'image/jpeg', 136194, '2026-03-07 20:12:56', NULL),
(56, 14, 'solicitudes/14/cedula_front/1772917726_8967b422.jpg', NULL, NULL, '2026-03-07 16:08:46', 'cedula_front'),
(57, 14, 'solicitudes/14/cedula_back/1772917726_365fb623.jpg', NULL, NULL, '2026-03-07 16:08:46', 'cedula_back'),
(58, 14, 'solicitudes/14/otros_ingresos_1/1772917726_54c0368b.jpg', NULL, NULL, '2026-03-07 16:08:46', 'otros_ingresos_1'),
(59, 14, 'solicitudes/14/otros_ingresos_1/1772917726_2d4dd92f.jpg', NULL, NULL, '2026-03-07 16:08:46', 'otros_ingresos_1'),
(60, 14, 'solicitudes/14/otros_ingresos_2/1772917726_0397e3aa.jpg', NULL, NULL, '2026-03-07 16:08:46', 'otros_ingresos_2'),
(61, 14, 'solicitudes/14/otros_ingresos_2/1772917726_c48cbff0.jpg', NULL, NULL, '2026-03-07 16:08:46', 'otros_ingresos_2'),
(62, 12, 'solicitudes/12/fotos_adicionales/1773064826_6806eeb0749c.jpg', 'image/jpeg', 2165275, '2026-03-09 14:00:26', NULL),
(63, 12, 'solicitudes/12/cedula_front/1773064839_46580c719ef8.jpg', 'image/jpeg', 3643512, '2026-03-09 14:00:39', NULL),
(64, 12, 'solicitudes/12/cedula_back/1773066774_84fcd82a6f9c.jpg', 'image/jpeg', 2872909, '2026-03-09 14:32:54', NULL),
(65, 12, 'solicitudes/12/docs_legales/1773066786_0b6f21d0f837.jpg', 'image/jpeg', 171499, '2026-03-09 14:33:06', NULL),
(66, 12, 'solicitudes/12/docs_generales/1773066874_e5d7349e71d7.jpg', 'image/jpeg', 1052287, '2026-03-09 14:34:34', NULL),
(67, 12, 'solicitudes/12/docs_generales/1773066874_f0ba58d0d620.jpg', 'image/jpeg', 993437, '2026-03-09 14:34:34', NULL),
(68, 12, 'solicitudes/12/docs_generales/1773066874_06c37b4d86e6.jpg', 'image/jpeg', 1017397, '2026-03-09 14:34:34', NULL),
(69, 12, 'solicitudes/12/docs_generales/1773066874_38c33b3aba7b.jpg', 'image/jpeg', 1071784, '2026-03-09 14:34:34', NULL),
(70, 12, 'solicitudes/12/docs_generales/1773066875_fbfb376fea93.jpg', 'image/jpeg', 1472176, '2026-03-09 14:34:35', NULL),
(71, 12, 'solicitudes/12/docs_generales/1773066876_ae06070dd8db.jpg', 'image/jpeg', 2017302, '2026-03-09 14:34:36', NULL),
(72, 16, 'solicitudes/16/cedula_back/1773085295_0fdb068a.jpg', NULL, NULL, '2026-03-09 14:41:35', 'cedula_back'),
(73, 16, 'solicitudes/16/fachada/1773085295_fad93b28.jpg', NULL, NULL, '2026-03-09 14:41:35', 'fachada'),
(74, 16, 'solicitudes/16/otros_ingresos_1/1773085295_c5690aa3.jpg', NULL, NULL, '2026-03-09 14:41:35', 'otros_ingresos_1'),
(75, 16, 'solicitudes/16/otros_ingresos_1/1773085295_f5bcb928.jpg', NULL, NULL, '2026-03-09 14:41:35', 'otros_ingresos_1'),
(76, 16, 'solicitudes/16/otros_ingresos_1/1773085295_49e04cc4.jpg', NULL, NULL, '2026-03-09 14:41:35', 'otros_ingresos_1'),
(77, 18, 'solicitudes/18/cedula_front/1773150800_112c37df.jpg', NULL, NULL, '2026-03-10 08:53:20', 'cedula_front'),
(78, 18, 'solicitudes/18/fachada/1773150800_b36bf303.jpg', NULL, NULL, '2026-03-10 08:53:20', 'fachada'),
(79, 18, 'solicitudes/18/otros_ingresos_1/1773150800_1ddfe90d.jpg', NULL, NULL, '2026-03-10 08:53:20', 'otros_ingresos_1'),
(80, 18, 'solicitudes/18/fotos_adicionales/1773151018_e379bc1aa9c4.jpg', 'image/jpeg', 1373497, '2026-03-10 13:56:58', NULL),
(81, 18, 'solicitudes/18/fotos_adicionales/1773151018_21ce236f35ad.jpg', 'image/jpeg', 1376193, '2026-03-10 13:56:58', NULL),
(82, 18, 'solicitudes/18/fotos_adicionales/1773151018_6d19e1d05067.jpg', 'image/jpeg', 1470835, '2026-03-10 13:56:58', NULL),
(83, 18, 'solicitudes/18/fotos_adicionales/1773151018_b602fb8b65d2.jpg', 'image/jpeg', 1556561, '2026-03-10 13:56:58', NULL),
(84, 18, 'solicitudes/18/fotos_adicionales/1773151018_e8539cd96900.jpg', 'image/jpeg', 1798399, '2026-03-10 13:56:58', NULL),
(85, 18, 'solicitudes/18/fotos_adicionales/1773151019_ccbc17d28076.jpg', 'image/jpeg', 3326413, '2026-03-10 13:56:59', NULL),
(86, 18, 'solicitudes/18/docs_legales/1773151107_b66959b0ab27.jpg', 'image/jpeg', 78321, '2026-03-10 13:58:27', NULL),
(87, 18, 'solicitudes/18/docs_generales/1773151386_f1b682df1abf.jpg', 'image/jpeg', 4320867, '2026-03-10 14:03:06', NULL),
(88, 12, 'solicitudes/12/fachada/1773154053_42bc61b5ce64.jpg', 'image/jpeg', 1071784, '2026-03-10 14:47:33', NULL),
(89, 12, 'solicitudes/12/docs_generales/1773154152_535fb96c9bef.jpg', 'image/jpeg', 1017397, '2026-03-10 14:49:12', NULL),
(90, 12, 'solicitudes/12/docs_generales/1773154152_619d8e5b1f5b.jpg', 'image/jpeg', 1052287, '2026-03-10 14:49:12', NULL),
(91, 12, 'solicitudes/12/docs_generales/1773154152_2ab8dd180897.jpg', 'image/jpeg', 1472176, '2026-03-10 14:49:12', NULL),
(92, 12, 'solicitudes/12/docs_generales/1773154152_713f08e9c5d9.jpg', 'image/jpeg', 2017302, '2026-03-10 14:49:12', NULL),
(93, 17, 'solicitudes/17/fachada/1773156237_973de46c626f.jpg', 'image/jpeg', 122815, '2026-03-10 15:23:57', NULL),
(94, 17, 'solicitudes/17/fachada/1773156239_6caa5b479a65.jpg', 'image/jpeg', 3156418, '2026-03-10 15:23:59', NULL),
(95, 17, 'solicitudes/17/cedula_front/1773156250_86a6e1c8a025.jpg', 'image/jpeg', 3746274, '2026-03-10 15:24:10', NULL),
(96, 17, 'solicitudes/17/cedula_back/1773156257_52b31a87d17d.jpg', 'image/jpeg', 2973995, '2026-03-10 15:24:17', NULL),
(97, 17, 'solicitudes/17/docs_generales/1773156267_580b9a6fa4c4.jpg', 'image/jpeg', 1803169, '2026-03-10 15:24:27', NULL),
(98, 17, 'solicitudes/17/docs_generales/1773156282_2873cd48e37c.jpg', 'image/jpeg', 1491381, '2026-03-10 15:24:42', NULL),
(99, 17, 'solicitudes/17/docs_generales/1773156288_5fb9b39ee214.jpg', 'image/jpeg', 1175614, '2026-03-10 15:24:48', NULL),
(100, 17, 'solicitudes/17/docs_generales/1773156294_9490cc019527.jpg', 'image/jpeg', 1139983, '2026-03-10 15:24:54', NULL),
(101, 17, 'solicitudes/17/docs_generales/1773156307_33ac46d87c72.jpg', 'image/jpeg', 1367398, '2026-03-10 15:25:07', NULL),
(102, 17, 'solicitudes/17/docs_generales/1773156315_36b0436fec21.jpg', 'image/jpeg', 1593285, '2026-03-10 15:25:15', NULL),
(103, 20, 'solicitudes/20/cedula_front/1773187589_cc5fb7f6.jpg', NULL, NULL, '2026-03-10 19:06:29', 'cedula_front'),
(104, 20, 'solicitudes/20/cedula_back/1773187589_94f59ab9.jpg', NULL, NULL, '2026-03-10 19:06:29', 'cedula_back'),
(105, 20, 'solicitudes/20/fachada/1773187589_b3c182ba.jpg', NULL, NULL, '2026-03-10 19:06:29', 'fachada'),
(106, 20, 'solicitudes/20/fachada/1773187589_127bfce5.jpg', NULL, NULL, '2026-03-10 19:06:29', 'fachada'),
(107, 3, 'solicitudes/3/docs_generales/1773195337_0474d9ec758d.jpg', 'image/jpeg', 115862, '2026-03-11 02:15:37', NULL),
(108, 3, 'solicitudes/3/fachada/1773242389_efdb74894981.jpg', 'image/jpeg', 3377954, '2026-03-11 15:19:49', NULL),
(109, 3, 'solicitudes/3/docs_generales/1773242409_38cef959670b.jpg', 'image/jpeg', 162300, '2026-03-11 15:20:09', NULL),
(110, 3, 'solicitudes/3/fotos_adicionales/1773242433_5a4aed37cf42.jpg', 'image/jpeg', 115862, '2026-03-11 15:20:33', NULL),
(111, 21, 'solicitudes/21/cedula_front/1773411879_3a770a65.jpg', NULL, NULL, '2026-03-13 09:24:39', 'cedula_front'),
(112, 21, 'solicitudes/21/cedula_back/1773411879_ef48255e.jpg', NULL, NULL, '2026-03-13 09:24:39', 'cedula_back'),
(113, 21, 'solicitudes/21/fachada/1773411879_ce28380f.jpg', NULL, NULL, '2026-03-13 09:24:39', 'fachada'),
(114, 21, 'solicitudes/21/otros_ingresos_1/1773411879_49396d92.jpg', NULL, NULL, '2026-03-13 09:24:39', 'otros_ingresos_1'),
(115, 21, 'solicitudes/21/otros_ingresos_2/1773411879_07f280f5.jpg', NULL, NULL, '2026-03-13 09:24:39', 'otros_ingresos_2'),
(116, 21, 'solicitudes/21/otros_ingresos_3/1773411879_11e4e76a.jpg', NULL, NULL, '2026-03-13 09:24:39', 'otros_ingresos_3'),
(117, 21, 'solicitudes/21/otros_ingresos_3/1773411879_a0dc575e.jpg', NULL, NULL, '2026-03-13 09:24:39', 'otros_ingresos_3'),
(118, 23, 'solicitudes/23/cedula_front/1773451435_98ee9cd2.jpg', NULL, NULL, '2026-03-13 20:23:55', 'cedula_front'),
(119, 23, 'solicitudes/23/cedula_back/1773451435_f645a7d0.jpg', NULL, NULL, '2026-03-13 20:23:55', 'cedula_back'),
(120, 23, 'solicitudes/23/fachada/1773451435_cfdee443.jpg', NULL, NULL, '2026-03-13 20:23:55', 'fachada'),
(121, 23, 'solicitudes/23/otros_ingresos_1/1773451435_14f0c024.jpg', NULL, NULL, '2026-03-13 20:23:55', 'otros_ingresos_1'),
(122, 23, 'solicitudes/23/otros_ingresos_1/1773451435_e15db5af.jpg', NULL, NULL, '2026-03-13 20:23:55', 'otros_ingresos_1'),
(123, 24, 'solicitudes/24/cedula_front/1773453866_403b2dc2.jpg', NULL, NULL, '2026-03-13 21:04:26', 'cedula_front'),
(124, 24, 'solicitudes/24/cedula_back/1773453866_2c4cd4ad.jpg', NULL, NULL, '2026-03-13 21:04:26', 'cedula_back'),
(125, 24, 'solicitudes/24/fachada/1773453866_9d27014f.jpg', NULL, NULL, '2026-03-13 21:04:26', 'fachada');

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
(1, 1, 1, 'KIARA Jessetht Osorno Selva ', '607280790007G', 'Bo.costa Rica semáforo colonial 4 cd norte 2 cd esté ', '58445150', 'Personal', '32', 0, 'Excelente', '', '2026-03-05 17:16:50', 'Vecino'),
(2, 1, 2, 'Miguel Aviles ', '', '', '89402905', 'Comercial', '', NULL, '', '', '2026-03-05 17:16:50', ''),
(3, 9, 1, 'Noel Orozco ', '0', '0', '57135209', 'Comercial', '', 0, '', '', '2026-03-05 17:21:55', ''),
(4, 9, 2, 'Rene Iván castillo quintero ', '0012411590056g', 'Vl.don Bosco licorería don Bosco 8c.25 VRS.e', '89796426', 'Personal', '5 ', 0, 'Buena', '', '2026-03-05 17:21:55', 'Amigo'),
(5, 13, 1, 'Eliézer Francisco Matamoros Rostran', '0010901900053M', 'Laureles sur distribuidora grande 2 1/2 al sur ', '57060698', 'Personal', '10', 1, 'Excelente', '', '2026-03-07 00:46:51', ''),
(6, 13, 2, 'Alfonso fitoria ', '', '', '88104968', 'Comercial', '8', 1, 'Buena', '', '2026-03-07 00:46:51', ''),
(7, 2, 1, 'Meyboll Catalina Caballero Peralta', '085-200385-000E', 'Bo. Pantasma Hospital Manolo Morales 2C. Al Sur 3C.', '7536-1763', 'Personal', '30 años', 0, 'Buena', 'Es una mujer tranquila trabajadora con su pulpería, nunca ha notado un mal comportamiento en ella con el vecindario. Es una persona dedicada a su familia y su pulpería.', '2026-03-07 20:03:34', 'Vecino'),
(8, 2, 2, 'Carlos- Proveedor Unielver', '', '', '7886-5859', 'Comercial', '02 años', NULL, 'Buena', 'Tiene dos años trabajando con Ana Bellis ella le factura entre C$1,000 a C$2,500 de contado sus facturas van en dependencia de lo que ella necesita.', '2026-03-07 20:03:34', ''),
(9, 14, 1, 'CHARLYS ESAU CRUZ', '0011712001033F', 'Lomas de Guadalupe Bomba ruta 168 5C E 1/2 C al norte ', '81932481', 'Personal', '8', 1, 'Excelente', '', '2026-03-07 21:34:29', 'Amigo'),
(10, 14, 2, 'Marwel', '', '', '78292507', 'Comercial', '6', NULL, 'Excelente', 'Esta personal es que le provee todo los aceites entre otros productos ', '2026-03-07 21:34:29', ''),
(11, 10, 1, 'Karla Vanessa Cordero González ', '0012603720057P', '', '58204050', 'Personal', '', 0, '', '', '2026-03-10 14:35:54', 'Amigo'),
(12, 10, 2, 'Édgar Lozano ', '', '', '22281011', 'Comercial', '', NULL, '', '', '2026-03-10 14:35:54', ''),
(13, 18, 1, 'Cristhian ', '', '', '76524808', 'Comercial', '', NULL, '', '', '2026-03-10 14:35:56', ''),
(14, 18, 2, 'Liliam manuela Pérez López ', '0011008790035a', 'Bo.francisco salazar semáforos entradas las colinas 5cs.', '84570506', 'Personal', '5 ', 0, 'Buena', '', '2026-03-10 14:35:56', 'Amigo'),
(15, 17, 1, 'Zulema Del Socorro Luna', '0011401720065J', '', '77126584', 'Personal', '', 0, '', '', '2026-03-10 15:08:49', 'Amigo'),
(16, 17, 2, 'David González ', '', '', '89416501', 'Comercial', '', 0, 'Excelente', '', '2026-03-10 15:08:49', ''),
(17, 15, 1, 'Kamila Sarai Flores González ', '0012712061057D', '', '81798516', 'Personal', '', 0, 'Excelente', '', '2026-03-10 15:54:23', 'Vecino'),
(18, 15, 2, 'Cristian cano ', '', '', '82680523', 'Comercial', '', 0, 'Excelente', '', '2026-03-10 15:54:23', ''),
(19, 20, 1, 'Roman Yamil Avendaño Martínez ', '0013011870030P', 'Residencial mayales casa 342', '78674424', 'Personal', '10', 1, 'Excelente', '', '2026-03-11 00:12:40', 'Amigo'),
(20, 20, 2, 'Cristian parrales', '', '', '78867371', 'Comercial', '', NULL, '', '', '2026-03-11 00:12:40', ''),
(21, 3, 1, 'José Miguel Soza Soza ', '4411211790006U', 'Rotonda bello horizonte 1c al sur 1/2 arriba ', '76600510', 'Personal', '', NULL, '', '', '2026-03-11 15:26:58', ''),
(22, 3, 2, '', '', '', '', '', '', NULL, '', '', '2026-03-11 15:26:58', ''),
(23, 21, 1, 'Luis Rodríguez ', '', '', '87963693', 'Comercial', '', NULL, '', '', '2026-03-13 14:30:12', ''),
(24, 21, 2, 'Juan Antonio Rivas ', '0012406730057Y', 'Cementerio viejo San Isidro de bola 3c al sur', '82514095', 'Personal', '', NULL, '', '', '2026-03-13 14:30:12', ''),
(25, 23, 1, 'Yahoska fuentes ', '', '', '86790655', 'Comercial', '', NULL, '', '', '2026-03-14 01:29:43', ''),
(26, 23, 2, 'Vanessa del Socorro Saravia Maradiaga ', '0011310800034N', 'Anexo villa fraternidad colegio San Jacinto 1 1/2 al sur 1c al E', '81986644', 'Personal', '', NULL, '', '', '2026-03-14 01:29:43', ''),
(27, 24, 1, 'Martin Rafael Navarrete Narváez ', '0012410780065G', 'Bo, San Judas del centro de salud 1c al sur 2 1/2 al E', '77274270', 'Personal', '', NULL, '', '', '2026-03-14 02:09:57', ''),
(28, 24, 2, 'Verónica del Carmen Suárez ', '0011806890014C', 'Bo. Francizco meza roja colegio Cristo rey 2C al O 2 C al N', '57901911', 'Personal', '', NULL, '', '', '2026-03-14 02:09:57', ''),
(29, 25, 1, 'Estela Ruth Urbina  Valle', '4491008710006D', '', '89996651', 'Personal', '', 0, 'Excelente', '', '2026-03-17 15:22:20', 'Amigo'),
(30, 25, 2, 'Noel ', '', '', '57135209', 'Comercial', 'Dismab', NULL, '', '', '2026-03-17 15:22:20', ''),
(31, 27, 1, 'Karen De los angeles Mendieta Sánchez ', '0011210800042V', 'Villa concha farmacia San Benito 10 cd sur 1/2 oeste ', '86204803', 'Personal', '', NULL, '', '', '2026-03-17 15:44:09', 'Amigo'),
(32, 27, 2, 'Arnold dismab ', '', '', '89402893', '', '', NULL, '', '', '2026-03-17 15:44:09', ''),
(33, 29, 1, 'Steven ', '', '', '81228925', 'Comercial', '', NULL, '', '', '2026-03-27 14:33:51', ''),
(34, 29, 2, 'José Ismael Quezada Lovo', '0012012001010M', 'Zonas 8 frente colegio Bella cruz ', '77200492', 'Personal', '', NULL, '', '', '2026-03-27 14:33:51', ''),
(35, 30, 1, 'Blanca azucena Pérez ', '0012603011065V', 'Frente a Fernández será camino sabana grande ', '51617313', 'Personal', '', NULL, '', '', '2026-03-28 05:08:53', ''),
(36, 30, 2, 'Eliath distribuidora Ebenezer ', '', '', '87284253', 'Comercial', '', NULL, '', '', '2026-03-28 05:08:53', ''),
(37, 31, 1, 'Reyna Isabel Chávez Flores ', '0022507720000T', '', '81899758', 'Personal', '', NULL, '', '', '2026-03-28 14:37:28', 'Amigo'),
(38, 31, 2, 'Referencia comercial ', '', '', '550016619', 'Comercial', '', NULL, '', '', '2026-03-28 14:37:28', ''),
(39, 32, 1, 'Carolina Del Carmen Silva ', '0010105790042T', '', '85522326', 'Personal', '', 1, 'Excelente', '', '2026-03-28 14:51:23', 'Amigo'),
(40, 32, 2, 'Venta de accesorios ENEIKI FIGUEROA ', '', '', '85570784', '', '', NULL, '', '', '2026-03-28 14:51:23', '');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `tb_solicitud_referencias_fotos`
--

INSERT INTO `tb_solicitud_referencias_fotos` (`idfoto`, `idsolicitud`, `idreferencia`, `referencia_num`, `tipo`, `filename`, `created_at`) VALUES
(1, 1, 1, 1, 'front', 'uploads/solicitudes/solicitud_1/referencias/referencia_1/1772731010_82b3b0ca_front.jpg', '2026-03-05 17:16:50'),
(2, 1, 1, 1, 'back', 'uploads/solicitudes/solicitud_1/referencias/referencia_1/1772731010_5f8079f2_back.jpg', '2026-03-05 17:16:50'),
(3, 9, 4, 2, 'front', 'uploads/solicitudes/solicitud_9/referencias/referencia_2/1772731315_45ed3331_front.jpg', '2026-03-05 17:21:55'),
(4, 9, 4, 2, 'back', 'uploads/solicitudes/solicitud_9/referencias/referencia_2/1772731315_7563e379_back.jpg', '2026-03-05 17:21:55'),
(5, 13, 5, 1, 'front', 'uploads/solicitudes/solicitud_13/referencias/referencia_1/1772844411_df972801_front.jpg', '2026-03-07 00:46:51'),
(6, 13, 5, 1, 'back', 'uploads/solicitudes/solicitud_13/referencias/referencia_1/1772844411_a2cf96ca_back.jpg', '2026-03-07 00:46:51'),
(7, 2, 7, 1, 'front', 'uploads/solicitudes/solicitud_2/referencias/referencia_1/1772913814_c7a71734_front.jpeg', '2026-03-07 20:03:34'),
(8, 2, 7, 1, 'back', 'uploads/solicitudes/solicitud_2/referencias/referencia_1/1772913814_228957b7_back.jpeg', '2026-03-07 20:03:34'),
(9, 14, 9, 1, 'front', 'uploads/solicitudes/solicitud_14/referencias/referencia_1/1772919269_0cd30a73_front.jpg', '2026-03-07 21:34:29'),
(10, 14, 9, 1, 'back', 'uploads/solicitudes/solicitud_14/referencias/referencia_1/1772919269_51d776a2_back.jpg', '2026-03-07 21:34:29'),
(11, 10, 11, 1, 'front', 'uploads/solicitudes/solicitud_10/referencias/referencia_1/1773153354_4a79a5f8_front.jpg', '2026-03-10 14:35:54'),
(12, 10, 11, 1, 'back', 'uploads/solicitudes/solicitud_10/referencias/referencia_1/1773153354_4e977441_back.jpg', '2026-03-10 14:35:54'),
(13, 18, 14, 2, 'front', 'uploads/solicitudes/solicitud_18/referencias/referencia_2/1773153356_d0fe1c4a_front.jpg', '2026-03-10 14:35:56'),
(14, 17, 15, 1, 'front', 'uploads/solicitudes/solicitud_17/referencias/referencia_1/1773155329_1ebc228f_front.jpg', '2026-03-10 15:08:49'),
(15, 17, 15, 1, 'back', 'uploads/solicitudes/solicitud_17/referencias/referencia_1/1773155329_7bb0ee70_back.jpg', '2026-03-10 15:08:49'),
(16, 15, 17, 1, 'front', 'uploads/solicitudes/solicitud_15/referencias/referencia_1/1773158063_36589163_front.jpg', '2026-03-10 15:54:23'),
(17, 15, 17, 1, 'back', 'uploads/solicitudes/solicitud_15/referencias/referencia_1/1773158063_9388d357_back.jpg', '2026-03-10 15:54:23'),
(18, 3, 21, 1, 'front', 'uploads/solicitudes/solicitud_3/referencias/referencia_1/1773242818_1e13e134_front.jpg', '2026-03-11 15:26:58'),
(19, 3, 21, 1, 'back', 'uploads/solicitudes/solicitud_3/referencias/referencia_1/1773242818_94a6ba0c_back.jpg', '2026-03-11 15:26:58'),
(20, 3, 21, 1, 'front', 'uploads/solicitudes/solicitud_3/referencias/referencia_1/1773242818_f80ba176_front.jpg', '2026-03-11 15:26:58'),
(21, 3, 21, 1, 'back', 'uploads/solicitudes/solicitud_3/referencias/referencia_1/1773242818_f74d1b34_back.jpg', '2026-03-11 15:26:58'),
(22, 21, 24, 2, 'front', 'uploads/solicitudes/solicitud_21/referencias/referencia_2/1773412212_bac85c55_front.jpg', '2026-03-13 14:30:12'),
(23, 21, 24, 2, 'back', 'uploads/solicitudes/solicitud_21/referencias/referencia_2/1773412212_1fc12f50_back.jpg', '2026-03-13 14:30:12'),
(24, 23, 26, 2, 'front', 'uploads/solicitudes/solicitud_23/referencias/referencia_2/1773451783_2039672d_front.jpg', '2026-03-14 01:29:43'),
(25, 23, 26, 2, 'back', 'uploads/solicitudes/solicitud_23/referencias/referencia_2/1773451783_9379013b_back.jpg', '2026-03-14 01:29:43'),
(26, 24, 27, 1, 'front', 'uploads/solicitudes/solicitud_24/referencias/referencia_1/1773454197_89ac5e6b_front.jpg', '2026-03-14 02:09:57'),
(27, 24, 27, 1, 'back', 'uploads/solicitudes/solicitud_24/referencias/referencia_1/1773454197_314c28da_back.jpg', '2026-03-14 02:09:57'),
(28, 24, 28, 2, 'front', 'uploads/solicitudes/solicitud_24/referencias/referencia_2/1773454197_95e914fe_front.jpg', '2026-03-14 02:09:57'),
(29, 24, 28, 2, 'back', 'uploads/solicitudes/solicitud_24/referencias/referencia_2/1773454197_a692c554_back.jpg', '2026-03-14 02:09:57');

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
(1, 3, 'Cliente pide financiamiento porque va a estudiar, quiere usar el dinero para matrícula, libros y los gastos de la universidad ', 'Asalariado ', 17000.00, '', 'Alemán Aidan Joshua Jiménez', '2026-03-03', '', '2026-03-03', '2026-03-05 16:21:38', 800.00, 12, 'Consumo', 'Cliente pide financiamiento porque va a estudiar, quiere usar el dinero para matrícula, libros y los gastos de la universidad '),
(2, 1, 'Arroz, café, pan, leche, gaseosas, medicamento, pollo, embutidos, detergente frijoles, aceite ', 'Pulpería ', 4800.00, '', 'FLORES DAYANA TÁMARA BLANDON', NULL, '', NULL, '2026-03-05 16:48:27', 1600.00, 12, 'Inversión', 'Cliente solicita crédito para inversión en comprá de granos básico y compra para fritanga'),
(3, 12, 'Arroz, azúcar,aceite,medicamento, pan gaseosa, detergente ', 'Variedades ', 4200.00, '', 'González Rebeca Del Socorro Castillo', NULL, '', '2026-03-06', '2026-03-06 20:50:44', 1200.00, 12, 'Inversión', 'Cliente solicita el crédito para mejorar el negocio '),
(4, 13, 'Cliente ofrece servicio de freno en sobremedida ', 'Taller de freno automotriz ', 90000.00, '', 'ROSTRAN BISMARK FRANCISCO RIVAS', '2026-03-06', '', NULL, '2026-03-07 00:42:24', 1500.00, 12, 'Inversión', 'Cliente pide financiación para meter más productos de accesorios de frenado '),
(5, 2, 'Gaseosas, café, pan, arroz, azúcar, meneitos,caramelos, embutidos,etc', '', 83200.00, '', 'Zamora Ana Bellis Gutierrez', '2026-02-26', 'Carlos Pineda', '2026-02-26', '2026-03-07 19:49:43', 1000.00, 10, 'Capital de trabajo', 'Clienta indica usar su financiamiento para reinvertir en el negocio comprar más granos básicos'),
(6, 16, 'Cliente vende cepillos de lavar ropa al detalle y por mayor ', 'Cepillos de lavar ropa ', 30000.00, '', 'Hernández Gema Cristina Alvarado', '2026-03-09', 'Clienta se le aprecia elaborando los cepillos, muy atareada, aparenta ser una persona muy seria y eficaz en si negocio ', '2026-03-09', '2026-03-09 19:49:14', 300.00, 12, NULL, 'La clienta indica que quiero el dinero para invertir en más productos para hacer más cepillos de lavar ropa ya que cada vez tiene más pedidos '),
(7, 10, 'Arroz, azúcar, aceite, pan, huevos, medicamentos, gaseosa, galletas ', 'Pulpería ', 3800.00, '', 'Rodríguez Carmen Del Socorro Herrera', '2026-03-10', '', NULL, '2026-03-10 14:01:36', 800.00, 9, 'Inversión', 'Cliente solicita el crédito para compra de un estante y inversión en compras de granos básicos '),
(8, 17, 'Arroz, azúcar, aceite, medicamento, galletas, café, pan, huevos, gaseosa ', 'Pulpería ', 13400.00, '', 'MARTINEZ JADITH GABRIELA VALLEJOS', '2026-03-10', '', NULL, '2026-03-10 15:01:55', 500.00, 6, 'Inversión', 'Cliente solicita el crédito para aumento de granos básicos '),
(9, 15, 'Arroz, azúcar, aceite medicamento, galletas, pan, gaseosa ', 'Pulpería ', 4200.00, '', 'OBANDO BELLY ALEXANDRA MEJIA', NULL, '', NULL, '2026-03-10 15:40:29', 1200.00, 10, 'Inversión', 'Cliente solicita el crédito para compra en granos básicos '),
(10, 20, 'Clientes tiene todo tipo de productos para el cabello, para cara, lavado y planchado', 'Salón de belleza ', 100000.00, '', 'PALACIOS EDEL JOSE VEGA', '2026-03-10', '', '2026-03-10', '2026-03-11 00:14:30', 10000.00, 36, 'Inversión', 'Clientes pide financiamiento para invertir en su negocio hacerlo más grande y poder expanderse en productos, tratar de comprar otro local, y cancelar una tarjeta '),
(11, 21, 'Azúcar, Maruchan, fósforos, meneitos, gaseosas, arroz, frijoles, aceites, todo de granos básicos.', 'Pulpería ', 20000.00, '', 'CABRERA CAUDIZ YAHOSKA NARVÁEZ', NULL, '', NULL, '2026-03-13 14:26:42', 900.00, 12, 'Inversión', 'Cliente pide financiamiento para mejora de su negocio meter más productos a su inventario para hacer un poco más grande si pulpería '),
(12, 14, 'Clientes tiene negocio de autolavado donde ofrece servicios de 150 córdobas hasta los 350 dólares ', 'Auto lavado ', 450000.00, '', 'CUADRA KARLA VANESSA PÉREZ', NULL, '', NULL, '2026-03-14 01:10:38', 10000.00, 36, 'Inversión', 'Quiere invertir en la fachada de su segundo negocio el cual desea apertura con el préstamo, \nlo cual será un taller de pintura para vehículos.'),
(13, 23, 'Cliente ofrece todo tipo de productos para el cabello y todo tipo de cosméticos para el trabajo de estilismo', 'Salón de belleza', 38000.00, '', 'SOZA ILEANA VICTORIA GUILLÉN', '2026-03-13', '', NULL, '2026-03-14 01:26:35', 1000.00, 12, 'Inversión', 'Cliente pide financiamiento para inversión en su negocio meter más productos de los que ella vende e invertir en nuevos '),
(14, 24, '', 'Asalariado ', 14000.00, '', 'OVIEDO FELIX DAVID QUIROZ', '2026-03-13', '', NULL, '2026-03-14 02:05:26', 600.00, 12, 'Inversión', 'Cliente pide financiamiento para mejora de su vivienda y empezar un pequeño emprendimiento '),
(15, 4, 'Arroz, aceite,panper, café, azúcar, gaseosa, medicamento, pollo, carne de Res,', 'Distribuidora ', 811600.00, '', 'CUADRA KARLA VANESSA PÉREZ', NULL, '', NULL, '2026-03-21 14:45:01', 5000.00, 12, 'Inversión', NULL),
(16, 28, 'Arroz, azúcar, detergente, medicamento, gaseosa, galletas, aceite,pan, huevos ', 'Pulpería ', 165080.00, '', 'CUADRA KARLA VANESSA PÉREZ', '2026-03-21', '', NULL, '2026-03-21 14:46:46', 2300.00, 12, 'Inversión', 'Cliente solicita préstamo para compra de granos básicos y ropa interior '),
(17, 30, 'Cliente ofrece todo tipo de comida, desayuno almuerzo y cenas', 'Comedor ', 750000.00, '', 'CARRIÓN NANCY VERÓNICA ROMERO', '2026-03-27', '', NULL, '2026-03-28 05:06:57', 2400.00, 12, 'Inversión', 'Cliente pide financiamiento para inversión y mejora de su negocio para hacer unas pequeñas remodelaciones'),
(18, 31, 'Pollo, arroz, azúcar, gaseosa ', 'Comedor ', 3800.00, '', 'HERNÁNDEZ FRANCIS ARICELA ARIAS', NULL, '', NULL, '2026-03-28 14:27:00', 500.00, 6, 'Inversión', 'Cliente solicita préstamo para compra de granos básicos '),
(19, 32, '', 'Venta de calzado ', 4200.00, '', 'CRUZ SANTO ELIODORO VARGAS', NULL, '', NULL, '2026-03-28 14:48:34', 1000.00, 12, 'Inversión', 'Cliente solicita préstamo para compra de accesorios para celular ');

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
(1, '2026-02-26', 36.6200, 37.0000, '2026-01-06 18:43:10', '2026-02-26 15:47:22');

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
(1, '0001', 'Lafise Dolares', 'banco', 'Lafise', '106202630', 'USD', '$', 1, '2026-03-04 09:01:03', NULL, NULL, 6564, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 1, NULL, NULL);

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
  `tipo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `email`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`, `perfil`, `idserie_recibo`) VALUES
(15, '208.96.130.158', 'ADMINISTRADOR', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'administrador@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705526891, 1776716802, 1, 'ADMINISTRADOR', 'ADMINISTRADOR', NULL, NULL, 1, NULL),
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
  `ip_address` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `email` varchar(254) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `activation_selector` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `activation_code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `forgotten_password_selector` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `forgotten_password_code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
  `remember_selector` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `remember_code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `company` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
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
(21, '::1', 'Roman Lainez', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'Rlainez@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043488, 1767572418, 1, 'Roman Lainez', 'Rlainez', NULL, NULL, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_backup_20260104_182005`
--

CREATE TABLE `users_backup_20260104_182005` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `email` varchar(254) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `activation_selector` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `activation_code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `forgotten_password_selector` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `forgotten_password_code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
  `remember_selector` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `remember_code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `company` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
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
(21, '::1', 'Roman Lainez', '$2y$10$v0eXtI1/KOlr5d2g1.F9kepj7UpnmY7BcZvkRXzRwM.akxxpQor76', 'Rlainez@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043488, 1767043674, 1, 'Roman Lainez', 'Rlainez', NULL, NULL, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_groups`
--

CREATE TABLE `users_groups` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `group_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `users_groups`
--

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tb_analisis_financiero_asalariado`
--
ALTER TABLE `tb_analisis_financiero_asalariado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tb_analisis_financiero_comerciante`
--
ALTER TABLE `tb_analisis_financiero_comerciante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tb_centro_costo`
--
ALTER TABLE `tb_centro_costo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tb_clientes`
--
ALTER TABLE `tb_clientes`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `tb_contratos`
--
ALTER TABLE `tb_contratos`
  MODIFY `idcontrato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_garantias`
--
ALTER TABLE `tb_garantias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT de la tabla `tb_garantias_fotos`
--
ALTER TABLE `tb_garantias_fotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_journal`
--
ALTER TABLE `tb_journal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_journal_entry`
--
ALTER TABLE `tb_journal_entry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_ledger`
--
ALTER TABLE `tb_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_perfil_integral_cliente`
--
ALTER TABLE `tb_perfil_integral_cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tb_period_lock`
--
ALTER TABLE `tb_period_lock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_prestamos`
--
ALTER TABLE `tb_prestamos`
  MODIFY `idprestamo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_prestamo_cuotas`
--
ALTER TABLE `tb_prestamo_cuotas`
  MODIFY `idcuota` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_prestamo_pagos`
--
ALTER TABLE `tb_prestamo_pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_series_recibos`
--
ALTER TABLE `tb_series_recibos`
  MODIFY `idserie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tb_solicitudes`
--
ALTER TABLE `tb_solicitudes`
  MODIFY `idsolicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `tb_solicitudes_comments`
--
ALTER TABLE `tb_solicitudes_comments`
  MODIFY `idcomment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_aprobaciones`
--
ALTER TABLE `tb_solicitud_aprobaciones`
  MODIFY `idaprobacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_photos`
--
ALTER TABLE `tb_solicitud_photos`
  MODIFY `idphoto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_propuestas`
--
ALTER TABLE `tb_solicitud_propuestas`
  MODIFY `idpropuesta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_referencias`
--
ALTER TABLE `tb_solicitud_referencias`
  MODIFY `idreferencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_referencias_fotos`
--
ALTER TABLE `tb_solicitud_referencias_fotos`
  MODIFY `idfoto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_uso_credito`
--
ALTER TABLE `tb_solicitud_uso_credito`
  MODIFY `iduso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `tb_tasa_cambio`
--
ALTER TABLE `tb_tasa_cambio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `teso_accounts`
--
ALTER TABLE `teso_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `teso_movimientos`
--
ALTER TABLE `teso_movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `teso_pagos`
--
ALTER TABLE `teso_pagos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
