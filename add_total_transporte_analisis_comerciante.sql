-- Agrega el campo total_transporte en analisis financiero comerciante (si no existe)
ALTER TABLE `tb_analisis_financiero_comerciante`
  ADD COLUMN IF NOT EXISTS `total_transporte` DECIMAL(14,2) NULL AFTER `vehiculo_particular`;
