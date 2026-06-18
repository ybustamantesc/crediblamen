-- Agrega los campos faltantes de Recomendación de Crédito a tb_analisis_financiero_asalariado
ALTER TABLE `tb_analisis_financiero_asalariado`
  ADD COLUMN IF NOT EXISTS `forma_pago` VARCHAR(100) NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `garantia_requerida` TEXT NULL,
  ADD COLUMN IF NOT EXISTS `fundamentacion_propuesta` TEXT NULL;
