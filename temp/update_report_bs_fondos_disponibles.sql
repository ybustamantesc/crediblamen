-- UPDATE report_bs = 'Fondos disponibles' for the first mapping set
-- Created: 2026-01-23
-- WARNING: Review the backup file before running. This UPDATE is idempotent but will overwrite existing report_bs values for matched accounts.

-- Preview (again) - confirm these are the rows you expect
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '1101%'
   OR code LIKE '1102%';

-- Perform the update
START TRANSACTION;

UPDATE tb_account
SET report_bs = 'Fondos disponibles'
WHERE code LIKE '1101%'
   OR code LIKE '1102%';

COMMIT;

-- Show results after update
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '1101%'
   OR code LIKE '1102%';

-- If you need to restore from the backup table do:
-- UPDATE tb_account t
-- JOIN backup_tb_account_report_bs_pre_fondos_disponibles b ON t.id = b.id
-- SET t.report_bs = b.report_bs;
