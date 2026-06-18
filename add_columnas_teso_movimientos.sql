-- Script para agregar columnas faltantes a teso_movimientos
-- Este script es seguro y no falla si las columnas ya existen

ALTER TABLE `teso_movimientos` 
ADD COLUMN IF NOT EXISTS `usuario_id` INT NULL AFTER `creado_por`,
ADD COLUMN IF NOT EXISTS `conciliado` TINYINT(1) NOT NULL DEFAULT 0 AFTER `contabilizado`,
ADD COLUMN IF NOT EXISTS `moneda` VARCHAR(10) DEFAULT 'NIO' AFTER `tipo`,
ADD COLUMN IF NOT EXISTS `tc_aplicada` DECIMAL(10,4) NULL AFTER `moneda`,
ADD COLUMN IF NOT EXISTS `monto_nio` DECIMAL(18,2) NULL AFTER `tc_aplicada`,
ADD COLUMN IF NOT EXISTS `monto_usd` DECIMAL(18,2) NULL AFTER `monto_nio`,
ADD COLUMN IF NOT EXISTS `observaciones` TEXT NULL AFTER `monto_usd`,
ADD COLUMN IF NOT EXISTS `idserie` INT NULL AFTER `observaciones`;

-- Agregar índices para mejorar búsquedas
ALTER TABLE `teso_movimientos` 
ADD INDEX IF NOT EXISTS `idx_usuario_id` (`usuario_id`),
ADD INDEX IF NOT EXISTS `idx_concepto` (`concepto`),
ADD INDEX IF NOT EXISTS `idx_cuenta_id` (`cuenta_id`),
ADD INDEX IF NOT EXISTS `idx_idserie` (`idserie`),
ADD INDEX IF NOT EXISTS `idx_moneda` (`moneda`);

-- Verificar estructura
DESC `teso_movimientos`;
