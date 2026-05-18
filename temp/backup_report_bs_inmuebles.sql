-- Backup current `report_bs` for affected accounts (Inmuebles, mobiliario y equipo, neto)
-- Created: 2026-01-24
-- This backup captures all accounts whose code starts with '180'

CREATE TABLE IF NOT EXISTS backup_tb_account_report_bs_pre_inmuebles AS
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '180%';

-- Verify backup contents
SELECT COUNT(*) AS rows_backed_up FROM backup_tb_account_report_bs_pre_inmuebles;
