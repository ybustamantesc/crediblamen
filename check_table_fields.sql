-- Script para ver la estructura de la tabla tb_analisis_financiero_comerciante
-- Ejecuta esto en phpMyAdmin o MySQL Workbench

DESC tb_analisis_financiero_comerciante;

-- O también puedes usar:
-- SHOW COLUMNS FROM tb_analisis_financiero_comerciante;

-- O para ver más detalles:
SELECT 
    COLUMN_NAME, 
    COLUMN_TYPE, 
    IS_NULLABLE, 
    COLUMN_KEY, 
    COLUMN_DEFAULT,
    EXTRA
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'tb_analisis_financiero_comerciante'
ORDER BY ORDINAL_POSITION;
