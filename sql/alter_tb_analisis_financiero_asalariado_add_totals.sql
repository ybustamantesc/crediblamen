-- Agrega los totales de transporte y vivienda a la tabla de análisis financiero asalariado
ALTER TABLE `tb_analisis_financiero_asalariado`
ADD COLUMN `total_transporte` DECIMAL(14,2) DEFAULT 0,
ADD COLUMN `total_gastos_vivienda` DECIMAL(14,2) DEFAULT 0;
