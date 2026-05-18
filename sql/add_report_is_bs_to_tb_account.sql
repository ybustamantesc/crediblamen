-- Add report_is and report_bs columns to tb_account if missing
ALTER TABLE `tb_account` 
    ADD COLUMN IF NOT EXISTS `report_is` VARCHAR(80) DEFAULT NULL COMMENT 'Key for Estado de Resultado mapping',
    ADD COLUMN IF NOT EXISTS `report_bs` VARCHAR(80) DEFAULT NULL COMMENT 'Key for Estado de Situación Financiera mapping';

-- If your MySQL version does not support IF NOT EXISTS for ADD COLUMN, run the equivalent commands conditionally via a script.
