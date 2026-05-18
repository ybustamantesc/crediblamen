-- Migration: add classification fields to tb_account
ALTER TABLE tb_account
ADD COLUMN `level` SMALLINT NOT NULL DEFAULT 4 AFTER `type`,
ADD COLUMN `muc_class` SMALLINT NULL AFTER `level`,
ADD COLUMN `muc_group` VARCHAR(64) NULL AFTER `muc_class`,
ADD COLUMN `muc_subgroup` VARCHAR(64) NULL AFTER `muc_group`,
ADD COLUMN `statement` ENUM('BS','IS','OFF') NOT NULL DEFAULT 'BS' AFTER `muc_subgroup`,
ADD COLUMN `regulatory_code` VARCHAR(64) NULL AFTER `statement`,
ADD COLUMN `must_report` TINYINT(1) NOT NULL DEFAULT 0 AFTER `regulatory_code`,
ADD COLUMN `postable` TINYINT(1) NOT NULL DEFAULT 1 AFTER `must_report`;

-- Optional indexes
ALTER TABLE tb_account ADD INDEX idx_muc_class (muc_class);
ALTER TABLE tb_account ADD INDEX idx_statement (statement);

-- Safe: set default naturaleza where null
UPDATE tb_account SET naturaleza = 'deudora' WHERE naturaleza IS NULL AND type IN ('activo','gasto');
UPDATE tb_account SET naturaleza = 'acreedora' WHERE naturaleza IS NULL AND type IN ('pasivo','patrimonio','ingreso');

-- End migration
