-- Añade columnas a `tb_clientes` necesarias para sincronizar con la Solicitud Inicial
ALTER TABLE `tb_clientes`
  ADD COLUMN IF NOT EXISTS `fecha_nacimiento` DATE NULL,
  ADD COLUMN IF NOT EXISTS `edad` INT NULL,
  ADD COLUMN IF NOT EXISTS `estado_civil` VARCHAR(60) NULL,
  ADD COLUMN IF NOT EXISTS `nombre_conyuge` VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS `dni_conyuge` VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS `ocupacion_conyuge` VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS `telefono_conyuge` VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS `numero_dependientes` INT NULL,
  ADD COLUMN IF NOT EXISTS `condicion_vivienda` VARCHAR(60) NULL,
  ADD COLUMN IF NOT EXISTS `tiempo_residir_anios` INT NULL,
  ADD COLUMN IF NOT EXISTS `tiempo_residir_meses` INT NULL,
  ADD COLUMN IF NOT EXISTS `nombre_empresa` VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS `direccion_empresa` VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS `telefono_empresa` VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS `cargo_puesto` VARCHAR(150) NULL,
  ADD COLUMN IF NOT EXISTS `tiempo_empleo_anios` INT NULL,
  ADD COLUMN IF NOT EXISTS `tiempo_empleo_meses` INT NULL,
  ADD COLUMN IF NOT EXISTS `tipo_contrato` VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS `ingreso_mensual_neto` DECIMAL(15,2) NULL,
  ADD COLUMN IF NOT EXISTS `deducciones` VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS `nombre_negocio` VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS `actividad_economica` VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS `telefono_negocio` VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS `tiempo_operacion_anios` INT NULL,
  ADD COLUMN IF NOT EXISTS `tiempo_operacion_meses` INT NULL,
  ADD COLUMN IF NOT EXISTS `ventas_buenos_amount` DECIMAL(15,2) NULL,
  ADD COLUMN IF NOT EXISTS `ventas_malos_amount` DECIMAL(15,2) NULL,
  ADD COLUMN IF NOT EXISTS `ventas_promedio_mensual` DECIMAL(15,2) NULL;

-- Index on numero_doc for quick lookup
-- Use a full-column index (avoid prefix length and IF NOT EXISTS for wider MySQL compatibility)
-- Add index only if it does not already exist. This uses information_schema
-- and a prepared statement for compatibility across MySQL versions.
SELECT COUNT(*) INTO @idx_exists FROM information_schema.STATISTICS
 WHERE table_schema = DATABASE() AND table_name = 'tb_clientes' AND index_name = 'idx_numero_doc';

SET @sql = IF(@idx_exists = 0,
  'ALTER TABLE `tb_clientes` ADD INDEX `idx_numero_doc` (`numero_doc`)',
  'SELECT "idx_already_exists"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
