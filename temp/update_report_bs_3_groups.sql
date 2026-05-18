-- Combined backup + update for 3 groups:
-- 1) Resultados del Ejercicio  (prefix 390%)
-- 2) Disponibilidades          (prefix 4101%)
-- 3) Cartera de créditos       (prefixes 4106%..4110%)
-- Created: 2026-01-24
-- WARNING: Review previews before running; this script updates tb_account.report_bs

-- BACKUP: store current report_bs values for all matched accounts
CREATE TABLE IF NOT EXISTS backup_tb_account_report_bs_pre_3_groups AS
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '390%'
   OR code LIKE '4101%'
   OR code LIKE '4106%'
   OR code LIKE '4107%'
   OR code LIKE '4108%'
   OR code LIKE '4109%'
   OR code LIKE '4110%';

SELECT COUNT(*) AS backed_up_rows FROM backup_tb_account_report_bs_pre_3_groups;

-- PREVIEWS: inspect rows to be modified for each group
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '390%'
ORDER BY code;

SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '4101%'
ORDER BY code;

SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '4106%'
   OR code LIKE '4107%'
   OR code LIKE '4108%'
   OR code LIKE '4109%'
   OR code LIKE '4110%'
ORDER BY code;

-- When previews look correct, uncomment the UPDATE block below and run the script.
-- Perform the updates in a transaction
START TRANSACTION;

UPDATE tb_account
SET report_bs = 'Resultados del ejercicio'
WHERE code LIKE '390%';

UPDATE tb_account
SET report_bs = 'Disponibilidades'
WHERE code LIKE '4101%';

UPDATE tb_account
SET report_bs = 'Cartera de créditos'
WHERE code LIKE '4106%'
   OR code LIKE '4107%'
   OR code LIKE '4108%'
   OR code LIKE '4109%'
   OR code LIKE '4110%';

COMMIT;

-- POST-UPDATE: verify results
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '390%'
ORDER BY code;

SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '4101%'
ORDER BY code;

SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '4106%'
   OR code LIKE '4107%'
   OR code LIKE '4108%'
   OR code LIKE '4109%'
   OR code LIKE '4110%'
ORDER BY code;

-- RESTORE (if needed): uncomment and run to restore original values from backup
-- UPDATE tb_account t
-- JOIN backup_tb_account_report_bs_pre_3_groups b ON t.id = b.id
-- SET t.report_bs = b.report_bs;
