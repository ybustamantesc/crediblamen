-- UPDATE report_bs = 'Otras cuentas por pagar, neto'
-- Created: 2026-01-24
-- WARNING: Review the backup file before running. This UPDATE will overwrite existing report_bs values for matched accounts.

-- Preview (confirm these are the rows you expect)
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '260%'
ORDER BY code;

-- Perform the update
START TRANSACTION;

UPDATE tb_account
SET report_bs = 'Otras cuentas por pagar, neto'
WHERE code LIKE '260%';

COMMIT;

-- Show results after update
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '260%'
ORDER BY code;

-- Restore if needed:
-- UPDATE tb_account t
-- JOIN backup_tb_account_report_bs_pre_cuentas_por_pagar b ON t.id = b.id
-- SET t.report_bs = b.report_bs;
