-- Backup current `report_bs` for affected accounts (Capital social / Aportes)
-- Created: 2026-01-24
-- This backup captures all accounts whose code starts with '310'

CREATE TABLE IF NOT EXISTS backup_tb_account_report_bs_pre_capital_social AS
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '310%';

-- Verify backup contents
SELECT COUNT(*) AS rows_backed_up FROM backup_tb_account_report_bs_pre_capital_social;
