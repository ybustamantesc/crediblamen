-- Crea la tabla tb_solicitud_referencias de forma idempotente
CREATE TABLE IF NOT EXISTS `tb_solicitud_referencias` (
    `idreferencia` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `idsolicitud` INT NOT NULL,
    `referencia_num` TINYINT NOT NULL DEFAULT 1,
    `nombre` VARCHAR(255) NULL,
    `cedula` VARCHAR(100) NULL,
    `direccion` TEXT NULL,
    `telefono` VARCHAR(100) NULL,
    `tipo_referencia` VARCHAR(80) NULL,
    `tipo_personal_relacion` VARCHAR(100) NULL,
    `desde_conoce_cliente` VARCHAR(255) NULL,
    `relacion_economica` TINYINT NULL,
    `opinion` VARCHAR(255) NULL,
    `comentarios` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_idsolicitud (`idsolicitud`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Asegurar que idreferencia tiene AUTO_INCREMENT (en caso de tabla existente)
ALTER TABLE `tb_solicitud_referencias` 
    MODIFY `idreferencia` INT NOT NULL AUTO_INCREMENT;
