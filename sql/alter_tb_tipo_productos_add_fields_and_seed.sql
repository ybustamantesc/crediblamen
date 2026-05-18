-- Add detailed product fields and seed default product types
-- Idempotent-ish: checks for column existence before altering, and inserts only if name not exists.

-- Add columns if not present. Use PREPARE/EXECUTE to avoid top-level IF/THEN (works in mysql CLI)
SET @tbl := 'tb_tipo_productos';

-- monto_min
SET @exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tbl AND COLUMN_NAME = 'monto_min');
SET @sql := IF(@exists = 0, 'ALTER TABLE tb_tipo_productos ADD COLUMN monto_min DECIMAL(12,2) DEFAULT NULL;', 'SELECT "column_exists_monto_min";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- monto_max
SET @exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tbl AND COLUMN_NAME = 'monto_max');
SET @sql := IF(@exists = 0, 'ALTER TABLE tb_tipo_productos ADD COLUMN monto_max DECIMAL(12,2) DEFAULT NULL;', 'SELECT "column_exists_monto_max";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tasa_mensual
SET @exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tbl AND COLUMN_NAME = 'tasa_mensual');
SET @sql := IF(@exists = 0, 'ALTER TABLE tb_tipo_productos ADD COLUMN tasa_mensual DECIMAL(7,4) DEFAULT NULL;', 'SELECT "column_exists_tasa_mensual";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- comision_desembolso
SET @exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tbl AND COLUMN_NAME = 'comision_desembolso');
SET @sql := IF(@exists = 0, 'ALTER TABLE tb_tipo_productos ADD COLUMN comision_desembolso DECIMAL(7,4) DEFAULT NULL;', 'SELECT "column_exists_comision_desembolso";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- plazo_min
SET @exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tbl AND COLUMN_NAME = 'plazo_min');
SET @sql := IF(@exists = 0, 'ALTER TABLE tb_tipo_productos ADD COLUMN plazo_min INT DEFAULT NULL;', 'SELECT "column_exists_plazo_min";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- plazo_max
SET @exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tbl AND COLUMN_NAME = 'plazo_max');
SET @sql := IF(@exists = 0, 'ALTER TABLE tb_tipo_productos ADD COLUMN plazo_max INT DEFAULT NULL;', 'SELECT "column_exists_plazo_max";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- clasificacion
SET @exists := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tbl AND COLUMN_NAME = 'clasificacion');
SET @sql := IF(@exists = 0, 'ALTER TABLE tb_tipo_productos ADD COLUMN clasificacion VARCHAR(100) DEFAULT NULL;', 'SELECT "column_exists_clasificacion";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Seed data: only insert when no row with same nombre exists
INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Micronegocio 300-499' AS nombre, 0.08 AS porcentaje, 300.00 AS monto_min, 499.00 AS monto_max, 0.08 AS tasa_mensual, 0.07 AS comision_desembolso, 6 AS plazo_min, 12 AS plazo_max, 'Negocios' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Micronegocio 300-499');

INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Micronegocio 500-999' AS nombre, 0.06 AS porcentaje, 500.00 AS monto_min, 999.00 AS monto_max, 0.06 AS tasa_mensual, 0.07 AS comision_desembolso, 6 AS plazo_min, 12 AS plazo_max, 'Negocios' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Micronegocio 500-999');

INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Pequeño negocio 1 1000-1499' AS nombre, 0.06 AS porcentaje, 1000.00 AS monto_min, 1499.00 AS monto_max, 0.06 AS tasa_mensual, 0.07 AS comision_desembolso, 10 AS plazo_min, 18 AS plazo_max, 'Negocios' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Pequeño negocio 1 1000-1499');

INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Pequeño Negocio 2 1500-4999' AS nombre, 0.06 AS porcentaje, 1500.00 AS monto_min, 4999.00 AS monto_max, 0.06 AS tasa_mensual, 0.07 AS comision_desembolso, 12 AS plazo_min, 24 AS plazo_max, 'Negocios' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Pequeño Negocio 2 1500-4999');

INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Pequeño Negocio 3 5000-9999' AS nombre, 0.05 AS porcentaje, 5000.00 AS monto_min, 9999.00 AS monto_max, 0.05 AS tasa_mensual, 0.05 AS comision_desembolso, 12 AS plazo_min, 36 AS plazo_max, 'Negocios' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Pequeño Negocio 3 5000-9999');

INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Pequeña Industria 10000-19999' AS nombre, 0.04 AS porcentaje, 10000.00 AS monto_min, 19999.00 AS monto_max, 0.04 AS tasa_mensual, 0.05 AS comision_desembolso, 24 AS plazo_min, 48 AS plazo_max, 'Negocios' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Pequeña Industria 10000-19999');

INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Pequeña Industria 20000-25000' AS nombre, 0.04 AS porcentaje, 20000.00 AS monto_min, 25000.00 AS monto_max, 0.04 AS tasa_mensual, 0.05 AS comision_desembolso, 24 AS plazo_min, 48 AS plazo_max, 'Negocios' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Pequeña Industria 20000-25000');

-- Seed: Personal (Personas)
INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Personal 300-499' AS nombre, 0.08 AS porcentaje, 300.00 AS monto_min, 499.00 AS monto_max, 0.08 AS tasa_mensual, 0.07 AS comision_desembolso, 4 AS plazo_min, 8 AS plazo_max, 'Personal' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Personal 300-499');

INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Personal 500-999' AS nombre, 0.06 AS porcentaje, 500.00 AS monto_min, 999.00 AS monto_max, 0.06 AS tasa_mensual, 0.07 AS comision_desembolso, 6 AS plazo_min, 12 AS plazo_max, 'Personal' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Personal 500-999');

INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Personal 1000-1499' AS nombre, 0.06 AS porcentaje, 1000.00 AS monto_min, 1499.00 AS monto_max, 0.06 AS tasa_mensual, 0.07 AS comision_desembolso, 10 AS plazo_min, 18 AS plazo_max, 'Personal' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Personal 1000-1499');

-- Seed: Viviendo o Hipotecario
INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Compra de lote 3000-4999' AS nombre, 0.06 AS porcentaje, 3000.00 AS monto_min, 4999.00 AS monto_max, 0.06 AS tasa_mensual, 0.07 AS comision_desembolso, 12 AS plazo_min, 36 AS plazo_max, 'Viviendo o Hipotecario' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Compra de lote 3000-4999');

INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Compra de lote 5000-7999' AS nombre, 0.05 AS porcentaje, 5000.00 AS monto_min, 7999.00 AS monto_max, 0.05 AS tasa_mensual, 0.05 AS comision_desembolso, 24 AS plazo_min, 36 AS plazo_max, 'Viviendo o Hipotecario' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Compra de lote 5000-7999');

INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Compra de lote 8000-10000' AS nombre, 0.05 AS porcentaje, 8000.00 AS monto_min, 10000.00 AS monto_max, 0.05 AS tasa_mensual, 0.05 AS comision_desembolso, 24 AS plazo_min, 36 AS plazo_max, 'Viviendo o Hipotecario' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Compra de lote 8000-10000');

INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Mejora de vivienda 300-499' AS nombre, 0.06 AS porcentaje, 300.00 AS monto_min, 499.00 AS monto_max, 0.06 AS tasa_mensual, 0.07 AS comision_desembolso, 6 AS plazo_min, 12 AS plazo_max, 'Viviendo o Hipotecario' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Mejora de vivienda 300-499');

INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Mejora de vivienda 500-999' AS nombre, 0.06 AS porcentaje, 500.00 AS monto_min, 999.00 AS monto_max, 0.06 AS tasa_mensual, 0.07 AS comision_desembolso, 6 AS plazo_min, 12 AS plazo_max, 'Viviendo o Hipotecario' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Mejora de vivienda 500-999');

INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Mejora de vivienda 1000-1499' AS nombre, 0.06 AS porcentaje, 1000.00 AS monto_min, 1499.00 AS monto_max, 0.06 AS tasa_mensual, 0.07 AS comision_desembolso, 8 AS plazo_min, 24 AS plazo_max, 'Viviendo o Hipotecario' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Mejora de vivienda 1000-1499');

INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Mejora de vivienda 1500-3000' AS nombre, 0.06 AS porcentaje, 1500.00 AS monto_min, 3000.00 AS monto_max, 0.06 AS tasa_mensual, 0.07 AS comision_desembolso, 12 AS plazo_min, 24 AS plazo_max, 'Viviendo o Hipotecario' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Mejora de vivienda 1500-3000');

-- Seed: Vehiculos Usados
INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Vehiculo usado 2000-2999' AS nombre, 0.06 AS porcentaje, 2000.00 AS monto_min, 2999.00 AS monto_max, 0.06 AS tasa_mensual, 0.07 AS comision_desembolso, 8 AS plazo_min, 18 AS plazo_max, 'Vehiculos Usados' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Vehiculo usado 2000-2999');

INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Vehiculo usado 3000-4999' AS nombre, 0.06 AS porcentaje, 3000.00 AS monto_min, 4999.00 AS monto_max, 0.06 AS tasa_mensual, 0.07 AS comision_desembolso, 12 AS plazo_min, 24 AS plazo_max, 'Vehiculos Usados' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Vehiculo usado 3000-4999');

INSERT INTO tb_tipo_productos (nombre, porcentaje, monto_min, monto_max, tasa_mensual, comision_desembolso, plazo_min, plazo_max, clasificacion)
SELECT * FROM (
  SELECT 'Vehiculo usado 5000-9999' AS nombre, 0.05 AS porcentaje, 5000.00 AS monto_min, 9999.00 AS monto_max, 0.05 AS tasa_mensual, 0.05 AS comision_desembolso, 12 AS plazo_min, 36 AS plazo_max, 'Vehiculos Usados' AS clasificacion
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tb_tipo_productos WHERE nombre = 'Vehiculo usado 5000-9999');
-- End of script
