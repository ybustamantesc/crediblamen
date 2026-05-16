-- Update report_bs for all 540x accounts
-- Sets all accounts with prefix 540 to 'Gastos de administración y otros'
-- Created: 2026-01-24

-- BACKUP: capture current report_bs for 540x
DROP TABLE IF EXISTS backup_tb_account_report_bs_pre_540;
CREATE TABLE backup_tb_account_report_bs_pre_540 AS
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '540%';

SELECT COUNT(*) AS backed_up_rows FROM backup_tb_account_report_bs_pre_540;

-- PREVIEW: show current values for 540x
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '540%' ORDER BY code;

-- Apply update inside transaction (run when ready)
START TRANSACTION;

UPDATE tb_account
SET report_bs = 'Gastos de administración y otros'
WHERE code LIKE '540%';

COMMIT;

-- POST-UPDATE: verify changes
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '540%' ORDER BY code;

-- RESTORE (if needed): uncomment to restore original values from backup
-- UPDATE tb_account t
-- JOIN backup_tb_account_report_bs_pre_540 b ON t.id = b.id
-- SET t.report_bs = b.report_bs;
