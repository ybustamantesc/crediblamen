-- Agrega la columna num_cuotas a tb_analisis_financiero_asalariado
ALTER TABLE `tb_analisis_financiero_asalariado`
  ADD COLUMN `num_cuotas` INT(11) DEFAULT 0;
