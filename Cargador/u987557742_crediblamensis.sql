-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-05-2026 a las 16:21:20
-- Versión del servidor: 10.4.11-MariaDB
-- Versión de PHP: 7.4.4

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `groups`
--

CREATE TABLE `groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `porcentaje_deuda_total` decimal(14,6) DEFAULT 0.000000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `porcentaje_deuda_total` decimal(14,6) DEFAULT 0.000000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(16, 'Carrillo', 'Erick Ramirez', 'asasddassd', '2321312', NULL, 3, '0012702981004X', NULL, 0, 0, '2026-03-10 15:46:06', '2026-03-10', 0, 'Soltero', 'SDASADSDADA', 'asas', 'dasa', '221312', 2, 'Propia', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'dabdsab', 'casai', '12', 21212, 1, '12312.00', '2312.00', NULL, NULL),
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
(28, 'CRUZ', 'SANTO ELIODORO VARGAS', 'Antiguo Cine ideal 3 cd oeste ', '828806760000N', NULL, 3, '4432104760000N', NULL, NULL, 0, NULL, '1976-04-21', 49, 'Union libre', NULL, NULL, NULL, NULL, NULL, 'Familiar', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Calzado Elio ', 'Venta de calzado ', NULL, 8, NULL, '3700.00', '2100.00', '4200.00', NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_contratos`
--

CREATE TABLE `tb_contratos` (
  `idcontrato` int(11) NOT NULL,
  `idprestamo` int(11) NOT NULL,
  `template_id` int(11) NOT NULL DEFAULT 0,
  `contenido` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_departamentos`
--

CREATE TABLE `tb_departamentos` (
  `id` varchar(2) NOT NULL,
  `nombre` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_distritos`
--

CREATE TABLE `tb_distritos` (
  `id` varchar(6) NOT NULL,
  `idprovincia` varchar(4) DEFAULT NULL,
  `iddepartamento` varchar(2) DEFAULT NULL,
  `nombre` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_monedas`
--

CREATE TABLE `tb_monedas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) DEFAULT NULL,
  `simbolo` varchar(6) DEFAULT NULL,
  `estado` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_pagos_detalle`
--

CREATE TABLE `tb_pagos_detalle` (
  `pdid` int(11) NOT NULL,
  `idpago` int(11) DEFAULT NULL,
  `idcuota` int(11) DEFAULT NULL,
  `monto_pagado` decimal(18,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `promotor` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_cuota` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_desembolso` date DEFAULT NULL,
  `primer_dia_pago` date DEFAULT NULL,
  `saldo_inicial` decimal(14,2) DEFAULT NULL,
  `pdf_printed_count` int(11) DEFAULT 0,
  `agrupacion_credito` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_modalidad_credito` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_sector_economico` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_municipio` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_sector_economico2` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rango_mora` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nivel` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_saldo` decimal(14,2) DEFAULT NULL,
  `codigo_busqueda2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexo` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anio_piriosidad` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primer_seg_nombre` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruta2` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `piriosidad_mes` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dia` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `periosidad_pagos` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cuota_no_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dias_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interes_devengado_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monto_cuota_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recibo_no` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monto_usd_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal_usd_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interes_usd_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saldo_usd_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comision_desembolso2_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mora_usd_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dias_mora_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dias_mora2_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serie` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `consecutivo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suma_principal_interes_mora_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resultado` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mes_desembolso` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rango` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mes_pagado` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anio_pagado` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rango2` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interes_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frecuencia_pago` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cedula_cliente` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cedula_promotor` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_tipo_zona` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_cliente2` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primer_nombre` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `segundo_nombre` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primer_apellido` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `segundo_apellido` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_prestamo_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monto_credito_saldo_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comision_desembolso_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_desembolso_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_exp_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `desembolsado` tinyint(1) NOT NULL DEFAULT 0,
  `obs_desembolso` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usuario_desembolso` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_desembolso_real` datetime DEFAULT NULL,
  `emitido` tinyint(1) NOT NULL DEFAULT 0,
  `id_cheque` int(11) DEFAULT NULL,
  `costos_legales` decimal(10,2) DEFAULT 0.00,
  `seguros` decimal(10,2) DEFAULT 0.00,
  `comisiones` decimal(10,2) DEFAULT 0.00
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
  `dias_mora_manual` int(11) DEFAULT NULL,
  `monto_mora` decimal(14,2) DEFAULT NULL,
  `cuota_no_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dias_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interes_devengado_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monto_cuota_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saldo_usd_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monto_credito_saldo_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comision_desembolso_raw` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_provincias`
--

CREATE TABLE `tb_provincias` (
  `id` varchar(4) NOT NULL,
  `nombre` varchar(45) DEFAULT NULL,
  `iddepartamento` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `idasesor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_solicitud_faf`
--

CREATE TABLE `tb_solicitud_faf` (
  `idfaf` int(11) NOT NULL,
  `idsolicitud` int(11) NOT NULL,
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'asalariado|comerciante',
  `data` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `teso_accounts`
--

INSERT INTO `teso_accounts` (`id`, `code`, `name`, `type`, `bank_name`, `account_number`, `currency`, `currency_symbol`, `estado`, `created_at`, `fecha_apertura`, `clabe`, `sig_cheque`, `dia_corte`, `ultimo_dia_mes`, `formato`, `cuenta_contable`, `nombre_banco`, `clave_banco`, `sucursal`, `funcionario`, `telefono`, `plaza`, `logo_banco`, `rfc`, `banco_extranjero`, `saldo_conciliado`, `total_cargos`, `total_abonos`, `cargos_transito`, `abonos_transito`, `montos_transito`, `saldos_sin_transito`, `saldo_inicial`, `saldo_actual`, `naturaleza`, `level`, `report_is`, `report_bs`) VALUES
(1, '0001', 'Lafise Dolares', 'banco', 'Lafise', '106202630', 'USD', '$', 1, '2026-03-04 09:01:03', NULL, NULL, 6564, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, 1, NULL, NULL),
(2, '0002', '', 'caja', '', 'Caja', '', 'C$', 1, '2026-05-05 09:21:30', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', NULL, 1, NULL, NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(8, '2026-05-08', 1, 'Desembolso préstamo #8 (Solicitud 15)', 'egreso', '1116.00', NULL, '2026-05-08 18:00:41', NULL);

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
  `tipo` varchar(50) DEFAULT NULL,
  `creado_por` varchar(255) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `teso_movimientos`
--

INSERT INTO `teso_movimientos` (`id`, `tipo_movimiento`, `concepto`, `forma_pago`, `fecha_registro`, `fecha_aplicacion`, `beneficiario`, `referencia1`, `referencia2`, `monto_total`, `iva_total`, `departamento`, `centro_costos`, `proyecto`, `descripcion`, `cuenta_id`, `created_at`, `cuenta_destino`, `tipo_transferencia`, `numero_cheque`, `estado`, `motivo_anulacion`, `fecha_anulacion`, `contabilizado`, `tipo`, `creado_por`, `fecha_creacion`) VALUES
(1, 'cheque', 'Desembolso Plan #1 - DAYANA TÁMARA BLANDON FLORES', 'CHEQUE', '2026-05-08', '2026-05-04', 'DAYANA TÁMARA BLANDON FLORES', 'PLAN#1', '', '1470.00', '0.00', NULL, NULL, NULL, 'Desembolso con costos: Legales=1, Seguros=1, Comisiones=1', 1, '2026-05-08 12:07:07', NULL, 'cargo', NULL, '1', NULL, NULL, 0, NULL, '15', '2026-05-08 13:07:07'),
(2, 'cheque', 'Cliente: Rebeca Del Socorro Castillo González | Monto crédito: 1200.00 | Tasa: 6.00% | Comisión: 7.00% | Plazo: 12', 'CHEQUE', '2026-05-08', '2026-05-08', 'Rebeca Del Socorro Castillo González', '', '', '1200.00', '0.00', NULL, NULL, NULL, 'Inicio crédito: 2026-05-08 | Monto cuota: 150.13 | Costos: Legales=0.00, Seguros=0.00, Comisiones=0.00 | Renovación: Principal=0.00, Interés Corriente=0.00, Interés Mora=0.00', 1, '2026-05-08 12:35:43', NULL, 'cargo', '1', '1', NULL, NULL, 0, NULL, '15', '2026-05-08 13:35:43'),
(3, 'cheque', 'Cliente: BISMARK FRANCISCO RIVAS ROSTRAN | Monto crédito: 1500.00 | Tasa: 6.00% | Comisión: 7.00% | Plazo: 12', 'CHEQUE', '2026-05-08', '2026-05-08', 'BISMARK FRANCISCO RIVAS ROSTRAN', '', 'p=6&fd=2026-05-08&pp=2026-06-07&cl=0.00&sg=0.00&cm=0.00', '1500.00', '0.00', NULL, NULL, NULL, 'Inicio crédito: 2026-05-08 | Monto de cuota: 187.67', 1, '2026-05-08 15:25:10', NULL, 'cargo', '2', 'activo', NULL, NULL, 0, 'desembolso_preview', '15', '2026-05-08 16:25:10'),
(4, 'cheque', 'Cliente: Eduardo Antonio Ferrufino | Monto crédito: 10000.00 | Tasa: 5.00% | Comisión: 5.00% | Plazo: 36', 'CHEQUE', '2026-05-08', '2026-05-08', 'Eduardo Antonio Ferrufino', 'Incluye: costos legales, seguros, comisiones', 'p=7&fd=2026-05-08&pp=2026-06-07&cl=100.00&sg=200.00&cm=50.00', '9650.00', '0.00', NULL, NULL, NULL, 'Inicio crédito: 2026-05-08 | Monto de cuota: 618.23', 1, '2026-05-08 15:39:47', NULL, 'cargo', '3', 'anulado', 'Anulacion por mala ejecucion', '2026-05-08 16:43:19', 0, 'desembolso_preview', '15', '2026-05-08 16:39:47'),
(5, 'cheque', 'Cliente: Eduardo Antonio Ferrufino | Monto crédito: 10000.00 | Tasa: 5.00% | Comisión: 5.00% | Plazo: 36', 'CHEQUE', '2026-05-08', '2026-05-08', 'Eduardo Antonio Ferrufino', '', 'p=7&fd=2026-05-08&pp=2026-06-07&cl=0.00&sg=0.00&cm=0.00', '8717.00', '0.00', NULL, NULL, NULL, 'Inicio crédito: 2026-05-08 | Monto de cuota: 618.23', 1, '2026-05-08 15:44:05', NULL, 'cargo', '4', 'activo', NULL, NULL, 0, 'desembolso_preview', '15', '2026-05-08 16:44:05'),
(6, 'cheque', 'Cliente: BELLY ALEXANDRA MEJIA OBANDO | Monto crédito: 1200.00 | Tasa: 6.00% | Comisión: 7.00% | Plazo: 10', 'CHEQUE', '2026-05-08', '2026-05-08', 'BELLY ALEXANDRA MEJIA OBANDO', '', 'p=8&fd=2026-05-08&pp=2026-06-07&cl=0.00&sg=0.00&cm=0.00&rn=0.00&rp=0.00&rc=0.00&rm=0.00', '1200.00', '0.00', NULL, NULL, NULL, 'Inicio crédito: 2026-05-08 | Monto de cuota: 171.44', 1, '2026-05-08 17:03:42', NULL, 'cargo', '5', 'anulado', 'Mal registrado', '2026-05-08 18:05:05', 0, 'desembolso_preview', '15', '2026-05-08 18:03:42'),
(7, 'cheque', 'Cliente: BELLY ALEXANDRA MEJIA OBANDO | Monto crédito: 1200.00 | Tasa: 6.00% | Comisión: 7.00% | Plazo: 10', 'CHEQUE', '2026-05-08', '2026-05-08', 'BELLY ALEXANDRA MEJIA OBANDO', 'Incluye: costos legales', 'p=8&fd=2026-05-08&pp=2026-06-07&cl=200.00&sg=0.00&cm=0.00&rn=0.00&rp=0.00&rc=0.00&rm=0.00', '1000.00', '0.00', NULL, NULL, NULL, 'Inicio crédito: 2026-05-08 | Monto de cuota: 171.44', 1, '2026-05-08 17:05:42', NULL, 'cargo', '6', 'activo', NULL, NULL, 0, 'desembolso_preview', '15', '2026-05-08 18:05:42');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tmp_accounts_import`
--

CREATE TABLE `tmp_accounts_import` (
  `code` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code_muc` varchar(64) DEFAULT NULL,
  `name_muc` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `email`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`, `perfil`, `idserie_recibo`) VALUES
(15, '208.96.130.158', 'ADMINISTRADOR', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'administrador@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705526891, 1778508811, 1, 'ADMINISTRADOR', 'ADMINISTRADOR', NULL, NULL, 1, NULL),
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
  `ip_address` varchar(45) CHARACTER SET utf8 NOT NULL,
  `username` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `email` varchar(254) CHARACTER SET utf8 NOT NULL,
  `activation_selector` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `activation_code` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `forgotten_password_selector` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `forgotten_password_code` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
  `remember_selector` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `remember_code` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `last_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `company` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `perfil` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `ip_address` varchar(45) CHARACTER SET utf8 NOT NULL,
  `username` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `email` varchar(254) CHARACTER SET utf8 NOT NULL,
  `activation_selector` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `activation_code` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `forgotten_password_selector` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `forgotten_password_code` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
  `remember_selector` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `remember_code` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `last_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `company` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `perfil` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_analisis_financiero_comerciante`
--
ALTER TABLE `tb_analisis_financiero_comerciante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `idcontrato` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_garantias`
--
ALTER TABLE `tb_garantias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_garantias_fotos`
--
ALTER TABLE `tb_garantias_fotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `idsolicitud` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_solicitudes_comments`
--
ALTER TABLE `tb_solicitudes_comments`
  MODIFY `idcomment` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_aprobaciones`
--
ALTER TABLE `tb_solicitud_aprobaciones`
  MODIFY `idaprobacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_photos`
--
ALTER TABLE `tb_solicitud_photos`
  MODIFY `idphoto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_propuestas`
--
ALTER TABLE `tb_solicitud_propuestas`
  MODIFY `idpropuesta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_referencias`
--
ALTER TABLE `tb_solicitud_referencias`
  MODIFY `idreferencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_referencias_fotos`
--
ALTER TABLE `tb_solicitud_referencias_fotos`
  MODIFY `idfoto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_uso_credito`
--
ALTER TABLE `tb_solicitud_uso_credito`
  MODIFY `iduso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_tasa_cambio`
--
ALTER TABLE `tb_tasa_cambio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `teso_accounts`
--
ALTER TABLE `teso_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `teso_movimientos`
--
ALTER TABLE `teso_movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `teso_pagos`
--
ALTER TABLE `teso_pagos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
