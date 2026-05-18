-- Crea la tabla teso_accounts para Cajas y Bancos (idempotente)
SET @tbl := 'teso_accounts';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@tbl)=0,
    CONCAT('CREATE TABLE ', @tbl, " (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(64) NULL,
        name VARCHAR(255) NOT NULL,
        type VARCHAR(50) NULL, -- caja|banco
        bank_name VARCHAR(255) NULL,
        account_number VARCHAR(128) NULL,
        currency VARCHAR(10) NULL,
        currency_symbol VARCHAR(8) NULL,
        estado TINYINT(1) NOT NULL DEFAULT 1,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX(code), INDEX(type)
    );"), 'SELECT "table_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
