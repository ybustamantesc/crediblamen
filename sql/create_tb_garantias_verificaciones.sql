-- Tabla para almacenar verificación (comentarios) de garantías
CREATE TABLE IF NOT EXISTS `tb_garantias_verificaciones` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `garantia_id` INT DEFAULT NULL,
  `solicitud_id` INT DEFAULT NULL,
  `verificador_usuario` VARCHAR(150) DEFAULT NULL,
  `comentario` TEXT DEFAULT NULL,
  `foto1` VARCHAR(255) DEFAULT NULL,
  `foto2` VARCHAR(255) DEFAULT NULL,
  `foto3` VARCHAR(255) DEFAULT NULL,
  `foto4` VARCHAR(255) DEFAULT NULL,
  `foto5` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`garantia_id`),
  INDEX (`solicitud_id`)
);
