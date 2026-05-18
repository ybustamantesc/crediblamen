-- ALTER script para añadir columnas faltantes en tb_perfil_integral_cliente
-- Ejecutar si obtuvo errores por columnas desconocidas (por ejemplo 'fecha_perfil')

ALTER TABLE `tb_perfil_integral_cliente`
  ADD COLUMN IF NOT EXISTS `nivel_riesgo` VARCHAR(50) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `tipo_ddc` VARCHAR(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `fecha_perfil` DATE DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `en_su_propio_pais` TINYINT(1) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `es_funcionario_publico` TINYINT(1) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `cargo_funcionario` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `pais_emision_documento` VARCHAR(150) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `categoria_otro` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `zona_cobertura` VARCHAR(50) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `sitio_web_centro_trabajo` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `ingreso_mensual_usd` DECIMAL(14,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `ingreso_mensual_cordobas` DECIMAL(14,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_profesion` VARCHAR(150) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_ocupacion_actual` VARCHAR(150) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_nombre_centro_trabajo` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_direccion_centro_trabajo` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_email_centro_trabajo` VARCHAR(150) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_sitio_web` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_telefono_centro_trabajo` VARCHAR(50) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_fax_centro_trabajo` VARCHAR(50) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_apartado_postal` VARCHAR(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_ingreso_usd` DECIMAL(14,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_ingreso_cordobas` DECIMAL(14,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `documento_legal_1_pais_emision` VARCHAR(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `documento_legal_2_pais_emision` VARCHAR(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `actividad_esperada_json` TEXT DEFAULT NULL;

-- Document numbers and dates for doc1/doc2 and registro
ALTER TABLE `tb_perfil_integral_cliente`
  ADD COLUMN IF NOT EXISTS `numero_registro` VARCHAR(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `fecha_emision_documento` DATE DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `fecha_vencimiento_documento` DATE DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `documento_legal_1_numero` VARCHAR(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `documento_legal_1_fecha_emision` DATE DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `documento_legal_1_fecha_vencimiento` DATE DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `documento_legal_2_numero` VARCHAR(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `documento_legal_2_fecha_emision` DATE DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `documento_legal_2_fecha_vencimiento` DATE DEFAULT NULL;

-- Origen de fondos (checkboxes) y campo 'otros'
ALTER TABLE `tb_perfil_integral_cliente`
  ADD COLUMN IF NOT EXISTS `origen_fondos` TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `origen_otros` VARCHAR(255) DEFAULT NULL;


-- Spouse / partner personal details
ALTER TABLE `tb_perfil_integral_cliente`
  ADD COLUMN IF NOT EXISTS `conyuge_primer_nombre` VARCHAR(150) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_segundo_nombre` VARCHAR(150) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_primer_apellido` VARCHAR(150) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_segundo_apellido` VARCHAR(150) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_direccion` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_telefono_domicilio` VARCHAR(50) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_celular` VARCHAR(50) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `conyuge_email_personal` VARCHAR(255) DEFAULT NULL;

-- Documentos legales adicionales para actividad económica (doc1/doc2)
-- Documentos doc1/doc2 intentionally omitted (fields hidden in form/PDF)

-- Tipo de relación de negocios con Crediblamen (checkboxes) y campo 'otro'
ALTER TABLE `tb_perfil_integral_cliente`
  ADD COLUMN IF NOT EXISTS `tipo_relacion` TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `tipo_relacion_otro` VARCHAR(255) DEFAULT NULL;

-- Nota: 'ADD COLUMN IF NOT EXISTS' funciona en MySQL 8+. Si tu MySQL es 5.7/5.x, elimina 'IF NOT EXISTS' y ejecuta el ALTER.
