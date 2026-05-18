-- UPDATE report_bs = 'Cartera de créditos, neto de provisiones por incobrabilidad'
-- Created: 2026-01-24
-- WARNING: Review the backup file before running. This UPDATE will overwrite existing report_bs values for matched accounts.

-- Preview (confirm these are the rows you expect)
SELECT id, code, name, report_bs
FROM tb_account
WHERE code IN (
  '14010101101','14010101201','14010101301',
  '14010201301','14010301301','14030101301',
  '14040101101','14040201301','14040301301',
  '14050101101',
  '14060101101','14060101301','14060102301','14060103301','14060301301','14060401301',
  '14080101101','14080101301'
);

-- Perform the update
START TRANSACTION;

UPDATE tb_account
SET report_bs = 'Cartera de créditos, neto de provisiones por incobrabilidad'
WHERE code IN (
  '14010101101','14010101201','14010101301',
  '14010201301','14010301301','14030101301',
  '14040101101','14040201301','14040301301',
  '14050101101',
  '14060101101','14060101301','14060102301','14060103301','14060301301','14060401301',
  '14080101101','14080101301'
);

COMMIT;

-- Show results after update
SELECT id, code, name, report_bs
FROM tb_account
WHERE code IN (
  '14010101101','14010101201','14010101301',
  '14010201301','14010301301','14030101301',
  '14040101101','14040201301','14040301301',
  '14050101101',
  '14060101101','14060101301','14060102301','14060103301','14060301301','14060401301',
  '14080101101','14080101301'
);

-- Restore if needed:
-- UPDATE tb_account t
-- JOIN backup_tb_account_report_bs_pre_cartera_creditos b ON t.id = b.id
-- SET t.report_bs = b.report_bs;
