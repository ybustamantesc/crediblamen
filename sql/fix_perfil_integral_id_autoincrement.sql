-- Script para corregir el campo id de tb_perfil_integral_cliente
-- Este script hace que el campo id sea AUTO_INCREMENT

-- Primero verificamos la estructura actual
DESCRIBE tb_perfil_integral_cliente;

-- Modificar el campo id para que sea AUTO_INCREMENT
ALTER TABLE `tb_perfil_integral_cliente` 
MODIFY COLUMN `id` INT NOT NULL AUTO_INCREMENT;

-- Verificar que el cambio se aplicó correctamente
SHOW CREATE TABLE tb_perfil_integral_cliente;
