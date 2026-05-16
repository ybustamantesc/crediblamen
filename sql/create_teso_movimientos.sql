-- Crear tabla teso_movimientos con campo primer_dia_pago y campos estándar
CREATE TABLE IF NOT EXISTS `teso_movimientos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tipo_movimiento` VARCHAR(50) DEFAULT NULL,
  `concepto` VARCHAR(255) DEFAULT NULL,
  `forma_pago` VARCHAR(50) DEFAULT NULL,
  `fecha_registro` DATE DEFAULT NULL,
  `fecha_aplicacion` DATE DEFAULT NULL,
  `primer_dia_pago` DATE DEFAULT NULL,
  `beneficiario` VARCHAR(255) DEFAULT NULL,
  `referencia1` VARCHAR(255) DEFAULT NULL,
  `referencia2` VARCHAR(255) DEFAULT NULL,
  `monto_total` DECIMAL(18,2) DEFAULT 0,
  `iva_total` DECIMAL(18,2) DEFAULT 0,
  `departamento` VARCHAR(100) DEFAULT NULL,
  `centro_costos` VARCHAR(100) DEFAULT NULL,
  `proyecto` VARCHAR(100) DEFAULT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `cuenta_id` INT DEFAULT NULL,
  `tipo_transferencia` VARCHAR(50) DEFAULT NULL,
  `numero_cheque` VARCHAR(50) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;