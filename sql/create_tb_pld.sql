-- DDL para módulo PLD (Prevención de Lavado de Activos)
-- Tablas: pld_kyc, pld_alertas, pld_rules, pld_scores, pld_audits

CREATE TABLE IF NOT EXISTS `pld_kyc` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` INT UNSIGNED DEFAULT NULL,
  `document_type` VARCHAR(64) DEFAULT NULL,
  `document_number` VARCHAR(128) DEFAULT NULL,
  `first_name` VARCHAR(128) DEFAULT NULL,
  `last_name` VARCHAR(128) DEFAULT NULL,
  `birth_date` DATE DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `phone` VARCHAR(64) DEFAULT NULL,
  `email` VARCHAR(128) DEFAULT NULL,
  `notes` TEXT,
  `documents` JSON DEFAULT NULL,
  `created_by` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_pldkyc_client` (`client_id`),
  INDEX `idx_pldkyc_docnum` (`document_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pld_alertas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` INT UNSIGNED DEFAULT NULL,
  `type` VARCHAR(64) DEFAULT NULL,
  `severity` TINYINT DEFAULT 1,
  `payload` JSON DEFAULT NULL,
  `status` VARCHAR(32) DEFAULT 'open',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `resolved_at` DATETIME DEFAULT NULL,
  `assigned_to` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_pldalert_client` (`client_id`),
  INDEX `idx_pldalert_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pld_rules` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL,
  `description` TEXT,
  `rule_type` VARCHAR(64) DEFAULT NULL,
  `config` JSON DEFAULT NULL,
  `active` TINYINT DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pld_scores` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` INT UNSIGNED DEFAULT NULL,
  `score` DECIMAL(5,2) DEFAULT 0.00,
  `reason` TEXT,
  `scored_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_pldscore_client` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pld_audits` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `entity` VARCHAR(128) NOT NULL,
  `entity_id` INT DEFAULT NULL,
  `action` VARCHAR(64) NOT NULL,
  `user_id` INT DEFAULT NULL,
  `notes` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_pldaudit_entity` (`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Fin DDL PLD
