-- Crea la tabla tb_solicitud_uso_credito de forma idempotente
CREATE TABLE IF NOT EXISTS `tb_solicitud_uso_credito` (
    `iduso` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `idsolicitud` INT NOT NULL,
    `descripcion` TEXT NULL,
    `fuente_ingreso` TEXT NULL,
    `monto_estimado_mes` DECIMAL(15,2) NULL,
    `monto_solicitado` DECIMAL(15,2) NULL,
    `plazo_solicitado` INT NULL,
    `destino_prestamo` VARCHAR(100) NULL,
    `destino_detalle` TEXT NULL,
    `declaracion_nombre` VARCHAR(255) NULL,
    `declaracion_firma` VARCHAR(255) NULL,
    `declaracion_fecha` DATE NULL,
    `evaluador_credito` VARCHAR(150) NULL,
    `fecha_evaluacion` DATE NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_idsolicitud (`idsolicitud`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Asegurar que iduso tiene AUTO_INCREMENT (en caso de tabla existente)
ALTER TABLE `tb_solicitud_uso_credito` 
    MODIFY `iduso` INT NOT NULL AUTO_INCREMENT;
