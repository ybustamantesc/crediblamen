-- CLEAR CONTABILIDAD (PREPARE + TRUNCATE)
-- Database: minitas
-- INSTRUCTIONS:
-- 1) First run the "REPORT" section to see row counts and confirm.
-- 2) Then, after verifying, run the "TRUNCATE" section to remove accounting accounts and journals.
-- NOTE: This script will only act on tables that exist in the database named 'minitas'.

-- =====================
-- REPORT - Run first
-- =====================
USE `minitas`;

SELECT table_name, table_rows
FROM information_schema.tables
WHERE table_schema = 'minitas'
  AND table_name IN (
    'tb_journal_entry',
    'tb_journal',
    'tb_account',
    'b_account',
    'teso_accounts',
    'tb_centro_costo'
  );

-- =====================
-- TRUNCATE - Run AFTER you confirm the report
-- =====================
-- Remove the leading comment block (/* ... */) below only after you reviewed the report above.

/*
SET FOREIGN_KEY_CHECKS = 0;

SELECT GROUP_CONCAT(CONCAT('TRUNCATE TABLE `', table_schema, '`.`', table_name, '`;') SEPARATOR ' ') INTO @stmts
FROM information_schema.tables
WHERE table_schema = 'minitas'
  AND table_name IN (
    'tb_journal_entry',
    'tb_journal',
    'tb_account',
    'b_account',
    'teso_accounts',
    'tb_centro_costo'
  );

-- If no matching tables found, @stmts will be NULL and we skip execution
SET @stmts = IFNULL(@stmts, 'SELECT "-- no tables to truncate --";');
PREPARE st FROM @stmts;
EXECUTE st;
DEALLOCATE PREPARE st;

SET FOREIGN_KEY_CHECKS = 1;
*/

-- End of script
