-- Backup current `report_bs` for affected accounts (Otros pasivos)
-- Created: 2026-01-24
-- This backup captures all accounts whose code starts with '290'

CREATE TABLE IF NOT EXISTS backup_tb_account_report_bs_pre_otros_pasivos AS
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '290%';

-- Verify backup contents
SELECT COUNT(*) AS rows_backed_up FROM backup_tb_account_report_bs_pre_otros_pasivos;
