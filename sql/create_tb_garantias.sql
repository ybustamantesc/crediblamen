-- Tabla para almacenar formato de garantía vinculado a solicitudes
CREATE TABLE IF NOT EXISTS `tb_garantias` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `solicitud_id` INT NOT NULL,
  `nombre` VARCHAR(255) NOT NULL,
  `cantidad` INT DEFAULT 1,
  `marca` VARCHAR(255) DEFAULT NULL,
  `modelo` VARCHAR(255) DEFAULT NULL,
  `n_serie` VARCHAR(255) DEFAULT NULL,
  `costo` DECIMAL(14,2) DEFAULT NULL,
  `tiempo_vida` VARCHAR(100) DEFAULT NULL,
  `foto1` VARCHAR(255) DEFAULT NULL,
  `foto2` VARCHAR(255) DEFAULT NULL,
  `foto3` VARCHAR(255) DEFAULT NULL,
  `foto4` VARCHAR(255) DEFAULT NULL,
  `foto5` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`solicitud_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Nota: se asume que existe la tabla `tb_solicitudes`.
-- Para agregar la FK, activar la siguiente instrucción y ajustar el nombre de la tabla destino si aplica:
-- ALTER TABLE `tb_garantias` ADD CONSTRAINT `fk_garantias_solicitud` FOREIGN KEY (`solicitud_id`) REFERENCES `tb_solicitudes`(`id`) ON DELETE CASCADE;
