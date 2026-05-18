-- Create table to store selected product proposals per solicitud
CREATE TABLE IF NOT EXISTS `tb_solicitud_propuestas` (
  `idpropuesta` int(11) NOT NULL AUTO_INCREMENT,
  `idsolicitud` int(11) NOT NULL,
  `idtipo_producto` int(11) NOT NULL,
  `monto` decimal(15,2) DEFAULT NULL,
  `tasa` decimal(10,4) DEFAULT NULL,
  `comision_desembolso` decimal(10,4) DEFAULT NULL,
  `plazo_min` int(11) DEFAULT NULL,
  `plazo_max` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idpropuesta`),
  KEY `idsolicitud_idx` (`idsolicitud`),
  KEY `idtipo_producto_idx` (`idtipo_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usage: import or run this script once to create the table. It's safe to re-run with IF NOT EXISTS.
