-- UPDATE report_bs = 'Deuda subordinada y obligaciones convertibles, neto'
-- Created: 2026-01-24
-- WARNING: Review the backup file before running. This UPDATE will overwrite existing report_bs values for matched accounts.

-- Preview (confirm these are the rows you expect)
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '280%'
ORDER BY code;

-- Perform the update
START TRANSACTION;

UPDATE tb_account
SET report_bs = 'Deuda subordinada y obligaciones convertibles, neto'
WHERE code LIKE '280%';

COMMIT;

-- Show results after update
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '280%'
ORDER BY code;

-- Restore if needed:
-- UPDATE tb_account t
-- JOIN backup_tb_account_report_bs_pre_deuda_subordinada b ON t.id = b.id
-- SET t.report_bs = b.report_bs;
