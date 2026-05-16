-- Actualizar columna forma_pago para soportar todas las frecuencias
-- 0=Diario, 1=Semanal, 2=Quincenal, 3=Mensual (default)

-- Cambiar el valor por defecto de forma_pago a 3 (Mensual)
ALTER TABLE `tb_prestamos` 
MODIFY COLUMN `forma_pago` TINYINT DEFAULT 3 COMMENT '0=Diario, 1=Semanal, 2=Quincenal, 3=Mensual';

-- Actualizar registros existentes que tengan 0 o 1 con el mapeo anterior
-- Nota: Si el mapeo anterior era diferente, ajustar según sea necesario
-- Antiguos valores: 0=mensual, 1=quincenal -> Nuevos valores: 0=Diario, 1=Semanal, 2=Quincenal, 3=Mensual

-- Si los datos existentes tenían 0=mensual, cambiarlos a 3=Mensual
UPDATE `tb_prestamos` SET `forma_pago` = 3 WHERE `forma_pago` = 0;

-- Si los datos existentes tenían 1=quincenal, cambiarlos a 2=Quincenal  
UPDATE `tb_prestamos` SET `forma_pago` = 2 WHERE `forma_pago` = 1;

-- Asegurarse de que todos los registros tengan un valor válido
UPDATE `tb_prestamos` SET `forma_pago` = 3 WHERE `forma_pago` IS NULL OR `forma_pago` NOT IN (0, 1, 2, 3);
