-- Migration: add monto_solicitado and plazo_solicitado to tb_solicitud_uso_credito if missing
SET @tbl := 'tb_solicitud_uso_credito';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@tbl AND COLUMN_NAME='monto_solicitado')=0,
  CONCAT('ALTER TABLE ', @tbl, ' ADD COLUMN monto_solicitado DECIMAL(15,2) NULL;'), 'SELECT "col_exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql2 := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@tbl AND COLUMN_NAME='plazo_solicitado')=0,
  CONCAT('ALTER TABLE ', @tbl, ' ADD COLUMN plazo_solicitado INT NULL;'), 'SELECT "col_exists"');
PREPARE stmt2 FROM @sql2; EXECUTE stmt2; DEALLOCATE PREPARE stmt2;

SELECT 'Migration complete - check columns in table ' AS msg, @tbl AS table_name;

-- Add destino_prestamo and destino_detalle if missing
SET @sql3 := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@tbl AND COLUMN_NAME='destino_prestamo')=0,
  CONCAT('ALTER TABLE ', @tbl, " ADD COLUMN destino_prestamo VARCHAR(100) NULL;"), 'SELECT "col_exists"');
PREPARE stmt3 FROM @sql3; EXECUTE stmt3; DEALLOCATE PREPARE stmt3;

SET @sql4 := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@tbl AND COLUMN_NAME='destino_detalle')=0,
  CONCAT('ALTER TABLE ', @tbl, " ADD COLUMN destino_detalle TEXT NULL;"), 'SELECT "col_exists"');
PREPARE stmt4 FROM @sql4; EXECUTE stmt4; DEALLOCATE PREPARE stmt4;

SELECT 'Added destino_prestamo/destino_detalle if missing' AS status;
