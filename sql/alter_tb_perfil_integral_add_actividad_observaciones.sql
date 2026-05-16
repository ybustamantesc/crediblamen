-- Add new column to store additional observations for actividad_esperada
-- This column holds up to 5000 characters as requested.
-- Review and backup before running.

ALTER TABLE `tb_perfil_integral_cliente`
  ADD COLUMN `actividad_esperada_observaciones` VARCHAR(5000) DEFAULT NULL AFTER `actividad_esperada_json`;

-- Reversion:
-- ALTER TABLE `tb_perfil_integral_cliente` DROP COLUMN `actividad_esperada_observaciones`;

-- Notes:
-- 1) The controller `Perfil_integral::save` will attempt to auto-add missing columns as TEXT if migration isn't applied,
--    but adding this explicit VARCHAR(5000) enforces the requested length limit.
-- 2) Run this via your DB client or phpMyAdmin, or using the included `run_migrations.ps1` if you prefer.
