-- Add report_type column to tb_account if missing
ALTER TABLE `tb_account` 
    ADD COLUMN IF NOT EXISTS `report_type` VARCHAR(10) DEFAULT NULL COMMENT 'BS=Balance, IS=Estado de Resultado';

-- Note: MySQL older versions may not support IF NOT EXISTS on ADD COLUMN.
-- If your MySQL version errors on the above, run the safe variant below manually:
-- ALTER TABLE `tb_account` ADD COLUMN `report_type` VARCHAR(10) DEFAULT NULL COMMENT 'BS=Balance, IS=Estado de Resultado';
