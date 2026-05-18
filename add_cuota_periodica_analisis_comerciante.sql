-- Agrega columnas para cuota periódica en análisis financiero comerciante (si no existen)
ALTER TABLE `tb_analisis_financiero_comerciante`
  ADD COLUMN IF NOT EXISTS `cuota_periodica` DECIMAL(14,2) NULL AFTER `flujo_neto_disponible`;

ALTER TABLE `tb_analisis_financiero_comerciante`
  ADD COLUMN IF NOT EXISTS `cuota_periodica_estim` DECIMAL(14,2) NULL AFTER `cuota_periodica`;
