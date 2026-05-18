-- Extra Contabilidad tables: mappings and ledger uniqueness

-- Mapping table to associate business events with debit/credit accounts
CREATE TABLE IF NOT EXISTS `tb_account_mapping` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mapping_key` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `debit_account_id` int NOT NULL,
  `credit_account_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mapping_key` (`mapping_key`),
  KEY `debit_idx` (`debit_account_id`),
  KEY `credit_idx` (`credit_account_id`),
  CONSTRAINT `fk_map_debit_account` FOREIGN KEY (`debit_account_id`) REFERENCES `tb_account` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_map_credit_account` FOREIGN KEY (`credit_account_id`) REFERENCES `tb_account` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ensure ledger has unique per account+period to allow upserts
-- Add unique key only if it does not already exist (avoids #1061 duplicate key errors)
SET @cnt = (SELECT COUNT(*) FROM information_schema.statistics
            WHERE table_schema = DATABASE()
              AND table_name = 'tb_ledger'
              AND index_name = 'account_period');
SET @sql = IF(@cnt = 0,
              'ALTER TABLE `tb_ledger` ADD UNIQUE KEY `account_period` (`account_id`,`period`);',
              'SELECT "account_period index already exists";');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add traceability columns to tb_journal for linking business source (payments/credits)
ALTER TABLE `tb_journal`
  ADD COLUMN IF NOT EXISTS `created_by` INT NULL AFTER `created_at`,
  ADD COLUMN IF NOT EXISTS `source_type` VARCHAR(50) NULL AFTER `created_by`,
  ADD COLUMN IF NOT EXISTS `source_id` INT NULL AFTER `source_type`,
  ADD COLUMN IF NOT EXISTS `voided` TINYINT(1) NOT NULL DEFAULT 0 AFTER `source_id`,
  ADD COLUMN IF NOT EXISTS `voided_by` INT NULL AFTER `voided`,
  ADD COLUMN IF NOT EXISTS `voided_at` DATETIME NULL AFTER `voided_by`;

-- Sample mappings (adjust codes to your chart of accounts)
-- Sample mappings: insert only when referenced account codes exist.
-- This uses subselects to find account ids by their `code`. Adjust the codes to match your chart of accounts.
INSERT IGNORE INTO `tb_account_mapping` (`mapping_key`,`description`,`debit_account_id`,`credit_account_id`)
SELECT 'loan_disbursement', 'Desembolso de crédito', da.id, ca.id
FROM (SELECT id FROM tb_account WHERE code = '1100') da
JOIN (SELECT id FROM tb_account WHERE code = '1000') ca;

INSERT IGNORE INTO `tb_account_mapping` (`mapping_key`,`description`,`debit_account_id`,`credit_account_id`)
SELECT 'loan_payment_principal', 'Pago principal de crédito', da.id, ca.id
FROM (SELECT id FROM tb_account WHERE code = '1000') da
JOIN (SELECT id FROM tb_account WHERE code = '1100') ca;

INSERT IGNORE INTO `tb_account_mapping` (`mapping_key`,`description`,`debit_account_id`,`credit_account_id`)
SELECT 'loan_payment_interest', 'Pago interés de crédito', da.id, ca.id
FROM (SELECT id FROM tb_account WHERE code = '1000') da
JOIN (SELECT id FROM tb_account WHERE code = '4000') ca;
