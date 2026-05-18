-- Script para asegurar que todas las tablas principales tengan AUTO_INCREMENT en sus claves primarias
-- Ejecutar este script si hay problemas al guardar registros nuevos

-- Tabla de solicitud referencias
ALTER TABLE `tb_solicitud_referencias` 
    MODIFY `idreferencia` INT NOT NULL AUTO_INCREMENT;

-- Tabla de solicitud uso de crédito
ALTER TABLE `tb_solicitud_uso_credito` 
    MODIFY `iduso` INT NOT NULL AUTO_INCREMENT;

-- Tabla de solicitud aprobaciones
ALTER TABLE `tb_solicitud_aprobaciones` 
    MODIFY `idaprobacion` INT NOT NULL AUTO_INCREMENT;

-- Tabla de garantías (si existe)
ALTER TABLE `tb_garantias` 
    MODIFY `idgarantia` INT NOT NULL AUTO_INCREMENT;

-- Tabla de perfil integral (si existe)
ALTER TABLE `tb_perfil_integral_cliente` 
    MODIFY `idperfil` INT NOT NULL AUTO_INCREMENT;

-- Verificar que se aplicaron correctamente
SHOW WARNINGS;
