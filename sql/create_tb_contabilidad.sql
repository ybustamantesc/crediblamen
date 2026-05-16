-- SQL scaffold for Contabilidad module
-- Chart of accounts
CREATE TABLE IF NOT EXISTS `tb_account` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Journal header
CREATE TABLE IF NOT EXISTS `tb_journal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `total_debit` decimal(14,2) DEFAULT 0,
  `total_credit` decimal(14,2) DEFAULT 0,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Journal lines
CREATE TABLE IF NOT EXISTS `tb_journal_entry` (
  `id` int NOT NULL AUTO_INCREMENT,
  `journal_id` int NOT NULL,
  `account_id` int NOT NULL,
  `debit` decimal(14,2) DEFAULT 0,
  `credit` decimal(14,2) DEFAULT 0,
  `description` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_idx` (`journal_id`),
  KEY `account_idx` (`account_id`),
  CONSTRAINT `fk_journal` FOREIGN KEY (`journal_id`) REFERENCES `tb_journal` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_account` FOREIGN KEY (`account_id`) REFERENCES `tb_account` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional ledger table for balances (simple)
CREATE TABLE IF NOT EXISTS `tb_ledger` (
  `id` int NOT NULL AUTO_INCREMENT,
  `account_id` int NOT NULL,
  `period` varchar(20) DEFAULT NULL,
  `debit` decimal(14,2) DEFAULT 0,
  `credit` decimal(14,2) DEFAULT 0,
  `balance` decimal(14,2) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `account_idx` (`account_id`),
  CONSTRAINT `fk_ledger_account` FOREIGN KEY (`account_id`) REFERENCES `tb_account` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample seed (optional)
INSERT INTO `tb_account` (`code`,`name`,`type`) VALUES
('1000','Caja','Activo'),
('1100','Bancos','Activo'),
('2000','Proveedores','Pasivo'),
('3000','Capital','Patrimonio'),
('4000','Ingresos por Intereses','Ingreso'),
('5000','Gastos de Operación','Gasto');
