-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generaciÃ³n: 06-01-2026 a las 17:25:33
-- VersiÃ³n del servidor: 11.8.3-MariaDB-log
-- VersiÃ³n de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u987557742_testsystem`
--

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
-- Estructura de tabla para la tabla `tb_account`
--

CREATE TABLE `tb_account` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

--
-- Volcado de datos para la tabla `tb_account_mapping`
--

INSERT INTO `tb_account_mapping` (`id`, `mapping_key`, `description`, `debit_account_id`, `credit_account_id`, `created_at`) VALUES
(4, 'loan_disbursement', 'Desembolso de crÃ©dito', 2, 1, '2025-12-02 12:02:28'),
(5, 'loan_payment_principal', 'Pago principal de crÃ©dito', 1, 2, '2025-12-02 12:02:28'),
(6, 'loan_payment_interest', 'Pago interÃ©s de crÃ©dito', 1, 5, '2025-12-02 12:02:28');

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
(1, 'Ruta Prueba', '5555 5555', 'Managua', '2024-02-06 12:07:42', 1);

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
  `ventas_promedio_mensual` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_clientes`
--

INSERT INTO `tb_clientes` (`idcliente`, `apellidos`, `nombres`, `direccion`, `telefono`, `email`, `tipo_doc`, `numero_doc`, `comentarios`, `estado`, `rechazado`, `fechaActualizacion`, `fecha_nacimiento`, `edad`, `estado_civil`, `nombre_conyuge`, `dni_conyuge`, `ocupacion_conyuge`, `telefono_conyuge`, `numero_dependientes`, `condicion_vivienda`, `tiempo_residir_anios`, `tiempo_residir_meses`, `nombre_empresa`, `direccion_empresa`, `telefono_empresa`, `cargo_puesto`, `tiempo_empleo_anios`, `tiempo_empleo_meses`, `tipo_contrato`, `ingreso_mensual_neto`, `deducciones`, `nombre_negocio`, `actividad_economica`, `telefono_negocio`, `tiempo_operacion_anios`, `tiempo_operacion_meses`, `ventas_buenos_amount`, `ventas_malos_amount`, `ventas_promedio_mensual`) VALUES
(2, 'Carrillo', 'Erick Antonio Ramirez', 'Bo batahola sur detras de sitel 1c arriba 1/2c al al sur', '76534038', NULL, 3, '0012702981004X', NULL, 0, 0, '2025-12-30 15:58:05', '1998-02-27', 27, 'Soltero', NULL, NULL, NULL, '76534038', NULL, 'Propia', 2, 2, 'Ernst &amp; Young', 'Managua', NULL, 'Staff I BI', 1, 1, 'Permanente', 360000.00, '1200', 'Serviconta', 'Servicios Profesionales', NULL, 1, 1, 1200.00, 300.00, 8400.00),
(3, 'AlemÃ¡n', 'Denis Ramon VÃ¡squez', 'Bo.Jonathan Gonzalez. Bancentro 22 c 1/2 al sur', '81112991', NULL, 3, '0010806670057W', NULL, NULL, 0, NULL, '1967-06-08', 58, 'Soltero', NULL, NULL, NULL, NULL, 1, 'Propia', 58, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Pulperia Algeria', NULL, '81112991', 14, NULL, 6500.00, 4200.00, 76400.00);

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
(1, 1, 4, '<!doctype html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\" />\r\n  <style>\r\n    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#222; }\r\n    .center { text-align:center; }\r\n    .small { font-size:11px; color:#444; }\r\n    .sig { margin-top:40px; }\r\n    .line { border-bottom:1px solid #000; width:240px; display:inline-block; }\r\n  </style>\r\n  <title>CONTRATO PRIVADO DE MUTUO (COMISION AMORTIZADA) SIN FIADOR</title>\r\n</head>\r\n<body>\r\n  <div class=\"center\">\r\n    <h2>CONTRATO PRIVADO DE MUTUO (COMISIÃ“N AMORTIZADA) SIN FIADOR</h2>\r\n    <div class=\"small\">Documento generado por Servicredit</div>\r\n  </div>\r\n  \r\n  <div style=\"background:#111;color:#fff;padding:10px;margin-top:12px;border-radius:4px;\">\r\n    <div style=\"text-align:center;font-weight:700;\">CONTRATO PRIVADO DE MUTUO</div>\r\n    <div style=\"text-align:center;color:#ff6666;margin-top:6px;\">NÂ° Cliente <span style=\"font-weight:700;color:#ff6666\">{{cliente_numero}}</span></div>\r\n  </div>\r\n\r\n  <p><strong>Nosotros:</strong> {{acreedor_fullname}}, mayor de edad, {{acreedor_estado_civil}}, {{acreedor_profesion}}, de este domicilio, identificada con cÃ©dula de identidad {{acreedor_doc}}; quien actÃºa en nombre y representaciÃ³n de la entidad jurÃ­dica <strong></strong>, conocida comercialmente como \"{{empresa_comercial}}\", a quien en lo sucesivo se le denominarÃ¡ <strong>\"EL ACREEDOR\"</strong>.</p>\r\n\r\n  <p><strong>Y:</strong> {{deudor_fullname}}, mayor de edad, {{deudor_estado_civil}}, {{deudor_profesion}}, identificada con cÃ©dula de identidad {{deudor_doc}}, con domicilio en {{deudor_direccion}}, a quien en lo sucesivo se le denominarÃ¡ <strong>\"EL DEUDOR\"</strong>.</p>\r\n\r\n  <p><strong>ANTECEDENTES:</strong></p>\r\n  <p>Con fecha de solicitud: <strong></strong>, el solicitante presentÃ³ la solicitud NÂ° <strong>9</strong> y se aprobÃ³ el prÃ©stamo NÂ° <strong>1</strong>. A continuaciÃ³n se consignan las declaraciones y antecedentes que constan en el expediente.</p>\r\n\r\n  <p><strong>OBJETO:</strong> El Acreedor otorga un prÃ©stamo al Deudor por la suma de <strong>$500.00</strong> dÃ³lares estadounidenses, con plazo de <strong>14</strong> meses y pagos de cuota segÃºn el plan de pagos adjunto.</p>\r\n\r\n  <p><strong>DESTINO DEL CRÃ‰DITO:</strong>  </p>\r\n\r\n  <p><strong>DECLARACIONES DEL DEUDOR:</strong></p>\r\n  <p></p>\r\n\r\n  <p>Las partes manifiestan que la informaciÃ³n contenida en la solicitud inicial y en el formato de Uso de CrÃ©dito fue proporcionada y verificada conforme a los documentos que forman parte del expediente.</p>\r\n\r\n  <h4>CLÃUSULA PRIMERA: OTORGAMIENTO DE CRÃ‰DITO Y DESTINO</h4>\r\n  <p>En este acto EL ACREEDOR otorga el presente crÃ©dito a EL DEUDOR por la Cantidad de <strong>$500.00</strong> ({{monto_credito_letras}}), que segÃºn el tipo de cambio oficial del cordoba con respecto al dÃ³lar autorizado por el Banco Central de Nicaragua para este dÃ­a a TREINTA Y SEIS CÃ“RDOBAS CON 6243/100 por dÃ³lar (USD x $1.00), equivalentes a <strong>{{monto_equivalente_usd}}</strong> (${{monto_credito_usd}}), el cual es destinado a prÃ©stamo para capital de trabajo.</p>\r\n\r\n  <h4>CLÃUSULA SEGUNDA: TASA DE INTERÃ‰S CORRIENTE Y MORATORIA</h4>\r\n  <p>EL DEUDOR reconoce a favor de EL ACREEDOR una Tasa de interÃ©s corriente del <strong>{{tasa_interes_corriente}}</strong>% anual sobre el saldo de principal desde la fecha de desembolso hasta el total de su cancelaciÃ³n y ademÃ¡s reconocerÃ¡ una Tasa de InterÃ©s Moratorio equivalente al <strong>{{tasa_moratoria}}</strong>% anual sobre las sumas adeudadas en mora.</p>\r\n\r\n  <h4>CLÃUSULA TERCERA: COMISIONES, GASTOS Y CARGOS CONEXOS</h4>\r\n  <p>a) ComisiÃ³n por desembolso: EL DEUDOR reconoce que pagarÃ¡ el <strong>{{comision_desembolso}}</strong>% sobre el monto del prÃ©stamo, en concepto de comisiÃ³n por desembolso, la cual serÃ¡ incluida y amortizada en las cuotas acordadas. AdemÃ¡s, el DEUDOR serÃ¡ responsable por los gastos de gestiÃ³n, notariales y administrativos necesarios para la ejecuciÃ³n del desembolso.</p>\r\n\r\n  <h4>CLÃUSULA CUARTA: PERIODO DE VIGENCIA, PLAZO Y MONTO DE LAS CUOTAS</h4>\r\n  <p>Este contrato tendrÃ¡ un plazo de <strong>14</strong> meses contados desde <strong>{{fecha_desembolso}}</strong>, venciendo dicho plazo el dÃ­a <strong>2026-07-09</strong>, salvo que se aplique la clÃ¡usula de vencimiento anticipado por incumplimiento.</p>\r\n\r\n  <h4>CLÃUSULA QUINTA: PLAN DE PAGOS</h4>\r\n  <p>El pago de las cuotas se realizarÃ¡ de acuerdo al plan de pagos adjunto que forma parte integrante de este contrato. La primera cuota vencerÃ¡ el dÃ­a <strong>2025-12-26</strong> y las siguientes conforme a la frecuencia pactada: <strong>{{frecuencia}}</strong>.</p>\r\n\r\n  <h4>CLÃUSULA SEXTA: INCUMPLIMIENTO</h4>\r\n  <p>En caso de incumplimiento en el pago de cualquiera de las cuotas, EL ACREEDOR podrÃ¡ exigir la totalidad del saldo vencido y devengado y aplicar los intereses moratorios establecidos en la ClÃ¡usula Segunda, asÃ­ como iniciar las gestiones de cobranza y acciones legales correspondientes.</p>\r\n\r\n  <h4>DETALLE ADICIONAL DEL PRÃ‰STAMO</h4>\r\n  <p>EL DEUDOR se obliga a pagar a EL ACREEDOR la cantidad de <strong>{{monto_principal}}</strong> ({{monto_principal_letras}}) en concepto de PRINCIPAL, y <strong>{{interes_total}}</strong> en concepto de INTERESES CORRIENTES, mÃ¡s <strong>{{comision_total}}</strong> en concepto de COMISIÃ“N POR DESEMBOLSO, para un total de <strong>{{total_conceptos}}</strong>.</p>\r\n\r\n  <p>El cronograma constarÃ¡ de <strong>14</strong> cuotas, de las cuales <strong>14</strong> serÃ¡n cuotas ordinarias de <strong>$44.26</strong> y una Ãºltima cuota de <strong>$44.26</strong>. Por ejemplo, se acuerda un monto por cuota de <strong>$44.26</strong> para las cuotas corrientes.</p>\r\n\r\n  <p>La primera cuota se realizarÃ¡ el dÃ­a <strong>2025-12-26</strong> y la Ãºltima cuota vencerÃ¡ el dÃ­a <strong>2026-07-09</strong>. En caso de que alguna fecha caiga en dÃ­a inhÃ¡bil, el pago se efectuarÃ¡ el dÃ­a hÃ¡bil siguiente salvo disposiciÃ³n en contrario.</p>\r\n\r\n  <h4>CLÃUSULA SÃ‰PTIMA: LUGAR, FORMA Y MEDIOS DE PAGO</h4>\r\n  <p>EL DEUDOR podrÃ¡ realizar los pagos de las cuotas de la presente obligaciÃ³n a EL ACREEDOR, en las siguientes formas:</p>\r\n  <ol type=\"a\">\r\n    <li>En las oficinas de CREDIBLAMEN;</li>\r\n    <li>Directamente a los gestores de cobros debidamente autorizados e identificados y designados por EL ACREEDOR;</li>\r\n    <li>TambiÃ©n podrÃ¡ realizar depÃ³sitos en las cuentas bancarias habilitadas por CREDIBLAMEN.</li>\r\n  </ol>\r\n\r\n  <h4>CLÃUSULA SEXTA: MANTENIMIENTO DE VALOR Y MONEDA DE REFERENCIA</h4>\r\n  <p>Conforme a lo establecido en el marco regulatorio, todas las variaciones de la Moneda Nacional (Devaluaciones) con respecto a la moneda de referencia serÃ¡n asumidas por EL DEUDOR; por ende, es entendido que el riesgo cambiario ha sido expresamente aceptado y asumido contractualmente por EL DEUDOR. El mantenimiento de valor se calcularÃ¡ sobre el saldo de principal a la fecha de corte neto.</p>\r\n\r\n  <h4>CLÃUSULA SÃ‰PTIMA (IMPUTACIÃ“N DE PAGO):</h4>\r\n  <p>EL DEUDOR reconoce que los pagos que realice se imputarÃ¡n en el siguiente orden: 1) Costos y gastos de recuperaciÃ³n extrajudicial o judicial; 2) Intereses moratorios que pudieran existir; 3) Gastos, costos y cargos conexos que pudieran proceder conforme a lo estipulado en este contrato; 4) Comisiones que pudieren proceder conforme a lo estipulado en este contrato; 5) Intereses corrientes adeudados; y 6) AmortizaciÃ³n al principal.</p>\r\n\r\n  <h4>CLÃUSULA OCTAVA: OBLIGACIONES DEL DEUDOR</h4>\r\n  <p>Al realizar los pagos en tiempo, modo y condiciones convenidas en el presente contrato, el DEUDOR se obliga a: a) No hacer uso diferente del dinero al que se ha estipulado en la clÃ¡usula segundo del presente contrato; b) Suministrar informaciones reales de su situaciÃ³n econÃ³mica y social antes, en el momento y despuÃ©s de otorgado el crÃ©dito; c) Comunicar, por escrito y en forma oportuna, a EL ACREEDOR cualquier cambio de su domicilio; d) Aceptar como vÃ¡lida cualquier notificaciÃ³n judicial o extrajudicial que se haga en la Ãºltima direcciÃ³n de su domicilio; e) Autorizar que EL ACREEDOR, a travÃ©s de sus representantes o funcionarios, supervise por medio de sus actuaciones el cumplimiento de las obligaciones asumidas.</p>\r\n\r\n  <h4>CLÃUSULA NOVENA: DERECHOS DEL ACREEDOR</h4>\r\n  <p>Al recibir, sin discriminaciÃ³n alguna, servicios de calidad y un trato respetuoso, EL ACREEDOR tendrÃ¡, entre otros, los derechos de: a) Exigir el pago oportuno de las cuotas; b) Aplicar intereses moratorios y cargos por mora; c) Ejecutar las garantÃ­as aportadas por el DEUDOR en caso de incumplimiento; d) Solicitar y recibir la informaciÃ³n necesaria para la administraciÃ³n y cobranza del crÃ©dito.</p>\r\n\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA: DERECHOS Y OBLIGACIONES DEL ACREEDOR</h4>\r\n  <p>EL ACREEDOR, ademÃ¡s de los derechos generales establecidos en este contrato y en la ley aplicable, tendrÃ¡ las siguientes facultades y obligaciones, sin perjuicio de otras que la normativa vigente reconozca:</p>\r\n  <ol type=\"a\">\r\n    <li>A ser atendido por EL DEUDOR y a recibir respuesta oportuna, fundamentada, comprensible e integral sobre los mismos cuando aplique.</li>\r\n    <li>A ser atendido en la sucursal de EL ACREEDOR donde suscribiÃ³ el presente contrato para realizar cualquier consulta sobre el mismo.</li>\r\n    <li>A recibir un ejemplar del presente contrato con sus respectivos anexos, incluyendo el Resumen Informativo y el plan de pago suscrito en la presente obligaciÃ³n.</li>\r\n    <li>A ser informado con la debida antelaciÃ³n sobre cualquier modificaciÃ³n que se pretenda introducir en las condiciones contractuales que le afecten; salvo disposiciÃ³n legal distinta, la comunicaciÃ³n se realizarÃ¡ con sesenta (60) dÃ­as calendario de anticipaciÃ³n; cuando las modificaciones se refieran a variaciones de tasas de interÃ©s, comisiones y/o costos, el plazo mÃ­nimo serÃ¡ de treinta (30) dÃ­as calendario.</li>\r\n    <li>A realizar el pago de forma anticipada, total o parcial, sin que por ello se imponga una penalidad que reduzca el derecho del DEUDOR a pagar anticipadamente; en tal caso se deberÃ¡n reducir los intereses generados a la fecha del pago.</li>\r\n    <li>A que EL ACREEDOR realice las gestiones de cobranza estrictamente respetando la tranquilidad familiar y laboral, la honorabilidad e integridad moral del DEUDOR, y a ser notificado en horarios razonables (no antes de las 8:00 ni despuÃ©s de las 19:00, salvo acuerdo expreso distinto).</li>\r\n    <li>Derecho de rescindir el contrato en caso de que EL ACREEDOR no cumpla con el desembolso del monto aprobado en el plazo establecido en la presente operaciÃ³n.</li>\r\n    <li>Los derechos previstos en la Ley No. 842 (Ley de ProtecciÃ³n de los Derechos de las Personas Consumidoras y Usuarias) y en la normativa aplicable, que prevalecerÃ¡n en caso de conflicto con las estipulaciones de este contrato.</li>\r\n  </ol>\r\n\r\n  <p>Adicionalmente, EL ACREEDOR se obliga a cumplir con las siguientes obligaciones y garantÃ­as de trato al DEUDOR:</p>\r\n  <ol type=\"a\">\r\n    <li>Respetar los tÃ©rminos y condiciones del contrato y brindar una respuesta oportuna, fundada, comprensible e integral a las consultas del DEUDOR.</li>\r\n    <li>Informar previamente al DEUDOR sobre las condiciones del crÃ©dito y cualquiera modificaciÃ³n que pudiera afectarle.</li>\r\n    <li>Brindar atenciÃ³n de calidad y facilitar el acceso al lugar de reclamo por parte del DEUDOR, proveyendo facilidades para que pueda formular el mismo y contar con un servicio de atenciÃ³n al usuario.</li>\r\n    <li>No exigir a las personas reclamantes la presentaciÃ³n de documentos o informaciÃ³n que no se encuentren en nuestro poder o que no guarden relaciÃ³n directa con la materia reclamada.</li>\r\n    <li>No exigir al DEUDOR la participaciÃ³n de un abogado para reclamos ordinarios; y no aplicar mÃ©todos o usos de cobro extrajudiciales que afecten el honor o la imagen del DEUDOR, ni que resulten intimidatorios.</li>\r\n    <li>Respetar las tareas de cobranza extrajudicial, de modo que las gestiones por parte de instituciones, abogados, gestores de cobranzas y servicios automatizados se realicen en horarios razonables (no antes de las 8:00 ni despuÃ©s de las 19:00, salvo acuerdo en contrario), y con respeto a la tranquilidad familiar y laboral del DEUDOR.</li>\r\n    <li>Proteger los datos personales del DEUDOR conforme a la normativa aplicable y a las polÃ­ticas de privacidad vigentes.</li>\r\n    <li>Entregar al DEUDOR copia del contrato en el momento de la firma y suministrar copia de todos los documentos que Ã©ste solicite con la debida antelaciÃ³n para la celebraciÃ³n del contrato y para responder todas las consultas que tenga.</li>\r\n    <li>Entregar, en un plazo no mayor de quince (15) dÃ­as hÃ¡biles, todos los documentos en los cuales se formalizÃ³ el crÃ©dito, debidamente firmados por las partes cuando asÃ­ proceda (Cancelaciones de Contratos, Liberaciones de Hipotecas o Prendas y Cesiones de garantÃ­a, si aplica).</li>\r\n    <li>Informar en la central de riesgo y a las autoridades correspondientes conforme a leyes del paÃ­s y Ãºnicamente cuando el DEUDOR incumpla el pago del crÃ©dito en la fecha establecida y de conformidad con la normativa aplicable.</li>\r\n    <li>Informar al DEUDOR, en forma previa a su aplicaciÃ³n, si existiese alguna modificaciÃ³n al contrato, siempre que la posibilidad de dicha modificaciÃ³n haya sido prevista expresamente en el presente contrato.</li>\r\n  </ol>\r\n\r\n  <p>Otras obligaciones y prerrogativas del ACREEDOR quedarÃ¡n sujetas a lo dispuesto en la legislaciÃ³n vigente y a las buenas prÃ¡cticas de protecciÃ³n al consumidor.</p>\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA PRIMERA: PERMUTA O CESIÃ“N DE CRÃ‰DITOS</h4>\r\n  <p>EL ACREEDOR podrÃ¡ permutar o ceder el crÃ©dito y sus garantÃ­as, sin necesidad de autorizaciÃ³n de parte de EL DEUDOR, bastando simplemente con la notificaciÃ³n que EL ACREEDOR cederÃ¡ a otro acreedor el presente crÃ©dito, de modo que el receptor del crÃ©dito deberÃ¡ respetar las condiciones originalmente pactadas en el contrato.</p>\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA SEGUNDA: VENCIMIENTO ANTICIPADO</h4>\r\n  <p>El presente contrato podrÃ¡ ser declarado como vencido, por parte de EL ACREEDOR, cuando EL DEUDOR incumpla con el pago de una o mÃ¡s de las cuotas del crÃ©dito objeto del presente contrato, o bien cuando EL DEUDOR incumpla cualesquiera de las obligaciones asumidas en razÃ³n del presente Contrato. No obstante, al plazo prefijado y la forma de pago convenida, y sin perjuicio de otras causales establecidas en este contrato, EL ACREEDOR podrÃ¡ dar por vencido anticipadamente el prÃ©stamo otorgado, resolviÃ©ndose este contrato de pleno derecho y EL ACREEDOR harÃ¡ exigible a EL DEUDOR, el pago inmediato de todo lo adeudado; con todos sus accesorios, sin necesidad de requerimiento judicial o extrajudicial, en los siguientes casos:</p>\r\n  <ol type=\"a\">\r\n    <li>Si el DEUDOR o una persona, sin o con sus instrucciones, impide a CREDIBLAMEN constatar el estado o inspeccionar los bienes constituidos en garantÃ­a a favor de CREDIBLAMEN;</li>\r\n    <li>Si se proporcionaron datos o informaciones falsas a CREDIBLAMEN sobre el DEUDOR;</li>\r\n    <li>En caso de que el DEUDOR, ya sea por presentaciÃ³n de declaratoria o por situaciÃ³n inscrita, impida o solicite su incapacidad para cumplir oportunamente con el pago de sus obligaciones corrientes o bien si el DEUDOR incurre en el deterioro de su situaciÃ³n econÃ³mica que pusiera en peligro el cumplimiento de sus obligaciones crediticias;</li>\r\n    <li>Por caso fortuito o fuerza mayor que impida que EL DEUDOR cumpliese con sus obligaciones crediticias;</li>\r\n    <li>Si el deudor faltase a las obligaciones establecidas en la ley; y</li>\r\n    <li>Si EL DEUDOR no entrega cualquier otra obligaciÃ³n que el deudor en favor de CREDIBLAMEN u otro acreedor tenga pendiente segÃºn lo establecido en el presente contrato.</li>\r\n  </ol>\r\n\r\n  <p><strong>CLAUSULAS PRINCIPALES:</strong></p>\r\n  <ol>\r\n    <li>El Deudor se obliga a pagar el capital y los intereses conforme al plan de pagos.</li>\r\n    <li>La comisiÃ³n de desembolso serÃ¡ amortizada en las cuotas segÃºn lo acordado.</li>\r\n    <li>El incumplimiento generarÃ¡ intereses moratorios y demÃ¡s acciones legales correspondientes.</li>\r\n  </ol>\r\n\r\n  <div class=\"sig\">\r\n    <div>\r\n      <div class=\"line\"></div>\r\n      <div>Firma del Deudor</div>\r\n    </div>\r\n    <div style=\"height:16px;\"></div>\r\n    <div>\r\n      <div class=\"line\"></div>\r\n      <div>Firma del Acreedor / Representante</div>\r\n    </div>\r\n  </div>\r\n\r\n  <div class=\"small\" style=\"margin-top:18px;\">Generado el: 17/12/2025</div>\r\n</body>\r\n</html>\r\n', 15, '2025-12-17 12:04:49'),
(2, 1, 4, '<!doctype html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\" />\r\n  <style>\r\n    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#222; }\r\n    .center { text-align:center; }\r\n    .small { font-size:11px; color:#444; }\r\n    .sig { margin-top:40px; }\r\n    .line { border-bottom:1px solid #000; width:240px; display:inline-block; }\r\n  </style>\r\n  <title>CONTRATO PRIVADO DE MUTUO (COMISION AMORTIZADA) SIN FIADOR</title>\r\n</head>\r\n<body>\r\n  <div class=\"center\">\r\n    <h2>CONTRATO PRIVADO DE MUTUO (COMISIÃ“N AMORTIZADA) SIN FIADOR</h2>\r\n    <div class=\"small\">Documento generado por Servicredit</div>\r\n  </div>\r\n  \r\n  <div style=\"background:#111;color:#fff;padding:10px;margin-top:12px;border-radius:4px;\">\r\n    <div style=\"text-align:center;font-weight:700;\">CONTRATO PRIVADO DE MUTUO</div>\r\n    <div style=\"text-align:center;color:#ff6666;margin-top:6px;\">NÂ° Cliente <span style=\"font-weight:700;color:#ff6666\">{{cliente_numero}}</span></div>\r\n  </div>\r\n\r\n  <p><strong>Nosotros:</strong> {{acreedor_fullname}}, mayor de edad, {{acreedor_estado_civil}}, {{acreedor_profesion}}, de este domicilio, identificada con cÃ©dula de identidad {{acreedor_doc}}; quien actÃºa en nombre y representaciÃ³n de la entidad jurÃ­dica <strong></strong>, conocida comercialmente como \"{{empresa_comercial}}\", a quien en lo sucesivo se le denominarÃ¡ <strong>\"EL ACREEDOR\"</strong>.</p>\r\n\r\n  <p><strong>Y:</strong> {{deudor_fullname}}, mayor de edad, {{deudor_estado_civil}}, {{deudor_profesion}}, identificada con cÃ©dula de identidad {{deudor_doc}}, con domicilio en {{deudor_direccion}}, a quien en lo sucesivo se le denominarÃ¡ <strong>\"EL DEUDOR\"</strong>.</p>\r\n\r\n  <p><strong>ANTECEDENTES:</strong></p>\r\n  <p>Con fecha de solicitud: <strong></strong>, el solicitante presentÃ³ la solicitud NÂ° <strong>9</strong> y se aprobÃ³ el prÃ©stamo NÂ° <strong>1</strong>. A continuaciÃ³n se consignan las declaraciones y antecedentes que constan en el expediente.</p>\r\n\r\n  <p><strong>OBJETO:</strong> El Acreedor otorga un prÃ©stamo al Deudor por la suma de <strong>$500.00</strong> dÃ³lares estadounidenses, con plazo de <strong>14</strong> meses y pagos de cuota segÃºn el plan de pagos adjunto.</p>\r\n\r\n  <p><strong>DESTINO DEL CRÃ‰DITO:</strong>  </p>\r\n\r\n  <p><strong>DECLARACIONES DEL DEUDOR:</strong></p>\r\n  <p></p>\r\n\r\n  <p>Las partes manifiestan que la informaciÃ³n contenida en la solicitud inicial y en el formato de Uso de CrÃ©dito fue proporcionada y verificada conforme a los documentos que forman parte del expediente.</p>\r\n\r\n  <h4>CLÃUSULA PRIMERA: OTORGAMIENTO DE CRÃ‰DITO Y DESTINO</h4>\r\n  <p>En este acto EL ACREEDOR otorga el presente crÃ©dito a EL DEUDOR por la Cantidad de <strong>$500.00</strong> ({{monto_credito_letras}}), que segÃºn el tipo de cambio oficial del cordoba con respecto al dÃ³lar autorizado por el Banco Central de Nicaragua para este dÃ­a a TREINTA Y SEIS CÃ“RDOBAS CON 6243/100 por dÃ³lar (USD x $1.00), equivalentes a <strong>{{monto_equivalente_usd}}</strong> (${{monto_credito_usd}}), el cual es destinado a prÃ©stamo para capital de trabajo.</p>\r\n\r\n  <h4>CLÃUSULA SEGUNDA: TASA DE INTERÃ‰S CORRIENTE Y MORATORIA</h4>\r\n  <p>EL DEUDOR reconoce a favor de EL ACREEDOR una Tasa de interÃ©s corriente del <strong>{{tasa_interes_corriente}}</strong>% anual sobre el saldo de principal desde la fecha de desembolso hasta el total de su cancelaciÃ³n y ademÃ¡s reconocerÃ¡ una Tasa de InterÃ©s Moratorio equivalente al <strong>{{tasa_moratoria}}</strong>% anual sobre las sumas adeudadas en mora.</p>\r\n\r\n  <h4>CLÃUSULA TERCERA: COMISIONES, GASTOS Y CARGOS CONEXOS</h4>\r\n  <p>a) ComisiÃ³n por desembolso: EL DEUDOR reconoce que pagarÃ¡ el <strong>{{comision_desembolso}}</strong>% sobre el monto del prÃ©stamo, en concepto de comisiÃ³n por desembolso, la cual serÃ¡ incluida y amortizada en las cuotas acordadas. AdemÃ¡s, el DEUDOR serÃ¡ responsable por los gastos de gestiÃ³n, notariales y administrativos necesarios para la ejecuciÃ³n del desembolso.</p>\r\n\r\n  <h4>CLÃUSULA CUARTA: PERIODO DE VIGENCIA, PLAZO Y MONTO DE LAS CUOTAS</h4>\r\n  <p>Este contrato tendrÃ¡ un plazo de <strong>14</strong> meses contados desde <strong>{{fecha_desembolso}}</strong>, venciendo dicho plazo el dÃ­a <strong>2026-07-09</strong>, salvo que se aplique la clÃ¡usula de vencimiento anticipado por incumplimiento.</p>\r\n\r\n  <h4>CLÃUSULA QUINTA: PLAN DE PAGOS</h4>\r\n  <p>El pago de las cuotas se realizarÃ¡ de acuerdo al plan de pagos adjunto que forma parte integrante de este contrato. La primera cuota vencerÃ¡ el dÃ­a <strong>2025-12-26</strong> y las siguientes conforme a la frecuencia pactada: <strong>{{frecuencia}}</strong>.</p>\r\n\r\n  <h4>CLÃUSULA SEXTA: INCUMPLIMIENTO</h4>\r\n  <p>En caso de incumplimiento en el pago de cualquiera de las cuotas, EL ACREEDOR podrÃ¡ exigir la totalidad del saldo vencido y devengado y aplicar los intereses moratorios establecidos en la ClÃ¡usula Segunda, asÃ­ como iniciar las gestiones de cobranza y acciones legales correspondientes.</p>\r\n\r\n  <h4>DETALLE ADICIONAL DEL PRÃ‰STAMO</h4>\r\n  <p>EL DEUDOR se obliga a pagar a EL ACREEDOR la cantidad de <strong>{{monto_principal}}</strong> ({{monto_principal_letras}}) en concepto de PRINCIPAL, y <strong>{{interes_total}}</strong> en concepto de INTERESES CORRIENTES, mÃ¡s <strong>{{comision_total}}</strong> en concepto de COMISIÃ“N POR DESEMBOLSO, para un total de <strong>{{total_conceptos}}</strong>.</p>\r\n\r\n  <p>El cronograma constarÃ¡ de <strong>14</strong> cuotas, de las cuales <strong>14</strong> serÃ¡n cuotas ordinarias de <strong>$44.26</strong> y una Ãºltima cuota de <strong>$44.26</strong>. Por ejemplo, se acuerda un monto por cuota de <strong>$44.26</strong> para las cuotas corrientes.</p>\r\n\r\n  <p>La primera cuota se realizarÃ¡ el dÃ­a <strong>2025-12-26</strong> y la Ãºltima cuota vencerÃ¡ el dÃ­a <strong>2026-07-09</strong>. En caso de que alguna fecha caiga en dÃ­a inhÃ¡bil, el pago se efectuarÃ¡ el dÃ­a hÃ¡bil siguiente salvo disposiciÃ³n en contrario.</p>\r\n\r\n  <h4>CLÃUSULA SÃ‰PTIMA: LUGAR, FORMA Y MEDIOS DE PAGO</h4>\r\n  <p>EL DEUDOR podrÃ¡ realizar los pagos de las cuotas de la presente obligaciÃ³n a EL ACREEDOR, en las siguientes formas:</p>\r\n  <ol type=\"a\">\r\n    <li>En las oficinas de CREDIBLAMEN;</li>\r\n    <li>Directamente a los gestores de cobros debidamente autorizados e identificados y designados por EL ACREEDOR;</li>\r\n    <li>TambiÃ©n podrÃ¡ realizar depÃ³sitos en las cuentas bancarias habilitadas por CREDIBLAMEN.</li>\r\n  </ol>\r\n\r\n  <h4>CLÃUSULA SEXTA: MANTENIMIENTO DE VALOR Y MONEDA DE REFERENCIA</h4>\r\n  <p>Conforme a lo establecido en el marco regulatorio, todas las variaciones de la Moneda Nacional (Devaluaciones) con respecto a la moneda de referencia serÃ¡n asumidas por EL DEUDOR; por ende, es entendido que el riesgo cambiario ha sido expresamente aceptado y asumido contractualmente por EL DEUDOR. El mantenimiento de valor se calcularÃ¡ sobre el saldo de principal a la fecha de corte neto.</p>\r\n\r\n  <h4>CLÃUSULA SÃ‰PTIMA (IMPUTACIÃ“N DE PAGO):</h4>\r\n  <p>EL DEUDOR reconoce que los pagos que realice se imputarÃ¡n en el siguiente orden: 1) Costos y gastos de recuperaciÃ³n extrajudicial o judicial; 2) Intereses moratorios que pudieran existir; 3) Gastos, costos y cargos conexos que pudieran proceder conforme a lo estipulado en este contrato; 4) Comisiones que pudieren proceder conforme a lo estipulado en este contrato; 5) Intereses corrientes adeudados; y 6) AmortizaciÃ³n al principal.</p>\r\n\r\n  <h4>CLÃUSULA OCTAVA: OBLIGACIONES DEL DEUDOR</h4>\r\n  <p>Al realizar los pagos en tiempo, modo y condiciones convenidas en el presente contrato, el DEUDOR se obliga a: a) No hacer uso diferente del dinero al que se ha estipulado en la clÃ¡usula segundo del presente contrato; b) Suministrar informaciones reales de su situaciÃ³n econÃ³mica y social antes, en el momento y despuÃ©s de otorgado el crÃ©dito; c) Comunicar, por escrito y en forma oportuna, a EL ACREEDOR cualquier cambio de su domicilio; d) Aceptar como vÃ¡lida cualquier notificaciÃ³n judicial o extrajudicial que se haga en la Ãºltima direcciÃ³n de su domicilio; e) Autorizar que EL ACREEDOR, a travÃ©s de sus representantes o funcionarios, supervise por medio de sus actuaciones el cumplimiento de las obligaciones asumidas.</p>\r\n\r\n  <h4>CLÃUSULA NOVENA: DERECHOS DEL ACREEDOR</h4>\r\n  <p>Al recibir, sin discriminaciÃ³n alguna, servicios de calidad y un trato respetuoso, EL ACREEDOR tendrÃ¡, entre otros, los derechos de: a) Exigir el pago oportuno de las cuotas; b) Aplicar intereses moratorios y cargos por mora; c) Ejecutar las garantÃ­as aportadas por el DEUDOR en caso de incumplimiento; d) Solicitar y recibir la informaciÃ³n necesaria para la administraciÃ³n y cobranza del crÃ©dito.</p>\r\n\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA: DERECHOS Y OBLIGACIONES DEL ACREEDOR</h4>\r\n  <p>EL ACREEDOR, ademÃ¡s de los derechos generales establecidos en este contrato y en la ley aplicable, tendrÃ¡ las siguientes facultades y obligaciones, sin perjuicio de otras que la normativa vigente reconozca:</p>\r\n  <ol type=\"a\">\r\n    <li>A ser atendido por EL DEUDOR y a recibir respuesta oportuna, fundamentada, comprensible e integral sobre los mismos cuando aplique.</li>\r\n    <li>A ser atendido en la sucursal de EL ACREEDOR donde suscribiÃ³ el presente contrato para realizar cualquier consulta sobre el mismo.</li>\r\n    <li>A recibir un ejemplar del presente contrato con sus respectivos anexos, incluyendo el Resumen Informativo y el plan de pago suscrito en la presente obligaciÃ³n.</li>\r\n    <li>A ser informado con la debida antelaciÃ³n sobre cualquier modificaciÃ³n que se pretenda introducir en las condiciones contractuales que le afecten; salvo disposiciÃ³n legal distinta, la comunicaciÃ³n se realizarÃ¡ con sesenta (60) dÃ­as calendario de anticipaciÃ³n; cuando las modificaciones se refieran a variaciones de tasas de interÃ©s, comisiones y/o costos, el plazo mÃ­nimo serÃ¡ de treinta (30) dÃ­as calendario.</li>\r\n    <li>A realizar el pago de forma anticipada, total o parcial, sin que por ello se imponga una penalidad que reduzca el derecho del DEUDOR a pagar anticipadamente; en tal caso se deberÃ¡n reducir los intereses generados a la fecha del pago.</li>\r\n    <li>A que EL ACREEDOR realice las gestiones de cobranza estrictamente respetando la tranquilidad familiar y laboral, la honorabilidad e integridad moral del DEUDOR, y a ser notificado en horarios razonables (no antes de las 8:00 ni despuÃ©s de las 19:00, salvo acuerdo expreso distinto).</li>\r\n    <li>Derecho de rescindir el contrato en caso de que EL ACREEDOR no cumpla con el desembolso del monto aprobado en el plazo establecido en la presente operaciÃ³n.</li>\r\n    <li>Los derechos previstos en la Ley No. 842 (Ley de ProtecciÃ³n de los Derechos de las Personas Consumidoras y Usuarias) y en la normativa aplicable, que prevalecerÃ¡n en caso de conflicto con las estipulaciones de este contrato.</li>\r\n  </ol>\r\n\r\n  <p>Adicionalmente, EL ACREEDOR se obliga a cumplir con las siguientes obligaciones y garantÃ­as de trato al DEUDOR:</p>\r\n  <ol type=\"a\">\r\n    <li>Respetar los tÃ©rminos y condiciones del contrato y brindar una respuesta oportuna, fundada, comprensible e integral a las consultas del DEUDOR.</li>\r\n    <li>Informar previamente al DEUDOR sobre las condiciones del crÃ©dito y cualquiera modificaciÃ³n que pudiera afectarle.</li>\r\n    <li>Brindar atenciÃ³n de calidad y facilitar el acceso al lugar de reclamo por parte del DEUDOR, proveyendo facilidades para que pueda formular el mismo y contar con un servicio de atenciÃ³n al usuario.</li>\r\n    <li>No exigir a las personas reclamantes la presentaciÃ³n de documentos o informaciÃ³n que no se encuentren en nuestro poder o que no guarden relaciÃ³n directa con la materia reclamada.</li>\r\n    <li>No exigir al DEUDOR la participaciÃ³n de un abogado para reclamos ordinarios; y no aplicar mÃ©todos o usos de cobro extrajudiciales que afecten el honor o la imagen del DEUDOR, ni que resulten intimidatorios.</li>\r\n    <li>Respetar las tareas de cobranza extrajudicial, de modo que las gestiones por parte de instituciones, abogados, gestores de cobranzas y servicios automatizados se realicen en horarios razonables (no antes de las 8:00 ni despuÃ©s de las 19:00, salvo acuerdo en contrario), y con respeto a la tranquilidad familiar y laboral del DEUDOR.</li>\r\n    <li>Proteger los datos personales del DEUDOR conforme a la normativa aplicable y a las polÃ­ticas de privacidad vigentes.</li>\r\n    <li>Entregar al DEUDOR copia del contrato en el momento de la firma y suministrar copia de todos los documentos que Ã©ste solicite con la debida antelaciÃ³n para la celebraciÃ³n del contrato y para responder todas las consultas que tenga.</li>\r\n    <li>Entregar, en un plazo no mayor de quince (15) dÃ­as hÃ¡biles, todos los documentos en los cuales se formalizÃ³ el crÃ©dito, debidamente firmados por las partes cuando asÃ­ proceda (Cancelaciones de Contratos, Liberaciones de Hipotecas o Prendas y Cesiones de garantÃ­a, si aplica).</li>\r\n    <li>Informar en la central de riesgo y a las autoridades correspondientes conforme a leyes del paÃ­s y Ãºnicamente cuando el DEUDOR incumpla el pago del crÃ©dito en la fecha establecida y de conformidad con la normativa aplicable.</li>\r\n    <li>Informar al DEUDOR, en forma previa a su aplicaciÃ³n, si existiese alguna modificaciÃ³n al contrato, siempre que la posibilidad de dicha modificaciÃ³n haya sido prevista expresamente en el presente contrato.</li>\r\n  </ol>\r\n\r\n  <p>Otras obligaciones y prerrogativas del ACREEDOR quedarÃ¡n sujetas a lo dispuesto en la legislaciÃ³n vigente y a las buenas prÃ¡cticas de protecciÃ³n al consumidor.</p>\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA PRIMERA: PERMUTA O CESIÃ“N DE CRÃ‰DITOS</h4>\r\n  <p>EL ACREEDOR podrÃ¡ permutar o ceder el crÃ©dito y sus garantÃ­as, sin necesidad de autorizaciÃ³n de parte de EL DEUDOR, bastando simplemente con la notificaciÃ³n que EL ACREEDOR cederÃ¡ a otro acreedor el presente crÃ©dito, de modo que el receptor del crÃ©dito deberÃ¡ respetar las condiciones originalmente pactadas en el contrato.</p>\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA SEGUNDA: VENCIMIENTO ANTICIPADO</h4>\r\n  <p>El presente contrato podrÃ¡ ser declarado como vencido, por parte de EL ACREEDOR, cuando EL DEUDOR incumpla con el pago de una o mÃ¡s de las cuotas del crÃ©dito objeto del presente contrato, o bien cuando EL DEUDOR incumpla cualesquiera de las obligaciones asumidas en razÃ³n del presente Contrato. No obstante, al plazo prefijado y la forma de pago convenida, y sin perjuicio de otras causales establecidas en este contrato, EL ACREEDOR podrÃ¡ dar por vencido anticipadamente el prÃ©stamo otorgado, resolviÃ©ndose este contrato de pleno derecho y EL ACREEDOR harÃ¡ exigible a EL DEUDOR, el pago inmediato de todo lo adeudado; con todos sus accesorios, sin necesidad de requerimiento judicial o extrajudicial, en los siguientes casos:</p>\r\n  <ol type=\"a\">\r\n    <li>Si el DEUDOR o una persona, sin o con sus instrucciones, impide a CREDIBLAMEN constatar el estado o inspeccionar los bienes constituidos en garantÃ­a a favor de CREDIBLAMEN;</li>\r\n    <li>Si se proporcionaron datos o informaciones falsas a CREDIBLAMEN sobre el DEUDOR;</li>\r\n    <li>En caso de que el DEUDOR, ya sea por presentaciÃ³n de declaratoria o por situaciÃ³n inscrita, impida o solicite su incapacidad para cumplir oportunamente con el pago de sus obligaciones corrientes o bien si el DEUDOR incurre en el deterioro de su situaciÃ³n econÃ³mica que pusiera en peligro el cumplimiento de sus obligaciones crediticias;</li>\r\n    <li>Por caso fortuito o fuerza mayor que impida que EL DEUDOR cumpliese con sus obligaciones crediticias;</li>\r\n    <li>Si el deudor faltase a las obligaciones establecidas en la ley; y</li>\r\n    <li>Si EL DEUDOR no entrega cualquier otra obligaciÃ³n que el deudor en favor de CREDIBLAMEN u otro acreedor tenga pendiente segÃºn lo establecido en el presente contrato.</li>\r\n  </ol>\r\n\r\n  <p><strong>CLAUSULAS PRINCIPALES:</strong></p>\r\n  <ol>\r\n    <li>El Deudor se obliga a pagar el capital y los intereses conforme al plan de pagos.</li>\r\n    <li>La comisiÃ³n de desembolso serÃ¡ amortizada en las cuotas segÃºn lo acordado.</li>\r\n    <li>El incumplimiento generarÃ¡ intereses moratorios y demÃ¡s acciones legales correspondientes.</li>\r\n  </ol>\r\n\r\n  <div class=\"sig\">\r\n    <div>\r\n      <div class=\"line\"></div>\r\n      <div>Firma del Deudor</div>\r\n    </div>\r\n    <div style=\"height:16px;\"></div>\r\n    <div>\r\n      <div class=\"line\"></div>\r\n      <div>Firma del Acreedor / Representante</div>\r\n    </div>\r\n  </div>\r\n\r\n  <div class=\"small\" style=\"margin-top:18px;\">Generado el: 17/12/2025</div>\r\n</body>\r\n</html>\r\n', 15, '2025-12-17 12:08:04');
INSERT INTO `tb_contratos` (`idcontrato`, `idprestamo`, `template_id`, `contenido`, `created_by`, `created_at`) VALUES
(3, 12, 4, '<!doctype html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\" />\r\n  <style>\r\n    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#222; }\r\n    .center { text-align:center; }\r\n    .small { font-size:11px; color:#444; }\r\n    .sig { margin-top:40px; }\r\n    .line { border-bottom:1px solid #000; width:240px; display:inline-block; }\r\n  </style>\r\n  <title>CONTRATO PRIVADO DE MUTUO (COMISION AMORTIZADA) SIN FIADOR</title>\r\n</head>\r\n<body>\r\n  <div class=\"center\">\r\n    <h2>CONTRATO PRIVADO DE MUTUO (COMISIÃ“N AMORTIZADA) SIN FIADOR</h2>\r\n    <div class=\"small\">Documento generado por Servicredit</div>\r\n  </div>\r\n  \r\n  <div style=\"background:#111;color:#fff;padding:10px;margin-top:12px;border-radius:4px;\">\r\n    <div style=\"text-align:center;font-weight:700;\">CONTRATO PRIVADO DE MUTUO</div>\r\n    <div style=\"text-align:center;color:#ff6666;margin-top:6px;\">NÂ° Cliente <span style=\"font-weight:700;color:#ff6666\"></span></div>\r\n  </div>\r\n\r\n  <p><strong>Nosotros:</strong> Emilia Del Socorro Mendieta, mayor de edad, casada, SociÃ³loga, de este domicilio, identificada con cÃ©dula de identidad nicaragÃ¼ense nÃºmero: cero, cero, uno, guion, uno, cuatro, cero, uno, seis, nueve, cero, cero, cuatro, seis, E (001-140169-0046E); quien actÃºa en nombre y representaciÃ³n de la entidad jurÃ­dica <strong>CREDI BLAMEN, SOCIEDAD ANÃ“NIMA</strong>, conocida comercialmente como â€œCREDIBLAMEN, S.A.â€, a quien en lo sucesivo se le denominarÃ¡ <strong>\"EL ACREEDOR\"</strong>.</p>\r\n\r\n  <p><strong>Y:</strong> Antonio Ramirez Carrillo Erick, mayor de edad, {{deudor_estado_civil}}, {{deudor_profesion}}, identificada con cÃ©dula de identidad 0012702981004X, con domicilio en aaa, a quien en lo sucesivo se le denominarÃ¡ <strong>\"EL DEUDOR\"</strong>.</p>\r\n\r\n  <p><strong>ANTECEDENTES:</strong></p>\r\n  <p>Con fecha de solicitud: <strong>2025-12-12 15:29:00</strong>, el solicitante presentÃ³ la solicitud NÂ° <strong>13</strong> y se aprobÃ³ el prÃ©stamo NÂ° <strong>12</strong>. A continuaciÃ³n se consignan las declaraciones y antecedentes que constan en el expediente.</p>\r\n\r\n  <p><strong>OBJETO:</strong> El Acreedor otorga un prÃ©stamo al Deudor por la suma de <strong>$400.00</strong> dÃ³lares estadounidenses, con plazo de <strong>12</strong> meses y pagos de cuota segÃºn el plan de pagos adjunto.</p>\r\n\r\n  <p><strong>DESTINO DEL CRÃ‰DITO:</strong> Consumo Completo</p>\r\n\r\n  <p><strong>DECLARACIONES DEL DEUDOR:</strong></p>\r\n  <p>podjdjak</p>\r\n\r\n  <p>Las partes manifiestan que la informaciÃ³n contenida en la solicitud inicial y en el formato de Uso de CrÃ©dito fue proporcionada y verificada conforme a los documentos que forman parte del expediente.</p>\r\n\r\n  <h4>CLÃUSULA PRIMERA: OTORGAMIENTO DE CRÃ‰DITO Y DESTINO</h4>\r\n  <p>En este acto EL ACREEDOR otorga el presente crÃ©dito a EL DEUDOR por la Cantidad de <strong>$400.00</strong> ({{monto_credito_letras}}), que segÃºn el tipo de cambio oficial del cordoba con respecto al dÃ³lar autorizado por el Banco Central de Nicaragua para este dÃ­a a TREINTA Y SEIS CÃ“RDOBAS CON 6243/100 por dÃ³lar (USD x $1.00), equivalentes a <strong>{{monto_equivalente_usd}}</strong> (${{monto_credito_usd}}), el cual es destinado a prÃ©stamo para capital de trabajo.</p>\r\n\r\n  <h4>CLÃUSULA SEGUNDA: TASA DE INTERÃ‰S CORRIENTE Y MORATORIA</h4>\r\n  <p>EL DEUDOR reconoce a favor de EL ACREEDOR una Tasa de interÃ©s corriente del <strong>{{tasa_interes_corriente}}</strong>% anual sobre el saldo de principal desde la fecha de desembolso hasta el total de su cancelaciÃ³n y ademÃ¡s reconocerÃ¡ una Tasa de InterÃ©s Moratorio equivalente al <strong>{{tasa_moratoria}}</strong>% anual sobre las sumas adeudadas en mora.</p>\r\n\r\n  <h4>CLÃUSULA TERCERA: COMISIONES, GASTOS Y CARGOS CONEXOS</h4>\r\n  <p>a) ComisiÃ³n por desembolso: EL DEUDOR reconoce que pagarÃ¡ el <strong>{{comision_desembolso}}</strong>% sobre el monto del prÃ©stamo, en concepto de comisiÃ³n por desembolso, la cual serÃ¡ incluida y amortizada en las cuotas acordadas. AdemÃ¡s, el DEUDOR serÃ¡ responsable por los gastos de gestiÃ³n, notariales y administrativos necesarios para la ejecuciÃ³n del desembolso.</p>\r\n\r\n  <h4>CLÃUSULA CUARTA: PERIODO DE VIGENCIA, PLAZO Y MONTO DE LAS CUOTAS</h4>\r\n  <p>Este contrato tendrÃ¡ un plazo de <strong>12</strong> meses contados desde <strong>{{fecha_desembolso}}</strong>, venciendo dicho plazo el dÃ­a <strong>2026-11-26</strong>, salvo que se aplique la clÃ¡usula de vencimiento anticipado por incumplimiento.</p>\r\n\r\n  <h4>CLÃUSULA QUINTA: PLAN DE PAGOS</h4>\r\n  <p>El pago de las cuotas se realizarÃ¡ de acuerdo al plan de pagos adjunto que forma parte integrante de este contrato. La primera cuota vencerÃ¡ el dÃ­a <strong>2025-12-26</strong> y las siguientes conforme a la frecuencia pactada: <strong>{{frecuencia}}</strong>.</p>\r\n\r\n  <h4>CLÃUSULA SEXTA: INCUMPLIMIENTO</h4>\r\n  <p>En caso de incumplimiento en el pago de cualquiera de las cuotas, EL ACREEDOR podrÃ¡ exigir la totalidad del saldo vencido y devengado y aplicar los intereses moratorios establecidos en la ClÃ¡usula Segunda, asÃ­ como iniciar las gestiones de cobranza y acciones legales correspondientes.</p>\r\n\r\n  <h4>DETALLE ADICIONAL DEL PRÃ‰STAMO</h4>\r\n  <p>EL DEUDOR se obliga a pagar a EL ACREEDOR la cantidad de <strong>{{monto_principal}}</strong> ({{monto_principal_letras}}) en concepto de PRINCIPAL, y <strong>{{interes_total}}</strong> en concepto de INTERESES CORRIENTES, mÃ¡s <strong>{{comision_total}}</strong> en concepto de COMISIÃ“N POR DESEMBOLSO, para un total de <strong>{{total_conceptos}}</strong>.</p>\r\n\r\n  <p>El cronograma constarÃ¡ de <strong>12</strong> cuotas, de las cuales <strong>12</strong> serÃ¡n cuotas ordinarias de <strong>$55.41</strong> y una Ãºltima cuota de <strong>$55.45</strong>. Por ejemplo, se acuerda un monto por cuota de <strong>$55.41</strong> para las cuotas corrientes.</p>\r\n\r\n  <p>La primera cuota se realizarÃ¡ el dÃ­a <strong>2025-12-26</strong> y la Ãºltima cuota vencerÃ¡ el dÃ­a <strong>2026-11-26</strong>. En caso de que alguna fecha caiga en dÃ­a inhÃ¡bil, el pago se efectuarÃ¡ el dÃ­a hÃ¡bil siguiente salvo disposiciÃ³n en contrario.</p>\r\n\r\n  <h4>CLÃUSULA SÃ‰PTIMA: LUGAR, FORMA Y MEDIOS DE PAGO</h4>\r\n  <p>EL DEUDOR podrÃ¡ realizar los pagos de las cuotas de la presente obligaciÃ³n a EL ACREEDOR, en las siguientes formas:</p>\r\n  <ol type=\"a\">\r\n    <li>En las oficinas de CREDIBLAMEN;</li>\r\n    <li>Directamente a los gestores de cobros debidamente autorizados e identificados y designados por EL ACREEDOR;</li>\r\n    <li>TambiÃ©n podrÃ¡ realizar depÃ³sitos en las cuentas bancarias habilitadas por CREDIBLAMEN.</li>\r\n  </ol>\r\n\r\n  <h4>CLÃUSULA SEXTA: MANTENIMIENTO DE VALOR Y MONEDA DE REFERENCIA</h4>\r\n  <p>Conforme a lo establecido en el marco regulatorio, todas las variaciones de la Moneda Nacional (Devaluaciones) con respecto a la moneda de referencia serÃ¡n asumidas por EL DEUDOR; por ende, es entendido que el riesgo cambiario ha sido expresamente aceptado y asumido contractualmente por EL DEUDOR. El mantenimiento de valor se calcularÃ¡ sobre el saldo de principal a la fecha de corte neto.</p>\r\n\r\n  <h4>CLÃUSULA SÃ‰PTIMA (IMPUTACIÃ“N DE PAGO):</h4>\r\n  <p>EL DEUDOR reconoce que los pagos que realice se imputarÃ¡n en el siguiente orden: 1) Costos y gastos de recuperaciÃ³n extrajudicial o judicial; 2) Intereses moratorios que pudieran existir; 3) Gastos, costos y cargos conexos que pudieran proceder conforme a lo estipulado en este contrato; 4) Comisiones que pudieren proceder conforme a lo estipulado en este contrato; 5) Intereses corrientes adeudados; y 6) AmortizaciÃ³n al principal.</p>\r\n\r\n  <h4>CLÃUSULA OCTAVA: OBLIGACIONES DEL DEUDOR</h4>\r\n  <p>Al realizar los pagos en tiempo, modo y condiciones convenidas en el presente contrato, el DEUDOR se obliga a: a) No hacer uso diferente del dinero al que se ha estipulado en la clÃ¡usula segundo del presente contrato; b) Suministrar informaciones reales de su situaciÃ³n econÃ³mica y social antes, en el momento y despuÃ©s de otorgado el crÃ©dito; c) Comunicar, por escrito y en forma oportuna, a EL ACREEDOR cualquier cambio de su domicilio; d) Aceptar como vÃ¡lida cualquier notificaciÃ³n judicial o extrajudicial que se haga en la Ãºltima direcciÃ³n de su domicilio; e) Autorizar que EL ACREEDOR, a travÃ©s de sus representantes o funcionarios, supervise por medio de sus actuaciones el cumplimiento de las obligaciones asumidas.</p>\r\n\r\n  <h4>CLÃUSULA NOVENA: DERECHOS DEL ACREEDOR</h4>\r\n  <p>Al recibir, sin discriminaciÃ³n alguna, servicios de calidad y un trato respetuoso, EL ACREEDOR tendrÃ¡, entre otros, los derechos de: a) Exigir el pago oportuno de las cuotas; b) Aplicar intereses moratorios y cargos por mora; c) Ejecutar las garantÃ­as aportadas por el DEUDOR en caso de incumplimiento; d) Solicitar y recibir la informaciÃ³n necesaria para la administraciÃ³n y cobranza del crÃ©dito.</p>\r\n\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA: DERECHOS Y OBLIGACIONES DEL ACREEDOR</h4>\r\n  <p>EL ACREEDOR, ademÃ¡s de los derechos generales establecidos en este contrato y en la ley aplicable, tendrÃ¡ las siguientes facultades y obligaciones, sin perjuicio de otras que la normativa vigente reconozca:</p>\r\n  <ol type=\"a\">\r\n    <li>A ser atendido por EL DEUDOR y a recibir respuesta oportuna, fundamentada, comprensible e integral sobre los mismos cuando aplique.</li>\r\n    <li>A ser atendido en la sucursal de EL ACREEDOR donde suscribiÃ³ el presente contrato para realizar cualquier consulta sobre el mismo.</li>\r\n    <li>A recibir un ejemplar del presente contrato con sus respectivos anexos, incluyendo el Resumen Informativo y el plan de pago suscrito en la presente obligaciÃ³n.</li>\r\n    <li>A ser informado con la debida antelaciÃ³n sobre cualquier modificaciÃ³n que se pretenda introducir en las condiciones contractuales que le afecten; salvo disposiciÃ³n legal distinta, la comunicaciÃ³n se realizarÃ¡ con sesenta (60) dÃ­as calendario de anticipaciÃ³n; cuando las modificaciones se refieran a variaciones de tasas de interÃ©s, comisiones y/o costos, el plazo mÃ­nimo serÃ¡ de treinta (30) dÃ­as calendario.</li>\r\n    <li>A realizar el pago de forma anticipada, total o parcial, sin que por ello se imponga una penalidad que reduzca el derecho del DEUDOR a pagar anticipadamente; en tal caso se deberÃ¡n reducir los intereses generados a la fecha del pago.</li>\r\n    <li>A que EL ACREEDOR realice las gestiones de cobranza estrictamente respetando la tranquilidad familiar y laboral, la honorabilidad e integridad moral del DEUDOR, y a ser notificado en horarios razonables (no antes de las 8:00 ni despuÃ©s de las 19:00, salvo acuerdo expreso distinto).</li>\r\n    <li>Derecho de rescindir el contrato en caso de que EL ACREEDOR no cumpla con el desembolso del monto aprobado en el plazo establecido en la presente operaciÃ³n.</li>\r\n    <li>Los derechos previstos en la Ley No. 842 (Ley de ProtecciÃ³n de los Derechos de las Personas Consumidoras y Usuarias) y en la normativa aplicable, que prevalecerÃ¡n en caso de conflicto con las estipulaciones de este contrato.</li>\r\n  </ol>\r\n\r\n  <p>Adicionalmente, EL ACREEDOR se obliga a cumplir con las siguientes obligaciones y garantÃ­as de trato al DEUDOR:</p>\r\n  <ol type=\"a\">\r\n    <li>Respetar los tÃ©rminos y condiciones del contrato y brindar una respuesta oportuna, fundada, comprensible e integral a las consultas del DEUDOR.</li>\r\n    <li>Informar previamente al DEUDOR sobre las condiciones del crÃ©dito y cualquiera modificaciÃ³n que pudiera afectarle.</li>\r\n    <li>Brindar atenciÃ³n de calidad y facilitar el acceso al lugar de reclamo por parte del DEUDOR, proveyendo facilidades para que pueda formular el mismo y contar con un servicio de atenciÃ³n al usuario.</li>\r\n    <li>No exigir a las personas reclamantes la presentaciÃ³n de documentos o informaciÃ³n que no se encuentren en nuestro poder o que no guarden relaciÃ³n directa con la materia reclamada.</li>\r\n    <li>No exigir al DEUDOR la participaciÃ³n de un abogado para reclamos ordinarios; y no aplicar mÃ©todos o usos de cobro extrajudiciales que afecten el honor o la imagen del DEUDOR, ni que resulten intimidatorios.</li>\r\n    <li>Respetar las tareas de cobranza extrajudicial, de modo que las gestiones por parte de instituciones, abogados, gestores de cobranzas y servicios automatizados se realicen en horarios razonables (no antes de las 8:00 ni despuÃ©s de las 19:00, salvo acuerdo en contrario), y con respeto a la tranquilidad familiar y laboral del DEUDOR.</li>\r\n    <li>Proteger los datos personales del DEUDOR conforme a la normativa aplicable y a las polÃ­ticas de privacidad vigentes.</li>\r\n    <li>Entregar al DEUDOR copia del contrato en el momento de la firma y suministrar copia de todos los documentos que Ã©ste solicite con la debida antelaciÃ³n para la celebraciÃ³n del contrato y para responder todas las consultas que tenga.</li>\r\n    <li>Entregar, en un plazo no mayor de quince (15) dÃ­as hÃ¡biles, todos los documentos en los cuales se formalizÃ³ el crÃ©dito, debidamente firmados por las partes cuando asÃ­ proceda (Cancelaciones de Contratos, Liberaciones de Hipotecas o Prendas y Cesiones de garantÃ­a, si aplica).</li>\r\n    <li>Informar en la central de riesgo y a las autoridades correspondientes conforme a leyes del paÃ­s y Ãºnicamente cuando el DEUDOR incumpla el pago del crÃ©dito en la fecha establecida y de conformidad con la normativa aplicable.</li>\r\n    <li>Informar al DEUDOR, en forma previa a su aplicaciÃ³n, si existiese alguna modificaciÃ³n al contrato, siempre que la posibilidad de dicha modificaciÃ³n haya sido prevista expresamente en el presente contrato.</li>\r\n  </ol>\r\n\r\n  <p>Otras obligaciones y prerrogativas del ACREEDOR quedarÃ¡n sujetas a lo dispuesto en la legislaciÃ³n vigente y a las buenas prÃ¡cticas de protecciÃ³n al consumidor.</p>\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA PRIMERA: PERMUTA O CESIÃ“N DE CRÃ‰DITOS</h4>\r\n  <p>EL ACREEDOR podrÃ¡ permutar o ceder el crÃ©dito y sus garantÃ­as, sin necesidad de autorizaciÃ³n de parte de EL DEUDOR, bastando simplemente con la notificaciÃ³n que EL ACREEDOR cederÃ¡ a otro acreedor el presente crÃ©dito, de modo que el receptor del crÃ©dito deberÃ¡ respetar las condiciones originalmente pactadas en el contrato.</p>\r\n\r\n  <h4>CLÃUSULA DÃ‰CIMA SEGUNDA: VENCIMIENTO ANTICIPADO</h4>\r\n  <p>El presente contrato podrÃ¡ ser declarado como vencido, por parte de EL ACREEDOR, cuando EL DEUDOR incumpla con el pago de una o mÃ¡s de las cuotas del crÃ©dito objeto del presente contrato, o bien cuando EL DEUDOR incumpla cualesquiera de las obligaciones asumidas en razÃ³n del presente Contrato. No obstante, al plazo prefijado y la forma de pago convenida, y sin perjuicio de otras causales establecidas en este contrato, EL ACREEDOR podrÃ¡ dar por vencido anticipadamente el prÃ©stamo otorgado, resolviÃ©ndose este contrato de pleno derecho y EL ACREEDOR harÃ¡ exigible a EL DEUDOR, el pago inmediato de todo lo adeudado; con todos sus accesorios, sin necesidad de requerimiento judicial o extrajudicial, en los siguientes casos:</p>\r\n  <ol type=\"a\">\r\n    <li>Si el DEUDOR o una persona, sin o con sus instrucciones, impide a CREDIBLAMEN constatar el estado o inspeccionar los bienes constituidos en garantÃ­a a favor de CREDIBLAMEN;</li>\r\n    <li>Si se proporcionaron datos o informaciones falsas a CREDIBLAMEN sobre el DEUDOR;</li>\r\n    <li>En caso de que el DEUDOR, ya sea por presentaciÃ³n de declaratoria o por situaciÃ³n inscrita, impida o solicite su incapacidad para cumplir oportunamente con el pago de sus obligaciones corrientes o bien si el DEUDOR incurre en el deterioro de su situaciÃ³n econÃ³mica que pusiera en peligro el cumplimiento de sus obligaciones crediticias;</li>\r\n    <li>Por caso fortuito o fuerza mayor que impida que EL DEUDOR cumpliese con sus obligaciones crediticias;</li>\r\n    <li>Si el deudor faltase a las obligaciones establecidas en la ley; y</li>\r\n    <li>Si EL DEUDOR no entrega cualquier otra obligaciÃ³n que el deudor en favor de CREDIBLAMEN u otro acreedor tenga pendiente segÃºn lo establecido en el presente contrato.</li>\r\n  </ol>\r\n\r\n  <p><strong>CLAUSULAS PRINCIPALES:</strong></p>\r\n  <ol>\r\n    <li>El Deudor se obliga a pagar el capital y los intereses conforme al plan de pagos.</li>\r\n    <li>La comisiÃ³n de desembolso serÃ¡ amortizada en las cuotas segÃºn lo acordado.</li>\r\n    <li>El incumplimiento generarÃ¡ intereses moratorios y demÃ¡s acciones legales correspondientes.</li>\r\n  </ol>\r\n\r\n  <div class=\"sig\">\r\n    <div>\r\n      <div class=\"line\"></div>\r\n      <div>Firma del Deudor</div>\r\n    </div>\r\n    <div style=\"height:16px;\"></div>\r\n    <div>\r\n      <div class=\"line\"></div>\r\n      <div>Firma del Acreedor / Representante</div>\r\n    </div>\r\n  </div>\r\n\r\n  <div class=\"small\" style=\"margin-top:18px;\">Generado el: 23/12/2025</div>\r\n</body>\r\n</html>\r\n', 15, '2025-12-23 09:48:33');

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
(1, 3, 'na', 1, 'na', 'na', 'na', 3.00, '2', NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-30 22:54:20', '2025-12-30 22:57:38');

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
  `voided_at` datetime DEFAULT NULL
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
  `description` varchar(512) DEFAULT NULL
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
  `matriz_answers` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `promotor` varchar(255) DEFAULT NULL,
  `tipo_cuota` varchar(50) DEFAULT NULL,
  `fecha_desembolso` date DEFAULT NULL,
  `primer_dia_pago` date DEFAULT NULL,
  `saldo_inicial` decimal(14,2) DEFAULT NULL,
  `pdf_printed_count` int(11) DEFAULT 0
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
  `principal` decimal(14,2) DEFAULT 0.00,
  `interes` decimal(14,2) DEFAULT 0.00,
  `cuota` decimal(14,2) DEFAULT 0.00,
  `saldo` decimal(14,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `comision` decimal(12,4) NOT NULL DEFAULT 0.0000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `idcliente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_solicitudes`
--

INSERT INTO `tb_solicitudes` (`idsolicitud`, `apellidos`, `nombres`, `direccion`, `telefono`, `email`, `tipo_doc`, `numero_doc`, `comentarios`, `estado`, `fechaActualizacion`, `negocio_propio`, `negocio_antiguedad`, `matricula_permiso`, `cedula_vigente`, `ingreso_promedio_alto`, `ingreso_promedio_bajo`, `otros_ingresos`, `otros_ingresos_docs`, `ahorros`, `inventario_disponible`, `cuentas_por_cobrar`, `porcentaje_recuperacion`, `gastos_fijos`, `gastos_operativos`, `margen_comercial`, `datos_personales`, `datos_conyuge`, `recibo_servicios`, `investigacion_vecinos`, `referencias_personales`, `barrio`, `municipio`, `tipo_credito`, `tipo_solicitud`, `estado_civil`, `uso_credito`, `analista`, `estado_aprobacion`, `fecha_solicitud`, `fuente_ingresos`, `telefono_trabajo`, `dni_conyuge`, `salario_conyuge`, `observaciones`, `giro_negocio`, `monto_solicitado`, `plazo_meses`, `frecuencia`, `tasa_interes`, `cuota_estim_estimada`, `garantia`, `otros_ingresos_detalle`, `ventas_promedio_diarios`, `ventas_promedio_mensual`, `detalle_inventario`, `cuentas_por_cobrar_amount`, `caja_amount`, `banco_amount`, `pago_alquiler`, `pago_trabajadores`, `energia`, `agua`, `internet`, `promotor`, `fecha_recepcion`, `ventas_dias_buenos`, `ventas_dias_malos`, `nombre_conyuge`, `ocupacion_conyuge`, `telefono_conyuge`, `numero_dependientes`, `fecha_nacimiento`, `edad`, `nombre_empresa`, `direccion_empresa`, `telefono_empresa`, `cargo_puesto`, `ingreso_mensual_neto`, `nombre_negocio`, `actividad_economica`, `ubicacion_negocio`, `telefono_negocio`, `numero_empleados`, `otros_gastos`, `es_nuevo`, `es_renovacion`, `tiempo_residir_anios`, `tiempo_residir_meses`, `condicion_vivienda`, `tiempo_empleo_anios`, `tiempo_empleo_meses`, `tipo_contrato`, `deducciones`, `tiempo_operacion_anios`, `tiempo_operacion_meses`, `propiedad_negocio`, `tipo_documento`, `ready_for_approval`, `rechazado`, `propuesta_tipos`, `ventas_dias_buenos_mask`, `ventas_dias_malos_mask`, `nombre_completo`, `comision_desembolso`, `edit_comment`, `rubro_credito`, `otros_ingresos_1_amount`, `otros_ingresos_1_margin`, `otros_ingresos_1_detalle`, `otros_ingresos_2_amount`, `otros_ingresos_2_margin`, `otros_ingresos_2_detalle`, `otros_ingresos_3_amount`, `otros_ingresos_3_margin`, `otros_ingresos_3_detalle`, `ventas_buenos_amount`, `ventas_malos_amount`, `declaro_verificacion`, `firma_solicitante`, `fecha_firma`, `energia_electrica`, `agua_potable`, `internet_telefonia`, `ddc_investigacion_campo`, `nombre_promotor`, `fecha_recepcion_solicitud`, `observaciones_promotor`, `destino_credito`, `idcliente`) VALUES
(3, 'Carrillo', 'Erick Antonio Ramirez', NULL, NULL, NULL, 3, '0012702981004X', NULL, NULL, '2025-12-30 15:58:00', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, 25.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CrÃ©ditos Personales', NULL, 'Soltero', NULL, NULL, 'pendiente', NULL, NULL, NULL, NULL, 0.00, NULL, 'Pizzeria casera', 500.00, 12, 'Mensual', 0.06, 71.43, 'Hipotecaria', NULL, NULL, 8400.00, NULL, 1200.00, 300.00, 1500.00, 1000.00, 1200.00, NULL, NULL, NULL, NULL, NULL, 1200, 300, NULL, NULL, '76534038', NULL, '1998-02-27', 27, 'Ernst & Young', 'Managua', NULL, 'Staff I BI', 360000.00, 'Serviconta', 'Servicios Profesionales', 'Bo batahola sur detras de sitel 1c arriba 1/2c al al sur ', NULL, 2, '100', 1, 0, 2, 2, NULL, 1, 1, NULL, 1200.00, 1, 1, NULL, NULL, 0, 0, '[\"9\"]', NULL, NULL, 'Erick Antonio Ramirez Carrillo', 0.0700, NULL, 'GanaderÃ­a', 1000.00, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1200.00, 300.00, NULL, NULL, '2025-12-30', 20.00, 220.00, 1200.00, NULL, 'Ruta Prueba', '2025-12-30', NULL, 'Consumo', 2),
(4, 'AlemÃ¡n', 'Denis Ramon VÃ¡squez', 'Bo.Jonathan Gonzalez. Bancentro 22 c 1/2 al sur', '81112991', NULL, 3, '0010806670057W', NULL, NULL, '2026-01-06 15:20:08', 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, NULL, NULL, 20.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'MicrocrÃ©ditos', NULL, 'Soltero', NULL, NULL, 'pendiente', '2026-01-06 08:33:00', NULL, NULL, NULL, 0.00, NULL, 'Pulpero', 500.00, 12, 'Quincenal', 0.06, 35.36, 'Prendaria', NULL, NULL, 76400.00, 'Arroz,Azucar,aceite, frijoles,ace, cloro,jabon,huevo,queso,leche agria,papel, coca cola,agua, raptor,medicamentos naturales, maggi.', 4500.00, 6300.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6500, 4200, NULL, NULL, NULL, 1, '1967-06-08', 58, NULL, NULL, NULL, NULL, NULL, 'Pulperia Algeria', NULL, 'Bo.Jonathan Gonzalez. Bancentro 22 c 1/2 al sur', '81112991', NULL, '2150', 0, 1, 58, NULL, 'Propia', NULL, NULL, NULL, NULL, 14, NULL, NULL, NULL, 0, 0, '[\"2\"]', 15, 112, 'Denis Ramon VÃ¡squez AlemÃ¡n', 0.0700, 'Solicitud pendiente de evidencia fotografica de las cuentas por cobrar', 'Comercio', 1500.00, 5.00, 'Seguros amÃ©rica', 1900.00, 50.00, 'Foto copiadora e impresiones', NULL, NULL, NULL, 6500.00, 4200.00, 1, NULL, '2026-01-06', 2636.57, 157.81, NULL, 'Se verifico con vecinos y se no se detecto ningÃºn comportamiento inusual ', 'Ruta Prueba', '2026-01-06', NULL, 'InversiÃ³n', 3);

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
(2, 4, 'solicitudes/4/otros_ingresos_1/1767712512_a3dbf3bc.png', NULL, NULL, '2026-01-06 10:15:12', 'otros_ingresos_1');

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
(15, '208.96.130.158', 'ADMINISTRADOR', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'administrador@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1705526891, 1767709581, 1, 'ADMINISTRADOR', 'ADMINISTRADOR', NULL, NULL, 1, NULL),
(19, '::1', 'erickprueba', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'erickprueba@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767042965, 1767043838, 1, 'erickprueba', 'erickprueba', NULL, NULL, 4, NULL),
(20, '::1', 'Carlos Mayeel Pineda', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'cpineda@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043445, 1767044221, 1, 'Carlos Mayeel Pineda', 'cpineda', NULL, NULL, 4, NULL),
(21, '::1', 'Roman Lainez', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'Rlainez@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043488, 1767572418, 1, 'Roman Lainez', 'Rlainez', NULL, NULL, 4, NULL),
(25, '::1', 'Roman Lainez', '$2y$10$y6SVrGnfhI6XSHdaJ41gceK524n9uVeUCt79ep5XF2yTLLBkB3Dfu', 'Rlainez@crediblamen.group', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1767043488, 1767572418, 1, 'Roman Lainez', 'Rlainez', NULL, NULL, 4, NULL);

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
(34, 21, 4);

--
-- Ãndices para tablas volcadas
--

--
-- Indices de la tabla `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tb_journal_entry`
--
ALTER TABLE `tb_journal_entry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_idx` (`journal_id`),
  ADD KEY `account_idx` (`account_id`);

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
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD KEY `fk_users_series_recibos` (`idserie_recibo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tb_clientes`
--
ALTER TABLE `tb_clientes`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_garantias`
--
ALTER TABLE `tb_garantias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tb_series_recibos`
--
ALTER TABLE `tb_series_recibos`
  MODIFY `idserie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tb_solicitudes`
--
ALTER TABLE `tb_solicitudes`
  MODIFY `idsolicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tb_solicitud_photos`
--
ALTER TABLE `tb_solicitud_photos`
  MODIFY `idphoto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_series_recibos` FOREIGN KEY (`idserie_recibo`) REFERENCES `tb_series_recibos` (`idserie`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
