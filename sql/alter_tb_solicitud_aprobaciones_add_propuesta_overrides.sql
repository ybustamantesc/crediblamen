-- Add JSON/text column to store propuesta_overrides (per-approval overrides of monto/tasa)
-- Idempotent: checks INFORMATION_SCHEMA before ALTER
SET @tbl := 'tb_solicitud_aprobaciones';
SET @col := 'propuesta_overrides';
SET @exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tbl AND COLUMN_NAME = @col);
SET @sql := IF(@exists = 0, 'ALTER TABLE tb_solicitud_aprobaciones ADD COLUMN propuesta_overrides TEXT DEFAULT NULL;', 'SELECT "column_exists_propuesta_overrides";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Note: this column stores a JSON array/object capturing overrides submitted during approval,
-- e.g. [{"id":9,"monto":600,"tasa":1.234}] so values for this approval are preserved.
