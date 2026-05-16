-- Create table for solicitud approvals
CREATE TABLE IF NOT EXISTS `tb_solicitud_aprobaciones` (
  `idaprobacion` int(11) NOT NULL AUTO_INCREMENT,
  `idsolicitud` int(11) NOT NULL,
  `role` varchar(80) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(120) DEFAULT NULL,
  `aprobado_por` varchar(50) DEFAULT NULL,
  `comment` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idaprobacion`),
  INDEX (`idsolicitud`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usage: run this file once (phpMyAdmin or mysql cli) to create the approvals table
