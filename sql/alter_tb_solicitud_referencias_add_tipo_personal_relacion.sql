-- Add column to store the "tipo_personal_relacion" (e.g. Vecino, Compañero, Amigo)
-- This file is idempotent on MySQL >= 8.0.16 using IF NOT EXISTS.
-- If your MySQL version is older, run the SELECT against INFORMATION_SCHEMA first and
-- only run the ALTER TABLE when the column does not exist.

ALTER TABLE `tb_solicitud_referencias`
  ADD COLUMN IF NOT EXISTS `tipo_personal_relacion` VARCHAR(100) DEFAULT NULL;

-- Alternative (manual) steps for older MySQL servers:
-- 1) Check if column exists:
--    SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS
--      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_solicitud_referencias' AND COLUMN_NAME = 'tipo_personal_relacion';
-- 2) If cnt = 0 then run:
--    ALTER TABLE `tb_solicitud_referencias` ADD COLUMN `tipo_personal_relacion` VARCHAR(100) DEFAULT NULL;
--
-- Usage example from shell (Windows PowerShell):
--   & "C:\\xampp\\mysql\\bin\\mysql.exe" -u root -p -D your_database_name -e "ALTER TABLE tb_solicitud_referencias ADD COLUMN IF NOT EXISTS tipo_personal_relacion VARCHAR(100) DEFAULT NULL;"
