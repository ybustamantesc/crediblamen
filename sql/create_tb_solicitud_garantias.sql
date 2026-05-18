-- Idempotent create for tb_solicitud_garantias
SET @tableName := 'tb_solicitud_garantias';
SET @exists := (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = @tableName);
SET @s := IF(@exists = 0, '
CREATE TABLE `tb_solicitud_garantias` (
  `idgarantia` INT NOT NULL AUTO_INCREMENT,
  `idsolicitud` INT NOT NULL,
  `nombre` VARCHAR(255) NOT NULL,
  `cantidad` INT DEFAULT NULL,
  `marca` VARCHAR(150) DEFAULT NULL,
  `modelo` VARCHAR(150) DEFAULT NULL,
  `costo` DECIMAL(15,2) DEFAULT NULL,
  `tiempo_vida` VARCHAR(100) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idgarantia`),
  INDEX (`idsolicitud`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
', 'SELECT "already_exists"');
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
