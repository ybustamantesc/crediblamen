-- Backup current report_bs for accounts matching prefix 320%
-- Created: 2026-01-24

CREATE TABLE IF NOT EXISTS backup_tb_account_report_bs_pre_capital_adicional AS
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '320%';

SELECT COUNT(*) AS backed_up_rows FROM backup_tb_account_report_bs_pre_capital_adicional;
-- Backup current `report_bs` for affected accounts (Capital adicional / Aporte adicional)
-- Created: 2026-01-24
-- This backup captures all accounts whose code starts with '320'

CREATE TABLE IF NOT EXISTS backup_tb_account_report_bs_pre_capital_adicional AS
SELECT id, code, name, report_bs
FROM tb_account
WHERE code LIKE '320%';

-- Verify backup contents
SELECT COUNT(*) AS rows_backed_up FROM backup_tb_account_report_bs_pre_capital_adicional;
