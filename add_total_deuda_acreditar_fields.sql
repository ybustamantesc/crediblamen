-- Agrega campos para Total de Deuda a Creditar y Porcentaje de Deuda Total
-- Ejecutar una vez en la base de datos de Crediblamen

ALTER TABLE `tb_analisis_financiero_comerciante`
  ADD COLUMN IF NOT EXISTS `total_deuda_acreditar` DECIMAL(14,2) NULL DEFAULT 0.00,
  ADD COLUMN IF NOT EXISTS `porcentaje_deuda_total` DECIMAL(14,6) NULL DEFAULT 0.000000;

ALTER TABLE `tb_analisis_financiero_asalariado`
  ADD COLUMN IF NOT EXISTS `total_deuda_acreditar` DECIMAL(14,2) NULL DEFAULT 0.00,
  ADD COLUMN IF NOT EXISTS `porcentaje_deuda_total` DECIMAL(14,6) NULL DEFAULT 0.000000;
