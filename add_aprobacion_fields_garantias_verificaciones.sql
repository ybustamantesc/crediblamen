-- Agregar columnas para nombre de garantía y estado de aprobación
ALTER TABLE `tb_garantias_verificaciones` 
ADD COLUMN `nombre_garantia` VARCHAR(255) DEFAULT NULL AFTER `garantia_id`,
ADD COLUMN `estado_aprobacion` VARCHAR(50) DEFAULT 'No aprobado' AFTER `nombre_garantia`;
