-- Agrega columnas para edición manual de días de mora y monto de mora
ALTER TABLE `tb_prestamo_cuotas`
  ADD COLUMN `dias_mora_manual` INT NULL DEFAULT NULL AFTER `dias_mora_raw`,
  ADD COLUMN `monto_mora` DECIMAL(14,2) NULL DEFAULT NULL AFTER `dias_mora_manual`;
