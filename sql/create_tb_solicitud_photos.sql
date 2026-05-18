-- Idempotent create for tb_solicitud_photos
SET @tableName := 'tb_solicitud_photos';
SET @exists := (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = @tableName);
-- If table does not exist, create it
SET @s := IF(@exists = 0, '
CREATE TABLE `tb_solicitud_photos` (
  `idphoto` INT NOT NULL AUTO_INCREMENT,
  `idsolicitud` INT NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `grupo` VARCHAR(64) DEFAULT NULL,
  `mime` VARCHAR(50) DEFAULT NULL,
  `size` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idphoto`),
  INDEX (`idsolicitud`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
', 'SELECT "already_exists"');
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ensure existing installations get the new `grupo` column if missing.
SET @col_exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'tb_solicitud_photos' AND column_name = 'grupo');
SET @alter_sql := IF(@col_exists = 0, 'ALTER TABLE `tb_solicitud_photos` ADD COLUMN `grupo` VARCHAR(64) DEFAULT NULL', 'SELECT "column_ok"');
PREPARE stmt2 FROM @alter_sql;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;
