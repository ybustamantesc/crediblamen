-- seed_accounts_import.sql
-- Carga cuentas desde el CSV exportado a:
-- C:/xampp/htdocs/servicredit/uploads/cuentas contables.csv
-- Este script intenta usar LOAD DATA LOCAL INFILE. Ejecutar con:
--   mysql --local-infile=1 -u <user> -p <database> < sql/seed_accounts_import.sql

SET @csv_path = 'C:/xampp/htdocs/servicredit/uploads/cuentas contables.csv';

-- 1) Crear tabla temporal para importar el CSV (idempotente)
DROP TABLE IF EXISTS tmp_accounts_import;
CREATE TABLE tmp_accounts_import (
  code VARCHAR(64) PRIMARY KEY,
  name VARCHAR(512),
  estructura VARCHAR(128),
  nivel VARCHAR(64),
  naturaleza VARCHAR(64)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) Cargar CSV (asegúrate de ejecutar el cliente mysql con --local-infile=1)
-- El CSV usa punto y coma como separador y tiene encabezado en la primera fila.
-- Ajusta CHARACTER SET si tu archivo no está en UTF8.
SET @s = CONCAT("LOAD DATA LOCAL INFILE '", @csv_path, "' \
",
                 "INTO TABLE tmp_accounts_import \
",
                 "CHARACTER SET utf8mb4 \
",
                 "FIELDS TERMINATED BY ';' \
",
                 "OPTIONALLY ENCLOSED BY '"' \
",
                 "LINES TERMINATED BY '\n' \
",
                 "IGNORE 1 LINES \
",
                 "(code, name, estructura, nivel, naturaleza);");
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3) Insertar filas nuevas en tb_account (si no existen por code)
-- Normalizamos el tipo bajándolo a minúsculas (ej: Activo -> activo)
INSERT INTO tb_account (`code`, `name`, `type`, `created_at`)
SELECT t.code, t.name, LOWER(TRIM(t.estructura)), NOW()
FROM tmp_accounts_import t
LEFT JOIN tb_account a ON a.code = t.code
WHERE a.id IS NULL;

-- 4) Actualizar nombre/tipo en cuentas existentes si difieren
UPDATE tb_account a
JOIN tmp_accounts_import t ON a.code = t.code
SET a.name = t.name,
    a.type = LOWER(TRIM(t.estructura))
WHERE (a.name <> t.name OR (a.type IS NOT NULL AND a.type <> LOWER(TRIM(t.estructura))) OR (a.type IS NULL AND TRIM(t.estructura) <> ''));

-- 5) (Opcional) Si deseas que 'parent_id' se derive por prefijo, puedes activar
-- el bloque siguiente manualmente. Está comentado por defecto.
-- Idea: busca el padre más cercano cuyo código es prefijo y está inserto.
-- Nota: este método asume que los códigos parent aparecen antes o ya existen.

/*
-- Derivar parent_id por prefijo (descomentar para activar)
UPDATE tb_account a
LEFT JOIN (
  SELECT c.code AS child_code, p.id AS parent_id
  FROM tmp_accounts_import c
  JOIN tb_account p ON CHAR_LENGTH(p.code) < CHAR_LENGTH(c.code)
    AND c.code LIKE CONCAT(p.code, '%')
  WHERE p.code IS NOT NULL
  -- seleccionar el parent con máxima longitud (el prefijo más largo)
  GROUP BY c.code
  HAVING parent_id = (
    SELECT id FROM tb_account pp WHERE c.code LIKE CONCAT(pp.code, '%') ORDER BY CHAR_LENGTH(pp.code) DESC LIMIT 1
  )
) mapping ON mapping.child_code = a.code
SET a.parent_id = mapping.parent_id
WHERE mapping.parent_id IS NOT NULL;
*/

-- 6) Limpieza
DROP TABLE IF EXISTS tmp_accounts_import;

SELECT CONCAT('Seeding complete. Accounts inserted/updated from: ', @csv_path) AS message;
