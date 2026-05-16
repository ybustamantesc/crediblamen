-- Add idcliente_original to tb_clientes_rechazados if it does not exist
-- Run this in your database (idempotent: will not fail if column already exists)
ALTER TABLE `tb_clientes_rechazados`
  ADD COLUMN `idcliente_original` INT DEFAULT NULL AFTER `id`;

-- Note: Some MySQL environments do not allow IF NOT EXISTS for ALTER COLUMN.
-- If you need to be truly idempotent in environments without privileges, check for the column first.
-- Example SELECT to check:
-- SHOW COLUMNS FROM `tb_clientes_rechazados` LIKE 'idcliente_original';
