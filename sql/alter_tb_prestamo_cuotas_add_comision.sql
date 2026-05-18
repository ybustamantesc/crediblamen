-- Simple, compatible migration: try to add column.
-- If you run MySQL 8.0+ you can use the IF NOT EXISTS form directly.
-- Recommended (MySQL 8+):
ALTER TABLE `tb_prestamo_cuotas`
    ADD COLUMN IF NOT EXISTS `comision` DECIMAL(12,4) NOT NULL DEFAULT 0;

-- If your MySQL is older and does not support ADD COLUMN IF NOT EXISTS, run a plain ALTER.
-- If the column already exists the ALTER will fail with an error you can safely ignore.
-- Example (works in phpMyAdmin or mysql CLI):
-- ALTER TABLE `tb_prestamo_cuotas` ADD COLUMN `comision` DECIMAL(12,4) NOT NULL DEFAULT 0;

-- Note: previous attempts that query INFORMATION_SCHEMA may fail under some hosting/phpMyAdmin setups
-- with permission errors (access denied for user to information_schema). The statements above avoid that.
