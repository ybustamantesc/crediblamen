-- Crea la tabla tb_tipo_productos de forma idempotente
SET @tbl := 'tb_tipo_productos';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@tbl)=0,
               CONCAT('CREATE TABLE ', @tbl, " (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(255) NOT NULL, porcentaje DECIMAL(7,4) NOT NULL DEFAULT 0.0000, estado TINYINT(1) NOT NULL DEFAULT 1, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX (nombre));"),
               'SELECT "table_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
