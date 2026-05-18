-- Combined backup + update for 3 groups (single script)
-- Groups based on attachments:
-- 1) Diferencia Cambiaria        -> codes starting with 454501 (and 4501 as fallback)
-- 2) Otros ingresos financieros  -> codes starting with 4113
-- 3) Obligaciones con instituciones financieras y otros financiamientos -> 5103, 5104, 51040901201, 5105
-- Created: 2026-01-24
-- WARNING: Review PREVIEW queries before running the UPDATE block.

-- BACKUP: save existing report_bs for all matched accounts
CREATE TABLE IF NOT EXISTS backup_tb_account_report_bs_pre_4501_4113_5103 AS
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '454501%'
   OR code LIKE '4501%'
   OR code LIKE '4113%'
   OR code LIKE '5103%'
   OR code LIKE '5104%'
   OR code = '51040901201'
   OR code LIKE '5105%';

SELECT COUNT(*) AS backed_up_rows FROM backup_tb_account_report_bs_pre_4501_4113_5103;

-- PREVIEW: inspect rows for each group
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '454501%'
   OR code LIKE '4501%'
ORDER BY code;

SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '4113%'
ORDER BY code;

SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '5103%'
   OR code LIKE '5104%'
   OR code = '51040901201'
   OR code LIKE '5105%'
ORDER BY code;

-- When previews look correct, run the UPDATEs below (they are inside a transaction)
START TRANSACTION;

UPDATE tb_account
SET report_bs = 'Diferencia cambiaria'
WHERE code LIKE '454501%'
   OR code LIKE '4501%';

UPDATE tb_account
SET report_bs = 'Otros ingresos financieros'
WHERE code LIKE '4113%';

UPDATE tb_account
SET report_bs = 'Obligaciones con instituciones financieras y otros financiamientos'
WHERE code LIKE '5103%'
   OR code LIKE '5104%'
   OR code = '51040901201'
   OR code LIKE '5105%';

COMMIT;

-- POST-UPDATE: verify results
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '454501%'
   OR code LIKE '4501%'
ORDER BY code;

SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '4113%'
ORDER BY code;

SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '5103%'
   OR code LIKE '5104%'
   OR code = '51040901201'
   OR code LIKE '5105%'
ORDER BY code;

-- RESTORE (if needed): uncomment and run to restore original values
-- UPDATE tb_account t
-- JOIN backup_tb_account_report_bs_pre_4501_4113_5103 b ON t.id = b.id
-- SET t.report_bs = b.report_bs;
