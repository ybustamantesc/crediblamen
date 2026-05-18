-- CLEAR CONTABILIDAD WITH BACKUPS
-- Database: minitas
-- WARNING: This script will CREATE backup tables and TRUNCATE originals.
-- Run only on the server where the 'minitas' database is available and after taking other backups if needed.

USE `minitas`;

-- safety: compute timestamp for backup suffix
SET @ts = DATE_FORMAT(NOW(), '%Y%m%d_%H%i%s');

-- disable foreign key checks while truncating
SET @fk_off = 'SET FOREIGN_KEY_CHECKS = 0;';
SET @fk_on  = 'SET FOREIGN_KEY_CHECKS = 1;';

-- Build statements for existing contabilidad tables
SELECT GROUP_CONCAT(
  CONCAT(
    'CREATE TABLE `', table_name, '_backup_', @ts, '` LIKE `', table_name, '`; ',
    'INSERT INTO `', table_name, '_backup_', @ts, '` SELECT * FROM `', table_name, '`; ',
    'TRUNCATE TABLE `', table_name, '`;'
  )
  SEPARATOR ' ')
INTO @stmts
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

-- If no matching tables, set a harmless no-op
SET @stmts = IFNULL(@stmts, 'SELECT "-- no contabilidad tables found --";');

-- Combine with FK toggle
SET @full = CONCAT(@fk_off, ' ', @stmts, ' ', @fk_on);

-- Prepare and execute
PREPARE st FROM @full;
EXECUTE st;
DEALLOCATE PREPARE st;

-- Done
SELECT 'Done. Backups created with suffix _backup_YYYYmmdd_HHMMSS and originals truncated.' AS info;
