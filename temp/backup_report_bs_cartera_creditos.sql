-- Backup of existing report_bs values for Cartera de créditos group
-- Created: 2026-01-24

-- Preview rows that will be affected
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

-- Create a lightweight backup table you can restore from if needed
CREATE TABLE IF NOT EXISTS backup_tb_account_report_bs_pre_cartera_creditos AS
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

-- Verify contents
SELECT COUNT(*) AS rows_backed_up FROM backup_tb_account_report_bs_pre_cartera_creditos;
