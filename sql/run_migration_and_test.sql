-- Script combinado: agrega columnas a tb_solicitudes (si no existen) y ejecuta
-- el script de prueba que copia un cliente a tb_clientes_rechazados y marca el
-- original como rechazado.
-- IMPORTANTE: Hacer backup antes de ejecutar en producción.

-- Backup recomendado (fuera de este script):
-- mysqldump -u <usuario> -p <basededatos> tb_solicitudes > backup_tb_solicitudes.sql

-- Empieza transacción
START TRANSACTION;

-- Función: para cada columna creamos y ejecutamos un ALTER TABLE solo si falta

-- 1) giro_negocio
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'giro_negocio') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `giro_negocio` VARCHAR(255) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2) monto_solicitado
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'monto_solicitado') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `monto_solicitado` DECIMAL(14,2) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3) plazo_meses
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'plazo_meses') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `plazo_meses` INT DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 4) frecuencia
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'frecuencia') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `frecuencia` VARCHAR(50) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 5) tasa_interes
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'tasa_interes') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `tasa_interes` DECIMAL(6,2) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 6) cuota_estim_estimada
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'cuota_estim_estimada') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `cuota_estim_estimada` DECIMAL(14,2) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 7) garantia
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'garantia') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `garantia` VARCHAR(255) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 8) otros_ingresos_detalle
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'otros_ingresos_detalle') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `otros_ingresos_detalle` TEXT DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 9) ventas_promedio_diarios
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'ventas_promedio_diarios') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `ventas_promedio_diarios` DECIMAL(14,2) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 10) ventas_promedio_mensual
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'ventas_promedio_mensual') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `ventas_promedio_mensual` DECIMAL(14,2) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 11) detalle_inventario
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'detalle_inventario') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `detalle_inventario` TEXT DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 12) cuentas_por_cobrar_amount
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'cuentas_por_cobrar_amount') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `cuentas_por_cobrar_amount` DECIMAL(14,2) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 13) caja_amount
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'caja_amount') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `caja_amount` DECIMAL(14,2) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 14) banco_amount
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'banco_amount') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `banco_amount` DECIMAL(14,2) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 15) pago_alquiler
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'pago_alquiler') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `pago_alquiler` DECIMAL(14,2) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 16) pago_trabajadores
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'pago_trabajadores') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `pago_trabajadores` DECIMAL(14,2) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 17) energia
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'energia') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `energia` DECIMAL(14,2) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 18) agua
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'agua') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `agua` DECIMAL(14,2) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 19) internet
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'internet') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `internet` DECIMAL(14,2) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 20) promotor
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'promotor') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `promotor` VARCHAR(100) DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 21) fecha_recepcion
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'fecha_recepcion') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `fecha_recepcion` DATE DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 22) observaciones
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitudes' AND COLUMN_NAME = 'observaciones') = 0,
  'ALTER TABLE `tb_solicitudes` ADD COLUMN `observaciones` TEXT DEFAULT NULL',
  'SELECT "column exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

COMMIT;

-- ==========================
-- Ahora el script de prueba (test_insert_rechazado.sql)
-- ==========================

-- Nota: este bloque modificará datos (marcará un cliente como rechazado). Úsalo solo en pruebas.

-- Asegurar que la tabla tb_clientes_rechazados tenga las columnas necesarias
SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_clientes_rechazados' AND COLUMN_NAME = 'idcliente_original') = 0,
  'ALTER TABLE `tb_clientes_rechazados` ADD COLUMN `idcliente_original` INT DEFAULT NULL',
  'SELECT "col_exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_clientes_rechazados' AND COLUMN_NAME = 'rechazo_motivo') = 0,
  'ALTER TABLE `tb_clientes_rechazados` ADD COLUMN `rechazo_motivo` TEXT DEFAULT NULL',
  'SELECT "col_exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_clientes_rechazados' AND COLUMN_NAME = 'rechazado_por') = 0,
  'ALTER TABLE `tb_clientes_rechazados` ADD COLUMN `rechazado_por` INT DEFAULT NULL',
  'SELECT "col_exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_clientes_rechazados' AND COLUMN_NAME = 'rechazado_en') = 0,
  'ALTER TABLE `tb_clientes_rechazados` ADD COLUMN `rechazado_en` DATETIME DEFAULT NULL',
  'SELECT "col_exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_clientes_rechazados' AND COLUMN_NAME = 'restaurado_comentario') = 0,
  'ALTER TABLE `tb_clientes_rechazados` ADD COLUMN `restaurado_comentario` TEXT DEFAULT NULL',
  'SELECT "col_exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_clientes_rechazados' AND COLUMN_NAME = 'restaurado_por') = 0,
  'ALTER TABLE `tb_clientes_rechazados` ADD COLUMN `restaurado_por` INT DEFAULT NULL',
  'SELECT "col_exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_clientes_rechazados' AND COLUMN_NAME = 'restaurado_en') = 0,
  'ALTER TABLE `tb_clientes_rechazados` ADD COLUMN `restaurado_en` DATETIME DEFAULT NULL',
  'SELECT "col_exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;


SET @cid := (SELECT idcliente FROM tb_clientes WHERE (rechazado IS NULL OR rechazado = 0) LIMIT 1);

-- Si no se encontró cliente, la siguiente inserción no hará nada
INSERT INTO tb_clientes_rechazados (idcliente_original, apellidos, nombres, direccion, telefono, tipo_doc, numero_doc, comentarios, rechazo_motivo, rechazado_por, rechazado_en)
SELECT idcliente, apellidos, nombres, direccion, telefono, tipo_doc, numero_doc, comentarios, 'Prueba automática', 1, NOW()
FROM tb_clientes WHERE idcliente = @cid;

UPDATE tb_clientes SET rechazado = 1, estado = 0 WHERE idcliente = @cid;

SELECT @cid AS cliente_id;
SELECT * FROM tb_clientes_rechazados WHERE rechazado_en >= DATE_SUB(NOW(), INTERVAL 1 MINUTE) ORDER BY rechazado_en DESC LIMIT 5;

-- Fin del script combinado
