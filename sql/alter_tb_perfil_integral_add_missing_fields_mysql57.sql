-- ALTER script para MySQL 5.7 (sin IF NOT EXISTS)
-- Ejecute cada ALTER solo si la columna no existe. Haga un respaldo antes.

ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `nivel_riesgo` VARCHAR(50) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `tipo_ddc` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `fecha_perfil` DATE DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `en_su_propio_pais` TINYINT(1) DEFAULT 0;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `es_funcionario_publico` TINYINT(1) DEFAULT 0;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `cargo_funcionario` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `pais_emision_documento` VARCHAR(150) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `categoria_otro` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `zona_cobertura` VARCHAR(50) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `sitio_web_centro_trabajo` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `ingreso_mensual_usd` DECIMAL(14,2) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `ingreso_mensual_cordobas` DECIMAL(14,2) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_profesion` VARCHAR(150) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_ocupacion_actual` VARCHAR(150) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_nombre_centro_trabajo` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_direccion_centro_trabajo` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_email_centro_trabajo` VARCHAR(150) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_sitio_web` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_telefono_centro_trabajo` VARCHAR(50) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_fax_centro_trabajo` VARCHAR(50) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_apartado_postal` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_ingreso_usd` DECIMAL(14,2) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_ingreso_cordobas` DECIMAL(14,2) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `documento_legal_1_pais_emision` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `documento_legal_2_pais_emision` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `actividad_esperada_json` TEXT DEFAULT NULL;

-- Document numbers and dates
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `numero_registro` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `fecha_emision_documento` DATE DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `fecha_vencimiento_documento` DATE DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `documento_legal_1_numero` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `documento_legal_1_fecha_emision` DATE DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `documento_legal_1_fecha_vencimiento` DATE DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `documento_legal_2_numero` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `documento_legal_2_fecha_emision` DATE DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `documento_legal_2_fecha_vencimiento` DATE DEFAULT NULL;

-- Origen de fondos
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `origen_fondos` TEXT DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `origen_otros` VARCHAR(255) DEFAULT NULL;

-- Spouse fields
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_primer_nombre` VARCHAR(150) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_segundo_nombre` VARCHAR(150) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_primer_apellido` VARCHAR(150) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_segundo_apellido` VARCHAR(150) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_direccion` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_telefono_domicilio` VARCHAR(50) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_celular` VARCHAR(50) DEFAULT NULL;
ALTER TABLE `tb_perfil_integral_cliente` ADD COLUMN `conyuge_email_personal` VARCHAR(255) DEFAULT NULL;
