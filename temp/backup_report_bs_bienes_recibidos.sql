-- Backup current `report_bs` for affected accounts
-- Created: 2026-01-24
-- Run this first to capture existing values before applying the update

CREATE TABLE IF NOT EXISTS backup_tb_account_report_bs_pre_bienes_recibidos AS
SELECT id, code, name, report_bs
FROM tb_account
WHERE code IN (
  '15010101101','15010901201','15010101301','15010901301'
);

-- Verify backup contents
SELECT COUNT(*) AS rows_backed_up FROM backup_tb_account_report_bs_pre_bienes_recibidos;
