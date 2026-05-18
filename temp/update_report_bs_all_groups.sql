-- Update ALL report_bs groups in a single script
-- This script aggregates the previously created group updates into one file.
-- It creates a single backup table, shows previews per group, runs updates inside a transaction,
-- and shows post-update verification. Restore statements are included and commented.
-- Created: 2026-01-24

-- BACKUP: capture current report_bs for all accounts that will be modified
DROP TABLE IF EXISTS backup_tb_account_report_bs_pre_all_groups;
CREATE TABLE backup_tb_account_report_bs_pre_all_groups AS
SELECT id, code, name, report_bs
FROM tb_account
WHERE
      code LIKE '1101%'
   OR code LIKE '1102%'
   OR code IN (
     '14010101101','14010101201','14010101301','14010201301','14010301301','14030101301',
     '14040101101','14040201301','14040301301','14050101101',
     '14060101101','14060101301','14060102301','14060103301','14060301301','14060401301',
     '14080101101','14080101301'
   )
   OR code IN ('15010101101','15010901201','15010101301','15010901301')
   OR code IN (
     '16020101101','16020101201','16020101202','16020101203',
     '16020601101','16020601201','16020901201'
   )
   OR code LIKE '180%'
   OR code LIKE '190%'
   OR code LIKE '220%'
   OR code LIKE '260%'
   OR code LIKE '270%'
   OR code LIKE '290%'
   OR code LIKE '280%'
   OR code LIKE '310%'
   OR code LIKE '320%'
   OR code LIKE '390%'
   OR code LIKE '4101%'
   OR code LIKE '4106%'
   OR code LIKE '4107%'
   OR code LIKE '4108%'
   OR code LIKE '4109%'
   OR code LIKE '4110%'
   OR code LIKE '454501%'
   OR code LIKE '4501%'
  OR code LIKE '5201%'
  OR code LIKE '5202%'
  OR code LIKE '4201%'
  OR code LIKE '4203%'
  OR code LIKE '5701%'
  OR code LIKE '540%'
  OR code LIKE '5511%'
  OR code LIKE '5601%'
   OR code LIKE '4113%'
   OR code LIKE '5103%'
   OR code LIKE '5104%'
   OR code = '51040901201'
   OR code LIKE '5105%';

SELECT COUNT(*) AS backed_up_rows FROM backup_tb_account_report_bs_pre_all_groups;

-- PREVIEWS: show accounts per logical group so you can verify before applying updates
-- Fondos disponibles
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '1101%' OR code LIKE '1102%' ORDER BY code;

-- Cartera de créditos (explicit list)
SELECT id, code, name, report_bs FROM tb_account WHERE code IN (
  '14010101101','14010101201','14010101301','14010201301','14010301301','14030101301',
  '14040101101','14040201301','14040301301','14050101101',
  '14060101101','14060101301','14060102301','14060103301','14060301301','14060401301',
  '14080101101','14080101301'
) ORDER BY code;

-- Bienes recibidos
SELECT id, code, name, report_bs FROM tb_account WHERE code IN ('15010101101','15010901201','15010101301','15010901301') ORDER BY code;

-- Otras cuentas por cobrar
SELECT id, code, name, report_bs FROM tb_account WHERE code IN ('16020101101','16020101201','16020101202','16020101203','16020601101','16020601201','16020901201') ORDER BY code;

-- Inmuebles, mobiliario y equipo
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '180%' ORDER BY code;

-- Otros activos
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '190%' ORDER BY code;

-- Obligaciones (varios tipos)
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '220%' ORDER BY code;

-- Otras cuentas por pagar
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '260%' ORDER BY code;

-- Provisiones
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '270%' ORDER BY code;

-- Otros pasivos
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '290%' ORDER BY code;

-- Deuda subordinada
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '280%' ORDER BY code;

-- Capital social / Capital adicional
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '310%' OR code LIKE '320%' ORDER BY code;

-- Resultados del ejercicio
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '390%' ORDER BY code;

-- Disponibilidades y Cartera (prefijos 410x)
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '4101%' OR code LIKE '4106%' OR code LIKE '4107%' OR code LIKE '4108%' OR code LIKE '4109%' OR code LIKE '4110%' ORDER BY code;

-- Diferencia cambiaria
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '454501%' OR code LIKE '4501%' OR code LIKE '5501%' OR code LIKE '555501%' ORDER BY code;

-- Otros ingresos financieros
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '4113%' ORDER BY code;

-- Obligaciones y gastos financieros (510x)
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '5103%' OR code LIKE '5104%' OR code = '51040901201' OR code LIKE '5105%' OR code LIKE '5107%' OR code LIKE '5108%' ORDER BY code;

-- Gasto por provisión por incobrabilidad / Gastos por saneamiento (520x)
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '5201%' OR code LIKE '5202%' ORDER BY code;

-- Ingresos por recuperación / disminución de provisión (420x)
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '4201%' OR code LIKE '4203%' ORDER BY code;

-- Pérdidas en asociadas (5701)
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '5701%' ORDER BY code;

-- Gastos de administración y otros (540x)
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '540%' ORDER BY code;

-- Otros gastos financieros (5511)
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '5511%' ORDER BY code;

-- Aportes y contribuciones (5601)
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '5601%' OR code LIKE '560101%' ORDER BY code;

-- Impuesto a la renta (6201)
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '6201%' OR code IN ('62010101201','62010201201') ORDER BY code;

-- Cuentas saneadas (820x)
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '8203%' OR code LIKE '8204%' OR code LIKE '8205%' ORDER BY code;

-- Contracuentas (8604 / 8605)
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '8604%' OR code LIKE '8605%' ORDER BY code;

-- When previews look correct, run the UPDATE block below. Keep a DB backup and/or run on staging first.
START TRANSACTION;

-- Apply grouped report_bs values
UPDATE tb_account SET report_bs = 'Fondos disponibles' WHERE code LIKE '1101%' OR code LIKE '1102%';

UPDATE tb_account SET report_bs = 'Cartera de créditos, neto de provisiones por incobrabilidad' WHERE code IN (
  '14010101101','14010101201','14010101301','14010201301','14010301301','14030101301',
  '14040101101','14040201301','14040301301','14050101101',
  '14060101101','14060101301','14060102301','14060103301','14060301301','14060401301',
  '14080101101','14080101301'
);

UPDATE tb_account SET report_bs = 'Bienes recibidos en pago y adjudicados, neto' WHERE code IN ('15010101101','15010901201','15010101301','15010901301');

UPDATE tb_account SET report_bs = 'Otras cuentas por cobrar, neto' WHERE code IN ('16020101101','16020101201','16020101202','16020101203','16020601101','16020601201','16020901201');

UPDATE tb_account SET report_bs = 'Inmuebles, mobiliario y equipo, neto' WHERE code LIKE '180%';

UPDATE tb_account SET report_bs = 'Otros activos, neto' WHERE code LIKE '190%';

UPDATE tb_account SET report_bs = 'Obligaciones con instituciones financieras y otros financiamientos, neto' WHERE code LIKE '220%';

UPDATE tb_account SET report_bs = 'Otras cuentas por pagar, neto' WHERE code LIKE '260%';

UPDATE tb_account SET report_bs = 'Provisiones, neto' WHERE code LIKE '270%';

UPDATE tb_account SET report_bs = 'Otros pasivos, neto' WHERE code LIKE '290%';

UPDATE tb_account SET report_bs = 'Deuda subordinada y obligaciones convertibles, neto' WHERE code LIKE '280%';

UPDATE tb_account SET report_bs = 'Capital social / Aportes, neto' WHERE code LIKE '310%';

UPDATE tb_account SET report_bs = 'Capital adicional / Aporte adicional, neto' WHERE code LIKE '320%';

UPDATE tb_account SET report_bs = 'Resultados del ejercicio' WHERE code LIKE '390%';

UPDATE tb_account SET report_bs = 'Disponibilidades' WHERE code LIKE '4101%';

UPDATE tb_account SET report_bs = 'Cartera de créditos' WHERE code LIKE '4106%' OR code LIKE '4107%' OR code LIKE '4108%' OR code LIKE '4109%' OR code LIKE '4110%';

UPDATE tb_account SET report_bs = 'Diferencia cambiaria' WHERE code LIKE '454501%' OR code LIKE '4501%' OR code LIKE '5501%' OR code LIKE '555501%';

UPDATE tb_account SET report_bs = 'Otros ingresos financieros' WHERE code LIKE '4113%';

UPDATE tb_account SET report_bs = 'Obligaciones con instituciones financieras y otros financiamientos' WHERE code LIKE '5103%' OR code LIKE '5104%' OR code = '51040901201' OR code LIKE '5105%';

UPDATE tb_account SET report_bs = 'Deuda subordinada y obligaciones convertibles' WHERE code LIKE '5107%' OR code LIKE '5108%';

-- 520x: provisiones y saneamiento
UPDATE tb_account SET report_bs = 'Gasto por provisión por incobrabilidad de la cartera de créditos' WHERE code LIKE '5201%';
UPDATE tb_account SET report_bs = 'Gastos por saneamiento de ingresos financieros' WHERE code LIKE '5202%';

-- 420x: ingresos por recuperaciones / disminución de provisión
UPDATE tb_account SET report_bs = 'Ingresos por recuperación de cartera (saneada)' WHERE code LIKE '4201%';
UPDATE tb_account SET report_bs = 'Ingresos por disminución de provisión' WHERE code LIKE '4203%';

-- 5701: Pérdidas en asociadas
UPDATE tb_account SET report_bs = 'Pérdidas en asociadas' WHERE code LIKE '5701%';

-- 540x: Gastos de administración y otros
UPDATE tb_account SET report_bs = 'Gastos de administración y otros' WHERE code LIKE '540%';

-- 5511: Otros gastos financieros
UPDATE tb_account SET report_bs = 'Otros gastos financieros' WHERE code LIKE '5511%';

-- 5601: Aportes y contribuciones
UPDATE tb_account SET report_bs = 'Aportes y otras contribuciones' WHERE code LIKE '5601%' OR code LIKE '560101%';

-- Impuesto a la renta (6201)
UPDATE tb_account SET report_bs = 'Gastos por impuesto sobre la renta' WHERE code LIKE '6201%' OR code IN ('62010101201','62010201201');

-- Cuentas saneadas (820x)
UPDATE tb_account SET report_bs = 'Cuentas saneadas' WHERE code LIKE '8203%' OR code LIKE '8204%' OR code LIKE '8205%';

-- Contracuentas relacionadas (8604, 8605)
UPDATE tb_account SET report_bs = 'Contracuenta de cuentas saneadas' WHERE code LIKE '8604%';
UPDATE tb_account SET report_bs = 'Contracuenta de ingresos en suspenso' WHERE code LIKE '8605%';

COMMIT;

-- POST-UPDATE: verify results for key groups
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '1101%' OR code LIKE '1102%' ORDER BY code;
SELECT id, code, name, report_bs FROM tb_account WHERE code IN (
  '14010101101','14010101201','14010101301','14010201301','14010301301','14030101301',
  '14040101101','14040201301','14040301301','14050101101',
  '14060101101','14060101301','14060102301','14060103301','14060301301','14060401301',
  '14080101101','14080101301'
) ORDER BY code;
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '180%' OR code LIKE '190%' ORDER BY code;
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '310%' OR code LIKE '320%' ORDER BY code;
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '390%' OR code LIKE '4101%' OR code LIKE '4106%' OR code LIKE '4113%' ORDER BY code;
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '5103%' OR code LIKE '5104%' OR code LIKE '5105%' OR code LIKE '5107%' OR code LIKE '5108%' ORDER BY code;

-- Verify impuesto a la renta
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '6201%' OR code IN ('62010101201','62010201201') ORDER BY code;

-- Verify cuentas saneadas
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '8203%' OR code LIKE '8204%' OR code LIKE '8205%' ORDER BY code;

-- Verify contracuentas 8604/8605
SELECT id, code, name, report_bs FROM tb_account WHERE code LIKE '8604%' OR code LIKE '8605%' ORDER BY code;

-- RESTORE (if needed): uncomment and run to restore original values
-- UPDATE tb_account t
-- JOIN backup_tb_account_report_bs_pre_all_groups b ON t.id = b.id
-- SET t.report_bs = b.report_bs;
