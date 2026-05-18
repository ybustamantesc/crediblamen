-- Add columns to track restorations on tb_clientes_rechazados
ALTER TABLE `tb_clientes_rechazados`
  ADD COLUMN IF NOT EXISTS `restaurado_comentario` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `restaurado_por` INT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `restaurado_en` DATETIME DEFAULT NULL;
