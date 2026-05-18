-- SQL: agregar columna idserie_recibo a tabla users
-- Ejecutar en la base de datos del proyecto

-- 1) Agregar columna si no existe
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `idserie_recibo` int(11) DEFAULT NULL;

-- 2) (Opcional) Agregar constraint FK si la tabla de series existe y la columna no tiene FK
-- Nota: Si tu servidor MySQL no soporta IF NOT EXISTS para ADD COLUMN, ejecuta manualmente:
-- ALTER TABLE `users` ADD `idserie_recibo` int(11) DEFAULT NULL;

-- Intentar agregar FK (silencioso si ya existe). Ajusta el nombre del constraint si es necesario.
SET @fk_name = 'fk_users_series_recibos';
SET @check = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = @fk_name);
SELECT @check;
-- Solo añadir la FK si no existe y la tabla tb_series_recibos existe
SET @tbl_exists = (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_series_recibos');
SELECT @tbl_exists;
-- Añadir FK si procede
SET @sql = IF(@check = 0 AND @tbl_exists = 1,
  CONCAT('ALTER TABLE `users` ADD CONSTRAINT ', @fk_name, ' FOREIGN KEY (`idserie_recibo`) REFERENCES `tb_series_recibos` (`idserie`) ON DELETE SET NULL ON UPDATE CASCADE;'),
  'SELECT "no action"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
