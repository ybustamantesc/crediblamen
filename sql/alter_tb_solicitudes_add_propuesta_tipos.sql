-- Add JSON/text column to store propuesta_tipos (selected product ids) for solicitudes
-- Idempotent: checks INFORMATION_SCHEMA before ALTER
SET @tbl := 'tb_solicitudes';
SET @col := 'propuesta_tipos';
SET @exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tbl AND COLUMN_NAME = @col);
SET @sql := IF(@exists = 0, 'ALTER TABLE tb_solicitudes ADD COLUMN propuesta_tipos TEXT DEFAULT NULL;', 'SELECT "column_exists_propuesta_tipos";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Note: this column stores a JSON array (e.g. ["1","3"]) of tb_tipo_productos IDs selected as proposals.
-- Use JSON functions in MySQL 5.7+ if desired; storing as TEXT keeps compatibility.
