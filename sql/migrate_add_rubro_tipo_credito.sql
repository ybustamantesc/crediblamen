-- Migration: add tipo_credito and rubro_credito to tb_solicitudes if missing
SET @tbl := 'tb_solicitudes';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@tbl AND COLUMN_NAME='tipo_credito')=0,
  CONCAT('ALTER TABLE ', @tbl, ' ADD COLUMN tipo_credito VARCHAR(100) NULL;'), 'SELECT "col_exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql2 := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@tbl AND COLUMN_NAME='rubro_credito')=0,
  CONCAT('ALTER TABLE ', @tbl, ' ADD COLUMN rubro_credito VARCHAR(255) NULL;'), 'SELECT "col_exists"');
PREPARE stmt2 FROM @sql2; EXECUTE stmt2; DEALLOCATE PREPARE stmt2;

SELECT 'Migration complete - check columns in table ' AS msg, @tbl AS table_name;
