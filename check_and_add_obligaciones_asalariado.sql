-- Verificar y agregar campos de obligaciones para Analisis Financiero Asalariado
-- Ejecutar en MySQL/MariaDB sobre la BD actual

-- 1) Ver campos actuales en la tabla
SHOW COLUMNS FROM `tb_analisis_financiero_asalariado`;

-- 2) Verificar campos clave de obligaciones
SELECT COLUMN_NAME, DATA_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'tb_analisis_financiero_asalariado'
  AND COLUMN_NAME IN (
    'olp_fecha','olp_cuota','olp_instituciones','olp_saldo','subtotal_olp_saldo',
    'ocp_fecha','ocp_cuota','ocp_instituciones','ocp_saldo','subtotal_ocp_saldo',
    'asal_olp_fecha','asal_olp_cuota','asal_olp_instituciones','asal_olp_saldo','asal_subtotal_olp_saldo'
  )
ORDER BY COLUMN_NAME;

-- 3) Agregar los faltantes (idempotente)
ALTER TABLE `tb_analisis_financiero_asalariado`
  ADD COLUMN IF NOT EXISTS `olp_fecha` VARCHAR(20) NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `olp_cuota` DECIMAL(14,2) NULL DEFAULT 0.00,
  ADD COLUMN IF NOT EXISTS `olp_instituciones` VARCHAR(100) NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `olp_saldo` DECIMAL(14,2) NULL DEFAULT 0.00,
  ADD COLUMN IF NOT EXISTS `subtotal_olp_saldo` DECIMAL(14,2) NULL DEFAULT 0.00,
  ADD COLUMN IF NOT EXISTS `ocp_fecha` VARCHAR(20) NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `ocp_cuota` DECIMAL(14,2) NULL DEFAULT 0.00,
  ADD COLUMN IF NOT EXISTS `ocp_instituciones` VARCHAR(100) NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `ocp_saldo` DECIMAL(14,2) NULL DEFAULT 0.00,
  ADD COLUMN IF NOT EXISTS `subtotal_ocp_saldo` DECIMAL(14,2) NULL DEFAULT 0.00,
  ADD COLUMN IF NOT EXISTS `asal_olp_fecha` VARCHAR(20) NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `asal_olp_cuota` DECIMAL(14,2) NULL DEFAULT 0.00,
  ADD COLUMN IF NOT EXISTS `asal_olp_instituciones` VARCHAR(100) NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `asal_olp_saldo` DECIMAL(14,2) NULL DEFAULT 0.00,
  ADD COLUMN IF NOT EXISTS `asal_subtotal_olp_saldo` DECIMAL(14,2) NULL DEFAULT 0.00;

-- 4) Confirmación rápida final
SHOW COLUMNS FROM `tb_analisis_financiero_asalariado`;
