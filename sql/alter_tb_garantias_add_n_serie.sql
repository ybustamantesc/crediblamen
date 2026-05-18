-- Agregar columna n_serie a tb_garantias si no existe
ALTER TABLE `tb_garantias`
ADD COLUMN IF NOT EXISTS `n_serie` VARCHAR(255) DEFAULT NULL AFTER `modelo`;

-- Nota: MySQL versiones antiguas no soportan IF NOT EXISTS en ADD COLUMN.
-- Si tu versión no lo soporta, usa este patrón seguro:
-- SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_garantias' AND COLUMN_NAME = 'n_serie');
-- PREPARE stmt FROM 'ALTER TABLE `tb_garantias` ADD COLUMN `n_serie` VARCHAR(255) DEFAULT NULL AFTER `modelo`';
-- IF @col_exists = 0 THEN
--   EXECUTE stmt;
-- END IF;
-- DEALLOCATE PREPARE stmt;
