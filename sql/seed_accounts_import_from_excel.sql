-- seed_accounts_import_from_excel.sql
-- Importar cuentas desde un CSV exportado desde tu Excel.
-- Ajusta @csv_path si el archivo está en otra ubicación.
-- El CSV debe usar punto y coma (;) como separador y tener encabezado.
-- Ejecución recomendada desde PowerShell/terminal:
--   mysql --local-infile=1 -u <user> -p <database> < sql/seed_accounts_import_from_excel.sql

SET @csv_path = 'C:/xampp/htdocs/servicredit/uploads/cuentas contables.csv';

-- 1) Crear tabla temporal que corresponde a las columnas que mostraste en el Excel
DROP TABLE IF EXISTS tmp_accounts_import_excel;
CREATE TABLE tmp_accounts_import_excel (
  CUENTACREDIBLAMEN VARCHAR(128),
  NOMBRECUENTA VARCHAR(512),
  CUENTAMUC VARCHAR(128),
  NOMBREDECUENTAMUC VARCHAR(512),
  COMENTARIOS VARCHAR(512)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Build LOAD DATA statement safely using CHAR() to represent special characters
SET @s = CONCAT(
  'LOAD DATA LOCAL INFILE ''', @csv_path, ''' ',
  'INTO TABLE tmp_accounts_import_excel ',
  'CHARACTER SET utf8mb4 ',
  'FIELDS TERMINATED BY '';'' ',
  'OPTIONALLY ENCLOSED BY ', CHAR(34), ' ',
  'LINES TERMINATED BY ', CHAR(10), ' ',
  'IGNORE 1 LINES ',
  '(CUENTACREDIBLAMEN, NOMBRECUENTA, CUENTAMUC, NOMBREDECUENTAMUC, COMENTARIOS)'
);
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3) Normalizar y mapear columnas a (code, name, estructura)
-- Determinaremos 'estructura' por el primer dígito del código principal (CUENTACREDIBLAMEN):
-- 1 -> Activo, 2 -> Pasivo, 3 -> Patrimonio, 4 -> Ingreso, 5 -> Gasto, otherwise 'otro'

DROP TABLE IF EXISTS tmp_accounts_mapped;
CREATE TABLE tmp_accounts_mapped AS
SELECT
  TRIM(CUENTACREDIBLAMEN) AS code,
  TRIM(NOMBRECUENTA) AS name,
  CASE
    WHEN LEFT(TRIM(CUENTACREDIBLAMEN),1) = '1' THEN 'activo'
    WHEN LEFT(TRIM(CUENTACREDIBLAMEN),1) = '2' THEN 'pasivo'
    WHEN LEFT(TRIM(CUENTACREDIBLAMEN),1) = '3' THEN 'patrimonio'
    WHEN LEFT(TRIM(CUENTACREDIBLAMEN),1) = '4' THEN 'ingreso'
    WHEN LEFT(TRIM(CUENTACREDIBLAMEN),1) = '5' THEN 'gasto'
    ELSE 'otro'
  END AS estructura,
  NULL AS nivel,
  NULL AS naturaleza
FROM tmp_accounts_import_excel;

-- 4) Insertar filas nuevas en tb_account (si no existen por code)
INSERT INTO tb_account (`code`, `name`, `type`, `created_at`)
SELECT t.code, t.name, t.estructura, NOW()
FROM tmp_accounts_mapped t
LEFT JOIN tb_account a ON a.code = t.code
WHERE a.id IS NULL;

-- 5) Actualizar nombre/tipo en cuentas existentes si difieren
UPDATE tb_account a
JOIN tmp_accounts_mapped t ON a.code = t.code
SET a.name = t.name,
    a.type = t.estructura
WHERE (a.name <> t.name OR (a.type IS NOT NULL AND a.type <> t.estructura) OR (a.type IS NULL AND TRIM(t.estructura) <> ''));

-- 6) (Opcional) Derivar parent_id por prefijo de código.
-- Esto intenta encontrar el padre cuyo código es prefijo más largo.
-- Actívalo solo si tus códigos usan prefijos jerárquicos (ej: 1, 1.1, 1.1.1 o 1000,1100,1110)

-- Ejemplo simple: para códigos numéricos sin puntos, buscar el parent por longitud menor que sea prefijo.
-- Si tus códigos usan puntos, puedes adaptar la lógica.

-- Uncomment block below to run parent derivation
/*
UPDATE tb_account a
JOIN (
  SELECT c.code AS child_code,
         (
           SELECT p.id FROM tb_account p
           WHERE LENGTH(p.code) < LENGTH(c.code)
             AND c.code LIKE CONCAT(p.code, '%')
           ORDER BY LENGTH(p.code) DESC
           LIMIT 1
         ) AS parent_id
  FROM tmp_accounts_mapped c
) mapping ON mapping.child_code = a.code
SET a.parent_id = mapping.parent_id
WHERE mapping.parent_id IS NOT NULL;
*/

-- 7) Limpieza
DROP TABLE IF EXISTS tmp_accounts_import_excel;
DROP TABLE IF EXISTS tmp_accounts_mapped;

SELECT CONCAT('Import finished. Source: ', @csv_path) AS message;
