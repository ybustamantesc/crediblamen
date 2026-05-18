-- Ensure provisiones por incobrabilidad (5201%) map to the report field
-- Created: 2026-01-24

-- BACKUP
DROP TABLE IF EXISTS backup_tb_account_report_bs_pre_5201;
CREATE TABLE backup_tb_account_report_bs_pre_5201 AS
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '5201%';

SELECT COUNT(*) AS backed_up_rows FROM backup_tb_account_report_bs_pre_5201;

-- PREVIEW
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '5201%' ORDER BY code;

-- UPDATE (run when ready)
START TRANSACTION;

UPDATE tb_account
SET report_bs = 'Gasto por provisión por incobrabilidad de la cartera de créditos'
WHERE code LIKE '5201%';

COMMIT;

-- POST-UPDATE: verify
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '5201%' ORDER BY code;

-- RESTORE (if needed)
-- UPDATE tb_account t
-- JOIN backup_tb_account_report_bs_pre_5201 b ON t.id = b.id
-- SET t.report_bs = b.report_bs;
