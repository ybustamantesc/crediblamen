-- UPDATE report_bs = 'Bienes recibidos en pago y adjudicados, neto'
-- Created: 2026-01-24
-- WARNING: Review the backup file before running. This UPDATE will overwrite existing report_bs values for matched accounts.

-- Preview (confirm these are the rows you expect)
SELECT id, code, name, report_bs
FROM tb_account
WHERE code IN (
  '15010101101','15010901201','15010101301','15010901301'
);

-- Perform the update
START TRANSACTION;

UPDATE tb_account
SET report_bs = 'Bienes recibidos en pago y adjudicados, neto'
WHERE code IN (
  '15010101101','15010901201','15010101301','15010901301'
);

COMMIT;

-- Show results after update
SELECT id, code, name, report_bs
FROM tb_account
WHERE code IN (
  '15010101101','15010901201','15010101301','15010901301'
);

-- Restore if needed:
-- UPDATE tb_account t
-- JOIN backup_tb_account_report_bs_pre_bienes_recibidos b ON t.id = b.id
-- SET t.report_bs = b.report_bs;
