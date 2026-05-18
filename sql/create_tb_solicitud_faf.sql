-- create table for Formato de Analisis Financiero (FAF)
-- Flexible storage: `data` will contain JSON or serialized form fields

CREATE TABLE IF NOT EXISTS `tb_solicitud_faf` (
  `idfaf` int(11) NOT NULL AUTO_INCREMENT,
  `idsolicitud` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL COMMENT 'asalariado|comerciante',
  `data` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`idfaf`),
  INDEX `idx_solicitud` (`idsolicitud`),
  INDEX `idx_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
