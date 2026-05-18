-- Migration: create table to store reference ID photos
CREATE TABLE IF NOT EXISTS `tb_solicitud_referencias_fotos` (
  `idfoto` INT AUTO_INCREMENT PRIMARY KEY,
  `idsolicitud` INT NOT NULL,
  `idreferencia` INT DEFAULT NULL,
  `referencia_num` TINYINT DEFAULT NULL,
  `tipo` VARCHAR(10) DEFAULT NULL,
  `filename` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
