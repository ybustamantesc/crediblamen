-- Idempotent: create tb_prestamo_cuotas
CREATE TABLE IF NOT EXISTS `tb_prestamo_cuotas` (
  `idcuota` INT NOT NULL AUTO_INCREMENT,
  `idprestamo` INT NOT NULL,
  `numero` INT NOT NULL,
  `fecha_vencimiento` DATE DEFAULT NULL,
  `principal` DECIMAL(14,2) DEFAULT 0,
  `interes` DECIMAL(14,2) DEFAULT 0,
  `cuota` DECIMAL(14,2) DEFAULT 0,
  `saldo` DECIMAL(14,2) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idcuota`),
  INDEX (`idprestamo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
