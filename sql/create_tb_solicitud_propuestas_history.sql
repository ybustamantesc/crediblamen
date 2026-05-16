-- Create history table for propuesta changes
CREATE TABLE IF NOT EXISTS `tb_solicitud_propuestas_history` (
  `idhistory` int(11) NOT NULL AUTO_INCREMENT,
  `idsolicitud` int(11) NOT NULL,
  `idtipo_producto` int(11) DEFAULT NULL,
  `field_name` varchar(80) NOT NULL,
  `old_value` text,
  `new_value` text,
  `comment` text,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(120) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idhistory`),
  KEY `idsolicitud_idx` (`idsolicitud`),
  KEY `idtipo_producto_idx` (`idtipo_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usage: run this file once to create the history table.
