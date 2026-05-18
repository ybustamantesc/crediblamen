-- Agrega el campo total_gastos_vivienda en analisis financiero comerciante (si no existe)
ALTER TABLE `tb_analisis_financiero_comerciante`
  ADD COLUMN IF NOT EXISTS `total_gastos_vivienda` DECIMAL(14,2) NULL AFTER `casa_propia`;
