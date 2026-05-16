-- Backup current `report_bs` for affected accounts (Otras cuentas por cobrar)
-- Created: 2026-01-24

CREATE TABLE IF NOT EXISTS backup_tb_account_report_bs_pre_cuentas_por_cobrar AS
SELECT id, code, name, report_bs
FROM tb_account
WHERE code IN (
  '16020101101','16020101201','16020101202','16020101203',
  '16020601101','16020601201','16020901201'
);

-- Verify backup contents
SELECT COUNT(*) AS rows_backed_up FROM backup_tb_account_report_bs_pre_cuentas_por_cobrar;
