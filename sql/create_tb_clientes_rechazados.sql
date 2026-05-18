-- Create table to store rejected clients (snapshot of tb_clientes)
CREATE TABLE IF NOT EXISTS `tb_clientes_rechazados` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `apellidos` VARCHAR(50) NOT NULL,
  `nombres` VARCHAR(80) NOT NULL,
  `direccion` VARCHAR(200) DEFAULT NULL,
  `telefono` VARCHAR(30) DEFAULT NULL,
  `tipo_doc` TINYINT(2) DEFAULT NULL,
  `numero_doc` VARCHAR(60) DEFAULT NULL,
  `comentarios` TEXT,
  `rechazo_motivo` VARCHAR(255) DEFAULT NULL,
  `rechazado_por` INT DEFAULT NULL,
  `rechazado_en` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `numero_doc_idx` (`numero_doc`),
  KEY `rechazado_por_idx` (`rechazado_por`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
