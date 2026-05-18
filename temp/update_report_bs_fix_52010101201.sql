-- Fix report_bs for single account 52010101201
-- Created: 2026-01-24

-- BACKUP current value for this account
DROP TABLE IF EXISTS backup_tb_account_report_bs_fix_52010101201;
CREATE TABLE backup_tb_account_report_bs_fix_52010101201 AS
SELECT id, code, name, report_bs
FROM tb_account
WHERE code = '52010101201';

SELECT COUNT(*) AS backed_up_rows FROM backup_tb_account_report_bs_fix_52010101201;

-- PREVIEW
SELECT id, code, name, report_bs FROM tb_account WHERE code = '52010101201';

-- UPDATE (run when ready)
START TRANSACTION;

UPDATE tb_account
SET report_bs = 'Gasto por provisión por incobrabilidad de la cartera de créditos'
WHERE code = '52010101201';

COMMIT;

-- VERIFY
SELECT id, code, name, report_bs FROM tb_account WHERE code = '52010101201';

-- RESTORE (if needed)
-- UPDATE tb_account t
-- JOIN backup_tb_account_report_bs_fix_52010101201 b ON t.id = b.id
-- SET t.report_bs = b.report_bs;
