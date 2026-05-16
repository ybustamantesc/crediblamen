-- idempotent migration for missing solicitud fields
-- Adds optional columns used by the form so saved values persist and can be shown on edit.
-- Designed for MySQL 8+ (uses ADD COLUMN IF NOT EXISTS). If you run MySQL <8, see notes below.

ALTER TABLE `tb_solicitudes`
  ADD COLUMN IF NOT EXISTS `declaro_verificacion` TINYINT(1) DEFAULT 0 COMMENT 'checkbox acepto verificacion',
  ADD COLUMN IF NOT EXISTS `firma_solicitante` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `fecha_firma` DATE DEFAULT NULL,

  ADD COLUMN IF NOT EXISTS `otros_ingresos_1_amount` DECIMAL(12,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `otros_ingresos_1_margin` DECIMAL(5,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `otros_ingresos_1_detalle` VARCHAR(255) DEFAULT NULL,

  ADD COLUMN IF NOT EXISTS `otros_ingresos_2_amount` DECIMAL(12,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `otros_ingresos_2_margin` DECIMAL(5,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `otros_ingresos_2_detalle` VARCHAR(255) DEFAULT NULL,

  ADD COLUMN IF NOT EXISTS `otros_ingresos_3_amount` DECIMAL(12,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `otros_ingresos_3_margin` DECIMAL(5,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `otros_ingresos_3_detalle` VARCHAR(255) DEFAULT NULL;

-- Ensure ventas_dias_buenos / ventas_dias_malos exist (view uses ventas_buenos_amount / ventas_malos_amount mapped to these)
ALTER TABLE `tb_solicitudes`
  ADD COLUMN IF NOT EXISTS `ventas_dias_buenos` INT(11) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `ventas_dias_malos` INT(11) DEFAULT NULL;

-- energy / water / internet columns sometimes named differently in migrations; add canonical names if missing
ALTER TABLE `tb_solicitudes`
  ADD COLUMN IF NOT EXISTS `energia` VARCHAR(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `agua` VARCHAR(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `internet` VARCHAR(100) DEFAULT NULL;

-- Also add columns that match the form input names exactly (view uses these names)
ALTER TABLE `tb_solicitudes`
  ADD COLUMN IF NOT EXISTS `energia_electrica` DECIMAL(12,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `agua_potable` DECIMAL(12,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `internet_telefonia` DECIMAL(12,2) DEFAULT NULL;

-- Destino de Crédito (selector nuevo en el formulario)
ALTER TABLE `tb_solicitudes`
  ADD COLUMN IF NOT EXISTS `destino_credito` VARCHAR(100) DEFAULT NULL;

-- DDC / firma / promotor / recepción fields used in the form
ALTER TABLE `tb_solicitudes`
  ADD COLUMN IF NOT EXISTS `ddc_investigacion_campo` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `nombre_promotor` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `fecha_recepcion_solicitud` DATE DEFAULT NULL;
-- Observaciones del promotor (texto libre)
ALTER TABLE `tb_solicitudes`
  ADD COLUMN IF NOT EXISTS `observaciones_promotor` TEXT DEFAULT NULL;

-- Nuevo / Renovación flags used by the word-mode header checkboxes
ALTER TABLE `tb_solicitudes`
  ADD COLUMN IF NOT EXISTS `es_nuevo` TINYINT(1) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `es_renovacion` TINYINT(1) DEFAULT 0;

-- If `propiedad_negocio` exists but is non-text (e.g., INT), attempt to convert to VARCHAR safely.
-- This block detects existence and type and will only run the MODIFY when appropriate.
SET @col_count := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tb_solicitudes'
    AND COLUMN_NAME = 'propiedad_negocio'
    AND DATA_TYPE <> 'varchar'
);

-- If @col_count > 0 then prepare a MODIFY statement to change the type to VARCHAR(255)
SET @sql := IF(@col_count > 0,
  'ALTER TABLE `tb_solicitudes` MODIFY `propiedad_negocio` VARCHAR(255) DEFAULT NULL',
  'SELECT "propiedad_negocio ok or not present; skipping MODIFY"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- NOTES for MySQL < 8:
-- If your server is older and does NOT support "ADD COLUMN IF NOT EXISTS", run the following checks before ALTERing:
-- 1) Run this to see if a column exists:
-- SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'declaro_verificacion';
-- 2) If it returns 0, then run: ALTER TABLE `tb_solicitudes` ADD COLUMN `declaro_verificacion` TINYINT(1) DEFAULT 0;
-- Repeat for each column you need to add.

-- End of migration
