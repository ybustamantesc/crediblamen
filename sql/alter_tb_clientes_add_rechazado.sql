-- Add a 'rechazado' flag to the main clients table to mark moved/rejected records
ALTER TABLE `tb_clientes`
  ADD COLUMN IF NOT EXISTS `rechazado` TINYINT(1) NOT NULL DEFAULT 0 AFTER `estado`;
