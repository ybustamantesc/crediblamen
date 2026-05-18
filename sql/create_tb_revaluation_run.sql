-- Migration: create table to record FX revaluation runs
-- File: sql/create_tb_revaluation_run.sql
CREATE TABLE IF NOT EXISTS `tb_revaluation_run` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `fecha` DATE NOT NULL,
  `currency` VARCHAR(8) NOT NULL DEFAULT 'USD',
  `tasa_anterior` DECIMAL(16,6) DEFAULT NULL,
  `tasa_nueva` DECIMAL(16,6) DEFAULT NULL,
  `created_by` INT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `executed` TINYINT(1) NOT NULL DEFAULT 0,
  `notes` TEXT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Optional table to log generated adjustment journals per account
CREATE TABLE IF NOT EXISTS `tb_revaluation_entry` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `run_id` INT NOT NULL,
  `account_id` INT NOT NULL,
  `journal_id` INT NULL,
  `opening_local` DECIMAL(20,6) DEFAULT 0,
  `revalued_local` DECIMAL(20,6) DEFAULT 0,
  `difference` DECIMAL(20,6) DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`run_id`)
);

-- Notes:
-- `tb_revaluation_run` stores each revaluation execution request (date, previous/new FX)
-- `tb_revaluation_entry` stores per-account results and optionally links to created journal entries
