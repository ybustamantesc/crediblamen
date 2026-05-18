-- Backup of existing report_bs values for accounts matching the first mapping (Fondos disponibles)
-- Created: 2026-01-23

-- Preview rows that will be affected
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '1101%'
   OR code LIKE '1102%';

-- Create a lightweight backup table you can restore from if needed
CREATE TABLE IF NOT EXISTS backup_tb_account_report_bs_pre_fondos_disponibles AS
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '1101%'
   OR code LIKE '1102%';

-- Verify contents
SELECT COUNT(*) AS rows_backed_up FROM backup_tb_account_report_bs_pre_fondos_disponibles;
