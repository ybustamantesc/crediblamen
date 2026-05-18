-- Agrega columna sexo a tb_solicitudes
-- Fecha: 2026-01-07

-- Verificar si la columna ya existe antes de agregarla
SET @dbname = DATABASE();
SET @tablename = 'tb_solicitudes';
SET @columnname = 'sexo';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  'SELECT ''Column already exists'' AS msg;',
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `sexo` VARCHAR(20) DEFAULT NULL AFTER `edad`;'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
