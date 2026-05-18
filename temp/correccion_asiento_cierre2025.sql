-- Asiento de ajuste: asjute de cierre contable importacion sistema 2025
-- Fecha: 2025-12-31

START TRANSACTION;

-- Ensure adjust account 9999 exists
INSERT INTO tb_account (`code`,`name`,`postable`,`type`,`naturaleza`,`created_at`) 
SELECT '9999','AJUSTE IMPORTACION',1,'MISC','acreedora',NOW() FROM DUAL 
WHERE NOT EXISTS (SELECT 1 FROM tb_account WHERE code = '9999');

-- Create journal
INSERT INTO tb_journal (`date`,`description`,`posted`,`posted_at`,`created_at`,`period_month`,`period_year`) 
VALUES ('2025-12-31','asjute de cierre contable importacion sistema 2025',1,NOW(),NOW(),12,2025);
SET @journal_id = LAST_INSERT_ID();

-- Adjustment lines (differences SaldoCorrecto - SaldoActual)
-- 1) 14010101301  diff = -11425066.21
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, a.id,
  (CASE WHEN a.naturaleza='deudora' AND -11425066.21 >= 0 THEN -11425066.21 WHEN a.naturaleza='acreedora' AND -11425066.21 < 0 THEN ABS(-11425066.21) ELSE 0 END),
  (CASE WHEN a.naturaleza='acreedora' AND -11425066.21 >= 0 THEN -11425066.21 WHEN a.naturaleza='deudora' AND -11425066.21 < 0 THEN ABS(-11425066.21) ELSE 0 END),
  'Ajuste cierre importacion 2025',1
FROM tb_account a WHERE a.code = '14010101301' LIMIT 1;

-- 2) 14040101101  diff = 11425066.24
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, a.id,
  (CASE WHEN a.naturaleza='deudora' AND 11425066.24 >= 0 THEN 11425066.24 WHEN a.naturaleza='acreedora' AND 11425066.24 < 0 THEN ABS(11425066.24) ELSE 0 END),
  (CASE WHEN a.naturaleza='acreedora' AND 11425066.24 >= 0 THEN 11425066.24 WHEN a.naturaleza='deudora' AND 11425066.24 < 0 THEN ABS(11425066.24) ELSE 0 END),
  'Ajuste cierre importacion 2025',1
FROM tb_account a WHERE a.code = '14040101101' LIMIT 1;

-- 3) 26040301201  diff = -10810.03
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, a.id,
  (CASE WHEN a.naturaleza='deudora' AND -10810.03 >= 0 THEN -10810.03 WHEN a.naturaleza='acreedora' AND -10810.03 < 0 THEN ABS(-10810.03) ELSE 0 END),
  (CASE WHEN a.naturaleza='acreedora' AND -10810.03 >= 0 THEN -10810.03 WHEN a.naturaleza='deudora' AND -10810.03 < 0 THEN ABS(-10810.03) ELSE 0 END),
  'Ajuste cierre importacion 2025',1
FROM tb_account a WHERE a.code = '26040301201' LIMIT 1;

-- 4) 26050301101  diff = -32691.33
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, a.id,
  (CASE WHEN a.naturaleza='deudora' AND -32691.33 >= 0 THEN -32691.33 WHEN a.naturaleza='acreedora' AND -32691.33 < 0 THEN ABS(-32691.33) ELSE 0 END),
  (CASE WHEN a.naturaleza='acreedora' AND -32691.33 >= 0 THEN -32691.33 WHEN a.naturaleza='deudora' AND -32691.33 < 0 THEN ABS(-32691.33) ELSE 0 END),
  'Ajuste cierre importacion 2025',1
FROM tb_account a WHERE a.code = '26050301101' LIMIT 1;

-- 5) 41060101201  diff = -13204388.02
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, a.id,
  (CASE WHEN a.naturaleza='deudora' AND -13204388.02 >= 0 THEN -13204388.02 WHEN a.naturaleza='acreedora' AND -13204388.02 < 0 THEN ABS(-13204388.02) ELSE 0 END),
  (CASE WHEN a.naturaleza='acreedora' AND -13204388.02 >= 0 THEN -13204388.02 WHEN a.naturaleza='deudora' AND -13204388.02 < 0 THEN ABS(-13204388.02) ELSE 0 END),
  'Ajuste cierre importacion 2025',1
FROM tb_account a WHERE a.code = '41060101201' LIMIT 1;

-- 6) 41060101203  diff = -369114.61
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, a.id,
  (CASE WHEN a.naturaleza='deudora' AND -369114.61 >= 0 THEN -369114.61 WHEN a.naturaleza='acreedora' AND -369114.61 < 0 THEN ABS(-369114.61) ELSE 0 END),
  (CASE WHEN a.naturaleza='acreedora' AND -369114.61 >= 0 THEN -369114.61 WHEN a.naturaleza='deudora' AND -369114.61 < 0 THEN ABS(-369114.61) ELSE 0 END),
  'Ajuste cierre importacion 2025',1
FROM tb_account a WHERE a.code = '41060101203' LIMIT 1;

-- 7) 43430301101  diff = 41330.55
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, a.id,
  (CASE WHEN a.naturaleza='deudora' AND 41330.55 >= 0 THEN 41330.55 WHEN a.naturaleza='acreedora' AND 41330.55 < 0 THEN ABS(41330.55) ELSE 0 END),
  (CASE WHEN a.naturaleza='acreedora' AND 41330.55 >= 0 THEN 41330.55 WHEN a.naturaleza='deudora' AND 41330.55 < 0 THEN ABS(41330.55) ELSE 0 END),
  'Ajuste cierre importacion 2025',1
FROM tb_account a WHERE a.code = '43430301101' LIMIT 1;

-- 8) 45450101101  diff = -989150.17
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, a.id,
  (CASE WHEN a.naturaleza='deudora' AND -989150.17 >= 0 THEN -989150.17 WHEN a.naturaleza='acreedora' AND -989150.17 < 0 THEN ABS(-989150.17) ELSE 0 END),
  (CASE WHEN a.naturaleza='acreedora' AND -989150.17 >= 0 THEN -989150.17 WHEN a.naturaleza='deudora' AND -989150.17 < 0 THEN ABS(-989150.17) ELSE 0 END),
  'Ajuste cierre importacion 2025',1
FROM tb_account a WHERE a.code = '45450101101' LIMIT 1;

-- 9) 54030901201  diff = 18190.20
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, a.id,
  (CASE WHEN a.naturaleza='deudora' AND 18190.20 >= 0 THEN 18190.20 WHEN a.naturaleza='acreedora' AND 18190.20 < 0 THEN ABS(18190.20) ELSE 0 END),
  (CASE WHEN a.naturaleza='acreedora' AND 18190.20 >= 0 THEN 18190.20 WHEN a.naturaleza='deudora' AND 18190.20 < 0 THEN ABS(18190.20) ELSE 0 END),
  'Ajuste cierre importacion 2025',1
FROM tb_account a WHERE a.code = '54030901201' LIMIT 1;

-- 10) 54050801201  diff = 37402.51
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, a.id,
  (CASE WHEN a.naturaleza='deudora' AND 37402.51 >= 0 THEN 37402.51 WHEN a.naturaleza='acreedora' AND 37402.51 < 0 THEN ABS(37402.51) ELSE 0 END),
  (CASE WHEN a.naturaleza='acreedora' AND 37402.51 >= 0 THEN 37402.51 WHEN a.naturaleza='deudora' AND 37402.51 < 0 THEN ABS(37402.51) ELSE 0 END),
  'Ajuste cierre importacion 2025',1
FROM tb_account a WHERE a.code = '54050801201' LIMIT 1;

-- 11) 54059901201  diff = 60692.17
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, a.id,
  (CASE WHEN a.naturaleza='deudora' AND 60692.17 >= 0 THEN 60692.17 WHEN a.naturaleza='acreedora' AND 60692.17 < 0 THEN ABS(60692.17) ELSE 0 END),
  (CASE WHEN a.naturaleza='acreedora' AND 60692.17 >= 0 THEN 60692.17 WHEN a.naturaleza='deudora' AND 60692.17 < 0 THEN ABS(60692.17) ELSE 0 END),
  'Ajuste cierre importacion 2025',1
FROM tb_account a WHERE a.code = '54059901201' LIMIT 1;

-- 12) 54059901301  diff = 18592.02
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, a.id,
  (CASE WHEN a.naturaleza='deudora' AND 18592.02 >= 0 THEN 18592.02 WHEN a.naturaleza='acreedora' AND 18592.02 < 0 THEN ABS(18592.02) ELSE 0 END),
  (CASE WHEN a.naturaleza='acreedora' AND 18592.02 >= 0 THEN 18592.02 WHEN a.naturaleza='deudora' AND 18592.02 < 0 THEN ABS(18592.02) ELSE 0 END),
  'Ajuste cierre importacion 2025',1
FROM tb_account a WHERE a.code = '54059901301' LIMIT 1;

-- 13) 55550101101  diff = 36549.44
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, a.id,
  (CASE WHEN a.naturaleza='deudora' AND 36549.44 >= 0 THEN 36549.44 WHEN a.naturaleza='acreedora' AND 36549.44 < 0 THEN ABS(36549.44) ELSE 0 END),
  (CASE WHEN a.naturaleza='acreedora' AND 36549.44 >= 0 THEN 36549.44 WHEN a.naturaleza='deudora' AND 36549.44 < 0 THEN ABS(36549.44) ELSE 0 END),
  'Ajuste cierre importacion 2025',1
FROM tb_account a WHERE a.code = '55550101101' LIMIT 1;

-- 14) 63010201201  diff = 1046583.42
INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
SELECT @journal_id, a.id,
  (CASE WHEN a.naturaleza='deudora' AND 1046583.42 >= 0 THEN 1046583.42 WHEN a.naturaleza='acreedora' AND 1046583.42 < 0 THEN ABS(1046583.42) ELSE 0 END),
  (CASE WHEN a.naturaleza='acreedora' AND 1046583.42 >= 0 THEN 1046583.42 WHEN a.naturaleza='deudora' AND 1046583.42 < 0 THEN ABS(1046583.42) ELSE 0 END),
  'Ajuste cierre importacion 2025',1
FROM tb_account a WHERE a.code = '63010201201' LIMIT 1;

-- Compute totals and insert balancing entry to 9999
SET @totdeb = (SELECT COALESCE(SUM(debit),0) FROM tb_journal_entry WHERE journal_id = @journal_id);
SET @totcre = (SELECT COALESCE(SUM(credit),0) FROM tb_journal_entry WHERE journal_id = @journal_id);
SET @diff = ROUND(@totdeb - @totcre,2);

SELECT @totdeb AS total_debit, @totcre AS total_credit, @diff AS diff;

SELECT id INTO @adjust_id FROM tb_account WHERE code = '9999' LIMIT 1;

INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)
VALUES (@journal_id, @adjust_id, CASE WHEN @diff < 0 THEN -@diff ELSE 0 END, CASE WHEN @diff > 0 THEN @diff ELSE 0 END, 'Ajuste cierre importacion 2025',1);

-- Update journal totals
UPDATE tb_journal SET total_debit = (SELECT COALESCE(SUM(debit),0) FROM tb_journal_entry WHERE journal_id = @journal_id), total_credit = (SELECT COALESCE(SUM(credit),0) FROM tb_journal_entry WHERE journal_id = @journal_id) WHERE id = @journal_id;

COMMIT;

-- End of script
