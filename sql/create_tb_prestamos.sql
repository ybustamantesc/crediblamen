-- Idempotent: create tb_prestamos
CREATE TABLE IF NOT EXISTS `tb_prestamos` (
  `idprestamo` INT NOT NULL AUTO_INCREMENT,
  `idsolicitud` INT DEFAULT NULL,
  `monto_credito` DECIMAL(14,2) DEFAULT 0,
  `monto_desembolsado` DECIMAL(14,2) DEFAULT 0,
  `interes_credito` DECIMAL(12,6) DEFAULT 0,
  `comision_desembolso` DECIMAL(8,4) DEFAULT 0,
  `numero_coutas` INT DEFAULT 0,
  `forma_pago` TINYINT DEFAULT 3, -- 0=Diario, 1=Semanal, 2=Quincenal, 3=Mensual
  `fecha_credito` DATE DEFAULT NULL,
  `estado` TINYINT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idprestamo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
